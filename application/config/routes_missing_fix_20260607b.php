<?php
defined("BASEPATH") OR exit("No direct script access allowed");
/* ADDITIVE 2026-06-07b: repoint endpoints the v2.11.0 app calls but that
   earlier route files pointed at MISSING controller methods (404). Loaded LAST
   so it wins. Production untouched. All targets verified to EXIST on staging.

   - funnel/path_conversion : was -> Mobile_read_api/funnel_path_conversion (missing).
                              real handler = Funnel_api/path_conversion (exists).
   - meeting_prep/generate  : was -> meetingprep/generate (missing).
                              real handler = CorporateMeetingPrepController/generate (exists).
   - cti/click_to_call      : not routed. M067_auto_cti_call_logging/click_to_call (exists).
   - cti/manual_link        : not routed. M067_auto_cti_call_logging/manual_link (exists). */

$route["api/funnel/path_conversion"]      = "Funnel_api/path_conversion";
$route["api/meeting_prep/generate"]["post"] = "CorporateMeetingPrepController/generate";
$route["api/cti/click_to_call"]["post"]   = "M067_auto_cti_call_logging/click_to_call";
$route["api/cti/manual_link"]["post"]     = "M067_auto_cti_call_logging/manual_link";
