<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * AnayaBriefing
 * application/controllers/AnayaBriefing.php
 *
 * Migration 079.1 - powers the "Open today's briefing" tile.
 * Schema-correct fix 2026-06-08:
 *   - init_call PK is `id` (not cid/cid_id).
 *   - company is company_master, joined via init_call.cmpid_id = company_master.id,
 *     name column = compname. There is NO company_id / cname column on init_call.
 *   - tblcallevents.cid_id references init_call.id.
 *
 * Never fabricates. All counts come from init_call / company_master / tblcallevents.
 */
class AnayaBriefing extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('digest_auth');
    }

    public function probe() {
        return $this->_json(array('ok' => true, 'deployed' => true, 'migration' => '079.1'));
    }

    public function briefing() {
        if (!digest_auth_check($this)) return; // rimlyproof_empty200_20260609: real 401
        $uid = (int) $this->input->get('uid');
        if ($uid <= 0) {
            return $this->_json(array('ok' => false, 'error' => 'uid required'), 400);
        }

        $today_start = date('Y-m-d 00:00:00');
        $today_end   = date('Y-m-d 23:59:59');

        $companies       = 0;
        $open_leads      = 0;
        $active_projects = 0;
        $meetings_today  = 0;
        $tasks_today     = 0;
        $stale_touches   = 0;

        // Companies = distinct company_master rows behind this BD's OPEN leads.
        try {
            $r = $this->db->select('COUNT(DISTINCT cmpid_id) n', false)
                ->from('init_call')->where('mainbd', $uid)
                ->where_not_in('cstatus', array(12, 13, 14))
                ->where('cmpid_id >', 0)
                ->get()->row();
            if ($r) $companies = (int) $r->n;
        } catch (Exception $e) { log_message('error', 'AnayaBriefing.php silent_catch: ' . $e->getMessage()); }

        try {
            $r = $this->db->select('COUNT(*) n', false)
                ->from('init_call')->where('mainbd', $uid)
                ->where_not_in('cstatus', array(12, 13, 14))
                ->get()->row();
            if ($r) $open_leads = (int) $r->n;
        } catch (Exception $e) { log_message('error', 'AnayaBriefing.php silent_catch: ' . $e->getMessage()); }

        try {
            $r = $this->db->select('COUNT(*) n', false)
                ->from('init_call')->where('mainbd', $uid)
                ->where_in('cstatus', array(8, 9, 10, 11))
                ->get()->row();
            if ($r) $active_projects = (int) $r->n;
        } catch (Exception $e) { log_message('error', 'AnayaBriefing.php silent_catch: ' . $e->getMessage()); }

        try {
            $r = $this->db->select('COUNT(*) n', false)
                ->from('tblcallevents')
                ->where('assignedto_id', $uid)
                ->where_in('actiontype_id', array(3, 4))
                ->where('appointmentdatetime >=', $today_start)
                ->where('appointmentdatetime <=', $today_end)
                ->get()->row();
            if ($r) $meetings_today = (int) $r->n;
        } catch (Exception $e) { log_message('error', 'AnayaBriefing.php silent_catch: ' . $e->getMessage()); }

        try {
            $r = $this->db->select('COUNT(*) n', false)
                ->from('tblcallevents')
                ->where('assignedto_id', $uid)
                ->where('appointmentdatetime >=', $today_start)
                ->where('appointmentdatetime <=', $today_end)
                ->get()->row();
            if ($r) $tasks_today = (int) $r->n;
        } catch (Exception $e) { log_message('error', 'AnayaBriefing.php silent_catch: ' . $e->getMessage()); }

        // Stale touches = open leads with no callevent in last 14 days.
        // tblcallevents.cid_id references init_call.id.
        try {
            $cutoff = date('Y-m-d 00:00:00', strtotime('-14 days'));
            $sql = "SELECT COUNT(*) AS n FROM init_call ic
                    WHERE ic.mainbd = ?
                      AND ic.cstatus NOT IN (12,13,14)
                      AND NOT EXISTS (
                          SELECT 1 FROM tblcallevents te
                          WHERE te.cid_id = ic.id
                            AND te.appointmentdatetime >= ?
                      )";
            $q = $this->db->query($sql, array($uid, $cutoff));
            $r = $q ? $q->row() : null;
            if ($r) $stale_touches = (int) $r->n;
        } catch (Exception $e) { log_message('error', 'AnayaBriefing.php silent_catch: ' . $e->getMessage()); }

        $leads_to_push = $this->_leads_to_push($uid, 5);

        $note = ($open_leads === 0)
            ? 'No open leads yet. Run a barge or research today.'
            : 'You have ' . $open_leads . ' open leads. Push the stale ones first.';

        return $this->_json(array(
            'ok'              => true,
            'uid'             => $uid,
            'companies'       => $companies,
            'open_leads'      => $open_leads,
            'active_projects' => $active_projects,
            'meetings_today'  => $meetings_today,
            'tasks_today'     => $tasks_today,
            'stale_touches'   => $stale_touches,
            'leads_to_push'   => $leads_to_push,
            'note'            => $note,
            'generated_at'    => date('c'),
            'migration'       => '079.1',
        ));
    }

    public function leads_to_push() {
        if (!digest_auth_check($this)) return; // rimlyproof_empty200_20260609: real 401
        $uid = (int) $this->input->get('uid');
        if ($uid <= 0) return $this->_json(array('ok' => false, 'error' => 'uid required'), 400);
        return $this->_json(array(
            'ok'     => true,
            'uid'    => $uid,
            'leads'  => $this->_leads_to_push($uid, 10),
        ));
    }

    private function _leads_to_push($uid, $limit = 5) {
        try {
            $sql = "SELECT ic.id AS cid_id, cm.compname AS school, ic.cstatus,
                           (SELECT MAX(te.appointmentdatetime) FROM tblcallevents te
                              WHERE te.cid_id = ic.id) AS last_touch
                    FROM init_call ic
                    LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
                    WHERE ic.mainbd = ?
                      AND ic.cstatus NOT IN (12,13,14)
                    ORDER BY last_touch ASC
                    LIMIT " . (int) $limit;
            $q = $this->db->query($sql, array($uid));
            $rows = $q ? $q->result() : array();
            $out = array();
            $stage_names = array(
                1 => 'Reachout', 2 => 'Reachout', 3 => 'Tentative',
                6 => 'Positive', 8 => 'Open RPEM', 9 => 'Very Positive',
                10 => 'Negotiation', 11 => 'Final',
            );
            foreach ($rows as $r) {
                $out[] = array(
                    'cid_id'     => (int) $r->cid_id,
                    'school'     => ($r->school !== null && $r->school !== '') ? $r->school : ('Lead #' . (int) $r->cid_id),
                    'stage'      => isset($stage_names[$r->cstatus]) ? $stage_names[$r->cstatus] : ('Stage ' . $r->cstatus),
                    'last_touch' => $r->last_touch,
                );
            }
            return $out;
        } catch (Exception $e) {
            return array();
        }
    }

    private function _json($payload, $code = 200) {
        $this->output
            ->set_status_header($code)
            ->set_content_type('application/json')
            ->set_output(json_encode($payload));
    }
}
