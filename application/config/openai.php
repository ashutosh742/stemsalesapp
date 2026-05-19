<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config['openai_api_key'] = 'REDACTED_OPENAI_KEY';
$config['openai_model']  = 'gpt-4o-mini'; // or gpt-4.1 if enabled


$config['openai_base_url'] = 'https://api.openai.com/v1/';

$config['openai_model'] = 'gpt-4-turbo-preview';  // Default model
$config['openai_max_tokens'] = 32768;  // Default max tokens
$config['openai_temperature'] = 0.3;   // Default temperature
$config['openai_timeout'] = 60;        // Request timeout in seconds