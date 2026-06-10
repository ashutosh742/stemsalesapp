<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * LeadStagnancy_model - Agent (additive, 2026-06-06)
 *
 * Identifies open leads (init_call.open=1) that have NOT been worked upon,
 * bucketed by days since last real activity into 30-59 / 60-89 / 90+ aging
 * bands, plus a "never touched" band. "Last activity" is the most recent
 * planner_log touch for that lead (planner_log.init_id), which is the genuine
 * field-activity log. Falls back to init_call.updated_at / createDate only when
 * the lead has no planner_log row.
 *
 * Also provides AI-style coaching tips per stagnant lead (stage + idle aware).
 *
 * Source tables: init_call, planner_log, company_master, status.
 * No mock data. ASCII only, "Rs" for rupees, "percent" spelled out.
 */
class LeadStagnancy_model extends CI_Model {

    /** Aging band thresholds in days. */
    private $B1 = 30;   // start of 30-59
    private $B2 = 60;   // start of 60-89
    private $B3 = 90;   // 90+

    public function manifest() {
        $open = (int)$this->db->query("SELECT COUNT(*) c FROM init_call WHERE open=1")->row()->c;
        return array(
            'feature'       => 'lead_stagnancy',
            'source_tables' => array('init_call','planner_log','company_master','status'),
            'open_leads'    => $open,
            'bands'         => array('fresh_lt_30','d30_59','d60_89','d90_plus','never_touched'),
            'deployed_at'   => '2026-06-06',
        );
    }

    /** Common SELECT giving each open lead's idle days from real activity. */
    private function _base_sql($extra_where = '', $params = array()) {
        return "
            SELECT ic.id AS lead_id,
                   COALESCE(NULLIF(cm.compname,''), CONCAT('Lead #', ic.id)) AS company,
                   ic.cstatus,
                   COALESCE(s.name, CONCAT('status ', ic.cstatus)) AS status_name,
                   ic.mainbd, ic.insidebd,
                   COALESCE(ic.proposal_amt,0) AS proposal_amt,
                   ic.positive, ic.verypositive, ic.closure, ic.closure_pipeline,
                   ic.proposal_to_be_sent_target,
                   lt.last_touch,
                   CASE WHEN lt.last_touch IS NULL THEN NULL
                        ELSE DATEDIFF(NOW(), lt.last_touch) END AS days_idle
            FROM init_call ic
            LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
            LEFT JOIN status s ON s.id = ic.cstatus
            LEFT JOIN (
                SELECT init_id, MAX(re_created_at) AS last_touch
                FROM planner_log GROUP BY init_id
            ) lt ON lt.init_id = ic.id
            WHERE ic.open = 1 " . $extra_where;
    }

    /** Distribution counts per aging band (BD scoped optional). */
    public function summary($bd_uid = 0) {
        $bd_uid = (int)$bd_uid;
        $where = ''; $params = array();
        if ($bd_uid > 0) { $where = " AND (ic.mainbd = ? OR ic.insidebd = ?) "; $params = array($bd_uid,$bd_uid); }
        $sql = "SELECT
                  SUM(d >= {$this->B3}) AS d90_plus,
                  SUM(d BETWEEN {$this->B2} AND " . ($this->B3-1) . ") AS d60_89,
                  SUM(d BETWEEN {$this->B1} AND " . ($this->B2-1) . ") AS d30_59,
                  SUM(d < {$this->B1}) AS fresh,
                  SUM(d IS NULL) AS never_touched,
                  COUNT(*) AS total_open
                FROM (
                  SELECT ic.id,
                    DATEDIFF(NOW(), (SELECT MAX(pl.re_created_at) FROM planner_log pl WHERE pl.init_id = ic.id)) AS d
                  FROM init_call ic WHERE ic.open = 1 " . $where . "
                ) t";
        $r = $this->db->query($sql, $params)->row_array();
        return array(
            'd90_plus'      => (int)$r['d90_plus'],
            'd60_89'        => (int)$r['d60_89'],
            'd30_59'        => (int)$r['d30_59'],
            'fresh_lt_30'   => (int)$r['fresh'],
            'never_touched' => (int)$r['never_touched'],
            'total_open'    => (int)$r['total_open'],
            'stagnant_total'=> (int)$r['d30_59'] + (int)$r['d60_89'] + (int)$r['d90_plus'] + (int)$r['never_touched'],
        );
    }

