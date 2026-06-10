<?php
// =====================================================================
// STEM CRM - Migration 025: Audio Capture Agent
// File: application/models/AIAgents/AudioCapture_model.php
// =====================================================================
// Purpose:
//   When a meeting ends, the phone uploads the Opus mono recording
//   (~4 MB for 45 minutes). This agent:
//
//     1) Accepts the upload, stores to /uploads/meeting_audio/<YYYY>/<MM>/
//     2) Calls OpenAI Whisper large-v3 with the audio file
//     3) Stores the transcript in meeting_audio_log
//     4) Hands the transcript to ExtractionAgent (downstream model)
//        which fills mom_draft fields against the agenda template
//     5) Returns a draft_id + extraction_confidence_pct to the caller
//
// Architecture notes user confirmed:
//   - Path B: phone records (Opus 24 kbps mono), uploads at meeting end
//   - Silent recording, internal QA only (consent A under India law)
//   - Whisper large-v3 via OpenAI for pilot (Rs 8 per meeting)
//   - 45 min audio = 4 MB upload, works on patchy 4G
//   - Pilot cost: 6 actors x ~80 meetings/month = Rs 4,340/month
//   - Org cost: 63 actors x 88 meetings/month = Rs 50,000/month
//
// Hooked from:
//   - controllers/MeetingLifecycle.php::end()
//   - meeting_audio_log stays as audit trail forever
//   - mom_draft is the working surface, mom_data captures final submit
// =====================================================================

defined('BASEPATH') OR exit('No direct script access allowed');

class AudioCapture_model extends CI_Model {

