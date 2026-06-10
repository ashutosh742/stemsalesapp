<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Planner v2 Purpose Cascade Endpoint - Production Parity
 *
 * Single unified endpoint that consolidates all 5 production cascade methods
 * and the 3 selectby branches into one signature, with the Fresh Meeting
 * fallback preserved.
 *
 * Production methods this mirrors:
 *   - Menu::purpose                  Menu.php line 14173 (single id lookup)
 *   - Menu::getpurpose               Menu.php line 14183 (action plus status)
 *   - Menu::getpurposebyinid         Menu.php line 14195 (action plus init_call)
 *   - Menu::getpurposebyinidnew      Menu.php line 14206 (3 selectby branches)
 *   - Menu::getPurposeByAction       Menu.php line 19777 (duplicate of above)
 *
 * Model methods this calls (all already in production):
 *   - Menu_model::get_purposebyid               line 2513
 *   - Menu_model::get_purposebya                line 2460 (has Barge rewrite quirk)
 *   - Menu_model::get_purposebyinid             line 2480
 *   - Menu_model::get_purposebyinidnew          line 2487
 *   - Menu_model::GetPurposeNameByActionId      line 2475
 *   - Menu_model::GetPurposeByActionAndStatusID line 20333
 *
 * NO new tables. NO new migration. NO production writes.
 *
 * Mount points:
 *   GET  /Menu/getpurposes_v2                       (web, session auth)
 *   GET  /api/planner/v2/purposes_v2                (mobile, bearer auth)
 *
 * Query params:
 *   action_id              int      required
 *   inid                   string   optional (int or comma-separated init_call ids)
 *   selectby               string   optional ('Next Follow Up Date' | 'Call On School' | default)
 *   cstatus                int      optional (used when no inid)
 *   apply_barge_rewrite    0|1      optional (default 0; Day Plan web sends 1)
 *
 * Response:
 *   {
 *     status: 'ok' | 'error',
 *     rows: [{id, name, action_id, status_id}],
 *     fallback_used: bool,
 *     branch: 'next_follow_up_date' | 'call_on_school' | 'default' | 'action_status' | 'action_only',
 *     barge_rewritten: bool,
 *     resolved_inid: string|null
 *   }
 *
 * Auth:
 *   /Menu/getpurposes_v2 reuses MY_Controller session check (same as the
 *   other Menu/* cascade methods).
 *   /api/planner/v2/purposes_v2 reuses stem_bearer_auth library, same as the
 *   rest of the planner v2 API surface.
 */

/* ---------------------------------------------------------------------------
 * 1) Web endpoint - drop into application/controllers/Menu.php
 * ------------------------------------------------------------------------- */

class Menu_PlannerV2_CascadePatch extends MY_Controller {

    /**
     * Drop this method into the existing Menu controller next to the other
     * cascade endpoints (around line 14250).
     *
     * Production parity:
     *   - Mirrors all 3 selectby branches of getpurposebyinidnew()
     *   - Mirrors the Barge rewrite quirk of get_purposebya() when
     *     apply_barge_rewrite=1
     *   - Returns Fresh Meeting (purpose id 34) as the empty fallback
     */
    public function getpurposes_v2() {
        $this->load->model('Menu_model');

        $action_id           = isset($_GET['action_id']) ? intval($_GET['action_id']) : 0;
        $inid_raw            = isset($_GET['inid']) ? trim($_GET['inid']) : '';
        $selectby            = isset($_GET['selectby']) ? trim($_GET['selectby']) : '';
        $cstatus             = isset($_GET['cstatus']) ? intval($_GET['cstatus']) : 0;
        $apply_barge_rewrite = isset($_GET['apply_barge_rewrite']) ? intval($_GET['apply_barge_rewrite']) : 0;

        if ($action_id <= 0) {
            return $this->_cascade_json(array(
                'status'  => 'error',
                'message' => 'action_id is required and must be a positive integer',
                'rows'    => array(),
            ), 400);
        }

        $rows            = array();
        $branch          = 'action_only';
        $barge_rewritten = false;
        $resolved_inid   = null;

        // Branch A: Next Follow Up Date - resolve next_folloup_have_date.id
        // back to init_call.id via tblcallevents, then run the standard
        // multi-lead cascade SQL.
        if ($selectby === 'Next Follow Up Date' && $inid_raw !== '') {
            $branch = 'next_follow_up_date';
            $follow_id = intval(rtrim($inid_raw, ','));
            // Two-hop resolution mirroring the production controller branch
            $resolve_q = $this->db->query(
                "SELECT cid_id FROM tblcallevents WHERE id = "
                ."(SELECT cid_id FROM next_folloup_have_date WHERE id = ?)",
                array($follow_id)
            );
            $resolve_rows = $resolve_q->result();
            if (!empty($resolve_rows) && isset($resolve_rows[0]->cid_id)) {
                $resolved_inid = $resolve_rows[0]->cid_id;
                $rows = $this->Menu_model->get_purposebyinidnew($action_id, $resolved_inid);
            }
        }
        // Branch B: Call On School - no inid filter, return all purposes for action
        elseif ($selectby === 'Call On School') {
            $branch = 'call_on_school';
            $rows = $this->Menu_model->GetPurposeNameByActionId($action_id);
        }
        // Branch C: default multi-lead path (inid present)
        elseif ($inid_raw !== '') {
            $branch = 'default';
            $inid_clean = rtrim($inid_raw, ',');
            // Guard against fully-empty list (would produce SQL IN () syntax error)
            if ($inid_clean !== '' && !preg_match('/^,+$/', $inid_clean)) {
                // Sanitise: only digits and commas allowed
                if (preg_match('/^[0-9,]+$/', $inid_clean)) {
                    // Apply Barge rewrite on the first inid's cstatus if requested
                    if ($apply_barge_rewrite === 1 && $action_id == 4) {
                        $first_id = intval(explode(',', $inid_clean)[0]);
                        $sq = $this->db->query(
                            "SELECT cstatus FROM init_call WHERE id = ?",
                            array($first_id)
                        );
                        $sr = $sq->result();
                        if (!empty($sr) && !in_array(intval($sr[0]->cstatus), array(1, 8, 13))) {
                            $action_id = 3;
                            $barge_rewritten = true;
                        }
                    }
                    $rows = $this->Menu_model->get_purposebyinidnew($action_id, $inid_clean);
                }
            }
        }
        // Branch D: action plus explicit cstatus (Day Plan form path)
        elseif ($cstatus > 0) {
            $branch = 'action_status';
            // Apply Barge rewrite if requested (mirrors get_purposebya quirk)
            if ($apply_barge_rewrite === 1 && $action_id == 4
                && !in_array($cstatus, array(1, 8, 13))) {
                $action_id = 3;
                $barge_rewritten = true;
            }
            $rows = $this->Menu_model->get_purposebya($action_id, $cstatus);
        }
        // Branch E: action only - no inid, no cstatus
        else {
            $branch = 'action_only';
            $rows = $this->Menu_model->GetPurposeNameByActionId($action_id);
        }

        // Fresh Meeting fallback (production behavior across all branches)
        $fallback_used = false;
        if (empty($rows)) {
            $rows = array(
                (object) array(
                    'id'        => 34,
                    'name'      => 'Fresh Meeting',
                    'action_id' => $action_id,
                    'status_id' => null,
                ),
            );
            $fallback_used = true;
        }

        // Normalise to array-of-assoc for JSON response
        $out = array();
        foreach ($rows as $r) {
            $out[] = array(
                'id'        => isset($r->id) ? intval($r->id) : null,
                'name'      => isset($r->name) ? $r->name : '',
                'action_id' => isset($r->action_id) ? intval($r->action_id) : $action_id,
                'status_id' => isset($r->status_id) ? intval($r->status_id) : null,
            );
        }

        return $this->_cascade_json(array(
            'status'          => 'ok',
            'rows'            => $out,
            'fallback_used'   => $fallback_used,
            'branch'          => $branch,
            'barge_rewritten' => $barge_rewritten,
            'resolved_inid'   => $resolved_inid,
        ));
    }

    private function _cascade_json($payload, $code = 200) {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($payload);
        return;
    }
}

/* ---------------------------------------------------------------------------
 * 2) Mobile endpoint - add to application/controllers/PlannerV2Api.php
 * ------------------------------------------------------------------------- */

class PlannerV2Api_CascadePatch extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Menu_model');
        $this->load->library('stem_bearer_auth');
        $this->stem_bearer_auth->verify();
    }

    /**
     * GET /api/planner/v2/purposes_v2
     *
     * Mobile-facing twin of Menu::getpurposes_v2. Same logic, same response
     * shape, bearer auth instead of session.
     *
     * The implementation is intentionally identical to the web method so the
     * mobile fetch returns exactly what the web AJAX returns. Diff-free.
     */
    public function purposes_v2() {
        $action_id           = isset($_GET['action_id']) ? intval($_GET['action_id']) : 0;
        $inid_raw            = isset($_GET['inid']) ? trim($_GET['inid']) : '';
        $selectby            = isset($_GET['selectby']) ? trim($_GET['selectby']) : '';
        $cstatus             = isset($_GET['cstatus']) ? intval($_GET['cstatus']) : 0;
        $apply_barge_rewrite = isset($_GET['apply_barge_rewrite']) ? intval($_GET['apply_barge_rewrite']) : 0;

        if ($action_id <= 0) {
            return $this->_api_json(array(
                'status'  => 'error',
                'message' => 'action_id is required and must be a positive integer',
                'rows'    => array(),
            ), 400);
        }

        $rows            = array();
        $branch          = 'action_only';
        $barge_rewritten = false;
        $resolved_inid   = null;

        if ($selectby === 'Next Follow Up Date' && $inid_raw !== '') {
            $branch = 'next_follow_up_date';
            $follow_id = intval(rtrim($inid_raw, ','));
            $resolve_q = $this->db->query(
                "SELECT cid_id FROM tblcallevents WHERE id = "
                ."(SELECT cid_id FROM next_folloup_have_date WHERE id = ?)",
                array($follow_id)
            );
            $resolve_rows = $resolve_q->result();
            if (!empty($resolve_rows) && isset($resolve_rows[0]->cid_id)) {
                $resolved_inid = $resolve_rows[0]->cid_id;
                $rows = $this->Menu_model->get_purposebyinidnew($action_id, $resolved_inid);
            }
        }
        elseif ($selectby === 'Call On School') {
            $branch = 'call_on_school';
            $rows = $this->Menu_model->GetPurposeNameByActionId($action_id);
        }
        elseif ($inid_raw !== '') {
            $branch = 'default';
            $inid_clean = rtrim($inid_raw, ',');
            if ($inid_clean !== '' && !preg_match('/^,+$/', $inid_clean)) {
                if (preg_match('/^[0-9,]+$/', $inid_clean)) {
                    if ($apply_barge_rewrite === 1 && $action_id == 4) {
                        $first_id = intval(explode(',', $inid_clean)[0]);
                        $sq = $this->db->query(
                            "SELECT cstatus FROM init_call WHERE id = ?",
                            array($first_id)
                        );
                        $sr = $sq->result();
                        if (!empty($sr) && !in_array(intval($sr[0]->cstatus), array(1, 8, 13))) {
                            $action_id = 3;
                            $barge_rewritten = true;
                        }
                    }
                    $rows = $this->Menu_model->get_purposebyinidnew($action_id, $inid_clean);
                }
            }
        }
        elseif ($cstatus > 0) {
            $branch = 'action_status';
            if ($apply_barge_rewrite === 1 && $action_id == 4
                && !in_array($cstatus, array(1, 8, 13))) {
                $action_id = 3;
                $barge_rewritten = true;
            }
            $rows = $this->Menu_model->get_purposebya($action_id, $cstatus);
        }
        else {
            $branch = 'action_only';
            $rows = $this->Menu_model->GetPurposeNameByActionId($action_id);
        }

        $fallback_used = false;
        if (empty($rows)) {
            $rows = array(
                (object) array(
                    'id'        => 34,
                    'name'      => 'Fresh Meeting',
                    'action_id' => $action_id,
                    'status_id' => null,
                ),
            );
            $fallback_used = true;
        }

        $out = array();
        foreach ($rows as $r) {
            $out[] = array(
                'id'        => isset($r->id) ? intval($r->id) : null,
                'name'      => isset($r->name) ? $r->name : '',
                'action_id' => isset($r->action_id) ? intval($r->action_id) : $action_id,
                'status_id' => isset($r->status_id) ? intval($r->status_id) : null,
            );
        }

        return $this->_api_json(array(
            'status'          => 'ok',
            'rows'            => $out,
            'fallback_used'   => $fallback_used,
            'branch'          => $branch,
            'barge_rewritten' => $barge_rewritten,
            'resolved_inid'   => $resolved_inid,
        ));
    }

    private function _api_json($payload, $code = 200) {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($payload);
        return;
    }
}

