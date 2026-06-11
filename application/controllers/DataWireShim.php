<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * DataWireShim.php  -  2026-06-04
 *
 * Adapter shim for mobile v2.11.0 endpoints that the app hits but were never
 * properly wired. Adapts the parameter shape and forwards to the real working
 * backend logic in Leads_api and LeadDetailController.
 *
 * NEW endpoints (additive, no production impact):
 *   GET /api/leads/list?bd_uid=X[&limit=50]      -> uses Leads_api SQL with uid=bd_uid
 *   GET /api/leads/detail?lead_id=X              -> forwards to LeadDetailController::index(X)
 *
 * Auth: Bearer 4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo (same as Leads_api).
 * Returns JSON. Plain English. No em-dashes. Rs for rupees.
 */

class DataWireShim extends CI_Controller {

    private $_known_token = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';

    public function __construct() {
        parent::__construct();
        $this->load->database();
        header('Content-Type: application/json; charset=utf-8');
    }

    private function _bearer_ok() {
        $hdr = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '';
        if (!$hdr && function_exists('apache_request_headers')) {
            $h = apache_request_headers();
            if (isset($h['Authorization'])) $hdr = $h['Authorization'];
        }
        if (!$hdr || stripos($hdr, 'Bearer ') !== 0) return false;
        $token = trim(substr($hdr, 7));
        $env = getenv('STEM_DIGEST_TOKEN');
        if ($env && hash_equals($env, $token)) return true;
        if (hash_equals($this->_known_token, $token)) return true;
        // AUTH 2026-06-06: accept per-user JWT via BearerAuth::resolve()
        $this->load->library('BearerAuth');
        $auth = $this->bearerauth->resolve();
        return !empty($auth['ok']);
    }

    private function _json($data, $code = 200) {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }

    /**
     * GET /api/leads/list?bd_uid=X[&uid=X][&limit=50][&cstatus=N]
     *
     * Reads bd_uid OR uid (whichever present), runs the same SQL as Leads_api::index,
     * returns the same shape the mobile LeadsScreen expects:
     *   { ok, count, data: [ ...leads ], leads: [...same...] }
     */
    public function leads_list() {
        if (!$this->_bearer_ok()) {
            $this->_json(array('ok' => false, 'error' => 'Unauthorized'), 401);
        }

        $bd_uid = isset($_GET['bd_uid']) ? (int)$_GET['bd_uid'] : 0;
        $uid    = isset($_GET['uid'])    ? (int)$_GET['uid']    : 0;
        $requested = $bd_uid > 0 ? $bd_uid : $uid;
        // rimlyproof_leadscope_20260609: a BD/ACM is HARD-LOCKED to their own
        // leads regardless of any bd_uid/uid param. Managers/system honour request.
        $eff = authunify_lead_scope_uid($requested);

        if ($eff <= 0) {
            $this->_json(array(
                'ok' => true,
                'count' => 0,
                'leads' => array(),
                'data'  => array(),
                'note'  => 'no_uid_provided'
            ));
        }

        $limit_raw = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
        $limit = ($limit_raw > 0 && $limit_raw <= 200) ? $limit_raw : 50;

        $apply_cstatus = isset($_GET['cstatus']) && $_GET['cstatus'] !== '';
        $cstatus_val   = $apply_cstatus ? (int)$_GET['cstatus'] : 0;

        $uid_int   = (int)$eff;
        $limit_int = (int)$limit;
        $where_cs  = $apply_cstatus ? ' AND ic.cstatus = ' . (int)$cstatus_val : '';

        // v2150 (B3) lead-scoring fields are derived with correlated subqueries
        // that degrade to 0/empty when no source row exists (never a 500):
        //   days_in_stage     = days since the latest lead_progression_log entry
        //                       for this lead (falls back to days_in_status).
        //   days_since_action = days since the latest tblcallevents action.
        //   contacts          = number of company_contact_master rows.
        $sql = "
            SELECT
                ic.id,
                cm.compname   AS company,
                cm.compname   AS company_name,
                cm.district   AS city,
                cm.state      AS state,
                ic.cstatus,
                s.name        AS cstatus_label,
                s.name        AS stage,
                ic.fbudget    AS fbudget,
                DATEDIFF(CURDATE(), ic.createDate) AS days_in_status,
                (SELECT DATEDIFF(CURDATE(), MAX(lpl.created_at))
                   FROM lead_progression_log lpl WHERE lpl.lead_id = ic.id) AS days_in_stage_raw,
                (SELECT DATEDIFF(CURDATE(), MAX(tce.date))
                   FROM tblcallevents tce WHERE tce.cid_id = ic.id) AS days_since_action_raw,
                (SELECT COUNT(*) FROM company_contact_master ccm
                   WHERE ccm.company_id = ic.cmpid_id) AS contacts_raw,
                ic.mainbd     AS bd_uid
            FROM init_call ic
            LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
            LEFT JOIN status s ON s.id = ic.cstatus
            WHERE (ic.mainbd = {$uid_int} OR ic.creator_id = {$uid_int})
            {$where_cs}
            ORDER BY ic.id DESC
            LIMIT {$limit_int}
        ";

        $rows = $this->db->query($sql)->result_array();

        $out = array();
        foreach ($rows as $r) {
            $cstatus_int = (int)$r['cstatus'];
            // budget: parse the first numeric run out of the free-text fbudget.
            $budget = 0;
            if (isset($r['fbudget']) && $r['fbudget'] !== '') {
                $digits = preg_replace('/[^0-9]/', '', (string)$r['fbudget']);
                $budget = ($digits === '') ? 0 : (int)$digits;
            }
            // days_in_stage: prefer the progression-log delta, else the
            // create-date delta (days_in_status), else 0.
            if ($r['days_in_stage_raw'] !== null) {
                $days_in_stage = (int)$r['days_in_stage_raw'];
            } else {
                $days_in_stage = (int)$r['days_in_status'];
            }
            $days_since_action = ($r['days_since_action_raw'] !== null) ? (int)$r['days_since_action_raw'] : 0;
            $out[] = array(
                'id'                => (int)$r['id'],
                'lead_id'           => (int)$r['id'],
                'company'           => $r['company']      ?: 'Unknown',
                'company_name'      => $r['company_name'] ?: 'Unknown',
                'city'              => $r['city']         ?: '',
                'state'             => $r['state']        ?: '',
                'cstatus'           => $cstatus_int,
                'cstatus_label'     => $r['cstatus_label'] ?: '',
                'stage'             => $r['stage']         ?: '',
                'fbudget'           => $r['fbudget']       ?: '',
                'days_in_status'    => (int)$r['days_in_status'],
                'bd_uid'            => (int)$r['bd_uid'],
                // v2150 (B3) additive lead-scoring fields
                'days_in_stage'     => $days_in_stage,
                'days_since_action' => $days_since_action,
                'budget'            => $budget,
                'contacts'          => (int)$r['contacts_raw'],
                'has_proposal'      => ($cstatus_int >= 7),
            );
        }

        $this->_json(array(
            'ok'         => true,
            'success'    => true,
            'stub'       => false,
            'count'      => count($out),
            'uid'        => $eff,
            'limit'      => $limit_int,
            'leads'      => $out,
            'rows'       => $out,
            'data'       => $out,
            'route'      => 'api/leads/list',
            'generated_at' => gmdate('c')
        ));
    }

