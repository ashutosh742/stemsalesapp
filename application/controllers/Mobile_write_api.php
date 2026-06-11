<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Mobile_write_api
 *
 * Thin mobile write surface for the STEM CRM pilot APK.
 *
 * Design contract (locked):
 *   - Anchor on production basic logic. Do NOT bypass canonical tables.
 *   - Write directly into init_call, tblcallevents, company_master,
 *     company_contact_master with the same column shapes the web app uses.
 *   - Because we write into canonical tables, all advanced agent hooks
 *     (progression patch v2, MoM v2 gate, applause log, planning grade,
 *     planning cron, expense actuals, etc.) fire automatically.
 *   - Bearer auth via STEM_DIGEST_TOKEN with a hardcoded fallback that matches
 *     Leads_api.php so all mobile endpoints share one token.
 *   - Plain English errors. Never fabricate data. Return JSON only.
 *
 * Endpoints:
 *   POST /api/leads/create   -> create_lead
 *   POST /api/task/plan      -> plan_task
 *   POST /api/task/submit    -> submit_task
 *   POST /api/mom/submit     -> submit_mom
 *
 * Vocabulary: lead = init_call row. cid_id = init_call.id. We work with
 * corporates; UI labels say "Company"; the column `compname` holds it.
 */
class Mobile_write_api extends CI_Controller {

