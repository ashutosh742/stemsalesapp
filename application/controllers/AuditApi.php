<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * AuditApi - Agent F, Blitz 30 May 2026
 *
 * Endpoint:
 *   GET /api/audit/field_history?table={t}&pk={id}
 *
 * Audit table situation (confirmed by SHOW TABLES):
 *   - No generic "audit_log" table exists in selfstaging_salescrm.
 *   - The closest real audit trail table is:
 *       company_log    : field-level change log for init_call rows (tracks cstatus, bd,
 *                        cluster, district, city changes). Columns: init_id, cid, old_status,
 *                        new_status, old_main_bd, new_main_bd, old_district_title,
 *                        new_district_title, update_by, created_at, etc.
 *       init_call_contact_history : contact-level field diffs for init_call rows.
 *                        Columns: cid_id, field_name, old_value, new_value, changed_by,
 *                        changed_at, reason_code, source.
 *   - For table="init_call": we merge both sources.
 *   - For table="company_master": we query company_log WHERE cid=pk.
 *   - For all other tables: we return rows=[] and document that no audit trail exists.
 *
 * Response shape per row:
 *   { audit_source, field_name, old_value, new_value, changed_by, changed_at, reason_code }
 */
class AuditApi extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    private function _bearer() {
        if (function_exists('authunify_ok') && authunify_ok()) { return true; } // rimlyproof_authunify_20260609

        @$this->config->load('custom', false, true);
        $token = $this->config->item('stem_digest_token');
        if (!$token) { $token = $this->config->item('csr_bearer_token'); }
        if (!$token) { $token = getenv('STEM_DIGEST_TOKEN'); }
        if (!$token) { $token = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo'; }
        $header = '';
        if (isset($_SERVER['HTTP_AUTHORIZATION'])) { $header = $_SERVER['HTTP_AUTHORIZATION']; }
        if (!$header && isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) { $header = $_SERVER['REDIRECT_HTTP_AUTHORIZATION']; }
        if (!$header && function_exists('getallheaders')) {
            foreach (getallheaders() as $k => $v) {
                if (strcasecmp($k, 'Authorization') === 0) { $header = $v; break; }
            }
        }
        $provided = trim(str_replace(['Bearer ', 'Bearer'], '', $header));
        if (!$token || $provided !== $token) {
            $this->output->set_status_header(401)->set_content_type('application/json')
                ->set_output(json_encode(['ok' => false, 'error' => 'unauthorized']));
            return false;
        }
        return true;
    }

    private function _json($rows, $route, $meta = []) {
        $this->output->set_content_type('application/json')->set_output(json_encode([
            'ok'           => true,
            'success'      => true,
            'stub'         => false,
            'rows'         => $rows,
            'data'         => array_merge(['count' => count($rows)], $meta),
            'route'        => $route,
            'generated_at' => date('c'),
        ]));
    }

    // -------------------------------------------------------------------------
    // GET /api/audit/field_history?table=&pk=
    // -------------------------------------------------------------------------
    public function field_history() {
        if (!$this->_bearer()) return;

        $table = $this->input->get('table', TRUE);
        $pk    = (int) $this->input->get('pk',    TRUE);

        if (!$table || !$pk) {
            $this->output->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode(['ok' => false, 'error' => 'table and pk are required']));
            return;
        }

        // Whitelist of tables with known audit sources
        $allowed_tables = [
            'init_call'      => true,
            'company_master' => true,
        ];

        if (!isset($allowed_tables[$table])) {
            // No audit trail available; document it clearly
            $this->_json([], 'api/audit/field_history', [
                'table'   => $table,
                'pk'      => $pk,
                'warning' => "No audit trail table found for '{$table}'. Available audit sources: init_call (via company_log + init_call_contact_history), company_master (via company_log). A generic audit_log table does not exist in this schema.",
            ]);
            return;
        }

        $rows = [];

