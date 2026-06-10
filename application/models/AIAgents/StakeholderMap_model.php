<?php
/**
 * StakeholderMap_model.php
 *
 * Migration 051 - School Stakeholder Relationship Map
 * Location: application/models/AIAgents/StakeholderMap_model.php
 *
 * Responsibilities:
 *   - CRUD for school_stakeholder rows
 *   - CRUD for school_stakeholder_relationship edges
 *   - Graph traversal helpers (find_path_to_dm, get_champions_for_lead,
 *     get_blockers_for_lead)
 *   - Backfill hook called by M037 dm_contact_block save handler
 *   - Pilot guard: is_lead_in_pilot() checks feature flag before any write
 *
 * Pilot uids (type_id mapping):
 *   SC  1000356 (type_id 25)
 *   BD  1000289, 1000351 (type_id 1)
 *   CM  1000305 (type_id 13)
 *   RM  1000269 (type_id 25/27)
 *
 * Standing rules:
 *   - Plain English, no em-dashes, no non-ASCII
 *   - "Rs" for rupees, "percent" spelled out, "over" for greater than
 *   - BearerAuth is handled in the controller; model receives validated uid
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class StakeholderMap_model extends CI_Model
{
    /** Feature flag code used across all methods */
    const FLAG_CODE  = 'stakeholder_map_051_enabled';

    /** WB pilot uids */
    const PILOT_UIDS = [1000289, 1000351, 1000305, 1000269, 1000356];

    /** Valid decision_role values */
    const DECISION_ROLES = [
        'DECISION_MAKER',
        'INFLUENCER',
        'CHAMPION',
        'BLOCKER',
        'GATEKEEPER',
        'BUDGET_HOLDER',
    ];

    /** Valid engagement_status values */
    const ENGAGEMENT_STATUSES = [
        'NOT_MET',
        'MET_ONCE',
        'ENGAGED',
        'BOUGHT_IN',
        'OPPOSED',
    ];

    /** Valid relationship_type values */
    const RELATIONSHIP_TYPES = [
        'reports_to',
        'champions',
        'blocks',
        'influences',
        'married_to',
        'alumni_of',
        'peer_of',
    ];

    /** Max length for the notes field */
    const MAX_NOTES_CHARS = 1000;

    public function __construct()
    {
        parent::__construct();
    }

    // =========================================================
    // Feature flag + pilot guard
    // =========================================================

    /**
     * Returns the current flag value: 0, 1 (pilot), or 2 (org-wide).
     */
    public function get_flag_value()
    {
        $row = $this->db->get_where('feature_flag', ["flag_key" => self::FLAG_CODE])->row();
        if (!$row) {
            return 0;
        }
        return (int) $row->flag_value;
    }

    /**
     * Returns TRUE if the feature is enabled for the given lead owner uid.
     *
     * @param int $lead_owner_uid   uid of the BD who owns the lead
     * @return bool
     */
    public function is_lead_in_pilot($lead_owner_uid)
    {
        $flag = $this->get_flag_value();
        if ($flag === 0) {
            return false;
        }
        if ($flag === 2) {
            return true;
        }
        // flag === 1: pilot only
        return in_array((int) $lead_owner_uid, self::PILOT_UIDS, true);
    }

    /**
     * Returns the lead owner uid for a given cid_id.
     *
     * @param int $cid_id
     * @return int|null
     */
    public function get_lead_owner_uid($cid_id)
    {
        // C4 FIX 2026-06-06: init_call has no bd_uid/cid_id/is_deleted columns.
        // PK is `id`; the BD owner column is `mainbd`. The passed value is init_call.id.
        $row = $this->db->select('mainbd AS bd_uid')
                        ->where('id', (int) $cid_id)
                        ->get('init_call')
                        ->row();
        return $row ? (int) $row->bd_uid : null;
    }

    // =========================================================
    // school_stakeholder CRUD
    // =========================================================

    /**
     * Return all non-deleted stakeholders for a lead.
     * Contacts fields (mobile, email) are masked unless caller_uid
     * equals the lead owner or caller role is CM/RM/SC.
     *
     * @param int    $cid_id
     * @param int    $caller_uid
     * @param string $caller_role  BD | CM | RM | SC | ACM
     * @return array
     */
    public function get_stakeholders_for_lead($cid_id, $caller_uid, $caller_role)
    {
        $lead_owner_uid = $this->get_lead_owner_uid($cid_id);

        $rows = $this->db->where('cid_id', (int) $cid_id)
                         ->where('is_deleted', 0)
                         ->order_by('FIELD(decision_role,"DECISION_MAKER","BUDGET_HOLDER","INFLUENCER","CHAMPION","GATEKEEPER","BLOCKER")', false, false)
                         ->order_by('full_name', 'ASC')
                         ->get('school_stakeholder')
                         ->result_array();

        $mask = !in_array($caller_role, ['CM', 'RM', 'SC', 'ACM'], true)
                && ((int) $caller_uid !== (int) $lead_owner_uid);

        foreach ($rows as &$row) {
            if ($mask) {
                $row['mobile'] = $this->_mask_contact($row['mobile']);
                $row['email']  = $this->_mask_contact($row['email']);
            }
        }
        unset($row);
        return $rows;
    }

    /**
     * Get a single stakeholder row by id. Returns null if not found or deleted.
     *
     * @param int $stakeholder_id
     * @return array|null
     */
    public function get_stakeholder_by_id($stakeholder_id)
    {
        $row = $this->db->where('id', (int) $stakeholder_id)
                        ->where('is_deleted', 0)
                        ->get('school_stakeholder')
                        ->row_array();
        return $row ?: null;
    }

    /**
     * Add a new stakeholder to a lead.
     *
     * @param array $data  Keys: cid_id, created_by_uid, full_name, designation,
     *                     mobile, email, decision_role, engagement_status, notes
     * @return array  ['success' => bool, 'id' => int|null, 'error' => string]
     */
    public function add_stakeholder(array $data)
    {
        $err = $this->_validate_stakeholder_data($data);
        if ($err) {
            return ['success' => false, 'id' => null, 'error' => $err];
        }

        $lead_owner_uid = $this->get_lead_owner_uid($data['cid_id']);
        if (!$this->is_lead_in_pilot($lead_owner_uid)) {
            return ['success' => false, 'id' => null, 'error' => 'Feature not enabled for this lead'];
        }

        $insert = [
            'cid_id'            => (int) $data['cid_id'],
            'created_by_uid'    => (int) $data['created_by_uid'],
            'full_name'         => trim(substr($data['full_name'], 0, 128)),
            'designation'       => isset($data['designation'])   ? trim(substr($data['designation'], 0, 128)) : null,
            'mobile'            => isset($data['mobile'])        ? trim($data['mobile']) : null,
            'email'             => isset($data['email'])         ? strtolower(trim($data['email'])) : null,
            'decision_role'     => $data['decision_role'],
            'engagement_status' => isset($data['engagement_status']) ? $data['engagement_status'] : 'NOT_MET',
            'notes'             => isset($data['notes'])         ? substr(trim($data['notes']), 0, self::MAX_NOTES_CHARS) : null,
            'created_at'        => date('Y-m-d H:i:s'),
        ];

        $this->db->insert('school_stakeholder', $insert);
        $new_id = (int) $this->db->insert_id();

        if (!$new_id) {
            return ['success' => false, 'id' => null, 'error' => 'Database insert failed'];
        }
        return ['success' => true, 'id' => $new_id, 'error' => null];
    }

    /**
     * Update an existing stakeholder.
     *
     * @param int   $stakeholder_id
     * @param array $data  Subset of stakeholder fields to update
     * @param int   $caller_uid
     * @return array  ['success' => bool, 'error' => string]
     */
    public function update_stakeholder($stakeholder_id, array $data, $caller_uid)
    {
        $existing = $this->get_stakeholder_by_id($stakeholder_id);
        if (!$existing) {
            return ['success' => false, 'error' => 'Stakeholder not found'];
        }

        $allowed = [
            'full_name', 'designation', 'mobile', 'email',
            'decision_role', 'engagement_status', 'last_touch_date',
            'last_touch_event_id', 'notes',
        ];

        $update = [];
        foreach ($allowed as $field) {
            if (array_key_exists($field, $data)) {
                $update[$field] = $data[$field];
            }
        }

        if (isset($update['decision_role'])
            && !in_array($update['decision_role'], self::DECISION_ROLES, true)) {
            return ['success' => false, 'error' => 'Invalid decision_role'];
        }
        if (isset($update['engagement_status'])
            && !in_array($update['engagement_status'], self::ENGAGEMENT_STATUSES, true)) {
            return ['success' => false, 'error' => 'Invalid engagement_status'];
        }
        if (isset($update['notes'])) {
            $update['notes'] = substr(trim($update['notes']), 0, self::MAX_NOTES_CHARS);
        }

        if (empty($update)) {
            return ['success' => false, 'error' => 'No valid fields to update'];
        }

        $this->db->where('id', (int) $stakeholder_id)->update('school_stakeholder', $update);
        return ['success' => true, 'error' => null];
    }

    /**
     * Soft-delete a stakeholder and all its edges.
     *
     * @param int $stakeholder_id
     * @param int $caller_uid
     * @return array  ['success' => bool, 'error' => string]
     */
    public function delete_stakeholder($stakeholder_id, $caller_uid)
    {
        $existing = $this->get_stakeholder_by_id($stakeholder_id);
        if (!$existing) {
            return ['success' => false, 'error' => 'Stakeholder not found'];
        }

        // Soft-delete the stakeholder node
        $this->db->where('id', (int) $stakeholder_id)
                 ->update('school_stakeholder', ['is_deleted' => 1]);

        // Soft-delete any edge that references this stakeholder
        $this->db->where('subject_id', (int) $stakeholder_id)
                 ->or_where('object_id', (int) $stakeholder_id)
                 ->update('school_stakeholder_relationship', ['is_deleted' => 1]);

        return ['success' => true, 'error' => null];
    }

    /**
     * Update last_touch_date and optionally last_touch_event_id for a
     * stakeholder. Called from the call-close handler when BD tags a
     * stakeholder against a tblcallevents row.
     *
     * @param int      $stakeholder_id
     * @param string   $touch_date       Y-m-d
     * @param int|null $event_id
     * @return bool
     */
    public function update_last_touch($stakeholder_id, $touch_date, $event_id = null)
    {
        $update = ['last_touch_date' => $touch_date];
        if ($event_id !== null) {
            $update['last_touch_event_id'] = (int) $event_id;
        }
        $this->db->where('id', (int) $stakeholder_id)->update('school_stakeholder', $update);
        return $this->db->affected_rows() > 0;
    }

    // =========================================================
    // school_stakeholder_relationship CRUD
    // =========================================================

    /**
     * Return all non-deleted edges for a lead.
     *
     * @param int $cid_id
     * @return array
     */
    public function get_relationships_for_lead($cid_id)
    {
        return $this->db->where('cid_id', (int) $cid_id)
                        ->where('is_deleted', 0)
                        ->get('school_stakeholder_relationship')
                        ->result_array();
    }

    /**
     * Add a directed edge between two stakeholders.
     * Enforces: both nodes must belong to the same cid_id.
     *
     * @param array $data  Keys: cid_id, subject_id, object_id, relationship_type, notes, created_by_uid
     * @return array  ['success' => bool, 'id' => int|null, 'error' => string]
     */
    public function add_relationship(array $data)
    {
        if (empty($data['cid_id']) || empty($data['subject_id']) || empty($data['object_id'])) {
            return ['success' => false, 'id' => null, 'error' => 'cid_id, subject_id and object_id are required'];
        }
        if ((int) $data['subject_id'] === (int) $data['object_id']) {
            return ['success' => false, 'id' => null, 'error' => 'A stakeholder cannot have a relationship with itself'];
        }
        if (!in_array($data['relationship_type'], self::RELATIONSHIP_TYPES, true)) {
            return ['success' => false, 'id' => null, 'error' => 'Invalid relationship_type'];
        }

        // Both nodes must belong to the same lead
        $subject = $this->get_stakeholder_by_id($data['subject_id']);
        $object  = $this->get_stakeholder_by_id($data['object_id']);

        if (!$subject || !$object) {
            return ['success' => false, 'id' => null, 'error' => 'One or both stakeholders not found'];
        }
        if ((int) $subject['cid_id'] !== (int) $data['cid_id']
            || (int) $object['cid_id'] !== (int) $data['cid_id']) {
            return ['success' => false, 'id' => null, 'error' => 'Both stakeholders must belong to the same lead'];
        }

        $lead_owner_uid = $this->get_lead_owner_uid($data['cid_id']);
        if (!$this->is_lead_in_pilot($lead_owner_uid)) {
            return ['success' => false, 'id' => null, 'error' => 'Feature not enabled for this lead'];
        }

        $insert = [
            'cid_id'            => (int) $data['cid_id'],
            'subject_id'        => (int) $data['subject_id'],
            'object_id'         => (int) $data['object_id'],
            'relationship_type' => $data['relationship_type'],
            'notes'             => isset($data['notes']) ? substr(trim($data['notes']), 0, 255) : null,
            'created_by_uid'    => (int) $data['created_by_uid'],
            'created_at'        => date('Y-m-d H:i:s'),
        ];

        // Restore a soft-deleted duplicate edge rather than failing
        $existing = $this->db
            ->where('subject_id', $insert['subject_id'])
            ->where('object_id', $insert['object_id'])
            ->where('relationship_type', $insert['relationship_type'])
            ->get('school_stakeholder_relationship')
            ->row_array();

        if ($existing) {
            if ($existing['is_deleted'] == 1) {
                $this->db->where('id', $existing['id'])
                         ->update('school_stakeholder_relationship', ['is_deleted' => 0, 'notes' => $insert['notes']]);
                return ['success' => true, 'id' => (int) $existing['id'], 'error' => null];
            }
            return ['success' => false, 'id' => null, 'error' => 'Relationship already exists'];
        }

        $this->db->insert('school_stakeholder_relationship', $insert);
        $new_id = (int) $this->db->insert_id();
        if (!$new_id) {
            return ['success' => false, 'id' => null, 'error' => 'Database insert failed'];
        }
        return ['success' => true, 'id' => $new_id, 'error' => null];
    }

    /**
     * Soft-delete a directed edge by its id.
     *
     * @param int $relationship_id
     * @return array  ['success' => bool, 'error' => string]
     */
    public function remove_relationship($relationship_id)
    {
        $row = $this->db->where('id', (int) $relationship_id)
                        ->where('is_deleted', 0)
                        ->get('school_stakeholder_relationship')
                        ->row_array();
        if (!$row) {
            return ['success' => false, 'error' => 'Relationship not found'];
        }
        $this->db->where('id', (int) $relationship_id)
                 ->update('school_stakeholder_relationship', ['is_deleted' => 1]);
        return ['success' => true, 'error' => null];
    }

    // =========================================================
    // M037 backfill hook
    // =========================================================

    /**
     * Auto-create the first DECISION_MAKER stakeholder when a BD saves
     * the dm_contact_block inside init_call. Called from the M037
     * save handler after the init_call row is written.
     *
     * Idempotent: if a DECISION_MAKER row already exists for this lead
     * (with is_dm_contact_backfill=1), the method updates name and
     * designation if they changed; it does not create a duplicate.
     *
     * @param int    $cid_id
     * @param int    $bd_uid         lead owner
     * @param string $dm_name
     * @param string $dm_designation
     * @return array  ['created' => bool, 'updated' => bool, 'id' => int]
     */
    public function auto_create_from_dm_contact($cid_id, $bd_uid, $dm_name, $dm_designation)
    {
        if (empty($dm_name) || trim($dm_name) === '') {
            return ['created' => false, 'updated' => false, 'id' => null];
        }

        if (!$this->is_lead_in_pilot($bd_uid)) {
            return ['created' => false, 'updated' => false, 'id' => null];
        }

        $existing = $this->db
            ->where('cid_id', (int) $cid_id)
            ->where('is_dm_contact_backfill', 1)
            ->where('is_deleted', 0)
            ->get('school_stakeholder')
            ->row_array();

        if ($existing) {
            // Update only if name or designation changed
            if ($existing['full_name'] !== $dm_name
                || $existing['designation'] !== $dm_designation) {
                $this->db->where('id', $existing['id'])
                         ->update('school_stakeholder', [
                             'full_name'   => trim(substr($dm_name, 0, 128)),
                             'designation' => trim(substr($dm_designation, 0, 128)),
                         ]);
                return ['created' => false, 'updated' => true, 'id' => (int) $existing['id']];
            }
            return ['created' => false, 'updated' => false, 'id' => (int) $existing['id']];
        }

        $insert = [
            'cid_id'                => (int) $cid_id,
            'created_by_uid'        => (int) $bd_uid,
            'full_name'             => trim(substr($dm_name, 0, 128)),
            'designation'           => trim(substr($dm_designation, 0, 128)),
            'decision_role'         => 'DECISION_MAKER',
            'engagement_status'     => 'MET_ONCE',
            'is_dm_contact_backfill'=> 1,
            'created_at'            => date('Y-m-d H:i:s'),
        ];
        $this->db->insert('school_stakeholder', $insert);
        $new_id = (int) $this->db->insert_id();
        return ['created' => true, 'updated' => false, 'id' => $new_id];
    }

    // =========================================================
    // Graph traversal helpers
    // =========================================================

    /**
     * Find the shortest directed path from any stakeholder at the lead
     * to a DECISION_MAKER. Returns the path as an ordered array of
     * stakeholder rows, or an empty array if no path exists.
     *
     * Uses breadth-first search over edges in the school_stakeholder_relationship
     * table. Maximum depth: 5 hops (sufficient for K-12 school hierarchies).
     *
     * @param int $cid_id
     * @param int $start_stakeholder_id  Source node for BFS
     * @return array  Ordered list of stakeholder rows from start to DM node
     */
    public function find_path_to_dm($cid_id, $start_stakeholder_id)
    {
        $max_depth = 5;

        // Load all nodes and edges for this lead into memory
        $nodes = [];
        $raw   = $this->db->where('cid_id', (int) $cid_id)
                          ->where('is_deleted', 0)
                          ->get('school_stakeholder')
                          ->result_array();
        foreach ($raw as $n) {
            $nodes[$n['id']] = $n;
        }

        $edges = $this->db->where('cid_id', (int) $cid_id)
                          ->where('is_deleted', 0)
                          ->get('school_stakeholder_relationship')
                          ->result_array();

        // Build adjacency list: subject -> [object, ...]
        $adj = [];
        foreach ($edges as $e) {
            $adj[$e['subject_id']][] = $e['object_id'];
        }

        // BFS
        $visited = [(int) $start_stakeholder_id => true];
        $queue   = [[(int) $start_stakeholder_id]];

        while (!empty($queue)) {
            $path = array_shift($queue);
            $current = end($path);

            if (count($path) > $max_depth + 1) {
                break;
            }

            if (isset($nodes[$current])
                && $nodes[$current]['decision_role'] === 'DECISION_MAKER'
                && $current !== (int) $start_stakeholder_id) {
                // Build result: array of stakeholder rows in path order
                $result = [];
                foreach ($path as $node_id) {
                    if (isset($nodes[$node_id])) {
                        $result[] = $nodes[$node_id];
                    }
                }
                return $result;
            }

            if (isset($adj[$current])) {
                foreach ($adj[$current] as $neighbor) {
                    if (!isset($visited[$neighbor])) {
                        $visited[$neighbor] = true;
                        $new_path = $path;
                        $new_path[] = $neighbor;
                        $queue[] = $new_path;
                    }
                }
            }
        }

        return [];
    }

    /**
     * Return all CHAMPION stakeholders for a lead.
     * Includes their engagement_status and any edges they have to
     * DECISION_MAKER nodes.
     *
     * @param int $cid_id
     * @return array  Stakeholder rows with a 'has_dm_path' bool added
     */
    public function get_champions_for_lead($cid_id)
    {
        $champions = $this->db
            ->where('cid_id', (int) $cid_id)
            ->where('decision_role', 'CHAMPION')
            ->where('is_deleted', 0)
            ->get('school_stakeholder')
            ->result_array();

        // For each champion, check whether they have any direct or indirect
        // edge to a DECISION_MAKER (one hop check only for performance)
        $dm_ids = $this->_get_dm_ids_for_lead($cid_id);

        foreach ($champions as &$c) {
            $direct_dm_edge = $this->db
                ->where('subject_id', (int) $c['id'])
                ->where_in('object_id', $dm_ids ?: [0])
                ->where('is_deleted', 0)
                ->count_all_results('school_stakeholder_relationship');
            $c['has_dm_path'] = $direct_dm_edge > 0;
        }
        unset($c);
        return $champions;
    }

    /**
     * Return all BLOCKER stakeholders for a lead.
     * Includes what they are blocking (edges where they are subject
     * and relationship_type = 'blocks').
     *
     * @param int $cid_id
     * @return array  Stakeholder rows with a 'blocking_ids' array added
     */
    public function get_blockers_for_lead($cid_id)
    {
        $blockers = $this->db
            ->where('cid_id', (int) $cid_id)
            ->where('decision_role', 'BLOCKER')
            ->where('is_deleted', 0)
            ->get('school_stakeholder')
            ->result_array();

        foreach ($blockers as &$b) {
            $block_edges = $this->db
                ->select('object_id')
                ->where('subject_id', (int) $b['id'])
                ->where('relationship_type', 'blocks')
                ->where('is_deleted', 0)
                ->get('school_stakeholder_relationship')
                ->result_array();
            $b['blocking_ids'] = array_column($block_edges, 'object_id');
        }
        unset($b);
        return $blockers;
    }

    // =========================================================
    // Aggregate queries (used by controller endpoints 8 and 9)
    // =========================================================

    /**
     * Return leads at cstatus 6+ with no DECISION_MAKER mapped.
     * Scoped to pilot uids when flag=1, all uids when flag=2.
     *
     * @return array
     */
    public function get_leads_missing_dm()
    {
        $flag = $this->get_flag_value();
        if ($flag === 0) {
            return [];
        }

        $this->db->select('ic.id AS cid_id, cm.compname AS school_name, ic.cstatus, ic.mainbd AS bd_uid,
            u.name AS bd_name,
            ic.updated_at AS cstatus_updated_at,
            DATEDIFF(NOW(), ic.updated_at) AS days_at_cstatus,
            (SELECT COUNT(*) FROM school_stakeholder s2
             WHERE s2.cid_id = ic.id AND s2.is_deleted = 0) AS total_stakeholders_mapped')
                 ->from('init_call ic')
                 ->join('company_master cm', 'cm.id = ic.cmpid_id', 'left')
                 ->join('user u', 'u.uid = ic.mainbd', 'left')
                 ->where_in('ic.cstatus', [6, 7, 8, 9])
                 ->where('(ic.deletebd IS NULL OR ic.deletebd = 0)', null, false)
                 ->where("NOT EXISTS (
                     SELECT 1 FROM school_stakeholder ss
                     WHERE ss.cid_id = ic.id
                       AND ss.decision_role = 'DECISION_MAKER'
                       AND ss.is_deleted = 0
                 )", null, false);

        if ($flag === 1) {
            $this->db->where_in('ic.mainbd', self::PILOT_UIDS);
        }

        return $this->db->order_by('ic.cstatus', 'DESC')
                        ->order_by('days_at_cstatus', 'DESC')
                        ->get()
                        ->result_array();
    }

    /**
     * Return per-BD stakeholder coverage summary.
     * Scoped to pilot uids when flag=1.
     *
     * @return array
     */
    public function get_summary_by_bd()
    {
        $flag = $this->get_flag_value();
        if ($flag === 0) {
            return [];
        }

        $this->db->select('ic.mainbd AS bd_uid,
            u.name AS bd_name,
            COUNT(DISTINCT ic.id) AS total_leads,
            SUM(CASE WHEN dm.cid_id IS NOT NULL THEN 1 ELSE 0 END) AS leads_with_dm,
            SUM(CASE WHEN dm.cid_id IS NULL THEN 1 ELSE 0 END) AS leads_without_dm,
            COALESCE(SUM(sc.stakeholder_count), 0) AS total_stakeholders,
            ROUND(COALESCE(AVG(sc.stakeholder_count), 0), 1) AS avg_per_lead')
                 ->from('init_call ic')
                 ->join('user u', 'u.uid = ic.mainbd', 'left')
                 ->join(
                     '(SELECT cid_id FROM school_stakeholder
                       WHERE decision_role = "DECISION_MAKER" AND is_deleted = 0
                       GROUP BY cid_id) dm',
                     'dm.cid_id = ic.id',
                     'left'
                 )
                 ->join(
                     '(SELECT cid_id, COUNT(*) AS stakeholder_count FROM school_stakeholder
                       WHERE is_deleted = 0 GROUP BY cid_id) sc',
                     'sc.cid_id = ic.id',
                     'left'
                 )
                 ->where_in('ic.cstatus', [6, 7, 8, 9])
                 ->where('(ic.deletebd IS NULL OR ic.deletebd = 0)', null, false);

        if ($flag === 1) {
            $this->db->where_in('ic.mainbd', self::PILOT_UIDS);
        }

        return $this->db->group_by('ic.mainbd, u.name')
                        ->order_by('leads_without_dm', 'DESC')
                        ->get()
                        ->result_array();
    }

    /**
     * Count DECISION_MAKER stakeholders for a lead.
     * Called by M012 progression gate before allowing cstatus 6 to 8.
     *
     * @param int $cid_id
     * @return int
     */
    public function count_dm_for_lead($cid_id)
    {
        return (int) $this->db
            ->where('cid_id', (int) $cid_id)
            ->where('decision_role', 'DECISION_MAKER')
            ->where('is_deleted', 0)
            ->count_all_results('school_stakeholder');
    }

    // =========================================================
    // Private helpers
    // =========================================================

    /**
     * Validate fields for add_stakeholder().
     *
     * @param array $data
     * @return string|null  Error message or null if valid
     */
    private function _validate_stakeholder_data(array $data)
    {
        if (empty($data['cid_id'])) {
            return 'cid_id is required';
        }
        if (empty($data['full_name']) || trim($data['full_name']) === '') {
            return 'full_name is required';
        }
        if (empty($data['decision_role'])
            || !in_array($data['decision_role'], self::DECISION_ROLES, true)) {
            return 'decision_role must be one of: ' . implode(', ', self::DECISION_ROLES);
        }
        if (isset($data['engagement_status'])
            && !in_array($data['engagement_status'], self::ENGAGEMENT_STATUSES, true)) {
            return 'engagement_status must be one of: ' . implode(', ', self::ENGAGEMENT_STATUSES);
        }
        return null;
    }

    /**
     * Return the ids of all DECISION_MAKER stakeholders for a lead.
     *
     * @param int $cid_id
     * @return array  Array of int ids
     */
    private function _get_dm_ids_for_lead($cid_id)
    {
        $rows = $this->db
            ->select('id')
            ->where('cid_id', (int) $cid_id)
            ->where('decision_role', 'DECISION_MAKER')
            ->where('is_deleted', 0)
            ->get('school_stakeholder')
            ->result_array();
        return array_column($rows, 'id');
    }

    /**
     * Mask a contact string for non-owner callers.
     * Shows first 2 chars then asterisks.
     *
     * @param string|null $value
     * @return string|null
     */
    private function _mask_contact($value)
    {
        if (empty($value)) {
            return $value;
        }
        $visible = substr($value, 0, 2);
        return $visible . str_repeat('*', max(0, strlen($value) - 2));
    }
}
/* end StakeholderMap_model.php */
