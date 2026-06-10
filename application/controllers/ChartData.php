<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ChartData controller - 25 chart-ready JSON endpoints
 * Deployed to staging: /home/selfstaging/public_html/application/controllers/ChartData.php
 * Created: 2026-06-07
 */
class ChartData extends CI_Controller {

    /* ------------------------------------------------------------------ */
    /* COLOURS                                                               */
    /* ------------------------------------------------------------------ */
    private $COLORS = [
        '#20808D','#A84B2F','#1B474D','#BCE2E7',
        '#944454','#FFC553','#848456','#6E522B'
    ];
    private $C_APPROVED  = '#437A22';
    private $C_PENDING   = '#964219';
    private $C_REJECTED  = '#A13544';
    private $C_TEAL      = '#20808D';

    /* ------------------------------------------------------------------ */
    /* HELPERS                                                               */
    /* ------------------------------------------------------------------ */

    /** Replace common non-ASCII punctuation with ASCII equivalents */
    private function _ascii(string $s): string {
        $find    = ["\xE2\x80\x93", "\xE2\x80\x94", "\xE2\x80\x98", "\xE2\x80\x99", "\xE2\x80\x9C", "\xE2\x80\x9D", "\xC2\xA0", "\xE2\x82\xB9"];
        $replace = ['-',             '-',             "'",             "'",             '"',             '"',             ' ',        'Rs'];
        return str_replace($find, $replace, $s);
    }

    /** Recursively sanitize all strings in a structure to ASCII */
    private function _sanitize($v) {
        if (is_string($v)) return $this->_ascii($v);
        if (is_array($v))  return array_map([$this, '_sanitize'], $v);
        return $v;
    }

    /** Output standard JSON envelope and exit */
    private function _out(array $payload): void {
        header('Content-Type: application/json; charset=utf-8');
        $payload = $this->_sanitize($payload);
        echo json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
        exit;
    }

    /** Build an empty envelope for a given chart type */
    private function _empty(string $chart, string $type, string $title): array {
        $base = [
            'ok'    => true,
            'chart' => $chart,
            'type'  => $type,
            'title' => $title,
            'empty' => true,
            'meta'  => ['total' => 0],
        ];
        if (in_array($type, ['donut','funnel','radar'])) {
            $base['segments'] = [];
        } else {
            $base['labels'] = [];
            $base['series'] = [];
        }
        return $base;
    }

    /** Run a raw query and return rows as associative array */
    private function _q(string $sql): array {
        $q = $this->db->query($sql);
        return $q ? $q->result_array() : [];
    }

    /** Map cstatus int to human stage name */
    private function _cstatus_name(int $s): string {
        $map = [
            0  => 'No Status',
            1  => 'Open',
            2  => 'Reachout',
            3  => 'Tentative',
            4  => 'Will do Later',
            5  => 'Not Interested',
            6  => 'Positive',
            7  => 'Closure',
            8  => 'OPEN RPEM',
            9  => 'Dropped',
            10 => 'In Progress',
            11 => 'Stalled',
            12 => 'Follow Up',
            13 => 'Converted',
        ];
        return $map[$s] ?? "Stage $s";
    }

    /* ================================================================== */
    /* 1. planner_approval                                                   */
    /* ================================================================== */
    public function planner_approval(): void {
        $rows = $this->_q(
            "SELECT DATE_FORMAT(created_at,'%Y-%m') m,
                    CASE WHEN approvel_status IS NULL OR approvel_status='' THEN 'unset' ELSE approvel_status END st,
                    COUNT(*) cnt
             FROM task_plan_for_today
             WHERE created_at >= '2026-01-01'
             GROUP BY m, st
             ORDER BY m"
        );

        if (empty($rows)) {
            $this->_out($this->_empty('planner_approval','stacked_bar','Planner Approval by Month'));
        }

        $months = [];
        $data   = [];
        foreach ($rows as $r) {
            $months[$r['m']] = true;
            $data[$r['m']][$r['st']] = (int)$r['cnt'];
        }
        $months = array_keys($months);
        sort($months);

        $statuses = ['Approved' => $this->C_APPROVED, 'pending' => $this->C_PENDING, 'Reject' => $this->C_REJECTED, 'unset' => $this->COLORS[3]];
        $series = [];
        foreach ($statuses as $st => $color) {
            $vals = [];
            foreach ($months as $m) {
                $vals[] = $data[$m][$st] ?? 0;
            }
            $series[] = ['name' => $st, 'color' => $color, 'data' => $vals];
        }

        $total = array_sum(array_column($rows, 'cnt'));
        $this->_out([
            'ok'     => true,
            'chart'  => 'planner_approval',
            'type'   => 'stacked_bar',
            'title'  => 'Planner Approval by Month',
            'empty'  => false,
            'labels' => $months,
            'series' => $series,
            'meta'   => ['total' => $total],
        ]);
    }

