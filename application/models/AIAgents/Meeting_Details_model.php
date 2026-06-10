<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Meeting_Details_model extends CI_Model {

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
    public function process_Meeting_Details($message, $analysisType, $sdate, $edate) 
    {
        // Get meeting data
        $meetingsData = $this->Menu_model->MeetingDetailsByRolesUid(
            $this->uid, $sdate, $edate, $this->uid, 'all'
        );

        $totalMeetingsUserByDatas = $this->Menu_model->TotalMeetingDetailsDatas(
            $this->uid, $sdate, $edate, 'total_plan_meetings', $this->uid, NULL
        );

        $meetingsDatas = [
            'total_meetingsData' => $meetingsData,
            'totalMeetingsByCompanyName' => $totalMeetingsUserByDatas,
        ];

        // Process meeting data into structured format
        $processedData = $this->process_raw_meeting_data($meetingsDatas['total_meetingsData']);

        // Prepare analysis data
        $analysisData = $this->prepare_meeting_analysis_data(
            $processedData, 
            $sdate, 
            $edate,
            $meetingsDatas['totalMeetingsByCompanyName']
        );

        // Extract specific user request if present
        $specificUser = $this->extract_specific_user_request($message, $analysisData['users']);

        // Generate comprehensive analysis based on the message
        $analysisResults = $this->generate_comprehensive_analysis(
            $message, 
            $specificUser, 
            $analysisData
        );

        // Prepare frontend data
        $frontendData = $this->prepare_meeting_frontend_data($analysisData, $specificUser);

        return [
            'content' => $analysisResults,
            'data' => $frontendData
        ];
    }

    /**
     * Process raw meeting data into structured format
     */
    private function process_raw_meeting_data($rawData)
    {
        if (empty($rawData) || !is_array($rawData)) {
            return [];
        }

        $processedData = [];

        foreach ($rawData as $userData) {
            if (!is_object($userData)) continue;

            $userId = $userData->user_id ?? 0;
            $username = $userData->name ?? 'Unknown User';

            $processedData[] = [
                'user_id' => $userId,
                'username' => $username,
                
                // Core Meeting Statistics
                'total_plan_meetings' => $userData->total_plan_meetings ?? 0,
                'complete_meetings' => $userData->complete_meetings ?? 0,
                'not_complete_meetings' => $userData->not_complete_meetings ?? 0,
                
                // Meeting Type Breakdown
                'total_sheduled_meetings' => $userData->total_sheduled_meetings ?? 0,
                'total_complete_sheduled_meetings' => $userData->total_complete_sheduled_meetings ?? 0,
                'not_complete_sheduled_meetings' => $userData->not_complete_sheduled_meetings ?? 0,
                
                'total_barg_meetings' => $userData->total_barg_meetings ?? 0,
                'total_complete_barg_meetings' => $userData->total_complete_barg_meetings ?? 0,
                'not_complete_barg_meetings' => $userData->not_complete_barg_meetings ?? 0,
                
                'total_join_meetings' => $userData->total_join_meetings ?? 0,
                'total_complete_join_meetings' => $userData->total_complete_join_meetings ?? 0,
                'not_complete_join_meetings' => $userData->not_complete_join_meetings ?? 0,
                
                'total_vm_meetings' => $userData->total_vm_meetings ?? 0,
                'total_complete_vm_meetings' => $userData->total_complete_vm_meetings ?? 0,
                'not_complete_vm_meetings' => $userData->not_complete_vm_meetings ?? 0,
                
                // RP Status Analysis
                'total_RP_meetings' => $userData->total_RP_meetings ?? 0,
                'total_NO_RP_meetings' => $userData->total_NO_RP_meetings ?? 0,
                'total_Only_Got_details_meetings' => $userData->total_Only_Got_details_meetings ?? 0,
                'total_Change_RP_meetings' => $userData->total_Change_RP_meetings ?? 0,
                
                // RP by Meeting Type
                'total_Sheduled_RP_meetings' => $userData->total_Sheduled_RP_meetings ?? 0,
                'total_Sheduled_NO_RP_meetings' => $userData->total_Sheduled_NO_RP_meetings ?? 0,
                'total_Sheduled_Only_Got_details_meetings' => $userData->total_Sheduled_Only_Got_details_meetings ?? 0,
                
                'total_Barge_RP_meetings' => $userData->total_Barge_RP_meetings ?? 0,
                'total_Barge_NO_RP_meetings' => $userData->total_Barge_NO_RP_meetings ?? 0,
                'total_Barge_Only_Got_details_meetings' => $userData->total_Barge_Only_Got_details_meetings ?? 0,
                
                'total_vm_RP_meetings' => $userData->total_vm_RP_meetings ?? 0,
                'total_vm_NO_RP_meetings' => $userData->total_vm_NO_RP_meetings ?? 0,
                'total_vm_Only_Got_details_meetings' => $userData->total_vm_Only_Got_details_meetings ?? 0,
                
                // Client Segmentation
                'total_potential_meetings' => $userData->total_potential_meetings ?? 0,
                'total_non_potential_meetings' => $userData->total_non_potential_meetings ?? 0,
                
                'total_topspender_meetings' => $userData->total_topspender_meetings ?? 0,
                'total_upsell_client_meetings' => $userData->total_upsell_client_meetings ?? 0,
                'total_focus_funnel_meetings' => $userData->total_focus_funnel_meetings ?? 0,
                'total_keycompany_meetings' => $userData->total_keycompany_meetings ?? 0,
                'total_pkclient_meetings' => $userData->total_pkclient_meetings ?? 0,
                'total_priorityc_meetings' => $userData->total_priorityc_meetings ?? 0,
                
                // Meeting Frequency & Freshness
                'total_fresh_meetings_task_on_month' => $userData->total_fresh_meetings_task_on_month ?? 0,
                'total_fresh_meetings_on_month' => $userData->total_fresh_meetings_on_month ?? 0,
                'total_fresh_meetings_user_count_on_month' => $userData->total_fresh_meetings_user_count_on_month ?? 0,
                'total_fresh_meetings' => $userData->total_fresh_meetings ?? 0,
                'total_re_meetings' => $userData->total_re_meetings ?? 0,
                
                // MOM Analysis
                'total_write_mom_rp_meetings' => $userData->total_write_mom_rp_meetings ?? 0,
                'total_pending_for_write_mom_rp_meeting' => $userData->total_pending_for_write_mom_rp_meeting ?? 0,
                'total_mom_data' => $userData->total_mom_data ?? 0,
                'total_approved_after_check_mom_data' => $userData->total_approved_after_check_mom_data ?? 0,
                'total_reject_after_check_mom_data' => $userData->total_reject_after_check_mom_data ?? 0,
                'total_NO_RP_after_check_mom_data' => $userData->total_NO_RP_after_check_mom_data ?? 0,
                'total_pending_for_check_mom_data' => $userData->total_pending_for_check_mom_data ?? 0,
                
                // Productivity
                'task_frequency_per_day' => $userData->task_frequency_per_day ?? 0,
                
                // Calculated Metrics
                'calculated_metrics' => $this->calculate_all_metrics($userData)
            ];
        }

        return $processedData;
    }

    /**
     * Calculate all metrics from user data
     */
    private function calculate_all_metrics($userData)
    {
        $total = $userData->total_plan_meetings ?? 0;
        $completed = $userData->complete_meetings ?? 0;
        $incomplete = $userData->not_complete_meetings ?? 0;
        $rpMeetings = $userData->total_RP_meetings ?? 0;
        $noRpMeetings = $userData->total_NO_RP_meetings ?? 0;
        $detailsOnly = $userData->total_Only_Got_details_meetings ?? 0;
        
        return [
            // Completion Metrics
            'completion_rate' => $this->calculate_percentage($completed, $total),
            'incompletion_rate' => $this->calculate_percentage($incomplete, $total),
            'scheduled_completion_rate' => $this->calculate_percentage(
                $userData->total_complete_sheduled_meetings ?? 0,
                $userData->total_sheduled_meetings ?? 0
            ),
            'barge_completion_rate' => $this->calculate_percentage(
                $userData->total_complete_barg_meetings ?? 0,
                $userData->total_barg_meetings ?? 0
            ),
            
            // RP Conversion Metrics
            'rp_conversion_rate' => $this->calculate_percentage($rpMeetings, $total),
            'no_rp_rate' => $this->calculate_percentage($noRpMeetings, $total),
            'details_only_rate' => $this->calculate_percentage($detailsOnly, $total),
            
            // Meeting Type Distribution
            'scheduled_percentage' => $this->calculate_percentage(
                $userData->total_sheduled_meetings ?? 0,
                $total
            ),
            'barge_in_percentage' => $this->calculate_percentage(
                $userData->total_barg_meetings ?? 0,
                $total
            ),
            'join_percentage' => $this->calculate_percentage(
                $userData->total_join_meetings ?? 0,
                $total
            ),
            'video_percentage' => $this->calculate_percentage(
                $userData->total_vm_meetings ?? 0,
                $total
            ),
            
            // Client Quality Metrics
            'potential_client_rate' => $this->calculate_percentage(
                $userData->total_potential_meetings ?? 0,
                $total
            ),
            'high_value_client_rate' => $this->calculate_percentage(
                ($userData->total_topspender_meetings ?? 0) +
                ($userData->total_keycompany_meetings ?? 0) +
                ($userData->total_priorityc_meetings ?? 0),
                $total
            ),
            
            // Meeting Freshness
            'fresh_vs_re_ratio' => $this->calculate_percentage(
                $userData->total_fresh_meetings ?? 0,
                max(($userData->total_fresh_meetings ?? 0) + ($userData->total_re_meetings ?? 0), 1)
            ),
            
            // MOM Compliance
            'mom_completion_rate' => $this->calculate_percentage(
                $userData->total_write_mom_rp_meetings ?? 0,
                $rpMeetings
            ),
            'mom_approval_rate' => $this->calculate_percentage(
                $userData->total_approved_after_check_mom_data ?? 0,
                $userData->total_mom_data ?? 0
            ),
            
            // Efficiency Metrics
            'wastage_rate' => $this->calculate_percentage($incomplete + $noRpMeetings, $total),
            'conversion_efficiency' => $this->calculate_percentage($rpMeetings, $completed),
            'productivity_score' => $this->calculate_productivity_score($userData)
        ];
    }

    /**
     * Calculate productivity score
     */
    private function calculate_productivity_score($userData)
    {
        $score = 0;
        $total = $userData->total_plan_meetings ?? 0;
        
        if ($total == 0) return 0;
        
        // Completion (30%)
        $completionRate = $this->calculate_percentage(
            $userData->complete_meetings ?? 0,
            $total
        );
        $score += $completionRate * 0.3;
        
        // RP Conversion (40%)
        $rpRate = $this->calculate_percentage(
            $userData->total_RP_meetings ?? 0,
            $total
        );
        $score += $rpRate * 0.4;
        
        // High Value Focus (20%)
        $hvcRate = $this->calculate_percentage(
            ($userData->total_topspender_meetings ?? 0) +
            ($userData->total_keycompany_meetings ?? 0) +
            ($userData->total_priorityc_meetings ?? 0),
            $total
        );
        $score += $hvcRate * 0.2;
        
        // MOM Compliance (10%)
        $momRate = $this->calculate_percentage(
            $userData->total_write_mom_rp_meetings ?? 0,
            $userData->total_RP_meetings ?? 0
        );
        $score += $momRate * 0.1;
        
        return round($score, 2);
    }

    /**
     * Calculate percentage
     */
    private function calculate_percentage($part, $total)
    {
        if ($total == 0) return 0;
        return round(($part / $total) * 100, 2);
    }

    /**
     * Prepare meeting analysis data with company details
     */
    private function prepare_meeting_analysis_data($processedData, $startDate, $endDate, $companyData)
    {
        $teamStats = $this->calculate_team_statistics($processedData);
        $users = $this->prepare_user_details($processedData);
        
        // Process company data for detailed analysis
        $companyAnalysis = $this->analyze_company_data($companyData, $processedData);
        
        // Categorize companies as Positive or Negative based on MOM remarks
        $categorizedCompanies = $this->categorize_companies_by_sentiment($companyData);

        return [
            'period' => "{$startDate} to {$endDate}",
            'users' => $users,
            'company_data' => $companyAnalysis,
            'categorized_companies' => $categorizedCompanies,
            'team_statistics' => $teamStats,
            'summary' => $this->prepare_summary($users, $teamStats, $companyAnalysis, $categorizedCompanies),
            'query_date' => $startDate,
            'analysis_date' => date('Y-m-d H:i:s')
        ];
    }

    /**
     * Categorize companies as Positive or Negative based on MOM remarks and current status
     */
    private function categorize_companies_by_sentiment($companyData)
    {
        $positiveCompanies = [];
        $negativeCompanies = [];
        
        if (empty($companyData)) {
            return [
                'positive_companies' => [],
                'negative_companies' => [],
                'positive_count' => 0,
                'negative_count' => 0
            ];
        }
        
        foreach ($companyData as $company) {
            $companyInfo = [
                'company_name' => $company->compname ?? 'Unknown',
                'cid' => $company->cid ?? 0,
                'mom_remarks' => $company->mom_remarks ?? '',
                'current_status' => $company->current_status ?? 'Unknown',
                'business_potentional_client' => $company->business_potentional_client ?? '',
                'cluster_name' => $company->cluster_name ?? 'Unknown',
                'meeting_type' => $company->task_name ?? 'Unknown',
                'meeting_status' => $company->meetings_status ?? 'Unknown',
                'potential' => $company->potential ?? 'no',
                'topspender' => $company->topspender ?? 'no',
                'priorityc' => $company->priorityc ?? 'no',
                'keycompany' => $company->keycompany ?? 'no',
                'anchor_clients' => $company->anchor_clients ?? 'no'
            ];
            
            // Determine if company is Positive or Negative
            $isPositive = $this->determine_company_sentiment($companyInfo);
            
            if ($isPositive) {
                $positiveCompanies[] = $companyInfo;
            } else {
                $negativeCompanies[] = $companyInfo;
            }
        }
        
        // Sort positive companies (best first) and negative companies (worst first)
        usort($positiveCompanies, function($a, $b) {
            return $this->calculate_company_score($b) - $this->calculate_company_score($a);
        });
        
        usort($negativeCompanies, function($a, $b) {
            return $this->calculate_company_score($a) - $this->calculate_company_score($b);
        });
        
        return [
            'positive_companies' => array_slice($positiveCompanies, 0, 10), // Top 10 positive
            'negative_companies' => array_slice($negativeCompanies, 0, 10), // Top 10 negative
            'positive_count' => count($positiveCompanies),
            'negative_count' => count($negativeCompanies),
            'total_companies' => count($companyData)
        ];
    }
    
    /**
     * Determine company sentiment based on MOM remarks and current status
     */
    private function determine_company_sentiment($companyInfo)
    {
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
        
        $momRemarks = strtolower($companyInfo['mom_remarks'] ?? '');
        $currentStatus = strtolower($companyInfo['current_status'] ?? '');
        
        $positiveScore = 0;
        $negativeScore = 0;
        
        // Check positive keywords in MOM remarks
        foreach ($positiveKeywords as $keyword) {
            if (strpos($momRemarks, $keyword) !== false) {
                $positiveScore++;
            }
        }
        
        // Check negative keywords in MOM remarks
        foreach ($negativeKeywords as $keyword) {
            if (strpos($momRemarks, $keyword) !== false) {
                $negativeScore++;
            }
        }
        
        // Check current status
        if (in_array($currentStatus, ['very positive', 'positive', 'excellent', 'good'])) {
            $positiveScore += 2;
        } elseif (in_array($currentStatus, ['negative', 'not interested', 'rejected'])) {
            $negativeScore += 2;
        }
        
        // Check business potential
        if ($companyInfo['potential'] === 'yes') {
            $positiveScore++;
        }
        
        if ($companyInfo['topspender'] === 'yes') {
            $positiveScore++;
        }
        
        if ($companyInfo['priorityc'] === 'yes') {
            $positiveScore++;
        }
        
        if ($companyInfo['keycompany'] === 'yes') {
            $positiveScore++;
        }
        
        if ($companyInfo['anchor_clients'] === 'yes') {
            $positiveScore++;
        }
        
        // Determine final sentiment
        return $positiveScore >= $negativeScore;
    }
    
    /**
     * Calculate company score for sorting
     */
    private function calculate_company_score($companyInfo)
    {
        $score = 0;
        
        // Status scoring
        $statusScores = [
            'very positive' => 5,
            'positive' => 4,
            'excellent' => 5,
            'good' => 3,
            'neutral' => 1,
            'negative' => -2,
            'not interested' => -3,
            'rejected' => -5
        ];
        
        $currentStatus = strtolower($companyInfo['current_status'] ?? '');
        if (isset($statusScores[$currentStatus])) {
            $score += $statusScores[$currentStatus];
        }
        
        // Business potential scoring
        if ($companyInfo['potential'] === 'yes') $score += 3;
        if ($companyInfo['topspender'] === 'yes') $score += 4;
        if ($companyInfo['priorityc'] === 'yes') $score += 3;
        if ($companyInfo['keycompany'] === 'yes') $score += 4;
        if ($companyInfo['anchor_clients'] === 'yes') $score += 3;
        
        // MOM remarks length (more details = better)
        $momLength = strlen($companyInfo['mom_remarks'] ?? '');
        if ($momLength > 100) $score += 2;
        elseif ($momLength > 50) $score += 1;
        
        return $score;
    }

    /**
     * Analyze company data
     */
    private function analyze_company_data($companyData, $userData)
    {
        if (empty($companyData)) {
            return [];
        }

        $analysis = [
            'total_companies' => count($companyData),
            'companies_by_user' => [],
            'companies_by_status' => [],
            'companies_by_potential' => [],
            'companies_by_client_type' => [],
            'meeting_details' => []
        ];

        // Group by user
        foreach ($companyData as $company) {
            $userId = $company->task_user_id ?? 0;
            $userName = $company->task_username ?? 'Unknown';
            
            if (!isset($analysis['companies_by_user'][$userId])) {
                $analysis['companies_by_user'][$userId] = [
                    'username' => $userName,
                    'count' => 0,
                    'companies' => []
                ];
            }
            
            $analysis['companies_by_user'][$userId]['count']++;
            $analysis['companies_by_user'][$userId]['companies'][] = [
                'company_name' => $company->compname ?? 'Unknown',
                'cid' => $company->cid ?? 0,
                'meeting_status' => $company->meetings_status ?? 'Unknown',
                'current_status' => $company->current_status ?? 'Unknown',
                'meeting_type' => $company->task_name ?? 'Unknown',
                'appointment_date' => $company->appointmentdatetime ?? '',
                'purpose_achieved' => $company->purpose_achieved ?? 'no',
                'potential' => $company->potential ?? 'no',
                'topspender' => $company->topspender ?? 'no',
                'priorityc' => $company->priorityc ?? 'no',
                'keycompany' => $company->keycompany ?? 'no',
                'focus_funnel' => $company->focus_funnel ?? 'no',
                'pkclient' => $company->pkclient ?? 'no',
                'anchor_clients' => $company->anchor_clients ?? 'no',
                'mom_remarks' => $company->mom_remarks ?? '',
                'business_potentional_client' => $company->business_potentional_client ?? '',
                'cluster_name' => $company->cluster_name ?? 'Unknown'
            ];

            // Count by status
            $status = $company->meetings_status ?? 'Unknown';
            if (!isset($analysis['companies_by_status'][$status])) {
                $analysis['companies_by_status'][$status] = 0;
            }
            $analysis['companies_by_status'][$status]++;

            // Count by potential
            $potential = $company->potential ?? 'no';
            if (!isset($analysis['companies_by_potential'][$potential])) {
                $analysis['companies_by_potential'][$potential] = 0;
            }
            $analysis['companies_by_potential'][$potential]++;

            // Count by client type
            if ($company->topspender === 'yes') {
                if (!isset($analysis['companies_by_client_type']['topspender'])) {
                    $analysis['companies_by_client_type']['topspender'] = 0;
                }
                $analysis['companies_by_client_type']['topspender']++;
            }
            
            if ($company->keycompany === 'yes') {
                if (!isset($analysis['companies_by_client_type']['keycompany'])) {
                    $analysis['companies_by_client_type']['keycompany'] = 0;
                }
                $analysis['companies_by_client_type']['keycompany']++;
            }
            
            if ($company->priorityc === 'yes') {
                if (!isset($analysis['companies_by_client_type']['priority'])) {
                    $analysis['companies_by_client_type']['priority'] = 0;
                }
                $analysis['companies_by_client_type']['priority']++;
            }
            
            if ($company->anchor_clients === 'yes') {
                if (!isset($analysis['companies_by_client_type']['anchor'])) {
                    $analysis['companies_by_client_type']['anchor'] = 0;
                }
                $analysis['companies_by_client_type']['anchor']++;
            }

            // Detailed meeting info
            $analysis['meeting_details'][] = [
                'user_id' => $userId,
                'username' => $userName,
                'company_name' => $company->compname ?? 'Unknown',
                'meeting_id' => $company->task_id ?? 0,
                'meeting_type' => $company->task_name ?? 'Unknown',
                'scheduled_time' => $company->appointmentdatetime ?? '',
                'status' => $company->meetings_status ?? 'Unknown',
                'purpose_achieved' => $company->purpose_achieved ?? 'no',
                'actontaken' => $company->actontaken ?? 'no',
                'current_status' => $company->current_status ?? 'Unknown',
                'potential' => $company->potential ?? 'no',
                'topspender' => $company->topspender ?? 'no',
                'priority' => $company->priorityc ?? 'no',
                'keycompany' => $company->keycompany ?? 'no',
                'focus_funnel' => $company->focus_funnel ?? 'no',
                'pkclient' => $company->pkclient ?? 'no',
                'anchor_client' => $company->anchor_clients ?? 'no',
                'cluster' => $company->cluster_name ?? 'Unknown',
                'partner' => $company->partner_name ?? 'Unknown',
                'mom_remarks' => $company->mom_remarks ?? '',
                'business_potentional_client' => $company->business_potentional_client ?? ''
            ];
        }

        return $analysis;
    }

    /**
     * Calculate team statistics
     */
    private function calculate_team_statistics($processedData)
    {
        if (empty($processedData)) {
            return $this->get_empty_team_stats();
        }

        $stats = [
            'total_users' => count($processedData),
            'aggregate_totals' => [],
            'average_rates' => [],
            'performance_distribution' => [],
            'key_insights' => []
        ];

        // Initialize aggregates
        $aggregates = [];
        $rates = [];
        $productivityScores = [];

        foreach ($processedData as $user) {
            // Aggregate counts
            foreach ($user as $key => $value) {
                if (is_numeric($value) && !in_array($key, ['user_id', 'calculated_metrics'])) {
                    if (!isset($aggregates[$key])) {
                        $aggregates[$key] = 0;
                    }
                    $aggregates[$key] += $value;
                }
            }

            // Collect calculated metrics for averages
            if (isset($user['calculated_metrics'])) {
                foreach ($user['calculated_metrics'] as $metric => $value) {
                    if (!isset($rates[$metric])) {
                        $rates[$metric] = [];
                    }
                    $rates[$metric][] = $value;
                }
            }

            // Collect productivity scores
            $productivityScores[] = $user['calculated_metrics']['productivity_score'] ?? 0;
        }

        $stats['aggregate_totals'] = $aggregates;

        // Calculate average rates
        foreach ($rates as $metric => $values) {
            $stats['average_rates'][$metric] = round(array_sum($values) / count($values), 2);
        }

        // Performance distribution
        $stats['performance_distribution'] = $this->calculate_performance_distribution($productivityScores);

        // Key insights
        $stats['key_insights'] = $this->generate_team_insights($processedData, $stats);

        return $stats;
    }

    /**
     * Prepare user details
     */
    private function prepare_user_details($processedData)
    {
        $users = [];

        foreach ($processedData as $user) {
            $users[] = [
                'user_id' => $user['user_id'],
                'username' => $user['username'],
                'raw_data' => $user,
                'calculated_metrics' => $user['calculated_metrics'],
                'rank' => 0
            ];
        }

        // Calculate ranks based on productivity score
        usort($users, function($a, $b) {
            return ($b['calculated_metrics']['productivity_score'] ?? 0) - 
                   ($a['calculated_metrics']['productivity_score'] ?? 0);
        });

        $rank = 1;
        foreach ($users as $key => &$user) {
            if ($key > 0 && 
                ($user['calculated_metrics']['productivity_score'] ?? 0) === 
                ($users[$key - 1]['calculated_metrics']['productivity_score'] ?? 0)) {
                $user['rank'] = $rank;
            } else {
                $user['rank'] = $rank;
                $rank++;
            }
        }

        return $users;
    }

    /**
     * Calculate performance distribution
     */
    private function calculate_performance_distribution($scores)
    {
        $distribution = [
            'excellent' => 0, // >80
            'good' => 0,      // 60-80
            'average' => 0,   // 40-60
            'poor' => 0       // <40
        ];

        foreach ($scores as $score) {
            if ($score > 80) {
                $distribution['excellent']++;
            } elseif ($score >= 60) {
                $distribution['good']++;
            } elseif ($score >= 40) {
                $distribution['average']++;
            } else {
                $distribution['poor']++;
            }
        }

        return $distribution;
    }

    /**
     * Generate team insights
     */
    private function generate_team_insights($users, $stats)
    {
        $insights = [];

        if (empty($users)) {
            return ['No data available'];
        }

        // Overall performance insight
        $avgProductivity = $stats['average_rates']['productivity_score'] ?? 0;
        if ($avgProductivity > 70) {
            $insights[] = "Strong overall performance: Average productivity score of {$avgProductivity}/100";
        } elseif ($avgProductivity > 50) {
            $insights[] = "Moderate performance: Average productivity score of {$avgProductivity}/100";
        } else {
            $insights[] = "Performance needs improvement: Average productivity score of {$avgProductivity}/100";
        }

        // RP conversion insight
        $avgRpRate = $stats['average_rates']['rp_conversion_rate'] ?? 0;
        if ($avgRpRate > 40) {
            $insights[] = "Good decision-maker engagement: {$avgRpRate}% of meetings reached RPs";
        } elseif ($avgRpRate > 20) {
            $insights[] = "Moderate RP conversion: Only {$avgRpRate}% of meetings reached decision-makers";
        } else {
            $insights[] = "Low RP conversion: Only {$avgRpRate}% of meetings reached decision-makers - focus on qualification";
        }

        // Completion rate insight
        $avgCompletion = $stats['average_rates']['completion_rate'] ?? 0;
        if ($avgCompletion > 80) {
            $insights[] = "High meeting completion rate: {$avgCompletion}%";
        } elseif ($avgCompletion > 60) {
            $insights[] = "Average meeting completion: {$avgCompletion}%";
        } else {
            $insights[] = "Low meeting completion: {$avgCompletion}% - scheduling discipline needed";
        }

        // High-value client insight
        $avgHvcRate = $stats['average_rates']['high_value_client_rate'] ?? 0;
        if ($avgHvcRate > 30) {
            $insights[] = "Good focus on high-value clients: {$avgHvcRate}% of meetings";
        } elseif ($avgHvcRate > 15) {
            $insights[] = "Moderate high-value client focus: {$avgHvcRate}%";
        } else {
            $insights[] = "Low high-value client focus: Only {$avgHvcRate}% of meetings with top clients";
        }

        // Wastage insight
        $avgWastage = $stats['average_rates']['wastage_rate'] ?? 0;
        if ($avgWastage > 40) {
            $insights[] = "High wastage rate: {$avgWastage}% of meetings result in incomplete or no-RP outcomes";
        } elseif ($avgWastage > 25) {
            $insights[] = "Moderate wastage: {$avgWastage}% of meetings not productive";
        } else {
            $insights[] = "Efficient meeting execution: Only {$avgWastage}% wastage";
        }

        return $insights;
    }

    /**
     * Prepare summary with company data
     */
    private function prepare_summary($users, $teamStats, $companyAnalysis, $categorizedCompanies)
    {
        $totalCompanies = $companyAnalysis['total_companies'] ?? 0;
        $activeCompanies = $companyAnalysis['companies_by_status']['Start'] ?? 0;
        $pendingCompanies = $companyAnalysis['companies_by_status']['Pending'] ?? 0;
        $potentialCompanies = $companyAnalysis['companies_by_potential']['yes'] ?? 0;
        $positiveCompanies = $categorizedCompanies['positive_count'] ?? 0;
        $negativeCompanies = $categorizedCompanies['negative_count'] ?? 0;

        return [
            'total_users' => count($users),
            'total_meetings' => $teamStats['aggregate_totals']['total_plan_meetings'] ?? 0,
            'completed_meetings' => $teamStats['aggregate_totals']['complete_meetings'] ?? 0,
            'rp_meetings' => $teamStats['aggregate_totals']['total_RP_meetings'] ?? 0,
            'average_completion_rate' => $teamStats['average_rates']['completion_rate'] ?? 0 . '%',
            'average_rp_conversion' => $teamStats['average_rates']['rp_conversion_rate'] ?? 0 . '%',
            'average_productivity' => $teamStats['average_rates']['productivity_score'] ?? 0 . '/100',
            'total_companies' => $totalCompanies,
            'active_companies' => $activeCompanies,
            'pending_companies' => $pendingCompanies,
            'potential_companies' => $potentialCompanies,
            'positive_companies' => $positiveCompanies,
            'negative_companies' => $negativeCompanies,
            'positive_negative_ratio' => $totalCompanies > 0 ? round(($positiveCompanies / $totalCompanies) * 100, 2) . '%' : '0%',
            'key_insights' => $teamStats['key_insights'],
            'performance_distribution' => $teamStats['performance_distribution']
        ];
    }

    /**
     * Get empty team stats
     */
    private function get_empty_team_stats()
    {
        return [
            'total_users' => 0,
            'aggregate_totals' => [],
            'average_rates' => [],
            'performance_distribution' => [],
            'key_insights' => []
        ];
    }

    /**
     * Generate comprehensive analysis
     */
    private function generate_comprehensive_analysis($message, $specificUser, $analysisData)
    {
        $lowerMessage = strtolower(trim($message));
        
        if ($specificUser) {
            return $this->generate_specific_user_analysis($specificUser, $analysisData, $message);
        } else {
            return $this->generate_team_analysis($analysisData, $message);
        }
    }

    /**
     * Generate specific user analysis
     */
    private function generate_specific_user_analysis($user, $teamData, $originalMessage)
    {
        $userData = $user['raw_data'];
        $metrics = $user['calculated_metrics'];
        $companyData = $this->get_user_company_data($user['user_id'], $teamData['company_data']);
        $categorizedData = $this->get_user_categorized_companies($user['user_id'], $teamData['categorized_companies']);
        
        // Format user data for analysis
        $formattedData = $this->format_user_data_for_analysis($userData, $metrics, $companyData, $teamData);
        
        // Create a clear, structured analysis
        $analysis = "SALES PERFORMANCE ANALYSIS: {$user['username']}\n";
        $analysis .= "=============================================\n\n";
        
        $analysis .= "OVERVIEW:\n";
        $analysis .= "- User ID: {$user['user_id']}\n";
        $analysis .= "- Rank: #{$user['rank']} out of " . count($teamData['users']) . "\n";
        $analysis .= "- Productivity Score: {$metrics['productivity_score']}/100\n\n";
        
        $analysis .= "MEETING PERFORMANCE:\n";
        $analysis .= "- Total Meetings: {$userData['total_plan_meetings']}\n";
        $analysis .= "- Completed: {$userData['complete_meetings']} ({$metrics['completion_rate']}%)\n";
        $analysis .= "- Incomplete: {$userData['not_complete_meetings']} ({$metrics['incompletion_rate']}%)\n";
        $analysis .= "- RP Meetings: {$userData['total_RP_meetings']} ({$metrics['rp_conversion_rate']}%)\n";
        $analysis .= "- NO RP Meetings: {$userData['total_NO_RP_meetings']} ({$metrics['no_rp_rate']}%)\n\n";
        
        $analysis .= "COMPANY SENTIMENT ANALYSIS:\n";
        $analysis .= "- Total Companies Engaged: " . count($companyData) . "\n";
        $analysis .= "- Positive Companies: {$categorizedData['positive_count']}\n";
        $analysis .= "- Negative Companies: {$categorizedData['negative_count']}\n";
        $analysis .= "- Positive Ratio: " . (count($companyData) > 0 ? round(($categorizedData['positive_count'] / count($companyData)) * 100, 2) . "%" : "0%") . "\n\n";
        
        if (!empty($categorizedData['positive_companies'])) {
            $analysis .= "TOP 3 POSITIVE COMPANIES:\n";
            $count = min(3, count($categorizedData['positive_companies']));
            for ($i = 0; $i < $count; $i++) {
                $company = $categorizedData['positive_companies'][$i];
                $analysis .= ($i + 1) . ". {$company['company_name']} (CID: {$company['cid']})\n";
                $analysis .= "   Status: {$company['current_status']}\n";
                $analysis .= "   Cluster: {$company['cluster_name']}\n";
                $analysis .= "   MOM Remarks: " . (strlen($company['mom_remarks']) > 100 ? substr($company['mom_remarks'], 0, 100) . "..." : $company['mom_remarks']) . "\n\n";
            }
        }
        
        $analysis .= "MEETING TYPE BREAKDOWN:\n";
        $analysis .= "- Scheduled: {$userData['total_sheduled_meetings']} ({$metrics['scheduled_percentage']}%)\n";
        $analysis .= "- Barge-in: {$userData['total_barg_meetings']} ({$metrics['barge_in_percentage']}%)\n";
        $analysis .= "- Join: {$userData['total_join_meetings']} ({$metrics['join_percentage']}%)\n";
        $analysis .= "- Video: {$userData['total_vm_meetings']} ({$metrics['video_percentage']}%)\n\n";
        
        $analysis .= "CLIENT QUALITY ANALYSIS:\n";
        $analysis .= "- Potential Clients: {$userData['total_potential_meetings']} ({$metrics['potential_client_rate']}%)\n";
        $analysis .= "- High-Value Clients: {$metrics['high_value_client_rate']}%\n";
        $analysis .= "- Top Spenders: {$userData['total_topspender_meetings']}\n";
        $analysis .= "- Key Companies: {$userData['total_keycompany_meetings']}\n";
        $analysis .= "- Priority Clients: {$userData['total_priorityc_meetings']}\n\n";
        
        $analysis .= "EFFICIENCY METRICS:\n";
        $analysis .= "- Wastage Rate: {$metrics['wastage_rate']}%\n";
        $analysis .= "- Conversion Efficiency: {$metrics['conversion_efficiency']}%\n";
        $analysis .= "- MOM Completion: {$metrics['mom_completion_rate']}%\n\n";
        
        // Add company-specific details if available
        if (!empty($companyData)) {
            $analysis .= "RECENT COMPANY MEETINGS:\n";
            $recentCompanies = array_slice($companyData, 0, 3);
            foreach ($recentCompanies as $company) {
                $analysis .= "- {$company['company_name']}: {$company['meeting_type']} - Status: {$company['status']}\n";
            }
            $analysis .= "\n";
        }
        
        $analysis .= "TEAM COMPARISON:\n";
        $analysis .= "- Team Avg Completion: " . ($teamData['team_statistics']['average_rates']['completion_rate'] ?? 0) . "%\n";
        $analysis .= "- Team Avg RP Rate: " . ($teamData['team_statistics']['average_rates']['rp_conversion_rate'] ?? 0) . "%\n";
        $analysis .= "- Team Avg Productivity: " . ($teamData['team_statistics']['average_rates']['productivity_score'] ?? 0) . "/100\n\n";
        
        $analysis .= "RECOMMENDATIONS:\n";
        
        // Generate recommendations based on performance
        if ($metrics['completion_rate'] < 50) {
            $analysis .= "1. Improve meeting completion rate - focus on better scheduling and follow-up\n";
        }
        
        if ($metrics['rp_conversion_rate'] < 20) {
            $analysis .= "2. Increase RP meetings - work on qualifying leads better before meetings\n";
        }
        
        if ($metrics['wastage_rate'] > 40) {
            $analysis .= "3. Reduce wasted meetings - better pre-meeting preparation needed\n";
        }
        
        if ($metrics['high_value_client_rate'] < 15) {
            $analysis .= "4. Focus more on high-value clients - prioritize top spenders and key companies\n";
        }
        
        if ($metrics['mom_completion_rate'] < 60) {
            $analysis .= "5. Improve MOM documentation - critical for follow-up and deal progression\n";
        }
        
        if ($categorizedData['negative_count'] > $categorizedData['positive_count']) {
            $analysis .= "6. High negative company ratio - focus on improving engagement quality\n";
        }
        
        if (empty($companyData) && $userData['total_plan_meetings'] > 0) {
            $analysis .= "7. No company details found for meetings - ensure proper meeting documentation\n";
        }
        
        return $analysis;
    }

    /**
     * Get user company data
     */
    private function get_user_company_data($userId, $companyAnalysis)
    {
        if (empty($companyAnalysis['meeting_details'])) {
            return [];
        }
        
        $userCompanies = [];
        foreach ($companyAnalysis['meeting_details'] as $meeting) {
            if ($meeting['user_id'] == $userId) {
                $userCompanies[] = $meeting;
            }
        }
        
        return $userCompanies;
    }
    
    /**
     * Get user categorized companies
     */
    private function get_user_categorized_companies($userId, $categorizedCompanies)
    {
        $userPositive = [];
        $userNegative = [];
        
        // Filter positive companies for this user
        foreach ($categorizedCompanies['positive_companies'] as $company) {
            // Note: We need to check if this company belongs to the user
            // This would require additional logic to map companies to users
            // For now, we'll return all categorized companies
            $userPositive[] = $company;
        }
        
        // Filter negative companies for this user
        foreach ($categorizedCompanies['negative_companies'] as $company) {
            $userNegative[] = $company;
        }
        
        return [
            'positive_companies' => array_slice($userPositive, 0, 10),
            'negative_companies' => array_slice($userNegative, 0, 10),
            'positive_count' => count($userPositive),
            'negative_count' => count($userNegative)
        ];
    }

    /**
     * Format user data for analysis
     */
    private function format_user_data_for_analysis($userData, $metrics, $companyData, $teamData)
    {
        $output = "User Performance Data\n";
        $output .= "Total Meetings: {$userData['total_plan_meetings']}\n";
        $output .= "Completed: {$userData['complete_meetings']} ({$metrics['completion_rate']}%)\n";
        $output .= "RP Meetings: {$userData['total_RP_meetings']} ({$metrics['rp_conversion_rate']}%)\n";
        $output .= "Productivity Score: {$metrics['productivity_score']}/100\n";
        
        if (!empty($companyData)) {
            $output .= "Company Meetings: " . count($companyData) . "\n";
        }
        
        return $output;
    }

    /**
     * Generate team analysis
     */
    private function generate_team_analysis($teamData, $originalMessage)
    {
        // Format team data for analysis
        $formattedData = $this->format_team_data_for_analysis($teamData);
        
        $analysis = "TEAM SALES PERFORMANCE REPORT\n";
        $analysis .= "Period: {$teamData['period']}\n";
        $analysis .= "Total Users: {$teamData['team_statistics']['total_users']}\n";
        $analysis .= "=============================================\n\n";
        
        $analysis .= "OVERALL PERFORMANCE:\n";
        $agg = $teamData['team_statistics']['aggregate_totals'];
        $analysis .= "- Total Meetings: " . ($agg['total_plan_meetings'] ?? 0) . "\n";
        $analysis .= "- Completed Meetings: " . ($agg['complete_meetings'] ?? 0) . "\n";
        $analysis .= "- RP Meetings: " . ($agg['total_RP_meetings'] ?? 0) . "\n";
        $analysis .= "- Fresh Meetings: " . ($agg['total_fresh_meetings'] ?? 0) . "\n";
        $analysis .= "- Re-Meetings: " . ($agg['total_re_meetings'] ?? 0) . "\n\n";
        
        $analysis .= "COMPANY SENTIMENT ANALYSIS:\n";
        $categorized = $teamData['categorized_companies'];
        $analysis .= "- Total Companies: {$categorized['total_companies']}\n";
        $analysis .= "- Positive Companies: {$categorized['positive_count']}\n";
        $analysis .= "- Negative Companies: {$categorized['negative_count']}\n";
        $analysis .= "- Positive/Negative Ratio: " . ($categorized['total_companies'] > 0 ? round(($categorized['positive_count'] / $categorized['total_companies']) * 100, 2) . "%" : "0%") . "\n\n";
        
        if (!empty($categorized['positive_companies'])) {
            $analysis .= "TOP 5 POSITIVE COMPANIES:\n";
            $count = min(5, count($categorized['positive_companies']));
            for ($i = 0; $i < $count; $i++) {
                $company = $categorized['positive_companies'][$i];
                $analysis .= ($i + 1) . ". {$company['company_name']} (CID: {$company['cid']})\n";
                $analysis .= "   Status: {$company['current_status']}\n";
                $analysis .= "   Cluster: {$company['cluster_name']}\n";
                $analysis .= "   Business Potential: {$company['business_potentional_client']}\n";
                $analysis .= "   MOM Remarks: " . (strlen($company['mom_remarks']) > 80 ? substr($company['mom_remarks'], 0, 80) . "..." : $company['mom_remarks']) . "\n\n";
            }
        }
        
        if (!empty($categorized['negative_companies'])) {
            $analysis .= "TOP 5 NEGATIVE COMPANIES (NEED ATTENTION):\n";
            $count = min(5, count($categorized['negative_companies']));
            for ($i = 0; $i < $count; $i++) {
                $company = $categorized['negative_companies'][$i];
                $analysis .= ($i + 1) . ". {$company['company_name']} (CID: {$company['cid']})\n";
                $analysis .= "   Status: {$company['current_status']}\n";
                $analysis .= "   Cluster: {$company['cluster_name']}\n";
                $analysis .= "   Business Potential: {$company['business_potentional_client']}\n";
                $analysis .= "   MOM Remarks: " . (strlen($company['mom_remarks']) > 80 ? substr($company['mom_remarks'], 0, 80) . "..." : $company['mom_remarks']) . "\n\n";
            }
        }
        
        $analysis .= "AVERAGE RATES:\n";
        $rates = $teamData['team_statistics']['average_rates'];
        $analysis .= "- Completion Rate: " . ($rates['completion_rate'] ?? 0) . "%\n";
        $analysis .= "- RP Conversion Rate: " . ($rates['rp_conversion_rate'] ?? 0) . "%\n";
        $analysis .= "- Productivity Score: " . ($rates['productivity_score'] ?? 0) . "/100\n";
        $analysis .= "- Wastage Rate: " . ($rates['wastage_rate'] ?? 0) . "%\n";
        $analysis .= "- High-Value Client Focus: " . ($rates['high_value_client_rate'] ?? 0) . "%\n\n";
        
        // Company data summary
        $companySummary = $teamData['company_data'];
        $analysis .= "COMPANY ENGAGEMENT:\n";
        $analysis .= "- Total Companies: " . ($companySummary['total_companies'] ?? 0) . "\n";
        
        if (!empty($companySummary['companies_by_status'])) {
            $analysis .= "- Meeting Status Breakdown:\n";
            foreach ($companySummary['companies_by_status'] as $status => $count) {
                $analysis .= "  • {$status}: {$count}\n";
            }
        }
        
        if (!empty($companySummary['companies_by_potential'])) {
            $analysis .= "- Potential Clients: " . ($companySummary['companies_by_potential']['yes'] ?? 0) . "\n";
        }
        
        if (!empty($companySummary['companies_by_client_type'])) {
            $analysis .= "- Client Type Distribution:\n";
            foreach ($companySummary['companies_by_client_type'] as $type => $count) {
                $analysis .= "  • " . ucfirst($type) . ": {$count}\n";
            }
        }
        $analysis .= "\n";
        
        $analysis .= "PERFORMANCE DISTRIBUTION:\n";
        $dist = $teamData['team_statistics']['performance_distribution'];
        $analysis .= "- Excellent (>80): {$dist['excellent']} users\n";
        $analysis .= "- Good (60-80): {$dist['good']} users\n";
        $analysis .= "- Average (40-60): {$dist['average']} users\n";
        $analysis .= "- Poor (<40): {$dist['poor']} users\n\n";
        
        $analysis .= "TOP PERFORMERS:\n";
        $topPerformers = array_slice($teamData['users'], 0, 3);
        foreach ($topPerformers as $index => $user) {
            $analysis .= ($index + 1) . ". {$user['username']} - Score: " . 
                        ($user['calculated_metrics']['productivity_score'] ?? 0) . "/100, " .
                        "RP Rate: " . ($user['calculated_metrics']['rp_conversion_rate'] ?? 0) . "%\n";
        }
        $analysis .= "\n";
        
        $analysis .= "KEY INSIGHTS:\n";
        foreach ($teamData['team_statistics']['key_insights'] as $insight) {
            $analysis .= "- {$insight}\n";
        }
        $analysis .= "\n";
        
        $analysis .= "RECOMMENDATIONS:\n";
        
        // Generate team-level recommendations
        if (($rates['completion_rate'] ?? 0) < 60) {
            $analysis .= "1. Team needs to improve meeting completion rates\n";
        }
        
        if (($rates['rp_conversion_rate'] ?? 0) < 25) {
            $analysis .= "2. Focus on reaching decision-makers (RPs) in meetings\n";
        }
        
        if (($rates['wastage_rate'] ?? 0) > 35) {
            $analysis .= "3. Reduce meeting wastage through better qualification\n";
        }
        
        if ($categorized['negative_count'] > $categorized['positive_count']) {
            $analysis .= "4. High negative sentiment among companies - improve engagement quality\n";
        }
        
        if (($companySummary['total_companies'] ?? 0) < count($teamData['users']) * 5) {
            $analysis .= "5. Increase company engagement - target more client meetings\n";
        }
        
        return $analysis;
    }

    /**
     * Format team data for analysis
     */
    private function format_team_data_for_analysis($teamData)
    {
        $output = "Team Performance Overview\n";
        $output .= "Total Users: {$teamData['team_statistics']['total_users']}\n";
        $output .= "Total Meetings: " . ($teamData['team_statistics']['aggregate_totals']['total_plan_meetings'] ?? 0) . "\n";
        $output .= "Average Productivity: " . ($teamData['team_statistics']['average_rates']['productivity_score'] ?? 0) . "/100\n";
        
        return $output;
    }

    /**
     * Extract specific user request
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
     * Prepare meeting frontend data
     */
    public function prepare_meeting_frontend_data($analysisData, $specificUser = null)
    {
        $response = [
            'status' => 'empty',
            'message' => 'No meeting data available for analysis',
            'tableData' => null,
            'chartData' => null,
            'summaryData' => [],
            'specificUserData' => null,
            'comparativeData' => null,
            'companyData' => null,
            'categorizedCompanies' => null,
            'period' => $analysisData['period'] ?? 'N/A',
            'generated_at' => date('Y-m-d H:i:s')
        ];

        if (empty($analysisData['users']) || !is_array($analysisData['users'])) {
            return $response;
        }

        $response['tableData'] = $this->prepare_meeting_table_data($analysisData['users']);
        $response['chartData'] = $this->prepare_meeting_chart_data($analysisData);
        $response['summaryData'] = $this->prepare_meeting_summary_data($analysisData);
        $response['companyData'] = $this->prepare_company_frontend_data($analysisData['company_data']);
        $response['categorizedCompanies'] = $this->prepare_categorized_companies_frontend_data($analysisData['categorized_companies']);
        
        if ($specificUser) {
            $userData = $this->find_user_data($analysisData['users'], $specificUser['user_id']);
            if ($userData) {
                $response['specificUserData'] = $this->prepare_specific_user_frontend_data($userData, $analysisData);
                $response['comparativeData'] = $this->prepare_comparative_data($userData, $analysisData);
            }
        }
        
        $response['status'] = 'success';
        $response['message'] = 'Sales meeting analysis data prepared successfully';
        $response['total_users'] = count($analysisData['users']);
        $response['date_range'] = $analysisData['period'];

        return $response;
    }

    /**
     * Prepare meeting table data
     */
    private function prepare_meeting_table_data($users)
    {
        $headers = [
            'Rank', 'Username', 'Total Meetings', 'Completed', 'RP Meetings',
            'RP Rate', 'High Value %', 'Productivity Score', 'MOM Rate',
            'Fresh Meetings', 'Wastage Rate', 'Companies'
        ];

        $rows = [];
        foreach ($users as $user) {
            $rows[] = [
                $user['rank'],
                $user['username'],
                $user['raw_data']['total_plan_meetings'] ?? 0,
                $user['raw_data']['complete_meetings'] ?? 0,
                $user['raw_data']['total_RP_meetings'] ?? 0,
                ($user['calculated_metrics']['rp_conversion_rate'] ?? 0) . '%',
                ($user['calculated_metrics']['high_value_client_rate'] ?? 0) . '%',
                ($user['calculated_metrics']['productivity_score'] ?? 0) . '/100',
                ($user['calculated_metrics']['mom_completion_rate'] ?? 0) . '%',
                $user['raw_data']['total_fresh_meetings'] ?? 0,
                ($user['calculated_metrics']['wastage_rate'] ?? 0) . '%',
                $user['raw_data']['total_plan_meetings'] ?? 0 // Placeholder for company count
            ];
        }

        return [
            'title' => 'Sales Team Meeting Performance',
            'headers' => $headers,
            'rows' => $rows,
            'sortable' => true,
            'paginate' => true,
            'items_per_page' => 10
        ];
    }

    /**
     * Prepare meeting chart data
     */
    private function prepare_meeting_chart_data($analysisData)
    {
        $users = $analysisData['users'];
        usort($users, function($a, $b) {
            return ($b['calculated_metrics']['productivity_score'] ?? 0) - 
                   ($a['calculated_metrics']['productivity_score'] ?? 0);
        });

        $labels = [];
        $productivityData = [];
        $rpData = [];
        $completionData = [];

        foreach ($users as $user) {
            $labels[] = substr($user['username'], 0, 12);
            $productivityData[] = $user['calculated_metrics']['productivity_score'] ?? 0;
            $rpData[] = $user['calculated_metrics']['rp_conversion_rate'] ?? 0;
            $completionData[] = $user['calculated_metrics']['completion_rate'] ?? 0;
        }

        return [
            'performance_comparison' => [
                'type' => 'bar',
                'title' => 'Team Performance Comparison',
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Productivity Score',
                        'data' => $productivityData,
                        'backgroundColor' => '#10a37f',
                        'borderColor' => '#0d8a6a',
                        'borderWidth' => 1
                    ],
                    [
                        'label' => 'RP Conversion Rate (%)',
                        'data' => $rpData,
                        'backgroundColor' => '#5436da',
                        'borderColor' => '#4529b5',
                        'borderWidth' => 1
                    ],
                    [
                        'label' => 'Completion Rate (%)',
                        'data' => $completionData,
                        'backgroundColor' => '#ffa726',
                        'borderColor' => '#f57c00',
                        'borderWidth' => 1
                    ]
                ]
            ],
            'meeting_type_distribution' => $this->prepare_meeting_type_distribution_chart($users),
            'performance_distribution_pie' => $this->prepare_performance_pie_chart($users),
            'high_value_focus_chart' => $this->prepare_high_value_focus_chart($users),
            'company_status_chart' => $this->prepare_company_status_chart($analysisData['company_data']),
            'company_sentiment_chart' => $this->prepare_company_sentiment_chart($analysisData['categorized_companies'])
        ];
    }

    /**
     * Prepare company sentiment chart
     */
    private function prepare_company_sentiment_chart($categorizedCompanies)
    {
        $positiveCount = $categorizedCompanies['positive_count'] ?? 0;
        $negativeCount = $categorizedCompanies['negative_count'] ?? 0;
        $total = $positiveCount + $negativeCount;
        
        if ($total == 0) {
            return [
                'type' => 'doughnut',
                'title' => 'Company Sentiment Analysis',
                'labels' => ['No Data'],
                'datasets' => [[
                    'data' => [1],
                    'backgroundColor' => ['#cccccc']
                ]]
            ];
        }

        return [
            'type' => 'doughnut',
            'title' => 'Company Sentiment Analysis',
            'labels' => ['Positive Companies', 'Negative Companies'],
            'datasets' => [[
                'data' => [$positiveCount, $negativeCount],
                'backgroundColor' => ['#4caf50', '#f44336'],
                'borderColor' => '#2a2a2a',
                'borderWidth' => 1
            ]]
        ];
    }

    /**
     * Prepare company status chart
     */
    private function prepare_company_status_chart($companyData)
    {
        if (empty($companyData['companies_by_status'])) {
            return [
                'type' => 'doughnut',
                'title' => 'Meeting Status Distribution',
                'labels' => ['No Data'],
                'datasets' => [[
                    'data' => [1],
                    'backgroundColor' => ['#cccccc']
                ]]
            ];
        }

        $labels = [];
        $data = [];
        $colors = [
            'Start' => '#4caf50',
            'Pending' => '#ff9800',
            'Complete' => '#2196f3',
            'Cancelled' => '#f44336'
        ];

        foreach ($companyData['companies_by_status'] as $status => $count) {
            $labels[] = $status;
            $data[] = $count;
        }

        // Generate colors
        $backgroundColors = [];
        foreach ($labels as $label) {
            $backgroundColors[] = $colors[$label] ?? '#cccccc';
        }

        return [
            'type' => 'doughnut',
            'title' => 'Meeting Status Distribution',
            'labels' => $labels,
            'datasets' => [[
                'data' => $data,
                'backgroundColor' => $backgroundColors,
                'borderColor' => '#2a2a2a',
                'borderWidth' => 1
            ]]
        ];
    }

    /**
     * Prepare meeting type distribution chart
     */
    private function prepare_meeting_type_distribution_chart($users)
    {
        $labels = [];
        $scheduledData = [];
        $bargeData = [];
        $joinData = [];
        $videoData = [];

        foreach ($users as $user) {
            $labels[] = substr($user['username'], 0, 10);
            $scheduledData[] = $user['calculated_metrics']['scheduled_percentage'] ?? 0;
            $bargeData[] = $user['calculated_metrics']['barge_in_percentage'] ?? 0;
            $joinData[] = $user['calculated_metrics']['join_percentage'] ?? 0;
            $videoData[] = $user['calculated_metrics']['video_percentage'] ?? 0;
        }

        return [
            'type' => 'bar',
            'title' => 'Meeting Type Distribution',
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Scheduled (%)',
                    'data' => $scheduledData,
                    'backgroundColor' => '#42a5f5',
                    'borderColor' => '#2196f3',
                    'borderWidth' => 1
                ],
                [
                    'label' => 'Barge-in (%)',
                    'data' => $bargeData,
                    'backgroundColor' => '#ab47bc',
                    'borderColor' => '#9c27b0',
                    'borderWidth' => 1
                ],
                [
                    'label' => 'Join (%)',
                    'data' => $joinData,
                    'backgroundColor' => '#26c6da',
                    'borderColor' => '#00acc1',
                    'borderWidth' => 1
                ],
                [
                    'label' => 'Video (%)',
                    'data' => $videoData,
                    'backgroundColor' => '#ffa726',
                    'borderColor' => '#ff9800',
                    'borderWidth' => 1
                ]
            ]
        ];
    }

    /**
     * Prepare performance pie chart
     */
    private function prepare_performance_pie_chart($users)
    {
        $distribution = [
            'Excellent (>80)' => 0,
            'Good (60-80)' => 0,
            'Average (40-60)' => 0,
            'Poor (<40)' => 0
        ];

        foreach ($users as $user) {
            $score = $user['calculated_metrics']['productivity_score'] ?? 0;
            
            if ($score > 80) {
                $distribution['Excellent (>80)']++;
            } elseif ($score >= 60) {
                $distribution['Good (60-80)']++;
            } elseif ($score >= 40) {
                $distribution['Average (40-60)']++;
            } else {
                $distribution['Poor (<40)']++;
            }
        }

        return [
            'type' => 'doughnut',
            'title' => 'Performance Distribution',
            'labels' => array_keys($distribution),
            'datasets' => [
                [
                    'data' => array_values($distribution),
                    'backgroundColor' => ['#10a37f', '#5436da', '#ffa726', '#ff6b6b'],
                    'borderColor' => '#2a2a2a',
                    'borderWidth' => 1
                ]
            ]
        ];
    }

    /**
     * Prepare high value focus chart
     */
    private function prepare_high_value_focus_chart($users)
    {
        $labels = [];
        $potentialData = [];
        $topSpenderData = [];
        $keyCompanyData = [];

        foreach ($users as $user) {
            $labels[] = substr($user['username'], 0, 10);
            $raw = $user['raw_data'];
            $total = $raw['total_plan_meetings'] ?? 1;
            
            $potentialData[] = round(($raw['total_potential_meetings'] ?? 0) / $total * 100, 2);
            $topSpenderData[] = round(($raw['total_topspender_meetings'] ?? 0) / $total * 100, 2);
            $keyCompanyData[] = round(($raw['total_keycompany_meetings'] ?? 0) / $total * 100, 2);
        }

        return [
            'type' => 'bar',
            'title' => 'High-Value Client Focus',
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Potential Clients (%)',
                    'data' => $potentialData,
                    'backgroundColor' => '#66bb6a',
                    'borderColor' => '#4caf50',
                    'borderWidth' => 1
                ],
                [
                    'label' => 'Top Spenders (%)',
                    'data' => $topSpenderData,
                    'backgroundColor' => '#ffa726',
                    'borderColor' => '#ff9800',
                    'borderWidth' => 1
                ],
                [
                    'label' => 'Key Companies (%)',
                    'data' => $keyCompanyData,
                    'backgroundColor' => '#42a5f5',
                    'borderColor' => '#2196f3',
                    'borderWidth' => 1
                ]
            ]
        ];
    }

    /**
     * Prepare company frontend data
     */
    private function prepare_company_frontend_data($companyData)
    {
        if (empty($companyData)) {
            return null;
        }

        return [
            'total_companies' => $companyData['total_companies'] ?? 0,
            'companies_by_status' => $companyData['companies_by_status'] ?? [],
            'companies_by_potential' => $companyData['companies_by_potential'] ?? [],
            'companies_by_client_type' => $companyData['companies_by_client_type'] ?? [],
            'meeting_details' => array_slice($companyData['meeting_details'] ?? [], 0, 20), // Limit to 20 for display
            'companies_by_user' => $companyData['companies_by_user'] ?? []
        ];
    }

    /**
     * Prepare categorized companies frontend data
     */
    private function prepare_categorized_companies_frontend_data($categorizedCompanies)
    {
        if (empty($categorizedCompanies)) {
            return null;
        }

        return [
            'positive_companies' => $categorizedCompanies['positive_companies'] ?? [],
            'negative_companies' => $categorizedCompanies['negative_companies'] ?? [],
            'positive_count' => $categorizedCompanies['positive_count'] ?? 0,
            'negative_count' => $categorizedCompanies['negative_count'] ?? 0,
            'total_companies' => $categorizedCompanies['total_companies'] ?? 0,
            'positive_negative_ratio' => $categorizedCompanies['total_companies'] > 0 ? 
                round(($categorizedCompanies['positive_count'] / $categorizedCompanies['total_companies']) * 100, 2) . '%' : '0%'
        ];
    }

    /**
     * Prepare meeting summary data
     */
    private function prepare_meeting_summary_data($analysisData)
    {
        $teamStats = $analysisData['team_statistics'];
        $companySummary = $analysisData['company_data'];
        $categorizedCompanies = $analysisData['categorized_companies'];

        return [
            'period' => $analysisData['period'],
            'total_users' => $teamStats['total_users'],
            'total_meetings' => $teamStats['aggregate_totals']['total_plan_meetings'] ?? 0,
            'completed_meetings' => $teamStats['aggregate_totals']['complete_meetings'] ?? 0,
            'rp_meetings' => $teamStats['aggregate_totals']['total_RP_meetings'] ?? 0,
            'average_completion_rate' => $teamStats['average_rates']['completion_rate'] ?? 0 . '%',
            'average_rp_rate' => $teamStats['average_rates']['rp_conversion_rate'] ?? 0 . '%',
            'average_productivity' => $teamStats['average_rates']['productivity_score'] ?? 0 . '/100',
            'average_wastage_rate' => $teamStats['average_rates']['wastage_rate'] ?? 0 . '%',
            'total_companies' => $companySummary['total_companies'] ?? 0,
            'active_companies' => $companySummary['companies_by_status']['Start'] ?? 0,
            'pending_companies' => $companySummary['companies_by_status']['Pending'] ?? 0,
            'potential_companies' => $companySummary['companies_by_potential']['yes'] ?? 0,
            'positive_companies' => $categorizedCompanies['positive_count'] ?? 0,
            'negative_companies' => $categorizedCompanies['negative_count'] ?? 0,
            'positive_negative_ratio' => $categorizedCompanies['total_companies'] > 0 ? 
                round(($categorizedCompanies['positive_count'] / $categorizedCompanies['total_companies']) * 100, 2) . '%' : '0%',
            'key_insights' => $teamStats['key_insights'],
            'performance_distribution' => $teamStats['performance_distribution'],
            'top_performers' => $this->get_top_performers_list($analysisData['users']),
            'areas_needing_attention' => $this->get_areas_needing_attention($analysisData['users']),
            'top_positive_companies' => array_slice($categorizedCompanies['positive_companies'] ?? [], 0, 3),
            'top_negative_companies' => array_slice($categorizedCompanies['negative_companies'] ?? [], 0, 3)
        ];
    }

    /**
     * Get top performers list
     */
    private function get_top_performers_list($users)
    {
        usort($users, function($a, $b) {
            return ($b['calculated_metrics']['productivity_score'] ?? 0) - 
                   ($a['calculated_metrics']['productivity_score'] ?? 0);
        });

        $topPerformers = [];
        $count = min(3, count($users));
        
        for ($i = 0; $i < $count; $i++) {
            $user = $users[$i];
            $topPerformers[] = [
                'username' => $user['username'],
                'productivity_score' => $user['calculated_metrics']['productivity_score'] ?? 0,
                'rp_rate' => $user['calculated_metrics']['rp_conversion_rate'] ?? 0,
                'strength' => $this->identify_top_performer_strength($user)
            ];
        }

        return $topPerformers;
    }

    /**
     * Identify top performer strength
     */
    private function identify_top_performer_strength($user)
    {
        $metrics = $user['calculated_metrics'];
        
        if (($metrics['rp_conversion_rate'] ?? 0) > 40) {
            return 'Excellent at reaching decision-makers';
        } elseif (($metrics['high_value_client_rate'] ?? 0) > 30) {
            return 'Great focus on high-value clients';
        } elseif (($metrics['completion_rate'] ?? 0) > 80) {
            return 'Strong meeting completion discipline';
        } else {
            return 'Balanced performance across metrics';
        }
    }

    /**
     * Get areas needing attention
     */
    private function get_areas_needing_attention($users)
    {
        $areas = [];
        
        // Calculate team averages for comparison
        $totalUsers = count($users);
        $totalRpRate = 0;
        $totalCompletion = 0;
        $totalMomRate = 0;
        
        foreach ($users as $user) {
            $totalRpRate += $user['calculated_metrics']['rp_conversion_rate'] ?? 0;
            $totalCompletion += $user['calculated_metrics']['completion_rate'] ?? 0;
            $totalMomRate += $user['calculated_metrics']['mom_completion_rate'] ?? 0;
        }
        
        $avgRpRate = $totalRpRate / max($totalUsers, 1);
        $avgCompletion = $totalCompletion / max($totalUsers, 1);
        $avgMomRate = $totalMomRate / max($totalUsers, 1);
        
        // Identify areas below average
        if ($avgRpRate < 30) {
            $areas[] = 'Low decision-maker engagement across team';
        }
        
        if ($avgCompletion < 60) {
            $areas[] = 'Poor meeting completion discipline';
        }
        
        if ($avgMomRate < 50) {
            $areas[] = 'Weak follow-up and documentation';
        }
        
        return array_slice($areas, 0, 3);
    }

    /**
     * Find user data by ID
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
     * Prepare specific user frontend data
     */
    private function prepare_specific_user_frontend_data($user, $teamData)
    {
        $companyData = $this->get_user_company_data($user['user_id'], $teamData['company_data']);
        $categorizedData = $this->get_user_categorized_companies($user['user_id'], $teamData['categorized_companies']);
        
        return [
            'basic_info' => [
                'username' => $user['username'],
                'user_id' => $user['user_id'],
                'rank' => $user['rank'],
                'total_users' => count($teamData['users']),
                'productivity_score' => $user['calculated_metrics']['productivity_score'] ?? 0
            ],
            'key_metrics' => [
                'completion_rate' => $user['calculated_metrics']['completion_rate'] ?? 0,
                'rp_conversion_rate' => $user['calculated_metrics']['rp_conversion_rate'] ?? 0,
                'wastage_rate' => $user['calculated_metrics']['wastage_rate'] ?? 0,
                'high_value_rate' => $user['calculated_metrics']['high_value_client_rate'] ?? 0,
                'mom_completion_rate' => $user['calculated_metrics']['mom_completion_rate'] ?? 0
            ],
            'meeting_distribution' => [
                'scheduled' => $user['raw_data']['total_sheduled_meetings'] ?? 0,
                'barge_in' => $user['raw_data']['total_barg_meetings'] ?? 0,
                'join' => $user['raw_data']['total_join_meetings'] ?? 0,
                'video' => $user['raw_data']['total_vm_meetings'] ?? 0
            ],
            'client_focus' => [
                'potential' => $user['raw_data']['total_potential_meetings'] ?? 0,
                'top_spender' => $user['raw_data']['total_topspender_meetings'] ?? 0,
                'key_company' => $user['raw_data']['total_keycompany_meetings'] ?? 0,
                'priority' => $user['raw_data']['total_priorityc_meetings'] ?? 0
            ],
            'company_data' => [
                'total_companies' => count($companyData),
                'companies' => array_slice($companyData, 0, 10) // Limit to 10 for display
            ],
            'company_sentiment' => [
                'positive_count' => $categorizedData['positive_count'] ?? 0,
                'negative_count' => $categorizedData['negative_count'] ?? 0,
                'positive_companies' => array_slice($categorizedData['positive_companies'] ?? [], 0, 5),
                'negative_companies' => array_slice($categorizedData['negative_companies'] ?? [], 0, 5)
            ]
        ];
    }

    /**
     * Prepare comparative data
     */
    private function prepare_comparative_data($user, $teamData)
    {
        $teamStats = $teamData['team_statistics'];
        
        return [
            'completion_rate' => [
                'user' => $user['calculated_metrics']['completion_rate'] ?? 0,
                'team_average' => $teamStats['average_rates']['completion_rate'] ?? 0,
                'difference' => round(($user['calculated_metrics']['completion_rate'] ?? 0) - ($teamStats['average_rates']['completion_rate'] ?? 0), 2)
            ],
            'rp_conversion' => [
                'user' => $user['calculated_metrics']['rp_conversion_rate'] ?? 0,
                'team_average' => $teamStats['average_rates']['rp_conversion_rate'] ?? 0,
                'difference' => round(($user['calculated_metrics']['rp_conversion_rate'] ?? 0) - ($teamStats['average_rates']['rp_conversion_rate'] ?? 0), 2)
            ],
            'productivity' => [
                'user' => $user['calculated_metrics']['productivity_score'] ?? 0,
                'team_average' => $teamStats['average_rates']['productivity_score'] ?? 0,
                'difference' => round(($user['calculated_metrics']['productivity_score'] ?? 0) - ($teamStats['average_rates']['productivity_score'] ?? 0), 2)
            ],
            'rank_context' => [
                'position' => $user['rank'],
                'total_users' => count($teamData['users']),
                'percentile' => round(($user['rank'] / count($teamData['users'])) * 100, 2)
            ]
        ];
    }
}