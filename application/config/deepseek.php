<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config['deepseek_api_key'] = 'REDACTED_DEEPSEEK_KEY';
$config['deepseek_api_url'] = 'https://api.deepseek.com/chat/completions';
$config['deepseek_model'] = 'deepseek-chat';
$config['deepseek_max_tokens'] = 32000; // Increased significantly
$config['deepseek_timeout'] = 120; // Increased timeout
$config['deepseek_max_input_tokens'] = 128000; // For context window