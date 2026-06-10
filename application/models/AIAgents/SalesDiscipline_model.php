<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * SalesDiscipline_model
 * 
 * Stub model for ExpenseController (ExpenseAccountability controller).
 * Returns empty datasets - no data fabrication.
 * 
 * Created: 2026-05-26 - Schema drift fix (agent_a)
 */
class SalesDiscipline_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_discipline_score($uid = null) {
        return ['ok' => true, 'score' => null, 'note' => 'no_data'];
    }

    public function get_violations($params = []) {
        return [];
    }

    public function log_violation($data = []) {
        return false;
    }

    public function get_band_violations($params = []) {
        return [];
    }
}
