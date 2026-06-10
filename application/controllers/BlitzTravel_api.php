<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * BlitzTravel_api - Agent D, Blitz 30 May 2026
 * File: application/controllers/BlitzTravel_api.php
 *
 * Endpoint:
 *   GET /api/travel/cluster/bd?uid={uid}
 *
 * ============================================================
 * TRAVEL CLUSTER LOGIC
 * ============================================================
 *
 * Groups all OPEN leads for a BD by district/city.
 *
 * Data sources:
 *   - init_call (ic)  -- one row per lead. mainbd = uid.
 *   - company_master (cm) -- joined on ic.cmpid_id.
 *     cm.district is preferred grouping key.
 *     cm.city is fallback when district is NULL or empty.
 *     When both are NULL, group is placed in 'Unknown'.
 *
 * Open lead definition:
 *   cstatus NOT IN (5, 10, 11, 14)
 *   (5=Not Interested, 10=TTD-Reachout, 11=WNO-Reachout, 14=On-Boarded)
 *
 * Pipeline (total_pipeline_rs):
 *   Sum of CAST(ic.fbudget AS UNSIGNED) for the cluster.
 *   Leads with NULL or zero fbudget contribute 0 to this sum.
 *
 * top_school_name:
 *   Name of the lead (company_master.compname) in that cluster
 *   with the highest cstatus value (most qualified lead).
 *   Tie-broken by most recent init_call.id DESC.
 *
 * lead_count:
 *   Count of open leads in that district/city group.
 *
 * Clusters are sorted by lead_count DESC (most work first).
 * ============================================================
 */
class BlitzTravel_api extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    private function _bearer() {
        if (function_exists('authunify_ok') && authunify_ok()) { return true; } // rimlyproof_authunify_20260609

        $h = $this->input->get_request_header('Authorization', TRUE);
        if (!$h || stripos($h, 'Bearer ') !== 0) {
            $this->output->set_status_header(200)->set_content_type('application/json')
                ->set_output(json_encode(['ok' => false, 'error' => 'unauthorized']));
            return false;
        }
        $tok = trim(substr($h, 7));
        $expected = trim(@file_get_contents(APPPATH . 'config/digest_token.txt'));
        if (!$expected || !hash_equals($expected, $tok)) {
            $this->output->set_status_header(200)->set_content_type('application/json')
                ->set_output(json_encode(['ok' => false, 'error' => 'bad_token']));
            return false;
        }
        return true;
    }

    private function _json($payload) {
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($payload));
    }

    /**
     * GET /api/travel/cluster/bd?uid={uid}
     *
     * Groups this BD's open leads by district/city.
     * Returns clusters with lead_count, top_school_name, total_pipeline_rs.
     */
    public function bd() {
        if (!$this->_bearer()) return;

        $uid = (int) $this->input->get('uid');
        if ($uid <= 0) {
            return $this->_json([
                'ok'           => false,
                'success'      => false,
                'stub'         => false,
                'rows'         => [],
                'data'         => ['count' => 0],
                'route'        => 'api/travel/cluster/bd',
                'generated_at' => date('c'),
                'error'        => 'uid is required and must be a positive integer',
            ]);
        }

        // ----------------------------------------------------------
        // Fetch all open leads for this BD with location and budget.
        // We pull individual rows (not a GROUP BY query) so we can
        // do PHP-side grouping and top_school detection cleanly.
        // ----------------------------------------------------------
        $leads = $this->db
            ->select([
                'ic.id AS cid_id',
                'ic.cstatus',
                'CAST(ic.fbudget AS UNSIGNED) AS fbudget_rs',
                'cm.compname',
                'TRIM(cm.district) AS district',
                'TRIM(cm.city) AS city',
            ])
            ->from('init_call ic')
            ->join('company_master cm', 'cm.id = ic.cmpid_id', 'left')
            ->where('ic.mainbd', $uid)
            ->where_not_in('ic.cstatus', [5, 10, 11, 14])
            ->get()->result();

        if (empty($leads)) {
            return $this->_json([
                'ok'           => true,
                'success'      => true,
                'stub'         => false,
                'rows'         => [],
                'data'         => ['count' => 0, 'bd_uid' => $uid],
                'route'        => 'api/travel/cluster/bd',
                'generated_at' => date('c'),
            ]);
        }

        // ----------------------------------------------------------
        // Group leads by district (preferred) else city.
        // Build cluster map: group_key => [leads array]
        // ----------------------------------------------------------
        $clusters = [];
        foreach ($leads as $lead) {
            // Determine group key: district > city > 'Unknown'
            $district = ($lead->district !== null && $lead->district !== '')
                ? $lead->district
                : null;
            $city = ($lead->city !== null && $lead->city !== '')
                ? $lead->city
                : null;

            if ($district) {
                $group_key = $district;
                $group_type = 'district';
            } elseif ($city) {
                $group_key = $city;
                $group_type = 'city';
            } else {
                $group_key = 'Unknown';
                $group_type = 'unknown';
            }

            if (!isset($clusters[$group_key])) {
                $clusters[$group_key] = [
                    'group_key'        => $group_key,
                    'group_type'       => $group_type,
                    'lead_count'       => 0,
                    'total_pipeline_rs' => 0,
                    'top_cstatus'      => -1,
                    'top_school_name'  => '',
                    'cid_ids'          => [],
                ];
            }

            $clusters[$group_key]['lead_count']++;
            $clusters[$group_key]['cid_ids'][] = (int) $lead->cid_id;

            // Add fbudget to pipeline (zero if NULL)
            $bval = (int) ($lead->fbudget_rs);
            if ($bval > 0) {
                $clusters[$group_key]['total_pipeline_rs'] += $bval;
            }

            // Track top school (highest cstatus; tie by last seen = higher cid_id)
            $cs = (int) $lead->cstatus;
            if ($cs > $clusters[$group_key]['top_cstatus']
                || ($cs === $clusters[$group_key]['top_cstatus']
                    && (int)$lead->cid_id > ($clusters[$group_key]['top_cid_id'] ?? 0))) {
                $clusters[$group_key]['top_cstatus']    = $cs;
                $clusters[$group_key]['top_school_name'] = $lead->compname
                    ? trim($lead->compname) : '';
                $clusters[$group_key]['top_cid_id']     = (int) $lead->cid_id;
            }
        }

        // ----------------------------------------------------------
        // Build output rows; strip internal tracking keys.
        // Sort by lead_count DESC.
        // ----------------------------------------------------------
        $rows = [];
        foreach ($clusters as $grp) {
            $rows[] = [
                'group_key'         => $grp['group_key'],
                'group_type'        => $grp['group_type'],
                'lead_count'        => $grp['lead_count'],
                'top_school_name'   => $grp['top_school_name'],
                'total_pipeline_rs' => $grp['total_pipeline_rs'],
            ];
        }

        usort($rows, function($a, $b) {
            return $b['lead_count'] - $a['lead_count'];
        });

        $this->_json([
            'ok'           => true,
            'success'      => true,
            'stub'         => false,
            'rows'         => $rows,
            'data'         => [
                'count'            => count($rows),
                'bd_uid'           => $uid,
                'total_open_leads' => count($leads),
            ],
            'route'        => 'api/travel/cluster/bd',
            'generated_at' => date('c'),
        ]);
    }
}
