<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * VoiceCommand_model - Feature (additive, 2026-06-06)
 *
 * Rule-based intent classifier over a transcribed voice/text command. There is
 * no audio table in this DB, so this feature parses a text transcript (the
 * client does speech-to-text) and maps it to an existing API route the mobile
 * app can then call. No mock data: when an intent resolves to a lead/company
 * lookup, the resolved entity is fetched from real tables.
 *
 * Returns: intent, confidence, resolved entities, and the target API route +
 * params the app should call next.
 *
 * Standing rules: ASCII only, "Rs" for rupees, "percent" spelled out.
 */
class VoiceCommand_model extends CI_Model {

    // intent => array(keywords[], target_route, method)
    private $intents = array(
        'show_my_leads' => array(
            'keywords' => array('my leads','show leads','open leads','pending leads','list leads'),
            'route'    => 'api/mobile_read/leads',
            'method'   => 'GET',
        ),
        'today_plan' => array(
            'keywords' => array('today plan','my plan','plan for today','schedule today','today schedule','what is my plan'),
            'route'    => 'api/mobile_read/calendar_upcoming',
            'method'   => 'GET',
        ),
        'next_best_action' => array(
            'keywords' => array('what next','next action','what should i do','best action','recommend','next best'),
            'route'    => 'api/next_best_action/recommend',
            'method'   => 'GET',
        ),
        'churn_risk' => array(
            'keywords' => array('churn','at risk','losing','going cold','risk leads'),
            'route'    => 'api/churn_predictor/score',
            'method'   => 'GET',
        ),
        'deal_coach' => array(
            'keywords' => array('coach','how to close','deal help','advise deal','close deal'),
            'route'    => 'api/deal_coach/advise',
            'method'   => 'GET',
        ),
        'forecast' => array(
            'keywords' => array('forecast','target','how much','revenue target','cluster forecast','projection'),
            'route'    => 'api/cluster_forecaster/forecast',
            'method'   => 'GET',
        ),
        'find_company' => array(
            'keywords' => array('find company','search company','look up','lookup','open company','find school'),
            'route'    => 'api/competitor_intel/themes',
            'method'   => 'GET',
        ),
    );

    public function manifest() {
        $out = array();
        foreach ($this->intents as $code => $cfg) {
            $out[] = array(
                'intent' => $code,
                'route'  => $cfg['route'],
                'method' => $cfg['method'],
            );
        }
        return array(
            'feature'      => 'voice_command',
            'mode'         => 'text_transcript_intent_router',
            'intent_count' => count($this->intents),
            'intents'      => $out,
            'deployed_at'  => '2026-06-06',
        );
    }

    /**
     * Parse a transcript into an intent + resolved entity + target route.
     */
    public function parse($transcript) {
        $t = strtolower(trim((string)$transcript));
        if ($t === '') {
            return array('ok' => false, 'error' => 'empty transcript');
        }

        $best = null; $best_hits = 0;
        foreach ($this->intents as $code => $cfg) {
            $hits = 0;
            foreach ($cfg['keywords'] as $kw) {
                if (strpos($t, $kw) !== false) $hits++;
            }
            if ($hits > $best_hits) { $best_hits = $hits; $best = $code; }
        }

        if ($best === null) {
            return array(
                'intent'     => 'unknown',
                'confidence' => 0.0,
                'transcript' => $transcript,
                'route'      => null,
            );
        }

        $cfg = $this->intents[$best];
        $confidence = min(0.95, 0.55 + 0.20 * $best_hits);

        // Resolve a company name if the command references one.
        $entity = $this->resolve_company($t);

        $params = array();
        if ($entity && isset($entity['company_id'])) {
            $params['company_id'] = $entity['company_id'];
        }

        return array(
            'intent'     => $best,
            'confidence' => round($confidence, 2),
            'transcript' => $transcript,
            'route'      => $cfg['route'],
            'method'     => $cfg['method'],
            'params'     => $params,
            'entity'     => $entity,
        );
    }

    /**
     * Light entity resolution: pull a candidate company name token sequence and
     * match against company_master.compname (real table). Returns null if none.
     */
    private function resolve_company($t) {
        // Try to grab text after common trigger words.
        $triggers = array('company ', 'school ', 'lookup ', 'look up ', 'find ', 'open ');
        $candidate = '';
        foreach ($triggers as $trg) {
            $pos = strpos($t, $trg);
            if ($pos !== false) {
                $candidate = trim(substr($t, $pos + strlen($trg)));
                break;
            }
        }
        if ($candidate === '' || strlen($candidate) < 3) return null;
        // Use only the first few words as the name guess.
        $words = preg_split('/\s+/', $candidate);
        $guess = implode(' ', array_slice($words, 0, 4));

        $row = $this->db->query("
            SELECT id, compname
            FROM company_master
            WHERE compname LIKE ?
            ORDER BY LENGTH(compname) ASC
            LIMIT 1", array('%' . $guess . '%'))->row();
        if (!$row) return null;
        return array(
            'company_id' => (int)$row->id,
            'compname'   => $row->compname,
            'matched_on' => $guess,
        );
    }
}
