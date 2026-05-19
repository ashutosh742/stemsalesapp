<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Planner v2 Purpose + Cascade Endpoints
 *
 * Two thin endpoints that feed the production-parity Purpose + Type-of-task
 * cascade on web (TaskPlannerV2.php) and mobile (NextDayPlannerV2Screen.js).
 *
 * NO new tables. NO new migration. Reuses:
 *   - purpose (id, action_id, status_id, name)        -- production table
 *   - action (id, name)                                -- actiontype master
 *   - init_call (id as inid, compname, cstatus)        -- lead master
 *   - Menu_model::GetAllCompanyByUserID($uid)          -- existing method
 *   - Menu_model::GetPurposeByActionAndStatusID()      -- existing method line 20333
 *
 * Mount points:
 *   GET  /Menu/getpurposebyactionstatus?action_id=X&status_id=Y    (web AdminLTE)
 *   GET  /api/planner/v2/leads                                      (mobile)
 *   GET  /api/planner/v2/purposes?action_id=X&status_id=Y           (mobile)
 *
 * Auth: existing session check on Menu, Bearer token on api/* group.
 *
 * Plain English responses. No new permissions required.
 */

/* ---------------------------------------------------------------------------
 * 1) Web endpoint - add to application/controllers/Menu.php
 * ------------------------------------------------------------------------- */

class Menu_PlannerV2_Patch extends MY_Controller {

    /**
     * Drop this method into the existing Menu controller.
     * Returns purpose rows for an action_id + status_id pair.
     * Consumed by TaskPlannerV2.php loadPurposeOptions() JS function.
     */
    public function getpurposebyactionstatus() {
        $action_id = isset($_GET['action_id']) ? intval($_GET['action_id']) : 0;
        $status_id = isset($_GET['status_id']) ? intval($_GET['status_id']) : 0;

        if ($action_id <= 0 || $status_id <= 0) {
            header('Content-Type: application/json');
            echo json_encode(array('status' => 'error', 'message' => 'action_id and status_id required', 'rows' => array()));
            return;
        }

        // Reuse production method (Menu_model.php line 20333)
        $rows = $this->Menu_model->GetPurposeByActionAndStatusID($action_id, $status_id);

        if (empty($rows)) {
            // Production fallback list when no row matches the pair
            $rows = array(
                array('id' => 1,  'name' => 'Introduction call'),
                array('id' => 2,  'name' => 'Discovery'),
                array('id' => 66, 'name' => 'Barg in Meeting'),
                array('id' => 70, 'name' => 'Follow-up'),
                array('id' => 94, 'name' => 'Research'),
            );
        }

        header('Content-Type: application/json');
        echo json_encode(array('status' => 'ok', 'rows' => $rows));
    }
}

/* ---------------------------------------------------------------------------
 * 2) Mobile endpoints - new controller application/controllers/PlannerV2Api.php
 * ------------------------------------------------------------------------- */

class PlannerV2Api extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Menu_model');
        $this->load->library('stem_bearer_auth'); // existing helper
        $this->stem_bearer_auth->verify();
    }

    /**
     * GET /api/planner/v2/leads
     *
     * Returns the BD's lead list for the planner cascade.
     * Response: { status, rows: [{inid, compname, cstatus}] }
     */
    public function leads() {
        $uid = $this->stem_bearer_auth->user_id();
        if (!$uid) {
            $this->_json(array('status' => 'error', 'message' => 'auth required'), 401);
            return;
        }

        $rows = $this->Menu_model->GetAllCompanyByUserID($uid);

        // Normalise to mobile-friendly shape. Production returns id+compname+cstatus.
        $out = array();
        foreach ($rows as $r) {
            $out[] = array(
                'inid'     => isset($r->id) ? intval($r->id) : intval($r['id']),
                'compname' => isset($r->compname) ? $r->compname : $r['compname'],
                'cstatus'  => isset($r->cstatus) ? intval($r->cstatus) : intval($r['cstatus']),
            );
        }

        $this->_json(array('status' => 'ok', 'rows' => $out));
    }

    /**
     * GET /api/planner/v2/purposes?action_id=X&status_id=Y
     *
     * Mirrors the web endpoint Menu::getpurposebyactionstatus.
     */
    public function purposes() {
        $action_id = isset($_GET['action_id']) ? intval($_GET['action_id']) : 0;
        $status_id = isset($_GET['status_id']) ? intval($_GET['status_id']) : 0;

        if ($action_id <= 0 || $status_id <= 0) {
            $this->_json(array('status' => 'error', 'message' => 'action_id and status_id required', 'rows' => array()), 400);
            return;
        }

        $rows = $this->Menu_model->GetPurposeByActionAndStatusID($action_id, $status_id);

        if (empty($rows)) {
            $rows = array(
                array('id' => 1,  'name' => 'Introduction call'),
                array('id' => 2,  'name' => 'Discovery'),
                array('id' => 66, 'name' => 'Barg in Meeting'),
                array('id' => 70, 'name' => 'Follow-up'),
                array('id' => 94, 'name' => 'Research'),
            );
        }

        // Normalise
        $out = array();
        foreach ($rows as $r) {
            $out[] = array(
                'id'   => isset($r->id) ? intval($r->id) : intval($r['id']),
                'name' => isset($r->name) ? $r->name : $r['name'],
            );
        }

        $this->_json(array('status' => 'ok', 'rows' => $out));
    }

    private function _json($payload, $code = 200) {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($payload);
    }
}

/* ---------------------------------------------------------------------------
 * 3) Routes patch - application/config/routes.php
 * ------------------------------------------------------------------------- */
/*
$route['api/planner/v2/leads']    = 'PlannerV2Api/leads';
$route['api/planner/v2/purposes'] = 'PlannerV2Api/purposes';
*/

/* ---------------------------------------------------------------------------
 * 4) Production parity notes
 * ------------------------------------------------------------------------- */
/*
 * The web Menu/getpurposebyactionstatus mirrors the production AJAX call
 * already wired into TaskPlanner2.php (line 2005 area), so the cascade
 * behaviour is identical. The production form has a known bug where the
 * Purpose field renders as Yes/No - v2 fixes that by always rendering the
 * real purpose dropdown.
 *
 * Persistence is unchanged: add_plan2($pdate, $uid, $ptime, $inid,
 * $ntaction, $ntstatus, $ntppose, $ttype, $tptime, $new_datetime,
 * $selectby='PlannerV2 Add Task', $jsonData). Parameter 7 is the
 * purpose_id from this endpoint.
 *
 * Auth: web reuses MY_Controller session, mobile reuses the staging bearer
 * token verified by stem_bearer_auth (same library used by all /api/* crons).
 */