    /* ================================================================== */
    /* 2. avg_tasks                                                          */
    /* ================================================================== */
    public function avg_tasks(): void {
        $rows = $this->_q(
            "SELECT DATE_FORMAT(created_at,'%Y-%m') m, ROUND(AVG(taskcnt),1) avg_t
             FROM task_plan_for_today
             WHERE created_at >= '2026-01-01'
             GROUP BY m
             ORDER BY m"
        );

        if (empty($rows)) {
            $this->_out($this->_empty('avg_tasks','line','Average Daily Tasks per Month'));
        }

        $labels = array_column($rows, 'm');
        $vals   = array_map(fn($r) => (float)$r['avg_t'], $rows);

        $this->_out([
            'ok'     => true,
            'chart'  => 'avg_tasks',
            'type'   => 'line',
            'title'  => 'Average Daily Tasks per Month',
            'empty'  => false,
            'labels' => $labels,
            'series' => [['name' => 'Avg Tasks', 'color' => $this->C_TEAL, 'data' => $vals]],
            'meta'   => ['total' => count($labels)],
        ]);
    }

    /* ================================================================== */
    /* 3. plan_health                                                        */
    /* ================================================================== */
    public function plan_health(): void {
        $rows = $this->_q(
            "SELECT CASE WHEN approvel_status IS NULL OR approvel_status='' THEN 'unset' ELSE approvel_status END st, COUNT(*) cnt
             FROM task_plan_for_today
             GROUP BY st
             ORDER BY cnt DESC"
        );

        if (empty($rows)) {
            $this->_out($this->_empty('plan_health','donut','Plan Health'));
        }

        $colorMap = ['Approved' => $this->C_APPROVED, 'pending' => $this->C_PENDING, 'Reject' => $this->C_REJECTED, 'unset' => $this->COLORS[3]];
        $segs = [];
        $total = 0;
        foreach ($rows as $i => $r) {
            $total += (int)$r['cnt'];
            $segs[] = [
                'label' => $r['st'],
                'value' => (int)$r['cnt'],
                'color' => $colorMap[$r['st']] ?? ($this->COLORS[$i % 8]),
            ];
        }

        $this->_out([
            'ok'       => true,
            'chart'    => 'plan_health',
            'type'     => 'donut',
            'title'    => 'Plan Health',
            'empty'    => false,
            'segments' => $segs,
            'meta'     => ['total' => $total],
        ]);
    }

    /* ================================================================== */
    /* 4. task_star                                                          */
    /* ================================================================== */
    public function task_star(): void {
        $rows = $this->_q(
            "SELECT star, COUNT(*) cnt
             FROM sales_task_star_rating
             GROUP BY star
             ORDER BY star"
        );

        if (empty($rows)) {
            $this->_out($this->_empty('task_star','bar','Task Star Ratings'));
        }

        $labels = [];
        $vals   = [];
        foreach ($rows as $r) {
            $labels[] = (string)$r['star'] . ' Star';
            $vals[]   = (int)$r['cnt'];
        }

        $this->_out([
            'ok'     => true,
            'chart'  => 'task_star',
            'type'   => 'bar',
            'title'  => 'Task Star Ratings',
            'empty'  => false,
            'labels' => $labels,
            'series' => [['name' => 'Count', 'color' => $this->C_TEAL, 'data' => $vals]],
            'meta'   => ['total' => array_sum($vals)],
        ]);
    }

    /* ================================================================== */
    /* 5. call_star                                                          */
    /* ================================================================== */
    public function call_star(): void {
        $rows = $this->_q(
            "SELECT star, COUNT(*) cnt
             FROM star_rating
             GROUP BY star
             ORDER BY star"
        );

        if (empty($rows)) {
            $this->_out($this->_empty('call_star','bar','Call Star Ratings'));
        }

        $labels = [];
        $vals   = [];
        foreach ($rows as $r) {
            $labels[] = (string)$r['star'] . ' Star';
            $vals[]   = (int)$r['cnt'];
        }

        $this->_out([
            'ok'     => true,
            'chart'  => 'call_star',
            'type'   => 'bar',
            'title'  => 'Call Star Ratings',
            'empty'  => false,
            'labels' => $labels,
            'series' => [['name' => 'Count', 'color' => $this->COLORS[1], 'data' => $vals]],
            'meta'   => ['total' => array_sum($vals)],
        ]);
    }

    /* ================================================================== */
    /* 6. exec_by_action                                                     */
    /* ================================================================== */
    public function exec_by_action(): void {
        $rows = $this->_q(
            "SELECT mt.taskaction action_name, COUNT(*) exec_count
             FROM task_execution_details ted
             JOIN main_task mt ON ted.main_task_id = mt.id
             GROUP BY mt.taskaction
             ORDER BY exec_count DESC
             LIMIT 12"
        );

        if (empty($rows)) {
            $this->_out($this->_empty('exec_by_action','hbar','Task Executions by Action'));
        }

        $labels = [];
        $vals   = [];
        foreach ($rows as $r) {
            $labels[] = $r['action_name'] ?: 'Unknown';
            $vals[]   = (int)$r['exec_count'];
        }

        $this->_out([
            'ok'     => true,
            'chart'  => 'exec_by_action',
            'type'   => 'hbar',
            'title'  => 'Task Executions by Action',
            'empty'  => false,
            'labels' => $labels,
            'series' => [['name' => 'Executions', 'color' => $this->C_TEAL, 'data' => $vals]],
            'meta'   => ['total' => array_sum($vals)],
        ]);
    }

