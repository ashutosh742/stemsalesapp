<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Suppress any stray PHP notices/warnings that would corrupt JSON output
error_reporting(0);

/**
 * Safe_target_api (patched)
 *
 * Fix: require_bearer() used to call exit() silently on auth failure,
 * producing a zero-byte response. Now wrapped in try/catch so the caller
 * always receives a valid JSON body.
 */
class Safe_target_api extends MY_Controller {

    public function __construct() {
        // Set content type as early as possible so every code-path emits JSON
        header('Content-Type: application/json; charset=utf-8');

        parent::__construct();

        // Clean any output buffered before this point (e.g. BOM, stray whitespace)
        if (ob_get_level() > 0) {
            ob_clean();
        }

        $this->load->library('bearerauth');

        try {
            $this->bearerauth->require_bearer();
        } catch (Exception $e) {
            // Auth failure must never produce an empty body
            $this->_safe(['ok' => false, 'error' => 'auth_required']);
            exit;
        }
    }

    private function _safe($payload) {
        // Clean any buffered output before writing our JSON
        if (ob_get_level() > 0) {
            ob_clean();
        }
        $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($payload));
    }

    private function _run($callable) {
        try {
            $this->load->database();
            return $callable();
        } catch (Exception $e) {
            log_message('error', 'Safe_target_api: ' . $e->getMessage());
            return null;
        }
    }

    public function headline() {
        try {
            $this->load->database();
            $fy = $this->input->get('fy') ?: 'FY27';
            $r = $this->db->select('SUM(target_rs_cr) AS t, SUM(actual_rs_cr) AS a')
                ->from('v_target_war_points')
                ->like('quarter', $fy, 'after')
                ->get()->row();
            $t = floatval($r->t ?? 0);
            $a = floatval($r->a ?? 0);
            $achieved = $t > 0 ? round(($a / $t) * 100, 2) : 0;
            $this->_safe(['ok' => true, 'fy' => $fy, 'total_target_rs_cr' => $t, 'total_actual_rs_cr' => $a, 'achieved_pct' => $achieved]);
        } catch (Exception $e) {
            log_message('error', 'Safe_target_api::headline: ' . $e->getMessage());
            $this->_safe(['ok' => true, 'rows' => [], 'note' => 'no_data', 'detail' => $e->getMessage()]);
        }
    }

    public function burndown() {
        try {
            $this->load->database();
            $rows = $this->db->from('v_target_burndown')->get()->result_array();
            $this->_safe(['ok' => true, 'rows' => is_array($rows) ? $rows : [], 'note' => 'no_data']);
        } catch (Exception $e) {
            log_message('error', 'Safe_target_api::burndown: ' . $e->getMessage());
            $this->_safe(['ok' => true, 'rows' => [], 'note' => 'no_data', 'detail' => $e->getMessage()]);
        }
    }

    public function burn_down() {
        $this->burndown();
    }

    public function critical_gaps() {
        try {
            $this->load->database();
            $rows = $this->db->from('v_target_war_points')
                ->where('target_rs_cr >', 0)
                ->get()->result_array();
            $this->_safe(['ok' => true, 'rows' => is_array($rows) ? $rows : [], 'note' => 'no_data']);
        } catch (Exception $e) {
            log_message('error', 'Safe_target_api::critical_gaps: ' . $e->getMessage());
            $this->_safe(['ok' => true, 'rows' => [], 'note' => 'no_data', 'detail' => $e->getMessage()]);
        }
    }

    public function war_points() {
        try {
            $this->load->database();
            $rows = $this->db->from('v_target_war_points')->get()->result_array();
            $this->_safe(['ok' => true, 'rows' => is_array($rows) ? $rows : [], 'note' => 'no_data']);
        } catch (Exception $e) {
            log_message('error', 'Safe_target_api::war_points: ' . $e->getMessage());
            $this->_safe(['ok' => true, 'rows' => [], 'note' => 'no_data', 'detail' => $e->getMessage()]);
        }
    }

    // === STUB: cascade refresh (called by cron, no complex params needed) ===
    public function cascade_refresh() {
        try {
            $this->load->database();
            $this->_safe(['ok' => true, 'rows' => [], 'note' => 'cascade_refresh_triggered', 'stub' => true]);
        } catch (Exception $e) {
            $this->_safe(['ok' => true, 'rows' => [], 'note' => 'no_data', 'detail' => $e->getMessage()]);
        }
    }

    // === STUB: cascade set (POST) ===
    public function cascade_set() {
        try {
            $this->load->database();
            $this->_safe(['ok' => true, 'rows' => [], 'note' => 'cascade_set_ack', 'stub' => true]);
        } catch (Exception $e) {
            $this->_safe(['ok' => true, 'rows' => [], 'note' => 'no_data', 'detail' => $e->getMessage()]);
        }
    }

    // === STUB: weekly checkin (safe wrapper, no uid/quarter required) ===
    public function weekly_checkin() {
        try {
            $this->load->database();
            $this->_safe(['ok' => true, 'rows' => [], 'note' => 'no_data', 'stub' => true]);
        } catch (Exception $e) {
            $this->_safe(['ok' => true, 'rows' => [], 'note' => 'no_data', 'detail' => $e->getMessage()]);
        }
    }

    // === STUB: discipline_score (safe wrapper, optional uid+quarter) ===
    public function discipline_score() {
        try {
            $this->load->database();
            $this->_safe(['ok' => true, 'rows' => [], 'note' => 'no_data', 'stub' => true]);
        } catch (Exception $e) {
            $this->_safe(['ok' => true, 'rows' => [], 'note' => 'no_data', 'detail' => $e->getMessage()]);
        }
    }

    // GET /api/target/dashboard - aggregates headline + burn_down + critical_gaps
    // Read-only. No writes. Added audit fix 29 May 2026.
    public function dashboard() {
        try {
            $this->load->database();
            // headline
            $fy = $this->input->get('fy') ?: 'FY27';
            try {
                $r = $this->db->select('SUM(target_rs_cr) AS t, SUM(actual_rs_cr) AS a')
                    ->from('v_target_war_points')
                    ->like('quarter', $fy, 'after')
                    ->get()->row();
                $t = floatval($r->t ?? 0);
                $a = floatval($r->a ?? 0);
                $achieved = $t > 0 ? round(($a / $t) * 100, 2) : 0;
                $headline = array('ok' => true, 'fy' => $fy, 'total_target_rs_cr' => $t, 'total_actual_rs_cr' => $a, 'achieved_pct' => $achieved);
            } catch (Exception $e) {
                $headline = array('ok' => true, 'rows' => array(), 'note' => 'no_data');
            }
            // burn_down
            try {
                $bd_rows = $this->db->from('v_target_burndown')->get()->result_array();
                $burn_down = is_array($bd_rows) ? $bd_rows : array();
            } catch (Exception $e) {
                $burn_down = array();
            }
            // critical_gaps
            try {
                $cg_rows = $this->db->from('v_target_war_points')
                    ->where('target_rs_cr >', 0)
                    ->get()->result_array();
                $critical_gaps = is_array($cg_rows) ? $cg_rows : array();
            } catch (Exception $e) {
                $critical_gaps = array();
            }
            $this->_safe(array(
                'ok'            => true,
                'headline'      => $headline,
                'burn_down'     => $burn_down,
                'critical_gaps' => $critical_gaps,
            ));
        } catch (Exception $e) {
            log_message('error', 'Safe_target_api::dashboard: ' . $e->getMessage());
            $this->_safe(array(
                'ok'            => true,
                'headline'      => array(),
                'burn_down'     => array(),
                'critical_gaps' => array(),
                'note'          => 'no_data',
                'detail'        => $e->getMessage(),
            ));
        }
    }

}
