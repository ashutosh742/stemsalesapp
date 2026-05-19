<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * DayCeremonyGuard_model
 * Migration 038 - Day Ceremony v2 strict gate
 *
 * Parallel to production Menu_model::submit_day. Never writes to user_day, daily_planner,
 * tblcallevents. Only writes to *_v2 tables.
 *
 * Use:
 *   $guard = $this->load->model('AIAgents/DayCeremonyGuard_model');
 *   $check = $guard->day_start_check($user_id, $lat, $lng, $photo_exif_dt);
 *   if (!$check['ok']) { return $check; }
 *   $guard->day_start_commit($user_id, $lat, $lng, $photo_url, $photo_exif_dt);
 *
 *   $close = $guard->day_close_check($user_id);
 *   if (!$close['ok']) { return $close; }   // breakdown returned for UI
 *   $guard->day_close_commit($user_id, $lat, $lng, $photo_url);
 */
class DayCeremonyGuard_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        date_default_timezone_set('Asia/Kolkata');
    }

    // ---------- DAY START ----------

    public function day_start_check($user_id, $lat, $lng, $photo_exif_dt = null) {
        $today = date('Y-m-d');
        $now   = date('Y-m-d H:i:s');
        $reasons = array();

        // 1) Previous day closed?
        $prev = date('Y-m-d', strtotime('-1 day'));
        $sql = "SELECT uclose FROM day_ceremony_v2 WHERE user_id=? AND ceremony_date=? LIMIT 1";
        $r = $this->db->query($sql, array($user_id, $prev))->row();
        $prev_closed_ok = 1;
        if ($r && empty($r->uclose)) {
            // Fall back: check production user_day for compatibility during migration
            $fallback = $this->db->query("SELECT uclose FROM user_day WHERE user_id=? AND DATE(ustart)=? ORDER BY id DESC LIMIT 1", array($user_id, $prev))->row();
            if (!$fallback || empty($fallback->uclose)) {
                $prev_closed_ok = 0;
                $reasons[] = 'previous_day_not_closed';
            }
        }

        // 2) Radius check
        $anchor = $this->db->query("SELECT lat,lng,radius_km FROM day_start_home_anchor_v2 WHERE user_id=? AND active=1 LIMIT 1", array($user_id))->row();
        $radius_ok = 1;
        if ($anchor) {
            $dist_km = $this->haversine_km($anchor->lat, $anchor->lng, $lat, $lng);
            if ($dist_km > floatval($anchor->radius_km)) {
                $radius_ok = 0;
                $reasons[] = 'outside_home_radius:' . round($dist_km, 1) . 'km';
            }
        }
        // No anchor = grace, do not block but record.

        // 3) Photo freshness
        $fresh_ok = 1;
        if (!empty($photo_exif_dt)) {
            $diff = abs(strtotime($now) - strtotime($photo_exif_dt));
            $allowed = intval($this->get_config('day_start_photo_freshness_minutes', 5)) * 60;
            if ($diff > $allowed) {
                $fresh_ok = 0;
                $reasons[] = 'photo_stale:' . intval($diff / 60) . 'min';
            }
        }

        // 4) Late minutes
        $expected = $this->get_config('day_start_expected_by', '09:30:00');
        $late_minutes = max(0, intval((strtotime($now) - strtotime($today . ' ' . $expected)) / 60));

        $ok = $prev_closed_ok && $radius_ok && $fresh_ok;
        return array(
            'ok' => $ok,
            'reasons' => $reasons,
            'prev_closed_ok' => $prev_closed_ok,
            'radius_ok' => $radius_ok,
            'fresh_ok' => $fresh_ok,
            'late_minutes' => $late_minutes
        );
    }

    public function day_start_commit($user_id, $lat, $lng, $photo_url, $photo_exif_dt = null) {
        $today = date('Y-m-d');
        $now   = date('Y-m-d H:i:s');
        $check = $this->day_start_check($user_id, $lat, $lng, $photo_exif_dt);

        $data = array(
            'user_id' => $user_id,
            'ceremony_date' => $today,
            'ustart' => $now,
            'ustart_lat' => $lat,
            'ustart_lng' => $lng,
            'ustart_photo_url' => $photo_url,
            'ustart_photo_exif_taken_at' => $photo_exif_dt,
            'ustart_radius_ok' => $check['radius_ok'],
            'ustart_photo_fresh_ok' => $check['fresh_ok'],
            'ustart_prev_day_closed_ok' => $check['prev_closed_ok'],
            'ustart_late_minutes' => $check['late_minutes'],
            'ustart_blocked_reason' => $check['ok'] ? null : implode(',', $check['reasons'])
        );
        // Upsert
        $exists = $this->db->query("SELECT id FROM day_ceremony_v2 WHERE user_id=? AND ceremony_date=?", array($user_id, $today))->row();
        if ($exists) {
            $this->db->where('id', $exists->id)->update('day_ceremony_v2', $data);
            return array('id' => $exists->id, 'check' => $check);
        } else {
            $this->db->insert('day_ceremony_v2', $data);
            return array('id' => $this->db->insert_id(), 'check' => $check);
        }
    }

    // ---------- DAY CLOSE ----------

    public function day_close_check($user_id) {
        $today = date('Y-m-d');
        $breakdown = array();

        // 1) Tasks not closed (production criterion)
        $rows = $this->db->query("SELECT id FROM tblcallevents WHERE assignedto_id=? AND DATE(appointmentdatetime)=? AND nextCFID=0 AND plan=1 AND approved_status=1 AND (delete_request='' OR delete_request IS NULL)", array($user_id, $today))->result();
        if (count($rows) > 0) {
            foreach ($rows as $r) {
                $breakdown[] = array('category' => 'task_not_closed', 'event_id' => $r->id);
            }
        }

        // 2) Meetings closed today but MoM not approved (extended criterion)
        $rows = $this->db->query("SELECT t.id FROM tblcallevents t LEFT JOIN mom_v2_mandatory m ON m.event_id=t.id WHERE t.assignedto_id=? AND DATE(t.appointmentdatetime)=? AND t.nextCFID!=0 AND t.actiontype_id IN (3,4) AND (m.id IS NULL OR m.status!='approved')", array($user_id, $today))->result();
        foreach ($rows as $r) {
            $breakdown[] = array('category' => 'mom_missing', 'event_id' => $r->id);
        }

        // 3) Meetings without photo
        $rows = $this->db->query("SELECT id FROM tblcallevents WHERE assignedto_id=? AND DATE(appointmentdatetime)=? AND nextCFID!=0 AND actiontype_id IN (3,4) AND (photo_url IS NULL OR photo_url='')", array($user_id, $today))->result();
        foreach ($rows as $r) {
            $breakdown[] = array('category' => 'photo_missing', 'event_id' => $r->id);
        }

        // 4) Meetings without GPS
        $rows = $this->db->query("SELECT id FROM tblcallevents WHERE assignedto_id=? AND DATE(appointmentdatetime)=? AND nextCFID!=0 AND actiontype_id IN (3,4) AND (latitude IS NULL OR latitude=0 OR longitude IS NULL OR longitude=0)", array($user_id, $today))->result();
        foreach ($rows as $r) {
            $breakdown[] = array('category' => 'geotag_missing', 'event_id' => $r->id);
        }

        // 5) Expense actuals missing (migration 009 criterion)
        $rows = $this->db->query("SELECT t.id FROM tblcallevents t LEFT JOIN expense_actuals_log e ON e.event_id=t.id WHERE t.assignedto_id=? AND DATE(t.appointmentdatetime)=? AND t.actiontype_id IN (3,4) AND t.nextCFID!=0 AND e.id IS NULL", array($user_id, $today))->result();
        foreach ($rows as $r) {
            $breakdown[] = array('category' => 'expense_actuals_missing', 'event_id' => $r->id);
        }

        // 6) New leads pending reupdate (production criterion)
        $rows = $this->db->query("SELECT id FROM init_call WHERE mainbd=? AND new_lead=1 AND apst IS NULL", array($user_id))->result();
        foreach ($rows as $r) {
            $breakdown[] = array('category' => 'new_lead_reupdate', 'lead_id' => $r->id);
        }

        // Persist breakdown
        $this->db->query("DELETE FROM day_close_pending_v2 WHERE user_id=? AND ceremony_date=? AND resolved_at IS NULL", array($user_id, $today));
        foreach ($breakdown as $b) {
            $this->db->insert('day_close_pending_v2', array_merge(array(
                'user_id' => $user_id,
                'ceremony_date' => $today
            ), array(
                'block_category' => $b['category'],
                'event_id' => isset($b['event_id']) ? $b['event_id'] : null,
                'lead_id'  => isset($b['lead_id'])  ? $b['lead_id']  : null
            )));
        }

        $by_cat = array();
        foreach ($breakdown as $b) {
            $by_cat[$b['category']] = (isset($by_cat[$b['category']]) ? $by_cat[$b['category']] : 0) + 1;
        }

        $ok = count($breakdown) === 0;
        return array(
            'ok' => $ok,
            'total_blockers' => count($breakdown),
            'breakdown_by_category' => $by_cat,
            'breakdown' => $breakdown
        );
    }

    public function day_close_commit($user_id, $lat, $lng, $photo_url) {
        $today = date('Y-m-d');
        $now   = date('Y-m-d H:i:s');
        $check = $this->day_close_check($user_id);
        if (!$check['ok']) {
            return array('ok' => false, 'blocked' => true, 'check' => $check);
        }
        $expected = $this->get_config('day_close_expected_by', '21:30:00');
        $late = max(0, intval((strtotime($now) - strtotime($today . ' ' . $expected)) / 60));
        $exists = $this->db->query("SELECT id FROM day_ceremony_v2 WHERE user_id=? AND ceremony_date=?", array($user_id, $today))->row();
        $data = array(
            'uclose' => $now,
            'uclose_lat' => $lat,
            'uclose_lng' => $lng,
            'uclose_photo_url' => $photo_url,
            'uclose_pending_breakdown_json' => json_encode($check['breakdown_by_category']),
            'uclose_late_minutes' => $late
        );
        if ($exists) {
            $this->db->where('id', $exists->id)->update('day_ceremony_v2', $data);
        } else {
            $this->db->insert('day_ceremony_v2', array_merge($data, array(
                'user_id' => $user_id,
                'ceremony_date' => $today
            )));
        }
        if ($late > 0) {
            $this->db->query("INSERT IGNORE INTO day_close_late_log_v2 (user_id, ceremony_date, closed_at) VALUES (?,?,?)", array($user_id, $today, $now));
        }
        return array('ok' => true, 'late_minutes' => $late);
    }

    // ---------- helpers ----------

    private function haversine_km($lat1, $lng1, $lat2, $lng2) {
        $R = 6371.0;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat/2)*sin($dLat/2) + cos(deg2rad($lat1))*cos(deg2rad($lat2))*sin($dLng/2)*sin($dLng/2);
        return 2 * $R * asin(sqrt($a));
    }

    private function get_config($key, $default) {
        $r = $this->db->query("SELECT config_value FROM day_ceremony_config_v2 WHERE config_key=?", array($key))->row();
        return $r ? $r->config_value : $default;
    }
}