    /* ================================================================== */
    /* 7. funnel_stage                                                       */
    /* ================================================================== */
    public function funnel_stage(): void {
        $rows = $this->_q(
            "SELECT cstatus, COUNT(*) cnt
             FROM init_call
             WHERE cstatus IS NOT NULL
             GROUP BY cstatus
             ORDER BY cnt DESC"
        );

        if (empty($rows)) {
            $this->_out($this->_empty('funnel_stage','funnel','Funnel Stage Distribution'));
        }

        // Order: Open(1), OPEN RPEM(8), Reachout(2), Will do Later(4), Not Interested(5), Tentative(3), Positive(6), Closure(7)
        $order = [1, 8, 2, 4, 5, 3, 6, 7];
        $byStatus = [];
        foreach ($rows as $r) {
            $byStatus[(int)$r['cstatus']] = (int)$r['cnt'];
        }

        // Build ordered segments first, then append anything else
        $seen = [];
        $segs = [];
        foreach ($order as $i => $s) {
            if (isset($byStatus[$s])) {
                $segs[] = [
                    'label' => $this->_cstatus_name($s),
                    'value' => $byStatus[$s],
                    'color' => $this->COLORS[$i % 8],
                ];
                $seen[$s] = true;
            }
        }
        // Append remaining statuses
        $ci = count($segs);
        foreach ($byStatus as $s => $cnt) {
            if (!isset($seen[$s])) {
                $segs[] = [
                    'label' => $this->_cstatus_name($s),
                    'value' => $cnt,
                    'color' => $this->COLORS[$ci % 8],
                ];
                $ci++;
            }
        }

        $total = array_sum(array_column($segs, 'value'));
        $this->_out([
            'ok'       => true,
            'chart'    => 'funnel_stage',
            'type'     => 'funnel',
            'title'    => 'Funnel Stage Distribution',
            'empty'    => false,
            'segments' => $segs,
            'meta'     => ['total' => $total],
        ]);
    }

    /* ================================================================== */
    /* 8. funnel_monthly                                                     */
    /* ================================================================== */
    public function funnel_monthly(): void {
        $rows = $this->_q(
            "SELECT DATE_FORMAT(createDate,'%Y-%m') m, cstatus, COUNT(*) cnt
             FROM init_call
             WHERE createDate >= '2026-01-01' AND cstatus IS NOT NULL
             GROUP BY m, cstatus
             ORDER BY m, cnt DESC"
        );

        if (empty($rows)) {
            $this->_out($this->_empty('funnel_monthly','stacked_bar','Monthly Funnel by Stage'));
        }

        // Top stages to show as series
        $topStages = [1 => 'Open', 8 => 'OPEN RPEM', 2 => 'Reachout', 3 => 'Tentative'];
        $months = [];
        $data   = [];
        foreach ($rows as $r) {
            $months[$r['m']] = true;
            $data[$r['m']][(int)$r['cstatus']] = (int)$r['cnt'];
        }
        $months = array_keys($months);
        sort($months);

        $series = [];
        $colors = [$this->C_TEAL, $this->COLORS[1], $this->COLORS[2], $this->COLORS[4]];
        $i = 0;
        foreach ($topStages as $s => $name) {
            $vals = [];
            foreach ($months as $m) {
                $vals[] = $data[$m][$s] ?? 0;
            }
            $series[] = ['name' => $name, 'color' => $colors[$i], 'data' => $vals];
            $i++;
        }

        $total = array_sum(array_column($rows, 'cnt'));
        $this->_out([
            'ok'     => true,
            'chart'  => 'funnel_monthly',
            'type'   => 'stacked_bar',
            'title'  => 'Monthly Funnel by Stage',
            'empty'  => false,
            'labels' => $months,
            'series' => $series,
            'meta'   => ['total' => $total, 'note' => 'Top 4 stages shown'],
        ]);
    }

    /* ================================================================== */
    /* 9. lead_source                                                        */
    /* ================================================================== */
    public function lead_source(): void {
        $rows = $this->_q(
            "SELECT COALESCE(NULLIF(lead_source,''), 'Not Captured') src, COUNT(*) cnt
             FROM init_call
             GROUP BY src
             ORDER BY cnt DESC"
        );

        if (empty($rows)) {
            $this->_out($this->_empty('lead_source','donut','Lead Source Distribution'));
        }

        $segs  = [];
        $total = 0;
        foreach ($rows as $i => $r) {
            $total += (int)$r['cnt'];
            $segs[] = [
                'label' => $r['src'],
                'value' => (int)$r['cnt'],
                'color' => $this->COLORS[$i % 8],
            ];
        }

        $this->_out([
            'ok'       => true,
            'chart'    => 'lead_source',
            'type'     => 'donut',
            'title'    => 'Lead Source Distribution',
            'empty'    => false,
            'segments' => $segs,
            'meta'     => ['total' => $total],
        ]);
    }

