<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration 040: WhatsApp send agent
 *
 * Outbound: BD composes from template + variables, agent renders body, calls Meta
 * Cloud API (or configured BSP), logs to whatsapp_send_v2. Status updates come via
 * webhook to /api/whatsapp/webhook_receive (controller).
 *
 * Inbound: webhook drops rows into whatsapp_inbound_v2 and updates whatsapp_optin_v2
 * last_user_message_at so the 24h session window opens for free-form replies.
 *
 * Production untouched. Never writes to tblcallevents directly; an Accept-as-event flow
 * exists in the controller that mints a tblcallevents row via the standard submit path.
 */
class Whatsapp_agent extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /* =====================================================================
     * PUBLIC: list active approved templates
     * ===================================================================== */
    public function list_templates() {
        return $this->db->where('is_active', 1)
                        ->where('meta_approval_status', 'approved')
                        ->order_by('display_name', 'ASC')
                        ->get('whatsapp_template_v2')->result_array();
    }

    public function list_all_templates() {
        // Includes pending templates so the demo composer can render them with a "pending Meta approval" badge
        return $this->db->where('is_active', 1)
                        ->order_by('meta_approval_status', 'ASC')
                        ->order_by('display_name', 'ASC')
                        ->get('whatsapp_template_v2')->result_array();
    }

    /* =====================================================================
     * PUBLIC: queue a send. Returns send_id or array(error=>...)
     * ===================================================================== */
    public function queue_send($args) {
        $from_uid    = isset($args['from_uid']) ? (int)$args['from_uid'] : 0;
        $to_phone    = isset($args['to_phone']) ? $this->_normalize_phone($args['to_phone']) : null;
        $template_id = isset($args['template_id']) ? (int)$args['template_id'] : 0;
        $variables   = isset($args['variables']) ? $args['variables'] : array();
        $to_lead_id  = isset($args['to_lead_id']) ? (int)$args['to_lead_id'] : null;
        $to_name     = isset($args['to_name']) ? $args['to_name'] : null;

        if (!$from_uid || !$to_phone || !$template_id) {
            return array('error' => 'missing_required_args');
        }

        // Pilot gate
        $cfg = $this->_get_config();
        if ($cfg['pilot_mode'] == '1') {
            $pilot = array_map('intval', explode(',', $cfg['pilot_uids_csv']));
            if (!in_array($from_uid, $pilot)) {
                return array('error' => 'not_in_pilot');
            }
        }

        $tpl = $this->db->get_where('whatsapp_template_v2',
            array('id' => $template_id, 'is_active' => 1))->row();
        if (!$tpl) return array('error' => 'template_not_found');

        // Render body
        $rendered = $this->_render_template($tpl->body_template, $variables);

        // Opt-in check
        $optin = $this->db->get_where('whatsapp_optin_v2', array('phone' => $to_phone))->row();
        if (!$optin || $optin->opt_out_at !== null) {
            // Templates can still be sent if category is UTILITY and consent is BD attestation
            if ($tpl->category === 'MARKETING') {
                return array('error' => 'no_optin_for_marketing');
            }
        }

        // Rate limits
        if (!$this->_within_rate_limits($from_uid, $to_phone, $cfg)) {
            return array('error' => 'rate_limit_exceeded');
        }

        // Insert queued row
        $this->db->insert('whatsapp_send_v2', array(
            'to_phone'               => $to_phone,
            'to_name'                => $to_name,
            'to_lead_id'             => $to_lead_id,
            'from_uid'               => $from_uid,
            'template_id'            => $template_id,
            'template_name'          => $tpl->template_name,
            'template_language'      => $tpl->language,
            'template_variables_json'=> json_encode($variables),
            'message_body'           => $rendered,
            'provider'               => $cfg['provider_active'],
            'status'                 => 'queued',
        ));
        $send_id = $this->db->insert_id();

        // Dispatch to provider
        $dispatch = $this->_dispatch($send_id, $tpl, $variables, $to_phone, $cfg);
        if (!empty($dispatch['error'])) {
            $this->db->where('id', $send_id)->update('whatsapp_send_v2', array(
                'status'        => 'failed',
                'error_code'    => $dispatch['error'],
                'error_message' => isset($dispatch['message']) ? $dispatch['message'] : null,
                'failed_at'     => date('Y-m-d H:i:s'),
            ));
            return array('error' => $dispatch['error'], 'send_id' => $send_id);
        }

        $this->db->where('id', $send_id)->update('whatsapp_send_v2', array(
            'status'             => 'sent',
            'provider_message_id'=> isset($dispatch['provider_message_id']) ? $dispatch['provider_message_id'] : null,
            'sent_at'            => date('Y-m-d H:i:s'),
        ));

        return array('send_id' => $send_id, 'provider_message_id' => isset($dispatch['provider_message_id']) ? $dispatch['provider_message_id'] : null);
    }

    /* =====================================================================
     * PUBLIC: recent sends for a BD (for activity timeline)
     * ===================================================================== */
    public function recent_for_bd($bd_uid, $limit = 50) {
        return $this->db->where('from_uid', $bd_uid)
                        ->order_by('queued_at', 'DESC')
                        ->limit($limit)
                        ->get('whatsapp_send_v2')->result_array();
    }

    /* =====================================================================
     * PUBLIC: webhook receive - status update or inbound message
     * ===================================================================== */
    public function handle_webhook_status_update($provider_message_id, $status, $timestamp = null) {
        $row = $this->db->get_where('whatsapp_send_v2',
            array('provider_message_id' => $provider_message_id))->row();
        if (!$row) return false;

        $field_map = array(
            'sent'      => 'sent_at',
            'delivered' => 'delivered_at',
            'read'      => 'read_at',
            'failed'    => 'failed_at',
        );
        if (!isset($field_map[$status])) return false;

        $update = array(
            'status' => $status,
            $field_map[$status] => $timestamp ? $timestamp : date('Y-m-d H:i:s'),
        );
        $this->db->where('id', $row->id)->update('whatsapp_send_v2', $update);
        return true;
    }

    public function handle_webhook_inbound($payload) {
        $from_phone = $this->_normalize_phone($payload['from_phone']);
        $provider_message_id = $payload['provider_message_id'];

        // Idempotency
        $exists = $this->db->get_where('whatsapp_inbound_v2',
            array('provider_message_id' => $provider_message_id))->row();
        if ($exists) return $exists->id;

        // Try to match to a lead via opt-in or init_call.dm_phone
        $match = $this->_match_inbound_phone($from_phone);

        $this->db->insert('whatsapp_inbound_v2', array(
            'from_phone'         => $from_phone,
            'from_name'          => isset($payload['from_name']) ? $payload['from_name'] : null,
            'to_phone_number_id' => isset($payload['to_phone_number_id']) ? $payload['to_phone_number_id'] : null,
            'message_body'       => isset($payload['message_body']) ? $payload['message_body'] : null,
            'media_url'          => isset($payload['media_url']) ? $payload['media_url'] : null,
            'media_type'         => isset($payload['media_type']) ? $payload['media_type'] : 'text',
            'provider_message_id'=> $provider_message_id,
            'received_at'        => isset($payload['received_at']) ? $payload['received_at'] : date('Y-m-d H:i:s'),
            'matched_lead_id'    => $match['lead_id'],
            'matched_bd_uid'     => $match['bd_uid'],
            'match_confidence'   => $match['confidence'],
        ));
        $id = $this->db->insert_id();

        // Update last_user_message_at on opt-in row (opens 24h session window)
        $this->db->set('last_user_message_at', date('Y-m-d H:i:s'))
                 ->where('phone', $from_phone)
                 ->update('whatsapp_optin_v2');

        return $id;
    }

    /* =====================================================================
     * PRIVATE
     * ===================================================================== */
    private function _render_template($body_template, $variables) {
        $rendered = $body_template;
        if (!is_array($variables)) $variables = array();
        $i = 1;
        foreach ($variables as $val) {
            $rendered = str_replace('{{' . $i . '}}', (string)$val, $rendered);
            $i++;
        }
        return $rendered;
    }

    private function _normalize_phone($p) {
        $p = preg_replace('/\D+/', '', $p);
        if (strlen($p) === 10) $p = '91' . $p; // India default
        return $p;
    }

    private function _within_rate_limits($uid, $phone, $cfg) {
        $per_uid_hr = (int)$cfg['rate_limit_per_uid_per_hour'];
        $per_phone_day = (int)$cfg['rate_limit_per_phone_per_day'];

        $hr = $this->db->where('from_uid', $uid)
                       ->where('queued_at >=', date('Y-m-d H:i:s', strtotime('-1 hour')))
                       ->count_all_results('whatsapp_send_v2');
        if ($hr >= $per_uid_hr) return false;

        $day = $this->db->where('to_phone', $phone)
                        ->where('queued_at >=', date('Y-m-d H:i:s', strtotime('-1 day')))
                        ->count_all_results('whatsapp_send_v2');
        if ($day >= $per_phone_day) return false;
        return true;
    }

    private function _match_inbound_phone($phone) {
        // Opt-in linked lead first
        $opt = $this->db->get_where('whatsapp_optin_v2', array('phone' => $phone))->row();
        if ($opt && $opt->linked_lead_id) {
            return array(
                'lead_id' => (int)$opt->linked_lead_id,
                'bd_uid'  => $opt->linked_bd_uid ? (int)$opt->linked_bd_uid : null,
                'confidence' => 0.950,
            );
        }
        // init_call.dm_phone match
        $clean = preg_replace('/\D+/', '', $phone);
        $last10 = substr($clean, -10);
        $hit = $this->db->select('cid_id, mainbd')
                        ->like('dm_phone', $last10, 'before')
                        ->order_by('createDate', 'DESC')
                        ->limit(1)
                        ->get('init_call')->row();
        if ($hit) {
            return array(
                'lead_id'    => (int)$hit->cid_id,
                'bd_uid'     => (int)$hit->mainbd,
                'confidence' => 0.800,
            );
        }
        return array('lead_id' => null, 'bd_uid' => null, 'confidence' => 0.000);
    }

    private function _dispatch($send_id, $tpl, $variables, $to_phone, $cfg) {
        // Production: call Meta Graph API. Demo/staging: stub a provider_message_id.
        $provider = $cfg['provider_active'];

        if ($provider === 'meta_cloud') {
            $phone_id   = $this->_resolve_secret($cfg['meta_phone_number_id_ref']);
            $token      = $this->_resolve_secret($cfg['meta_access_token_ref']);
            if (!$phone_id || !$token) {
                return array('provider_message_id' => 'stub-' . uniqid(), 'note' => 'meta_secrets_missing_using_stub');
            }
            $url = "https://graph.facebook.com/v18.0/{$phone_id}/messages";
            $body = array(
                'messaging_product' => 'whatsapp',
                'to' => $to_phone,
                'type' => 'template',
                'template' => array(
                    'name' => $tpl->template_name,
                    'language' => array('code' => $tpl->language),
                    'components' => array(
                        array(
                            'type' => 'body',
                            'parameters' => array_map(function($v){
                                return array('type'=>'text','text'=>(string)$v);
                            }, $variables),
                        )
                    ),
                ),
            );
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Authorization: Bearer ' . $token,
                'Content-Type: application/json',
            ));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            $resp = curl_exec($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if ($code < 200 || $code >= 300) {
                return array('error' => 'meta_api_failed', 'message' => substr($resp, 0, 500));
            }
            $j = json_decode($resp, true);
            $msg_id = isset($j['messages'][0]['id']) ? $j['messages'][0]['id'] : null;
            return array('provider_message_id' => $msg_id);
        }

        // Other providers can be added later. Until then, stub.
        return array('provider_message_id' => 'stub-' . uniqid());
    }

    private function _resolve_secret($ref) {
        // Pattern: env:NAME means read from environment. Production secret store would
        // implement more sources (AWS SM, GCP SM). Never store raw tokens in DB.
        if (strpos($ref, 'env:') === 0) {
            $name = substr($ref, 4);
            return getenv($name) ? getenv($name) : null;
        }
        return null;
    }

    private function _get_config() {
        $rows = $this->db->get('whatsapp_config_v2')->result_array();
        $out = array();
        foreach ($rows as $r) $out[$r['config_key']] = $r['config_value'];
        return $out;
    }
}
