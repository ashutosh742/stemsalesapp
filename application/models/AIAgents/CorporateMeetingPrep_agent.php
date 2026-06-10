<?php
/**
 * CorporateMeetingPrep_agent  (F61b)
 * Real implementation 2026-06-07. Builds genuine meeting prep from live data:
 *   event -> lead -> company -> recent event history -> DM contact -> latest MOM.
 * Persists a run row to meeting_prep_run (tracking) and returns a full prep
 * artifact. artifact_for_event regenerates fresh from live data (always current).
 *
 * Rules honored: ASCII only ("Rs", "percent"); additive (no schema change);
 * empty data -> correct shape {ok:true, empty:true, ...}; never 5xx for a real user.
 * LLM auto-upgrade: _llm_polish() upgrades the narrative when an OpenAI key is
 * live; deterministic data-driven prep is returned until then.
 */
class CorporateMeetingPrep_agent extends CI_Model {

    private $cstatus_label = array(
        1=>'Open', 2=>'Reachout', 3=>'Tentative', 4=>'Will do Later',
        5=>'Not Interested', 6=>'Positive', 7=>'Closure', 8=>'OPEN RPEM',
        9=>'Very Positive', 10=>'TTD-Reachout', 11=>'WNO-Reachout',
        12=>'Positive-NAP', 13=>'Very Positive-NAP', 14=>'On-Boarded',
    );

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    private function _ascii($s) {
        $s = (string)$s;
        $s = preg_replace('/[^\x20-\x7E\n]/', '', $s);
        $s = preg_replace('/[ \t]+/', ' ', $s);
        return trim($s);
    }

    private function _stage_label($id) {
        $id = (int)$id;
        return isset($this->cstatus_label[$id]) ? $this->cstatus_label[$id] : ('Stage ' . $id);
    }

    private function _row($sql) {
        $prev = $this->db->db_debug; $this->db->db_debug = false;
        $r = $this->db->query($sql); $this->db->db_debug = $prev;
        if (!$r || !is_object($r)) return null;
        $a = $r->row_array();
        return $a ? $a : null;
    }
    private function _rows($sql) {
        $prev = $this->db->db_debug; $this->db->db_debug = false;
        $r = $this->db->query($sql); $this->db->db_debug = $prev;
        if (!$r || !is_object($r)) return array();
        return $r->result_array();
    }

    public function probe() {
        return array("ok" => true, "migration" => "042", "status" => "ready", "note" => "agent_live");
    }

    // Optional LLM narrative upgrade. Returns $fallback unchanged if no key.
    private function _llm_polish($prompt, $fallback) {
        $key = getenv('OPENAI_API_KEY');
        if (!$key) {
            $cfg = APPPATH . 'config/openai.php';
            if (is_file($cfg)) { $c = include $cfg; if (is_array($c) && !empty($c['api_key'])) $key = $c['api_key']; }
        }
        if (!$key) return $fallback;
        try {
            $payload = json_encode(array(
                'model' => 'gpt-4o-mini',
                'messages' => array(
                    array('role'=>'system','content'=>'You are a B2B sales meeting-prep assistant. ASCII only. Use "Rs" and spell out "percent". No markdown headers, plain text.'),
                    array('role'=>'user','content'=>$prompt),
                ),
                'temperature' => 0.3, 'max_tokens' => 500,
            ));
            $ch = curl_init('https://api.openai.com/v1/chat/completions');
            curl_setopt_array($ch, array(
                CURLOPT_RETURNTRANSFER=>true, CURLOPT_POST=>true, CURLOPT_TIMEOUT=>12,
                CURLOPT_HTTPHEADER=>array('Content-Type: application/json','Authorization: Bearer '.$key),
                CURLOPT_POSTFIELDS=>$payload,
            ));
            $resp = curl_exec($ch); $code = curl_getinfo($ch, CURLINFO_HTTP_CODE); curl_close($ch);
            if ($code !== 200 || !$resp) return $fallback;
            $j = json_decode($resp, true);
            $txt = isset($j['choices'][0]['message']['content']) ? trim($j['choices'][0]['message']['content']) : '';
            return $txt !== '' ? $txt : $fallback;
        } catch (\Throwable $e) { return $fallback; }
    }