    /* ================================================================== */
    /* 10. closure_timeline                                                  */
    /* ================================================================== */
    public function closure_timeline(): void {
        // special_remarks is empty on staging => empty state
        $count = $this->_q("SELECT COUNT(*) cnt FROM special_remarks")[0]['cnt'] ?? 0;
        if ((int)$count === 0) {
            $this->_out($this->_empty('closure_timeline','bar','Closure Timeline Buckets'));
        }

        // If data exists: parse JSON remark_text for closure timeline key
        $rows = $this->_q("SELECT remark_text FROM special_remarks WHERE remark_text IS NOT NULL");
        $buckets = [];
        foreach ($rows as $r) {
            $j = @json_decode($r['remark_text'], true);
            if (is_array($j)) {
                $tl = $j['closure_timeline'] ?? ($j['timeline'] ?? null);
                if ($tl) {
                    $buckets[$tl] = ($buckets[$tl] ?? 0) + 1;
                }
            }
        }

        if (empty($buckets)) {
            $this->_out($this->_empty('closure_timeline','bar','Closure Timeline Buckets'));
        }

        arsort($buckets);
        $labels = array_keys($buckets);
        $vals   = array_values($buckets);

        $this->_out([
            'ok'     => true,
            'chart'  => 'closure_timeline',
            'type'   => 'bar',
            'title'  => 'Closure Timeline Buckets',
            'empty'  => false,
            'labels' => $labels,
            'series' => [['name' => 'Count', 'color' => $this->C_TEAL, 'data' => $vals]],
            'meta'   => ['total' => array_sum($vals)],
        ]);
    }

    /* ================================================================== */
    /* 11. closure_level                                                     */
    /* ================================================================== */
    public function closure_level(): void {
        $count = $this->_q("SELECT COUNT(*) cnt FROM special_remarks")[0]['cnt'] ?? 0;
        if ((int)$count === 0) {
            $this->_out($this->_empty('closure_level','hbar','Closure Level Categories'));
        }

        $rows = $this->_q("SELECT remark_text FROM special_remarks WHERE remark_text IS NOT NULL");
        $cats = [];
        foreach ($rows as $r) {
            $j = @json_decode($r['remark_text'], true);
            if (is_array($j)) {
                $lv = $j['closure_level'] ?? ($j['level'] ?? null);
                if ($lv) {
                    $cats[$lv] = ($cats[$lv] ?? 0) + 1;
                }
            }
        }

        if (empty($cats)) {
            $this->_out($this->_empty('closure_level','hbar','Closure Level Categories'));
        }

        arsort($cats);
        $labels = array_keys($cats);
        $vals   = array_values($cats);

        $this->_out([
            'ok'     => true,
            'chart'  => 'closure_level',
            'type'   => 'hbar',
            'title'  => 'Closure Level Categories',
            'empty'  => false,
            'labels' => $labels,
            'series' => [['name' => 'Count', 'color' => $this->C_TEAL, 'data' => $vals]],
            'meta'   => ['total' => array_sum($vals)],
        ]);
    }

    /* ================================================================== */
    /* 12. proposal_type                                                     */
    /* ================================================================== */
    public function proposal_type(): void {
        $rows = $this->_q(
            "SELECT CASE WHEN propasal_types IS NULL OR TRIM(propasal_types)='' OR propasal_types='0' THEN 'unlabeled' ELSE TRIM(propasal_types) END pt, COUNT(*) cnt
             FROM proposal
             GROUP BY pt
             ORDER BY cnt DESC"
        );

        if (empty($rows)) {
            $this->_out($this->_empty('proposal_type','donut','Proposal Types'));
        }

        $segs  = [];
        $total = 0;
        foreach ($rows as $i => $r) {
            $total += (int)$r['cnt'];
            $segs[] = [
                'label' => $r['pt'],
                'value' => (int)$r['cnt'],
                'color' => $this->COLORS[$i % 8],
            ];
        }

        $this->_out([
            'ok'       => true,
            'chart'    => 'proposal_type',
            'type'     => 'donut',
            'title'    => 'Proposal Types',
            'empty'    => false,
            'segments' => $segs,
            'meta'     => ['total' => $total],
        ]);
    }

    /* ================================================================== */
    /* 13. proposal_status                                                   */
    /* ================================================================== */
    public function proposal_status(): void {
        $rows = $this->_q(
            "SELECT apr, COUNT(*) cnt
             FROM proposal
             GROUP BY apr
             ORDER BY apr"
        );

        if (empty($rows)) {
            $this->_out($this->_empty('proposal_status','donut','Proposal Approval Status'));
        }

        $labelMap = ['0' => 'Pending', '1' => 'Approved', '2' => 'Returned'];
        $colorMap = ['0' => $this->C_PENDING, '1' => $this->C_APPROVED, '2' => $this->C_REJECTED];
        $segs  = [];
        $total = 0;
        foreach ($rows as $r) {
            $k = (string)$r['apr'];
            $total += (int)$r['cnt'];
            $segs[] = [
                'label' => $labelMap[$k] ?? "Status $k",
                'value' => (int)$r['cnt'],
                'color' => $colorMap[$k] ?? $this->COLORS[0],
            ];
        }

        $this->_out([
            'ok'       => true,
            'chart'    => 'proposal_status',
            'type'     => 'donut',
            'title'    => 'Proposal Approval Status',
            'empty'    => false,
            'segments' => $segs,
            'meta'     => ['total' => $total],
        ]);
    }

