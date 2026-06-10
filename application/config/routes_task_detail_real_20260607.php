<?php
defined("BASEPATH") OR exit("No direct script access allowed");
/* ADDITIVE 2026-06-07: repoint task/detail + task/save_draft to the real
   tblcallevents-backed handler (accepts tid, surfaces meeting/RP-MOM/proposal
   context). Last include so it wins over routes_real_29 + routes_mobile_pilot.
   Production untouched. */
$route["api/task/detail"]     = "Mobile_task_detail_api/detail";
$route["api/task/save_draft"] = "Mobile_task_detail_api/save_draft";