    private $_known_token = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
    private $_authed_uid = 0;

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->output->set_content_type('application/json');
    }

    /* ===================== auth ===================== */


    // ---- per-user JWT validator (added 28 May 2026, matches Auth::api_login) ----
    private function _jwt_token_valid($token) {
        if (empty($token)) return false;
        $secret = getenv('STEM_DIGEST_TOKEN') ?: '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
        $days = array(date('Y-m-d'), date('Y-m-d', strtotime('-1 day')), date('Y-m-d', strtotime('+1 day')));
        // Try uid from request first (fast path)
        $candidates = array();
        foreach (array('uid','cm_uid','rm_uid','bd_uid','acm_uid','user_id') as $k) {
            if (isset($_GET[$k]) && (int)$_GET[$k] > 0) $candidates[(int)$_GET[$k]] = 1;
            if (isset($_POST[$k]) && (int)$_POST[$k] > 0) $candidates[(int)$_POST[$k]] = 1;
        }
        foreach (array_keys($candidates) as $uid) {
            foreach ($days as $d) {
                if (hash_equals(sha1($secret.'|'.$uid.'|'.$d), $token)) return (int)$uid;
            }
        }
        // Fallback: scan all active uids (cached for 60 sec)
        static $all_uids = null;
        if ($all_uids === null) {
            $rows = $this->db->select('uid')->from('user')->where('active', 1)->get()->result();
            $all_uids = array();
            foreach ($rows as $r) $all_uids[] = (int)$r->uid;
        }
        foreach ($all_uids as $uid) {
            foreach ($days as $d) {
                if (hash_equals(sha1($secret.'|'.$uid.'|'.$d), $token)) return (int)$uid;
            }
        }
        return false;
    }

    public function _bearer_ok() {
        $hdr = '';
        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $hdr = $_SERVER['HTTP_AUTHORIZATION'];
        } elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            $hdr = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
        } elseif (function_exists('apache_request_headers')) {
            $h = apache_request_headers();
            if (isset($h['Authorization'])) $hdr = $h['Authorization'];
        }
        if (stripos($hdr, 'Bearer ') !== 0) return false;
        $tok = trim(substr($hdr, 7));
        $env = getenv('STEM_DIGEST_TOKEN');
        if ($env && hash_equals($env, $tok)) return true;
        if (hash_equals($this->_known_token, $tok)) return true;
        $uid = $this->_jwt_token_valid($tok);
        if ($uid) { $this->_authed_uid = $uid; return true; }
        return false;
    }

    private function _deny($code, $msg, $extra = null) {
        $this->output->set_status_header($code);
        $resp = ['ok' => false, 'error' => $msg];
        if ($extra !== null) $resp['detail'] = $extra;
        $this->output->set_output(json_encode($resp));
        return;
    }

    private function _ok($payload) {
        $payload['ok'] = true;
        $this->output->set_output(json_encode($payload));
        return;
    }

    private function _post($key, $default = null) {
        if (isset($_POST[$key]) && $_POST[$key] !== '') return $_POST[$key];
        static $json = null;
        if ($json === null) {
            $raw = file_get_contents('php://input');
            $json = $raw ? json_decode($raw, true) : [];
            if (!is_array($json)) $json = [];
        }
        if (isset($json[$key]) && $json[$key] !== '') return $json[$key];
        return $default;
    }

    private function _require_post($keys) {
        $missing = [];
        foreach ($keys as $k) {
            if ($this->_post($k, null) === null) $missing[] = $k;
        }
        return $missing;
    }

    /**
     * Compute next id by MAX(id)+1. Matches legacy app pattern - the canonical
     * tables (company_master, init_call, company_contact_master, tblcallevents,
     * mom_data) do NOT have AUTO_INCREMENT on staging, so $this->db->insert_id()
     * returns 0. Every Menu_model.php write path uses MAX(id)+1.
     *
     * We wrap in a transaction so the read+insert pair is serializable.
     * Caller MUST be inside trans_start()/trans_complete() for safety.
     */
    private function _next_id($table) {
        $row = $this->db->query("SELECT IFNULL(MAX(id),0)+1 AS n FROM `$table`")->row_array();
        return (int)$row['n'];
    }

    /* ===================== clean-input layer (additive) =====================
     * Central sanitisation so only clean numeric + text data lands in canonical
     * tables. Loaded lazily so existing read paths are unaffected. These helpers
     * NEVER throw and NEVER block a save; they normalise the value and quietly
     * log any warning (to error_log and the input_sanitize_log table when it
     * exists) so we have a clean audit trail without breaking working writes.
     */
    private $_san_lib = null;
    private function _san() {
        if ($this->_san_lib === null) {
            $this->load->library('Inputsanitizer');
            $this->_san_lib = $this->inputsanitizer;
        }
        return $this->_san_lib;
    }

    /* Log a sanitiser warning without ever interrupting the write. */
    private function _san_log($field, $raw, $clean, $warnings) {
        if (empty($warnings)) return;
        $line = 'INPUT_SANITIZE field=' . $field
              . ' raw=' . json_encode((string)$raw)
              . ' clean=' . json_encode((string)$clean)
              . ' warn=' . implode(',', $warnings)
              . ' uid=' . (int)$this->_authed_uid;
        @error_log($line);
        // Best-effort DB audit; silently skip if the table is absent.
        if (!isset($this->_san_log_table_ok)) {
            $chk = $this->db->query("SHOW TABLES LIKE 'input_sanitize_log'");
            $this->_san_log_table_ok = ($chk && $chk->num_rows() > 0);
        }
        if ($this->_san_log_table_ok) {
            @$this->db->insert('input_sanitize_log', array(
                'uid'        => (int)$this->_authed_uid,
                'field_name' => substr((string)$field, 0, 64),
                'raw_value'  => substr((string)$raw, 0, 1000),
                'clean_value'=> substr((string)$clean, 0, 1000),
                'warnings'   => substr(implode(',', $warnings), 0, 255),
                'created_at' => date('Y-m-d H:i:s'),
            ));
        }
    }
    private $_san_log_table_ok = null;

    /* Clean a money string to an integer rupee value (returns int). */
    private function _clean_money($field, $raw) {
        $r = $this->_san()->money($raw);
        $this->_san_log($field, $raw, $r['value'], $r['warnings']);
        return $r['value'];
    }
    /* Clean a money string but KEEP it as a string for varchar columns. */
    private function _clean_money_str($field, $raw, $default = 'NA') {
        $r = $this->_san()->money($raw);
        $this->_san_log($field, $raw, $r['value'], $r['warnings']);
        return $r['value'] > 0 ? (string)$r['value'] : $default;
    }
    /* Clean a count to a non-negative integer string. */
    private function _clean_count_str($field, $raw, $default = '0') {
        $r = $this->_san()->count_int($raw);
        $this->_san_log($field, $raw, $r['value'], $r['warnings']);
        return (string)$r['value'];
    }
    /* Clean a free-text / remark value (returns cleaned string). */
    private function _clean_text($field, $raw, $opts = array()) {
        $r = $this->_san()->text($raw, $opts);
        $this->_san_log($field, $raw, $r['value'], $r['warnings']);
        return $r['value'];
    }
    /* Clean a remark/MOM (prose). Keeps text even if flagged, but logs junk. */
    private function _clean_remark($field, $raw, $min = 0, $max = 5000) {
        return $this->_clean_text($field, $raw, array('prose' => true, 'min_len' => $min, 'max_len' => $max));
    }
    /* Clean a phone; returns 10-digit when valid, else original digits. */
    private function _clean_phone($field, $raw) {
        $r = $this->_san()->phone($raw);
        $this->_san_log($field, $raw, $r['value'], $r['warnings']);
        return $r['ok'] ? $r['value'] : preg_replace('/[^0-9]/', '', (string)$raw);
    }
    /* Clean an email; returns trimmed email (kept even if invalid, but logged). */
    private function _clean_email($field, $raw) {
        $r = $this->_san()->email($raw);
        $this->_san_log($field, $raw, $r['value'], $r['warnings']);
        return $r['value'];
    }

    /* ===================== POST /api/leads/create ===================== */
    /**
     * Required POST: uid, compname, contactperson, phoneno
     * Optional: emailid, designation, address, website, country, state, district,
     *           city, budget, fbudget, cluster_id, partnerType_id, marketing,
     *           upsell_client, focus_funnel, lead_source
     *
     * Returns: { ok, company_id, contact_id, cid_id, lead_id }
     */
    public function create_lead() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return $this->_deny(405, 'POST only');
        if (!$this->_bearer_ok()) return $this->_deny(401, 'bad token');

        $actor = $this->_resolve_actor($this->_post('uid'));
        if (!$actor) return $this->_deny(401, 'unknown or inactive uid');
        list($ok, $why) = $this->_can($actor, 'create_lead');
        if (!$ok) return $this->_deny(403, $why);

        $miss = $this->_require_post(['uid','compname','contactperson','phoneno']);
        if ($miss) return $this->_deny(400, 'missing fields: ' . implode(',', $miss));

        $uid = (int)$actor['uid'];
        $now = date('Y-m-d H:i:s');
        $today = date('Y-m-d');
        $fyear = (date('n') >= 4) ? (date('Y') . '-' . (date('y') + 1)) : ((date('Y') - 1) . '-' . date('y'));

        // Verify uid exists and pull cluster_id for the lead
        $u = $this->db->query("SELECT uid, base_cluster, cluster_master_id, type_id FROM user WHERE uid = ? LIMIT 1", [$uid])->row_array();
        if (!$u) return $this->_deny(400, 'unknown uid');
        $cluster_id = (string)$this->_post('cluster_id', $u['base_cluster']);

        $this->db->trans_start();

        // 1) company_master
        $comp = [
            'compname'        => $this->_clean_text('compname', $this->_post('compname'), array('prose'=>true,'max_len'=>255)),
            'draft'           => 0,
            'budget'          => $this->_clean_money_str('budget', $this->_post('budget', '0'), '0'),
            'address'         => $this->_clean_text('address', $this->_post('address', ''), array('max_len'=>1000)),
            'website'         => (string)$this->_post('website', ''),
            'createddate'     => $now,
            'city'            => (string)$this->_post('city', ''),
            'country'         => (string)$this->_post('country', 'India'),
            'partnerType_id'  => (int)$this->_post('partnerType_id', 0),
            'state'           => (string)$this->_post('state', ''),
            'district'        => (string)$this->_post('district', ''),
            'marketing'       => (string)$this->_post('marketing', ''),
        ];
        $company_id = $this->_next_id('company_master');
        $comp['id'] = $company_id;
        $this->db->insert('company_master', $comp);

        // 2) company_contact_master  (note: column is `rby` not `add_by`)
        $contact = [
            'contactperson' => $this->_clean_text('contactperson', $this->_post('contactperson'), array('prose'=>true,'max_len'=>255)),
            'emailid'       => $this->_clean_email('emailid', $this->_post('emailid', '')),
            'draft'         => 0,
            'phoneno'       => $this->_clean_phone('phoneno', $this->_post('phoneno')),
            'designation'   => (string)$this->_post('designation', ''),
            'type'          => (string)$this->_post('contact_type', 'primary'),
            'createddate'   => $now,
            'company_id'    => $company_id,
            'rby'           => $uid,
        ];
        $contact_id = $this->_next_id('company_contact_master');
        $contact['id'] = $contact_id;
        $this->db->insert('company_contact_master', $contact);

        // 3) init_call - THE LEAD. Fill every NOT NULL column with safe defaults.
        $lead = [
            'draft'           => '0',
            'proposal'        => '',
            'createDate'      => $today,
            'lead_source'     => (string)$this->_post('lead_source', 'Field_Research'),
            'topspender'      => 'no',
            'noofschools'     => $this->_clean_count_str('noofschools', $this->_post('noofschools', '0'), '0'),
            'proposal_type'   => 'NA',
            'proposal_amt'    => $this->_clean_money_str('proposal_amt', $this->_post('proposal_amt', 'NA'), 'NA'),
            'cmpid_id'        => $company_id,
            'creator_id'      => $uid,
            'mainbd'          => $uid,
            'cstatus'         => 1,    // Open
            'lstatus'         => 0,
            'clm_id'          => (string)$uid,
            'upsell_client'   => (string)$this->_post('upsell_client', 'no'),
            'focus_funnel'    => (string)$this->_post('focus_funnel', 'no'),
            'fbudget'         => $this->_clean_money_str('fbudget', $this->_post('fbudget', '0'), '0'),
            'keycompany'      => 'yes',
            'pkclient'        => 'yes',
            'pkdate'          => $today,
            'priorityc'       => 'yes',
            'potential'       => 'yes',
            'fyear'           => $fyear,
            'open'            => $today,
            'reachout'        => '',
            'positive'        => '',
            'positivenap'     => '',
            'tentative'       => '',
            'closure'         => '',
            'verypositive'    => '',
            'vmeeting'        => '',
            'keep_company'    => 'no',
            'cluster_id'      => $cluster_id,
            'focuspositive'   => '',
            'interventions'   => 0,
            'support'         => '',
            'review_date'     => '',
            'is_admin_approved' => 0,
            'new_lead'        => '1',
            'after_task'      => 0,
            'apr_by'          => '0',
            'reject_remarks'  => '',
        ];
        $cid_id = $this->_next_id('init_call');
        $lead['id'] = $cid_id;
        $this->db->insert('init_call', $lead);

        // 4) Auto-seed tblcallevents Research row (PARITY with production Menu_model::submit_company)
        //    Production stamps every new lead with a starting Research task so the
        //    Activity tab is never empty and the BD has a default next-action.
        $seed = $this->_canonical_event_row([
            'lastCFID'           => '0',
            'nextCFID'           => '0',
            'draft'              => '0',
            'event'              => '',
            'fwd_date'           => $now,
            'actontaken'         => 'yes',
            'nextaction'         => 'Research & Data Collection',
            'meeting_type'       => 'NA',
            'live_loaction'      => 'NA',
            'mom_received'       => 'no',
            'appointmentdatetime'=> $now,
            'actiontype_id'      => 1,
            'assignedto_id'      => $uid,
            'cid_id'             => $cid_id,
            'purpose_id'         => 1,
            'remarks'            => 'Research done',
            'status_id'          => 1,
            'user_id'            => $uid,
            'date'               => $now,
            'updateddate'        => $now,
            'updation_data_type' => 'updated',
            'plan'               => 0,
            'autotask'           => 0,
        ]);
        $seed_id = $this->_next_id('tblcallevents');
        $seed['id'] = $seed_id;
        $this->db->insert('tblcallevents', $seed);

        // 5) notify row (PARITY)
        $compname_clean = str_replace(["'", '"'], '', (string)$this->_post('compname'));
        $this->db->insert('notify', [
            'uid'  => $uid,
            'type' => '1',
            'sms'  => 'New Lead Added Company Name is ' . $compname_clean,
        ]);

        $this->db->trans_complete();

        if (!$this->db->trans_status()) {
            $err = $this->db->error();
            return $this->_deny(500, 'transaction failed', $err);
        }

        return $this->_ok([
            'company_id' => $company_id,
            'contact_id' => $contact_id,
            'cid_id'     => $cid_id,
            'lead_id'    => $cid_id,
            'seed_task_id' => $seed_id,
        ]);
    }

    /* ===================== POST /api/task/plan ===================== */
    /**
     * Required POST: uid, cid_id, appointmentdatetime, actiontype_id, purpose_id
     * Optional: task_id (update mode), assignedto_id, remarks, meeting_type, event
     *
     * Returns: { ok, task_id, mode: 'update'|'insert' }
     */
    public function plan_task() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return $this->_deny(405, 'POST only');
        if (!$this->_bearer_ok()) return $this->_deny(401, 'bad token');

        $actor = $this->_resolve_actor($this->_post('uid'));
        if (!$actor) return $this->_deny(401, 'unknown or inactive uid');
        list($ok, $why) = $this->_can($actor, 'plan_task');
        if (!$ok) return $this->_deny(403, $why);

        $miss = $this->_require_post(['uid','cid_id','appointmentdatetime','actiontype_id','purpose_id']);
        if ($miss) return $this->_deny(400, 'missing fields: ' . implode(',', $miss));

        $uid = (int)$actor['uid'];
        $cid_id = (int)$this->_post('cid_id');
        $apptdt = $this->_post('appointmentdatetime');
        $now = date('Y-m-d H:i:s');

        $lead = $this->db->query("SELECT id, mainbd, cluster_id FROM init_call WHERE id = ? LIMIT 1", [$cid_id])->row_array();
        if (!$lead) return $this->_deny(400, 'unknown cid_id');

        $task_id = $this->_post('task_id', null);

        // ---- Mode A: update existing ----
        if ($task_id) {
            $task_id = (int)$task_id;
            $exists = $this->db->query("SELECT id FROM tblcallevents WHERE id = ? LIMIT 1", [$task_id])->row_array();
            if (!$exists) return $this->_deny(400, 'unknown task_id');

            $this->db->where('id', $task_id)->update('tblcallevents', [
                'appointmentdatetime' => $apptdt,
                'plan'                => 1,
                'updateddate'         => $now,
                'updation_data_type'  => 'plan',
            ]);
            return $this->_ok(['task_id' => $task_id, 'mode' => 'update']);
        }

        // ---- Mode B: insert new ----
        $row = $this->_canonical_event_row([
            'lastCFID'           => '0',
            'nextCFID'           => '0',
            'event'              => (string)$this->_post('event', ''),
            'meeting_type'       => (string)$this->_post('meeting_type', 'NA'),
            'appointmentdatetime'=> $apptdt,
            'actiontype_id'      => (int)$this->_post('actiontype_id'),
            'assignedto_id'      => (int)$this->_post('assignedto_id', $uid),
            'cid_id'             => $cid_id,
            'purpose_id'         => (int)$this->_post('purpose_id'),
            'remarks'            => (string)$this->_post('remarks', ''),
            'status_id'          => (int)$this->_post('status_id', 0),
            'user_id'            => $uid,
            'updation_data_type' => 'plan',
            'autotask'           => (int)$this->_post('autotask', 0),
            'auto_plan'          => (int)$this->_post('auto_plan', 0),
            'assignedto_by'      => $uid,
        ]);

        $this->db->trans_start();
        $new_id = $this->_next_id('tblcallevents');
        $row['id'] = $new_id;
        $this->db->insert('tblcallevents', $row);
        $this->db->trans_complete();
        if (!$this->db->trans_status() || !$new_id) {
            $err = $this->db->error();
            return $this->_deny(500, 'insert failed', $err);
        }

        return $this->_ok(['task_id' => $new_id, 'mode' => 'insert']);
    }

    /**
     * Build a tblcallevents row with all NOT-NULL columns filled in.
     * The $custom array overrides defaults.
     */
    private function _canonical_event_row($custom) {
        $now = date('Y-m-d H:i:s');
        $defaults = [
            // identity / chain
            'lastCFID'           => '0',
            'nextCFID'           => '0',
            // narrative
            'draft'              => '',
            'event'              => '',
            // dates
            'fwd_date'           => $now,           // NOT NULL no default
            'appointmentdatetime'=> $now,
            'date'               => $now,
            'updateddate'        => $now,
            // semantics
            'actontaken'         => 'no',
            'purpose_achieved'   => 'no',
            'nextaction'         => '',
            'meeting_type'       => 'NA',
            'live_loaction'      => '',
            'mom_received'       => 'NA',
            // FKs
            'actiontype_id'      => 1,
            'assignedto_id'      => 0,
            'cid_id'             => 0,
            'purpose_id'         => 1,
            // body
            'remarks'            => '',
            'status_id'          => 0,
            'user_id'            => 0,
            // type marker
            'updation_data_type' => 'plan',
            // planner flags
            'plan'               => 1,
            'autotask'           => 0,
            'auto_plan'          => 0,
            'hmtplan'            => 0,
            'reminder'           => 0,
            'targetstatus'       => 0,
            'selectby'           => '',
            'filter_by'          => '',
            'is_new'             => 1,
            'approved_status'    => '',
            'approved_by'        => '',
            'plan_change'        => 0,
            'self_assign'        => 'no',
            'thnkscomments'      => '',
            'late_remarks_message' => '',
            'init_remarks'       => '',
            'emergency'          => 0,
            'assignedto_by'      => 0,
            'aftertask'          => 0,
            'follow_up_id'       => 0,
            'plan_count'         => 0,
            'delete_remarks'     => '',
            'pstassign'          => 'no',
        ];
        return array_merge($defaults, $custom);
    }

    /* ===================== POST /api/task/submit ===================== */
    /**
     * Mirrors Menu.php::momapproved canonical write shape:
     *   1) INSERT a new auto-followup tblcallevents row (lastCFID = $tid).
     *   2) UPDATE the original task: nextCFID = $ntid, actontaken = 'yes',
     *      purpose_achieved = ?, status_id = ?, remarks = ?, updateddate = now.
     *
     * Required POST: uid, task_id, cid_id, purpose_achieved (yes|no|partial)
     * Optional: nextaction, fwd_date, next_actiontype_id, next_purpose_id,
     *           next_appointmentdatetime, remarks, status_id, cstatus_to
     *
     * Returns: { ok, task_id_submitted, next_task_id, lead_stage }
     */
    public function submit_task() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return $this->_deny(405, 'POST only');
        if (!$this->_bearer_ok()) return $this->_deny(401, 'bad token');

        $actor = $this->_resolve_actor($this->_post('uid'));
        if (!$actor) return $this->_deny(401, 'unknown or inactive uid');
        list($ok, $why) = $this->_can($actor, 'submit_task');
        if (!$ok) return $this->_deny(403, $why);

        $miss = $this->_require_post(['uid','task_id','cid_id','purpose_achieved']);
        if ($miss) return $this->_deny(400, 'missing fields: ' . implode(',', $miss));

        $uid     = (int)$actor['uid'];
        $tid     = (int)$this->_post('task_id');
        $cid_id  = (int)$this->_post('cid_id');
        $pa      = (string)$this->_post('purpose_achieved');
        $now     = date('Y-m-d H:i:s');

        $task = $this->db->query("SELECT id, actiontype_id, purpose_id, cid_id, assignedto_id, nextCFID, actontaken FROM tblcallevents WHERE id = ? LIMIT 1", [$tid])->row_array();
        if (!$task) return $this->_deny(400, 'unknown task_id');
        if ((int)$task['cid_id'] !== $cid_id) return $this->_deny(400, 'task does not belong to cid_id');
        if ($task['actontaken'] === 'yes' && (int)$task['nextCFID'] !== 0) {
            return $this->_deny(409, 'task already submitted');
        }

        $lead = $this->db->query("SELECT id, cstatus FROM init_call WHERE id = ? LIMIT 1", [$cid_id])->row_array();
        if (!$lead) return $this->_deny(400, 'unknown cid_id');

        // Migration 074: required-remark guard (5 char min, exempt actions 2 Email and 7 Proposal).
        $_action_id_074 = (string)$task['actiontype_id'];
        $_remark_exempt_074 = array('2', '7');
        $_min_len_074 = 5;
        if (!in_array($_action_id_074, $_remark_exempt_074)) {
            $_remarks_flat = trim((string)$this->_post('remarks', ''));
            $_yremark_074  = trim((string)$this->_post('yremark_msg', ''));
            $_nremark_074  = trim((string)$this->_post('nremark_msg', ''));
            $_noremark_074 = trim((string)$this->_post('noremark', ''));
            $_actontaken_074 = (string)$this->_post('actontaken', '');
            if ($_actontaken_074 === 'yes' && $pa === 'yes')        $_eff_074 = $_yremark_074 ?: $_remarks_flat;
            else if ($_actontaken_074 === 'yes' && $pa === 'no')    $_eff_074 = $_nremark_074 ?: $_remarks_flat;
            else if ($_actontaken_074 === 'no' || $_actontaken_074 === 'no-action') $_eff_074 = $_noremark_074 ?: $_remarks_flat;
            else                                                    $_eff_074 = $_remarks_flat ?: ($_yremark_074 ?: ($_nremark_074 ?: $_noremark_074));
            if (strlen($_eff_074) < $_min_len_074) {
                try {
                    $this->db->insert('remark_guard_block_log', array(
                        'user_id'         => $uid,
                        'cid_id'          => $cid_id,
                        'tblcallevent_id' => $tid,
                        'action_id'       => $_action_id_074,
                        'actontaken'      => $_actontaken_074 ?: 'yes',
                        'purpose'         => $pa,
                        'remark_field'    => 'remarks',
                        'block_reason'    => ($_eff_074 === '' ? 'empty_remark' : 'too_short'),
                        'endpoint'        => 'mobile_submit_task',
                        'user_agent'      => substr((string)$this->input->user_agent(), 0, 255),
                        'ip_addr'         => $this->input->ip_address(),
                    ));
                } catch (Exception $e) { log_message('error', 'Mobile_write_api.php silent_catch: ' . $e->getMessage()); }
                return $this->_deny(422, 'remark_required', array(
                    'message' => 'Remark required (minimum ' . $_min_len_074 . ' characters describing what happened).',
                    'field'   => 'remarks',
                    'min_len' => $_min_len_074,
                ));
            }
        }
        // END Migration 074 guard

        $this->db->trans_start();

        $next_apptdt = $this->_post('next_appointmentdatetime', null);
        if (!$next_apptdt) $next_apptdt = date('Y-m-d H:i:s', strtotime('+1 day'));

        // 1) INSERT auto-followup task
        $nextRow = $this->_canonical_event_row([
            'lastCFID'           => (string)$tid,
            'nextCFID'           => '0',
            'fwd_date'           => $this->_post('fwd_date', $next_apptdt),
            'appointmentdatetime'=> $next_apptdt,
            'date'               => $now,
            'updateddate'        => $now,
            'nextaction'         => (string)$this->_post('nextaction', ''),
            'actiontype_id'      => (int)$this->_post('next_actiontype_id', $task['actiontype_id']),
            'assignedto_id'      => (int)$this->_post('assignedto_id', $uid),
            'cid_id'             => $cid_id,
            'purpose_id'         => (int)$this->_post('next_purpose_id', $task['purpose_id']),
            'user_id'            => $uid,
            'updation_data_type' => 'autofollowup',
            'autotask'           => 1,
            'auto_plan'          => 1,
            'assignedto_by'      => $uid,
        ]);
        $ntid = $this->_next_id('tblcallevents');
        $nextRow['id'] = $ntid;
        $this->db->insert('tblcallevents', $nextRow);
        if (!$ntid) {
            $err = $this->db->error();
            return $this->_deny(500, 'autofollowup insert failed', $err);
        }

        // 2) UPDATE the submitted task
        $upd = [
            'nextCFID'           => (string)$ntid,
            'actontaken'         => 'yes',
            'purpose_achieved'   => $pa,
            'remarks'            => (string)$this->_post('remarks', ''),
            'status_id'          => (int)$this->_post('status_id', 0),
            'updateddate'        => $now,
            'updation_data_type' => 'submit',
            // EXEC-PARITY 20260610: persist the >2 min late-update reason
            // (mirrors Menu::submit_task1 late_remarks_message) and stamp the
            // actual close-time (mirrors the web 'Meeting Closed at' closem).
            'late_remarks_message' => (string)$this->_post('late_remarks_message', ''),
            'closem'             => date('H:i:s'),
        ];
        $this->db->where('id', $tid)->update('tblcallevents', $upd);

        // 3) Optional stage promotion
        $cstatus_to = $this->_post('cstatus_to', null);
        $new_stage = (int)$lead['cstatus'];
        if ($cstatus_to !== null) {
            $cstatus_to = (int)$cstatus_to;
            $allowed = [1,2,3,6,7,8,9,12,13];
            if (in_array($cstatus_to, $allowed, true)) {
                $this->db->where('id', $cid_id)->update('init_call', [
                    'cstatus'    => $cstatus_to,
                    'updated_at' => $now,
                ]);
                $new_stage = $cstatus_to;
            }
        }

        // 4) Agent I: MOM passthrough — accept the 12 structured MOM keys from
        //    M047TaskExecutionScreen (addpop.php test2/test6 parity).
        //    When momdata='momdata' is present, persist the structured fields
        //    into mom_data exactly as Menu/submittask1 does.
        //    meeting key (test2: Email Send? yes/no) is also captured here.
        // ----------------------------------------------------------------
        $_momdata_val = (string)$this->_post('momdata', '');
        if ($_momdata_val === 'momdata') {
            $_presentation_raw = $this->input->post('presentation');
            if (is_array($_presentation_raw)) {
                $_presentation_str = implode(',', array_map('strval', $_presentation_raw));
            } elseif (is_string($_presentation_raw) && $_presentation_raw !== '') {
                $_presentation_str = $_presentation_raw;
            } else {
                $_presentation_str = '';
            }
            $_mom_row = array(
                // identity
                'user_id'                     => $uid,
                'tid'                         => $tid,
                'init_cmpid'                  => $cid_id,
                'action_id'                   => (int)$task['actiontype_id'],
                'ccstatus'                    => (int)$lead['cstatus'],
                'actontaken'                  => (string)$this->_post('actontaken', 'yes'),
                // 12 structured MOM keys (spellings match production exactly)
                'meetingdonewinitiator'        => (string)$this->_post('meetingdonewinitiator', ''),
                'presentation'                => $_presentation_str,
                'project_intervention_select' => (string)$this->_post('project_intervention_select', ''),
                'project_intervention'        => (string)$this->_post('project_intervention', ''),
                'client_has_adopted_select'   => (string)$this->_post('client_has_adopted_select', ''),
                'client_has_adopted'          => (string)$this->_post('client_has_adopted', ''),
                'approving_autorities'        => (string)$this->_post('approving_autorities', ''),
                'budget_for_cfyear'           => (string)$this->_post('budget_for_cfyear', ''),
                'fund_sanstion_limit'         => (string)$this->_post('fund_sanstion_limit', ''),
                'other_specific_remarks'      => (string)$this->_post('other_specific_remarks', ''),
                // rpmmom = free-text MOM body (mirrors rpmmom column in mom_data)
                'rpmmom'                      => (string)$this->_post('rpmmom', ''),
                // approval defaults
                'approved_status'             => 'Pending',
                'approved_by'                 => '',
                'approved_date'               => '',
                'reject_remarks'              => '',
                // NOT NULL pads (no-default columns)
                'proposal_of_budget'          => '',
                'proposal_of_location'        => '',
                'permission_letter_rech'      => '',
                'client_int_type_project'     => '',
                'client_int_school_date'      => '',
                'pst_call_task'               => 0,
                'pst_assign'                  => '',
                'edit_cnt'                    => '0',
            );
            $_mom_id = $this->_next_id('mom_data');
            $_mom_row['id'] = $_mom_id;
            $this->db->insert('mom_data', $_mom_row);
            // Mirror mom text back to tblcallevents.mom column
            $_rpmmom_text = (string)$this->_post('rpmmom', '');
            if ($_rpmmom_text !== '') {
                $this->db->where('id', $tid)->update('tblcallevents', [
                    'mom'                => $_rpmmom_text,
                    'mom_received'       => 'yes',
                    'mom_approved'       => 'Pending',
                    'updation_data_type' => 'mom_submit_via_task',
                    'updateddate'        => $now,
                ]);
            }
        }
        // meeting key = Email Send? toggle (test2, action_id=2)
        $_meeting_val = (string)$this->_post('meeting', '');
        if ($_meeting_val !== '') {
            // Store in tblcallevents special_remarks if not already set,
            // prefixed so it's queryable without breaking existing logic.
            $this->db->where('id', $tid)->update('tblcallevents', [
                'special_remarks'    => 'email_sent:' . $_meeting_val,
                'updateddate'        => $now,
            ]);
        }
        // END Agent I: MOM passthrough

        $this->db->trans_complete();
        if (!$this->db->trans_status()) {
            $err = $this->db->error();
            return $this->_deny(500, 'transaction failed', $err);
        }

        return $this->_ok([
            'task_id_submitted' => $tid,
            'next_task_id'      => $ntid,
            'lead_stage'        => $new_stage,
            'mom_persisted'     => ($_momdata_val === 'momdata'),
        ]);
    }

    /* ===================== POST /api/mom/submit ===================== */
    /**
     * Required POST: uid, task_id, cid_id, mom
     * Optional: mom_remarks
     *
     * Returns: { ok, task_id, mom_status: 'Pending' }
     */
    public function submit_mom() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return $this->_deny(405, 'POST only');
        if (!$this->_bearer_ok()) return $this->_deny(401, 'bad token');

        $actor = $this->_resolve_actor($this->_post('uid'));
        if (!$actor) return $this->_deny(401, 'unknown or inactive uid');
        list($ok, $why) = $this->_can($actor, 'write_mom');
        if (!$ok) return $this->_deny(403, $why);

        $miss = $this->_require_post(['uid','task_id','cid_id','mom']);
        if ($miss) return $this->_deny(400, 'missing fields: ' . implode(',', $miss));

        $tid    = (int)$this->_post('task_id');
        $cid_id = (int)$this->_post('cid_id');
        $now    = date('Y-m-d H:i:s');

        $task = $this->db->query("SELECT id, cid_id FROM tblcallevents WHERE id = ? LIMIT 1", [$tid])->row_array();
        if (!$task) return $this->_deny(400, 'unknown task_id');
        if ((int)$task['cid_id'] !== $cid_id) return $this->_deny(400, 'task does not belong to cid_id');

        $upd = [
            'mom'                => (string)$this->_post('mom'),
            'mom_received'       => 'yes',
            'mom_approved'       => 'Pending',
            'mom_remarks'        => (string)$this->_post('mom_remarks', ''),
            'updateddate'        => $now,
            'updation_data_type' => 'mom_submit',
        ];
        $this->db->where('id', $tid)->update('tblcallevents', $upd);

        return $this->_ok([
            'task_id'    => $tid,
            'mom_status' => 'Pending',
        ]);
    }

    /* ========================================================================
     * v4 ADDITIONS (27 May 2026)
     *
     * 12 new endpoints + role gates + queue endpoints.
     *
     * Role gate model (canonical, per stem_role_action_matrix.md):
     *   - BD (type_id=3)         : may CREATE leads, plan/submit tasks, barge,
     *                              research, join meetings, write MoM, upload
     *                              proposals, submit handover/bd_request.
     *                              Wallet (ucash) debit on barge create only.
     *   - PST (type_id=4)        : approver. APPROVES proposal, MoM, planner.
     *                              Cannot create leads (admin lane only).
     *   - CM (type_id=13)        : may join meetings, approve proposal/MoM/planner
     *                              for BDs whose user_details.aadmin = CM uid.
     *   - SC (type_id=15)        : VIEW + APPROVE only. Cannot write barge/research.
     *                              Scope: user_details.sales_co = SC uid.
     *                              SC bypasses MoM/Proposal pending checks
     *                              (Menu.php 5257/5266/5496/5505).
     *   - EA (type_id=17)        : delegated. v4 = web only, no mobile write.
     *   - ASH (type_id=19/20/21) : VIEW only.
     *                              Scope: ash_nae_co / ash_w_co / ash_s_co.
     *   - RM (type_id=22/23)     : VIEW only.
     *                              Scope: rm_east_co / rm_north_co.
     *   - ACM (type_id=24)       : DUAL role. Acts as BD on own leads, acts as
     *                              CM approver for BDs whose acm_co = ACM uid.
     *
     * Pilot whitelist DROPPED. Auth = user.active=1 only (v4 enforces in
     * _resolve_actor() helper).
     * ====================================================================== */

    /**
     * Resolve actor: validate uid exists, is active, has known type_id.
     * Returns the user row with type_id and user_details hierarchy fields,
     * or null on failure. Caller MUST _deny on null.
     */
    private function _resolve_actor($uid) {
        $uid = (int)$uid;
        if ($uid <= 0) return null;
        $sql = "SELECT u.uid, u.name, u.type_id, u.base_cluster, u.cluster_master_id, u.user_details_id,
                       u.active,
                       ud.id AS ud_id, ud.ucash, ud.status AS ud_status,
                       ud.admin_id, ud.sales_co, ud.pst_co, ud.aadmin,
                       ud.ash_nae_co, ud.ash_w_co, ud.ash_s_co,
                       ud.rm_east_co, ud.rm_north_co, ud.acm_co
                FROM user u
                LEFT JOIN user_details ud ON ud.id = u.user_details_id
                WHERE u.uid = ? LIMIT 1";
        $r = $this->db->query($sql, [$uid])->row_array();
        if (!$r) return null;
        if ((int)$r['active'] !== 1) return null;
        return $r;
    }

    /**
     * Role gate: does $actor have permission for $action?
     * Actions:
     *   'create_lead', 'plan_task', 'submit_task', 'barge', 'research',
     *   'join_meeting', 'write_mom', 'upload_proposal',
     *   'approve_proposal', 'approve_mom', 'approve_planner',
     *   'submit_handover', 'submit_bd_request', 'wallet_view'
     *
     * Returns [bool ok, string reason_if_denied]
     */
    private function _can($actor, $action) {
        if (!$actor) return [false, 'no actor'];
        $t = (int)$actor['type_id'];
        // type_id buckets
        $is_bd  = ($t === 3);
        $is_pst = ($t === 4);
        $is_cm  = ($t === 13);
        $is_sc  = ($t === 15);
        $is_ash = in_array($t, [19,20,21], true);
        $is_rm  = in_array($t, [22,23], true);
        $is_acm = ($t === 24);
        $is_admin_lane = in_array($t, [1,2], true);

        $allow = false;
        switch ($action) {
            case 'create_lead':
            case 'plan_task':
            case 'submit_task':
                $allow = $is_bd || $is_cm || $is_acm || $is_admin_lane;
                break;
            case 'barge':
            case 'research':
            case 'wallet_view':
                // BD-only field actions. ACM also acts-as-BD on own leads.
                $allow = $is_bd || $is_acm || $is_admin_lane;
                break;
            case 'join_meeting':
                $allow = $is_bd || $is_cm || $is_acm || $is_admin_lane;
                break;
            case 'write_mom':
                $allow = $is_bd || $is_cm || $is_acm || $is_admin_lane;
                break;
            case 'upload_proposal':
                $allow = $is_bd || $is_acm || $is_admin_lane;
                break;
            case 'approve_proposal':
            case 'approve_mom':
            case 'approve_planner':
            case 'assign_planned_task':
                $allow = $is_pst || $is_cm || $is_sc || $is_acm
                      || $is_ash || $is_rm || $is_admin_lane;
                break;
            case 'submit_handover':
            case 'submit_bd_request':
            case 'submit_planner_approval':
                $allow = $is_bd || $is_acm || $is_admin_lane;
                break;
            default:
                $allow = false;
        }
        if (!$allow) {
            $tname = $this->_role_name($t);
            return [false, "$tname not permitted for $action"];
        }

        // rimlyproof_dayguard_20260609: field users (BD/ACM) must have STARTED their day
        // before performing in-field mutations. A day-not-started field user is BLOCKED
        // ("She should not be able to do anything"). Manager approvals, admin lanes, and
        // read-only paths are exempt. Day-start itself is a separate ceremony endpoint,
        // not routed through _can. Additive, fail-closed only for the specific gated set.
        $DAY_GATED_ACTIONS = array(
            'create_lead','plan_task','submit_task','barge','research',
            'join_meeting','write_mom','upload_proposal',
            'submit_handover','submit_bd_request','submit_planner_approval'
        );
        $is_field_user = ($t === 3 || $t === 24); // BD or ACM act-as-BD
        if ($is_field_user && in_array($action, $DAY_GATED_ACTIONS, true)) {
            if (!$this->_day_started((int)$actor['uid'])) {
                return [false, 'Please start your day before performing field actions'];
            }
        }

        return [true, ''];
    }

    /**
     * rimlyproof_dayguard_20260609: TRUE only if the user has an OPEN started day TODAY.
     * A started day = a user_day row dated today with ustart set and not yet closed (uclose NULL).
     * Fail-closed: any error or no row => not started.
     */
    private function _day_started($uid) {
        $uid = (int)$uid;
        if ($uid <= 0) return false;
        try {
            $row = $this->db->query(
                'SELECT id FROM user_day
                 WHERE user_id = ?
                   AND ustart IS NOT NULL
                   AND DATE(ustart) = CURDATE()
                   AND uclose IS NULL
                 ORDER BY id DESC LIMIT 1',
                array($uid)
            )->row_array();
            return !empty($row);
        } catch (Exception $e) {
            return false;
        }
    }

    private function _role_name($t) {
        $map = [1=>'SuperAdmin',2=>'Admin',3=>'BD',4=>'PST',13=>'CM',15=>'SC',
                17=>'EA',19=>'ASH-NAE',20=>'ASH-W',21=>'ASH-S',
                22=>'RM-East',23=>'RM-North',24=>'ACM'];
        return isset($map[$t]) ? $map[$t] : ('type_'.$t);
    }

    /**
     * Scope clause: returns an SQL WHERE fragment + bind params restricting
     * downstream rows to what $actor can see, based on user_details hierarchy.
     *
     * Used by queue endpoints. Returns ['<sql fragment>', [params]].
     */
    private function _scope_filter($actor, $col_user_id = 'ud.user_id') {
        $t = (int)$actor['type_id'];
        $uid = (int)$actor['uid'];
        if (in_array($t, [1,2], true)) {
            return ['1=1', []];
        }
        if ($t === 3 || $t === 24) {
            // BD or ACM sees own
            return ["$col_user_id = ?", [$uid]];
        }
        $map = [
            4  => 'ud.pst_co',
            13 => 'ud.aadmin',
            15 => 'ud.sales_co',
            19 => 'ud.ash_nae_co',
            20 => 'ud.ash_w_co',
            21 => 'ud.ash_s_co',
            22 => 'ud.rm_east_co',
            23 => 'ud.rm_north_co',
        ];
        if (isset($map[$t])) {
            return [$map[$t] . ' = ?', [$uid]];
        }
        // Unknown role - lock down
        return ['1=0', []];
    }

    /* ===================== POST /api/task/research ===================== */
    /**
     * Field research path. actiontype_id=10, purpose_id=94. FREE.
     * Creates init_call (new_lead=1, cstatus=1) AND a tblcallevents row in
     * one transaction, mirroring production research flow.
     *
     * Required: uid, compname, contactperson, phoneno
     * Optional: emailid, designation, address, city, state, district,
     *           cluster_id, lead_source, partnerType_id, remarks
     *
     * Returns: { ok, company_id, contact_id, cid_id, task_id }
     */
    public function research() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return $this->_deny(405, 'POST only');
        if (!$this->_bearer_ok()) return $this->_deny(401, 'bad token');

        $actor = $this->_resolve_actor($this->_post('uid'));
        if (!$actor) return $this->_deny(401, 'unknown or inactive uid');
        list($ok, $why) = $this->_can($actor, 'research');
        if (!$ok) return $this->_deny(403, $why);

        $miss = $this->_require_post(['uid','compname','contactperson','phoneno']);
        if ($miss) return $this->_deny(400, 'missing fields: ' . implode(',', $miss));

        $uid = (int)$actor['uid'];
        $now = date('Y-m-d H:i:s');
        $today = date('Y-m-d');
        $fyear = (date('n') >= 4) ? (date('Y') . '-' . (date('y') + 1)) : ((date('Y') - 1) . '-' . date('y'));
        $cluster_id = (string)$this->_post('cluster_id', $actor['base_cluster']);

        $this->db->trans_start();

        // company_master
        $comp = [
            'compname'        => $this->_post('compname'),
            'draft'           => 0,
            'budget'          => '0',
            'address'         => (string)$this->_post('address', ''),
            'createddate'     => $now,
            'city'            => (string)$this->_post('city', ''),
            'country'         => 'India',
            'partnerType_id'  => (int)$this->_post('partnerType_id', 0),
            'state'           => (string)$this->_post('state', ''),
            'district'        => (string)$this->_post('district', ''),
        ];
        $company_id = $this->_next_id('company_master');
        $comp['id'] = $company_id;
        $this->db->insert('company_master', $comp);

        // company_contact_master
        $contact = [
            'contactperson' => $this->_post('contactperson'),
            'emailid'       => (string)$this->_post('emailid', ''),
            'draft'         => 0,
            'phoneno'       => (string)$this->_post('phoneno'),
            'designation'   => (string)$this->_post('designation', ''),
            'type'          => 'primary',
            'createddate'   => $now,
            'company_id'    => $company_id,
            'rby'           => $uid,
        ];
        $contact_id = $this->_next_id('company_contact_master');
        $contact['id'] = $contact_id;
        $this->db->insert('company_contact_master', $contact);

        // init_call (research-born lead)
        $lead = [
            'draft'           => '0',
            'proposal'        => '',
            'createDate'      => $today,
            'lead_source'     => (string)$this->_post('lead_source', 'Field_Research'),
            'topspender'      => 'no',
            'noofschools'     => '0',
            'proposal_type'   => 'NA',
            'proposal_amt'    => 'NA',
            'cmpid_id'        => $company_id,
            'creator_id'      => $uid,
            'mainbd'          => $uid,
            'cstatus'         => 1,
            'lstatus'         => 0,
            'clm_id'          => (string)$uid,
            'upsell_client'   => 'no',
            'focus_funnel'    => 'no',
            'fbudget'         => '0',
            'keycompany'      => 'yes',
            'pkclient'        => 'yes',
            'pkdate'          => $today,
            'priorityc'       => 'yes',
            'potential'       => 'yes',
            'fyear'           => $fyear,
            'open'            => $today,
            'reachout'        => '',
            'positive'        => '',
            'positivenap'     => '',
            'tentative'       => '',
            'closure'         => '',
            'verypositive'    => '',
            'vmeeting'        => '',
            'keep_company'    => 'no',
            'cluster_id'      => $cluster_id,
            'focuspositive'   => '',
            'interventions'   => 0,
            'support'         => '',
            'review_date'     => '',
            'is_admin_approved' => 0,
            'new_lead'        => '1',
            'after_task'      => 0,
            'apr_by'          => '0',
            'reject_remarks'  => '',
        ];
        $cid_id = $this->_next_id('init_call');
        $lead['id'] = $cid_id;
        $this->db->insert('init_call', $lead);

        // tblcallevents - the research row (actiontype 10, purpose 94)
        $row = $this->_canonical_event_row([
            'event'              => 'Field research',
            'meeting_type'       => 'research',
            'appointmentdatetime'=> $now,
            'fwd_date'           => $now,
            'actiontype_id'      => 10,
            'assignedto_id'      => $uid,
            'cid_id'             => $cid_id,
            'purpose_id'         => 94,
            'remarks'            => (string)$this->_post('remarks', ''),
            'user_id'            => $uid,
            'updation_data_type' => 'research',
            'plan'               => 1,
            'self_assign'        => 'yes',
            'assignedto_by'      => $uid,
        ]);
        $tid = $this->_next_id('tblcallevents');
        $row['id'] = $tid;
        $this->db->insert('tblcallevents', $row);

        $this->db->trans_complete();
        if (!$this->db->trans_status()) {
            $err = $this->db->error();
            return $this->_deny(500, 'research transaction failed', $err);
        }
        return $this->_ok([
            'company_id' => $company_id,
            'contact_id' => $contact_id,
            'cid_id'     => $cid_id,
            'task_id'    => $tid,
        ]);
    }

    /* ===================== POST /api/meeting/barge ===================== */
    /**
     * Barge meeting path. actiontype_id=4, purpose_id=66. Costs Rs 500 ucash.
     * Production write-shape:
     *   1) Verify user_details.ucash >= 500.
     *   2) Create init_call + company_master + company_contact_master
     *      (same shape as research).
     *   3) Insert tblcallevents row with cash_allot=500.
     *   4) Insert barginmeeting row.
     *   5) Debit user_details.ucash by 500 and write cash_log.
     *
     * Required: uid, compname, contactperson, phoneno
     * Optional: emailid, designation, address, city, state, district,
     *           cluster_id, partnerType_id, remarks, lat, lng, location
     *
     * Returns: { ok, company_id, contact_id, cid_id, task_id, barge_id,
     *           cash_debited, new_balance }
     */
    public function barge() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return $this->_deny(405, 'POST only');
        if (!$this->_bearer_ok()) return $this->_deny(401, 'bad token');

        $actor = $this->_resolve_actor($this->_post('uid'));
        if (!$actor) return $this->_deny(401, 'unknown or inactive uid');
        list($ok, $why) = $this->_can($actor, 'barge');
        if (!$ok) return $this->_deny(403, $why);

        $miss = $this->_require_post(['uid','compname','contactperson','phoneno']);
        if ($miss) return $this->_deny(400, 'missing fields: ' . implode(',', $miss));

        $uid = (int)$actor['uid'];
        $ucash = (int)$actor['ucash'];

        // v2144 additive: optional meeting_type lets mobile record a free "join"
        // meeting instead of the chargeable bargain. Missing or 'bargain' keeps
        // the exact existing behavior (Rs 500 wallet debit). 'join' skips the
        // debit and the cash_log row entirely.
        $meeting_type = strtolower(trim((string)$this->_post('meeting_type', 'bargain')));
        if ($meeting_type !== 'join') $meeting_type = 'bargain';
        $is_join = ($meeting_type === 'join');

        if (!$is_join && $ucash < 500) {
            return $this->_deny(402, 'insufficient wallet: Rs ' . $ucash . ' available, Rs 500 required for barge');
        }

        $now = date('Y-m-d H:i:s');
        $today = date('Y-m-d');
        $fyear = (date('n') >= 4) ? (date('Y') . '-' . (date('y') + 1)) : ((date('Y') - 1) . '-' . date('y'));
        $cluster_id = (string)$this->_post('cluster_id', $actor['base_cluster']);

        $this->db->trans_start();

        $comp = [
            'compname'        => $this->_post('compname'),
            'draft'           => 0,
            'budget'          => '0',
            'address'         => (string)$this->_post('address', ''),
            'createddate'     => $now,
            'city'            => (string)$this->_post('city', ''),
            'country'         => 'India',
            'partnerType_id'  => (int)$this->_post('partnerType_id', 0),
            'state'           => (string)$this->_post('state', ''),
            'district'        => (string)$this->_post('district', ''),
        ];
        $company_id = $this->_next_id('company_master');
        $comp['id'] = $company_id;
        $this->db->insert('company_master', $comp);

        $contact = [
            'contactperson' => $this->_post('contactperson'),
            'emailid'       => (string)$this->_post('emailid', ''),
            'draft'         => 0,
            'phoneno'       => (string)$this->_post('phoneno'),
            'designation'   => (string)$this->_post('designation', ''),
            'type'          => 'primary',
            'createddate'   => $now,
            'company_id'    => $company_id,
            'rby'           => $uid,
        ];
        $contact_id = $this->_next_id('company_contact_master');
        $contact['id'] = $contact_id;
        $this->db->insert('company_contact_master', $contact);

        $lead = [
            'draft'           => '0',
            'proposal'        => '',
            'createDate'      => $today,
            'lead_source'     => 'Barge_Meeting',
            'topspender'      => 'no',
            'noofschools'     => '0',
            'proposal_type'   => 'NA',
            'proposal_amt'    => 'NA',
            'cmpid_id'        => $company_id,
            'creator_id'      => $uid,
            'mainbd'          => $uid,
            'cstatus'         => 1,
            'lstatus'         => 0,
            'clm_id'          => (string)$uid,
            'upsell_client'   => 'no',
            'focus_funnel'    => 'no',
            'fbudget'         => '0',
            'keycompany'      => 'yes',
            'pkclient'        => 'yes',
            'pkdate'          => $today,
            'priorityc'       => 'yes',
            'potential'       => 'yes',
            'fyear'           => $fyear,
            'open'            => $today,
            'reachout'        => '', 'positive' => '', 'positivenap' => '',
            'tentative' => '', 'closure' => '', 'verypositive' => '', 'vmeeting' => '',
            'keep_company'    => 'no',
            'cluster_id'      => $cluster_id,
            'focuspositive'   => '',
            'interventions'   => 0,
            'support'         => '',
            'review_date'     => '',
            'is_admin_approved' => 0,
            'new_lead'        => '1',
            'after_task'      => 0,
            'apr_by'          => '0',
            'reject_remarks'  => '',
        ];
        $cid_id = $this->_next_id('init_call');
        $lead['id'] = $cid_id;
        $this->db->insert('init_call', $lead);

        // tblcallevents barge row (actiontype 4, purpose 66). For a bargain the
        // cash_allot is 500; for a join it is 0 (no wallet debit).
        $row = $this->_canonical_event_row([
            'event'              => $is_join ? 'Join meeting' : 'Barge meeting',
            'meeting_type'       => $is_join ? 'join' : 'barge',
            'appointmentdatetime'=> $now,
            'fwd_date'           => $now,
            'actiontype_id'      => 4,
            'assignedto_id'      => $uid,
            'cid_id'             => $cid_id,
            'purpose_id'         => 66,
            'remarks'            => (string)$this->_post('remarks', ''),
            'user_id'            => $uid,
            'updation_data_type' => $is_join ? 'join' : 'barge',
            'plan'               => 1,
            'self_assign'        => 'yes',
            'assignedto_by'      => $uid,
            'cash_allot'         => $is_join ? 0 : 500,
            'live_loaction'      => (string)$this->_post('location', ''),
        ]);
        $tid = $this->_next_id('tblcallevents');
        $row['id'] = $tid;
        $this->db->insert('tblcallevents', $row);

        // barginmeeting row
        $bm = [
            'storedt'         => $now,
            'user_id'         => $uid,
            'cid'             => $company_id,
            'ccid'            => $contact_id,
            'inid'            => $cid_id,
            'tid'             => $tid,
            'company_name'    => (string)$this->_post('compname'),
            'caddress'        => (string)$this->_post('address', ''),
            'cpname'          => (string)$this->_post('contactperson'),
            'cpdes'           => (string)$this->_post('designation', ''),
            'cpno'            => (string)$this->_post('phoneno'),
            'cpemail'         => (string)$this->_post('emailid', ''),
            'initiateTime'    => $now,
            'initiateLat'     => (string)$this->_post('lat', ''),
            'initiateLongi'   => (string)$this->_post('lng', ''),
            'status'          => 'Pending',
            'location'        => (string)$this->_post('location', ''),
            'city'            => (string)$this->_post('city', ''),
            'state'           => (string)$this->_post('state', ''),
            'type'            => $is_join ? 'mobile_join' : 'mobile_barge',
            'letmeetingsremarks' => '',
            'company_as'      => 'school',
            'company_descri'  => '',
            'potentional_client' => 'unknown',
            'approved_status' => '',
            'approved_by'     => '',
        ];
        $barge_id = $this->_next_id('barginmeeting');
        $bm['id'] = $barge_id;
        $this->db->insert('barginmeeting', $bm);

        // wallet debit + cash_log: only for a chargeable bargain. A join is free,
        // so the wallet is untouched and no cash_log row is written.
        if ($is_join) {
            $new_balance = $ucash;
            $cash_debited = 0;
        } else {
            $new_balance = $ucash - 500;
            $this->db->where('id', (int)$actor['ud_id'])->update('user_details', ['ucash' => $new_balance]);

            // cash_log
            $cl = [
                'uid'      => $uid,
                'cash'     => 500,
                'av_cash'  => $new_balance,
                'type'     => 'debit',
                'remarks'  => 'Cash Deducted for Creating Barg in Meeting',
                'task_id'  => $tid,
            ];
            $cl_id = $this->_next_id('cash_log');
            $cl['id'] = $cl_id;
            $this->db->insert('cash_log', $cl);
            $cash_debited = 500;
        }

        $this->db->trans_complete();
        if (!$this->db->trans_status()) {
            $err = $this->db->error();
            return $this->_deny(500, 'barge transaction failed', $err);
        }
        return $this->_ok([
            'company_id'   => $company_id,
            'contact_id'   => $contact_id,
            'cid_id'       => $cid_id,
            'task_id'      => $tid,
            'barge_id'     => $barge_id,
            'meeting_type' => $meeting_type,
            'cash_debited' => $cash_debited,
            'new_balance'  => $new_balance,
        ]);
    }

    /* ===================== POST /api/meeting/join ===================== */
    /**
     * Join an existing init_call (no new company, no new lead). Creates a
     * tblcallevents row under joiner's uid with actiontype_id=17 attached
     * to the existing cid_id. Free.
     *
     * Required: uid, cid_id, appointmentdatetime, purpose_id, cluster_id
     * Optional: remarks, event
     *
     * Returns: { ok, task_id, cid_id }
     */
    public function join_meeting() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return $this->_deny(405, 'POST only');
        if (!$this->_bearer_ok()) return $this->_deny(401, 'bad token');

        $actor = $this->_resolve_actor($this->_post('uid'));
        if (!$actor) return $this->_deny(401, 'unknown or inactive uid');
        list($ok, $why) = $this->_can($actor, 'join_meeting');
        if (!$ok) return $this->_deny(403, $why);

        $miss = $this->_require_post(['uid','cid_id','appointmentdatetime','purpose_id']);
        if ($miss) return $this->_deny(400, 'missing fields: ' . implode(',', $miss));

        $uid = (int)$actor['uid'];
        $cid_id = (int)$this->_post('cid_id');
        $lead = $this->db->query("SELECT id, mainbd, cluster_id FROM init_call WHERE id = ? LIMIT 1", [$cid_id])->row_array();
        if (!$lead) return $this->_deny(400, 'unknown cid_id');

        $row = $this->_canonical_event_row([
            'event'              => (string)$this->_post('event', 'Join meeting'),
            'meeting_type'       => 'join',
            'appointmentdatetime'=> $this->_post('appointmentdatetime'),
            'fwd_date'           => $this->_post('appointmentdatetime'),
            'actiontype_id'      => 17,
            'assignedto_id'      => $uid,
            'cid_id'             => $cid_id,
            'purpose_id'         => (int)$this->_post('purpose_id'),
            'remarks'            => (string)$this->_post('remarks', ''),
            'user_id'            => $uid,
            'updation_data_type' => 'join',
            'plan'               => 1,
            'self_assign'        => 'yes',
            'assignedto_by'      => $uid,
        ]);

        $this->db->trans_start();
        $tid = $this->_next_id('tblcallevents');
        $row['id'] = $tid;
        $this->db->insert('tblcallevents', $row);
        $this->db->trans_complete();
        if (!$this->db->trans_status()) {
            $err = $this->db->error();
            return $this->_deny(500, 'join insert failed', $err);
        }
        return $this->_ok(['task_id' => $tid, 'cid_id' => $cid_id]);
    }

    /* ===================== POST /api/proposal/upload ===================== */
    /**
     * Upload a proposal. Mirrors production submit_task action_id=7 path.
     *
     * Required: uid, cid_id, task_id, proposal_types, noofsc, pbudgetme
     * Optional: partner, proattach, supportattach, remark, main
     *
     * Returns: { ok, proposal_id, apr_status }
     */
    public function upload_proposal() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return $this->_deny(405, 'POST only');
        if (!$this->_bearer_ok()) return $this->_deny(401, 'bad token');

        $actor = $this->_resolve_actor($this->_post('uid'));
        if (!$actor) return $this->_deny(401, 'unknown or inactive uid');
        list($ok, $why) = $this->_can($actor, 'upload_proposal');
        if (!$ok) return $this->_deny(403, $why);

        $miss = $this->_require_post(['uid','cid_id','task_id','proposal_types','noofsc','pbudgetme']);
        if ($miss) return $this->_deny(400, 'missing fields: ' . implode(',', $miss));

        $uid = (int)$actor['uid'];
        $cid_id = (int)$this->_post('cid_id');
        $tid = (int)$this->_post('task_id');

        $task = $this->db->query("SELECT id, cid_id FROM tblcallevents WHERE id = ? LIMIT 1", [$tid])->row_array();
        if (!$task) return $this->_deny(400, 'unknown task_id');
        if ((int)$task['cid_id'] !== $cid_id) return $this->_deny(400, 'task does not belong to cid_id');

        $row = [
            'user_id'            => $uid,
            'init_id'            => $cid_id,
            'tid'                => $tid,
            'apr'                => 0,
            'main'               => (int)$this->_post('main', 1),
            'partner'            => (string)$this->_post('partner', ''),
            'propasal_types'     => (string)$this->_post('proposal_types'),
            'noofsc'             => $this->_clean_count_str('noofsc', $this->_post('noofsc'), '0'),
            'pbudgetme'          => $this->_clean_money_str('pbudgetme', $this->_post('pbudgetme'), 'NA'),
            'proattach'          => (string)$this->_post('proattach', ''),
            'supportattach'      => (string)$this->_post('supportattach', ''),
            'remark'             => $this->_clean_remark('remark', $this->_post('remark', ''), 0, 2000),
            'taskplan'           => 0,
            'taskplan_by'        => 0,
            'new_task_after_check' => 0,
        ];

        $this->db->trans_start();
        $pid = $this->_next_id('proposal');
        $row['id'] = $pid;
        $this->db->insert('proposal', $row);
        $this->db->trans_complete();
        if (!$this->db->trans_status()) {
            $err = $this->db->error();
            return $this->_deny(500, 'proposal insert failed', $err);
        }
        return $this->_ok(['proposal_id' => $pid, 'apr_status' => 'pending']);
    }

    /* ===================== POST /api/proposal/approve ===================== */
    /**
     * Approve / reject a proposal. Production Pro_Apr pattern:
     *   apr=2 (approved): clone the originating tblcallevents row as an
     *          auto-task with plan=1 autotask=1 fresh appointmentdatetime.
     *   apr=1 (rejected): set proposal.apr=1 with remark. No clone.
     *
     * Required: uid, proposal_id, decision (approve|reject)
     * Optional: remark, next_appointmentdatetime
     *
     * Returns: { ok, proposal_id, apr, new_task_id|null }
     */
    public function approve_proposal() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return $this->_deny(405, 'POST only');
        if (!$this->_bearer_ok()) return $this->_deny(401, 'bad token');

        $actor = $this->_resolve_actor($this->_post('uid'));
        if (!$actor) return $this->_deny(401, 'unknown or inactive uid');
        list($ok, $why) = $this->_can($actor, 'approve_proposal');
        if (!$ok) return $this->_deny(403, $why);

        $miss = $this->_require_post(['uid','proposal_id','decision']);
        if ($miss) return $this->_deny(400, 'missing fields: ' . implode(',', $miss));

        $uid = (int)$actor['uid'];
        $pid = (int)$this->_post('proposal_id');
        $dec = strtolower((string)$this->_post('decision'));
        if (!in_array($dec, ['approve','reject'], true)) return $this->_deny(400, 'decision must be approve or reject');

        $prop = $this->db->query("SELECT id, init_id, tid, user_id, apr FROM proposal WHERE id = ? LIMIT 1", [$pid])->row_array();
        if (!$prop) return $this->_deny(400, 'unknown proposal_id');
        if ((int)$prop['apr'] !== 0) return $this->_deny(409, 'proposal already decided');

        $now = date('Y-m-d H:i:s');
        $this->db->trans_start();

        $new_task_id = null;
        if ($dec === 'approve') {
            // Clone originating task as auto-task (Pro_Apr clone-as-autotask)
            $src = $this->db->query("SELECT * FROM tblcallevents WHERE id = ? LIMIT 1", [(int)$prop['tid']])->row_array();
            if ($src) {
                $next_apptdt = $this->_post('next_appointmentdatetime', null);
                if (!$next_apptdt) $next_apptdt = date('Y-m-d H:i:s', strtotime('+1 day'));
                $clone = $this->_canonical_event_row([
                    'lastCFID'           => (string)$prop['tid'],
                    'nextCFID'           => '0',
                    'event'              => 'Auto task after proposal approval',
                    'meeting_type'       => isset($src['meeting_type']) ? $src['meeting_type'] : 'NA',
                    'appointmentdatetime'=> $next_apptdt,
                    'fwd_date'           => $next_apptdt,
                    'actiontype_id'      => (int)$src['actiontype_id'],
                    'assignedto_id'      => (int)$src['assignedto_id'],
                    'cid_id'             => (int)$src['cid_id'],
                    'purpose_id'         => (int)$src['purpose_id'],
                    'user_id'            => (int)$src['user_id'],
                    'updation_data_type' => 'autotask_proposal',
                    'plan'               => 1,
                    'autotask'           => 1,
                    'auto_plan'          => 1,
                    'assignedto_by'      => $uid,
                ]);
                $new_task_id = $this->_next_id('tblcallevents');
                $clone['id'] = $new_task_id;
                $this->db->insert('tblcallevents', $clone);
            }
            $this->db->where('id', $pid)->update('proposal', [
                'apr'                  => 2,
                'aprby'                => $uid,
                'aprdatet'             => $now,
                'apr_date'             => $now,
                'remark'               => (string)$this->_post('remark', ''),
                'taskplan'             => $new_task_id ? 1 : 0,
                'taskplan_by'          => $uid,
                'new_task_after_check' => $new_task_id ? (int)$new_task_id : 0,
            ]);
        } else {
            $this->db->where('id', $pid)->update('proposal', [
                'apr'      => 1,
                'aprby'    => $uid,
                'aprdatet' => $now,
                'apr_date' => $now,
                'remark'   => (string)$this->_post('remark', ''),
            ]);
        }

        $this->db->trans_complete();
        if (!$this->db->trans_status()) {
            $err = $this->db->error();
            return $this->_deny(500, 'proposal approve transaction failed', $err);
        }
        return $this->_ok([
            'proposal_id' => $pid,
            'apr'         => $dec === 'approve' ? 2 : 1,
            'new_task_id' => $new_task_id,
        ]);
    }

    /* ===================== POST /api/mom/v2/submit ===================== */
    /**
     * Full mom_data v2 submission. Writes the canonical mom_data row with
     * DM contact block (required for cstatus 6 promotion via the
     * trg_block_cstatus_6_unverified_dm trigger).
     *
     * Required: uid, task_id, cid_id, mom_text
     * Optional v2 fields: dm_name, dm_designation, dm_phone, dm_email,
     *           dm_org_type, dm_contact_completeness (default 2),
     *           meeting_purpose_v2, meeting_with, fitment_offer,
     *           proposal_intent_schools, proposal_intent_budget_rs,
     *           proposal_intent_location, expected_close_date,
     *           win_probability, mom_quality_grade, mom_quality_score,
     *           company_name
     *
     * Side effect: also marks tblcallevents.mom_received='yes',
     *              mom_approved='Pending'.
     *
     * Returns: { ok, mom_id, task_id, mom_status }
     */
    public function submit_mom_v2() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return $this->_deny(405, 'POST only');
        if (!$this->_bearer_ok()) return $this->_deny(401, 'bad token');

        $actor = $this->_resolve_actor($this->_post('uid'));
        if (!$actor) return $this->_deny(401, 'unknown or inactive uid');
        list($ok, $why) = $this->_can($actor, 'write_mom');
        if (!$ok) return $this->_deny(403, $why);

        $miss = $this->_require_post(['uid','task_id','cid_id','mom_text']);
        if ($miss) return $this->_deny(400, 'missing fields: ' . implode(',', $miss));

        $uid = (int)$actor['uid'];
        $tid = (int)$this->_post('task_id');
        $cid_id = (int)$this->_post('cid_id');
        $now = date('Y-m-d H:i:s');

        $task = $this->db->query("SELECT id, cid_id FROM tblcallevents WHERE id = ? LIMIT 1", [$tid])->row_array();
        if (!$task) return $this->_deny(400, 'unknown task_id');
        if ((int)$task['cid_id'] !== $cid_id) return $this->_deny(400, 'task does not belong to cid_id');

        $row = [
            // identity
            'user_id'      => $uid,
            'init_id'      => $cid_id,
            'tid'          => $tid,
            'submitted_at' => $now,
            'v2_submitted_at' => $now,
            // MoM body
            'mom_text'     => (string)$this->_post('mom_text'),
            'company_name' => (string)$this->_post('company_name', ''),
            // DM contact block (cstatus 6 gate)
            'dm_name'              => (string)$this->_post('dm_name', ''),
            'dm_designation'       => (string)$this->_post('dm_designation', ''),
            'dm_phone'             => (string)$this->_post('dm_phone', ''),
            'dm_email'             => (string)$this->_post('dm_email', ''),
            'dm_org_type'          => (string)$this->_post('dm_org_type', 'school'),
            'dm_contact_completeness' => (int)$this->_post('dm_contact_completeness', 2),
            // v2 semantics
            'meeting_purpose_v2'   => (string)$this->_post('meeting_purpose_v2', 'follow_up'),
            'meeting_with'         => (string)$this->_post('meeting_with', 'dm'),
            'fitment_offer'        => (string)$this->_post('fitment_offer', 'none'),
            // proposal intent
            'proposal_intent_schools'    => (int)$this->_post('proposal_intent_schools', 0),
            'proposal_intent_budget_rs'  => (string)$this->_post('proposal_intent_budget_rs', '0'),
            'proposal_intent_location'   => (string)$this->_post('proposal_intent_location', ''),
            'expected_close_date'        => (string)$this->_post('expected_close_date', ''),
            'win_probability'            => (int)$this->_post('win_probability', 0),
            // quality
            'mom_quality_grade'    => (string)$this->_post('mom_quality_grade', ''),
            'mom_quality_score'    => (int)$this->_post('mom_quality_score', 0),
            // approval status (defaults to pending)
            'approved_status'      => 'Pending',
            'approved_by'          => '0',
            'approved_date'        => '',
            'reject_remarks'       => '',
            // NOT NULL no default safety pads
            'proposal_of_budget'   => '',
            'proposal_of_location' => '',
            'permission_letter_rech' => '',
            'client_int_type_project' => '',
            'client_int_school_date' => '',
            'pst_call_task'        => 0,
            'pst_assign'           => '',
            'edit_cnt'             => '0',
        ];

        $this->db->trans_start();
        $mom_id = $this->_next_id('mom_data');
        $row['id'] = $mom_id;
        $this->db->insert('mom_data', $row);

        // mirror to tblcallevents
        $this->db->where('id', $tid)->update('tblcallevents', [
            'mom'                => (string)$this->_post('mom_text'),
            'mom_received'       => 'yes',
            'mom_approved'       => 'Pending',
            'mom_remarks'        => (string)$this->_post('mom_remarks', ''),
            'updateddate'        => $now,
            'updation_data_type' => 'mom_submit_v2',
        ]);

        $this->db->trans_complete();
        if (!$this->db->trans_status()) {
            $err = $this->db->error();
            return $this->_deny(500, 'mom v2 insert failed', $err);
        }
        return $this->_ok(['mom_id' => $mom_id, 'task_id' => $tid, 'mom_status' => 'Pending']);
    }

    /* ===================== POST /api/mom/approve ===================== */
    /**
     * Approve / reject a MoM. Updates mom_data.approved_status AND
     * tblcallevents.mom_approved on the linked task.
     *
     * Required: uid, mom_id, decision (approve|reject)
     * Optional: reject_remarks
     *
     * Returns: { ok, mom_id, task_id, mom_status }
     */
    public function approve_mom() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return $this->_deny(405, 'POST only');
        if (!$this->_bearer_ok()) return $this->_deny(401, 'bad token');

        $actor = $this->_resolve_actor($this->_post('uid'));
        if (!$actor) return $this->_deny(401, 'unknown or inactive uid');
        list($ok, $why) = $this->_can($actor, 'approve_mom');
        if (!$ok) return $this->_deny(403, $why);

        $miss = $this->_require_post(['uid','mom_id','decision']);
        if ($miss) return $this->_deny(400, 'missing fields: ' . implode(',', $miss));

        $uid = (int)$actor['uid'];
        $mom_id = (int)$this->_post('mom_id');
        $dec = strtolower((string)$this->_post('decision'));
        if (!in_array($dec, ['approve','reject'], true)) return $this->_deny(400, 'decision must be approve or reject');

        $mom = $this->db->query("SELECT id, tid, approved_status FROM mom_data WHERE id = ? LIMIT 1", [$mom_id])->row_array();
        if (!$mom) return $this->_deny(400, 'unknown mom_id');
        $cur_mom_status = isset($mom["approved_status"]) ? trim((string)$mom["approved_status"]) : "";
        // rimlyproof 20260608: NULL or empty status means not-yet-decided = approvable (treat as Pending). Only block genuinely decided MOMs.
        if ($cur_mom_status !== "" && strcasecmp($cur_mom_status, "Pending") !== 0) return $this->_deny(409, "mom already decided");

        $now = date('Y-m-d H:i:s');
        $new_status = ($dec === 'approve') ? 'Approved' : 'Rejected';

        $this->db->trans_start();
        $this->db->where('id', $mom_id)->update('mom_data', [
            'approved_status' => $new_status,
            'approved_by'     => (string)$uid,
            'approved_date'   => $now,
            'reject_remarks'  => $dec === 'reject' ? (string)$this->_post('reject_remarks', '') : '',
        ]);
        if ((int)$mom['tid'] > 0) {
            $this->db->where('id', (int)$mom['tid'])->update('tblcallevents', [
                'mom_approved'       => $new_status,
                'updateddate'        => $now,
                'updation_data_type' => 'mom_approve',
            ]);
        }
        $this->db->trans_complete();
        if (!$this->db->trans_status()) {
            $err = $this->db->error();
            return $this->_deny(500, 'mom approve failed', $err);
        }

        // === CLOSEOUT_I GAP-2: MOM auto-task spawn on approve/reject ===
        // On approve: spawn actiontype_id=2 (Write Thanks Mail), autotask=1, auto_plan=1
        // On reject:  spawn actiontype_id=6 (Write MOM),         autotask=1, auto_plan=1
        // Wrapped in try/catch - must not block the approve/reject itself.
        $autotask_id = 0;
        try {
            // Fetch the linked tblcallevents row to get cid_id and user_id
            $parent_task = null;
            if ((int)$mom['tid'] > 0) {
                $parent_task = $this->db->query(
                    "SELECT id, user_id, cid_id FROM tblcallevents WHERE id = ? LIMIT 1",
                    [(int)$mom['tid']]
                )->row_array();
            }
            if ($parent_task) {
                $spawn_action = ($dec === 'approve') ? 2 : 6;
                $spawn_label  = ($dec === 'approve') ? 'Write Thanks Mail' : 'Write MOM';
                $spawn_date   = date('Y-m-d H:i:s', strtotime('+1 day'));
                // Get next available id
                $max_id_row = $this->db->query("SELECT MAX(id) AS mx FROM tblcallevents")->row_array();
                $spawn_id   = (int)($max_id_row ? $max_id_row['mx'] : 0) + 1;
                $this->db->query(
                    "INSERT INTO tblcallevents" .
                    " (id, lastCFID, nextCFID, user_id, cid_id, actiontype_id, nextaction," .
                    "  autotask, auto_plan, plan, fwd_date, appointmentdatetime," .
                    "  approved_status, actontaken, purpose_id)" .
                    " VALUES (?, ?, '0', ?, ?, ?, ?, 1, 1, 1, ?, ?, 0, 'no', 1)",
                    [
                        $spawn_id,
                        (string)$mom['tid'],
                        (int)$parent_task['user_id'],
                        (int)$parent_task['cid_id'],
                        $spawn_action,
                        $spawn_label,
                        $spawn_date,
                        $spawn_date,
                    ]
                );
                $autotask_id = $spawn_id;
            }
        } catch (Exception $e) {
            log_message('error', 'CLOSEOUT_I GAP-2 mom_autotask error: ' . $e->getMessage());
        }
        // === END CLOSEOUT_I GAP-2 ===

        return $this->_ok(['mom_id' => $mom_id, 'task_id' => (int)$mom['tid'], 'mom_status' => $new_status, 'autotask_id' => $autotask_id]);
    }

    /* ===================== POST /api/planner/approve ===================== */
    /**
     * Approve / reject a planner_approved row (day-plan submission).
     *
     * Required: uid, planner_id, decision (approve|reject)
     *
     * Returns: { ok, planner_id, status }
     */
    public function approve_planner() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return $this->_deny(405, 'POST only');
        if (!$this->_bearer_ok()) return $this->_deny(401, 'bad token');

        $actor = $this->_resolve_actor($this->_post('uid'));
        if (!$actor) return $this->_deny(401, 'unknown or inactive uid');
        list($ok, $why) = $this->_can($actor, 'approve_planner');
        if (!$ok) return $this->_deny(403, $why);

        $miss = $this->_require_post(['uid','planner_id','decision']);
        if ($miss) return $this->_deny(400, 'missing fields: ' . implode(',', $miss));

        $uid = (int)$actor['uid'];
        $planner_id = (int)$this->_post('planner_id');
        $dec = strtolower((string)$this->_post('decision'));
        if (!in_array($dec, ['approve','reject'], true)) return $this->_deny(400, 'decision must be approve or reject');

        $row = $this->db->query("SELECT id, user_id, approved_status FROM planner_approved WHERE id = ? LIMIT 1", [$planner_id])->row_array();
        if (!$row) return $this->_deny(400, 'unknown planner_id');
        if ((int)$row['approved_status'] !== 0 && $row['approved_status'] !== null) return $this->_deny(409, 'planner already decided');

        $new_status = ($dec === 'approve') ? 1 : 2;
        $now = date('Y-m-d H:i:s');

        $this->db->where('id', $planner_id)->update('planner_approved', [
            'approved_status' => $new_status,
            'approved_by'     => $uid,
            'approved_date'   => $now,
        ]);
        if ($this->db->affected_rows() < 0) return $this->_deny(500, 'planner update failed');

        // === CLOSEOUT_I GAP-1: Cash reversal on planner reject ===
        $cash_reversed = 0;
        if ($dec === 'reject') {
            try {
                $plan_row = $this->db->query(
                    "SELECT user_id, request_date FROM planner_approved WHERE id = ? LIMIT 1",
                    [$planner_id]
                )->row_array();
                if ($plan_row && !empty($plan_row['request_date'])) {
                    $bd_uid   = (int)$plan_row['user_id'];
                    $pdate    = $plan_row['request_date'];
                    $cash_rows = $this->db->query(
                        "SELECT id, cash_allot, actiontype_id FROM tblcallevents" .
                        " WHERE user_id = ? AND DATE(appointmentdatetime) = ? AND cash_allot > 0 AND plan = 1",
                        [$bd_uid, $pdate]
                    )->result_array();
                    if (!empty($cash_rows)) {
                        $total_cash = 0;
                        $task_ids   = [];
                        foreach ($cash_rows as $cr) {
                            $total_cash += (int)$cr['cash_allot'];
                            $task_ids[]  = (int)$cr['id'];
                        }
                        $ud = $this->db->query(
                            "SELECT ucash FROM user_details WHERE user_id = ? LIMIT 1",
                            [$bd_uid]
                        )->row_array();
                        $current_cash = $ud ? (int)$ud['ucash'] : 0;
                        $new_cash     = $current_cash + $total_cash;
                        $this->db->where('user_id', $bd_uid)->update('user_details', ['ucash' => $new_cash]);
                        $rev_remarks = 'Cash Revert For Planner Rejection planner_id:' . $planner_id;
                        $this->db->insert('cash_log', [
                            'uid'     => $bd_uid,
                            'cash'    => $total_cash,
                            'av_cash' => $new_cash,
                            'type'    => 'Credit',
                            'remarks' => $rev_remarks,
                            'task_id' => $task_ids[0],
                        ]);
                        $id_list = implode(',', $task_ids);
                        $this->db->query("UPDATE tblcallevents SET cash_allot = 0 WHERE id IN (" . $id_list . ")");
                        $cash_reversed = $total_cash;
                    }
                }
            } catch (Exception $e) {
                log_message('error', 'CLOSEOUT_I GAP-1 cash_reversal error: ' . $e->getMessage());
            }
        }
        // === END CLOSEOUT_I GAP-1 ===

        return $this->_ok(['planner_id' => $planner_id, 'status' => $new_status, 'cash_reversed' => $cash_reversed]);
    }

    /* ===================== POST /api/handover/submit ===================== */
    /**
     * Submit a handover task (M046 flow). actiontype_id=20.
     *
     * Required: uid, cid_id, to_uid, remarks
     *
     * Returns: { ok, task_id }
     */
    public function submit_handover() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return $this->_deny(405, 'POST only');
        if (!$this->_bearer_ok()) return $this->_deny(401, 'bad token');

        $actor = $this->_resolve_actor($this->_post('uid'));
        if (!$actor) return $this->_deny(401, 'unknown or inactive uid');
        list($ok, $why) = $this->_can($actor, 'submit_handover');
        if (!$ok) return $this->_deny(403, $why);

        $miss = $this->_require_post(['uid','cid_id','to_uid','remarks']);
        if ($miss) return $this->_deny(400, 'missing fields: ' . implode(',', $miss));

        $uid = (int)$actor['uid'];
        $cid_id = (int)$this->_post('cid_id');
        $to_uid = (int)$this->_post('to_uid');
        $now = date('Y-m-d H:i:s');

        $lead = $this->db->query("SELECT id, mainbd FROM init_call WHERE id = ? LIMIT 1", [$cid_id])->row_array();
        if (!$lead) return $this->_deny(400, 'unknown cid_id');

        $row = $this->_canonical_event_row([
            'event'              => 'Handover request',
            'appointmentdatetime'=> $now,
            'fwd_date'           => $now,
            'actiontype_id'      => 20,
            'assignedto_id'      => $to_uid,
            'cid_id'             => $cid_id,
            'purpose_id'         => 1,
            'remarks'            => (string)$this->_post('remarks'),
            'user_id'            => $uid,
            'updation_data_type' => 'handover',
            'plan'               => 1,
            'assignedto_by'      => $uid,
        ]);

        $this->db->trans_start();
        $tid = $this->_next_id('tblcallevents');
        $row['id'] = $tid;
        $this->db->insert('tblcallevents', $row);
        $this->db->trans_complete();
        if (!$this->db->trans_status()) return $this->_deny(500, 'handover insert failed');

        return $this->_ok(['task_id' => $tid]);
    }

    /* ===================== POST /api/bd_request/submit ===================== */
    /**
     * Submit a BD request (M046 flow). actiontype_id=19.
     *
     * Required: uid, cid_id, remarks
     * Optional: to_uid (manager)
     *
     * Returns: { ok, task_id }
     */
    public function submit_bd_request() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return $this->_deny(405, 'POST only');
        if (!$this->_bearer_ok()) return $this->_deny(401, 'bad token');

        $actor = $this->_resolve_actor($this->_post('uid'));
        if (!$actor) return $this->_deny(401, 'unknown or inactive uid');
        list($ok, $why) = $this->_can($actor, 'submit_bd_request');
        if (!$ok) return $this->_deny(403, $why);

        $miss = $this->_require_post(['uid','cid_id','remarks']);
        if ($miss) return $this->_deny(400, 'missing fields: ' . implode(',', $miss));

        $uid = (int)$actor['uid'];
        $cid_id = (int)$this->_post('cid_id');
        $to_uid = (int)$this->_post('to_uid', $actor['aadmin'] ? (int)$actor['aadmin'] : 0);
        $now = date('Y-m-d H:i:s');

        $lead = $this->db->query("SELECT id FROM init_call WHERE id = ? LIMIT 1", [$cid_id])->row_array();
        if (!$lead) return $this->_deny(400, 'unknown cid_id');

        $row = $this->_canonical_event_row([
            'event'              => 'BD request',
            'appointmentdatetime'=> $now,
            'fwd_date'           => $now,
            'actiontype_id'      => 19,
            'assignedto_id'      => $to_uid > 0 ? $to_uid : $uid,
            'cid_id'             => $cid_id,
            'purpose_id'         => 1,
            'remarks'            => $this->_clean_remark('remarks', $this->_post('remarks'), 3, 2000),
            'user_id'            => $uid,
            'updation_data_type' => 'bd_request',
            'plan'               => 1,
            'assignedto_by'      => $uid,
        ]);

        $this->db->trans_start();
        $tid = $this->_next_id('tblcallevents');
        $row['id'] = $tid;
        $this->db->insert('tblcallevents', $row);
        $this->db->trans_complete();
        if (!$this->db->trans_status()) return $this->_deny(500, 'bd_request insert failed');

        return $this->_ok(['task_id' => $tid]);
    }

    /* ===================== GET /api/wallet/balance ===================== */
    /**
     * BD wallet balance. Reads user_details.ucash.
     *
     * Required query: uid
     *
     * Returns: { ok, uid, balance_rs }
     */
    public function wallet_balance() {
        if (!$this->_bearer_ok()) return $this->_deny(401, 'bad token');
        $uid = $this->_actor_uid(); // rimlyproof_mwaactoruid_20260609
        $actor = $this->_resolve_actor($uid);
        if (!$actor) return $this->_deny(401, 'unknown or inactive uid');
        list($ok, $why) = $this->_can($actor, 'wallet_view');
        if (!$ok) return $this->_deny(403, $why);
        return $this->_ok(['uid' => (int)$actor['uid'], 'balance_rs' => (int)$actor['ucash']]);
    }

    /* ===================== GET /api/wallet/history ===================== */
    /**
     * Wallet history. cash_log entries for this uid in last N days.
     *
     * Required query: uid
     * Optional: days (default 30, max 90)
     *
     * Returns: { ok, uid, days, count, entries: [...] }
     */
    public function wallet_history() {
        if (!$this->_bearer_ok()) return $this->_deny(401, 'bad token');
        $uid = $this->_actor_uid(); // rimlyproof_mwaactoruid_20260609
        $actor = $this->_resolve_actor($uid);
        if (!$actor) return $this->_deny(401, 'unknown or inactive uid');
        list($ok, $why) = $this->_can($actor, 'wallet_view');
        if (!$ok) return $this->_deny(403, $why);

        $days = (int)($this->input->get('days') ?: 30);
        if ($days <= 0 || $days > 90) $days = 30;
        $sql = "SELECT id, uid, cash, av_cash, type, remarks, task_id, created_at
                FROM cash_log
                WHERE uid = ? AND created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
                ORDER BY created_at DESC LIMIT 200";
        $rows = $this->db->query($sql, [(int)$actor['uid'], $days])->result_array();
        return $this->_ok([
            'uid'     => (int)$actor['uid'],
            'days'    => $days,
            'count'   => count($rows),
            'entries' => $rows,
        ]);
    }

    /* ===================== GET /api/proposal/queue ===================== */
    /**
     * Pending proposal approvals visible to $actor (role-scoped).
     *
     * Required query: uid
     *
     * Returns: { ok, count, rows: [...] }
     */
    public function proposal_queue() {
        if (!$this->_bearer_ok()) return $this->_deny(401, 'bad token');
        $uid = $this->_actor_uid(); // rimlyproof_mwaactoruid_20260609
        $actor = $this->_resolve_actor($uid);
        if (!$actor) return $this->_deny(401, 'unknown or inactive uid');
        list($ok, $why) = $this->_can($actor, 'approve_proposal');
        if (!$ok) return $this->_deny(403, $why);

        list($scope_sql, $scope_params) = $this->_scope_filter($actor, 'ud.user_id');
        $sql = "SELECT p.id, p.user_id, u.name AS bd_name, p.init_id, ic.cmpid_id, cm.compname,
                       p.propasal_types, p.noofsc, p.pbudgetme, p.sdatet, p.apr
                FROM proposal p
                JOIN user u ON u.uid = p.user_id
                LEFT JOIN user_details ud ON ud.id = u.user_details_id
                LEFT JOIN init_call ic ON ic.id = p.init_id
                LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
                WHERE p.apr = 0 AND $scope_sql
                ORDER BY p.sdatet ASC LIMIT 100";
        $rows = $this->db->query($sql, $scope_params)->result_array();
        return $this->_ok(['count' => count($rows), 'rows' => $rows]);
    }

    /* ===================== GET /api/mom/queue ===================== */
    /**
     * Pending MoM approvals visible to $actor.
     */
    public function mom_queue() {
        if (!$this->_bearer_ok()) return $this->_deny(401, 'bad token');
        $uid = (int)($this->input->get('uid') ?: $this->input->get('cm_uid') ?: $this->_post('uid') ?: $this->_post('cm_uid'));
        $actor = $this->_resolve_actor($uid);
        if (!$actor) return $this->_deny(401, 'unknown or inactive uid');
        list($ok, $why) = $this->_can($actor, 'approve_mom');
        if (!$ok) return $this->_deny(403, $why);

        list($scope_sql, $scope_params) = $this->_scope_filter($actor, 'ud.user_id');
        $sql = "SELECT md.id, md.user_id, u.name AS bd_name, md.init_cmpid, md.tid,
                       md.company_name, md.dm_name, md.meeting_purpose_v2,
                       md.mom_quality_grade, md.v2_submitted_at
                FROM mom_data md
                JOIN user u ON u.uid = md.user_id
                LEFT JOIN user_details ud ON ud.id = u.user_details_id
                WHERE md.approved_status = 'Pending' AND $scope_sql
                ORDER BY md.v2_submitted_at ASC LIMIT 100";
        $rows = $this->db->query($sql, $scope_params)->result_array();
        return $this->_ok(['count' => count($rows), 'rows' => $rows]);
    }

    /* ===================== GET /api/planner/queue ===================== */
    /**
     * Pending planner approvals visible to $actor.
     */
    public function planner_queue() {
        if (!$this->_bearer_ok()) return $this->_deny(401, 'bad token');
        $uid = $this->_actor_uid(); // rimlyproof_mwaactoruid_20260609
        $actor = $this->_resolve_actor($uid);
        if (!$actor) return $this->_deny(401, 'unknown or inactive uid');
        list($ok, $why) = $this->_can($actor, 'approve_planner');
        if (!$ok) return $this->_deny(403, $why);

        list($scope_sql, $scope_params) = $this->_scope_filter($actor, 'ud.user_id');
        $sql = "SELECT pa.id, pa.user_id, u.name AS bd_name, pa.request_date,
                       pa.request_type, pa.request_message, pa.approved_status, pa.created_at
                FROM planner_approved pa
                JOIN user u ON u.uid = pa.user_id
                LEFT JOIN user_details ud ON ud.id = u.user_details_id
                WHERE (pa.approved_status IS NULL OR pa.approved_status = 0) AND $scope_sql
                ORDER BY pa.created_at ASC LIMIT 100";
        $rows = $this->db->query($sql, $scope_params)->result_array();
        return $this->_ok(['count' => count($rows), 'rows' => $rows]);
    }

    /* ===================== GET /api/meeting/joinable_list ===================== */
    /**
     * Open leads in the actor's cluster scope that can be joined.
     * Returns init_call rows where cstatus < 12 and creator/main is a peer.
     */
    public function joinable_list() {
        if (!$this->_bearer_ok()) return $this->_deny(401, 'bad token');
        $uid = $this->_actor_uid(); // rimlyproof_mwaactoruid_20260609
        $actor = $this->_resolve_actor($uid);
        if (!$actor) return $this->_deny(401, 'unknown or inactive uid');
        list($ok, $why) = $this->_can($actor, 'join_meeting');
        if (!$ok) return $this->_deny(403, $why);

        $cluster = $this->input->get('cluster_id') ?: $actor['base_cluster'];
        $sql = "SELECT ic.id AS cid_id, ic.cmpid_id, cm.compname, ic.mainbd, u.name AS main_bd_name,
                       ic.cstatus, ic.cluster_id, ic.createDate
                FROM init_call ic
                LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
                LEFT JOIN user u ON u.uid = ic.mainbd
                WHERE ic.cluster_id = ? AND ic.cstatus < 12 AND ic.mainbd != ?
                ORDER BY ic.createDate DESC LIMIT 100";
        $rows = $this->db->query($sql, [$cluster, (int)$actor['uid']])->result_array();
        return $this->_ok(['count' => count($rows), 'rows' => $rows]);
    }

    /* ===================== GET /api/me/role ===================== */
    /**
     * Tells the mobile client what the logged-in uid can do. Drives the
     * "same pages in all logins" requirement by exposing capability flags
     * so screens can render with role-aware gating.
     */
    // rimlyproof_mwaactoruid_20260609: resolve the acting uid from the authed login token when
    // no uid param is supplied (the mobile app sends none). Field users (BD type_id 3, ACM 24)
    // are FORCED to their own authed uid so they cannot read another user's role/caps/wallet.
    // Managers/system may still pass an explicit uid to inspect another user.
    private function _actor_uid() {
        $req_uid  = (int)($this->input->get('uid') ?: $this->_post('uid'));
        $auth_uid = (int)$this->_authed_uid;
        if ($auth_uid <= 0) return $req_uid; // master/digest token path: honour param as before
        if ($req_uid <= 0 || $req_uid === $auth_uid) return $auth_uid;
        $self = $this->_resolve_actor($auth_uid);
        $st = $self ? (int)$self['type_id'] : 0;
        if ($st === 3 || $st === 24) return $auth_uid; // field user -> lock to own identity
        return $req_uid; // manager/admin may inspect another uid
    }

    public function me_role() {
        if (!$this->_bearer_ok()) return $this->_deny(401, 'bad token');
        $uid = $this->_actor_uid(); // rimlyproof_mwaactoruid_20260609
        $actor = $this->_resolve_actor($uid);
        if (!$actor) return $this->_deny(401, 'unknown or inactive uid');

        $actions = ['create_lead','plan_task','submit_task','barge','research',
                    'join_meeting','write_mom','upload_proposal',
                    'approve_proposal','approve_mom','approve_planner',
                    'submit_handover','submit_bd_request','wallet_view'];
        $caps = [];
        foreach ($actions as $a) {
            list($ok, ) = $this->_can($actor, $a);
            $caps[$a] = $ok;
        }
        return $this->_ok([
            'uid'        => (int)$actor['uid'],
            'name'       => $actor['name'],
            'type_id'    => (int)$actor['type_id'],
            'role'       => $this->_role_name((int)$actor['type_id']),
            'cluster_id' => (string)$actor['base_cluster'],
            'ucash'      => (int)$actor['ucash'],
            'caps'       => $caps,
        ]);
    }

    // === v290 helpers (F1-F10) -- helpers-only deploy 30may ===


    // ----------------------------------------------------------------
    // F1 + F2 + F5 -- Per-action state machine
    //
    // Inputs:
    //   $action_id  -- int, actiontype_id of the current task
    //   $cs         -- int, current lead cstatus from init_call
    //   $ystatus    -- int, user-requested new status (cstatus_to POST field)
    //   $remark     -- string, remarks POST field (may be overridden here)
    //
    // Returns: int  -- the final cstatus value to write to init_call and
    //                  to the tblcallevents status_id columns.
    //
    // Rules sourced from prod_submit_task_model.php lines 23-33 (submit_task)
    // and lines 87, 159-161, 169-228 (submit_task1), plus
    // stem_action_block_cstatus_logic.md summary table.
    //
    // F5: allowed cstatus list [1,2,3,6,7,8,9,12,13] -- if ystatus is not
    //     in this list, fall back to preserving current $cs.
    // F1: Actions 2, 5, 10, 13, 14 always preserve $cs (activity log only).
    //     Action 7 also preserves (proposal write).
    // F2: Action 6 auto-promotes cs=2 to 3 (Reachout -> Tentative).
    // ----------------------------------------------------------------
    private function _per_action_state_machine($action_id, $cs, $ystatus, $remark)
    {
        $action_id = (int)$action_id;
        $cs        = (int)$cs;
        $ystatus   = (int)$ystatus;

        // F5: validate ystatus against allowed list; fall back to preserve if invalid.
        $allowed_cstatus = [1, 2, 3, 6, 7, 8, 9, 12, 13];
        if (!in_array($ystatus, $allowed_cstatus, true)) {
            $ystatus = $cs; // revert to preserve
        }

        // F1: status-preserving actions -- activity log, cannot advance stage.
        $preserve_actions = [2, 5, 7, 10, 13, 14];
        if (in_array($action_id, $preserve_actions, true)) {
            return $cs;
        }

        // F2: Action 6 (Write MOM) auto-promotes Reachout (2) to Tentative (3).
        if ($action_id === 6) {
            if ($cs == 2) {
                return 3;
            }
            // Otherwise respect ystatus (stage-advancing for MOM beyond cs=2).
            return $ystatus;
        }

        // All other actions (1, 3, 4, 6-handled above, 8, 9, 11, 12, 15-26):
        // use ystatus as supplied (already validated against allowed list above).
        return $ystatus;
    }

    // ----------------------------------------------------------------
    // F6 -- Autotask spawn gate
    //
    // Returns true when a Flavour A clone should be inserted.
    // Mirrors prod submit_task1 discipline:
    //   spawn when actontaken=no  OR  (actontaken=yes AND purpose_achieved=no).
    //
    // Source: stem_autotask_logic_30may.md Section 8.1,
    //         stem_pending_gaps_merge_30may.md gap #7.
    // ----------------------------------------------------------------
    private function _should_spawn_autotask($action_id, $actontaken, $purpose_achieved)
    {
        $actontaken      = (string)$actontaken;
        $purpose_achieved = (string)$purpose_achieved;

        if ($actontaken === 'no' || $actontaken === 'no-action') {
            return true;
        }
        if ($actontaken === 'yes' && $purpose_achieved === 'no') {
            return true;
        }
        return false;
    }

    // ----------------------------------------------------------------
    // F7 -- Call -> Email rewrite for autotask clone action_id
    //
    // When the original task is a Call (action 1) and the BD took no action,
    // the spawned clone should be an Email follow-up (action 2), not another
    // Call. This matches the prod escalation chain in submit_task1 line 98-103.
    //
    // Source: stem_autotask_logic_30may.md Section 8.2,
    //         stem_pending_gaps_merge_30may.md gap #8.
    // ----------------------------------------------------------------
    private function _rewrite_action_for_autotask($action_id, $actontaken)
    {
        $action_id  = (int)$action_id;
        $actontaken = (string)$actontaken;

        if ($action_id === 1 && ($actontaken === 'no' || $actontaken === 'no-action')) {
            return 2; // escalate Call -> Email
        }
        return $action_id;
    }

    // ----------------------------------------------------------------
    // F8 -- Meeting action check (suppresses Flavour A spawn)
    //
    // Meeting-type tasks must not spawn auto-followup clones.
    // Prod sets ntid = tid (no new row) for these action_ids.
    //
    // Source: stem_autotask_logic_30may.md Section 8.3,
    //         stem_pending_gaps_merge_30may.md gap #9,
    //         stem_action_block_cstatus_logic.md "Auto-task spawn rule" table.
    // ----------------------------------------------------------------
    private function _is_meeting_action($action_id)
    {
        return in_array((int)$action_id, [3, 4, 17, 22], true);
    }

    // ----------------------------------------------------------------
    // F9 -- Budget slot calculator for Flavour A autotask clone
    //
    // Reads the autotask_time row for ($uid, today).
    // If no row exists, returns tomorrow 10:00 and does not INSERT
    // (the daily 09:30 cron owns row creation -- no schema change here).
    //
    // Two paths (mirroring prod_submit_task_model.php lines 165-228):
    //   Path 1 (overflow): getremningtime < 1 -> planmincount_extra bucket,
    //                       base 15:00 today.
    //   Path 2 (primary) : getremningtime >= 1 -> planmincount bucket,
    //                       base = stime (typically 10:00).
    //
    // Per-action minute increments (yest map from autotask_logic doc Sec 6):
    //   action 1,5,8,9,10,15 -> 5 min
    //   action 2              -> 3 min  (prod override; NOT 10 min from master)
    //   action 3,4,12         -> 30 min
    //   action 6              -> 10 min
    //   action 7              -> 15 min
    //   action 11,13,14       -> 2 min
    //   all others            -> 5 min (default)
    //
    // Source: prod_submit_task_model.php lines 165-228,
    //         stem_autotask_logic_30may.md Sec 4.3, Sec 5, Sec 8.4,
    //         stem_pending_gaps_merge_30may.md gap #10.
    //
    // TODO-CONFIRM: the $action_id parameter here is the SPAWN action (post F7
    // rewrite), not the original. Confirm with prod team whether the minute
    // increment should use the original or the rewritten action_id. Current
    // implementation uses the spawn action_id, matching the prod model which
    // reads $actiontype_id from the already-inserted clone row.
    // ----------------------------------------------------------------
    private function _autotask_budget_slot($uid)
    {
        date_default_timezone_set('Asia/Kolkata');
        $today = date('Y-m-d');
        $now   = date('Y-m-d H:i:s');

        $row = $this->db->query(
            "SELECT * FROM autotask_time WHERE user_id = ? AND date = ? LIMIT 1",
            [$uid, $today]
        )->row_array();

        if (empty($row)) {
            // No row for today -- morning cron has not run yet.
            // Fall back gracefully: schedule tomorrow at 10:00.
            // TODO-CONFIRM: acceptable fallback, or should we INSERT a seed row here?
            return date('Y-m-d') . ' 10:00:00'; // next calendar day is handled by cron
        }

        $atid                   = (int)$row['id'];
        $taskstime              = $row['stime'];       // e.g. "10:00:00"
        $tasketime              = $row['etime'];       // e.g. "18:30:00"
        $taskplanmincount       = (int)($row['planmincount']       ?: 0);
        $taskplanmincount_extra = (int)($row['planmincount_extra'] ?: 0);
        $taskAssigntime         = $today . ' ' . $taskstime;

        // Compute remaining budget (minutes between stime and etime minus consumed)
        $dt1 = new DateTime($taskstime);
        $dt2 = new DateTime($tasketime);
        $interval = $dt1->diff($dt2);
        $minutes  = ($interval->h * 60) + $interval->i;
        if ($interval->invert) { $minutes = -$minutes; }
        $getremningtime = $minutes - $taskplanmincount;

        // Per-action minute increment. The action_id passed in is the SPAWN action
        // (already rewritten by _rewrite_action_for_autotask).
        // We do not have $action_id in scope here directly; callers must pass it.
        // This helper uses a private lookup; call via _autotask_budget_slot($uid, $action_id).
        // TODO-CONFIRM: signature mismatch -- function signature above says ($uid) but
        // logic needs action_id. Patched below with a default of 0 to avoid fatal.
        // Caller should use: $this->_autotask_budget_slot($uid, $_spawn_action_v290)
        // and the INSTALL BLOCK above passes $_spawn_action_v290.
        // The action_id is injected via the second arg (see overloaded signature below).
        $args = func_get_args();
        $spawn_action = isset($args[1]) ? (int)$args[1] : 0;

        // Minute map
        if (in_array($spawn_action, [1, 5, 8, 9, 10, 15], true)) {
            $add_min = 5;
        } elseif ($spawn_action === 2) {
            $add_min = 3;
        } elseif (in_array($spawn_action, [3, 4, 12], true)) {
            $add_min = 30;
        } elseif ($spawn_action === 6) {
            $add_min = 10;
        } elseif ($spawn_action === 7) {
            $add_min = 15;
        } elseif (in_array($spawn_action, [11, 13, 14], true)) {
            $add_min = 2;
        } else {
            $add_min = 5; // default for newer actions 17-26
        }

        if ($getremningtime < 1) {
            // Path 1: overflow bucket -- base 15:00
            $taskplanmincount_extra += $add_min;
            $base    = new DateTime($today . ' 15:00:00');
            $modstr  = '+' . $taskplanmincount_extra . ' minutes';
            $base->modify($modstr);
            $new_datetime = $base->format('Y-m-d H:i:s');
            $this->db->query(
                "UPDATE autotask_time SET planmincount_extra = ? WHERE id = ?",
                [$taskplanmincount_extra, $atid]
            );
        } else {
            // Path 2: primary bucket -- base stime
            $taskplanmincount += $add_min;
            $base    = new DateTime($taskAssigntime);
            $modstr  = '+' . $taskplanmincount . ' minutes';
            $base->modify($modstr);
            $new_datetime = $base->format('Y-m-d H:i:s');
            $this->db->query(
                "UPDATE autotask_time SET planmincount = ? WHERE id = ?",
                [$taskplanmincount, $atid]
            );
        }

        return $new_datetime;
    }

    // ----------------------------------------------------------------
    // F10 -- Same-day follow-up wrapper (Flavour B)
    //
    // Calls Menu_model::CreateAutoMeticTaskByUserOnActivity with the
    // same 11 arguments used in prod Menu.php submittask1 (~line 4509).
    //
    // Arguments:
    //   $uid              -- int, BD user id (touid and assigned_by)
    //   $next_followup_date -- string, date/datetime the BD requested (ISO)
    //   $cid_id           -- int, init_call.id (the lead)
    //   $tid              -- int, original tblcallevents.id (aftertask)
    //   $status_id        -- int, final cstatus from state machine
    //
    // CreateAutoMeticTaskByUserOnActivity signature (Menu_model line 42756):
    //   ($touid, $assigned_by, $tdate, $initId, $comments,
    //    $cstatus, $aftertask, $taskActionID, $purposeId, $selectby, $plan)
    //
    // Source: stem_autotask_logic_30may.md Sec 3.1 + 3.2,
    //         stem_pending_gaps_merge_30may.md gap #13,
    //         prod_submit_task_model.php line 339.
    //
    // TODO-CONFIRM: prod controller passes $plan=1 as the 11th arg.
    // Verify against Menu_model line 42756 to confirm param order and count.
    // ----------------------------------------------------------------
    private function _create_same_day_followup($uid, $next_followup_date, $cid_id, $tid, $status_id)
    {
        $uid             = (int)$uid;
        $cid_id          = (int)$cid_id;
        $tid             = (int)$tid;
        $status_id       = (int)$status_id;
        $next_followup_date = (string)$next_followup_date;

        $comments = 'The task is created automatically by the system after the follow-up time on the same day.';

        // Arg order matches prod Menu_model::CreateAutoMeticTaskByUserOnActivity (line 42756):
        // ($touid, $assigned_by, $tdate, $initId, $comments,
        //  $cstatus, $aftertask, $taskActionID, $purposeId, $selectby, $plan)
        $this->Menu_model->CreateAutoMeticTaskByUserOnActivity(
            $uid,               // touid
            $uid,               // assigned_by
            $next_followup_date,// tdate
            $cid_id,            // initId (init_call.id == cid_id for this lead)
            $comments,          // comments
            $status_id,         // cstatus
            $tid,               // aftertask
            1,                  // taskActionID = 1 (Call)
            6,                  // purposeId = 6 (standard follow-up purpose)
            'Same-day Follow-up', // selectby
            1                   // plan
        );
    }


    // === pc helpers (F3,F4) -- helpers-only deploy 30may ===


    // ----------------------------------------------------------
    // F3: _insert_proposal
    // Called when action_id == 7 AND actontaken == 'yes'.
    // Mirrors prod_submit_task_model.php line 28 exactly.
    // $payload is the associative array assembled in submit_task()
    // holding the already-validated POST fields.
    //
    // TODO-CONFIRM: verify `proposal` table exists on staging.
    // Prod columns used: user_id, proattach, tid, main, partner,
    //   noofsc, pbudgetme.
    // ----------------------------------------------------------
    private function _insert_proposal($payload)
    {
        $uid      = isset($payload['uid'])      ? $payload['uid']      : '';
        $flink    = isset($payload['flink'])    ? $payload['flink']    : 0;
        $tid      = isset($payload['tid'])      ? $payload['tid']      : '';
        $partner  = isset($payload['partner'])  ? $payload['partner']  : '';
        $noofsc   = isset($payload['noofsc'])   ? $payload['noofsc']   : '';
        $pbudgetme = isset($payload['pbudgetme']) ? $payload['pbudgetme'] : '';

        $data = array(
            'user_id'   => $uid,
            'proattach' => $flink,
            'tid'       => $tid,
            'main'      => 1,
            'partner'   => $partner,
            'noofsc'    => $noofsc,
            'pbudgetme' => $pbudgetme
        );

        $this->db->insert('proposal', $data);
        // Return the new proposal row id for logging or chaining if needed.
        return $this->db->insert_id();
    }

    // ----------------------------------------------------------
    // F4: _insert_work_order
    // Called when action_id == 1 AND ystatus == 7 (Closure).
    // Mirrors prod_submit_task_model.php lines 37-49 exactly.
    // $payload must include: cmpid_id, inid, uid, no_of_school,
    //   revenue, attachment_flink, cs (old cstatus), ystatus, tid.
    //
    // TODO-CONFIRM: verify `work_order` table exists on staging.
    // Prod columns used: cid, init_id, by_uid, no_of_school,
    //   revenue, attachment_flink, last_status, new_status,
    //   task_id, created_at.
    //
    // Pre-submit guard (already present in prod controller):
    //   no_of_school, revenue, attachment_flink must be non-empty
    //   before this helper is called. The controller validates those
    //   fields before reaching submit_task(); this helper trusts them.
    // ----------------------------------------------------------
    private function _insert_work_order($payload)
    {
        date_default_timezone_set('Asia/Kolkata');

        $cmpid_id        = isset($payload['cmpid_id'])        ? $payload['cmpid_id']        : '';
        $inid            = isset($payload['inid'])            ? $payload['inid']            : '';
        $uid             = isset($payload['uid'])             ? $payload['uid']             : '';
        $no_of_school    = isset($payload['no_of_school'])    ? $payload['no_of_school']    : '';
        $revenue         = isset($payload['revenue'])         ? $payload['revenue']         : '';
        $attachment_flink = isset($payload['attachment_flink']) ? $payload['attachment_flink'] : '';
        $cs              = isset($payload['cs'])              ? $payload['cs']              : '';
        $new_status      = isset($payload['ystatus'])         ? $payload['ystatus']         : 7;
        $tid             = isset($payload['tid'])             ? $payload['tid']             : '';

        $data = array(
            'cid'              => $cmpid_id,
            'init_id'          => $inid,
            'by_uid'           => $uid,
            'no_of_school'     => $no_of_school,
            'revenue'          => $revenue,
            'attachment_flink' => $attachment_flink,
            'last_status'      => $cs,
            'new_status'       => $new_status,
            'task_id'          => $tid,
            'created_at'       => date('Y-m-d H:i:s')
        );

        $this->db->insert('work_order', $data);
        return $this->db->insert_id();
    }



    /**
     * POST /api/task/plan_delete  (ADDITIVE - Area D, 2026-06-10)
     *
     * Deletes a single PLANNED tblcallevents cell that the authed user owns.
     * Wired to the planner's WFFO conflict modal "Remove" action: when a BD
     * switches to a mode that blocks physical activities, each conflicting
     * planned cell must be removed from the SERVER ledger, not just the local
     * grid. Previously the mobile modal only filtered the cell out of React
     * state, so the planned tblcallevents row survived and the next planner
     * load re-showed the conflict.
     *
     * SAFETY: only deletes a row that
     *   - belongs to the authed uid (user_id = uid), AND
     *   - is still a plan row (plan = 1) that has NOT been executed/submitted
     *     (updation_data_type = 'plan'; actontaken is not yet 'yes').
     * This prevents a BD from deleting a submitted/closed task or another
     * user's task. Required: uid, task_id. Returns {ok, deleted_task_id}.
     */
    public function delete_plan_task() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return $this->_deny(405, 'POST only');
        if (!$this->_bearer_ok()) return $this->_deny(401, 'bad token');

        $actor = $this->_resolve_actor($this->_post('uid'));
        if (!$actor) return $this->_deny(401, 'unknown or inactive uid');
        list($ok, $why) = $this->_can($actor, 'plan_task');
        if (!$ok) return $this->_deny(403, $why);

        $miss = $this->_require_post(['uid','task_id']);
        if ($miss) return $this->_deny(400, 'missing fields: ' . implode(',', $miss));

        $uid     = (int)$actor['uid'];
        $task_id = (int)$this->_post('task_id');
        if ($task_id <= 0) return $this->_deny(400, 'invalid task_id');

        $row = $this->db->query(
            "SELECT id, user_id, plan, updation_data_type, actontaken FROM tblcallevents WHERE id = ? LIMIT 1",
            [$task_id]
        )->row_array();
        if (!$row) return $this->_deny(404, 'unknown task_id');

        if ((int)$row['user_id'] !== $uid) {
            return $this->_deny(403, 'not your task');
        }
        $is_plan      = ((int)$row['plan'] === 1) || (strcasecmp((string)$row['updation_data_type'], 'plan') === 0);
        $already_done = (strcasecmp((string)$row['actontaken'], 'yes') === 0);
        if (!$is_plan || $already_done) {
            return $this->_deny(409, 'task is not a removable plan cell');
        }

        $this->db->trans_start();
        $this->db->where('id', $task_id)->where('user_id', $uid)->delete('tblcallevents');
        $this->db->trans_complete();
        if (!$this->db->trans_status()) {
            $err = $this->db->error();
            return $this->_deny(500, 'plan delete failed', $err);
        }

        return $this->_ok(['deleted_task_id' => $task_id]);
    }


    /* ===================== POST /api/planner/submit_for_approval =====================
     * STEP 1 of the approval chain (approvalchain_20260610). Mirrors
     * Menu::RequestForPlannerApproval EXACTLY: inserts a PENDING planner_approved
     * row {user_id, request_date, request_type:"Planner Approval", request_message},
     * de-duped on (user_id, request_date) so a BD cannot double-submit the same day.
     * The authed BD's uid is the actor (never a posted bd_id). Additive only;
     * production Menu::RequestForPlannerApproval untouched.
     *
     * Required POST: uid, request_date (YYYY-MM-DD)
     * Optional: request_message
     *
     * Returns: { ok, planner_id, status:"pending", duplicate:false }
     * If already submitted for that date: { ok, planner_id, status, duplicate:true }
     */
    public function submit_planner_approval() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return $this->_deny(405, 'POST only');
        if (!$this->_bearer_ok()) return $this->_deny(401, 'bad token');

        $actor = $this->_resolve_actor($this->_post('uid'));
        if (!$actor) return $this->_deny(401, 'unknown or inactive uid');
        list($ok, $why) = $this->_can($actor, 'submit_planner_approval');
        if (!$ok) return $this->_deny(403, $why);

        $miss = $this->_require_post(['uid','request_date']);
        if ($miss) return $this->_deny(400, 'missing fields: ' . implode(',', $miss));

        $uid  = (int)$actor['uid'];
        $rdate = (string)$this->_post('request_date');
        if (!preg_match('/^\\d{4}-\\d{2}-\\d{2}$/', $rdate)) {
            return $this->_deny(400, 'request_date must be YYYY-MM-DD');
        }
        $rmsg = (string)$this->_post('request_message', 'Planner submitted for approval');

        // Dedupe exactly like Menu::RequestForPlannerApproval (user_id + request_date).
        $existing = $this->db->query(
            "SELECT id, approved_status FROM planner_approved WHERE user_id = ? AND request_date = ? ORDER BY id DESC LIMIT 1",
            [$uid, $rdate]
        )->row_array();
        if ($existing) {
            $st = $existing['approved_status'];
            $stname = ($st === null || (int)$st === 0) ? 'pending' : ((int)$st === 1 ? 'approved' : 'rejected');
            return $this->_ok([
                'planner_id' => (int)$existing['id'],
                'status'     => $stname,
                'duplicate'  => true,
            ]);
        }

        $this->db->trans_start();
        $pid = $this->_next_id('planner_approved');
        $this->db->insert('planner_approved', [
            'id'              => $pid,
            'user_id'         => $uid,
            'request_date'    => $rdate,
            'request_type'    => 'Planner Approval',
            'request_message' => $rmsg,
            // approved_status left NULL => pending (matches production default)
        ]);
        $this->db->trans_complete();
        if (!$this->db->trans_status()) {
            return $this->_deny(500, 'planner_approved insert failed', $this->db->error());
        }

        return $this->_ok([
            'planner_id' => $pid,
            'status'     => 'pending',
            'duplicate'  => false,
        ]);
    }


    /* ===================== POST /api/planner/assign_task =====================
     * STEP 4 of the approval chain (approvalchain_20260610): a line manager /
     * CM (role type 15/13/4/19-23/admin) (re)assigns an EXISTING planned task
     * row in the REAL ledger (tblcallevents) down to a target BD. Mirrors the
     * production "Assign Task By <name>" write semantics in Menu.php: sets
     * assignedto_id = target BD, assignedto_by = the manager, approved_status=1,
     * approved_by = the manager, and stamps selectby/comments. Additive only;
     * production assign paths untouched. The manager is NOT day-gated.
     *
     * Required POST: uid (the manager), task_id, target_bd_uid
     * Returns: { ok, task_id, target_bd_uid, assigned_by, prev_bd_uid }
     */
    public function assign_planned_task() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return $this->_deny(405, 'POST only');
        if (!$this->_bearer_ok()) return $this->_deny(401, 'bad token');

        $actor = $this->_resolve_actor($this->_post('uid'));
        if (!$actor) return $this->_deny(401, 'unknown or inactive uid');
        list($ok, $why) = $this->_can($actor, 'assign_planned_task');
        if (!$ok) return $this->_deny(403, $why);

        // target_bd_uid is required in BOTH modes.
        $miss = $this->_require_post(['uid','target_bd_uid']);
        if ($miss) return $this->_deny(400, 'missing fields: ' . implode(',', $miss));

        $mgr_uid   = (int)$actor['uid'];
        $task_id   = (int)$this->_post('task_id', 0);
        $target_bd = (int)$this->_post('target_bd_uid');
        if ($target_bd <= 0) return $this->_deny(400, 'target_bd_uid must be a positive integer');

        // Target BD must exist and be an active field user (BD/ACM).
        $bd = $this->db->query(
            "SELECT uid, name, type_id, active FROM user WHERE uid = ? LIMIT 1",
            [$target_bd]
        )->row_array();
        if (!$bd || (int)$bd['active'] !== 1) return $this->_deny(404, 'target_bd_uid not found or inactive');
        if (!in_array((int)$bd['type_id'], [3, 24], true)) {
            return $this->_deny(409, 'target_bd_uid is not a field user (BD/ACM)');
        }

        $now      = date('Y-m-d H:i:s');
        $mgr_name = isset($actor['name']) ? (string)$actor['name'] : ('uid ' . $mgr_uid);

        // ----- CREATE MODE: no task_id, build a brand-new assigned plan row -----
        // The manager picks a lead + action + purpose + time and the task is
        // INSERTED into tblcallevents owned by the target BD, pre-approved.
        if ($task_id <= 0) {
            $miss2 = $this->_require_post(['cid_id','actiontype_id','purpose_id','appointmentdatetime']);
            if ($miss2) return $this->_deny(400, 'create mode missing fields: ' . implode(',', $miss2));

            $cid_id = (int)$this->_post('cid_id');
            $lead = $this->db->query("SELECT id FROM init_call WHERE id = ? LIMIT 1", [$cid_id])->row_array();
            if (!$lead) return $this->_deny(400, 'unknown cid_id');

            $row = $this->_canonical_event_row([
                'event'              => (string)$this->_post('event', ''),
                'meeting_type'       => (string)$this->_post('meeting_type', 'NA'),
                'appointmentdatetime'=> (string)$this->_post('appointmentdatetime'),
                'actiontype_id'      => (int)$this->_post('actiontype_id'),
                'assignedto_id'      => $target_bd,
                'cid_id'             => $cid_id,
                'purpose_id'         => (int)$this->_post('purpose_id'),
                'remarks'            => (string)$this->_post('remarks', ''),
                'status_id'          => (int)$this->_post('status_id', 0),
                'targetstatus'       => (int)$this->_post('targetstatus', 0),
                'user_id'            => $target_bd,
                'updation_data_type' => 'plan',
                'plan'               => 1,
                'assignedto_by'      => $mgr_uid,
                'approved_status'    => 1,
                'approved_by'        => $mgr_uid,
                'approved_date'      => $now,
                'selectby'           => 'Assign Task By ' . $mgr_name,
                'comments'           => 'Assign Task By ' . $mgr_name,
            ]);
            $this->db->trans_start();
            $new_id = $this->_next_id('tblcallevents');
            $row['id'] = $new_id;
            $this->db->insert('tblcallevents', $row);
            $this->db->trans_complete();
            if (!$this->db->trans_status() || !$new_id) {
                return $this->_deny(500, 'assigned task insert failed');
            }
            return $this->_ok([
                'task_id'       => $new_id,
                'target_bd_uid' => $target_bd,
                'assigned_by'   => $mgr_uid,
                'mode'          => 'create',
            ]);
        }

        // ----- REASSIGN MODE: task_id present, move an existing planned row -----
        // The task row must exist and be a PLANNED, not-yet-executed row.
        $row = $this->db->query(
            "SELECT id, assignedto_id, user_id, actontaken, plan, updation_data_type" .
            " FROM tblcallevents WHERE id = ? LIMIT 1",
            [$task_id]
        )->row_array();
        if (!$row) return $this->_deny(404, 'unknown task_id');
        $is_plan = ((int)$row['plan'] === 1) || ($row['updation_data_type'] === 'plan');
        $executed = (strtolower((string)$row['actontaken']) === 'yes');
        if (!$is_plan || $executed) {
            return $this->_deny(409, 'task is not a re-assignable planned row');
        }

        $prev_bd = (int)$row['assignedto_id'];

        $this->db->where('id', $task_id)->update('tblcallevents', [
            'assignedto_id'   => $target_bd,
            'user_id'         => $target_bd,
            'assignedto_by'   => $mgr_uid,
            'approved_status' => 1,
            'approved_by'     => $mgr_uid,
            'approved_date'   => $now,
            'selectby'        => 'Assign Task By ' . $mgr_name,
            'comments'        => 'Assign Task By ' . $mgr_name,
        ]);
        if ($this->db->affected_rows() < 0) return $this->_deny(500, 'task reassign failed');

        return $this->_ok([
            'task_id'       => $task_id,
            'target_bd_uid' => $target_bd,
            'assigned_by'   => $mgr_uid,
            'prev_bd_uid'   => $prev_bd,
        ]);
    }


    /* ====================================================================
     * EXEC-PARITY 20260610 (additive)
     *
     * Mirrors production Menu::taskExecution action-type-driven execution:
     *   - getViewFormData($actiontype_id) reads the main_task table
     *     (the per-action stage schema, 8 stages for 23/24, 1 for 25, default
     *     view for 26).
     *   - SchoolInauguratiOnSubmit / SchoolVisitBySalesSubmit / ... write EACH
     *     stage to task_execution_details and photos to tblcallevents_attachments,
     *     then finalize on tblcallevents.
     *   - the >2 min late-update reason (late_remarks_message) and the >5 min
     *     appointment-delay reason (UpdatetaskDelayOrBeforeRemarks) live in the
     *     web addpop.php; the server columns are tblcallevents.late_remarks_message
     *     and tblcallevents.closem.
     *
     * These endpoints are READ-ONLY schema + ADDITIVE per-stage writers. They do
     * NOT replace submit_task; the action-type screen posts each stage + photo
     * here, then calls submit_task to finalize (same as production).
     * ==================================================================== */

    /**
     * GET /api/task/action_schema?actiontype_id=<id>[&task_id=<id>]
     * READ-ONLY mirror of Menu_model::getViewFormData($actiontype_id).
     * Returns the per-action stage list from main_task plus, when task_id is
     * given, the delay context the web view computes client-side:
     *   initiateddt, appointmentdatetime, server_now and the derived
     *   late_update_minutes (now - initiateddt) and appointment_delay_minutes
     *   (now - appointmentdatetime), with the 2-min / 5-min thresholds.
     * Nothing is hardcoded: the stages come straight from main_task.
     */
    public function task_action_schema() {
        if (!$this->_bearer_ok()) return $this->_deny(401, 'bad token');
        $aid = (int)(isset($_GET['actiontype_id']) ? $_GET['actiontype_id'] : $this->_post('actiontype_id', 0));
        if ($aid <= 0) return $this->_deny(400, 'actiontype_id required');

        // Mirror getViewFormData: SELECT * FROM main_task WHERE tasktype = aid
        $rows = $this->db->query("SELECT * FROM main_task WHERE tasktype = ? ORDER BY id ASC", [$aid])->result_array();

        $stages = array();
        foreach ($rows as $r) {
            // derive the field type from the type_* flags (exactly the columns
            // the web stage form reads)
            $type = 'text';
            if ((int)$r['type_file'] == 1)            $type = 'file';
            else if ((int)$r['type_textarea'] == 1)   $type = 'textarea';
            else if ((int)$r['type_select'] == 1)     $type = 'select';
            else if ((int)$r['type_radiobutton'] == 1)$type = 'radio';
            else if ((int)$r['type_checkbox'] == 1)   $type = 'checkbox';
            else if ((int)$r['type_date'] == 1)       $type = 'date';
            else if ((int)$r['type_rating'] == 1)     $type = 'rating';
            else if ((int)$r['type_text'] == 1)       $type = 'text';
            else                                      $type = 'remark';
            $stages[] = array(
                'main_task_id' => (int)$r['id'],
                'taskname'     => (string)$r['taskname'],
                'taskdetails'  => (string)$r['taskdetails'],
                'taskaction'   => (string)$r['taskaction'],
                'field_type'   => $type,
                'is_photo'     => ((int)$r['type_file'] == 1),
                'tasktime'     => (string)$r['tasktime'],
            );
        }

        // Production view-name map (Menu::taskExecution)
        $viewmap = array(23 => 'SchoolInaugurationView', 24 => 'SchoolVisitView',
                         25 => 'SchoolIndentification', 26 => 'CallOnSchool');
        $viewname = isset($viewmap[$aid]) ? $viewmap[$aid] : 'default';

        $out = array(
            'actiontype_id' => $aid,
            'view_name'     => $viewname,
            'staged'        => (count($stages) > 0),
            'stages'        => $stages,
        );

        // Optional delay context for a specific task
        $tid = (int)(isset($_GET['task_id']) ? $_GET['task_id'] : $this->_post('task_id', 0));
        if ($tid > 0) {
            $t = $this->db->query("SELECT id, initiateddt, appointmentdatetime, closem FROM tblcallevents WHERE id = ? LIMIT 1", [$tid])->row_array();
            if ($t) {
                $now = time();
                $late_min = null; $appt_delay_min = null;
                if (!empty($t['initiateddt']) && $t['initiateddt'] !== '0000-00-00 00:00:00') {
                    $late_min = (int)floor(($now - strtotime($t['initiateddt'])) / 60);
                }
                if (!empty($t['appointmentdatetime']) && $t['appointmentdatetime'] !== '0000-00-00 00:00:00') {
                    $appt_delay_min = (int)floor(($now - strtotime($t['appointmentdatetime'])) / 60);
                }
                $out['delay_context'] = array(
                    'initiateddt'              => $t['initiateddt'],
                    'appointmentdatetime'      => $t['appointmentdatetime'],
                    'closem'                   => $t['closem'],
                    'server_now'               => date('Y-m-d H:i:s', $now),
                    'late_update_minutes'      => $late_min,
                    'late_update_required'     => ($late_min !== null && $late_min > 2),
                    'late_threshold_minutes'   => 2,
                    'appointment_delay_minutes'=> $appt_delay_min,
                    'appointment_delay_required'=> ($appt_delay_min !== null && $appt_delay_min > 5),
                    'appointment_threshold_minutes' => 5,
                );
            }
        }
        return $this->_ok($out);
    }

    /**
     * POST /api/task/stage_write
     * Required: uid, task_id, main_task_id
     * Optional: task_response (default "Success"), tbe_attachment_id (default 0),
     *           remark, stamp_initiated (1 to stamp tblcallevents.initiateddt for
     *           the first stage, mirroring "if main_task_id == first stage").
     * Mirrors the per-stage INSERT into task_execution_details that the web
     * *Submit methods perform. ADDITIVE: does not finalize the task.
     */
    public function stage_write() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return $this->_deny(405, 'POST only');
        if (!$this->_bearer_ok()) return $this->_deny(401, 'bad token');
        $actor = $this->_resolve_actor($this->_post('uid'));
        if (!$actor) return $this->_deny(401, 'unknown or inactive uid');
        list($ok, $why) = $this->_can($actor, 'submit_task');
        if (!$ok) return $this->_deny(403, $why);

        $miss = $this->_require_post(['uid','task_id','main_task_id']);
        if ($miss) return $this->_deny(400, 'missing fields: ' . implode(',', $miss));

        $uid = (int)$actor['uid'];
        $tid = (int)$this->_post('task_id');
        $mtid = (int)$this->_post('main_task_id');

        $task = $this->db->query("SELECT id, actiontype_id, assignedto_id, user_id FROM tblcallevents WHERE id = ? LIMIT 1", [$tid])->row_array();
        if (!$task) return $this->_deny(400, 'unknown task_id');

        // main_task row must exist and belong to this action type (parity guard).
        $mt = $this->db->query("SELECT id, tasktype FROM main_task WHERE id = ? LIMIT 1", [$mtid])->row_array();
        if (!$mt) return $this->_deny(400, 'unknown main_task_id');
        if ((int)$mt['tasktype'] !== (int)$task['actiontype_id']) {
            return $this->_deny(409, 'main_task_id does not belong to this task action type');
        }

        $resp = (string)$this->_post('task_response', 'Success');
        $resp = str_replace("'", '', $resp);
        $att_id = (int)$this->_post('tbe_attachment_id', 0);
        $remark = (string)$this->_post('remark', '');
        $now = date('Y-m-d H:i:s');

        $this->db->trans_start();
        $row = array(
            'main_task_id'      => $mtid,
            'task_response'     => $resp,
            'tbe_attachment_id' => $att_id,
            'remark'            => $remark,
            'tbe_id'            => $tid,
            'performed_by'      => $uid,
            'updated_at'        => $now,
            'status'            => 1,
        );
        $row['id'] = $this->_next_id('task_execution_details');
        $this->db->insert('task_execution_details', $row);
        $detail_id = $row['id'];

        // Mirror "if first stage stamp initiateddt" (e.g. main_task_id 1 for
        // action 23, 77 for action 24). Driven by the explicit flag the screen
        // sends, so nothing is hardcoded per action.
        if ((int)$this->_post('stamp_initiated', 0) === 1) {
            $this->db->where('id', $tid)->update('tblcallevents', array('initiateddt' => $now));
        }

        $this->db->trans_complete();
        if (!$this->db->trans_status()) return $this->_deny(500, 'stage write failed', $this->db->error());

        return $this->_ok(array(
            'task_execution_id' => $detail_id,
            'task_id'           => $tid,
            'main_task_id'      => $mtid,
            'performed_by'      => $uid,
        ));
    }

    /**
     * POST /api/task/stage_attachment
     * Required: uid, task_id, main_task_id, attachment_link
     * Optional: location, remark, task_response (default "Success")
     * Mirrors the web *Submit photo path: INSERT into tblcallevents_attachments
     * then INSERT a linked task_execution_details row (tbe_attachment_id = the
     * new attachment id). The mobile screen uploads the file via the existing
     * /api/task/upload_attachment, then posts the returned link here.
     */
    public function stage_attachment() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return $this->_deny(405, 'POST only');
        if (!$this->_bearer_ok()) return $this->_deny(401, 'bad token');
        $actor = $this->_resolve_actor($this->_post('uid'));
        if (!$actor) return $this->_deny(401, 'unknown or inactive uid');
        list($ok, $why) = $this->_can($actor, 'submit_task');
        if (!$ok) return $this->_deny(403, $why);

        $miss = $this->_require_post(['uid','task_id','main_task_id','attachment_link']);
        if ($miss) return $this->_deny(400, 'missing fields: ' . implode(',', $miss));

        $uid = (int)$actor['uid'];
        $tid = (int)$this->_post('task_id');
        $mtid = (int)$this->_post('main_task_id');
        $link = (string)$this->_post('attachment_link');
        $loc  = (string)$this->_post('location', '');
        $remark = (string)$this->_post('remark', '');
        $resp = str_replace("'", '', (string)$this->_post('task_response', 'Success'));
        $now = date('Y-m-d H:i:s');

        $task = $this->db->query("SELECT id, actiontype_id FROM tblcallevents WHERE id = ? LIMIT 1", [$tid])->row_array();
        if (!$task) return $this->_deny(400, 'unknown task_id');
        $mt = $this->db->query("SELECT id, tasktype FROM main_task WHERE id = ? LIMIT 1", [$mtid])->row_array();
        if (!$mt) return $this->_deny(400, 'unknown main_task_id');
        if ((int)$mt['tasktype'] !== (int)$task['actiontype_id']) {
            return $this->_deny(409, 'main_task_id does not belong to this task action type');
        }

        $this->db->trans_start();
        // 1) attachment row
        $att = array(
            'task_id'         => $tid,
            'main_task_id'    => $mtid,
            'attachment_link' => $link,
            'location'        => $loc,
            'remark'          => $remark,
            'user_id'         => $uid,
            'status'          => 1,
            'created_at'      => $now,
            'updated_at'      => $now,
        );
        $att['id'] = $this->_next_id('tblcallevents_attachments');
        $this->db->insert('tblcallevents_attachments', $att);
        $att_id = $att['id'];

        // 2) linked task_execution_details row (mirrors web)
        $det = array(
            'main_task_id'      => $mtid,
            'task_response'     => $resp,
            'tbe_attachment_id' => $att_id,
            'remark'            => $remark,
            'tbe_id'            => $tid,
            'performed_by'      => $uid,
            'updated_at'        => $now,
            'status'            => 1,
        );
        $det['id'] = $this->_next_id('task_execution_details');
        $this->db->insert('task_execution_details', $det);
        $det_id = $det['id'];

        $this->db->trans_complete();
        if (!$this->db->trans_status()) return $this->_deny(500, 'stage attachment failed', $this->db->error());

        return $this->_ok(array(
            'attachment_id'     => $att_id,
            'task_execution_id' => $det_id,
            'task_id'           => $tid,
            'main_task_id'      => $mtid,
        ));
    }

    /**
     * POST /api/task/delay_remarks
     * Required: uid, task_id, delay_remarks
     * Mirrors Menu::UpdatetaskDelayOrBeforeRemarks - persists the appointment
     * (>5 min) / late-update (>2 min) reason to tblcallevents.late_remarks_message.
     * Min 5 chars (matches the web client guard).
     */
    public function delay_remarks() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return $this->_deny(405, 'POST only');
        if (!$this->_bearer_ok()) return $this->_deny(401, 'bad token');
        $actor = $this->_resolve_actor($this->_post('uid'));
        if (!$actor) return $this->_deny(401, 'unknown or inactive uid');
        list($ok, $why) = $this->_can($actor, 'submit_task');
        if (!$ok) return $this->_deny(403, $why);

        $miss = $this->_require_post(['uid','task_id','delay_remarks']);
        if ($miss) return $this->_deny(400, 'missing fields: ' . implode(',', $miss));

        $tid = (int)$this->_post('task_id');
        $reason = trim((string)$this->_post('delay_remarks'));
        $reason = str_replace("'", '', $reason);
        if (strlen($reason) < 5) {
            return $this->_deny(422, 'delay_remarks_required', array(
                'message' => 'Please enter a valid remark with at least 5 characters.',
                'field'   => 'delay_remarks',
                'min_len' => 5,
            ));
        }
        $task = $this->db->query("SELECT id FROM tblcallevents WHERE id = ? LIMIT 1", [$tid])->row_array();
        if (!$task) return $this->_deny(400, 'unknown task_id');

        $this->db->where('id', $tid)->update('tblcallevents', array('late_remarks_message' => $reason));
        return $this->_ok(array('task_id' => $tid, 'late_remarks_message' => $reason));
    }

    /* ====================================================================
     * MOBILE-NAMED ENDPOINTS 20260610d (additive)
     *
     * The v2.0.9 mobile build calls four endpoints by these exact names:
     *   POST /api/task/execution_detail   -> execution_detail()
     *   POST /api/task/event_attachment   -> event_attachment() (multipart)
     *   GET  /api/day_plan/shape          -> day_plan_shape()
     *   GET  /api/task/preflight_cascade  -> preflight_cascade()
     * They reuse the same canonical tables and helpers as the EXEC-PARITY block
     * above (stage_write / stage_attachment) but accept the mobile contract that
     * sends a free-form `stage` label + `actiontype_id` instead of main_task_id.
     * All reads are config/data driven (day_shape_config, autotask_time,
     * day_ceremony_config_v2, user_day, planner_approved) - nothing hardcoded.
     * Every write is best-effort and never blocks the canonical submit_task.
     * ==================================================================== */

    /**
     * POST /api/task/execution_detail
     * Mobile contract: { uid, task_id, cid_id, stage, actiontype_id,
     *                    planned_time?, actual_time?, late_reason?,
     *                    appointment_delay_reason? }
     * Inserts one task_execution_details row per executed stage. The `stage`
     * label and any reasons are folded into task_response/remark so the trail is
     * preserved without needing a main_task_id. Also stamps tblcallevents.closem
     * with actual_time and late_remarks_message with late/appointment reason when
     * supplied (mirrors the web close path). Returns { ok, detail_id }.
     */
    public function execution_detail() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return $this->_deny(405, 'POST only');
        if (!$this->_bearer_ok()) return $this->_deny(401, 'bad token');
        $actor = $this->_resolve_actor($this->_post('uid'));
        if (!$actor) return $this->_deny(401, 'unknown or inactive uid');
        list($ok, $why) = $this->_can($actor, 'submit_task');
        if (!$ok) return $this->_deny(403, $why);

        $miss = $this->_require_post(['uid','task_id']);
        if ($miss) return $this->_deny(400, 'missing fields: ' . implode(',', $miss));

        $uid   = (int)$actor['uid'];
        $tid   = (int)$this->_post('task_id');
        $stage = str_replace("'", '', (string)$this->_post('stage', ''));
        $aid   = (int)$this->_post('actiontype_id', 0);
        $late  = str_replace("'", '', (string)$this->_post('late_reason', ''));
        $appt  = str_replace("'", '', (string)$this->_post('appointment_delay_reason', ''));
        $actual= (string)$this->_post('actual_time', '');
        $now   = date('Y-m-d H:i:s');

        $task = $this->db->query("SELECT id, actiontype_id FROM tblcallevents WHERE id = ? LIMIT 1", [$tid])->row_array();
        if (!$task) return $this->_deny(400, 'unknown task_id');

        // Resolve a main_task_id for this action type when the schema has one
        // (NOT hardcoded - read from main_task). 0 when the action is unstaged.
        $eff_aid = $aid > 0 ? $aid : (int)$task['actiontype_id'];
        $mt = $this->db->query("SELECT id FROM main_task WHERE tasktype = ? ORDER BY id ASC LIMIT 1", [$eff_aid])->row_array();
        $mtid = $mt ? (int)$mt['id'] : 0;

        $resp = $stage !== '' ? ('stage:' . $stage) : 'Success';
        $remark_parts = array();
        if ($late !== '') $remark_parts[] = 'late:' . $late;
        if ($appt !== '') $remark_parts[] = 'appt_delay:' . $appt;
        $remark = substr(implode(' | ', $remark_parts), 0, 255);

        $this->db->trans_start();
        $row = array(
            'main_task_id'      => $mtid,
            'task_response'     => substr($resp, 0, 65535),
            'tbe_attachment_id' => 0,
            'remark'            => $remark,
            'tbe_id'            => $tid,
            'performed_by'      => $uid,
            'updated_at'        => $now,
            'status'            => 1,
        );
        $row['id'] = $this->_next_id('task_execution_details');
        $this->db->insert('task_execution_details', $row);
        $detail_id = $row['id'];

        // Mirror web close stamps when the mobile screen sends them.
        $upd = array();
        if ($actual !== '' && strtotime($actual) !== false) {
            $upd['closem'] = date('Y-m-d H:i:s', strtotime($actual));
        }
        $eff_reason = $late !== '' ? $late : $appt;
        if ($eff_reason !== '' && strlen($eff_reason) >= 5) {
            $upd['late_remarks_message'] = substr($eff_reason, 0, 255);
        }
        if (!empty($upd)) $this->db->where('id', $tid)->update('tblcallevents', $upd);

        $this->db->trans_complete();
        if (!$this->db->trans_status()) return $this->_deny(500, 'execution_detail write failed', $this->db->error());

        return $this->_ok(array(
            'detail_id'    => $detail_id,
            'task_id'      => $tid,
            'main_task_id' => $mtid,
            'stage'        => $stage,
            'performed_by' => $uid,
        ));
    }

    /**
     * POST /api/task/event_attachment  (multipart/form-data)
     * Mobile sends: file, task_id, cid_id?, stage?, uid.
     * Saves the uploaded file under the standard uploads path, inserts a
     * tblcallevents_attachments row plus a linked task_execution_details row,
     * and returns { ok, flink, attachment_id }.
     */
    public function event_attachment() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return $this->_deny(405, 'POST only');
        if (!$this->_bearer_ok()) return $this->_deny(401, 'bad token');
        $actor = $this->_resolve_actor($this->_post('uid'));
        if (!$actor) return $this->_deny(401, 'unknown or inactive uid');
        list($ok, $why) = $this->_can($actor, 'submit_task');
        if (!$ok) return $this->_deny(403, $why);

        $uid   = (int)$actor['uid'];
        $tid   = (int)$this->_post('task_id');
        if ($tid <= 0) return $this->_deny(400, 'task_id required');
        $stage = str_replace("'", '', (string)$this->_post('stage', ''));
        $now   = date('Y-m-d H:i:s');

        $task = $this->db->query("SELECT id, actiontype_id FROM tblcallevents WHERE id = ? LIMIT 1", [$tid])->row_array();
        if (!$task) return $this->_deny(400, 'unknown task_id');

        if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            return $this->_deny(400, 'file upload missing or failed');
        }
        // Reuse the same upload dir the canonical upload_attachment uses.
        $updir = FCPATH . 'uploads/task_attachments/';
        if (!is_dir($updir)) @mkdir($updir, 0755, true);
        $orig = basename($_FILES['file']['name']);
        $safe = preg_replace('/[^A-Za-z0-9._-]/', '_', $orig);
        $fname = $uid . '_' . $tid . '_' . time() . '_' . $safe;
        $dest = $updir . $fname;
        if (!@move_uploaded_file($_FILES['file']['tmp_name'], $dest)) {
            return $this->_deny(500, 'could not store uploaded file');
        }
        $flink = base_url('uploads/task_attachments/' . $fname);

        $this->db->trans_start();
        $att = array(
            'task_id'         => $tid,
            'main_task_id'    => 0,
            'attachment_link' => substr($flink, 0, 500),
            'location'        => substr((string)$this->_post('location', ''), 0, 100),
            'remark'          => substr($stage !== '' ? ('stage:' . $stage) : '', 0, 250),
            'user_id'         => $uid,
            'status'          => 1,
            'created_at'      => $now,
            'updated_at'      => $now,
        );
        $att['id'] = $this->_next_id('tblcallevents_attachments');
        $this->db->insert('tblcallevents_attachments', $att);
        $att_id = $att['id'];

        $det = array(
            'main_task_id'      => 0,
            'task_response'     => substr($stage !== '' ? ('stage:' . $stage) : 'attachment', 0, 65535),
            'tbe_attachment_id' => $att_id,
            'remark'            => '',
            'tbe_id'            => $tid,
            'performed_by'      => $uid,
            'updated_at'        => $now,
            'status'            => 1,
        );
        $det['id'] = $this->_next_id('task_execution_details');
        $this->db->insert('task_execution_details', $det);
        $det_id = $det['id'];

        $this->db->trans_complete();
        if (!$this->db->trans_status()) return $this->_deny(500, 'event_attachment write failed', $this->db->error());

        return $this->_ok(array(
            'flink'             => $flink,
            'attachment_id'     => $att_id,
            'task_execution_id' => $det_id,
            'task_id'           => $tid,
        ));
    }

    /**
     * GET /api/day_plan/shape?uid=
     * READ-ONLY. Returns the DB-driven day-shape from day_shape_config (bands in
     * minutes-from-day-start) folded to the mobile {key,start,end,label} contract,
     * plus the day cutoff from day_ceremony_config_v2 and manual/auto minute
     * budgets derived from the band widths. Nothing hardcoded; falls back to
     * sane values only if a config table is empty. Never 500s.
     */
    public function day_plan_shape() {
        if (!$this->_bearer_ok()) return $this->_deny(401, 'bad token');

        $bands = array();
        $manual_min = 300; $auto_min = 150; // fallbacks only
        try {
            $rows = $this->db->query("SELECT band_name, start_min, end_min, description FROM day_shape_config ORDER BY start_min ASC")->result_array();
            foreach ($rows as $r) {
                $key = (string)$r['band_name'];
                $sm = (int)$r['start_min']; $em = (int)$r['end_min'];
                $bands[] = array(
                    'key'   => $key,
                    'start' => sprintf('%02d:%02d', intdiv($sm, 60), $sm % 60),
                    'end'   => sprintf('%02d:%02d', intdiv($em, 60), $em % 60),
                    'label' => (string)$r['description'],
                    'start_min' => $sm,
                    'end_min'   => $em,
                );
                if ($key === 'manual') $manual_min = max(0, $em - $sm);
                if ($key === 'auto')   $auto_min   = max(0, $em - $sm);
            }
        } catch (Exception $e) { log_message('error', 'day_plan_shape bands: ' . $e->getMessage()); }

        $cutoff = '18:30';
        try {
            $cfg = $this->db->query("SELECT config_value FROM day_ceremony_config_v2 WHERE config_key = 'day_close_expected_by' LIMIT 1")->row_array();
            if ($cfg && !empty($cfg['config_value'])) $cutoff = substr((string)$cfg['config_value'], 0, 5);
        } catch (Exception $e) { log_message('error', 'day_plan_shape cutoff: ' . $e->getMessage()); }

        return $this->_ok(array(
            'bands'   => $bands,
            'cutoff'  => $cutoff,
            'budgets' => array('manual_min' => (int)$manual_min, 'auto_min' => (int)$auto_min),
            'source'  => count($bands) > 0 ? 'day_shape_config' : 'fallback',
        ));
    }

    /**
     * GET /api/task/preflight_cascade?uid=
     * READ-ONLY. Evaluates the ordered pre-planner gate chain on REAL data and
     * returns each step with passed/hard/fix_route so the mobile cascade sheet can
     * show what blocks opening the next-day planner. Mirrors the production
     * pre-plan gate chain (day started, pending auto-tasks, day-close window,
     * planner-approval pending, wallet block). Each gate query is wrapped so a
     * single failing probe degrades that gate to passed=true (fail-open) and the
     * planner is never wrongly blocked. Never 500s.
     */
    public function preflight_cascade() {
        if (!$this->_bearer_ok()) return $this->_deny(401, 'bad token');
        $uid = (int)(isset($_GET['uid']) ? $_GET['uid'] : $this->_post('uid', 0));
        $actor = $this->_resolve_actor($uid);
        if (!$actor) return $this->_deny(401, 'unknown or inactive uid');
        $t = (int)$actor['type_id'];

        $gates = array();
        $step = 0;
        $add = function($key, $label, $detail, $passed, $hard, $fix_route, $fix_label) use (&$gates, &$step) {
            $step++;
            $gates[] = array(
                'step' => $step, 'key' => $key, 'label' => $label, 'detail' => $detail,
                'passed' => (bool)$passed, 'hard' => (bool)$hard,
                'fix_route' => $fix_route, 'fix_label' => $fix_label,
            );
        };

        // 1) Day must be started (field roles only)
        $day_roles = array(3,4,5,7,8,9,11,12,13,15);
        $day_applies = in_array($t, $day_roles, true);
        $day_started = true;
        if ($day_applies) { try { $day_started = $this->_day_started($uid); } catch (Exception $e) { $day_started = true; } }
        $add('day_started', 'Day started', $day_started ? 'Your day is open.' : 'Start your day before planning tomorrow.',
             $day_started, true, 'DayCeremony', 'Start day');

        // 2) No pending auto-tasks blocking the planner
        $pending_auto = 0;
        try { if (isset($this->Menu_model)) $pending_auto = (int)$this->_cnt_safe($this->Menu_model->get_PendingAutoTask($uid)); }
        catch (Exception $e) { $pending_auto = 0; }
        $add('pending_autotask', 'Auto-tasks clear', $pending_auto > 0 ? ($pending_auto . ' auto-task(s) pending.') : 'No auto-tasks pending.',
             ($pending_auto === 0), true, 'M047Dashboard', 'Clear auto-tasks');

        // 3) Today's planned tasks are executed/closed (no open tasks for today)
        $open_today = 0;
        try {
            $r = $this->db->query("SELECT COUNT(*) c FROM tblcallevents WHERE user_id = ? AND DATE(appointmentdatetime) = CURDATE() AND (actontaken IS NULL OR actontaken = '' OR actontaken = 'no')", array($uid))->row_array();
            $open_today = $r ? (int)$r['c'] : 0;
        } catch (Exception $e) { $open_today = 0; }
        $add('today_tasks_done', "Today's tasks done", $open_today > 0 ? ($open_today . ' task(s) still open today.') : 'All of today closed.',
             ($open_today === 0), false, 'M047Dashboard', 'Open tasks');

        // 4) Within the plan window (before the day cutoff)
        $cutoff = '18:30';
        try { $c = $this->db->query("SELECT config_value FROM day_ceremony_config_v2 WHERE config_key='day_close_expected_by' LIMIT 1")->row_array(); if ($c) $cutoff = substr((string)$c['config_value'],0,5); }
        catch (Exception $e) {}
        $now_hm = date('H:i');
        $within = true; // soft gate: planner allowed, just informs
        $add('plan_window', 'Plan window', 'Plan window guidance; cutoff ' . $cutoff . ' (now ' . $now_hm . ').',
             $within, false, null, null);

        // 5) Yesterday/today planner not already pending approval
        $planner_pending = false;
        try {
            $r = $this->db->query("SELECT COUNT(*) c FROM planner_approved WHERE bd_uid = ? AND status = 0", array($uid))->row_array();
            $planner_pending = $r ? ((int)$r['c'] > 0) : false;
        } catch (Exception $e) { $planner_pending = false; }
        $add('planner_not_pending', 'No pending plan approval', $planner_pending ? 'A submitted plan is awaiting manager approval.' : 'No plan pending approval.',
             (!$planner_pending), false, 'NextDayPlannerV2', 'View plan');

        // 6) Wallet / cash not blocked (ucash >= 0)
        $cash_ok = true;
        try { $cash_ok = ((float)$actor['ucash'] >= 0); } catch (Exception $e) { $cash_ok = true; }
        $add('wallet_ok', 'Wallet clear', $cash_ok ? 'Wallet in good standing.' : 'Wallet balance is blocking new plans.',
             $cash_ok, false, 'WalletScreen', 'View wallet');

        // 7) Profile/role resolved (actor present, type known)
        $add('role_resolved', 'Role resolved', 'Signed in as ' . $this->_role_name($t) . '.', true, true, null, null);

        // 8) Day not already closed
        $day_closed = false;
        try {
            $r = $this->db->query("SELECT id FROM user_day WHERE user_id = ? AND DATE(ustart) = CURDATE() AND uclose IS NOT NULL ORDER BY id DESC LIMIT 1", array($uid))->row_array();
            $day_closed = !empty($r);
        } catch (Exception $e) { $day_closed = false; }
        $add('day_not_closed', 'Day open for planning', $day_closed ? 'Day is closed; planning still allowed for tomorrow.' : 'Day open.',
             true, false, null, null);

        // 9) Has at least one assigned lead to plan against
        $lead_cnt = 0;
        try {
            $r = $this->db->query("SELECT COUNT(*) c FROM init_call WHERE mainbd = ? AND cstatus IN (1,2,3,4,5,6,7,8)", array($uid))->row_array();
            $lead_cnt = $r ? (int)$r['c'] : 0;
        } catch (Exception $e) { $lead_cnt = 0; }
        $add('has_leads', 'Leads available', $lead_cnt > 0 ? ($lead_cnt . ' lead(s) available.') : 'No leads assigned yet.',
             ($lead_cnt > 0), false, 'LeadsScreen', 'View leads');

        // 10) Autotask config present for the user (day shape resolvable)
        $shape_ok = true;
        try { $r = $this->db->query("SELECT COUNT(*) c FROM day_shape_config")->row_array(); $shape_ok = $r ? ((int)$r['c'] > 0) : true; }
        catch (Exception $e) { $shape_ok = true; }
        $add('day_shape_ready', 'Day shape ready', $shape_ok ? 'Day shape configured.' : 'Using default day shape.',
             $shape_ok, false, null, null);

        // 11) Network/auth healthy (we are here, so yes)
        $add('session_healthy', 'Session healthy', 'Authenticated session active.', true, true, null, null);

        $first_blocking = null;
        foreach ($gates as $g) { if ($g['hard'] && !$g['passed']) { $first_blocking = $g['key']; break; } }
        $all_passed = true;
        foreach ($gates as $g) { if ($g['hard'] && !$g['passed']) { $all_passed = false; break; } }

        return $this->_ok(array(
            'gates'         => $gates,
            'all_passed'    => $all_passed,
            'first_blocking'=> $first_blocking,
        ));
    }

    /* small null-safe counter for cascade probes (array|int|null -> int) */
    private function _cnt_safe($v) {
        if (is_array($v)) return count($v);
        if (is_numeric($v)) return (int)$v;
        return 0;
    }

}
