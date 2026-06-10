<?php
/**
 * STEM CRM - Migration 027 - Stakeholder Contact Book Agent
 *
 * Builds and maintains stakeholder_contact_book per cid_id. Five roles:
 *   - primary_dm       (the main decision maker BD has been talking to)
 *   - secondary_dm     (other people seen in meetings)
 *   - cfo_bursar       (finance side)
 *   - principal        (school principal, often final signer)
 *   - trustee          (board member, optional but visible)
 *
 * Sources harvested:
 *   - init_call               (BD's first capture of primary DM contact)
 *   - mom_data attendees      (people seen in meetings, expand DM set)
 *   - linkedin_csr_check      (Phase 2: cfo/principal identified by CSR check)
 *   - manual_entry            (BD or CM types in directly)
 *
 * Bounce tracking: bounce_flag set when migration 026 Gmail send returns
 * permanent failure. Soft bounces tracked via bounce_soft_count, hard bounce
 * sets bounce_flag=1 (skip in resolve_recipients).
 *
 * Verification: BD can verify a contact (verified_flag=1) to flag it as
 * confirmed. Verified contacts are preferred over unverified when multiple
 * exist for a role.
 *
 * Plain English. No em-dashes. No non-ASCII.
 *
 * Author: STEM Learning ops
 * Date: 17 May 2026
 */

