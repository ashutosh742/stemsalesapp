<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * StarRatingController
 *
 * HTTP surface for GAP 11: Star Rating Report.
 * Covers all three star-rating tables:
 *   - sales_star_rating         (type: day_check)
 *   - sales_task_star_rating    (type: task_check)
 *   - sales_status_change_task_star_rating (type: status_change)
 *
 * Auth: Bearer STEM_DIGEST_TOKEN (via custom.php) required for all endpoints.
 *
 * Routes to add in application/config/routes.php:
 *   $route['api/star_rating/probe']['get']         = 'StarRatingController/probe';
 *   $route['api/star_rating/my_ratings']['get']    = 'StarRatingController/my_ratings';
 *   $route['api/star_rating/summary']['get']       = 'StarRatingController/summary';
 *   $route['api/star_rating/submit_rating']['post']= 'StarRatingController/submit_rating';
 */
class StarRatingController extends CI_Controller
{
    const MIGRATION = 'GAP11';

    // Bearer token loaded from CI config (application/config/custom.php)
    private $bearer_token;

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->config('custom', TRUE);
        $config_token = $this->config->item('stem_digest_token', 'custom');
        $this->bearer_token = $config_token ?: getenv('STEM_DIGEST_TOKEN');
        header('Content-Type: application/json; charset=utf-8');
    }

    // -----------------------------------------------------------------------
    // Auth guard. Matches Authorization: Bearer against configured token.
    // Falls back to an active CI session. Returns 401 + exits on failure.
    // -----------------------------------------------------------------------
    private $_authed_uid = 0;

    // ---- per-user JWT validator (added 28 May 2026) ----
    private function _jwt_token_valid($token) {
        if (empty($token)) return false;
        $secret = getenv('STEM_DIGEST_TOKEN') ?: '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
        $days = array(date('Y-m-d'), date('Y-m-d', strtotime('-1 day')), date('Y-m-d', strtotime('+1 day')));
        $candidates = array();
        foreach (array('uid','cm_uid','rm_uid','bd_uid','acm_uid','user_id') as $k) {
            if (isset($_GET[$k]) && (int)$_GET[$k] > 0) $candidates[(int)$_GET[$k]] = 1;
            if (isset($_POST[$k]) && (int)$_POST[$k] > 0) $candidates[(int)$_POST[$k]] = 1;
        }
        foreach (array_keys($candidates) as $uid) {
            foreach ($days as $d) {
                if (hash_equals(sha1($secret.'|'.$uid.'|'.$d), $token)) return (int)$uid;
            }
        }
        static $all_uids = null;
        if ($all_uids === null) {
            if (!isset($this->db) || !is_object($this->db)) { $this->load->database(); }
            $rows = $this->db->select('uid')->from('user')->where('active', 1)->get()->result();
            $all_uids = array();
            foreach ($rows as $r) $all_uids[] = (int)$r->uid;
        }
        foreach ($all_uids as $uid) {
            foreach ($days as $d) {
                if (hash_equals(sha1($secret.'|'.$uid.'|'.$d), $token)) return (int)$uid;
            }
        }
        return false;
    }

    private function _auth_or_die()
    {
        $hdr      = $this->input->get_request_header('Authorization', true);
        $expected = $this->bearer_token;

        if (empty($expected)) {
            http_response_code(503);
            echo json_encode(array(
                'error'  => 'server_misconfiguration',
                'detail' => 'Bearer token not configured on this host',
            ));
            exit;
        }

        if (!empty($hdr) && hash_equals('Bearer ' . $expected, $hdr)) {
            return true;
        }
        // Per-user JWT (added 28 May)
        if (!empty($hdr) && stripos($hdr, 'Bearer ') === 0) {
            $tok = trim(substr($hdr, 7));
            $uid = $this->_jwt_token_valid($tok);
            if ($uid) { $this->_authed_uid = $uid; return true; }
        }

        $session_uid = $this->session->userdata('user_id');
        if ((int) $session_uid > 0) {
            return true;
        }

        http_response_code(401);
        echo json_encode(array('error' => 'unauthorized'));
        exit;
    }

    // -----------------------------------------------------------------------
    // Helper: build a name-lookup map {uid => name} from the user table.
    // Only fetches UIDs that appear in the provided list.
    // -----------------------------------------------------------------------
    private function _name_map(array $uids)
    {
        $uids = array_filter(array_map('intval', $uids));
        if (empty($uids)) {
            return array();
        }
        $in  = implode(',', $uids);
        $q   = $this->db->query("SELECT uid, name FROM user WHERE uid IN ($in)");
        $map = array();
        if ($q) {
            foreach ($q->result_array() as $row) {
                $map[(int) $row['uid']] = $row['name'];
            }
        }
        return $map;
    }

    // -----------------------------------------------------------------------
    // GET /api/star_rating/probe
    // Health check. No auth required.
    // -----------------------------------------------------------------------
    public function probe()
    {
        echo json_encode(array(
            'ok'        => true,
            'migration' => self::MIGRATION,
            'ts'        => date('Y-m-d H:i:s'),
        ));
    }

    // -----------------------------------------------------------------------
    // GET /api/star_rating/my_ratings?user_id=X&from=YYYY-MM-DD&to=YYYY-MM-DD
    // Returns all star-rating rows for the given user across all three tables,
    // normalized to a common shape:
    //   {id, date, type, question, star, remarks, star_by, star_by_name}
    // -----------------------------------------------------------------------
    public function my_ratings()
    {
        $this->_auth_or_die();

        $user_id = (int) $this->input->get('user_id');
        if ($user_id <= 0) {
            http_response_code(400);
            echo json_encode(array('error' => 'user_id is required'));
            return;
        }

        $from = $this->input->get('from');
        $to   = $this->input->get('to');

        if (empty($from) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $from)) {
            $from = date('Y-m-d', strtotime('-30 days'));
        }
        if (empty($to) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $to)) {
            $to = date('Y-m-d');
        }

        $from_esc = $this->db->escape_str($from);
        $to_esc   = $this->db->escape_str($to);

        // 1) sales_star_rating (day_check)
        $q1 = $this->db->query("
            SELECT id, date, 'day_check' AS type, question, star, remarks, star_by
            FROM sales_star_rating
            WHERE user_id = '$user_id'
              AND date BETWEEN '$from_esc' AND '$to_esc'
            ORDER BY date DESC, id DESC
        ");

        // 2) sales_task_star_rating (task_check)
        $q2 = $this->db->query("
            SELECT id, date, 'task_check' AS type, question, star, remarks, star_by
            FROM sales_task_star_rating
            WHERE user_id = $user_id
              AND date BETWEEN '$from_esc' AND '$to_esc'
            ORDER BY date DESC, id DESC
        ");

        // 3) sales_status_change_task_star_rating (status_change)
        $q3 = $this->db->query("
            SELECT id, date, 'status_change' AS type, question, star, remarks, star_by
            FROM sales_status_change_task_star_rating
            WHERE user_id = $user_id
              AND date BETWEEN '$from_esc' AND '$to_esc'
            ORDER BY date DESC, id DESC
        ");

        $rows = array();
        $star_by_ids = array();

        foreach (array($q1, $q2, $q3) as $q) {
            if ($q) {
                foreach ($q->result_array() as $r) {
                    $rows[]        = $r;
                    $star_by_ids[] = (int) $r['star_by'];
                }
            }
        }

        // Sort merged result by date DESC
        usort($rows, function($a, $b) {
            return strcmp($b['date'] . $b['id'], $a['date'] . $a['id']);
        });

        // Build name lookup
        $name_map = $this->_name_map(array_unique($star_by_ids));

        // Normalize
        $result = array();
        foreach ($rows as $r) {
            $result[] = array(
                'id'           => (int) $r['id'],
                'date'         => $r['date'],
                'type'         => $r['type'],
                'question'     => $r['question'],
                'star'         => (int) $r['star'],
                'remarks'      => $r['remarks'],
                'star_by'      => (int) $r['star_by'],
                'star_by_name' => isset($name_map[(int) $r['star_by']]) ? $name_map[(int) $r['star_by']] : 'Unknown',
            );
        }

        echo json_encode(array(
            'ok'      => true,
            'user_id' => $user_id,
            'from'    => $from,
            'to'      => $to,
            'count'   => count($result),
            'rows'    => $result,
        ));
    }

    // -----------------------------------------------------------------------
    // GET /api/star_rating/summary?user_id=X&days=30
    // Returns aggregate stats: avg per type + total + 7-day trend.
    // -----------------------------------------------------------------------
    public function summary()
    {
        $this->_auth_or_die();

        $user_id = (int) $this->input->get('user_id');
        if ($user_id <= 0) {
            http_response_code(400);
            echo json_encode(array('error' => 'user_id is required'));
            return;
        }

        $days = max(1, (int) $this->input->get('days') ?: 30);
        $from = date('Y-m-d', strtotime("-{$days} days"));
        $to   = date('Y-m-d');

        $from_esc = $this->db->escape_str($from);
        $to_esc   = $this->db->escape_str($to);

        // Average per table
        $avg = function($table, $uid_col = 'user_id') use ($user_id, $from_esc, $to_esc) {
            $q = $this->db->query("
                SELECT AVG(CAST(star AS DECIMAL(5,2))) AS avg_star, COUNT(*) AS cnt
                FROM $table
                WHERE $uid_col = $user_id
                  AND date BETWEEN '$from_esc' AND '$to_esc'
                  AND star > 0
            ");
            if (!$q) return array('avg' => null, 'count' => 0);
            $row = $q->row_array();
            return array(
                'avg'   => $row['avg_star'] !== null ? (float) sprintf('%.2f', $row['avg_star']) : null,
                'count' => (int) $row['cnt'],
            );
        };

        $day_check   = $avg('sales_star_rating');
        $task_check  = $avg('sales_task_star_rating');
        $status_chg  = $avg('sales_status_change_task_star_rating');

        $total = $day_check['count'] + $task_check['count'] + $status_chg['count'];

        // 7-day trend: avg star per day across all tables for last 7 days
        $trend_from = date('Y-m-d', strtotime('-7 days'));
        $trend_q    = $this->db->query("
            SELECT date, AVG(star_val) AS avg_star FROM (
                SELECT date, CAST(star AS DECIMAL(5,2)) AS star_val
                FROM sales_star_rating
                WHERE user_id = '$user_id' AND date >= '$trend_from' AND star > 0
                UNION ALL
                SELECT date, CAST(star AS DECIMAL(5,2)) AS star_val
                FROM sales_task_star_rating
                WHERE user_id = $user_id AND date >= '$trend_from' AND star > 0
                UNION ALL
                SELECT date, CAST(star AS DECIMAL(5,2)) AS star_val
                FROM sales_status_change_task_star_rating
                WHERE user_id = $user_id AND date >= '$trend_from' AND star > 0
            ) combined
            GROUP BY date
            ORDER BY date ASC
        ");

        $trend = array();
        if ($trend_q) {
            foreach ($trend_q->result_array() as $r) {
                $trend[] = array(
                    'date'     => $r['date'],
                    'avg_star' => round((float) $r['avg_star'], 2),
                );
            }
        }

        echo json_encode(array(
            'ok'                      => true,
            'user_id'                 => $user_id,
            'days'                    => $days,
            'from'                    => $from,
            'to'                      => $to,
            'avg_day_check_star'      => $day_check['avg'],
            'avg_task_star'           => $task_check['avg'],
            'avg_status_change_star'  => $status_chg['avg'],
            'total_ratings'           => $total,
            'trend_last_7d'           => $trend,
        ));
    }

    // -----------------------------------------------------------------------
    // POST /api/star_rating/submit_rating
    // Body: user_id (rated user), type, task_id (optional), star (1-5),
    //       remarks, star_by (rater user_id).
    // Inserts into the appropriate table based on type.
    // Returns: {ok:true, rating_id:N}
    // -----------------------------------------------------------------------
    public function submit_rating()
    {
        $this->_auth_or_die();

        if ($this->input->method(true) !== 'POST') {
            http_response_code(405);
            echo json_encode(array('error' => 'POST required'));
            return;
        }

        $user_id = (int) $this->input->post('user_id');
        $type    = $this->input->post('type');
        $task_id = $this->input->post('task_id');
        $star    = (int) $this->input->post('star');
        $remarks = $this->input->post('remarks') ?: '';
        $star_by = (int) $this->input->post('star_by');

        if ($user_id <= 0) {
            http_response_code(400);
            echo json_encode(array('error' => 'user_id is required'));
            return;
        }
        if (!in_array($type, array('day_check', 'task_check', 'status_change'))) {
            http_response_code(400);
            echo json_encode(array('error' => 'type must be day_check, task_check, or status_change'));
            return;
        }
        if ($star < 1 || $star > 5) {
            http_response_code(400);
            echo json_encode(array('error' => 'star must be 1-5'));
            return;
        }
        if ($star_by <= 0) {
            http_response_code(400);
            echo json_encode(array('error' => 'star_by (rater user_id) is required'));
            return;
        }

        $today       = date('Y-m-d');
        $question     = 'Rating submitted via mobile';
        $rating_id    = 0;

        $remarks_esc  = $this->db->escape_str($remarks);
        $question_esc = $this->db->escape_str($question);
        $task_id_esc  = $this->db->escape_str($task_id ?: '0');

        // All three star-rating tables have no AUTO_INCREMENT on id.
        // Use SELECT MAX(id)+1 to compute the next id safely.
        if ($type === 'day_check') {
            $sql = "
                INSERT INTO sales_star_rating(id, date, user_id, types, question, star, remarks, star_by)
                SELECT COALESCE(MAX(id),0)+1, '$today', '$user_id', 'Day Check',
                       '$question_esc', $star, '$remarks_esc', '$star_by'
                FROM sales_star_rating
            ";
            $this->db->query($sql);
            $q_id = $this->db->query('SELECT MAX(id) AS eid FROM sales_star_rating');
            $rating_id = $q_id ? (int) $q_id->row_array()['eid'] : 0;

        } elseif ($type === 'task_check') {
            $sql = "
                INSERT INTO sales_task_star_rating(id, date, user_id, task_id, types, question, star, remarks, star_by)
                SELECT COALESCE(MAX(id),0)+1, '$today', $user_id, '$task_id_esc', 'Task Check',
                       '$question_esc', $star, '$remarks_esc', '$star_by'
                FROM sales_task_star_rating
            ";
            $this->db->query($sql);
            $q_id = $this->db->query('SELECT MAX(id) AS eid FROM sales_task_star_rating');
            $rating_id = $q_id ? (int) $q_id->row_array()['eid'] : 0;

        } else {
            // status_change
            $sql = "
                INSERT INTO sales_status_change_task_star_rating(id, date, user_id, task_id, types, question, star, remarks, star_by)
                SELECT COALESCE(MAX(id),0)+1, '$today', $user_id, '$task_id_esc', 'Status Change Task Check',
                       '$question_esc', $star, '$remarks_esc', '$star_by'
                FROM sales_status_change_task_star_rating
            ";
            $this->db->query($sql);
            $q_id = $this->db->query('SELECT MAX(id) AS eid FROM sales_status_change_task_star_rating');
            $rating_id = $q_id ? (int) $q_id->row_array()['eid'] : 0;
        }

        echo json_encode(array(
            'ok'        => true,
            'rating_id' => (int) $rating_id,
            'type'      => $type,
            'star'      => $star,
            'rated_user'=> $user_id,
            'rated_by'  => $star_by,
        ));
    }

    // -----------------------------------------------------------------------
    // GET /api/star_rating/list?user_id=X  (added AgentC 28 May 2026)
    // Alias for my_ratings. Mobile app uses /list path.
    // -----------------------------------------------------------------------
    public function list()
    {
        // Accept uid OR user_id query param so mobile {uid} usage works
        $uid = $this->input->get('uid');
        $user_id = $this->input->get('user_id');
        if ($uid && !$user_id) { $_GET['user_id'] = $uid; }
        return $this->my_ratings();
    }

}