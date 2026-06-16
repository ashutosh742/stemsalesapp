<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Mobile_status_api Controller  (ADDITIVE - staging fix Area A, 2026-06-10)
 *
 * GET /api/status/transitions?cstatus=<id>[&cmm_init_id=<init_call id>]
 *
 * READ-ONLY mirror of Menu::getstatusbd() (Menu.php ~line 3985), the production
 * status picker that decides which ystatus values are LEGAL next-statuses for a
 * task's current cstatus. Mobile previously offered a FLAT 13-item list with the
 * wrong labels; this endpoint returns ONLY the legal transitions so the mobile
 * outcome picker matches production exactly.
 *
 * PRODUCTION RULE (getstatusbd):
 *   - If a proposal already exists for this init_call in the current financial
 *     year (GetProposalUploadOnCINOrNotINFYear) -> only the CURRENT cstatus is
 *     selectable (the lead is locked to its stage once a proposal is out).
 *   - Otherwise the legal next-statuses are a fixed per-cstatus map:
 *       1  (Open)              -> 8
 *       2  (Reachout)          -> 2,4,5
 *       3  (Tentative)         -> 3
 *       4  (Will do Later)     -> 1,8,2
 *       5  (Not Interested)    -> 1,8,2
 *       6  (Positive)          -> 6
 *       7  (Closure)           -> 7
 *       8  (OPEN RPEM)         -> 2
 *       9  (Very Positive)     -> 9
 *       10 (TTD-Reachout)      -> 3
 *       11 (WNO-Reachout)      -> 3
 *       12 (Positive-NAP)      -> 12
 *       13 (Very Positive-NAP) -> 13
 *       14 (On-Boarded)        -> 14
 *
 * The labels are read from the REAL `status` master table (not hardcoded), so the
 * mobile picker shows the same names production shows.
 *
 * STRICTLY ADDITIVE - touches no existing file, no schema changes, reads real
 * staging data, reuses existing Menu_model methods. Never 500: any failure
 * returns a 200 envelope with ok:false so the mobile screen falls back to the
 * full master list. Auth via BearerAuth (same library the other mobile read
 * endpoints use). Production stemapp.in is NOT touched.
 */
class Mobile_status_api extends CI_Controller {

    private $auth_uid  = 0;
    private $auth_role = '';

    public function __construct()
    {
        parent::__construct();
        $this->output->set_content_type('application/json');
        $this->load->library('BearerAuth');
        $this->load->model('Menu_model');
    }

    private function json_out($data, $status = 200)
    {
        $this->output
             ->set_status_header($status)
             ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    private function auth_check()
    {
        $auth = $this->bearerauth->resolve();
        if (empty($auth['ok'])) {
            $this->json_out(array('ok' => false, 'error' => 'unauthorized'), 401);
            return false;
        }
        $this->auth_uid  = isset($auth['uid'])  ? (int)$auth['uid']                 : 0;
        $this->auth_role = isset($auth['role']) ? strtolower((string)$auth['role']) : '';
        return true;
    }

    /**
     * The legal cstatus -> {ystatus,...} map. Mirrors Menu::getstatusbd() exactly.
     * A cstatus with no entry maps to itself (production renders no option, which
     * in practice keeps the current status), so we default to [cstatus].
     */
    private function legal_map()
    {
        return array(
            1  => array(8),
            2  => array(2, 4, 5),
            3  => array(3),
            4  => array(1, 8, 2),
            5  => array(1, 8, 2),
            6  => array(6),
            7  => array(7),
            8  => array(2),
            9  => array(9),
            10 => array(3),
            11 => array(3),
            12 => array(12),
            13 => array(13),
            14 => array(14),
        );
    }

    /** Build [{value,label}] from a list of status ids, using the real master. */
    private function options_for_ids($ids)
    {
        $out = array();
        if (empty($ids)) { return $out; }
        foreach ($ids as $sid) {
            $sid = (int) $sid;
            $rows = $this->Menu_model->get_statusbyid($sid);
            if (!empty($rows)) {
                foreach ($rows as $d) {
                    $out[] = array(
                        'value' => (int) $d->id,
                        'label' => (string) $d->name,
                    );
                }
            }
        }
        return $out;
    }

    /**
     * GET /api/status/transitions?cstatus=<id>[&cmm_init_id=<init_call id>]
     * Returns { ok, cstatus, proposal_lock, options:[{value,label}] }.
     */
    public function transitions()
    {
        if ( ! $this->auth_check()) { return; }

        try {
            $cstatus     = (int) $this->input->get('cstatus');
            $cmm_init_id = (int) $this->input->get('cmm_init_id');

            if ($cstatus <= 0) {
                // No current status known -> hand back the full master list so the
                // mobile picker can still render (honest, no crash).
                $all = $this->Menu_model->get_status();
                $opts = array();
                foreach ($all as $d) {
                    $opts[] = array('value' => (int)$d->id, 'label' => (string)$d->name);
                }
                $this->json_out(array(
                    'ok'            => true,
                    'cstatus'       => 0,
                    'proposal_lock' => false,
                    'options'       => $opts,
                ));
                return;
            }

            // Proposal-in-FY lock: production restricts to the current cstatus only.
            $proposal_lock = false;
            if ($cmm_init_id > 0) {
                try {
                    $fy = $this->Menu_model->getFinancialYearRange();
                    $sd = isset($fy['start_date']) ? $fy['start_date'] : null;
                    $ed = isset($fy['end_date'])   ? $fy['end_date']   : null;
                    if ($sd && $ed) {
                        $exists = $this->Menu_model->GetProposalUploadOnCINOrNotINFYear($cmm_init_id, $sd, $ed);
                        if (is_array($exists) && sizeof($exists) > 0) {
                            $proposal_lock = true;
                        }
                    }
                } catch (Exception $e) {
                    // ignore - fall through to the normal map
                }
            }

            if ($proposal_lock) {
                $options = $this->options_for_ids(array($cstatus));
            } else {
                $map = $this->legal_map();
                $ids = isset($map[$cstatus]) ? $map[$cstatus] : array($cstatus);
                $options = $this->options_for_ids($ids);
            }

            // Safety: never hand back an empty list - fall back to current cstatus.
            if (empty($options)) {
                $options = $this->options_for_ids(array($cstatus));
            }

            $this->json_out(array(
                'ok'            => true,
                'cstatus'       => $cstatus,
                'proposal_lock' => $proposal_lock,
                'options'       => $options,
            ));
        } catch (Exception $e) {
            $this->json_out(array('ok' => false, 'error' => 'status_transitions_failed'), 200);
        } catch (Throwable $e) {
            $this->json_out(array('ok' => false, 'error' => 'status_transitions_failed'), 200);
        }
    }
}
