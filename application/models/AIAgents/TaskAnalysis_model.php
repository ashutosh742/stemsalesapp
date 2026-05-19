<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TaskAnalysis_model extends CI_Model {

    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Menu_model');
        $this->load->model('Report_model');
        $this->load->model('ChatAI_model');
        $this->load->helper('url');
        
    }

    /**
     * Main method to process task analysis
     * 
     * @param string $message User query
     * @param string $analysisType Type of analysis
     * @param string $sdate Start date
     * @param string $edate End date
     * @return array Processed analysis data
     */
    public function process_task_analysis($message, $analysisType, $sdate, $edate) 
    {
        // Get task data from database
        $taskData = $this->Menu_model->GetClusterTaskOnSelfOrOtherFunnelTask(
            $this->uid, $sdate, $edate, 'all', $this->uid, 'all'
        );

      

        // Process task data into structured format
        $processedData = $this->process_raw_task_data($taskData);

        // Get user attendance data
        $userAttendanceData = $this->get_user_attendance_data($sdate);

        // Merge task data with user information
        $analysisData = $this->prepare_analysis_data(
            $processedData, 
            $userAttendanceData, 
            $sdate, 
            $edate
        );

        // Extract specific user request if present
        $specificUser = $this->extract_specific_user_request($message, $analysisData['users']);

        // Generate appropriate prompt based on request type
        $prompt = $specificUser 
            ? $this->generate_specific_user_analysis_prompt($message, $specificUser, $analysisData)
            : $this->generate_team_analysis_prompt($message, $analysisData);

        // Get AI response
        $aiResponse = $this->ChatAI_model->call_chatgpt_api($prompt, []);

        // Prepare frontend data
        $frontendData = $this->prepare_frontend_data($analysisData, $specificUser);

        return [
            'content' => $aiResponse,
            'data' => $frontendData
        ];
    }

    /**
     * Process raw task data into structured format
     * 
     * @param array $rawData Raw task data from database
     * @return array Structured task data
     */
    private function process_raw_task_data($rawData)
    {
        $processedData = [];

        if (empty($rawData) || !is_array($rawData)) {
            return $processedData;
        }

        foreach ($rawData as $task) {
            $processedData[] = [
                'user_id' => $task->user_id ?? 0,
                'username' => $task->username ?? 'Unknown User',
                'total_task' => (int)($task->total_task ?? 0),
                'total_complete_task' => (int)($task->total_complete_task ?? 0),
                'total_pending_task' => (int)($task->total_pending_task ?? 0),
                'completion_rate' => $this->calculate_completion_rate($task),
                'task_efficiency' => $this->calculate_task_efficiency($task),
                'own_funnel_stats' => $this->extract_own_funnel_stats($task),
                'team_funnel_stats' => $this->extract_team_funnel_stats($task),
                'status_change_stats' => $this->extract_status_change_stats($task)
            ];
        }

        return $processedData;
    }

    /**
     * Calculate task completion rate
     * 
     * @param object $task Task object
     * @return float Completion rate percentage
     */
    private function calculate_completion_rate($task)
    {
        $total = (int)($task->total_task ?? 0);
        $completed = (int)($task->total_complete_task ?? 0);
        
        return $total > 0 ? round(($completed / $total) * 100, 2) : 0;
    }

    /**
     * Calculate task efficiency score
     * 
     * @param object $task Task object
     * @return float Efficiency score
     */
    private function calculate_task_efficiency($task)
    {
        $completed = (int)($task->total_complete_task ?? 0);
        $total = (int)($task->total_task ?? 0);
        $specialRemarks = (int)($task->total_special_remarks_complete_task ?? 0);
        $statusChanges = (int)($task->total_status_change_task ?? 0);
        
        if ($total === 0) return 0;
        
        $baseEfficiency = ($completed / $total) * 100;
        $qualityBonus = ($specialRemarks / max($completed, 1)) * 10;
        $proactivityBonus = ($statusChanges / max($completed, 1)) * 15;
        
        return min(100, $baseEfficiency + $qualityBonus + $proactivityBonus);
    }

    /**
     * Extract own funnel statistics
     * 
     * @param object $task Task object
     * @return array Own funnel statistics
     */
    private function extract_own_funnel_stats($task)
    {
        return [
            'total' => (int)($task->total_own_funnel_task ?? 0),
            'completed' => (int)($task->total_own_funnel_complete_task ?? 0),
            'pending' => (int)($task->total_own_funnel_pending_task ?? 0),
            'completion_rate' => $this->calculate_own_funnel_completion($task),
            'aypy_completed' => (int)($task->total_own_aypy_funnel_complete_task ?? 0),
            'aypn_completed' => (int)($task->total_own_aypn_funnel_complete_task ?? 0),
            'status_changes' => (int)($task->total_own_funnel_status_change_task ?? 0)
        ];
    }

    /**
     * Calculate own funnel completion rate
     * 
     * @param object $task Task object
     * @return float Completion rate percentage
     */
    private function calculate_own_funnel_completion($task)
    {
        $total = (int)($task->total_own_funnel_task ?? 0);
        $completed = (int)($task->total_own_funnel_complete_task ?? 0);
        
        return $total > 0 ? round(($completed / $total) * 100, 2) : 0;
    }

    /**
     * Extract team funnel statistics
     * 
     * @param object $task Task object
     * @return array Team funnel statistics
     */
    private function extract_team_funnel_stats($task)
    {
        return [
            'total' => (int)($task->total_team_funnel_task ?? 0),
            'completed' => (int)($task->total_team_funnel_complete_task ?? 0),
            'pending' => (int)($task->total_team_funnel_pending_task ?? 0),
            'completion_rate' => $this->calculate_team_funnel_completion($task),
            'aypy_completed' => (int)($task->total_team_aypy_funnel_complete_task ?? 0),
            'aypn_completed' => (int)($task->total_team_aypn_funnel_complete_task ?? 0),
            'status_changes' => (int)($task->total_team_funnel_status_change_task ?? 0)
        ];
    }

    /**
     * Calculate team funnel completion rate
     * 
     * @param object $task Task object
     * @return float Completion rate percentage
     */
    private function calculate_team_funnel_completion($task)
    {
        $total = (int)($task->total_team_funnel_task ?? 0);
        $completed = (int)($task->total_team_funnel_complete_task ?? 0);
        
        return $total > 0 ? round(($completed / $total) * 100, 2) : 0;
    }

    /**
     * Extract status change statistics
     * 
     * @param object $task Task object
     * @return array Status change statistics
     */
    private function extract_status_change_stats($task)
    {
        return [
            'total_status_changes' => (int)($task->total_status_change_task ?? 0),
            'own_funnel_status_changes' => (int)($task->total_own_funnel_status_change_task ?? 0),
            'team_funnel_status_changes' => (int)($task->total_team_funnel_status_change_task ?? 0),
            'own_aypy_status_changes' => (int)($task->total_own_funnel_aypy_status_change_task ?? 0),
            'team_aypy_status_changes' => (int)($task->total_team_funnel_aypy_status_change_task ?? 0)
        ];
    }

    /**
     * Get user attendance data
     * 
     * @param string $date Date for attendance check
     * @return array Attendance data
     */
    private function get_user_attendance_data($date)
    {
        $presentUsers = $this->Report_model->GetTodaysAllUserByReportingManager(
            'total_present', $this->uid, $date
        );
        
        return $this->process_attendance_data($presentUsers);
    }

    /**
     * Process attendance data
     * 
     * @param array $attendanceRaw Raw attendance data
     * @return array Processed attendance data
     */
    private function process_attendance_data($attendanceRaw)
    {
        $processed = [];
        
        if (empty($attendanceRaw) || !is_array($attendanceRaw)) {
            return $processed;
        }

        foreach ($attendanceRaw as $attendance) {
            $userId = $attendance->user_id ?? 0;
            $processed[$userId] = [
                'is_present' => true,
                'start_from' => $attendance->start_from ?? 'Not Present',
                'start_time' => $attendance->start_date ?? null,
                'end_time' => $attendance->uclose_date ?? null,
                'start_image' => $this->construct_image_url($attendance->start_image ?? null),
                'end_image' => $this->construct_image_url($attendance->close_image ?? null),
                'has_start_image' => !empty($attendance->start_image),
                'has_end_image' => !empty($attendance->close_image),
                'start_location' => [
                    'latitude' => $attendance->start_slatitude ?? null,
                    'longitude' => $attendance->start_slongitude ?? null
                ],
                'end_location' => [
                    'latitude' => $attendance->close_clatitude ?? null,
                    'longitude' => $attendance->close_clongitude ?? null
                ],
                'work_mode' => $attendance->type_name ?? 'Unknown'
            ];
        }
        
        return $processed;
    }

    /**
     * Prepare comprehensive analysis data
     * 
     * @param array $taskData Processed task data
     * @param array $attendanceData Attendance data
     * @param string $startDate Start date
     * @param string $endDate End date
     * @return array Comprehensive analysis data
     */
    private function prepare_analysis_data($taskData, $attendanceData, $startDate, $endDate)
    {
        // Calculate team statistics
        $teamStats = $this->calculate_team_statistics($taskData, $attendanceData);

        // Prepare user details
        $users = $this->prepare_user_details($taskData, $attendanceData);

        return [
            'period' => "{$startDate} to {$endDate}",
            'users' => $users,
            'team_statistics' => $teamStats,
            'summary' => $this->prepare_summary_statistics($users, $teamStats),
            'query_date' => $startDate,
            'analysis_date' => date('Y-m-d H:i:s')
        ];
    }

    /**
     * Calculate team statistics
     * 
     * @param array $taskData Task data
     * @param array $attendanceData Attendance data
     * @return array Team statistics
     */
    private function calculate_team_statistics($taskData, $attendanceData)
    {
        if (empty($taskData)) {
            return $this->get_empty_team_stats();
        }

        $stats = [
            'total_tasks' => 0,
            'total_completed' => 0,
            'total_pending' => 0,
            'total_users' => count($taskData),
            'present_users' => 0,
            'average_completion_rate' => 0,
            'total_efficiency_score' => 0,
            'total_own_funnel_tasks' => 0,
            'total_team_funnel_tasks' => 0,
            'total_status_changes' => 0,
            'top_performers' => [],
            'needs_attention' => []
        ];

        $completionRates = [];
        $efficiencyScores = [];

        foreach ($taskData as $user) {
            $userId = $user['user_id'];
            
            // Aggregate totals
            $stats['total_tasks'] += $user['total_task'];
            $stats['total_completed'] += $user['total_complete_task'];
            $stats['total_pending'] += $user['total_pending_task'];
            $stats['total_own_funnel_tasks'] += $user['own_funnel_stats']['total'];
            $stats['total_team_funnel_tasks'] += $user['team_funnel_stats']['total'];
            $stats['total_status_changes'] += $user['status_change_stats']['total_status_changes'];
            
            // Track completion rates and efficiency
            $completionRates[] = $user['completion_rate'];
            $efficiencyScores[] = $user['task_efficiency'];
            
            // Check attendance
            if (isset($attendanceData[$userId]) && $attendanceData[$userId]['is_present']) {
                $stats['present_users']++;
            }

            // Identify top performers (completion rate > 90% and efficiency > 80)
            if ($user['completion_rate'] > 90 && $user['task_efficiency'] > 80) {
                $stats['top_performers'][] = [
                    'user_id' => $userId,
                    'username' => $user['username'],
                    'completion_rate' => $user['completion_rate'],
                    'efficiency' => $user['task_efficiency']
                ];
            }

            // Identify users needing attention (completion rate < 60%)
            if ($user['completion_rate'] < 60) {
                $stats['needs_attention'][] = [
                    'user_id' => $userId,
                    'username' => $user['username'],
                    'completion_rate' => $user['completion_rate'],
                    'pending_tasks' => $user['total_pending_task']
                ];
            }
        }

        // Calculate averages
        if (!empty($completionRates)) {
            $stats['average_completion_rate'] = round(
                array_sum($completionRates) / count($completionRates), 
                2
            );
            $stats['total_efficiency_score'] = round(
                array_sum($efficiencyScores) / count($efficiencyScores), 
                2
            );
        }

        return $stats;
    }

    /**
     * Get empty team statistics structure
     * 
     * @return array Empty statistics
     */
    private function get_empty_team_stats()
    {
        return [
            'total_tasks' => 0,
            'total_completed' => 0,
            'total_pending' => 0,
            'total_users' => 0,
            'present_users' => 0,
            'average_completion_rate' => 0,
            'total_efficiency_score' => 0,
            'total_own_funnel_tasks' => 0,
            'total_team_funnel_tasks' => 0,
            'total_status_changes' => 0,
            'top_performers' => [],
            'needs_attention' => []
        ];
    }

    /**
     * Prepare user details with task and attendance data
     * 
     * @param array $taskData Task data
     * @param array $attendanceData Attendance data
     * @return array User details
     */
    private function prepare_user_details($taskData, $attendanceData)
    {
        $users = [];

        foreach ($taskData as $task) {
            $userId = $task['user_id'];
            $attendance = $attendanceData[$userId] ?? null;

            $users[] = [
                'user_id' => $userId,
                'username' => $task['username'],
                'task_statistics' => [
                    'total' => $task['total_task'],
                    'completed' => $task['total_complete_task'],
                    'pending' => $task['total_pending_task'],
                    'completion_rate' => $task['completion_rate'],
                    'efficiency_score' => $task['task_efficiency'],
                    'special_remarks' => (int)($task['special_remarks'] ?? 0),
                    'next_step_confirmations' => (int)($task['next_step_confirmations'] ?? 0)
                ],
                'funnel_analysis' => [
                    'own_funnel' => $task['own_funnel_stats'],
                    'team_funnel' => $task['team_funnel_stats'],
                    'funnel_ratio' => $this->calculate_funnel_ratio($task),
                    'aypy_effectiveness' => $this->calculate_aypy_effectiveness($task)
                ],
                'status_change_analysis' => $task['status_change_stats'],
                'attendance' => $attendance ?: [
                    'is_present' => false,
                    'work_mode' => 'Absent',
                    'start_time' => null,
                    'end_time' => null
                ],
                'performance_metrics' => $this->calculate_performance_metrics($task),
                'rank' => 0 // Will be calculated later
            ];
        }

        // Calculate ranks
        $users = $this->calculate_user_ranks($users);

        return $users;
    }

    /**
     * Calculate funnel ratio (own vs team)
     * 
     * @param array $task Task data
     * @return array Funnel ratios
     */
    private function calculate_funnel_ratio($task)
    {
        $ownTotal = $task['own_funnel_stats']['total'];
        $teamTotal = $task['team_funnel_stats']['total'];
        $total = $ownTotal + $teamTotal;

        if ($total === 0) {
            return ['own' => 0, 'team' => 0];
        }

        return [
            'own' => round(($ownTotal / $total) * 100, 2),
            'team' => round(($teamTotal / $total) * 100, 2)
        ];
    }

    /**
     * Calculate AYPY effectiveness
     * 
     * @param array $task Task data
     * @return array AYPY metrics
     */
    private function calculate_aypy_effectiveness($task)
    {
        $ownAypy = $task['own_funnel_stats']['aypy_completed'];
        $ownAypn = $task['own_funnel_stats']['aypn_completed'];
        $teamAypy = $task['team_funnel_stats']['aypy_completed'];
        $teamAypn = $task['team_funnel_stats']['aypn_completed'];

        $totalOwn = $ownAypy + $ownAypn;
        $totalTeam = $teamAypy + $teamAypn;

        return [
            'own_aypy_rate' => $totalOwn > 0 ? round(($ownAypy / $totalOwn) * 100, 2) : 0,
            'team_aypy_rate' => $totalTeam > 0 ? round(($teamAypy / $totalTeam) * 100, 2) : 0,
            'total_aypy_conversion' => ($totalOwn + $totalTeam) > 0 ? 
                round((($ownAypy + $teamAypy) / ($totalOwn + $totalTeam)) * 100, 2) : 0
        ];
    }

    /**
     * Calculate performance metrics
     * 
     * @param array $task Task data
     * @return array Performance metrics
     */
    private function calculate_performance_metrics($task)
    {
        return [
            'productivity_score' => $this->calculate_productivity_score($task),
            'quality_score' => $this->calculate_quality_score($task),
            'proactivity_score' => $this->calculate_proactivity_score($task),
            'overall_performance' => $this->calculate_overall_performance($task)
        ];
    }

    /**
     * Calculate productivity score
     * 
     * @param array $task Task data
     * @return float Productivity score
     */
    private function calculate_productivity_score($task)
    {
        $completed = $task['total_complete_task'];
        $total = $task['total_task'];
        
        if ($total === 0) return 0;
        
        return round(($completed / $total) * 100, 2);
    }

    /**
     * Calculate quality score
     * 
     * @param array $task Task data
     * @return float Quality score
     */
    private function calculate_quality_score($task)
    {
        $specialRemarks = (int)($task['special_remarks'] ?? 0);
        $completed = max($task['total_complete_task'], 1);
        
        return round(($specialRemarks / $completed) * 100, 2);
    }

    /**
     * Calculate proactivity score
     * 
     * @param array $task Task data
     * @return float Proactivity score
     */
    private function calculate_proactivity_score($task)
    {
        $statusChanges = $task['status_change_stats']['total_status_changes'];
        $completed = max($task['total_complete_task'], 1);
        
        return min(100, round(($statusChanges / $completed) * 100, 2));
    }

    /**
     * Calculate overall performance score
     * 
     * @param array $task Task data
     * @return float Overall performance
     */
    private function calculate_overall_performance($task)
    {
        $productivity = $this->calculate_productivity_score($task);
        $quality = $this->calculate_quality_score($task);
        $proactivity = $this->calculate_proactivity_score($task);
        
        // Weighted average: Productivity 50%, Quality 30%, Proactivity 20%
        return round(
            ($productivity * 0.5) + 
            ($quality * 0.3) + 
            ($proactivity * 0.2), 
            2
        );
    }

    /**
     * Calculate user ranks based on overall performance
     * 
     * @param array $users Users data
     * @return array Users with ranks
     */
    private function calculate_user_ranks($users)
    {
        // Sort users by overall performance
        usort($users, function($a, $b) {
            return $b['performance_metrics']['overall_performance'] - 
                   $a['performance_metrics']['overall_performance'];
        });

        // Assign ranks
        $rank = 1;
        foreach ($users as $key => &$user) {
            // Handle ties
            if ($key > 0 && 
                $user['performance_metrics']['overall_performance'] === 
                $users[$key - 1]['performance_metrics']['overall_performance']) {
                $user['rank'] = $rank;
            } else {
                $user['rank'] = ++$rank;
            }
        }

        return $users;
    }

    /**
     * Prepare summary statistics
     * 
     * @param array $users Users data
     * @param array $teamStats Team statistics
     * @return array Summary statistics
     */
    private function prepare_summary_statistics($users, $teamStats)
    {
        return [
            'total_users_analyzed' => count($users),
            'present_users' => $teamStats['present_users'],
            'absent_users' => count($users) - $teamStats['present_users'],
            'attendance_rate' => count($users) > 0 ? 
                round(($teamStats['present_users'] / count($users)) * 100, 2) : 0,
            'total_task_volume' => $teamStats['total_tasks'],
            'overall_completion_rate' => $teamStats['average_completion_rate'],
            'overall_efficiency' => $teamStats['total_efficiency_score'],
            'team_productivity' => $this->calculate_team_productivity($users),
            'key_insights' => $this->generate_key_insights($users, $teamStats)
        ];
    }

    /**
     * Calculate team productivity metrics
     * 
     * @param array $users Users data
     * @return array Productivity metrics
     */
    private function calculate_team_productivity($users)
    {
        if (empty($users)) {
            return ['average_tasks_per_user' => 0, 'average_completion_time' => 0];
        }

        $totalTasks = 0;
        $totalCompleted = 0;
        $completionRates = [];

        foreach ($users as $user) {
            $totalTasks += $user['task_statistics']['total'];
            $totalCompleted += $user['task_statistics']['completed'];
            $completionRates[] = $user['task_statistics']['completion_rate'];
        }

        return [
            'average_tasks_per_user' => round($totalTasks / count($users), 2),
            'average_completed_per_user' => round($totalCompleted / count($users), 2),
            'median_completion_rate' => $this->calculate_median($completionRates),
            'std_deviation_completion' => $this->calculate_standard_deviation($completionRates)
        ];
    }

    /**
     * Generate key insights from data
     * 
     * @param array $users Users data
     * @param array $teamStats Team statistics
     * @return array Key insights
     */
    private function generate_key_insights($users, $teamStats)
    {
        $insights = [];

        if (empty($users)) {
            return ['No user data available for analysis'];
        }

        // Insight 1: Overall performance
        if ($teamStats['average_completion_rate'] > 90) {
            $insights[] = "Excellent team performance with {$teamStats['average_completion_rate']}% average completion rate";
        } elseif ($teamStats['average_completion_rate'] > 75) {
            $insights[] = "Good team performance with {$teamStats['average_completion_rate']}% average completion rate";
        } else {
            $insights[] = "Team needs improvement - current completion rate is {$teamStats['average_completion_rate']}%";
        }

        // Insight 2: Top performers
        if (!empty($teamStats['top_performers'])) {
            $topCount = count($teamStats['top_performers']);
            $insights[] = "{$topCount} top performer(s) identified with completion rate > 90%";
        }

        // Insight 3: Areas needing attention
        if (!empty($teamStats['needs_attention'])) {
            $attentionCount = count($teamStats['needs_attention']);
            $insights[] = "{$attentionCount} user(s) need attention with completion rate < 60%";
        }

        // Insight 4: Task distribution
        $ownFunnelPercentage = $teamStats['total_own_funnel_tasks'] > 0 ? 
            round(($teamStats['total_own_funnel_tasks'] / ($teamStats['total_own_funnel_tasks'] + $teamStats['total_team_funnel_tasks'])) * 100, 2) : 0;
        $insights[] = "Task distribution: {$ownFunnelPercentage}% own funnel tasks, " . (100 - $ownFunnelPercentage) . "% team funnel tasks";

        return $insights;
    }

    /**
     * Calculate median of an array
     * 
     * @param array $numbers Array of numbers
     * @return float Median value
     */
    private function calculate_median($numbers)
    {
        if (empty($numbers)) {
            return 0;
        }

        sort($numbers);
        $count = count($numbers);
        $middle = floor($count / 2);

        if ($count % 2 == 0) {
            return round(($numbers[$middle - 1] + $numbers[$middle]) / 2, 2);
        } else {
            return round($numbers[$middle], 2);
        }
    }

    /**
     * Calculate standard deviation
     * 
     * @param array $numbers Array of numbers
     * @return float Standard deviation
     */
    private function calculate_standard_deviation($numbers)
    {
        if (empty($numbers) || count($numbers) < 2) {
            return 0;
        }

        $mean = array_sum($numbers) / count($numbers);
        $sum = 0;

        foreach ($numbers as $number) {
            $sum += pow($number - $mean, 2);
        }

        return round(sqrt($sum / (count($numbers) - 1)), 2);
    }

    /**
     * Extract specific user from query
     * 
     * @param string $message User query
     * @param array $users Users data
     * @return array|null Specific user data or null
     */
    public function extract_specific_user_request($message, $users)
    {
        if (empty($message) || empty($users)) {
            return null;
        }

        $lowerMessage = strtolower(trim($message));

        foreach ($users as $user) {
            $username = strtolower($user['username'] ?? '');
            
            // Direct username match
            if (strpos($lowerMessage, $username) !== false) {
                return $user;
            }

            // Check for name parts
            $nameParts = explode(' ', $username);
            foreach ($nameParts as $part) {
                if (strlen($part) > 3 && strpos($lowerMessage, $part) !== false) {
                    return $user;
                }
            }

            // Check for user ID
            $userId = (string)($user['user_id'] ?? '');
            if (strpos($lowerMessage, $userId) !== false) {
                return $user;
            }
        }

        return null;
    }

    /**
     * Generate AI prompt for specific user analysis
     * 
     * @param string $message User query
     * @param array $user User data
     * @param array $teamData Team data
     * @return string Formatted prompt
     */
    public function generate_specific_user_analysis_prompt($message, $user, $teamData)
    {
        $formattedData = "SPECIFIC USER TASK ANALYSIS - {$user['username']} (ID: {$user['user_id']})\n\n";
        
        $formattedData .= "PERFORMANCE SUMMARY:\n";
        $formattedData .= "- Overall Rank: #{$user['rank']} out of {$teamData['team_statistics']['total_users']} users\n";
        $formattedData .= "- Task Completion Rate: {$user['task_statistics']['completion_rate']}%\n";
        $formattedData .= "- Task Efficiency Score: {$user['task_efficiency']}/100\n";
        $formattedData .= "- Overall Performance Score: {$user['performance_metrics']['overall_performance']}/100\n\n";
        
        $formattedData .= "TASK STATISTICS:\n";
        $formattedData .= "- Total Tasks Assigned: {$user['task_statistics']['total']}\n";
        $formattedData .= "- Tasks Completed: {$user['task_statistics']['completed']}\n";
        $formattedData .= "- Tasks Pending: {$user['task_statistics']['pending']}\n";
        $formattedData .= "- Special Remarks Tasks: {$user['task_statistics']['special_remarks']}\n";
        $formattedData .= "- Next Step Confirmations: {$user['task_statistics']['next_step_confirmations']}\n\n";
        
        $formattedData .= "FUNNEL ANALYSIS:\n";
        $formattedData .= "Own Funnel:\n";
        $formattedData .= "  - Total Tasks: {$user['funnel_analysis']['own_funnel']['total']}\n";
        $formattedData .= "  - Completed: {$user['funnel_analysis']['own_funnel']['completed']}\n";
        $formattedData .= "  - Completion Rate: {$user['funnel_analysis']['own_funnel']['completion_rate']}%\n";
        $formattedData .= "  - AYPY Completed: {$user['funnel_analysis']['own_funnel']['aypy_completed']}\n";
        $formattedData .= "  - AYPN Completed: {$user['funnel_analysis']['own_funnel']['aypn_completed']}\n\n";
        
        $formattedData .= "Team Funnel:\n";
        $formattedData .= "  - Total Tasks: {$user['funnel_analysis']['team_funnel']['total']}\n";
        $formattedData .= "  - Completed: {$user['funnel_analysis']['team_funnel']['completed']}\n";
        $formattedData .= "  - Completion Rate: {$user['funnel_analysis']['team_funnel']['completion_rate']}%\n";
        $formattedData .= "  - AYPY Completed: {$user['funnel_analysis']['team_funnel']['aypy_completed']}\n";
        $formattedData .= "  - AYPN Completed: {$user['funnel_analysis']['team_funnel']['aypn_completed']}\n\n";
        
        $formattedData .= "Funnel Distribution:\n";
        $formattedData .= "  - Own Funnel: {$user['funnel_analysis']['funnel_ratio']['own']}%\n";
        $formattedData .= "  - Team Funnel: {$user['funnel_analysis']['funnel_ratio']['team']}%\n\n";
        
        $formattedData .= "AYPY Effectiveness:\n";
        $formattedData .= "  - Own AYPY Rate: {$user['funnel_analysis']['aypy_effectiveness']['own_aypy_rate']}%\n";
        $formattedData .= "  - Team AYPY Rate: {$user['funnel_analysis']['aypy_effectiveness']['team_aypy_rate']}%\n";
        $formattedData .= "  - Overall AYPY Conversion: {$user['funnel_analysis']['aypy_effectiveness']['total_aypy_conversion']}%\n\n";
        
        $formattedData .= "STATUS CHANGE ACTIVITY:\n";
        $formattedData .= "- Total Status Changes: {$user['status_change_analysis']['total_status_changes']}\n";
        $formattedData .= "- Own Funnel Status Changes: {$user['status_change_analysis']['own_funnel_status_changes']}\n";
        $formattedData .= "- Team Funnel Status Changes: {$user['status_change_analysis']['team_funnel_status_changes']}\n";
        $formattedData .= "- AYPY Status Changes: {$user['status_change_analysis']['own_aypy_status_changes']} (own), {$user['status_change_analysis']['team_aypy_status_changes']} (team)\n\n";
        
        $formattedData .= "ATTENDANCE STATUS:\n";
        $formattedData .= "- Present: " . ($user['attendance']['is_present'] ? 'Yes' : 'No') . "\n";
        if ($user['attendance']['is_present']) {
            $formattedData .= "- Work Mode: {$user['attendance']['work_mode']}\n";
            $formattedData .= "- Start Time: {$user['attendance']['start_time']}\n";
            $formattedData .= "- End Time: {$user['attendance']['end_time']}\n";
        }
        
        $formattedData .= "\nTEAM CONTEXT:\n";
        $formattedData .= "- Team Size: {$teamData['team_statistics']['total_users']} users\n";
        $formattedData .= "- Team Average Completion Rate: {$teamData['team_statistics']['average_completion_rate']}%\n";
        $formattedData .= "- User's Performance vs Average: " . 
            round($user['task_statistics']['completion_rate'] - $teamData['team_statistics']['average_completion_rate'], 2) . "%\n";
        
        $prompt = "You are a business analytics AI specializing in task performance analysis. ";
        $prompt .= "Analyze the following user task data and provide detailed, actionable insights:\n\n";
        $prompt .= $formattedData . "\n\n";
        $prompt .= "User Query: {$message}\n\n";
        $prompt .= "Provide a comprehensive analysis with these sections:\n";
        $prompt .= "1. PERFORMANCE OVERVIEW: Brief summary of user's performance\n";
        $prompt .= "2. STRENGTHS IDENTIFIED: Key areas where the user excels\n";
        $prompt .= "3. AREAS FOR IMPROVEMENT: Specific metrics that need attention\n";
        $prompt .= "4. FUNNEL ANALYSIS: Effectiveness in own vs team funnel tasks\n";
        $prompt .= "5. AYPY PERFORMANCE: Analysis of 'Appointment Yes - Payment Yes' conversions\n";
        $prompt .= "6. PROACTIVITY ASSESSMENT: Status change activity and task management\n";
        $prompt .= "7. RECOMMENDATIONS: Actionable suggestions for improvement\n";
        $prompt .= "8. COMPARATIVE ANALYSIS: How user compares to team averages\n\n";
        
        $prompt .= "Format: Use clear sections with bullet points. Include specific numbers and percentages from the data.";
        
        return $prompt;
    }

    /**
     * Generate AI prompt for team analysis
     * 
     * @param string $message User query
     * @param array $analysisData Analysis data
     * @return string Formatted prompt
     */
    public function generate_team_analysis_prompt($message, $analysisData)
    {
        $teamStats = $analysisData['team_statistics'];
        $summary = $analysisData['summary'];
        
        $formattedData = "TEAM TASK ANALYSIS REPORT\n";
        $formattedData .= "Period: {$analysisData['period']}\n";
        $formattedData .= "Analysis Date: {$analysisData['analysis_date']}\n\n";
        
        $formattedData .= "EXECUTIVE SUMMARY:\n";
        $formattedData .= "- Total Users Analyzed: {$summary['total_users_analyzed']}\n";
        $formattedData .= "- Present Users: {$summary['present_users']} ({$summary['attendance_rate']}% attendance rate)\n";
        $formattedData .= "- Total Task Volume: {$summary['total_task_volume']} tasks\n";
        $formattedData .= "- Overall Completion Rate: {$summary['overall_completion_rate']}%\n";
        $formattedData .= "- Team Efficiency Score: {$summary['overall_efficiency']}/100\n\n";
        
        $formattedData .= "DETAILED STATISTICS:\n";
        $formattedData .= "- Total Tasks Completed: {$teamStats['total_completed']}\n";
        $formattedData .= "- Total Tasks Pending: {$teamStats['total_pending']}\n";
        $formattedData .= "- Own Funnel Tasks: {$teamStats['total_own_funnel_tasks']}\n";
        $formattedData .= "- Team Funnel Tasks: {$teamStats['total_team_funnel_tasks']}\n";
        $formattedData .= "- Total Status Changes: {$teamStats['total_status_changes']}\n\n";
        
        $formattedData .= "PERFORMANCE DISTRIBUTION:\n";
        $formattedData .= "- Top Performers (>90% completion): " . count($teamStats['top_performers']) . " users\n";
        $formattedData .= "- Needs Attention (<60% completion): " . count($teamStats['needs_attention']) . " users\n\n";
        
        $formattedData .= "PRODUCTIVITY METRICS:\n";
        $formattedData .= "- Average Tasks per User: {$summary['team_productivity']['average_tasks_per_user']}\n";
        $formattedData .= "- Average Completed per User: {$summary['team_productivity']['average_completed_per_user']}\n";
        $formattedData .= "- Median Completion Rate: {$summary['team_productivity']['median_completion_rate']}%\n";
        $formattedData .= "- Completion Rate Std Deviation: {$summary['team_productivity']['std_deviation_completion']}%\n\n";
        
        $formattedData .= "KEY INSIGHTS:\n";
        foreach ($summary['key_insights'] as $insight) {
            $formattedData .= "- {$insight}\n";
        }
        
        $formattedData .= "\nTOP 5 PERFORMERS:\n";
        $sortedUsers = $analysisData['users'];
        usort($sortedUsers, function($a, $b) {
            return $b['performance_metrics']['overall_performance'] - 
                   $a['performance_metrics']['overall_performance'];
        });
        
        for ($i = 0; $i < min(5, count($sortedUsers)); $i++) {
            $user = $sortedUsers[$i];
            $formattedData .= ($i + 1) . ". {$user['username']} - ";
            $formattedData .= "Overall: {$user['performance_metrics']['overall_performance']}%, ";
            $formattedData .= "Completion: {$user['task_statistics']['completion_rate']}%, ";
            $formattedData .= "Tasks: {$user['task_statistics']['completed']}/{$user['task_statistics']['total']}\n";
        }
        
        $formattedData .= "\nUSERS NEEDING ATTENTION:\n";
        foreach ($teamStats['needs_attention'] as $user) {
            $formattedData .= "- {$user['username']} - ";
            $formattedData .= "Completion: {$user['completion_rate']}%, ";
            $formattedData .= "Pending Tasks: {$user['pending_tasks']}\n";
        }
        
        $prompt = "You are a senior business analytics AI. Analyze the following team task performance data ";
        $prompt .= "and provide a comprehensive, executive-level report:\n\n";
        $prompt .= $formattedData . "\n\n";
        $prompt .= "User Query: {$message}\n\n";
        $prompt .= "Generate a detailed analysis report with these sections:\n";
        $prompt .= "1. EXECUTIVE SUMMARY: High-level overview of team performance\n";
        $prompt .= "2. TEAM PERFORMANCE ANALYSIS: Detailed analysis of key metrics\n";
        $prompt .= "3. INDIVIDUAL PERFORMANCE HIGHLIGHTS: Top performers and areas of concern\n";
        $prompt .= "4. FUNNEL EFFECTIVENESS: Analysis of own vs team funnel performance\n";
        $prompt .= "5. PRODUCTIVITY TRENDS: Patterns in task completion and efficiency\n";
        $prompt .= "6. ATTENDANCE IMPACT: Correlation between attendance and performance\n";
        $prompt .= "7. ACTIONABLE RECOMMENDATIONS: Specific, measurable actions for improvement\n";
        $prompt .= "8. RISK ASSESSMENT: Areas requiring immediate attention\n";
        $prompt .= "9. PERFORMANCE FORECAST: Based on current trends\n\n";
        
        $prompt .= "Format: Use professional business report format with clear headings. ";
        $prompt .= "Include data-driven insights and specific recommendations.";
        
        return $prompt;
    }

    /**
     * Prepare data for frontend display
     * 
     * @param array $analysisData Analysis data
     * @param array|null $specificUser Specific user data if requested
     * @return array Frontend-ready data
     */
    public function prepare_frontend_data($analysisData, $specificUser = null)
    {
        $response = [
            'status' => 'empty',
            'message' => 'No data available for analysis',
            'tableData' => null,
            'chartData' => null,
            'summaryData' => [],
            'specificUserData' => null,
            'comparativeData' => null,
            'period' => $analysisData['period'] ?? 'N/A',
            'generated_at' => date('Y-m-d H:i:s')
        ];

        if (empty($analysisData['users']) || !is_array($analysisData['users'])) {
            return $response;
        }

        // Prepare main table data
        $response['tableData'] = $this->prepare_main_table_data($analysisData['users']);
        
        // Prepare chart data
        $response['chartData'] = $this->prepare_chart_data($analysisData);
        
        // Prepare summary data
        $response['summaryData'] = $this->prepare_summary_data($analysisData);
        
        // Prepare specific user data if requested
        if ($specificUser) {
            $response['specificUserData'] = $this->prepare_specific_user_frontend_data($specificUser, $analysisData);
            $response['comparativeData'] = $this->prepare_comparative_data($specificUser, $analysisData);
        }
        
        // Update response status
        $response['status'] = 'success';
        $response['message'] = 'Task analysis data prepared successfully';
        $response['total_users'] = count($analysisData['users']);
        $response['date_range'] = $analysisData['period'];

        return $response;
    }

    /**
     * Prepare main table data for frontend
     * 
     * @param array $users Users data
     * @return array Table data structure
     */
    private function prepare_main_table_data($users)
    {
        $headers = [
            'Rank', 'Username', 'Total Tasks', 'Completed', 'Pending',
            'Completion Rate', 'Efficiency Score', 'Overall Performance',
            'Own Funnel Comp.', 'Team Funnel Comp.', 'Status Changes', 'Attendance'
        ];

        $rows = [];
        foreach ($users as $user) {
            $rows[] = [
                $user['rank'],
                $user['username'],
                $user['task_statistics']['total'],
                $user['task_statistics']['completed'],
                $user['task_statistics']['pending'],
                $user['task_statistics']['completion_rate'] . '%',
                round($user['task_efficiency'], 1) . '/100',
                $user['performance_metrics']['overall_performance'] . '%',
                $user['funnel_analysis']['own_funnel']['completed'] . ' (' . 
                    $user['funnel_analysis']['own_funnel']['completion_rate'] . '%)',
                $user['funnel_analysis']['team_funnel']['completed'] . ' (' . 
                    $user['funnel_analysis']['team_funnel']['completion_rate'] . '%)',
                $user['status_change_analysis']['total_status_changes'],
                $user['attendance']['is_present'] ? 'Present' : 'Absent'
            ];
        }

        return [
            'title' => 'Team Task Performance Analysis',
            'headers' => $headers,
            'rows' => $rows,
            'sortable' => true,
            'paginate' => true,
            'items_per_page' => 10
        ];
    }

    /**
     * Prepare chart data for visualization
     * 
     * @param array $analysisData Analysis data
     * @return array Chart data structure
     */
    private function prepare_chart_data($analysisData)
    {
        $users = $analysisData['users'];
        
        // Sort users by performance for better visualization
        usort($users, function($a, $b) {
            return $b['performance_metrics']['overall_performance'] - 
                   $a['performance_metrics']['overall_performance'];
        });

        $labels = [];
        $completionData = [];
        $efficiencyData = [];
        $performanceData = [];

        foreach ($users as $user) {
            $labels[] = substr($user['username'], 0, 15) . '...';
            $completionData[] = $user['task_statistics']['completion_rate'];
            $efficiencyData[] = $user['task_efficiency'];
            $performanceData[] = $user['performance_metrics']['overall_performance'];
        }

        return [
            'performance_comparison' => [
                'type' => 'bar',
                'title' => 'Team Performance Comparison',
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Completion Rate (%)',
                        'data' => $completionData,
                        'backgroundColor' => '#10a37f',
                        'borderColor' => '#0d8a6a',
                        'borderWidth' => 1
                    ],
                    [
                        'label' => 'Efficiency Score',
                        'data' => $efficiencyData,
                        'backgroundColor' => '#5436da',
                        'borderColor' => '#4529b5',
                        'borderWidth' => 1
                    ],
                    [
                        'label' => 'Overall Performance (%)',
                        'data' => $performanceData,
                        'backgroundColor' => '#ffa726',
                        'borderColor' => '#f57c00',
                        'borderWidth' => 1
                    ]
                ]
            ],
            'funnel_distribution' => $this->prepare_funnel_chart_data($users),
            'performance_distribution' => $this->prepare_performance_distribution_chart($users),
            'attendance_impact' => $this->prepare_attendance_impact_chart($users)
        ];
    }

    /**
     * Prepare funnel distribution chart data
     * 
     * @param array $users Users data
     * @return array Funnel chart data
     */
    private function prepare_funnel_chart_data($users)
    {
        $labels = [];
        $ownFunnelData = [];
        $teamFunnelData = [];
        $ownCompletionData = [];
        $teamCompletionData = [];

        foreach ($users as $user) {
            $labels[] = substr($user['username'], 0, 10);
            $ownFunnelData[] = $user['funnel_analysis']['own_funnel']['completed'];
            $teamFunnelData[] = $user['funnel_analysis']['team_funnel']['completed'];
            $ownCompletionData[] = $user['funnel_analysis']['own_funnel']['completion_rate'];
            $teamCompletionData[] = $user['funnel_analysis']['team_funnel']['completion_rate'];
        }

        return [
            'type' => 'bar',
            'title' => 'Funnel Task Distribution',
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Own Funnel Completed',
                    'data' => $ownFunnelData,
                    'backgroundColor' => '#26c6da',
                    'borderColor' => '#00acc1',
                    'borderWidth' => 1
                ],
                [
                    'label' => 'Team Funnel Completed',
                    'data' => $teamFunnelData,
                    'backgroundColor' => '#ff6b6b',
                    'borderColor' => '#f44336',
                    'borderWidth' => 1
                ]
            ]
        ];
    }

    /**
     * Prepare performance distribution chart
     * 
     * @param array $users Users data
     * @return array Performance distribution data
     */
    private function prepare_performance_distribution_chart($users)
    {
        $performanceCategories = [
            'Excellent (>90%)' => 0,
            'Good (75-90%)' => 0,
            'Average (60-75%)' => 0,
            'Needs Improvement (<60%)' => 0
        ];

        foreach ($users as $user) {
            $score = $user['performance_metrics']['overall_performance'];
            
            if ($score > 90) {
                $performanceCategories['Excellent (>90%)']++;
            } elseif ($score >= 75) {
                $performanceCategories['Good (75-90%)']++;
            } elseif ($score >= 60) {
                $performanceCategories['Average (60-75%)']++;
            } else {
                $performanceCategories['Needs Improvement (<60%)']++;
            }
        }

        return [
            'type' => 'doughnut',
            'title' => 'Performance Distribution',
            'labels' => array_keys($performanceCategories),
            'datasets' => [
                [
                    'data' => array_values($performanceCategories),
                    'backgroundColor' => ['#10a37f', '#5436da', '#ffa726', '#ff6b6b'],
                    'borderColor' => '#2a2a2a',
                    'borderWidth' => 1
                ]
            ]
        ];
    }

    /**
     * Prepare attendance impact chart
     * 
     * @param array $users Users data
     * @return array Attendance impact data
     */
    private function prepare_attendance_impact_chart($users)
    {
        $presentUsers = [];
        $absentUsers = [];

        foreach ($users as $user) {
            if ($user['attendance']['is_present']) {
                $presentUsers[] = $user;
            } else {
                $absentUsers[] = $user;
            }
        }

        $presentAvg = $this->calculate_average_performance($presentUsers);
        $absentAvg = $this->calculate_average_performance($absentUsers);

        return [
            'type' => 'bar',
            'title' => 'Attendance Impact on Performance',
            'labels' => ['Present Users', 'Absent Users'],
            'datasets' => [
                [
                    'label' => 'Average Performance (%)',
                    'data' => [$presentAvg, $absentAvg],
                    'backgroundColor' => ['#10a37f', '#ff6b6b'],
                    'borderColor' => ['#0d8a6a', '#f44336'],
                    'borderWidth' => 1
                ]
            ]
        ];
    }

    /**
     * Calculate average performance for a user group
     * 
     * @param array $users Users data
     * @return float Average performance
     */
    private function calculate_average_performance($users)
    {
        if (empty($users)) {
            return 0;
        }

        $total = 0;
        foreach ($users as $user) {
            $total += $user['performance_metrics']['overall_performance'];
        }

        return round($total / count($users), 2);
    }

    /**
     * Prepare summary data for frontend
     * 
     * @param array $analysisData Analysis data
     * @return array Summary data
     */
    private function prepare_summary_data($analysisData)
    {
        $teamStats = $analysisData['team_statistics'];
        $summary = $analysisData['summary'];

        return [
            'period' => $analysisData['period'],
            'total_users' => $teamStats['total_users'],
            'present_users' => $summary['present_users'],
            'absent_users' => $summary['absent_users'],
            'attendance_rate' => $summary['attendance_rate'] . '%',
            'total_tasks' => $teamStats['total_tasks'],
            'completed_tasks' => $teamStats['total_completed'],
            'pending_tasks' => $teamStats['total_pending'],
            'completion_rate' => $teamStats['average_completion_rate'] . '%',
            'efficiency_score' => $teamStats['total_efficiency_score'] . '/100',
            'own_funnel_tasks' => $teamStats['total_own_funnel_tasks'],
            'team_funnel_tasks' => $teamStats['total_team_funnel_tasks'],
            'status_changes' => $teamStats['total_status_changes'],
            'top_performers_count' => count($teamStats['top_performers']),
            'needs_attention_count' => count($teamStats['needs_attention']),
            'key_insights' => $summary['key_insights'],
            'productivity_metrics' => $summary['team_productivity']
        ];
    }

    /**
     * Prepare specific user data for frontend
     * 
     * @param array $user User data
     * @param array $teamData Team data
     * @return array Formatted user data
     */
    private function prepare_specific_user_frontend_data($user, $teamData)
    {
        return [
            'basic_info' => [
                'username' => $user['username'],
                'user_id' => $user['user_id'],
                'rank' => $user['rank'],
                'total_users' => $teamData['team_statistics']['total_users'],
                'performance_percentile' => $this->calculate_performance_percentile($user, $teamData['users'])
            ],
            'task_statistics' => $user['task_statistics'],
            'funnel_analysis' => $user['funnel_analysis'],
            'performance_metrics' => $user['performance_metrics'],
            'status_change_analysis' => $user['status_change_analysis'],
            'attendance' => $user['attendance'],
            'task_efficiency' => $user['task_efficiency']
        ];
    }

    /**
     * Calculate performance percentile
     * 
     * @param array $user User data
     * @param array $allUsers All users data
     * @return float Percentile rank
     */
    private function calculate_performance_percentile($user, $allUsers)
    {
        if (empty($allUsers) || count($allUsers) < 2) {
            return 100;
        }

        $betterCount = 0;
        $userScore = $user['performance_metrics']['overall_performance'];

        foreach ($allUsers as $otherUser) {
            if ($otherUser['user_id'] === $user['user_id']) continue;
            
            if ($otherUser['performance_metrics']['overall_performance'] < $userScore) {
                $betterCount++;
            }
        }

        return round(($betterCount / (count($allUsers) - 1)) * 100, 2);
    }

    /**
     * Prepare comparative data
     * 
     * @param array $user User data
     * @param array $teamData Team data
     * @return array Comparative data
     */
    private function prepare_comparative_data($user, $teamData)
    {
        $teamStats = $teamData['team_statistics'];
        
        return [
            'completion_rate' => [
                'user' => $user['task_statistics']['completion_rate'],
                'team_average' => $teamStats['average_completion_rate'],
                'difference' => round($user['task_statistics']['completion_rate'] - $teamStats['average_completion_rate'], 2),
                'comparison' => $this->get_comparison_text(
                    $user['task_statistics']['completion_rate'], 
                    $teamStats['average_completion_rate']
                )
            ],
            'efficiency' => [
                'user' => $user['task_efficiency'],
                'team_average' => $teamStats['total_efficiency_score'],
                'difference' => round($user['task_efficiency'] - $teamStats['total_efficiency_score'], 2)
            ],
            'overall_performance' => [
                'user' => $user['performance_metrics']['overall_performance'],
                'team_average' => $this->calculate_team_average_performance($teamData['users']),
                'difference' => round($user['performance_metrics']['overall_performance'] - 
                    $this->calculate_team_average_performance($teamData['users']), 2)
            ],
            'rank_context' => [
                'position' => $user['rank'],
                'total_users' => count($teamData['users']),
                'top_performers_count' => count($teamStats['top_performers']),
                'in_top_percent' => round(($user['rank'] / count($teamData['users'])) * 100, 2)
            ]
        ];
    }

    /**
     * Calculate team average performance
     * 
     * @param array $users Users data
     * @return float Average performance
     */
    private function calculate_team_average_performance($users)
    {
        if (empty($users)) {
            return 0;
        }

        $total = 0;
        foreach ($users as $user) {
            $total += $user['performance_metrics']['overall_performance'];
        }

        return round($total / count($users), 2);
    }

    /**
     * Get comparison text for metrics
     * 
     * @param float $userValue User's value
     * @param float $teamValue Team average value
     * @return string Comparison text
     */
    private function get_comparison_text($userValue, $teamValue)
    {
        $difference = $userValue - $teamValue;
        
        if ($difference > 10) {
            return "Significantly above team average";
        } elseif ($difference > 5) {
            return "Above team average";
        } elseif ($difference > 0) {
            return "Slightly above team average";
        } elseif ($difference > -5) {
            return "Slightly below team average";
        } elseif ($difference > -10) {
            return "Below team average";
        } else {
            return "Significantly below team average";
        }
    }

    /**
     * Construct image URL from path
     * 
     * @param string|null $imagePath Image path
     * @return string|null Full URL or null
     */
    private function construct_image_url($imagePath)
    {
        if (empty($imagePath) || in_array($imagePath, ['null', 'NULL', null], true)) {
            return null;
        }

        // If already a full URL, return as is
        if (filter_var($imagePath, FILTER_VALIDATE_URL)) {
            return $imagePath;
        }

        // Remove leading slash if present
        $imagePath = ltrim($imagePath, '/');

        // Construct full URL
        $baseUrl = rtrim(base_url(), '/');
        
        return $baseUrl . '/uploads/attendance/' . $imagePath;
    }
}