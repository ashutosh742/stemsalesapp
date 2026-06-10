<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PendingMoMWriteAfterRPMeetings_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Menu_model');
        $this->load->model('Report_model');
        $this->load->model('ChatAI_model');
    }

    public function process_PendingMoMWriteAfterRPMeetings_analysis($message, $analysisType, $sdate, $edate) {
        
        // Get pending MoM meetings data
        $pendingForWriteMomMeetingLists = $this->Menu_model->GetPendingForWriteMomMeetingList($this->uid);
        
        // If no data, return empty response
        if (empty($pendingForWriteMomMeetingLists)) {
            return [
                'content' => 'No pending MoM meetings found for the specified criteria.',
                'data' => [
                    'tableData' => null,
                    'summaryData' => [],
                    'chartData' => null,
                    'userPerformanceData' => null,
                    'companyAnalysisData' => null,
                    'status' => 'empty',
                    'message' => 'No pending MoM meetings found'
                ]
            ];
        }

        // Process meeting data into structured format
        $meetingsArray = [];
        $userStats = [];
        $companyStats = [];
        
        foreach ($pendingForWriteMomMeetingLists as $meeting) {
            // Convert to array if it's an object
            if (is_object($meeting)) {
                $meeting = (array)$meeting;
            }
            
            // Calculate meeting duration in minutes
            $startTime = strtotime($meeting['startm'] ?? '');
            $closeTime = strtotime($meeting['closem'] ?? '');
            $duration = 0;
            
            if ($startTime && $closeTime) {
                $duration = round(($closeTime - $startTime) / 60, 2); // Duration in minutes
            }
            
            // Check if meeting started on time
            $scheduledTime = strtotime($meeting['storedt'] ?? '');
            $startDelay = 0;
            $isDelayed = 'On Time';
            
            if ($scheduledTime && $startTime) {
                $startDelay = round(($startTime - $scheduledTime) / 60, 2); // Delay in minutes
                if ($startDelay > 15) { // More than 15 minutes delay
                    $isDelayed = 'Delayed';
                }
            }
            
            // Store meeting data
            $meetingData = [
                'meeting_id' => $meeting['meeting_id'] ?? 0,
                'user_id' => $meeting['user_id'] ?? 0,
                'user_name' => $meeting['user_name'] ?? 'Unknown User',
                'company_name' => $meeting['company_name'] ?? 'Unknown Company',
                'company_id' => $meeting['cid'] ?? 0,
                'task_id' => $meeting['task_id'] ?? 0,
                'scheduled_time' => $meeting['storedt'] ?? 'N/A',
                'actual_start' => $meeting['startm'] ?? 'N/A',
                'actual_close' => $meeting['closem'] ?? 'N/A',
                'meeting_status' => $meeting['meeting_status'] ?? 'Unknown',
                'meeting_type' => $meeting['mtype'] ?? 'Unknown',
                'task_name' => $meeting['task_name'] ?? 'No Task',
                'task_approved_by' => $meeting['task_approved_by'] ?? 'Not Approved',
                'task_approved_status' => $meeting['task_approved_status'] ?? 0,
                'current_status' => $meeting['current_status'] ?? 'Unknown',
                'task_plan_time_status' => $meeting['task_plan_time_status'] ?? 'Open',
                'task_complete_time_new_status' => $meeting['task_complete_time_new_status'] ?? 'Open',
                'write_mom_id' => $meeting['write_mom_id'] ?? null,
                'nextCFID' => $meeting['nextCFID'] ?? 0,
                'meeting_duration' => $duration,
                'start_delay' => $startDelay,
                'is_delayed' => $isDelayed,
                'has_pending_mom' => empty($meeting['write_mom_id']) ? true : false
            ];
            
            $meetingsArray[] = $meetingData;
            
            // Update user statistics
            $userId = $meeting['user_id'] ?? 0;
            $userName = $meeting['user_name'] ?? 'Unknown';
            
            if (!isset($userStats[$userId])) {
                $userStats[$userId] = [
                    'user_id' => $userId,
                    'user_name' => $userName,
                    'total_meetings' => 0,
                    'pending_mom_count' => 0,
                    'total_duration' => 0,
                    'average_duration' => 0,
                    'delayed_meetings' => 0,
                    'companies_visited' => [],
                    'task_types' => []
                ];
            }
            
            $userStats[$userId]['total_meetings']++;
            $userStats[$userId]['total_duration'] += $duration;
            $userStats[$userId]['average_duration'] = $userStats[$userId]['total_meetings'] > 0 ? 
                $userStats[$userId]['total_duration'] / $userStats[$userId]['total_meetings'] : 0;
            
            if ($meetingData['has_pending_mom']) {
                $userStats[$userId]['pending_mom_count']++;
            }
            
            if ($isDelayed === 'Delayed') {
                $userStats[$userId]['delayed_meetings']++;
            }
            
            // Track unique companies
            $companyId = $meeting['cid'] ?? 0;
            $companyName = $meeting['company_name'] ?? 'Unknown';
            if (!in_array($companyId, $userStats[$userId]['companies_visited'])) {
                $userStats[$userId]['companies_visited'][] = $companyId;
            }
            
            // Track task types
            $taskName = $meeting['task_name'] ?? 'Unknown';
            if (!isset($userStats[$userId]['task_types'][$taskName])) {
                $userStats[$userId]['task_types'][$taskName] = 0;
            }
            $userStats[$userId]['task_types'][$taskName]++;
            
            // Update company statistics
            if (!isset($companyStats[$companyId])) {
                $companyStats[$companyId] = [
                    'company_id' => $companyId,
                    'company_name' => $companyName,
                    'total_meetings' => 0,
                    'pending_mom_count' => 0,
                    'users_visited' => [],
                    'last_meeting_date' => '',
                    'average_duration' => 0,
                    'total_duration' => 0
                ];
            }
            
            $companyStats[$companyId]['total_meetings']++;
            $companyStats[$companyId]['total_duration'] += $duration;
            $companyStats[$companyId]['average_duration'] = $companyStats[$companyId]['total_meetings'] > 0 ? 
                $companyStats[$companyId]['total_duration'] / $companyStats[$companyId]['total_meetings'] : 0;
            
            if ($meetingData['has_pending_mom']) {
                $companyStats[$companyId]['pending_mom_count']++;
            }
            
            // Track users who visited
            if (!in_array($userId, $companyStats[$companyId]['users_visited'])) {
                $companyStats[$companyId]['users_visited'][] = $userId;
            }
            
            // Update last meeting date
            $meetingDate = strtotime($meeting['storedt'] ?? '');
            if ($meetingDate > strtotime($companyStats[$companyId]['last_meeting_date'])) {
                $companyStats[$companyId]['last_meeting_date'] = $meeting['storedt'] ?? '';
            }
        }
        
        // Calculate overall statistics
        $totalMeetings = count($meetingsArray);
        $totalPendingMom = array_sum(array_column(array_map(function($m) {
            return ['pending' => $m['has_pending_mom'] ? 1 : 0];
        }, $meetingsArray), 'pending'));
        
        $totalDuration = array_sum(array_column($meetingsArray, 'meeting_duration'));
        $averageDuration = $totalMeetings > 0 ? $totalDuration / $totalMeetings : 0;
        
        $delayedMeetings = count(array_filter($meetingsArray, function($m) {
            return $m['is_delayed'] === 'Delayed';
        }));
        
        $uniqueUsers = count($userStats);
        $uniqueCompanies = count($companyStats);
        
        // Prepare analysis data
        $analysisData = [
            'period' => $sdate . ' to ' . $edate,
            'meetings' => $meetingsArray,
            'user_stats' => $userStats,
            'company_stats' => $companyStats,
            'summary_stats' => [
                'total_meetings' => $totalMeetings,
                'total_pending_mom' => $totalPendingMom,
                'pending_mom_percentage' => $totalMeetings > 0 ? round(($totalPendingMom / $totalMeetings) * 100, 1) : 0,
                'total_duration_hours' => round($totalDuration / 60, 1),
                'average_duration_minutes' => round($averageDuration, 1),
                'delayed_meetings' => $delayedMeetings,
                'delayed_percentage' => $totalMeetings > 0 ? round(($delayedMeetings / $totalMeetings) * 100, 1) : 0,
                'unique_users' => $uniqueUsers,
                'unique_companies' => $uniqueCompanies,
                'meetings_per_user' => $uniqueUsers > 0 ? round($totalMeetings / $uniqueUsers, 1) : 0,
                'pending_mom_per_user' => $uniqueUsers > 0 ? round($totalPendingMom / $uniqueUsers, 1) : 0
            ]
        ];
        
        // Check if user is asking for specific user or company analysis
        $specificRequest = $this->extract_specific_request($message, $userStats, $companyStats);
        
        if ($specificRequest['type'] === 'user') {
            // Generate prompt for specific user analysis
            $prompt = $this->generate_specific_user_analysis_prompt($message, $specificRequest['data'], $analysisData);
        } elseif ($specificRequest['type'] === 'company') {
            // Generate prompt for specific company analysis
            $prompt = $this->generate_specific_company_analysis_prompt($message, $specificRequest['data'], $analysisData);
        } else {
            // Generate prompt for general analysis
            $prompt = $this->generate_pending_mom_analysis_prompt($message, $analysisData);
        }
        
        // Get ChatGPT response
        $chatgptResponse = $this->ChatAI_model->call_chatgpt_api($prompt, []);
        
        // Prepare data for frontend
        $frontendData = $this->prepare_pending_mom_analysis_data($analysisData, $specificRequest);
        
        return [
            'content' => $chatgptResponse,
            'data' => $frontendData
        ];
    }

    private function extract_specific_request($message, $userStats, $companyStats) {
        $lowerMessage = strtolower($message);
        
        // Check for user requests
        foreach ($userStats as $userId => $userData) {
            $userName = strtolower($userData['user_name']);
            
            // Check for exact name match
            if (strpos($lowerMessage, $userName) !== false) {
                return [
                    'type' => 'user',
                    'data' => $userData
                ];
            }
            
            // Check for partial name match
            $nameParts = explode(' ', $userName);
            foreach ($nameParts as $part) {
                if (strlen($part) > 3 && strpos($lowerMessage, $part) !== false) {
                    return [
                        'type' => 'user',
                        'data' => $userData
                    ];
                }
            }
        }
        
        // Check for company requests
        foreach ($companyStats as $companyId => $companyData) {
            $companyName = strtolower($companyData['company_name']);
            
            // Check for company name match
            if (strpos($lowerMessage, $companyName) !== false) {
                return [
                    'type' => 'company',
                    'data' => $companyData
                ];
            }
        }
        
        return ['type' => 'general', 'data' => null];
    }

    private function generate_specific_user_analysis_prompt($message, $userData, $analysisData) {
        $formattedData = "SPECIFIC USER ANALYSIS - {$userData['user_name']}\n\n";
        
        $formattedData .= "USER PERFORMANCE METRICS:\n";
        $formattedData .= "- Total Meetings Conducted: {$userData['total_meetings']}\n";
        $formattedData .= "- Pending MoM Meetings: {$userData['pending_mom_count']}\n";
        $formattedData .= "- Pending MoM Percentage: " . ($userData['total_meetings'] > 0 ? 
            round(($userData['pending_mom_count'] / $userData['total_meetings']) * 100, 1) : 0) . "%\n";
        $formattedData .= "- Average Meeting Duration: " . round($userData['average_duration'], 1) . " minutes\n";
        $formattedData .= "- Delayed Meetings: {$userData['delayed_meetings']}\n";
        $formattedData .= "- Unique Companies Visited: " . count($userData['companies_visited']) . "\n";
        
        $formattedData .= "\nTASK TYPE DISTRIBUTION:\n";
        foreach ($userData['task_types'] as $task => $count) {
            $percentage = $userData['total_meetings'] > 0 ? 
                round(($count / $userData['total_meetings']) * 100, 1) : 0;
            $formattedData .= "- {$task}: {$count} meetings ({$percentage}%)\n";
        }
        
        $formattedData .= "\nTEAM COMPARISON:\n";
        $formattedData .= "- Team Average Meetings per User: {$analysisData['summary_stats']['meetings_per_user']}\n";
        $formattedData .= "- Team Average Pending MoM per User: {$analysisData['summary_stats']['pending_mom_per_user']}\n";
        $formattedData .= "- Team Pending MoM Percentage: {$analysisData['summary_stats']['pending_mom_percentage']}%\n";
        
        // Calculate user's rank
        $userPendingCount = $userData['pending_mom_count'];
        $higherCount = 0;
        $lowerCount = 0;
        
        foreach ($analysisData['user_stats'] as $otherUser) {
            if ($otherUser['user_id'] == $userData['user_id']) continue;
            
            if ($otherUser['pending_mom_count'] > $userPendingCount) {
                $higherCount++;
            } elseif ($otherUser['pending_mom_count'] < $userPendingCount) {
                $lowerCount++;
            }
        }
        
        $totalUsers = count($analysisData['user_stats']);
        if ($totalUsers > 1) {
            $percentile = round(($lowerCount / ($totalUsers - 1)) * 100, 1);
            $position = $higherCount + 1;
            $formattedData .= "- Pending MoM Rank: {$position} out of {$totalUsers} users (better than {$percentile}%)\n";
        }
        
        $prompt = "You are a sales performance analytics AI. Analyze the following user data for pending MoM documentation:\n\n";
        $prompt .= $formattedData . "\n\n";
        $prompt .= "User Query: {$message}\n\n";
        $prompt .= "Please provide a comprehensive analysis with:\n";
        $prompt .= "1. User performance overview\n";
        $prompt .= "2. Pending MoM documentation analysis\n";
        $prompt .= "3. Meeting efficiency and timeliness\n";
        $prompt .= "4. Comparison with team averages\n";
        $prompt .= "5. Task type patterns and preferences\n";
        $prompt .= "6. Recommendations for improving MoM documentation\n";
        $prompt .= "7. Suggestions for follow-up actions\n\n";
        
        $prompt .= "Format response with clear sections and use specific numbers.";
        
        return $prompt;
    }

    private function generate_specific_company_analysis_prompt($message, $companyData, $analysisData) {
        $formattedData = "SPECIFIC COMPANY ANALYSIS - {$companyData['company_name']}\n\n";
        
        $formattedData .= "COMPANY ENGAGEMENT METRICS:\n";
        $formattedData .= "- Total Meetings: {$companyData['total_meetings']}\n";
        $formattedData .= "- Pending MoM Meetings: {$companyData['pending_mom_count']}\n";
        $formattedData .= "- Pending MoM Percentage: " . ($companyData['total_meetings'] > 0 ? 
            round(($companyData['pending_mom_count'] / $companyData['total_meetings']) * 100, 1) : 0) . "%\n";
        $formattedData .= "- Average Meeting Duration: " . round($companyData['average_duration'], 1) . " minutes\n";
        $formattedData .= "- Unique Users Visited: " . count($companyData['users_visited']) . "\n";
        $formattedData .= "- Last Meeting Date: {$companyData['last_meeting_date']}\n";
        
        $formattedData .= "\nOVERALL STATISTICS:\n";
        $formattedData .= "- Total Companies in System: {$analysisData['summary_stats']['unique_companies']}\n";
        $formattedData .= "- Average Meetings per Company: " . ($analysisData['summary_stats']['unique_companies'] > 0 ? 
            round($analysisData['summary_stats']['total_meetings'] / $analysisData['summary_stats']['unique_companies'], 1) : 0) . "\n";
        $formattedData .= "- Overall Pending MoM Percentage: {$analysisData['summary_stats']['pending_mom_percentage']}%\n";
        
        $prompt = "You are a business relationship analytics AI. Analyze the following company data for pending MoM documentation:\n\n";
        $prompt .= $formattedData . "\n\n";
        $prompt .= "User Query: {$message}\n\n";
        $prompt .= "Please provide a comprehensive analysis with:\n";
        $prompt .= "1. Company engagement overview\n";
        $prompt .= "2. Pending MoM documentation status\n";
        $prompt .= "3. Meeting frequency and patterns\n";
        $prompt .= "4. Comparison with overall averages\n";
        $prompt .= "5. Risk assessment for relationship management\n";
        $prompt .= "6. Recommendations for follow-up actions\n";
        $prompt .= "7. Suggestions for improving documentation with this client\n\n";
        
        $prompt .= "Format response with clear sections and use specific numbers.";
        
        return $prompt;
    }

    private function generate_pending_mom_analysis_prompt($message, $analysisData) {
        $formattedData = "PENDING MOM ANALYSIS for Period: {$analysisData['period']}\n\n";
        
        $formattedData .= "OVERALL SUMMARY:\n";
        $formattedData .= "- Total Meetings Analyzed: {$analysisData['summary_stats']['total_meetings']}\n";
        $formattedData .= "- Meetings with Pending MoM: {$analysisData['summary_stats']['total_pending_mom']}\n";
        $formattedData .= "- Pending MoM Percentage: {$analysisData['summary_stats']['pending_mom_percentage']}%\n";
        $formattedData .= "- Total Meeting Duration: {$analysisData['summary_stats']['total_duration_hours']} hours\n";
        $formattedData .= "- Average Meeting Duration: {$analysisData['summary_stats']['average_duration_minutes']} minutes\n";
        $formattedData .= "- Delayed Meetings: {$analysisData['summary_stats']['delayed_meetings']} ({$analysisData['summary_stats']['delayed_percentage']}%)\n";
        $formattedData .= "- Unique Users: {$analysisData['summary_stats']['unique_users']}\n";
        $formattedData .= "- Unique Companies: {$analysisData['summary_stats']['unique_companies']}\n";
        
        $formattedData .= "\nUSER PERFORMANCE RANKING (by pending MoM count):\n";
        $usersByPending = [];
        foreach ($analysisData['user_stats'] as $user) {
            $usersByPending[] = [
                'name' => $user['user_name'],
                'pending_count' => $user['pending_mom_count'],
                'total_meetings' => $user['total_meetings'],
                'pending_percentage' => $user['total_meetings'] > 0 ? 
                    round(($user['pending_mom_count'] / $user['total_meetings']) * 100, 1) : 0
            ];
        }
        
        // Sort by pending count (descending)
        usort($usersByPending, function($a, $b) {
            return $b['pending_count'] - $a['pending_count'];
        });
        
        $top5 = array_slice($usersByPending, 0, 5);
        foreach ($top5 as $index => $user) {
            $rank = $index + 1;
            $formattedData .= "{$rank}. {$user['name']} - {$user['pending_count']} pending MoM ({$user['pending_percentage']}% of their meetings)\n";
        }
        
        $formattedData .= "\nCOMPANY ANALYSIS (most meetings with pending MoM):\n";
        $companiesByPending = [];
        foreach ($analysisData['company_stats'] as $company) {
            if ($company['pending_mom_count'] > 0) {
                $companiesByPending[] = [
                    'name' => $company['company_name'],
                    'pending_count' => $company['pending_mom_count'],
                    'total_meetings' => $company['total_meetings'],
                    'pending_percentage' => $company['total_meetings'] > 0 ? 
                        round(($company['pending_mom_count'] / $company['total_meetings']) * 100, 1) : 0
                ];
            }
        }
        
        // Sort by pending count (descending)
        usort($companiesByPending, function($a, $b) {
            return $b['pending_count'] - $a['pending_count'];
        });
        
        $top5Companies = array_slice($companiesByPending, 0, 5);
        foreach ($top5Companies as $index => $company) {
            $rank = $index + 1;
            $formattedData .= "{$rank}. {$company['name']} - {$company['pending_count']} pending MoM ({$company['pending_percentage']}%)\n";
        }
        
        $formattedData .= "\nMEETING PATTERNS:\n";
        
        // Calculate day-wise distribution
        $dayDistribution = ['Monday' => 0, 'Tuesday' => 0, 'Wednesday' => 0, 'Thursday' => 0, 'Friday' => 0, 'Saturday' => 0, 'Sunday' => 0];
        $timeDistribution = ['Morning' => 0, 'Afternoon' => 0, 'Evening' => 0];
        
        foreach ($analysisData['meetings'] as $meeting) {
            $meetingDate = strtotime($meeting['scheduled_time']);
            if ($meetingDate) {
                $dayOfWeek = date('l', $meetingDate);
                $dayDistribution[$dayOfWeek]++;
                
                $hour = date('H', $meetingDate);
                if ($hour < 12) {
                    $timeDistribution['Morning']++;
                } elseif ($hour < 17) {
                    $timeDistribution['Afternoon']++;
                } else {
                    $timeDistribution['Evening']++;
                }
            }
        }
        
        $formattedData .= "Day-wise Distribution:\n";
        foreach ($dayDistribution as $day => $count) {
            if ($count > 0) {
                $percentage = $analysisData['summary_stats']['total_meetings'] > 0 ? 
                    round(($count / $analysisData['summary_stats']['total_meetings']) * 100, 1) : 0;
                $formattedData .= "- {$day}: {$count} meetings ({$percentage}%)\n";
            }
        }
        
        $formattedData .= "\nTime-wise Distribution:\n";
        foreach ($timeDistribution as $time => $count) {
            $percentage = $analysisData['summary_stats']['total_meetings'] > 0 ? 
                round(($count / $analysisData['summary_stats']['total_meetings']) * 100, 1) : 0;
            $formattedData .= "- {$time}: {$count} meetings ({$percentage}%)\n";
        }
        
        $formattedData .= "\nCRITICAL ISSUES IDENTIFIED:\n";
        
        // Identify users with high pending MoM percentage
        $criticalUsers = [];
        foreach ($analysisData['user_stats'] as $user) {
            if ($user['total_meetings'] >= 3) {
                $pendingPercentage = ($user['pending_mom_count'] / $user['total_meetings']) * 100;
                if ($pendingPercentage > 50) {
                    $criticalUsers[] = [
                        'name' => $user['user_name'],
                        'pending_percentage' => round($pendingPercentage, 1),
                        'total_meetings' => $user['total_meetings']
                    ];
                }
            }
        }
        
        if (!empty($criticalUsers)) {
            $formattedData .= "Users with >50% pending MoM:\n";
            foreach ($criticalUsers as $user) {
                $formattedData .= "- {$user['name']}: {$user['pending_percentage']}% pending ({$user['total_meetings']} total meetings)\n";
            }
        } else {
            $formattedData .= "No users with critical pending MoM levels (>50%)\n";
        }
        
        $prompt = "You are a sales operations analytics AI. Analyze the following pending MoM documentation data:\n\n";
        $prompt .= $formattedData . "\n\n";
        $prompt .= "User Query: {$message}\n\n";
        $prompt .= "Please provide a comprehensive analysis with:\n";
        $prompt .= "1. Overall summary and key metrics\n";
        $prompt .= "2. Identify top 5 users with most pending MoM documentation\n";
        $prompt .= "3. Company-wise analysis of pending documentation\n";
        $prompt .= "4. Meeting patterns and scheduling insights\n";
        $prompt .= "5. Critical areas needing immediate attention\n";
        $prompt .= "6. Recommendations for improving MoM documentation process\n";
        $prompt .= "7. Suggest follow-up actions for management\n";
        $prompt .= "8. Provide actionable insights for sales team improvement\n\n";
        
        $prompt .= "Format response with clear sections, use bullet points where appropriate, and highlight key findings.";
        
        return $prompt;
    }

    private function prepare_pending_mom_analysis_data($analysisData, $specificRequest) {
        $preparedData = [
            'tableData' => null,
            'summaryData' => $analysisData['summary_stats'],
            'chartData' => null,
            'userPerformanceData' => null,
            'companyAnalysisData' => null,
            'meetingPatternData' => null,
            'specificRequestData' => null,
            'status' => 'success',
            'message' => 'Pending MoM analysis data prepared successfully'
        ];
        
        // Prepare main meetings table
        $tableRows = [];
        foreach ($analysisData['meetings'] as $meeting) {
            $tableRows[] = [
                $meeting['user_name'],
                $meeting['company_name'],
                $meeting['scheduled_time'],
                $meeting['actual_start'],
                $meeting['actual_close'],
                round($meeting['meeting_duration'], 1) . ' min',
                $meeting['is_delayed'],
                $meeting['task_name'],
                $meeting['current_status'],
                $meeting['has_pending_mom'] ? 'Yes' : 'No'
            ];
        }
        
        $preparedData['tableData'] = [
            'title' => 'Pending MoM Meetings (' . count($analysisData['meetings']) . ' meetings)',
            'headers' => ['User', 'Company', 'Scheduled', 'Actual Start', 'Actual Close', 'Duration', 'Delay Status', 'Task', 'Status', 'Pending MoM'],
            'rows' => $tableRows
        ];
        
        // Prepare user performance data
        $userPerformanceRows = [];
        $userPendingData = [];
        $userNames = [];
        
        foreach ($analysisData['user_stats'] as $user) {
            $pendingPercentage = $user['total_meetings'] > 0 ? 
                round(($user['pending_mom_count'] / $user['total_meetings']) * 100, 1) : 0;
            
            $userPerformanceRows[] = [
                $user['user_name'],
                $user['total_meetings'],
                $user['pending_mom_count'],
                $pendingPercentage . '%',
                round($user['average_duration'], 1) . ' min',
                $user['delayed_meetings'],
                count($user['companies_visited'])
            ];
            
            $userNames[] = $user['user_name'];
            $userPendingData[] = $user['pending_mom_count'];
        }
        
        $preparedData['userPerformanceData'] = [
            'title' => 'User Performance Analysis',
            'headers' => ['User', 'Total Meetings', 'Pending MoM', 'Pending %', 'Avg Duration', 'Delayed Meetings', 'Unique Companies'],
            'rows' => $userPerformanceRows
        ];
        
        // Prepare company analysis data
        $companyAnalysisRows = [];
        foreach ($analysisData['company_stats'] as $company) {
            $pendingPercentage = $company['total_meetings'] > 0 ? 
                round(($company['pending_mom_count'] / $company['total_meetings']) * 100, 1) : 0;
            
            $companyAnalysisRows[] = [
                $company['company_name'],
                $company['total_meetings'],
                $company['pending_mom_count'],
                $pendingPercentage . '%',
                round($company['average_duration'], 1) . ' min',
                count($company['users_visited']),
                $company['last_meeting_date']
            ];
        }
        
        $preparedData['companyAnalysisData'] = [
            'title' => 'Company Analysis',
            'headers' => ['Company', 'Total Meetings', 'Pending MoM', 'Pending %', 'Avg Duration', 'Unique Users', 'Last Meeting'],
            'rows' => $companyAnalysisRows
        ];
        
        // Prepare chart data
        if (!empty($userNames) && !empty($userPendingData)) {
            // User performance chart
            $userPerformanceChart = [
                'labels' => $userNames,
                'datasets' => [
                    [
                        'label' => 'Pending MoM Count',
                        'data' => $userPendingData,
                        'backgroundColor' => '#ff6b6b',
                        'borderColor' => '#ff3d3d',
                        'borderWidth' => 1
                    ]
                ]
            ];
            
            // Prepare pending vs completed chart
            $pendingCompletedChart = [
                'labels' => ['Pending MoM', 'Completed Documentation'],
                'datasets' => [
                    [
                        'label' => 'Meetings',
                        'data' => [
                            $analysisData['summary_stats']['total_pending_mom'],
                            $analysisData['summary_stats']['total_meetings'] - $analysisData['summary_stats']['total_pending_mom']
                        ],
                        'backgroundColor' => ['#ff6b6b', '#10a37f'],
                        'borderColor' => ['#ff3d3d', '#0d8c6b'],
                        'borderWidth' => 1
                    ]
                ]
            ];
            
            $preparedData['chartData'] = [
                'userPerformance' => $userPerformanceChart,
                'pendingCompleted' => $pendingCompletedChart
            ];
        }
        
        // Prepare meeting pattern data
        $dayDistribution = ['Monday' => 0, 'Tuesday' => 0, 'Wednesday' => 0, 'Thursday' => 0, 'Friday' => 0, 'Saturday' => 0, 'Sunday' => 0];
        $timeDistribution = ['Morning (6AM-12PM)' => 0, 'Afternoon (12PM-5PM)' => 0, 'Evening (5PM-9PM)' => 0];
        
        foreach ($analysisData['meetings'] as $meeting) {
            $meetingDate = strtotime($meeting['scheduled_time']);
            if ($meetingDate) {
                $dayOfWeek = date('l', $meetingDate);
                $dayDistribution[$dayOfWeek]++;
                
                $hour = date('H', $meetingDate);
                if ($hour < 12) {
                    $timeDistribution['Morning (6AM-12PM)']++;
                } elseif ($hour < 17) {
                    $timeDistribution['Afternoon (12PM-5PM)']++;
                } else {
                    $timeDistribution['Evening (5PM-9PM)']++;
                }
            }
        }
        
        $preparedData['meetingPatternData'] = [
            'dayDistribution' => $dayDistribution,
            'timeDistribution' => $timeDistribution
        ];
        
        // Prepare specific request data if applicable
        if ($specificRequest['type'] !== 'general' && $specificRequest['data']) {
            $preparedData['specificRequestData'] = [
                'type' => $specificRequest['type'],
                'data' => $specificRequest['data']
            ];
        }
        
        // Add critical alerts
        $criticalAlerts = [];
        
        // Users with high pending percentage
        foreach ($analysisData['user_stats'] as $user) {
            if ($user['total_meetings'] >= 3) {
                $pendingPercentage = ($user['pending_mom_count'] / $user['total_meetings']) * 100;
                if ($pendingPercentage > 50) {
                    $criticalAlerts[] = [
                        'type' => 'user',
                        'name' => $user['user_name'],
                        'message' => "{$user['user_name']} has " . round($pendingPercentage, 1) . "% pending MoM documentation",
                        'severity' => 'high'
                    ];
                }
            }
        }
        
        // Companies with high pending percentage
        foreach ($analysisData['company_stats'] as $company) {
            if ($company['total_meetings'] >= 3) {
                $pendingPercentage = ($company['pending_mom_count'] / $company['total_meetings']) * 100;
                if ($pendingPercentage > 75) {
                    $criticalAlerts[] = [
                        'type' => 'company',
                        'name' => $company['company_name'],
                        'message' => "{$company['company_name']} has " . round($pendingPercentage, 1) . "% pending MoM documentation",
                        'severity' => 'medium'
                    ];
                }
            }
        }
        
        $preparedData['criticalAlerts'] = $criticalAlerts;
        
        return $preparedData;
    }
}