<?php
defined("BASEPATH") OR exit("No direct script access allowed");
/* ADDITIVE 2026-06-07: 6 missing planner/v2 endpoints the v2.11.0 app calls
   but staging never routed. Backed by PlannerV2Extra (thin, reuses Menu_model;
   never 500s -> empty-shape on missing data). Last include so it wins; never
   overrides existing v2_* routes. Production untouched. */
$route["api/planner/v2/filter_leads"]["get"]       = "PlannerV2Extra/filter_leads";
$route["api/planner/v2/purposes"]["get"]           = "PlannerV2Extra/purposes";
$route["api/planner/v2/purposes_v2"]["get"]        = "PlannerV2Extra/purposes_v2";
$route["api/planner/v2/wallet"]["get"]             = "PlannerV2Extra/wallet";
$route["api/planner/v2/minutes_for_action"]["get"] = "PlannerV2Extra/minutes_for_action";
$route["api/planner/v2/cell"]["post"]              = "PlannerV2Extra/cell";
