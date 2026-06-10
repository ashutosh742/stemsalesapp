<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * application/controllers/Leads_api.php
 *
 * GET /api/leads?uid=<uid>&limit=50&cstatus=<optional>
 *
 * Returns init_call rows where mainbd=uid OR creator_id=uid.
 * Joins company_master for school name, district, state.
 * Joins status for cstatus_label.
 * Optionally filters by cstatus.
 * Orders by last_event_date DESC (from tblcallevents).
 * Caps at the limit param (max 200, default 50).
 *
 * Response shape:
 *   {ok:true, count:N,
 *    leads:[{id, company_name, city, state, cstatus, cstatus_label, fbudget,
 *            last_event_date, last_event_purpose, days_in_status}]}
 *
 * Auth: Bearer 4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo
 * Plain English only. No em-dashes. No non-ASCII. Uses Rs not currency symbol.
 *
 * NOTE: Use $_GET directly for parameter reading.
 * CI3 $this->input->get() returns null (not false) for absent params on this server config,
 * which causes (null !== false) === true and then (int)null === 0 to silently filter cstatus=0.
 */
class Leads_api extends CI_Controller {

    private $_known_token = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';

    public function __construct() {
        parent::__construct();
        $this->load->database();
        header('Content-Type: application/json; charset=utf-8');
    }

    private function _bearer_ok() {
        if (function_exists('authunify_ok') && authunify_ok()) { return true; } // rimlyproof_authunify_20260609

        $hdr = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '';
        if (!$hdr && function_exists('apache_request_headers')) {
            $h = apache_request_headers();
            if (isset($h['Authorization'])) $hdr = $h['Authorization'];
        }
        if (!$hdr || stripos($hdr, 'Bearer ') !== 0) return false;
        $token = trim(substr($hdr, 7));
        $env = getenv('STEM_DIGEST_TOKEN');
        if ($env && hash_equals($env, $token)) return true;
        return hash_equals($this->_known_token, $token);
    }

    private function _json($data, $code = 200) {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }

    /**
     * GET /api/leads?uid=<uid>&limit=50&cstatus=<optional>
     */
    public function index() {
        if (!$this->_bearer_ok()) {
            $this->_json(array('ok' => false, 'error' => 'Unauthorized'), 401);
        }

        // Use $_GET directly: CI3 input->get() returns null for absent params on this server,
        // causing cstatus to incorrectly default to 0 when not provided.
        $uid = isset($_GET['uid']) ? (int)$_GET['uid'] : 0;
        if ($uid <= 0) {
            $this->_json(array('ok' => false, 'error' => 'uid param required'), 400);
        }

        $limit_raw = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
        $limit = ($limit_raw > 0 && $limit_raw <= 200) ? $limit_raw : 50;

        // Only apply cstatus filter when explicitly present in the query string
        $apply_cstatus = isset($_GET['cstatus']) && $_GET['cstatus'] !== '';
        $cstatus_val   = $apply_cstatus ? (int)$_GET['cstatus'] : 0;

        $uid_int   = (int)$uid;
        $limit_int = (int)$limit;
        $where_cs  = $apply_cstatus ? ' AND ic.cstatus = ' . (int)$cstatus_val : '';

        // Fetch leads. Avoid joining tblcallevents (714K rows) in main query.
        $sql = "
            SELECT
                ic.id,
                cm.compname   AS company_name,
                cm.district   AS city,
                cm.state      AS state,
                ic.cstatus,
                s.name        AS cstatus_label,
                ic.fbudget    AS fbudget,
                DATEDIFF(CURDATE(), ic.createDate) AS days_in_status
            FROM init_call ic
            LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
            LEFT JOIN status s ON s.id = ic.cstatus
            WHERE (ic.mainbd = {$uid_int} OR ic.creator_id = {$uid_int})
            {$where_cs}
            ORDER BY ic.id DESC
            LIMIT {$limit_int}
        ";

        $rows  = $this->db->query($sql)->result_array();
        $leads = array();
        $cid_ids = array();

        foreach ($rows as $r) {
            $cid = (int)$r['id'];
            $cid_ids[] = $cid;
            $leads[$cid] = array(
                'id'                 => $cid,
                'company_name'       => $r['company_name'] ?: '',
                'city'               => $r['city'] ?: '',
                'state'              => $r['state'] ?: '',
                'cstatus'            => (int)$r['cstatus'],
                'cstatus_label'      => $r['cstatus_label'] ?: '',
                'fbudget'            => $r['fbudget'] ?: '',
                'last_event_date'    => null,
                'last_event_purpose' => '',
                'days_in_status'     => (int)$r['days_in_status'],
            );
        }

        // Enrich with last event date (tblcallevents.cid_id is indexed).
        if (!empty($cid_ids)) {
            $id_list = implode(',', $cid_ids);

            $ev_rows = $this->db->query(
                "SELECT t.cid_id, MAX(t.appointmentdatetime) AS last_event_date
                 FROM tblcallevents t
                 WHERE t.cid_id IN ({$id_list})
                 GROUP BY t.cid_id"
            )->result_array();
            foreach ($ev_rows as $ev) {
                $cid = (int)$ev['cid_id'];
                if (isset($leads[$cid])) {
                    $leads[$cid]['last_event_date'] = $ev['last_event_date'];
                }
            }

            // Last event purpose from most recent event per lead
            $pr_rows = $this->db->query(
                "SELECT t.cid_id, p.name AS purpose_name
                 FROM tblcallevents t
                 LEFT JOIN purpose p ON p.id = t.purpose_id
                 WHERE t.cid_id IN ({$id_list})
                   AND t.appointmentdatetime = (
                       SELECT MAX(t2.appointmentdatetime)
                       FROM tblcallevents t2
                       WHERE t2.cid_id = t.cid_id
                   )
                 GROUP BY t.cid_id"
            )->result_array();
            foreach ($pr_rows as $pr) {
                $cid = (int)$pr['cid_id'];
                if (isset($leads[$cid])) {
                    $leads[$cid]['last_event_purpose'] = $pr['purpose_name'] ?: '';
                }
            }
        }

        // Sort by last_event_date DESC (nulls last)
        $out = array_values($leads);
        usort($out, function($a, $b) {
            if ($a['last_event_date'] === $b['last_event_date']) return 0;
            if ($a['last_event_date'] === null) return 1;
            if ($b['last_event_date'] === null) return -1;
            return strcmp($b['last_event_date'], $a['last_event_date']);
        });

        $this->_json(array('ok' => true, 'count' => count($out), 'leads' => $out));
    }
}
