<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class LineManagerCallingonRPLeads_model extends CI_Model {

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
     * Main method to process meeting analysis
     */
    public function process_LineManagerCallingonRPLeads_analysis($message, $analysisType, $sdate, $edate) 
    {
        // Get meeting details data
        $raw = $this->Report_model->LineManagerCallingonRPLeadsLists(
            $this->uid,
            $sdate,
            $edate,
            1,
            $this->uid
        );

        $totalTasksUserByDatas = $raw['totalTasksUserByDatas'];

        // Check if data exists
        if (empty($totalTasksUserByDatas) || !is_array($totalTasksUserByDatas)) {
            return [
                'content' => "No task data found for the period {$sdate} to {$edate}.",
                'data' => [
                    'status' => 'empty',
                    'message' => 'No task data available for analysis',
                    'period' => "{$sdate} to {$edate}"
                ]
            ];
        }

        // Extract count from message (top 10, top 20, top 30, top 50)
        $topCount = $this->extract_top_count_from_message($message);
        
        // Generate comprehensive analysis
        $analysis = $this->generate_detailed_company_analysis(
            $totalTasksUserByDatas, 
            $message, 
            $topCount,
            $sdate, 
            $edate
        );

        return [
            'content' => $analysis,
            'data' => $totalTasksUserByDatas
        ];
    }





public function arrayObjectToText($data)
{
    $text = '';

    foreach ($data as $index => $obj) {
        $text .= "Task " . ($index + 1) . ":\n";

        foreach ($obj as $key => $value) {

            

            if (is_string($value) && trim($value) === '') {
                $value = 'N/A';
            }
            $text .= ucfirst(str_replace('_', ' ', $key)) . ": {$value}\n";
        }

        $text .= str_repeat('-', 40) . "\n";
    }

    return $text;
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
     * Generate detailed company analysis
     */
    private function generate_detailed_company_analysis($taskData, $message, $topCount, $startDate, $endDate)
    {
        // Process and analyze the data
        $analysisResults = $this->analyze_task_data_deep($taskData, $topCount);
        
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
     * Deep analysis of task data
     */
    private function analyze_task_data_deep($taskData, $topCount)
    {
        if (empty($taskData) || !is_array($taskData)) {
            return [
                'status' => 'empty',
                'message' => 'No task data to analyze'
            ];
        }

        $analysis = [
            'total_tasks' => count($taskData),
            'users_summary' => [],
            'companies_analysis' => [],
            'performance_metrics' => [],
            'sentiment_analysis' => [],
            'priority_flags' => [],
            'recommendations' => [],
            'top_companies' => []
        ];

        // Group by user
        $usersData = [];
        foreach ($taskData as $task) {
            $userId = $task->task_user_id ?? 0;
            $userName = $task->task_username ?? 'Unknown User';
            
            if (!isset($usersData[$userId])) {
                $usersData[$userId] = [
                    'user_id' => $userId,
                    'username' => $userName,
                    'total_tasks' => 0,
                    'completed_tasks' => 0,
                    'pending_tasks' => 0,
                    'positive_status' => 0,
                    'tentative_status' => 0,
                    'negative_status' => 0,
                    'companies' => [],
                    'tasks' => []
                ];
            }
            
            $usersData[$userId]['total_tasks']++;
            
            // Check task status
            $actontaken = strtolower($task->actontaken ?? '');
            if ($actontaken === 'yes') {
                $usersData[$userId]['completed_tasks']++;
            } else {
                $usersData[$userId]['pending_tasks']++;
            }
            
            // Check sentiment/status
            $currentStatus = strtolower($task->current_status ?? '');
            if (in_array($currentStatus, ['positive', 'very positive', 'excellent', 'good'])) {
                $usersData[$userId]['positive_status']++;
            } elseif (in_array($currentStatus, ['tentative', 'will do later'])) {
                $usersData[$userId]['tentative_status']++;
            } elseif (in_array($currentStatus, ['negative', 'not interested', 'rejected'])) {
                $usersData[$userId]['negative_status']++;
            }
            
            // Add company to user's list
            $companyId = $task->cid ?? 0;
            $companyName = $task->compname ?? 'Unknown Company';
            
            if (!isset($usersData[$userId]['companies'][$companyId])) {
                $usersData[$userId]['companies'][$companyId] = [
                    'company_name' => trim($companyName),
                    'task_count' => 0,
                    'last_task_date' => '',
                    'latest_status' => ''
                ];
            }
            
            $usersData[$userId]['companies'][$companyId]['task_count']++;
            
            // Update with latest task info
            $taskDate = $task->appointmentdatetime ?? '';
            if (empty($usersData[$userId]['companies'][$companyId]['last_task_date']) || 
                strtotime($taskDate) > strtotime($usersData[$userId]['companies'][$companyId]['last_task_date'])) {
                $usersData[$userId]['companies'][$companyId]['last_task_date'] = $taskDate;
                $usersData[$userId]['companies'][$companyId]['latest_status'] = $task->current_status ?? '';
            }
            
            // Store task details
            $usersData[$userId]['tasks'][] = [
                'company_name' => trim($companyName),
                'company_id' => $companyId,
                'task_id' => $task->task_id ?? 0,
                'task_type' => $task->task_name ?? 'Call',
                'task_date' => $taskDate,
                'current_status' => $task->current_status ?? '',
                'task_time_status' => $task->task_time_status ?? '',
                'purpose_achieved' => $task->purpose_achieved ?? '',
                'action_taken' => $task->actontaken ?? '',
                'remarks' => $task->remarks ?? '',
                'mom_remarks' => $task->mom_remarks ?? '',
                'potential' => $task->potential ?? '',
                'topspender' => $task->topspender ?? '',
                'keycompany' => $task->keycompany ?? '',
                'priorityc' => $task->priorityc ?? '',
                'pkclient' => $task->pkclient ?? '',
                'upsell_client' => $task->upsell_client ?? '',
                'focus_funnel' => $task->focus_funnel ?? '',
                'partner_name' => $task->partner_name ?? '',
                'main_bd_name' => $task->main_bd_name ?? '',
                'main_bd_manager_name' => $task->main_bd_manager_name ?? '',
                'selectby' => $task->selectby ?? '',
                'fwd_date' => $task->fwd_date ?? '',
                'updated_at' => $task->updated_at ?? ''
            ];
        }
        
        // Convert to indexed array
        $analysis['users_summary'] = array_values($usersData);
        
        // Analyze individual companies
        $companiesAnalysis = $this->analyze_companies_individually($taskData);
        $analysis['companies_analysis'] = $companiesAnalysis;
        
        // Calculate performance metrics
        $analysis['performance_metrics'] = $this->calculate_performance_metrics($taskData);
        
        // Sentiment analysis
        $analysis['sentiment_analysis'] = $this->analyze_sentiment($taskData);
        
        // Priority flags
        $analysis['priority_flags'] = $this->identify_priority_flags($taskData);
        
        // Recommendations
        $analysis['recommendations'] = $this->generate_recommendations($taskData, $usersData);
        
        // Select top companies
        $analysis['top_companies'] = $this->select_top_companies($taskData, $topCount);
        
        return $analysis;
    }

    /**
     * Analyze companies individually
     */
    private function analyze_companies_individually($taskData)
    {
        $companies = [];
        
        foreach ($taskData as $task) {
            $companyId = $task->cid ?? 0;
            $companyName = trim($task->compname ?? 'Unknown Company');
            
            if (!isset($companies[$companyId])) {
                $companies[$companyId] = [
                    'company_id' => $companyId,
                    'company_name' => $companyName,
                    'total_tasks' => 0,
                    'tasks' => [],
                    'users_engaged' => [],
                    'first_task_date' => null,
                    'last_task_date' => null,
                    'status_summary' => [],
                    'positive_interactions' => 0,
                    'tentative_interactions' => 0,
                    'negative_interactions' => 0,
                    'priority_flags' => [],
                    'task_types' => [],
                    'mom_analysis' => []
                ];
            }
            
            $companies[$companyId]['total_tasks']++;
            
            // Track task
            $taskDetails = [
                'task_id' => $task->task_id ?? 0,
                'task_date' => $task->appointmentdatetime ?? '',
                'task_type' => $task->task_name ?? '',
                'current_status' => $task->current_status ?? '',
                'task_time_status' => $task->task_time_status ?? '',
                'purpose_achieved' => $task->purpose_achieved ?? '',
                'action_taken' => $task->actontaken ?? '',
                'remarks' => $task->remarks ?? '',
                'mom_remarks' => $task->mom_remarks ?? '',
                'user_id' => $task->task_user_id ?? 0,
                'username' => $task->task_username ?? ''
            ];
            
            $companies[$companyId]['tasks'][] = $taskDetails;
            
            // Track users
            $userId = $task->task_user_id ?? 0;
            $userName = $task->task_username ?? '';
            $userKey = $userId . '_' . $userName;
            if (!in_array($userKey, $companies[$companyId]['users_engaged'])) {
                $companies[$companyId]['users_engaged'][] = $userKey;
            }
            
            // Update dates
            $taskDate = $task->appointmentdatetime ?? '';
            if (empty($companies[$companyId]['first_task_date']) || 
                strtotime($taskDate) < strtotime($companies[$companyId]['first_task_date'])) {
                $companies[$companyId]['first_task_date'] = $taskDate;
            }
            
            if (empty($companies[$companyId]['last_task_date']) || 
                strtotime($taskDate) > strtotime($companies[$companyId]['last_task_date'])) {
                $companies[$companyId]['last_task_date'] = $taskDate;
            }
            
            // Update status summary
            $status = $task->current_status ?? '';
            if (!isset($companies[$companyId]['status_summary'][$status])) {
                $companies[$companyId]['status_summary'][$status] = 0;
            }
            $companies[$companyId]['status_summary'][$status]++;
            
            // Task type distribution
            $taskType = $task->task_name ?? '';
            if (!isset($companies[$companyId]['task_types'][$taskType])) {
                $companies[$companyId]['task_types'][$taskType] = 0;
            }
            $companies[$companyId]['task_types'][$taskType]++;
            
            // Sentiment analysis
            $currentStatus = strtolower($task->current_status ?? '');
            if (in_array($currentStatus, ['positive', 'very positive', 'excellent', 'good'])) {
                $companies[$companyId]['positive_interactions']++;
            } elseif (in_array($currentStatus, ['tentative', 'will do later'])) {
                $companies[$companyId]['tentative_interactions']++;
            } elseif (in_array($currentStatus, ['negative', 'not interested', 'rejected'])) {
                $companies[$companyId]['negative_interactions']++;
            }
            
            // Priority flags
            $priorityFlags = [];
            if ($task->potential === 'yes') $priorityFlags[] = 'High Potential';
            if ($task->topspender === 'yes') $priorityFlags[] = 'Top Spender';
            if ($task->keycompany === 'yes') $priorityFlags[] = 'Key Company';
            if ($task->priorityc === 'yes') $priorityFlags[] = 'Priority Client';
            if ($task->pkclient === 'yes') $priorityFlags[] = 'PK Client';
            if ($task->focus_funnel === 'yes') $priorityFlags[] = 'Focus Funnel';
            
            $companies[$companyId]['priority_flags'] = array_unique(array_merge(
                $companies[$companyId]['priority_flags'], 
                $priorityFlags
            ));
            
            // MOM analysis
            if (!empty($task->mom_remarks)) {
                $companies[$companyId]['mom_analysis'][] = [
                    'task_id' => $task->task_id ?? 0,
                    'date' => $task->appointmentdatetime ?? '',
                    'remarks' => $task->mom_remarks,
                    'user' => $task->task_username ?? ''
                ];
            }
        }
        
        // Convert to indexed array
        return array_values($companies);
    }

    /**
     * Calculate performance metrics
     */
    private function calculate_performance_metrics($taskData)
    {
        $metrics = [
            'total_tasks' => count($taskData),
            'completed_tasks' => 0,
            'pending_tasks' => 0,
            'purpose_achieved' => 0,
            'action_taken' => 0,
            'positive_status_count' => 0,
            'tentative_status_count' => 0,
            'negative_status_count' => 0,
            'tasks_by_type' => [],
            'tasks_by_status' => [],
            'tasks_by_partner' => []
        ];
        
        foreach ($taskData as $task) {
            // Completion status
            $actontaken = strtolower($task->actontaken ?? '');
            if ($actontaken === 'yes') {
                $metrics['completed_tasks']++;
            } else {
                $metrics['pending_tasks']++;
            }
            
            // Purpose achieved
            if (strtolower($task->purpose_achieved ?? '') === 'yes') {
                $metrics['purpose_achieved']++;
            }
            
            // Action taken
            if (strtolower($task->actontaken ?? '') === 'yes') {
                $metrics['action_taken']++;
            }
            
            // Sentiment/Status
            $currentStatus = strtolower($task->current_status ?? '');
            if (in_array($currentStatus, ['positive', 'very positive', 'excellent', 'good'])) {
                $metrics['positive_status_count']++;
            } elseif (in_array($currentStatus, ['tentative', 'will do later'])) {
                $metrics['tentative_status_count']++;
            } elseif (in_array($currentStatus, ['negative', 'not interested', 'rejected'])) {
                $metrics['negative_status_count']++;
            }
            
            // Task type distribution
            $type = $task->task_name ?? 'Unknown';
            if (!isset($metrics['tasks_by_type'][$type])) {
                $metrics['tasks_by_type'][$type] = 0;
            }
            $metrics['tasks_by_type'][$type]++;
            
            // Task status distribution
            $taskStatus = $task->current_status ?? 'Unknown';
            if (!isset($metrics['tasks_by_status'][$taskStatus])) {
                $metrics['tasks_by_status'][$taskStatus] = 0;
            }
            $metrics['tasks_by_status'][$taskStatus]++;
            
            // Partner distribution
            $partner = $task->partner_name ?? 'Unknown';
            if (!isset($metrics['tasks_by_partner'][$partner])) {
                $metrics['tasks_by_partner'][$partner] = 0;
            }
            $metrics['tasks_by_partner'][$partner]++;
        }
        
        // Calculate percentages
        $metrics['completion_rate'] = $metrics['total_tasks'] > 0 ? 
            round(($metrics['completed_tasks'] / $metrics['total_tasks']) * 100, 2) : 0;
        
        $metrics['purpose_achieved_rate'] = $metrics['total_tasks'] > 0 ? 
            round(($metrics['purpose_achieved'] / $metrics['total_tasks']) * 100, 2) : 0;
        
        $metrics['action_taken_rate'] = $metrics['total_tasks'] > 0 ? 
            round(($metrics['action_taken'] / $metrics['total_tasks']) * 100, 2) : 0;
        
        $metrics['positive_sentiment_rate'] = $metrics['total_tasks'] > 0 ? 
            round(($metrics['positive_status_count'] / $metrics['total_tasks']) * 100, 2) : 0;
        
        $metrics['tentative_sentiment_rate'] = $metrics['total_tasks'] > 0 ? 
            round(($metrics['tentative_status_count'] / $metrics['total_tasks']) * 100, 2) : 0;
        
        $metrics['negative_sentiment_rate'] = $metrics['total_tasks'] > 0 ? 
            round(($metrics['negative_status_count'] / $metrics['total_tasks']) * 100, 2) : 0;
        
        return $metrics;
    }

    /**
     * Analyze sentiment
     */
    private function analyze_sentiment($taskData)
    {
        $sentiment = [
            'overall_sentiment' => 'Neutral',
            'sentiment_distribution' => [],
            'positive_factors' => [],
            'negative_factors' => [],
            'mom_analysis' => [],
            'remarks_analysis' => []
        ];
        
        $sentimentScores = [];
        $allMomRemarks = [];
        $allRemarks = [];
        
        foreach ($taskData as $task) {
            $currentStatus = strtolower($task->current_status ?? '');
            $status = $task->current_status ?? 'Unknown';
            
            // Count sentiment distribution
            if (!isset($sentiment['sentiment_distribution'][$status])) {
                $sentiment['sentiment_distribution'][$status] = 0;
            }
            $sentiment['sentiment_distribution'][$status]++;
            
            // Score sentiment
            $score = 0;
            if (in_array($currentStatus, ['very positive'])) $score = 5;
            elseif (in_array($currentStatus, ['positive'])) $score = 4;
            elseif (in_array($currentStatus, ['excellent'])) $score = 5;
            elseif (in_array($currentStatus, ['good'])) $score = 3;
            elseif (in_array($currentStatus, ['tentative'])) $score = 2;
            elseif (in_array($currentStatus, ['will do later'])) $score = 1;
            elseif (in_array($currentStatus, ['neutral'])) $score = 1;
            elseif (in_array($currentStatus, ['negative'])) $score = -2;
            elseif (in_array($currentStatus, ['not interested'])) $score = -3;
            elseif (in_array($currentStatus, ['rejected'])) $score = -5;
            
            $sentimentScores[] = $score;
            
            // Collect MOM remarks
            $momRemarks = trim($task->mom_remarks ?? '');
            if (!empty($momRemarks)) {
                $allMomRemarks[] = [
                    'company' => $task->compname ?? '',
                    'remarks' => $momRemarks,
                    'status' => $status,
                    'score' => $score
                ];
            }
            
            // Collect remarks
            $remarks = trim($task->remarks ?? '');
            if (!empty($remarks)) {
                $allRemarks[] = [
                    'company' => $task->compname ?? '',
                    'remarks' => $remarks,
                    'status' => $status,
                    'task_type' => $task->task_name ?? ''
                ];
            }
            
            // Identify positive factors
            if ($score >= 3) {
                $positiveFactors = [];
                if ($task->potential === 'yes') $positiveFactors[] = 'High Potential';
                if ($task->topspender === 'yes') $positiveFactors[] = 'Top Spender';
                if ($task->keycompany === 'yes') $positiveFactors[] = 'Key Company';
                if ($task->priorityc === 'yes') $positiveFactors[] = 'Priority Client';
                if ($task->pkclient === 'yes') $positiveFactors[] = 'PK Client';
                if ($task->actontaken === 'yes') $positiveFactors[] = 'Action Taken';
                if ($task->purpose_achieved === 'yes') $positiveFactors[] = 'Purpose Achieved';
                
                $sentiment['positive_factors'] = array_merge($sentiment['positive_factors'], $positiveFactors);
            }
            
            // Identify negative factors
            if ($score <= 0) {
                $negativeFactors = [];
                if (strtolower($task->actontaken ?? '') === 'no') $negativeFactors[] = 'No Action Taken';
                if (strtolower($task->purpose_achieved ?? '') === 'no') $negativeFactors[] = 'Purpose Not Achieved';
                
                $sentiment['negative_factors'] = array_merge($sentiment['negative_factors'], $negativeFactors);
            }
        }
        
        // Calculate overall sentiment
        if (!empty($sentimentScores)) {
            $averageScore = array_sum($sentimentScores) / count($sentimentScores);
            if ($averageScore >= 3) {
                $sentiment['overall_sentiment'] = 'Positive';
            } elseif ($averageScore >= 1) {
                $sentiment['overall_sentiment'] = 'Neutral';
            } else {
                $sentiment['overall_sentiment'] = 'Negative';
            }
        }
        
        // Analyze MOM remarks
        $sentiment['mom_analysis'] = $this->analyze_mom_remarks($allMomRemarks);
        
        // Analyze remarks
        $sentiment['remarks_analysis'] = $this->analyze_task_remarks($allRemarks);
        
        // Remove duplicates
        $sentiment['positive_factors'] = array_unique($sentiment['positive_factors']);
        $sentiment['negative_factors'] = array_unique($sentiment['negative_factors']);
        
        return $sentiment;
    }

    /**
     * Analyze MOM remarks
     */
    private function analyze_mom_remarks($momRemarks)
    {
        if (empty($momRemarks)) {
            return [
                'total_mom_remarks' => 0,
                'coverage_rate' => 0,
                'key_themes' => [],
                'sentiment_in_mom' => []
            ];
        }
        
        $analysis = [
            'total_mom_remarks' => count($momRemarks),
            'coverage_rate' => 0,
            'key_themes' => [],
            'sentiment_in_mom' => [],
            'detailed_remarks' => []
        ];
        
        $positiveKeywords = [
            'positive', 'very positive', 'excellent', 'good', 'great', 'progress',
            'interested', 'agreed', 'approved', 'confirmed', 'scheduled', 'follow up',
            'next meeting', 'deal', 'contract', 'purchase', 'buy', 'order',
            'successful', 'productive', 'valuable', 'promising', 'opportunity',
            'collaboration', 'partnership', 'expansion', 'growth'
        ];
        
        $negativeKeywords = [
            'negative', 'not interested', 'rejected', 'declined', 'cancelled',
            'postponed', 'delayed', 'concern', 'issue', 'problem', 'challenge',
            'budget', 'cost', 'expensive', 'too high', 'competitor', 'alternative',
            'waiting', 'pending', 'no response', 'busy', 'not available',
            'unclear', 'confused', 'doubt', 'hesitant', 'uncertain'
        ];
        
        $themeKeywords = [
            'price' => 'Price Discussion',
            'discount' => 'Discount Discussion',
            'proposal' => 'Proposal Sent',
            'demo' => 'Product Demo',
            'trial' => 'Trial Requested',
            'contract' => 'Contract Discussion',
            'payment' => 'Payment Terms',
            'delivery' => 'Delivery Timeline',
            'support' => 'Support Discussion',
            'technical' => 'Technical Requirements'
        ];
        
        $positiveCount = 0;
        $negativeCount = 0;
        $themes = [];
        
        foreach ($momRemarks as $remark) {
            $text = strtolower($remark['remarks']);
            $analysis['detailed_remarks'][] = [
                'company' => $remark['company'],
                'remarks' => $remark['remarks'],
                'sentiment' => $remark['score'] >= 3 ? 'Positive' : ($remark['score'] <= 0 ? 'Negative' : 'Neutral')
            ];
            
            // Check sentiment
            $isPositive = false;
            $isNegative = false;
            
            foreach ($positiveKeywords as $keyword) {
                if (strpos($text, $keyword) !== false) {
                    $isPositive = true;
                    break;
                }
            }
            
            foreach ($negativeKeywords as $keyword) {
                if (strpos($text, $keyword) !== false) {
                    $isNegative = true;
                    break;
                }
            }
            
            if ($isPositive && !$isNegative) {
                $positiveCount++;
            } elseif ($isNegative && !$isPositive) {
                $negativeCount++;
            }
            
            // Identify themes
            foreach ($themeKeywords as $keyword => $theme) {
                if (strpos($text, $keyword) !== false) {
                    if (!isset($themes[$theme])) {
                        $themes[$theme] = 0;
                    }
                    $themes[$theme]++;
                }
            }
        }
        
        $analysis['sentiment_in_mom'] = [
            'positive' => $positiveCount,
            'negative' => $negativeCount,
            'neutral' => count($momRemarks) - $positiveCount - $negativeCount
        ];
        
        arsort($themes);
        $analysis['key_themes'] = array_slice($themes, 0, 5, true);
        
        return $analysis;
    }

    /**
     * Analyze task remarks
     */
    private function analyze_task_remarks($remarks)
    {
        if (empty($remarks)) {
            return [
                'total_remarks' => 0,
                'remarks_by_type' => [],
                'common_patterns' => []
            ];
        }
        
        $analysis = [
            'total_remarks' => count($remarks),
            'remarks_by_type' => [],
            'common_patterns' => [],
            'actionable_remarks' => []
        ];
        
        $patterns = [
            'Task Complete' => 0,
            'Follow Up' => 0,
            'Reschedule' => 0,
            'Technical Issue' => 0,
            'Decision Pending' => 0
        ];
        
        foreach ($remarks as $remark) {
            $text = strtolower($remark['remarks']);
            $taskType = strtolower($remark['task_type'] ?? '');
            
            // Categorize by type
            if (!isset($analysis['remarks_by_type'][$taskType])) {
                $analysis['remarks_by_type'][$taskType] = 0;
            }
            $analysis['remarks_by_type'][$taskType]++;
            
            // Identify patterns
            if (strpos($text, 'complete') !== false) $patterns['Task Complete']++;
            if (strpos($text, 'follow up') !== false) $patterns['Follow Up']++;
            if (strpos($text, 'reschedule') !== false) $patterns['Reschedule']++;
            if (strpos($text, 'technical') !== false) $patterns['Technical Issue']++;
            if (strpos($text, 'decision pending') !== false) $patterns['Decision Pending']++;
            
            // Identify actionable remarks
            if (strpos($text, 'next call') !== false || 
                strpos($text, 'follow up') !== false ||
                strpos($text, 'call back') !== false ||
                strpos($text, 'send proposal') !== false) {
                $analysis['actionable_remarks'][] = [
                    'company' => $remark['company'],
                    'remark' => $remark['remarks'],
                    'action' => $this->extract_action_from_remark($remark['remarks'])
                ];
            }
        }
        
        $analysis['common_patterns'] = array_filter($patterns, function($count) {
            return $count > 0;
        });
        
        return $analysis;
    }

    /**
     * Extract action from remark
     */
    private function extract_action_from_remark($remark)
    {
        $remark = strtolower($remark);
        
        if (strpos($remark, 'next call') !== false) return 'Schedule Next Call';
        if (strpos($remark, 'follow up') !== false) return 'Follow Up Required';
        if (strpos($remark, 'call back') !== false) return 'Call Back Needed';
        if (strpos($remark, 'send proposal') !== false) return 'Send Proposal';
        if (strpos($remark, 'send quote') !== false) return 'Send Quote';
        if (strpos($remark, 'demo') !== false) return 'Arrange Demo';
        
        return 'General Follow Up';
    }

    /**
     * Identify priority flags
     */
    private function identify_priority_flags($taskData)
    {
        $flags = [
            'high_potential_clients' => [],
            'top_spenders' => [],
            'key_companies' => [],
            'priority_clients' => [],
            'pk_clients' => [],
            'focus_funnel_clients' => [],
            'no_action_taken' => [],
            'purpose_not_achieved' => [],
            'pending_tasks' => [],
            'negative_sentiment' => []
        ];
        
        foreach ($taskData as $task) {
            $companyName = trim($task->compname ?? 'Unknown Company');
            $companyId = $task->cid ?? 0;
            $userId = $task->task_user_id ?? 0;
            $userName = $task->task_username ?? '';
            $taskId = $task->task_id ?? 0;
            
            $companyInfo = [
                'company_id' => $companyId,
                'company_name' => $companyName,
                'user_id' => $userId,
                'username' => $userName,
                'task_id' => $taskId,
                'task_date' => $task->appointmentdatetime ?? '',
                'current_status' => $task->current_status ?? '',
                'task_time_status' => $task->task_time_status ?? ''
            ];
            
            // High potential clients
            if ($task->potential === 'yes') {
                $flags['high_potential_clients'][] = $companyInfo;
            }
            
            // Top spenders
            if ($task->topspender === 'yes') {
                $flags['top_spenders'][] = $companyInfo;
            }
            
            // Key companies
            if ($task->keycompany === 'yes') {
                $flags['key_companies'][] = $companyInfo;
            }
            
            // Priority clients
            if ($task->priorityc === 'yes') {
                $flags['priority_clients'][] = $companyInfo;
            }
            
            // PK clients
            if ($task->pkclient === 'yes') {
                $flags['pk_clients'][] = $companyInfo;
            }
            
            // Focus funnel clients
            if ($task->focus_funnel === 'yes') {
                $flags['focus_funnel_clients'][] = $companyInfo;
            }
            
            // No action taken
            if (strtolower($task->actontaken ?? '') === 'no') {
                $flags['no_action_taken'][] = $companyInfo;
            }
            
            // Purpose not achieved
            if (strtolower($task->purpose_achieved ?? '') === 'no') {
                $flags['purpose_not_achieved'][] = $companyInfo;
            }
            
            // Pending tasks
            $actontaken = strtolower($task->actontaken ?? '');
            if ($actontaken !== 'yes') {
                $flags['pending_tasks'][] = $companyInfo;
            }
            
            // Negative sentiment
            $currentStatus = strtolower($task->current_status ?? '');
            if (in_array($currentStatus, ['negative', 'not interested', 'rejected'])) {
                $flags['negative_sentiment'][] = $companyInfo;
            }
        }
        
        // Count flags
        foreach ($flags as $key => &$value) {
            if (is_array($value)) {
                $flags[$key . '_count'] = count($value);
            }
        }
        
        return $flags;
    }

    /**
     * Generate recommendations
     */
    private function generate_recommendations($taskData, $usersData)
    {
        $recommendations = [
            'general' => [],
            'user_specific' => [],
            'company_specific' => [],
            'process_improvements' => []
        ];
        
        // Calculate metrics for recommendations
        $metrics = $this->calculate_performance_metrics($taskData);
        
        // General recommendations
        if ($metrics['completion_rate'] < 70) {
            $recommendations['general'][] = [
                'priority' => 'High',
                'title' => 'Improve Task Completion Rate',
                'description' => "Current task completion rate is {$metrics['completion_rate']}%. Focus on completing pending tasks and taking action.",
                'action' => 'Follow up on pending tasks and ensure action is taken.'
            ];
        }
        
        if ($metrics['purpose_achieved_rate'] < 60) {
            $recommendations['general'][] = [
                'priority' => 'Medium',
                'title' => 'Increase Purpose Achievement',
                'description' => "Only {$metrics['purpose_achieved_rate']}% of tasks achieved their purpose. Better task planning and execution needed.",
                'action' => 'Review task objectives before execution and ensure clear purpose.'
            ];
        }
        
        if ($metrics['positive_sentiment_rate'] < 40) {
            $recommendations['general'][] = [
                'priority' => 'High',
                'title' => 'Improve Positive Outcomes',
                'description' => "Only {$metrics['positive_sentiment_rate']}% of tasks have positive outcomes. Focus on better preparation and execution.",
                'action' => 'Train team on effective communication and value delivery.'
            ];
        }
        
        // User-specific recommendations
        foreach ($usersData as $userId => $userData) {
            $userRecs = [];
            
            $completionRate = $userData['total_tasks'] > 0 ? 
                round(($userData['completed_tasks'] / $userData['total_tasks']) * 100, 2) : 0;
            
            $positiveRate = $userData['total_tasks'] > 0 ? 
                round(($userData['positive_status'] / $userData['total_tasks']) * 100, 2) : 0;
            
            if ($completionRate < 60) {
                $userRecs[] = "Improve task completion rate (currently {$completionRate}%)";
            }
            
            if ($positiveRate < 30) {
                $userRecs[] = "Increase positive task outcomes (currently {$positiveRate}%)";
            }
            
            if ($userData['negative_status'] > $userData['positive_status']) {
                $userRecs[] = "Focus on improving task outcomes - more negative than positive outcomes";
            }
            
            if (!empty($userRecs)) {
                $recommendations['user_specific'][$userId] = [
                    'username' => $userData['username'],
                    'recommendations' => $userRecs
                ];
            }
        }
        
        // Process improvements
        $momCoverage = count(array_filter($taskData, function($task) {
            return !empty(trim($task->mom_remarks ?? ''));
        }));
        
        $momRate = count($taskData) > 0 ? 
            round(($momCoverage / count($taskData)) * 100, 2) : 0;
        
        if ($momRate < 50) {
            $recommendations['process_improvements'][] = [
                'area' => 'Documentation',
                'issue' => 'Low MOM completion rate',
                'rate' => $momRate . '%',
                'recommendation' => 'Make MOM completion mandatory for all tasks'
            ];
        }
        
        return $recommendations;
    }

    /**
     * Select top companies based on various criteria
     */
    private function select_top_companies($taskData, $topCount)
    {
        if (empty($taskData)) {
            return [];
        }
        
        // Score each company
        $companyScores = [];
        
        foreach ($taskData as $task) {
            $companyId = $task->cid ?? 0;
            $companyName = trim($task->compname ?? 'Unknown Company');
            
            if (!isset($companyScores[$companyId])) {
                $companyScores[$companyId] = [
                    'company_id' => $companyId,
                    'company_name' => $companyName,
                    'score' => 0,
                    'tasks' => [],
                    'reasons' => []
                ];
            }
            
            // Add task to company
            $companyScores[$companyId]['tasks'][] = [
                'task_id' => $task->task_id ?? 0,
                'date' => $task->appointmentdatetime ?? '',
                'status' => $task->current_status ?? '',
                'task_type' => $task->task_name ?? '',
                'potential' => $task->potential ?? '',
                'topspender' => $task->topspender ?? '',
                'keycompany' => $task->keycompany ?? ''
            ];
            
            // Calculate score
            $score = 0;
            $reasons = [];
            
            // Status scoring
            $status = strtolower($task->current_status ?? '');
            if ($status === 'positive') {
                $score += 8;
                $reasons[] = 'Positive Status';
            } elseif ($status === 'very positive') {
                $score += 10;
                $reasons[] = 'Very Positive Status';
            } elseif ($status === 'excellent') {
                $score += 10;
                $reasons[] = 'Excellent Status';
            } elseif ($status === 'good') {
                $score += 6;
                $reasons[] = 'Good Status';
            } elseif ($status === 'tentative') {
                $score += 3;
                $reasons[] = 'Tentative Status';
            }
            
            // Potential client
            if ($task->potential === 'yes') {
                $score += 12;
                $reasons[] = 'High Potential Client';
            }
            
            // Top spender
            if ($task->topspender === 'yes') {
                $score += 15;
                $reasons[] = 'Top Spender';
            }
            
            // Key company
            if ($task->keycompany === 'yes') {
                $score += 12;
                $reasons[] = 'Key Company';
            }
            
            // Priority client
            if ($task->priorityc === 'yes') {
                $score += 10;
                $reasons[] = 'Priority Client';
            }
            
            // PK client
            if ($task->pkclient === 'yes') {
                $score += 8;
                $reasons[] = 'PK Client';
            }
            
            // Focus funnel
            if ($task->focus_funnel === 'yes') {
                $score += 10;
                $reasons[] = 'Focus Funnel Client';
            }
            
            // Purpose achieved
            if (strtolower($task->purpose_achieved ?? '') === 'yes') {
                $score += 8;
                $reasons[] = 'Purpose Achieved';
            }
            
            // Action taken
            if (strtolower($task->actontaken ?? '') === 'yes') {
                $score += 5;
                $reasons[] = 'Action Taken';
            }
            
            // Recent task bonus (within last 7 days)
            $taskDate = $task->appointmentdatetime ?? '';
            if (!empty($taskDate) && strtotime($taskDate) > strtotime('-7 days')) {
                $score += 5;
                $reasons[] = 'Recent Task';
            }
            
            // Add to company score
            $companyScores[$companyId]['score'] += $score;
            $companyScores[$companyId]['reasons'] = array_unique(
                array_merge($companyScores[$companyId]['reasons'], $reasons)
            );
        }
        
        // Sort by score
        usort($companyScores, function($a, $b) {
            return $b['score'] - $a['score'];
        });
        
        // Select top companies
        $topCompanies = array_slice($companyScores, 0, $topCount);
        
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
     * Generate justification for company selection
     */
    private function generate_company_justification($company)
    {
        $justification = "Selected as a top company because: ";
        
        // Get top 3 reasons
        $topReasons = array_slice($company['reasons'], 0, 3);
        
        if (!empty($topReasons)) {
            $justification .= implode(', ', $topReasons);
        } else {
            $justification .= "High overall engagement score of {$company['score']}";
        }
        
        // Add task count
        $taskCount = count($company['tasks'] ?? []);
        $justification .= ". Has {$taskCount} task(s) in the period.";
        
        return $justification;
    }

    /**
     * Generate response based on message
     */
    private function generate_response_based_on_message($message, $analysisResults, $topCount, $startDate, $endDate)
    {
        $lowerMessage = strtolower(trim($message));
        
        $response = "🔍 **TASK ANALYSIS REPORT**\n\n";
        $response .= "**Period:** {$startDate} to {$endDate}\n";
        $response .= "**Total Tasks Analyzed:** {$analysisResults['total_tasks']}\n\n";
        
        // Add summary based on message intent
        if (strpos($lowerMessage, 'top') !== false) {
            $response .= $this->generate_top_companies_summary($analysisResults, $topCount);
        }
        
        if (strpos($lowerMessage, 'performance') !== false || 
            strpos($lowerMessage, 'metrics') !== false) {
            $response .= $this->generate_performance_summary($analysisResults);
        }
        
        if (strpos($lowerMessage, 'sentiment') !== false || 
            strpos($lowerMessage, 'status') !== false) {
            $response .= $this->generate_sentiment_summary($analysisResults);
        }
        
        if (strpos($lowerMessage, 'recommend') !== false || 
            strpos($lowerMessage, 'suggestion') !== false) {
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
        $response = "🏆 **TOP {$topCount} COMPANIES ANALYSIS**\n\n";
        
        if (empty($analysisResults['top_companies'])) {
            $response .= "No companies found for analysis.\n\n";
            return $response;
        }
        
        $response .= "Here are the top {$topCount} companies based on engagement score:\n\n";
        
        foreach ($analysisResults['top_companies'] as $company) {
            $companyName = $company['company_name'];
            // Clean up company name by removing leading numbers/spaces
            $companyName = preg_replace('/^\s*\d+\.?\s*/', '', $companyName);
            $companyName = trim($companyName);
            
            $response .= "**#{$company['rank']}. {$companyName} - CID: {$company['company_id']}**\n";
            $response .= "   📊 Score: {$company['score']}\n";
            $response .= "   📅 Tasks: " . count($company['tasks']) . "\n";
            $response .= "   💡 Why Selected: {$company['justification']}\n\n";
        }
        
        return $response;
    }

    /**
     * Generate performance summary
     */
    private function generate_performance_summary($analysisResults)
    {
        $metrics = $analysisResults['performance_metrics'];
        
        $response = "📈 **PERFORMANCE METRICS SUMMARY**\n\n";
        $response .= "**Task Completion:** {$metrics['completion_rate']}%\n";
        $response .= "   - Completed: {$metrics['completed_tasks']} tasks\n";
        $response .= "   - Pending: {$metrics['pending_tasks']} tasks\n\n";
        
        $response .= "**Purpose Achievement:** {$metrics['purpose_achieved_rate']}%\n";
        $response .= "**Action Taken:** {$metrics['action_taken_rate']}%\n\n";
        
        $response .= "**Sentiment Distribution:**\n";
        $response .= "   - Positive: {$metrics['positive_sentiment_rate']}%\n";
        $response .= "   - Tentative: {$metrics['tentative_sentiment_rate']}%\n";
        $response .= "   - Negative: {$metrics['negative_sentiment_rate']}%\n\n";
        
        $response .= "**Task Type Distribution:**\n";
        foreach ($metrics['tasks_by_type'] as $type => $count) {
            $percentage = round(($count / $metrics['total_tasks']) * 100, 2);
            $response .= "   - {$type}: {$count} ({$percentage}%)\n";
        }
        $response .= "\n";
        
        return $response;
    }

    /**
     * Generate sentiment summary
     */
    private function generate_sentiment_summary($analysisResults)
    {
        $sentiment = $analysisResults['sentiment_analysis'];
        $metrics = $analysisResults['performance_metrics'];
        
        $response = "😊 **SENTIMENT ANALYSIS**\n\n";
        $response .= "**Overall Sentiment:** {$sentiment['overall_sentiment']}\n";
        $response .= "   - Positive: {$metrics['positive_sentiment_rate']}%\n";
        $response .= "   - Tentative: {$metrics['tentative_sentiment_rate']}%\n";
        $response .= "   - Negative: {$metrics['negative_sentiment_rate']}%\n\n";
        
        $response .= "**Status Distribution:**\n";
        foreach ($sentiment['sentiment_distribution'] as $status => $count) {
            $percentage = round(($count / $metrics['total_tasks']) * 100, 2);
            $response .= "   - {$status}: {$count} ({$percentage}%)\n";
        }
        $response .= "\n";
        
        if (!empty($sentiment['positive_factors'])) {
            $response .= "**Positive Factors Identified:**\n";
            foreach ($sentiment['positive_factors'] as $factor) {
                $response .= "   - {$factor}\n";
            }
            $response .= "\n";
        }
        
        if (!empty($sentiment['negative_factors'])) {
            $response .= "**Areas Needing Improvement:**\n";
            foreach ($sentiment['negative_factors'] as $factor) {
                $response .= "   - {$factor}\n";
            }
            $response .= "\n";
        }
        
        // MOM analysis
        $momAnalysis = $sentiment['mom_analysis'];
        if ($momAnalysis['total_mom_remarks'] > 0) {
            $response .= "**MOM Remarks Analysis:**\n";
            $response .= "   - Total MOM Remarks: {$momAnalysis['total_mom_remarks']}\n";
            $response .= "   - Positive in MOM: {$momAnalysis['sentiment_in_mom']['positive']}\n";
            $response .= "   - Negative in MOM: {$momAnalysis['sentiment_in_mom']['negative']}\n";
            
            if (!empty($momAnalysis['key_themes'])) {
                $response .= "   - Key Themes in MOM:\n";
                foreach ($momAnalysis['key_themes'] as $theme => $count) {
                    $response .= "     • {$theme}: {$count}\n";
                }
            }
            $response .= "\n";
        }
        
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
                    $response .= "   - {$rec}\n";
                }
                $response .= "\n";
            }
        }
        
        if (!empty($recommendations['process_improvements'])) {
            $response .= "**Process Improvements:**\n";
            foreach ($recommendations['process_improvements'] as $improvement) {
                $response .= "**📋 {$improvement['area']}**\n";
                $response .= "   - Issue: {$improvement['issue']} ({$improvement['rate']})\n";
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
        $response .= $this->generate_sentiment_summary($analysisResults);
        $response .= $this->generate_top_companies_summary($analysisResults, $topCount);
        $response .= $this->generate_recommendations_summary($analysisResults);
        
        // Add priority flags if any
        $flags = $analysisResults['priority_flags'];
        $hasFlags = false;
        
        foreach ($flags as $key => $flagArray) {
            if (strpos($key, '_count') !== false && $flagArray > 0) {
                $hasFlags = true;
                break;
            }
        }
        
        if ($hasFlags) {
            $response .= "🚨 **PRIORITY ALERTS**\n\n";
            
            $alertTypes = [
                'high_potential_clients_count' => 'High Potential Clients',
                'top_spenders_count' => 'Top Spenders',
                'key_companies_count' => 'Key Companies',
                'priority_clients_count' => 'Priority Clients',
                'pk_clients_count' => 'PK Clients',
                'focus_funnel_clients_count' => 'Focus Funnel Clients',
                'no_action_taken_count' => 'No Action Taken Tasks',
                'negative_sentiment_count' => 'Negative Sentiment Tasks'
            ];
            
            foreach ($alertTypes as $key => $label) {
                if (isset($flags[$key]) && $flags[$key] > 0) {
                    $response .= "   - {$label}: {$flags[$key]}\n";
                }
            }
            $response .= "\n";
        }
        
        return $response;
    }
}