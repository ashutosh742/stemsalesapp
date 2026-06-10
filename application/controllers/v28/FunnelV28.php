<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * FunnelV28 Controller
 *
 * Exposes 12 funnel-analysis API routes for STEM CRM v2.8.
 * Tables used: init_call (leads), tblcallevents (events), company_master, user.
 * All responses: {"ok":true,"success":true,"rows":[...],"count":N}
 * All queries include LIMIT 100 for safety.
 *
 * cstatus values (INT): 1=Open, 2=Reachout, 3=Tentative, 6=Positive,
 *   8=Open RPEM, 9=Very Positive, 12=Won, 13=Lost
 *
 * CREATION-PATH logic:
 *   BARGE_UNKNOWN    : init_call.new_lead=1 AND first tblcallevents.actiontype_id=4 AND purpose_id=66
 *   BARGE_FROM_FUNNEL: tblcallevents.actiontype_id=4 AND purpose_id=66 with prior init_call record
 *   RESEARCH_BORN    : init_call.new_lead=1 AND first tblcallevents.actiontype_id=10 AND purpose_id=94
 *   NEW_LEAD_FORM    : init_call.creator_id=mainbd AND first tblcallevents.actiontype_id=1 AND purpose_id=1
 *   ADMIN_CREATED    : init_call.creator_id != mainbd AND no tblcallevents row exists
 *   EXCEL_IMPORTED   : batch createDate pattern OR missing creator_id (creator_id=0 or NULL)
 */
class FunnelV28 extends CI_Controller {

    /** rimlyproof_myleads_default_20260608: uid resolved from per-user login token, 0 if master/digest */
    private $auth_uid = 0;

