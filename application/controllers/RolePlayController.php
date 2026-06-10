<?php
/**
 * RolePlayController.php - CI3 routing shim
 * audit_20260606: RolePlay.php defines class RolePlayController but filename is RolePlay.php.
 * CI3 route target "RolePlayController/x" needs this file to exist.
 * This shim requires RolePlay.php and re-exports the class.
 * Additive only. Staging only. Production untouched.
 */
defined("BASEPATH") OR exit("No direct script access allowed");

require_once APPPATH . "controllers/RolePlay.php";

// RolePlayController is already defined by the require above.
// No further code needed - CI3 will instantiate it directly.
