<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * routes_gapfix2_20260607 - ADDITIVE 2026-06-07
 * Maps the 4 last-remaining mobile endpoints to MobileGapFix2_api.
 * Append-only; included LAST so these win. Production untouched.
 */

$route['api/review_schedule/planner_blocks']['get']    = 'MobileGapFix2_api/planner_blocks';
$route['api/csr_prospect/apollo/quota_status']['get']  = 'MobileGapFix2_api/apollo_quota_status';
$route['api/district_intel/accept_corporate']['post']  = 'MobileGapFix2_api/accept_corporate';
$route['api/coach/knowledge/upload_artifact']['post']  = 'MobileGapFix2_api/upload_artifact';
