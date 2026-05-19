<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Load the secrets helper. Reads from env or /etc/stemapp/secrets.env.
require_once APPPATH . 'config/secrets.php';

$config['deepseek_api_key']         = stem_secret('deepseek_api_key', '');
$config['deepseek_api_url']         = 'https://api.deepseek.com/chat/completions';
$config['deepseek_model']           = 'deepseek-chat';
$config['deepseek_max_tokens']      = 32000;
$config['deepseek_timeout']         = 120;
$config['deepseek_max_input_tokens'] = 128000;
