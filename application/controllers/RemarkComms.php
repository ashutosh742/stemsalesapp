<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * RemarkComms Controller - Agent (additive, 2026-06-06)
 *
 * Routes:
 *   $route['api/remark_comms/probe']      = 'RemarkComms/probe';
 *   $route['api/remark_comms/scan']       = 'RemarkComms/scan';        (?limit=)
 *   $route['api/remark_comms/classify']   = 'RemarkComms/classify';    (?text=)
 *   $route['api/remark_comms/for_lead']   = 'RemarkComms/for_lead';    (?init_id=)
 *   $route['api/remark_comms/queue_email']= 'RemarkComms/queue_email'; (POST)
 */
class RemarkComms extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('AIAgents/RemarkComms_model', 'rc');
        $this->load->library('BearerAuth');
        $this->bearerauth->require_valid_token();
        $this->output->set_content_type('application/json');
    }

    public function probe() {
        echo json_encode(array('ok' => true) + $this->rc->manifest());
    }

    public function scan() {
        $limit = (int)$this->input->get('limit');
        echo json_encode(array('ok'=>true) + $this->rc->scan_catalog($limit));
    }

    public function classify() {
        $text = (string)$this->input->get('text');
        if ($text === '') {
            http_response_code(400);
            echo json_encode(array('ok'=>false, 'error'=>'text required'));
            return;
        }
        echo json_encode(array('ok'=>true, 'text'=>$text, 'result'=>$this->rc->classify($text)));
    }

    public function for_lead() {
        $init_id = (int)$this->input->get('init_id');
        if ($init_id <= 0) {
            http_response_code(400);
            echo json_encode(array('ok'=>false, 'error'=>'init_id required'));
            return;
        }
        $res = $this->rc->for_lead($init_id);
        if ($res === null) {
            http_response_code(404);
            echo json_encode(array('ok'=>false, 'error'=>'lead not found'));
            return;
        }
        echo json_encode(array('ok'=>true, 'lead'=>$res));
    }

    public function queue_email() {
        $auth = $this->bearerauth->resolve();
        $uid  = isset($auth['uid']) ? (int)$auth['uid'] : 0;
        $in   = json_decode(file_get_contents('php://input'), true);
        if (!is_array($in)) $in = $_POST;
        $to    = isset($in['to_email']) ? trim($in['to_email']) : '';
        $subj  = isset($in['subject']) ? trim($in['subject']) : '';
        $body  = isset($in['body']) ? (string)$in['body'] : '';
        $cid   = isset($in['cid_id']) ? (int)$in['cid_id'] : 0;
        if ($to === '' || $subj === '') {
            http_response_code(400);
            echo json_encode(array('ok'=>false, 'error'=>'to_email and subject required'));
            return;
        }
        $qid = $this->rc->queue_email($uid, $cid, $to, $subj, $body);
        echo json_encode(array('ok'=>true, 'queued_id'=>$qid, 'status'=>'queued',
                               'note'=>'Email queued to comm_outbox; the outbox worker delivers it.'));
    }
}
