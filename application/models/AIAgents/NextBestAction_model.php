<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * NextBestAction_model - Agent (additive, 2026-06-06)
 *
 * Computes the recommended next action per lead for a BD, derived from the
 * real init_call pipeline (62k+ rows). No mock data: every row is a live lead.
 *
 * Stage signals (init_call columns, all real):
 *   positive=1                      -> "Send proposal" (move to proposal stage)
 *   proposal_to_be_sent_target=1    -> "Submit proposal now"
 *   verypositive=1                  -> "Schedule closure meeting"
 *   closure=1 / closure_pipeline=1  -> "Push to sign-off"
 *   else (open, cstatus in early)   -> "Make discovery call"
 *
 * Priority score = recency (days since updated_at) + stage weight.
 * Standing rules: ASCII only, "Rs" for rupees, "percent" spelled out.
 */
class NextBestAction_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    /** Probe manifest. */
    public function manifest() {
        $total = (int)$this->db->query("SELECT COUNT(*) c FROM init_call")->row()->c;
        return array(
            'feature'     => 'next_best_action',
            'source_table'=> 'init_call',
            'total_leads' => $total,
            'stages'      => array('discovery','proposal','closure','signoff'),
            'deployed_at' => '2026-06-06',
        );
    }

    /**
     * Recommendations for a BD (uid maps to init_call.mainbd or insidebd).
     * Returns up to $limit prioritized next actions, real leads only.
     */
    public function for_bd($bd_uid, $limit = 25) {
        $bd_uid = (int)$bd_uid;
        $limit  = (int)$limit;
        if ($bd_uid <= 0) {
            // No uid context (system token): return org-wide hottest actions.
            $where = "ic.open = 1";
            $params = array();
        } else {
            $where = "(ic.mainbd = ? OR ic.insidebd = ?) AND ic.open = 1";
            $params = array($bd_uid, $bd_uid);
        }

        $sql = "
            SELECT ic.id AS lead_id,
                   COALESCE(cm.compname, 'Lead #' || ic.id) AS company,
                   ic.cstatus,
                   ic.positive, ic.verypositive, ic.closure, ic.closure_pipeline,
                   ic.proposal_to_be_sent_target,
                   COALESCE(ic.proposal_amt, 0) AS proposal_amt,
                   ic.updated_at,
                   DATEDIFF(NOW(), COALESCE(ic.updated_at, ic.createDate)) AS days_idle
            FROM init_call ic
            LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
            WHERE $where
            ORDER BY days_idle DESC
            LIMIT 400";
        // SQLite-style concat not valid in MySQL; rebuild company expr cleanly.
        $sql = str_replace("COALESCE(cm.compname, 'Lead #' || ic.id)",
                           "COALESCE(NULLIF(cm.compname,''), CONCAT('Lead #', ic.id))", $sql);

        $rows = $this->db->query($sql, $params)->result_array();
        $out = array();
        foreach ($rows as $r) {
            $action = $this->_action_for($r);
            $out[] = array(
                'lead_id'      => (int)$r['lead_id'],
                'company'      => $r['company'],
                'next_action'  => $action['label'],
                'stage'        => $action['stage'],
                'priority'     => $action['weight'] + min((int)$r['days_idle'], 60),
                'days_idle'    => (int)$r['days_idle'],
                'proposal_amt' => (float)$r['proposal_amt'],
                'reason'       => $action['reason'],
            );
        }
        // Sort by priority desc, take top N.
        usort($out, function($a, $b) { return $b['priority'] - $a['priority']; });
        return array_slice($out, 0, $limit);
    }

    private function _action_for($r) {
        if ((int)$r['closure'] === 1 || (int)$r['closure_pipeline'] === 1) {
            return array('label'=>'Push to sign-off', 'stage'=>'signoff', 'weight'=>40,
                         'reason'=>'Lead is in closure pipeline; drive the sign-off.');
        }
        if ((int)$r['verypositive'] === 1) {
            return array('label'=>'Schedule closure meeting', 'stage'=>'closure', 'weight'=>35,
                         'reason'=>'Very positive sentiment; book a closing meeting.');
        }
        if ((int)$r['proposal_to_be_sent_target'] === 1) {
            return array('label'=>'Submit proposal now', 'stage'=>'proposal', 'weight'=>30,
                         'reason'=>'Marked proposal-to-be-sent; submit today.');
        }
        if ((int)$r['positive'] === 1) {
            return array('label'=>'Send proposal', 'stage'=>'proposal', 'weight'=>25,
                         'reason'=>'Positive lead with no proposal yet.');
        }
        return array('label'=>'Make discovery call', 'stage'=>'discovery', 'weight'=>10,
                     'reason'=>'Early-stage open lead; re-engage with a discovery call.');
    }
}
