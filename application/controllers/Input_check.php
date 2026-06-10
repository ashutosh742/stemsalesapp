<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Input_check - validation PREVIEW endpoint (additive, read-only).
 *
 * Lets the mobile app validate a data-entry payload BEFORE it submits, so
 * garbage (bad amounts, junk remarks, malformed phone/email) can be caught at
 * the source and the user prompted to fix it. This endpoint NEVER writes to the
 * database. It only normalises and reports.
 *
 * Route (class-name-only target, registered in routes_missing_features.php):
 *   POST /api/input_check/validate
 *
 * Auth: Bearer token via BearerAuth library (same as other mobile endpoints).
 *
 * Request body (JSON or form):
 *   {
 *     "form": "proposal" | "lead" | "mom" | "bd_request" | custom,
 *     "data": { field: value, ... }
 *   }
 * If "form" is given, a known field->type spec is used. You may also pass an
 * explicit "spec": { field: type } to validate arbitrary fields. Types:
 *   money | count | phone | email | name | website | remark | mom | text
 *
 * Response:
 *   {
 *     ok: true|false,
 *     blocking: [field,...],
 *     fields: { field: { value, display?, ok, warnings, raw } }
 *   }
 *
 * ASCII only. "Rs" for rupees. No em-dashes.
 */
class Input_check extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library('Inputsanitizer');
        $this->output->set_content_type('application/json');
    }

    /* Known per-form validation specs. Mirrors the dirty fields the write API
     * sanitises so the preview matches what actually gets stored. */
    private function _spec_for($form) {
        $specs = array(
            'lead' => array(
                'compname'      => 'name',
                'contactperson' => 'name',
                'phoneno'       => 'phone',
                'emailid'       => 'email',
                'address'       => 'text',
                'budget'        => 'money',
                'fbudget'       => 'money',
                'proposal_amt'  => 'money',
                'noofschools'   => 'count',
                'website'       => 'website',
            ),
            'proposal' => array(
                'noofsc'    => 'count',
                'pbudgetme' => 'money',
                'remark'    => 'remark',
            ),
            'mom' => array(
                'mom'         => 'mom',
                'mom_text'    => 'mom',
                'mom_remarks' => 'remark',
            ),
            'bd_request' => array(
                'remarks' => 'remark',
            ),
        );
        return isset($specs[$form]) ? $specs[$form] : array();
    }

    private function _body() {
        $raw = file_get_contents('php://input');
        $j = $raw ? json_decode($raw, true) : array();
        if (!is_array($j)) $j = array();
        // Merge POST too, so form-encoded callers work.
        if (!empty($_POST)) $j = array_merge($j, $_POST);
        return $j;
    }

    private function _deny($code, $msg) {
        $this->output->set_status_header($code);
        echo json_encode(array('ok' => false, 'error' => $msg));
    }

    public function validate() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return $this->_deny(405, 'POST only');

        // Bearer auth, consistent with the other mobile endpoints.
        $this->load->library('BearerAuth');
        if (!$this->bearerauth->require_valid_token()) {
            return $this->_deny(401, 'invalid or missing bearer token');
        }

        $body = $this->_body();
        $form = isset($body['form']) ? (string)$body['form'] : '';
        $data = isset($body['data']) && is_array($body['data']) ? $body['data'] : array();

        // Allow caller to pass an explicit spec, else derive from the form name.
        $spec = array();
        if (isset($body['spec']) && is_array($body['spec'])) {
            $spec = $body['spec'];
        } else {
            $spec = $this->_spec_for($form);
        }

        // If neither spec nor known form, validate any data keys as generic text
        // plus best-effort type guessing for common field names.
        if (empty($spec) && !empty($data)) {
            foreach (array_keys($data) as $k) {
                $lk = strtolower($k);
                if (preg_match('/(budget|amt|amount|price|pbudgetme|fbudget)/', $lk)) $spec[$k] = 'money';
                elseif (preg_match('/(noof|count|qty|schools|noofsc)/', $lk))         $spec[$k] = 'count';
                elseif (preg_match('/(phone|mobile|contact_no|phoneno)/', $lk))       $spec[$k] = 'phone';
                elseif (preg_match('/(email|emailid)/', $lk))                          $spec[$k] = 'email';
                elseif (preg_match('/(remark|mom|note|comment)/', $lk))                $spec[$k] = 'remark';
                else                                                                   $spec[$k] = 'text';
            }
        }

        if (empty($spec)) {
            return $this->_deny(400, 'no fields to validate (provide form or spec or data)');
        }

        $result = $this->inputsanitizer->validate_map($data, $spec);
        echo json_encode($result);
    }

    /* Lightweight liveness probe. */
    public function probe() {
        echo json_encode(array(
            'ok'    => true,
            'name'  => 'input_check',
            'forms' => array('lead', 'proposal', 'mom', 'bd_request'),
            'types' => array('money','count','phone','email','name','website','remark','mom','text'),
        ));
    }
}
