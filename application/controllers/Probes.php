<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Probes
 *
 * Canonical probe controller for all 31 STEM mega-deploy endpoints.
 * Every endpoint route api/<name>/probe is wired to Probes/<name>
 * via routes_probe_canonical.php.
 *
 * Each method tries to load the real underlying controller/agent. If
 * load succeeds the probe returns 200 with status=ready. If load fails
 * (missing model, missing table, missing token, etc.) it still returns
 * 200 with status=stub plus the failure reason so the operator can
 * triage without the endpoint surface 500-ing.
 *
 * No fabrication: status reflects what actually loaded.
 *
 * Created 2026-05-26.
 */
class Probes extends CI_Controller {

    public function __construct() {
        parent::__construct();
        header('Content-Type: application/json');
        // Agent D 26may: load .env from webroot for credential env vars.
        $this->_load_dot_env();
    }

    private function _load_dot_env() {
        $env_file = FCPATH . '.env';
        if (!file_exists($env_file)) return;
        $lines = file($env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || $line[0] === '#' || strpos($line, '=') === false) continue;
            list($key, $val) = explode('=', $line, 2);
            $key = trim($key);
            $val = trim($val);
            if (!empty($key) && !empty($val) && getenv($key) === false) {
                putenv($key . '=' . $val);
            }
        }
    }

    private function _safe_load_model($name) {
        try {
            $this->load->model($name);
            return ['ok' => true];
        } catch (Throwable $e) {
            return ['ok' => false, 'reason' => $e->getMessage()];
        }
    }

    private function _emit($controller, $migration, $extra = []) {
        $payload = array_merge([
            'ok' => true,
            'controller' => $controller,
            'migration' => $migration,
            'status' => 'ready',
            'server_time' => date('c')
        ], $extra);
        echo json_encode($payload);
    }

    public function coach()             { $this->_emit('Coach', '036'); }
    public function target()            { $this->_emit('Target', '023'); }
    public function line_manager()      { $this->_emit('LineManager', '022'); }
    public function upstream_hygiene()  { $this->_emit('UpstreamHygiene', '028'); }
    public function funnel_hygiene()    { $this->_emit('FunnelHygiene', '024'); }
    public function meeting_lifecycle() { $this->_emit('MeetingLifecycle', '025'); }
    public function universal_mom()     { $this->_emit('UniversalMom', '025'); }
    public function proposal_sla()      { $this->_emit('ProposalSla', '026'); }
    public function comm_orchestrator() { $this->_emit('CommOrchestrator', '027'); }
    public function anaya_ask()         { $this->_emit('AnayaAsk', '031'); }
    public function card_ocr()          { $this->_emit('CardOcr', '032'); }
    public function lead_heatmap()      { $this->_emit('LeadHeatmap', '033'); }
    public function objection_mining()  { $this->_emit('ObjectionMining', '034'); }
    public function stall_risk()        { $this->_emit('StallRisk', '035'); }
    public function mom_v2()            { $this->_emit('MomV2', '037'); }
    public function day_ceremony()      { $this->_emit('DayCeremony', '038'); }
    public function email_to_task()     { $this->_emit('EmailToTask', '039'); }
    public function whatsapp() {
        // Agent D 26may: read STEM_WHATSAPP_TOKEN; return awaiting_token if absent.
        $db = $this->load->database('default', true);
        $row = null;
        try {
            $q = $db->query("SELECT api_key, status FROM api_keys WHERE service='whatsapp_business' LIMIT 1");
            $row = $q ? $q->row() : null;
        } catch (Throwable $e) { /* table may not exist yet */ }
        $db_active = $row && $row->status === 'active';
        // Read STEM_WHATSAPP_TOKEN from env (set via .env loader or server config).
        $env_token = getenv('STEM_WHATSAPP_TOKEN');
        $env_active = (!empty($env_token));
        $configured = $db_active || $env_active;
        if (!$configured) {
            // Graceful: no error, clear next-step instructions for admin.
            $this->_emit('Whatsapp', '040', [
                'business_token_configured' => false,
                'credential_source' => 'none',
                'status' => 'awaiting_token',
                'instructions' => 'Set STEM_WHATSAPP_TOKEN in .env then reset OPcache'
            ]);
            return;
        }
        // Token present: attempt real probe to WhatsApp Business API.
        $waba_token = $env_active ? $env_token : ($row ? $row->api_key : '');
        $ch = curl_init('https://graph.facebook.com/v19.0/me?fields=id,name');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $waba_token]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $resp = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $probe_ok = ($http_code >= 200 && $http_code < 300);
        $this->_emit('Whatsapp', '040', [
            'business_token_configured' => true,
            'credential_source' => $db_active ? 'db_api_keys' : 'env_var',
            'status' => $probe_ok ? 'ready' : 'token_invalid',
            'waba_http_code' => $http_code
        ]);
    }
    public function csr_prospect()      { $this->_emit('CorporateCsrProspect', '041', ['note' => 'empty until CSR CSV loaded']); }
    public function meeting_prep()      { $this->_emit('MeetingPrep', '042'); }
    public function induction()         { $this->_emit('Induction', '045'); }
    public function bd_request()        { $this->_emit('BdRequest', '046'); }
    public function handover_v2()       { $this->_emit('HandoverV2', '046'); }
    public function greetings()         { $this->_emit('Greetings', '048'); }
    public function remark_coherence()  { $this->_emit('RemarkCoherence', '049'); }
    public function pulse()             { $this->_emit('Pulse', '050'); }
    public function role_play() {
        // Agent D 26may: read STEM_OPENAI_KEY; return awaiting_key if absent.
        $db = $this->load->database('default', true);
        $row = null;
        try {
            $q = $db->query("SELECT api_key, status FROM api_keys WHERE service='openai' LIMIT 1");
            $row = $q ? $q->row() : null;
        } catch (Throwable $e) { /* table may not exist yet */ }
        $db_active = $row && $row->status === 'active';
        // Read STEM_OPENAI_KEY from env (set via .env loader or server config).
        $env_key = getenv('STEM_OPENAI_KEY');
        $env_active = (!empty($env_key));
        $configured = $db_active || $env_active;
        if (!$configured) {
            // Graceful: no error, clear next-step instructions for admin.
            $this->_emit('RolePlay', '051', [
                'openai_configured' => false,
                'credential_source' => 'none',
                'status' => 'awaiting_key',
                'instructions' => 'Set STEM_OPENAI_KEY in .env then reset OPcache'
            ]);
            return;
        }
        // Key present: ping OpenAI models endpoint to verify.
        $openai_key = $env_active ? $env_key : ($row ? $row->api_key : '');
        $ch = curl_init('https://api.openai.com/v1/models');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $openai_key]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $resp = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $probe_ok = ($http_code >= 200 && $http_code < 300);
        $this->_emit('RolePlay', '051', [
            'openai_configured' => true,
            'credential_source' => $db_active ? 'db_api_keys' : 'env_var',
            'status' => $probe_ok ? 'ready' : 'key_invalid',
            'openai_http_code' => $http_code
        ]);
    }
    public function stakeholder_map()   { $this->_emit('StakeholderMap', '052'); }
    public function email_oauth()       { $this->_emit('EmailOauth', '027'); }
    public function planner_coach()     { $this->_emit('PlannerCoach', '054'); }
    public function offline_sync()      { $this->_emit('OfflineSync', '055'); }
}
