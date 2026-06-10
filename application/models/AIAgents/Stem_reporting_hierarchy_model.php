<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ReportingHierarchy_model
 *
 * Single source of truth for the STEM 5-level reporting tree. Reads from the
 * reporting_hierarchy table seeded by migration 023.3 from the canonical
 * Sales-Team-Reporting.xlsx file.
 *
 * Every line manager scorecard, review session router, escalation ticket, and
 * cron should call this model. Do not query user.parent_uid directly.
 *
 * Tree shape (5 levels):
 *   Level 1  Director       (Meera Dhanuka)
 *   Level 2  RM             (4 active: Sunny, Mahesh, Sadanand, Mehak)
 *   Level 3  CM             (5 active across 9 clusters)
 *   Level 4  ACM            (6 active)
 *   Level 5  SC / BD        (7 SCs + 34 BDs)
 *
 * Special routing:
 *   - Punjab and Delhi have no RM. ACM/CM there reports direct to Director.
 *   - East roll-up: Mehak Sarraf covers West Bengal (cluster 8) AND
 *     Jharkhand (cluster 9), so her cluster_id is NULL.
 *   - PC (Product Coordinator) rows are accommodated in the role enum but
 *     are not seeded in 023.3. Add via direct insert when the role lands.
 */
class ReportingHierarchy_model extends CI_Model
{
    /** @var string */
    private $tbl = 'reporting_hierarchy';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // ------------------------------------------------------------------
    // BASIC LOOKUPS
    // ------------------------------------------------------------------

    /**
     * Get the active hierarchy row for one employee.
     * Returns NULL if not found.
     */
    public function get_employee($uid)
    {
        return $this->db
            ->where('employee_uid', (int)$uid)
            ->where('status', 'Active')
            ->where('effective_to', NULL)
            ->get($this->tbl)
            ->row_array();
    }

    /**
     * Get every active employee. Optional role filter.
     * @param string|array|null $role  Single role, list of roles, or NULL for all
     */
    public function all_active($role = NULL)
    {
        $this->db->where('status', 'Active')->where('effective_to', NULL);
        if ($role !== NULL) {
            if (is_array($role)) {
                $this->db->where_in('role', $role);
            } else {
                $this->db->where('role', $role);
            }
        }
        return $this->db->order_by('level, cluster_id, employee_name')
                        ->get($this->tbl)
                        ->result_array();
    }

    /**
     * Get every employee inside a given cluster (active only).
     */
    public function in_cluster($cluster_id, $role = NULL)
    {
        $this->db->where('cluster_id', (int)$cluster_id)
                 ->where('status', 'Active')
                 ->where('effective_to', NULL);
        if ($role !== NULL) {
            if (is_array($role)) $this->db->where_in('role', $role);
            else $this->db->where('role', $role);
        }
        return $this->db->order_by('role, employee_name')->get($this->tbl)->result_array();
    }

    // ------------------------------------------------------------------
    // MANAGER LOOKUPS
    // ------------------------------------------------------------------

    /**
     * Direct manager of a given employee. Returns NULL for Director.
     */
    public function manager_of($uid)
    {
        $row = $this->get_employee($uid);
        if (!$row || empty($row['manager_uid'])) return NULL;
        return $this->get_employee($row['manager_uid']);
    }

    /**
     * Skip-level manager (one above direct). Used for escalation.
     * Returns NULL if no skip exists (i.e. direct manager is already Director).
     */
    public function skip_manager_of($uid)
    {
        $row = $this->get_employee($uid);
        if (!$row || empty($row['skip_manager_uid'])) return NULL;
        return $this->get_employee($row['skip_manager_uid']);
    }

    /**
     * Full escalation chain from employee up to Director.
     * Returns an ordered array: [direct_manager, skip_manager, director].
     * Each element is a hierarchy row, or NULL if that rung does not exist.
     */
    public function escalation_chain($uid)
    {
        $row = $this->get_employee($uid);
        if (!$row) return [NULL, NULL, NULL];
        return [
            $row['manager_uid']      ? $this->get_employee($row['manager_uid'])      : NULL,
            $row['skip_manager_uid'] ? $this->get_employee($row['skip_manager_uid']) : NULL,
            $row['director_uid']     ? $this->get_employee($row['director_uid'])     : NULL,
        ];
    }

    // ------------------------------------------------------------------
    // DIRECT REPORTS
    // ------------------------------------------------------------------

