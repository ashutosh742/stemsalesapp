<?php
// =====================================================================
// routes_pst_remark.php - PST Queue + Remark Coherence routes
// Migration 049 / Agent 5
// Controllers in root controllers dir per CI3 convention on this server.
// =====================================================================

// PST Queue routes
$route['api/pst/probe']       = 'Pst/probe';
$route['api/pst/unassigned']  = 'Pst/unassigned';
$route['api/pst/queue']       = 'Pst/queue';
$route['api/pst/assign']      = 'Pst/assign';
$route['api/pst/change']      = 'Pst/change';
$route['api/pst/conversions'] = 'Pst/conversions';

// Remark routes
$route['api/remark/probe']                 = 'Remark/probe';
$route['api/remark/add']                   = 'Remark/add';
$route['api/remark/list_for_event/(:any)'] = 'Remark/list_for_event/$1';

// Remark Coherence routes (M049)
$route['api/remark/coherence/probe'] = 'RemarkCoherence/probe';
$route['api/remark/coherence/late']  = 'RemarkCoherence/late';
$route['api/remark/coherence/score'] = 'RemarkCoherence/score';