    /** Bearer token for all read endpoints */
    const BEARER = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->output->set_content_type('application/json');
    }

    // -------------------------------------------------------------------------
    // HELPERS
    // -------------------------------------------------------------------------

    /**
     * auth_check
     * Validates Authorization: Bearer <token> header.
     * Returns false and sends 401 if invalid.
     */
    private function auth_check()
    {
        $header = $this->input->get_request_header('Authorization', TRUE);
        $expected = 'Bearer ' . self::BEARER;
        // master/digest token path (unchanged)
        if ($header && trim($header) === $expected) {
            return true;
        }
        // rimlyproof_bearerdelegate_20260608: ALSO accept per-user login token
        $this->load->library('BearerAuth');
        $ba = $this->bearerauth->resolve();
        if (!empty($ba['ok']) && !empty($ba['uid'])) {
            $this->auth_uid = (int)$ba['uid'];
            // rimlyproof_leadscope_20260609: a FIELD user (BD/ACM) is hard-locked
            // to their OWN funnel. Override any bd_uid/uid param so every drill-down
            // method below (which all read $this->input->get('bd_uid'|'uid') i.e.
            // $_GET) is scoped to the caller. Managers/system are NOT overridden and
            // keep team/org-wide visibility. Single source of truth on the role.
            $role = isset($ba['role']) ? strtolower((string)$ba['role']) : '';
            if ($role === 'bd' || $role === 'acm') {
                $_GET['bd_uid']     = (int)$this->auth_uid;
                $_GET['uid']        = (int)$this->auth_uid;
                $_REQUEST['bd_uid'] = (int)$this->auth_uid;
                $_REQUEST['uid']    = (int)$this->auth_uid;
            }
            return true;
        }
        $this->json_out(['ok' => false, 'error' => 'unauthorized'], 401);
        return false;
    }

    /**
     * json_out
     * Sends JSON and exits.
     */
    private function json_out($data, $status = 200)
    {
        $this->output
             ->set_status_header($status)
             ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    /**
     * empty_ok
     * Standard empty-data envelope.
     */
    private function empty_ok($note = 'no_data')
    {
        return ['ok' => true, 'success' => true, 'rows' => [], 'count' => 0, 'note' => $note];
    }

    /**
     * rows_ok
     * Standard rows envelope.
     */
    private function rows_ok($rows, $extra = [])
    {
        $base = ['ok' => true, 'success' => true, 'rows' => $rows, 'count' => count($rows)];
        return array_merge($base, $extra);
    }

    /**
     * cstatus_label
     * Maps INT cstatus to human-readable label.
     */
    private function cstatus_label($cstatus)
    {
        $map = [
            1  => 'Open',
            2  => 'Reachout',
            3  => 'Tentative',
            6  => 'Positive',
            8  => 'Open RPEM',
            9  => 'Very Positive',
            12 => 'Won',
            13 => 'Lost',
        ];
        return isset($map[(int)$cstatus]) ? $map[(int)$cstatus] : 'Unknown';
    }

    // -------------------------------------------------------------------------
    // ENDPOINTS
    // -------------------------------------------------------------------------

    /**
     * all
     * GET /api/funnel/all
     *
     * Returns up to 100 leads from init_call joined with company name and
     * BD user name. Supports optional ?cstatus=INT and ?bd_uid=INT filters.
     */
    public function all()
    {
        if (!$this->auth_check()) return;

        $cstatus = $this->input->get('cstatus');
        $bd_uid  = $this->input->get('bd_uid');

        $sql = "
            SELECT
                ic.id,
                ic.cstatus,
                ic.createDate,
                ic.mainbd,
                ic.fbudget,
                ic.closure_pipeline,
                ic.new_lead,
                cm.compname AS company_name,
                u.name AS bd_name
            FROM init_call ic
            LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
            LEFT JOIN user u ON u.uid = ic.mainbd
            WHERE 1=1
        ";
        $binds = [];

        if ($cstatus !== false && $cstatus !== null && $cstatus !== '') {
            $sql .= " AND ic.cstatus = ?";
            $binds[] = (int)$cstatus;
        }
        if ($bd_uid !== false && $bd_uid !== null && $bd_uid !== '') {
            $sql .= " AND ic.mainbd = ?";
            $binds[] = (int)$bd_uid;
        }

        $sql .= " ORDER BY ic.createDate DESC LIMIT 100";

        $result = $this->db->query($sql, $binds);
        $rows = $result ? $result->result_array() : [];

        if (empty($rows)) {
            return $this->json_out($this->empty_ok());
        }

        foreach ($rows as &$row) {
            $row['cstatus']       = (int)$row['cstatus'];
            $row['cstatus_label'] = $this->cstatus_label($row['cstatus']);
        }
        unset($row);

        $this->json_out($this->rows_ok($rows));
    }

    /**
     * closing
     * GET /api/funnel/closing
     *
     * Returns leads in closure pipeline (closure_pipeline IS NOT NULL and not empty),
     * typically cstatus IN (6,9,3) with a closure date set. Supports ?bd_uid=INT.
     */
    public function closing()
    {
        if (!$this->auth_check()) return;

        $bd_uid = $this->input->get('bd_uid');

        $sql = "
            SELECT
                ic.id,
                ic.cstatus,
                ic.createDate,
                ic.mainbd,
                ic.fbudget,
                ic.closure_pipeline,
                cm.compname AS company_name,
                u.name AS bd_name
            FROM init_call ic
            LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
            LEFT JOIN user u ON u.uid = ic.mainbd
            WHERE ic.cstatus IN (3, 6, 9)
              AND ic.closure_pipeline IS NOT NULL
              AND ic.closure_pipeline != ''
        ";
        $binds = [];

        if ($bd_uid !== false && $bd_uid !== null && $bd_uid !== '') {
            $sql .= " AND ic.mainbd = ?";
            $binds[] = (int)$bd_uid;
        }

        $sql .= " ORDER BY ic.createDate DESC LIMIT 100";

        $result = $this->db->query($sql, $binds);
        $rows = $result ? $result->result_array() : [];

        if (empty($rows)) {
            return $this->json_out($this->empty_ok());
        }

        foreach ($rows as &$row) {
            $row['cstatus']       = (int)$row['cstatus'];
            $row['cstatus_label'] = $this->cstatus_label($row['cstatus']);
        }
        unset($row);

        $this->json_out($this->rows_ok($rows));
    }

    /**
     * lost
     * GET /api/funnel/lost
     *
     * Returns leads with cstatus=13 (Lost). Supports ?bd_uid=INT filter.
     */
    public function lost()
    {
        if (!$this->auth_check()) return;

        $bd_uid = $this->input->get('bd_uid');

        $sql = "
            SELECT
                ic.id,
                ic.cstatus,
                ic.createDate,
                ic.updated_at,
                ic.mainbd,
                ic.fbudget,
                cm.compname AS company_name,
                u.name AS bd_name
            FROM init_call ic
            LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
            LEFT JOIN user u ON u.uid = ic.mainbd
            WHERE ic.cstatus = 13
        ";
        $binds = [];

        if ($bd_uid !== false && $bd_uid !== null && $bd_uid !== '') {
            $sql .= " AND ic.mainbd = ?";
            $binds[] = (int)$bd_uid;
        }

        $sql .= " ORDER BY ic.updated_at DESC LIMIT 100";

        $result = $this->db->query($sql, $binds);
        $rows = $result ? $result->result_array() : [];

        if (empty($rows)) {
            return $this->json_out($this->empty_ok());
        }

        foreach ($rows as &$row) {
            $row['cstatus'] = (int)$row['cstatus'];
            $row['cstatus_label'] = 'Lost';
        }
        unset($row);

        $this->json_out($this->rows_ok($rows));
    }

    /**
     * my_leads
     * GET /api/funnel/my_leads?bd_uid=INT
     *
     * Returns all active (non-Won, non-Lost) leads for the given BD user.
     */
    public function my_leads()
    {
        if (!$this->auth_check()) return;

        $bd_uid = (int)$this->input->get('bd_uid');
        // rimlyproof_myleads_default_20260608: default to the logged-in BD when no param
        if ($bd_uid <= 0 && $this->auth_uid > 0) {
            $bd_uid = $this->auth_uid;
        }
        if ($bd_uid <= 0) {
            return $this->json_out(['ok' => false, 'error' => 'bd_uid is required'], 400);
        }

        $sql = "
            SELECT
                ic.id,
                ic.cstatus,
                ic.createDate,
                ic.updated_at,
                ic.fbudget,
                ic.closure_pipeline,
                ic.new_lead,
                cm.compname AS company_name,
                (
                    SELECT t.date
                    FROM tblcallevents t
                    WHERE t.cid_id = ic.id
                    ORDER BY t.date DESC
                    LIMIT 1
                ) AS last_touch_date
            FROM init_call ic
            LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
            WHERE ic.mainbd = ?
              AND (ic.cstatus NOT IN (12, 13) OR ic.cstatus IS NULL)
            ORDER BY ic.updated_at DESC
            LIMIT 100
        ";

        $result = $this->db->query($sql, [(int)$bd_uid]);
        $rows = $result ? $result->result_array() : [];

        if (empty($rows)) {
            return $this->json_out($this->empty_ok());
        }

        foreach ($rows as &$row) {
            $row['cstatus']       = (int)$row['cstatus'];
            $row['cstatus_label'] = $this->cstatus_label($row['cstatus']);
        }
        unset($row);

        $this->json_out($this->rows_ok($rows, ['bd_uid' => $bd_uid]));
    }

    /**
     * new
     * GET /api/funnel/new
     *
     * Returns recently created leads (cstatus=1, Open) within last 30 days,
     * with creation-path classification.
     */
    public function new_leads()
    {
        if (!$this->auth_check()) return;

        $since = date('Y-m-d', strtotime('-30 days'));

        // rimlyproof_newleads_scope_20260609: scope to caller for FIELD users.
        $nl_bd  = $this->input->get('bd_uid');
        $nl_has = ($nl_bd !== false && $nl_bd !== null && $nl_bd !== '');
        $nl_and = $nl_has ? (' AND ic.mainbd = ' . (int)$nl_bd) : '';

        $sql = "
            SELECT
                ic.id,
                ic.cstatus,
                ic.createDate,
                ic.mainbd,
                ic.creator_id,
                ic.new_lead,
                cm.compname AS company_name,
                u.name AS bd_name,
                (
                    SELECT t2.actiontype_id
                    FROM tblcallevents t2
                    WHERE t2.cid_id = ic.id
                    ORDER BY t2.id ASC
                    LIMIT 1
                ) AS first_actiontype_id,
                (
                    SELECT t2.purpose_id
                    FROM tblcallevents t2
                    WHERE t2.cid_id = ic.id
                    ORDER BY t2.id ASC
                    LIMIT 1
                ) AS first_purpose_id,
                (
                    SELECT COUNT(*)
                    FROM tblcallevents t3
                    WHERE t3.cid_id = ic.id
                ) AS event_count
            FROM init_call ic
            LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
            LEFT JOIN user u ON u.uid = ic.mainbd
            WHERE ic.cstatus = 1
              AND ic.createDate >= ?
              AND ic.createDate != '0000-00-00'
              {$nl_and}
            ORDER BY ic.createDate DESC
            LIMIT 100
        ";

        $result = $this->db->query($sql, [$since]);
        $rows = $result ? $result->result_array() : [];

        if (empty($rows)) {
            return $this->json_out($this->empty_ok());
        }

        foreach ($rows as &$row) {
            $row['cstatus']        = (int)$row['cstatus'];
            $row['cstatus_label']  = $this->cstatus_label($row['cstatus']);
            $row['creation_path']  = $this->_classify_creation_path(
                $row['new_lead'],
                $row['creator_id'],
                $row['mainbd'],
                (int)$row['first_actiontype_id'],
                (int)$row['first_purpose_id'],
                (int)$row['event_count']
            );
        }
        unset($row);

        $this->json_out($this->rows_ok($rows, ['since' => $since]));
    }

    /**
     * no_dm
     * GET /api/funnel/no_dm
     *
     * Returns active leads (not Won/Lost) where DM contact name is missing.
     * Supports ?bd_uid=INT filter.
     */
    public function no_dm()
    {
        if (!$this->auth_check()) return;

        $bd_uid = $this->input->get('bd_uid');

        $sql = "
            SELECT
                ic.id,
                ic.cstatus,
                ic.createDate,
                ic.mainbd,
                ic.fbudget,
                cm.compname AS company_name,
                u.name AS bd_name
            FROM init_call ic
            LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
            LEFT JOIN user u ON u.uid = ic.mainbd
            WHERE (ic.dm_contact_name IS NULL OR ic.dm_contact_name = '')
              AND ic.cstatus NOT IN (12, 13)
        ";
        $binds = [];

        if ($bd_uid !== false && $bd_uid !== null && $bd_uid !== '') {
            $sql .= " AND ic.mainbd = ?";
            $binds[] = (int)$bd_uid;
        }

        $sql .= " ORDER BY ic.createDate DESC LIMIT 100";

        $result = $this->db->query($sql, $binds);
        $rows = $result ? $result->result_array() : [];

        if (empty($rows)) {
            return $this->json_out($this->empty_ok());
        }

        foreach ($rows as &$row) {
            $row['cstatus']       = (int)$row['cstatus'];
            $row['cstatus_label'] = $this->cstatus_label($row['cstatus']);
        }
        unset($row);

        $this->json_out($this->rows_ok($rows));
    }

    /**
     * promotions
     * GET /api/funnel/promotions
     *
     * Returns leads that advanced (cstatus improved) within last 30 days,
     * detected via tblcallevents targetstatus transitions where
     * targetstatus > status_id (numeric cstatus advancement).
     * Supports ?bd_uid=INT filter.
     */
    public function promotions()
    {
        if (!$this->auth_check()) return;

        $bd_uid = $this->input->get('bd_uid');
        $since  = date('Y-m-d', strtotime('-30 days'));

        $sql = "
            SELECT
                ic.id,
                ic.cstatus,
                ic.createDate,
                ic.mainbd,
                ic.fbudget,
                cm.compname AS company_name,
                u.name AS bd_name,
                t.status_id AS from_status,
                t.targetstatus AS to_status,
                t.date AS transition_date
            FROM tblcallevents t
            JOIN init_call ic ON ic.id = t.cid_id
            LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
            LEFT JOIN user u ON u.uid = ic.mainbd
            WHERE t.targetstatus IS NOT NULL
              AND t.targetstatus > 0
              AND t.targetstatus != t.status_id
              AND DATE(t.date) >= ?
        ";
        $binds = [$since];

        if ($bd_uid !== false && $bd_uid !== null && $bd_uid !== '') {
            $sql .= " AND ic.mainbd = ?";
            $binds[] = (int)$bd_uid;
        }

        $sql .= " ORDER BY t.date DESC LIMIT 100";

        $result = $this->db->query($sql, $binds);
        $rows = $result ? $result->result_array() : [];

        if (empty($rows)) {
            return $this->json_out($this->empty_ok());
        }

        foreach ($rows as &$row) {
            $row['cstatus']        = (int)$row['cstatus'];
            $row['cstatus_label']  = $this->cstatus_label($row['cstatus']);
            $row['from_status']    = (int)$row['from_status'];
            $row['to_status']      = (int)$row['to_status'];
            $row['from_label']     = $this->cstatus_label($row['from_status']);
            $row['to_label']       = $this->cstatus_label($row['to_status']);
        }
        unset($row);

        $this->json_out($this->rows_ok($rows, ['since' => $since]));
    }

    /**
     * stage_counts
     * GET /api/funnel/stage_counts
     *
     * Returns count of leads grouped by cstatus (all stages).
     * Supports ?bd_uid=INT to filter by BD.
     */
    public function stage_counts()
    {
        if (!$this->auth_check()) return;

        $bd_uid = $this->input->get('bd_uid');

        $sql = "
            SELECT
                ic.cstatus,
                COUNT(*) AS lead_count
            FROM init_call ic
            WHERE 1=1
        ";
        $binds = [];

        if ($bd_uid !== false && $bd_uid !== null && $bd_uid !== '') {
            $sql .= " AND ic.mainbd = ?";
            $binds[] = (int)$bd_uid;
        }

        $sql .= " GROUP BY ic.cstatus ORDER BY ic.cstatus ASC LIMIT 100";

        $result = $this->db->query($sql, $binds);
        $raw = $result ? $result->result_array() : [];

        if (empty($raw)) {
            return $this->json_out($this->empty_ok());
        }

        $rows = [];
        foreach ($raw as $r) {
            $rows[] = [
                'cstatus'      => (int)$r['cstatus'],
                'cstatus_label'=> $this->cstatus_label($r['cstatus']),
                'lead_count'   => (int)$r['lead_count'],
            ];
        }

        $total = array_sum(array_column($rows, 'lead_count'));

        $this->json_out([
            'ok'      => true,
            'success' => true,
            'rows'    => $rows,
            'count'   => count($rows),
            'total_leads' => $total,
        ]);
    }

    /**
     * stuck
     * GET /api/funnel/stuck
     *
     * Returns leads that have not had any tblcallevents activity in 7+ days
     * and are still active (cstatus not in 12,13).
     * Supports ?bd_uid=INT filter and ?days=INT (default 7).
     */
    public function stuck()
    {
        if (!$this->auth_check()) return;

        $bd_uid   = $this->input->get('bd_uid');
        $days_raw = $this->input->get('days');
        $days     = ($days_raw && (int)$days_raw > 0) ? (int)$days_raw : 7;
        $cutoff   = date('Y-m-d', strtotime("-{$days} days"));

        // Optimized: filter init_call first (small set), THEN look up max(date) via
        // correlated subquery only for those rows. Avoids full GROUP BY over 200k
        // tblcallevents rows. LIMIT applied early.
        $binds = [];
        $where = " WHERE ic.cstatus NOT IN (12, 13) AND ic.cstatus IS NOT NULL";
        if ($bd_uid !== false && $bd_uid !== null && $bd_uid !== '') {
            $where .= " AND ic.mainbd = ?";
            $binds[] = (int)$bd_uid;
        }
        // Also bound init_call to last 18 months so we never scan the full archive.
        $where .= " AND ic.createDate >= DATE_SUB(NOW(), INTERVAL 18 MONTH)";

        // Step 1: candidate init_call ids (cheap, indexed)
        $sql_cand = "SELECT ic.id, ic.cstatus, ic.createDate, ic.mainbd, ic.fbudget, ic.cmpid_id FROM init_call ic $where ORDER BY ic.id DESC LIMIT 2000";
        $cand_res = $this->db->query($sql_cand, $binds);
        $candidates = $cand_res ? $cand_res->result_array() : [];
        if (empty($candidates)) {
            return $this->json_out($this->empty_ok(['stuck_threshold_days' => $days]));
        }
        $cand_ids = array_map(function($r){return (int)$r['id'];}, $candidates);
        $bd_ids   = array_unique(array_map(function($r){return (int)$r['mainbd'];}, $candidates));
        $cmp_ids  = array_unique(array_filter(array_map(function($r){return (int)$r['cmpid_id'];}, $candidates)));

        // Step 2: max(date) for those ids (indexed lookup on cid_id)
        $in_ids = implode(',', $cand_ids);
        $last_map = [];
        $last_res = $this->db->query("SELECT cid_id, MAX(date) AS last_event_date FROM tblcallevents WHERE cid_id IN ($in_ids) GROUP BY cid_id");
        if ($last_res) {
            foreach ($last_res->result_array() as $lr) {
                $last_map[(int)$lr['cid_id']] = $lr['last_event_date'];
            }
        }

        // Step 3: name lookups
        $cmp_map = [];
        if (!empty($cmp_ids)) {
            $in_cmp = implode(',', $cmp_ids);
            $cr = $this->db->query("SELECT id, compname FROM company_master WHERE id IN ($in_cmp)");
            if ($cr) foreach ($cr->result_array() as $crow) $cmp_map[(int)$crow['id']] = $crow['compname'];
        }
        $bd_map = [];
        $bd_ids_filtered = array_filter($bd_ids);
        if (!empty($bd_ids_filtered)) {
            $in_bd = implode(',', $bd_ids_filtered);
            $br = $this->db->query("SELECT uid, name FROM user WHERE uid IN ($in_bd)");
            if ($br) foreach ($br->result_array() as $brow) $bd_map[(int)$brow['uid']] = $brow['name'];
        }

        // Step 4: build stuck rows in PHP
        $rows = [];
        $cutoff_ts = strtotime($cutoff);
        foreach ($candidates as $c) {
            $cid = (int)$c['id'];
            $last = $last_map[$cid] ?? null;
            $last_ts = $last ? strtotime($last) : null;
            if ($last !== null && $last_ts >= $cutoff_ts) continue;
            $row = [
                'id'             => $c['id'],
                'cstatus'        => (int)$c['cstatus'],
                'cstatus_label'  => $this->cstatus_label((int)$c['cstatus']),
                'createDate'     => $c['createDate'],
                'mainbd'         => $c['mainbd'],
                'fbudget'        => $c['fbudget'],
                'company_name'   => $cmp_map[(int)$c['cmpid_id']] ?? null,
                'bd_name'        => $bd_map[(int)$c['mainbd']] ?? null,
                'last_event_date'=> $last,
                'days_since_touch' => $last_ts ? (int)floor((time() - $last_ts) / 86400) : null,
            ];
            $rows[] = $row;
            if (count($rows) >= 100) break;
        }

        if (empty($rows)) {
            return $this->json_out($this->empty_ok(['stuck_threshold_days' => $days]));
        }

        $this->json_out($this->rows_ok($rows, ['stuck_threshold_days' => $days]));
    }

    /**
     * summary
     * GET /api/funnel/summary
     *
     * Returns a cohort-level summary:
     * - Total leads per creation path
     * - Conversion rate to Won (cstatus=12)
     * - Funnel drop rates across stages
     */
    public function summary()
    {
        if (!$this->auth_check()) return;

        // rimlyproof_summary_scope_20260609: scope summary to the caller for FIELD
        // users. auth_check() has already forced $_GET['bd_uid'] to a BD/ACM's own
        // uid; managers/system pass nothing here and keep org-wide visibility.
        $sc_bd = $this->input->get('bd_uid');
        $sc_has = ($sc_bd !== false && $sc_bd !== null && $sc_bd !== '');
        $sc_where = $sc_has ? (' WHERE ic.mainbd = ' . (int)$sc_bd) : '';
        $sc_and   = $sc_has ? (' AND ic.mainbd = ' . (int)$sc_bd)   : '';

        // Stage counts
        $sql_stages = "
            SELECT
                ic.cstatus,
                COUNT(*) AS cnt
            FROM init_call ic
            {$sc_where}
            GROUP BY ic.cstatus
            ORDER BY ic.cstatus ASC
            LIMIT 100
        ";
        $res_stages = $this->db->query($sql_stages);
        $stage_rows = $res_stages ? $res_stages->result_array() : [];

        $stage_map = [];
        $total = 0;
        foreach ($stage_rows as $r) {
            $cs = (int)$r['cstatus'];
            $stage_map[$cs] = (int)$r['cnt'];
            $total += (int)$r['cnt'];
        }

        $won  = isset($stage_map[12]) ? $stage_map[12] : 0;
        $lost = isset($stage_map[13]) ? $stage_map[13] : 0;
        $active = $total - $won - $lost;
        $conv_rate = $total > 0 ? round($won / $total * 100, 2) : 0;

        // Creation path distribution
        $sql_paths = "
            SELECT
                ic.new_lead,
                ic.creator_id,
                ic.mainbd,
                (
                    SELECT t2.actiontype_id
                    FROM tblcallevents t2
                    WHERE t2.cid_id = ic.id
                    ORDER BY t2.id ASC
                    LIMIT 1
                ) AS first_actiontype_id,
                (
                    SELECT t2.purpose_id
                    FROM tblcallevents t2
                    WHERE t2.cid_id = ic.id
                    ORDER BY t2.id ASC
                    LIMIT 1
                ) AS first_purpose_id,
                (
                    SELECT COUNT(*)
                    FROM tblcallevents t3
                    WHERE t3.cid_id = ic.id
                ) AS event_count
            FROM init_call ic
            {$sc_where}
            LIMIT 100
        ";
        $res_paths = $this->db->query($sql_paths);
        $path_rows = $res_paths ? $res_paths->result_array() : [];

        $path_counts = [
            'BARGE_UNKNOWN'     => 0,
            'BARGE_FROM_FUNNEL' => 0,
            'RESEARCH_BORN'     => 0,
            'NEW_LEAD_FORM'     => 0,
            'ADMIN_CREATED'     => 0,
            'EXCEL_IMPORTED'    => 0,
            'UNKNOWN'           => 0,
        ];

        foreach ($path_rows as $r) {
            $path = $this->_classify_creation_path(
                $r['new_lead'],
                $r['creator_id'],
                $r['mainbd'],
                (int)$r['first_actiontype_id'],
                (int)$r['first_purpose_id'],
                (int)$r['event_count']
            );
            if (isset($path_counts[$path])) {
                $path_counts[$path]++;
            } else {
                $path_counts['UNKNOWN']++;
            }
        }

        $stages_out = [];
        foreach ($stage_rows as $r) {
            $cs = (int)$r['cstatus'];
            $stages_out[] = [
                'cstatus'       => $cs,
                'cstatus_label' => $this->cstatus_label($cs),
                'count'         => (int)$r['cnt'],
            ];
        }

        $this->json_out([
            'ok'              => true,
            'success'         => true,
            'total_leads'     => $total,
            'active_leads'    => $active,
            'won_leads'       => $won,
            'lost_leads'      => $lost,
            'conversion_rate_pct' => $conv_rate,
            'stage_breakdown' => $stages_out,
            'creation_paths'  => $path_counts,
            'note'            => 'creation_paths sampled from top 100 rows only',
        ]);
    }

    /**
     * transfers
     * GET /api/funnel/transfers
     *
     * Returns leads where mainbd changed (detected by tblcallevents reassign_type IS NOT NULL).
     * Supports ?bd_uid=INT (filter by current mainbd).
     */
    public function transfers()
    {
        if (!$this->auth_check()) return;

        $bd_uid = $this->input->get('bd_uid');

        $sql = "
            SELECT
                ic.id,
                ic.cstatus,
                ic.createDate,
                ic.mainbd,
                ic.fbudget,
                cm.compname AS company_name,
                u.name AS bd_name,
                t.date AS transfer_date,
                t.reassign_type
            FROM tblcallevents t
            JOIN init_call ic ON ic.id = t.cid_id
            LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
            LEFT JOIN user u ON u.uid = ic.mainbd
            WHERE t.reassign_type IS NOT NULL
        ";
        $binds = [];

        if ($bd_uid !== false && $bd_uid !== null && $bd_uid !== '') {
            $sql .= " AND ic.mainbd = ?";
            $binds[] = (int)$bd_uid;
        }

        $sql .= " ORDER BY t.date DESC LIMIT 100";

        $result = $this->db->query($sql, $binds);
        $rows = $result ? $result->result_array() : [];

        if (empty($rows)) {
            return $this->json_out($this->empty_ok());
        }

        foreach ($rows as &$row) {
            $row['cstatus']       = (int)$row['cstatus'];
            $row['cstatus_label'] = $this->cstatus_label($row['cstatus']);
        }
        unset($row);

        $this->json_out($this->rows_ok($rows));
    }

    /**
     * won
     * GET /api/funnel/won
     *
     * Returns leads with cstatus=12 (Won). Supports ?bd_uid=INT filter.
     */
    public function won()
    {
        if (!$this->auth_check()) return;

        $bd_uid = $this->input->get('bd_uid');

        $sql = "
            SELECT
                ic.id,
                ic.cstatus,
                ic.createDate,
                ic.updated_at,
                ic.mainbd,
                ic.fbudget,
                ic.closure_pipeline,
                cm.compname AS company_name,
                u.name AS bd_name
            FROM init_call ic
            LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
            LEFT JOIN user u ON u.uid = ic.mainbd
            WHERE ic.cstatus = 12
        ";
        $binds = [];

        if ($bd_uid !== false && $bd_uid !== null && $bd_uid !== '') {
            $sql .= " AND ic.mainbd = ?";
            $binds[] = (int)$bd_uid;
        }

        $sql .= " ORDER BY ic.updated_at DESC LIMIT 100";

        $result = $this->db->query($sql, $binds);
        $rows = $result ? $result->result_array() : [];

        if (empty($rows)) {
            return $this->json_out($this->empty_ok());
        }

        foreach ($rows as &$row) {
            $row['cstatus'] = (int)$row['cstatus'];
            $row['cstatus_label'] = 'Won';
        }
        unset($row);

        $this->json_out($this->rows_ok($rows));
    }

    // -------------------------------------------------------------------------
    // PRIVATE HELPERS
    // -------------------------------------------------------------------------

    /**
     * _classify_creation_path
     * Returns one of: BARGE_UNKNOWN, BARGE_FROM_FUNNEL, RESEARCH_BORN,
     *   NEW_LEAD_FORM, ADMIN_CREATED, EXCEL_IMPORTED, UNKNOWN
     *
     * @param mixed $new_lead         init_call.new_lead value
     * @param int   $creator_id       init_call.creator_id
     * @param int   $mainbd           init_call.mainbd
     * @param int   $first_action     first tblcallevents.actiontype_id for this lead
     * @param int   $first_purpose    first tblcallevents.purpose_id for this lead
     * @param int   $event_count      total tblcallevents rows for this lead
     */
    private function _classify_creation_path($new_lead, $creator_id, $mainbd,
                                              $first_action, $first_purpose, $event_count)
    {
        $creator_id  = (int)$creator_id;
        $mainbd      = (int)$mainbd;
        $new_lead_flag = ($new_lead == '1' || $new_lead === 1 || $new_lead === '1');

        // EXCEL_IMPORTED: creator_id is 0/NULL or batch pattern
        if ($creator_id == 0) {
            return 'EXCEL_IMPORTED';
        }

        // BARGE_UNKNOWN: new lead flag, first event is barge-in type
        if ($new_lead_flag && $first_action === 4 && $first_purpose === 66) {
            return 'BARGE_UNKNOWN';
        }

        // BARGE_FROM_FUNNEL: barge-in event with prior init_call (new_lead not set)
        if (!$new_lead_flag && $first_action === 4 && $first_purpose === 66) {
            return 'BARGE_FROM_FUNNEL';
        }

        // RESEARCH_BORN: new lead with research event
        if ($new_lead_flag && $first_action === 10 && $first_purpose === 94) {
            return 'RESEARCH_BORN';
        }

        // NEW_LEAD_FORM: creator is the BD (creator_id == mainbd), first event is form-type
        if ($creator_id === $mainbd && $first_action === 1 && $first_purpose === 1) {
            return 'NEW_LEAD_FORM';
        }

        // ADMIN_CREATED: creator differs from BD and no events exist
        if ($creator_id !== $mainbd && $event_count == 0) {
            return 'ADMIN_CREATED';
        }

        return 'UNKNOWN';
    }
}
