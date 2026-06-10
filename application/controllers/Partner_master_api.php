<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Partner_master_api
 * Migration 058 surface.
 * GET /api/partner_master/types?source_only=0  (default 0 = buyer types only)
 */
class Partner_master_api extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        // CORS for the mobile app
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Authorization, Content-Type');
        header('Content-Type: application/json');
    }

    public function types() {
        $source_only = $this->input->get('source_only');
        $sql = "SELECT id, name, clr, is_source_only FROM partner_master";
        if ($source_only === '1') {
            $sql .= " WHERE is_source_only = 1";
        } else if ($source_only === '0' || $source_only === NULL) {
            $sql .= " WHERE is_source_only = 0";
        }
        $sql .= " ORDER BY name ASC";
        $q = $this->db->query($sql);
        echo json_encode(array(
            'ok'    => true,
            'count' => $q->num_rows(),
            'rows'  => $q->result_array(),
        ));
    }
}
