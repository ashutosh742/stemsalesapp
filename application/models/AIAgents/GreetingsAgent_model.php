<?php
/**
 * GreetingsAgent_model
 *
 * Migration 048 - Greetings Engine
 *
 * Responsibilities:
 *   1. Daily queue: detect today's matched stakeholders (birthday + festival + custom)
 *   2. Suppression rules (Lost lead 30 day cool-off, do_not_contact, no channel)
 *   3. Template selection with language fallback
 *   4. Variable substitution
 *   5. Insert greeting_task + daily_planner row (task_subtype='greeting')
 *   6. Dispatch on approval via WhatsappAgent + Crm_emailer
 *   7. Log every channel attempt to greeting_sent_log
 *
 * Path: application/models/AIAgents/GreetingsAgent_model.php
 * Author: STEM Learning - Migration 048
 * Date: 21 May 2026
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class GreetingsAgent_model extends CI_Model
{
    const MAX_TASKS_PER_BD_PER_DAY = 8;
    const LOST_COOLOFF_DAYS        = 30;
    const ACTIONTYPE_GREETING      = 14;
    const PURPOSE_GREETING         = 200;

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('AIAgents/WhatsappAgent_model', 'whatsapp_agent');
        $this->load->library('Crm_emailer');
    }

    // ----------------------------------------------------------
    // 1. Daily queue runner (called by cron at 6:00 AM IST)
    // ----------------------------------------------------------
    public function queue_run($target_date = null)
    {
        $today = $target_date ?: date('Y-m-d');

        $matches = $this->find_matches_for_date($today);
        $created = 0;
        $suppressed_lost = 0;
        $suppressed_dnc = 0;
        $suppressed_dup = 0;
        $suppressed_nochan = 0;
        $bd_counts = array();

        foreach ($matches as $m) {
            // suppression: do_not_contact
            if (!empty($m['do_not_contact'])) { $suppressed_dnc++; continue; }

            // suppression: no contact channel at all
            if (empty($m['mobile_no']) && empty($m['email'])) { $suppressed_nochan++; continue; }

            // suppression: Lost lead within cool-off
            if ($this->is_in_lost_cooloff($m['init_call_id'])) { $suppressed_lost++; continue; }

            // suppression: cap per BD per day
            $bd = (int)$m['bd_uid'];
            if (!isset($bd_counts[$bd])) { $bd_counts[$bd] = 0; }
            if ($bd_counts[$bd] >= self::MAX_TASKS_PER_BD_PER_DAY) { continue; }

            // suppression: idempotency
            if ($this->task_exists($m['stakeholder_dob_id'], $m['occasion_id'], $today)) {
                $suppressed_dup++; continue;
            }

            // build drafts
            $tpl_wa  = $this->pick_template($m['occasion_code'], 'whatsapp', $m['language_pref']);
            $tpl_em  = $this->pick_template($m['occasion_code'], 'email',    $m['language_pref']);

            $wa_body   = $tpl_wa ? $this->substitute($tpl_wa['body'], $m) : null;
            $em_subj   = $tpl_em ? $this->substitute($tpl_em['subject'], $m) : null;
            $em_body   = $tpl_em ? $this->substitute($tpl_em['body'], $m) : null;

            // insert task
            $task_id = $this->insert_greeting_task($m, $today, $wa_body, $em_subj, $em_body);

            // insert daily_planner row so it surfaces in My Tasks
            $planner_id = $this->insert_planner_row($m['bd_uid'], $m['init_call_id'], $today, $task_id, $m['occasion_name']);

            // backlink
            $this->db->update('greeting_task', array('daily_planner_id' => $planner_id), array('id' => $task_id));

            $created++;
            $bd_counts[$bd]++;
        }

        return array(
            'date'                 => $today,
            'matches_found'        => count($matches),
            'tasks_created'        => $created,
            'suppressed_lost'      => $suppressed_lost,
            'suppressed_dnc'       => $suppressed_dnc,
            'suppressed_dup'       => $suppressed_dup,
            'suppressed_no_chan'   => $suppressed_nochan,
            'bd_count'             => count($bd_counts),
        );
    }

    // ----------------------------------------------------------
    // 2. Match finder - birthdays + festivals + custom
    // ----------------------------------------------------------
    private function find_matches_for_date($date)
    {
        $mmdd = date('m-d', strtotime($date));

        // 2a. Birthday matches: any stakeholder whose dob month-day equals today
        $sql_birthday = "
            SELECT
                sd.id                    AS stakeholder_dob_id,
                sd.init_call_id,
                sd.stakeholder_name,
                sd.stakeholder_designation,
                sd.mobile_no,
                sd.email,
                sd.do_not_contact,
                sd.language_pref,
                ic.mainbd                AS bd_uid,
                cm.compname              AS school_name,
                u.firstname              AS bd_first,
                u.lastname               AS bd_last,
                'dm_birthday'            AS occasion_code,
                CONCAT(sd.stakeholder_name, ' birthday') AS occasion_name,
                NULL                     AS festival_occ_id
            FROM stakeholder_dob sd
            JOIN init_call ic ON ic.id = sd.init_call_id
            LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
            JOIN user u ON u.uid = ic.mainbd
            WHERE sd.dob IS NOT NULL
              AND DATE_FORMAT(sd.dob, '%m-%d') = ?
        ";
        $birthdays = $this->db->query($sql_birthday, array($mmdd))->result_array();

        // attach the synthetic birthday occasion id (one shared row per BD with custom occasion_id stored)
        $birthday_occ = $this->upsert_birthday_occasion_row();
        foreach ($birthdays as &$b) {
            $b['occasion_id'] = $birthday_occ;
        }
        unset($b);

        // 2b. Festival matches for today
        $sql_fest = "
            SELECT
                sd.id                    AS stakeholder_dob_id,
                sd.init_call_id,
                sd.stakeholder_name,
                sd.stakeholder_designation,
                sd.mobile_no,
                sd.email,
                sd.do_not_contact,
                sd.language_pref,
                ic.mainbd                AS bd_uid,
                cm.compname              AS school_name,
                u.firstname              AS bd_first,
                u.lastname               AS bd_last,
                go.occasion_code,
                go.occasion_name,
                go.id                    AS occasion_id
            FROM stakeholder_dob sd
            JOIN init_call ic ON ic.id = sd.init_call_id
            LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
            JOIN user u ON u.uid = ic.mainbd
            JOIN greeting_occasion go ON go.occasion_date = ? AND go.active = 1
            WHERE sd.do_not_contact = 0
        ";
        $festivals = $this->db->query($sql_fest, array($date))->result_array();

        return array_merge($birthdays, $festivals);
    }

    private function upsert_birthday_occasion_row()
    {
        $row = $this->db->get_where('greeting_occasion', array('occasion_code' => 'dm_birthday'))->row_array();
        if ($row) { return $row['id']; }
        $this->db->insert('greeting_occasion', array(
            'occasion_type' => 'birthday',
            'occasion_code' => 'dm_birthday',
            'occasion_name' => 'Stakeholder birthday',
            'recurring'     => 1,
            'active'        => 1,
        ));
        return $this->db->insert_id();
    }

    // ----------------------------------------------------------
    // 3. Suppression helpers
    // ----------------------------------------------------------
    private function is_in_lost_cooloff($init_call_id)
    {
        $sql = "
            SELECT id FROM lead_progression_log
            WHERE cid_id = ? AND to_status = 13
              AND created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
            ORDER BY created_at DESC LIMIT 1
        ";
        $row = $this->db->query($sql, array($init_call_id, self::LOST_COOLOFF_DAYS))->row();
        return !!$row;
    }

    private function task_exists($stakeholder_dob_id, $occasion_id, $date)
    {
        $row = $this->db->get_where('greeting_task', array(
            'stakeholder_dob_id' => $stakeholder_dob_id,
            'occasion_id'        => $occasion_id,
            'occasion_date'      => $date,
        ))->row();
        return !!$row;
    }

    // ----------------------------------------------------------
    // 4. Template + substitution
    // ----------------------------------------------------------
    private function pick_template($occasion_code, $channel, $lang)
    {
        // try exact match
        $row = $this->db->get_where('greeting_template', array(
            'occasion_code' => $occasion_code,
            'channel'       => $channel,
            'language'      => $lang,
            'active'        => 1,
        ))->row_array();
        if ($row) { return $row; }

        // fallback 'both'
        $row = $this->db->get_where('greeting_template', array(
            'occasion_code' => $occasion_code,
            'channel'       => 'both',
            'language'      => $lang,
            'active'        => 1,
        ))->row_array();
        if ($row) { return $row; }

        // fallback English
        if ($lang !== 'en') {
            $row = $this->db->get_where('greeting_template', array(
                'occasion_code' => $occasion_code,
                'channel'       => $channel,
                'language'      => 'en',
                'active'        => 1,
            ))->row_array();
            if ($row) { return $row; }
        }
        return null;
    }

    private function substitute($text, $m)
    {
        if ($text === null) { return null; }
        $vars = array(
            '{stakeholder_name}' => $m['stakeholder_name'],
            '{designation}'      => $m['stakeholder_designation'] ?: 'Sir/Madam',
            '{school_name}'      => $m['school_name'] ?: 'your institution',
            '{bd_name}'          => trim(($m['bd_first'] ?: '') . ' ' . ($m['bd_last'] ?: '')),
            '{occasion_name}'    => $m['occasion_name'],
        );
        return strtr($text, $vars);
    }

    // ----------------------------------------------------------
    // 5. Insert helpers
    // ----------------------------------------------------------
    private function insert_greeting_task($m, $date, $wa_body, $em_subj, $em_body)
    {
        $this->db->insert('greeting_task', array(
            'bd_uid'               => $m['bd_uid'],
            'init_call_id'         => $m['init_call_id'],
            'stakeholder_dob_id'   => $m['stakeholder_dob_id'],
            'occasion_id'          => $m['occasion_id'],
            'occasion_date'        => $date,
            'status'               => 'pending',
            'draft_whatsapp_body'  => $wa_body,
            'draft_email_subject'  => $em_subj,
            'draft_email_body'     => $em_body,
        ));
        return $this->db->insert_id();
    }

    private function insert_planner_row($bd_uid, $init_call_id, $date, $task_id, $occasion_name)
    {
        $this->db->insert('daily_planner', array(
            'user_id'        => $bd_uid,
            'cid_id'         => $init_call_id,
            'plan_date'      => $date,
            'actiontype_id'  => self::ACTIONTYPE_GREETING,
            'purpose_id'     => self::PURPOSE_GREETING,
            'task_subtype'   => 'greeting',
            'task_ref_id'    => $task_id,
            'is_auto'        => 1,
            'remarks'        => 'Greeting: ' . $occasion_name,
            'created_at'     => date('Y-m-d H:i:s'),
        ));
        return $this->db->insert_id();
    }

    // ----------------------------------------------------------
    // 6. Approve and dispatch (called from controller)
    // ----------------------------------------------------------
    public function approve_and_send($task_id, $bd_uid)
    {
        $task = $this->db->get_where('greeting_task', array('id' => $task_id, 'bd_uid' => $bd_uid))->row_array();
        if (!$task) { return array('ok' => false, 'error' => 'task_not_found'); }
        if ($task['status'] !== 'pending') { return array('ok' => false, 'error' => 'not_pending', 'current_status' => $task['status']); }

        $sd = $this->db->get_where('stakeholder_dob', array('id' => $task['stakeholder_dob_id']))->row_array();

        $this->db->update('greeting_task', array(
            'status'      => 'approved',
            'approved_at' => date('Y-m-d H:i:s'),
            'approved_by' => $bd_uid,
        ), array('id' => $task_id));

        $wa_ok = false; $em_ok = false;
        $wa_err = null; $em_err = null;

        // WhatsApp
        if (!empty($sd['mobile_no']) && !empty($task['draft_whatsapp_body'])) {
            $tpl = $this->db->get_where('greeting_template', array(
                'occasion_code' => $this->get_occ_code($task['occasion_id']),
                'channel'       => 'whatsapp',
                'language'      => $sd['language_pref'],
                'active'        => 1,
            ))->row_array();

            $res = $this->whatsapp_agent->send_template(
                $sd['mobile_no'],
                $tpl ? $tpl['whatsapp_template_name'] : null,
                $task['draft_whatsapp_body']
            );
            $wa_ok = !empty($res['ok']);
            $wa_err = $wa_ok ? null : ($res['error'] ?? 'unknown');
            $this->log_send($task_id, 'whatsapp', $sd['mobile_no'], $res);
        }

        // Email
        if (!empty($sd['email']) && !empty($task['draft_email_body'])) {
            $res = $this->crm_emailer->send_html(
                $sd['email'],
                $task['draft_email_subject'],
                $task['draft_email_body'],
                $bd_uid
            );
            $em_ok = !empty($res['ok']);
            $em_err = $em_ok ? null : ($res['error'] ?? 'unknown');
            $this->log_send($task_id, 'email', $sd['email'], $res);
        }

        // Final status
        $final = 'failed';
        if ($wa_ok && $em_ok) { $final = 'sent'; }
        else if ($wa_ok || $em_ok) { $final = 'partial'; }

        $this->db->update('greeting_task', array(
            'status'  => $final,
            'sent_at' => date('Y-m-d H:i:s'),
        ), array('id' => $task_id));

        return array(
            'ok'           => ($final !== 'failed'),
            'status'       => $final,
            'whatsapp_ok'  => $wa_ok,
            'email_ok'     => $em_ok,
            'whatsapp_err' => $wa_err,
            'email_err'    => $em_err,
        );
    }

    private function get_occ_code($occasion_id)
    {
        $row = $this->db->get_where('greeting_occasion', array('id' => $occasion_id))->row_array();
        return $row ? $row['occasion_code'] : null;
    }

    private function log_send($task_id, $channel, $recipient, $res)
    {
        $this->db->insert('greeting_sent_log', array(
            'greeting_task_id'    => $task_id,
            'channel'             => $channel,
            'recipient'           => $recipient,
            'provider_message_id' => $res['message_id'] ?? null,
            'status'              => !empty($res['ok']) ? 'sent' : 'failed',
            'provider_response'   => json_encode($res),
        ));
    }

    // ----------------------------------------------------------
    // 7. Skip
    // ----------------------------------------------------------
    public function skip($task_id, $bd_uid, $reason)
    {
        $this->db->update('greeting_task', array(
            'status'      => 'skipped',
            'skip_reason' => $reason,
            'approved_at' => date('Y-m-d H:i:s'),
            'approved_by' => $bd_uid,
        ), array('id' => $task_id, 'bd_uid' => $bd_uid, 'status' => 'pending'));
        return $this->db->affected_rows() > 0;
    }

    // ----------------------------------------------------------
    // 8. Edit draft (BD changes message before send)
    // ----------------------------------------------------------
    public function edit_draft($task_id, $bd_uid, $wa_body, $em_subj, $em_body)
    {
        $upd = array();
        if ($wa_body !== null) { $upd['draft_whatsapp_body'] = $wa_body; }
        if ($em_subj !== null) { $upd['draft_email_subject'] = $em_subj; }
        if ($em_body !== null) { $upd['draft_email_body']    = $em_body; }
        if (empty($upd)) { return false; }

        $this->db->update('greeting_task', $upd, array(
            'id' => $task_id,
            'bd_uid' => $bd_uid,
            'status' => 'pending',
        ));
        return $this->db->affected_rows() > 0;
    }
}