        if ($table === 'init_call') {
            // Source 1: company_log (init_id = pk)
            // Flatten the wide change-log row into per-field rows
            $q1 = $this->db->query(
                "SELECT id, init_id, cid,
                        old_status, new_status,
                        old_main_bd, new_main_bd,
                        old_district_title, new_district_title,
                        old_city_title, new_city_title,
                        old_cluster_id, new_cluster_id,
                        old_in_quarter, new_in_quarter,
                        update_by, created_at
                 FROM company_log
                 WHERE init_id = ?
                 ORDER BY created_at ASC",
                [$pk]
            );
            $cl_rows = $q1->result_array();

            // Define which (old_,new_) column pairs represent field changes
            $field_pairs = [
                'cstatus'          => ['old_status',         'new_status'],
                'mainbd'           => ['old_main_bd',        'new_main_bd'],
                'district'         => ['old_district_title', 'new_district_title'],
                'city'             => ['old_city_title',     'new_city_title'],
                'cluster_id'       => ['old_cluster_id',     'new_cluster_id'],
                'in_quarter'       => ['old_in_quarter',     'new_in_quarter'],
            ];

            foreach ($cl_rows as $cl) {
                foreach ($field_pairs as $fname => [$ocol, $ncol]) {
                    $old_v = $cl[$ocol] ?? null;
                    $new_v = $cl[$ncol] ?? null;
                    // Only emit row when at least one side is non-null
                    if ($old_v !== null || $new_v !== null) {
                        $rows[] = [
                            'audit_source' => 'company_log',
                            'log_row_id'   => (int)$cl['id'],
                            'field_name'   => $fname,
                            'old_value'    => $old_v,
                            'new_value'    => $new_v,
                            'changed_by'   => $cl['update_by'],
                            'changed_at'   => $cl['created_at'],
                            'reason_code'  => null,
                        ];
                    }
                }
            }

            // Source 2: init_call_contact_history (cid_id = pk)
            $q2 = $this->db->query(
                "SELECT id, cid_id, field_name, old_value, new_value,
                        changed_by, changed_at, reason_code, source
                 FROM init_call_contact_history
                 WHERE cid_id = ?
                 ORDER BY changed_at ASC",
                [$pk]
            );
            foreach ($q2->result_array() as $cr) {
                $rows[] = [
                    'audit_source' => 'init_call_contact_history',
                    'log_row_id'   => (int)$cr['id'],
                    'field_name'   => $cr['field_name'],
                    'old_value'    => $cr['old_value'],
                    'new_value'    => $cr['new_value'],
                    'changed_by'   => (string)$cr['changed_by'],
                    'changed_at'   => $cr['changed_at'],
                    'reason_code'  => $cr['reason_code'],
                ];
            }

            // Sort merged result by changed_at ascending
            usort($rows, function($a, $b) {
                return strcmp($a['changed_at'] ?? '', $b['changed_at'] ?? '');
            });

        } elseif ($table === 'company_master') {
            // Source: company_log WHERE cid = pk
            $q = $this->db->query(
                "SELECT id, init_id, cid,
                        old_status, new_status,
                        old_main_bd, new_main_bd,
                        old_district_title, new_district_title,
                        old_city_title, new_city_title,
                        old_cluster_id, new_cluster_id,
                        update_by, created_at
                 FROM company_log
                 WHERE cid = ?
                 ORDER BY created_at ASC",
                [$pk]
            );
            $field_pairs = [
                'status'     => ['old_status',         'new_status'],
                'mainbd'     => ['old_main_bd',        'new_main_bd'],
                'district'   => ['old_district_title', 'new_district_title'],
                'city'       => ['old_city_title',     'new_city_title'],
                'cluster_id' => ['old_cluster_id',     'new_cluster_id'],
            ];
            foreach ($q->result_array() as $cl) {
                foreach ($field_pairs as $fname => [$ocol, $ncol]) {
                    $old_v = $cl[$ocol] ?? null;
                    $new_v = $cl[$ncol] ?? null;
                    if ($old_v !== null || $new_v !== null) {
                        $rows[] = [
                            'audit_source' => 'company_log',
                            'log_row_id'   => (int)$cl['id'],
                            'field_name'   => $fname,
                            'old_value'    => $old_v,
                            'new_value'    => $new_v,
                            'changed_by'   => $cl['update_by'],
                            'changed_at'   => $cl['created_at'],
                            'reason_code'  => null,
                        ];
                    }
                }
            }
        }

        $this->_json($rows, 'api/audit/field_history', [
            'table'         => $table,
            'pk'            => $pk,
            'audit_sources' => ($table === 'init_call')
                ? ['company_log (init_id)', 'init_call_contact_history (cid_id)']
                : ['company_log (cid)'],
            'schema_note'   => 'No generic audit_log table exists. company_log and init_call_contact_history are the canonical audit trail tables for this schema.',
        ]);
    }
}
