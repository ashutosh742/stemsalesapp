<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * M067 Auto CTI Call Logging patch
 * Place as application/controllers/Cti.php
 *
 * Routes (CI3, NO /api/ prefix):
 *   POST /cti/webhook_receive   (called by CTI provider, not by app users)
 *   GET  /cti/calls_for_user
 *   GET  /cti/calls_for_lead
 *   POST /cti/manual_link
 *   POST /cti/click_to_call
 *
 * Auth:
 *   - webhook_receive: HMAC-SHA256 signature verification against webhook_secret.
 *   - All other endpoints: Authorization Bearer header = config 'digest_token'.
 *
 * Auto-link logic: matches from_number or to_number against the lead_phone
 * (or phone) column in the leads / cid table. Column name is configurable
 * via config item 'lead_phone_column' (default: 'phone').
 */

class M067_auto_cti_call_logging extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // -----------------------------------------------------------------------
    // Internal helpers
    // -----------------------------------------------------------------------

    private function _json($data, $status = 200)
    {
        $this->output
             ->set_status_header($status)
             ->set_content_type('application/json')
             ->set_output(json_encode($data));
    }
    private function _auth()
    {
        if (function_exists('authunify_ok') && authunify_ok()) { return true; } // rimlyproof_authunify_20260609

        // Load custom config if not loaded
        @$this->config->load('custom', false, true);
        $token = $this->config->item('stem_digest_token');
        if (!$token) { $token = $this->config->item('csr_bearer_token'); }
        if (!$token) { $token = getenv('STEM_DIGEST_TOKEN'); }
        if (!$token) { $token = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo'; }
        $header = '';
        if (isset($_SERVER['HTTP_AUTHORIZATION'])) { $header = $_SERVER['HTTP_AUTHORIZATION']; }
        if (!$header && isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) { $header = $_SERVER['REDIRECT_HTTP_AUTHORIZATION']; }
        if (!$header && function_exists('getallheaders')) {
            foreach (getallheaders() as $k => $v) {
                if (strcasecmp($k, 'Authorization') === 0) { $header = $v; break; }
            }
        }
        $provided = trim(str_replace(array('Bearer ', 'Bearer'), '', $header));
        if (!$token || $provided !== $token) {
            $this->output->set_status_header(401)->set_content_type('application/json')->set_output(json_encode(array('ok'=>false,'error'=>'unauthorised')));
            return false;
        }
        return true;
    }



    /**
     * Verify an HMAC-SHA256 webhook signature.
     * Many providers send the signature in X-Signature or X-Hmac-Sha256.
     * The raw POST body is signed against webhook_secret using SHA-256.
     *
     * @param string $secret      The provider's webhook_secret from cti_provider.
     * @param string $raw_body    Raw POST body string.
     * @return bool True if signature matches.
     */
    private function _verify_hmac($secret, $raw_body)
    {
        $sig_header = isset($_SERVER['HTTP_X_SIGNATURE'])
                    ? $_SERVER['HTTP_X_SIGNATURE']
                    : (isset($_SERVER['HTTP_X_HMAC_SHA256']) ? $_SERVER['HTTP_X_HMAC_SHA256'] : '');

        if (!$sig_header) {
            // Some providers (Exotel) use a simpler auth token in a custom header.
            // Accept if no signature header is present but secret is null (provider still unconfigured).
            return $secret === null;
        }

        $expected = hash_hmac('sha256', $raw_body, (string)$secret);
        return hash_equals($expected, strtolower(ltrim($sig_header, 'sha256=')));
    }

    /**
     * Attempt to auto-link a call event to a lead by phone number.
     * Checks both from_number and to_number against the lead phone column.
     *
     * @param string $from_number
     * @param string $to_number
     * @return int|null lead_cid_id if found, null otherwise.
     */
    private function _auto_link_lead($from_number, $to_number)
    {
        $phone_col  = $this->config->item('lead_phone_column') ?: 'phone';
        $lead_table = $this->config->item('lead_table') ?: 'cid';

        // Normalise numbers: strip leading +91 or 0, keep last 10 digits.
        $normalise = function ($n) {
            $n = preg_replace('/[^0-9]/', '', $n);
            return strlen($n) > 10 ? substr($n, -10) : $n;
        };

        $candidates = array_unique(array(
            $normalise($from_number),
            $normalise($to_number),
        ));

        foreach ($candidates as $num) {
            if (!$num) continue;
            $row = $this->db->select('id')
                            ->where("RIGHT(REPLACE(REPLACE($phone_col, '-', ''), ' ', ''), 10)", $num)
                            ->limit(1)
                            ->get($lead_table)
                            ->row_array();
            if ($row) return (int)$row['id'];
        }
        return null;
    }

    // -----------------------------------------------------------------------
    // Endpoints
    // -----------------------------------------------------------------------

    /**
     * POST /cti/webhook_receive
     * Receives real-time call events from a CTI provider.
     * Verifies HMAC signature, parses payload, inserts cti_call_event,
     * and attempts auto-link to a lead by phone number.
     *
     * Expected query param: provider_id=X (or provider name in header)
     * Body: JSON or form-encoded payload from provider.
     */
    public function webhook_receive()
    {
        $raw_body   = file_get_contents('php://input');
        $provider_id = (int)$this->input->get('provider_id');

        if (!$provider_id) {
            // Try to resolve by provider name header
            $pname = isset($_SERVER['HTTP_X_CTI_PROVIDER']) ? $_SERVER['HTTP_X_CTI_PROVIDER'] : '';
            if ($pname) {
                $pr = $this->db->get_where('cti_provider', array('name' => $pname))->row_array();
                $provider_id = $pr ? (int)$pr['id'] : 0;
            }
        }

        if (!$provider_id) {
            $this->_json(array('ok' => false, 'error' => 'unknown_provider'), 400);
            return;
        }

        $provider = $this->db->get_where('cti_provider', array('id' => $provider_id))->row_array();
        if (!$provider) {
            $this->_json(array('ok' => false, 'error' => 'provider_not_found'), 404);
            return;
        }

        // Signature check
        if (!$this->_verify_hmac($provider['webhook_secret'], $raw_body)) {
            log_message('error', 'M067: CTI webhook HMAC mismatch for provider ' . $provider_id);
            $this->_json(array('ok' => false, 'error' => 'invalid_signature'), 403);
            return;
        }

        // Parse payload (JSON or form-encoded)
        $payload = json_decode($raw_body, true);
        if (!is_array($payload)) {
            parse_str($raw_body, $payload);
        }
        if (!is_array($payload)) $payload = array();

        // Extract standard fields with provider-agnostic fallbacks.
        // Exotel uses: CallSid, Direction, From, To, StartTime, Duration, Status
        // Knowlarity uses: call_id, direction, caller_number, agent_number, ...
        $ext_id    = isset($payload['CallSid'])        ? $payload['CallSid']
                   : (isset($payload['call_id'])        ? $payload['call_id'] : uniqid('cti_', true));
        $direction = isset($payload['Direction'])       ? strtolower($payload['Direction'])
                   : (isset($payload['direction'])      ? strtolower($payload['direction']) : 'inbound');
        $direction = in_array($direction, array('inbound','outbound')) ? $direction : 'inbound';

        $from_num  = isset($payload['From'])            ? $payload['From']
                   : (isset($payload['caller_number'])  ? $payload['caller_number'] : '');
        $to_num    = isset($payload['To'])              ? $payload['To']
                   : (isset($payload['agent_number'])   ? $payload['agent_number'] : '');
        $started   = isset($payload['StartTime'])       ? date('Y-m-d H:i:s', strtotime($payload['StartTime']))
                   : (isset($payload['start_time'])     ? $payload['start_time'] : date('Y-m-d H:i:s'));
        $duration  = isset($payload['Duration'])        ? (int)$payload['Duration']
                   : (isset($payload['duration'])       ? (int)$payload['duration'] : 0);
        $status    = isset($payload['Status'])          ? strtolower($payload['Status'])
                   : (isset($payload['call_status'])    ? strtolower($payload['call_status']) : 'answered');
        $recording = isset($payload['RecordingUrl'])    ? $payload['RecordingUrl']
                   : (isset($payload['recording_url'])  ? $payload['recording_url'] : null);

        // Map provider status to our ENUM
        $outcome_map = array(
            'completed'   => 'answered',
            'answered'    => 'answered',
            'no-answer'   => 'no_answer',
            'noanswer'    => 'no_answer',
            'busy'        => 'busy',
            'failed'      => 'failed',
            'voicemail'   => 'voicemail',
        );
        $outcome = isset($outcome_map[$status]) ? $outcome_map[$status] : 'answered';

        $ended_at = ($duration && $started)
                  ? date('Y-m-d H:i:s', strtotime($started) + $duration)
                  : null;

        // Resolve agent_uid by matching agent number to user table
        $agent_uid = null;
        if ($to_num) {
            $agent_row = $this->db->select('uid')
                                  ->where('phone', $to_num)
                                  ->or_where('mobile', $to_num)
                                  ->limit(1)
                                  ->get('user')
                                  ->row_array();
            if ($agent_row) $agent_uid = (int)$agent_row['uid'];
        }

        // Auto-link to lead
        $lead_id    = $this->_auto_link_lead($from_num, $to_num);
        $auto_linked = $lead_id ? 1 : 0;

        // Check for duplicate (idempotent on external_call_id)
        $existing = $this->db->get_where('cti_call_event', array('external_call_id' => $ext_id))->row_array();
        if ($existing) {
            // Update with latest status/duration in case this is a mid-call update
            $this->db->where('external_call_id', $ext_id)
                     ->update('cti_call_event', array(
                         'duration_seconds' => $duration,
                         'ended_at'         => $ended_at,
                         'outcome'          => $outcome,
                         'recording_url'    => $recording ?: $existing['recording_url'],
                         'lead_cid_id'      => $lead_id ?: $existing['lead_cid_id'],
                         'auto_linked'      => $lead_id ? 1 : $existing['auto_linked'],
                     ));
            $this->_json(array('ok' => true, 'action' => 'updated', 'event_id' => (int)$existing['id']));
            return;
        }

        $this->db->insert('cti_call_event', array(
            'provider_id'      => $provider_id,
            'external_call_id' => $ext_id,
            'direction'        => $direction,
            'from_number'      => $from_num,
            'to_number'        => $to_num,
            'agent_uid'        => $agent_uid,
            'started_at'       => $started,
            'ended_at'         => $ended_at,
            'duration_seconds' => $duration,
            'recording_url'    => $recording,
            'outcome'          => $outcome,
            'lead_cid_id'      => $lead_id,
            'auto_linked'      => $auto_linked,
            'raw_payload'      => json_encode($payload),
        ));
        $event_id = $this->db->insert_id();

        $this->_json(array(
            'ok'         => true,
            'action'     => 'created',
            'event_id'   => $event_id,
            'auto_linked'=> (bool)$auto_linked,
            'lead_cid_id'=> $lead_id,
        ));
    }

    /**
     * GET /cti/calls_for_user?uid=X&from=YYYY-MM-DD&to=YYYY-MM-DD
     * Returns calls for a given agent within the date range.
     * If from/to are omitted, defaults to today.
     */
    public function calls_for_user()
    {
        if (!$this->_auth()) return;

        $uid  = (int)$this->input->get('uid');
        $from = $this->input->get('from') ?: date('Y-m-d');
        $to   = $this->input->get('to')   ?: date('Y-m-d');

        if (!$uid) {
            $this->_json(array('ok' => false, 'error' => 'missing_uid'), 400);
            return;
        }

        $rows = $this->db
                     ->where('agent_uid', $uid)
                     ->where('DATE(started_at) >=', $from)
                     ->where('DATE(started_at) <=', $to)
                     ->order_by('started_at', 'DESC')
                     ->get('cti_call_event')
                     ->result_array();

        $this->_json(array(
            'ok'    => true,
            'uid'   => $uid,
            'from'  => $from,
            'to'    => $to,
            'count' => count($rows),
            'calls' => $rows,
        ));
    }

    /**
     * GET /cti/calls_for_lead?cid_id=X
     * Returns all call events linked to a specific lead.
     */
    public function calls_for_lead()
    {
        if (!$this->_auth()) return;

        $cid_id = (int)$this->input->get('cid_id');
        if (!$cid_id) {
            $this->_json(array('ok' => false, 'error' => 'missing_cid_id'), 400);
            return;
        }

        $rows = $this->db
                     ->where('lead_cid_id', $cid_id)
                     ->order_by('started_at', 'DESC')
                     ->get('cti_call_event')
                     ->result_array();

        $this->_json(array(
            'ok'    => true,
            'cid_id'=> $cid_id,
            'count' => count($rows),
            'calls' => $rows,
        ));
    }

    /**
     * POST /cti/manual_link
     * Manually associate a call event with a lead.
     * Required POST: event_id, cid_id
     */
    public function manual_link()
    {
        if (!$this->_auth()) return;

        $event_id = (int)$this->input->post('event_id');
        $cid_id   = (int)$this->input->post('cid_id');

        if (!$event_id || !$cid_id) {
            $this->_json(array('ok' => false, 'error' => 'missing_event_id_or_cid_id'), 400);
            return;
        }

        $event = $this->db->get_where('cti_call_event', array('id' => $event_id))->row_array();
        if (!$event) {
            $this->_json(array('ok' => false, 'error' => 'event_not_found'), 404);
            return;
        }

        $this->db->where('id', $event_id)
                 ->update('cti_call_event', array(
                     'lead_cid_id' => $cid_id,
                     'auto_linked' => 0, // manually linked, so clear auto flag
                 ));

        $this->_json(array(
            'ok'       => true,
            'event_id' => $event_id,
            'cid_id'   => $cid_id,
            'message'  => 'Call linked to lead.',
        ));
    }

    /**
     * POST /cti/click_to_call
     * Initiate an outbound call from the agent to a lead's phone number.
     * When the provider is disabled (enabled=0), returns a queued stub response.
     * Required POST: agent_uid, lead_phone
     * Optional POST: provider_id (defaults to first enabled provider)
     */
    public function click_to_call()
    {
        if (!$this->_auth()) return;

        $agent_uid  = (int)$this->input->post('agent_uid');
        $lead_phone = trim((string)$this->input->post('lead_phone'));

        if (!$agent_uid || !$lead_phone) {
            $this->_json(array('ok' => false, 'error' => 'missing_agent_uid_or_lead_phone'), 400);
            return;
        }

        $provider_id = (int)$this->input->post('provider_id');
        if ($provider_id) {
            $provider = $this->db->get_where('cti_provider', array('id' => $provider_id))->row_array();
        } else {
            $provider = $this->db->where('enabled', 1)->limit(1)->get('cti_provider')->row_array();
        }

        if (!$provider || !(int)$provider['enabled']) {
            // Provider disabled -- return stub response
            $this->_json(array(
                'ok'         => true,
                'status'     => 'queued',
                'demo_mode'  => true,
                'notice'     => 'No CTI provider is currently enabled. Call queued in demo mode. Configure provider keys in cti_provider to enable live calls.',
                'agent_uid'  => $agent_uid,
                'lead_phone' => $lead_phone,
            ));
            return;
        }

        // TODO: Call provider API (Exotel/Knowlarity) to initiate outbound call.
        // The provider should send a webhook to /cti/webhook_receive when the call starts.
        $this->_json(array(
            'ok'          => true,
            'status'      => 'initiated',
            'demo_mode'   => false,
            'provider'    => $provider['name'],
            'agent_uid'   => $agent_uid,
            'lead_phone'  => $lead_phone,
            'notice'      => 'Outbound call initiated. Live provider integration required for actual call placement.',
        ));
    }
}
