<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CorporateCsrProspect_model.php
 *
 * Loader shim: CI3 looks for filename CorporateCsrProspect_model.php
 * but the actual implementation lives in CorporateCsrProspect_agent.php.
 * Include it if class not already defined.
 */

if (!class_exists('CorporateCsrProspect_model', false)) {
    require_once __DIR__ . '/CorporateCsrProspect_agent.php';
}