class Stakeholder_contact_book_agent extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // ========================================================================
    // INITIALISATION - called once per lead when comm agent first acts on it
    // ========================================================================

    /**
     * initialise_book - seed stakeholder_contact_book from init_call. Idempotent.
     * Sets init_call.comm_book_initialised_at on success.
     */
    public function initialise_book($cid_id) {
        $lead = $this->db->get_where('init_call', array('id' => $cid_id))->row_array();
        if (empty($lead)) {
            log_message('error', "[m027 stakeholder] cid $cid_id not found in init_call");
            return false;
        }

        if (!empty($lead['comm_book_initialised_at'])) {
            return true; // already initialised
        }

        $school_name = $lead['school_name'];

        // 1) Primary DM from init_call
        if (!empty($lead['email_dm']) && !empty($lead['dm_name'])) {
            $this->upsert_contact($cid_id, 'primary_dm', array(
                'name'        => $lead['dm_name'],
                'email'       => $lead['email_dm'],
                'phone'       => isset($lead['phone_dm']) ? $lead['phone_dm'] : null,
                'designation' => isset($lead['dm_designation']) ? $lead['dm_designation'] : null,
                'source'      => 'init_call',
                'school_name' => $school_name,
            ));
        }

        // 2) Principal from init_call if present (column added by migration 027)
        if (!empty($lead['principal_email']) && !empty($lead['principal_name'])) {
            $this->upsert_contact($cid_id, 'principal', array(
                'name'        => $lead['principal_name'],
                'email'       => $lead['principal_email'],
                'designation' => 'Principal',
                'source'      => 'init_call',
                'school_name' => $school_name,
            ));
        }

        // 3) Harvest from mom_data attendees (recent)
        $this->harvest_from_mom($cid_id, $school_name);

        // 4) Harvest from linkedin_csr_check if migration 021 deployed
        if ($this->table_exists('linkedin_csr_check')) {
            $this->harvest_from_csr_check($cid_id, $school_name);
        }

        // Mark as initialised
        $this->db->where('id', $cid_id)->update('init_call', array(
            'comm_book_initialised_at' => date('Y-m-d H:i:s'),
        ));

        return true;
    }

    /**
     * upsert_contact - insert if (cid_id, role, email) is new; update name/
     * phone/designation if email matches; never overwrite verified_flag=1.
     */
    public function upsert_contact($cid_id, $role, $data) {
        $email = $this->normalise_email(isset($data['email']) ? $data['email'] : '');
        if (empty($email)) {
            log_message('debug', "[m027 stakeholder] empty email for $role cid=$cid_id");
            return false;
        }

        $existing = $this->db->get_where('stakeholder_contact_book', array(
            'cid_id' => $cid_id,
            'role'   => $role,
            'email'  => $email,
        ))->row_array();

        if (!empty($existing)) {
            // Update if not verified
            if ((int) $existing['verified_flag'] !== 1) {
                $update = array(
                    'name'        => $data['name'],
                    'phone'       => isset($data['phone']) ? $data['phone'] : $existing['phone'],
                    'designation' => isset($data['designation']) ? $data['designation'] : $existing['designation'],
                    'last_seen_at'=> date('Y-m-d H:i:s'),
                );
                $this->db->where('id', $existing['id'])->update('stakeholder_contact_book', $update);
            }
            return (int) $existing['id'];
        }

        // Insert new
        $row = array(
            'cid_id'        => $cid_id,
            'role'          => $role,
            'name'          => $data['name'],
            'email'         => $email,
            'phone'         => isset($data['phone']) ? $data['phone'] : null,
            'designation'   => isset($data['designation']) ? $data['designation'] : null,
            'source'        => isset($data['source']) ? $data['source'] : 'manual_entry',
            'school_name'   => isset($data['school_name']) ? $data['school_name'] : null,
            'verified_flag' => 0,
            'bounce_flag'   => 0,
            'bounce_soft_count' => 0,
            'active'        => 1,
            'created_at'    => date('Y-m-d H:i:s'),
            'last_seen_at'  => date('Y-m-d H:i:s'),
        );
        $this->db->insert('stakeholder_contact_book', $row);
        $id = $this->db->insert_id();
        log_message('info', "[m027 stakeholder] added $role for cid=$cid_id id=$id email=$email");
        return $id;
    }

    // ========================================================================
    // HARVESTERS
    // ========================================================================

    private function harvest_from_mom($cid_id, $school_name) {
        // Look at last 5 approved MoMs for this cid and harvest attendees
        $moms = $this->db->select('id, attendees_json')
            ->from('mom_data')
            ->where('cid_id', $cid_id)
            ->where('approved_status', '1')
            ->order_by('id', 'DESC')
            ->limit(5)
            ->get()->result_array();

        $count = 0;
        foreach ($moms as $mom) {
            $attendees = json_decode($mom['attendees_json'], true);
            if (!is_array($attendees)) continue;

            foreach ($attendees as $att) {
                if (empty($att['email'])) continue;
                $role = $this->infer_role_from_designation(
                    isset($att['designation']) ? $att['designation'] : ''
                );
                $this->upsert_contact($cid_id, $role, array(
                    'name'        => isset($att['name']) ? $att['name'] : 'Unknown',
                    'email'       => $att['email'],
                    'phone'       => isset($att['phone']) ? $att['phone'] : null,
                    'designation' => isset($att['designation']) ? $att['designation'] : null,
                    'source'      => 'mom_v2',
                    'school_name' => $school_name,
                ));
                $count++;
            }
        }
        return $count;
    }

    private function harvest_from_csr_check($cid_id, $school_name) {
        $rows = $this->db->select('*')
            ->from('linkedin_csr_check')
            ->where('cid_id', $cid_id)
            ->where('csr_verdict', 'csr')
            ->limit(10)
            ->get()->result_array();

        $count = 0;
        foreach ($rows as $r) {
            if (empty($r['dm_email'])) continue;
            $role = $this->infer_role_from_designation(isset($r['dm_designation']) ? $r['dm_designation'] : '');
            $this->upsert_contact($cid_id, $role, array(
                'name'        => isset($r['dm_name']) ? $r['dm_name'] : 'Unknown',
                'email'       => $r['dm_email'],
                'designation' => isset($r['dm_designation']) ? $r['dm_designation'] : null,
                'source'      => 'linkedin_csr',
                'school_name' => $school_name,
            ));
            $count++;
        }
        return $count;
    }

    // ========================================================================
    // ROLE INFERENCE
    // ========================================================================

    /**
     * infer_role_from_designation - map a free-text designation to one of the
     * 5 roles. Conservative: defaults to secondary_dm for ambiguous cases.
     */
    public function infer_role_from_designation($designation) {
        $d = strtolower(trim($designation));
        if (empty($d)) return 'secondary_dm';

        if (preg_match('/\b(principal|head\s*mistress|head\s*master|hm)\b/i', $d)) return 'principal';
        if (preg_match('/\b(cfo|bursar|finance|accounts\s*head|treasurer)\b/i', $d)) return 'cfo_bursar';
        if (preg_match('/\b(trustee|board|director|chairman|chairperson|owner|management)\b/i', $d)) return 'trustee';
        if (preg_match('/\b(coordinator|head\s*of|hod|academic\s*head|stem\s*coordinator)\b/i', $d)) return 'primary_dm';

        return 'secondary_dm';
    }

    // ========================================================================
    // BOUNCE TRACKING (called from migration 026 send pipe on failure)
    // ========================================================================

    public function record_bounce($email, $cid_id, $is_hard) {
        $email = $this->normalise_email($email);

        if ($is_hard) {
            $this->db->where('email', $email)
                ->where('cid_id', $cid_id)
                ->update('stakeholder_contact_book', array(
                    'bounce_flag' => 1,
                    'bounce_last_at' => date('Y-m-d H:i:s'),
                ));
            log_message('info', "[m027 stakeholder] hard bounce for $email cid=$cid_id, marked");
        } else {
            $this->db->where('email', $email)
                ->where('cid_id', $cid_id)
                ->set('bounce_soft_count', 'bounce_soft_count + 1', false)
                ->set('bounce_last_at', date('Y-m-d H:i:s'))
                ->update('stakeholder_contact_book');

            // Promote to hard bounce after 3 soft bounces
            $row = $this->db->get_where('stakeholder_contact_book', array(
                'email' => $email, 'cid_id' => $cid_id,
            ))->row();
            if (!empty($row) && (int) $row->bounce_soft_count >= 3) {
                $this->db->where('id', $row->id)->update('stakeholder_contact_book', array(
                    'bounce_flag' => 1,
                ));
            }
        }
    }

    // ========================================================================
    // VERIFICATION
    // ========================================================================

    public function verify_contact($contact_id, $verified_by_uid) {
        $this->db->where('id', $contact_id)->update('stakeholder_contact_book', array(
            'verified_flag'   => 1,
            'verified_by_uid' => $verified_by_uid,
            'verified_at'     => date('Y-m-d H:i:s'),
        ));
        return true;
    }

    public function deactivate_contact($contact_id, $reason) {
        $this->db->where('id', $contact_id)->update('stakeholder_contact_book', array(
            'active' => 0,
            'deactivated_reason' => $reason,
            'deactivated_at' => date('Y-m-d H:i:s'),
        ));
        return true;
    }

    // ========================================================================
    // QUERIES
    // ========================================================================

    public function list_for_lead($cid_id, $active_only = true) {
        $this->db->select('*')->from('stakeholder_contact_book')
            ->where('cid_id', $cid_id);
        if ($active_only) $this->db->where('active', 1);
        $this->db->order_by('role', 'ASC')->order_by('verified_flag', 'DESC')
            ->order_by('id', 'ASC');
        return $this->db->get()->result_array();
    }

    public function preferred_contact_for_role($cid_id, $role) {
        $row = $this->db->select('*')->from('stakeholder_contact_book')
            ->where('cid_id', $cid_id)
            ->where('role', $role)
            ->where('active', 1)
            ->where('bounce_flag', 0)
            ->order_by('verified_flag', 'DESC')
            ->order_by('last_seen_at', 'DESC')
            ->limit(1)
            ->get()->row_array();
        return $row;
    }

    // ========================================================================
    // HELPERS
    // ========================================================================

    private function normalise_email($email) {
        return strtolower(trim($email));
    }

    private function table_exists($table) {
        return $this->db->table_exists($table);
    }
}
