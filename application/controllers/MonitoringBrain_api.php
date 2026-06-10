<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * STEM Learning - Sales Monitoring Brain (READ-ONLY)
 * Built 2026-06-08. Additive only. Never writes. Production never touched.
 *
 * One unifying read layer that stitches the live funnel ledger (init_call),
 * companies (company_master), clusters (cluster / cluster_company_index),
 * owners (auth_user), status labels (status) and partner types
 * (partner_type_master) into ONE master-lead view, then derives the six
 * computations from the Sales Monitoring Mechanism:
 *   funnel | ageing/SLA | proposal readiness | concentration | capacity | data-quality
 *
 * DATA HYGIENE NOTES (verified against live staging 2026-06-08):
 *   - proposal-sent signal = proposaldate is a VALID date (NOT NULL, not the
 *     zero-date 0000-00-00, not empty). The legacy `proposal` text column is
 *     contaminated with email junk and must NOT be used as a flag.
 *   - There is NO reliable per-row money column. `potential` is a yes/no flag,
 *     `proposal_amt` is mostly "NA", `fbudget`/`fbudget_max_cr` are dirty.
 *     So pipeline VALUE is sourced only from the verified 3-source consensus
 *     reconciliation (All-Flags-Consensus), total Rs 665.4 Cr.
 *   - Counts (leads / active / RP-done / proposals-sent / pending / closures)
 *     come straight from cstatus + proposaldate which ARE clean.
 *
 * Strategic flags (Top Spender, Focus Funnel, Upsell, Priority, Key,
 * Potential, Q1 Closure, To-Be-Nurtured) are reported funnel-first with a
 * consensus confidence tier (green=consensus / amber=one-dissenter /
 * red=BD-only) sourced from the 3-source reconciliation.
 *
 * Routes (all GET, all bearer-auth, all read-only):
 *   GET /api/brain/probe
 *   GET /api/brain/kpi                 -> headline KPI tiles
 *   GET /api/brain/scorecard           -> cluster x BD scorecard
 *   GET /api/brain/send_now            -> Send-Now action list (ranked by ageing)
 *   GET /api/brain/bottleneck          -> pending proposals by age + owner
 *   GET /api/brain/concentration       -> lead-count concentration + consensus money
 *   GET /api/brain/capacity            -> live load per BD vs ceiling
 *   GET /api/brain/flags               -> 8 strategic flags w/ consensus tiers
 *   GET /api/brain/data_quality        -> dupes / unknown / AMR-vs-funnel mismatch
 *   GET /api/brain/alerts              -> auto-fired alert rules
 *   GET /api/brain/digest              -> compact bundle for the daily pre-standup digest
 *
 * Common optional filters (query string), applied where meaningful:
 *   cluster=<name>  bd=<uid>  partner_type=<id>  stage=<cstatus>
 *   confidence=consensus|one_dissenter|bd_only  limit=<n>
 *
 * Empty results return {"ok":true,"empty":true, ...correct shape...}.
 * ASCII only. "Rs" for rupees. "percent" spelled out. No em/en dashes.
 */
class MonitoringBrain_api extends CI_Controller
{
    private $token = null;

    // Funnel stage labels (from `status` table). Keyed by init_call.cstatus.
    private $STAGE = array(
        1 => 'Open',           2 => 'Reachout',        3 => 'Tentative',
        4 => 'Will do Later',  5 => 'Not Interested',  6 => 'Positive',
        7 => 'Closure',        8 => 'OPEN RPEM',        9 => 'Very Positive',
        10=> 'TTD-Reachout',   11=> 'WNO-Reachout',     12=> 'Positive-NAP',
        13=> 'Very Positive-NAP', 14=> 'On-Boarded',
    );

    // Stages that count as "RP done" (a real positive meeting happened).
    private $RP_DONE_STAGES = array(6, 9, 12, 13, 7, 14);
    // Stages that count as "active funnel" (not dead/dump/not-interested).
    private $ACTIVE_STAGES  = array(1, 2, 3, 6, 8, 9, 10, 12, 13);
    // Positive stages that should normally have a proposal sent.
    private $POSITIVE_STAGES = array(6, 9, 12, 13);

    // Reusable SQL fragment: proposaldate is a VALID date (proposal sent).
    // Used everywhere a "proposal sent" signal is needed.
    private $PROPDATE_VALID = "(i.proposaldate IS NOT NULL AND i.proposaldate <> '0000-00-00' AND i.proposaldate <> '')";
    private $PROPDATE_MISSING = "(i.proposaldate IS NULL OR i.proposaldate = '0000-00-00' OR i.proposaldate = '')";

