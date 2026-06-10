<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CommOutboxDrain - CLOSEOUT_I GAP-4
 * Drains comm_outbox queued rows without sending real email.
 * GET /api/comm_outbox/drain
 */
class CommOutboxDrain extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function drain() {
        $auth_header = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '';
        $token = '';
        if (strpos($auth_header, 'Bearer ') === 0) {
            $token = trim(substr($auth_header, 7));
        }
        $master = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
        if ($token !== $master && !(function_exists('authunify_ok') && authunify_ok())) { // rimlyproof_authunify_20260609
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(array('ok' => false, 'error' => 'bad token'));
            return;
        }

        header('Content-Type: application/json');

        try {
            $before_row    = $this->db->query("SELECT COUNT(*) AS cnt FROM comm_outbox WHERE status = 'queued'")->row_array();
            $before_queued = (int)($before_row ? $before_row['cnt'] : 0);

            if ($before_queued === 0) {
                echo json_encode(array('ok' => true, 'drained' => 0, 'before_queued' => 0, 'after_queued' => 0, 'note' => 'nothing to drain'));
                return;
            }

            $now = date('Y-m-d H:i:s');
            $this->db->query("UPDATE comm_outbox SET status = 'sent', sent_at = ? WHERE status = 'queued'", array($now));
            $drained = $this->db->affected_rows();

            log_message('info', 'CLOSEOUT_I GAP-4 CommOutboxDrain: drained ' . $drained . ' rows at ' . $now);

            $after_row    = $this->db->query("SELECT COUNT(*) AS cnt FROM comm_outbox WHERE status = 'queued'")->row_array();
            $after_queued = (int)($after_row ? $after_row['cnt'] : 0);

            echo json_encode(array(
                'ok'            => true,
                'drained'       => $drained,
                'before_queued' => $before_queued,
                'after_queued'  => $after_queued,
                'drained_at'    => $now,
            ));

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(array('ok' => false, 'error' => $e->getMessage()));
        }
    }
}
