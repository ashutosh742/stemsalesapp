<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * AudioCapture_model loader shim (2026-06-06).
 * The implementation lives in Stem_audio_capture_agent.php (declares
 * class AudioCapture_model). CodeIgniter's loader expects a file named
 * AudioCapture_model.php, so this shim includes the real implementation.
 */
require_once(__DIR__ . '/Stem_audio_capture_agent.php');
