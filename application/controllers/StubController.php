<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * StubController
 *
 * Universal stub for API endpoints that have not been built yet.
 * Returns a stable JSON envelope so the mobile app stops crashing on 404s
 * while real controllers are still being implemented per migration plan.
 *
 * Envelope choice:
 *   {"ok":true,"stub":true,"rows":[],"route":"<uri>","note":"endpoint stub - not built yet"}
 *
 * Notes:
 *   - Returns 200 OK (not 404) so client retry logic does not loop.
 *   - "rows":[] keeps any rows[] consumer happy.
 *   - "data":{} also included so success-envelope consumers parse cleanly.
 *   - "stub":true flag lets the app show a "coming soon" placeholder if it wants.
 *   - NO database calls. NO writes. Pure constant response. Zero DB load.
 *
 * Deployed: 30 May 2026 (selfstagingstemapp.in only - read only on production)
 */
class StubController extends CI_Controller {

    public function __construct() {
        parent::__construct();
        // Allow CORS for the mobile app (matches other v2.8 controllers)
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Authorization, Content-Type');
        header('Content-Type: application/json');
    }

    /**
     * Universal handler. Reflects the requested URI so the client can log it.
     * The router maps every missing /api/* route to this one method.
     */
    public function handle() {
        $uri = $this->uri->uri_string();

        $payload = array(
            'ok'      => true,
            'success' => true,           // dual envelope - some clients check 'success'
            'stub'    => true,
            'route'   => $uri,
            'rows'    => array(),
            'data'    => new stdClass(), // empty object, not array
            'count'   => 0,
            'note'    => 'endpoint stub - not built yet (migration pending)',
            'ts'      => date('c'),
        );

        http_response_code(200);
        echo json_encode($payload);
    }

    /**
     * Probe variant. Same envelope but explicitly marks ok:true so
     * orchestrator and crons stop logging 'migration not deployed yet'.
     */
    public function probe() {
        $payload = array(
            'ok'      => true,
            'success' => true,
            'stub'    => true,
            'probe'   => true,
            'route'   => $this->uri->uri_string(),
            'note'    => 'stub probe - real implementation pending',
            'ts'      => date('c'),
        );
        http_response_code(200);
        echo json_encode($payload);
    }
}
