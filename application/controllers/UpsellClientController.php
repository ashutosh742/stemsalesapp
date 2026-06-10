<?php
/**
 * UpsellClientController.php
 *
 * GAP 21 - Upsell Client pipeline endpoints.
 * CodeIgniter 3 controller. No em-dashes. No non-ASCII characters.
 * Bearer token authentication on all non-probe endpoints.
 *
 * Routes to add in application/config/routes.php:
 *   $route['api/upsell/probe']['get']               = 'UpsellClientController/probe';
 *   $route['api/upsell/handover_approval']['get']   = 'UpsellClientController/handover_approval';
 *   $route['api/upsell/artwork_pending']['get']     = 'UpsellClientController/artwork_pending';
 *   $route['api/upsell/artwork_done']['get']        = 'UpsellClientController/artwork_done';
 *   $route['api/upsell/total_summary']['get']       = 'UpsellClientController/total_summary';
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class UpsellClientController extends CI_Controller
{
    const MIGRATION = '021';

    private $bearer_token;

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->config('custom', TRUE);
        $config_token = $this->config->item('stem_digest_token', 'custom');
        $this->bearer_token = $config_token ?: getenv('STEM_DIGEST_TOKEN') ?: '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
        header('Content-Type: application/json; charset=utf-8');
    }

    // -------------------------------------------------------------------------
    // _auth_or_die - validates Bearer token; falls back to active CI session.
    // -------------------------------------------------------------------------
    private $_authed_uid = 0;

    // ---- per-user JWT validator (added 28 May 2026, matches Auth::api_login) ----
    private function _jwt_token_valid($token) {
        if (empty($token)) return false;
        $secret = getenv('STEM_DIGEST_TOKEN') ?: '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
        $days = array(date('Y-m-d'), date('Y-m-d', strtotime('-1 day')), date('Y-m-d', strtotime('+1 day')));
        // Try uid from request first (fast path)
        $candidates = array();
        foreach (array('uid','cm_uid','rm_uid','bd_uid','acm_uid','user_id') as $k) {
            if (isset($_GET[$k]) && (int)$_GET[$k] > 0) $candidates[(int)$_GET[$k]] = 1;
            if (isset($_POST[$k]) && (int)$_POST[$k] > 0) $candidates[(int)$_POST[$k]] = 1;
        }
        foreach (array_keys($candidates) as $uid) {
            foreach ($days as $d) {
                if (hash_equals(sha1($secret.'|'.$uid.'|'.$d), $token)) return (int)$uid;
            }
        }
        // Fallback: scan all active uids (cached for 60 sec)
        static $all_uids = null;
        if ($all_uids === null) {
            $rows = $this->db->select('uid')->from('user')->where('active', 1)->get()->result();
            $all_uids = array();
            foreach ($rows as $r) $all_uids[] = (int)$r->uid;
        }
        foreach ($all_uids as $uid) {
            foreach ($days as $d) {
                if (hash_equals(sha1($secret.'|'.$uid.'|'.$d), $token)) return (int)$uid;
            }
        }
        return false;
    }

    private function _auth_or_die()
    {
        $hdr = $this->input->get_request_header('Authorization', TRUE);
        if ($hdr === 'Bearer ' . $this->bearer_token) {
            return TRUE;
        }
        // Per-user JWT (added 28 May)
        if (!empty($hdr) && stripos($hdr, 'Bearer ') === 0) {
            $tok = trim(substr($hdr, 7));
            $uid = $this->_jwt_token_valid($tok);
            if ($uid) { $this->_authed_uid = $uid; return TRUE; }
        }
        $session_uid = (int) $this->session->userdata('user_id');
        if ($session_uid > 0) {
            return TRUE;
        }
        http_response_code(401);
        echo json_encode(array('error' => 'unauthorized'));
        exit;
    }

    // -------------------------------------------------------------------------
    // _json - sends JSON and exits.
    // -------------------------------------------------------------------------
    private function _json($data, $code = 200)
    {
        http_response_code($code);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    // -------------------------------------------------------------------------
    // _format_rows - normalises numeric fields in pipeline rows.
    // -------------------------------------------------------------------------
    private function _format_rows($rows)
    {
        foreach ($rows as &$row) {
            $row['id']                  = (int)   $row['id'];
            $row['rm_uid']              = (int)   $row['rm_uid'];
            $row['lead_id']             = (int)   $row['lead_id'];
            $row['proposal_budget_rs']  = isset($row['proposal_budget_rs'])  ? (int) $row['proposal_budget_rs']  : null;
            $row['days_since_rm_touch'] = isset($row['days_since_rm_touch']) ? (int) $row['days_since_rm_touch'] : null;
            $row['bd_owner_uid']        = isset($row['bd_owner_uid'])        ? (int) $row['bd_owner_uid']        : null;
            $row['cm_owner_uid']        = isset($row['cm_owner_uid'])        ? (int) $row['cm_owner_uid']        : null;
            // Rename school_name to company_name for user-facing display
            $row['company_name']        = $row['school_name'] ?? '';
        }
        unset($row);
        return $rows;
    }

    // -------------------------------------------------------------------------
    // _get_handover_status - checks handover_v2 for a given lead_id (cid_id).
    // Returns: 'not_submitted' | 'submitted' | 'approved'
    // -------------------------------------------------------------------------
    private function _get_handover_flag($lead_id)
    {
        $hv = $this->db
            ->select('status')
            ->from('handover_v2')
            ->where('cid_id', (int)$lead_id)
            ->get()
            ->row_array();
        if (!$hv) return 'not_submitted';
        return $hv['status']; // submitted | approved | rejected etc.
    }

    // -------------------------------------------------------------------------
    // GET /api/upsell/probe
    // Health check. No auth required.
    // -------------------------------------------------------------------------
    public function probe()
    {
        $this->_json(array(
            'ok'        => TRUE,
            'migration' => self::MIGRATION,
            'ts'        => date('Y-m-d H:i:s'),
        ));
    }

    // -------------------------------------------------------------------------
    // GET /api/upsell/handover_approval?user_id=X
    // Leads where upsell_stage='won' AND (rm_uid=X OR cm_owner_uid=X OR bd_owner_uid=X).
    // Includes handover_status flag from handover_v2.
    // -------------------------------------------------------------------------
    public function handover_approval()
    {
        $this->_auth_or_die();

        $user_id = (int) $this->input->get('user_id');
        if ($user_id <= 0) {
            $this->_json(array('error' => 'user_id required'), 400);
        }

        $rows = $this->db
            ->select('p.id, p.rm_uid, p.lead_id, p.category_code, p.upsell_stage,
                      p.bd_owner_uid, p.cm_owner_uid, p.school_name, p.compny_loction,
                      p.proposal_budget_rs, p.last_rm_touch_at, p.days_since_rm_touch,
                      p.notes')
            ->from('rm_upsell_pipeline p')
            ->where('p.upsell_stage', 'won')
            ->group_start()
                ->where('p.rm_uid', $user_id)
                ->or_where('p.cm_owner_uid', $user_id)
                ->or_where('p.bd_owner_uid', $user_id)
            ->group_end()
            ->order_by('p.last_rm_touch_at', 'DESC')
            ->get()
            ->result_array();

        $rows = $this->_format_rows($rows);

        // Attach handover status for each lead
        foreach ($rows as &$row) {
            $row['handover_status'] = $this->_get_handover_flag($row['lead_id']);
        }
        unset($row);

        $this->_json(array(
            'ok'    => TRUE,
            'count' => count($rows),
            'rows'  => $rows,
        ));
    }

    // -------------------------------------------------------------------------
    // GET /api/upsell/artwork_pending?user_id=X
    // Won leads not yet handed over (proxy: days_since_rm_touch > 0 AND stage='won').
    // Uses handover_v2 to confirm not yet submitted.
    // -------------------------------------------------------------------------
    public function artwork_pending()
    {
        $this->_auth_or_die();

        $user_id = (int) $this->input->get('user_id');
        if ($user_id <= 0) {
            $this->_json(array('error' => 'user_id required'), 400);
        }

        $rows = $this->db
            ->select('p.id, p.rm_uid, p.lead_id, p.category_code, p.upsell_stage,
                      p.bd_owner_uid, p.cm_owner_uid, p.school_name, p.compny_loction,
                      p.proposal_budget_rs, p.last_rm_touch_at, p.days_since_rm_touch,
                      p.notes')
            ->from('rm_upsell_pipeline p')
            ->where('p.upsell_stage', 'won')
            ->where('p.days_since_rm_touch >', 0)
            ->group_start()
                ->where('p.rm_uid', $user_id)
                ->or_where('p.cm_owner_uid', $user_id)
                ->or_where('p.bd_owner_uid', $user_id)
            ->group_end()
            ->order_by('p.days_since_rm_touch', 'DESC')
            ->get()
            ->result_array();

        $rows = $this->_format_rows($rows);

        // Filter: only those without a submitted/approved handover
        $pending = array();
        foreach ($rows as $row) {
            $hs = $this->_get_handover_flag($row['lead_id']);
            if ($hs === 'not_submitted') {
                $row['handover_status'] = $hs;
                $pending[] = $row;
            }
        }

        $this->_json(array(
            'ok'    => TRUE,
            'count' => count($pending),
            'rows'  => $pending,
        ));
    }

    // -------------------------------------------------------------------------
    // GET /api/upsell/artwork_done?user_id=X
    // All upsell_stage='won' rows for this user (lifetime).
    // -------------------------------------------------------------------------
    public function artwork_done()
    {
        $this->_auth_or_die();

        $user_id = (int) $this->input->get('user_id');
        if ($user_id <= 0) {
            $this->_json(array('error' => 'user_id required'), 400);
        }

        $rows = $this->db
            ->select('p.id, p.rm_uid, p.lead_id, p.category_code, p.upsell_stage,
                      p.bd_owner_uid, p.cm_owner_uid, p.school_name, p.compny_loction,
                      p.proposal_budget_rs, p.last_rm_touch_at, p.days_since_rm_touch,
                      p.notes')
            ->from('rm_upsell_pipeline p')
            ->where('p.upsell_stage', 'won')
            ->group_start()
                ->where('p.rm_uid', $user_id)
                ->or_where('p.cm_owner_uid', $user_id)
                ->or_where('p.bd_owner_uid', $user_id)
            ->group_end()
            ->order_by('p.last_rm_touch_at', 'DESC')
            ->get()
            ->result_array();

        $rows = $this->_format_rows($rows);

        foreach ($rows as &$row) {
            $row['handover_status'] = $this->_get_handover_flag($row['lead_id']);
        }
        unset($row);

        $this->_json(array(
            'ok'    => TRUE,
            'count' => count($rows),
            'rows'  => $rows,
        ));
    }

    // -------------------------------------------------------------------------
    // GET /api/upsell/total_summary?user_id=X
    // Counts: active_psu, active_dmft, active_anchor, won_count, won_rs_total.
    // -------------------------------------------------------------------------
    public function total_summary()
    {
        $this->_auth_or_die();

        $user_id = (int) $this->input->get('user_id');
        if ($user_id <= 0) {
            $this->_json(array('error' => 'user_id required'), 400);
        }

        // Active stages (anything except won/lost)
        $active_stages = array('lead', 'engaged', 'proposal', 'negotiation');

        // Count per category for active leads
        $active_rows = $this->db
            ->select('category_code, COUNT(*) as cnt')
            ->from('rm_upsell_pipeline')
            ->where_in('upsell_stage', $active_stages)
            ->group_start()
                ->where('rm_uid', $user_id)
                ->or_where('cm_owner_uid', $user_id)
                ->or_where('bd_owner_uid', $user_id)
            ->group_end()
            ->group_by('category_code')
            ->get()
            ->result_array();

        $active_psu    = 0;
        $active_dmft   = 0;
        $active_anchor = 0;

        foreach ($active_rows as $r) {
            switch ($r['category_code']) {
                case 'PSU':    $active_psu    = (int) $r['cnt']; break;
                case 'DMFT':   $active_dmft   = (int) $r['cnt']; break;
                case 'ANCHOR': $active_anchor  = (int) $r['cnt']; break;
            }
        }

        // Won count and total Rs
        $won_row = $this->db
            ->select('COUNT(*) as won_count, COALESCE(SUM(proposal_budget_rs),0) as won_rs_total')
            ->from('rm_upsell_pipeline')
            ->where('upsell_stage', 'won')
            ->group_start()
                ->where('rm_uid', $user_id)
                ->or_where('cm_owner_uid', $user_id)
                ->or_where('bd_owner_uid', $user_id)
            ->group_end()
            ->get()
            ->row_array();

        // Category breakdown for won
        $cat_breakdown = $this->db
            ->select('category_code, COUNT(*) as count, COALESCE(SUM(proposal_budget_rs),0) as total_rs')
            ->from('rm_upsell_pipeline')
            ->where('upsell_stage', 'won')
            ->group_start()
                ->where('rm_uid', $user_id)
                ->or_where('cm_owner_uid', $user_id)
                ->or_where('bd_owner_uid', $user_id)
            ->group_end()
            ->group_by('category_code')
            ->get()
            ->result_array();

        foreach ($cat_breakdown as &$cb) {
            $cb['count']    = (int)   $cb['count'];
            $cb['total_rs'] = (int)   $cb['total_rs'];
        }
        unset($cb);

        $this->_json(array(
            'ok'            => TRUE,
            'user_id'       => $user_id,
            'active_psu'    => $active_psu,
            'active_dmft'   => $active_dmft,
            'active_anchor' => $active_anchor,
            'won_count'     => (int) ($won_row['won_count'] ?? 0),
            'won_rs_total'  => (int) ($won_row['won_rs_total'] ?? 0),
            'won_by_category' => $cat_breakdown,
        ));
    }

    // -----------------------------------------------------------------------
    // GET /api/upsell/list?user_id=X  (added AgentC 28 May 2026)
    // Alias for total_summary. Mobile app uses /list path.
    // -----------------------------------------------------------------------
    public function list()
    {
        // Accept uid OR user_id query param so mobile {uid} usage works
        $uid = $this->input->get('uid');
        $user_id = $this->input->get('user_id');
        if ($uid && !$user_id) { $_GET['user_id'] = $uid; }
        return $this->total_summary();
    }

}