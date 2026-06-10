<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// === Agent D Coach routes (29 May 2026) ===
// Controller: v28/CoachV28 (file lives in controllers/v28/CoachV28.php)
// Overrides StubController entries set by routes_404_stubs.php
// Uses plain string format to safely override prior string assignments.

$route['api/coach/ack_overdue']       = 'v28/CoachV28/ack_overdue';
$route['api/coach/candidate_faqs']    = 'v28/CoachV28/candidate_faqs';
$route['api/coach/distribution_gaps'] = 'v28/CoachV28/distribution_gaps';
$route['api/coach/expiring']          = 'v28/CoachV28/expiring';
$route['api/coach/lessons']           = 'v28/CoachV28/lessons';
$route['api/coach/library']           = 'v28/CoachV28/library';
$route['api/coach/unanswered_top']    = 'v28/CoachV28/unanswered_top';
$route['api/coach/whats_new']         = 'v28/CoachV28/whats_new';

// POST write stubs (staging read-only) - also override stub entries
$route['api/coach/knowledge/create']  = 'v28/CoachV28/knowledge_create';
$route['api/coach/knowledge/upload']  = 'v28/CoachV28/knowledge_upload';
$route['api/coach/knowledge_upload']  = 'v28/CoachV28/knowledge_upload_flat';

// === END Agent D Coach routes ===
