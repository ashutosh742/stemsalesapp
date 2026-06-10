<?php
/**
 * WalletApi
 *
 * GET /api/wallet/balance?uid={uid}
 *   Returns net wallet balance: SUM of all credit-type rows minus SUM of all debit-type rows
 *   from cash_log for the given uid.
 *   cash_log.type values observed: 'debit', 'Deduct', 'credit', 'Credit' (case-insensitive).
 *   We treat LIKE 'deduct%' OR = 'debit' as debits; everything else as credits.
 *
 * GET /api/wallet/history?uid={uid}&limit=20
 *   Returns the last N cash_log rows for the uid (default 20, max 100).
 *
 * Agent E, Blitz 30 May 2026
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class WalletApi extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // -------------------------------------------------------------------------
    private function _bearer() {
        if (function_exists('authunify_ok') && authunify_ok()) { return true; } // rimlyproof_authunify_20260609

        $h = $this->input->get_request_header('Authorization', TRUE);
        if (!$h || stripos($h, 'Bearer ') !== 0) {
            $this->_json(['ok' => false, 'error' => 'unauthorized'], 401);
            return false;
        }
        $tok      = trim(substr($h, 7));
        $expected = trim(@file_get_contents(APPPATH . 'config/digest_token.txt'));
        if (!$expected || !hash_equals($expected, $tok)) {
            $this->_json(['ok' => false, 'error' => 'bad_token'], 401);
            return false;
        }
        return true;
    }

    // -------------------------------------------------------------------------
    // GET /api/wallet/balance?uid={uid}
    // -------------------------------------------------------------------------
    public function balance_index() {
        if (!$this->_bearer()) return;

        $uid = (int) $this->input->get('uid');
        if ($uid <= 0) {
            $this->_json(['ok' => false, 'error' => 'uid is required and must be a positive integer'], 400);
            return;
        }

        // cash_log.type is varchar(100); real values include 'Deduct', 'debit', 'Credit', 'credit'.
        // We classify: if LOWER(type) LIKE 'deduct%' OR LOWER(type) = 'debit' => debit, else => credit.
        $sql = "
            SELECT
                COALESCE(SUM(CASE
                    WHEN LOWER(type) LIKE 'deduct%' OR LOWER(type) = 'debit'
                    THEN ABS(cash)
                    ELSE 0
                END), 0) AS total_debits,
                COALESCE(SUM(CASE
                    WHEN NOT (LOWER(type) LIKE 'deduct%' OR LOWER(type) = 'debit')
                    THEN ABS(cash)
                    ELSE 0
                END), 0) AS total_credits,
                COUNT(*)  AS total_rows
            FROM cash_log
            WHERE uid = ?
        ";

        $row = $this->db->query($sql, [$uid])->row_array();

        $credits = (int) ($row['total_credits'] ?? 0);
        $debits  = (int) ($row['total_debits']  ?? 0);
        $balance = $credits - $debits;

        $this->_json([
            'ok'           => true,
            'success'      => true,
            'stub'         => false,
            'rows'         => [],
            'data'         => [
                'uid'           => $uid,
                'balance'       => $balance,
                'total_credits' => $credits,
                'total_debits'  => $debits,
                'total_rows'    => (int) ($row['total_rows'] ?? 0),
            ],
            'route'        => 'api/wallet/balance',
            'generated_at' => date('c'),
        ]);
    }

    // -------------------------------------------------------------------------
    // GET /api/wallet/history?uid={uid}&limit=20
    // -------------------------------------------------------------------------
    public function history_index() {
        if (!$this->_bearer()) return;

        $uid   = (int) $this->input->get('uid');
        $limit = (int) $this->input->get('limit');
        if ($uid <= 0) {
            $this->_json(['ok' => false, 'error' => 'uid is required and must be a positive integer'], 400);
            return;
        }
        if ($limit <= 0 || $limit > 100) $limit = 20;

        $sql = "
            SELECT
                id,
                uid,
                cash        AS amount,
                av_cash     AS available_cash_after,
                type,
                remarks,
                task_id,
                created_at  AS date
            FROM cash_log
            WHERE uid = ?
            ORDER BY created_at DESC, id DESC
            LIMIT ?
        ";

        $rows = $this->db->query($sql, [$uid, $limit])->result_array();

        $this->_json([
            'ok'           => true,
            'success'      => true,
            'stub'         => false,
            'rows'         => $rows,
            'data'         => ['count' => count($rows), 'uid' => $uid, 'limit' => $limit],
            'route'        => 'api/wallet/history',
            'generated_at' => date('c'),
        ]);
    }

    // -------------------------------------------------------------------------
    private function _json($payload, $status = 200) {
        $this->output
             ->set_status_header($status)
             ->set_content_type('application/json')
             ->set_output(json_encode($payload));
    }
}