    private $audio_root = '/var/www/uploads/meeting_audio';
    private $openai_endpoint = 'https://api.openai.com/v1/audio/transcriptions';
    private $whisper_model = 'whisper-1';
    private $cost_per_minute_rs = 0.20;  // Rs 12 per hour, prorated

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->config('audio_capture');
    }

    // -----------------------------------------------------------------
    // ENTRY POINT - called by MeetingLifecycle::end() after upload
    // $params:
    //   callevent_id (int)         tblcallevents row id
    //   cid_id (int)               init_call.id
    //   actor_uid (int)
    //   actor_role (string)        BD|CM|RM|SH|Director
    //   audio_path (string)        local path to opus file
    //   duration_seconds (int)
    //   meeting_classification (string)
    //   is_travel_cluster (int)
    // -----------------------------------------------------------------
    public function process_meeting_audio($params) {
        $callevent_id = (int)$params['callevent_id'];
        $cid_id = (int)$params['cid_id'];
        $actor_uid = (int)$params['actor_uid'];
        $actor_role = $params['actor_role'];
        $audio_path = $params['audio_path'];
        $duration_seconds = (int)$params['duration_seconds'];
        $classification = $params['meeting_classification'];
        $is_travel_cluster = (int)($params['is_travel_cluster'] ?? 0);

        $duration_minutes = ceil($duration_seconds / 60);
        $cost_rs = round($duration_minutes * $this->cost_per_minute_rs, 2);

        // Sanity checks
        if (!file_exists($audio_path)) {
            return array('ok' => false, 'reason' => 'audio_file_missing', 'path' => $audio_path);
        }
        $size_bytes = filesize($audio_path);
        if ($size_bytes < 1024) {
            return array('ok' => false, 'reason' => 'audio_file_too_small', 'size' => $size_bytes);
        }

        // Move to permanent home keyed by year/month
        $target_dir = $this->audio_root . '/' . date('Y') . '/' . date('m');
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0775, true);
        }
        $final_name = sprintf('%s_%d_uid%d.opus',
            date('Ymd_His'), $callevent_id, $actor_uid);
        $final_path = $target_dir . '/' . $final_name;
        if (!rename($audio_path, $final_path)) {
            // fall back to copy
            copy($audio_path, $final_path);
        }

        // Insert audit row first so failures still leave a trail
        $audit = array(
            'callevent_id' => $callevent_id,
            'cid_id' => $cid_id,
            'actor_uid' => $actor_uid,
            'actor_role' => $actor_role,
            'audio_path' => $final_path,
            'duration_seconds' => $duration_seconds,
            'file_size_bytes' => $size_bytes,
            'classification' => $classification,
            'is_travel_cluster' => $is_travel_cluster,
            'uploaded_at' => date('Y-m-d H:i:s'),
            'whisper_status' => 'pending',
            'cost_rs' => $cost_rs
        );
        $this->db->insert('meeting_audio_log', $audit);
        $audit_id = $this->db->insert_id();

        // Call Whisper
        $transcript_result = $this->transcribe_with_whisper($final_path);
        if (!$transcript_result['ok']) {
            $this->db->where('id', $audit_id)
                     ->update('meeting_audio_log', array(
                        'whisper_status' => 'failed',
                        'whisper_error' => substr($transcript_result['error'], 0, 500)
                     ));
            return array('ok' => false, 'reason' => 'whisper_failed',
                         'detail' => $transcript_result['error'],
                         'audio_log_id' => $audit_id);
        }

        $transcript = $transcript_result['text'];
        $whisper_seconds = $transcript_result['elapsed_seconds'];

        $this->db->where('id', $audit_id)
                 ->update('meeting_audio_log', array(
                    'whisper_status' => 'done',
                    'transcript_text' => $transcript,
                    'transcript_word_count' => str_word_count($transcript),
                    'whisper_elapsed_seconds' => $whisper_seconds
                 ));

        // Hand transcript to extraction agent
        $this->load->model('AIAgents/MomDraftExtractor_model', 'extractor');
        $extraction = $this->extractor->extract_draft(array(
            'transcript' => $transcript,
            'cid_id' => $cid_id,
            'callevent_id' => $callevent_id,
            'actor_uid' => $actor_uid,
            'actor_role' => $actor_role,
            'classification' => $classification
        ));

        if ($extraction['ok']) {
            $this->db->where('id', $audit_id)
                     ->update('meeting_audio_log', array(
                        'extraction_done' => 1,
                        'extraction_confidence_pct' => $extraction['confidence_pct'],
                        'mom_draft_id' => $extraction['draft_id']
                     ));
        }

        return array(
            'ok' => true,
            'audio_log_id' => $audit_id,
            'transcript_word_count' => str_word_count($transcript),
            'whisper_elapsed_seconds' => $whisper_seconds,
            'extraction' => $extraction,
            'cost_rs' => $cost_rs,
            'audio_path' => $final_path
        );
    }

    // -----------------------------------------------------------------
    // Whisper wrapper
    // -----------------------------------------------------------------
    private function transcribe_with_whisper($audio_path) {
        $start = microtime(true);
        $api_key = $this->config->item('openai_api_key');
        if (empty($api_key)) {
            return array('ok' => false, 'error' => 'openai_api_key not configured');
        }

        $cfile = curl_file_create($audio_path, 'audio/ogg', basename($audio_path));
        $post = array(
            'file' => $cfile,
            'model' => $this->whisper_model,
            'language' => 'en',  // primary; Hindi/Marathi handled via transliteration downstream
            'response_format' => 'verbose_json',
            'temperature' => '0'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->openai_endpoint);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer ' . $api_key
        ));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_TIMEOUT, 240);  // 4 min cap for 45 min audio

        $raw = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);

        $elapsed = round(microtime(true) - $start, 2);

        if ($code !== 200 || empty($raw)) {
            return array('ok' => false, 'error' => 'http_' . $code . ' ' . $err . ' body ' . substr($raw, 0, 200));
        }
        $j = json_decode($raw, true);
        if (!isset($j['text'])) {
            return array('ok' => false, 'error' => 'no_text_in_response');
        }
        return array('ok' => true,
                     'text' => $j['text'],
                     'elapsed_seconds' => $elapsed,
                     'language_detected' => $j['language'] ?? 'unknown');
    }

    // -----------------------------------------------------------------
    // Used by cron: retry stuck rows from yesterday
    // -----------------------------------------------------------------
    public function retry_failed_transcriptions() {
        $rows = $this->db->where('whisper_status', 'failed')
                         ->where('uploaded_at >=', date('Y-m-d 00:00:00', strtotime('-1 day')))
                         ->where('retry_count <', 2)
                         ->get('meeting_audio_log')->result_array();
        $results = array();
        foreach ($rows as $r) {
            $this->db->where('id', $r['id'])
                     ->set('retry_count', 'retry_count + 1', false)
                     ->set('last_retry_at', date('Y-m-d H:i:s'))
                     ->update('meeting_audio_log');

            $t = $this->transcribe_with_whisper($r['audio_path']);
            if ($t['ok']) {
                $this->db->where('id', $r['id'])
                         ->update('meeting_audio_log', array(
                            'whisper_status' => 'done',
                            'transcript_text' => $t['text'],
                            'transcript_word_count' => str_word_count($t['text']),
                            'whisper_elapsed_seconds' => $t['elapsed_seconds']
                         ));
                $results[] = array('id' => $r['id'], 'ok' => true);
            } else {
                $results[] = array('id' => $r['id'], 'ok' => false, 'error' => $t['error']);
            }
        }
        return $results;
    }

    // -----------------------------------------------------------------
    // Storage hygiene: purge audio files older than 90 days
    // Transcripts stay forever, only the source audio is deleted.
    // -----------------------------------------------------------------
    public function purge_old_audio($keep_days = 90) {
        $cutoff = date('Y-m-d H:i:s', strtotime('-' . $keep_days . ' days'));
        $rows = $this->db->select('id, audio_path')
                         ->where('uploaded_at <', $cutoff)
                         ->where('audio_purged', 0)
                         ->get('meeting_audio_log')->result_array();
        $purged = 0;
        foreach ($rows as $r) {
            if (file_exists($r['audio_path'])) {
                unlink($r['audio_path']);
            }
            $this->db->where('id', $r['id'])
                     ->update('meeting_audio_log', array(
                        'audio_purged' => 1,
                        'audio_purged_at' => date('Y-m-d H:i:s')
                     ));
            $purged++;
        }
        return $purged;
    }

    // -----------------------------------------------------------------
    // Daily cost report for finance
    // -----------------------------------------------------------------
    public function daily_cost_report($date = null) {
        $date = $date ?: date('Y-m-d', strtotime('-1 day'));
        $sql = "SELECT COUNT(*) AS meetings,
                       SUM(duration_seconds)/60 AS minutes_total,
                       SUM(cost_rs) AS cost_rs_total,
                       SUM(CASE WHEN whisper_status='done' THEN 1 ELSE 0 END) AS done,
                       SUM(CASE WHEN whisper_status='failed' THEN 1 ELSE 0 END) AS failed
                FROM meeting_audio_log
                WHERE DATE(uploaded_at) = ?";
        return $this->db->query($sql, array($date))->row_array();
    }
}
// END AudioCapture_model
