<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Relationship_map - M050
 * /api/relationship_map/probe         - liveness check
 * /api/relationship_map/for_lead/:id  - stakeholders for a given lead
 */
class Relationship_map extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        header('Content-Type: application/json');
    }

    private function _out($p) { echo json_encode($p); exit; }

    // GET /api/relationship_map/probe
    public function probe() {
        $this->_out([
            'ok'          => true,
            'controller'  => 'Relationship_map',
            'migration'   => 'M050',
            'status'      => 'ready',
            'server_time' => date('c'),
        ]);
    }

    // GET /api/relationship_map/for_lead?lead_id=<id>
    // Returns BD contacts (users) who have interacted with this lead
    public function for_lead() {
        try {
            $lead_id = (int)$this->input->get('lead_id');
            if (!$lead_id) {
                $this->_out(['ok' => false, 'error' => 'lead_id required']);
            }

            // Lead basics
            $lead = $this->db->query(
                "SELECT ic.id, ic.cmpid_id, ic.mainbd, ic.cstatus,
                        cm.compname AS school,
                        u.name AS mainbd_name
                 FROM init_call ic
                 LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
                 LEFT JOIN user_details u    ON u.user_id = ic.mainbd
                 WHERE ic.id = ?
                 LIMIT 1",
                [$lead_id]
            )->row_array();

            if (!$lead) {
                $this->_out(['ok' => true, 'lead' => null, 'stakeholders' => [], 'count' => 0]);
            }

            // All BD users who have logged events for this lead
            $stakeholders = $this->db->query(
                "SELECT t.user_id, u.name AS bd_name, u.type_id,
                        COUNT(*) AS interactions,
                        MAX(t.date) AS last_interaction,
                        SUM(CASE WHEN t.actiontype_id IN (3,4) THEN 1 ELSE 0 END) AS meetings,
                        SUM(CASE WHEN t.actiontype_id = 1 THEN 1 ELSE 0 END) AS calls
                 FROM tblcallevents t
                 LEFT JOIN user_details u ON u.user_id = t.user_id
                 WHERE t.cid_id = ?
                 GROUP BY t.user_id, u.name, u.type_id
                 ORDER BY interactions DESC
                 LIMIT 30",
                [$lead_id]
            )->result_array();

            $this->_out([
                'ok'          => true,
                'lead'        => $lead,
                'stakeholders'=> $stakeholders,
                'count'       => count($stakeholders),
            ]);
        } catch (Exception $e) {
            $this->_out(['ok' => true, 'rows' => [], 'note' => 'error', 'detail' => $e->getMessage()]);
        }
    }
}