    // BD live-load ceiling (capacity guard). Per Mechanism Layer 3.
    private $BD_LOAD_CEILING = 300;
    // Proposal-pending SLA breach threshold (days). Per Mechanism alert rule.
    private $PROPOSAL_SLA_DAYS = 30;
    // RP-done-but-no-proposal nudge threshold (days).
    private $RP_NUDGE_DAYS = 14;

    // Verified consensus pipeline value (Rs Cr) from the 3-source reconciliation.
    private $CONSENSUS_PIPELINE_CR = 665.4;

    // Consensus reconciliation (from All-Flags-Consensus, 3-source check).
    // funnel = BD record, consensus = all sources agree, value in Rs Cr.
    private $FLAG_CONSENSUS = array(
        'top_spender'    => array('label'=>'Top Spender',    'funnel'=>774,  'consensus'=>149, 'one_dissenter'=>1535, 'rev_cr'=>68.2),
        'focus_funnel'   => array('label'=>'Focus Funnel',   'funnel'=>869,  'consensus'=>215, 'one_dissenter'=>1539, 'rev_cr'=>210.0),
        'upsell'         => array('label'=>'Upsell',         'funnel'=>208,  'consensus'=>43,  'one_dissenter'=>1194, 'rev_cr'=>24.9),
        'priority'       => array('label'=>'Priority Client','funnel'=>296,  'consensus'=>197, 'one_dissenter'=>3193, 'rev_cr'=>64.7),
        'key_client'     => array('label'=>'Key Client',     'funnel'=>700,  'consensus'=>356, 'one_dissenter'=>3393, 'rev_cr'=>108.1),
        'potential'      => array('label'=>'Potential Client','funnel'=>2207,'consensus'=>909, 'one_dissenter'=>2921, 'rev_cr'=>167.2),
        'q1_closure'     => array('label'=>'Q1 Closure',     'funnel'=>468,  'consensus'=>31,  'one_dissenter'=>550,  'rev_cr'=>5.8),
        'to_be_nurtured' => array('label'=>'To Be Nurtured', 'funnel'=>586,  'consensus'=>138, 'one_dissenter'=>2770, 'rev_cr'=>16.5),
    );

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library('Bearer_auth');
        $this->load->helper('url');
        $this->token = $this->bearer_auth->get_bearer_token();
    }

    // ----------------------------------------------------------------- helpers
    private function _json($data, $code = 200)
    {
        // Force clean float serialization (e.g. 1.6 not 1.60000000000000008).
        // serialize_precision = -1 makes PHP emit the shortest round-trippable
        // representation, which matches our intended rounded values.
        @ini_set('serialize_precision', '-1');
        @ini_set('precision', '14');
        $this->output
            ->set_status_header($code)
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }

    private function _auth_ok()
    {
        return $this->bearer_auth->verify($this->token);
    }

    private function _deny()
    {
        return $this->_json(array('ok' => false, 'error' => 'unauthorized'), 401);
    }

    // Round to a clean float. With serialize_precision=-1 set in _json(),
    // round() output serializes as the shortest decimal (e.g. 1.6, 665.4).
    private function _money($v, $dp = 2)
    {
        return round((float) $v, $dp);
    }

    private function _pct($num, $den, $dp = 1)
    {
        if ($den <= 0) return 0.0;
        return round(($num / $den) * 100, $dp);
    }

    // Common WHERE filters from query string, applied to the init_call alias `i`
    // and company alias `c`. Returns nothing; mutates $this->db.
    private function _apply_filters()
    {
        $bd = (int) $this->input->get('bd');
        if ($bd > 0) { $this->db->where('i.mainbd', $bd); }

        $stage = $this->input->get('stage');
        if ($stage !== null && $stage !== '') { $this->db->where('i.cstatus', (int)$stage); }

        $cluster = trim((string) $this->input->get('cluster'));
        if ($cluster !== '') { $this->db->where('cl.clustername', $cluster); }

        $pt = (int) $this->input->get('partner_type');
        if ($pt > 0) { $this->db->where('c.partnerType_id', $pt); }
    }

    private function _stage_label($cstatus)
    {
        $c = (int) $cstatus;
        return isset($this->STAGE[$c]) ? $this->STAGE[$c] : 'Unknown';
    }

    // --------------------------------------------------------------- endpoints

    public function probe()
    {
        $this->_json(array(
            'ok' => true,
            'service' => 'monitoring_brain',
            'version' => '1.1',
            'built' => '2026-06-08',
            'read_only' => true,
            'endpoints' => array(
                'kpi','scorecard','send_now','bottleneck','concentration',
                'capacity','flags','data_quality','alerts','digest'
            ),
            'fetched_at' => date('Y-m-d H:i:s'),
        ), 200);
    }

    /**
     * KPI tiles: total leads, active funnel, RP done, proposals sent (valid
     * proposaldate), pending proposals (>SLA), closures. Pipeline VALUE comes
     * from the verified consensus figure, not a dirty row column. Honors filters.
     */
    public function kpi()
    {
        if (!$this->_auth_ok()) return $this->_deny();

        $this->db->from('init_call i')
                 ->join('company_master c', 'c.id = i.cmpid_id', 'left')
                 ->join('cluster cl', 'cl.id = i.cluster_id', 'left');
        $this->_apply_filters();

        $rows = $this->db->select("i.cstatus AS cstatus,
                                    CASE WHEN {$this->PROPDATE_VALID} THEN 1 ELSE 0 END AS prop_sent,
                                    CASE WHEN {$this->PROPDATE_VALID}
                                              AND i.cstatus NOT IN (7,14)
                                              AND DATEDIFF(NOW(), i.proposaldate) > {$this->PROPOSAL_SLA_DAYS}
                                         THEN 1 ELSE 0 END AS pending_breach", false)
                         ->get()->result_array();

        $total = count($rows);
        $active = 0; $rp_done = 0; $prop_sent = 0; $pending = 0; $closures = 0;

        foreach ($rows as $r) {
            $cs = (int) $r['cstatus'];
            if (in_array($cs, $this->ACTIVE_STAGES, true)) $active++;
            if (in_array($cs, $this->RP_DONE_STAGES, true)) $rp_done++;
            if ($cs === 7 || $cs === 14) $closures++;
            if ((int) $r['prop_sent'] === 1) $prop_sent++;
            if ((int) $r['pending_breach'] === 1) $pending++;
        }

        // Is a filter active? If so we cannot attribute the global consensus
        // pipeline value to a subset, so report it as global context only.
        $filtered = ($this->input->get('bd') || $this->input->get('cluster')
                     || $this->input->get('partner_type') || $this->input->get('stage'));

        $this->_json(array(
            'ok' => true,
            'empty' => ($total === 0),
            'tiles' => array(
                'total_leads'        => $total,
                'active_funnel'      => $active,
                'rp_done'            => $rp_done,
                'rp_rate_pct'        => $this->_pct($rp_done, $total),
                'proposals_sent'     => $prop_sent,
                'pending_over_sla'   => $pending,
                'rp_to_proposal_pct' => $this->_pct($prop_sent, $rp_done),
                'closures'           => $closures,
            ),
            'pipeline_value' => array(
                'consensus_pipeline_rs_cr' => $this->_money($this->CONSENSUS_PIPELINE_CR),
                'scope' => $filtered ? 'global (filters do not subset verified value)' : 'global',
                'source' => 'All-Flags-Consensus 3-source reconciliation',
            ),
            'sla_days' => $this->PROPOSAL_SLA_DAYS,
            'filters' => $this->_echo_filters(),
            'fetched_at' => date('Y-m-d H:i:s'),
        ), 200);
    }

    /**
     * Cluster x BD scorecard: leads, RP done, proposals (valid proposaldate),
     * conversion, load.
     */
    public function scorecard()
    {
        if (!$this->_auth_ok()) return $this->_deny();

        $this->db->from('init_call i')
                 ->join('company_master c', 'c.id = i.cmpid_id', 'left')
                 ->join('cluster cl', 'cl.id = i.cluster_id', 'left')
                 ->join('auth_user u', 'u.id = i.mainbd', 'left');
        $this->_apply_filters();

        $rows = $this->db->select("COALESCE(cl.clustername,'(no cluster)') AS cluster,
                                    i.mainbd AS bd_uid,
                                    TRIM(CONCAT(COALESCE(u.first_name,''),' ',COALESCE(u.last_name,''))) AS bd_name,
                                    i.cstatus AS cstatus,
                                    CASE WHEN {$this->PROPDATE_VALID} THEN 1 ELSE 0 END AS prop_sent", false)
                         ->get()->result_array();

        $agg = array();
        foreach ($rows as $r) {
            $key = $r['cluster'] . '||' . $r['bd_uid'];
            if (!isset($agg[$key])) {
                $name = trim((string)$r['bd_name']);
                $agg[$key] = array(
                    'cluster' => $r['cluster'],
                    'bd_uid'  => (int) $r['bd_uid'],
                    'bd_name' => ($name !== '') ? $name : ('uid '.$r['bd_uid']),
                    'leads' => 0, 'rp_done' => 0, 'proposals' => 0,
                );
            }
            $agg[$key]['leads']++;
            $cs = (int) $r['cstatus'];
            if (in_array($cs, $this->RP_DONE_STAGES, true)) $agg[$key]['rp_done']++;
            if ((int) $r['prop_sent'] === 1) $agg[$key]['proposals']++;
        }

        $out = array();
        foreach ($agg as $a) {
            $a['rp_to_proposal_pct'] = $this->_pct($a['proposals'], $a['rp_done']);
            $a['load_vs_ceiling_pct'] = $this->_pct($a['leads'], $this->BD_LOAD_CEILING);
            $out[] = $a;
        }
        usort($out, function($x, $y){ return $y['leads'] - $x['leads']; });

        $limit = (int) $this->input->get('limit');
        if ($limit > 0) $out = array_slice($out, 0, $limit);

        $this->_json(array(
            'ok' => true,
            'empty' => (count($out) === 0),
            'count' => count($out),
            'rows' => $out,
            'filters' => $this->_echo_filters(),
            'fetched_at' => date('Y-m-d H:i:s'),
        ), 200);
    }

    /**
     * Send-Now list: positive-stage leads with no proposal yet (no valid
     * proposaldate), ranked by ageing (oldest positive meeting first = most urgent).
     */
    public function send_now()
    {
        if (!$this->_auth_ok()) return $this->_deny();

        $this->db->from('init_call i')
                 ->join('company_master c', 'c.id = i.cmpid_id', 'left')
                 ->join('cluster cl', 'cl.id = i.cluster_id', 'left')
                 ->join('auth_user u', 'u.id = i.mainbd', 'left')
                 ->where_in('i.cstatus', $this->POSITIVE_STAGES)
                 ->where($this->PROPDATE_MISSING, null, false);
        $this->_apply_filters();

        $rows = $this->db->select("i.id AS cid, c.compname AS company, i.mainbd AS bd_uid,
                                    TRIM(CONCAT(COALESCE(u.first_name,''),' ',COALESCE(u.last_name,''))) AS bd_name,
                                    COALESCE(cl.clustername,'(no cluster)') AS cluster,
                                    i.cstatus AS cstatus,
                                    i.pstadt AS positive_at, i.updated_at AS updated_at,
                                    DATEDIFF(NOW(), COALESCE(NULLIF(i.pstadt,'0000-00-00'), NULLIF(i.updated_at,'0000-00-00'), i.created_at)) AS days_aged", false)
                         ->order_by('days_aged', 'desc')
                         ->get()->result_array();

        $out = array();
        foreach ($rows as $r) {
            $out[] = array(
                'cid' => (int) $r['cid'],
                'company' => $r['company'],
                'bd_uid' => (int) $r['bd_uid'],
                'bd_name' => trim((string)$r['bd_name']) ?: ('uid '.$r['bd_uid']),
                'cluster' => $r['cluster'],
                'stage' => $this->_stage_label($r['cstatus']),
                'days_aged' => (int) $r['days_aged'],
            );
        }

        $limit = (int) $this->input->get('limit'); if ($limit <= 0) $limit = 200;
        $out = array_slice($out, 0, $limit);

        $this->_json(array(
            'ok' => true,
            'empty' => (count($out) === 0),
            'count' => count($out),
            'rows' => $out,
            'note' => 'Positive-stage leads with no proposal sent yet, ranked by ageing.',
            'filters' => $this->_echo_filters(),
            'fetched_at' => date('Y-m-d H:i:s'),
        ), 200);
    }

    /**
     * Bottleneck / SLA: leads where a proposal was sent (valid proposaldate) but
     * the deal has not closed, aged beyond the SLA threshold. Oldest first.
     */
    public function bottleneck()
    {
        if (!$this->_auth_ok()) return $this->_deny();

        $this->db->from('init_call i')
                 ->join('company_master c', 'c.id = i.cmpid_id', 'left')
                 ->join('cluster cl', 'cl.id = i.cluster_id', 'left')
                 ->join('auth_user u', 'u.id = i.mainbd', 'left')
                 ->where($this->PROPDATE_VALID, null, false)
                 ->where_not_in('i.cstatus', array(7, 14));
        $this->_apply_filters();

        $rows = $this->db->select("i.id AS cid, c.compname AS company, i.mainbd AS bd_uid,
                                    TRIM(CONCAT(COALESCE(u.first_name,''),' ',COALESCE(u.last_name,''))) AS bd_name,
                                    COALESCE(cl.clustername,'(no cluster)') AS cluster,
                                    i.cstatus AS cstatus, i.proposaldate AS proposaldate,
                                    DATEDIFF(NOW(), i.proposaldate) AS days_pending", false)
                         ->order_by('days_pending', 'desc')
                         ->get()->result_array();

        $out = array(); $breaches = 0;
        foreach ($rows as $r) {
            $dp = (int) $r['days_pending'];
            $breach = ($dp > $this->PROPOSAL_SLA_DAYS);
            if ($breach) $breaches++;
            $out[] = array(
                'cid' => (int) $r['cid'],
                'company' => $r['company'],
                'bd_uid' => (int) $r['bd_uid'],
                'bd_name' => trim((string)$r['bd_name']) ?: ('uid '.$r['bd_uid']),
                'cluster' => $r['cluster'],
                'stage' => $this->_stage_label($r['cstatus']),
                'proposal_date' => $r['proposaldate'],
                'days_pending' => $dp,
                'sla_breach' => $breach,
            );
        }

        $limit = (int) $this->input->get('limit'); if ($limit <= 0) $limit = 200;
        $out = array_slice($out, 0, $limit);

        $this->_json(array(
            'ok' => true,
            'empty' => (count($out) === 0),
            'count' => count($out),
            'sla_breaches' => $breaches,
            'sla_days' => $this->PROPOSAL_SLA_DAYS,
            'rows' => $out,
            'filters' => $this->_echo_filters(),
            'fetched_at' => date('Y-m-d H:i:s'),
        ), 200);
    }

    /**
     * Concentration risk: there is no clean per-row money column, so we measure
     * concentration by ACTIVE LEAD COUNT share across cluster / BD / partner type
     * (clean signal), and separately surface the verified consensus pipeline
     * VALUE by strategic flag (the only trustworthy money breakdown).
     */
    public function concentration()
    {
        if (!$this->_auth_ok()) return $this->_deny();

        $this->db->from('init_call i')
                 ->join('company_master c', 'c.id = i.cmpid_id', 'left')
                 ->join('cluster cl', 'cl.id = i.cluster_id', 'left')
                 ->join('auth_user u', 'u.id = i.mainbd', 'left')
                 ->join('partner_type_master pt', 'pt.id = c.partnerType_id', 'left')
                 ->where_in('i.cstatus', $this->ACTIVE_STAGES);
        $this->_apply_filters();

        $rows = $this->db->select("COALESCE(cl.clustername,'(no cluster)') AS cluster,
                                    i.mainbd AS bd_uid,
                                    TRIM(CONCAT(COALESCE(u.first_name,''),' ',COALESCE(u.last_name,''))) AS bd_name,
                                    COALESCE(pt.display_name,'(unknown)') AS partner_type", false)
                         ->get()->result_array();

        $by_cluster = array(); $by_bd = array(); $by_pt = array(); $grand = 0;
        foreach ($rows as $r) {
            $grand++;
            $cl = $r['cluster'];
            $bd = trim((string)$r['bd_name']) ?: ('uid '.$r['bd_uid']);
            $pt = $r['partner_type'];
            $by_cluster[$cl] = ($by_cluster[$cl] ?? 0) + 1;
            $by_bd[$bd]      = ($by_bd[$bd] ?? 0) + 1;
            $by_pt[$pt]      = ($by_pt[$pt] ?? 0) + 1;
        }

        $grand_total = ($grand > 0) ? $grand : 1;
        $rank = function($map) use ($grand_total) {
            $out = array();
            foreach ($map as $k => $v) {
                $out[] = array('name' => $k, 'active_leads' => (int)$v,
                               'share_pct' => round(($v / $grand_total) * 100, 1));
            }
            usort($out, function($a, $b){ return $b['active_leads'] - $a['active_leads']; });
            return array_slice($out, 0, 15);
        };

        $top_cluster = $rank($by_cluster);
        $single_point = (count($top_cluster) > 0 && $top_cluster[0]['share_pct'] >= 30.0);

        // Verified money concentration by strategic flag (consensus value).
        $by_flag = array();
        foreach ($this->FLAG_CONSENSUS as $f) {
            $by_flag[] = array(
                'flag' => $f['label'],
                'consensus_rs_cr' => $this->_money($f['rev_cr']),
                'share_of_pipeline_pct' => $this->_pct($f['rev_cr'], $this->CONSENSUS_PIPELINE_CR),
            );
        }
        usort($by_flag, function($a, $b){ return ($b['consensus_rs_cr'] <=> $a['consensus_rs_cr']); });

        $this->_json(array(
            'ok' => true,
            'empty' => ($grand <= 0),
            'basis' => 'active_lead_count',
            'total_active_leads' => $grand,
            'by_cluster' => $top_cluster,
            'by_bd' => $rank($by_bd),
            'by_partner_type' => $rank($by_pt),
            'single_point_risk' => $single_point,
            'pipeline_value_by_flag' => array(
                'total_rs_cr' => $this->_money($this->CONSENSUS_PIPELINE_CR),
                'rows' => $by_flag,
                'source' => 'All-Flags-Consensus 3-source reconciliation',
            ),
            'note' => 'Concentration measured by active lead count (clean signal). Money share is from verified consensus value. Single-point risk flagged when one cluster holds 30 percent or more of active leads.',
            'filters' => $this->_echo_filters(),
            'fetched_at' => date('Y-m-d H:i:s'),
        ), 200);
    }

    /**
     * Capacity & coverage: live leads per active BD vs ceiling.
     */
    public function capacity()
    {
        if (!$this->_auth_ok()) return $this->_deny();

        $this->db->from('init_call i')
                 ->join('auth_user u', 'u.id = i.mainbd', 'left')
                 ->where_in('i.cstatus', $this->ACTIVE_STAGES);
        $this->_apply_filters();

        $rows = $this->db->select("i.mainbd AS bd_uid,
                                    TRIM(CONCAT(COALESCE(u.first_name,''),' ',COALESCE(u.last_name,''))) AS bd_name,
                                    COUNT(*) AS live_leads", false)
                         ->group_by('i.mainbd')
                         ->get()->result_array();

        $out = array(); $over = 0;
        foreach ($rows as $r) {
            $live = (int) $r['live_leads'];
            $overloaded = ($live > $this->BD_LOAD_CEILING);
            if ($overloaded) $over++;
            $out[] = array(
                'bd_uid' => (int) $r['bd_uid'],
                'bd_name' => trim((string)$r['bd_name']) ?: ('uid '.$r['bd_uid']),
                'live_leads' => $live,
                'ceiling' => $this->BD_LOAD_CEILING,
                'load_pct' => $this->_pct($live, $this->BD_LOAD_CEILING),
                'overloaded' => $overloaded,
            );
        }
        usort($out, function($a, $b){ return $b['live_leads'] - $a['live_leads']; });

        $limit = (int) $this->input->get('limit');
        if ($limit > 0) $out = array_slice($out, 0, $limit);

        $this->_json(array(
            'ok' => true,
            'empty' => (count($out) === 0),
            'count' => count($out),
            'overloaded_bds' => $over,
            'ceiling' => $this->BD_LOAD_CEILING,
            'rows' => $out,
            'filters' => $this->_echo_filters(),
            'fetched_at' => date('Y-m-d H:i:s'),
        ), 200);
    }

    /**
     * Strategic flags with consensus confidence tiers. Funnel-first headline,
     * consensus from 3-source reconciliation. Honors confidence filter.
     */
    public function flags()
    {
        if (!$this->_auth_ok()) return $this->_deny();

        $confidence = trim((string) $this->input->get('confidence'));

        $out = array();
        foreach ($this->FLAG_CONSENSUS as $key => $f) {
            $funnel = $f['funnel'];
            $consensus = $f['consensus'];
            $one = $f['one_dissenter'];
            // confidence badge from consensus share of the funnel claim
            $share = ($funnel > 0) ? ($consensus / $funnel) : 0;
            if ($share >= 0.5)      $badge = 'green';
            elseif ($share >= 0.2)  $badge = 'amber';
            else                    $badge = 'red';

            $headline = $funnel;
            if ($confidence === 'consensus')      $headline = $consensus;
            elseif ($confidence === 'one_dissenter') $headline = $one;
            elseif ($confidence === 'bd_only')    $headline = $funnel;

            $out[] = array(
                'key' => $key,
                'label' => $f['label'],
                'headline_count' => $headline,
                'funnel_count' => $funnel,
                'consensus_count' => $consensus,
                'one_dissenter_count' => $one,
                'verified_rs_cr' => $this->_money($f['rev_cr']),
                'confidence' => $badge,
                'consensus_share_pct' => $this->_pct($consensus, $funnel),
            );
        }

        $this->_json(array(
            'ok' => true,
            'empty' => false,
            'count' => count($out),
            'mode' => ($confidence !== '' ? $confidence : 'funnel_first'),
            'rows' => $out,
            'note' => 'Funnel-first headline with consensus confidence badge. Green = all 3 sources agree, amber = one dissenter, red = BD record only.',
            'fetched_at' => date('Y-m-d H:i:s'),
        ), 200);
    }

    /**
     * Data-quality exceptions: dupes (same company name), unknown/blank owner
     * or cluster, and AMR-vs-funnel flag mismatch totals.
     */
    public function data_quality()
    {
        if (!$this->_auth_ok()) return $this->_deny();

        // Duplicate company names among live leads (top offenders)
        $dupes = $this->db->query(
            "SELECT c.compname AS company, COUNT(*) AS n
             FROM init_call i JOIN company_master c ON c.id = i.cmpid_id
             WHERE c.compname IS NOT NULL AND c.compname <> ''
             GROUP BY c.compname HAVING n > 1 ORDER BY n DESC LIMIT 25"
        )->result_array();

        // Leads with no owner
        $no_owner = (int) $this->db->query(
            "SELECT COUNT(*) AS n FROM init_call i WHERE i.mainbd IS NULL OR i.mainbd = 0"
        )->row()->n;

        // Leads with no cluster link
        $no_cluster = (int) $this->db->query(
            "SELECT COUNT(*) AS n FROM init_call i
             LEFT JOIN cluster cl ON cl.id = i.cluster_id
             WHERE cl.id IS NULL"
        )->row()->n;

        // AMR-vs-funnel flag mismatch (from 3-source reconciliation)
        $mismatch = array();
        foreach ($this->FLAG_CONSENSUS as $key => $f) {
            $mismatch[] = array(
                'flag' => $f['label'],
                'funnel_claims' => $f['funnel'],
                'consensus' => $f['consensus'],
                'one_dissenter' => $f['one_dissenter'],
                'needs_reconciliation' => $f['one_dissenter'],
            );
        }

        $this->_json(array(
            'ok' => true,
            'empty' => false,
            'duplicate_company_names' => $dupes,
            'leads_without_owner' => $no_owner,
            'leads_without_cluster' => $no_cluster,
            'flag_mismatch' => $mismatch,
            'note' => 'needs_reconciliation = leads where sources disagree on a strategic flag.',
            'fetched_at' => date('Y-m-d H:i:s'),
        ), 200);
    }

    /**
     * Alerts: auto-fired rules from the Mechanism. Returns counts so the
     * dashboard / digest can lead with blocking items.
     */
    public function alerts()
    {
        if (!$this->_auth_ok()) return $this->_deny();

        // Proposal pending beyond SLA (valid proposaldate, not closed)
        $pending_breach = (int) $this->db->query(
            "SELECT COUNT(*) AS n FROM init_call i
             WHERE {$this->PROPDATE_VALID}
               AND i.cstatus NOT IN (7,14)
               AND DATEDIFF(NOW(), i.proposaldate) > {$this->PROPOSAL_SLA_DAYS}"
        )->row()->n;

        // RP done (positive) but no proposal sent in RP_NUDGE_DAYS days
        $rp_no_prop = (int) $this->db->query(
            "SELECT COUNT(*) AS n FROM init_call i
             WHERE i.cstatus IN (6,9,12,13)
               AND {$this->PROPDATE_MISSING}
               AND DATEDIFF(NOW(), COALESCE(NULLIF(i.pstadt,'0000-00-00'), NULLIF(i.updated_at,'0000-00-00'), i.created_at)) > {$this->RP_NUDGE_DAYS}"
        )->row()->n;

        // BD load above ceiling
        $over = $this->db->query(
            "SELECT i.mainbd AS bd_uid, COUNT(*) AS live FROM init_call i
             WHERE i.cstatus IN (1,2,3,6,8,9,10,12,13) AND i.mainbd IS NOT NULL AND i.mainbd > 0
             GROUP BY i.mainbd HAVING live > {$this->BD_LOAD_CEILING}"
        )->result_array();

        $alerts = array(
            array('rule' => 'proposal_pending_over_sla',
                  'desc' => 'Proposal pending more than '.$this->PROPOSAL_SLA_DAYS.' days - escalate to owner',
                  'count' => $pending_breach, 'severity' => 'high'),
            array('rule' => 'rp_done_no_proposal_'.$this->RP_NUDGE_DAYS.'d',
                  'desc' => 'RP done but no proposal in '.$this->RP_NUDGE_DAYS.' days - nudge BD',
                  'count' => $rp_no_prop, 'severity' => 'medium'),
            array('rule' => 'bd_load_over_ceiling',
                  'desc' => 'BD live-load above ceiling of '.$this->BD_LOAD_CEILING.' - trigger re-assignment',
                  'count' => count($over), 'severity' => 'medium'),
        );

        $total = $pending_breach + $rp_no_prop + count($over);

        $this->_json(array(
            'ok' => true,
            'empty' => ($total === 0),
            'total_alerts' => $total,
            'alerts' => $alerts,
            'overloaded_bds' => $over,
            'fetched_at' => date('Y-m-d H:i:s'),
        ), 200);
    }

    /**
     * Digest: compact bundle for the daily pre-standup recurring task.
     * Top Send-Now items, SLA breach count, alert counts, consensus pipeline.
     */
    public function digest()
    {
        if (!$this->_auth_ok()) return $this->_deny();

        // Top 10 Send-Now by ageing (positive stages, no proposal sent yet)
        $send_now = $this->db->query(
            "SELECT i.id AS cid, c.compname AS company, i.mainbd AS bd_uid,
                    COALESCE(cl.clustername,'(no cluster)') AS cluster,
                    DATEDIFF(NOW(), COALESCE(NULLIF(i.pstadt,'0000-00-00'), NULLIF(i.updated_at,'0000-00-00'), i.created_at)) AS days_aged
             FROM init_call i
             JOIN company_master c ON c.id = i.cmpid_id
             LEFT JOIN cluster cl ON cl.id = i.cluster_id
             WHERE i.cstatus IN (6,9,12,13)
               AND {$this->PROPDATE_MISSING}
             ORDER BY days_aged DESC LIMIT 10"
        )->result_array();

        $send_now_total = (int) $this->db->query(
            "SELECT COUNT(*) AS n FROM init_call i
             WHERE i.cstatus IN (6,9,12,13) AND {$this->PROPDATE_MISSING}"
        )->row()->n;

        $pending_breach = (int) $this->db->query(
            "SELECT COUNT(*) AS n FROM init_call i
             WHERE {$this->PROPDATE_VALID}
               AND i.cstatus NOT IN (7,14)
               AND DATEDIFF(NOW(), i.proposaldate) > {$this->PROPOSAL_SLA_DAYS}"
        )->row()->n;

        $rp_no_prop = (int) $this->db->query(
            "SELECT COUNT(*) AS n FROM init_call i
             WHERE i.cstatus IN (6,9,12,13)
               AND {$this->PROPDATE_MISSING}
               AND DATEDIFF(NOW(), COALESCE(NULLIF(i.pstadt,'0000-00-00'), NULLIF(i.updated_at,'0000-00-00'), i.created_at)) > {$this->RP_NUDGE_DAYS}"
        )->row()->n;

        $this->_json(array(
            'ok' => true,
            'date' => date('Y-m-d'),
            'send_now_top' => $send_now,
            'send_now_total' => $send_now_total,
            'sla_breaches' => $pending_breach,
            'rp_done_no_proposal' => $rp_no_prop,
            'consensus_pipeline_rs_cr' => $this->_money($this->CONSENSUS_PIPELINE_CR),
            'note' => 'Pre-standup monitoring digest. Read-only snapshot.',
            'fetched_at' => date('Y-m-d H:i:s'),
        ), 200);
    }

    private function _echo_filters()
    {
        return array(
            'bd' => (int) $this->input->get('bd'),
            'cluster' => trim((string) $this->input->get('cluster')),
            'partner_type' => (int) $this->input->get('partner_type'),
            'stage' => $this->input->get('stage'),
            'confidence' => trim((string) $this->input->get('confidence')),
        );
    }
}
