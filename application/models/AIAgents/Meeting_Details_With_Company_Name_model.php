<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Meeting_Details_With_Company_Name_model extends CI_Model {

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
    public function process_Meeting_Details_With_Company_Name($message, $analysisType, $sdate, $edate) 
    {
        // Get meeting details data
        $totalMeetingsUserByDatas = $this->Menu_model->TotalMeetingDetailsDatas(
            $this->uid, $sdate, $edate, 'total_plan_meetings', $this->uid, NULL
        );

        
  

        // Check if data exists
        if (empty($totalMeetingsUserByDatas) || !is_array($totalMeetingsUserByDatas)) {
            return [
                'content' => "No meeting data found for the period {$sdate} to {$edate}.",
                'data' => [
                    'status' => 'empty',
                    'message' => 'No meeting data available for analysis',
                    'period' => "{$sdate} to {$edate}"
                ]
            ];
        }

        // Extract count from message (top 10, top 20, top 30, top 50)
        $topCount = $this->extract_top_count_from_message($message);
        
        // Generate comprehensive analysis
        $analysis = $this->generate_detailed_company_analysis(
            $totalMeetingsUserByDatas, 
            $message, 
            $topCount,
            $sdate, 
            $edate
        );

        // Prepare frontend data
        $frontendData = $this->prepare_detailed_frontend_data(
            $totalMeetingsUserByDatas, 
            $topCount,
            $sdate, 
            $edate
        );

        return [
            'content' => $analysis,
            'data' => $totalMeetingsUserByDatas
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
     * Generate detailed company analysis
     */
    private function generate_detailed_company_analysis($meetingData, $message, $topCount, $startDate, $endDate)
    {
        // Process and analyze the data
        $analysisResults = $this->analyze_meeting_data_deep($meetingData, $topCount);
        
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
     * Deep analysis of meeting data
     */
    private function analyze_meeting_data_deep($meetingData, $topCount)
    {
        if (empty($meetingData) || !is_array($meetingData)) {
            return [
                'status' => 'empty',
                'message' => 'No meeting data to analyze'
            ];
        }

        $analysis = [
            'total_meetings' => count($meetingData),
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
        foreach ($meetingData as $meeting) {
            $userId = $meeting->task_user_id ?? 0;
            $userName = $meeting->task_username ?? 'Unknown User';
            
            if (!isset($usersData[$userId])) {
                $usersData[$userId] = [
                    'user_id' => $userId,
                    'username' => $userName,
                    'total_meetings' => 0,
                    'completed_meetings' => 0,
                    'pending_meetings' => 0,
                    'rp_meetings' => 0,
                    'no_rp_meetings' => 0,
                    'positive_status' => 0,
                    'negative_status' => 0,
                    'companies' => [],
                    'meetings' => []
                ];
            }
            
            $usersData[$userId]['total_meetings']++;
            
            // Check meeting status
            $status = strtolower($meeting->meetings_status ?? '');
            if ($status === 'close' || $status === 'rpclose') {
                $usersData[$userId]['completed_meetings']++;
            } else {
                $usersData[$userId]['pending_meetings']++;
            }
            
            // Check RP status
            $mtype = strtolower($meeting->mtype ?? '');
            if ($mtype === 'rp') {
                $usersData[$userId]['rp_meetings']++;
            } elseif ($mtype === 'no rp') {
                $usersData[$userId]['no_rp_meetings']++;
            }
            
            // Check sentiment
            $currentStatus = strtolower($meeting->current_status ?? '');
            if (in_array($currentStatus, ['very positive', 'positive', 'excellent', 'good'])) {
                $usersData[$userId]['positive_status']++;
            } elseif (in_array($currentStatus, ['negative', 'not interested', 'rejected', 'tentative'])) {
                $usersData[$userId]['negative_status']++;
            }
            
            // Add company to user's list
            $companyId = $meeting->cid ?? 0;
            $companyName = $meeting->compname ?? 'Unknown Company';
            
            if (!isset($usersData[$userId]['companies'][$companyId])) {
                $usersData[$userId]['companies'][$companyId] = [
                    'company_name' => $companyName,
                    'meeting_count' => 0,
                    'last_meeting_date' => '',
                    'latest_status' => ''
                ];
            }
            
            $usersData[$userId]['companies'][$companyId]['meeting_count']++;
            
            // Update with latest meeting info
            $meetingDate = $meeting->appointmentdatetime ?? '';
            if (empty($usersData[$userId]['companies'][$companyId]['last_meeting_date']) || 
                strtotime($meetingDate) > strtotime($usersData[$userId]['companies'][$companyId]['last_meeting_date'])) {
                $usersData[$userId]['companies'][$companyId]['last_meeting_date'] = $meetingDate;
                $usersData[$userId]['companies'][$companyId]['latest_status'] = $meeting->current_status ?? '';
            }
            
            // Store meeting details
            $usersData[$userId]['meetings'][] = [
                'company_name' => $companyName,
                'company_id' => $companyId,
                'meeting_id' => $meeting->task_id ?? 0,
                'meeting_type' => $meeting->task_name ?? 'Unknown',
                'meeting_date' => $meetingDate,
                'meeting_status' => $meeting->meetings_status ?? '',
                'current_status' => $meeting->current_status ?? '',
                'purpose_achieved' => $meeting->purpose_achieved ?? '',
                'action_taken' => $meeting->actontaken ?? '',
                'remarks' => $meeting->remarks ?? '',
                'mom_remarks' => $meeting->mom_remarks ?? '',
                'mtype' => $meeting->mtype ?? '',
                'potential' => $meeting->potential ?? '',
                'topspender' => $meeting->topspender ?? '',
                'keycompany' => $meeting->keycompany ?? '',
                'priorityc' => $meeting->priorityc ?? '',
                'anchor_clients' => $meeting->anchor_clients ?? '',
                'business_potential' => $meeting->business_potentional_client ?? '',
                'cluster_name' => $meeting->cluster_name ?? '',
                'partner_name' => $meeting->partner_name ?? '',
                'start_time' => $meeting->startm ?? '',
                'close_time' => $meeting->closem ?? '',
                'init_photo' => $meeting->initphoto ?? '',
                'close_photo' => $meeting->cphoto ?? ''
            ];
        }
        
        // Convert to indexed array
        $analysis['users_summary'] = array_values($usersData);
        
        // Analyze individual companies
        $companiesAnalysis = $this->analyze_companies_individually($meetingData);
        $analysis['companies_analysis'] = $companiesAnalysis;
        
        // Calculate performance metrics
        $analysis['performance_metrics'] = $this->calculate_performance_metrics($meetingData);
        
        // Sentiment analysis
        $analysis['sentiment_analysis'] = $this->analyze_sentiment($meetingData);
        
        // Priority flags
        $analysis['priority_flags'] = $this->identify_priority_flags($meetingData);
        
        // Recommendations
        $analysis['recommendations'] = $this->generate_recommendations($meetingData, $usersData);
        
        // Select top companies
        $analysis['top_companies'] = $this->select_top_companies($meetingData, $topCount);
        
        return $analysis;
    }

    /**
     * Analyze companies individually
     */
    private function analyze_companies_individually($meetingData)
    {
        $companies = [];
        
        foreach ($meetingData as $meeting) {
            $companyId = $meeting->cid ?? 0;
            $companyName = $meeting->compname ?? 'Unknown Company';
            
            if (!isset($companies[$companyId])) {
                $companies[$companyId] = [
                    'company_id' => $companyId,
                    'company_name' => $companyName,
                    'total_meetings' => 0,
                    'meetings' => [],
                    'users_engaged' => [],
                    'first_meeting_date' => null,
                    'last_meeting_date' => null,
                    'status_summary' => [],
                    'rp_count' => 0,
                    'no_rp_count' => 0,
                    'positive_interactions' => 0,
                    'negative_interactions' => 0,
                    'priority_flags' => [],
                    'mom_analysis' => []
                ];
            }
            
            $companies[$companyId]['total_meetings']++;
            
            // Track meeting
            $meetingDetails = [
                'meeting_id' => $meeting->task_id ?? 0,
                'meeting_date' => $meeting->appointmentdatetime ?? '',
                'meeting_type' => $meeting->task_name ?? '',
                'status' => $meeting->meetings_status ?? '',
                'current_status' => $meeting->current_status ?? '',
                'purpose_achieved' => $meeting->purpose_achieved ?? '',
                'action_taken' => $meeting->actontaken ?? '',
                'remarks' => $meeting->remarks ?? '',
                'mom_remarks' => $meeting->mom_remarks ?? '',
                'user_id' => $meeting->task_user_id ?? 0,
                'username' => $meeting->task_username ?? ''
            ];
            
            $companies[$companyId]['meetings'][] = $meetingDetails;
            
            // Track users
            $userId = $meeting->task_user_id ?? 0;
            if (!in_array($userId, $companies[$companyId]['users_engaged'])) {
                $companies[$companyId]['users_engaged'][] = $userId;
            }
            
            // Update dates
            $meetingDate = $meeting->appointmentdatetime ?? '';
            if (empty($companies[$companyId]['first_meeting_date']) || 
                strtotime($meetingDate) < strtotime($companies[$companyId]['first_meeting_date'])) {
                $companies[$companyId]['first_meeting_date'] = $meetingDate;
            }
            
            if (empty($companies[$companyId]['last_meeting_date']) || 
                strtotime($meetingDate) > strtotime($companies[$companyId]['last_meeting_date'])) {
                $companies[$companyId]['last_meeting_date'] = $meetingDate;
            }
            
            // Update status summary
            $status = $meeting->current_status ?? '';
            if (!isset($companies[$companyId]['status_summary'][$status])) {
                $companies[$companyId]['status_summary'][$status] = 0;
            }
            $companies[$companyId]['status_summary'][$status]++;
            
            // RP analysis
            $mtype = strtolower($meeting->mtype ?? '');
            if ($mtype === 'rp') {
                $companies[$companyId]['rp_count']++;
            } elseif ($mtype === 'no rp') {
                $companies[$companyId]['no_rp_count']++;
            }
            
            // Sentiment analysis
            $currentStatus = strtolower($meeting->current_status ?? '');
            if (in_array($currentStatus, ['very positive', 'positive', 'excellent', 'good'])) {
                $companies[$companyId]['positive_interactions']++;
            } elseif (in_array($currentStatus, ['negative', 'not interested', 'rejected', 'tentative'])) {
                $companies[$companyId]['negative_interactions']++;
            }
            
            // Priority flags
            $priorityFlags = [];
            if ($meeting->potential === 'yes') $priorityFlags[] = 'High Potential';
            if ($meeting->topspender === 'yes') $priorityFlags[] = 'Top Spender';
            if ($meeting->keycompany === 'yes') $priorityFlags[] = 'Key Company';
            if ($meeting->priorityc === 'yes') $priorityFlags[] = 'Priority Client';
            if ($meeting->anchor_clients === 'yes') $priorityFlags[] = 'Anchor Client';
            
            $companies[$companyId]['priority_flags'] = array_unique(array_merge(
                $companies[$companyId]['priority_flags'], 
                $priorityFlags
            ));
            
            // MOM analysis
            if (!empty($meeting->mom_remarks)) {
                $companies[$companyId]['mom_analysis'][] = [
                    'meeting_id' => $meeting->task_id ?? 0,
                    'date' => $meeting->appointmentdatetime ?? '',
                    'remarks' => $meeting->mom_remarks,
                    'user' => $meeting->task_username ?? ''
                ];
            }
        }
        
        // Convert to indexed array
        return array_values($companies);
    }

    /**
     * Calculate performance metrics
     */
    private function calculate_performance_metrics($meetingData)
    {
        $metrics = [
            'total_meetings' => count($meetingData),
            'completed_meetings' => 0,
            'pending_meetings' => 0,
            'rp_meetings' => 0,
            'no_rp_meetings' => 0,
            'purpose_achieved' => 0,
            'action_taken' => 0,
            'positive_status_count' => 0,
            'negative_status_count' => 0,
            'meetings_by_type' => [],
            'meetings_by_status' => [],
            'meetings_by_cluster' => [],
            'meetings_by_partner' => []
        ];
        
        foreach ($meetingData as $meeting) {
            // Completion status
            $status = strtolower($meeting->meetings_status ?? '');
            if ($status === 'close' || $status === 'rpclose') {
                $metrics['completed_meetings']++;
            } else {
                $metrics['pending_meetings']++;
            }
            
            // RP status
            $mtype = strtolower($meeting->mtype ?? '');
            if ($mtype === 'rp') {
                $metrics['rp_meetings']++;
            } elseif ($mtype === 'no rp') {
                $metrics['no_rp_meetings']++;
            }
            
            // Purpose achieved
            if (strtolower($meeting->purpose_achieved ?? '') === 'yes') {
                $metrics['purpose_achieved']++;
            }
            
            // Action taken
            if (strtolower($meeting->actontaken ?? '') === 'yes') {
                $metrics['action_taken']++;
            }
            
            // Sentiment
            $currentStatus = strtolower($meeting->current_status ?? '');
            if (in_array($currentStatus, ['very positive', 'positive', 'excellent', 'good'])) {
                $metrics['positive_status_count']++;
            } elseif (in_array($currentStatus, ['negative', 'not interested', 'rejected', 'tentative'])) {
                $metrics['negative_status_count']++;
            }
            
            // Meeting type distribution
            $type = $meeting->task_name ?? 'Unknown';
            if (!isset($metrics['meetings_by_type'][$type])) {
                $metrics['meetings_by_type'][$type] = 0;
            }
            $metrics['meetings_by_type'][$type]++;
            
            // Meeting status distribution
            $meetingStatus = $meeting->meetings_status ?? 'Unknown';
            if (!isset($metrics['meetings_by_status'][$meetingStatus])) {
                $metrics['meetings_by_status'][$meetingStatus] = 0;
            }
            $metrics['meetings_by_status'][$meetingStatus]++;
            
            // Cluster distribution
            $cluster = $meeting->cluster_name ?? 'Unknown';
            if (!isset($metrics['meetings_by_cluster'][$cluster])) {
                $metrics['meetings_by_cluster'][$cluster] = 0;
            }
            $metrics['meetings_by_cluster'][$cluster]++;
            
            // Partner distribution
            $partner = $meeting->partner_name ?? 'Unknown';
            if (!isset($metrics['meetings_by_partner'][$partner])) {
                $metrics['meetings_by_partner'][$partner] = 0;
            }
            $metrics['meetings_by_partner'][$partner]++;
        }
        
        // Calculate percentages
        $metrics['completion_rate'] = $metrics['total_meetings'] > 0 ? 
            round(($metrics['completed_meetings'] / $metrics['total_meetings']) * 100, 2) : 0;
        
        $metrics['rp_conversion_rate'] = $metrics['total_meetings'] > 0 ? 
            round(($metrics['rp_meetings'] / $metrics['total_meetings']) * 100, 2) : 0;
        
        $metrics['purpose_achieved_rate'] = $metrics['total_meetings'] > 0 ? 
            round(($metrics['purpose_achieved'] / $metrics['total_meetings']) * 100, 2) : 0;
        
        $metrics['action_taken_rate'] = $metrics['total_meetings'] > 0 ? 
            round(($metrics['action_taken'] / $metrics['total_meetings']) * 100, 2) : 0;
        
        $metrics['positive_sentiment_rate'] = $metrics['total_meetings'] > 0 ? 
            round(($metrics['positive_status_count'] / $metrics['total_meetings']) * 100, 2) : 0;
        
        $metrics['negative_sentiment_rate'] = $metrics['total_meetings'] > 0 ? 
            round(($metrics['negative_status_count'] / $metrics['total_meetings']) * 100, 2) : 0;
        
        return $metrics;
    }

    /**
     * Analyze sentiment
     */
    private function analyze_sentiment($meetingData)
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
        
        foreach ($meetingData as $meeting) {
            $currentStatus = strtolower($meeting->current_status ?? '');
            $status = $meeting->current_status ?? 'Unknown';
            
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
            elseif (in_array($currentStatus, ['neutral'])) $score = 1;
            elseif (in_array($currentStatus, ['negative'])) $score = -2;
            elseif (in_array($currentStatus, ['not interested'])) $score = -3;
            elseif (in_array($currentStatus, ['rejected'])) $score = -5;
            
            $sentimentScores[] = $score;
            
            // Collect MOM remarks
            $momRemarks = trim($meeting->mom_remarks ?? '');
            if (!empty($momRemarks)) {
                $allMomRemarks[] = [
                    'company' => $meeting->compname ?? '',
                    'remarks' => $momRemarks,
                    'status' => $status,
                    'score' => $score
                ];
            }
            
            // Collect remarks
            $remarks = trim($meeting->remarks ?? '');
            if (!empty($remarks)) {
                $allRemarks[] = [
                    'company' => $meeting->compname ?? '',
                    'remarks' => $remarks,
                    'status' => $status,
                    'mtype' => $meeting->mtype ?? ''
                ];
            }
            
            // Identify positive factors
            if ($score >= 3) {
                $positiveFactors = [];
                if ($meeting->potential === 'yes') $positiveFactors[] = 'High Potential';
                if ($meeting->topspender === 'yes') $positiveFactors[] = 'Top Spender';
                if ($meeting->keycompany === 'yes') $positiveFactors[] = 'Key Company';
                if ($meeting->priorityc === 'yes') $positiveFactors[] = 'Priority Client';
                if ($meeting->anchor_clients === 'yes') $positiveFactors[] = 'Anchor Client';
                if ($meeting->actontaken === 'yes') $positiveFactors[] = 'Action Taken';
                if ($meeting->purpose_achieved === 'yes') $positiveFactors[] = 'Purpose Achieved';
                
                $sentiment['positive_factors'] = array_merge($sentiment['positive_factors'], $positiveFactors);
            }
            
            // Identify negative factors
            if ($score <= 0) {
                $negativeFactors = [];
                if (strtolower($meeting->actontaken ?? '') === 'no') $negativeFactors[] = 'No Action Taken';
                if (strtolower($meeting->purpose_achieved ?? '') === 'no') $negativeFactors[] = 'Purpose Not Achieved';
                if (strtolower($meeting->mtype ?? '') === 'no rp') $negativeFactors[] = 'No RP Meeting';
                
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
        $sentiment['remarks_analysis'] = $this->analyze_meeting_remarks($allRemarks);
        
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
     * Analyze meeting remarks
     */
    private function analyze_meeting_remarks($remarks)
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
            'Meeting Close' => 0,
            'No RP' => 0,
            'Follow Up' => 0,
            'Reschedule' => 0,
            'Technical Issue' => 0,
            'Decision Pending' => 0
        ];
        
        foreach ($remarks as $remark) {
            $text = strtolower($remark['remarks']);
            $mtype = strtolower($remark['mtype'] ?? '');
            
            // Categorize by type
            if (!isset($analysis['remarks_by_type'][$mtype])) {
                $analysis['remarks_by_type'][$mtype] = 0;
            }
            $analysis['remarks_by_type'][$mtype]++;
            
            // Identify patterns
            if (strpos($text, 'meeting close') !== false) $patterns['Meeting Close']++;
            if (strpos($text, 'no rp') !== false) $patterns['No RP']++;
            if (strpos($text, 'follow up') !== false) $patterns['Follow Up']++;
            if (strpos($text, 'reschedule') !== false) $patterns['Reschedule']++;
            if (strpos($text, 'technical') !== false) $patterns['Technical Issue']++;
            if (strpos($text, 'decision pending') !== false) $patterns['Decision Pending']++;
            
            // Identify actionable remarks
            if (strpos($text, 'next meeting') !== false || 
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
        
        if (strpos($remark, 'next meeting') !== false) return 'Schedule Next Meeting';
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
    private function identify_priority_flags($meetingData)
    {
        $flags = [
            'high_potential_clients' => [],
            'top_spenders' => [],
            'key_companies' => [],
            'priority_clients' => [],
            'anchor_clients' => [],
            'no_rp_meetings' => [],
            'no_action_taken' => [],
            'purpose_not_achieved' => [],
            'pending_meetings' => [],
            'negative_sentiment' => []
        ];
        
        foreach ($meetingData as $meeting) {
            $companyName = $meeting->compname ?? 'Unknown Company';
            $companyId = $meeting->cid ?? 0;
            $userId = $meeting->task_user_id ?? 0;
            $userName = $meeting->task_username ?? '';
            $meetingId = $meeting->task_id ?? 0;
            
            $companyInfo = [
                'company_id' => $companyId,
                'company_name' => $companyName,
                'user_id' => $userId,
                'username' => $userName,
                'meeting_id' => $meetingId,
                'meeting_date' => $meeting->appointmentdatetime ?? '',
                'current_status' => $meeting->current_status ?? ''
            ];
            
            // High potential clients
            if ($meeting->potential === 'yes') {
                $flags['high_potential_clients'][] = $companyInfo;
            }
            
            // Top spenders
            if ($meeting->topspender === 'yes') {
                $flags['top_spenders'][] = $companyInfo;
            }
            
            // Key companies
            if ($meeting->keycompany === 'yes') {
                $flags['key_companies'][] = $companyInfo;
            }
            
            // Priority clients
            if ($meeting->priorityc === 'yes') {
                $flags['priority_clients'][] = $companyInfo;
            }
            
            // Anchor clients
            if ($meeting->anchor_clients === 'yes') {
                $flags['anchor_clients'][] = $companyInfo;
            }
            
            // No RP meetings
            if (strtolower($meeting->mtype ?? '') === 'no rp') {
                $flags['no_rp_meetings'][] = $companyInfo;
            }
            
            // No action taken
            if (strtolower($meeting->actontaken ?? '') === 'no') {
                $flags['no_action_taken'][] = $companyInfo;
            }
            
            // Purpose not achieved
            if (strtolower($meeting->purpose_achieved ?? '') === 'no') {
                $flags['purpose_not_achieved'][] = $companyInfo;
            }
            
            // Pending meetings
            $status = strtolower($meeting->meetings_status ?? '');
            if (!in_array($status, ['close', 'rpclose'])) {
                $flags['pending_meetings'][] = $companyInfo;
            }
            
            // Negative sentiment
            $currentStatus = strtolower($meeting->current_status ?? '');
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
    private function generate_recommendations($meetingData, $usersData)
    {
        $recommendations = [
            'general' => [],
            'user_specific' => [],
            'company_specific' => [],
            'process_improvements' => []
        ];
        
        // Calculate metrics for recommendations
        $metrics = $this->calculate_performance_metrics($meetingData);
        
        // General recommendations
        if ($metrics['rp_conversion_rate'] < 30) {
            $recommendations['general'][] = [
                'priority' => 'High',
                'title' => 'Improve RP Conversion',
                'description' => "Current RP conversion rate is {$metrics['rp_conversion_rate']}%. Focus on qualifying leads better and ensuring meetings are with decision-makers.",
                'action' => 'Train team on lead qualification and meeting preparation.'
            ];
        }
        
        if ($metrics['completion_rate'] < 70) {
            $recommendations['general'][] = [
                'priority' => 'Medium',
                'title' => 'Increase Meeting Completion',
                'description' => "Only {$metrics['completion_rate']}% of meetings are completed. Improve follow-up and reduce cancellations.",
                'action' => 'Implement better scheduling practices and confirmation calls.'
            ];
        }
        
        if ($metrics['positive_sentiment_rate'] < 40) {
            $recommendations['general'][] = [
                'priority' => 'High',
                'title' => 'Improve Meeting Outcomes',
                'description' => "Only {$metrics['positive_sentiment_rate']}% of meetings have positive outcomes. Focus on better preparation and value delivery.",
                'action' => 'Review meeting scripts and value proposition.'
            ];
        }
        
        // User-specific recommendations
        foreach ($usersData as $userId => $userData) {
            $userRecs = [];
            
            $completionRate = $userData['total_meetings'] > 0 ? 
                round(($userData['completed_meetings'] / $userData['total_meetings']) * 100, 2) : 0;
            
            $rpRate = $userData['total_meetings'] > 0 ? 
                round(($userData['rp_meetings'] / $userData['total_meetings']) * 100, 2) : 0;
            
            if ($completionRate < 60) {
                $userRecs[] = "Improve meeting completion rate (currently {$completionRate}%)";
            }
            
            if ($rpRate < 25) {
                $userRecs[] = "Increase RP meetings (currently {$rpRate}%)";
            }
            
            if ($userData['negative_status'] > $userData['positive_status']) {
                $userRecs[] = "Focus on improving meeting outcomes - more negative than positive outcomes";
            }
            
            if (!empty($userRecs)) {
                $recommendations['user_specific'][$userId] = [
                    'username' => $userData['username'],
                    'recommendations' => $userRecs
                ];
            }
        }
        
        // Process improvements
        $momCoverage = count(array_filter($meetingData, function($meeting) {
            return !empty(trim($meeting->mom_remarks ?? ''));
        }));
        
        $momRate = count($meetingData) > 0 ? 
            round(($momCoverage / count($meetingData)) * 100, 2) : 0;
        
        if ($momRate < 50) {
            $recommendations['process_improvements'][] = [
                'area' => 'Documentation',
                'issue' => 'Low MOM completion rate',
                'rate' => $momRate . '%',
                'recommendation' => 'Make MOM completion mandatory for all meetings'
            ];
        }
        
        // Check for photo documentation
        $photoCoverage = count(array_filter($meetingData, function($meeting) {
            return !empty($meeting->initphoto ?? '');
        }));
        
        $photoRate = count($meetingData) > 0 ? 
            round(($photoCoverage / count($meetingData)) * 100, 2) : 0;
        
        if ($photoRate < 80) {
            $recommendations['process_improvements'][] = [
                'area' => 'Verification',
                'issue' => 'Low photo documentation rate',
                'rate' => $photoRate . '%',
                'recommendation' => 'Enforce photo documentation for all field meetings'
            ];
        }
        
        return $recommendations;
    }

    /**
     * Select top companies based on various criteria
     */
    private function select_top_companies($meetingData, $topCount)
    {
        if (empty($meetingData)) {
            return [];
        }
        
        // Score each company
        $companyScores = [];
        
        foreach ($meetingData as $meeting) {
            $companyId = $meeting->cid ?? 0;
            $companyName = $meeting->compname ?? 'Unknown Company';
            
            if (!isset($companyScores[$companyId])) {
                $companyScores[$companyId] = [
                    'company_id' => $companyId,
                    'company_name' => $companyName,
                    'score' => 0,
                    'meetings' => [],
                    'reasons' => []
                ];
            }
            
            // Add meeting to company
            $companyScores[$companyId]['meetings'][] = [
                'meeting_id' => $meeting->task_id ?? 0,
                'date' => $meeting->appointmentdatetime ?? '',
                'status' => $meeting->current_status ?? '',
                'mtype' => $meeting->mtype ?? '',
                'potential' => $meeting->potential ?? '',
                'topspender' => $meeting->topspender ?? ''
            ];
            
            // Calculate score
            $score = 0;
            $reasons = [];
            
            // Status scoring
            $status = strtolower($meeting->current_status ?? '');
            if ($status === 'very positive') {
                $score += 10;
                $reasons[] = 'Very Positive Status';
            } elseif ($status === 'positive') {
                $score += 8;
                $reasons[] = 'Positive Status';
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
            
            // RP meeting
            if (strtolower($meeting->mtype ?? '') === 'rp') {
                $score += 15;
                $reasons[] = 'RP Meeting';
            }
            
            // Potential client
            if ($meeting->potential === 'yes') {
                $score += 12;
                $reasons[] = 'High Potential Client';
            }
            
            // Top spender
            if ($meeting->topspender === 'yes') {
                $score += 15;
                $reasons[] = 'Top Spender';
            }
            
            // Key company
            if ($meeting->keycompany === 'yes') {
                $score += 12;
                $reasons[] = 'Key Company';
            }
            
            // Priority client
            if ($meeting->priorityc === 'yes') {
                $score += 10;
                $reasons[] = 'Priority Client';
            }
            
            // Anchor client
            if ($meeting->anchor_clients === 'yes') {
                $score += 12;
                $reasons[] = 'Anchor Client';
            }
            
            // Purpose achieved
            if (strtolower($meeting->purpose_achieved ?? '') === 'yes') {
                $score += 8;
                $reasons[] = 'Purpose Achieved';
            }
            
            // Action taken
            if (strtolower($meeting->actontaken ?? '') === 'yes') {
                $score += 5;
                $reasons[] = 'Action Taken';
            }
            
            // Recent meeting bonus (within last 7 days)
            $meetingDate = $meeting->appointmentdatetime ?? '';
            if (!empty($meetingDate) && strtotime($meetingDate) > strtotime('-7 days')) {
                $score += 5;
                $reasons[] = 'Recent Meeting';
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
        
        // Add meeting count
        $meetingCount = count($company['meetings'] ?? []);
        $justification .= ". Has {$meetingCount} meeting(s) in the period.";
        
        return $justification;
    }

    /**
     * Generate response based on message
     */
    private function generate_response_based_on_message($message, $analysisResults, $topCount, $startDate, $endDate)
    {
        $lowerMessage = strtolower(trim($message));
        
        $response = "🔍 **SALES MEETING ANALYSIS REPORT**\n\n";
        $response .= "**Period:** {$startDate} to {$endDate}\n";
        $response .= "**Total Meetings Analyzed:** {$analysisResults['total_meetings']}\n\n";
        
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
            $response .= "**#{$company['rank']}. {$company['company_name']} - CID : {$company['company_id']}**\n";
            $response .= "   📊 Score: {$company['score']}\n";
            $response .= "   📅 Meetings: " . count($company['meetings']) . "\n";
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
        $response .= "**Meeting Completion:** {$metrics['completion_rate']}%\n";
        $response .= "   - Completed: {$metrics['completed_meetings']} meetings\n";
        $response .= "   - Pending: {$metrics['pending_meetings']} meetings\n\n";
        
        $response .= "**RP Conversion:** {$metrics['rp_conversion_rate']}%\n";
        $response .= "   - RP Meetings: {$metrics['rp_meetings']}\n";
        $response .= "   - No RP Meetings: {$metrics['no_rp_meetings']}\n\n";
        
        $response .= "**Purpose Achievement:** {$metrics['purpose_achieved_rate']}%\n";
        $response .= "**Action Taken:** {$metrics['action_taken_rate']}%\n\n";
        
        $response .= "**Meeting Type Distribution:**\n";
        foreach ($metrics['meetings_by_type'] as $type => $count) {
            $percentage = round(($count / $metrics['total_meetings']) * 100, 2);
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
        $response .= "   - Negative: {$metrics['negative_sentiment_rate']}%\n\n";
        
        $response .= "**Sentiment Distribution:**\n";
        foreach ($sentiment['sentiment_distribution'] as $status => $count) {
            $percentage = round(($count / $metrics['total_meetings']) * 100, 2);
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
                'no_rp_meetings_count' => 'No RP Meetings',
                'negative_sentiment_count' => 'Negative Sentiment Meetings'
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

    /**
     * Prepare detailed frontend data
     */
    private function prepare_detailed_frontend_data($meetingData, $topCount, $startDate, $endDate)
    {
        $response = [
            'status' => 'success',
            'message' => 'Meeting analysis data prepared successfully',
            'period' => "{$startDate} to {$endDate}",
            'generated_at' => date('Y-m-d H:i:s'),
            'summary' => [],
            'detailed_analysis' => [],
            'top_companies' => [],
            'charts' => [],
            'tables' => []
        ];
        
        if (empty($meetingData) || !is_array($meetingData)) {
            $response['status'] = 'empty';
            $response['message'] = 'No meeting data available for analysis';
            return $response;
        }
        
        // Analyze data
        $analysis = $this->analyze_meeting_data_deep($meetingData, $topCount);
        
        // Prepare summary
        $response['summary'] = [
            'total_meetings' => $analysis['total_meetings'],
            'total_users' => count($analysis['users_summary']),
            'total_companies' => count($analysis['companies_analysis']),
            'completion_rate' => $analysis['performance_metrics']['completion_rate'] ?? 0,
            'rp_conversion_rate' => $analysis['performance_metrics']['rp_conversion_rate'] ?? 0,
            'overall_sentiment' => $analysis['sentiment_analysis']['overall_sentiment'] ?? 'Neutral',
            'top_companies_count' => count($analysis['top_companies'])
        ];
        
        // Prepare detailed analysis
        $response['detailed_analysis'] = [
            'performance_metrics' => $analysis['performance_metrics'],
            'sentiment_analysis' => $analysis['sentiment_analysis'],
            'priority_flags' => $this->simplify_priority_flags($analysis['priority_flags']),
            'recommendations' => $analysis['recommendations']
        ];
        
        // Prepare top companies
        $response['top_companies'] = $analysis['top_companies'];
        
        // Prepare charts data
        $response['charts'] = $this->prepare_charts_data($analysis);
        
        // Prepare tables data
        $response['tables'] = $this->prepare_tables_data($analysis);
        
        return $response;
    }

    /**
     * Simplify priority flags for frontend
     */
    private function simplify_priority_flags($flags)
    {
        $simplified = [];
        
        $flagLabels = [
            'high_potential_clients_count' => 'High Potential Clients',
            'top_spenders_count' => 'Top Spenders',
            'key_companies_count' => 'Key Companies',
            'priority_clients_count' => 'Priority Clients',
            'anchor_clients_count' => 'Anchor Clients',
            'no_rp_meetings_count' => 'No RP Meetings',
            'no_action_taken_count' => 'No Action Taken',
            'purpose_not_achieved_count' => 'Purpose Not Achieved',
            'pending_meetings_count' => 'Pending Meetings',
            'negative_sentiment_count' => 'Negative Sentiment'
        ];
        
        foreach ($flagLabels as $key => $label) {
            if (isset($flags[$key]) && $flags[$key] > 0) {
                $simplified[] = [
                    'label' => $label,
                    'count' => $flags[$key],
                    'priority' => $this->determine_flag_priority($key, $flags[$key])
                ];
            }
        }
        
        return $simplified;
    }

    /**
     * Determine flag priority
     */
    private function determine_flag_priority($flagKey, $count)
    {
        $highPriorityFlags = [
            'negative_sentiment_count',
            'no_rp_meetings_count',
            'purpose_not_achieved_count'
        ];
        
        $mediumPriorityFlags = [
            'no_action_taken_count',
            'pending_meetings_count'
        ];
        
        if (in_array($flagKey, $highPriorityFlags)) {
            return $count > 5 ? 'High' : ($count > 2 ? 'Medium' : 'Low');
        } elseif (in_array($flagKey, $mediumPriorityFlags)) {
            return $count > 10 ? 'High' : ($count > 5 ? 'Medium' : 'Low');
        } else {
            return 'Info'; // Positive flags like high potential clients
        }
    }

    /**
     * Prepare charts data
     */
    private function prepare_charts_data($analysis)
    {
        $charts = [];
        
        // Performance metrics chart
        $metrics = $analysis['performance_metrics'];
        $charts['performance_metrics'] = [
            'type' => 'bar',
            'title' => 'Key Performance Metrics',
            'labels' => ['Completion Rate', 'RP Conversion', 'Purpose Achieved', 'Action Taken'],
            'datasets' => [[
                'label' => 'Percentage (%)',
                'data' => [
                    $metrics['completion_rate'] ?? 0,
                    $metrics['rp_conversion_rate'] ?? 0,
                    $metrics['purpose_achieved_rate'] ?? 0,
                    $metrics['action_taken_rate'] ?? 0
                ],
                'backgroundColor' => ['#4CAF50', '#2196F3', '#FF9800', '#9C27B0']
            ]]
        ];
        
        // Sentiment distribution chart
        $sentiment = $analysis['sentiment_analysis']['sentiment_distribution'] ?? [];
        if (!empty($sentiment)) {
            $charts['sentiment_distribution'] = [
                'type' => 'doughnut',
                'title' => 'Meeting Sentiment Distribution',
                'labels' => array_keys($sentiment),
                'datasets' => [[
                    'data' => array_values($sentiment),
                    'backgroundColor' => $this->generate_chart_colors(count($sentiment))
                ]]
            ];
        }
        
        // Meeting type distribution
        $meetingTypes = $analysis['performance_metrics']['meetings_by_type'] ?? [];
        if (!empty($meetingTypes)) {
            $charts['meeting_type_distribution'] = [
                'type' => 'pie',
                'title' => 'Meeting Type Distribution',
                'labels' => array_keys($meetingTypes),
                'datasets' => [[
                    'data' => array_values($meetingTypes),
                    'backgroundColor' => $this->generate_chart_colors(count($meetingTypes))
                ]]
            ];
        }
        
        // Top companies score chart
        $topCompanies = array_slice($analysis['top_companies'] ?? [], 0, 10);
        if (!empty($topCompanies)) {
            $companyNames = [];
            $companyScores = [];
            
            foreach ($topCompanies as $company) {
                $companyNames[] = substr($company['company_name'], 0, 20) . '...';
                $companyScores[] = $company['score'];
            }
            
            $charts['top_companies_score'] = [
                'type' => 'bar',
                'title' => 'Top Companies Engagement Score',
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
        
        return array_slice($colors, 0, $count);
    }

    /**
     * Prepare tables data
     */
    private function prepare_tables_data($analysis)
    {
        $tables = [];
        
        // Users summary table
        $users = $analysis['users_summary'] ?? [];
        if (!empty($users)) {
            $userRows = [];
            foreach ($users as $user) {
                $completionRate = $user['total_meetings'] > 0 ? 
                    round(($user['completed_meetings'] / $user['total_meetings']) * 100, 2) : 0;
                
                $rpRate = $user['total_meetings'] > 0 ? 
                    round(($user['rp_meetings'] / $user['total_meetings']) * 100, 2) : 0;
                
                $userRows[] = [
                    $user['username'],
                    $user['total_meetings'],
                    $user['completed_meetings'],
                    $user['pending_meetings'],
                    $user['rp_meetings'],
                    $user['no_rp_meetings'],
                    $completionRate . '%',
                    $rpRate . '%',
                    count($user['companies'] ?? [])
                ];
            }
            
            $tables['users_summary'] = [
                'title' => 'Users Performance Summary',
                'headers' => ['User', 'Total', 'Completed', 'Pending', 'RP', 'No RP', 'Completion %', 'RP %', 'Companies'],
                'rows' => $userRows
            ];
        }
        
        // Top companies table
        $topCompanies = $analysis['top_companies'] ?? [];
        if (!empty($topCompanies)) {
            $companyRows = [];
            foreach ($topCompanies as $company) {
                $meetingCount = count($company['meetings'] ?? []);
                $latestStatus = '';
                $latestDate = '';
                
                if ($meetingCount > 0) {
                    $latestMeeting = end($company['meetings']);
                    $latestStatus = $latestMeeting['status'] ?? '';
                    $latestDate = $latestMeeting['date'] ?? '';
                }
                
                $companyRows[] = [
                    $company['rank'],
                    $company['company_name'],
                    $company['score'],
                    $meetingCount,
                    $latestStatus,
                    $latestDate,
                    implode(', ', array_slice($company['reasons'] ?? [], 0, 3))
                ];
            }
            
            $tables['top_companies'] = [
                'title' => 'Top Companies Analysis',
                'headers' => ['Rank', 'Company', 'Score', 'Meetings', 'Latest Status', 'Latest Date', 'Key Reasons'],
                'rows' => $companyRows
            ];
        }
        
        // Priority alerts table
        $flags = $this->simplify_priority_flags($analysis['priority_flags'] ?? []);
        if (!empty($flags)) {
            $flagRows = [];
            foreach ($flags as $flag) {
                $flagRows[] = [
                    $flag['label'],
                    $flag['count'],
                    $flag['priority']
                ];
            }
            
            $tables['priority_alerts'] = [
                'title' => 'Priority Alerts',
                'headers' => ['Alert Type', 'Count', 'Priority'],
                'rows' => $flagRows
            ];
        }
        
        // Recommendations table
        $recommendations = $analysis['recommendations']['general'] ?? [];
        if (!empty($recommendations)) {
            $recRows = [];
            foreach ($recommendations as $rec) {
                $recRows[] = [
                    $rec['priority'],
                    $rec['title'],
                    $rec['description'],
                    $rec['action']
                ];
            }
            
            $tables['recommendations'] = [
                'title' => 'Key Recommendations',
                'headers' => ['Priority', 'Title', 'Description', 'Action'],
                'rows' => $recRows
            ];
        }
        
        return $tables;
    }
}