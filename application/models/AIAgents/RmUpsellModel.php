<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * RmUpsell_model - Migration 023
 *
 * RM (Regional Manager) ownership of upsell motion across 3 categories:
 *   - PSU (Public Sector Undertaking): KVS, JNV, PMSHRI, Samagra Shiksha, govt tenders
 *   - DMFT (District Mineral Foundation Trust): Odisha, Jharkhand, Chhattisgarh primarily
 *   - ANCHOR (Key/high-budget): existing accounts over Rs 50 lakh OR over 50 labs
 *
 * High-budget cold (non-existing) STAYS with BD - RM does NOT take cold prospecting.
 *
 * Tables:
 *   - rm_upsell_pipeline (cached, refreshed nightly)
 *   - account_category_tag (join key on init_call.id)
 *   - cm_joint_meeting_log (RM also flagged when category PSU/DMFT/Anchor)
 *
 * KPIs (K13-K16, surfaced via line_manager_scorecard_daily):
 *   K13: PSU pipeline touch every 14 days
 *   K14: DMFT cycle visits >=1 per district per quarter
 *   K15: Anchor renewal lock-rate >80 percent
 *   K16: RM-led closure share >30 percent of Won-Rs in PSU+DMFT+Anchor
 */
class RmUpsell_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Return the 3-lane pipeline (PSU/DMFT/ANCHOR) for one RM.
     * Each lane is grouped by upsell_stage (lead|engaged|proposal|negotiation|won|lost).
     */
    public function pipeline_for_rm($rm_uid) {
        $rm_uid = (int)$rm_uid;
        $sql = "
            SELECT
                p.id, p.lead_id, p.category_code, p.upsell_stage, p.fbudget_rs,
                p.last_rm_touch_at, p.last_rm_touch_event_id, p.next_action_due,
                p.touch_count_this_quarter, p.notes,
                DATEDIFF(NOW(), p.last_rm_touch_at) AS days_since_rm_touch,
                ic.compny_nm AS school_name, ic.compny_loction AS location,
                u.first_name AS bd_first, u.last_name AS bd_last, u.uid AS bd_uid
            FROM rm_upsell_pipeline p
            INNER JOIN init_call ic ON ic.id = p.lead_id
            LEFT JOIN user u ON u.uid = ic.mainbd
            WHERE p.rm_uid = ?
              AND p.category_code IN ('PSU','DMFT','ANCHOR')
              AND p.upsell_stage NOT IN ('won','lost')
            ORDER BY
                FIELD(p.category_code,'PSU','ANCHOR','DMFT'),
                FIELD(p.upsell_stage,'negotiation','proposal','engaged','lead'),
                p.fbudget_rs DESC
        ";
        $rows = $this->db->query($sql, array($rm_uid))->result_array();

        $lanes = array('PSU' => array(), 'DMFT' => array(), 'ANCHOR' => array());
        foreach ($rows as $r) {
            $r['stale_touch'] = ($r['days_since_rm_touch'] > 14 && $r['category_code'] === 'PSU') ? 1 : 0;
            $lanes[$r['category_code']][] = $r;
        }
        return $lanes;
    }

    /**
     * One-category view (used by RM kanban filter).
     */
    public function pipeline_by_category($rm_uid, $category_code) {
        $rm_uid = (int)$rm_uid;
        $category_code = strtoupper($category_code);
        if (!in_array($category_code, array('PSU','DMFT','ANCHOR'))) {
            return array();
        }
        $sql = "
            SELECT
                p.*,
                DATEDIFF(NOW(), p.last_rm_touch_at) AS days_since_rm_touch,
                ic.compny_nm AS school_name, ic.compny_loction AS location,
                u.first_name AS bd_first, u.last_name AS bd_last
            FROM rm_upsell_pipeline p
            INNER JOIN init_call ic ON ic.id = p.lead_id
            LEFT JOIN user u ON u.uid = ic.mainbd
            WHERE p.rm_uid = ?
              AND p.category_code = ?
            ORDER BY FIELD(p.upsell_stage,'negotiation','proposal','engaged','lead','won','lost'),
                     p.fbudget_rs DESC
        ";
        return $this->db->query($sql, array($rm_uid, $category_code))->result_array();
    }

    /**
     * Anchor renewals due in next 60 days (used by monthly RM cron + RM dashboard).
     */
    public function anchor_renewals_due($rm_uid = null, $within_days = 60) {
        $within_days = (int)$within_days;
        $params = array($within_days);
        $where = "p.category_code='ANCHOR' AND p.renewal_due_date IS NOT NULL AND p.renewal_due_date <= DATE_ADD(CURDATE(), INTERVAL ? DAY)";
        if ($rm_uid !== null) {
            $where .= " AND p.rm_uid = ?";
            $params[] = (int)$rm_uid;
        }
        $sql = "
            SELECT
                p.id, p.lead_id, p.rm_uid, p.renewal_due_date, p.fbudget_rs,
                p.upsell_stage, p.last_rm_touch_at,
                DATEDIFF(p.renewal_due_date, CURDATE()) AS days_to_renewal,
                ic.compny_nm AS school_name, ic.compny_loction AS location,
                ur.first_name AS rm_first, ur.last_name AS rm_last
            FROM rm_upsell_pipeline p
            INNER JOIN init_call ic ON ic.id = p.lead_id
            LEFT JOIN user ur ON ur.uid = p.rm_uid
            WHERE $where
            ORDER BY p.renewal_due_date ASC
        ";
        return $this->db->query($sql, $params)->result_array();
    }

    /**
     * POST /api/rm_upsell/touch - RM logs a touch on an upsell pipeline row.
     * Auto-pulls latest tblcallevents row for the lead if event_id not given.
     */
    public function log_touch($pipeline_id, $rm_uid, $event_id, $stage, $note) {
        $pipeline_id = (int)$pipeline_id;
        $rm_uid      = (int)$rm_uid;
        $event_id    = (int)$event_id;
        $stage       = strtolower($stage);
        $valid_stages = array('lead','engaged','proposal','negotiation','won','lost');
        if (!in_array($stage, $valid_stages)) {
            return array('ok' => false, 'error' => 'invalid upsell_stage');
        }

        // Update pipeline row
        $upd = array(
            'rm_uid'                  => $rm_uid,
            'upsell_stage'            => $stage,
            'last_rm_touch_at'        => date('Y-m-d H:i:s'),
            'last_rm_touch_event_id'  => $event_id ?: null,
            'notes'                   => $note,
        );
        $upd['touch_count_this_quarter'] = "GREATEST(1, touch_count_this_quarter + 1)";
        // Use raw SQL for the increment
        $this->db->set('rm_uid', $rm_uid)
                 ->set('upsell_stage', $stage)
                 ->set('last_rm_touch_at', date('Y-m-d H:i:s'))
                 ->set('last_rm_touch_event_id', $event_id ?: null)
                 ->set('notes', $note)
                 ->set('touch_count_this_quarter', 'touch_count_this_quarter + 1', FALSE)
                 ->where('id', $pipeline_id)
                 ->update('rm_upsell_pipeline');
        return array(
            'ok'           => true,
            'pipeline_id'  => $pipeline_id,
            'upsell_stage' => $stage,
        );
    }

    /**
     * RM scorecard KPIs (K13-K16). Used by line_manager_scorecard_daily refresh
     * and 0c647bbd section 13.97.
     */
    public function rm_scorecard($rm_uid, $date_yyyymmdd) {
        $rm_uid = (int)$rm_uid;
        $today  = $date_yyyymmdd;

        // K13: PSU pipeline touch every 14 days
        $sql_k13 = "
            SELECT
                SUM(CASE WHEN DATEDIFF(NOW(), last_rm_touch_at) > 14 THEN 1 ELSE 0 END) AS psu_stale,
                COUNT(*) AS psu_total
            FROM rm_upsell_pipeline
            WHERE rm_uid = ? AND category_code='PSU' AND upsell_stage NOT IN ('won','lost')
        ";
        $k13 = $this->db->query($sql_k13, array($rm_uid))->row_array();
        $k13_pct = ($k13['psu_total'] > 0)
            ? round(100 * (1 - ($k13['psu_stale'] / $k13['psu_total'])), 2)
            : 100.0;

        // K14: DMFT quarter visit per district
        $sql_k14 = "
            SELECT COUNT(DISTINCT ic.compny_loction) AS districts_touched
            FROM cm_joint_meeting_log jml
            INNER JOIN init_call ic ON ic.id = jml.lead_id
            INNER JOIN account_category_tag act ON act.lead_id = ic.id
            WHERE jml.rm_uid = ?
              AND act.category_code='DMFT'
              AND jml.meeting_date >= DATE_SUB(?, INTERVAL 90 DAY)
              AND jml.joined='yes'
        ";
        $k14 = $this->db->query($sql_k14, array($rm_uid, $today))->row_array();

        // K15: Anchor renewal lock-rate (last 90 days completed renewals)
        $sql_k15 = "
            SELECT
                SUM(CASE WHEN upsell_stage='won' THEN 1 ELSE 0 END) AS won_renewals,
                COUNT(*) AS due_renewals
            FROM rm_upsell_pipeline
            WHERE rm_uid = ?
              AND category_code='ANCHOR'
              AND renewal_due_date BETWEEN DATE_SUB(?, INTERVAL 90 DAY) AND ?
        ";
        $k15 = $this->db->query($sql_k15, array($rm_uid, $today, $today))->row_array();
        $k15_pct = ($k15['due_renewals'] > 0)
            ? round(100 * $k15['won_renewals'] / $k15['due_renewals'], 2)
            : null;

        // K16: RM-led closure share in PSU+DMFT+ANCHOR (last 90 days)
        $sql_k16 = "
            SELECT
                SUM(CASE WHEN rl.rm_attributed=1 THEN rl.closed_value_rs ELSE 0 END) AS rm_led_rs,
                SUM(rl.closed_value_rs) AS total_rs
            FROM revenue_actual_ledger rl
            INNER JOIN account_category_tag act ON act.lead_id = rl.lead_id
            WHERE act.category_code IN ('PSU','DMFT','ANCHOR')
              AND rl.signoff_at >= DATE_SUB(?, INTERVAL 90 DAY)
              AND rl.cluster_id IN (SELECT cluster_id FROM user WHERE uid = ?)
        ";
        $k16 = $this->db->query($sql_k16, array($today, $rm_uid))->row_array();
        $k16_pct = ($k16['total_rs'] > 0)
            ? round(100 * $k16['rm_led_rs'] / $k16['total_rs'], 2)
            : null;

        return array(
            'rm_uid'                  => $rm_uid,
            'date'                    => $today,
            'k13_psu_freshness_pct'   => $k13_pct,
            'k13_psu_stale_count'     => (int)$k13['psu_stale'],
            'k14_dmft_districts'      => (int)$k14['districts_touched'],
            'k15_anchor_lock_rate_pct'=> $k15_pct,
            'k15_won_renewals'        => (int)$k15['won_renewals'],
            'k15_due_renewals'        => (int)$k15['due_renewals'],
            'k16_rm_led_share_pct'    => $k16_pct,
        );
    }

    /**
     * Headline counts for one RM, used in morning brief 77b08026 per-RM section.
     */
    public function rm_headline($rm_uid) {
        $rm_uid = (int)$rm_uid;
        $sql = "
            SELECT
                category_code,
                COUNT(*) AS active_count,
                SUM(CASE WHEN upsell_stage='negotiation' THEN 1 ELSE 0 END) AS negotiation_count,
                SUM(CASE WHEN DATEDIFF(NOW(), last_rm_touch_at) > 14 THEN 1 ELSE 0 END) AS stale_count,
                SUM(fbudget_rs) AS pipeline_rs
            FROM rm_upsell_pipeline
            WHERE rm_uid = ? AND upsell_stage NOT IN ('won','lost')
            GROUP BY category_code
        ";
        return $this->db->query($sql, array($rm_uid))->result_array();
    }
}

/* End of file RmUpsell_model.php */
/* Location: ./application/models/AIAgents/RmUpsell_model.php */
