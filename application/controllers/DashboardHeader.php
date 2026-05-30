<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * DashboardHeader
 * application/controllers/DashboardHeader.php
 *
 * Migration 079.1 - restores the four header tiles on AgentsHubScreen.
 *
 * Endpoints:
 *   GET /api/dashboard/header_counts?uid=<uid>
 *     Returns { ok, uid, companies, leads, projects, visits, generated_at }
 *
 *   GET /api/dashboard/header_counts/probe
 *     Returns { ok:true, deployed:true } for cron smoke checks.
 *
 * Never fabricates. If a sub-query fails, that key is returned as 0.
 * Counts are derived from existing production tables only:
 *   - init_call (mainbd) for Companies and Open Leads
 *   - tblcallevents (assignedto_id) for Active Projects and Visits this month
 */
class DashboardHeader extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('digest_auth');
    }

    public function probe() {
        return $this->_json(array('ok' => true, 'deployed' => true, 'migration' => '079.1'));
    }

    public function header_counts() {
        if (!digest_auth_check($this)) return;

        $uid = (int) $this->input->get('uid');
        if ($uid <= 0) {
            return $this->_json(array(
                'ok' => false, 'error' => 'uid required',
                'companies' => 0, 'leads' => 0, 'projects' => 0, 'visits' => 0,
            ), 400);
        }

        $companies = 0;
        $leads     = 0;
        $projects  = 0;
        $visits    = 0;

        // Companies = distinct company_id on init_call rows owned by this BD.
        try {
            $row = $this->db
                ->select('COUNT(DISTINCT company_id) AS n', false)
                ->from('init_call')
                ->where('mainbd', $uid)
                ->get()->row();
            if ($row) $companies = (int) $row->n;
        } catch (Exception $e) {}

        // Open Leads = init_call rows where cstatus is not Won/Lost/Dropped.
        // 12 = Won, 13 = Lost, 14 = Dropped (per stem_current_app_logic.md).
        try {
            $row = $this->db
                ->select('COUNT(*) AS n', false)
                ->from('init_call')
                ->where('mainbd', $uid)
                ->where_not_in('cstatus', array(12, 13, 14))
                ->get()->row();
            if ($row) $leads = (int) $row->n;
        } catch (Exception $e) {}

        // Active Projects = leads in advanced stages (RPEM and above, < Won).
        try {
            $row = $this->db
                ->select('COUNT(*) AS n', false)
                ->from('init_call')
                ->where('mainbd', $uid)
                ->where_in('cstatus', array(8, 9, 10, 11))
                ->get()->row();
            if ($row) $projects = (int) $row->n;
        } catch (Exception $e) {}

        // Visits this month = tblcallevents rows for this BD where
        // actiontype is in (3,4) meeting/visit AND appointmentdatetime within this month.
        try {
            $month_start = date('Y-m-01 00:00:00');
            $next_month  = date('Y-m-01 00:00:00', strtotime('+1 month'));
            $row = $this->db
                ->select('COUNT(*) AS n', false)
                ->from('tblcallevents')
                ->where('assignedto_id', $uid)
                ->where_in('actiontype_id', array(3, 4))
                ->where('appointmentdatetime >=', $month_start)
                ->where('appointmentdatetime <',  $next_month)
                ->get()->row();
            if ($row) $visits = (int) $row->n;
        } catch (Exception $e) {}

        return $this->_json(array(
            'ok'           => true,
            'uid'          => $uid,
            'companies'    => $companies,
            'leads'        => $leads,
            'projects'     => $projects,
            'visits'       => $visits,
            'generated_at' => date('c'),
            'migration'    => '079.1',
        ));
    }

    private function _json($payload, $code = 200) {
        $this->output
            ->set_status_header($code)
            ->set_content_type('application/json')
            ->set_output(json_encode($payload));
    }
}
