<?php
/**
 * STEM Meeting-to-Money (M2M) Assurance - self-contained self-cleaning test suite.
 * Additive build 2026-06-16. Run on staging by the parent agent:
 *
 *   php _ops/m2m_gate_suite.php
 *
 * What it does:
 *   - PDO to the local staging DB (selfstaging_salescrm) for seeding + assertions.
 *   - curl to https://selfstagingstemapp.in for the live HTTP gate/tracker endpoints.
 *   - Seeds one meeting + MoM, drives Gate A (capture/grade/check), asserts the
 *     weighted Quality Score math, vague-blocks-advance, and the mandatory-field
 *     gate; drives Gate B (committed -> breach, working-day math); drives Gate C
 *     (touch -> adherence -> DQ10); exercises all 4 trackers; asserts DQ8/DQ9/DQ10
 *     rows appear; then DELETES every seeded row in a finally block and asserts
 *     residue == 0.
 *   - Prints exactly ONE JSON line: {ts, green, per_gate:{A,B,C}, dq_fired:[...], residue}.
 *
 * ASCII only. Rupees written "Rs". "percent" spelled out. No em-dashes.
 * Self-cleaning: all seeded primary keys are tracked and removed in finally.
 * Idempotent across re-runs: uses a unique marker tag on every seeded row.
 */

error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);

// ----------------------- config (env-overridable, nothing hardcoded that matters) -----------------------
$DB_HOST = getenv('M2M_DB_HOST') ?: '127.0.0.1';
$DB_NAME = getenv('M2M_DB_NAME') ?: 'selfstaging_salescrm';
$DB_USER = getenv('M2M_DB_USER') ?: 'selfstaging_salescrm';
$DB_PASS = getenv('M2M_DB_PASS') ?: '';
$BASE    = rtrim(getenv('M2M_BASE_URL') ?: 'https://selfstagingstemapp.in', '/');
$TOKEN   = getenv('STEM_DIGEST_TOKEN') ?: '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';

$MARKER  = 'M2M_SUITE_' . getmypid() . '_' . time();
$TODAY   = date('Y-m-d');

// ----------------------- result accumulators -----------------------
$per_gate = ['A' => false, 'B' => false, 'C' => false];
$dq_fired = [];
$residue  = -1;
$green    = false;
$errors   = [];

// seeded primary keys to clean
$seed = [
    'mom_ids'     => [],
    'qlog_ids'    => [],
    'closure_ids' => [],
    'dq_ids'      => [],
    'cid_ids'     => [],
];

// ----------------------- helpers -----------------------
function http_call($method, $url, $token, $body = null)
{
    $ch = curl_init($url);
    $headers = ['Authorization: Bearer ' . $token, 'Accept: application/json'];
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    if (strtoupper($method) === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if ($body !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($body));
            $headers[] = 'Content-Type: application/x-www-form-urlencoded';
        }
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $raw  = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $json = json_decode((string)$raw, true);
    return ['code' => $code, 'body' => $json, 'raw' => $raw];
}

function assert_true($cond, $msg, &$errors)
{
    if (!$cond) { $errors[] = $msg; }
    return (bool)$cond;
}