    /**
     * Direct reports for a manager. Used by review_session target gate to
     * compute how many targets the manager must set before submitting.
     */
    public function direct_reports($manager_uid, $role_filter = NULL)
    {
        $this->db->where('manager_uid', (int)$manager_uid)
                 ->where('status', 'Active')
                 ->where('effective_to', NULL);
        if ($role_filter !== NULL) {
            if (is_array($role_filter)) $this->db->where_in('role', $role_filter);
            else $this->db->where('role', $role_filter);
        }
        return $this->db->order_by('role, cluster_id, employee_name')
                        ->get($this->tbl)
                        ->result_array();
    }

    /**
     * Direct + indirect (entire subtree) for a manager.
     * Breadth-first walk. Cap depth at 5 to match the 5-level tree.
     */
    public function full_subtree($manager_uid, $max_depth = 5)
    {
        $out = [];
        $queue = [[(int)$manager_uid, 0]];
        $seen = [(int)$manager_uid => true];
        while (!empty($queue)) {
            list($current, $depth) = array_shift($queue);
            if ($depth >= $max_depth) continue;
            $children = $this->direct_reports($current);
            foreach ($children as $child) {
                $cid = (int)$child['employee_uid'];
                if (isset($seen[$cid])) continue;
                $seen[$cid] = true;
                $child['_depth'] = $depth + 1;
                $out[] = $child;
                $queue[] = [$cid, $depth + 1];
            }
        }
        return $out;
    }

    /**
     * Count of direct reports for a manager (used by target gate).
     */
    public function direct_report_count($manager_uid, $role_filter = NULL)
    {
        $this->db->where('manager_uid', (int)$manager_uid)
                 ->where('status', 'Active')
                 ->where('effective_to', NULL);
        if ($role_filter !== NULL) {
            if (is_array($role_filter)) $this->db->where_in('role', $role_filter);
            else $this->db->where('role', $role_filter);
        }
        return (int)$this->db->count_all_results($this->tbl);
    }

    // ------------------------------------------------------------------
    // CLUSTER + ROLE ROLL-UPS
    // ------------------------------------------------------------------

    /**
     * Returns the BDs under a given line manager (CM or ACM).
     * This is what migration 022 scorecard cron queries to compute K1-K7.
     */
    public function bds_under_manager($manager_uid)
    {
        return $this->direct_reports($manager_uid, 'BD');
    }

    /**
     * Returns the cluster head (RM if present, else CM/ACM acting as lead).
     */
    public function cluster_head($cluster_id)
    {
        // Prefer RM
        $rm = $this->db->where('cluster_id', (int)$cluster_id)
                       ->where('role', 'RM')
                       ->where('status', 'Active')
                       ->where('effective_to', NULL)
                       ->get($this->tbl)->row_array();
        if ($rm) return $rm;

        // Fall back to CM
        $cm = $this->db->where('cluster_id', (int)$cluster_id)
                       ->where('role', 'CM')
                       ->where('status', 'Active')
                       ->where('effective_to', NULL)
                       ->get($this->tbl)->row_array();
        if ($cm) return $cm;

        // Fall back to ACM (Punjab case: Ruchika)
        return $this->db->where('cluster_id', (int)$cluster_id)
                        ->where('role', 'ACM')
                        ->where('status', 'Active')
                        ->where('effective_to', NULL)
                        ->order_by('id', 'ASC')
                        ->get($this->tbl)->row_array();
    }

    /**
     * Map cluster_id -> array of clusters the East RM covers.
     * Special-cases Mehak Sarraf who has cluster_id NULL but spans 8 and 9.
     */
    public function clusters_for_rm($rm_uid)
    {
        $row = $this->get_employee($rm_uid);
        if (!$row || $row['role'] !== 'RM') return [];
        // Single-cluster RM
        if (!empty($row['cluster_id'])) {
            return [(int)$row['cluster_id']];
        }
        // East RM Mehak: cluster_text = 'East', spans 8 and 9
        if ($row['cluster_text'] === 'East') return [8, 9];
        return [];
    }

    // ------------------------------------------------------------------
    // REVIEW ROUTING
    // ------------------------------------------------------------------

    /**
     * Who reviews this employee at quarter end?
     * Direct manager normally. Skip-level if direct manager is on leave
     * (caller must check leave_request separately).
     */
    public function reviewer_for($uid)
    {
        return $this->manager_of($uid);
    }

