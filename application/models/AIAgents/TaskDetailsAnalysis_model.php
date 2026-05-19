<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TaskDetailsAnalysis_model extends CI_Model {

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
    public function process_total_task_details_analysis($message, $analysisType, $sdate, $edate) 
    {
        // Get task data from database
        $totalTasksUserByDatas = $this->Menu_model->GetTeamTaskOnSelfOrOtherFunnelTaskListsDats(
            $this->uid, $sdate, $edate, 'total_task', $this->uid, 'all', NULL
        );

        // Process task data into structured format
        $processedData = $this->process_raw_task_data($totalTasksUserByDatas);

        // Merge task data with user information
        $analysisData = $this->prepare_analysis_data(
            $processedData, 
            $sdate, 
            $edate
        );

        // Extract specific user request if present
        $specificUser = $this->extract_specific_user_request($message, $analysisData['users']);

        // Generate additional company analysis
        $analysisData['additional_company_analysis'] = $this->generate_additional_company_analysis($totalTasksUserByDatas);

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
        $userTasks = [];

        if (empty($rawData) || !is_array($rawData)) {
            return $processedData;
        }

        // Group tasks by user
        foreach ($rawData as $task) {
            if (!is_object($task)) continue;
            
            $userId = $task->task_user_id ?? $task->assignedto_id ?? 0;
            $username = $task->task_username ?? 'Unknown User';
            
            if (!isset($userTasks[$userId])) {
                $userTasks[$userId] = [
                    'user_id' => $userId,
                    'username' => $username,
                    'tasks' => []
                ];
            }
            
            $userTasks[$userId]['tasks'][] = $task;
        }

        // Process each user's tasks
        foreach ($userTasks as $userId => $userData) {
            $tasks = $userData['tasks'];
            
            // Extract company task data (default 20, max 200 companies)
            $companyTasks = $this->extract_company_task_data($tasks, 200);
            
            $processedData[] = [
                'user_id' => $userId,
                'username' => $userData['username'],
                'total_task' => count($tasks),
                'total_complete_task' => $this->count_completed_tasks($tasks),
                'total_pending_task' => $this->count_pending_tasks($tasks),
                'completion_rate' => $this->calculate_completion_rate($tasks),
                'task_efficiency' => $this->calculate_task_efficiency($tasks),
                'meeting_statistics' => $this->extract_meeting_statistics($tasks),
                'sales_metrics' => $this->extract_sales_metrics($tasks),
                'client_analysis' => $this->extract_client_analysis($tasks),
                'task_quality' => $this->extract_task_quality($tasks),
                'status_analysis' => $this->extract_status_analysis($tasks),
                'company_analysis' => $this->analyze_companies($companyTasks),
                'company_task_data' => $companyTasks,
                'positive_companies' => $this->get_top_positive_companies($companyTasks, 10),
                'negative_companies' => $this->get_top_negative_companies($companyTasks, 10),
                'to_work_companies' => $this->get_top_to_work_companies($companyTasks, 10),
                'overall_companies' => $this->get_top_overall_companies($companyTasks, 10),
                'remarks_analysis' => $this->analyze_remarks($tasks),
                'mtype_analysis' => $this->analyze_mtypes($tasks),
                'time_analysis' => $this->analyze_time_metrics($tasks)
            ];
        }

        return $processedData;
    }

    /**
     * Generate additional company analysis
     */
    private function generate_additional_company_analysis($rawData)
    {
        $analysis = [
            'top_positive_companies' => [],
            'top_negative_companies' => [],
            'top_companies_by_tasks' => [],
            'top_remeeting_companies' => [],
            'top_fresh_companies' => []
        ];

        if (empty($rawData) || !is_array($rawData)) {
            return $analysis;
        }

        // Initialize arrays for different analyses
        $positiveCompanies = [];
        $negativeCompanies = [];
        $companyTaskCounts = [];
        $remeetingCompanies = [];
        $freshCompanies = [];

        foreach ($rawData as $task) {
            if (!is_object($task)) continue;
            
            $companyId = $task->cid ?? 0;
            $companyName = $task->compname ?? 'Unknown Company';
            $taskName = $task->task_name ?? 'Unknown Task';
            $currentStatus = strtoupper($task->current_status ?? '');
            $remarks = trim($task->remarks ?? '');
            $isMeeting = stripos($taskName, 'meeting') !== false;
            $isFresh = !$isMeeting; // Assuming non-meeting tasks are "fresh"

            // Track company task counts
            $companyKey = $companyId . '_' . $companyName;
            if (!isset($companyTaskCounts[$companyKey])) {
                $companyTaskCounts[$companyKey] = [
                    'cid' => $companyId,
                    'compname' => $companyName,
                    'count' => 0,
                    'tasks' => []
                ];
            }
            $companyTaskCounts[$companyKey]['count']++;
            $companyTaskCounts[$companyKey]['tasks'][] = [
                'task_name' => $taskName,
                'current_status' => $currentStatus,
                'remarks' => $remarks
            ];

            // Track positive companies
            if (strpos($currentStatus, 'POSITIVE') !== false || 
                stripos($taskName, 'positive') !== false) {
                
                if (!isset($positiveCompanies[$companyKey])) {
                    $positiveCompanies[$companyKey] = [
                        'cid' => $companyId,
                        'compname' => $companyName,
                        'count' => 0,
                        'tasks' => []
                    ];
                }
                $positiveCompanies[$companyKey]['count']++;
                $positiveCompanies[$companyKey]['tasks'][] = [
                    'task_name' => $taskName,
                    'current_status' => $currentStatus,
                    'remarks' => $remarks
                ];
            }

            // Track negative companies
            if (strpos($currentStatus, 'NEGATIVE') !== false || 
                stripos($taskName, 'negative') !== false) {
                
                if (!isset($negativeCompanies[$companyKey])) {
                    $negativeCompanies[$companyKey] = [
                        'cid' => $companyId,
                        'compname' => $companyName,
                        'count' => 0,
                        'tasks' => []
                    ];
                }
                $negativeCompanies[$companyKey]['count']++;
                $negativeCompanies[$companyKey]['tasks'][] = [
                    'task_name' => $taskName,
                    'current_status' => $currentStatus,
                    'remarks' => $remarks
                ];
            }

            // Track remeeting companies (multiple meeting tasks)
            if ($isMeeting) {
                if (!isset($remeetingCompanies[$companyKey])) {
                    $remeetingCompanies[$companyKey] = [
                        'cid' => $companyId,
                        'compname' => $companyName,
                        'meeting_count' => 0,
                        'tasks' => []
                    ];
                }
                $remeetingCompanies[$companyKey]['meeting_count']++;
                $remeetingCompanies[$companyKey]['tasks'][] = [
                    'task_name' => $taskName,
                    'current_status' => $currentStatus,
                    'remarks' => $remarks
                ];
            }

            // Track fresh companies (non-meeting tasks)
            if ($isFresh) {
                if (!isset($freshCompanies[$companyKey])) {
                    $freshCompanies[$companyKey] = [
                        'cid' => $companyId,
                        'compname' => $companyName,
                        'fresh_task_count' => 0,
                        'tasks' => []
                    ];
                }
                $freshCompanies[$companyKey]['fresh_task_count']++;
                $freshCompanies[$companyKey]['tasks'][] = [
                    'task_name' => $taskName,
                    'current_status' => $currentStatus,
                    'remarks' => $remarks
                ];
            }
        }

        // Sort and get top 20 for each category
        $analysis['top_positive_companies'] = $this->get_top_companies_by_count($positiveCompanies, 20, 'positive');
        $analysis['top_negative_companies'] = $this->get_top_companies_by_count($negativeCompanies, 20, 'negative');
        $analysis['top_companies_by_tasks'] = $this->get_top_companies_by_task_count($companyTaskCounts, 20);
        $analysis['top_remeeting_companies'] = $this->get_top_remeeting_companies($remeetingCompanies, 20);
        $analysis['top_fresh_companies'] = $this->get_top_fresh_companies($freshCompanies, 20);

        return $analysis;
    }

    /**
     * Get top companies by count
     */
    private function get_top_companies_by_count($companies, $limit, $type = 'positive')
    {
        uasort($companies, function($a, $b) {
            return $b['count'] - $a['count'];
        });
        
        $result = [];
        $count = 0;
        
        foreach ($companies as $company) {
            if ($count >= $limit) break;
            
            // Get representative task for each company
            $representativeTask = $this->get_representative_task($company['tasks'], $type);
            
            $result[] = [
                'cid' => $company['cid'],
                'compname' => $company['compname'],
                'task_name' => $representativeTask['task_name'],
                'current_status' => $representativeTask['current_status'],
                'remarks' => $representativeTask['remarks'],
                'total_count' => $company['count']
            ];
            $count++;
        }
        
        return $result;
    }

    /**
     * Get top companies by total task count
     */
    private function get_top_companies_by_task_count($companyTaskCounts, $limit)
    {
        uasort($companyTaskCounts, function($a, $b) {
            return $b['count'] - $a['count'];
        });
        
        $result = [];
        $count = 0;
        
        foreach ($companyTaskCounts as $company) {
            if ($count >= $limit) break;
            
            // Get representative task (most recent or with remarks)
            $representativeTask = $this->get_representative_task($company['tasks']);
            
            $result[] = [
                'cid' => $company['cid'],
                'compname' => $company['compname'],
                'task_name' => $representativeTask['task_name'],
                'current_status' => $representativeTask['current_status'],
                'remarks' => $representativeTask['remarks'],
                'total_tasks' => $company['count']
            ];
            $count++;
        }
        
        return $result;
    }

    /**
     * Get top remeeting companies
     */
    private function get_top_remeeting_companies($remeetingCompanies, $limit)
    {
        uasort($remeetingCompanies, function($a, $b) {
            return $b['meeting_count'] - $a['meeting_count'];
        });
        
        $result = [];
        $count = 0;
        
        foreach ($remeetingCompanies as $company) {
            if ($count >= $limit) break;
            
            // Get representative meeting task
            $representativeTask = $this->get_representative_meeting_task($company['tasks']);
            
            $result[] = [
                'cid' => $company['cid'],
                'compname' => $company['compname'],
                'task_name' => $representativeTask['task_name'],
                'current_status' => $representativeTask['current_status'],
                'remarks' => $representativeTask['remarks'],
                'meeting_count' => $company['meeting_count']
            ];
            $count++;
        }
        
        return $result;
    }

    /**
     * Get top fresh companies
     */
    private function get_top_fresh_companies($freshCompanies, $limit)
    {
        uasort($freshCompanies, function($a, $b) {
            return $b['fresh_task_count'] - $a['fresh_task_count'];
        });
        
        $result = [];
        $count = 0;
        
        foreach ($freshCompanies as $company) {
            if ($count >= $limit) break;
            
            // Get representative fresh task
            $representativeTask = $this->get_representative_fresh_task($company['tasks']);
            
            $result[] = [
                'cid' => $company['cid'],
                'compname' => $company['compname'],
                'task_name' => $representativeTask['task_name'],
                'current_status' => $representativeTask['current_status'],
                'remarks' => $representativeTask['remarks'],
                'fresh_task_count' => $company['fresh_task_count']
            ];
            $count++;
        }
        
        return $result;
    }

    /**
     * Get representative task for a company
     */
    private function get_representative_task($tasks, $type = '')
    {
        if (empty($tasks)) {
            return [
                'task_name' => 'No task data',
                'current_status' => 'N/A',
                'remarks' => ''
            ];
        }

        // Try to find a task with remarks first
        foreach ($tasks as $task) {
            if (!empty(trim($task['remarks']))) {
                return $task;
            }
        }

        // Otherwise return the first task
        return $tasks[0];
    }

    /**
     * Get representative meeting task
     */
    private function get_representative_meeting_task($tasks)
    {
        if (empty($tasks)) {
            return [
                'task_name' => 'No meeting data',
                'current_status' => 'N/A',
                'remarks' => ''
            ];
        }

        // Try to find a meeting task with remarks
        foreach ($tasks as $task) {
            if (!empty(trim($task['remarks'])) && 
                stripos($task['task_name'], 'meeting') !== false) {
                return $task;
            }
        }

        // Return first meeting task
        foreach ($tasks as $task) {
            if (stripos($task['task_name'], 'meeting') !== false) {
                return $task;
            }
        }

        return $tasks[0];
    }

    /**
     * Get representative fresh task
     */
    private function get_representative_fresh_task($tasks)
    {
        if (empty($tasks)) {
            return [
                'task_name' => 'No task data',
                'current_status' => 'N/A',
                'remarks' => ''
            ];
        }

        // Try to find a fresh task (non-meeting) with remarks
        foreach ($tasks as $task) {
            if (!empty(trim($task['remarks'])) && 
                stripos($task['task_name'], 'meeting') === false) {
                return $task;
            }
        }

        // Return first non-meeting task
        foreach ($tasks as $task) {
            if (stripos($task['task_name'], 'meeting') === false) {
                return $task;
            }
        }

        return $tasks[0];
    }

    /**
     * Extract company-specific task data with remarks
     */
    private function extract_company_task_data($tasks, $limit = 20)
    {
        $companyTasks = [];
        
        foreach ($tasks as $task) {
            if (!is_object($task)) continue;
            
            $companyName = $task->compname ?? 'Unknown Company';
            $taskName = $task->task_name ?? 'Unknown Task';
            $remarks = trim($task->remarks ?? '');
            $currentStatus = strtoupper($task->current_status ?? '');
            $purposeAchieved = strtolower($task->purpose_achieved ?? '');
            $mtype = strtoupper($task->mtype ?? '');
            
            if (!isset($companyTasks[$companyName])) {
                $companyTasks[$companyName] = [
                    'company_name' => $companyName,
                    'tasks' => [],
                    'total_tasks' => 0,
                    'positive_tasks' => 0,
                    'negative_tasks' => 0,
                    'to_work_tasks' => 0,
                    'meeting_tasks' => 0,
                    'proposal_tasks' => 0,
                    'completed_tasks' => 0,
                    'pending_tasks' => 0
                ];
            }
            
            $companyTasks[$companyName]['total_tasks']++;
            
            // Classify task
            $isPositive = false;
            $isNegative = false;
            $isToWork = false;
            
            // Check if positive
            if (strpos($currentStatus, 'POSITIVE') !== false || 
                strpos($taskName, 'positive') !== false ||
                $purposeAchieved == 'yes') {
                $isPositive = true;
                $companyTasks[$companyName]['positive_tasks']++;
            }
            
            // Check if negative
            elseif (strpos($currentStatus, 'NEGATIVE') !== false || 
                   strpos($taskName, 'negative') !== false ||
                   $purposeAchieved == 'no') {
                $isNegative = true;
                $companyTasks[$companyName]['negative_tasks']++;
            }
            
            // Check if needs work
            elseif ($currentStatus == 'OPEN' || 
                   empty($currentStatus) ||
                   strpos($taskName, 'follow') !== false ||
                   strpos($taskName, 'pending') !== false) {
                $isToWork = true;
                $companyTasks[$companyName]['to_work_tasks']++;
            }
            
            // Track meeting tasks
            if (strpos(strtolower($taskName), 'meeting') !== false) {
                $companyTasks[$companyName]['meeting_tasks']++;
            }
            
            // Track proposal tasks
            if ($mtype == 'PROPOSAL' || strpos($taskName, 'proposal') !== false) {
                $companyTasks[$companyName]['proposal_tasks']++;
            }
            
            // Track completion status
            if (isset($task->nextCFID) && $task->nextCFID != 0) {
                $companyTasks[$companyName]['completed_tasks']++;
            } else {
                $companyTasks[$companyName]['pending_tasks']++;
            }
            
            // Add task details
            $companyTasks[$companyName]['tasks'][] = [
                'task_name' => $taskName,
                'remarks' => $remarks,
                'status' => $currentStatus,
                'purpose_achieved' => $purposeAchieved,
                'mtype' => $mtype,
                'is_positive' => $isPositive,
                'is_negative' => $isNegative,
                'is_to_work' => $isToWork,
                'task_date' => $task->task_date ?? '',
                'assigned_to' => $task->task_username ?? 'Unknown'
            ];
        }
        
        // Sort companies by total tasks and limit
        uasort($companyTasks, function($a, $b) {
            return $b['total_tasks'] - $a['total_tasks'];
        });
        
        return array_slice($companyTasks, 0, $limit, true);
    }

    /**
     * Get top positive companies with tasks and remarks
     */
    private function get_top_positive_companies($companyTasks, $limit = 10)
    {
        $positiveCompanies = [];
        
        foreach ($companyTasks as $companyName => $companyData) {
            if ($companyData['positive_tasks'] > 0) {
                $positiveCompanies[$companyName] = [
                    'company_name' => $companyName,
                    'positive_tasks' => $companyData['positive_tasks'],
                    'total_tasks' => $companyData['total_tasks'],
                    'positive_ratio' => round(($companyData['positive_tasks'] / $companyData['total_tasks']) * 100, 2),
                    'tasks' => []
                ];
                
                // Get positive tasks with remarks
                foreach ($companyData['tasks'] as $task) {
                    if ($task['is_positive'] && !empty($task['remarks'])) {
                        $positiveCompanies[$companyName]['tasks'][] = [
                            'task_name' => $task['task_name'],
                            'remarks' => $task['remarks'],
                            'status' => $task['status'],
                            'date' => $task['task_date']
                        ];
                    }
                }
                
                // Limit tasks per company
                $positiveCompanies[$companyName]['tasks'] = array_slice(
                    $positiveCompanies[$companyName]['tasks'], 
                    0, 
                    5
                );
            }
        }
        
        // Sort by positive ratio and limit
        uasort($positiveCompanies, function($a, $b) {
            if ($b['positive_ratio'] === $a['positive_ratio']) {
                return $b['positive_tasks'] - $a['positive_tasks'];
            }
            return $b['positive_ratio'] - $a['positive_ratio'];
        });
        
        return array_slice($positiveCompanies, 0, $limit, true);
    }

    /**
     * Get top negative companies with tasks and remarks
     */
    private function get_top_negative_companies($companyTasks, $limit = 10)
    {
        $negativeCompanies = [];
        
        foreach ($companyTasks as $companyName => $companyData) {
            if ($companyData['negative_tasks'] > 0) {
                $negativeCompanies[$companyName] = [
                    'company_name' => $companyName,
                    'negative_tasks' => $companyData['negative_tasks'],
                    'total_tasks' => $companyData['total_tasks'],
                    'negative_ratio' => round(($companyData['negative_tasks'] / $companyData['total_tasks']) * 100, 2),
                    'tasks' => []
                ];
                
                // Get negative tasks with remarks
                foreach ($companyData['tasks'] as $task) {
                    if ($task['is_negative'] && !empty($task['remarks'])) {
                        $negativeCompanies[$companyName]['tasks'][] = [
                            'task_name' => $task['task_name'],
                            'remarks' => $task['remarks'],
                            'status' => $task['status'],
                            'date' => $task['task_date']
                        ];
                    }
                }
                
                // Limit tasks per company
                $negativeCompanies[$companyName]['tasks'] = array_slice(
                    $negativeCompanies[$companyName]['tasks'], 
                    0, 
                    5
                );
            }
        }
        
        // Sort by negative ratio and limit
        uasort($negativeCompanies, function($a, $b) {
            if ($b['negative_ratio'] === $a['negative_ratio']) {
                return $b['negative_tasks'] - $a['negative_tasks'];
            }
            return $b['negative_ratio'] - $a['negative_ratio'];
        });
        
        return array_slice($negativeCompanies, 0, $limit, true);
    }

    /**
     * Get top companies to work on with tasks and remarks
     */
    private function get_top_to_work_companies($companyTasks, $limit = 10)
    {
        $toWorkCompanies = [];
        
        foreach ($companyTasks as $companyName => $companyData) {
            if ($companyData['to_work_tasks'] > 0) {
                $toWorkCompanies[$companyName] = [
                    'company_name' => $companyName,
                    'to_work_tasks' => $companyData['to_work_tasks'],
                    'total_tasks' => $companyData['total_tasks'],
                    'pending_tasks' => $companyData['pending_tasks'],
                    'work_ratio' => round(($companyData['to_work_tasks'] / $companyData['total_tasks']) * 100, 2),
                    'tasks' => []
                ];
                
                // Get tasks that need work with remarks
                foreach ($companyData['tasks'] as $task) {
                    if ($task['is_to_work'] && !empty($task['remarks'])) {
                        $toWorkCompanies[$companyName]['tasks'][] = [
                            'task_name' => $task['task_name'],
                            'remarks' => $task['remarks'],
                            'status' => $task['status'],
                            'date' => $task['task_date']
                        ];
                    }
                }
                
                // Limit tasks per company
                $toWorkCompanies[$companyName]['tasks'] = array_slice(
                    $toWorkCompanies[$companyName]['tasks'], 
                    0, 
                    5
                );
            }
        }
        
        // Sort by to-work ratio and limit
        uasort($toWorkCompanies, function($a, $b) {
            if ($b['work_ratio'] === $a['work_ratio']) {
                return $b['pending_tasks'] - $a['pending_tasks'];
            }
            return $b['work_ratio'] - $a['work_ratio'];
        });
        
        return array_slice($toWorkCompanies, 0, $limit, true);
    }

    /**
     * Get overall top companies with comprehensive analysis
     */
    private function get_top_overall_companies($companyTasks, $limit = 10)
    {
        $overallCompanies = [];
        
        foreach ($companyTasks as $companyName => $companyData) {
            $engagementScore = $this->calculate_company_engagement_score($companyData);
            
            $overallCompanies[$companyName] = [
                'company_name' => $companyName,
                'total_tasks' => $companyData['total_tasks'],
                'engagement_score' => $engagementScore,
                'positive_tasks' => $companyData['positive_tasks'],
                'negative_tasks' => $companyData['negative_tasks'],
                'to_work_tasks' => $companyData['to_work_tasks'],
                'meeting_tasks' => $companyData['meeting_tasks'],
                'proposal_tasks' => $companyData['proposal_tasks'],
                'completed_tasks' => $companyData['completed_tasks'],
                'pending_tasks' => $companyData['pending_tasks'],
                'completion_rate' => round(($companyData['completed_tasks'] / $companyData['total_tasks']) * 100, 2),
                'key_tasks' => []
            ];
            
            // Get key tasks (most recent or significant)
            $tasks = $companyData['tasks'];
            usort($tasks, function($a, $b) {
                return strtotime($b['date'] ?? '') - strtotime($a['date'] ?? '');
            });
            
            foreach (array_slice($tasks, 0, 3) as $task) {
                if (!empty($task['remarks'])) {
                    $overallCompanies[$companyName]['key_tasks'][] = [
                        'task_name' => $task['task_name'],
                        'remarks' => $task['remarks'],
                        'status' => $task['status'],
                        'date' => $task['task_date']
                    ];
                }
            }
        }
        
        // Sort by engagement score and limit
        uasort($overallCompanies, function($a, $b) {
            if ($b['engagement_score'] === $a['engagement_score']) {
                return $b['total_tasks'] - $a['total_tasks'];
            }
            return $b['engagement_score'] - $a['engagement_score'];
        });
        
        return array_slice($overallCompanies, 0, $limit, true);
    }

    /**
     * Calculate company engagement score
     */
    private function calculate_company_engagement_score($companyData)
    {
        $score = 0;
        
        // Base score from total tasks (max 30)
        $score += min($companyData['total_tasks'] * 0.5, 30);
        
        // Bonus for positive tasks (max 30)
        $score += min($companyData['positive_tasks'] * 3, 30);
        
        // Bonus for meetings (max 20)
        $score += min($companyData['meeting_tasks'] * 2, 20);
        
        // Bonus for proposals (max 20)
        $score += min($companyData['proposal_tasks'] * 4, 20);
        
        // Penalty for negative tasks (max -20)
        $score -= min($companyData['negative_tasks'] * 2, 20);
        
        // Bonus for completion rate
        $completionRate = $companyData['total_tasks'] > 0 ? 
            ($companyData['completed_tasks'] / $companyData['total_tasks']) * 100 : 0;
        $score += $completionRate * 0.2;
        
        return round(max($score, 0), 2);
    }

    /**
     * Count completed tasks (nextCFID != 0)
     */
    private function count_completed_tasks($tasks)
    {
        $completed = 0;
        foreach ($tasks as $task) {
            if (isset($task->nextCFID) && $task->nextCFID != 0) {
                $completed++;
            }
        }
        return $completed;
    }

    /**
     * Count pending tasks (nextCFID == 0)
     */
    private function count_pending_tasks($tasks)
    {
        $pending = 0;
        foreach ($tasks as $task) {
            if (isset($task->nextCFID) && $task->nextCFID == 0) {
                $pending++;
            }
        }
        return $pending;
    }

    /**
     * Calculate completion rate
     */
    private function calculate_completion_rate($tasks)
    {
        $total = count($tasks);
        if ($total === 0) return 0;
        
        $completed = $this->count_completed_tasks($tasks);
        return round(($completed / $total) * 100, 2);
    }

    /**
     * Calculate task efficiency based on various factors
     */
    private function calculate_task_efficiency($tasks)
    {
        $total = count($tasks);
        if ($total === 0) return 0;
        
        $completed = $this->count_completed_tasks($tasks);
        $baseEfficiency = ($completed / $total) * 100;
        
        // Adjust based on meeting outcomes
        $meetingStats = $this->extract_meeting_statistics($tasks);
        $positiveMeetings = $meetingStats['positive_meetings'] ?? 0;
        $proposalMeetings = $meetingStats['proposal_meetings'] ?? 0;
        
        $qualityBonus = ($positiveMeetings / max($completed, 1)) * 20;
        $proposalBonus = ($proposalMeetings / max($completed, 1)) * 15;
        
        return min(100, $baseEfficiency + $qualityBonus + $proposalBonus);
    }

    /**
     * Extract meeting statistics
     */
    private function extract_meeting_statistics($tasks)
    {
        $stats = [
            'total_meetings' => 0,
            'successful_meetings' => 0,
            'failed_meetings' => 0,
            'positive_meetings' => 0,
            'negative_meetings' => 0,
            'proposal_meetings' => 0,
            'no_rp_meetings' => 0,
            'scheduled_meetings' => 0,
            'barg_in_meetings' => 0
        ];

        foreach ($tasks as $task) {
            $taskName = strtolower($task->task_name ?? '');
            $mtype = strtoupper($task->mtype ?? '');
            $purposeAchieved = strtolower($task->purpose_achieved ?? '');
            $currentStatus = strtolower($task->current_status ?? '');
            
            // Count by meeting type
            if (strpos($taskName, 'meeting') !== false) {
                $stats['total_meetings']++;
                
                if (strpos($taskName, 'scheduled') !== false) {
                    $stats['scheduled_meetings']++;
                }
                if (strpos($taskName, 'barg') !== false) {
                    $stats['barg_in_meetings']++;
                }
                
                // Check meeting outcomes
                if ($purposeAchieved == 'yes') {
                    $stats['successful_meetings']++;
                } elseif ($purposeAchieved == 'no') {
                    $stats['failed_meetings']++;
                }
                
                if (strpos($currentStatus, 'positive') !== false) {
                    $stats['positive_meetings']++;
                } elseif (strpos($currentStatus, 'negative') !== false) {
                    $stats['negative_meetings']++;
                }
                
                if ($mtype == 'PROPOSAL') {
                    $stats['proposal_meetings']++;
                } elseif ($mtype == 'NO RP') {
                    $stats['no_rp_meetings']++;
                }
            }
        }

        return $stats;
    }

    /**
     * Extract sales metrics
     */
    private function extract_sales_metrics($tasks)
    {
        $metrics = [
            'total_clients' => 0,
            'potential_clients' => 0,
            'top_spenders' => 0,
            'priority_clients' => 0,
            'key_companies' => 0,
            'pk_clients' => 0,
            'upsell_clients' => 0,
            'focus_funnel' => 0,
            'client_types' => []
        ];

        $clients = [];
        
        foreach ($tasks as $task) {
            $cid = $task->cid ?? null;
            $company = $task->compname ?? 'Unknown';
            
            if ($cid && !isset($clients[$cid])) {
                $clients[$cid] = [
                    'company' => $company,
                    'potential' => strtolower($task->potential ?? '') == 'yes',
                    'topspender' => strtolower($task->topspender ?? '') == 'yes',
                    'priority' => strtolower($task->priorityc ?? '') == 'yes',
                    'keycompany' => strtolower($task->keycompany ?? '') == 'yes',
                    'pkclient' => strtolower($task->pkclient ?? '') == 'yes',
                    'upsell' => strtolower($task->upsell_client ?? '') == 'yes',
                    'focus' => strtolower($task->focus_funnel ?? '') == 'yes'
                ];
                
                $metrics['total_clients']++;
                if ($clients[$cid]['potential']) $metrics['potential_clients']++;
                if ($clients[$cid]['topspender']) $metrics['top_spenders']++;
                if ($clients[$cid]['priority']) $metrics['priority_clients']++;
                if ($clients[$cid]['keycompany']) $metrics['key_companies']++;
                if ($clients[$cid]['pkclient']) $metrics['pk_clients']++;
                if ($clients[$cid]['upsell']) $metrics['upsell_clients']++;
                if ($clients[$cid]['focus']) $metrics['focus_funnel']++;
            }
        }
        
        $metrics['client_types'] = $clients;
        
        return $metrics;
    }

    /**
     * Extract client analysis
     */
    private function extract_client_analysis($tasks)
    {
        $analysis = [
            'total_unique_clients' => 0,
            'high_value_clients' => 0,
            'client_engagement_rate' => 0,
            'repeat_clients' => 0,
            'client_concentration' => [],
            'client_potential_score' => 0
        ];

        $clientCounts = [];
        $clientScores = [];
        
        foreach ($tasks as $task) {
            $cid = $task->cid ?? null;
            if ($cid) {
                if (!isset($clientCounts[$cid])) {
                    $clientCounts[$cid] = 0;
                }
                $clientCounts[$cid]++;
                
                // Calculate client score
                $score = 0;
                if (strtolower($task->potential ?? '') == 'yes') $score += 3;
                if (strtolower($task->topspender ?? '') == 'yes') $score += 5;
                if (strtolower($task->priorityc ?? '') == 'yes') $score += 4;
                if (strtolower($task->keycompany ?? '') == 'yes') $score += 4;
                if (strtolower($task->pkclient ?? '') == 'yes') $score += 3;
                $clientScores[$cid] = $score;
            }
        }

        $analysis['total_unique_clients'] = count($clientCounts);
        
        // Count high value clients (score > 5)
        foreach ($clientScores as $score) {
            if ($score > 5) {
                $analysis['high_value_clients']++;
            }
        }
        
        // Calculate engagement rate
        if (count($clientCounts) > 0) {
            $analysis['client_engagement_rate'] = round(array_sum($clientCounts) / count($clientCounts), 2);
        }
        
        // Count repeat clients (more than 1 task)
        foreach ($clientCounts as $count) {
            if ($count > 1) {
                $analysis['repeat_clients']++;
            }
        }
        
        // Client concentration analysis
        arsort($clientCounts);
        $analysis['client_concentration'] = array_slice($clientCounts, 0, 5, true);
        
        // Average potential score
        if (!empty($clientScores)) {
            $analysis['client_potential_score'] = round(array_sum($clientScores) / count($clientScores), 2);
        }

        return $analysis;
    }

    /**
     * Extract task quality metrics
     */
    private function extract_task_quality($tasks)
    {
        $quality = [
            'special_remarks_tasks' => 0,
            'next_step_confirmed' => 0,
            'proposal_required' => 0,
            'closing_timeline_set' => 0,
            'action_taken' => 0,
            'quality_score' => 0
        ];

        foreach ($tasks as $task) {
            if (!empty(trim($task->special_remarks ?? ''))) $quality['special_remarks_tasks']++;
            if (!empty(trim($task->next_step_confirmation ?? ''))) $quality['next_step_confirmed']++;
            if (!empty(trim($task->proposal_require ?? ''))) $quality['proposal_required']++;
            if (!empty(trim($task->closing_timeline ?? ''))) $quality['closing_timeline_set']++;
            if (strtolower($task->actontaken ?? '') == 'yes') $quality['action_taken']++;
        }

        $total = count($tasks);
        if ($total > 0) {
            $qualityScore = (
                ($quality['special_remarks_tasks'] / $total * 30) +
                ($quality['next_step_confirmed'] / $total * 25) +
                ($quality['proposal_required'] / $total * 20) +
                ($quality['closing_timeline_set'] / $total * 15) +
                ($quality['action_taken'] / $total * 10)
            );
            $quality['quality_score'] = round($qualityScore, 2);
        }

        return $quality;
    }

    /**
     * Extract status analysis
     */
    private function extract_status_analysis($tasks)
    {
        $statuses = [
            'open' => 0,
            'positive_nap' => 0,
            'negative' => 0,
            'proposal' => 0,
            'others' => 0,
            'status_changes' => 0,
            'status_distribution' => []
        ];

        foreach ($tasks as $task) {
            $status = strtoupper($task->current_status ?? '');
            $taskTimeStatus = strtoupper($task->task_time_status ?? '');
            
            // Count status types
            if ($status == 'OPEN') {
                $statuses['open']++;
            } elseif (strpos($status, 'POSITIVE') !== false) {
                $statuses['positive_nap']++;
            } elseif (strpos($status, 'NEGATIVE') !== false) {
                $statuses['negative']++;
            } elseif ($status == 'PROPOSAL') {
                $statuses['proposal']++;
            } else {
                $statuses['others']++;
            }
            
            // Track status distribution
            if (!empty($status)) {
                if (!isset($statuses['status_distribution'][$status])) {
                    $statuses['status_distribution'][$status] = 0;
                }
                $statuses['status_distribution'][$status]++;
            }
            
            // Check for status changes
            if (!empty($taskTimeStatus) && $taskTimeStatus != $status) {
                $statuses['status_changes']++;
            }
        }

        return $statuses;
    }

    /**
     * Analyze companies worked with
     */
    private function analyze_companies($companyTasks)
    {
        $analysis = [
            'total_companies' => count($companyTasks),
            'top_companies' => [],
            'company_engagement' => [],
            'diversity_score' => 0
        ];

        foreach ($companyTasks as $companyName => $tasks) {
            $analysis['company_engagement'][$companyName] = count($tasks);
        }

        // Sort by engagement
        arsort($analysis['company_engagement']);
        $analysis['top_companies'] = array_slice($analysis['company_engagement'], 0, 5, true);

        // Calculate diversity score (more companies = higher score)
        if ($analysis['total_companies'] > 0) {
            $totalTasks = array_sum($analysis['company_engagement']);
            $averageTasksPerCompany = $totalTasks / $analysis['total_companies'];
            
            // Lower concentration = higher diversity
            $maxCompany = max($analysis['company_engagement']);
            $concentration = $maxCompany / $totalTasks;
            $analysis['diversity_score'] = round((1 - $concentration) * 100, 2);
        }

        return $analysis;
    }

    /**
     * Analyze remarks
     */
    private function analyze_remarks($tasks)
    {
        $remarks = [
            'total_remarks' => 0,
            'common_keywords' => [],
            'sentiment_score' => 0,
            'remark_length_avg' => 0,
            'remark_categories' => [
                'meeting_outcome' => 0,
                'next_steps' => 0,
                'client_feedback' => 0,
                'issues_notes' => 0,
                'others' => 0
            ]
        ];

        $allRemarks = '';
        $remarkLengths = [];
        $positiveWords = ['good', 'great', 'excellent', 'positive', 'successful', 'yes', 'confirmed', 'agreed'];
        $negativeWords = ['no', 'failed', 'negative', 'rejected', 'canceled', 'postponed', 'issue', 'problem'];

        foreach ($tasks as $task) {
            $remark = trim($task->remarks ?? '');
            if (!empty($remark)) {
                $remarks['total_remarks']++;
                $allRemarks .= ' ' . strtolower($remark);
                $remarkLengths[] = strlen($remark);
                
                // Categorize remarks
                $lowerRemark = strtolower($remark);
                if (strpos($lowerRemark, 'meeting') !== false || 
                    strpos($lowerRemark, 'close') !== false) {
                    $remarks['remark_categories']['meeting_outcome']++;
                } elseif (strpos($lowerRemark, 'next') !== false || 
                         strpos($lowerRemark, 'follow') !== false) {
                    $remarks['remark_categories']['next_steps']++;
                } elseif (strpos($lowerRemark, 'client') !== false || 
                         strpos($lowerRemark, 'customer') !== false) {
                    $remarks['remark_categories']['client_feedback']++;
                } elseif (strpos($lowerRemark, 'issue') !== false || 
                         strpos($lowerRemark, 'problem') !== false) {
                    $remarks['remark_categories']['issues_notes']++;
                } else {
                    $remarks['remark_categories']['others']++;
                }
            }
        }

        // Extract common keywords
        $words = str_word_count($allRemarks, 1);
        $wordCounts = array_count_values($words);
        arsort($wordCounts);
        $remarks['common_keywords'] = array_slice($wordCounts, 0, 10, true);

        // Calculate average remark length
        if (!empty($remarkLengths)) {
            $remarks['remark_length_avg'] = round(array_sum($remarkLengths) / count($remarkLengths), 2);
        }

        // Calculate sentiment score
        $positiveCount = 0;
        $negativeCount = 0;
        foreach ($words as $word) {
            if (in_array($word, $positiveWords)) $positiveCount++;
            if (in_array($word, $negativeWords)) $negativeCount++;
        }
        
        $totalWords = count($words);
        if ($totalWords > 0) {
            $remarks['sentiment_score'] = round((($positiveCount - $negativeCount) / $totalWords) * 100, 2);
        }

        return $remarks;
    }

    /**
     * Analyze meeting types
     */
    private function analyze_mtypes($tasks)
    {
        $mtypes = [];
        foreach ($tasks as $task) {
            $mtype = strtoupper(trim($task->mtype ?? ''));
            if (!empty($mtype)) {
                if (!isset($mtypes[$mtype])) {
                    $mtypes[$mtype] = 0;
                }
                $mtypes[$mtype]++;
            }
        }
        
        arsort($mtypes);
        return $mtypes;
    }

    /**
     * Analyze time metrics
     */
    private function analyze_time_metrics($tasks)
    {
        $timeMetrics = [
            'total_time_spent' => 0,
            'avg_time_per_task' => 0,
            'late_tasks' => 0,
            'on_time_tasks' => 0,
            'time_efficiency' => 0
        ];

        $timeValues = [];
        $scheduledCount = 0;

        foreach ($tasks as $task) {
            // Analyze time taken
            $timeTaken = $task->total_time_taken ?? '';
            if (!empty($timeTaken)) {
                $timeInSeconds = $this->parse_time_to_seconds($timeTaken);
                if ($timeInSeconds > 0) {
                    $timeValues[] = $timeInSeconds;
                }
            }
            
            // Check for late tasks
            $lateRemarks = $task->late_remarks_message ?? '';
            if (!empty(trim($lateRemarks))) {
                $timeMetrics['late_tasks']++;
            } else {
                $timeMetrics['on_time_tasks']++;
            }
            
            // Count scheduled tasks
            if (strpos(strtolower($task->task_name ?? ''), 'scheduled') !== false) {
                $scheduledCount++;
            }
        }

        // Calculate total and average time
        if (!empty($timeValues)) {
            $timeMetrics['total_time_spent'] = array_sum($timeValues);
            $timeMetrics['avg_time_per_task'] = round(array_sum($timeValues) / count($timeValues), 2);
        }

        // Calculate time efficiency
        $totalTasks = count($tasks);
        if ($totalTasks > 0) {
            $completionRate = $this->calculate_completion_rate($tasks);
            $onTimeRate = ($timeMetrics['on_time_tasks'] / $totalTasks) * 100;
            $timeMetrics['time_efficiency'] = round(($completionRate * 0.6) + ($onTimeRate * 0.4), 2);
        }

        return $timeMetrics;
    }

    /**
     * Parse time string to seconds
     */
    private function parse_time_to_seconds($timeString)
    {
        $seconds = 0;
        
        // Handle format like "1 hours 42 minutes and 2 seconds"
        if (preg_match('/(\d+)\s*hours?/', $timeString, $matches)) {
            $seconds += intval($matches[1]) * 3600;
        }
        if (preg_match('/(\d+)\s*minutes?/', $timeString, $matches)) {
            $seconds += intval($matches[1]) * 60;
        }
        if (preg_match('/(\d+)\s*seconds?/', $timeString, $matches)) {
            $seconds += intval($matches[1]);
        }
        
        return $seconds;
    }

    /**
     * Prepare comprehensive analysis data
     */
    private function prepare_analysis_data($taskData, $startDate, $endDate)
    {
        // Calculate team statistics
        $teamStats = $this->calculate_team_statistics($taskData);

        // Prepare user details
        $users = $this->prepare_user_details($taskData);

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
     */
    private function calculate_team_statistics($taskData)
    {
        if (empty($taskData)) {
            return $this->get_empty_team_stats();
        }

        $stats = [
            'total_tasks' => 0,
            'total_completed' => 0,
            'total_pending' => 0,
            'total_users' => count($taskData),
            'average_completion_rate' => 0,
            'total_efficiency_score' => 0,
            'total_meetings' => 0,
            'total_clients' => 0,
            'total_companies' => 0,
            'total_status_changes' => 0,
            'overall_quality_score' => 0,
            'top_performers' => [],
            'needs_attention' => [],
            'sales_insights' => []
        ];

        $completionRates = [];
        $efficiencyScores = [];
        $qualityScores = [];
        $allClients = [];
        $allCompanies = [];

        foreach ($taskData as $user) {
            // Aggregate totals
            $stats['total_tasks'] += $user['total_task'];
            $stats['total_completed'] += $user['total_complete_task'];
            $stats['total_pending'] += $user['total_pending_task'];
            $stats['total_meetings'] += $user['meeting_statistics']['total_meetings'];
            $stats['total_clients'] += $user['client_analysis']['total_unique_clients'];
            $stats['total_status_changes'] += $user['status_analysis']['status_changes'];
            
            // Collect clients and companies
            $allClients = array_merge($allClients, array_keys($user['sales_metrics']['client_types'] ?? []));
            $allCompanies = array_merge($allCompanies, array_keys($user['company_analysis']['company_engagement'] ?? []));
            
            // Track scores
            $completionRates[] = $user['completion_rate'];
            $efficiencyScores[] = $user['task_efficiency'];
            $qualityScores[] = $user['task_quality']['quality_score'];

            // Identify top performers
            if ($user['completion_rate'] > 85 && $user['task_efficiency'] > 80) {
                $stats['top_performers'][] = [
                    'user_id' => $user['user_id'],
                    'username' => $user['username'],
                    'completion_rate' => $user['completion_rate'],
                    'efficiency' => $user['task_efficiency'],
                    'quality_score' => $user['task_quality']['quality_score']
                ];
            }

            // Identify users needing attention
            if ($user['completion_rate'] < 50 || $user['task_efficiency'] < 60) {
                $stats['needs_attention'][] = [
                    'user_id' => $user['user_id'],
                    'username' => $user['username'],
                    'completion_rate' => $user['completion_rate'],
                    'efficiency' => $user['task_efficiency'],
                    'pending_tasks' => $user['total_pending_task']
                ];
            }
        }

        // Calculate averages
        if (!empty($completionRates)) {
            $stats['average_completion_rate'] = round(array_sum($completionRates) / count($completionRates), 2);
            $stats['total_efficiency_score'] = round(array_sum($efficiencyScores) / count($efficiencyScores), 2);
            $stats['overall_quality_score'] = round(array_sum($qualityScores) / count($qualityScores), 2);
        }

        // Remove duplicates and count unique clients/companies
        $stats['total_clients'] = count(array_unique($allClients));
        $stats['total_companies'] = count(array_unique($allCompanies));

        // Generate sales insights
        $stats['sales_insights'] = $this->generate_sales_insights($taskData);

        return $stats;
    }

    /**
     * Generate sales insights from team data
     */
    private function generate_sales_insights($taskData)
    {
        $insights = [];
        
        if (empty($taskData)) {
            return $insights;
        }

        // Calculate overall metrics
        $totalTasks = 0;
        $totalMeetings = 0;
        $successfulMeetings = 0;
        $proposalMeetings = 0;
        $totalPotentialClients = 0;
        $totalHighValueClients = 0;

        foreach ($taskData as $user) {
            $totalTasks += $user['total_task'];
            $totalMeetings += $user['meeting_statistics']['total_meetings'];
            $successfulMeetings += $user['meeting_statistics']['successful_meetings'];
            $proposalMeetings += $user['meeting_statistics']['proposal_meetings'];
            $totalPotentialClients += $user['sales_metrics']['potential_clients'];
            $totalHighValueClients += $user['client_analysis']['high_value_clients'];
        }

        // Generate insights
        if ($totalMeetings > 0) {
            $successRate = round(($successfulMeetings / $totalMeetings) * 100, 2);
            $insights[] = "Overall meeting success rate: {$successRate}%";
            
            if ($successRate > 70) {
                $insights[] = "Excellent meeting success rate - team is effectively closing meetings";
            } elseif ($successRate > 50) {
                $insights[] = "Good meeting success rate - room for improvement in follow-ups";
            } else {
                $insights[] = "Meeting success rate needs improvement - focus on preparation and qualification";
            }
        }

        if ($proposalMeetings > 0) {
            $proposalRate = round(($proposalMeetings / $totalMeetings) * 100, 2);
            $insights[] = "Proposal generation rate: {$proposalRate}% of meetings lead to proposals";
        }

        if ($totalPotentialClients > 0) {
            $insights[] = "Total potential clients identified: {$totalPotentialClients}";
        }

        if ($totalHighValueClients > 0) {
            $insights[] = "High-value clients engaged: {$totalHighValueClients}";
        }

        // Client concentration insight
        $allClients = [];
        foreach ($taskData as $user) {
            foreach ($user['client_analysis']['client_concentration'] as $client => $count) {
                if (!isset($allClients[$client])) {
                    $allClients[$client] = 0;
                }
                $allClients[$client] += $count;
            }
        }
        
        arsort($allClients);
        $topClients = array_slice($allClients, 0, 3, true);
        if (!empty($topClients)) {
            $topClientNames = implode(', ', array_keys($topClients));
            $insights[] = "Top 3 most engaged clients: {$topClientNames}";
        }

        return $insights;
    }

    /**
     * Get empty team statistics structure
     */
    private function get_empty_team_stats()
    {
        return [
            'total_tasks' => 0,
            'total_completed' => 0,
            'total_pending' => 0,
            'total_users' => 0,
            'average_completion_rate' => 0,
            'total_efficiency_score' => 0,
            'total_meetings' => 0,
            'total_clients' => 0,
            'total_companies' => 0,
            'total_status_changes' => 0,
            'overall_quality_score' => 0,
            'top_performers' => [],
            'needs_attention' => [],
            'sales_insights' => []
        ];
    }

    /**
     * Prepare user details with task data
     */
    private function prepare_user_details($taskData)
    {
        $users = [];

        foreach ($taskData as $task) {
            $users[] = [
                'user_id' => $task['user_id'],
                'username' => $task['username'],
                'task_statistics' => [
                    'total' => $task['total_task'],
                    'completed' => $task['total_complete_task'],
                    'pending' => $task['total_pending_task'],
                    'completion_rate' => $task['completion_rate'],
                    'efficiency_score' => $task['task_efficiency']
                ],
                'meeting_analysis' => $task['meeting_statistics'],
                'sales_metrics' => $task['sales_metrics'],
                'client_analysis' => $task['client_analysis'],
                'task_quality' => $task['task_quality'],
                'status_analysis' => $task['status_analysis'],
                'company_analysis' => $task['company_analysis'],
                'company_task_data' => $task['company_task_data'],
                'positive_companies' => $task['positive_companies'],
                'negative_companies' => $task['negative_companies'],
                'to_work_companies' => $task['to_work_companies'],
                'overall_companies' => $task['overall_companies'],
                'remarks_analysis' => $task['remarks_analysis'],
                'mtype_analysis' => $task['mtype_analysis'],
                'time_analysis' => $task['time_analysis'],
                'performance_metrics' => $this->calculate_performance_metrics($task),
                'rank' => 0 // Will be calculated later
            ];
        }

        // Calculate ranks
        $users = $this->calculate_user_ranks($users);

        return $users;
    }

    /**
     * Calculate performance metrics
     */
    private function calculate_performance_metrics($task)
    {
        return [
            'productivity_score' => $task['completion_rate'],
            'quality_score' => $task['task_quality']['quality_score'],
            'sales_score' => $this->calculate_sales_score($task),
            'client_management_score' => $this->calculate_client_management_score($task),
            'overall_performance' => $this->calculate_overall_performance($task)
        ];
    }

    /**
     * Calculate sales score
     */
    private function calculate_sales_score($task)
    {
        $meetingSuccess = $task['meeting_statistics']['successful_meetings'] > 0 ? 
            ($task['meeting_statistics']['successful_meetings'] / max($task['meeting_statistics']['total_meetings'], 1)) * 100 : 0;
        
        $proposalRate = $task['meeting_statistics']['proposal_meetings'] > 0 ? 
            ($task['meeting_statistics']['proposal_meetings'] / max($task['meeting_statistics']['total_meetings'], 1)) * 100 : 0;
        
        $potentialClientScore = ($task['sales_metrics']['potential_clients'] / max($task['sales_metrics']['total_clients'], 1)) * 100;
        
        return round(($meetingSuccess * 0.4) + ($proposalRate * 0.3) + ($potentialClientScore * 0.3), 2);
    }

    /**
     * Calculate client management score
     */
    private function calculate_client_management_score($task)
    {
        $clientEngagement = $task['client_analysis']['client_engagement_rate'];
        $repeatClients = $task['client_analysis']['repeat_clients'] > 0 ? 
            ($task['client_analysis']['repeat_clients'] / max($task['client_analysis']['total_unique_clients'], 1)) * 100 : 0;
        
        $highValueClients = $task['client_analysis']['high_value_clients'] > 0 ? 
            ($task['client_analysis']['high_value_clients'] / max($task['client_analysis']['total_unique_clients'], 1)) * 100 : 0;
        
        return round(($clientEngagement * 0.4) + ($repeatClients * 0.3) + ($highValueClients * 0.3), 2);
    }

    /**
     * Calculate overall performance
     */
    private function calculate_overall_performance($task)
    {
        $productivity = $task['completion_rate'];
        $quality = $task['task_quality']['quality_score'];
        $sales = $this->calculate_sales_score($task);
        $clientManagement = $this->calculate_client_management_score($task);
        
        // Weighted average
        return round(
            ($productivity * 0.3) + 
            ($quality * 0.25) + 
            ($sales * 0.25) + 
            ($clientManagement * 0.2), 
            2
        );
    }

    /**
     * Calculate user ranks
     */
    private function calculate_user_ranks($users)
    {
        usort($users, function($a, $b) {
            return $b['performance_metrics']['overall_performance'] - 
                   $a['performance_metrics']['overall_performance'];
        });

        $rank = 1;
        foreach ($users as $key => &$user) {
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
     */
    private function prepare_summary_statistics($users, $teamStats)
    {
        return [
            'total_users_analyzed' => count($users),
            'total_task_volume' => $teamStats['total_tasks'],
            'overall_completion_rate' => $teamStats['average_completion_rate'],
            'overall_efficiency' => $teamStats['total_efficiency_score'],
            'overall_quality' => $teamStats['overall_quality_score'],
            'meeting_metrics' => [
                'total_meetings' => $teamStats['total_meetings'],
                'success_rate' => $teamStats['total_meetings'] > 0 ? 
                    round(($teamStats['sales_insights']['successful_meetings'] ?? 0) / $teamStats['total_meetings'] * 100, 2) : 0
            ],
            'client_metrics' => [
                'total_clients' => $teamStats['total_clients'],
                'total_companies' => $teamStats['total_companies']
            ],
            'team_productivity' => $this->calculate_team_productivity($users),
            'key_insights' => $this->generate_key_insights($users, $teamStats)
        ];
    }

    /**
     * Calculate team productivity
     */
    private function calculate_team_productivity($users)
    {
        if (empty($users)) {
            return ['average_tasks_per_user' => 0, 'average_completed_per_user' => 0];
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
     * Generate key insights
     */
    private function generate_key_insights($users, $teamStats)
    {
        $insights = [];

        if (empty($users)) {
            return ['No user data available for analysis'];
        }

        // Performance insights
        if ($teamStats['average_completion_rate'] > 85) {
            $insights[] = "Excellent team completion rate: {$teamStats['average_completion_rate']}%";
        } elseif ($teamStats['average_completion_rate'] > 70) {
            $insights[] = "Good team completion rate: {$teamStats['average_completion_rate']}%";
        } else {
            $insights[] = "Team completion rate needs improvement: {$teamStats['average_completion_rate']}%";
        }

        // Meeting insights
        if ($teamStats['total_meetings'] > 0) {
            $insights[] = "Total meetings conducted: {$teamStats['total_meetings']}";
        }

        // Client insights
        if ($teamStats['total_clients'] > 0) {
            $clientsPerUser = round($teamStats['total_clients'] / count($users), 2);
            $insights[] = "Average clients per user: {$clientsPerUser}";
        }

        // Top performers insight
        if (!empty($teamStats['top_performers'])) {
            $topCount = count($teamStats['top_performers']);
            $insights[] = "{$topCount} top performer(s) identified with completion rate > 85%";
        }

        // Attention needed insight
        if (!empty($teamStats['needs_attention'])) {
            $attentionCount = count($teamStats['needs_attention']);
            $insights[] = "{$attentionCount} user(s) need attention with completion rate < 50%";
        }

        return $insights;
    }

    /**
     * Calculate median
     */
    private function calculate_median($numbers)
    {
        if (empty($numbers)) return 0;
        sort($numbers);
        $count = count($numbers);
        $middle = floor($count / 2);
        return $count % 2 == 0 ? 
            round(($numbers[$middle - 1] + $numbers[$middle]) / 2, 2) : 
            round($numbers[$middle], 2);
    }

    /**
     * Calculate standard deviation
     */
    private function calculate_standard_deviation($numbers)
    {
        if (empty($numbers) || count($numbers) < 2) return 0;
        $mean = array_sum($numbers) / count($numbers);
        $sum = 0;
        foreach ($numbers as $number) {
            $sum += pow($number - $mean, 2);
        }
        return round(sqrt($sum / (count($numbers) - 1)), 2);
    }

    /**
     * Extract specific user from query
     */
    public function extract_specific_user_request($message, $users)
    {
        if (empty($message) || empty($users)) {
            return null;
        }

        $lowerMessage = strtolower(trim($message));

        foreach ($users as $user) {
            $username = strtolower($user['username'] ?? '');
            
            if (strpos($lowerMessage, $username) !== false) {
                return $user;
            }

            $nameParts = explode(' ', $username);
            foreach ($nameParts as $part) {
                if (strlen($part) > 3 && strpos($lowerMessage, $part) !== false) {
                    return $user;
                }
            }

            $userId = (string)($user['user_id'] ?? '');
            if (strpos($lowerMessage, $userId) !== false) {
                return $user;
            }
        }

        return null;
    }

    /**
     * Generate AI prompt for specific user analysis
     */
    public function generate_specific_user_analysis_prompt($message, $user, $teamData)
    {
        $formattedData = "SALES TASK ANALYSIS - {$user['username']} (ID: {$user['user_id']})\n\n";
        
        $formattedData .= "PERFORMANCE OVERVIEW:\n";
        $formattedData .= "- Overall Rank: #{$user['rank']} out of {$teamData['team_statistics']['total_users']} users\n";
        $formattedData .= "- Task Completion Rate: {$user['task_statistics']['completion_rate']}%\n";
        $formattedData .= "- Task Efficiency Score: {$user['task_statistics']['efficiency_score']}/100\n";
        $formattedData .= "- Overall Performance Score: {$user['performance_metrics']['overall_performance']}/100\n\n";
        
        $formattedData .= "TASK STATISTICS:\n";
        $formattedData .= "- Total Tasks: {$user['task_statistics']['total']}\n";
        $formattedData .= "- Completed Tasks: {$user['task_statistics']['completed']}\n";
        $formattedData .= "- Pending Tasks: {$user['task_statistics']['pending']}\n\n";
        
        $formattedData .= "MEETING PERFORMANCE:\n";
        $meeting = $user['meeting_analysis'];
        $formattedData .= "- Total Meetings: {$meeting['total_meetings']}\n";
        $formattedData .= "- Successful Meetings: {$meeting['successful_meetings']}\n";
        $formattedData .= "- Failed Meetings: {$meeting['failed_meetings']}\n";
        $formattedData .= "- Positive Meetings: {$meeting['positive_meetings']}\n";
        $formattedData .= "- Proposal Meetings: {$meeting['proposal_meetings']}\n";
        $formattedData .= "- No RP Meetings: {$meeting['no_rp_meetings']}\n\n";
        
        $formattedData .= "SALES METRICS:\n";
        $sales = $user['sales_metrics'];
        $formattedData .= "- Total Clients: {$sales['total_clients']}\n";
        $formattedData .= "- Potential Clients: {$sales['potential_clients']}\n";
        $formattedData .= "- Top Spenders: {$sales['top_spenders']}\n";
        $formattedData .= "- Key Companies: {$sales['key_companies']}\n";
        $formattedData .= "- Priority Clients: {$sales['priority_clients']}\n";
        $formattedData .= "- PK Clients: {$sales['pk_clients']}\n\n";
        
        $formattedData .= "CLIENT ANALYSIS:\n";
        $client = $user['client_analysis'];
        $formattedData .= "- Unique Clients: {$client['total_unique_clients']}\n";
        $formattedData .= "- High Value Clients: {$client['high_value_clients']}\n";
        $formattedData .= "- Client Engagement Rate: {$client['client_engagement_rate']}\n";
        $formattedData .= "- Repeat Clients: {$client['repeat_clients']}\n";
        $formattedData .= "- Client Potential Score: {$client['client_potential_score']}/10\n\n";
        
        $formattedData .= "TASK QUALITY METRICS:\n";
        $quality = $user['task_quality'];
        $formattedData .= "- Special Remarks: {$quality['special_remarks_tasks']}\n";
        $formattedData .= "- Next Step Confirmed: {$quality['next_step_confirmed']}\n";
        $formattedData .= "- Proposal Required: {$quality['proposal_required']}\n";
        $formattedData .= "- Closing Timeline Set: {$quality['closing_timeline_set']}\n";
        $formattedData .= "- Action Taken: {$quality['action_taken']}\n";
        $formattedData .= "- Overall Quality Score: {$quality['quality_score']}/100\n\n";
        
        $formattedData .= "STATUS ANALYSIS:\n";
        $status = $user['status_analysis'];
        $formattedData .= "- Open Tasks: {$status['open']}\n";
        $formattedData .= "- Positive-NAP: {$status['positive_nap']}\n";
        $formattedData .= "- Negative: {$status['negative']}\n";
        $formattedData .= "- Proposal: {$status['proposal']}\n";
        $formattedData .= "- Status Changes: {$status['status_changes']}\n\n";
        
        $formattedData .= "COMPANY PERFORMANCE ANALYSIS:\n";
        
        if (!empty($user['positive_companies'])) {
            $formattedData .= "Top Positive Companies:\n";
            foreach (array_slice($user['positive_companies'], 0, 3) as $companyName => $companyData) {
                $formattedData .= "- {$companyName}: {$companyData['positive_tasks']} positive tasks ({$companyData['positive_ratio']}% ratio)\n";
            }
        }
        
        if (!empty($user['negative_companies'])) {
            $formattedData .= "Top Negative Companies:\n";
            foreach (array_slice($user['negative_companies'], 0, 3) as $companyName => $companyData) {
                $formattedData .= "- {$companyName}: {$companyData['negative_tasks']} negative tasks ({$companyData['negative_ratio']}% ratio)\n";
            }
        }
        
        if (!empty($user['to_work_companies'])) {
            $formattedData .= "Companies Needing Attention:\n";
            foreach (array_slice($user['to_work_companies'], 0, 3) as $companyName => $companyData) {
                $formattedData .= "- {$companyName}: {$companyData['pending_tasks']} pending tasks\n";
            }
        }
        
        $formattedData .= "\nCOMPANY ANALYSIS:\n";
        $company = $user['company_analysis'];
        $formattedData .= "- Total Companies: {$company['total_companies']}\n";
        $formattedData .= "- Company Diversity Score: {$company['diversity_score']}/100\n";
        $formattedData .= "- Top Companies: " . implode(', ', array_keys($company['top_companies'])) . "\n\n";
        
        $formattedData .= "REMARKS ANALYSIS:\n";
        $remarks = $user['remarks_analysis'];
        $formattedData .= "- Total Remarks: {$remarks['total_remarks']}\n";
        $formattedData .= "- Average Remark Length: {$remarks['remark_length_avg']} characters\n";
        $formattedData .= "- Sentiment Score: {$remarks['sentiment_score']}/100\n";
        $formattedData .= "- Common Keywords: " . implode(', ', array_keys($remarks['common_keywords'])) . "\n\n";
        
        $formattedData .= "TIME ANALYSIS:\n";
        $time = $user['time_analysis'];
        $formattedData .= "- Late Tasks: {$time['late_tasks']}\n";
        $formattedData .= "- On-time Tasks: {$time['on_time_tasks']}\n";
        $formattedData .= "- Time Efficiency: {$time['time_efficiency']}/100\n\n";
        
        $formattedData .= "PERFORMANCE METRICS:\n";
        $metrics = $user['performance_metrics'];
        $formattedData .= "- Productivity Score: {$metrics['productivity_score']}/100\n";
        $formattedData .= "- Quality Score: {$metrics['quality_score']}/100\n";
        $formattedData .= "- Sales Score: {$metrics['sales_score']}/100\n";
        $formattedData .= "- Client Management Score: {$metrics['client_management_score']}/100\n";
        $formattedData .= "- Overall Performance: {$metrics['overall_performance']}/100\n\n";
        
        $formattedData .= "TEAM CONTEXT:\n";
        $formattedData .= "- Team Size: {$teamData['team_statistics']['total_users']} users\n";
        $formattedData .= "- Team Average Completion: {$teamData['team_statistics']['average_completion_rate']}%\n";
        $formattedData .= "- Team Average Efficiency: {$teamData['team_statistics']['total_efficiency_score']}/100\n";
        
        // Add additional company analysis for this user
        $additionalAnalysis = $teamData['additional_company_analysis'] ?? [];
        if (!empty($additionalAnalysis)) {
            $formattedData .= "\nADDITIONAL COMPANY ANALYSIS FOR USER:\n";
            
            // Find user's companies in the additional analysis
            $userPositiveCompanies = $this->filter_user_companies($additionalAnalysis['top_positive_companies'] ?? [], $user['user_id']);
            if (!empty($userPositiveCompanies)) {
                $formattedData .= "User's Top Positive Companies:\n";
                foreach (array_slice($userPositiveCompanies, 0, 3) as $company) {
                    $formattedData .= "- {$company['compname']}: {$company['total_count']} positive tasks\n";
                }
            }
            
            $userNegativeCompanies = $this->filter_user_companies($additionalAnalysis['top_negative_companies'] ?? [], $user['user_id']);
            if (!empty($userNegativeCompanies)) {
                $formattedData .= "\nUser's Top Negative Companies:\n";
                foreach (array_slice($userNegativeCompanies, 0, 3) as $company) {
                    $formattedData .= "- {$company['compname']}: {$company['total_count']} negative tasks\n";
                }
            }
        }
        
        $prompt = "You are a sales analytics AI specializing in task performance analysis. ";
        $prompt .= "Analyze the following user sales task data and provide detailed, actionable insights:\n\n";
        $prompt .= $formattedData . "\n\n";
        $prompt .= "User Query: {$message}\n\n";
        $prompt .= "Provide a comprehensive sales performance analysis with these sections:\n";
        $prompt .= "1. PERFORMANCE OVERVIEW: Brief summary of sales performance\n";
        $prompt .= "2. STRENGTHS IDENTIFIED: Key areas where the user excels in sales\n";
        $prompt .= "3. AREAS FOR IMPROVEMENT: Specific sales metrics that need attention\n";
        $prompt .= "4. MEETING EFFECTIVENESS: Analysis of meeting success and quality\n";
        $prompt .= "5. CLIENT MANAGEMENT: Effectiveness in client engagement and retention\n";
        $prompt .= "6. SALES PIPELINE HEALTH: Analysis of potential clients and conversions\n";
        $prompt .= "7. TASK QUALITY ASSESSMENT: Quality of follow-ups and documentation\n";
        $prompt .= "8. COMPANY PERFORMANCE ANALYSIS: Analysis of company engagement and performance\n";
        $prompt .= "9. COMPANY-SPECIFIC INSIGHTS: Analysis of top positive, negative, and high-volume companies\n";
        $prompt .= "10. ACTIONABLE RECOMMENDATIONS: Specific sales improvement suggestions\n";
        $prompt .= "11. COMPARATIVE ANALYSIS: How user compares to team averages\n\n";
        
        $prompt .= "Format: Use clear sections with bullet points. Include specific numbers and percentages from the data.";
        
        return $prompt;
    }

    /**
     * Filter companies by user (placeholder - needs implementation based on your data structure)
     */
    private function filter_user_companies($companies, $userId)
    {
        // This is a placeholder - you need to implement logic to filter companies by user
        // based on your actual data structure
        return array_slice($companies, 0, 5);
    }

    /**
     * Generate AI prompt for team analysis
     */
    public function generate_team_analysis_prompt($message, $analysisData)
    {
        $teamStats = $analysisData['team_statistics'];
        $summary = $analysisData['summary'];
        $additionalAnalysis = $analysisData['additional_company_analysis'] ?? [];
        
        $formattedData = "TEAM SALES TASK ANALYSIS REPORT\n";
        $formattedData .= "Period: {$analysisData['period']}\n";
        $formattedData .= "Analysis Date: {$analysisData['analysis_date']}\n\n";
        
        $formattedData .= "EXECUTIVE SUMMARY:\n";
        $formattedData .= "- Total Users Analyzed: {$summary['total_users_analyzed']}\n";
        $formattedData .= "- Total Task Volume: {$summary['total_task_volume']} tasks\n";
        $formattedData .= "- Overall Completion Rate: {$summary['overall_completion_rate']}%\n";
        $formattedData .= "- Team Efficiency Score: {$summary['overall_efficiency']}/100\n";
        $formattedData .= "- Overall Quality Score: {$summary['overall_quality']}/100\n\n";
        
        $formattedData .= "DETAILED STATISTICS:\n";
        $formattedData .= "- Total Tasks Completed: {$teamStats['total_completed']}\n";
        $formattedData .= "- Total Tasks Pending: {$teamStats['total_pending']}\n";
        $formattedData .= "- Total Meetings: {$teamStats['total_meetings']}\n";
        $formattedData .= "- Total Clients: {$teamStats['total_clients']}\n";
        $formattedData .= "- Total Companies: {$teamStats['total_companies']}\n";
        $formattedData .= "- Total Status Changes: {$teamStats['total_status_changes']}\n\n";
        
        $formattedData .= "ADDITIONAL COMPANY ANALYSIS:\n\n";
        
        // Top Positive Companies
        if (!empty($additionalAnalysis['top_positive_companies'])) {
            $formattedData .= "Top 3 Positive Companies:\n";
            $count = 0;
            foreach ($additionalAnalysis['top_positive_companies'] as $company) {
                if ($count >= 3) break;
                $formattedData .= "- {$company['compname']} (ID: {$company['cid']}): ";
                $formattedData .= "{$company['total_count']} positive tasks, ";
                $formattedData .= "Recent Task: {$company['task_name']}, Status: {$company['current_status']}\n";
                $count++;
            }
            $formattedData .= "\n";
        }
        
        // Top Negative Companies
        if (!empty($additionalAnalysis['top_negative_companies'])) {
            $formattedData .= "Top 3 Negative Companies:\n";
            $count = 0;
            foreach ($additionalAnalysis['top_negative_companies'] as $company) {
                if ($count >= 3) break;
                $formattedData .= "- {$company['compname']} (ID: {$company['cid']}): ";
                $formattedData .= "{$company['total_count']} negative tasks, ";
                $formattedData .= "Recent Task: {$company['task_name']}, Status: {$company['current_status']}\n";
                $count++;
            }
            $formattedData .= "\n";
        }
        
        // Top Companies by Task Volume
        if (!empty($additionalAnalysis['top_companies_by_tasks'])) {
            $formattedData .= "Top 3 Companies by Task Volume:\n";
            $count = 0;
            foreach ($additionalAnalysis['top_companies_by_tasks'] as $company) {
                if ($count >= 3) break;
                $formattedData .= "- {$company['compname']} (ID: {$company['cid']}): ";
                $formattedData .= "{$company['total_tasks']} total tasks, ";
                $formattedData .= "Recent Task: {$company['task_name']}\n";
                $count++;
            }
            $formattedData .= "\n";
        }
        
        // Top Re-meeting Companies
        if (!empty($additionalAnalysis['top_remeeting_companies'])) {
            $formattedData .= "Top 3 Re-meeting Companies:\n";
            $count = 0;
            foreach ($additionalAnalysis['top_remeeting_companies'] as $company) {
                if ($count >= 3) break;
                $formattedData .= "- {$company['compname']} (ID: {$company['cid']}): ";
                $formattedData .= "{$company['meeting_count']} meeting tasks, ";
                $formattedData .= "Recent Meeting: {$company['task_name']}\n";
                $count++;
            }
            $formattedData .= "\n";
        }
        
        // Top Fresh Companies
        if (!empty($additionalAnalysis['top_fresh_companies'])) {
            $formattedData .= "Top 3 Fresh Companies (Non-meeting tasks):\n";
            $count = 0;
            foreach ($additionalAnalysis['top_fresh_companies'] as $company) {
                if ($count >= 3) break;
                $formattedData .= "- {$company['compname']} (ID: {$company['cid']}): ";
                $formattedData .= "{$company['fresh_task_count']} fresh tasks, ";
                $formattedData .= "Recent Task: {$company['task_name']}\n";
                $count++;
            }
            $formattedData .= "\n";
        }
        
        $formattedData .= "PERFORMANCE DISTRIBUTION:\n";
        $formattedData .= "- Top Performers (>85% completion): " . count($teamStats['top_performers']) . " users\n";
        $formattedData .= "- Needs Attention (<50% completion): " . count($teamStats['needs_attention']) . " users\n\n";
        
        $formattedData .= "SALES INSIGHTS:\n";
        foreach ($teamStats['sales_insights'] as $insight) {
            $formattedData .= "- {$insight}\n";
        }
        $formattedData .= "\n";
        
        $formattedData .= "COMPANY PERFORMANCE HIGHLIGHTS:\n";
        
        // Get aggregated company data
        $allCompanies = [];
        foreach ($analysisData['users'] as $user) {
            foreach ($user['company_task_data'] ?? [] as $companyName => $companyData) {
                if (!isset($allCompanies[$companyName])) {
                    $allCompanies[$companyName] = [
                        'total_tasks' => 0,
                        'positive_tasks' => 0,
                        'negative_tasks' => 0
                    ];
                }
                $allCompanies[$companyName]['total_tasks'] += $companyData['total_tasks'] ?? 0;
                $allCompanies[$companyName]['positive_tasks'] += $companyData['positive_tasks'] ?? 0;
                $allCompanies[$companyName]['negative_tasks'] += $companyData['negative_tasks'] ?? 0;
            }
        }
        
        // Sort by total tasks
        uasort($allCompanies, function($a, $b) {
            return $b['total_tasks'] - $a['total_tasks'];
        });
        
        $formattedData .= "Top 5 Most Engaged Companies:\n";
        $count = 0;
        foreach ($allCompanies as $companyName => $data) {
            if ($count >= 5) break;
            $positiveRate = $data['total_tasks'] > 0 ? 
                round(($data['positive_tasks'] / $data['total_tasks']) * 100, 2) : 0;
            $formattedData .= "- {$companyName}: {$data['total_tasks']} total tasks, {$positiveRate}% positive rate\n";
            $count++;
        }
        
        $formattedData .= "\nPRODUCTIVITY METRICS:\n";
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
        
        $prompt = "You are a senior sales analytics AI. Analyze the following team sales task performance data ";
        $prompt .= "including detailed company analysis. Provide a comprehensive, executive-level sales report:\n\n";
        $prompt .= $formattedData . "\n\n";
        $prompt .= "User Query: {$message}\n\n";
        $prompt .= "Generate a detailed sales performance analysis report with these sections:\n";
        $prompt .= "1. EXECUTIVE SUMMARY: High-level overview of team sales performance\n";
        $prompt .= "2. TEAM PERFORMANCE ANALYSIS: Detailed analysis of key sales metrics\n";
        $prompt .= "3. MEETING EFFECTIVENESS: Analysis of meeting success rates and quality\n";
        $prompt .= "4. CLIENT ENGAGEMENT: Analysis of client relationships and retention\n";
        $prompt .= "5. SALES PIPELINE HEALTH: Status of potential clients and conversions\n";
        $prompt .= "6. COMPANY ENGAGEMENT ANALYSIS: Analysis of company relationships and engagement patterns\n";
        $prompt .= "7. COMPANY PERFORMANCE DEEP DIVE: Analysis of positive, negative, high-volume, remeeting, and fresh companies\n";
        $prompt .= "8. INDIVIDUAL PERFORMANCE HIGHLIGHTS: Top performers and areas of concern\n";
        $prompt .= "9. PRODUCTIVITY TRENDS: Patterns in task completion and efficiency\n";
        $prompt .= "10. ACTIONABLE RECOMMENDATIONS: Specific, measurable sales improvement actions\n";
        $prompt .= "11. RISK ASSESSMENT: Sales risks requiring immediate attention\n";
        $prompt .= "12. PERFORMANCE FORECAST: Sales projections based on current trends\n\n";
        
        $prompt .= "Format: Use professional sales report format with clear headings. ";
        $prompt .= "Include data-driven insights and specific sales recommendations. ";
        $prompt .= "For the company analysis section, focus on insights from the additional company data provided.";
        
        return $prompt;
    }

    /**
     * Prepare data for frontend display
     */
    public function prepare_frontend_data($analysisData, $specificUser = null)
    {
        $response = [
            'status' => 'empty',
            'message' => 'No data available for analysis',
            'tableData' => null,
            'chartData' => null,
            'companyTables' => $this->get_empty_company_tables(),
            'additionalCompanyTables' => $this->get_empty_additional_company_tables(),
            'summaryData' => [],
            'specificUserData' => null,
            'comparativeData' => null,
            'period' => $analysisData['period'] ?? 'N/A',
            'generated_at' => date('Y-m-d H:i:s')
        ];

        if (empty($analysisData['users']) || !is_array($analysisData['users'])) {
            return $response;
        }

        $response['tableData'] = $this->prepare_main_table_data($analysisData['users']);
        $response['chartData'] = $this->prepare_chart_data($analysisData);
        $response['summaryData'] = $this->prepare_summary_data($analysisData);
        
        // Prepare company tables based on user request
        if ($specificUser) {
            $userData = $this->find_user_data($analysisData['users'], $specificUser['user_id']);
            if ($userData) {
                $response['companyTables'] = $this->prepare_company_tables($userData);
                $response['specificUserData'] = $this->prepare_specific_user_frontend_data($userData, $analysisData);
                $response['comparativeData'] = $this->prepare_comparative_data($userData, $analysisData);
            }
        } else {
            // For team view, aggregate company data from all users
            $response['companyTables'] = $this->prepare_team_company_tables($analysisData['users']);
        }
        
        // Prepare additional company analysis tables
        if (!empty($analysisData['additional_company_analysis'])) {
            $response['additionalCompanyTables'] = $this->prepare_additional_company_tables(
                $analysisData['additional_company_analysis']
            );
        }
        
        $response['status'] = 'success';
        $response['message'] = 'Sales task analysis data prepared successfully';
        $response['total_users'] = count($analysisData['users']);
        $response['date_range'] = $analysisData['period'];

        return $response;
    }

    /**
     * Get empty company tables structure
     */
    private function get_empty_company_tables()
    {
        return [
            'positive_companies' => [
                'title' => 'Top 10 Positive Companies',
                'headers' => ['Company Name', 'Positive Tasks', 'Total Tasks', 'Positive Ratio', 'Key Task', 'Remarks'],
                'rows' => [],
                'empty_message' => 'No positive company data available',
                'sortable' => true,
                'paginate' => true,
                'items_per_page' => 10
            ],
            'negative_companies' => [
                'title' => 'Top 10 Negative Companies',
                'headers' => ['Company Name', 'Negative Tasks', 'Total Tasks', 'Negative Ratio', 'Key Task', 'Remarks'],
                'rows' => [],
                'empty_message' => 'No negative company data available',
                'sortable' => true,
                'paginate' => true,
                'items_per_page' => 10
            ],
            'to_work_companies' => [
                'title' => 'Top 10 Companies to Work On',
                'headers' => ['Company Name', 'Pending Tasks', 'Total Tasks', 'Work Ratio', 'Key Task', 'Remarks'],
                'rows' => [],
                'empty_message' => 'No companies need immediate attention',
                'sortable' => true,
                'paginate' => true,
                'items_per_page' => 10
            ],
            'overall_companies' => [
                'title' => 'Top 10 Overall Companies',
                'headers' => ['Company Name', 'Total Tasks', 'Engagement Score', 'Completion Rate', 'Key Task', 'Remarks'],
                'rows' => [],
                'empty_message' => 'No company data available',
                'sortable' => true,
                'paginate' => true,
                'items_per_page' => 10
            ]
        ];
    }

    /**
     * Get empty additional company tables structure
     */
    private function get_empty_additional_company_tables()
    {
        return [
            'top_positive_companies' => [
                'title' => 'Top 20 Positive Companies',
                'headers' => ['Company ID', 'Company Name', 'Task Name', 'Current Status', 'Remarks', 'Total Positive Tasks'],
                'rows' => [],
                'empty_message' => 'No positive company data available',
                'sortable' => true,
                'paginate' => true,
                'items_per_page' => 10
            ],
            'top_negative_companies' => [
                'title' => 'Top 20 Negative Companies',
                'headers' => ['Company ID', 'Company Name', 'Task Name', 'Current Status', 'Remarks', 'Total Negative Tasks'],
                'rows' => [],
                'empty_message' => 'No negative company data available',
                'sortable' => true,
                'paginate' => true,
                'items_per_page' => 10
            ],
            'top_companies_by_tasks' => [
                'title' => 'Top 20 Companies by Task Volume',
                'headers' => ['Company ID', 'Company Name', 'Task Name', 'Current Status', 'Remarks', 'Total Tasks'],
                'rows' => [],
                'empty_message' => 'No company task data available',
                'sortable' => true,
                'paginate' => true,
                'items_per_page' => 10
            ],
            'top_remeeting_companies' => [
                'title' => 'Top 20 Re-meeting Companies',
                'headers' => ['Company ID', 'Company Name', 'Task Name', 'Current Status', 'Remarks', 'Meeting Count'],
                'rows' => [],
                'empty_message' => 'No remeeting company data available',
                'sortable' => true,
                'paginate' => true,
                'items_per_page' => 10
            ],
            'top_fresh_companies' => [
                'title' => 'Top 20 Fresh Companies',
                'headers' => ['Company ID', 'Company Name', 'Task Name', 'Current Status', 'Remarks', 'Fresh Task Count'],
                'rows' => [],
                'empty_message' => 'No fresh company data available',
                'sortable' => true,
                'paginate' => true,
                'items_per_page' => 10
            ]
        ];
    }

    /**
     * Prepare additional company tables
     */
    private function prepare_additional_company_tables($additionalAnalysis)
    {
        $tables = $this->get_empty_additional_company_tables();
        
        // Top Positive Companies
        $tables['top_positive_companies']['rows'] = array_map(function($company) {
            return [
                $company['cid'],
                $company['compname'],
                $company['task_name'],
                $company['current_status'],
                $this->truncate_text($company['remarks'], 50),
                $company['total_count']
            ];
        }, $additionalAnalysis['top_positive_companies'] ?? []);
        
        // Top Negative Companies
        $tables['top_negative_companies']['rows'] = array_map(function($company) {
            return [
                $company['cid'],
                $company['compname'],
                $company['task_name'],
                $company['current_status'],
                $this->truncate_text($company['remarks'], 50),
                $company['total_count']
            ];
        }, $additionalAnalysis['top_negative_companies'] ?? []);
        
        // Top Companies by Task Volume
        $tables['top_companies_by_tasks']['rows'] = array_map(function($company) {
            return [
                $company['cid'],
                $company['compname'],
                $company['task_name'],
                $company['current_status'],
                $this->truncate_text($company['remarks'], 50),
                $company['total_tasks']
            ];
        }, $additionalAnalysis['top_companies_by_tasks'] ?? []);
        
        // Top Re-meeting Companies
        $tables['top_remeeting_companies']['rows'] = array_map(function($company) {
            return [
                $company['cid'],
                $company['compname'],
                $company['task_name'],
                $company['current_status'],
                $this->truncate_text($company['remarks'], 50),
                $company['meeting_count']
            ];
        }, $additionalAnalysis['top_remeeting_companies'] ?? []);
        
        // Top Fresh Companies
        $tables['top_fresh_companies']['rows'] = array_map(function($company) {
            return [
                $company['cid'],
                $company['compname'],
                $company['task_name'],
                $company['current_status'],
                $this->truncate_text($company['remarks'], 50),
                $company['fresh_task_count']
            ];
        }, $additionalAnalysis['top_fresh_companies'] ?? []);
        
        return $tables;
    }

    /**
     * Find specific user data
     */
    private function find_user_data($users, $userId)
    {
        foreach ($users as $user) {
            if ($user['user_id'] == $userId) {
                return $user;
            }
        }
        return null;
    }

    /**
     * Prepare main table data
     */
    private function prepare_main_table_data($users)
    {
        $headers = [
            'Rank', 'Username', 'Total Tasks', 'Completed', 'Pending',
            'Completion Rate', 'Efficiency', 'Overall Performance',
            'Meetings', 'Successful Mtgs', 'Total Clients', 'Potential Clients',
            'Quality Score', 'Status Changes'
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
                $user['task_statistics']['efficiency_score'] . '/100',
                $user['performance_metrics']['overall_performance'] . '%',
                $user['meeting_analysis']['total_meetings'],
                $user['meeting_analysis']['successful_meetings'],
                $user['sales_metrics']['total_clients'],
                $user['sales_metrics']['potential_clients'],
                $user['task_quality']['quality_score'] . '/100',
                $user['status_analysis']['status_changes']
            ];
        }

        return [
            'title' => 'Sales Team Task Performance Analysis',
            'headers' => $headers,
            'rows' => $rows,
            'sortable' => true,
            'paginate' => true,
            'items_per_page' => 10
        ];
    }

    /**
     * Prepare chart data
     */
    private function prepare_chart_data($analysisData)
    {
        $users = $analysisData['users'];
        usort($users, function($a, $b) {
            return $b['performance_metrics']['overall_performance'] - 
                   $a['performance_metrics']['overall_performance'];
        });

        $labels = [];
        $completionData = [];
        $efficiencyData = [];
        $performanceData = [];

        foreach ($users as $user) {
            $labels[] = substr($user['username'], 0, 12);
            $completionData[] = $user['task_statistics']['completion_rate'];
            $efficiencyData[] = $user['task_statistics']['efficiency_score'];
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
            'meeting_success_rate' => $this->prepare_meeting_chart_data($users),
            'client_distribution' => $this->prepare_client_chart_data($users),
            'performance_distribution' => $this->prepare_performance_distribution_chart($users)
        ];
    }

    /**
     * Prepare meeting chart data
     */
    private function prepare_meeting_chart_data($users)
    {
        $labels = [];
        $totalMeetings = [];
        $successfulMeetings = [];
        $successRate = [];

        foreach ($users as $user) {
            $labels[] = substr($user['username'], 0, 10);
            $totalMeetings[] = $user['meeting_analysis']['total_meetings'];
            $successfulMeetings[] = $user['meeting_analysis']['successful_meetings'];
            
            $rate = $user['meeting_analysis']['total_meetings'] > 0 ? 
                round(($user['meeting_analysis']['successful_meetings'] / $user['meeting_analysis']['total_meetings']) * 100, 2) : 0;
            $successRate[] = $rate;
        }

        return [
            'type' => 'bar',
            'title' => 'Meeting Performance Analysis',
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Total Meetings',
                    'data' => $totalMeetings,
                    'backgroundColor' => '#26c6da',
                    'borderColor' => '#00acc1',
                    'borderWidth' => 1
                ],
                [
                    'label' => 'Successful Meetings',
                    'data' => $successfulMeetings,
                    'backgroundColor' => '#66bb6a',
                    'borderColor' => '#4caf50',
                    'borderWidth' => 1
                ],
                [
                    'label' => 'Success Rate (%)',
                    'data' => $successRate,
                    'type' => 'line',
                    'borderColor' => '#ff9800',
                    'backgroundColor' => 'transparent',
                    'borderWidth' => 2,
                    'fill' => false
                ]
            ]
        ];
    }

    /**
     * Prepare client chart data
     */
    private function prepare_client_chart_data($users)
    {
        $labels = [];
        $totalClients = [];
        $potentialClients = [];
        $highValueClients = [];

        foreach ($users as $user) {
            $labels[] = substr($user['username'], 0, 10);
            $totalClients[] = $user['sales_metrics']['total_clients'];
            $potentialClients[] = $user['sales_metrics']['potential_clients'];
            $highValueClients[] = $user['client_analysis']['high_value_clients'];
        }

        return [
            'type' => 'bar',
            'title' => 'Client Portfolio Analysis',
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Total Clients',
                    'data' => $totalClients,
                    'backgroundColor' => '#42a5f5',
                    'borderColor' => '#2196f3',
                    'borderWidth' => 1
                ],
                [
                    'label' => 'Potential Clients',
                    'data' => $potentialClients,
                    'backgroundColor' => '#ab47bc',
                    'borderColor' => '#9c27b0',
                    'borderWidth' => 1
                ],
                [
                    'label' => 'High Value Clients',
                    'data' => $highValueClients,
                    'backgroundColor' => '#ffa726',
                    'borderColor' => '#ff9800',
                    'borderWidth' => 1
                ]
            ]
        ];
    }

    /**
     * Prepare performance distribution chart
     */
    private function prepare_performance_distribution_chart($users)
    {
        $performanceCategories = [
            'Excellent (>85%)' => 0,
            'Good (70-85%)' => 0,
            'Average (50-70%)' => 0,
            'Needs Improvement (<50%)' => 0
        ];

        foreach ($users as $user) {
            $score = $user['performance_metrics']['overall_performance'];
            
            if ($score > 85) {
                $performanceCategories['Excellent (>85%)']++;
            } elseif ($score >= 70) {
                $performanceCategories['Good (70-85%)']++;
            } elseif ($score >= 50) {
                $performanceCategories['Average (50-70%)']++;
            } else {
                $performanceCategories['Needs Improvement (<50%)']++;
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
     * Prepare summary data
     */
    private function prepare_summary_data($analysisData)
    {
        $teamStats = $analysisData['team_statistics'];
        $summary = $analysisData['summary'];
        $additionalAnalysis = $analysisData['additional_company_analysis'] ?? [];

        $summaryData = [
            'period' => $analysisData['period'],
            'total_users' => $teamStats['total_users'],
            'total_tasks' => $teamStats['total_tasks'],
            'completed_tasks' => $teamStats['total_completed'],
            'pending_tasks' => $teamStats['total_pending'],
            'completion_rate' => $teamStats['average_completion_rate'] . '%',
            'efficiency_score' => $teamStats['total_efficiency_score'] . '/100',
            'quality_score' => $teamStats['overall_quality_score'] . '/100',
            'total_meetings' => $teamStats['total_meetings'],
            'total_clients' => $teamStats['total_clients'],
            'total_companies' => $teamStats['total_companies'],
            'status_changes' => $teamStats['total_status_changes'],
            'top_performers_count' => count($teamStats['top_performers']),
            'needs_attention_count' => count($teamStats['needs_attention']),
            'key_insights' => $summary['key_insights'],
            'sales_insights' => $teamStats['sales_insights'],
            'productivity_metrics' => $summary['team_productivity'],
            'additional_company_stats' => $this->prepare_additional_company_stats($additionalAnalysis)
        ];

        return $summaryData;
    }

    /**
     * Prepare additional company statistics
     */
    private function prepare_additional_company_stats($additionalAnalysis)
    {
        return [
            'total_positive_companies' => count($additionalAnalysis['top_positive_companies'] ?? []),
            'total_negative_companies' => count($additionalAnalysis['top_negative_companies'] ?? []),
            'top_company_by_tasks' => !empty($additionalAnalysis['top_companies_by_tasks']) ? 
                $additionalAnalysis['top_companies_by_tasks'][0]['compname'] ?? 'N/A' : 'N/A',
            'top_remeeting_company' => !empty($additionalAnalysis['top_remeeting_companies']) ? 
                $additionalAnalysis['top_remeeting_companies'][0]['compname'] ?? 'N/A' : 'N/A',
            'top_fresh_company' => !empty($additionalAnalysis['top_fresh_companies']) ? 
                $additionalAnalysis['top_fresh_companies'][0]['compname'] ?? 'N/A' : 'N/A'
        ];
    }

    /**
     * Prepare company tables for specific user
     */
    private function prepare_company_tables($userData)
    {
        $tables = $this->get_empty_company_tables();
        
        // Prepare Positive Companies table
        $tables['positive_companies']['rows'] = $this->format_positive_company_rows(
            $userData['positive_companies'] ?? []
        );
        
        // Prepare Negative Companies table
        $tables['negative_companies']['rows'] = $this->format_negative_company_rows(
            $userData['negative_companies'] ?? []
        );
        
        // Prepare To Work Companies table
        $tables['to_work_companies']['rows'] = $this->format_to_work_company_rows(
            $userData['to_work_companies'] ?? []
        );
        
        // Prepare Overall Companies table
        $tables['overall_companies']['rows'] = $this->format_overall_company_rows(
            $userData['overall_companies'] ?? []
        );
        
        return $tables;
    }

    /**
     * Prepare team company tables (aggregated from all users)
     */
    private function prepare_team_company_tables($users)
    {
        $tables = $this->get_empty_company_tables();
        
        // Aggregate company data from all users
        $allPositiveCompanies = [];
        $allNegativeCompanies = [];
        $allToWorkCompanies = [];
        $allOverallCompanies = [];
        
        foreach ($users as $user) {
            // Aggregate positive companies
            foreach ($user['positive_companies'] ?? [] as $companyName => $companyData) {
                if (!isset($allPositiveCompanies[$companyName])) {
                    $allPositiveCompanies[$companyName] = $companyData;
                } else {
                    $allPositiveCompanies[$companyName]['positive_tasks'] += $companyData['positive_tasks'];
                    $allPositiveCompanies[$companyName]['total_tasks'] += $companyData['total_tasks'];
                    $allPositiveCompanies[$companyName]['tasks'] = array_merge(
                        $allPositiveCompanies[$companyName]['tasks'],
                        $companyData['tasks']
                    );
                }
            }
            
            // Aggregate negative companies
            foreach ($user['negative_companies'] ?? [] as $companyName => $companyData) {
                if (!isset($allNegativeCompanies[$companyName])) {
                    $allNegativeCompanies[$companyName] = $companyData;
                } else {
                    $allNegativeCompanies[$companyName]['negative_tasks'] += $companyData['negative_tasks'];
                    $allNegativeCompanies[$companyName]['total_tasks'] += $companyData['total_tasks'];
                    $allNegativeCompanies[$companyName]['tasks'] = array_merge(
                        $allNegativeCompanies[$companyName]['tasks'],
                        $companyData['tasks']
                    );
                }
            }
            
            // Aggregate to-work companies
            foreach ($user['to_work_companies'] ?? [] as $companyName => $companyData) {
                if (!isset($allToWorkCompanies[$companyName])) {
                    $allToWorkCompanies[$companyName] = $companyData;
                } else {
                    $allToWorkCompanies[$companyName]['to_work_tasks'] += $companyData['to_work_tasks'];
                    $allToWorkCompanies[$companyName]['total_tasks'] += $companyData['total_tasks'];
                    $allToWorkCompanies[$companyName]['pending_tasks'] += $companyData['pending_tasks'];
                    $allToWorkCompanies[$companyName]['tasks'] = array_merge(
                        $allToWorkCompanies[$companyName]['tasks'],
                        $companyData['tasks']
                    );
                }
            }
            
            // Aggregate overall companies
            foreach ($user['overall_companies'] ?? [] as $companyName => $companyData) {
                if (!isset($allOverallCompanies[$companyName])) {
                    $allOverallCompanies[$companyName] = $companyData;
                } else {
                    $allOverallCompanies[$companyName]['total_tasks'] += $companyData['total_tasks'];
                    $allOverallCompanies[$companyName]['positive_tasks'] += $companyData['positive_tasks'];
                    $allOverallCompanies[$companyName]['negative_tasks'] += $companyData['negative_tasks'];
                    $allOverallCompanies[$companyName]['to_work_tasks'] += $companyData['to_work_tasks'];
                    $allOverallCompanies[$companyName]['meeting_tasks'] += $companyData['meeting_tasks'];
                    $allOverallCompanies[$companyName]['proposal_tasks'] += $companyData['proposal_tasks'];
                    $allOverallCompanies[$companyName]['completed_tasks'] += $companyData['completed_tasks'];
                    $allOverallCompanies[$companyName]['pending_tasks'] += $companyData['pending_tasks'];
                    $allOverallCompanies[$companyName]['key_tasks'] = array_merge(
                        $allOverallCompanies[$companyName]['key_tasks'],
                        $companyData['key_tasks']
                    );
                }
            }
        }
        
        // Recalculate ratios and scores for aggregated data
        foreach ($allPositiveCompanies as &$company) {
            $company['positive_ratio'] = round(($company['positive_tasks'] / $company['total_tasks']) * 100, 2);
        }
        
        foreach ($allNegativeCompanies as &$company) {
            $company['negative_ratio'] = round(($company['negative_tasks'] / $company['total_tasks']) * 100, 2);
        }
        
        foreach ($allToWorkCompanies as &$company) {
            $company['work_ratio'] = round(($company['to_work_tasks'] / $company['total_tasks']) * 100, 2);
        }
        
        foreach ($allOverallCompanies as &$company) {
            $company['completion_rate'] = round(($company['completed_tasks'] / $company['total_tasks']) * 100, 2);
            $company['engagement_score'] = $this->calculate_company_engagement_score($company);
        }
        
        // Sort and limit
        uasort($allPositiveCompanies, function($a, $b) {
            return $b['positive_ratio'] - $a['positive_ratio'];
        });
        $allPositiveCompanies = array_slice($allPositiveCompanies, 0, 10, true);
        
        uasort($allNegativeCompanies, function($a, $b) {
            return $b['negative_ratio'] - $a['negative_ratio'];
        });
        $allNegativeCompanies = array_slice($allNegativeCompanies, 0, 10, true);
        
        uasort($allToWorkCompanies, function($a, $b) {
            return $b['work_ratio'] - $a['work_ratio'];
        });
        $allToWorkCompanies = array_slice($allToWorkCompanies, 0, 10, true);
        
        uasort($allOverallCompanies, function($a, $b) {
            return $b['engagement_score'] - $a['engagement_score'];
        });
        $allOverallCompanies = array_slice($allOverallCompanies, 0, 10, true);
        
        // Format rows
        $tables['positive_companies']['rows'] = $this->format_positive_company_rows($allPositiveCompanies);
        $tables['negative_companies']['rows'] = $this->format_negative_company_rows($allNegativeCompanies);
        $tables['to_work_companies']['rows'] = $this->format_to_work_company_rows($allToWorkCompanies);
        $tables['overall_companies']['rows'] = $this->format_overall_company_rows($allOverallCompanies);
        
        return $tables;
    }

    /**
     * Format positive company rows
     */
    private function format_positive_company_rows($positiveCompanies)
    {
        $rows = [];
        
        foreach ($positiveCompanies as $companyName => $companyData) {
            $keyTask = $this->get_key_task_for_display($companyData['tasks'] ?? []);
            
            $rows[] = [
                $companyName,
                $companyData['positive_tasks'],
                $companyData['total_tasks'],
                $companyData['positive_ratio'] . '%',
                $keyTask['task_name'] ?? 'N/A',
                $this->truncate_text($keyTask['remarks'] ?? '', 50)
            ];
        }
        
        return $rows;
    }

    /**
     * Format negative company rows
     */
    private function format_negative_company_rows($negativeCompanies)
    {
        $rows = [];
        
        foreach ($negativeCompanies as $companyName => $companyData) {
            $keyTask = $this->get_key_task_for_display($companyData['tasks'] ?? []);
            
            $rows[] = [
                $companyName,
                $companyData['negative_tasks'],
                $companyData['total_tasks'],
                $companyData['negative_ratio'] . '%',
                $keyTask['task_name'] ?? 'N/A',
                $this->truncate_text($keyTask['remarks'] ?? '', 50)
            ];
        }
        
        return $rows;
    }

    /**
     * Format to-work company rows
     */
    private function format_to_work_company_rows($toWorkCompanies)
    {
        $rows = [];
        
        foreach ($toWorkCompanies as $companyName => $companyData) {
            $keyTask = $this->get_key_task_for_display($companyData['tasks'] ?? []);
            
            $rows[] = [
                $companyName,
                $companyData['pending_tasks'],
                $companyData['total_tasks'],
                $companyData['work_ratio'] . '%',
                $keyTask['task_name'] ?? 'N/A',
                $this->truncate_text($keyTask['remarks'] ?? '', 50)
            ];
        }
        
        return $rows;
    }

    /**
     * Format overall company rows
     */
    private function format_overall_company_rows($overallCompanies)
    {
        $rows = [];
        
        foreach ($overallCompanies as $companyName => $companyData) {
            $keyTask = $this->get_key_task_for_display($companyData['key_tasks'] ?? []);
            
            $rows[] = [
                $companyName,
                $companyData['total_tasks'],
                $companyData['engagement_score'] . '/100',
                $companyData['completion_rate'] . '%',
                $keyTask['task_name'] ?? 'N/A',
                $this->truncate_text($keyTask['remarks'] ?? '', 50)
            ];
        }
        
        return $rows;
    }

    /**
     * Get key task for display
     */
    private function get_key_task_for_display($tasks)
    {
        if (empty($tasks)) {
            return ['task_name' => 'N/A', 'remarks' => 'No remarks available'];
        }
        
        // Sort by date (most recent first)
        usort($tasks, function($a, $b) {
            $dateA = strtotime($a['date'] ?? '');
            $dateB = strtotime($b['date'] ?? '');
            return $dateB - $dateA;
        });
        
        return $tasks[0] ?? ['task_name' => 'N/A', 'remarks' => 'No remarks available'];
    }

    /**
     * Truncate text for display
     */
    private function truncate_text($text, $length)
    {
        if (strlen($text) <= $length) {
            return $text;
        }
        return substr($text, 0, $length) . '...';
    }

    /**
     * Prepare specific user frontend data
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
            'meeting_analysis' => $user['meeting_analysis'],
            'sales_metrics' => $user['sales_metrics'],
            'client_analysis' => $user['client_analysis'],
            'task_quality' => $user['task_quality'],
            'status_analysis' => $user['status_analysis'],
            'company_analysis' => $user['company_analysis'],
            'remarks_analysis' => $user['remarks_analysis'],
            'time_analysis' => $user['time_analysis'],
            'performance_metrics' => $user['performance_metrics']
        ];
    }

    /**
     * Calculate performance percentile
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
                'user' => $user['task_statistics']['efficiency_score'],
                'team_average' => $teamStats['total_efficiency_score'],
                'difference' => round($user['task_statistics']['efficiency_score'] - $teamStats['total_efficiency_score'], 2)
            ],
            'quality_score' => [
                'user' => $user['task_quality']['quality_score'],
                'team_average' => $teamStats['overall_quality_score'],
                'difference' => round($user['task_quality']['quality_score'] - $teamStats['overall_quality_score'], 2)
            ],
            'overall_performance' => [
                'user' => $user['performance_metrics']['overall_performance'],
                'team_average' => $this->calculate_team_average_performance($teamData['users']),
                'difference' => round($user['performance_metrics']['overall_performance'] - 
                    $this->calculate_team_average_performance($teamData['users']), 2)
            ],
            'meeting_success' => [
                'user_success_rate' => $user['meeting_analysis']['total_meetings'] > 0 ? 
                    round(($user['meeting_analysis']['successful_meetings'] / $user['meeting_analysis']['total_meetings']) * 100, 2) : 0,
                'team_success_rate' => $teamStats['total_meetings'] > 0 ? 
                    round(($teamStats['sales_insights']['successful_meetings'] ?? 0) / $teamStats['total_meetings'] * 100, 2) : 0
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
     */
    private function calculate_team_average_performance($users)
    {
        if (empty($users)) return 0;
        $total = 0;
        foreach ($users as $user) {
            $total += $user['performance_metrics']['overall_performance'];
        }
        return round($total / count($users), 2);
    }

    /**
     * Get comparison text
     */
    private function get_comparison_text($userValue, $teamValue)
    {
        $difference = $userValue - $teamValue;
        
        if ($difference > 10) return "Significantly above team average";
        if ($difference > 5) return "Above team average";
        if ($difference > 0) return "Slightly above team average";
        if ($difference > -5) return "Slightly below team average";
        if ($difference > -10) return "Below team average";
        return "Significantly below team average";
    }
}