    /* ================================================================== */
    /* 14. proposal_sla                                                      */
    /* ================================================================== */
    public function proposal_sla(): void {
        $rows = $this->_q(
            "SELECT status, COUNT(*) cnt
             FROM proposal_sla_tracker
             GROUP BY status
             ORDER BY cnt DESC"
        );

        if (empty($rows)) {
            $this->_out($this->_empty('proposal_sla','bar','Proposal SLA Tracker Status'));
        }

        $labels = array_column($rows, 'status');
        $vals   = array_map(fn($r) => (int)$r['cnt'], $rows);

        $this->_out([
            'ok'     => true,
            'chart'  => 'proposal_sla',
            'type'   => 'bar',
            'title'  => 'Proposal SLA Tracker Status',
            'empty'  => false,
            'labels' => $labels,
            'series' => [['name' => 'Count', 'color' => $this->C_TEAL, 'data' => $vals]],
            'meta'   => ['total' => array_sum($vals)],
        ]);
    }

    /* ================================================================== */
    /* 15. mom_status                                                        */
    /* ================================================================== */
    public function mom_status(): void {
        $rows = $this->_q(
            "SELECT COALESCE(approved_status,'unapproved') st, COUNT(*) cnt
             FROM mom_data
             GROUP BY st
             ORDER BY cnt DESC"
        );

        if (empty($rows)) {
            $this->_out($this->_empty('mom_status','donut','MOM Approval Status'));
        }

        $colorMap = ['Approved' => $this->C_APPROVED, 'Reject' => $this->C_REJECTED, 'Rejected' => $this->C_REJECTED];
        $segs  = [];
        $total = 0;
        foreach ($rows as $i => $r) {
            $total += (int)$r['cnt'];
            $segs[] = [
                'label' => $r['st'],
                'value' => (int)$r['cnt'],
                'color' => $colorMap[$r['st']] ?? $this->COLORS[$i % 8],
            ];
        }

        $this->_out([
            'ok'       => true,
            'chart'    => 'mom_status',
            'type'     => 'donut',
            'title'    => 'MOM Approval Status',
            'empty'    => false,
            'segments' => $segs,
            'meta'     => ['total' => $total],
        ]);
    }

    /* ================================================================== */
    /* 16. mom_volume                                                        */
    /* ================================================================== */
    public function mom_volume(): void {
        // cdate is the real date column (datetime, default current_timestamp)
        $rows = $this->_q(
            "SELECT DATE_FORMAT(cdate,'%Y-%m') m, COUNT(*) cnt
             FROM mom_data
             WHERE cdate >= '2026-01-01'
             GROUP BY m
             ORDER BY m"
        );

        if (empty($rows)) {
            $this->_out($this->_empty('mom_volume','line','MOM Volume by Month'));
        }

        $labels = array_column($rows, 'm');
        $vals   = array_map(fn($r) => (int)$r['cnt'], $rows);

        $this->_out([
            'ok'     => true,
            'chart'  => 'mom_volume',
            'type'   => 'line',
            'title'  => 'MOM Volume by Month',
            'empty'  => false,
            'labels' => $labels,
            'series' => [['name' => 'MOM Count', 'color' => $this->C_TEAL, 'data' => $vals]],
            'meta'   => ['total' => array_sum($vals)],
        ]);
    }

    /* ================================================================== */
    /* 17. mom_quality                                                       */
    /* ================================================================== */
    public function mom_quality(): void {
        $rows = $this->_q(
            "SELECT mom_quality_grade, COUNT(*) cnt
             FROM mom_data
             WHERE mom_quality_grade IS NOT NULL
             GROUP BY mom_quality_grade
             ORDER BY FIELD(mom_quality_grade,'A','B','C','D')"
        );

        if (empty($rows)) {
            $this->_out($this->_empty('mom_quality','bar','MOM Quality Grade Distribution'));
        }

        $labels = array_column($rows, 'mom_quality_grade');
        $vals   = array_map(fn($r) => (int)$r['cnt'], $rows);

        $this->_out([
            'ok'     => true,
            'chart'  => 'mom_quality',
            'type'   => 'bar',
            'title'  => 'MOM Quality Grade Distribution',
            'empty'  => false,
            'labels' => $labels,
            'series' => [['name' => 'Count', 'color' => $this->C_TEAL, 'data' => $vals]],
            'meta'   => ['total' => array_sum($vals)],
        ]);
    }