    /**
     * GET /api/leads/detail?lead_id=X
     *
     * Forwards to LeadDetailController which expects /api/lead/detail/{cid_id}.
     * Reads lead_id, then internally instantiates and calls the existing controller.
     */
    public function leads_detail() {
        if (!$this->_bearer_ok()) {
            $this->_json(array('ok' => false, 'error' => 'Unauthorized'), 401);
        }

        $lead_id = isset($_GET['lead_id']) ? (int)$_GET['lead_id'] : 0;
        if ($lead_id <= 0) {
            $lead_id = isset($_GET['cid_id']) ? (int)$_GET['cid_id'] : 0;
        }
        if ($lead_id <= 0) {
            $this->_json(array('ok' => false, 'error' => 'lead_id param required'), 400);
        }

        // Pull the same payload LeadDetailController produces via direct SQL
        // (avoid double-instantiating a CI controller which is messy).
        $row = $this->db->query("
            SELECT
                ic.id AS cid_id,
                ic.cstatus,
                s.name AS cstatus_label,
                ic.fbudget,
                ic.mainbd,
                ic.creator_id,
                ic.clm_id,
                ic.insidebd,
                ic.createDate,
                ic.created_at,
                ic.updated_at,
                cm.compname  AS company_name,
                cm.district  AS city,
                cm.state     AS state,
                cm.address   AS address
            FROM init_call ic
            LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
            LEFT JOIN status s ON s.id = ic.cstatus
            WHERE ic.id = " . (int)$lead_id . "
            LIMIT 1
        ")->row_array();

        if (!$row) {
            $this->_json(array(
                'ok'    => true,
                'found' => false,
                'lead'  => null,
                'note'  => 'lead_not_found'
            ));
        }

        // rimlyproof_leadscope_20260609: a field user (BD/ACM) may only open a
        // lead they own or created. Managers/system see all. Prevents viewing
        // another BD's lead detail by guessing/iterating lead_id.
        $owner_uid   = isset($row['mainbd'])     ? (int)$row['mainbd']     : 0;
        $creator_uid = isset($row['creator_id']) ? (int)$row['creator_id'] : 0;
        if (!authunify_lead_can_view($owner_uid, $creator_uid)) {
            $this->_json(array(
                'ok'    => false,
                'error' => 'forbidden',
                'note'  => 'lead_not_in_your_scope'
            ), 403);
        }

        // Recent events for this lead (cap 10 most recent)
        $events = $this->db->query("
            SELECT
                id, user_id, actiontype_id, purpose_id, status_id,
                date AS event_date, appointmentdatetime, mom, remarks
            FROM tblcallevents
            WHERE cid_id = " . (int)$lead_id . "
            ORDER BY id DESC
            LIMIT 10
        ")->result_array();

        $this->_json(array(
            'ok'      => true,
            'success' => true,
            'stub'    => false,
            'found'   => true,
            'lead'    => $row,
            'data'    => $row,
            'events'  => $events,
            'event_count' => count($events),
            'route'   => 'api/leads/detail',
            'generated_at' => gmdate('c')
        ));
    }
}
