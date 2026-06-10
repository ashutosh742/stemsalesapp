<?php
/**
 * AudioCapture_model v2 - Migration 025 patch
 *
 * Adds dual-backend support for transcription:
 *  - openai_api: OpenAI Whisper API (pilot phase, 25 May to 31 May 2026)
 *  - self_host:  On-prem whisper.cpp service at https://whisper.stem-internal.local
 *
 * Reads TRANSCRIPTION_BACKEND from /etc/stem-secrets/transcription.env on each call,
 * so a single env flip cuts over without code redeploy.
 *
 * Failover: if self_host returns 503 or times out and FAILOVER_TO_API=1, falls back
 * to OpenAI API automatically. Logs which backend served each transcription in
 * meeting_audio_log.transcription_backend.
 *
 * Replaces stem_audio_capture_agent_php.php (v1) on 1 Jun 2026 deploy.
 *
 * Owner: STEM Learning
 * Plain English. No em-dashes. "Rs" for rupees, "percent" spelled out.
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class AudioCapture_model extends CI_Model
{
    private $config_path = '/etc/stem-secrets/transcription.env';
    private $config_cache = null;
    private $cache_loaded_at = 0;
    private $cache_ttl_sec = 60;

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        log_message('info', 'AudioCapture v2 loaded');
    }

    /**
     * Load transcription config from env file. Cached for 60 seconds to avoid
     * disk hit on every meeting end.
     */
    private function load_config()
    {
        if ($this->config_cache !== null && (time() - $this->cache_loaded_at) < $this->cache_ttl_sec) {
            return $this->config_cache;
        }

        $defaults = [
            'TRANSCRIPTION_BACKEND'  => 'openai_api',
            'WHISPER_API_KEY'        => '',
            'WHISPER_ONPREM_URL'     => 'https://whisper.stem-internal.local',
            'WHISPER_ONPREM_CERT'    => '/etc/stem-secrets/client.crt',
            'WHISPER_ONPREM_KEY'     => '/etc/stem-secrets/client.key',
            'FAILOVER_TO_API'        => '1',
            'WHISPER_MODEL'          => 'whisper-1',
            'WHISPER_TIMEOUT_SEC'    => '120',
        ];

        if (!file_exists($this->config_path)) {
            log_message('error', "transcription.env not found at {$this->config_path}, using defaults");
            $this->config_cache = $defaults;
            $this->cache_loaded_at = time();
            return $this->config_cache;
        }

        $lines = file($this->config_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $cfg = $defaults;
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) continue;
            if (strpos($line, '=') === false) continue;
            list($k, $v) = explode('=', $line, 2);
            $cfg[trim($k)] = trim($v);
        }

        $this->config_cache = $cfg;
        $this->cache_loaded_at = time();
        return $this->config_cache;
    }

    /**
     * Main entry called from MeetingLifecycle::end_meeting after audio upload.
     *
     * @param int    $meeting_lifecycle_id
     * @param string $audio_path  local path to uploaded .opus file
     * @return array  [success, transcript_text, segments, language, backend_used, duration_sec, error?]
     */
    public function transcribe_meeting($meeting_lifecycle_id, $audio_path)
    {
        if (!file_exists($audio_path)) {
            return ['success' => false, 'error' => 'audio_file_missing'];
        }

        $file_size_mb = filesize($audio_path) / 1024 / 1024;
        if ($file_size_mb > 25) {
            // OpenAI hard limit. On-prem can take more but cap here for sanity.
            log_message('warning', "Audio {$meeting_lifecycle_id} is {$file_size_mb} MB, oversized");
            return ['success' => false, 'error' => 'audio_too_large'];
        }

        $cfg = $this->load_config();
        $backend = $cfg['TRANSCRIPTION_BACKEND'];

        // Log attempt start
        $audio_log_id = $this->db->insert('meeting_audio_log', [
            'meeting_lifecycle_id' => $meeting_lifecycle_id,
            'audio_path' => $audio_path,
            'file_size_mb' => round($file_size_mb, 2),
            'transcription_backend' => $backend,
            'transcription_status' => 'in_progress',
            'transcription_attempts' => 1,
            'created_at' => date('Y-m-d H:i:s'),
        ]) ? $this->db->insert_id() : 0;

        $started_at = microtime(true);
        $result = null;

        if ($backend === 'self_host') {
            $result = $this->transcribe_via_onprem($audio_path, $cfg);

            // Failover if on-prem failed and flag set
            if (!$result['success'] && $cfg['FAILOVER_TO_API'] === '1') {
                log_message('warning', "On-prem failed for {$meeting_lifecycle_id}: {$result['error']}. Failing over to API.");
                $this->db->where('id', $audio_log_id)->update('meeting_audio_log', [
                    'failover_triggered' => 1,
                    'failover_reason' => $result['error'],
                ]);
                $result = $this->transcribe_via_openai($audio_path, $cfg);
                $result['backend_used'] = 'openai_api_failover';
            } else {
                $result['backend_used'] = 'self_host';
            }
        } else {
            // openai_api (pilot default)
            $result = $this->transcribe_via_openai($audio_path, $cfg);
            $result['backend_used'] = 'openai_api';
        }

        $elapsed_sec = round(microtime(true) - $started_at, 2);

        // Persist outcome
        $update = [
            'transcription_status' => $result['success'] ? 'completed' : 'failed',
            'backend_used' => $result['backend_used'],
            'elapsed_sec' => $elapsed_sec,
            'completed_at' => date('Y-m-d H:i:s'),
        ];
        if ($result['success']) {
            $update['transcript_text'] = $result['transcript_text'];
            $update['language_detected'] = $result['language'];
            $update['duration_sec'] = $result['duration_sec'];
        } else {
            $update['error_message'] = $result['error'];
            $update['transcription_status'] = 'failed_retry_later';
        }
        $this->db->where('id', $audio_log_id)->update('meeting_audio_log', $update);

        $result['audio_log_id'] = $audio_log_id;
        $result['elapsed_sec'] = $elapsed_sec;
        return $result;
    }

    /**
     * Call on-prem whisper.cpp service via mTLS
     */
    private function transcribe_via_onprem($audio_path, $cfg)
    {
        $url = rtrim($cfg['WHISPER_ONPREM_URL'], '/') . '/transcribe';

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => [
                'file' => new CURLFile($audio_path, 'audio/opus', basename($audio_path)),
                'language' => 'auto',
            ],
            CURLOPT_SSLCERT => $cfg['WHISPER_ONPREM_CERT'],
            CURLOPT_SSLKEY => $cfg['WHISPER_ONPREM_KEY'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => intval($cfg['WHISPER_TIMEOUT_SEC']),
            CURLOPT_CONNECTTIMEOUT => 10,
        ]);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);

        if ($http_code === 503) {
            return ['success' => false, 'error' => 'onprem_queue_full'];
        }
        if ($http_code === 0 || $err) {
            return ['success' => false, 'error' => "onprem_network: {$err}"];
        }
        if ($http_code !== 200) {
            return ['success' => false, 'error' => "onprem_http_{$http_code}"];
        }

        $data = json_decode($response, true);
        if (!isset($data['text'])) {
            return ['success' => false, 'error' => 'onprem_malformed_response'];
        }

        return [
            'success' => true,
            'transcript_text' => $data['text'],
            'segments' => $data['segments'] ?? [],
            'language' => $data['language'] ?? 'auto',
            'duration_sec' => $data['duration_sec'] ?? 0,
        ];
    }

    /**
     * Call OpenAI Whisper API
     */
    private function transcribe_via_openai($audio_path, $cfg)
    {
        if (empty($cfg['WHISPER_API_KEY'])) {
            return ['success' => false, 'error' => 'api_key_missing'];
        }

        $url = 'https://api.openai.com/v1/audio/transcriptions';

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => [
                'file' => new CURLFile($audio_path, 'audio/opus', basename($audio_path)),
                'model' => $cfg['WHISPER_MODEL'],
                'response_format' => 'verbose_json',
            ],
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $cfg['WHISPER_API_KEY'],
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => intval($cfg['WHISPER_TIMEOUT_SEC']),
        ]);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);

        if ($http_code === 429) {
            return ['success' => false, 'error' => 'openai_rate_limited'];
        }
        if ($http_code !== 200) {
            return ['success' => false, 'error' => "openai_http_{$http_code}: " . substr($response, 0, 200)];
        }

        $data = json_decode($response, true);
        if (!isset($data['text'])) {
            return ['success' => false, 'error' => 'openai_malformed_response'];
        }

        return [
            'success' => true,
            'transcript_text' => $data['text'],
            'segments' => $data['segments'] ?? [],
            'language' => $data['language'] ?? 'auto',
            'duration_sec' => $data['duration'] ?? 0,
        ];
    }

    /**
     * Retry failed transcriptions. Called by nightly cron at 02:00 IST.
     * Picks up rows with transcription_status='failed_retry_later' AND
     * transcription_attempts < 3 AND created_at within last 7 days.
     */
    public function retry_failed()
    {
        $rows = $this->db
            ->select('id, meeting_lifecycle_id, audio_path, transcription_attempts')
            ->from('meeting_audio_log')
            ->where('transcription_status', 'failed_retry_later')
            ->where('transcription_attempts <', 3)
            ->where('created_at >', date('Y-m-d H:i:s', strtotime('-7 days')))
            ->limit(50)
            ->get()->result_array();

        $retried = 0;
        $recovered = 0;
        foreach ($rows as $row) {
            if (!file_exists($row['audio_path'])) {
                $this->db->where('id', $row['id'])->update('meeting_audio_log', [
                    'transcription_status' => 'audio_lost',
                    'error_message' => 'file_missing_during_retry',
                ]);
                continue;
            }

            $this->db->where('id', $row['id'])->set('transcription_attempts',
                'transcription_attempts + 1', false)->update('meeting_audio_log');

            $result = $this->transcribe_meeting(
                $row['meeting_lifecycle_id'],
                $row['audio_path']
            );
            $retried++;
            if ($result['success']) {
                $recovered++;
            }
        }

        log_message('info', "AudioCapture retry: {$retried} attempted, {$recovered} recovered");
        return ['retried' => $retried, 'recovered' => $recovered];
    }

    /**
     * Daily cost report. Sums OpenAI API spend and on-prem usage hours for the day.
     * Sent to stemlearning@gmail.com at 23:30 IST as part of the daily ops digest.
     */
    public function daily_cost_report($date = null)
    {
        if ($date === null) {
            $date = date('Y-m-d');
        }

        $rows = $this->db
            ->select('backend_used, SUM(duration_sec) AS total_sec, COUNT(*) AS n')
            ->from('meeting_audio_log')
            ->where('DATE(created_at)', $date)
            ->where('transcription_status', 'completed')
            ->group_by('backend_used')
            ->get()->result_array();

        $report = [
            'date' => $date,
            'openai_api_meetings' => 0,
            'openai_api_minutes' => 0,
            'openai_api_cost_rs' => 0,
            'self_host_meetings' => 0,
            'self_host_minutes' => 0,
            'failover_meetings' => 0,
        ];

        foreach ($rows as $r) {
            $minutes = round($r['total_sec'] / 60, 1);
            if ($r['backend_used'] === 'openai_api') {
                $report['openai_api_meetings'] = $r['n'];
                $report['openai_api_minutes'] = $minutes;
                // 0.006 USD per minute. USD-INR ~83.5 in May 2026.
                $report['openai_api_cost_rs'] = round($minutes * 0.006 * 83.5, 2);
            } elseif ($r['backend_used'] === 'self_host') {
                $report['self_host_meetings'] = $r['n'];
                $report['self_host_minutes'] = $minutes;
            } elseif ($r['backend_used'] === 'openai_api_failover') {
                $report['failover_meetings'] = $r['n'];
                $report['openai_api_minutes'] += $minutes;
                $report['openai_api_cost_rs'] += round($minutes * 0.006 * 83.5, 2);
            }
        }

        return $report;
    }

    /**
     * Purge audio older than 90 days. Called by weekly cron Sunday 03:00 IST.
     * Transcript text stays. Only the .opus file is deleted to reclaim disk.
     */
    public function purge_old_audio()
    {
        $cutoff = date('Y-m-d', strtotime('-90 days'));
        $rows = $this->db
            ->select('id, audio_path')
            ->from('meeting_audio_log')
            ->where('DATE(created_at) <', $cutoff)
            ->where('audio_purged', 0)
            ->limit(500)
            ->get()->result_array();

        $purged = 0;
        $freed_mb = 0;
        foreach ($rows as $row) {
            if (file_exists($row['audio_path'])) {
                $freed_mb += filesize($row['audio_path']) / 1024 / 1024;
                unlink($row['audio_path']);
            }
            $this->db->where('id', $row['id'])->update('meeting_audio_log', [
                'audio_purged' => 1,
                'audio_purged_at' => date('Y-m-d H:i:s'),
            ]);
            $purged++;
        }

        log_message('info', "AudioCapture purge: {$purged} files, " . round($freed_mb, 1) . " MB freed");
        return ['purged' => $purged, 'freed_mb' => round($freed_mb, 1)];
    }

    /**
     * Health check used by monitoring. Returns backend status.
     */
    public function health()
    {
        $cfg = $this->load_config();
        $out = [
            'backend' => $cfg['TRANSCRIPTION_BACKEND'],
            'failover_enabled' => $cfg['FAILOVER_TO_API'] === '1',
        ];

        if ($cfg['TRANSCRIPTION_BACKEND'] === 'self_host') {
            $ch = curl_init(rtrim($cfg['WHISPER_ONPREM_URL'], '/') . '/health');
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 5,
                CURLOPT_SSLCERT => $cfg['WHISPER_ONPREM_CERT'],
                CURLOPT_SSLKEY => $cfg['WHISPER_ONPREM_KEY'],
            ]);
            $resp = curl_exec($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            $out['onprem_reachable'] = ($code === 200);
            $out['onprem_response'] = json_decode($resp, true);
        }

        return $out;
    }
}
