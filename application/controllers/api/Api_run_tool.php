<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Api_run_tool
 *
 * Generic dispatcher that lets a mobile stub screen invoke any AIAgents/
 * model method by name, without us shipping a one-off controller per tool.
 * Pattern matches the existing Chat::process() pipeline.
 *
 * Consumed by mobile stub routes:
 *   - /bd-sales-review-v2       -> tool = bd_sales_review_v2
 *   - /all-review-planning      -> tool = all_review_planning
 *   - /annual-review            -> tool = annual_review
 *   - /rm-early-planner-request -> tool = rm_early_planner_request
 *   - /sc-notifications         -> tool = sc_notifications
 *
 * Whitelist below keeps this dispatcher safe: every callable tool is
 * explicit so we never expose an arbitrary AIAgents method to the network.
 *
 * Plain English. Rs for rupees. No em-dashes.
 */
class Api_run_tool extends CI_Controller {

    /** tool name -> [model_load_path, model_property, method] */
    private $tools = [
        'bd_sales_review_v2'       => ['AIAgents/TeamDetails_model',              'teamdetails_model',             'bd_sales_review_v2'],
        'all_review_planning'      => ['AIAgents/TeamDetails_model',              'teamdetails_model',             'all_review_planning'],
        'annual_review'            => ['AIAgents/TeamDetails_model',              'teamdetails_model',             'annual_review'],
        'rm_early_planner_request' => ['AIAgents/FutureTaskDetailsSummary_model', 'futuretaskdetailssummary_model','rm_early_planner_request'],
        'sc_notifications'         => ['AIAgents/TaskAnalysis_model',             'taskanalysis_model',            'sc_notifications'],
    ];

    public function __construct() {
        parent::__construct();
        $this->_check_auth();
    }

    /**
     * POST { tool: "<name>", params: {...} }
     * Returns whatever the underlying AIAgents model method returns, wrapped
     * in a JSON envelope. Unknown tool name returns 400.
     */
    public function run() {
        $body   = json_decode($this->input->raw_input_stream, true);
        $tool   = $body['tool'] ?? '';
        $params = $body['params'] ?? [];
        if (!isset($this->tools[$tool])) {
            return $this->_json(['error' => 'unknown tool', 'tool' => $tool], 400);
        }
        list($load_path, $prop, $method) = $this->tools[$tool];
        $this->load->model($load_path);
        $model = $this->{$prop};
        if (!method_exists($model, $method)) {
            // The migration roadmap may rename methods; fail soft so the
            // mobile stub can show a friendly empty state.
            return $this->_json([
                'error' => 'method not yet implemented',
                'tool'  => $tool,
                'hint'  => 'See migration roadmap for ' . $load_path . '::' . $method,
            ], 501);
        }
        $result = call_user_func([$model, $method], $params);
        $this->_json(['tool' => $tool, 'result' => $result]);
    }

    /** GET list of registered tools so the mobile app can render its menu. */
    public function list_tools() {
        $this->_json(['tools' => array_keys($this->tools)]);
    }

    private function _check_auth() {
        $hdr = $this->input->get_request_header('Authorization');
        if (!$hdr || strpos($hdr, 'Bearer ') !== 0) {
            $this->_json(['error' => 'unauthorized'], 401);
            exit;
        }
    }

    private function _json($data, $status = 200) {
        $this->output
            ->set_status_header($status)
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }
}
