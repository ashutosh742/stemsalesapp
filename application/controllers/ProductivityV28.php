<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ProductivityV28 Controller
 *
 * Exposes BD/CM productivity scoring and stuck leads detection for STEM CRM v2.8.
 * All responses are JSON. All writes are guarded by token auth (run_nightly only).
 */
class ProductivityV28 extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('ProductivityV28_model', 'prod_model');
        $this->output->set_content_type('application/json');
    }

    // -------------------------------------------------------------------------
    // HELPERS
    // -------------------------------------------------------------------------

    /**
     * json_out
     * Sends a JSON response and exits.
     *
     * @param array $data
     * @param int   $status HTTP status code.
     */
    private function json_out($data, $status = 200)
    {
        $this->output
             ->set_status_header($status)
             ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    /**
     * today
     * Returns current date as YYYY-MM-DD in IST.
     */
    private function today()
    {
        return date('Y-m-d');
    }

    /**
     * resolve_date
     * Reads optional ?date= query param, falls back to today.
     */
    private function resolve_date()
    {
        $d = $this->input->get('date');
        if ($d && preg_match('/^\d{4}-\d{2}-\d{2}$/', $d)) {
            return $d;
        }
        return $this->today();
    }

    // -------------------------------------------------------------------------
    // ENDPOINTS
    // -------------------------------------------------------------------------

    /**
     * bd_today
     *
     * GET /api/v28/productivity/bd_today?bd_uid=<id>[&date=YYYY-MM-DD]
     *
     * Returns the bd_productivity_daily row for the given BD and date.
     * If the row is missing (i.e. nightly cron has not run yet), computes
     * it live and upserts it before returning.
     */
    public function bd_today()
    {
        $bd_uid = (int) $this->input->get('bd_uid');
        if ($bd_uid <= 0) {
            return $this->json_out(['error' => 'bd_uid is required'], 400);
        }

        $for_date = $this->resolve_date();
        $row      = $this->prod_model->get_bd_daily_row($bd_uid, $for_date);

        if ( ! $row) {
            // Live compute and persist
            $row = $this->prod_model->score_bd_day($bd_uid, $for_date);
            $this->prod_model->upsert_bd_daily($row);
        }

        $this->json_out(['success' => true, 'data' => $row]);
    }

    /**
     * cm_today
     *
     * GET /api/v28/productivity/cm_today?cm_uid=<id>[&date=YYYY-MM-DD]
     *
     * Returns the cm_productivity_daily row for the given CM and date.
     * Live-computes if missing.
     */
    public function cm_today()
    {
        $cm_uid = (int) $this->input->get('cm_uid');
        if ($cm_uid <= 0) {
            return $this->json_out(['error' => 'cm_uid is required'], 400);
        }

        $for_date = $this->resolve_date();
        $row      = $this->prod_model->get_cm_daily_row($cm_uid, $for_date);

        if ( ! $row) {
            $row = $this->prod_model->score_cm_day($cm_uid, $for_date);
            $this->prod_model->upsert_cm_daily($row);
        }

        $this->json_out(['success' => true, 'data' => $row]);
    }

    /**
     * stuck_leads
     *
     * GET /api/v28/productivity/stuck_leads[?bd_uid=<id>][&date=YYYY-MM-DD]
     *
     * Returns rows from stuck_leads_daily for the given date.
     * Optionally filtered by bd_uid.
     * Sorted by days_in_stage descending.
     */
    public function stuck_leads()
    {
        $for_date = $this->resolve_date();
        $bd_uid   = $this->input->get('bd_uid');
        $bd_uid   = ($bd_uid && (int) $bd_uid > 0) ? (int) $bd_uid : null;

        $leads = $this->prod_model->get_stuck_leads_for_date($for_date, $bd_uid);

        $this->json_out([
            'success'      => true,
            'date'         => $for_date,
            'total'        => count($leads),
            'stuck_leads'  => $leads,
        ]);
    }

    /**
     * run_nightly
     *
     * POST /api/v28/productivity/run_nightly?token=<STEM_DIGEST_TOKEN>
     *
     * Token-protected endpoint called by cron at 23:30 IST.
     * Iterates all active users (type_id IN 1, 13, 28), scores each,
     * writes rows, runs detect_stuck_leads, then emails admin summary.
     */
    public function run_nightly()
    {
        // Token guard
        $token         = $this->input->get('token');
        $expected_token = defined('STEM_DIGEST_TOKEN') ? STEM_DIGEST_TOKEN : getenv('STEM_DIGEST_TOKEN');

        if ( ! $token || ! hash_equals((string) $expected_token, (string) $token)) {
            return $this->json_out(['error' => 'Unauthorized'], 401);
        }

        $for_date   = $this->resolve_date();
        $users      = $this->prod_model->get_all_active_users();

        $bd_type_ids = [1, 28]; // BD executive type IDs
        $cm_type_ids = [13];    // CM type IDs

        $bd_count      = 0;
        $cm_count      = 0;
        $bd_score_sum  = 0.0;
        $cm_score_sum  = 0.0;

        foreach ($users as $user) {
            $type_id = (int) $user['type_id'];

            if (in_array($type_id, $bd_type_ids)) {
                $row = $this->prod_model->score_bd_day((int) $user['id'], $for_date);
                $this->prod_model->upsert_bd_daily($row);
                $bd_count++;
                $bd_score_sum += $row['score_pct'];
            }

            if (in_array($type_id, $cm_type_ids)) {
                $row = $this->prod_model->score_cm_day((int) $user['id'], $for_date);
                $this->prod_model->upsert_cm_daily($row);
                $cm_count++;
                $cm_score_sum += $row['score_pct'];
            }
        }

        // Detect stuck leads
        $stuck_count = $this->prod_model->detect_stuck_leads($for_date);

        // Summary data for email
        $bd_avg_score = $bd_count > 0 ? round($bd_score_sum / $bd_count, 2) : 0;
        $worst_bds    = $this->prod_model->get_worst_bd_scores($for_date, 5);
        $most_stuck   = $this->prod_model->get_most_stuck_leads($for_date, 5);

        // Build and send summary email
        $this->_send_nightly_summary([
            'for_date'     => $for_date,
            'bd_count'     => $bd_count,
            'cm_count'     => $cm_count,
            'bd_avg_score' => $bd_avg_score,
            'stuck_count'  => $stuck_count,
            'worst_bds'    => $worst_bds,
            'most_stuck'   => $most_stuck,
        ]);

        $this->json_out([
            'success'      => true,
            'for_date'     => $for_date,
            'bd_scored'    => $bd_count,
            'cm_scored'    => $cm_count,
            'bd_avg_score' => $bd_avg_score,
            'stuck_leads'  => $stuck_count,
        ]);
    }

    /**
     * probe
     *
     * GET /api/v28/productivity/probe
     *
     * Health check endpoint. Returns {ok: true}.
     */
    public function probe()
    {
        $this->json_out(['ok' => true]);
    }

    // -------------------------------------------------------------------------
    // PRIVATE HELPERS
    // -------------------------------------------------------------------------

    /**
     * _send_nightly_summary
     *
     * Composes and sends the nightly digest email to the configured admin address.
     *
     * @param array $summary
     */
    private function _send_nightly_summary($summary)
    {
        $admin_email = defined('STEM_ADMIN_EMAIL') ? STEM_ADMIN_EMAIL : getenv('STEM_ADMIN_EMAIL');
        if ( ! $admin_email) {
            log_message('error', 'ProductivityV28: STEM_ADMIN_EMAIL not configured, skipping nightly email.');
            return;
        }

        $worst_bd_lines = '';
        foreach ($summary['worst_bds'] as $i => $b) {
            $rank = $i + 1;
            $name = htmlspecialchars($b['bd_name'] ?? 'Unknown BD');
            $pct  = number_format((float) ($b['score_pct'] ?? 0), 1);
            $worst_bd_lines .= "<tr>
                <td>{$rank}</td>
                <td>{$name}</td>
                <td>{$pct} percent</td>
                <td>{$b['executed_min']} min</td>
            </tr>";
        }

        $stuck_lines = '';
        foreach ($summary['most_stuck'] as $i => $s) {
            $rank    = $i + 1;
            $company = htmlspecialchars($s['company_name'] ?? 'Unknown');
            $bd_name = htmlspecialchars($s['bd_name']      ?? 'Unknown BD');
            $days    = (int) ($s['days_in_stage'] ?? 0);
            $status  = (int) ($s['cstatus']       ?? 0);
            $stuck_lines .= "<tr>
                <td>{$rank}</td>
                <td>{$company}</td>
                <td>Status {$status}</td>
                <td>{$days} days</td>
                <td>{$bd_name}</td>
            </tr>";
        }

        $subject = "STEM CRM Nightly Digest - {$summary['for_date']}";

        $body = "
<html><body style='font-family:Arial,sans-serif;color:#222;'>
<h2>STEM CRM Nightly Productivity Digest</h2>
<p><strong>Date:</strong> {$summary['for_date']}</p>

<h3>Summary</h3>
<ul>
  <li>BDs scored: {$summary['bd_count']}</li>
  <li>CMs scored: {$summary['cm_count']}</li>
  <li>Average BD score: {$summary['bd_avg_score']} percent</li>
  <li>Stuck leads detected: {$summary['stuck_count']}</li>
</ul>

<h3>Top 5 Worst BD Scores</h3>
<table border='1' cellpadding='6' cellspacing='0' style='border-collapse:collapse;'>
  <tr><th>Rank</th><th>BD Name</th><th>Score</th><th>Executed</th></tr>
  {$worst_bd_lines}
</table>

<h3>Top 5 Most Stuck Leads</h3>
<table border='1' cellpadding='6' cellspacing='0' style='border-collapse:collapse;'>
  <tr><th>Rank</th><th>Company</th><th>Stage</th><th>Days Stuck</th><th>BD</th></tr>
  {$stuck_lines}
</table>

<p style='color:#888;font-size:12px;'>Generated by STEM CRM v2.8 nightly cron at 23:30 IST.</p>
</body></html>
        ";

        $this->load->library('email');
        $this->email->initialize([
            'mailtype' => 'html',
            'charset'  => 'utf-8',
        ]);
        $this->email->from('no-reply@stemcrm.in', 'STEM CRM System');
        $this->email->to($admin_email);
        $this->email->subject($subject);
        $this->email->message($body);

        if ( ! $this->email->send()) {
            log_message('error', 'ProductivityV28 nightly email failed: ' . $this->email->print_debugger());
        }
    }
}
