<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Load the secrets helper. Reads from env or /etc/stemapp/secrets.env.
// Never falls back to a hardcoded key.
require_once APPPATH . 'config/secrets.php';

$config['openai_api_key']    = stem_secret('openai_api_key', '');
$config['openai_base_url']   = 'https://api.openai.com/v1/';
$config['openai_model']      = 'gpt-4o';        // Production default for ChatAI_model
$config['openai_max_tokens'] = 32768;
$config['openai_temperature'] = 0.3;
$config['openai_timeout']    = 60;