    /* ================================================================== */
    /* 18. rp_share                                                          */
    /* ================================================================== */
    public function rp_share(): void {
        // RP = approved_status='RP', NO RP = 'NO RP', Untagged = everything else (including NULL)
        $rows = $this->_q(
            "SELECT
               SUM(approved_status='RP') rp_cnt,
               SUM(approved_status='NO RP') norp_cnt,
               SUM(approved_status NOT IN ('RP','NO RP') OR approved_status IS NULL) untagged_cnt
             FROM mom_data"
        );

        if (empty($rows)) {
            $this->_out($this->_empty('rp_share','donut','RP Share in MOM'));
        }

        $r = $rows[0];
        $rp       = (int)$r['rp_cnt'];
        $norp     = (int)$r['norp_cnt'];
        $untagged = (int)$r['untagged_cnt'];
        $total    = $rp + $norp + $untagged;

        if ($total === 0) {
            $this->_out($this->_empty('rp_share','donut','RP Share in MOM'));
        }

        $this->_out([
            'ok'       => true,
            'chart'    => 'rp_share',
            'type'     => 'donut',
            'title'    => 'RP Share in MOM',
            'empty'    => false,
            'segments' => [
                ['label' => 'RP',       'value' => $rp,       'color' => $this->C_APPROVED],
                ['label' => 'NO RP',    'value' => $norp,     'color' => $this->C_REJECTED],
                ['label' => 'Untagged', 'value' => $untagged, 'color' => $this->COLORS[3]],
            ],
            'meta' => ['total' => $total],
        ]);
    }

    /* ================================================================== */
    /* 19. rp_outcome                                                        */
    /* ================================================================== */
    public function rp_outcome(): void {
        $rows = $this->_q(
            "SELECT status, COUNT(*) cnt
             FROM role_play_session
             GROUP BY status
             ORDER BY cnt DESC"
        );

        if (empty($rows)) {
            $this->_out($this->_empty('rp_outcome','bar','Role Play Outcome'));
        }

        $labels = array_column($rows, 'status');
        $vals   = array_map(fn($r) => (int)$r['cnt'], $rows);

        $this->_out([
            'ok'     => true,
            'chart'  => 'rp_outcome',
            'type'   => 'bar',
            'title'  => 'Role Play Outcome',
            'empty'  => false,
            'labels' => $labels,
            'series' => [['name' => 'Sessions', 'color' => $this->C_TEAL, 'data' => $vals]],
            'meta'   => ['total' => array_sum($vals)],
        ]);
    }

    /* ================================================================== */
    /* 20. rp_radar                                                          */
    /* ================================================================== */
    public function rp_radar(): void {
        $rows = $this->_q(
            "SELECT
               ROUND(AVG(discovery_quality),1)    disc,
               ROUND(AVG(objection_handling),1)   obj,
               ROUND(AVG(value_articulation),1)   val,
               ROUND(AVG(next_step_clarity),1)    nxt
             FROM role_play_score"
        );

        if (empty($rows) || $rows[0]['disc'] === null) {
            $this->_out($this->_empty('rp_radar','radar','Role Play Score Radar'));
        }

        $r = $rows[0];
        $axes = [
            ['label' => 'Discovery Quality',    'value' => (float)$r['disc']],
            ['label' => 'Objection Handling',   'value' => (float)$r['obj']],
            ['label' => 'Value Articulation',   'value' => (float)$r['val']],
            ['label' => 'Next Step Clarity',    'value' => (float)$r['nxt']],
        ];

        foreach ($axes as $i => &$ax) {
            $ax['color'] = $this->COLORS[$i % 8];
        }

        $this->_out([
            'ok'       => true,
            'chart'    => 'rp_radar',
            'type'     => 'radar',
            'title'    => 'Role Play Score Radar',
            'empty'    => false,
            'segments' => $axes,
            'meta'     => ['note' => 'Axes 0-25'],
        ]);
    }

    /* ================================================================== */
    /* 21. expense_type                                                      */
    /* ================================================================== */
    public function expense_type(): void {
        $rows = $this->_q(
            "SELECT expense_type, COUNT(*) cnt, SUM(expense) total_rs
             FROM cash_expense
             WHERE created_at >= '2026-01-01'
             GROUP BY expense_type
             ORDER BY total_rs DESC
             LIMIT 12"
        );

        if (empty($rows)) {
            $this->_out($this->_empty('expense_type','hbar','Expense by Type (Rs)'));
        }

        $labels = [];
        $vals   = [];
        foreach ($rows as $r) {
            $labels[] = $r['expense_type'] ?: 'Other';
            $vals[]   = (int)round($r['total_rs']);
        }

        $this->_out([
            'ok'     => true,
            'chart'  => 'expense_type',
            'type'   => 'hbar',
            'title'  => 'Expense by Type (Rs)',
            'empty'  => false,
            'labels' => $labels,
            'series' => [['name' => 'Total Rs', 'color' => $this->C_TEAL, 'data' => $vals]],
            'meta'   => ['total' => array_sum($vals), 'note' => 'Rs amounts, top 12 by spend'],
        ]);
    }