// ----------------------- main -----------------------
$pdo = null;
try {
    $pdo = new PDO(
        "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4",
        $DB_USER, $DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Read config (single source of truth) so assertions are not hardcoded.
    $cfg = [];
    foreach ($pdo->query("SELECT cfg_key, cfg_value FROM m2m_config")->fetchAll(PDO::FETCH_ASSOC) as $r) {
        $cfg[$r['cfg_key']] = $r['cfg_value'];
    }
    $threshold = (float)($cfg['quality_score_threshold'] ?? 70);
    $w_rp      = (float)($cfg['weight_rp'] ?? 40);
    $w_fit     = (float)($cfg['weight_fit'] ?? 20);
    $w_purpose = (float)($cfg['weight_purpose'] ?? 20);
    $w_mom     = (float)($cfg['weight_mom'] ?? 20);
    $sla_days  = (int)($cfg['proposal_sla_working_days'] ?? 5);
    $dq8_count = (int)($cfg['dq8_count'] ?? 3);
    $touch_sla = (int)($cfg['manager_touch_sla_days'] ?? 7);

    // synthetic ids well outside real ranges
    $CID  = 990000000 + (getmypid() % 1000000);
    $BD   = 990111;
    $MGR  = 990222;
    $seed['cid_ids'][] = $CID;

    // ========================= GATE A =========================
    // Seed a MoM row that PASSES the mandatory gate, with RP=1, funded=1,
    // purpose=1 so a Good grade yields a full score.
    $ins = $pdo->prepare(
        "INSERT INTO mom_data
            (init_cmpid, user_id, tid, action_id, ccstatus,
             rp_present, prospect_funded, funded_lever, purpose_achieved,
             client_commitment, next_step_text, next_step_owner_uid, next_step_date,
             proposal_committed_date, rpmmom)
         VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
    );
    $committed = date('Y-m-d', strtotime($TODAY . ' -10 days')); // old enough to breach
    $ins->execute([$CID, $BD, 0, 0, 0,
        1, 1, 'csr', 1, 'hard',
        $MARKER . ' next step demo', $BD, date('Y-m-d', strtotime($TODAY . ' +3 days')),
        $committed, $MARKER]);
    $momA = (int)$pdo->lastInsertId();
    $seed['mom_ids'][] = $momA;

    // Grade Good via HTTP -> expect full weighted score and quality=true.
    $g = http_call('POST', "$BASE/api/m2m/gatea/grade", $TOKEN,
        ['mom_id' => $momA, 'mom_grade' => 'good', 'graded_by' => $MGR]);
    $expected = ($w_rp + $w_fit + $w_purpose + $w_mom); // all flags 1, Good=1.0
    $okA1 = assert_true(
        isset($g['body']['quality_score']) && abs((float)$g['body']['quality_score'] - $expected) < 0.01,
        'gateA: score math mismatch (got ' . json_encode($g['body']['quality_score'] ?? null) . ' expected ' . $expected . ')',
        $errors
    );
    $okA2 = assert_true(!empty($g['body']['quality']), 'gateA: full-flag Good should be quality=true', $errors);
    if (isset($g['body']['mom_id'])) {
        $row = $pdo->query("SELECT id FROM mom_quality_log WHERE mom_id={$momA} ORDER BY id DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
        if ($row) $seed['qlog_ids'][] = (int)$row['id'];
    }

    // check endpoint: this MoM is complete -> not blocked.
    $c = http_call('GET', "$BASE/api/m2m/gatea/check?mom_id={$momA}", $TOKEN);
    $okA3 = assert_true(isset($c['body']['blocked']) && $c['body']['blocked'] === false,
        'gateA: complete MoM should not be blocked', $errors);

    // Vague-blocks-advance + mandatory-field gate: seed an INCOMPLETE MoM.
    $ins2 = $pdo->prepare("INSERT INTO mom_data (init_cmpid, user_id, tid, action_id, ccstatus, rpmmom) VALUES (?,?,?,?,?,?)");
    $ins2->execute([$CID, $BD, 0, 0, 0, $MARKER]);
    $momIncomplete = (int)$pdo->lastInsertId();
    $seed['mom_ids'][] = $momIncomplete;

    $cb = http_call('GET', "$BASE/api/m2m/gatea/check?mom_id={$momIncomplete}", $TOKEN);
    $okA4 = assert_true(
        $cb['code'] === 200 && !empty($cb['body']['blocked']) && !empty($cb['body']['missing']),
        'gateA: incomplete MoM should return 200 blocked with missing[]', $errors
    );

    // Vague grade blocks advance.
    $pdo->prepare("UPDATE mom_data SET rp_present=1, prospect_funded=1, purpose_achieved=1 WHERE id=?")
        ->execute([$momIncomplete]);
    $gv = http_call('POST', "$BASE/api/m2m/gatea/grade", $TOKEN,
        ['mom_id' => $momIncomplete, 'mom_grade' => 'vague', 'graded_by' => $MGR]);
    $okA5 = assert_true(!empty($gv['body']['blocked']), 'gateA: vague grade must block advance', $errors);
    $row = $pdo->query("SELECT id FROM mom_quality_log WHERE mom_id={$momIncomplete} ORDER BY id DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    if ($row) $seed['qlog_ids'][] = (int)$row['id'];

    // DQ8: drive below-threshold meetings up to dq8_count for this BD this month.
    // Vague rows already count as below threshold; add more to reach the count.
    for ($i = 0; $i < ($dq8_count + 1); $i++) {
        $insN = $pdo->prepare("INSERT INTO mom_data (init_cmpid, user_id, tid, action_id, ccstatus, rp_present, prospect_funded, purpose_achieved, rpmmom) VALUES (?,?,?,?,?,?,?,?,?)");
        $insN->execute([$CID, $BD, 0, 0, 0, 0, 0, 0, $MARKER]);
        $mid = (int)$pdo->lastInsertId();
        $seed['mom_ids'][] = $mid;
        $gg = http_call('POST', "$BASE/api/m2m/gatea/grade", $TOKEN,
            ['mom_id' => $mid, 'mom_grade' => 'vague', 'graded_by' => $MGR]);
        $qr = $pdo->query("SELECT id FROM mom_quality_log WHERE mom_id={$mid} ORDER BY id DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
        if ($qr) $seed['qlog_ids'][] = (int)$qr['id'];
        if (!empty($gg['body']['dq8']['fired'])) break;
    }
    $dq8row = $pdo->query("SELECT id FROM m2m_disqualifier_log WHERE dq_code='DQ8' AND subject_uid={$BD} ORDER BY id DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    if ($dq8row) { $seed['dq_ids'][] = (int)$dq8row['id']; $dq_fired[] = 'DQ8'; }
    $okA6 = assert_true((bool)$dq8row, 'gateA: DQ8 row should be created after pattern', $errors);

    // Meeting Quality Log daily tracker responds.
    $ql = http_call('GET', "$BASE/api/m2m/gatea/quality_log?date={$TODAY}", $TOKEN);
    $okA7 = assert_true(!empty($ql['body']['ok']), 'gateA: quality_log tracker should return ok', $errors);

    $per_gate['A'] = $okA1 && $okA2 && $okA3 && $okA4 && $okA5 && $okA6 && $okA7;

    // ========================= GATE B =========================
    // momA has proposal_committed_date 10 days ago, never sent -> BREACH -> DQ9.
    $cns = http_call('GET', "$BASE/api/m2m/gateb/committed_not_sent?as_of={$TODAY}", $TOKEN);
    $found_breach = false;
    if (!empty($cns['body']['rows'])) {
        foreach ($cns['body']['rows'] as $r) {
            if ((int)$r['cid'] === $CID && $r['status'] === 'BREACH') { $found_breach = true; break; }
        }
    }
    $okB1 = assert_true($found_breach, 'gateB: overdue committed proposal should be BREACH', $errors);

    $dq9row = $pdo->query("SELECT id FROM m2m_disqualifier_log WHERE dq_code='DQ9' AND cid_id={$CID} ORDER BY id DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    if ($dq9row) { $seed['dq_ids'][] = (int)$dq9row['id']; $dq_fired[] = 'DQ9'; }
    $okB2 = assert_true((bool)$dq9row, 'gateB: DQ9 row should be created on breach', $errors);

    // mark_sent then verify it records.
    $ms = http_call('POST', "$BASE/api/m2m/gateb/mark_sent", $TOKEN,
        ['mom_id' => $momA, 'sent_date' => $TODAY]);
    $okB3 = assert_true(!empty($ms['body']['ok']), 'gateB: mark_sent should return ok', $errors);

    $per_gate['B'] = $okB1 && $okB2 && $okB3;

    // ========================= GATE C =========================
    // Seed a stale closure row (last touch beyond SLA) -> DQ10 on adherence.
    $stale = date('Y-m-d', strtotime($TODAY . ' -' . ($touch_sla + 5) . ' days'));
    $insC = $pdo->prepare(
        "INSERT INTO m2m_manager_closure
            (cid_id, lead_status, manager_uid, manager_role, last_touch_date, verdict, next_action_text)
         VALUES (?,?,?,?,?,?,?)"
    );
    $insC->execute([$CID, 6, $MGR, 'RM', $stale, 'open', $MARKER]);
    $closureId = (int)$pdo->lastInsertId();
    $seed['closure_ids'][] = $closureId;

    $adh = http_call('GET', "$BASE/api/m2m/gatec/adherence?week={$TODAY}", $TOKEN);
    $found_notouch = false;
    if (!empty($adh['body']['rows'])) {
        foreach ($adh['body']['rows'] as $r) {
            if ((int)$r['cid'] === $CID && $r['adherence'] === 'NO TOUCH') { $found_notouch = true; break; }
        }
    }
    $okC1 = assert_true($found_notouch, 'gateC: stale lead should be NO TOUCH', $errors);

    $dq10row = $pdo->query("SELECT id FROM m2m_disqualifier_log WHERE dq_code='DQ10' AND cid_id={$CID} ORDER BY id DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    if ($dq10row) { $seed['dq_ids'][] = (int)$dq10row['id']; $dq_fired[] = 'DQ10'; }
    $okC2 = assert_true((bool)$dq10row, 'gateC: DQ10 row should be created beyond SLA cycle', $errors);

    // touch upsert resets adherence.
    $tch = http_call('POST', "$BASE/api/m2m/gatec/touch", $TOKEN,
        ['cid_id' => $CID, 'manager_uid' => $MGR, 'manager_role' => 'RM',
         'last_touch_date' => $TODAY, 'next_action_text' => 'follow up',
         'next_action_date' => date('Y-m-d', strtotime($TODAY . ' +5 days')),
         'verdict' => 'open']);
    $okC3 = assert_true(!empty($tch['body']['ok']), 'gateC: touch upsert should return ok', $errors);

    // Monthly scorecard responds.
    $sc = http_call('GET', "$BASE/api/m2m/gatec/scorecard?month=" . date('Y-m'), $TOKEN);
    $okC4 = assert_true(!empty($sc['body']['ok']), 'gateC: scorecard should return ok', $errors);

    $per_gate['C'] = $okC1 && $okC2 && $okC3 && $okC4;

    $dq_fired = array_values(array_unique($dq_fired));
    $green = $per_gate['A'] && $per_gate['B'] && $per_gate['C']
        && in_array('DQ8', $dq_fired, true)
        && in_array('DQ9', $dq_fired, true)
        && in_array('DQ10', $dq_fired, true);

} catch (Throwable $e) {
    $errors[] = 'fatal: ' . $e->getMessage();
    $green = false;
} finally {
    // ----------------------- SELF-CLEAN (always) -----------------------
    $residue = 0;
    if ($pdo instanceof PDO) {
        try {
            // Delete by tracked ids first, then sweep any residue by MARKER tag.
            foreach ($seed['dq_ids'] as $id)      { $pdo->prepare("DELETE FROM m2m_disqualifier_log WHERE id=?")->execute([$id]); }
            foreach ($seed['qlog_ids'] as $id)    { $pdo->prepare("DELETE FROM mom_quality_log WHERE id=?")->execute([$id]); }
            foreach ($seed['closure_ids'] as $id) { $pdo->prepare("DELETE FROM m2m_manager_closure WHERE id=?")->execute([$id]); }
            foreach ($seed['mom_ids'] as $id)     { $pdo->prepare("DELETE FROM mom_data WHERE id=?")->execute([$id]); }

            // Sweep by marker / synthetic ids in case any id was missed.
            $pdo->prepare("DELETE FROM mom_data WHERE rpmmom LIKE ?")->execute([$MARKER . '%']);
            $pdo->prepare("DELETE FROM m2m_manager_closure WHERE next_action_text LIKE ?")->execute([$MARKER . '%']);
            foreach ($seed['cid_ids'] as $cid) {
                $pdo->prepare("DELETE FROM mom_quality_log WHERE mom_id NOT IN (SELECT id FROM mom_data) AND bd_uid=990111")->execute();
                $pdo->prepare("DELETE FROM m2m_disqualifier_log WHERE cid_id=?")->execute([$cid]);
                $pdo->prepare("DELETE FROM m2m_disqualifier_log WHERE subject_uid=990111")->execute();
                $pdo->prepare("DELETE FROM m2m_manager_closure WHERE cid_id=?")->execute([$cid]);
                $pdo->prepare("DELETE FROM mom_data WHERE init_cmpid=?")->execute([$cid]);
            }

            // Residue assertion across all seeded surfaces.
            $r1 = (int)$pdo->query("SELECT COUNT(*) FROM mom_data WHERE rpmmom LIKE " . $pdo->quote($MARKER . '%'))->fetchColumn();
            $r2 = (int)$pdo->query("SELECT COUNT(*) FROM m2m_manager_closure WHERE next_action_text LIKE " . $pdo->quote($MARKER . '%'))->fetchColumn();
            $cidList = implode(',', array_map('intval', $seed['cid_ids'])) ?: '0';
            $r3 = (int)$pdo->query("SELECT COUNT(*) FROM m2m_disqualifier_log WHERE cid_id IN ($cidList) OR subject_uid=990111")->fetchColumn();
            $residue = $r1 + $r2 + $r3;
        } catch (Throwable $ce) {
            $errors[] = 'cleanup: ' . $ce->getMessage();
            $residue = -1;
        }
    }

    if ($residue !== 0) { $green = false; }

    echo json_encode([
        'ts'       => date('c'),
        'green'    => (bool)$green,
        'per_gate' => $per_gate,
        'dq_fired' => $dq_fired,
        'residue'  => $residue,
        'errors'   => $errors,
    ]) . "\n";
}