    // Core: build a real prep artifact for one event_id. Returns assoc array.
    private function _build_prep($event_id) {
        $event_id = (int)$event_id;
        if ($event_id <= 0) return array('ok'=>true, 'empty'=>true, 'event_id'=>$event_id, 'reason'=>'no_event_id');

        // 1. Event + lead + company
        $ev = $this->_row(
            "SELECT e.id AS event_id, e.cid_id, e.user_id AS bd_uid, e.appointmentdatetime, "
          . "e.meeting_type, e.purpose_id, e.actiontype_id, "
          . "c.id AS lead_id, c.cstatus, c.cmpid_id, c.proposaldate, c.proposal_amt, c.fbudget, "
          . "c.dm_contact_name, c.dm_contact_designation, c.dm_contact_phone, c.dm_contact_email, "
          . "cm.compname, cm.city, cm.state, cm.website "
          . "FROM tblcallevents e "
          . "JOIN init_call c ON c.id = e.cid_id "
          . "LEFT JOIN company_master cm ON cm.id = c.cmpid_id "
          . "WHERE e.id = " . $event_id . " LIMIT 1"
        );
        if (!$ev) {
            return array('ok'=>true, 'empty'=>true, 'event_id'=>$event_id, 'reason'=>'event_not_found');
        }

        $lead_id = (int)$ev['lead_id'];
        $company = $this->_ascii(isset($ev['compname']) ? $ev['compname'] : '');
        $stage   = $this->_stage_label($ev['cstatus']);

        // 2. Recent event history (last 5) on this lead. Date columns are
        // sparsely populated; COALESCE across all known date fields.
        $history = $this->_rows(
            "SELECT e.id AS event_id, e.meeting_type, e.mom, e.remarks, "
          . "COALESCE(e.complete_time, e.event_date, e.date, e.appointmentdatetime, e.updateddate) AS touch_at "
          . "FROM tblcallevents e WHERE e.cid_id = " . $lead_id . " "
          . "AND e.id <> " . $event_id . " "
          . "ORDER BY COALESCE(e.complete_time, e.event_date, e.date, e.appointmentdatetime, e.updateddate) DESC LIMIT 5"
        );

        // 3. Latest MOM v2 record for richer context (DM, proposal, win prob)
        $mom = $this->_row(
            "SELECT meeting_purpose_v2, dm_name, dm_designation, dm_phone, "
          . "win_probability, expected_close_date, proposal_value_rs, company_name, cdate "
          . "FROM mom_data WHERE init_cmpid = " . $lead_id . " "
          . "ORDER BY id DESC LIMIT 1"
        );

        // 4. Assemble the DM contact (prefer lead, fall back to MOM)
        $dm_name = $this->_ascii($ev['dm_contact_name']);
        $dm_desg = $this->_ascii($ev['dm_contact_designation']);
        $dm_phone= $this->_ascii($ev['dm_contact_phone']);
        if ($dm_name === '' && $mom && !empty($mom['dm_name']))        $dm_name = $this->_ascii($mom['dm_name']);
        if ($dm_desg === '' && $mom && !empty($mom['dm_designation']))  $dm_desg = $this->_ascii($mom['dm_designation']);
        if ($dm_phone === '' && $mom && !empty($mom['dm_phone']))       $dm_phone = $this->_ascii($mom['dm_phone']);

        // 5. Build talking points from history MOMs + stage
        $points = array();
        foreach ($history as $h) {
            $m = $this->_ascii(isset($h['mom']) ? $h['mom'] : '');
            if ($m !== '' && strlen($m) > 12) { $points[] = $m; }
            if (count($points) >= 3) break;
        }

        // 6. Compose deterministic briefing text
        $when = $this->_ascii($ev['appointmentdatetime']);
        $lines = array();
        $lines[] = "MEETING PREP - " . ($company !== '' ? $company : ('Lead ' . $lead_id));
        if ($when !== '') $lines[] = "Scheduled: " . $when;
        $loc = trim($this->_ascii($ev['city']) . ($ev['state'] ? (', ' . $this->_ascii($ev['state'])) : ''));
        if ($loc !== '') $lines[] = "Location: " . $loc;
        $lines[] = "Current Stage: " . $stage;
        if ($dm_name !== '') {
            $dmline = "Decision Maker: " . $dm_name;
            if ($dm_desg !== '') $dmline .= " (" . $dm_desg . ")";
            if ($dm_phone !== '') $dmline .= " - " . $dm_phone;
            $lines[] = $dmline;
        }
        if ($mom) {
            if (!empty($mom['win_probability']))     $lines[] = "Win probability: " . (int)$mom['win_probability'] . " percent";
            if (!empty($mom['proposal_value_rs']))   $lines[] = "Proposal value: Rs " . number_format((float)$mom['proposal_value_rs'], 0);
            if (!empty($mom['expected_close_date'])) $lines[] = "Expected close: " . $this->_ascii($mom['expected_close_date']);
        }
        // clean fbudget if present and numeric
        if (!empty($ev['fbudget']) && preg_match('/^[0-9]+(\.[0-9]+)?$/', (string)$ev['fbudget'])) {
            $lines[] = "Budget on file: Rs " . number_format((float)$ev['fbudget'], 0);
        }
        $lines[] = "";
        $lines[] = "RECENT HISTORY (" . count($history) . " prior touches)";
        if ($history) {
            foreach ($history as $h) {
                $d = $this->_ascii(!empty($h['touch_at']) ? $h['touch_at'] : 'date n/a');
                $r = $this->_ascii($h['remarks'] ? $h['remarks'] : ($h['mom'] ? $h['mom'] : 'Touch logged'));
                if (strlen($r) > 160) $r = substr($r, 0, 157) . '...';
                $lines[] = "- " . $d . ": " . $r;
            }
        } else {
            $lines[] = "- No prior events logged on this lead.";
        }
        $lines[] = "";
        $lines[] = "TALKING POINTS";
        if ($points) {
            foreach ($points as $p) {
                if (strlen($p) > 160) $p = substr($p, 0, 157) . '...';
                $lines[] = "- " . $p;
            }
        } else {
            $lines[] = "- Confirm current requirement and budget owner.";
            $lines[] = "- Reconfirm timeline and next milestone.";
            $lines[] = "- Identify any open objections from prior visits.";
        }
        $lines[] = "";
        $lines[] = "SUGGESTED OBJECTIVE";
        $lines[] = "- Advance from " . $stage . " toward the next stage with a clear committed next step.";

        $briefing = implode("\n", $lines);
        $briefing = $this->_llm_polish(
            "Polish this corporate meeting-prep briefing, keep every fact and the section labels "
          . "(RECENT HISTORY / TALKING POINTS / SUGGESTED OBJECTIVE):\n\n" . $briefing,
            $briefing
        );

        return array(
            'ok' => true,
            'event_id' => $event_id,
            'lead_id' => $lead_id,
            'company' => $company,
            'stage' => $stage,
            'scheduled_at' => $when,
            'decision_maker' => array('name'=>$dm_name, 'designation'=>$dm_desg, 'phone'=>$dm_phone),
            'history_count' => count($history),
            'briefing' => $briefing,
        );
    }