    /* ================================================================== */
    /* 22. expense_pipeline                                                  */
    /* ================================================================== */
    public function expense_pipeline(): void {
        $rows = $this->_q(
            "SELECT
               SUM(CASE WHEN verify=0 THEN 1 ELSE 0 END)               pending_verify,
               SUM(CASE WHEN verify=1 AND admin_apr=0 THEN 1 ELSE 0 END) admin_pending,
               SUM(CASE WHEN admin_apr=1 AND account_apr=0 THEN 1 ELSE 0 END) accounts_pending,
               SUM(CASE WHEN account_apr=1 THEN 1 ELSE 0 END)           accounts_approved
             FROM cash_expense"
        );

        $r = $rows[0] ?? null;
        if (!$r) {
            $this->_out($this->_empty('expense_pipeline','status_bar','Expense Approval Pipeline'));
        }

        $stages = [
            'Pending Verify'     => (int)$r['pending_verify'],
            'Admin Approved'     => (int)$r['admin_pending'],
            'Accounts Pending'   => (int)$r['accounts_pending'],
            'Accounts Approved'  => (int)$r['accounts_approved'],
        ];

        $labels = array_keys($stages);
        $vals   = array_values($stages);

        $colors = [$this->C_PENDING, $this->COLORS[0], $this->COLORS[4], $this->C_APPROVED];
        $series = [];
        foreach ($labels as $i => $lbl) {
            $series[] = ['name' => $lbl, 'color' => $colors[$i], 'data' => [$vals[$i]]];
        }

        $this->_out([
            'ok'     => true,
            'chart'  => 'expense_pipeline',
            'type'   => 'status_bar',
            'title'  => 'Expense Approval Pipeline',
            'empty'  => false,
            'labels' => ['Expenses'],
            'series' => $series,
            'meta'   => ['total' => array_sum($vals)],
        ]);
    }

    /* ================================================================== */
    /* 23. pipeline_coverage                                                 */
    /* ================================================================== */
    public function pipeline_coverage(): void {
        $rows = $this->_q(
            "SELECT scope_uid, pipeline_rs, target_rs, ratio, band
             FROM pipeline_coverage_snapshot
             WHERE scope_type='bd'
             ORDER BY id DESC
             LIMIT 8"
        );

        if (empty($rows)) {
            $this->_out($this->_empty('pipeline_coverage','grouped_bar','Pipeline Coverage vs Target'));
        }

        $labels   = [];
        $pipeline = [];
        $target   = [];
        $bands    = [];
        foreach ($rows as $r) {
            $labels[]   = 'BD-' . $r['scope_uid'];
            $pipeline[] = (int)round($r['pipeline_rs']);
            $target[]   = (int)round($r['target_rs']);
            $bands[]    = $r['band'];
        }

        $this->_out([
            'ok'     => true,
            'chart'  => 'pipeline_coverage',
            'type'   => 'grouped_bar',
            'title'  => 'Pipeline Coverage vs Target',
            'empty'  => false,
            'labels' => $labels,
            'series' => [
                ['name' => 'Pipeline (Rs)', 'color' => $this->C_TEAL,    'data' => $pipeline],
                ['name' => 'Target (Rs)',   'color' => $this->COLORS[1], 'data' => $target],
            ],
            'meta'   => ['bands' => $bands, 'note' => 'Latest snapshot per BD'],
        ]);
    }

    /* ================================================================== */
    /* 24. ai_lead_band                                                      */
    /* ================================================================== */
    public function ai_lead_band(): void {
        $rows = $this->_q(
            "SELECT confidence_band, COUNT(*) cnt, ROUND(AVG(win_probability),2) avg_win
             FROM ai_lead_score
             GROUP BY confidence_band
             ORDER BY FIELD(confidence_band,'high','medium','low')"
        );

        if (empty($rows)) {
            $this->_out($this->_empty('ai_lead_band','grouped_bar','AI Lead Score by Confidence Band'));
        }

        // Ensure all three bands present
        $all   = ['high' => null, 'medium' => null, 'low' => null];
        foreach ($rows as $r) {
            $all[$r['confidence_band']] = $r;
        }

        $labels   = [];
        $counts   = [];
        $avgWins  = [];
        foreach ($all as $band => $r) {
            $labels[]  = ucfirst($band);
            $counts[]  = $r ? (int)$r['cnt'] : 0;
            $avgWins[] = $r ? (float)$r['avg_win'] : 0;
        }

        $this->_out([
            'ok'     => true,
            'chart'  => 'ai_lead_band',
            'type'   => 'grouped_bar',
            'title'  => 'AI Lead Score by Confidence Band',
            'empty'  => false,
            'labels' => $labels,
            'series' => [
                ['name' => 'Lead Count',       'color' => $this->C_TEAL,    'data' => $counts],
                ['name' => 'Avg Win Prob (percent)', 'color' => $this->COLORS[1], 'data' => $avgWins],
            ],
            'meta'   => ['total' => array_sum($counts)],
        ]);
    }