    /**
     * Who signs off a stage gate (G1-G4) for a BD?
     * Hierarchy:
     *   - G1, G2  -> CM (direct manager if BD)
     *   - G3      -> RM (skip-level if BD reports to ACM/CM)
     *   - G4      -> Director (final closure signoff over Rs 50 lakh)
     */
    public function signoff_for($uid, $gate)
    {
        $chain = $this->escalation_chain($uid);
        switch (strtoupper($gate)) {
            case 'G1':
            case 'G2':
                return $chain[0];
            case 'G3':
                // skip if exists, else direct
                return $chain[1] ?: $chain[0];
            case 'G4':
                return $chain[2] ?: $chain[1] ?: $chain[0];
            default:
                return $chain[0];
        }
    }

    // ------------------------------------------------------------------
    // BULK HELPERS FOR CRON USE
    // ------------------------------------------------------------------

    /**
     * All managers (CM, ACM, RM, Director) keyed by uid.
     * Cron uses this to loop scorecard computation.
     */
    public function all_managers()
    {
        $rows = $this->all_active(['Director', 'RM', 'CM', 'ACM']);
        $out = [];
        foreach ($rows as $r) $out[(int)$r['employee_uid']] = $r;
        return $out;
    }

    /**
     * All BDs keyed by uid. Includes cluster_id for cadence engine routing.
     */
    public function all_bds()
    {
        $rows = $this->all_active('BD');
        $out = [];
        foreach ($rows as $r) $out[(int)$r['employee_uid']] = $r;
        return $out;
    }

    /**
     * Sales coordinators (SC role), keyed by uid.
     * Used by SC discipline cadence in migration 023.2.
     */
    public function all_scs()
    {
        $rows = $this->all_active('SC');
        $out = [];
        foreach ($rows as $r) $out[(int)$r['employee_uid']] = $r;
        return $out;
    }

    /**
     * RMs only.
     */
    public function all_rms()
    {
        $rows = $this->all_active('RM');
        $out = [];
        foreach ($rows as $r) $out[(int)$r['employee_uid']] = $r;
        return $out;
    }

    // ------------------------------------------------------------------
    // QUARTER CONFIG HELPERS
    // ------------------------------------------------------------------

    /**
     * Current quarter row (the one CURDATE() falls inside).
     */
    public function current_quarter()
    {
        return $this->db->get('v_current_quarter')->row_array() ?: NULL;
    }

    /**
     * Next quarter row.
     */
    public function next_quarter()
    {
        return $this->db->get('v_next_quarter')->row_array() ?: NULL;
    }

    /**
     * Specific quarter by fiscal year + quarter number.
     */
    public function quarter_config($fy, $quarter)
    {
        return $this->db->where('fiscal_year', (int)$fy)
                        ->where('quarter', (int)$quarter)
                        ->get('quarter_config')
                        ->row_array() ?: NULL;
    }

    // ------------------------------------------------------------------
    // WRITE PATH (for HR moves)
    // ------------------------------------------------------------------

    /**
     * Move an employee to a new manager. Closes the old row and opens new.
     * Always called inside a DB transaction by the caller.
     */
    public function reassign_manager($employee_uid, $new_manager_uid, $effective_date, $note = NULL)
    {
        $emp = $this->get_employee($employee_uid);
        if (!$emp) return ['ok' => false, 'error' => 'employee not found'];

        $new_mgr = $this->get_employee($new_manager_uid);
        if (!$new_mgr) return ['ok' => false, 'error' => 'new manager not found'];

        // Close current row
        $this->db->where('id', $emp['id'])
                 ->update($this->tbl, [
                     'effective_to' => $effective_date,
                     'notes' => trim(($emp['notes'] ?? '') . ' | Reassigned: ' . ($note ?? '')),
                 ]);

        // Insert new row
        $insert = $emp;
        unset($insert['id'], $insert['created_at'], $insert['updated_at']);
        $insert['manager_uid'] = $new_mgr['employee_uid'];
        $insert['manager_name'] = $new_mgr['employee_name'];
        $insert['skip_manager_uid'] = $new_mgr['manager_uid'];
        $insert['skip_manager_name'] = $new_mgr['manager_name'];
        $insert['effective_from'] = $effective_date;
        $insert['effective_to'] = NULL;
        $insert['notes'] = $note;
        $this->db->insert($this->tbl, $insert);
        return ['ok' => true, 'new_id' => $this->db->insert_id()];
    }
}
