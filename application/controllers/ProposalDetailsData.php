<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * STEM CRM v2.8 - Proposal pipeline (rebuilt to match real schema).
 *
 * actiontype_id = 11 -> Proposal task in tblcallevents.
 * tblcallevents.user_id is the BD owner.
 * tblcallevents.cid_id -> init_call.id -> init_call.cmpid_id -> company_master.id
 */
class ProposalDetailsData extends CI_Controller {

    public function probe() {
        $this->output->set_content_type('application/json')
            ->set_output(json_encode(['ok' => true, 'controller' => 'ProposalDetailsData']));
    }

    private function _init() {
        $this->output->set_content_type('application/json');
        $auth = $this->input->get_request_header('Authorization', TRUE);
        if (empty($auth) || strpos($auth, 'Bearer ') !== 0) {
            http_response_code(401);
            echo json_encode(['ok' => false, 'message' => 'Unauthorized']);
            return false;
        }
        $bd_uid = (int) $this->input->get('bd_uid', TRUE);
        if ($bd_uid <= 0) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'message' => 'bd_uid is required']);
            return false;
        }
        return $bd_uid;
    }

    private function _get_proposals($bd_uid, array $where_extra = []) {
        $sql = "SELECT
                    e.id,
                    e.cid_id,
                    cm.compname AS company_name,
                    e.date      AS planDate,
                    e.complete_time,
                    e.initiate_time,
                    e.purpose_id,
                    e.approved_status,
                    e.actiontype_id
                FROM tblcallevents e
                LEFT JOIN init_call      ic ON ic.id = e.cid_id
                LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
                WHERE e.actiontype_id = 11
                  AND e.user_id = ?";
        $bind = [$bd_uid];
        foreach ($where_extra as $clause) {
            $sql .= " AND " . $clause;
        }
        $sql .= " ORDER BY e.date DESC";
        $q = $this->db->query($sql, $bind);
        return $q ? $q->result_array() : [];
    }

    public function planned_proposal() {
        $bd = $this->_init();   if ($bd === false) return;
        echo json_encode(['ok' => true, 'rows' =>
            $this->_get_proposals($bd, ["e.approved_status = '1'", "e.complete_time IS NULL"])]);
    }
    public function complete_proposal() {
        $bd = $this->_init();   if ($bd === false) return;
        echo json_encode(['ok' => true, 'rows' =>
            $this->_get_proposals($bd, ["e.complete_time IS NOT NULL"])]);
    }
    public function pending_proposal() {
        $bd = $this->_init();   if ($bd === false) return;
        echo json_encode(['ok' => true, 'rows' =>
            $this->_get_proposals($bd, ["e.initiate_time IS NULL"])]);
    }
    public function proposal_approved() {
        $bd = $this->_init();   if ($bd === false) return;
        echo json_encode(['ok' => true, 'rows' =>
            $this->_get_proposals($bd, ["e.approved_status = '2'"])]);
    }
    public function proposal_reject() {
        $bd = $this->_init();   if ($bd === false) return;
        echo json_encode(['ok' => true, 'rows' =>
            $this->_get_proposals($bd, ["e.approved_status = '3'"])]);
    }
    public function pending_for_approved() {
        $bd = $this->_init();   if ($bd === false) return;
        echo json_encode(['ok' => true, 'rows' =>
            $this->_get_proposals($bd, ["e.approved_status = '0'"])]);
    }
    public function proposal_details_main() {
        $bd = $this->_init();   if ($bd === false) return;
        echo json_encode(['ok' => true, 'rows' => $this->_get_proposals($bd, [])]);
    }
}
