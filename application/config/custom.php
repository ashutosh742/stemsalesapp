<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$config['csr_bearer_token'] = getenv('STEM_DIGEST_TOKEN') ?: '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
$config['stem_digest_token'] = $config['csr_bearer_token'];