/* ---------------------------------------------------------------------------
 * 3) Routes patch - application/config/routes.php
 * ------------------------------------------------------------------------- */
/*
$route['api/planner/v2/purposes_v2'] = 'PlannerV2Api/purposes_v2';
// /Menu/getpurposes_v2 is auto-routed by the standard Menu controller
*/

/* ---------------------------------------------------------------------------
 * 4) Smoke test plan (staging only - stemapp.in)
 * ------------------------------------------------------------------------- */
/*
1. curl 'https://stemapp.in/Menu/getpurposes_v2?action_id=1&cstatus=1'
   Expect: 4 Call purposes for Open

2. curl 'https://stemapp.in/Menu/getpurposes_v2?action_id=4&cstatus=6'
   Expect: Fresh Meeting fallback (action 4 has no rows for status 6)

3. curl 'https://stemapp.in/Menu/getpurposes_v2?action_id=4&cstatus=6&apply_barge_rewrite=1'
   Expect: Scheduled Meeting purposes (rewrite to 3), barge_rewritten=true

4. curl 'https://stemapp.in/Menu/getpurposes_v2?action_id=4&cstatus=8'
   Expect: 12 Barge purposes for Open RPEM (no rewrite)

5. curl 'https://stemapp.in/Menu/getpurposes_v2?action_id=1&inid=12345'
   Expect: purposes for action 1 plus the lead's cstatus

6. curl 'https://stemapp.in/Menu/getpurposes_v2?action_id=3&inid=12345,12346,12347'
   Expect: union of purposes across 3 leads' distinct cstatus

7. curl 'https://stemapp.in/Menu/getpurposes_v2?action_id=1&inid=999&selectby=Next+Follow+Up+Date'
   Expect: follow-up resolution then purposes for action 1 plus resolved cstatus

8. curl 'https://stemapp.in/Menu/getpurposes_v2?action_id=1&selectby=Call+On+School'
   Expect: all 4 Call purposes regardless of inid

9. curl 'https://stemapp.in/api/planner/v2/purposes_v2?action_id=1&cstatus=1' \
      -H 'Authorization: Bearer <STEM_DIGEST_TOKEN>'
   Expect: same as test 1 but via bearer auth
*/
