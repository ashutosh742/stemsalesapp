<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * STEM CRM - Route Brain controller (Migration 069)
 *
 * Endpoints under /api/route_brain/*
 * Auth via Bearer STEM_DIGEST_TOKEN (matches other internal endpoints).
 */
class RouteBrain extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('AIAgents/RouteBrain_model');
        $this->load->library('AgentDispatcher');
        $this->output->set_content_type('application/json');
        $this->_auth();
    }

    // ---- per-user JWT validator (copied from Mobile_read_api 28 May 2026) ----
    private function _jwt_token_valid($token) {
        if (empty($token)) return false;
        $secret = getenv('STEM_DIGEST_TOKEN') ?: '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
        $days = array(date('Y-m-d'), date('Y-m-d', strtotime('-1 day')), date('Y-m-d', strtotime('+1 day')));
        $candidates = array();
        foreach (array('uid','cm_uid','rm_uid','bd_uid','acm_uid','user_id') as $k) {
            if (isset($_GET[$k]) && (int)$_GET[$k] > 0) $candidates[(int)$_GET[$k]] = 1;
            if (isset($_POST[$k]) && (int)$_POST[$k] > 0) $candidates[(int)$_POST[$k]] = 1;
        }
        foreach (array_keys($candidates) as $uid) {
            foreach ($days as $d) {
                if (hash_equals(sha1($secret.'|'.$uid.'|'.$d), $token)) return (int)$uid;
            }
        }
        static $all_uids = null;
        if ($all_uids === null) {
            $rows = $this->db->select('uid')->from('user')->where('active', 1)->get()->result();
            $all_uids = array();
            foreach ($rows as $r) $all_uids[] = (int)$r->uid;
        }
        foreach ($all_uids as $uid) {
            foreach ($days as $d) {
                if (hash_equals(sha1($secret.'|'.$uid.'|'.$d), $token)) return (int)$uid;
            }
        }
        return false;
    }

    private function _auth()
    {
        $hdr = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '';
        if (!$hdr) { $hdr = isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION']) ? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] : ''; }
        if (!$hdr && function_exists('getallheaders')) {
            foreach (getallheaders() as $k => $v) {
                if (strcasecmp($k, 'Authorization') === 0) { $hdr = $v; break; }
            }
        }
        if (strpos($hdr, 'Bearer ') !== 0) {
            // fallback: mobile app session
            $sid = $this->session->userdata('user_id');
            if ((int)$sid > 0) return;
            $this->output->set_status_header(401)->set_output(json_encode(array('ok'=>false,'error'=>'unauthorized')));
            exit;
        }
        $token = trim(substr($hdr, 7));
        $secret = getenv('STEM_DIGEST_TOKEN') ?: '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
        // Accept admin token
        if (hash_equals($secret, $token)) return;
        // Accept per-user JWT
        if ($this->_jwt_token_valid($token)) return;
        // fallback: mobile app session
        $sid = $this->session->userdata('user_id');
        if ((int)$sid > 0) return;
        $this->output->set_status_header(401)->set_output(json_encode(array('ok'=>false,'error'=>'unauthorized')));
        exit;
    }

    public function probe()
    {
        $this->output->set_output(json_encode(['ok'=>true,'migration'=>'069','feature'=>'route_brain']));
    }

    /**
     * GET /api/route_brain/preview?bd_uid=&cluster_id=&plan_date=&time_budget=480&wallet=3000
     */
    public function preview()
    {
        $bd_uid = (int)$this->input->get('bd_uid');
        $cluster_id = (int)$this->input->get('cluster_id');
        $plan_date = $this->input->get('plan_date') ?: date('Y-m-d', strtotime('+1 day'));
        $tb = (int)($this->input->get('time_budget') ?: 480);
        $wallet = (int)($this->input->get('wallet') ?: 3000);

        if (!$bd_uid || !$cluster_id) {
            $this->output->set_status_header(400)->set_output(json_encode(['error'=>'bd_uid and cluster_id required']));
            return;
        }
        $preview = $this->agentdispatcher->on_plan_build($bd_uid, $cluster_id, $plan_date, $tb, $wallet);
        $this->output->set_output(json_encode($preview));
    }

    /**
     * POST /api/route_brain/submit_plan
     * Body: bd_uid, cluster_id, plan_date, preview_json
     */
    public function submit_plan()
    {
        $bd_uid = (int)$this->input->post('bd_uid');
        $cluster_id = (int)$this->input->post('cluster_id');
        $plan_date = $this->input->post('plan_date');
        $preview = json_decode($this->input->post('preview_json'), true);
        if (!$bd_uid || !$cluster_id || !$plan_date || !$preview) {
            $this->output->set_status_header(400)->set_output(json_encode(['error'=>'missing fields']));
            return;
        }
        $plan_id = $this->agentdispatcher->on_plan_submit($bd_uid, $cluster_id, $plan_date, $preview);
        $this->output->set_output(json_encode(['plan_id'=>$plan_id]));
    }

    /**
     * POST /api/route_brain/meeting_end
     * Body: bd_uid, stop_id, mom_id (optional), minutes_left (optional)
     */
    public function meeting_end()
    {
        $bd_uid = (int)$this->input->post('bd_uid');
        $stop_id = (int)$this->input->post('stop_id');
        $mom_id = $this->input->post('mom_id') ? (int)$this->input->post('mom_id') : null;
        $minutes_left = (int)($this->input->post('minutes_left') ?: 120);

        if (!$bd_uid || !$stop_id) {
            $this->output->set_status_header(400)->set_output(json_encode(['error'=>'bd_uid and stop_id required']));
            return;
        }
        $sugs = $this->agentdispatcher->on_meeting_end($bd_uid, $stop_id, $mom_id, $minutes_left);
        $this->output->set_output(json_encode(['suggestions'=>$sugs]));
    }

    /**
     * POST /api/route_brain/walkin_act
     * Body: suggestion_id, action_type (visited/rejected/snoozed/ignored)
     */
    public function walkin_act()
    {
        $sug_id = (int)$this->input->post('suggestion_id');
        $action = $this->input->post('action_type');
        if (!$sug_id || !in_array($action, ['visited','rejected','snoozed','ignored'])) {
            $this->output->set_status_header(400)->set_output(json_encode(['error'=>'bad input']));
            return;
        }
        $this->db->where('id',$sug_id)->update('post_meeting_suggestion',[
            'acted_on'=>1,'action_type'=>$action,'acted_at'=>date('Y-m-d H:i:s')
        ]);
        $this->db->insert('agent_orchestration_log',[
            'trigger_event'=>'walk_in_acted','agent_name'=>'RouteBrain_model',
            'accountability_uid'=>(int)$this->input->post('bd_uid'),
            'input_snapshot_json'=>json_encode(['sug_id'=>$sug_id]),
            'output_snapshot_json'=>json_encode(['action'=>$action]),
            'created_at'=>date('Y-m-d H:i:s'),
        ]);
        $this->output->set_output(json_encode(['ok'=>true]));
    }

    /**
     * POST /api/route_brain/day_close
     * Body: bd_uid, score_date
     */
    public function day_close()
    {
        $bd_uid = (int)$this->input->post('bd_uid');
        $sd = $this->input->post('score_date') ?: date('Y-m-d');
        $sid = $this->agentdispatcher->on_day_close($bd_uid, $sd);
        $this->output->set_output(json_encode(['score_id'=>$sid]));
    }

    /**
     * GET /api/route_brain/efficiency?bd_uid=&from=&to=
     */
    public function efficiency()
    {
        $bd_uid = (int)$this->input->get('bd_uid');
        $from = $this->input->get('from') ?: date('Y-m-d', strtotime('-7 days'));
        $to = $this->input->get('to') ?: date('Y-m-d');
        $rows = $this->db->where('bd_uid',$bd_uid)
                          ->where('score_date >=',$from)->where('score_date <=',$to)
                          ->order_by('score_date','asc')
                          ->get('route_efficiency_score')->result();
        $this->output->set_output(json_encode(['rows'=>$rows]));
    }

    /**
     * GET /api/route_brain/opportunity_vs_execution?cluster_id=
     */
    public function opportunity_vs_execution()
    {
        $cluster_id = $this->input->get('cluster_id');
        $q = $this->db->from('v_opportunity_vs_execution');
        if ($cluster_id) $q->where('cluster_id', (int)$cluster_id);
        $rows = $q->get()->result();
        $this->output->set_output(json_encode(['rows'=>$rows]));
    }

    public function preview_named()
    {
        $bd_uid = (int)$this->input->get('bd_uid');
        $cluster_id = (int)$this->input->get('cluster_id');
        $plan_date = $this->input->get('plan_date') ?: date('Y-m-d', strtotime('+1 day'));
        $tb = (int)($this->input->get('time_budget') ?: 480);
        $wallet = (int)($this->input->get('wallet') ?: 3000);
        if (!$bd_uid || !$cluster_id) {
            $this->output->set_status_header(400)->set_output(json_encode(['error'=>'bd_uid and cluster_id required']));
            return;
        }
        $preview = $this->agentdispatcher->on_plan_build($bd_uid, $cluster_id, $plan_date, $tb, $wallet);
        $comps_raw = $preview['companies'] ?? [];
        // companies may be array of stdClass (from model) or array of arrays
        $ids = [];
        foreach ($comps_raw as $c) {
            $ids[] = is_array($c) ? (int)$c['company_id'] : (int)$c->company_id;
        }
        $names = [];
        if (!empty($ids)) {
            $ids_csv = implode(',', $ids);
            $q = $this->db->query("SELECT id, compname, address, district, state, partnerType_id, comp_business_potential, budget FROM company_master WHERE id IN ($ids_csv)");
            foreach ($q->result() as $r) {
                $names[(int)$r->id] = [
                    'name' => trim((string)$r->compname),
                    'address' => trim((string)$r->address),
                    'district' => trim((string)$r->district),
                    'state' => trim((string)$r->state),
                    'partner_type_id' => (int)$r->partnerType_id,
                    'business_potential' => (string)$r->comp_business_potential,
                    'budget' => (string)$r->budget,
                ];
            }
        }
        $partner_map = [
            1=>'Corporate', 2=>'MNC', 3=>'Foundation', 4=>'PSU', 5=>'Trust', 6=>'Family Office',
            7=>'NGO Partner', 8=>'CSR Implementation', 9=>'Individual', 10=>'Govt Body',
            11=>'Academic', 12=>'Media', 13=>'Other'
        ];
        $cluster_name = '';
        $cq = $this->db->query("SELECT clustername AS cluster_name FROM cluster WHERE id = ?", [$cluster_id]);
        if ($row = $cq->row()) $cluster_name = $row->cluster_name;
        $enriched = [];
        foreach ($comps_raw as $cobj) {
            $c = is_array($cobj) ? $cobj : (array)$cobj;
            $cid = (int)$c['company_id'];
            $meta = $names[$cid] ?? [];
            $pt = (int)$c['partner_type_id'];
            $c['name'] = $meta['name'] ?? ('Company '.$cid);
            $c['address'] = $meta['address'] ?? '';
            $c['district'] = $meta['district'] ?? '';
            $c['state'] = $meta['state'] ?? '';
            $c['budget'] = $meta['budget'] ?? '';
            $c['partner_label'] = $partner_map[$pt] ?? ('Type '.$pt);
            $c['business_potential'] = !empty($meta['business_potential']) ? $meta['business_potential'] : $c['business_potential'];
            $enriched[] = $c;
        }
        $preview['companies'] = $enriched;
        $preview['cluster_name'] = $cluster_name;
        $preview['partner_summary'] = $this->_partner_summary($cluster_id);
        // M070 geofence telemetry block for UI
        $preview['geofence_telemetry'] = $this->_geofence_telemetry($bd_uid, $cluster_id, $enriched);
        $this->output->set_output(json_encode($preview));
    }

    /**
     * M070: geofence telemetry summary used by the UI Efficiency panel.
     * Pulls home anchor status, cluster geocode coverage, today gate pass-rate, login GPS capture state.
     */
    private function _geofence_telemetry($bd_uid, $cluster_id, $companies)
    {
        // Home anchor presence + radius
        $anchor = $this->db->where(['user_id'=>$bd_uid,'anchor_label'=>'home','active'=>1])
                           ->order_by('id','desc')->limit(1)
                           ->get('day_start_home_anchor_v2')->row_array();
        $anchor_set = $anchor ? 1 : 0;
        $anchor_lat = $anchor['lat'] ?? null;
        $anchor_lng = $anchor['lng'] ?? null;
        $anchor_radius_km = $anchor ? (double)($anchor['radius_km'] ?? 5.0) : null;

        // Cluster geocode coverage
        $tot_q = $this->db->query('SELECT COUNT(*) c FROM cluster_company_index WHERE cluster_id=?', [$cluster_id])->row();
        $geo_q = $this->db->query('SELECT COUNT(*) c FROM cluster_company_index WHERE cluster_id=? AND lat IS NOT NULL AND lng IS NOT NULL', [$cluster_id])->row();
        $tot = (int)($tot_q->c ?? 0); $geo = (int)($geo_q->c ?? 0);
        $geo_pct = $tot > 0 ? round(100.0 * $geo / $tot, 1) : 0.0;

        // Today gate pass-rate (7-day average to give signal pre-pilot)
        $gate_q = $this->db->query("SELECT gate_status, COUNT(*) c FROM geofence_gate_log WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) GROUP BY gate_status");
        $counts = ['pass'=>0,'missing'=>0,'out_of_range'=>0,'mocked_time'=>0,'low_accuracy'=>0,'school_ungeocoded'=>0,'anchor_unset'=>0];
        $total = 0;
        foreach ($gate_q->result() as $r) {
            $counts[$r->gate_status] = (int)$r->c;
            $total += (int)$r->c;
        }
        $pass_pct = $total > 0 ? round(100.0 * $counts['pass'] / $total, 1) : null;

        // Login GPS capture state today
        $login_q = $this->db->query('SELECT COUNT(*) c FROM login_session WHERE user_id=? AND lat IS NOT NULL AND DATE(captured_at)=CURDATE()', [$bd_uid])->row();
        $login_captured_today = ($login_q && (int)$login_q->c > 0) ? 1 : 0;

        return [
            'home_anchor_set'      => $anchor_set,
            'home_anchor_lat'      => $anchor_lat,
            'home_anchor_lng'      => $anchor_lng,
            'home_anchor_radius_km'=> $anchor_radius_km,
            'cluster_geocoded_pct' => $geo_pct,
            'cluster_companies_total' => $tot,
            'cluster_companies_geocoded' => $geo,
            'gate_pass_pct_7d'     => $pass_pct,
            'gate_counts_7d'       => $counts,
            'gate_total_7d'        => $total,
            'login_gps_today'      => $login_captured_today,
        ];
    }

    private function _partner_summary($cluster_id)
    {
        $sql = "SELECT cm.partnerType_id, COUNT(*) as cnt FROM cluster_company_index cci JOIN company_master cm ON cm.id = cci.company_id WHERE cci.cluster_id = ? GROUP BY cm.partnerType_id ORDER BY cnt DESC";
        $q = $this->db->query($sql, [$cluster_id]);
        $partner_map = [1=>'Corporate',2=>'MNC',3=>'Foundation',4=>'PSU',5=>'Trust',6=>'Family Office',7=>'NGO',8=>'CSR Impl',9=>'Individual',10=>'Govt',11=>'Academic',12=>'Media',13=>'Other'];
        $out = [];
        foreach ($q->result() as $r) {
            $out[] = ['label' => $partner_map[(int)$r->partnerType_id] ?? ('Type '.$r->partnerType_id), 'count' => (int)$r->cnt];
        }
        return $out;
    }

}
