<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class FutureTaskDetailsSummary_model extends CI_Model {

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
     * Main method to process future task analysis
     */
    public function process_FutureTaskDetailsSummary_model($message, $analysisType, $sdate, $edate) 
    {
        // Get future tasks data
        $futuretasks = $this->Report_model->GetOurFutureTaskListsByRoles($this->uid, $this->uid, 'all');

        // Check if data exists
        if (empty($futuretasks)) {
            return [
                'content' => "No future task data found for the period {$sdate} to {$edate}.",
                'data' => [
                    'status' => 'empty',
                    'message' => 'No future task data available for analysis',
                    'period' => "{$sdate} to {$edate}"
                ]
            ];
        }

        // Extract count from message (top 10, top 20, top 30, top 50)
        $topCount = $this->extract_top_count_from_message($message);
        
        // Generate comprehensive analysis
        $analysis = $this->generate_detailed_future_task_analysis(
            $futuretasks, 
            $message, 
            $topCount,
            $sdate, 
            $edate
        );

        // Prepare frontend data
        $frontendData = $this->prepare_detailed_frontend_data(
            $futuretasks, 
            $topCount,
            $sdate, 
            $edate
        );

        return [
            'content' => $analysis,
            'data' => $futuretasks
        ];
    }

    /**
     * Extract top count from message (top 10, top 20, etc.)
     */
    private function extract_top_count_from_message($message)
    {
        $lowerMessage = strtolower(trim($message));
        
        if (preg_match('/top\s+(\d+)/', $lowerMessage, $matches)) {
            $count = intval($matches[1]);
            return min($count, 100); // Cap at 100
        }
        
        // Default based on common patterns
        if (strpos($lowerMessage, 'top 50') !== false) return 50;
        if (strpos($lowerMessage, 'top 30') !== false) return 30;
        if (strpos($lowerMessage, 'top 20') !== false) return 20;
        if (strpos($lowerMessage, 'top 10') !== false) return 10;
        
        return 10; // Default to top 10
    }

    /**
     * Generate detailed future task analysis
     */
    private function generate_detailed_future_task_analysis($futureTasks, $message, $topCount, $startDate, $endDate)
    {
        // Process and analyze the data
        $analysisResults = $this->analyze_future_task_data_deep($futureTasks, $topCount);
        
        // Generate response based on message intent
        $response = $this->generate_response_based_on_message(
            $message, 
            $analysisResults, 
            $topCount, 
            $startDate, 
            $endDate
        );
        
        return $response;
    }

    /**
     * Deep analysis of future task data
     */
    private function analyze_future_task_data_deep($futureTasks, $topCount)
    {
        if (empty($futureTasks)) {
            return [
                'status' => 'empty',
                'message' => 'No future task data to analyze'
            ];
        }

        $analysis = [
            'total_tasks' => count($futureTasks),
            'total_users' => 0,
            'total_companies' => 0,
            'task_distribution' => [],
            'user_distribution' => [],
            'company_distribution' => [],
            'time_analysis' => [],
            'status_analysis' => [],
            'task_type_analysis' => [],
            'priority_analysis' => [],
            'performance_metrics' => [],
            'upcoming_tasks' => [],
            'overdue_tasks' => [],
            'recommendations' => [],
            'top_users' => [],
            'top_companies' => [],
            'table_data_analysis' => []
        ];

        // Analyze data
        $analysis['task_distribution'] = $this->analyze_task_distribution($futureTasks);
        $analysis['user_distribution'] = $this->analyze_user_distribution($futureTasks);
        $analysis['company_distribution'] = $this->analyze_company_distribution($futureTasks);
        $analysis['time_analysis'] = $this->analyze_time_distribution($futureTasks);
        $analysis['status_analysis'] = $this->analyze_status_distribution($futureTasks);
        $analysis['task_type_analysis'] = $this->analyze_task_type_distribution($futureTasks);
        $analysis['priority_analysis'] = $this->analyze_priority_distribution($futureTasks);
        $analysis['performance_metrics'] = $this->calculate_performance_metrics($futureTasks);
        $analysis['upcoming_tasks'] = $this->identify_upcoming_tasks($futureTasks);
        $analysis['overdue_tasks'] = $this->identify_overdue_tasks($futureTasks);
        $analysis['recommendations'] = $this->generate_recommendations($futureTasks);
        $analysis['top_users'] = $this->select_top_users($futureTasks, $topCount);
        $analysis['top_companies'] = $this->select_top_companies($futureTasks, $topCount);
        $analysis['table_data_analysis'] = $this->analyze_table_data($futureTasks);

        // Update counts
        $analysis['total_users'] = count($analysis['user_distribution']);
        $analysis['total_companies'] = count($analysis['company_distribution']);

        return $analysis;
    }

    /**
     * Analyze task distribution
     */
    private function analyze_task_distribution($tasks)
    {
        $distribution = [
            'by_day' => [],
            'by_week' => [],
            'by_month' => [],
            'by_hour' => [],
            'by_weekday' => []
        ];

        $dailyCounts = [];
        $hourlyCounts = array_fill(0, 24, 0);
        $weekdayCounts = array_fill(0, 7, 0);
        
        foreach ($tasks as $task) {
            $appointmentDate = $task->appointmentdatetime ?? '';
            
            if (!empty($appointmentDate)) {
                $date = date('Y-m-d', strtotime($appointmentDate));
                $hour = date('H', strtotime($appointmentDate));
                $weekday = date('w', strtotime($appointmentDate));
                $month = date('Y-m', strtotime($appointmentDate));
                $week = date('Y-W', strtotime($appointmentDate));
                
                // Daily counts
                if (!isset($dailyCounts[$date])) {
                    $dailyCounts[$date] = 0;
                }
                $dailyCounts[$date]++;
                
                // Hourly distribution
                if (is_numeric($hour)) {
                    $hourlyCounts[intval($hour)]++;
                }
                
                // Weekday distribution
                if (is_numeric($weekday)) {
                    $weekdayCounts[intval($weekday)]++;
                }
                
                // Monthly counts
                if (!isset($distribution['by_month'][$month])) {
                    $distribution['by_month'][$month] = 0;
                }
                $distribution['by_month'][$month]++;
                
                // Weekly counts
                if (!isset($distribution['by_week'][$week])) {
                    $distribution['by_week'][$week] = 0;
                }
                $distribution['by_week'][$week]++;
            }
        }
        
        // Sort daily trends
        ksort($dailyCounts);
        $distribution['by_day'] = $dailyCounts;
        
        // Hourly distribution with labels
        foreach ($hourlyCounts as $hour => $count) {
            if ($count > 0) {
                $hourLabel = sprintf('%02d:00-%02d:59', $hour, $hour);
                $distribution['by_hour'][$hourLabel] = $count;
            }
        }
        
        // Weekday distribution with labels
        $weekdayLabels = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        foreach ($weekdayCounts as $weekday => $count) {
            if ($count > 0) {
                $distribution['by_weekday'][$weekdayLabels[$weekday]] = $count;
            }
        }
        
        return $distribution;
    }

    /**
     * Analyze user distribution
     */
    private function analyze_user_distribution($tasks)
    {
        $userDistribution = [];
        
        foreach ($tasks as $task) {
            $userName = $task->task_user_name ?? 'Unknown';
            $mainBDName = $task->main_bd_name ?? 'Unknown';
            
            // Use task_user_name as primary, fall back to main_bd_name
            $primaryUser = !empty($userName) ? $userName : $mainBDName;
            
            if (!isset($userDistribution[$primaryUser])) {
                $userDistribution[$primaryUser] = [
                    'username' => $primaryUser,
                    'total_tasks' => 0,
                    'emails' => 0,
                    'calls' => 0,
                    'other_tasks' => 0,
                    'upcoming_tasks' => 0,
                    'completed_tasks' => 0,
                    'pending_tasks' => 0,
                    'companies_handled' => [],
                    'task_types' => []
                ];
            }
            
            $userDistribution[$primaryUser]['total_tasks']++;
            
            // Count by task type
            $taskName = strtolower($task->task_name ?? '');
            if (strpos($taskName, 'email') !== false) {
                $userDistribution[$primaryUser]['emails']++;
            } elseif (strpos($taskName, 'call') !== false) {
                $userDistribution[$primaryUser]['calls']++;
            } else {
                $userDistribution[$primaryUser]['other_tasks']++;
            }
            
            // Track companies
            $companyName = $task->compname ?? 'Unknown';
            if (!in_array($companyName, $userDistribution[$primaryUser]['companies_handled'])) {
                $userDistribution[$primaryUser]['companies_handled'][] = $companyName;
            }
            
            // Track task types
            if (!isset($userDistribution[$primaryUser]['task_types'][$taskName])) {
                $userDistribution[$primaryUser]['task_types'][$taskName] = 0;
            }
            $userDistribution[$primaryUser]['task_types'][$taskName]++;
            
            // Count by status
            $status = strtolower($task->current_status ?? '');
            if (strpos($status, 'positive') !== false || strpos($status, 'complete') !== false) {
                $userDistribution[$primaryUser]['completed_tasks']++;
            } elseif (strpos($status, 'open') !== false || strpos($status, 'pending') !== false) {
                $userDistribution[$primaryUser]['pending_tasks']++;
            }
        }
        
        // Convert to indexed array and calculate percentages
        $result = [];
        foreach ($userDistribution as $userData) {
            if ($userData['total_tasks'] > 0) {
                $userData['completion_rate'] = round(($userData['completed_tasks'] / $userData['total_tasks']) * 100, 2);
                $userData['email_percentage'] = round(($userData['emails'] / $userData['total_tasks']) * 100, 2);
                $userData['call_percentage'] = round(($userData['calls'] / $userData['total_tasks']) * 100, 2);
                $userData['unique_companies'] = count($userData['companies_handled']);
            } else {
                $userData['completion_rate'] = 0;
                $userData['email_percentage'] = 0;
                $userData['call_percentage'] = 0;
                $userData['unique_companies'] = 0;
            }
            $result[] = $userData;
        }
        
        // Sort by total tasks descending
        usort($result, function($a, $b) {
            return $b['total_tasks'] - $a['total_tasks'];
        });
        
        return $result;
    }

    /**
     * Analyze company distribution
     */
    private function analyze_company_distribution($tasks)
    {
        $companyDistribution = [];
        
        foreach ($tasks as $task) {
            $companyName = $task->compname ?? 'Unknown Company';
            $companyId = $task->cid ?? 0;
            
            if (!isset($companyDistribution[$companyId])) {
                $companyDistribution[$companyId] = [
                    'company_id' => $companyId,
                    'company_name' => $companyName,
                    'total_tasks' => 0,
                    'assigned_users' => [],
                    'task_types' => [],
                    'first_task_date' => null,
                    'last_task_date' => null,
                    'status_distribution' => [],
                    'review_types' => []
                ];
            }
            
            $companyDistribution[$companyId]['total_tasks']++;
            
            // Track assigned users
            $userName = $task->task_user_name ?? $task->main_bd_name ?? 'Unknown';
            if (!in_array($userName, $companyDistribution[$companyId]['assigned_users'])) {
                $companyDistribution[$companyId]['assigned_users'][] = $userName;
            }
            
            // Track task types
            $taskName = $task->task_name ?? 'Unknown';
            if (!isset($companyDistribution[$companyId]['task_types'][$taskName])) {
                $companyDistribution[$companyId]['task_types'][$taskName] = 0;
            }
            $companyDistribution[$companyId]['task_types'][$taskName]++;
            
            // Track dates
            $taskDate = $task->appointmentdatetime ?? '';
            if (!empty($taskDate)) {
                if (empty($companyDistribution[$companyId]['first_task_date']) || 
                    strtotime($taskDate) < strtotime($companyDistribution[$companyId]['first_task_date'])) {
                    $companyDistribution[$companyId]['first_task_date'] = $taskDate;
                }
                
                if (empty($companyDistribution[$companyId]['last_task_date']) || 
                    strtotime($taskDate) > strtotime($companyDistribution[$companyId]['last_task_date'])) {
                    $companyDistribution[$companyId]['last_task_date'] = $taskDate;
                }
            }
            
            // Track status distribution
            $status = $task->current_status ?? 'Unknown';
            if (!isset($companyDistribution[$companyId]['status_distribution'][$status])) {
                $companyDistribution[$companyId]['status_distribution'][$status] = 0;
            }
            $companyDistribution[$companyId]['status_distribution'][$status]++;
            
            // Track review types
            $reviewType = $task->reviewtype ?? '';
            if (!empty($reviewType) && !in_array($reviewType, $companyDistribution[$companyId]['review_types'])) {
                $companyDistribution[$companyId]['review_types'][] = $reviewType;
            }
        }
        
        // Convert to indexed array
        $result = [];
        foreach ($companyDistribution as $companyData) {
            $companyData['unique_users'] = count($companyData['assigned_users']);
            $companyData['unique_task_types'] = count($companyData['task_types']);
            $companyData['unique_review_types'] = count($companyData['review_types']);
            
            // Calculate engagement score
            $companyData['engagement_score'] = $this->calculate_company_engagement_score($companyData);
            $companyData['engagement_level'] = $this->get_engagement_level($companyData['engagement_score']);
            
            $result[] = $companyData;
        }
        
        // Sort by total tasks descending
        usort($result, function($a, $b) {
            return $b['total_tasks'] - $a['total_tasks'];
        });
        
        return $result;
    }

    /**
     * Calculate company engagement score
     */
    private function calculate_company_engagement_score($companyData)
    {
        $score = 0;
        
        // Task frequency (max 30 points)
        $taskCount = $companyData['total_tasks'];
        if ($taskCount >= 10) $score += 30;
        elseif ($taskCount >= 5) $score += 20;
        elseif ($taskCount >= 3) $score += 15;
        elseif ($taskCount >= 2) $score += 10;
        elseif ($taskCount > 0) $score += 5;
        
        // User diversity (max 20 points)
        $userCount = $companyData['unique_users'];
        if ($userCount >= 3) $score += 20;
        elseif ($userCount >= 2) $score += 15;
        elseif ($userCount >= 1) $score += 10;
        
        // Task type diversity (max 15 points)
        $taskTypeCount = $companyData['unique_task_types'];
        if ($taskTypeCount >= 3) $score += 15;
        elseif ($taskTypeCount >= 2) $score += 10;
        elseif ($taskTypeCount >= 1) $score += 5;
        
        // Review type diversity (max 10 points)
        $reviewTypeCount = $companyData['unique_review_types'];
        if ($reviewTypeCount >= 2) $score += 10;
        elseif ($reviewTypeCount >= 1) $score += 5;
        
        // Recent activity (max 15 points)
        if (!empty($companyData['last_task_date'])) {
            $daysSinceLast = $this->calculate_days_since_last_task($companyData['last_task_date']);
            if ($daysSinceLast <= 7) $score += 15;
            elseif ($daysSinceLast <= 30) $score += 10;
            elseif ($daysSinceLast <= 90) $score += 5;
        }
        
        // Status distribution (max 10 points)
        $positiveStatuses = ['Positive', 'Positive-NAP', 'Very Positive', 'Closure', 'On Boarded'];
        $positiveCount = 0;
        foreach ($companyData['status_distribution'] as $status => $count) {
            if (in_array($status, $positiveStatuses)) {
                $positiveCount += $count;
            }
        }
        
        if ($taskCount > 0) {
            $positivePercentage = ($positiveCount / $taskCount) * 100;
            if ($positivePercentage >= 50) $score += 10;
            elseif ($positivePercentage >= 30) $score += 7;
            elseif ($positivePercentage >= 10) $score += 4;
        }
        
        return $score;
    }

    /**
     * Calculate days since last task
     */
    private function calculate_days_since_last_task($lastTaskDate)
    {
        if (empty($lastTaskDate)) {
            return 999;
        }
        
        $lastDate = strtotime($lastTaskDate);
        $currentDate = time();
        
        $secondsDiff = $currentDate - $lastDate;
        return floor($secondsDiff / (60 * 60 * 24));
    }

    /**
     * Get engagement level
     */
    private function get_engagement_level($score)
    {
        if ($score >= 80) return 'Very High';
        if ($score >= 60) return 'High';
        if ($score >= 40) return 'Medium';
        if ($score >= 20) return 'Low';
        return 'Very Low';
    }

    /**
     * Analyze time distribution
     */
    private function analyze_time_distribution($tasks)
    {
        $timeAnalysis = [
            'immediate_tasks' => 0,      // Today
            'short_term_tasks' => 0,     // Next 7 days
            'medium_term_tasks' => 0,    // Next 30 days
            'long_term_tasks' => 0,      // Beyond 30 days
            'task_timeline' => [],
            'peak_periods' => []
        ];
        
        $today = date('Y-m-d');
        $nextWeek = date('Y-m-d', strtotime('+7 days'));
        $nextMonth = date('Y-m-d', strtotime('+30 days'));
        
        $dailyTaskCounts = [];
        
        foreach ($tasks as $task) {
            $taskDate = $task->appointmentdatetime ?? '';
            
            if (!empty($taskDate)) {
                $dateOnly = date('Y-m-d', strtotime($taskDate));
                
                // Categorize by timeframe
                if ($dateOnly == $today) {
                    $timeAnalysis['immediate_tasks']++;
                } elseif ($dateOnly <= $nextWeek) {
                    $timeAnalysis['short_term_tasks']++;
                } elseif ($dateOnly <= $nextMonth) {
                    $timeAnalysis['medium_term_tasks']++;
                } else {
                    $timeAnalysis['long_term_tasks']++;
                }
                
                // Add to timeline
                if (!isset($dailyTaskCounts[$dateOnly])) {
                    $dailyTaskCounts[$dateOnly] = 0;
                }
                $dailyTaskCounts[$dateOnly]++;
                
                // Add to detailed timeline
                $timeAnalysis['task_timeline'][] = [
                    'date' => $dateOnly,
                    'time' => date('H:i', strtotime($taskDate)),
                    'company' => $task->compname ?? '',
                    'task' => $task->task_name ?? '',
                    'user' => $task->task_user_name ?? '',
                    'status' => $task->current_status ?? ''
                ];
            }
        }
        
        // Sort timeline by date
        usort($timeAnalysis['task_timeline'], function($a, $b) {
            return strtotime($a['date'] . ' ' . $a['time']) - strtotime($b['date'] . ' ' . $b['time']);
        });
        
        // Identify peak periods (top 5 busiest days)
        arsort($dailyTaskCounts);
        $peakDays = array_slice($dailyTaskCounts, 0, 5, true);
        
        foreach ($peakDays as $date => $count) {
            $timeAnalysis['peak_periods'][] = [
                'date' => $date,
                'task_count' => $count,
                'day_of_week' => date('l', strtotime($date))
            ];
        }
        
        return $timeAnalysis;
    }

    /**
     * Analyze status distribution
     */
    private function analyze_status_distribution($tasks)
    {
        $statusAnalysis = [
            'status_counts' => [],
            'status_categories' => [
                'positive' => 0,
                'neutral' => 0,
                'negative' => 0,
                'pending' => 0,
                'completed' => 0
            ],
            'status_trends' => [],
            'conversion_rates' => []
        ];
        
        $statusDefinitions = [
            'positive' => ['Positive', 'Positive-NAP', 'Very Positive', 'Very Positive-NAP', 'Closure', 'On Boarded'],
            'neutral' => ['Open', 'OPEN RPEM', 'Reachout', 'Tentative', 'Will do Later'],
            'negative' => ['Not Interested', 'Rejected'],
            'pending' => ['Pending', 'Open', 'OPEN RPEM'],
            'completed' => ['Positive', 'Positive-NAP', 'Closure', 'On Boarded']
        ];
        
        $monthlyStatus = [];
        
        foreach ($tasks as $task) {
            $status = $task->current_status ?? 'Unknown';
            $taskDate = $task->appointmentdatetime ?? '';
            
            // Count statuses
            if (!isset($statusAnalysis['status_counts'][$status])) {
                $statusAnalysis['status_counts'][$status] = 0;
            }
            $statusAnalysis['status_counts'][$status]++;
            
            // Categorize statuses
            foreach ($statusDefinitions as $category => $statusList) {
                if (in_array($status, $statusList)) {
                    $statusAnalysis['status_categories'][$category]++;
                }
            }
            
            // Track monthly trends
            if (!empty($taskDate)) {
                $month = date('Y-m', strtotime($taskDate));
                if (!isset($monthlyStatus[$month])) {
                    $monthlyStatus[$month] = [];
                }
                if (!isset($monthlyStatus[$month][$status])) {
                    $monthlyStatus[$month][$status] = 0;
                }
                $monthlyStatus[$month][$status]++;
            }
        }
        
        // Calculate status trends
        ksort($monthlyStatus);
        foreach ($monthlyStatus as $month => $statusData) {
            $statusAnalysis['status_trends'][$month] = $statusData;
        }
        
        // Calculate conversion rates
        $totalTasks = count($tasks);
        if ($totalTasks > 0) {
            $statusAnalysis['conversion_rates'] = [
                'positive_rate' => round(($statusAnalysis['status_categories']['positive'] / $totalTasks) * 100, 2),
                'completion_rate' => round(($statusAnalysis['status_categories']['completed'] / $totalTasks) * 100, 2),
                'pending_rate' => round(($statusAnalysis['status_categories']['pending'] / $totalTasks) * 100, 2)
            ];
        }
        
        return $statusAnalysis;
    }

    /**
     * Analyze task type distribution
     */
    private function analyze_task_type_distribution($tasks)
    {
        $taskTypeAnalysis = [
            'type_counts' => [],
            'type_by_user' => [],
            'type_by_company' => [],
            'type_efficiency' => []
        ];
        
        foreach ($tasks as $task) {
            $taskType = $task->task_name ?? 'Unknown';
            $userName = $task->task_user_name ?? 'Unknown';
            $companyName = $task->compname ?? 'Unknown';
            $status = $task->current_status ?? 'Unknown';
            
            // Count task types
            if (!isset($taskTypeAnalysis['type_counts'][$taskType])) {
                $taskTypeAnalysis['type_counts'][$taskType] = 0;
            }
            $taskTypeAnalysis['type_counts'][$taskType]++;
            
            // Count by user
            if (!isset($taskTypeAnalysis['type_by_user'][$userName])) {
                $taskTypeAnalysis['type_by_user'][$userName] = [];
            }
            if (!isset($taskTypeAnalysis['type_by_user'][$userName][$taskType])) {
                $taskTypeAnalysis['type_by_user'][$userName][$taskType] = 0;
            }
            $taskTypeAnalysis['type_by_user'][$userName][$taskType]++;
            
            // Count by company
            if (!isset($taskTypeAnalysis['type_by_company'][$companyName])) {
                $taskTypeAnalysis['type_by_company'][$companyName] = [];
            }
            if (!isset($taskTypeAnalysis['type_by_company'][$companyName][$taskType])) {
                $taskTypeAnalysis['type_by_company'][$companyName][$taskType] = 0;
            }
            $taskTypeAnalysis['type_by_company'][$companyName][$taskType]++;
            
            // Track efficiency by task type
            if (!isset($taskTypeAnalysis['type_efficiency'][$taskType])) {
                $taskTypeAnalysis['type_efficiency'][$taskType] = [
                    'total' => 0,
                    'positive' => 0,
                    'completed' => 0
                ];
            }
            $taskTypeAnalysis['type_efficiency'][$taskType]['total']++;
            
            $positiveStatuses = ['Positive', 'Positive-NAP', 'Very Positive', 'Closure', 'On Boarded'];
            if (in_array($status, $positiveStatuses)) {
                $taskTypeAnalysis['type_efficiency'][$taskType]['positive']++;
                $taskTypeAnalysis['type_efficiency'][$taskType]['completed']++;
            }
        }
        
        // Calculate efficiency rates
        foreach ($taskTypeAnalysis['type_efficiency'] as $taskType => &$data) {
            if ($data['total'] > 0) {
                $data['positive_rate'] = round(($data['positive'] / $data['total']) * 100, 2);
                $data['completion_rate'] = round(($data['completed'] / $data['total']) * 100, 2);
            } else {
                $data['positive_rate'] = 0;
                $data['completion_rate'] = 0;
            }
        }
        
        return $taskTypeAnalysis;
    }

    /**
     * Analyze priority distribution
     */
    private function analyze_priority_distribution($tasks)
    {
        $priorityAnalysis = [
            'priority_tasks' => [],
            'high_value_tasks' => [],
            'time_sensitive_tasks' => [],
            'follow_up_tasks' => [],
            'risk_assessment' => []
        ];
        
        $today = date('Y-m-d');
        $tomorrow = date('Y-m-d', strtotime('+1 day'));
        $nextWeek = date('Y-m-d', strtotime('+7 days'));
        
        foreach ($tasks as $task) {
            $taskDate = $task->appointmentdatetime ?? '';
            $companyName = $task->compname ?? '';
            $userName = $task->task_user_name ?? '';
            $status = $task->current_status ?? '';
            $reviewType = $task->reviewtype ?? '';
            $markedDays = $task->marked_meeting_days ?? '';
            
            if (!empty($taskDate)) {
                $dateOnly = date('Y-m-d', strtotime($taskDate));
                
                // Priority tasks (today or overdue)
                if ($dateOnly <= $today) {
                    $priorityAnalysis['priority_tasks'][] = [
                        'task_id' => $task->task_id ?? 0,
                        'company' => $companyName,
                        'user' => $userName,
                        'task' => $task->task_name ?? '',
                        'scheduled_date' => $taskDate,
                        'status' => $status,
                        'priority_reason' => 'Due today or overdue'
                    ];
                }
                
                // High value tasks (based on company and status)
                if (strpos($status, 'Positive') !== false || strpos($status, 'Very Positive') !== false) {
                    $priorityAnalysis['high_value_tasks'][] = [
                        'task_id' => $task->task_id ?? 0,
                        'company' => $companyName,
                        'user' => $userName,
                        'task' => $task->task_name ?? '',
                        'scheduled_date' => $taskDate,
                        'status' => $status,
                        'value_indicator' => 'High potential conversion'
                    ];
                }
                
                // Time sensitive tasks (next 3 days)
                if ($dateOnly <= date('Y-m-d', strtotime('+3 days'))) {
                    $priorityAnalysis['time_sensitive_tasks'][] = [
                        'task_id' => $task->task_id ?? 0,
                        'company' => $companyName,
                        'user' => $userName,
                        'task' => $task->task_name ?? '',
                        'scheduled_date' => $taskDate,
                        'status' => $status,
                        'urgency' => 'Upcoming deadline'
                    ];
                }
                
                // Follow-up tasks (based on review type)
                if (!empty($reviewType)) {
                    $priorityAnalysis['follow_up_tasks'][] = [
                        'task_id' => $task->task_id ?? 0,
                        'company' => $companyName,
                        'user' => $userName,
                        'task' => $task->task_name ?? '',
                        'scheduled_date' => $taskDate,
                        'review_type' => $reviewType,
                        'status' => $status
                    ];
                }
            }
            
            // Risk assessment
            $riskLevel = $this->assess_task_risk($task);
            if ($riskLevel !== 'Low') {
                $priorityAnalysis['risk_assessment'][] = [
                    'task_id' => $task->task_id ?? 0,
                    'company' => $companyName,
                    'user' => $userName,
                    'task' => $task->task_name ?? '',
                    'scheduled_date' => $taskDate,
                    'status' => $status,
                    'risk_level' => $riskLevel,
                    'risk_factors' => $this->identify_risk_factors($task)
                ];
            }
        }
        
        // Count priority tasks
        $priorityAnalysis['priority_task_count'] = count($priorityAnalysis['priority_tasks']);
        $priorityAnalysis['high_value_task_count'] = count($priorityAnalysis['high_value_tasks']);
        $priorityAnalysis['time_sensitive_task_count'] = count($priorityAnalysis['time_sensitive_tasks']);
        $priorityAnalysis['follow_up_task_count'] = count($priorityAnalysis['follow_up_tasks']);
        $priorityAnalysis['risk_task_count'] = count($priorityAnalysis['risk_assessment']);
        
        return $priorityAnalysis;
    }

    /**
     * Assess task risk
     */
    private function assess_task_risk($task)
    {
        $riskScore = 0;
        $riskFactors = [];
        
        $status = $task->current_status ?? '';
        $taskDate = $task->appointmentdatetime ?? '';
        $markedDays = $task->marked_meeting_days ?? '';
        
        // Status-based risk
        $negativeStatuses = ['Not Interested', 'Rejected', 'Will do Later'];
        if (in_array($status, $negativeStatuses)) {
            $riskScore += 3;
            $riskFactors[] = 'Negative status: ' . $status;
        }
        
        // Time-based risk
        if (!empty($taskDate)) {
            $daysUntil = $this->calculate_days_until_task($taskDate);
            if ($daysUntil < 0) {
                $riskScore += 4; // Overdue
                $riskFactors[] = 'Task is overdue';
            } elseif ($daysUntil <= 1) {
                $riskScore += 3; // Due today/tomorrow
                $riskFactors[] = 'Task due within 24 hours';
            } elseif ($daysUntil <= 3) {
                $riskScore += 2; // Due in 3 days
                $riskFactors[] = 'Task due within 3 days';
            }
        }
        
        // Review type risk
        $reviewType = $task->reviewtype ?? '';
        if (!empty($reviewType)) {
            if (strpos($reviewType, 'Weekly') !== false) {
                $riskScore += 1;
            } elseif (strpos($reviewType, 'Monthly') !== false) {
                $riskScore += 2;
            }
        }
        
        // Determine risk level
        if ($riskScore >= 5) return 'High';
        if ($riskScore >= 3) return 'Medium';
        if ($riskScore >= 1) return 'Low';
        return 'None';
    }

    /**
     * Calculate days until task
     */
    private function calculate_days_until_task($taskDate)
    {
        if (empty($taskDate)) {
            return 999;
        }
        
        $taskTime = strtotime($taskDate);
        $currentTime = time();
        
        $secondsDiff = $taskTime - $currentTime;
        return floor($secondsDiff / (60 * 60 * 24));
    }

    /**
     * Identify risk factors
     */
    private function identify_risk_factors($task)
    {
        $factors = [];
        
        $status = $task->current_status ?? '';
        $taskDate = $task->appointmentdatetime ?? '';
        $reviewType = $task->reviewtype ?? '';
        
        // Status factors
        if (strpos($status, 'Not Interested') !== false) {
            $factors[] = 'Client not interested';
        }
        if (strpos($status, 'Rejected') !== false) {
            $factors[] = 'Proposal rejected';
        }
        if (strpos($status, 'Will do Later') !== false) {
            $factors[] = 'Deferred decision';
        }
        
        // Time factors
        if (!empty($taskDate)) {
            $daysUntil = $this->calculate_days_until_task($taskDate);
            if ($daysUntil < 0) {
                $factors[] = 'Overdue by ' . abs($daysUntil) . ' days';
            } elseif ($daysUntil <= 1) {
                $factors[] = 'Due within 24 hours';
            }
        }
        
        // Review type factors
        if (!empty($reviewType)) {
            if (strpos($reviewType, 'Monthly') !== false) {
                $factors[] = 'Monthly review - critical timing';
            }
        }
        
        return $factors;
    }

    /**
     * Calculate performance metrics
     */
    private function calculate_performance_metrics($tasks)
    {
        $metrics = [
            'total_tasks' => count($tasks),
            'completed_tasks' => 0,
            'pending_tasks' => 0,
            'overdue_tasks' => 0,
            'upcoming_tasks' => 0,
            'completion_rate' => 0,
            'average_tasks_per_user' => 0,
            'average_tasks_per_company' => 0,
            'response_time_efficiency' => 0,
            'task_distribution_efficiency' => 0
        ];
        
        $userTaskCounts = [];
        $companyTaskCounts = [];
        $today = date('Y-m-d');
        
        foreach ($tasks as $task) {
            $userName = $task->task_user_name ?? 'Unknown';
            $companyName = $task->compname ?? 'Unknown';
            $status = $task->current_status ?? '';
            $taskDate = $task->appointmentdatetime ?? '';
            
            // Count user tasks
            if (!isset($userTaskCounts[$userName])) {
                $userTaskCounts[$userName] = 0;
            }
            $userTaskCounts[$userName]++;
            
            // Count company tasks
            if (!isset($companyTaskCounts[$companyName])) {
                $companyTaskCounts[$companyName] = 0;
            }
            $companyTaskCounts[$companyName]++;
            
            // Count completed tasks
            $positiveStatuses = ['Positive', 'Positive-NAP', 'Very Positive', 'Closure', 'On Boarded'];
            if (in_array($status, $positiveStatuses)) {
                $metrics['completed_tasks']++;
            } else {
                $metrics['pending_tasks']++;
            }
            
            // Count overdue and upcoming tasks
            if (!empty($taskDate)) {
                $dateOnly = date('Y-m-d', strtotime($taskDate));
                if ($dateOnly < $today) {
                    $metrics['overdue_tasks']++;
                } elseif ($dateOnly >= $today && $dateOnly <= date('Y-m-d', strtotime('+7 days'))) {
                    $metrics['upcoming_tasks']++;
                }
            }
        }
        
        // Calculate rates
        if ($metrics['total_tasks'] > 0) {
            $metrics['completion_rate'] = round(($metrics['completed_tasks'] / $metrics['total_tasks']) * 100, 2);
        }
        
        // Calculate averages
        if (!empty($userTaskCounts)) {
            $metrics['average_tasks_per_user'] = round($metrics['total_tasks'] / count($userTaskCounts), 2);
        }
        
        if (!empty($companyTaskCounts)) {
            $metrics['average_tasks_per_company'] = round($metrics['total_tasks'] / count($companyTaskCounts), 2);
        }
        
        // Calculate efficiency metrics (simplified for now)
        $metrics['response_time_efficiency'] = $this->calculate_response_time_efficiency($tasks);
        $metrics['task_distribution_efficiency'] = $this->calculate_distribution_efficiency($userTaskCounts);
        
        return $metrics;
    }

    /**
     * Calculate response time efficiency
     */
    private function calculate_response_time_efficiency($tasks)
    {
        // This is a simplified calculation
        // In a real system, you would track actual response times
        
        $efficiencyScore = 0;
        $timelyResponses = 0;
        $totalTasks = count($tasks);
        
        foreach ($tasks as $task) {
            $status = $task->current_status ?? '';
            $taskDate = $task->appointmentdatetime ?? '';
            
            if (!empty($taskDate)) {
                $daysUntil = $this->calculate_days_until_task($taskDate);
                
                // Consider tasks that are completed or have positive status as timely
                $positiveStatuses = ['Positive', 'Positive-NAP', 'Very Positive', 'Closure', 'On Boarded'];
                if (in_array($status, $positiveStatuses) || $daysUntil > 0) {
                    $timelyResponses++;
                }
            }
        }
        
        if ($totalTasks > 0) {
            $efficiencyScore = round(($timelyResponses / $totalTasks) * 100, 2);
        }
        
        return $efficiencyScore;
    }

    /**
     * Calculate distribution efficiency
     */
    private function calculate_distribution_efficiency($userTaskCounts)
    {
        if (empty($userTaskCounts)) {
            return 0;
        }
        
        $totalTasks = array_sum($userTaskCounts);
        $userCount = count($userTaskCounts);
        
        if ($userCount == 0) {
            return 0;
        }
        
        $idealTasksPerUser = $totalTasks / $userCount;
        $varianceSum = 0;
        
        foreach ($userTaskCounts as $taskCount) {
            $variance = abs($taskCount - $idealTasksPerUser);
            $varianceSum += $variance;
        }
        
        $averageVariance = $varianceSum / $userCount;
        
        // Efficiency is higher when variance is lower
        $maxAcceptableVariance = $idealTasksPerUser * 0.5; // 50% variation is acceptable
        if ($maxAcceptableVariance == 0) {
            return 100;
        }
        
        $efficiency = 100 - min(100, ($averageVariance / $maxAcceptableVariance) * 100);
        return round($efficiency, 2);
    }

    /**
     * Identify upcoming tasks
     */
    private function identify_upcoming_tasks($tasks)
    {
        $upcomingTasks = [];
        $today = date('Y-m-d');
        $nextWeek = date('Y-m-d', strtotime('+7 days'));
        
        foreach ($tasks as $task) {
            $taskDate = $task->appointmentdatetime ?? '';
            
            if (!empty($taskDate)) {
                $dateOnly = date('Y-m-d', strtotime($taskDate));
                
                if ($dateOnly >= $today && $dateOnly <= $nextWeek) {
                    $upcomingTasks[] = [
                        'task_id' => $task->task_id ?? 0,
                        'company' => $task->compname ?? '',
                        'user' => $task->task_user_name ?? '',
                        'task' => $task->task_name ?? '',
                        'scheduled_date' => $taskDate,
                        'status' => $task->current_status ?? '',
                        'review_type' => $task->reviewtype ?? '',
                        'days_until' => $this->calculate_days_until_task($taskDate)
                    ];
                }
            }
        }
        
        // Sort by date
        usort($upcomingTasks, function($a, $b) {
            return strtotime($a['scheduled_date']) - strtotime($b['scheduled_date']);
        });
        
        return $upcomingTasks;
    }

    /**
     * Identify overdue tasks
     */
    private function identify_overdue_tasks($tasks)
    {
        $overdueTasks = [];
        $today = date('Y-m-d');
        
        foreach ($tasks as $task) {
            $taskDate = $task->appointmentdatetime ?? '';
            $status = $task->current_status ?? '';
            
            if (!empty($taskDate)) {
                $dateOnly = date('Y-m-d', strtotime($taskDate));
                
                // Consider tasks overdue if they are before today AND not completed
                $completedStatuses = ['Positive', 'Positive-NAP', 'Very Positive', 'Closure', 'On Boarded'];
                if ($dateOnly < $today && !in_array($status, $completedStatuses)) {
                    $overdueDays = floor((strtotime($today) - strtotime($dateOnly)) / (60 * 60 * 24));
                    
                    $overdueTasks[] = [
                        'task_id' => $task->task_id ?? 0,
                        'company' => $task->compname ?? '',
                        'user' => $task->task_user_name ?? '',
                        'task' => $task->task_name ?? '',
                        'scheduled_date' => $taskDate,
                        'status' => $status,
                        'overdue_days' => $overdueDays,
                        'review_type' => $task->reviewtype ?? '',
                        'priority' => $this->determine_overdue_priority($overdueDays)
                    ];
                }
            }
        }
        
        // Sort by overdue days (most overdue first)
        usort($overdueTasks, function($a, $b) {
            return $b['overdue_days'] - $a['overdue_days'];
        });
        
        return $overdueTasks;
    }

    /**
     * Determine overdue priority
     */
    private function determine_overdue_priority($overdueDays)
    {
        if ($overdueDays > 14) return 'Critical';
        if ($overdueDays > 7) return 'High';
        if ($overdueDays > 3) return 'Medium';
        return 'Low';
    }

    /**
     * Generate recommendations
     */
    private function generate_recommendations($tasks)
    {
        $recommendations = [
            'general' => [],
            'user_specific' => [],
            'company_specific' => [],
            'process_improvements' => [],
            'timeline_optimization' => []
        ];
        
        $totalTasks = count($tasks);
        $overdueTasks = $this->identify_overdue_tasks($tasks);
        $upcomingTasks = $this->identify_upcoming_tasks($tasks);
        
        // General recommendations
        if (count($overdueTasks) > 0) {
            $recommendations['general'][] = [
                'priority' => 'High',
                'title' => 'Address Overdue Tasks',
                'description' => "There are " . count($overdueTasks) . " overdue tasks that need immediate attention.",
                'action' => 'Review overdue tasks and assign resources to complete them.'
            ];
        }
        
        if (count($upcomingTasks) > 10) {
            $recommendations['general'][] = [
                'priority' => 'Medium',
                'title' => 'Manage Upcoming Workload',
                'description' => "You have " . count($upcomingTasks) . " tasks scheduled for the next week.",
                'action' => 'Prioritize and schedule resources for upcoming tasks.'
            ];
        }
        
        // User-specific recommendations
        $userDistribution = $this->analyze_user_distribution($tasks);
        foreach ($userDistribution as $user) {
            $userRecs = [];
            
            if ($user['total_tasks'] > 20) {
                $userRecs[] = [
                    'priority' => 'Medium',
                    'message' => 'High task volume - consider delegating or rescheduling'
                ];
            }
            
            if ($user['completion_rate'] < 50) {
                $userRecs[] = [
                    'priority' => 'High',
                    'message' => 'Low completion rate - needs process improvement'
                ];
            }
            
            if (!empty($userRecs)) {
                $recommendations['user_specific'][$user['username']] = [
                    'username' => $user['username'],
                    'recommendations' => $userRecs,
                    'metrics' => [
                        'total_tasks' => $user['total_tasks'],
                        'completion_rate' => $user['completion_rate']
                    ]
                ];
            }
        }
        
        // Company-specific recommendations
        $companyDistribution = $this->analyze_company_distribution($tasks);
        foreach ($companyDistribution as $company) {
            if ($company['total_tasks'] > 5 && $company['engagement_level'] === 'Low') {
                $recommendations['company_specific'][] = [
                    'company_name' => $company['company_name'],
                    'task_count' => $company['total_tasks'],
                    'engagement_level' => $company['engagement_level'],
                    'recommendation' => 'Improve engagement strategy for this client'
                ];
            }
        }
        
        // Process improvements
        $timeAnalysis = $this->analyze_time_distribution($tasks);
        if ($timeAnalysis['immediate_tasks'] > 5) {
            $recommendations['process_improvements'][] = [
                'area' => 'Task Scheduling',
                'issue' => 'Too many immediate tasks',
                'metric' => $timeAnalysis['immediate_tasks'] . ' tasks due today',
                'recommendation' => 'Spread out task deadlines to avoid bottlenecks'
            ];
        }
        
        // Timeline optimization
        $priorityAnalysis = $this->analyze_priority_distribution($tasks);
        if ($priorityAnalysis['priority_task_count'] > 0) {
            $recommendations['timeline_optimization'][] = [
                'strategy' => 'Focus on Priority Tasks',
                'details' => $priorityAnalysis['priority_task_count'] . ' tasks are due today or overdue',
                'action' => 'Allocate resources to complete priority tasks first'
            ];
        }
        
        return $recommendations;
    }

    /**
     * Select top users
     */
    private function select_top_users($tasks, $topCount)
    {
        $userDistribution = $this->analyze_user_distribution($tasks);
        
        // Sort by total tasks descending
        usort($userDistribution, function($a, $b) {
            return $b['total_tasks'] - $a['total_tasks'];
        });
        
        // Select top users
        $topUsers = array_slice($userDistribution, 0, $topCount);
        
        // Add ranking
        $rank = 1;
        foreach ($topUsers as &$user) {
            $user['rank'] = $rank;
            $user['performance_score'] = $this->calculate_user_performance_score($user);
            $user['performance_rating'] = $this->get_performance_rating($user['performance_score']);
            $rank++;
        }
        
        return $topUsers;
    }

    /**
     * Calculate user performance score
     */
    private function calculate_user_performance_score($user)
    {
        $score = 0;
        
        // Task volume (max 30 points)
        $taskCount = $user['total_tasks'];
        if ($taskCount >= 20) $score += 30;
        elseif ($taskCount >= 15) $score += 25;
        elseif ($taskCount >= 10) $score += 20;
        elseif ($taskCount >= 5) $score += 15;
        elseif ($taskCount >= 2) $score += 10;
        elseif ($taskCount > 0) $score += 5;
        
        // Completion rate (max 30 points)
        $completionRate = $user['completion_rate'];
        if ($completionRate >= 80) $score += 30;
        elseif ($completionRate >= 60) $score += 25;
        elseif ($completionRate >= 40) $score += 20;
        elseif ($completionRate >= 20) $score += 15;
        elseif ($completionRate > 0) $score += 10;
        
        // Company diversity (max 20 points)
        $companyCount = $user['unique_companies'];
        if ($companyCount >= 10) $score += 20;
        elseif ($companyCount >= 7) $score += 15;
        elseif ($companyCount >= 5) $score += 10;
        elseif ($companyCount >= 3) $score += 5;
        
        // Task type balance (max 20 points)
        $emailPercentage = $user['email_percentage'] ?? 0;
        $callPercentage = $user['call_percentage'] ?? 0;
        
        // Balanced approach is better (neither too many emails nor too many calls)
        $balanceScore = 100 - abs($emailPercentage - $callPercentage);
        $score += round($balanceScore * 0.2); // Convert to 20 point scale
        
        return $score;
    }

    /**
     * Get performance rating
     */
    private function get_performance_rating($score)
    {
        if ($score >= 80) return 'Excellent';
        if ($score >= 60) return 'Good';
        if ($score >= 40) return 'Average';
        if ($score >= 20) return 'Needs Improvement';
        return 'Poor';
    }

    /**
     * Select top companies
     */
    private function select_top_companies($tasks, $topCount)
    {
        $companyDistribution = $this->analyze_company_distribution($tasks);
        
        // Sort by engagement score descending
        usort($companyDistribution, function($a, $b) {
            return $b['engagement_score'] - $a['engagement_score'];
        });
        
        // Select top companies
        $topCompanies = array_slice($companyDistribution, 0, $topCount);
        
        // Add ranking and justification
        $rank = 1;
        foreach ($topCompanies as &$company) {
            $company['rank'] = $rank;
            $company['justification'] = $this->generate_company_justification($company);
            $rank++;
        }
        
        return $topCompanies;
    }

    /**
     * Generate company justification
     */
    private function generate_company_justification($company)
    {
        $justification = "Selected as a top company because: ";
        
        $reasons = [];
        
        if ($company['total_tasks'] >= 5) {
            $reasons[] = "high task frequency ({$company['total_tasks']} tasks)";
        }
        
        if ($company['unique_users'] >= 2) {
            $reasons[] = "multiple team members involved ({$company['unique_users']} users)";
        }
        
        if ($company['engagement_level'] === 'High' || $company['engagement_level'] === 'Very High') {
            $reasons[] = "high engagement level ({$company['engagement_level']})";
        }
        
        if (!empty($company['review_types'])) {
            $reasons[] = "regular review cycles";
        }
        
        if (empty($reasons)) {
            $reasons[] = "consistent engagement pattern";
        }
        
        $justification .= implode(', ', $reasons);
        $justification .= ". Overall engagement score: {$company['engagement_score']}.";
        
        return $justification;
    }

    /**
     * Analyze table data
     */
    private function analyze_table_data($tasks)
    {
        $tableData = [
            'task_schedule_table' => [],
            'user_performance_table' => [],
            'company_engagement_table' => [],
            'priority_tasks_table' => [],
            'status_distribution_table' => [],
            'time_analysis_table' => [],
            'task_type_table' => [],
            'upcoming_tasks_table' => [],
            'overdue_tasks_table' => [],
            'efficiency_metrics_table' => []
        ];

        // Task Schedule Table
        $tableData['task_schedule_table'] = $this->generate_task_schedule_table($tasks);
        
        // User Performance Table
        $tableData['user_performance_table'] = $this->generate_user_performance_table($tasks);
        
        // Company Engagement Table
        $tableData['company_engagement_table'] = $this->generate_company_engagement_table($tasks);
        
        // Priority Tasks Table
        $tableData['priority_tasks_table'] = $this->generate_priority_tasks_table($tasks);
        
        // Status Distribution Table
        $tableData['status_distribution_table'] = $this->generate_status_distribution_table_data($tasks);
        
        // Time Analysis Table
        $tableData['time_analysis_table'] = $this->generate_time_analysis_table($tasks);
        
        // Task Type Table
        $tableData['task_type_table'] = $this->generate_task_type_table($tasks);
        
        // Upcoming Tasks Table
        $tableData['upcoming_tasks_table'] = $this->identify_upcoming_tasks($tasks);
        
        // Overdue Tasks Table
        $tableData['overdue_tasks_table'] = $this->identify_overdue_tasks($tasks);
        
        // Efficiency Metrics Table
        $tableData['efficiency_metrics_table'] = $this->generate_efficiency_metrics_table($tasks);

        return $tableData;
    }

    /**
     * Generate task schedule table
     */
    private function generate_task_schedule_table($tasks)
    {
        $scheduleTable = [];
        
        foreach ($tasks as $task) {
            $taskDate = $task->appointmentdatetime ?? '';
            
            if (!empty($taskDate)) {
                $scheduleTable[] = [
                    'date' => date('Y-m-d', strtotime($taskDate)),
                    'time' => date('H:i', strtotime($taskDate)),
                    'company' => $task->compname ?? '',
                    'user' => $task->task_user_name ?? '',
                    'task_type' => $task->task_name ?? '',
                    'status' => $task->current_status ?? '',
                    'review_type' => $task->reviewtype ?? '',
                    'task_id' => $task->task_id ?? 0
                ];
            }
        }
        
        // Sort by date and time
        usort($scheduleTable, function($a, $b) {
            $dateCompare = strcmp($a['date'], $b['date']);
            if ($dateCompare === 0) {
                return strcmp($a['time'], $b['time']);
            }
            return $dateCompare;
        });
        
        return $scheduleTable;
    }

    /**
     * Generate user performance table
     */
    private function generate_user_performance_table($tasks)
    {
        $userDistribution = $this->analyze_user_distribution($tasks);
        $performanceTable = [];
        
        foreach ($userDistribution as $user) {
            $performanceTable[] = [
                'username' => $user['username'],
                'total_tasks' => $user['total_tasks'],
                'completed_tasks' => $user['completed_tasks'],
                'pending_tasks' => $user['pending_tasks'],
                'completion_rate' => $user['completion_rate'] . '%',
                'emails' => $user['emails'],
                'calls' => $user['calls'],
                'unique_companies' => $user['unique_companies'],
                'performance_score' => $this->calculate_user_performance_score($user),
                'performance_rating' => $this->get_performance_rating($this->calculate_user_performance_score($user))
            ];
        }
        
        return $performanceTable;
    }

    /**
     * Generate company engagement table
     */
    private function generate_company_engagement_table($tasks)
    {
        $companyDistribution = $this->analyze_company_distribution($tasks);
        $engagementTable = [];
        
        foreach ($companyDistribution as $company) {
            $engagementTable[] = [
                'company_name' => $company['company_name'],
                'total_tasks' => $company['total_tasks'],
                'unique_users' => $company['unique_users'],
                'unique_task_types' => $company['unique_task_types'],
                'first_task_date' => $company['first_task_date'],
                'last_task_date' => $company['last_task_date'],
                'engagement_score' => $company['engagement_score'],
                'engagement_level' => $company['engagement_level'],
                'days_since_last_task' => $this->calculate_days_since_last_task($company['last_task_date'])
            ];
        }
        
        return $engagementTable;
    }

    /**
     * Generate priority tasks table
     */
    private function generate_priority_tasks_table($tasks)
    {
        $priorityAnalysis = $this->analyze_priority_distribution($tasks);
        $priorityTable = [];
        
        // Combine all priority tasks
        $allPriorityTasks = array_merge(
            $priorityAnalysis['priority_tasks'],
            $priorityAnalysis['high_value_tasks'],
            $priorityAnalysis['time_sensitive_tasks']
        );
        
        foreach ($allPriorityTasks as $task) {
            $priorityTable[] = [
                'task_id' => $task['task_id'] ?? 0,
                'company' => $task['company'] ?? '',
                'user' => $task['user'] ?? '',
                'task_type' => $task['task'] ?? '',
                'scheduled_date' => $task['scheduled_date'] ?? '',
                'status' => $task['status'] ?? '',
                'priority_type' => $this->determine_priority_type($task),
                'priority_level' => $this->determine_priority_level($task)
            ];
        }
        
        // Sort by priority level
        usort($priorityTable, function($a, $b) {
            $priorityOrder = ['Critical' => 4, 'High' => 3, 'Medium' => 2, 'Low' => 1];
            $aLevel = $priorityOrder[$a['priority_level']] ?? 0;
            $bLevel = $priorityOrder[$b['priority_level']] ?? 0;
            return $bLevel - $aLevel;
        });
        
        return $priorityTable;
    }

    /**
     * Determine priority type
     */
    private function determine_priority_type($task)
    {
        if (isset($task['priority_reason']) && strpos($task['priority_reason'], 'Due today') !== false) {
            return 'Immediate';
        }
        if (isset($task['value_indicator']) && strpos($task['value_indicator'], 'High potential') !== false) {
            return 'High Value';
        }
        if (isset($task['urgency']) && strpos($task['urgency'], 'Upcoming deadline') !== false) {
            return 'Time Sensitive';
        }
        return 'Standard';
    }

    /**
     * Determine priority level
     */
    private function determine_priority_level($task)
    {
        $scheduledDate = $task['scheduled_date'] ?? '';
        $status = $task['status'] ?? '';
        
        if (empty($scheduledDate)) {
            return 'Medium';
        }
        
        $daysUntil = $this->calculate_days_until_task($scheduledDate);
        
        if ($daysUntil < 0) {
            return 'Critical';
        } elseif ($daysUntil <= 1) {
            return 'High';
        } elseif ($daysUntil <= 3) {
            return 'Medium';
        }
        
        return 'Low';
    }

    /**
     * Generate status distribution table data
     */
    private function generate_status_distribution_table_data($tasks)
    {
        $statusAnalysis = $this->analyze_status_distribution($tasks);
        $statusTable = [];
        
        foreach ($statusAnalysis['status_counts'] as $status => $count) {
            $percentage = round(($count / count($tasks)) * 100, 2);
            $category = $this->categorize_status($status);
            
            $statusTable[] = [
                'status' => $status,
                'count' => $count,
                'percentage' => $percentage . '%',
                'category' => $category,
                'trend' => $this->get_status_trend($status, $statusAnalysis['status_trends'])
            ];
        }
        
        // Sort by count descending
        usort($statusTable, function($a, $b) {
            return $b['count'] - $a['count'];
        });
        
        return $statusTable;
    }

    /**
     * Categorize status
     */
    private function categorize_status($status)
    {
        $positiveStatuses = ['Positive', 'Positive-NAP', 'Very Positive', 'Very Positive-NAP', 'Closure', 'On Boarded'];
        $neutralStatuses = ['Open', 'OPEN RPEM', 'Reachout', 'Tentative', 'Will do Later'];
        $negativeStatuses = ['Not Interested', 'Rejected'];
        
        if (in_array($status, $positiveStatuses)) return 'Positive';
        if (in_array($status, $neutralStatuses)) return 'Neutral';
        if (in_array($status, $negativeStatuses)) return 'Negative';
        return 'Unknown';
    }

    /**
     * Get status trend
     */
    private function get_status_trend($status, $statusTrends)
    {
        if (empty($statusTrends) || count($statusTrends) < 2) {
            return 'Stable';
        }
        
        $months = array_keys($statusTrends);
        $lastMonth = end($months);
        $secondLastMonth = prev($months);
        
        $lastMonthCount = $statusTrends[$lastMonth][$status] ?? 0;
        $secondLastMonthCount = $statusTrends[$secondLastMonth][$status] ?? 0;
        
        if ($secondLastMonthCount == 0) {
            return $lastMonthCount > 0 ? 'Increasing' : 'Stable';
        }
        
        $change = (($lastMonthCount - $secondLastMonthCount) / $secondLastMonthCount) * 100;
        
        if ($change > 20) return 'Increasing';
        if ($change < -20) return 'Decreasing';
        return 'Stable';
    }

    /**
     * Generate time analysis table
     */
    private function generate_time_analysis_table($tasks)
    {
        $timeAnalysis = $this->analyze_time_distribution($tasks);
        $timeTable = [];
        
        $timeCategories = [
            'immediate_tasks' => 'Today',
            'short_term_tasks' => 'Next 7 Days',
            'medium_term_tasks' => 'Next 30 Days',
            'long_term_tasks' => 'Beyond 30 Days'
        ];
        
        foreach ($timeCategories as $key => $label) {
            $timeTable[] = [
                'timeframe' => $label,
                'task_count' => $timeAnalysis[$key],
                'percentage' => round(($timeAnalysis[$key] / count($tasks)) * 100, 2) . '%',
                'workload' => $this->assess_workload($timeAnalysis[$key], $label)
            ];
        }
        
        return $timeTable;
    }

    /**
     * Assess workload
     */
    private function assess_workload($taskCount, $timeframe)
    {
        $workloadThresholds = [
            'Today' => ['high' => 5, 'medium' => 3],
            'Next 7 Days' => ['high' => 15, 'medium' => 10],
            'Next 30 Days' => ['high' => 30, 'medium' => 20],
            'Beyond 30 Days' => ['high' => 50, 'medium' => 30]
        ];
        
        $threshold = $workloadThresholds[$timeframe] ?? ['high' => 10, 'medium' => 5];
        
        if ($taskCount >= $threshold['high']) return 'Heavy';
        if ($taskCount >= $threshold['medium']) return 'Moderate';
        return 'Light';
    }

    /**
     * Generate task type table
     */
    private function generate_task_type_table($tasks)
    {
        $taskTypeAnalysis = $this->analyze_task_type_distribution($tasks);
        $taskTypeTable = [];
        
        foreach ($taskTypeAnalysis['type_counts'] as $taskType => $count) {
            $efficiency = $taskTypeAnalysis['type_efficiency'][$taskType] ?? ['positive_rate' => 0, 'completion_rate' => 0];
            
            $taskTypeTable[] = [
                'task_type' => $taskType,
                'count' => $count,
                'percentage' => round(($count / count($tasks)) * 100, 2) . '%',
                'positive_rate' => $efficiency['positive_rate'] . '%',
                'completion_rate' => $efficiency['completion_rate'] . '%',
                'efficiency_rating' => $this->get_efficiency_rating($efficiency['completion_rate'])
            ];
        }
        
        // Sort by count descending
        usort($taskTypeTable, function($a, $b) {
            return $b['count'] - $a['count'];
        });
        
        return $taskTypeTable;
    }

    /**
     * Get efficiency rating
     */
    private function get_efficiency_rating($completionRate)
    {
        if ($completionRate >= 80) return 'Excellent';
        if ($completionRate >= 60) return 'Good';
        if ($completionRate >= 40) return 'Average';
        if ($completionRate >= 20) return 'Below Average';
        return 'Poor';
    }

    /**
     * Generate efficiency metrics table
     */
    private function generate_efficiency_metrics_table($tasks)
    {
        $performanceMetrics = $this->calculate_performance_metrics($tasks);
        $efficiencyTable = [];
        
        $metrics = [
            ['label' => 'Total Tasks', 'value' => $performanceMetrics['total_tasks']],
            ['label' => 'Completion Rate', 'value' => $performanceMetrics['completion_rate'] . '%'],
            ['label' => 'Average Tasks per User', 'value' => $performanceMetrics['average_tasks_per_user']],
            ['label' => 'Average Tasks per Company', 'value' => $performanceMetrics['average_tasks_per_company']],
            ['label' => 'Response Time Efficiency', 'value' => $performanceMetrics['response_time_efficiency'] . '%'],
            ['label' => 'Task Distribution Efficiency', 'value' => $performanceMetrics['task_distribution_efficiency'] . '%'],
            ['label' => 'Overdue Tasks', 'value' => $performanceMetrics['overdue_tasks']],
            ['label' => 'Upcoming Tasks', 'value' => $performanceMetrics['upcoming_tasks']]
        ];
        
        foreach ($metrics as $metric) {
            $efficiencyTable[] = [
                'metric' => $metric['label'],
                'value' => $metric['value'],
                'rating' => $this->rate_efficiency_metric($metric['label'], $metric['value'])
            ];
        }
        
        return $efficiencyTable;
    }

    /**
     * Rate efficiency metric
     */
    private function rate_efficiency_metric($metric, $value)
    {
        $numericValue = floatval($value);
        
        switch ($metric) {
            case 'Completion Rate':
                if ($numericValue >= 80) return 'Excellent';
                if ($numericValue >= 60) return 'Good';
                if ($numericValue >= 40) return 'Average';
                return 'Needs Improvement';
                
            case 'Response Time Efficiency':
                if ($numericValue >= 90) return 'Excellent';
                if ($numericValue >= 75) return 'Good';
                if ($numericValue >= 50) return 'Average';
                return 'Needs Improvement';
                
            case 'Task Distribution Efficiency':
                if ($numericValue >= 85) return 'Excellent';
                if ($numericValue >= 70) return 'Good';
                if ($numericValue >= 50) return 'Average';
                return 'Needs Improvement';
                
            case 'Overdue Tasks':
                if ($numericValue == 0) return 'Excellent';
                if ($numericValue <= 3) return 'Good';
                if ($numericValue <= 10) return 'Average';
                return 'Needs Improvement';
                
            default:
                return 'Satisfactory';
        }
    }

    /**
     * Generate response based on message
     */
    private function generate_response_based_on_message($message, $analysisResults, $topCount, $startDate, $endDate)
    {
        $lowerMessage = strtolower(trim($message));
        
        $response = "📊 **FUTURE TASK ANALYSIS REPORT**\n\n";
        $response .= "**Period:** {$startDate} to {$endDate}\n";
        $response .= "**Total Tasks Analyzed:** {$analysisResults['total_tasks']}\n";
        $response .= "**Total Users:** {$analysisResults['total_users']}\n";
        $response .= "**Total Companies:** {$analysisResults['total_companies']}\n\n";
        
        // Add summary based on message intent
        if (strpos($lowerMessage, 'top compan') !== false) {
            $response .= $this->generate_top_companies_summary($analysisResults, $topCount);
        }
        
        if (strpos($lowerMessage, 'top user') !== false) {
            $response .= $this->generate_top_users_summary($analysisResults, $topCount);
        }
        
        if (strpos($lowerMessage, 'upcoming') !== false || 
            strpos($lowerMessage, 'schedule') !== false) {
            $response .= $this->generate_upcoming_tasks_summary($analysisResults);
        }
        
        if (strpos($lowerMessage, 'overdue') !== false) {
            $response .= $this->generate_overdue_tasks_summary($analysisResults);
        }
        
        if (strpos($lowerMessage, 'priority') !== false) {
            $response .= $this->generate_priority_tasks_summary($analysisResults);
        }
        
        if (strpos($lowerMessage, 'status') !== false) {
            $response .= $this->generate_status_summary($analysisResults);
        }
        
        if (strpos($lowerMessage, 'performance') !== false) {
            $response .= $this->generate_performance_summary($analysisResults);
        }
        
        if (strpos($lowerMessage, 'recommend') !== false) {
            $response .= $this->generate_recommendations_summary($analysisResults);
        }
        
        // Default comprehensive report
        if (strpos($lowerMessage, 'detail') !== false || 
            strpos($lowerMessage, 'comprehensive') !== false ||
            strlen($lowerMessage) < 5) {
            $response .= $this->generate_comprehensive_summary($analysisResults, $topCount);
        }
        
        return $response;
    }

    /**
     * Generate top companies summary
     */
    private function generate_top_companies_summary($analysisResults, $topCount)
    {
        $response = "🏆 **TOP {$topCount} COMPANIES BY ENGAGEMENT**\n\n";
        
        if (empty($analysisResults['top_companies'])) {
            $response .= "No companies found for analysis.\n\n";
            return $response;
        }
        
        $response .= "Here are the top {$topCount} companies based on engagement score:\n\n";
        
        foreach ($analysisResults['top_companies'] as $company) {
            $response .= "**#{$company['rank']}. {$company['company_name']}**\n";
            $response .= "   📊 Engagement Score: {$company['engagement_score']} ({$company['engagement_level']})\n";
            $response .= "   📄 Total Tasks: {$company['total_tasks']}\n";
            $response .= "   👥 Assigned Users: {$company['unique_users']}\n";
            $response .= "   💡 Why Selected: {$company['justification']}\n\n";
        }
        
        return $response;
    }

    /**
     * Generate top users summary
     */
    private function generate_top_users_summary($analysisResults, $topCount)
    {
        $response = "👥 **TOP {$topCount} PERFORMING USERS**\n\n";
        
        if (empty($analysisResults['top_users'])) {
            $response .= "No user data found for analysis.\n\n";
            return $response;
        }
        
        $response .= "Here are the top {$topCount} users based on performance score:\n\n";
        
        foreach ($analysisResults['top_users'] as $user) {
            $response .= "**#{$user['rank']}. {$user['username']}**\n";
            $response .= "   📊 Performance Score: {$user['performance_score']} ({$user['performance_rating']})\n";
            $response .= "   📄 Total Tasks: {$user['total_tasks']}\n";
            $response .= "   ✅ Completion Rate: {$user['completion_rate']}%\n";
            $response .= "   🏢 Companies Handled: {$user['unique_companies']}\n";
            $response .= "   📧 Emails: {$user['emails']} | 📞 Calls: {$user['calls']}\n\n";
        }
        
        return $response;
    }

    /**
     * Generate upcoming tasks summary
     */
    private function generate_upcoming_tasks_summary($analysisResults)
    {
        $upcomingTasks = $analysisResults['upcoming_tasks'];
        
        $response = "📅 **UPCOMING TASKS (Next 7 Days)**\n\n";
        
        if (empty($upcomingTasks)) {
            $response .= "No upcoming tasks scheduled for the next 7 days.\n\n";
            return $response;
        }
        
        $response .= "Total Upcoming Tasks: " . count($upcomingTasks) . "\n\n";
        
        // Group by day
        $tasksByDay = [];
        foreach ($upcomingTasks as $task) {
            $date = date('Y-m-d', strtotime($task['scheduled_date']));
            if (!isset($tasksByDay[$date])) {
                $tasksByDay[$date] = [];
            }
            $tasksByDay[$date][] = $task;
        }
        
        ksort($tasksByDay);
        
        foreach ($tasksByDay as $date => $tasks) {
            $dayName = date('l', strtotime($date));
            $response .= "**{$dayName} ({$date})** - " . count($tasks) . " task(s)\n";
            
            foreach ($tasks as $task) {
                $time = date('H:i', strtotime($task['scheduled_date']));
                $response .= "   • {$time} - {$task['company']} - {$task['task']} ({$task['user']})\n";
            }
            $response .= "\n";
        }
        
        return $response;
    }

    /**
     * Generate overdue tasks summary
     */
    private function generate_overdue_tasks_summary($analysisResults)
    {
        $overdueTasks = $analysisResults['overdue_tasks'];
        
        $response = "⚠️ **OVERDUE TASKS**\n\n";
        
        if (empty($overdueTasks)) {
            $response .= "No overdue tasks found. Great job!\n\n";
            return $response;
        }
        
        $response .= "Total Overdue Tasks: " . count($overdueTasks) . "\n\n";
        
        // Group by priority
        $tasksByPriority = [];
        foreach ($overdueTasks as $task) {
            $priority = $task['priority'];
            if (!isset($tasksByPriority[$priority])) {
                $tasksByPriority[$priority] = [];
            }
            $tasksByPriority[$priority][] = $task;
        }
        
        // Display in priority order
        $priorityOrder = ['Critical', 'High', 'Medium', 'Low'];
        foreach ($priorityOrder as $priority) {
            if (!empty($tasksByPriority[$priority])) {
                $response .= "**{$priority} Priority ({$priority})** - " . count($tasksByPriority[$priority]) . " task(s)\n";
                
                foreach ($tasksByPriority[$priority] as $task) {
                    $originalDate = date('Y-m-d', strtotime($task['scheduled_date']));
                    $response .= "   • {$task['company']} - {$task['task']} ({$task['user']})\n";
                    $response .= "     Overdue since: {$originalDate} ({$task['overdue_days']} days ago)\n";
                }
                $response .= "\n";
            }
        }
        
        return $response;
    }

    /**
     * Generate priority tasks summary
     */
    private function generate_priority_tasks_summary($analysisResults)
    {
        $priorityAnalysis = $analysisResults['priority_analysis'];
        
        $response = "🎯 **PRIORITY TASK ANALYSIS**\n\n";
        
        $response .= "**Task Priority Overview:**\n";
        $response .= "   • Immediate Tasks (Due Today): {$priorityAnalysis['priority_task_count']}\n";
        $response .= "   • High Value Tasks: {$priorityAnalysis['high_value_task_count']}\n";
        $response .= "   • Time Sensitive Tasks: {$priorityAnalysis['time_sensitive_task_count']}\n";
        $response .= "   • Follow-up Tasks: {$priorityAnalysis['follow_up_task_count']}\n";
        $response .= "   • At Risk Tasks: {$priorityAnalysis['risk_task_count']}\n\n";
        
        if ($priorityAnalysis['priority_task_count'] > 0) {
            $response .= "**Immediate Action Required:**\n";
            $immediateTasks = array_slice($priorityAnalysis['priority_tasks'], 0, 5);
            foreach ($immediateTasks as $task) {
                $response .= "   • {$task['company']} - {$task['task']} (Assigned to: {$task['user']})\n";
            }
            $response .= "\n";
        }
        
        return $response;
    }

    /**
     * Generate status summary
     */
    private function generate_status_summary($analysisResults)
    {
        $statusAnalysis = $analysisResults['status_analysis'];
        
        $response = "📈 **TASK STATUS ANALYSIS**\n\n";
        
        $response .= "**Status Distribution:**\n";
        foreach ($statusAnalysis['status_categories'] as $category => $count) {
            $percentage = round(($count / $analysisResults['total_tasks']) * 100, 2);
            $response .= "   • " . ucfirst($category) . ": {$count} ({$percentage}%)\n";
        }
        $response .= "\n";
        
        if (!empty($statusAnalysis['conversion_rates'])) {
            $rates = $statusAnalysis['conversion_rates'];
            $response .= "**Conversion Rates:**\n";
            $response .= "   • Positive Rate: {$rates['positive_rate']}%\n";
            $response .= "   • Completion Rate: {$rates['completion_rate']}%\n";
            $response .= "   • Pending Rate: {$rates['pending_rate']}%\n";
            $response .= "\n";
        }
        
        $response .= "**Top 5 Statuses:**\n";
        arsort($statusAnalysis['status_counts']);
        $topStatuses = array_slice($statusAnalysis['status_counts'], 0, 5, true);
        
        foreach ($topStatuses as $status => $count) {
            $percentage = round(($count / $analysisResults['total_tasks']) * 100, 2);
            $response .= "   • {$status}: {$count} ({$percentage}%)\n";
        }
        $response .= "\n";
        
        return $response;
    }

    /**
     * Generate performance summary
     */
    private function generate_performance_summary($analysisResults)
    {
        $metrics = $analysisResults['performance_metrics'];
        
        $response = "📊 **PERFORMANCE METRICS**\n\n";
        
        $response .= "**Task Management Metrics:**\n";
        $response .= "   • Total Tasks: {$metrics['total_tasks']}\n";
        $response .= "   • Completed Tasks: {$metrics['completed_tasks']}\n";
        $response .= "   • Pending Tasks: {$metrics['pending_tasks']}\n";
        $response .= "   • Overdue Tasks: {$metrics['overdue_tasks']}\n";
        $response .= "   • Upcoming Tasks: {$metrics['upcoming_tasks']}\n";
        $response .= "   • Completion Rate: {$metrics['completion_rate']}%\n\n";
        
        $response .= "**Efficiency Metrics:**\n";
        $response .= "   • Avg Tasks per User: {$metrics['average_tasks_per_user']}\n";
        $response .= "   • Avg Tasks per Company: {$metrics['average_tasks_per_company']}\n";
        $response .= "   • Response Time Efficiency: {$metrics['response_time_efficiency']}%\n";
        $response .= "   • Task Distribution Efficiency: {$metrics['task_distribution_efficiency']}%\n\n";
        
        return $response;
    }

    /**
     * Generate recommendations summary
     */
    private function generate_recommendations_summary($analysisResults)
    {
        $recommendations = $analysisResults['recommendations'];
        
        $response = "💡 **RECOMMENDATIONS & ACTION ITEMS**\n\n";
        
        if (!empty($recommendations['general'])) {
            $response .= "**General Recommendations:**\n";
            foreach ($recommendations['general'] as $rec) {
                $priorityIcon = $rec['priority'] === 'High' ? '🔴' : ($rec['priority'] === 'Medium' ? '🟡' : '🟢');
                $response .= "{$priorityIcon} **{$rec['title']}**\n";
                $response .= "   - {$rec['description']}\n";
                $response .= "   - **Action:** {$rec['action']}\n\n";
            }
        }
        
        if (!empty($recommendations['user_specific'])) {
            $response .= "**User-Specific Recommendations:**\n";
            foreach ($recommendations['user_specific'] as $userId => $userRec) {
                $response .= "**👤 {$userRec['username']}**\n";
                foreach ($userRec['recommendations'] as $rec) {
                    $priorityIcon = $rec['priority'] === 'High' ? '🔴' : ($rec['priority'] === 'Medium' ? '🟡' : '🟢');
                    $response .= "{$priorityIcon} {$rec['message']}\n";
                }
                $response .= "\n";
            }
        }
        
        if (!empty($recommendations['process_improvements'])) {
            $response .= "**Process Improvements:**\n";
            foreach ($recommendations['process_improvements'] as $improvement) {
                $response .= "**📋 {$improvement['area']}**\n";
                $response .= "   - Issue: {$improvement['issue']}\n";
                $response .= "   - Metric: {$improvement['metric']}\n";
                $response .= "   - Recommendation: {$improvement['recommendation']}\n\n";
            }
        }
        
        return $response;
    }

    /**
     * Generate comprehensive summary
     */
    private function generate_comprehensive_summary($analysisResults, $topCount)
    {
        $response = "";
        
        // Add all sections
        $response .= $this->generate_performance_summary($analysisResults);
        $response .= $this->generate_status_summary($analysisResults);
        $response .= $this->generate_upcoming_tasks_summary($analysisResults);
        $response .= $this->generate_overdue_tasks_summary($analysisResults);
        $response .= $this->generate_priority_tasks_summary($analysisResults);
        $response .= $this->generate_top_companies_summary($analysisResults, $topCount);
        $response .= $this->generate_top_users_summary($analysisResults, $topCount);
        $response .= $this->generate_recommendations_summary($analysisResults);
        
        return $response;
    }

    /**
     * Prepare detailed frontend data
     */
    private function prepare_detailed_frontend_data($tasks, $topCount, $startDate, $endDate)
    {
        $response = [
            'status' => 'success',
            'message' => 'Future task analysis data prepared successfully',
            'period' => "{$startDate} to {$endDate}",
            'generated_at' => date('Y-m-d H:i:s'),
            'summary' => [],
            'detailed_analysis' => [],
            'top_companies' => [],
            'top_users' => [],
            'table_data_analysis' => [],
            'charts' => [],
            'tables' => []
        ];
        
        if (empty($tasks)) {
            $response['status'] = 'empty';
            $response['message'] = 'No future task data available for analysis';
            return $response;
        }
        
        // Analyze data
        $analysis = $this->analyze_future_task_data_deep($tasks, $topCount);
        
        // Prepare summary
        $response['summary'] = [
            'total_tasks' => $analysis['total_tasks'],
            'total_users' => $analysis['total_users'],
            'total_companies' => $analysis['total_companies'],
            'completion_rate' => $analysis['performance_metrics']['completion_rate'] ?? 0,
            'overdue_tasks' => $analysis['performance_metrics']['overdue_tasks'] ?? 0,
            'upcoming_tasks' => $analysis['performance_metrics']['upcoming_tasks'] ?? 0,
            'priority_tasks' => $analysis['priority_analysis']['priority_task_count'] ?? 0,
            'top_users_count' => count($analysis['top_users'] ?? []),
            'top_companies_count' => count($analysis['top_companies'] ?? []),
            'table_insights_count' => count($analysis['table_data_analysis'] ?? [])
        ];
        
        // Prepare detailed analysis
        $response['detailed_analysis'] = [
            'task_distribution' => $analysis['task_distribution'],
            'user_distribution' => $analysis['user_distribution'],
            'company_distribution' => $analysis['company_distribution'],
            'time_analysis' => $analysis['time_analysis'],
            'status_analysis' => $analysis['status_analysis'],
            'task_type_analysis' => $analysis['task_type_analysis'],
            'priority_analysis' => $analysis['priority_analysis'],
            'performance_metrics' => $analysis['performance_metrics'],
            'upcoming_tasks' => $analysis['upcoming_tasks'],
            'overdue_tasks' => $analysis['overdue_tasks'],
            'recommendations' => $analysis['recommendations'],
            'table_data_analysis' => $analysis['table_data_analysis']
        ];
        
        // Prepare top companies
        $response['top_companies'] = $analysis['top_companies'];
        
        // Prepare top users
        $response['top_users'] = $analysis['top_users'];
        
        // Prepare charts data
        $response['charts'] = $this->prepare_charts_data($analysis);
        
        // Prepare tables data
        $response['tables'] = $this->prepare_tables_data_for_frontend($analysis);
        
        return $response;
    }

    /**
     * Prepare charts data
     */
    private function prepare_charts_data($analysis)
    {
        $charts = [];
        
        // Task status distribution chart
        $statusAnalysis = $analysis['status_analysis'];
        if (!empty($statusAnalysis['status_counts'])) {
            $statusData = [];
            $statusLabels = [];
            
            foreach ($statusAnalysis['status_counts'] as $status => $count) {
                $statusLabels[] = $status;
                $statusData[] = $count;
            }
            
            $charts['task_status_distribution'] = [
                'type' => 'pie',
                'title' => 'Task Status Distribution',
                'labels' => $statusLabels,
                'datasets' => [[
                    'data' => $statusData,
                    'backgroundColor' => $this->generate_chart_colors(count($statusData))
                ]]
            ];
        }
        
        // Task type distribution chart
        $taskTypeAnalysis = $analysis['task_type_analysis'];
        if (!empty($taskTypeAnalysis['type_counts'])) {
            $typeData = [];
            $typeLabels = [];
            
            foreach ($taskTypeAnalysis['type_counts'] as $type => $count) {
                $typeLabels[] = $type;
                $typeData[] = $count;
            }
            
            $charts['task_type_distribution'] = [
                'type' => 'bar',
                'title' => 'Task Type Distribution',
                'labels' => $typeLabels,
                'datasets' => [[
                    'label' => 'Task Count',
                    'data' => $typeData,
                    'backgroundColor' => '#2196F3'
                ]]
            ];
        }
        
        // Time distribution chart
        $timeAnalysis = $analysis['time_analysis'];
        $timeCategories = [
            'immediate_tasks' => 'Today',
            'short_term_tasks' => 'Next 7 Days',
            'medium_term_tasks' => 'Next 30 Days',
            'long_term_tasks' => 'Beyond 30 Days'
        ];
        
        $timeData = [];
        $timeLabels = [];
        
        foreach ($timeCategories as $key => $label) {
            if (isset($timeAnalysis[$key])) {
                $timeLabels[] = $label;
                $timeData[] = $timeAnalysis[$key];
            }
        }
        
        if (!empty($timeData)) {
            $charts['time_distribution'] = [
                'type' => 'doughnut',
                'title' => 'Task Time Distribution',
                'labels' => $timeLabels,
                'datasets' => [[
                    'data' => $timeData,
                    'backgroundColor' => $this->generate_chart_colors(count($timeData))
                ]]
            ];
        }
        
        // User performance chart
        $topUsers = array_slice($analysis['top_users'] ?? [], 0, 10);
        if (!empty($topUsers)) {
            $userNames = [];
            $userScores = [];
            
            foreach ($topUsers as $user) {
                $userNames[] = substr($user['username'], 0, 15) . '...';
                $userScores[] = $user['performance_score'];
            }
            
            $charts['user_performance'] = [
                'type' => 'bar',
                'title' => 'Top User Performance Scores',
                'labels' => $userNames,
                'datasets' => [[
                    'label' => 'Performance Score',
                    'data' => $userScores,
                    'backgroundColor' => '#4CAF50'
                ]]
            ];
        }
        
        // Company engagement chart
        $topCompanies = array_slice($analysis['top_companies'] ?? [], 0, 10);
        if (!empty($topCompanies)) {
            $companyNames = [];
            $companyScores = [];
            
            foreach ($topCompanies as $company) {
                $companyNames[] = substr($company['company_name'], 0, 15) . '...';
                $companyScores[] = $company['engagement_score'];
            }
            
            $charts['company_engagement'] = [
                'type' => 'bar',
                'title' => 'Top Company Engagement Scores',
                'labels' => $companyNames,
                'datasets' => [[
                    'label' => 'Engagement Score',
                    'data' => $companyScores,
                    'backgroundColor' => '#FF5722'
                ]]
            ];
        }
        
        return $charts;
    }

    /**
     * Generate chart colors
     */
    private function generate_chart_colors($count)
    {
        $colors = [
            '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF',
            '#FF9F40', '#FF6384', '#C9CBCF', '#4BC0C0', '#FF6384',
            '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'
        ];
        
        // If we need more colors than available, cycle through them
        if ($count > count($colors)) {
            $additionalColors = [];
            while (count($additionalColors) < $count - count($colors)) {
                $additionalColors = array_merge($additionalColors, $colors);
            }
            $colors = array_merge($colors, $additionalColors);
        }
        
        return array_slice($colors, 0, $count);
    }

    /**
     * Prepare tables data for frontend
     */
    private function prepare_tables_data_for_frontend($analysis)
    {
        $tables = [];
        
        // Task Schedule Table
        if (!empty($analysis['table_data_analysis']['task_schedule_table'])) {
            $tables['task_schedule'] = [
                'title' => 'Task Schedule',
                'headers' => ['Date', 'Time', 'Company', 'User', 'Task Type', 'Status', 'Review Type'],
                'rows' => [],
                'key' => 'task_schedule'
            ];
            
            foreach ($analysis['table_data_analysis']['task_schedule_table'] as $task) {
                $tables['task_schedule']['rows'][] = [
                    'date' => $task['date'],
                    'time' => $task['time'],
                    'company' => $task['company'],
                    'user' => $task['user'],
                    'task_type' => $task['task_type'],
                    'status' => $task['status'],
                    'review_type' => $task['review_type']
                ];
            }
        }
        
        // User Performance Table
        if (!empty($analysis['table_data_analysis']['user_performance_table'])) {
            $tables['user_performance'] = [
                'title' => 'User Performance',
                'headers' => ['Username', 'Total Tasks', 'Completed', 'Pending', 'Completion Rate', 'Emails', 'Calls', 'Companies', 'Performance'],
                'rows' => [],
                'key' => 'user_performance'
            ];
            
            foreach ($analysis['table_data_analysis']['user_performance_table'] as $user) {
                $tables['user_performance']['rows'][] = [
                    'username' => $user['username'],
                    'total_tasks' => $user['total_tasks'],
                    'completed_tasks' => $user['completed_tasks'],
                    'pending_tasks' => $user['pending_tasks'],
                    'completion_rate' => $user['completion_rate'],
                    'emails' => $user['emails'],
                    'calls' => $user['calls'],
                    'unique_companies' => $user['unique_companies'],
                    'performance_rating' => $user['performance_rating']
                ];
            }
        }
        
        // Company Engagement Table
        if (!empty($analysis['table_data_analysis']['company_engagement_table'])) {
            $tables['company_engagement'] = [
                'title' => 'Company Engagement',
                'headers' => ['Company', 'Total Tasks', 'Assigned Users', 'Task Types', 'First Task', 'Last Task', 'Engagement Score', 'Engagement Level'],
                'rows' => [],
                'key' => 'company_engagement'
            ];
            
            foreach ($analysis['table_data_analysis']['company_engagement_table'] as $company) {
                $tables['company_engagement']['rows'][] = [
                    'company_name' => $company['company_name'],
                    'total_tasks' => $company['total_tasks'],
                    'unique_users' => $company['unique_users'],
                    'unique_task_types' => $company['unique_task_types'],
                    'first_task_date' => $company['first_task_date'],
                    'last_task_date' => $company['last_task_date'],
                    'engagement_score' => $company['engagement_score'],
                    'engagement_level' => $company['engagement_level']
                ];
            }
        }
        
        // Upcoming Tasks Table
        if (!empty($analysis['upcoming_tasks'])) {
            $tables['upcoming_tasks'] = [
                'title' => 'Upcoming Tasks (Next 7 Days)',
                'headers' => ['Date', 'Company', 'User', 'Task', 'Status', 'Days Until'],
                'rows' => [],
                'key' => 'upcoming_tasks'
            ];
            
            foreach ($analysis['upcoming_tasks'] as $task) {
                $tables['upcoming_tasks']['rows'][] = [
                    'date' => date('Y-m-d', strtotime($task['scheduled_date'])),
                    'company' => $task['company'],
                    'user' => $task['user'],
                    'task' => $task['task'],
                    'status' => $task['status'],
                    'days_until' => $task['days_until']
                ];
            }
        }
        
        // Overdue Tasks Table
        if (!empty($analysis['overdue_tasks'])) {
            $tables['overdue_tasks'] = [
                'title' => 'Overdue Tasks',
                'headers' => ['Company', 'User', 'Task', 'Original Date', 'Overdue Days', 'Priority'],
                'rows' => [],
                'key' => 'overdue_tasks'
            ];
            
            foreach ($analysis['overdue_tasks'] as $task) {
                $tables['overdue_tasks']['rows'][] = [
                    'company' => $task['company'],
                    'user' => $task['user'],
                    'task' => $task['task'],
                    'original_date' => date('Y-m-d', strtotime($task['scheduled_date'])),
                    'overdue_days' => $task['overdue_days'],
                    'priority' => $task['priority']
                ];
            }
        }
        
        return $tables;
    }
}