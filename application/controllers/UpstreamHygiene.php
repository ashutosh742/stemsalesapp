<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * UpstreamHygiene controller - Migration 028
 *
 * Replaces the stub. Implements real queries for:
 *   stagnant_open_45, stagnant_reachout_30, wallet_triggers
 *
 * All endpoints require Bearer token auth.
 * Returns JSON: {ok:true, rows:[...], count:N, source:'live'}
 */
class UpstreamHygiene extends CI_Controller {

    const BEARER_TOKEN = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
    const MIGRATION    = '028';

    public function __construct() {
        parent::__construct();
        header('Content-Type: application/json');
    }

    // ---------------------------------------------------------------
    // AUTH HELPER
    // ---------------------------------------------------------------
    private function _check_auth() {
        if (function_exists('authunify_ok') && authunify_ok()) { return true; } // rimlyproof_authunify_20260609

        $headers = getallheaders();
        $auth    = isset($headers['Authorization']) ? $headers['Authorization'] : '';
        if (empty($auth) && function_exists('apache_request_headers')) {
            $ah   = apache_request_headers();
            $auth = isset($ah['Authorization']) ? $ah['Authorization'] : '';
        }
        if (empty($auth)) {
            // Try HTTP_AUTHORIZATION
            $auth = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '';
        }
        if (stripos($auth, 'bearer ') === 0) {
            $token = trim(substr($auth, 7));
            if ($token === self::BEARER_TOKEN) {
                return true;
            }
        }
        http_response_code(401);
        echo json_encode(['ok' => false, 'error' => 'unauthorized']);
        return false;
    }

    // ---------------------------------------------------------------
    // PROBE
    // ---------------------------------------------------------------
    public function probe() {
        echo json_encode([
            'ok'         => true,
            'controller' => 'UpstreamHygiene',
            'migration'  => self::MIGRATION,
            'deployed'   => true,
            'source'     => 'live',
        ]);
    }

    // ---------------------------------------------------------------
    // STAGNANT OPEN 45 (cstatus = 1)
    // ---------------------------------------------------------------
    public function stagnant_open_45() {
        if (!$this->_check_auth()) return;

        $days_threshold = (int) $this->input->get('days_threshold');
        if ($days_threshold <= 0) $days_threshold = 45;

        // Build query: for each init_call in cstatus=1 with a BD assigned,
        // find the last tblcallevents touch. Return leads where days since
        // last touch is >= days_threshold. Cap at 50 rows ordered by
        // days_stagnant DESC.
        $sql = "
            SELECT
                ic.id                                    AS cid_id,
                u.name                                   AS bd_name,
                COALESCE(cm.compname, 'Unknown')         AS school,
                DATEDIFF(NOW(), lt.last_date)            AS days_stagnant,
                DATE(lt.last_date)                       AS last_touch
            FROM init_call ic
            JOIN user u ON u.uid = ic.mainbd
            LEFT JOIN company_master cm ON cm.id = CAST(ic.clm_id AS UNSIGNED)
            JOIN (
                SELECT cid_id, MAX(date) AS last_date
                FROM tblcallevents
                GROUP BY cid_id
            ) lt ON lt.cid_id = ic.id
            WHERE ic.cstatus = 1
              AND ic.mainbd > 0
              AND DATEDIFF(NOW(), lt.last_date) >= ?
            ORDER BY days_stagnant DESC
            LIMIT 50
        ";

        $result = $this->db->query($sql, [$days_threshold]);

        if (!$result) {
            http_response_code(500);
            echo json_encode(['ok' => false, 'error' => 'query_failed', 'source' => 'live']);
            return;
        }

        $rows = $result->result_array();
        $count = count($rows);

        $response = [
            'ok'             => true,
            'rows'           => $rows,
            'count'          => $count,
            'source'         => 'live',
            'days_threshold' => $days_threshold,
            'cstatus'        => 1,
        ];

        if ($count === 0) {
            $response['note'] = 'no_stagnant_leads';
        }

        echo json_encode($response);
    }

    // ---------------------------------------------------------------
    // STAGNANT REACHOUT 30 (cstatus = 2)
    // ---------------------------------------------------------------
    public function stagnant_reachout_30() {
        if (!$this->_check_auth()) return;

        $days_threshold = (int) $this->input->get('days_threshold');
        if ($days_threshold <= 0) $days_threshold = 30;

        // For reachout (cstatus=2) the spec says qualifying touch is
        // actiontype_id IN (1, 2, 10) - phone, email, WhatsApp.
        // We join tblcallevents filtered to those actiontype_ids.
        $sql = "
            SELECT
                ic.id                                    AS cid_id,
                u.name                                   AS bd_name,
                COALESCE(cm.compname, 'Unknown')         AS school,
                DATEDIFF(NOW(), lt.last_date)            AS days_stagnant,
                DATE(lt.last_date)                       AS last_touch
            FROM init_call ic
            JOIN user u ON u.uid = ic.mainbd
            LEFT JOIN company_master cm ON cm.id = CAST(ic.clm_id AS UNSIGNED)
            JOIN (
                SELECT cid_id, MAX(date) AS last_date
                FROM tblcallevents
                WHERE actiontype_id IN (1, 2, 10)
                GROUP BY cid_id
            ) lt ON lt.cid_id = ic.id
            WHERE ic.cstatus = 2
              AND ic.mainbd > 0
              AND DATEDIFF(NOW(), lt.last_date) >= ?
            ORDER BY days_stagnant DESC
            LIMIT 50
        ";

        $result = $this->db->query($sql, [$days_threshold]);

        if (!$result) {
            http_response_code(500);
            echo json_encode(['ok' => false, 'error' => 'query_failed', 'source' => 'live']);
            return;
        }

        $rows = $result->result_array();
        $count = count($rows);

        $response = [
            'ok'             => true,
            'rows'           => $rows,
            'count'          => $count,
            'source'         => 'live',
            'days_threshold' => $days_threshold,
            'cstatus'        => 2,
        ];

        if ($count === 0) {
            $response['note'] = 'no_stagnant_leads';
        }

        echo json_encode($response);
    }

    // ---------------------------------------------------------------
    // WALLET TRIGGERS
    // ---------------------------------------------------------------
    public function wallet_triggers() {
        if (!$this->_check_auth()) return;

        $days = (int) $this->input->get('days');
        if ($days <= 0) $days = 7;

        $sql = "
            SELECT
                id,
                bd_uid,
                lead_id,
                reason,
                amount_rs,
                triggered_at
            FROM wallet_trigger_log
            WHERE triggered_at >= NOW() - INTERVAL ? DAY
            ORDER BY triggered_at DESC
            LIMIT 200
        ";

        $result = $this->db->query($sql, [$days]);

        if (!$result) {
            http_response_code(500);
            echo json_encode(['ok' => false, 'error' => 'query_failed', 'source' => 'live']);
            return;
        }

        $rows  = $result->result_array();
        $count = count($rows);

        $total_rs = 0;
        foreach ($rows as $row) {
            $total_rs += (float) $row['amount_rs'];
        }

        $response = [
            'ok'       => true,
            'rows'     => $rows,
            'count'    => $count,
            'total_rs' => $total_rs,
            'source'   => 'live',
            'days'     => $days,
        ];

        if ($count === 0) {
            $response['note'] = 'no_wallet_triggers';
        }

        echo json_encode($response);
    }
}
