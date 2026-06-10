<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * STEM CRM - Agent Dispatcher (Migration 069)
 *
 * Single entry point that fires the right agent at the right lifecycle moment
 * and writes accountability rows to agent_orchestration_log.
 *
 * Style: plain English. No em-dashes. No non-ASCII. NEVER fabricate.
 */
class AgentDispatcher
{
    private $CI;
    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->database();
        $this->CI->load->model('AIAgents/RouteBrain_model');
        // Lazy-load others when called.
    }

    /**
     * Fire on plan_build. Returns the cluster preview.
     */
    public function on_plan_build($bd_uid, $cluster_id, $plan_date, $time_budget = 480, $wallet = 3000)
    {
        $t0 = microtime(true);

        // Step 1: Prospect agent refreshes the cluster company index (best effort)
        try { $this->_run_prospect_refresh($bd_uid, $cluster_id); } catch (Throwable $e) {
            $this->_log('plan_build','prospect_refresh',$bd_uid,null,null,null,null,
                ['cluster_id'=>$cluster_id],[],null,'prospect_outer:'.substr($e->getMessage(),0,180));
        }

        // Step 2: Route Brain builds the scored preview
        $preview = $this->CI->RouteBrain_model->preview_cluster_plan($bd_uid, $cluster_id, $plan_date, $time_budget, $wallet);
        $this->_log('plan_build','RouteBrain_model',$bd_uid,null,null,null,null,
            ['cluster_id'=>$cluster_id,'plan_date'=>$plan_date],
            ['stops'=>count($preview['suggested_sequence']),'eff'=>$preview['efficiency_pct']],
            (int)((microtime(true)-$t0)*1000));

        // Step 3: Apollo enrichment - top 3 ranked companies without verified DM (best effort)
        try { $this->_run_apollo_dm_enrich($bd_uid, $preview); } catch (Throwable $e) {
            $this->_log('plan_build','apollo_enrich',$bd_uid,null,null,null,null,
                [],[],null,'apollo_outer:'.substr($e->getMessage(),0,180));
        }

        // Step 4: Planner coach grade (safe heuristic only - no model load to avoid class redeclare)
        try {
            $coach_grade = $this->_run_planner_coach_heuristic($preview);
            if ($coach_grade !== null) $preview['planner_coach_grade'] = $coach_grade;
            $this->_log('plan_build','planner_coach_heuristic',$bd_uid,null,null,null,null,
                ['cluster_id'=>$cluster_id,'plan_date'=>$plan_date],['grade'=>$coach_grade],null);
        } catch (Throwable $e) {
            // Never fail plan_build on coach grade
        }

        return $preview;
    }

    private function _run_prospect_refresh($bd_uid, $cluster_id)
    {
        // Try Corporate CSR prospect agent first (M041), then generic prospect model
        $t = microtime(true);
        try {
            if (file_exists(APPPATH.'models/AIAgents/CorporateCsrProspect_agent.php')) {
                $this->CI->load->model('AIAgents/CorporateCsrProspect_agent');
                $obj = isset($this->CI->corporatecsrprospect_agent) ? $this->CI->corporatecsrprospect_agent
                     : (isset($this->CI->CorporateCsrProspect_agent) ? $this->CI->CorporateCsrProspect_agent : null);
                if ($obj && method_exists($obj, 'refresh_cluster')) {
                    $out = $obj->refresh_cluster($cluster_id);
                    $this->_log('plan_build','CorporateCsrProspect_agent',$bd_uid,null,null,null,null,
                        ['cluster_id'=>$cluster_id],['refreshed'=>$out],
                        (int)((microtime(true)-$t)*1000));
                    return;
                }
            }
            // Log a queued refresh - the controller cron will actually rebuild the index
            $this->_log('plan_build','prospect_refresh_queued',$bd_uid,null,null,null,null,
                ['cluster_id'=>$cluster_id],['queued'=>1],
                (int)((microtime(true)-$t)*1000));
        } catch (Throwable $e) {
            $this->_log('plan_build','prospect_refresh',$bd_uid,null,null,null,null,
                ['cluster_id'=>$cluster_id],[],null,'prospect_refresh_failed:'.substr($e->getMessage(),0,200));
        }
    }

    private function _run_apollo_dm_enrich($bd_uid, $preview)
    {
        // Top 3 ranked companies with no verified DM yet get Apollo enrichment
        $top = array_slice($preview['companies'] ?? [], 0, 3);
        if (empty($top)) return;

        $agent_loaded = false;
        $agent_obj = null;
        if (file_exists(APPPATH.'models/AIAgents/Stem_dm_verify_agent.php')) {
            try {
                $this->CI->load->model('AIAgents/Stem_dm_verify_agent');
                $agent_obj = isset($this->CI->stem_dm_verify_agent) ? $this->CI->stem_dm_verify_agent
                           : (isset($this->CI->Stem_dm_verify_agent) ? $this->CI->Stem_dm_verify_agent : null);
                $agent_loaded = ($agent_obj !== null);
            } catch (Throwable $e) {
                $agent_loaded = false;
            }
        }

        foreach ($top as $c) {
            $cid = is_array($c) ? (int)($c['company_id'] ?? 0) : (int)($c->company_id ?? 0);
            $vdm = is_array($c) ? (int)($c['verified_dm_count'] ?? 0) : (int)($c->verified_dm_count ?? 0);
            if ($cid <= 0) continue;
            if ($vdm > 0) continue;
            $t = microtime(true);
            try {
                if ($agent_loaded && method_exists($agent_obj, 'enrich_company')) {
                    $r = $agent_obj->enrich_company($cid);
                    $this->_log('plan_build','Stem_dm_verify_agent',$bd_uid,null,null,$cid,null,
                        ['company_id'=>$cid],is_array($r)?$r:['ok'=>1],
                        (int)((microtime(true)-$t)*1000));
                } else {
                    // Queue for async enrichment - safe insert only if table exists
                    if ($this->CI->db->table_exists('dm_enrich_queue')) {
                        $this->CI->db->insert('dm_enrich_queue',['company_id'=>$cid,'requested_by'=>$bd_uid,'requested_at'=>date('Y-m-d H:i:s')]);
                    }
                    $this->_log('plan_build','dm_enrich_queue',$bd_uid,null,null,$cid,null,
                        ['company_id'=>$cid],['queued'=>1],
                        (int)((microtime(true)-$t)*1000));
                }
            } catch (Throwable $e) {
                $this->_log('plan_build','Stem_dm_verify_agent',$bd_uid,null,null,$cid,null,
                    ['company_id'=>$cid],[],null,'apollo_failed:'.substr($e->getMessage(),0,200));
            }
        }
    }

    /**
     * Heuristic planner coach grade - no external model loads.
     * Grades A through D based on efficiency and stop count.
     */
    private function _run_planner_coach_heuristic($preview)
    {
        $eff = (int)($preview['efficiency_pct'] ?? 0);
        $stops = count($preview['suggested_sequence'] ?? []);
        if ($eff >= 80 && $stops >= 5) return 'A';
        if ($eff >= 70 && $stops >= 4) return 'B';
        if ($eff >= 50 && $stops >= 3) return 'C';
        return 'D';
    }

    /**
     * Fire on meeting_end. Returns walk-in suggestions.
     */
    public function on_meeting_end($bd_uid, $stop_id, $mom_id = null)
    {
        $t0 = microtime(true);
        $suggestions = $this->CI->RouteBrain_model->suggest_walkins($bd_uid, $stop_id);

        // Persist top suggestions
        $persisted = [];
        foreach (array_slice($suggestions, 0, 3) as $sug) {
            $this->CI->db->insert('route_walkin_suggestion',[
                'after_stop_id'       => $stop_id,
                'suggested_company_id'=> $sug['company_id'],
                'distance_km'         => $sug['distance_km'],
                'drive_minutes'       => $sug['drive_minutes'],
                'priority_score'      => $sug['priority_score'],
                'reason_code'         => $sug['reason_code'],
                'reason_text'         => $sug['reason_text'],
                'suggested_at'        => date('Y-m-d H:i:s'),
            ]);
            $persisted[] = $sug['company_id'];
        }
        $this->_log('meeting_end','RouteBrain_model',$bd_uid,null,$stop_id,null,null,
            ['stop_id'=>$stop_id],['walk_in_count'=>count($persisted)],null);

        // Meeting prep agent (M042) - generate prep for top suggestion (best effort)
        try {
            if (!empty($persisted) && file_exists(APPPATH.'models/AIAgents/CorporateMeetingPrep_model.php')) {
                $this->CI->load->model('AIAgents/CorporateMeetingPrep_model');
                $obj = isset($this->CI->corporatemeetingprep_model) ? $this->CI->corporatemeetingprep_model : null;
                if ($obj && method_exists($obj, 'generate_for_company')) {
                    $prep = $obj->generate_for_company($persisted[0], $bd_uid);
                    $this->_log('meeting_end','CorporateMeetingPrep_model',$bd_uid,null,$stop_id,$persisted[0],null,
                        ['company_id'=>$persisted[0]],['prep_id'=>$prep['id'] ?? null],null);
                }
            }
        } catch (Throwable $e) {}

        // Sentiment agent (M063) - score the MoM we just finished (best effort)
        try {
            if ($mom_id && file_exists(APPPATH.'models/AIAgents/Sentiment_agent.php')) {
                $this->CI->load->model('AIAgents/Sentiment_agent');
                $obj = isset($this->CI->sentiment_agent) ? $this->CI->sentiment_agent : null;
                if ($obj && method_exists($obj, 'record')) {
                    $s = $obj->record($mom_id);
                    $this->_log('meeting_end','Sentiment_agent',$bd_uid,null,$stop_id,null,null,
                        ['mom_id'=>$mom_id],['score'=>$s['score'] ?? null,'at_risk'=>$s['at_risk'] ?? 0],null);
                }
            }
        } catch (Throwable $e) {}

        // Lead followup tracker (M025) - best effort
        try {
            if (file_exists(APPPATH.'models/AIAgents/LeadFollowupTracker_model.php')) {
                $this->CI->load->model('AIAgents/LeadFollowupTracker_model');
                $obj = isset($this->CI->leadfollowuptracker_model) ? $this->CI->leadfollowuptracker_model : null;
                if ($obj && method_exists($obj, 'seed_after_meeting')) {
                    $tasks = $obj->seed_after_meeting($bd_uid, $stop_id);
                    $this->_log('meeting_end','LeadFollowupTracker_model',$bd_uid,null,$stop_id,null,null,
                        ['stop_id'=>$stop_id],['tasks_created'=>$tasks],null);
                }
            }
        } catch (Throwable $e) {}

        return $suggestions;
    }

    /**
     * Fire on day_close. Score execution vs plan.
     */
    public function on_day_close($bd_uid, $score_date)
    {
        $t0 = microtime(true);
        $score_id = $this->CI->RouteBrain_model->score_execution($bd_uid, $score_date);
        $this->_log('day_close','RouteBrain_model',$bd_uid,null,null,null,null,
            ['score_date'=>$score_date],['score_id'=>$score_id],
            (int)((microtime(true)-$t0)*1000));
        return $score_id;
    }

    private function _log($trigger,$agent,$uid,$plan_id=null,$stop_id=null,$company_id=null,$lead_id=null,$in=[],$out=[],$latency_ms=null,$err=null)
    {
        try {
            $this->CI->db->insert('agent_orchestration_log',[
                'trigger_event'       => $trigger,
                'agent_name'          => $agent,
                'accountability_uid'  => (int)$uid,
                'route_plan_id'       => $plan_id,
                'route_stop_id'       => $stop_id,
                'company_id'          => $company_id,
                'lead_id'             => $lead_id,
                'input_snapshot_json' => json_encode($in),
                'output_snapshot_json'=> json_encode($out),
                'latency_ms'          => $latency_ms,
                'error_code'          => $err,
                'created_at'          => date('Y-m-d H:i:s'),
            ]);
        } catch (Throwable $e) {
            // Never fail caller on log insert
        }
    }
}
