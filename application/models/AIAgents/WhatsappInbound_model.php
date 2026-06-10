<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * WhatsappInbound_model - Feature (additive, 2026-06-06)
 *
 * Stores inbound WhatsApp messages into whatsapp_inbound_v2 and attempts to
 * match the sender phone to a real lead via company_contact_master.phoneno.
 * (init_call.dm_contact_phone is empty in this DB, so contact phones are the
 * only genuine matching source.)
 *
 * Phone matching: normalize to trailing 10 digits (India local), then compare
 * against a digit-normalized company_contact_master.phoneno. On a hit we map
 * company_id -> the most recent init_call lead for that company and its mainbd.
 *
 * Does NOT touch the existing Whatsapp.php controller / its tables.
 * Standing rules: ASCII only, "Rs" for rupees, "percent" spelled out.
 */
class WhatsappInbound_model extends CI_Model {

    public function manifest() {
        $total = (int)$this->db->query("SELECT COUNT(*) c FROM whatsapp_inbound_v2")->row()->c;
        $matched = (int)$this->db->query("SELECT COUNT(*) c FROM whatsapp_inbound_v2 WHERE matched_lead_id IS NOT NULL AND matched_lead_id > 0")->row()->c;
        $contacts = (int)$this->db->query("SELECT COUNT(*) c FROM company_contact_master WHERE phoneno IS NOT NULL AND phoneno <> ''")->row()->c;
        return array(
            'feature'           => 'whatsapp_inbound',
            'sink_table'        => 'whatsapp_inbound_v2',
            'match_source'      => 'company_contact_master.phoneno',
            'matchable_contacts'=> $contacts,
            'stored_total'      => $total,
            'stored_matched'    => $matched,
            'deployed_at'       => '2026-06-06',
        );
    }

    /** Reduce a phone string to its trailing 10 digits for matching. */
    private function norm10($phone) {
        $d = preg_replace('/\D+/', '', (string)$phone);
        if (strlen($d) > 10) $d = substr($d, -10);
        return $d;
    }

    /**
     * Try to match a sender phone to a lead.
     * Returns array(lead_id, bd_uid, confidence, company) or nulls if no match.
     */
    public function match_phone($from_phone) {
        $n = $this->norm10($from_phone);
        if (strlen($n) < 10) {
            return array('lead_id' => null, 'bd_uid' => null, 'confidence' => 0.0, 'company' => null);
        }
        // Find a contact whose phoneno digits end with the same 10 digits.
        $sql = "
            SELECT ccm.company_id
            FROM company_contact_master ccm
            WHERE ccm.phoneno IS NOT NULL AND ccm.phoneno <> ''
              AND RIGHT(REGEXP_REPLACE(ccm.phoneno, '[^0-9]', ''), 10) = ?
            LIMIT 1";
        $row = $this->db->query($sql, array($n))->row();
        if (!$row) {
            return array('lead_id' => null, 'bd_uid' => null, 'confidence' => 0.0, 'company' => null);
        }
        $company_id = (int)$row->company_id;
        // Most recent lead for that company.
        $lead = $this->db->query("
            SELECT ic.id AS lead_id, ic.mainbd,
                   COALESCE(NULLIF(cm.compname,''), CONCAT('Company #', ?)) AS company
            FROM init_call ic
            LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
            WHERE ic.cmpid_id = ?
            ORDER BY ic.id DESC LIMIT 1", array($company_id, $company_id))->row();
        if (!$lead) {
            // Company exists but no lead row; still return company.
            $cm = $this->db->query("SELECT compname FROM company_master WHERE id = ? LIMIT 1", array($company_id))->row();
            return array(
                'lead_id'    => null,
                'bd_uid'     => null,
                'confidence' => 0.50,
                'company'    => $cm ? $cm->compname : ('Company #' . $company_id),
            );
        }
        return array(
            'lead_id'    => (int)$lead->lead_id,
            'bd_uid'     => $lead->mainbd !== null ? (int)$lead->mainbd : null,
            'confidence' => 0.90,
            'company'    => $lead->company,
        );
    }

    /**
     * Store an inbound message and attach any match. Returns inserted row id + match.
     */
    public function store($payload) {
        $from_phone = isset($payload['from_phone']) ? (string)$payload['from_phone'] : '';
        $match = $this->match_phone($from_phone);

        $data = array(
            'from_phone'          => $from_phone,
            'from_name'           => isset($payload['from_name']) ? (string)$payload['from_name'] : null,
            'to_phone_number_id'  => isset($payload['to_phone_number_id']) ? (string)$payload['to_phone_number_id'] : null,
            'message_body'        => isset($payload['message_body']) ? (string)$payload['message_body'] : null,
            'media_url'           => isset($payload['media_url']) ? (string)$payload['media_url'] : null,
            'media_type'          => isset($payload['media_type']) ? (string)$payload['media_type'] : 'text',
            'provider_message_id' => isset($payload['provider_message_id']) ? (string)$payload['provider_message_id'] : null,
            'received_at'         => date('Y-m-d H:i:s'),
            'matched_lead_id'     => $match['lead_id'],
            'matched_bd_uid'      => $match['bd_uid'],
            'match_confidence'    => $match['confidence'],
            'status'              => ($match['bd_uid'] ? 'assigned' : 'new'),
            'assigned_to_uid'     => $match['bd_uid'],
        );
        // Guard media_type against unknown enum values.
        $allowed = array('text','image','document','audio','video','button','interactive');
        if (!in_array($data['media_type'], $allowed, true)) $data['media_type'] = 'text';

        $this->db->insert('whatsapp_inbound_v2', $data);
        $id = (int)$this->db->insert_id();
        return array('id' => $id, 'match' => $match);
    }

    /** Recent inbound rows (real data). */
    public function recent($limit = 25) {
        $limit = (int)$limit;
        if ($limit <= 0 || $limit > 100) $limit = 25;
        return $this->db->query("
            SELECT id, from_phone, from_name, message_body, received_at,
                   matched_lead_id, matched_bd_uid, match_confidence, status
            FROM whatsapp_inbound_v2
            ORDER BY id DESC LIMIT ?", array($limit))->result_array();
    }
}
