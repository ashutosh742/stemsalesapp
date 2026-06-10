<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * audio_capture.php - Migration 025 config (created 2026-06-06)
 *
 * Minimal config so AudioCapture_model can instantiate on staging.
 * Transcription (Whisper) only runs at meeting-end with a real key set.
 * Leave openai_api_key blank on staging unless live transcription is being tested;
 * the model checks for a key before calling the API.
 */
$config['openai_api_key']       = getenv('OPENAI_API_KEY') ?: '';
$config['whisper_model']        = 'whisper-1';
$config['audio_root']           = '/home/selfstaging/uploads/meeting_audio';
$config['cost_per_minute_rs']   = 0.20;
$config['max_upload_mb']        = 10;
$config['transcription_enabled']= false; // staging default: off
