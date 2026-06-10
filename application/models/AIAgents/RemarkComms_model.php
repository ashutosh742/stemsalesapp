<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * RemarkComms_model - Agent (additive, 2026-06-06)
 *
 * Detects when a sales remark IMPLIES a follow-up communication (send an email
 * or a WhatsApp message), classifies the suggested channel, and checks whether
 * a matching communication was actually sent for that lead.
 *
 * Real sources:
 *   - todays_remark (status remark catalog), init_call remark fields, special_remarks
 *   - Email sent evidence: comm_outbox (cid_id) + crm_email_logs
 *   - WhatsApp sent evidence: whatsapp_send_v2 (to_lead_id)
 *
 * Intent detection is a transparent rule set on the remark text (no opaque
 * scoring). No mock data. ASCII only, "Rs" for rupees, "percent" spelled out.
 */
class RemarkComms_model extends CI_Model {

    // Intent keyword rules -> (intent, suggested_channel)
    private function _rules() {
        return array(
            array('kw' => array('revised proposal','send proposal','submit proposal','share proposal','proposal to be sent','send revised'), 'intent' => 'send_proposal', 'channel' => 'email'),
            array('kw' => array('calendar sent','send calendar','meeting calendar','send invite','calendar invite','meeting invite'), 'intent' => 'send_calendar_invite', 'channel' => 'email'),
            array('kw' => array('presentation','demo','ppt'), 'intent' => 'share_presentation', 'channel' => 'email'),
            array('kw' => array('vendor reg','vendor registration','registration form','reg form','documents','brochure','catalogue','quotation','quote'), 'intent' => 'share_document', 'channel' => 'email'),
            array('kw' => array('whatsapp','wa msg','message on whatsapp','send message','text the client','ping on whatsapp'), 'intent' => 'send_whatsapp', 'channel' => 'whatsapp'),
            array('kw' => array('follow up','followup','call again','reconnect','revert','will get back','call back','reminder'), 'intent' => 'follow_up', 'channel' => 'whatsapp'),
            array('kw' => array('send mail','send email','email the client','share over mail','mail the','drop a mail'), 'intent' => 'send_email', 'channel' => 'email'),
        );
    }

    public function manifest() {
        $tr = (int)$this->db->query("SELECT COUNT(*) c FROM todays_remark")->row()->c;
        $em = (int)$this->db->query("SELECT COUNT(*) c FROM crm_email_logs")->row()->c;
        $ob = (int)$this->db->query("SELECT COUNT(*) c FROM comm_outbox")->row()->c;
        $wa = (int)$this->db->query("SELECT COUNT(*) c FROM whatsapp_send_v2")->row()->c;
        return array(
            'feature'       => 'remark_comms',
            'source_tables' => array('todays_remark','init_call','comm_outbox','crm_email_logs','whatsapp_send_v2'),
            'remark_catalog'=> $tr,
            'email_logs'    => $em,
            'comm_outbox'   => $ob,
            'whatsapp_send' => $wa,
            'deployed_at'   => '2026-06-06',
        );
    }

    /** Classify a single remark string. Returns intent + channel or no_action. */
    public function classify($text) {
        $t = strtolower(trim((string)$text));
        if ($t === '') return array('has_intent'=>false, 'intent'=>'none', 'channel'=>null, 'matched'=>null);
        foreach ($this->_rules() as $rule) {
            foreach ($rule['kw'] as $kw) {
                if (strpos($t, $kw) !== false) {
                    return array('has_intent'=>true, 'intent'=>$rule['intent'], 'channel'=>$rule['channel'], 'matched'=>$kw);
                }
            }
        }
        return array('has_intent'=>false, 'intent'=>'none', 'channel'=>null, 'matched'=>null);
    }

