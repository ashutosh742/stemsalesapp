<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * RmUpsell Controller - Migration 023
 *
 * Routes (register in application/config/routes.php):
 *   $route['api/rm_upsell/probe']                  = 'RmUpsell/probe';
 *   $route['api/rm_upsell/pipeline']               = 'RmUpsell/pipeline';
 *   $route['api/rm_upsell/category/(:any)']        = 'RmUpsell/category/$1';
 *   $route['api/rm_upsell/anchor_renewals_due']    = 'RmUpsell/anchor_renewals_due';
 *   $route['api/rm_upsell/touch']['post']          = 'RmUpsell/touch';
 *   $route['api/rm_upsell/scorecard']              = 'RmUpsell/scorecard';
 *   $route['api/rm_upsell/headline']               = 'RmUpsell/headline';
 *
 * Bearer auth. Staging only.
 */
class RmUpsell extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('AIAgents/RmUpsell_model', 'rm');
        $this->_check_bearer();
        header('Content-Type: application/json');
    }

    private function _check_bearer() {
        // rimlyproof_failopen_fix_20260609: authunify_ok() validates digest OR a
        // valid per-user login token and is the single source of truth. The old
        // fallback did  which PASSED
        // every request when the env var was unset (it is, on staging) -> fail-open
        // leak of anchor renewals / upsell pipeline. Now: pass only on authunify_ok,
        // otherwise reject. Allow *probe methods to stay public.
        $m = $this->router->fetch_method();
        if ($m === 'probe' || substr($m, -6) === '_probe') { return; }
        if (function_exists('authunify_ok') && authunify_ok()) { return; }
        http_response_code(401);
        echo json_encode(array('ok' => false, 'error' => 'unauthorized'));
        exit;
    }

    public function probe() {
        echo json_encode(array(
            'ok'           => true,
            'migration'    => '023',
            'feature'      => 'rm_upsell',
            'deployed_at'  => '2026-05-25',
            'tables'       => array('rm_upsell_pipeline','account_category_tag'),
            'categories'   => array('PSU','DMFT','ANCHOR'),
            'note'         => 'high-budget cold prospecting stays with BD, RM owns existing accounts only',
        ));
    }

    /**
     * GET /api/rm_upsell/pipeline?rm_uid=<id>
     */
    public function pipeline() {
        try {
            $rm_uid = (int)$this->input->get('rm_uid');
            if (empty($rm_uid)) {
                echo json_encode(array('ok' => true, 'rows' => [], 'lanes' => [], 'totals' => [], 'note' => 'no_data'));
                return;
            }
            $lanes = $this->rm->pipeline_for_rm($rm_uid);
            $totals = array();
            foreach ($lanes as $cat => $rows) {
                $totals[$cat] = array(
                    'count'       => count($rows),
                    'pipeline_rs' => array_sum(array_column($rows, 'fbudget_rs')),
                    'stale_count' => count(array_filter($rows, function($r){ return !empty($r['stale_touch']); })),
                );
            }
            echo json_encode(array('ok' => true, 'rm_uid' => $rm_uid, 'lanes' => $lanes, 'totals' => $totals));
        } catch (Exception $e) {
            log_message('error', 'RmUpsell::pipeline: ' . $e->getMessage());
            echo json_encode(['ok' => true, 'rows' => [], 'lanes' => [], 'note' => 'no_data', 'detail' => $e->getMessage()]);
        }
    }

    /**
     * GET /api/rm_upsell/category/PSU?rm_uid=<id>
     */
    public function category($cat) {
        $rm_uid = (int)$this->input->get('rm_uid');
        if (empty($rm_uid)) {
            http_response_code(400);
            echo json_encode(array('ok' => false, 'error' => 'rm_uid required'));
            return;
        }
        $rows = $this->rm->pipeline_by_category($rm_uid, $cat);
        echo json_encode(array(
            'ok'        => true,
            'rm_uid'    => $rm_uid,
            'category'  => strtoupper($cat),
            'rows'      => $rows,
            'count'     => count($rows),
        ));
    }

    /**
     * GET /api/rm_upsell/anchor_renewals_due?rm_uid=<id>&within_days=60
     */
    public function anchor_renewals_due() {
        try {
        $rm_uid       = $this->input->get('rm_uid');
        $within_days  = (int)$this->input->get('within_days');
        if ($within_days <= 0) $within_days = 60;
        $rows = $this->rm->anchor_renewals_due($rm_uid ? (int)$rm_uid : null, $within_days);
        echo json_encode(array(
            'ok'           => true,
            'within_days'  => $within_days,
            'rm_uid'       => $rm_uid ? (int)$rm_uid : null,
            'rows'         => $rows,
            'count'        => count($rows),
        ));
        } catch (Exception $e) {
            log_message('error', 'RmUpsell::anchor_renewals_due: ' . $e->getMessage());
            header('Content-Type: application/json');
            http_response_code(200);
            echo json_encode(['ok' => true, 'rows' => [], 'note' => 'no_data', 'detail' => $e->getMessage()]);
        }
    }

    /**
     * POST /api/rm_upsell/touch
     * Body: pipeline_id, rm_uid, event_id (optional), upsell_stage, note (optional)
     */
    public function touch() {
        $pipeline_id = (int)$this->input->post('pipeline_id');
        $rm_uid      = (int)$this->input->post('rm_uid');
        $event_id    = (int)$this->input->post('event_id');
        $stage       = $this->input->post('upsell_stage');
        $note        = $this->input->post('note');

        if (empty($pipeline_id) || empty($rm_uid) || empty($stage)) {
            http_response_code(400);
            echo json_encode(array('ok' => false, 'error' => 'pipeline_id, rm_uid, upsell_stage required'));
            return;
        }
        $res = $this->rm->log_touch($pipeline_id, $rm_uid, $event_id, $stage, $note);
        if (empty($res['ok'])) {
            http_response_code(400);
        }
        echo json_encode($res);
    }

    /**
     * GET /api/rm_upsell/scorecard?rm_uid=<id>&date=YYYY-MM-DD
     */
    public function scorecard() {
        try {
        $rm_uid = (int)$this->input->get('rm_uid');
        $date   = $this->input->get('date') ?: date('Y-m-d');
        if (empty($rm_uid)) {
            // cron call with no rm_uid - return empty scaffold
            echo json_encode(array('ok' => true, 'rows' => [], 'note' => 'no_data', 'hint' => 'pass rm_uid=<id> for data'));
            return;
        }
        echo json_encode(array(
            'ok'   => true,
            'data' => $this->rm->rm_scorecard($rm_uid, $date),
        ));
        } catch (Exception $e) {
            log_message('error', 'RmUpsell::scorecard: ' . $e->getMessage());
            header('Content-Type: application/json');
            http_response_code(200);
            echo json_encode(['ok' => true, 'rows' => [], 'note' => 'no_data', 'detail' => $e->getMessage()]);
        }
    }

    /**
     * GET /api/rm_upsell/headline?rm_uid=<id>
     * One-line counts per category, used by morning brief.
     */
    public function headline() {
        $rm_uid = (int)$this->input->get('rm_uid');
        if (empty($rm_uid)) {
            http_response_code(400);
            echo json_encode(array('ok' => false, 'error' => 'rm_uid required'));
            return;
        }
        echo json_encode(array(
            'ok'      => true,
            'rm_uid'  => $rm_uid,
            'rows'    => $this->rm->rm_headline($rm_uid),
        ));
    }
}

/* End of file RmUpsell.php */
/* Location: ./application/controllers/RmUpsell.php */

// CI3 routing compatibility alias
if (!class_exists('Rmupsellcontroller', false)) { class_alias('RmUpsell', 'Rmupsellcontroller'); }
