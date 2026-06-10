<?php
/**
 * STEM CRM v2.8
 * Controller: GetTeamTaskOnSelfOrOtherFunnelTaskLists
 *
 * Audit rows sno 21-34 - 14 funnel drill-down surfaces.
 * All routes share this controller, distinguished by ?list_type query param.
 *
 * Verified schema (29 May 2026):
 *   init_call (l)       - PK id, mainbd (BD owner uid), cmpid_id, cstatus INT,
 *                          createDate, updated_at, fbudget
 *   tblcallevents (ce)  - cid_id -> init_call.id, user_id (BD uid), date,
 *                          actiontype_id, purpose_id, approved_status
 *   company_master (cm) - PK id, compname
 *   user (u)            - PK uid, name, type_id, admin_id (manager link)
 *
 * cstatus integer enum:
 *   1=Open, 2=Reachout, 3=Tentative, 6=Positive, 8=Open RPEM,
 *   9=Very Positive, 12=Won, 13=Lost
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class GetTeamTaskOnSelfOrOtherFunnelTaskLists extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper('url');
    }

    public function probe()
    {
        $this->_json(['ok' => true, 'controller' => get_class($this)]);
    }

    public function drilldown()
    {
        $list_type = trim((string) $this->input->get('list_type', TRUE));
        $bd_uid    = (int) $this->input->get('bd_uid', TRUE);
        $from      = trim((string) $this->input->get('from', TRUE));
        $to        = trim((string) $this->input->get('to', TRUE));

        if ($list_type === '') {
            $this->_json(['ok' => false, 'error' => 'list_type is required']);
            return;
        }

        if ($from !== '' && ! $this->_valid_date($from)) { $from = ''; }
        if ($to   !== '' && ! $this->_valid_date($to))   { $to   = ''; }

        $today = date('Y-m-d');

        // Base SELECT: derive last_touch_date and days_in_stage from
        // tblcallevents via correlated subqueries (alias is referenceable
        // anywhere in the outer query).
        $base_select = "
            l.id            AS cid_id,
            COALESCE(cm.compname, '') AS company_name,
            l.cstatus       AS cstatus,
            l.mainbd        AS bd_uid,
            COALESCE(u.name, '') AS bd_name,
            l.fbudget       AS fbudget,
            DATE(l.createDate) AS create_date,
            (
                SELECT DATE(MAX(ce.date))
                FROM tblcallevents ce
                WHERE ce.cid_id = l.id
            ) AS last_touch_date,
            DATEDIFF(NOW(), COALESCE(l.updated_at, l.createDate)) AS days_in_stage
        ";

        $base_from = "
            init_call l
            LEFT JOIN company_master cm ON cm.id = l.cmpid_id
            LEFT JOIN user u            ON u.uid = l.mainbd
        ";

        $where = '1=1';

        // Helper subquery for team-of-manager filter: BDs whose admin_id is the manager uid
        $team_subq = "(SELECT uid FROM user WHERE admin_id = {$bd_uid})";

        switch ($list_type)
        {
            case 'self_funnel_open':
                $where .= " AND l.cstatus NOT IN (12, 13)";
                if ($bd_uid > 0) { $where .= " AND l.mainbd = {$bd_uid}"; }
                break;

            case 'self_funnel_lost':
                $where .= " AND l.cstatus = 13";
                if ($bd_uid > 0) { $where .= " AND l.mainbd = {$bd_uid}"; }
                break;

            case 'team_funnel_open':
                $where .= " AND l.cstatus NOT IN (12, 13)";
                if ($bd_uid > 0) { $where .= " AND l.mainbd IN {$team_subq}"; }
                break;

            case 'team_funnel_lost':
                $where .= " AND l.cstatus = 13";
                if ($bd_uid > 0) { $where .= " AND l.mainbd IN {$team_subq}"; }
                break;

            case 'status_change_today':
                $where .= " AND DATE(l.updated_at) = '{$today}'";
                if ($bd_uid > 0) { $where .= " AND l.mainbd = {$bd_uid}"; }
                break;

            case 'status_change_week':
                $week_start = date('Y-m-d', strtotime('monday this week'));
                $where .= " AND DATE(l.updated_at) BETWEEN '{$week_start}' AND '{$today}'";
                if ($bd_uid > 0) { $where .= " AND l.mainbd = {$bd_uid}"; }
                break;

            case 'no_activity_3d':
            case 'no_activity_7d':
            case 'no_activity_15d':
                $days = (int) substr($list_type, 12, -1);
                $cutoff = date('Y-m-d', strtotime("-{$days} days"));
                // Open leads where the latest tblcallevents.date is older than cutoff
                // OR no event exists at all.
                $where .= " AND l.cstatus NOT IN (12, 13)
                    AND (
                        (SELECT MAX(ce.date) FROM tblcallevents ce WHERE ce.cid_id = l.id) < '{$cutoff}'
                        OR (SELECT MAX(ce.date) FROM tblcallevents ce WHERE ce.cid_id = l.id) IS NULL
                    )";
                if ($bd_uid > 0) { $where .= " AND l.mainbd = {$bd_uid}"; }
                break;

            case 'stuck_below_positive':
                // Stage below Positive (cstatus < 6), open for at least 7 days
                $where .= " AND l.cstatus NOT IN (6, 9, 12, 13)
                    AND DATEDIFF(NOW(), COALESCE(l.updated_at, l.createDate)) >= 7";
                if ($bd_uid > 0) { $where .= " AND l.mainbd = {$bd_uid}"; }
                break;

            case 'positive_open':
                $where .= " AND l.cstatus = 6";
                if ($bd_uid > 0) { $where .= " AND l.mainbd = {$bd_uid}"; }
                break;

            case 'very_positive_open':
                $where .= " AND l.cstatus = 9";
                if ($bd_uid > 0) { $where .= " AND l.mainbd = {$bd_uid}"; }
                break;

            case 'won_today':
                $where .= " AND l.cstatus = 12 AND DATE(l.updated_at) = '{$today}'";
                if ($bd_uid > 0) { $where .= " AND l.mainbd = {$bd_uid}"; }
                break;

            case 'lost_today':
                $where .= " AND l.cstatus = 13 AND DATE(l.updated_at) = '{$today}'";
                if ($bd_uid > 0) { $where .= " AND l.mainbd = {$bd_uid}"; }
                break;

            default:
                $this->_json(['ok' => false, 'error' => 'unknown list_type']);
                return;
        }

        // Optional date range filter on derived last_touch_date.
        // Because last_touch_date is computed via SELECT subquery, it is NOT
        // available in WHERE - wrap the whole query as a subquery and filter
        // in the outer SELECT.
        $inner_sql = "SELECT {$base_select} FROM {$base_from} WHERE {$where}";

        $outer_where = '';
        if ($from !== '' && $to !== '') {
            $outer_where = "WHERE last_touch_date BETWEEN '{$from}' AND '{$to}'";
        } elseif ($from !== '') {
            $outer_where = "WHERE last_touch_date >= '{$from}'";
        } elseif ($to !== '') {
            $outer_where = "WHERE last_touch_date <= '{$to}'";
        }

        $sql = "SELECT * FROM ({$inner_sql}) AS t {$outer_where} ORDER BY last_touch_date ASC LIMIT 500";

        $query = $this->db->query($sql);
        if ($query === false) {
            $this->_json([
                'ok'        => false,
                'error'     => 'database_error',
                'list_type' => $list_type,
                'rows'      => [],
            ]);
            return;
        }

        $this->_json([
            'ok'        => true,
            'list_type' => $list_type,
            'count'     => $query->num_rows(),
            'rows'      => $query->result_array(),
        ]);
    }

    private function _json(array $payload)
    {
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($payload));
    }

    private function _valid_date($date)
    {
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $date)) { return false; }
        $parts = explode('-', $date);
        return checkdate((int)$parts[1], (int)$parts[2], (int)$parts[0]);
    }
}