    /* ================================================================== */
    /* 25. day_ceremony                                                      */
    /* ================================================================== */
    public function day_ceremony(): void {
        $rows = $this->_q(
            "SELECT
               COUNT(*)                                  total,
               SUM(ustart IS NOT NULL)                   started,
               SUM(uclose IS NOT NULL)                   closed,
               IFNULL(SUM(within_geofence=1),0)          geo
             FROM day_ceremony_v2"
        );

        $r = $rows[0] ?? null;
        if (!$r || (int)$r['total'] === 0) {
            $this->_out($this->_empty('day_ceremony','grouped_bar','Day Ceremony Completion'));
        }

        $labels = ['Day Ceremony'];
        $total  = (int)$r['total'];

        $this->_out([
            'ok'     => true,
            'chart'  => 'day_ceremony',
            'type'   => 'grouped_bar',
            'title'  => 'Day Ceremony Completion',
            'empty'  => false,
            'labels' => $labels,
            'series' => [
                ['name' => 'Total',   'color' => $this->COLORS[0], 'data' => [(int)$r['total']]],
                ['name' => 'Started', 'color' => $this->C_APPROVED,'data' => [(int)$r['started']]],
                ['name' => 'Closed',  'color' => $this->C_TEAL,    'data' => [(int)$r['closed']]],
                ['name' => 'In Geofence', 'color' => $this->COLORS[3], 'data' => [(int)$r['geo']]],
            ],
            'meta'   => ['total' => $total],
        ]);
    }

    /* ================================================================== */
    /* 26. action_type_distribution                                          */
    /* ================================================================== */
    public function action_type_distribution(): void {
        $from = $this->input->get('from');
        $to   = $this->input->get('to');

        // main_task has no real date column (type_date is a tinyint flag).
        // tasktime is a varchar that may hold a date string; apply filter if provided.
        $where = '';
        if ($from && $to) {
            $from = $this->db->escape_str($from);
            $to   = $this->db->escape_str($to);
            $where = "WHERE tasktime BETWEEN '$from' AND '$to'";
        }

        $rows = $this->_q(
            "SELECT taskaction, COUNT(*) AS cnt
             FROM main_task
             $where
             GROUP BY taskaction
             ORDER BY taskaction"
        );

        if (empty($rows)) {
            $env = $this->_empty('action_type', 'bar', 'Action Type Distribution');
            $env['series'] = [['name' => 'Tasks', 'color' => '#20808D', 'data' => []]];
            $this->_out($env);
        }

        $labels = [];
        $vals   = [];
        foreach ($rows as $r) {
            $labels[] = $r['taskaction'] ?: 'Unknown';
            $vals[]   = (int)$r['cnt'];
        }

        $meta = ['total' => array_sum($vals)];
        if ($from && $to) {
            $meta['from'] = $from;
            $meta['to']   = $to;
        }

        $this->_out([
            'ok'     => true,
            'chart'  => 'action_type',
            'type'   => 'bar',
            'title'  => 'Action Type Distribution',
            'empty'  => false,
            'labels' => $labels,
            'series' => [['name' => 'Tasks', 'color' => '#20808D', 'data' => $vals]],
            'meta'   => $meta,
        ]);
    }

    /* ================================================================== */
    /* 27. plan_vs_completed                                                 */
    /* ================================================================== */
    public function plan_vs_completed(): void {
        $from = $this->input->get('from');
        $to   = $this->input->get('to');

        $where = '';
        if ($from && $to) {
            $from = $this->db->escape_str($from);
            $to   = $this->db->escape_str($to);
            $where = "WHERE plan_date BETWEEN '$from' AND '$to'";
        }

        $rows = $this->_q(
            "SELECT plan_date,
                    SUM(tasks_planned)   AS planned,
                    SUM(tasks_completed) AS completed
             FROM planner_coach_execution
             $where
             GROUP BY plan_date
             ORDER BY plan_date"
        );

        if (empty($rows)) {
            $env = $this->_empty('plan_vs_completed', 'grouped_bar', 'Plan vs Completed');
            $env['series'] = [
                ['name' => 'Planned',   'color' => '#1B474D', 'data' => []],
                ['name' => 'Completed', 'color' => '#20808D', 'data' => []],
            ];
            $this->_out($env);
        }

        $labels    = [];
        $planned   = [];
        $completed = [];
        foreach ($rows as $r) {
            $labels[]    = $r['plan_date'];
            $planned[]   = (int)$r['planned'];
            $completed[] = (int)$r['completed'];
        }

        $meta = ['total_planned' => array_sum($planned), 'total_completed' => array_sum($completed)];
        if ($from && $to) {
            $meta['from'] = $from;
            $meta['to']   = $to;
        }

        $this->_out([
            'ok'     => true,
            'chart'  => 'plan_vs_completed',
            'type'   => 'grouped_bar',
            'title'  => 'Plan vs Completed',
            'empty'  => false,
            'labels' => $labels,
            'series' => [
                ['name' => 'Planned',   'color' => '#1B474D', 'data' => $planned],
                ['name' => 'Completed', 'color' => '#20808D', 'data' => $completed],
            ],
            'meta'   => $meta,
        ]);
    }

}