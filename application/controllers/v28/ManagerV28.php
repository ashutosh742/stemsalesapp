<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ManagerV28 Controller
 *
 * Routes:
 *   GET /api/manager/incentive/current
 *   GET /api/manager/incentive/history
 *   GET /api/manager/incentive/weekly
 *
 * Real tables: manager_incentive, manager_incentive_ledger
 * manager_incentive: id, manager_uid, manager_role, incentive_week, gross_rs,
 *   deduction_rs, net_rs, grade, payout_status, approved_by_uid, approved_at
 * manager_incentive_ledger: id, manager_uid, period, amount_rs, grade, computed_at
 */
class ManagerV28 extends CI_Controller {

    private $token = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';

    public function __construct()
    {
        parent::__construct();
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Authorization, Content-Type');
        $this->output->set_content_type('application/json');
    }

    private function auth()
    {
        if (function_exists('authunify_ok') && authunify_ok()) { return true; } // rimlyproof_authunify_20260609

        $h = $this->input->get_request_header('Authorization', TRUE);
        if (!$h || trim(str_replace('Bearer', '', $h)) !== $this->token) {
            $this->json_out(['ok' => false, 'error' => 'unauthorized'], 401);
            return false;
        }
        return true;
    }

    private function json_out($data, $status = 200)
    {
        $this->output->set_status_header($status)
                     ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    /**
     * GET /api/manager/incentive/current?manager_uid=<uid>
     * Latest incentive record for the manager (most recent incentive_week).
     */
    public function incentive_current()
    {
        if (!$this->auth()) return;
        $manager_uid = (int) $this->input->get('manager_uid');

        $this->db->select('m.id, m.manager_uid, u.name AS manager_name, m.manager_role,
                           m.incentive_week, m.gross_rs, m.deduction_rs, m.net_rs,
                           m.grade, m.payout_status, m.approved_at')
                 ->from('manager_incentive m')
                 ->join('user u', 'u.uid = m.manager_uid', 'left');

        if ($manager_uid > 0) {
            $this->db->where('m.manager_uid', $manager_uid);
        }

        $row = $this->db->order_by('m.incentive_week', 'DESC')
                        ->limit(1)
                        ->get()->row_array();

        if (!$row) {
            $this->json_out(['ok' => true, 'success' => true, 'rows' => [], 'count' => 0, 'note' => 'no_data']);
            return;
        }
        $this->json_out(['ok' => true, 'success' => true, 'data' => $row]);
    }

    /**
     * GET /api/manager/incentive/history?manager_uid=<uid>[&limit=<n>]
     * Full incentive history from manager_incentive_ledger.
     */
    public function incentive_history()
    {
        if (!$this->auth()) return;
        $manager_uid = (int) $this->input->get('manager_uid');
        $limit       = min(100, max(1, (int) ($this->input->get('limit') ?? 24)));

        $this->db->select('l.id, l.manager_uid, u.name AS manager_name,
                           l.period, l.amount_rs, l.grade, l.computed_at')
                 ->from('manager_incentive_ledger l')
                 ->join('user u', 'u.uid = l.manager_uid', 'left');

        if ($manager_uid > 0) {
            $this->db->where('l.manager_uid', $manager_uid);
        }

        $rows = $this->db->order_by('l.computed_at', 'DESC')
                         ->limit($limit)
                         ->get()->result_array();

        $this->json_out(['ok' => true, 'success' => true, 'rows' => $rows, 'count' => count($rows)]);
    }

    /**
     * GET /api/manager/incentive/weekly?manager_uid=<uid>
     * Weekly incentive breakdown from manager_incentive table.
     */
    public function incentive_weekly()
    {
        if (!$this->auth()) return;
        $manager_uid = (int) $this->input->get('manager_uid');

        $this->db->select('m.id, m.manager_uid, u.name AS manager_name, m.manager_role,
                           m.incentive_week, m.gross_rs, m.deduction_rs, m.net_rs,
                           m.grade, m.payout_status')
                 ->from('manager_incentive m')
                 ->join('user u', 'u.uid = m.manager_uid', 'left');

        if ($manager_uid > 0) {
            $this->db->where('m.manager_uid', $manager_uid);
        }

        $rows = $this->db->order_by('m.incentive_week', 'DESC')
                         ->limit(52)
                         ->get()->result_array();

        $this->json_out(['ok' => true, 'success' => true, 'rows' => $rows, 'count' => count($rows)]);
    }
}
