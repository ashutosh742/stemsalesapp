<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	https://codeigniter.com/user_guide/general/hooks.html
|
*/

/* ---- JSON body -> $_POST merge (installed 2026-06-08, permanent global fix) ---- */
$hook['pre_controller'][] = array(
    'class'    => 'JsonPostMerge',
    'function' => 'merge',
    'filename' => 'JsonPostMerge.php',
    'filepath' => 'hooks',
);