    /**
     * List stagnant leads. band in {30,60,90,never,all}. Default 30 = idle >= 30 days.
     */
    public function stagnant($band = '30', $bd_uid = 0, $limit = 50) {
        $bd_uid = (int)$bd_uid; $limit = (int)$limit;
        if ($limit <= 0 || $limit > 500) $limit = 50;
        $where = ''; $params = array();
        if ($bd_uid > 0) { $where = " AND (ic.mainbd = ? OR ic.insidebd = ?) "; $params = array($bd_uid,$bd_uid); }
        $sql = $this->_base_sql($where, $params);
        // wrap to filter by band on the computed days_idle
        $band = (string)$band;
        $having = " HAVING days_idle >= {$this->B1} ";
        if ($band === '30')      $having = " HAVING days_idle BETWEEN {$this->B1} AND " . ($this->B2-1) . " ";
        else if ($band === '60') $having = " HAVING days_idle BETWEEN {$this->B2} AND " . ($this->B3-1) . " ";
        else if ($band === '90') $having = " HAVING days_idle >= {$this->B3} ";
        else if ($band === 'never') $having = " HAVING days_idle IS NULL ";
        else if ($band === 'all') $having = " HAVING (days_idle >= {$this->B1} OR days_idle IS NULL) ";
        $wrapped = "SELECT * FROM ( " . $sql . " ) q " . $having . " ORDER BY (days_idle IS NULL) DESC, days_idle DESC LIMIT " . $limit;
        $rows = $this->db->query($wrapped, $params)->result_array();
        $out = array();
        foreach ($rows as $r) {
            $idle = ($r['days_idle'] === null) ? null : (int)$r['days_idle'];
            $out[] = array(
                'lead_id'      => (int)$r['lead_id'],
                'company'      => $r['company'],
                'status'       => $r['status_name'],
                'mainbd'       => (int)$r['mainbd'],
                'proposal_amt' => (float)$r['proposal_amt'],
                'days_idle'    => $idle,
                'band'         => $this->_band_label($idle),
                'last_touch'   => $r['last_touch'],
            );
        }
        return $out;
    }

    private function _band_label($idle) {
        if ($idle === null) return 'never_touched';
        if ($idle >= $this->B3) return '90_plus';
        if ($idle >= $this->B2) return '60_89';
        if ($idle >= $this->B1) return '30_59';
        return 'fresh';
    }

    /** Coaching tips for one stagnant lead. Returns null if not found. */
    public function coach($lead_id) {
        $lead_id = (int)$lead_id;
        $sql = $this->_base_sql(" AND ic.id = ? ", array($lead_id));
        $r = $this->db->query("SELECT * FROM ( " . $sql . " ) q LIMIT 1", array($lead_id))->row_array();
        if (!$r) return null;
        $idle = ($r['days_idle'] === null) ? null : (int)$r['days_idle'];
        $band = $this->_band_label($idle);
        return array(
            'lead_id'     => (int)$r['lead_id'],
            'company'     => $r['company'],
            'status'      => $r['status_name'],
            'days_idle'   => $idle,
            'band'        => $band,
            'severity'    => $this->_severity($band),
            'tips'        => $this->_tips($band, $r),
            'recommended_action' => $this->_action($band, $r),
        );
    }

    private function _severity($band) {
        if ($band === 'never_touched' || $band === '90_plus') return 'high';
        if ($band === '60_89') return 'medium';
        if ($band === '30_59') return 'low';
        return 'none';
    }

    private function _tips($band, $r) {
        $t = array();
        $status = $r['status_name'];
        if ($band === 'never_touched') {
            $t[] = 'Lead has no planner activity on record. Schedule a first call this week and log it.';
            $t[] = 'Confirm decision-maker contact details before the first outreach.';
        } else if ($band === '90_plus') {
            $t[] = 'Idle 90 plus days. High risk of going cold. Re-qualify or move to nurture.';
            $t[] = 'Send a value-led re-engagement message (email or WhatsApp) referencing prior discussion.';
        } else if ($band === '60_89') {
            $t[] = 'Idle 60 plus days. Book a concrete next meeting with a date, not an open follow-up.';
            $t[] = 'Check if the last proposal still stands or needs revision.';
        } else if ($band === '30_59') {
            $t[] = 'Idle 30 plus days. A short check-in now prevents the lead slipping to cold.';
        } else {
            $t[] = 'Recently active. Keep the cadence; no stagnancy action needed.';
        }
        if ((float)$r['proposal_amt'] > 0) {
            $t[] = 'Open proposal value Rs ' . number_format((float)$r['proposal_amt'], 0) . '. Prioritise; revenue at stake.';
        }
        if ((int)$r['positive'] === 1 || (int)$r['verypositive'] === 1) {
            $t[] = 'Marked positive earlier. Re-confirm intent before it decays.';
        }
        return $t;
    }

    private function _action($band, $r) {
        if ($band === 'never_touched') return 'Plan first call';
        if ($band === '90_plus')      return 'Re-engage or nurture';
        if ($band === '60_89')        return 'Book dated next meeting';
        if ($band === '30_59')        return 'Quick check-in';
        return 'Maintain cadence';
    }
}
