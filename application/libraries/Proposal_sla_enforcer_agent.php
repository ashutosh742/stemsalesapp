<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Proposal_sla_enforcer_agent library stub
 * Created to satisfy ProposalSlaController which loads this library.
 * Returns safe empty responses so the controller never 500s.
 */
class Proposal_sla_enforcer_agent {

    public function __construct() {}

    public function probe() {
        return ['ok' => true, 'status' => 'stub', 'note' => 'no_data'];
    }

    public function mark_proposal_submitted($cid_id, $bd_uid, $doc_url) {
        return ['ok' => true, 'note' => 'no_data'];
    }

    public function grant_extension($sla_id, $bd_uid, $reason) {
        return ['ok' => true, 'note' => 'no_data'];
    }

    public function check_planner_block($bd_uid, $plan_date) {
        return ['ok' => true, 'blocked' => false, 'note' => 'no_data'];
    }

    public function backlog() {
        return ['ok' => true, 'rows' => [], 'note' => 'no_data'];
    }
}