    /**
     * Scan the remark catalog (todays_remark) and flag which imply a send.
     * This is the "what kind of email/WhatsApp should be sent as per remark" view.
     */
    public function scan_catalog($limit = 200) {
        $limit = (int)$limit; if ($limit <= 0 || $limit > 1000) $limit = 200;
        $rows = $this->db->query("SELECT id, name, status_id FROM todays_remark ORDER BY id ASC LIMIT " . $limit)->result_array();
        $out = array(); $intent_count = 0; $email_c = 0; $wa_c = 0;
        foreach ($rows as $r) {
            $c = $this->classify($r['name']);
            if ($c['has_intent']) {
                $intent_count++;
                if ($c['channel'] === 'email') $email_c++; else if ($c['channel'] === 'whatsapp') $wa_c++;
                $out[] = array(
                    'remark_id'  => (int)$r['id'],
                    'remark'     => $r['name'],
                    'status_id'  => (int)$r['status_id'],
                    'intent'     => $c['intent'],
                    'channel'    => $c['channel'],
                    'matched_on' => $c['matched'],
                );
            }
        }
        return array(
            'scanned'        => count($rows),
            'with_intent'    => $intent_count,
            'suggest_email'  => $email_c,
            'suggest_whatsapp'=> $wa_c,
            'items'          => $out,
        );
    }

    /**
     * For one lead: gather its remarks (init_call fields + matching todays_remark
     * via status), detect intent, and check whether a matching comm was SENT.
     */
    public function for_lead($init_id) {
        $init_id = (int)$init_id;
        $lead = $this->db->query("
            SELECT ic.id, ic.cstatus, ic.kcremark, ic.pkcremark, ic.pnpremark, ic.reject_remarks,
                   COALESCE(NULLIF(cm.compname,''), CONCAT('Lead #', ic.id)) AS company
            FROM init_call ic LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
            WHERE ic.id = ? LIMIT 1", array($init_id))->row_array();
        if (!$lead) return null;

        $remarks = array();
        foreach (array('kcremark','pkcremark','pnpremark','reject_remarks') as $f) {
            if (!empty($lead[$f])) $remarks[] = array('source'=>$f, 'text'=>$lead[$f]);
        }
        // catalog remark for current status
        $tr = $this->db->query("SELECT name FROM todays_remark WHERE status_id = ? LIMIT 5", array((int)$lead['cstatus']))->result_array();
        foreach ($tr as $row) $remarks[] = array('source'=>'status_remark', 'text'=>$row['name']);

        $detected = array();
        foreach ($remarks as $rm) {
            $c = $this->classify($rm['text']);
            if ($c['has_intent']) {
                $detected[] = array('source'=>$rm['source'], 'remark'=>$rm['text'],
                                    'intent'=>$c['intent'], 'channel'=>$c['channel']);
            }
        }

        // sent evidence
        $email_sent = (int)$this->db->query("SELECT COUNT(*) c FROM comm_outbox WHERE cid_id = ?", array($init_id))->row()->c;
        $wa_sent    = (int)$this->db->query("SELECT COUNT(*) c FROM whatsapp_send_v2 WHERE to_lead_id = ?", array($init_id))->row()->c;

        $needs_email = false; $needs_wa = false;
        foreach ($detected as $d) {
            if ($d['channel'] === 'email') $needs_email = true;
            if ($d['channel'] === 'whatsapp') $needs_wa = true;
        }

        return array(
            'lead_id'       => $init_id,
            'company'       => $lead['company'],
            'detected'      => $detected,
            'email' => array(
                'implied_by_remark' => $needs_email,
                'sent_count'        => $email_sent,
                'status'            => $needs_email ? ($email_sent > 0 ? 'sent_as_per_remark' : 'pending_send') : 'not_required',
            ),
            'whatsapp' => array(
                'implied_by_remark' => $needs_wa,
                'sent_count'        => $wa_sent,
                'status'            => $needs_wa ? ($wa_sent > 0 ? 'sent_as_per_remark' : 'pending_send') : 'not_required',
                'note'              => 'WhatsApp send requires STEM_WHATSAPP_TOKEN (config step).',
            ),
        );
    }

    /**
     * Queue a remark-driven email into comm_outbox (delegates to the existing
     * outbox drainer). Returns the queue id. WhatsApp queue is gated on token.
     */
    public function queue_email($from_uid, $cid_id, $to_email, $subject, $body) {
        $data = array(
            'from_uid'  => (int)$from_uid,
            'to_email'  => (string)$to_email,
            'subject'   => (string)$subject,
            'body_text' => (string)$body,
            'cid_id'    => (int)$cid_id ?: null,
            'status'    => 'queued',
            'queued_at' => date('Y-m-d H:i:s'),
        );
        $this->db->insert('comm_outbox', $data);
        return (int)$this->db->insert_id();
    }
}
