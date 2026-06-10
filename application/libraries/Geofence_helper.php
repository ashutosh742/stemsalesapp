<?php
/**
 * Geofence_helper
 * Shared GPS / geofence utility library for STEM CRM
 *
 * File: application/libraries/Geofence_helper.php
 * Loaded via: $this->load->library('Geofence_helper');
 *
 * Public API:
 *   haversine_m($lat1, $lng1, $lat2, $lng2)            -> meters
 *   haversine_km($lat1, $lng1, $lat2, $lng2)           -> km
 *   within_radius($lat, $lng, $a_lat, $a_lng, $r_km)   -> bool
 *   distance_to_anchor($lat, $lng, $a_lat, $a_lng)     -> ['m'=>float, 'km'=>float]
 *   nearest_anchor($lat, $lng, $anchors)               -> ['anchor'=>row, 'distance_m'=>float] | null
 *   detect_mock($accuracy_m, $reading_time, $now_ts)   -> enum 'pass'|'low_accuracy'|'mocked_time'|'missing'
 *   evaluate_gate($lat,$lng,$accuracy_m,$reading_time,$anchor)
 *                                                      -> ['status'=>enum, 'distance_m'=>float|null, 'is_mock'=>0|1]
 *   log_gate($CI, $user_id, $surface, $payload)        -> int log_id  (writes to geofence_gate_log)
 *
 * Defined gate_status values (mirrors prospecting_event_audit + adds anchor_unset):
 *   pass | missing | out_of_range | mocked_time | low_accuracy | school_ungeocoded | anchor_unset
 *
 * Plain English. No fabrication. All math uses double precision.
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Geofence_helper
{
    // Tunable thresholds (override per-call if needed)
    const EARTH_RADIUS_M       = 6371000.0;
    const LOW_ACCURACY_M       = 200.0;   // anything worse than 200m is low confidence
    const READING_STALE_SEC    = 600;     // reading older than 10 minutes is suspicious
    const DEFAULT_RADIUS_KM    = 5.0;     // home anchor default

    /** Great-circle distance in meters */
    public function haversine_m($lat1, $lng1, $lat2, $lng2)
    {
        if ($lat1 === null || $lng1 === null || $lat2 === null || $lng2 === null) {
            return null;
        }
        $lat1 = (double)$lat1; $lng1 = (double)$lng1;
        $lat2 = (double)$lat2; $lng2 = (double)$lng2;
        $phi1 = deg2rad($lat1);
        $phi2 = deg2rad($lat2);
        $dphi = deg2rad($lat2 - $lat1);
        $dlam = deg2rad($lng2 - $lng1);
        $a = sin($dphi/2) * sin($dphi/2)
           + cos($phi1) * cos($phi2) * sin($dlam/2) * sin($dlam/2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return self::EARTH_RADIUS_M * $c;
    }

    public function haversine_km($lat1, $lng1, $lat2, $lng2)
    {
        $m = $this->haversine_m($lat1, $lng1, $lat2, $lng2);
        return $m === null ? null : $m / 1000.0;
    }

    public function within_radius($lat, $lng, $anchor_lat, $anchor_lng, $radius_km)
    {
        $km = $this->haversine_km($lat, $lng, $anchor_lat, $anchor_lng);
        if ($km === null) return false;
        return $km <= (double)$radius_km;
    }

    public function distance_to_anchor($lat, $lng, $anchor_lat, $anchor_lng)
    {
        $m = $this->haversine_m($lat, $lng, $anchor_lat, $anchor_lng);
        if ($m === null) return ['m' => null, 'km' => null];
        return ['m' => round($m, 2), 'km' => round($m / 1000.0, 4)];
    }

    /**
     * Find closest anchor from a list. Each anchor must have keys 'lat' and 'lng'.
     * Returns ['anchor'=>row, 'distance_m'=>float] or null.
     */
    public function nearest_anchor($lat, $lng, $anchors)
    {
        if ($lat === null || $lng === null || !is_array($anchors) || empty($anchors)) {
            return null;
        }
        $best = null;
        $best_m = PHP_INT_MAX;
        foreach ($anchors as $a) {
            if (!isset($a['lat']) || !isset($a['lng']) || $a['lat'] === null || $a['lng'] === null) {
                continue;
            }
            $m = $this->haversine_m($lat, $lng, $a['lat'], $a['lng']);
            if ($m !== null && $m < $best_m) {
                $best_m = $m;
                $best = $a;
            }
        }
        if ($best === null) return null;
        return ['anchor' => $best, 'distance_m' => round($best_m, 2)];
    }

    /**
     * Mock / stale detection.
     * Returns:
     *   'missing'      - reading absent
     *   'mocked_time'  - reading_time more than READING_STALE_SEC older than now (likely replay)
     *   'low_accuracy' - accuracy_m above LOW_ACCURACY_M
     *   'pass'         - all clear
     */
    public function detect_mock($accuracy_m, $reading_time, $now_ts = null)
    {
        if ($accuracy_m === null && $reading_time === null) return 'missing';
        if ($now_ts === null) $now_ts = time();

        // reading_time can be unix int or datetime string
        $rt_ts = null;
        if ($reading_time !== null) {
            if (is_numeric($reading_time)) {
                $rt_ts = (int)$reading_time;
            } else {
                $rt_ts = strtotime((string)$reading_time);
            }
        }
        if ($rt_ts && ($now_ts - $rt_ts) > self::READING_STALE_SEC) {
            return 'mocked_time';
        }
        if ($accuracy_m !== null && (double)$accuracy_m > self::LOW_ACCURACY_M) {
            return 'low_accuracy';
        }
        return 'pass';
    }

    /**
     * Full gate evaluation against a single anchor.
     * @param array|null $anchor  ['lat'=>..,'lng'=>..,'radius_km'=>..,'label'=>..]
     * @return array ['status'=>enum, 'distance_m'=>float|null, 'is_mock'=>0|1, 'anchor_label'=>str|null]
     */
    public function evaluate_gate($lat, $lng, $accuracy_m, $reading_time, $anchor)
    {
        $out = [
            'status'       => 'pass',
            'distance_m'   => null,
            'is_mock'      => 0,
            'anchor_label' => $anchor['label'] ?? null,
        ];

        // 1) reading present?
        if ($lat === null || $lng === null) {
            $out['status'] = 'missing';
            return $out;
        }

        // 2) mock / stale / low accuracy
        $mock = $this->detect_mock($accuracy_m, $reading_time);
        if ($mock === 'mocked_time') { $out['status'] = 'mocked_time'; $out['is_mock'] = 1; return $out; }
        if ($mock === 'low_accuracy') { $out['status'] = 'low_accuracy'; return $out; }

        // 3) anchor present?
        if (!is_array($anchor) || !isset($anchor['lat']) || !isset($anchor['lng']) || $anchor['lat']===null || $anchor['lng']===null) {
            $out['status'] = 'anchor_unset';
            return $out;
        }

        $radius_km = isset($anchor['radius_km']) ? (double)$anchor['radius_km'] : self::DEFAULT_RADIUS_KM;
        $dist_m = $this->haversine_m($lat, $lng, $anchor['lat'], $anchor['lng']);
        $out['distance_m'] = round($dist_m, 2);

        if ($dist_m > ($radius_km * 1000.0)) {
            $out['status'] = 'out_of_range';
            return $out;
        }
        $out['status'] = 'pass';
        return $out;
    }

    /**
     * Persist a gate evaluation to geofence_gate_log.
     * $CI is the controller / model context (so we can reach $CI->db).
     * Returns insert id, or 0 on failure (logs warning to error log, never throws).
     */
    public function log_gate($CI, $user_id, $surface, $payload = [])
    {
        try {
            $row = [
                'created_at'       => date('Y-m-d H:i:s'),
                'user_id'          => (int)$user_id,
                'surface'          => substr((string)$surface, 0, 32),
                'ref_table'        => isset($payload['ref_table']) ? substr($payload['ref_table'], 0, 64) : null,
                'ref_id'           => isset($payload['ref_id']) ? (int)$payload['ref_id'] : null,
                'lat'              => $payload['lat']        ?? null,
                'lng'              => $payload['lng']        ?? null,
                'accuracy_m'       => $payload['accuracy_m'] ?? null,
                'anchor_label'     => isset($payload['anchor_label']) ? substr($payload['anchor_label'], 0, 32) : null,
                'anchor_lat'       => $payload['anchor_lat'] ?? null,
                'anchor_lng'       => $payload['anchor_lng'] ?? null,
                'anchor_radius_km' => $payload['anchor_radius_km'] ?? null,
                'distance_m'       => $payload['distance_m'] ?? null,
                'gate_status'      => $payload['gate_status'] ?? 'missing',
                'is_mock'          => isset($payload['is_mock']) ? (int)!!$payload['is_mock'] : 0,
                'device_info'      => isset($payload['device_info']) ? substr($payload['device_info'], 0, 255) : null,
                'notes'            => isset($payload['notes']) ? substr($payload['notes'], 0, 500) : null,
            ];
            $CI->db->insert('geofence_gate_log', $row);
            return (int)$CI->db->insert_id();
        } catch (Exception $e) {
            error_log('[Geofence_helper] log_gate failed: ' . $e->getMessage());
            return 0;
        }
    }
}