    public function generate_for_event($event_id, $trigger = "on_demand") {
        $event_id = (int)$event_id;
        $prep = $this->_build_prep($event_id);
        if (empty($prep['ok']) || !empty($prep['empty'])) {
            return $prep; // correct empty shape, no persistence
        }

        // Persist a run row for tracking (status completed). Never fatal.
        try {
            $now = date('Y-m-d H:i:s');
            $bd  = 0;
            $evrow = $this->_row("SELECT user_id FROM tblcallevents WHERE id = " . $event_id . " LIMIT 1");
            if ($evrow && isset($evrow['user_id'])) $bd = (int)$evrow['user_id'];
            $prev = $this->db->db_debug; $this->db->db_debug = false;
            $this->db->insert('meeting_prep_run', array(
                'triggered_by_uid' => $bd,
                'init_call_id'     => (int)$prep['lead_id'],
                'cmpid_id'         => null,
                'status'           => 'completed',
                'started_at'       => $now,
                'completed_at'     => $now,
            ));
            $run_id = (int)$this->db->insert_id();
            if ($run_id > 0) {
                $this->db->insert('meeting_prep_artifact', array(
                    'meeting_prep_run_id' => $run_id,
                    'artifact_type'       => 'briefing_text',
                    'rel_path'            => 'inline:event_' . $event_id,
                ));
            }
            $this->db->db_debug = $prev;
            $prep['run_id'] = $run_id;
        } catch (\Throwable $e) {
            log_message('error', 'CorporateMeetingPrep_agent::generate persist: ' . $e->getMessage());
        }
        $prep['trigger'] = $this->_ascii($trigger);
        return $prep;
    }

    public function auto_scan($lookahead = 150, $cap = 20) {
        // Find upcoming events in the next $lookahead days and generate prep.
        $lookahead = max(1, min(365, (int)$lookahead));
        $cap = max(1, min(50, (int)$cap));
        $rows = $this->_rows(
            "SELECT e.id FROM tblcallevents e JOIN init_call c ON c.id = e.cid_id "
          . "WHERE e.appointmentdatetime BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL " . $lookahead . " DAY) "
          . "AND (c.deletebd IS NULL OR c.deletebd = 0) "
          . "ORDER BY e.appointmentdatetime ASC LIMIT " . $cap
        );
        $done = 0;
        foreach ($rows as $r) {
            $res = $this->generate_for_event((int)$r['id'], 'auto_scan');
            if (!empty($res['ok']) && empty($res['empty'])) $done++;
        }
        return array('ok'=>true, 'scanned'=>count($rows), 'generated'=>$done,
                     'lookahead_days'=>$lookahead, 'reason'=> count($rows)===0 ? 'no_rows' : null);
    }

    // Always regenerate fresh from live data so the artifact is current.
    public function artifact_for_event($event_id) {
        return $this->_build_prep((int)$event_id);
    }

    public function runs_today($bd_uid = null) {
        try {
            if ($this->db->table_exists("meeting_prep_run")) {
                $today = date("Y-m-d");
                $q = $this->db->select("*")->from("meeting_prep_run")
                    ->where("DATE(started_at) =", $today)
                    ->order_by("started_at", "DESC")->limit(50);
                if ($bd_uid) { $q->where("triggered_by_uid", (int)$bd_uid); }
                $rows = $q->get()->result_array();
                return array("ok" => true, "date" => $today, "count" => count($rows), "runs" => $rows,
                             "reason" => count($rows) === 0 ? "no_rows" : null);
            }
        } catch (Exception $e) {
            log_message("error", "CorporateMeetingPrep_agent::runs_today: " . $e->getMessage());
        }
        return array("ok" => true, "date" => date("Y-m-d"), "count" => 0, "runs" => array(), "reason" => "no_rows");
    }
}
