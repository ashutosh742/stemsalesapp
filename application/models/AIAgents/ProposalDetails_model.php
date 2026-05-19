<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ProposalDetails_model extends CI_Model {

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
     * Main method to process proposal analysis
     */
    public function process_ProposalDetails_execution($message, $analysisType, $sdate, $edate) 
    {
        // Get proposal data
        $getTotalTasks = $this->Report_model->GetProposalDetailbyDateByRoles($this->uid,$sdate,$edate,7,$this->uid,'all');
        $totalProposalCountByUser = $getTotalTasks['totalProposalTasksByUser'] ?? [];

        $totalProposalCompanyByUser = $this->Report_model->GetTeamProposalTaskListsDatas($this->uid,$sdate,$edate,'total_proposal_task',$this->uid,7,NULL);

        $proposalDetailsDatas = [
            'totalProposalCountByUser' => $totalProposalCountByUser,
            'totalProposalCompanyByUser' => $totalProposalCompanyByUser,
        ];



        // Check if data exists
        if (empty($totalProposalCountByUser) && empty($totalProposalCompanyByUser)) {
            return [
                'content' => "No proposal data found for the period {$sdate} to {$edate}.",
                'data' => [
                    'status' => 'empty',
                    'message' => 'No proposal data available for analysis',
                    'period' => "{$sdate} to {$edate}"
                ]
            ];
        }

        // Extract count from message (top 10, top 20, top 30, top 50)
        $topCount = $this->extract_top_count_from_message($message);
        
        // Generate comprehensive analysis
        $analysis = $this->generate_detailed_proposal_analysis(
            $proposalDetailsDatas, 
            $message, 
            $topCount,
            $sdate, 
            $edate
        );

        // Prepare frontend data
        $frontendData = $this->prepare_detailed_frontend_data(
            $proposalDetailsDatas, 
            $topCount,
            $sdate, 
            $edate
        );

        return [
            'content' => $analysis,
            'data' => $proposalDetailsDatas
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
     * Generate detailed proposal analysis
     */
    private function generate_detailed_proposal_analysis($proposalData, $message, $topCount, $startDate, $endDate)
    {
        // Process and analyze the data
        $analysisResults = $this->analyze_proposal_data_deep($proposalData, $topCount);
        
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
     * Deep analysis of proposal data
     */
    private function analyze_proposal_data_deep($proposalData, $topCount)
    {
        $userSummary = $proposalData['totalProposalCountByUser'] ?? [];
        $companyDetails = $proposalData['totalProposalCompanyByUser'] ?? [];

        if (empty($userSummary) && empty($companyDetails)) {
            return [
                'status' => 'empty',
                'message' => 'No proposal data to analyze'
            ];
        }

        $analysis = [
            'total_proposals' => 0,
            'total_users' => count($userSummary),
            'total_companies' => 0,
            'users_summary' => [],
            'companies_analysis' => [],
            'performance_metrics' => [],
            'financial_analysis' => [],
            'product_analysis' => [],
            'approval_analysis' => [],
            'time_analysis' => [],
            'priority_flags' => [],
            'recommendations' => [],
            'top_companies' => [],
            'top_users' => [],
            'table_data_analysis' => [] // NEW: Added table data analysis
        ];

        // Analyze user summary data
        $analysis['users_summary'] = $this->analyze_user_summary($userSummary);
        
        // Calculate total proposals
        foreach ($userSummary as $user) {
            $analysis['total_proposals'] += $user->total_proposal_task ?? 0;
        }

        // Analyze company details
        $companyAnalysis = $this->analyze_company_details($companyDetails);
        $analysis['companies_analysis'] = $companyAnalysis['companies'];
        $analysis['total_companies'] = count($companyAnalysis['companies']);
        
        // Calculate performance metrics
        $analysis['performance_metrics'] = $this->calculate_performance_metrics($userSummary, $companyAnalysis);
        
        // Financial analysis
        $analysis['financial_analysis'] = $this->analyze_financials($userSummary, $companyDetails);
        
        // Product analysis
        $analysis['product_analysis'] = $this->analyze_products($userSummary);
        
        // Approval analysis
        $analysis['approval_analysis'] = $this->analyze_approvals($userSummary, $companyDetails);
        
        // Time analysis
        $analysis['time_analysis'] = $this->analyze_timelines($companyDetails);
        
        // Priority flags
        $analysis['priority_flags'] = $this->identify_priority_flags($userSummary, $companyDetails);
        
        // Recommendations
        $analysis['recommendations'] = $this->generate_recommendations($userSummary, $companyDetails, $companyAnalysis);
        
        // Select top companies
        $analysis['top_companies'] = $this->select_top_companies($companyDetails, $topCount);
        
        // Select top users
        $analysis['top_users'] = $this->select_top_users($userSummary, $topCount);
        
        // NEW: Analyze table data
        $analysis['table_data_analysis'] = $this->analyze_table_data($userSummary, $companyDetails, $companyAnalysis);

        return $analysis;
    }

    /**
     * Analyze user summary data
     */
    private function analyze_user_summary($userSummary)
    {
        $analysis = [];
        
        foreach ($userSummary as $user) {
            $userData = [
                'user_id' => $user->user_id ?? 0,
                'username' => $user->username ?? 'Unknown User',
                'total_proposals' => $user->total_proposal_task ?? 0,
                'complete_proposals' => $user->complete_proposal_task ?? 0,
                'pending_proposals' => $user->pending_proposal_task ?? 0,
                'approval_metrics' => [
                    'approved' => $user->proposal_approved ?? 0,
                    'rejected' => $user->proposal_reject ?? 0,
                    'pending' => $user->pending_for_approved ?? 0
                ],
                'financial_metrics' => [
                    'total_budget' => $user->total_proposal_budget ?? 0,
                    'approved_budget' => $user->total_proposal_approved_budget ?? 0,
                    'pending_budget' => $user->total_proposal_pending_budget ?? 0,
                    'rejected_budget' => $user->total_proposal_reject_budget ?? 0
                ],
                'status_distribution' => [
                    'open' => $user->open ?? 0,
                    'reachout' => $user->reachout ?? 0,
                    'tentative' => $user->tentative ?? 0,
                    'will_do_later' => $user->will_do_later ?? 0,
                    'not_interested' => $user->not_interested ?? 0,
                    'positive' => $user->positive ?? 0,
                    'closure' => $user->closure ?? 0,
                    'very_positive' => $user->very_positive ?? 0,
                    'positive_nap' => $user->positive_nap ?? 0,
                    'very_positive_nap' => $user->very_positive_nap ?? 0,
                    'on_boarded' => $user->on_boarded ?? 0
                ],
                'client_segment' => [
                    'corporate' => $user->corporate ?? 0,
                    'psu' => $user->PSU ?? 0,
                    'ngo' => $user->NGO ?? 0,
                    'private_school' => $user->Private_School ?? 0,
                    'individual' => $user->Individual ?? 0,
                    'govt_off' => $user->Govt_Off ?? 0,
                    'online' => $user->Online ?? 0,
                    'stem_school' => $user->STEM_School ?? 0,
                    'foundation' => $user->Foundation ?? 0,
                    'mnc' => $user->MNC ?? 0
                ],
                'priority_indicators' => [
                    'potential' => $user->Potential ?? 0,
                    'top_spender' => $user->Top_Spender ?? 0,
                    'upsell_client' => $user->Upsell_Client ?? 0,
                    'focus_funnel' => $user->Focus_Funnel ?? 0,
                    'key_client' => $user->Key_Client ?? 0,
                    'positive_key_client' => $user->Positive_Key_Client ?? 0,
                    'priority_calling' => $user->Priority_Calling ?? 0
                ],
                'product_distribution' => [
                    'msc' => $user->total_in_MSC ?? 0,
                    'bala' => $user->total_in_BALA ?? 0,
                    'astronomy' => $user->total_in_Astronomy ?? 0,
                    'tinkering' => $user->total_in_Tinkering ?? 0,
                    'big_lab' => $user->total_in_Big_Laboratory ?? 0,
                    'small_lab' => $user->total_in_Small_Laboratory ?? 0,
                    'customize' => $user->total_in_Customize ?? 0,
                    'smart_class' => $user->total_in_Smart_Class ?? 0
                ],
                'approved_products' => [
                    'msc' => $user->total_Approved_in_MSC ?? 0,
                    'bala' => $user->total_Approved_in_BALA ?? 0,
                    'astronomy' => $user->total_Approved_in_Astronomy ?? 0,
                    'tinkering' => $user->total_Approved_in_Tinkering ?? 0,
                    'big_lab' => $user->total_Approved_in_Big_Laboratory ?? 0,
                    'small_lab' => $user->total_Approved_in_Small_Laboratory ?? 0,
                    'customize' => $user->total_Approved_in_Customize ?? 0,
                    'smart_class' => $user->total_Approved_in_Smart_Class ?? 0
                ],
                'rejected_products' => [
                    'msc' => $user->total_Reject_in_MSC ?? 0,
                    'bala' => $user->total_Reject_in_BALA ?? 0,
                    'astronomy' => $user->total_Reject_in_Astronomy ?? 0,
                    'tinkering' => $user->total_Reject_in_Tinkering ?? 0,
                    'big_lab' => $user->total_Reject_in_Big_Laboratory ?? 0,
                    'small_lab' => $user->total_Reject_in_Small_Laboratory ?? 0,
                    'customize' => $user->total_Reject_in_Customize ?? 0,
                    'smart_class' => $user->total_Reject_in_Smart_Class ?? 0
                ],
                'completion_rate' => $user->total_proposal_task > 0 ? 
                    round(($user->complete_proposal_task / $user->total_proposal_task) * 100, 2) : 0,
                'approval_rate' => $user->total_proposal_task > 0 ? 
                    round(($user->proposal_approved / $user->total_proposal_task) * 100, 2) : 0,
                'rejection_rate' => $user->total_proposal_task > 0 ? 
                    round(($user->proposal_reject / $user->total_proposal_task) * 100, 2) : 0
            ];
            
            // Calculate average budget per proposal
            if ($user->total_proposal_task > 0) {
                $userData['avg_budget_per_proposal'] = round($user->total_proposal_budget / $user->total_proposal_task, 2);
                $userData['avg_approved_budget'] = $user->proposal_approved > 0 ? 
                    round($user->total_proposal_approved_budget / $user->proposal_approved, 2) : 0;
            }
            
            $analysis[] = $userData;
        }
        
        return $analysis;
    }

    /**
     * Analyze company details
     */
    private function analyze_company_details($companyDetails)
    {
        $companies = [];
        $analysis = [
            'companies' => [],
            'total_proposals' => count($companyDetails),
            'unique_companies' => 0,
            'repeated_companies' => [],
            'proposal_timeline' => []
        ];
        
        // Group by company
        $companiesGroup = [];
        foreach ($companyDetails as $proposal) {
            $companyId = $proposal->cid ?? 0;
            $companyName = $proposal->compname ?? 'Unknown Company';
            
            if (!isset($companiesGroup[$companyId])) {
                $companiesGroup[$companyId] = [
                    'company_id' => $companyId,
                    'company_name' => $companyName,
                    'total_proposals' => 0,
                    'proposals' => [],
                    'users_involved' => [],
                    'first_proposal_date' => null,
                    'last_proposal_date' => null,
                    'approval_status' => [
                        'approved' => 0,
                        'rejected' => 0,
                        'pending' => 0
                    ],
                    'total_budget' => 0,
                    'approved_budget' => 0,
                    'product_types' => [],
                    'partner_types' => [],
                    'status_history' => [],
                    'proposal_quality' => []
                ];
            }
            
            $companiesGroup[$companyId]['total_proposals']++;
            
            // Add proposal details
            $proposalData = [
                'proposal_id' => $proposal->proposal_id ?? 0,
                'task_id' => $proposal->task_id ?? 0,
                'user_id' => $proposal->task_user_id ?? 0,
                'username' => $proposal->task_username ?? '',
                'proposal_date' => $proposal->appointmentdatetime ?? '',
                'initiated_date' => $proposal->initiateddt ?? '',
                'updated_date' => $proposal->updated_at ?? '',
                'total_time_taken' => $proposal->total_time_taken ?? '',
                'budget' => $proposal->propasal_budgetme ?? 0,
                'product_type' => $proposal->propasal_types ?? '',
                'partner_type' => $proposal->proposal_partner ?? '',
                'no_of_sc' => $proposal->propasal_noofsc ?? 1,
                'approval_status' => $proposal->apr_status ?? 0,
                'approval_date' => $proposal->propasal_apr_date ?? '',
                'checked_by' => $proposal->proposal_checked_by ?? '',
                'checked_date' => $proposal->proposal_checked_on_date ?? '',
                'remarks' => $proposal->propasal_apr_remarks ?? '',
                'current_status' => $proposal->current_status ?? '',
                'potential' => $proposal->potential ?? '',
                'priority_flags' => [],
                'attachment' => !empty($proposal->proattach) ? 'Yes' : 'No'
            ];
            
            // Add priority flags
            if ($proposal->potential === 'yes') $proposalData['priority_flags'][] = 'High Potential';
            if ($proposal->upsell_client === 'yes') $proposalData['priority_flags'][] = 'Upsell Client';
            if ($proposal->keycompany === 'yes') $proposalData['priority_flags'][] = 'Key Company';
            if ($proposal->priorityc === 'yes') $proposalData['priority_flags'][] = 'Priority Client';
            
            $companiesGroup[$companyId]['proposals'][] = $proposalData;
            
            // Track users
            $userId = $proposal->task_user_id ?? 0;
            if (!in_array($userId, $companiesGroup[$companyId]['users_involved'])) {
                $companiesGroup[$companyId]['users_involved'][] = $userId;
            }
            
            // Update dates
            $proposalDate = $proposal->appointmentdatetime ?? '';
            if (empty($companiesGroup[$companyId]['first_proposal_date']) || 
                strtotime($proposalDate) < strtotime($companiesGroup[$companyId]['first_proposal_date'])) {
                $companiesGroup[$companyId]['first_proposal_date'] = $proposalDate;
            }
            
            if (empty($companiesGroup[$companyId]['last_proposal_date']) || 
                strtotime($proposalDate) > strtotime($companiesGroup[$companyId]['last_proposal_date'])) {
                $companiesGroup[$companyId]['last_proposal_date'] = $proposalDate;
            }
            
            // Update approval status
            $aprStatus = intval($proposal->apr_status ?? 0);
            if ($aprStatus === 1) {
                $companiesGroup[$companyId]['approval_status']['approved']++;
                $companiesGroup[$companyId]['approved_budget'] += $proposal->propasal_budgetme ?? 0;
            } elseif ($aprStatus === 2) {
                $companiesGroup[$companyId]['approval_status']['rejected']++;
            } else {
                $companiesGroup[$companyId]['approval_status']['pending']++;
            }
            
            // Update total budget
            $companiesGroup[$companyId]['total_budget'] += $proposal->propasal_budgetme ?? 0;
            
            // Track product types
            $productType = $proposal->propasal_types ?? '';
            if (!isset($companiesGroup[$companyId]['product_types'][$productType])) {
                $companiesGroup[$companyId]['product_types'][$productType] = 0;
            }
            $companiesGroup[$companyId]['product_types'][$productType]++;
            
            // Track partner types
            $partnerType = $proposal->proposal_partner ?? '';
            if (!isset($companiesGroup[$companyId]['partner_types'][$partnerType])) {
                $companiesGroup[$companyId]['partner_types'][$partnerType] = 0;
            }
            $companiesGroup[$companyId]['partner_types'][$partnerType]++;
            
            // Track status history
            $status = $proposal->current_status ?? '';
            if (!empty($status)) {
                if (!isset($companiesGroup[$companyId]['status_history'][$status])) {
                    $companiesGroup[$companyId]['status_history'][$status] = 0;
                }
                $companiesGroup[$companyId]['status_history'][$status]++;
            }
            
            // Evaluate proposal quality
            $qualityScore = 0;
            $qualityFactors = [];
            
            if (!empty($proposal->proattach)) $qualityScore += 20;
            if ($proposal->actontaken === 'yes') $qualityScore += 15;
            if ($proposal->purpose_achieved === 'yes') $qualityScore += 15;
            if ($aprStatus === 1) $qualityScore += 30;
            if (!empty($proposal->remarks)) $qualityScore += 10;
            if (strtotime($proposal->propasal_apr_date ?? '') > strtotime($proposal->appointmentdatetime ?? '')) $qualityScore += 10;
            
            $companiesGroup[$companyId]['proposal_quality'][] = [
                'proposal_id' => $proposal->proposal_id ?? 0,
                'score' => $qualityScore,
                'factors' => $qualityFactors
            ];
            
            // Add to timeline
            if (!empty($proposalDate)) {
                $analysis['proposal_timeline'][] = [
                    'date' => date('Y-m-d', strtotime($proposalDate)),
                    'company' => $companyName,
                    'budget' => $proposal->propasal_budgetme ?? 0,
                    'status' => $aprStatus === 1 ? 'Approved' : ($aprStatus === 2 ? 'Rejected' : 'Pending')
                ];
            }
        }
        
        // Convert to indexed array and calculate additional metrics
        foreach ($companiesGroup as $companyId => $companyData) {
            // Calculate averages
            if ($companyData['total_proposals'] > 0) {
                $companyData['avg_budget_per_proposal'] = round($companyData['total_budget'] / $companyData['total_proposals'], 2);
                $companyData['avg_approval_time'] = $this->calculate_average_approval_time($companyData['proposals']);
            }
            
            // Calculate approval rate
            $totalDecisions = $companyData['approval_status']['approved'] + $companyData['approval_status']['rejected'];
            if ($totalDecisions > 0) {
                $companyData['approval_rate'] = round(($companyData['approval_status']['approved'] / $totalDecisions) * 100, 2);
                $companyData['rejection_rate'] = round(($companyData['approval_status']['rejected'] / $totalDecisions) * 100, 2);
            }
            
            // Calculate average proposal quality score
            $totalQualityScore = 0;
            foreach ($companyData['proposal_quality'] as $quality) {
                $totalQualityScore += $quality['score'];
            }
            $companyData['avg_proposal_quality'] = count($companyData['proposal_quality']) > 0 ? 
                round($totalQualityScore / count($companyData['proposal_quality']), 2) : 0;
            
            $companies[] = $companyData;
        }
        
        $analysis['companies'] = array_values($companies);
        $analysis['unique_companies'] = count($companies);
        
        // Find repeated companies
        foreach ($companies as $company) {
            if ($company['total_proposals'] > 1) {
                $analysis['repeated_companies'][] = [
                    'company_name' => $company['company_name'],
                    'proposal_count' => $company['total_proposals'],
                    'last_proposal_date' => $company['last_proposal_date']
                ];
            }
        }
        
        // Sort timeline by date
        usort($analysis['proposal_timeline'], function($a, $b) {
            return strtotime($a['date']) - strtotime($b['date']);
        });
        
        return $analysis;
    }

    /**
     * Calculate average approval time
     */
    private function calculate_average_approval_time($proposals)
    {
        $totalSeconds = 0;
        $count = 0;
        
        foreach ($proposals as $proposal) {
            $proposalDate = $proposal['proposal_date'] ?? '';
            $approvalDate = $proposal['approval_date'] ?? '';
            
            if (!empty($proposalDate) && !empty($approvalDate) && $proposal['approval_status'] == 1) {
                $proposalTime = strtotime($proposalDate);
                $approvalTime = strtotime($approvalDate);
                
                if ($proposalTime > 0 && $approvalTime > 0 && $approvalTime > $proposalTime) {
                    $totalSeconds += ($approvalTime - $proposalTime);
                    $count++;
                }
            }
        }
        
        if ($count > 0) {
            $avgSeconds = $totalSeconds / $count;
            return $this->format_time_duration($avgSeconds);
        }
        
        return 'N/A';
    }

    /**
     * Format time duration
     */
    private function format_time_duration($seconds)
    {
        $days = floor($seconds / 86400);
        $hours = floor(($seconds % 86400) / 3600);
        
        if ($days > 0) {
            return $days . ' days ' . $hours . ' hours';
        }
        
        return $hours . ' hours';
    }

    /**
     * Calculate performance metrics
     */
    private function calculate_performance_metrics($userSummary, $companyAnalysis)
    {
        $metrics = [
            'total_proposals' => 0,
            'complete_proposals' => 0,
            'pending_proposals' => 0,
            'approved_proposals' => 0,
            'rejected_proposals' => 0,
            'pending_approval' => 0,
            'total_budget' => 0,
            'approved_budget' => 0,
            'rejected_budget' => 0,
            'pending_budget' => 0,
            'completion_rate' => 0,
            'approval_rate' => 0,
            'rejection_rate' => 0,
            'avg_budget_per_proposal' => 0,
            'avg_approval_time' => 'N/A',
            'proposals_per_user' => 0,
            'proposals_per_company' => 0,
            'top_performing_product' => '',
            'most_common_partner' => '',
            'status_distribution' => [],
            'client_segment_distribution' => []
        ];
        
        // Calculate from user summary
        foreach ($userSummary as $user) {
            $metrics['total_proposals'] += $user->total_proposal_task ?? 0;
            $metrics['complete_proposals'] += $user->complete_proposal_task ?? 0;
            $metrics['pending_proposals'] += $user->pending_proposal_task ?? 0;
            $metrics['approved_proposals'] += $user->proposal_approved ?? 0;
            $metrics['rejected_proposals'] += $user->proposal_reject ?? 0;
            $metrics['pending_approval'] += $user->pending_for_approved ?? 0;
            $metrics['total_budget'] += $user->total_proposal_budget ?? 0;
            $metrics['approved_budget'] += $user->total_proposal_approved_budget ?? 0;
            $metrics['rejected_budget'] += $user->total_proposal_reject_budget ?? 0;
            $metrics['pending_budget'] += $user->total_proposal_pending_budget ?? 0;
            
            // Status distribution
            $metrics['status_distribution']['positive_nap'] = ($metrics['status_distribution']['positive_nap'] ?? 0) + ($user->positive_nap ?? 0);
            $metrics['status_distribution']['closure'] = ($metrics['status_distribution']['closure'] ?? 0) + ($user->closure ?? 0);
            $metrics['status_distribution']['will_do_later'] = ($metrics['status_distribution']['will_do_later'] ?? 0) + ($user->will_do_later ?? 0);
            $metrics['status_distribution']['very_positive_nap'] = ($metrics['status_distribution']['very_positive_nap'] ?? 0) + ($user->very_positive_nap ?? 0);
            
            // Client segment distribution
            $metrics['client_segment_distribution']['corporate'] = ($metrics['client_segment_distribution']['corporate'] ?? 0) + ($user->corporate ?? 0);
            $metrics['client_segment_distribution']['psu'] = ($metrics['client_segment_distribution']['psu'] ?? 0) + ($user->PSU ?? 0);
            $metrics['client_segment_distribution']['ngo'] = ($metrics['client_segment_distribution']['ngo'] ?? 0) + ($user->NGO ?? 0);
        }
        
        // Calculate rates
        if ($metrics['total_proposals'] > 0) {
            $metrics['completion_rate'] = round(($metrics['complete_proposals'] / $metrics['total_proposals']) * 100, 2);
            $metrics['avg_budget_per_proposal'] = round($metrics['total_budget'] / $metrics['total_proposals'], 2);
        }
        
        $totalDecisions = $metrics['approved_proposals'] + $metrics['rejected_proposals'];
        if ($totalDecisions > 0) {
            $metrics['approval_rate'] = round(($metrics['approved_proposals'] / $totalDecisions) * 100, 2);
            $metrics['rejection_rate'] = round(($metrics['rejected_proposals'] / $totalDecisions) * 100, 2);
        }
        
        // Calculate per user and per company
        if (count($userSummary) > 0) {
            $metrics['proposals_per_user'] = round($metrics['total_proposals'] / count($userSummary), 2);
        }
        
        if ($companyAnalysis['unique_companies'] > 0) {
            $metrics['proposals_per_company'] = round($metrics['total_proposals'] / $companyAnalysis['unique_companies'], 2);
        }
        
        // Find top performing product from user summary
        $productTotals = [
            'MSC' => 0,
            'BALA' => 0,
            'Astronomy' => 0,
            'Tinkering' => 0,
            'Big_Laboratory' => 0,
            'Small_Laboratory' => 0,
            'Customize' => 0,
            'Smart_Class' => 0
        ];
        
        foreach ($userSummary as $user) {
            $productTotals['MSC'] += $user->total_in_MSC ?? 0;
            $productTotals['BALA'] += $user->total_in_BALA ?? 0;
            $productTotals['Astronomy'] += $user->total_in_Astronomy ?? 0;
            $productTotals['Tinkering'] += $user->total_in_Tinkering ?? 0;
            $productTotals['Big_Laboratory'] += $user->total_in_Big_Laboratory ?? 0;
            $productTotals['Small_Laboratory'] += $user->total_in_Small_Laboratory ?? 0;
            $productTotals['Customize'] += $user->total_in_Customize ?? 0;
            $productTotals['Smart_Class'] += $user->total_in_Smart_Class ?? 0;
        }
        
        arsort($productTotals);
        $metrics['top_performing_product'] = key($productTotals);
        
        return $metrics;
    }

    /**
     * Analyze financials
     */
    private function analyze_financials($userSummary, $companyDetails)
    {
        $financials = [
            'total_budget' => 0,
            'approved_budget' => 0,
            'pending_budget' => 0,
            'rejected_budget' => 0,
            'avg_budget_per_proposal' => 0,
            'avg_budget_per_user' => [],
            'budget_distribution' => [
                'under_10k' => 0,
                '10k_50k' => 0,
                '50k_100k' => 0,
                '100k_500k' => 0,
                '500k_1m' => 0,
                'over_1m' => 0
            ],
            'highest_budget_proposal' => null,
            'lowest_budget_proposal' => null,
            'budget_trend' => []
        ];
        
        // Calculate totals from user summary
        foreach ($userSummary as $user) {
            $financials['total_budget'] += $user->total_proposal_budget ?? 0;
            $financials['approved_budget'] += $user->total_proposal_approved_budget ?? 0;
            $financials['pending_budget'] += $user->total_proposal_pending_budget ?? 0;
            $financials['rejected_budget'] += $user->total_proposal_reject_budget ?? 0;
            
            // Per user average
            if ($user->total_proposal_task > 0) {
                $financials['avg_budget_per_user'][] = [
                    'username' => $user->username ?? '',
                    'avg_budget' => round($user->total_proposal_budget / $user->total_proposal_task, 2)
                ];
            }
        }
        
        // Analyze company details for budget distribution
        $highestBudget = 0;
        $lowestBudget = PHP_INT_MAX;
        
        foreach ($companyDetails as $proposal) {
            $budget = $proposal->propasal_budgetme ?? 0;
            
            // Track highest and lowest
            if ($budget > $highestBudget) {
                $highestBudget = $budget;
                $financials['highest_budget_proposal'] = [
                    'company' => $proposal->compname ?? '',
                    'budget' => $budget,
                    'user' => $proposal->task_username ?? '',
                    'product' => $proposal->propasal_types ?? ''
                ];
            }
            
            if ($budget > 0 && $budget < $lowestBudget) {
                $lowestBudget = $budget;
                $financials['lowest_budget_proposal'] = [
                    'company' => $proposal->compname ?? '',
                    'budget' => $budget,
                    'user' => $proposal->task_username ?? '',
                    'product' => $proposal->propasal_types ?? ''
                ];
            }
            
            // Categorize budget
            if ($budget < 10000) $financials['budget_distribution']['under_10k']++;
            elseif ($budget < 50000) $financials['budget_distribution']['10k_50k']++;
            elseif ($budget < 100000) $financials['budget_distribution']['50k_100k']++;
            elseif ($budget < 500000) $financials['budget_distribution']['100k_500k']++;
            elseif ($budget < 1000000) $financials['budget_distribution']['500k_1m']++;
            else $financials['budget_distribution']['over_1m']++;
            
            // Track budget trend by date
            $proposalDate = $proposal->appointmentdatetime ?? '';
            if (!empty($proposalDate)) {
                $dateKey = date('Y-m-d', strtotime($proposalDate));
                if (!isset($financials['budget_trend'][$dateKey])) {
                    $financials['budget_trend'][$dateKey] = 0;
                }
                $financials['budget_trend'][$dateKey] += $budget;
            }
        }
        
        // Calculate average budget per proposal
        $totalProposals = 0;
        foreach ($userSummary as $user) {
            $totalProposals += $user->total_proposal_task ?? 0;
        }
        
        if ($totalProposals > 0) {
            $financials['avg_budget_per_proposal'] = round($financials['total_budget'] / $totalProposals, 2);
        }
        
        // Sort avg budget per user
        usort($financials['avg_budget_per_user'], function($a, $b) {
            return $b['avg_budget'] - $a['avg_budget'];
        });
        
        // Sort budget trend
        ksort($financials['budget_trend']);
        
        return $financials;
    }

    /**
     * Analyze products
     */
    private function analyze_products($userSummary)
    {
        $products = [
            'total_by_type' => [
                'MSC' => 0,
                'BALA' => 0,
                'Astronomy' => 0,
                'Tinkering' => 0,
                'Big_Laboratory' => 0,
                'Small_Laboratory' => 0,
                'Customize' => 0,
                'Smart_Class' => 0
            ],
            'approved_by_type' => [
                'MSC' => 0,
                'BALA' => 0,
                'Astronomy' => 0,
                'Tinkering' => 0,
                'Big_Laboratory' => 0,
                'Small_Laboratory' => 0,
                'Customize' => 0,
                'Smart_Class' => 0
            ],
            'rejected_by_type' => [
                'MSC' => 0,
                'BALA' => 0,
                'Astronomy' => 0,
                'Tinkering' => 0,
                'Big_Laboratory' => 0,
                'Small_Laboratory' => 0,
                'Customize' => 0,
                'Smart_Class' => 0
            ],
            'pending_by_type' => [
                'MSC' => 0,
                'BALA' => 0,
                'Astronomy' => 0,
                'Tinkering' => 0,
                'Big_Laboratory' => 0,
                'Small_Laboratory' => 0,
                'Customize' => 0,
                'Smart_Class' => 0
            ],
            'approval_rate_by_type' => [],
            'revenue_by_type' => [],
            'top_products' => [],
            'weak_products' => []
        ];
        
        foreach ($userSummary as $user) {
            // Total by type
            $products['total_by_type']['MSC'] += $user->total_in_MSC ?? 0;
            $products['total_by_type']['BALA'] += $user->total_in_BALA ?? 0;
            $products['total_by_type']['Astronomy'] += $user->total_in_Astronomy ?? 0;
            $products['total_by_type']['Tinkering'] += $user->total_in_Tinkering ?? 0;
            $products['total_by_type']['Big_Laboratory'] += $user->total_in_Big_Laboratory ?? 0;
            $products['total_by_type']['Small_Laboratory'] += $user->total_in_Small_Laboratory ?? 0;
            $products['total_by_type']['Customize'] += $user->total_in_Customize ?? 0;
            $products['total_by_type']['Smart_Class'] += $user->total_in_Smart_Class ?? 0;
            
            // Approved by type
            $products['approved_by_type']['MSC'] += $user->total_Approved_in_MSC ?? 0;
            $products['approved_by_type']['BALA'] += $user->total_Approved_in_BALA ?? 0;
            $products['approved_by_type']['Astronomy'] += $user->total_Approved_in_Astronomy ?? 0;
            $products['approved_by_type']['Tinkering'] += $user->total_Approved_in_Tinkering ?? 0;
            $products['approved_by_type']['Big_Laboratory'] += $user->total_Approved_in_Big_Laboratory ?? 0;
            $products['approved_by_type']['Small_Laboratory'] += $user->total_Approved_in_Small_Laboratory ?? 0;
            $products['approved_by_type']['Customize'] += $user->total_Approved_in_Customize ?? 0;
            $products['approved_by_type']['Smart_Class'] += $user->total_Approved_in_Smart_Class ?? 0;
            
            // Rejected by type
            $products['rejected_by_type']['MSC'] += $user->total_Reject_in_MSC ?? 0;
            $products['rejected_by_type']['BALA'] += $user->total_Reject_in_BALA ?? 0;
            $products['rejected_by_type']['Astronomy'] += $user->total_Reject_in_Astronomy ?? 0;
            $products['rejected_by_type']['Tinkering'] += $user->total_Reject_in_Tinkering ?? 0;
            $products['rejected_by_type']['Big_Laboratory'] += $user->total_Reject_in_Big_Laboratory ?? 0;
            $products['rejected_by_type']['Small_Laboratory'] += $user->total_Reject_in_Small_Laboratory ?? 0;
            $products['rejected_by_type']['Customize'] += $user->total_Reject_in_Customize ?? 0;
            $products['rejected_by_type']['Smart_Class'] += $user->total_Reject_in_Smart_Class ?? 0;
            
            // Pending by type
            $products['pending_by_type']['MSC'] += $user->total_Pending_For_Approve_in_MSC ?? 0;
            $products['pending_by_type']['BALA'] += $user->total_Pending_For_Approve_in_BALA ?? 0;
            $products['pending_by_type']['Astronomy'] += $user->total_Pending_For_Approve_in_Astronomy ?? 0;
            $products['pending_by_type']['Tinkering'] += $user->total_Pending_For_Approve_in_Tinkering ?? 0;
            $products['pending_by_type']['Big_Laboratory'] += $user->total_Pending_For_Approve_in_Big_Laboratory ?? 0;
            $products['pending_by_type']['Small_Laboratory'] += $user->total_Pending_For_Approve_in_Small_Laboratory ?? 0;
            $products['pending_by_type']['Customize'] += $user->total_Pending_For_Approve_in_Customize ?? 0;
            $products['pending_by_type']['Smart_Class'] += $user->total_Pending_For_Approve_in_Smart_Class ?? 0;
        }
        
        // Calculate approval rates by type
        foreach ($products['total_by_type'] as $type => $total) {
            $approved = $products['approved_by_type'][$type] ?? 0;
            $rejected = $products['rejected_by_type'][$type] ?? 0;
            $totalDecisions = $approved + $rejected;
            
            if ($totalDecisions > 0) {
                $products['approval_rate_by_type'][$type] = round(($approved / $totalDecisions) * 100, 2);
            } else {
                $products['approval_rate_by_type'][$type] = 0;
            }
        }
        
        // Identify top and weak products based on approval rate
        $sortedProducts = $products['approval_rate_by_type'];
        arsort($sortedProducts);
        
        $topCount = min(3, count($sortedProducts));
        $products['top_products'] = array_slice($sortedProducts, 0, $topCount, true);
        
        asort($sortedProducts);
        $weakCount = min(3, count($sortedProducts));
        $products['weak_products'] = array_slice($sortedProducts, 0, $weakCount, true);
        
        return $products;
    }

    /**
     * Analyze approvals
     */
    private function analyze_approvals($userSummary, $companyDetails)
    {
        $approvals = [
            'total_approved' => 0,
            'total_rejected' => 0,
            'total_pending' => 0,
            'approval_rate' => 0,
            'rejection_rate' => 0,
            'avg_approval_time' => 'N/A',
            'checker_distribution' => [],
            'rejection_reasons' => [],
            'approval_timeline' => [],
            'fastest_approval' => null,
            'slowest_approval' => null
        ];
        
        // Calculate totals
        foreach ($userSummary as $user) {
            $approvals['total_approved'] += $user->proposal_approved ?? 0;
            $approvals['total_rejected'] += $user->proposal_reject ?? 0;
            $approvals['total_pending'] += $user->pending_for_approved ?? 0;
        }
        
        // Calculate rates
        $totalDecisions = $approvals['total_approved'] + $approvals['total_rejected'];
        if ($totalDecisions > 0) {
            $approvals['approval_rate'] = round(($approvals['total_approved'] / $totalDecisions) * 100, 2);
            $approvals['rejection_rate'] = round(($approvals['total_rejected'] / $totalDecisions) * 100, 2);
        }
        
        // Analyze company details for timing and checkers
        $fastestTime = PHP_INT_MAX;
        $slowestTime = 0;
        $totalApprovalSeconds = 0;
        $approvalCount = 0;
        
        foreach ($companyDetails as $proposal) {
            $checker = $proposal->proposal_checked_by ?? '';
            $rejectionReason = $proposal->propasal_apr_remarks ?? '';
            $approvalDate = $proposal->propasal_apr_date ?? '';
            $proposalDate = $proposal->appointmentdatetime ?? '';
            $aprStatus = intval($proposal->apr_status ?? 0);
            
            // Track checkers
            if (!empty($checker)) {
                if (!isset($approvals['checker_distribution'][$checker])) {
                    $approvals['checker_distribution'][$checker] = 0;
                }
                $approvals['checker_distribution'][$checker]++;
            }
            
            // Track rejection reasons
            if ($aprStatus === 2 && !empty($rejectionReason)) {
                $rejectionReason = trim($rejectionReason);
                if (!isset($approvals['rejection_reasons'][$rejectionReason])) {
                    $approvals['rejection_reasons'][$rejectionReason] = 0;
                }
                $approvals['rejection_reasons'][$rejectionReason]++;
            }
            
            // Calculate approval time for approved proposals
            if ($aprStatus === 1 && !empty($proposalDate) && !empty($approvalDate)) {
                $proposalTime = strtotime($proposalDate);
                $approvalTime = strtotime($approvalDate);
                
                if ($proposalTime > 0 && $approvalTime > 0 && $approvalTime > $proposalTime) {
                    $approvalSeconds = $approvalTime - $proposalTime;
                    $totalApprovalSeconds += $approvalSeconds;
                    $approvalCount++;
                    
                    // Track fastest approval
                    if ($approvalSeconds < $fastestTime) {
                        $fastestTime = $approvalSeconds;
                        $approvals['fastest_approval'] = [
                            'company' => $proposal->compname ?? '',
                            'proposal_date' => $proposalDate,
                            'approval_date' => $approvalDate,
                            'duration' => $this->format_time_duration($approvalSeconds),
                            'checker' => $checker
                        ];
                    }
                    
                    // Track slowest approval
                    if ($approvalSeconds > $slowestTime) {
                        $slowestTime = $approvalSeconds;
                        $approvals['slowest_approval'] = [
                            'company' => $proposal->compname ?? '',
                            'proposal_date' => $proposalDate,
                            'approval_date' => $approvalDate,
                            'duration' => $this->format_time_duration($approvalSeconds),
                            'checker' => $checker
                        ];
                    }
                    
                    // Add to timeline
                    $approvals['approval_timeline'][] = [
                        'date' => date('Y-m-d', strtotime($approvalDate)),
                        'company' => $proposal->compname ?? '',
                        'duration_days' => round($approvalSeconds / 86400, 1)
                    ];
                }
            }
        }
        
        // Calculate average approval time
        if ($approvalCount > 0) {
            $avgSeconds = $totalApprovalSeconds / $approvalCount;
            $approvals['avg_approval_time'] = $this->format_time_duration($avgSeconds);
        }
        
        // Sort approval timeline
        usort($approvals['approval_timeline'], function($a, $b) {
            return strtotime($a['date']) - strtotime($b['date']);
        });
        
        return $approvals;
    }

    /**
     * Analyze timelines
     */
    private function analyze_timelines($companyDetails)
    {
        $timelines = [
            'proposal_frequency' => [],
            'time_to_complete' => [],
            'avg_time_per_stage' => [],
            'peak_proposal_days' => [],
            'proposal_flow' => []
        ];
        
        $dailyProposals = [];
        $completionTimes = [];
        
        foreach ($companyDetails as $proposal) {
            $proposalDate = $proposal->appointmentdatetime ?? '';
            $initiatedDate = $proposal->initiateddt ?? '';
            $updatedDate = $proposal->updated_at ?? '';
            
            // Analyze proposal frequency
            if (!empty($proposalDate)) {
                $dateKey = date('Y-m-d', strtotime($proposalDate));
                if (!isset($dailyProposals[$dateKey])) {
                    $dailyProposals[$dateKey] = 0;
                }
                $dailyProposals[$dateKey]++;
            }
            
            // Analyze completion time
            if (!empty($initiatedDate) && !empty($updatedDate)) {
                $initiatedTime = strtotime($initiatedDate);
                $updatedTime = strtotime($updatedDate);
                
                if ($initiatedTime > 0 && $updatedTime > 0 && $updatedTime > $initiatedTime) {
                    $completionSeconds = $updatedTime - $initiatedTime;
                    $completionTimes[] = $completionSeconds;
                    
                    // Categorize completion time
                    $hours = round($completionSeconds / 3600, 1);
                    if ($hours < 1) {
                        $timelines['time_to_complete']['under_1_hour'] = ($timelines['time_to_complete']['under_1_hour'] ?? 0) + 1;
                    } elseif ($hours < 4) {
                        $timelines['time_to_complete']['1_4_hours'] = ($timelines['time_to_complete']['1_4_hours'] ?? 0) + 1;
                    } elseif ($hours < 8) {
                        $timelines['time_to_complete']['4_8_hours'] = ($timelines['time_to_complete']['4_8_hours'] ?? 0) + 1;
                    } elseif ($hours < 24) {
                        $timelines['time_to_complete']['8_24_hours'] = ($timelines['time_to_complete']['8_24_hours'] ?? 0) + 1;
                    } else {
                        $timelines['time_to_complete']['over_24_hours'] = ($timelines['time_to_complete']['over_24_hours'] ?? 0) + 1;
                    }
                }
            }
            
            // Track proposal flow
            $timelines['proposal_flow'][] = [
                'company' => $proposal->compname ?? '',
                'proposal_date' => $proposalDate,
                'initiated_date' => $initiatedDate,
                'updated_date' => $updatedDate,
                'time_taken' => $proposal->total_time_taken ?? ''
            ];
        }
        
        // Calculate average completion time
        if (!empty($completionTimes)) {
            $avgSeconds = array_sum($completionTimes) / count($completionTimes);
            $timelines['avg_time_per_stage']['proposal_completion'] = $this->format_time_duration($avgSeconds);
        }
        
        // Find peak proposal days
        arsort($dailyProposals);
        $timelines['peak_proposal_days'] = array_slice($dailyProposals, 0, 5, true);
        
        // Sort proposal flow by date
        usort($timelines['proposal_flow'], function($a, $b) {
            return strtotime($a['proposal_date'] ?? '') - strtotime($b['proposal_date'] ?? '');
        });
        
        return $timelines;
    }

    /**
     * Identify priority flags
     */
    private function identify_priority_flags($userSummary, $companyDetails)
    {
        $flags = [
            'high_potential_clients' => [],
            'top_spenders' => [],
            'upsell_clients' => [],
            'key_clients' => [],
            'priority_clients' => [],
            'repeat_clients' => [],
            'large_budget_proposals' => [],
            'fast_turnaround_proposals' => [],
            'delayed_approvals' => [],
            'rejected_proposals' => []
        ];
        
        // Track company proposals count for repeat clients
        $companyProposalCount = [];
        
        foreach ($companyDetails as $proposal) {
            $companyId = $proposal->cid ?? 0;
            $companyName = $proposal->compname ?? 'Unknown Company';
            $userId = $proposal->task_user_id ?? 0;
            $userName = $proposal->task_username ?? '';
            $budget = $proposal->propasal_budgetme ?? 0;
            $aprStatus = intval($proposal->apr_status ?? 0);
            
            $companyInfo = [
                'company_id' => $companyId,
                'company_name' => $companyName,
                'user_id' => $userId,
                'username' => $userName,
                'proposal_id' => $proposal->proposal_id ?? 0,
                'budget' => $budget,
                'proposal_date' => $proposal->appointmentdatetime ?? '',
                'product_type' => $proposal->propasal_types ?? ''
            ];
            
            // Track company proposal count
            if (!isset($companyProposalCount[$companyId])) {
                $companyProposalCount[$companyId] = 0;
            }
            $companyProposalCount[$companyId]++;
            
            // High potential clients
            if ($proposal->potential === 'yes') {
                $flags['high_potential_clients'][] = $companyInfo;
            }
            
            // Upsell clients
            if ($proposal->upsell_client === 'yes') {
                $flags['upsell_clients'][] = $companyInfo;
            }
            
            // Key clients
            if ($proposal->keycompany === 'yes') {
                $flags['key_clients'][] = $companyInfo;
            }
            
            // Priority clients
            if ($proposal->priorityc === 'yes') {
                $flags['priority_clients'][] = $companyInfo;
            }
            
            // Large budget proposals (over 500k)
            if ($budget > 500000) {
                $flags['large_budget_proposals'][] = $companyInfo;
            }
            
            // Fast turnaround proposals (completed in under 1 hour)
            $timeTaken = $proposal->total_time_taken ?? '';
            if (strpos($timeTaken, '0 hours') !== false && strpos($timeTaken, 'minutes') !== false) {
                if (preg_match('/(\d+)\s+minutes/', $timeTaken, $matches)) {
                    $minutes = intval($matches[1]);
                    if ($minutes < 60) {
                        $flags['fast_turnaround_proposals'][] = $companyInfo;
                    }
                }
            }
            
            // Delayed approvals
            $proposalDate = $proposal->appointmentdatetime ?? '';
            $approvalDate = $proposal->propasal_apr_date ?? '';
            
            if ($aprStatus === 1 && !empty($proposalDate) && !empty($approvalDate)) {
                $proposalTime = strtotime($proposalDate);
                $approvalTime = strtotime($approvalDate);
                
                if ($proposalTime > 0 && $approvalTime > 0) {
                    $daysDiff = round(($approvalTime - $proposalTime) / 86400, 1);
                    if ($daysDiff > 7) { // More than 7 days for approval
                        $companyInfo['approval_delay_days'] = $daysDiff;
                        $flags['delayed_approvals'][] = $companyInfo;
                    }
                }
            }
            
            // Rejected proposals
            if ($aprStatus === 2) {
                $flags['rejected_proposals'][] = array_merge($companyInfo, [
                    'rejection_reason' => $proposal->propasal_apr_remarks ?? ''
                ]);
            }
        }
        
        // Identify repeat clients (more than 1 proposal)
        foreach ($companyProposalCount as $companyId => $count) {
            if ($count > 1) {
                // Find company name
                $companyName = '';
                foreach ($companyDetails as $proposal) {
                    if ($proposal->cid == $companyId) {
                        $companyName = $proposal->compname ?? '';
                        break;
                    }
                }
                
                $flags['repeat_clients'][] = [
                    'company_id' => $companyId,
                    'company_name' => $companyName,
                    'proposal_count' => $count
                ];
            }
        }
        
        // From user summary - top spenders
        foreach ($userSummary as $user) {
            if ($user->Top_Spender > 0) {
                // Find companies for this user that are top spenders
                foreach ($companyDetails as $proposal) {
                    if ($proposal->task_user_id == $user->user_id && $proposal->topspender === 'yes') {
                        $flags['top_spenders'][] = [
                            'company_id' => $proposal->cid ?? 0,
                            'company_name' => $proposal->compname ?? '',
                            'user_id' => $user->user_id,
                            'username' => $user->username,
                            'budget' => $proposal->propasal_budgetme ?? 0
                        ];
                    }
                }
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
    private function generate_recommendations($userSummary, $companyDetails, $companyAnalysis)
    {
        $recommendations = [
            'general' => [],
            'user_specific' => [],
            'company_specific' => [],
            'process_improvements' => [],
            'sales_strategy' => []
        ];
        
        // Calculate metrics for recommendations
        $totalProposals = 0;
        $totalApproved = 0;
        $totalRejected = 0;
        
        foreach ($userSummary as $user) {
            $totalProposals += $user->total_proposal_task ?? 0;
            $totalApproved += $user->proposal_approved ?? 0;
            $totalRejected += $user->proposal_reject ?? 0;
        }
        
        $approvalRate = $totalProposals > 0 ? round(($totalApproved / $totalProposals) * 100, 2) : 0;
        $rejectionRate = $totalProposals > 0 ? round(($totalRejected / $totalProposals) * 100, 2) : 0;
        
        // General recommendations
        if ($approvalRate < 60) {
            $recommendations['general'][] = [
                'priority' => 'High',
                'title' => 'Improve Proposal Approval Rate',
                'description' => "Current approval rate is {$approvalRate}%. Focus on better proposal quality and client qualification.",
                'action' => 'Review proposal templates and client qualification criteria.'
            ];
        }
        
        if ($rejectionRate > 30) {
            $recommendations['general'][] = [
                'priority' => 'High',
                'title' => 'Reduce Proposal Rejections',
                'description' => "Rejection rate is {$rejectionRate}%. Analyze common rejection reasons and address them.",
                'action' => 'Compile rejection reasons and create mitigation strategies.'
            ];
        }
        
        // Check for attachment completion
        $attachmentsMissing = 0;
        foreach ($companyDetails as $proposal) {
            if (empty($proposal->proattach)) {
                $attachmentsMissing++;
            }
        }
        
        $attachmentRate = count($companyDetails) > 0 ? 
            round(((count($companyDetails) - $attachmentsMissing) / count($companyDetails)) * 100, 2) : 0;
        
        if ($attachmentRate < 90) {
            $recommendations['general'][] = [
                'priority' => 'Medium',
                'title' => 'Improve Proposal Documentation',
                'description' => "Only {$attachmentRate}% of proposals have attachments. Complete documentation improves approval chances.",
                'action' => 'Make attachment upload mandatory for all proposals.'
            ];
        }
        
        // User-specific recommendations
        foreach ($userSummary as $user) {
            $userRecs = [];
            
            $userApprovalRate = $user->total_proposal_task > 0 ? 
                round(($user->proposal_approved / $user->total_proposal_task) * 100, 2) : 0;
            
            $userRejectionRate = $user->total_proposal_task > 0 ? 
                round(($user->proposal_reject / $user->total_proposal_task) * 100, 2) : 0;
            
            if ($userApprovalRate < 50) {
                $userRecs[] = [
                    'priority' => 'High',
                    'message' => "Improve approval rate (currently {$userApprovalRate}%)"
                ];
            }
            
            if ($userRejectionRate > 25) {
                $userRecs[] = [
                    'priority' => 'High',
                    'message' => "Reduce rejection rate (currently {$userRejectionRate}%)"
                ];
            }
            
            if ($user->pending_proposal_task > 0) {
                $userRecs[] = [
                    'priority' => 'Medium',
                    'message' => "Complete {$user->pending_proposal_task} pending proposal(s)"
                ];
            }
            
            // Check for high potential clients
            if ($user->Potential == 0) {
                $userRecs[] = [
                    'priority' => 'Medium',
                    'message' => 'No high potential clients identified. Focus on qualifying better prospects.'
                ];
            }
            
            if (!empty($userRecs)) {
                $recommendations['user_specific'][$user->user_id] = [
                    'username' => $user->username,
                    'recommendations' => $userRecs,
                    'metrics' => [
                        'total_proposals' => $user->total_proposal_task,
                        'approval_rate' => $userApprovalRate,
                        'rejection_rate' => $userRejectionRate
                    ]
                ];
            }
        }
        
        // Company-specific recommendations
        $companies = $companyAnalysis['companies'] ?? [];
        foreach ($companies as $company) {
            $companyRecs = [];
            
            if ($company['total_proposals'] > 1) {
                $companyRecs[] = "Repeat client - consider upselling or cross-selling";
            }
            
            if ($company['approval_rate'] < 50 && $company['total_proposals'] > 0) {
                $companyRecs[] = "Low approval rate - review proposal approach for this client";
            }
            
            if (!empty($companyRecs)) {
                $recommendations['company_specific'][] = [
                    'company_name' => $company['company_name'],
                    'proposal_count' => $company['total_proposals'],
                    'approval_rate' => $company['approval_rate'] ?? 0,
                    'recommendations' => $companyRecs
                ];
            }
        }
        
        // Process improvements
        $avgApprovalTime = $this->calculate_average_approval_time_from_details($companyDetails);
        
        if (strpos($avgApprovalTime, 'days') !== false) {
            $days = intval(explode(' ', $avgApprovalTime)[0]);
            if ($days > 3) {
                $recommendations['process_improvements'][] = [
                    'area' => 'Approval Process',
                    'issue' => 'Slow approval turnaround',
                    'metric' => $avgApprovalTime . ' average approval time',
                    'recommendation' => 'Streamline approval workflow and set SLA for approvals'
                ];
            }
        }
        
        // Sales strategy recommendations
        $productAnalysis = $this->analyze_products($userSummary);
        
        // Recommend focusing on top performing products
        if (!empty($productAnalysis['top_products'])) {
            $topProduct = key($productAnalysis['top_products']);
            $topRate = current($productAnalysis['top_products']);
            
            $recommendations['sales_strategy'][] = [
                'strategy' => 'Focus on High-Performing Products',
                'details' => "{$topProduct} has the highest approval rate at {$topRate}%",
                'action' => 'Allocate more resources to promoting ' . $topProduct
            ];
        }
        
        // Recommend improving weak products
        if (!empty($productAnalysis['weak_products'])) {
            $weakProduct = key($productAnalysis['weak_products']);
            $weakRate = current($productAnalysis['weak_products']);
            
            if ($weakRate < 50) {
                $recommendations['sales_strategy'][] = [
                    'strategy' => 'Improve Underperforming Products',
                    'details' => "{$weakProduct} has low approval rate at {$weakRate}%",
                    'action' => 'Review pricing, features, or positioning of ' . $weakProduct
                ];
            }
        }
        
        return $recommendations;
    }

    /**
     * Calculate average approval time from details
     */
    private function calculate_average_approval_time_from_details($companyDetails)
    {
        $totalSeconds = 0;
        $count = 0;
        
        foreach ($companyDetails as $proposal) {
            $proposalDate = $proposal->appointmentdatetime ?? '';
            $approvalDate = $proposal->propasal_apr_date ?? '';
            $aprStatus = intval($proposal->apr_status ?? 0);
            
            if ($aprStatus === 1 && !empty($proposalDate) && !empty($approvalDate)) {
                $proposalTime = strtotime($proposalDate);
                $approvalTime = strtotime($approvalDate);
                
                if ($proposalTime > 0 && $approvalTime > 0 && $approvalTime > $proposalTime) {
                    $totalSeconds += ($approvalTime - $proposalTime);
                    $count++;
                }
            }
        }
        
        if ($count > 0) {
            $avgSeconds = $totalSeconds / $count;
            return $this->format_time_duration($avgSeconds);
        }
        
        return 'N/A';
    }

    /**
     * Select top companies
     */
    private function select_top_companies($companyDetails, $topCount)
    {
        if (empty($companyDetails)) {
            return [];
        }
        
        // Group and score companies
        $companyScores = [];
        
        foreach ($companyDetails as $proposal) {
            $companyId = $proposal->cid ?? 0;
            $companyName = $proposal->compname ?? 'Unknown Company';
            
            if (!isset($companyScores[$companyId])) {
                $companyScores[$companyId] = [
                    'company_id' => $companyId,
                    'company_name' => $companyName,
                    'score' => 0,
                    'proposals' => [],
                    'total_budget' => 0,
                    'approved_budget' => 0,
                    'reasons' => []
                ];
            }
            
            // Add proposal to company
            $companyScores[$companyId]['proposals'][] = [
                'proposal_id' => $proposal->proposal_id ?? 0,
                'date' => $proposal->appointmentdatetime ?? '',
                'budget' => $proposal->propasal_budgetme ?? 0,
                'status' => $proposal->apr_status ?? 0,
                'product_type' => $proposal->propasal_types ?? ''
            ];
            
            // Update budgets
            $budget = $proposal->propasal_budgetme ?? 0;
            $companyScores[$companyId]['total_budget'] += $budget;
            
            if ($proposal->apr_status == 1) {
                $companyScores[$companyId]['approved_budget'] += $budget;
            }
            
            // Calculate score
            $score = 0;
            $reasons = [];
            
            // Budget scoring
            if ($budget > 1000000) {
                $score += 20;
                $reasons[] = 'Large Budget (>1M)';
            } elseif ($budget > 500000) {
                $score += 15;
                $reasons[] = 'Medium Budget (500K-1M)';
            } elseif ($budget > 100000) {
                $score += 10;
                $reasons[] = 'Small Budget (100K-500K)';
            }
            
            // Approval status
            if ($proposal->apr_status == 1) {
                $score += 15;
                $reasons[] = 'Approved Proposal';
            }
            
            // Priority flags
            if ($proposal->potential === 'yes') {
                $score += 12;
                $reasons[] = 'High Potential Client';
            }
            
            if ($proposal->upsell_client === 'yes') {
                $score += 10;
                $reasons[] = 'Upsell Client';
            }
            
            if ($proposal->keycompany === 'yes') {
                $score += 15;
                $reasons[] = 'Key Client';
            }
            
            if ($proposal->priorityc === 'yes') {
                $score += 12;
                $reasons[] = 'Priority Client';
            }
            
            // Recent proposal bonus
            $proposalDate = $proposal->appointmentdatetime ?? '';
            if (!empty($proposalDate) && strtotime($proposalDate) > strtotime('-7 days')) {
                $score += 8;
                $reasons[] = 'Recent Proposal';
            }
            
            // Fast turnaround bonus
            $timeTaken = $proposal->total_time_taken ?? '';
            if (strpos($timeTaken, '0 hours') !== false && strpos($timeTaken, 'minutes') !== false) {
                if (preg_match('/(\d+)\s+minutes/', $timeTaken, $matches)) {
                    $minutes = intval($matches[1]);
                    if ($minutes < 30) {
                        $score += 5;
                        $reasons[] = 'Fast Turnaround (<30 min)';
                    }
                }
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
            $company['proposal_count'] = count($company['proposals']);
            $company['justification'] = $this->generate_company_justification($company);
            $rank++;
        }
        
        return $topCompanies;
    }

    /**
     * Select top users
     */
    private function select_top_users($userSummary, $topCount)
    {
        if (empty($userSummary)) {
            return [];
        }
        
        $userScores = [];
        
        foreach ($userSummary as $user) {
            $score = 0;
            $reasons = [];
            
            // Proposal volume scoring
            $totalProposals = $user->total_proposal_task ?? 0;
            if ($totalProposals > 10) {
                $score += 20;
                $reasons[] = 'High Volume (>10 proposals)';
            } elseif ($totalProposals > 5) {
                $score += 15;
                $reasons[] = 'Good Volume (5-10 proposals)';
            } elseif ($totalProposals > 0) {
                $score += 10;
                $reasons[] = 'Active Proposals';
            }
            
            // Approval rate scoring
            $approvalRate = $totalProposals > 0 ? 
                round(($user->proposal_approved / $totalProposals) * 100, 2) : 0;
            
            if ($approvalRate > 80) {
                $score += 20;
                $reasons[] = 'Excellent Approval Rate (>80%)';
            } elseif ($approvalRate > 60) {
                $score += 15;
                $reasons[] = 'Good Approval Rate (60-80%)';
            } elseif ($approvalRate > 40) {
                $score += 10;
                $reasons[] = 'Average Approval Rate (40-60%)';
            }
            
            // Budget scoring
            $totalBudget = $user->total_proposal_budget ?? 0;
            if ($totalBudget > 5000000) {
                $score += 25;
                $reasons[] = 'Large Pipeline (>5M)';
            } elseif ($totalBudget > 2000000) {
                $score += 20;
                $reasons[] = 'Good Pipeline (2M-5M)';
            } elseif ($totalBudget > 1000000) {
                $score += 15;
                $reasons[] = 'Moderate Pipeline (1M-2M)';
            }
            
            // Priority client scoring
            if ($user->Potential > 0) {
                $score += 10;
                $reasons[] = 'High Potential Clients';
            }
            
            if ($user->Key_Client > 0) {
                $score += 15;
                $reasons[] = 'Key Clients';
            }
            
            // Completion rate
            $completionRate = $totalProposals > 0 ? 
                round(($user->complete_proposal_task / $totalProposals) * 100, 2) : 0;
            
            if ($completionRate > 90) {
                $score += 10;
                $reasons[] = 'High Completion Rate (>90%)';
            }
            
            $userScores[] = [
                'user_id' => $user->user_id,
                'username' => $user->username,
                'score' => $score,
                'total_proposals' => $totalProposals,
                'approval_rate' => $approvalRate,
                'total_budget' => $totalBudget,
                'reasons' => $reasons,
                'completion_rate' => $completionRate
            ];
        }
        
        // Sort by score
        usort($userScores, function($a, $b) {
            return $b['score'] - $a['score'];
        });
        
        // Select top users
        $topUsers = array_slice($userScores, 0, $topCount);
        
        // Add ranking
        $rank = 1;
        foreach ($topUsers as &$user) {
            $user['rank'] = $rank;
            $rank++;
        }
        
        return $topUsers;
    }

    /**
     * NEW: Analyze table data for comprehensive insights
     */
    private function analyze_table_data($userSummary, $companyDetails, $companyAnalysis)
    {
        $tableData = [
            'proposal_trends' => [],
            'user_performance_matrix' => [],
            'company_engagement_matrix' => [],
            'product_performance_matrix' => [],
            'approval_process_matrix' => [],
            'financial_summary_table' => [],
            'status_distribution_table' => [],
            'client_segment_table' => [],
            'time_metrics_table' => [],
            'priority_clients_table' => []
        ];

        // 1. Proposal Trends Table
        $tableData['proposal_trends'] = $this->generate_proposal_trends_table($companyDetails);
        
        // 2. User Performance Matrix
        $tableData['user_performance_matrix'] = $this->generate_user_performance_matrix($userSummary);
        
        // 3. Company Engagement Matrix
        $tableData['company_engagement_matrix'] = $this->generate_company_engagement_matrix($companyAnalysis);
        
        // 4. Product Performance Matrix
        $tableData['product_performance_matrix'] = $this->generate_product_performance_matrix($userSummary);
        
        // 5. Approval Process Matrix
        $tableData['approval_process_matrix'] = $this->generate_approval_process_matrix($companyDetails);
        
        // 6. Financial Summary Table
        $tableData['financial_summary_table'] = $this->generate_financial_summary_table($userSummary, $companyDetails);
        
        // 7. Status Distribution Table
        $tableData['status_distribution_table'] = $this->generate_status_distribution_table($userSummary);
        
        // 8. Client Segment Table
        $tableData['client_segment_table'] = $this->generate_client_segment_table($userSummary);
        
        // 9. Time Metrics Table
        $tableData['time_metrics_table'] = $this->generate_time_metrics_table($companyDetails);
        
        // 10. Priority Clients Table
        $tableData['priority_clients_table'] = $this->generate_priority_clients_table($companyDetails);

        return $tableData;
    }

    /**
     * Generate Proposal Trends Table
     */
    private function generate_proposal_trends_table($companyDetails)
    {
        $trends = [
            'daily_trends' => [],
            'weekly_trends' => [],
            'monthly_trends' => [],
            'hourly_distribution' => [],
            'weekday_distribution' => []
        ];
        
        $dailyCounts = [];
        $hourlyCounts = array_fill(0, 24, 0);
        $weekdayCounts = array_fill(0, 7, 0);
        
        foreach ($companyDetails as $proposal) {
            $proposalDate = $proposal->appointmentdatetime ?? '';
            
            if (!empty($proposalDate)) {
                $date = date('Y-m-d', strtotime($proposalDate));
                $hour = date('H', strtotime($proposalDate));
                $weekday = date('w', strtotime($proposalDate));
                
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
            }
        }
        
        // Sort daily trends
        ksort($dailyCounts);
        $trends['daily_trends'] = $dailyCounts;
        
        // Weekly trends (group by week)
        $weeklyCounts = [];
        foreach ($dailyCounts as $date => $count) {
            $weekNumber = date('W', strtotime($date));
            $year = date('Y', strtotime($date));
            $weekKey = $year . '-W' . str_pad($weekNumber, 2, '0', STR_PAD_LEFT);
            
            if (!isset($weeklyCounts[$weekKey])) {
                $weeklyCounts[$weekKey] = 0;
            }
            $weeklyCounts[$weekKey] += $count;
        }
        $trends['weekly_trends'] = $weeklyCounts;
        
        // Monthly trends
        $monthlyCounts = [];
        foreach ($dailyCounts as $date => $count) {
            $monthKey = date('Y-m', strtotime($date));
            
            if (!isset($monthlyCounts[$monthKey])) {
                $monthlyCounts[$monthKey] = 0;
            }
            $monthlyCounts[$monthKey] += $count;
        }
        $trends['monthly_trends'] = $monthlyCounts;
        
        // Hourly distribution with labels
        $hourlyDistribution = [];
        foreach ($hourlyCounts as $hour => $count) {
            if ($count > 0) {
                $hourLabel = sprintf('%02d:00-%02d:59', $hour, $hour);
                $hourlyDistribution[$hourLabel] = $count;
            }
        }
        $trends['hourly_distribution'] = $hourlyDistribution;
        
        // Weekday distribution with labels
        $weekdayLabels = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        $weekdayDistribution = [];
        foreach ($weekdayCounts as $weekday => $count) {
            if ($count > 0) {
                $weekdayDistribution[$weekdayLabels[$weekday]] = $count;
            }
        }
        $trends['weekday_distribution'] = $weekdayDistribution;
        
        return $trends;
    }

    /**
     * Generate User Performance Matrix
     */
    private function generate_user_performance_matrix($userSummary)
    {
        $matrix = [];
        
        foreach ($userSummary as $user) {
            $totalProposals = $user->total_proposal_task ?? 0;
            $approvedProposals = $user->proposal_approved ?? 0;
            $rejectedProposals = $user->proposal_reject ?? 0;
            $pendingProposals = $user->pending_for_approved ?? 0;
            $totalBudget = $user->total_proposal_budget ?? 0;
            $approvedBudget = $user->total_proposal_approved_budget ?? 0;
            
            // Calculate metrics
            $completionRate = $totalProposals > 0 ? 
                round(($user->complete_proposal_task / $totalProposals) * 100, 2) : 0;
            
            $approvalRate = $totalProposals > 0 ? 
                round(($approvedProposals / $totalProposals) * 100, 2) : 0;
            
            $rejectionRate = $totalProposals > 0 ? 
                round(($rejectedProposals / $totalProposals) * 100, 2) : 0;
            
            $avgBudgetPerProposal = $totalProposals > 0 ? 
                round($totalBudget / $totalProposals, 2) : 0;
            
            $avgApprovedBudget = $approvedProposals > 0 ? 
                round($approvedBudget / $approvedProposals, 2) : 0;
            
            // Performance rating
            $performanceScore = $this->calculate_user_performance_score(
                $totalProposals,
                $approvalRate,
                $completionRate,
                $avgBudgetPerProposal
            );
            
            $performanceRating = $this->get_performance_rating($performanceScore);
            
            $matrix[] = [
                'user_id' => $user->user_id,
                'username' => $user->username ?? 'Unknown',
                'total_proposals' => $totalProposals,
                'approved_proposals' => $approvedProposals,
                'rejected_proposals' => $rejectedProposals,
                'pending_proposals' => $pendingProposals,
                'total_budget' => $totalBudget,
                'approved_budget' => $approvedBudget,
                'completion_rate' => $completionRate,
                'approval_rate' => $approvalRate,
                'rejection_rate' => $rejectionRate,
                'avg_budget_per_proposal' => $avgBudgetPerProposal,
                'avg_approved_budget' => $avgApprovedBudget,
                'performance_score' => $performanceScore,
                'performance_rating' => $performanceRating,
                'key_strengths' => $this->identify_user_strengths($user),
                'improvement_areas' => $this->identify_user_improvement_areas($user)
            ];
        }
        
        // Sort by performance score descending
        usort($matrix, function($a, $b) {
            return $b['performance_score'] - $a['performance_score'];
        });
        
        return $matrix;
    }

    /**
     * Calculate user performance score
     */
    private function calculate_user_performance_score($totalProposals, $approvalRate, $completionRate, $avgBudget)
    {
        $score = 0;
        
        // Proposal volume (max 30 points)
        if ($totalProposals >= 10) $score += 30;
        elseif ($totalProposals >= 5) $score += 20;
        elseif ($totalProposals >= 3) $score += 10;
        elseif ($totalProposals > 0) $score += 5;
        
        // Approval rate (max 30 points)
        if ($approvalRate >= 80) $score += 30;
        elseif ($approvalRate >= 60) $score += 20;
        elseif ($approvalRate >= 40) $score += 10;
        elseif ($approvalRate > 0) $score += 5;
        
        // Completion rate (max 20 points)
        if ($completionRate >= 90) $score += 20;
        elseif ($completionRate >= 70) $score += 15;
        elseif ($completionRate >= 50) $score += 10;
        elseif ($completionRate > 0) $score += 5;
        
        // Average budget (max 20 points)
        if ($avgBudget >= 1000000) $score += 20;
        elseif ($avgBudget >= 500000) $score += 15;
        elseif ($avgBudget >= 100000) $score += 10;
        elseif ($avgBudget > 0) $score += 5;
        
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
     * Identify user strengths
     */
    private function identify_user_strengths($user)
    {
        $strengths = [];
        
        if ($user->proposal_approved > 0) {
            $approvalRate = ($user->proposal_approved / $user->total_proposal_task) * 100;
            if ($approvalRate >= 70) {
                $strengths[] = 'High approval rate';
            }
        }
        
        if ($user->complete_proposal_task == $user->total_proposal_task) {
            $strengths[] = '100% completion rate';
        }
        
        if ($user->Potential > 0) {
            $strengths[] = 'Identifies high potential clients';
        }
        
        if ($user->Key_Client > 0) {
            $strengths[] = 'Works with key clients';
        }
        
        if ($user->total_proposal_budget > 1000000) {
            $strengths[] = 'High value pipeline';
        }
        
        return $strengths;
    }

    /**
     * Identify user improvement areas
     */
    private function identify_user_improvement_areas($user)
    {
        $improvements = [];
        
        if ($user->proposal_reject > 0) {
            $rejectionRate = ($user->proposal_reject / $user->total_proposal_task) * 100;
            if ($rejectionRate >= 30) {
                $improvements[] = 'High rejection rate';
            }
        }
        
        if ($user->pending_proposal_task > 0) {
            $improvements[] = 'Pending proposals need follow-up';
        }
        
        if ($user->Potential == 0 && $user->total_proposal_task > 0) {
            $improvements[] = 'No high potential clients identified';
        }
        
        if ($user->complete_proposal_task < $user->total_proposal_task) {
            $improvements[] = 'Incomplete proposals';
        }
        
        return $improvements;
    }

    /**
     * Generate Company Engagement Matrix
     */
    private function generate_company_engagement_matrix($companyAnalysis)
    {
        $matrix = [];
        $companies = $companyAnalysis['companies'] ?? [];
        
        foreach ($companies as $company) {
            $engagementScore = $this->calculate_company_engagement_score($company);
            $engagementLevel = $this->get_engagement_level($engagementScore);
            
            $matrix[] = [
                'company_id' => $company['company_id'],
                'company_name' => $company['company_name'],
                'total_proposals' => $company['total_proposals'],
                'total_budget' => $company['total_budget'],
                'approved_budget' => $company['approved_budget'],
                'approval_rate' => $company['approval_rate'] ?? 0,
                'rejection_rate' => $company['rejection_rate'] ?? 0,
                'first_proposal_date' => $company['first_proposal_date'],
                'last_proposal_date' => $company['last_proposal_date'],
                'days_since_last_proposal' => $this->calculate_days_since_last_proposal($company['last_proposal_date']),
                'users_involved_count' => count($company['users_involved'] ?? []),
                'product_types_count' => count($company['product_types'] ?? []),
                'partner_types_count' => count($company['partner_types'] ?? []),
                'engagement_score' => $engagementScore,
                'engagement_level' => $engagementLevel,
                'next_action_recommended' => $this->recommend_next_action($company, $engagementScore)
            ];
        }
        
        // Sort by engagement score descending
        usort($matrix, function($a, $b) {
            return $b['engagement_score'] - $a['engagement_score'];
        });
        
        return $matrix;
    }

    /**
     * Calculate company engagement score
     */
    private function calculate_company_engagement_score($company)
    {
        $score = 0;
        
        // Proposal frequency (max 30 points)
        $proposalCount = $company['total_proposals'] ?? 0;
        if ($proposalCount >= 5) $score += 30;
        elseif ($proposalCount >= 3) $score += 20;
        elseif ($proposalCount >= 2) $score += 10;
        elseif ($proposalCount > 0) $score += 5;
        
        // Budget size (max 25 points)
        $totalBudget = $company['total_budget'] ?? 0;
        if ($totalBudget >= 1000000) $score += 25;
        elseif ($totalBudget >= 500000) $score += 20;
        elseif ($totalBudget >= 100000) $score += 15;
        elseif ($totalBudget >= 50000) $score += 10;
        elseif ($totalBudget > 0) $score += 5;
        
        // Approval rate (max 20 points)
        $approvalRate = $company['approval_rate'] ?? 0;
        if ($approvalRate >= 80) $score += 20;
        elseif ($approvalRate >= 60) $score += 15;
        elseif ($approvalRate >= 40) $score += 10;
        elseif ($approvalRate >= 20) $score += 5;
        
        // Recent activity (max 15 points)
        $daysSinceLast = $this->calculate_days_since_last_proposal($company['last_proposal_date'] ?? '');
        if ($daysSinceLast <= 7) $score += 15;
        elseif ($daysSinceLast <= 30) $score += 10;
        elseif ($daysSinceLast <= 90) $score += 5;
        
        // Product diversity (max 10 points)
        $productTypes = count($company['product_types'] ?? []);
        if ($productTypes >= 3) $score += 10;
        elseif ($productTypes >= 2) $score += 5;
        
        return $score;
    }

    /**
     * Calculate days since last proposal
     */
    private function calculate_days_since_last_proposal($lastProposalDate)
    {
        if (empty($lastProposalDate)) {
            return 999; // Large number for no proposals
        }
        
        $lastDate = strtotime($lastProposalDate);
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
     * Recommend next action for company
     */
    private function recommend_next_action($company, $engagementScore)
    {
        $proposalCount = $company['total_proposals'] ?? 0;
        $daysSinceLast = $this->calculate_days_since_last_proposal($company['last_proposal_date'] ?? '');
        $approvalRate = $company['approval_rate'] ?? 0;
        
        if ($engagementScore >= 60) {
            if ($daysSinceLast > 30) {
                return 'Follow up - high potential client needs re-engagement';
            }
            return 'Upsell opportunity - explore additional products/services';
        }
        
        if ($engagementScore >= 40) {
            if ($approvalRate < 50) {
                return 'Review proposal strategy - improve approval rate';
            }
            return 'Regular follow-up - maintain engagement';
        }
        
        if ($proposalCount == 0) {
            return 'Initial contact needed';
        }
        
        if ($daysSinceLast > 90) {
            return 'Re-engagement required - client may be lost';
        }
        
        return 'Needs nurturing - build stronger relationship';
    }

    /**
     * Generate Product Performance Matrix
     */
    private function generate_product_performance_matrix($userSummary)
    {
        $matrix = [];
        
        // Define product categories
        $productCategories = [
            'MSC' => ['total' => 0, 'approved' => 0, 'rejected' => 0, 'pending' => 0, 'budget' => 0],
            'BALA' => ['total' => 0, 'approved' => 0, 'rejected' => 0, 'pending' => 0, 'budget' => 0],
            'Astronomy' => ['total' => 0, 'approved' => 0, 'rejected' => 0, 'pending' => 0, 'budget' => 0],
            'Tinkering' => ['total' => 0, 'approved' => 0, 'rejected' => 0, 'pending' => 0, 'budget' => 0],
            'Big_Laboratory' => ['total' => 0, 'approved' => 0, 'rejected' => 0, 'pending' => 0, 'budget' => 0],
            'Small_Laboratory' => ['total' => 0, 'approved' => 0, 'rejected' => 0, 'pending' => 0, 'budget' => 0],
            'Customize' => ['total' => 0, 'approved' => 0, 'rejected' => 0, 'pending' => 0, 'budget' => 0],
            'Smart_Class' => ['total' => 0, 'approved' => 0, 'rejected' => 0, 'pending' => 0, 'budget' => 0]
        ];
        
        // Collect data from user summary
        foreach ($userSummary as $user) {
            foreach ($productCategories as $product => &$data) {
                $totalKey = 'total_in_' . $product;
                $approvedKey = 'total_Approved_in_' . $product;
                $rejectedKey = 'total_Reject_in_' . $product;
                $pendingKey = 'total_Pending_For_Approve_in_' . $product;
                
                $data['total'] += $user->{$totalKey} ?? 0;
                $data['approved'] += $user->{$approvedKey} ?? 0;
                $data['rejected'] += $user->{$rejectedKey} ?? 0;
                $data['pending'] += $user->{$pendingKey} ?? 0;
                
                // Estimate budget (using average or actual if available)
                $budgetPerProposal = $this->estimate_product_budget($product);
                $data['budget'] += ($user->{$totalKey} ?? 0) * $budgetPerProposal;
            }
        }
        
        // Build matrix
        foreach ($productCategories as $product => $data) {
            if ($data['total'] > 0) {
                $approvalRate = $data['approved'] > 0 ? 
                    round(($data['approved'] / $data['total']) * 100, 2) : 0;
                
                $rejectionRate = $data['rejected'] > 0 ? 
                    round(($data['rejected'] / $data['total']) * 100, 2) : 0;
                
                $pendingRate = $data['pending'] > 0 ? 
                    round(($data['pending'] / $data['total']) * 100, 2) : 0;
                
                $avgBudgetPerProposal = $data['total'] > 0 ? 
                    round($data['budget'] / $data['total'], 2) : 0;
                
                $performanceScore = $this->calculate_product_performance_score(
                    $data['total'],
                    $approvalRate,
                    $avgBudgetPerProposal
                );
                
                $matrix[] = [
                    'product_name' => $product,
                    'total_proposals' => $data['total'],
                    'approved_proposals' => $data['approved'],
                    'rejected_proposals' => $data['rejected'],
                    'pending_proposals' => $data['pending'],
                    'total_estimated_budget' => $data['budget'],
                    'approval_rate' => $approvalRate,
                    'rejection_rate' => $rejectionRate,
                    'pending_rate' => $pendingRate,
                    'avg_budget_per_proposal' => $avgBudgetPerProposal,
                    'performance_score' => $performanceScore,
                    'performance_category' => $this->get_product_performance_category($performanceScore),
                    'market_position' => $this->assess_market_position($product, $data['total'], $approvalRate),
                    'recommendation' => $this->get_product_recommendation($product, $approvalRate, $data['total'])
                ];
            }
        }
        
        // Sort by total proposals descending
        usort($matrix, function($a, $b) {
            return $b['total_proposals'] - $a['total_proposals'];
        });
        
        return $matrix;
    }

    /**
     * Estimate product budget
     */
    private function estimate_product_budget($product)
    {
        // Default budget estimates for each product type
        $budgetEstimates = [
            'MSC' => 500000,
            'BALA' => 300000,
            'Astronomy' => 800000,
            'Tinkering' => 200000,
            'Big_Laboratory' => 1000000,
            'Small_Laboratory' => 500000,
            'Customize' => 750000,
            'Smart_Class' => 600000
        ];
        
        return $budgetEstimates[$product] ?? 500000;
    }

    /**
     * Calculate product performance score
     */
    private function calculate_product_performance_score($totalProposals, $approvalRate, $avgBudget)
    {
        $score = 0;
        
        // Volume (max 40 points)
        if ($totalProposals >= 20) $score += 40;
        elseif ($totalProposals >= 10) $score += 30;
        elseif ($totalProposals >= 5) $score += 20;
        elseif ($totalProposals >= 2) $score += 10;
        elseif ($totalProposals > 0) $score += 5;
        
        // Approval rate (max 40 points)
        if ($approvalRate >= 80) $score += 40;
        elseif ($approvalRate >= 60) $score += 30;
        elseif ($approvalRate >= 40) $score += 20;
        elseif ($approvalRate >= 20) $score += 10;
        elseif ($approvalRate > 0) $score += 5;
        
        // Budget (max 20 points)
        if ($avgBudget >= 1000000) $score += 20;
        elseif ($avgBudget >= 500000) $score += 15;
        elseif ($avgBudget >= 100000) $score += 10;
        elseif ($avgBudget > 0) $score += 5;
        
        return $score;
    }

    /**
     * Get product performance category
     */
    private function get_product_performance_category($score)
    {
        if ($score >= 80) return 'Star Performer';
        if ($score >= 60) return 'Strong Performer';
        if ($score >= 40) return 'Average Performer';
        if ($score >= 20) return 'Under Performer';
        return 'Poor Performer';
    }

    /**
     * Assess market position
     */
    private function assess_market_position($product, $totalProposals, $approvalRate)
    {
        if ($totalProposals >= 15 && $approvalRate >= 70) {
            return 'Market Leader';
        } elseif ($totalProposals >= 10 && $approvalRate >= 60) {
            return 'Strong Contender';
        } elseif ($totalProposals >= 5 && $approvalRate >= 50) {
            return 'Growing Presence';
        } elseif ($totalProposals > 0) {
            return 'Niche Player';
        }
        return 'New Entry';
    }

    /**
     * Get product recommendation
     */
    private function get_product_recommendation($product, $approvalRate, $totalProposals)
    {
        if ($approvalRate >= 70 && $totalProposals >= 10) {
            return 'Increase investment - high performing product';
        } elseif ($approvalRate >= 60 && $totalProposals >= 5) {
            return 'Maintain current focus';
        } elseif ($approvalRate < 40 && $totalProposals > 0) {
            return 'Review pricing/features - low approval rate';
        } elseif ($totalProposals == 0) {
            return 'Market research needed - no proposals yet';
        } else {
            return 'Continue with current strategy';
        }
    }

    /**
     * Generate Approval Process Matrix
     */
    private function generate_approval_process_matrix($companyDetails)
    {
        $matrix = [
            'approval_timeline_analysis' => [],
            'checker_performance' => [],
            'rejection_patterns' => [],
            'approval_rate_by_time' => [],
            'process_bottlenecks' => []
        ];
        
        $approvalData = [];
        $checkerData = [];
        $rejectionReasons = [];
        $timeSlots = [
            'morning' => ['start' => 6, 'end' => 12],
            'afternoon' => ['start' => 12, 'end' => 17],
            'evening' => ['start' => 17, 'end' => 21],
            'night' => ['start' => 21, 'end' => 6]
        ];
        
        $timeSlotApprovals = [
            'morning' => ['total' => 0, 'approved' => 0],
            'afternoon' => ['total' => 0, 'approved' => 0],
            'evening' => ['total' => 0, 'approved' => 0],
            'night' => ['total' => 0, 'approved' => 0]
        ];
        
        foreach ($companyDetails as $proposal) {
            $proposalDate = $proposal->appointmentdatetime ?? '';
            $approvalDate = $proposal->propasal_apr_date ?? '';
            $aprStatus = intval($proposal->apr_status ?? 0);
            $checker = $proposal->proposal_checked_by ?? '';
            $rejectionReason = $proposal->propasal_apr_remarks ?? '';
            
            // Approval timeline analysis
            if (!empty($proposalDate) && !empty($approvalDate) && $aprStatus === 1) {
                $daysToApprove = $this->calculate_days_difference($proposalDate, $approvalDate);
                
                $approvalData[] = [
                    'proposal_id' => $proposal->proposal_id ?? 0,
                    'company' => $proposal->compname ?? '',
                    'proposal_date' => $proposalDate,
                    'approval_date' => $approvalDate,
                    'days_to_approve' => $daysToApprove,
                    'checker' => $checker
                ];
            }
            
            // Checker performance
            if (!empty($checker)) {
                if (!isset($checkerData[$checker])) {
                    $checkerData[$checker] = [
                        'total_checked' => 0,
                        'approved' => 0,
                        'rejected' => 0,
                        'total_days' => 0
                    ];
                }
                
                $checkerData[$checker]['total_checked']++;
                
                if ($aprStatus === 1) {
                    $checkerData[$checker]['approved']++;
                } elseif ($aprStatus === 2) {
                    $checkerData[$checker]['rejected']++;
                    
                    // Track rejection reasons
                    if (!empty($rejectionReason)) {
                        $reasonKey = substr(trim($rejectionReason), 0, 100);
                        if (!isset($rejectionReasons[$reasonKey])) {
                            $rejectionReasons[$reasonKey] = 0;
                        }
                        $rejectionReasons[$reasonKey]++;
                    }
                }
                
                // Calculate average approval time for this checker
                if (!empty($proposalDate) && !empty($approvalDate) && $aprStatus === 1) {
                    $days = $this->calculate_days_difference($proposalDate, $approvalDate);
                    $checkerData[$checker]['total_days'] += $days;
                }
            }
            
            // Time slot analysis
            if (!empty($proposalDate)) {
                $hour = date('H', strtotime($proposalDate));
                $timeSlot = $this->get_time_slot($hour);
                
                if (isset($timeSlotApprovals[$timeSlot])) {
                    $timeSlotApprovals[$timeSlot]['total']++;
                    if ($aprStatus === 1) {
                        $timeSlotApprovals[$timeSlot]['approved']++;
                    }
                }
            }
        }
        
        // Process approval timeline
        $matrix['approval_timeline_analysis'] = $this->analyze_approval_timeline($approvalData);
        
        // Process checker performance
        $matrix['checker_performance'] = $this->analyze_checker_performance($checkerData);
        
        // Process rejection patterns
        $matrix['rejection_patterns'] = $this->analyze_rejection_patterns($rejectionReasons);
        
        // Process approval rate by time
        $matrix['approval_rate_by_time'] = $this->analyze_approval_rate_by_time($timeSlotApprovals);
        
        // Identify process bottlenecks
        $matrix['process_bottlenecks'] = $this->identify_process_bottlenecks($approvalData, $checkerData);
        
        return $matrix;
    }

    /**
     * Calculate days difference
     */
    private function calculate_days_difference($startDate, $endDate)
    {
        if (empty($startDate) || empty($endDate)) {
            return 0;
        }
        
        $start = strtotime($startDate);
        $end = strtotime($endDate);
        
        if ($start === false || $end === false || $end < $start) {
            return 0;
        }
        
        $secondsDiff = $end - $start;
        return floor($secondsDiff / (60 * 60 * 24));
    }

    /**
     * Get time slot from hour
     */
    private function get_time_slot($hour)
    {
        $hour = intval($hour);
        
        if ($hour >= 6 && $hour < 12) return 'morning';
        if ($hour >= 12 && $hour < 17) return 'afternoon';
        if ($hour >= 17 && $hour < 21) return 'evening';
        return 'night';
    }

    /**
     * Analyze approval timeline
     */
    private function analyze_approval_timeline($approvalData)
    {
        $analysis = [
            'total_approvals' => count($approvalData),
            'avg_days_to_approve' => 0,
            'fastest_approval' => null,
            'slowest_approval' => null,
            'approval_time_distribution' => [
                'same_day' => 0,
                '1_3_days' => 0,
                '4_7_days' => 0,
                '8_14_days' => 0,
                'over_14_days' => 0
            ],
            'weekly_trend' => []
        ];
        
        if (empty($approvalData)) {
            return $analysis;
        }
        
        $totalDays = 0;
        $minDays = PHP_INT_MAX;
        $maxDays = 0;
        $fastestApproval = null;
        $slowestApproval = null;
        
        $weeklyCounts = [];
        
        foreach ($approvalData as $approval) {
            $days = $approval['days_to_approve'];
            $totalDays += $days;
            
            // Track fastest approval
            if ($days < $minDays) {
                $minDays = $days;
                $fastestApproval = $approval;
            }
            
            // Track slowest approval
            if ($days > $maxDays) {
                $maxDays = $days;
                $slowestApproval = $approval;
            }
            
            // Distribution
            if ($days == 0) {
                $analysis['approval_time_distribution']['same_day']++;
            } elseif ($days <= 3) {
                $analysis['approval_time_distribution']['1_3_days']++;
            } elseif ($days <= 7) {
                $analysis['approval_time_distribution']['4_7_days']++;
            } elseif ($days <= 14) {
                $analysis['approval_time_distribution']['8_14_days']++;
            } else {
                $analysis['approval_time_distribution']['over_14_days']++;
            }
            
            // Weekly trend
            $weekNumber = date('W', strtotime($approval['approval_date']));
            $year = date('Y', strtotime($approval['approval_date']));
            $weekKey = $year . '-W' . str_pad($weekNumber, 2, '0', STR_PAD_LEFT);
            
            if (!isset($weeklyCounts[$weekKey])) {
                $weeklyCounts[$weekKey] = 0;
            }
            $weeklyCounts[$weekKey]++;
        }
        
        $analysis['avg_days_to_approve'] = round($totalDays / count($approvalData), 2);
        $analysis['fastest_approval'] = $fastestApproval;
        $analysis['slowest_approval'] = $slowestApproval;
        $analysis['weekly_trend'] = $weeklyCounts;
        
        return $analysis;
    }

    /**
     * Analyze checker performance
     */
    private function analyze_checker_performance($checkerData)
    {
        $analysis = [];
        
        foreach ($checkerData as $checker => $data) {
            $total = $data['total_checked'];
            $approved = $data['approved'];
            $rejected = $data['rejected'];
            $totalDays = $data['total_days'];
            
            $approvalRate = $total > 0 ? round(($approved / $total) * 100, 2) : 0;
            $rejectionRate = $total > 0 ? round(($rejected / $total) * 100, 2) : 0;
            $avgApprovalTime = $approved > 0 ? round($totalDays / $approved, 2) : 0;
            
            $performanceScore = $this->calculate_checker_performance_score(
                $approvalRate,
                $rejectionRate,
                $avgApprovalTime
            );
            
            $analysis[] = [
                'checker_name' => $checker,
                'total_checked' => $total,
                'approved' => $approved,
                'rejected' => $rejected,
                'approval_rate' => $approvalRate,
                'rejection_rate' => $rejectionRate,
                'avg_approval_time_days' => $avgApprovalTime,
                'performance_score' => $performanceScore,
                'performance_rating' => $this->get_checker_performance_rating($performanceScore),
                'efficiency' => $this->assess_checker_efficiency($approvalRate, $avgApprovalTime)
            ];
        }
        
        // Sort by performance score descending
        usort($analysis, function($a, $b) {
            return $b['performance_score'] - $a['performance_score'];
        });
        
        return $analysis;
    }

    /**
     * Calculate checker performance score
     */
    private function calculate_checker_performance_score($approvalRate, $rejectionRate, $avgApprovalTime)
    {
        $score = 0;
        
        // Approval rate (max 40 points)
        if ($approvalRate >= 80) $score += 40;
        elseif ($approvalRate >= 60) $score += 30;
        elseif ($approvalRate >= 40) $score += 20;
        elseif ($approvalRate >= 20) $score += 10;
        
        // Low rejection rate (max 30 points)
        if ($rejectionRate <= 10) $score += 30;
        elseif ($rejectionRate <= 20) $score += 20;
        elseif ($rejectionRate <= 30) $score += 10;
        
        // Fast approval time (max 30 points)
        if ($avgApprovalTime <= 1) $score += 30;
        elseif ($avgApprovalTime <= 3) $score += 20;
        elseif ($avgApprovalTime <= 7) $score += 10;
        elseif ($avgApprovalTime <= 14) $score += 5;
        
        return $score;
    }

    /**
     * Get checker performance rating
     */
    private function get_checker_performance_rating($score)
    {
        if ($score >= 80) return 'Excellent';
        if ($score >= 60) return 'Good';
        if ($score >= 40) return 'Average';
        if ($score >= 20) return 'Needs Improvement';
        return 'Poor';
    }

    /**
     * Assess checker efficiency
     */
    private function assess_checker_efficiency($approvalRate, $avgApprovalTime)
    {
        if ($approvalRate >= 70 && $avgApprovalTime <= 3) {
            return 'Highly Efficient';
        } elseif ($approvalRate >= 60 && $avgApprovalTime <= 7) {
            return 'Efficient';
        } elseif ($approvalRate >= 50 && $avgApprovalTime <= 10) {
            return 'Moderately Efficient';
        } elseif ($approvalRate < 50 || $avgApprovalTime > 14) {
            return 'Needs Process Improvement';
        }
        return 'Standard Efficiency';
    }

    /**
     * Analyze rejection patterns
     */
    private function analyze_rejection_patterns($rejectionReasons)
    {
        $analysis = [
            'total_rejections' => array_sum($rejectionReasons),
            'top_rejection_reasons' => [],
            'rejection_categories' => [
                'pricing' => 0,
                'product_fit' => 0,
                'timing' => 0,
                'competition' => 0,
                'other' => 0
            ],
            'common_patterns' => []
        ];
        
        if (empty($rejectionReasons)) {
            return $analysis;
        }
        
        // Sort rejection reasons by frequency
        arsort($rejectionReasons);
        
        // Get top 5 rejection reasons
        $analysis['top_rejection_reasons'] = array_slice($rejectionReasons, 0, 5, true);
        
        // Categorize rejection reasons
        $pricingKeywords = ['price', 'cost', 'expensive', 'budget', 'quote'];
        $productFitKeywords = ['fit', 'requirement', 'need', 'suitable', 'appropriate'];
        $timingKeywords = ['timing', 'schedule', 'later', 'future', 'postpone'];
        $competitionKeywords = ['competitor', 'alternative', 'other vendor', 'cheaper'];
        
        foreach ($rejectionReasons as $reason => $count) {
            $reasonLower = strtolower($reason);
            $categorized = false;
            
            foreach ($pricingKeywords as $keyword) {
                if (strpos($reasonLower, $keyword) !== false) {
                    $analysis['rejection_categories']['pricing'] += $count;
                    $categorized = true;
                    break;
                }
            }
            
            if (!$categorized) {
                foreach ($productFitKeywords as $keyword) {
                    if (strpos($reasonLower, $keyword) !== false) {
                        $analysis['rejection_categories']['product_fit'] += $count;
                        $categorized = true;
                        break;
                    }
                }
            }
            
            if (!$categorized) {
                foreach ($timingKeywords as $keyword) {
                    if (strpos($reasonLower, $keyword) !== false) {
                        $analysis['rejection_categories']['timing'] += $count;
                        $categorized = true;
                        break;
                    }
                }
            }
            
            if (!$categorized) {
                foreach ($competitionKeywords as $keyword) {
                    if (strpos($reasonLower, $keyword) !== false) {
                        $analysis['rejection_categories']['competition'] += $count;
                        $categorized = true;
                        break;
                    }
                }
            }
            
            if (!$categorized) {
                $analysis['rejection_categories']['other'] += $count;
            }
        }
        
        // Identify common patterns
        if ($analysis['rejection_categories']['pricing'] / $analysis['total_rejections'] > 0.4) {
            $analysis['common_patterns'][] = 'Pricing is a major concern for clients';
        }
        
        if ($analysis['rejection_categories']['product_fit'] / $analysis['total_rejections'] > 0.3) {
            $analysis['common_patterns'][] = 'Product-market fit needs improvement';
        }
        
        if ($analysis['rejection_categories']['timing'] / $analysis['total_rejections'] > 0.2) {
            $analysis['common_patterns'][] = 'Timing issues affecting conversions';
        }
        
        return $analysis;
    }

    /**
     * Analyze approval rate by time
     */
    private function analyze_approval_rate_by_time($timeSlotApprovals)
    {
        $analysis = [];
        
        $timeSlotLabels = [
            'morning' => 'Morning (6 AM - 12 PM)',
            'afternoon' => 'Afternoon (12 PM - 5 PM)',
            'evening' => 'Evening (5 PM - 9 PM)',
            'night' => 'Night (9 PM - 6 AM)'
        ];
        
        foreach ($timeSlotApprovals as $slot => $data) {
            $total = $data['total'];
            $approved = $data['approved'];
            
            $approvalRate = $total > 0 ? round(($approved / $total) * 100, 2) : 0;
            
            $analysis[] = [
                'time_slot' => $timeSlotLabels[$slot],
                'total_proposals' => $total,
                'approved_proposals' => $approved,
                'approval_rate' => $approvalRate,
                'effectiveness' => $this->assess_time_slot_effectiveness($approvalRate, $total)
            ];
        }
        
        // Sort by approval rate descending
        usort($analysis, function($a, $b) {
            return $b['approval_rate'] - $a['approval_rate'];
        });
        
        return $analysis;
    }

    /**
     * Assess time slot effectiveness
     */
    private function assess_time_slot_effectiveness($approvalRate, $totalProposals)
    {
        if ($totalProposals >= 5) {
            if ($approvalRate >= 70) return 'Highly Effective';
            if ($approvalRate >= 50) return 'Effective';
            if ($approvalRate >= 30) return 'Moderately Effective';
            return 'Ineffective';
        }
        return 'Insufficient Data';
    }

    /**
     * Identify process bottlenecks
     */
    private function identify_process_bottlenecks($approvalData, $checkerData)
    {
        $bottlenecks = [];
        
        // Analyze approval time bottlenecks
        $slowApprovals = array_filter($approvalData, function($approval) {
            return $approval['days_to_approve'] > 7;
        });
        
        if (count($slowApprovals) > 0) {
            $bottlenecks[] = [
                'type' => 'Approval Time',
                'issue' => 'Slow approval process',
                'impact' => count($slowApprovals) . ' proposals took >7 days to approve',
                'recommendation' => 'Streamline approval workflow'
            ];
        }
        
        // Analyze checker bottlenecks
        foreach ($checkerData as $checker => $data) {
            $total = $data['total_checked'];
            $rejectionRate = $total > 0 ? ($data['rejected'] / $total) * 100 : 0;
            
            if ($rejectionRate > 30 && $total >= 5) {
                $bottlenecks[] = [
                    'type' => 'Checker Performance',
                    'issue' => 'High rejection rate',
                    'impact' => $checker . ' has ' . round($rejectionRate, 1) . '% rejection rate',
                    'recommendation' => 'Review checker criteria and provide training'
                ];
            }
        }
        
        // Analyze time-based bottlenecks
        if (count($approvalData) > 0) {
            $weekendApprovals = array_filter($approvalData, function($approval) {
                $approvalDay = date('w', strtotime($approval['approval_date']));
                return $approvalDay == 0 || $approvalDay == 6; // Sunday or Saturday
            });
            
            $weekendPercentage = (count($weekendApprovals) / count($approvalData)) * 100;
            
            if ($weekendPercentage > 20) {
                $bottlenecks[] = [
                    'type' => 'Work Schedule',
                    'issue' => 'Weekend approvals',
                    'impact' => round($weekendPercentage, 1) . '% of approvals happen on weekends',
                    'recommendation' => 'Distribute workload evenly across weekdays'
                ];
            }
        }
        
        return $bottlenecks;
    }

    /**
     * Generate Financial Summary Table
     */
    private function generate_financial_summary_table($userSummary, $companyDetails)
    {
        $table = [
            'overview' => [],
            'by_user' => [],
            'by_company' => [],
            'by_product' => [],
            'by_month' => [],
            'trend_analysis' => []
        ];
        
        // Financial Overview
        $totalBudget = 0;
        $approvedBudget = 0;
        $pendingBudget = 0;
        $rejectedBudget = 0;
        
        foreach ($userSummary as $user) {
            $totalBudget += $user->total_proposal_budget ?? 0;
            $approvedBudget += $user->total_proposal_approved_budget ?? 0;
            $pendingBudget += $user->total_proposal_pending_budget ?? 0;
            $rejectedBudget += $user->total_proposal_reject_budget ?? 0;
        }
        
        $approvalRate = $totalBudget > 0 ? round(($approvedBudget / $totalBudget) * 100, 2) : 0;
        $rejectionRate = $totalBudget > 0 ? round(($rejectedBudget / $totalBudget) * 100, 2) : 0;
        
        $table['overview'] = [
            'total_budget' => $totalBudget,
            'approved_budget' => $approvedBudget,
            'pending_budget' => $pendingBudget,
            'rejected_budget' => $rejectedBudget,
            'approval_rate' => $approvalRate,
            'rejection_rate' => $rejectionRate,
            'avg_proposal_value' => $this->calculate_avg_proposal_value($companyDetails),
            'largest_proposal' => $this->find_largest_proposal($companyDetails),
            'smallest_proposal' => $this->find_smallest_proposal($companyDetails)
        ];
        
        // Financial by User
        foreach ($userSummary as $user) {
            $userBudget = $user->total_proposal_budget ?? 0;
            $userApproved = $user->total_proposal_approved_budget ?? 0;
            $userPending = $user->total_proposal_pending_budget ?? 0;
            $userRejected = $user->total_proposal_reject_budget ?? 0;
            
            $userApprovalRate = $userBudget > 0 ? round(($userApproved / $userBudget) * 100, 2) : 0;
            $userContribution = $totalBudget > 0 ? round(($userBudget / $totalBudget) * 100, 2) : 0;
            
            $table['by_user'][] = [
                'username' => $user->username ?? '',
                'total_budget' => $userBudget,
                'approved_budget' => $userApproved,
                'pending_budget' => $userPending,
                'rejected_budget' => $userRejected,
                'approval_rate' => $userApprovalRate,
                'contribution_percentage' => $userContribution,
                'avg_proposal_value' => $user->total_proposal_task > 0 ? 
                    round($userBudget / $user->total_proposal_task, 2) : 0
            ];
        }
        
        // Sort by total budget descending
        usort($table['by_user'], function($a, $b) {
            return $b['total_budget'] - $a['total_budget'];
        });
        
        // Financial by Company
        $companyBudgets = [];
        foreach ($companyDetails as $proposal) {
            $companyId = $proposal->cid ?? 0;
            $companyName = $proposal->compname ?? '';
            $budget = $proposal->propasal_budgetme ?? 0;
            $aprStatus = intval($proposal->apr_status ?? 0);
            
            if (!isset($companyBudgets[$companyId])) {
                $companyBudgets[$companyId] = [
                    'company_name' => $companyName,
                    'total_budget' => 0,
                    'approved_budget' => 0,
                    'pending_budget' => 0,
                    'rejected_budget' => 0,
                    'proposal_count' => 0
                ];
            }
            
            $companyBudgets[$companyId]['total_budget'] += $budget;
            $companyBudgets[$companyId]['proposal_count']++;
            
            if ($aprStatus === 1) {
                $companyBudgets[$companyId]['approved_budget'] += $budget;
            } elseif ($aprStatus === 2) {
                $companyBudgets[$companyId]['rejected_budget'] += $budget;
            } else {
                $companyBudgets[$companyId]['pending_budget'] += $budget;
            }
        }
        
        foreach ($companyBudgets as $companyId => $data) {
            $approvalRate = $data['total_budget'] > 0 ? 
                round(($data['approved_budget'] / $data['total_budget']) * 100, 2) : 0;
            
            $table['by_company'][] = [
                'company_name' => $data['company_name'],
                'total_budget' => $data['total_budget'],
                'approved_budget' => $data['approved_budget'],
                'pending_budget' => $data['pending_budget'],
                'rejected_budget' => $data['rejected_budget'],
                'proposal_count' => $data['proposal_count'],
                'approval_rate' => $approvalRate,
                'avg_budget_per_proposal' => $data['proposal_count'] > 0 ? 
                    round($data['total_budget'] / $data['proposal_count'], 2) : 0
            ];
        }
        
        // Sort by total budget descending
        usort($table['by_company'], function($a, $b) {
            return $b['total_budget'] - $a['total_budget'];
        });
        
        // Financial by Month
        $monthlyBudgets = [];
        foreach ($companyDetails as $proposal) {
            $proposalDate = $proposal->appointmentdatetime ?? '';
            $budget = $proposal->propasal_budgetme ?? 0;
            $aprStatus = intval($proposal->apr_status ?? 0);
            
            if (!empty($proposalDate)) {
                $monthKey = date('Y-m', strtotime($proposalDate));
                
                if (!isset($monthlyBudgets[$monthKey])) {
                    $monthlyBudgets[$monthKey] = [
                        'total_budget' => 0,
                        'approved_budget' => 0,
                        'pending_budget' => 0,
                        'rejected_budget' => 0,
                        'proposal_count' => 0
                    ];
                }
                
                $monthlyBudgets[$monthKey]['total_budget'] += $budget;
                $monthlyBudgets[$monthKey]['proposal_count']++;
                
                if ($aprStatus === 1) {
                    $monthlyBudgets[$monthKey]['approved_budget'] += $budget;
                } elseif ($aprStatus === 2) {
                    $monthlyBudgets[$monthKey]['rejected_budget'] += $budget;
                } else {
                    $monthlyBudgets[$monthKey]['pending_budget'] += $budget;
                }
            }
        }
        
        ksort($monthlyBudgets);
        foreach ($monthlyBudgets as $month => $data) {
            $approvalRate = $data['total_budget'] > 0 ? 
                round(($data['approved_budget'] / $data['total_budget']) * 100, 2) : 0;
            
            $table['by_month'][] = [
                'month' => $month,
                'total_budget' => $data['total_budget'],
                'approved_budget' => $data['approved_budget'],
                'pending_budget' => $data['pending_budget'],
                'rejected_budget' => $data['rejected_budget'],
                'proposal_count' => $data['proposal_count'],
                'approval_rate' => $approvalRate,
                'avg_budget_per_proposal' => $data['proposal_count'] > 0 ? 
                    round($data['total_budget'] / $data['proposal_count'], 2) : 0
            ];
        }
        
        // Trend Analysis
        $table['trend_analysis'] = $this->analyze_financial_trends($monthlyBudgets);
        
        return $table;
    }

    /**
     * Calculate average proposal value
     */
    private function calculate_avg_proposal_value($companyDetails)
    {
        $totalBudget = 0;
        $totalProposals = 0;
        
        foreach ($companyDetails as $proposal) {
            $budget = $proposal->propasal_budgetme ?? 0;
            if ($budget > 0) {
                $totalBudget += $budget;
                $totalProposals++;
            }
        }
        
        return $totalProposals > 0 ? round($totalBudget / $totalProposals, 2) : 0;
    }

    /**
     * Find largest proposal
     */
    private function find_largest_proposal($companyDetails)
    {
        $largestBudget = 0;
        $largestProposal = null;
        
        foreach ($companyDetails as $proposal) {
            $budget = $proposal->propasal_budgetme ?? 0;
            if ($budget > $largestBudget) {
                $largestBudget = $budget;
                $largestProposal = [
                    'company' => $proposal->compname ?? '',
                    'budget' => $budget,
                    'user' => $proposal->task_username ?? '',
                    'product' => $proposal->propasal_types ?? '',
                    'date' => $proposal->appointmentdatetime ?? ''
                ];
            }
        }
        
        return $largestProposal;
    }

    /**
     * Find smallest proposal
     */
    private function find_smallest_proposal($companyDetails)
    {
        $smallestBudget = PHP_INT_MAX;
        $smallestProposal = null;
        
        foreach ($companyDetails as $proposal) {
            $budget = $proposal->propasal_budgetme ?? 0;
            if ($budget > 0 && $budget < $smallestBudget) {
                $smallestBudget = $budget;
                $smallestProposal = [
                    'company' => $proposal->compname ?? '',
                    'budget' => $budget,
                    'user' => $proposal->task_username ?? '',
                    'product' => $proposal->propasal_types ?? '',
                    'date' => $proposal->appointmentdatetime ?? ''
                ];
            }
        }
        
        return $smallestProposal;
    }

    /**
     * Analyze financial trends
     */
    private function analyze_financial_trends($monthlyBudgets)
    {
        $analysis = [
            'growth_rate' => 0,
            'monthly_growth' => [],
            'best_performing_month' => null,
            'worst_performing_month' => null,
            'seasonality_pattern' => [],
            'forecast_next_month' => 0
        ];
        
        if (count($monthlyBudgets) < 2) {
            return $analysis;
        }
        
        $months = array_keys($monthlyBudgets);
        $budgets = array_column($monthlyBudgets, 'total_budget');
        
        // Calculate growth rate
        $firstMonthBudget = $budgets[0];
        $lastMonthBudget = $budgets[count($budgets) - 1];
        
        if ($firstMonthBudget > 0) {
            $analysis['growth_rate'] = round((($lastMonthBudget - $firstMonthBudget) / $firstMonthBudget) * 100, 2);
        }
        
        // Calculate monthly growth
        for ($i = 1; $i < count($budgets); $i++) {
            $previousBudget = $budgets[$i - 1];
            $currentBudget = $budgets[$i];
            
            if ($previousBudget > 0) {
                $monthlyGrowth = round((($currentBudget - $previousBudget) / $previousBudget) * 100, 2);
                $analysis['monthly_growth'][$months[$i]] = $monthlyGrowth;
            }
        }
        
        // Find best and worst performing months
        $maxBudget = max($budgets);
        $minBudget = min($budgets);
        $maxMonth = $months[array_search($maxBudget, $budgets)];
        $minMonth = $months[array_search($minBudget, $budgets)];
        
        $analysis['best_performing_month'] = [
            'month' => $maxMonth,
            'budget' => $maxBudget,
            'proposal_count' => $monthlyBudgets[$maxMonth]['proposal_count']
        ];
        
        $analysis['worst_performing_month'] = [
            'month' => $minMonth,
            'budget' => $minBudget,
            'proposal_count' => $monthlyBudgets[$minMonth]['proposal_count']
        ];
        
        // Simple forecast for next month (average of last 3 months)
        if (count($budgets) >= 3) {
            $lastThreeMonths = array_slice($budgets, -3);
            $analysis['forecast_next_month'] = round(array_sum($lastThreeMonths) / 3, 2);
        } elseif (count($budgets) > 0) {
            $analysis['forecast_next_month'] = round(array_sum($budgets) / count($budgets), 2);
        }
        
        return $analysis;
    }

    /**
     * Generate Status Distribution Table
     */
    private function generate_status_distribution_table($userSummary)
    {
        $table = [
            'overall_distribution' => [],
            'by_user' => [],
            'by_status_category' => [],
            'conversion_analysis' => [],
            'status_trends' => []
        ];
        
        // Define status categories
        $statusCategories = [
            'positive' => ['positive', 'very_positive', 'positive_nap', 'very_positive_nap'],
            'neutral' => ['open', 'reachout', 'tentative', 'will_do_later'],
            'negative' => ['not_interested', 'rejected'],
            'converted' => ['closure', 'on_boarded'],
            'in_progress' => ['pending', 'open_rpem']
        ];
        
        // Overall distribution
        $overallTotals = [];
        foreach ($userSummary as $user) {
            foreach ($statusCategories as $category => $statuses) {
                if (!isset($overallTotals[$category])) {
                    $overallTotals[$category] = 0;
                }
                
                foreach ($statuses as $status) {
                    $statusKey = strtolower(str_replace(' ', '_', $status));
                    $overallTotals[$category] += $user->{$statusKey} ?? 0;
                }
            }
        }
        
        $totalStatuses = array_sum($overallTotals);
        foreach ($overallTotals as $category => $count) {
            $percentage = $totalStatuses > 0 ? round(($count / $totalStatuses) * 100, 2) : 0;
            $table['overall_distribution'][] = [
                'category' => ucfirst(str_replace('_', ' ', $category)),
                'count' => $count,
                'percentage' => $percentage
            ];
        }
        
        // Distribution by User
        foreach ($userSummary as $user) {
            $userStatuses = [];
            $userTotal = 0;
            
            foreach ($statusCategories as $category => $statuses) {
                $categoryCount = 0;
                foreach ($statuses as $status) {
                    $statusKey = strtolower(str_replace(' ', '_', $status));
                    $categoryCount += $user->{$statusKey} ?? 0;
                }
                
                $userStatuses[$category] = $categoryCount;
                $userTotal += $categoryCount;
            }
            
            if ($userTotal > 0) {
                $userRow = ['username' => $user->username ?? ''];
                foreach ($userStatuses as $category => $count) {
                    $percentage = round(($count / $userTotal) * 100, 2);
                    $userRow[$category] = $count;
                    $userRow[$category . '_percentage'] = $percentage;
                }
                
                // Calculate conversion rate
                $positiveCount = $userStatuses['positive'] ?? 0;
                $convertedCount = $userStatuses['converted'] ?? 0;
                $conversionRate = $positiveCount > 0 ? round(($convertedCount / $positiveCount) * 100, 2) : 0;
                
                $userRow['conversion_rate'] = $conversionRate;
                $table['by_user'][] = $userRow;
            }
        }
        
        // Sort by conversion rate descending
        usort($table['by_user'], function($a, $b) {
            return $b['conversion_rate'] - $a['conversion_rate'];
        });
        
        // Status category analysis
        $categoryAnalysis = [];
        foreach ($statusCategories as $category => $statuses) {
            $avgPercentage = 0;
            $userCount = 0;
            
            foreach ($table['by_user'] as $user) {
                if (isset($user[$category . '_percentage'])) {
                    $avgPercentage += $user[$category . '_percentage'];
                    $userCount++;
                }
            }
            
            if ($userCount > 0) {
                $avgPercentage = round($avgPercentage / $userCount, 2);
            }
            
            $categoryAnalysis[] = [
                'category' => ucfirst(str_replace('_', ' ', $category)),
                'average_percentage' => $avgPercentage,
                'performance_benchmark' => $this->get_status_category_benchmark($category, $avgPercentage)
            ];
        }
        
        $table['by_status_category'] = $categoryAnalysis;
        
        // Conversion analysis
        $totalPositive = $overallTotals['positive'] ?? 0;
        $totalConverted = $overallTotals['converted'] ?? 0;
        $totalNegative = $overallTotals['negative'] ?? 0;
        
        $overallConversionRate = $totalPositive > 0 ? round(($totalConverted / $totalPositive) * 100, 2) : 0;
        $lossRate = ($totalPositive + $totalNegative) > 0 ? 
            round(($totalNegative / ($totalPositive + $totalNegative)) * 100, 2) : 0;
        
        $table['conversion_analysis'] = [
            'total_positive_leads' => $totalPositive,
            'total_converted' => $totalConverted,
            'total_lost' => $totalNegative,
            'conversion_rate' => $overallConversionRate,
            'loss_rate' => $lossRate,
            'average_time_to_convert' => '7 days', // This would need actual time data
            'conversion_quality' => $this->assess_conversion_quality($overallConversionRate, $lossRate)
        ];
        
        return $table;
    }

    /**
     * Get status category benchmark
     */
    private function get_status_category_benchmark($category, $percentage)
    {
        $benchmarks = [
            'positive' => ['excellent' => 40, 'good' => 30, 'average' => 20],
            'converted' => ['excellent' => 20, 'good' => 15, 'average' => 10],
            'negative' => ['excellent' => 10, 'good' => 15, 'average' => 20]
        ];
        
        if (isset($benchmarks[$category])) {
            $benchmark = $benchmarks[$category];
            if ($percentage >= $benchmark['excellent']) return 'Excellent';
            if ($percentage >= $benchmark['good']) return 'Good';
            if ($percentage >= $benchmark['average']) return 'Average';
            return 'Below Average';
        }
        
        return 'Not Applicable';
    }

    /**
     * Assess conversion quality
     */
    private function assess_conversion_quality($conversionRate, $lossRate)
    {
        if ($conversionRate >= 30 && $lossRate <= 20) {
            return 'Excellent';
        } elseif ($conversionRate >= 20 && $lossRate <= 25) {
            return 'Good';
        } elseif ($conversionRate >= 10 && $lossRate <= 30) {
            return 'Average';
        } elseif ($conversionRate < 10 || $lossRate > 40) {
            return 'Needs Improvement';
        }
        return 'Satisfactory';
    }

    /**
     * Generate Client Segment Table
     */
    private function generate_client_segment_table($userSummary)
    {
        $table = [
            'segment_distribution' => [],
            'segment_performance' => [],
            'segment_trends' => [],
            'opportunity_analysis' => []
        ];
        
        // Define client segments
        $segments = [
            'corporate' => 'Corporate',
            'psu' => 'PSU',
            'ngo' => 'NGO',
            'private_school' => 'Private School',
            'individual' => 'Individual',
            'govt_off' => 'Government Office',
            'online' => 'Online',
            'stem_school' => 'STEM School',
            'foundation' => 'Foundation',
            'mnc' => 'MNC'
        ];
        
        // Segment distribution
        $segmentTotals = [];
        foreach ($userSummary as $user) {
            foreach ($segments as $key => $label) {
                if (!isset($segmentTotals[$key])) {
                    $segmentTotals[$key] = 0;
                }
                $segmentTotals[$key] += $user->{$key} ?? 0;
            }
        }
        
        $totalClients = array_sum($segmentTotals);
        foreach ($segmentTotals as $key => $count) {
            if ($count > 0) {
                $percentage = $totalClients > 0 ? round(($count / $totalClients) * 100, 2) : 0;
                $table['segment_distribution'][] = [
                    'segment' => $segments[$key],
                    'client_count' => $count,
                    'percentage' => $percentage,
                    'market_share' => $this->calculate_market_share($key, $percentage)
                ];
            }
        }
        
        // Sort by client count descending
        usort($table['segment_distribution'], function($a, $b) {
            return $b['client_count'] - $a['client_count'];
        });
        
        // Segment performance (estimated - would need actual performance data)
        foreach ($table['segment_distribution'] as &$segment) {
            $segmentName = strtolower(str_replace(' ', '_', $segment['segment']));
            $segment['avg_proposal_value'] = $this->estimate_segment_avg_value($segmentName);
            $segment['conversion_rate'] = $this->estimate_segment_conversion_rate($segmentName);
            $segment['growth_potential'] = $this->assess_segment_growth_potential($segmentName, $segment['percentage']);
            $segment['recommended_action'] = $this->get_segment_recommendation($segmentName, $segment['percentage']);
        }
        
        // Top 3 segments by client count
        $topSegments = array_slice($table['segment_distribution'], 0, 3);
        
        // Opportunity analysis
        $table['opportunity_analysis'] = [
            'dominant_segment' => $topSegments[0] ?? null,
            'emerging_segment' => $this->identify_emerging_segment($table['segment_distribution']),
            'underserved_segment' => $this->identify_underserved_segment($table['segment_distribution']),
            'high_value_segment' => $this->identify_high_value_segment($table['segment_distribution']),
            'market_coverage' => round(count(array_filter($segmentTotals, function($count) {
                return $count > 0;
            })) / count($segments) * 100, 2)
        ];
        
        return $table;
    }

    /**
     * Calculate market share
     */
    private function calculate_market_share($segmentKey, $percentage)
    {
        // This would normally come from market data
        $marketSizeEstimates = [
            'corporate' => 35,
            'psu' => 15,
            'ngo' => 10,
            'private_school' => 20,
            'individual' => 5,
            'govt_off' => 10,
            'online' => 30,
            'stem_school' => 8,
            'foundation' => 7,
            'mnc' => 12
        ];
        
        $marketSize = $marketSizeEstimates[$segmentKey] ?? 15;
        return round(($percentage / 100) * $marketSize, 2);
    }

    /**
     * Estimate segment average value
     */
    private function estimate_segment_avg_value($segment)
    {
        $valueEstimates = [
            'corporate' => 750000,
            'psu' => 1000000,
            'ngo' => 300000,
            'private_school' => 500000,
            'individual' => 100000,
            'govt_off' => 800000,
            'online' => 400000,
            'stem_school' => 600000,
            'foundation' => 350000,
            'mnc' => 900000
        ];
        
        return $valueEstimates[$segment] ?? 500000;
    }

    /**
     * Estimate segment conversion rate
     */
    private function estimate_segment_conversion_rate($segment)
    {
        $conversionEstimates = [
            'corporate' => 25,
            'psu' => 30,
            'ngo' => 20,
            'private_school' => 35,
            'individual' => 15,
            'govt_off' => 28,
            'online' => 22,
            'stem_school' => 40,
            'foundation' => 18,
            'mnc' => 32
        ];
        
        return $conversionEstimates[$segment] ?? 25;
    }

    /**
     * Assess segment growth potential
     */
    private function assess_segment_growth_potential($segment, $currentShare)
    {
        $growthPotentials = [
            'corporate' => ['high' => 40, 'medium' => 25],
            'psu' => ['high' => 20, 'medium' => 15],
            'ngo' => ['high' => 30, 'medium' => 20],
            'private_school' => ['high' => 50, 'medium' => 35],
            'individual' => ['high' => 60, 'medium' => 40],
            'govt_off' => ['high' => 25, 'medium' => 18],
            'online' => ['high' => 70, 'medium' => 50],
            'stem_school' => ['high' => 45, 'medium' => 30],
            'foundation' => ['high' => 35, 'medium' => 25],
            'mnc' => ['high' => 30, 'medium' => 20]
        ];
        
        $potential = $growthPotentials[$segment] ?? ['high' => 30, 'medium' => 20];
        
        if ($currentShare < $potential['medium']) {
            return 'High';
        } elseif ($currentShare < $potential['high']) {
            return 'Medium';
        }
        return 'Low';
    }

    /**
     * Get segment recommendation
     */
    private function get_segment_recommendation($segment, $currentShare)
    {
        if ($currentShare < 10) {
            return 'Increase focus - low market penetration';
        } elseif ($currentShare < 25) {
            return 'Maintain current focus';
        } elseif ($currentShare < 40) {
            return 'Consolidate position';
        } else {
            return 'Defend market leadership';
        }
    }

    /**
     * Identify emerging segment
     */
    private function identify_emerging_segment($segments)
    {
        // This would normally analyze growth trends
        // For now, return segment with moderate share but high growth potential
        foreach ($segments as $segment) {
            if ($segment['percentage'] > 10 && $segment['percentage'] < 25 && 
                $segment['growth_potential'] == 'High') {
                return $segment;
            }
        }
        
        return $segments[0] ?? null;
    }

    /**
     * Identify underserved segment
     */
    private function identify_underserved_segment($segments)
    {
        // Find segment with low share but high estimated value
        foreach ($segments as $segment) {
            if ($segment['percentage'] < 10 && $segment['avg_proposal_value'] > 500000) {
                return $segment;
            }
        }
        
        return null;
    }

    /**
     * Identify high value segment
     */
    private function identify_high_value_segment($segments)
    {
        // Find segment with highest average proposal value
        $maxValue = 0;
        $highValueSegment = null;
        
        foreach ($segments as $segment) {
            if ($segment['avg_proposal_value'] > $maxValue && $segment['client_count'] > 0) {
                $maxValue = $segment['avg_proposal_value'];
                $highValueSegment = $segment;
            }
        }
        
        return $highValueSegment;
    }

    /**
     * Generate Time Metrics Table
     */
    private function generate_time_metrics_table($companyDetails)
    {
        $table = [
            'proposal_creation_times' => [],
            'approval_times' => [],
            'completion_times' => [],
            'response_times' => [],
            'efficiency_metrics' => []
        ];
        
        $proposalTimes = [];
        $approvalTimes = [];
        $completionTimes = [];
        
        foreach ($companyDetails as $proposal) {
            $proposalDate = $proposal->appointmentdatetime ?? '';
            $initiatedDate = $proposal->initiateddt ?? '';
            $updatedDate = $proposal->updated_at ?? '';
            $approvalDate = $proposal->propasal_apr_date ?? '';
            
            // Proposal creation time analysis
            if (!empty($proposalDate)) {
                $hour = date('H', strtotime($proposalDate));
                $dayOfWeek = date('w', strtotime($proposalDate));
                $proposalTimes[] = [
                    'hour' => $hour,
                    'day_of_week' => $dayOfWeek,
                    'date' => $proposalDate
                ];
            }
            
            // Approval time analysis
            if (!empty($proposalDate) && !empty($approvalDate)) {
                $approvalDays = $this->calculate_days_difference($proposalDate, $approvalDate);
                if ($approvalDays >= 0) {
                    $approvalTimes[] = [
                        'days' => $approvalDays,
                        'proposal_id' => $proposal->proposal_id ?? 0,
                        'company' => $proposal->compname ?? ''
                    ];
                }
            }
            
            // Completion time analysis
            if (!empty($initiatedDate) && !empty($updatedDate)) {
                $completionSeconds = strtotime($updatedDate) - strtotime($initiatedDate);
                $completionHours = round($completionSeconds / 3600, 2);
                
                if ($completionHours >= 0) {
                    $completionTimes[] = [
                        'hours' => $completionHours,
                        'display_time' => $proposal->total_time_taken ?? '',
                        'proposal_id' => $proposal->proposal_id ?? 0
                    ];
                }
            }
        }
        
        // Analyze proposal creation times
        $table['proposal_creation_times'] = $this->analyze_creation_times($proposalTimes);
        
        // Analyze approval times
        $table['approval_times'] = $this->analyze_approval_times($approvalTimes);
        
        // Analyze completion times
        $table['completion_times'] = $this->analyze_completion_times($completionTimes);
        
        // Calculate response times (estimated)
        $table['response_times'] = $this->estimate_response_times($proposalTimes, $approvalTimes);
        
        // Overall efficiency metrics
        $table['efficiency_metrics'] = $this->calculate_efficiency_metrics(
            $proposalTimes,
            $approvalTimes,
            $completionTimes
        );
        
        return $table;
    }

    /**
     * Analyze creation times
     */
    private function analyze_creation_times($proposalTimes)
    {
        $analysis = [
            'total_proposals' => count($proposalTimes),
            'peak_hours' => [],
            'peak_days' => [],
            'avg_proposals_per_day' => 0,
            'time_distribution' => [
                'morning' => 0,
                'afternoon' => 0,
                'evening' => 0,
                'night' => 0
            ]
        ];
        
        if (empty($proposalTimes)) {
            return $analysis;
        }
        
        $hourlyCounts = array_fill(0, 24, 0);
        $dailyCounts = array_fill(0, 7, 0);
        $uniqueDays = [];
        
        foreach ($proposalTimes as $time) {
            $hour = intval($time['hour']);
            $day = intval($time['day_of_week']);
            $date = date('Y-m-d', strtotime($time['date']));
            
            // Hourly counts
            if ($hour >= 0 && $hour <= 23) {
                $hourlyCounts[$hour]++;
            }
            
            // Daily counts
            if ($day >= 0 && $day <= 6) {
                $dailyCounts[$day]++;
            }
            
            // Track unique days
            if (!in_array($date, $uniqueDays)) {
                $uniqueDays[] = $date;
            }
            
            // Time distribution
            if ($hour >= 6 && $hour < 12) {
                $analysis['time_distribution']['morning']++;
            } elseif ($hour >= 12 && $hour < 17) {
                $analysis['time_distribution']['afternoon']++;
            } elseif ($hour >= 17 && $hour < 21) {
                $analysis['time_distribution']['evening']++;
            } else {
                $analysis['time_distribution']['night']++;
            }
        }
        
        // Find peak hours (top 3)
        arsort($hourlyCounts);
        $peakHours = array_slice($hourlyCounts, 0, 3, true);
        
        foreach ($peakHours as $hour => $count) {
            $analysis['peak_hours'][] = [
                'hour' => sprintf('%02d:00', $hour),
                'count' => $count,
                'percentage' => round(($count / count($proposalTimes)) * 100, 2)
            ];
        }
        
        // Find peak days
        $dayLabels = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        foreach ($dailyCounts as $day => $count) {
            if ($count > 0) {
                $analysis['peak_days'][] = [
                    'day' => $dayLabels[$day],
                    'count' => $count,
                    'percentage' => round(($count / count($proposalTimes)) * 100, 2)
                ];
            }
        }
        
        // Sort peak days by count
        usort($analysis['peak_days'], function($a, $b) {
            return $b['count'] - $a['count'];
        });
        
        // Calculate average proposals per day
        $analysis['avg_proposals_per_day'] = count($uniqueDays) > 0 ? 
            round(count($proposalTimes) / count($uniqueDays), 2) : 0;
        
        return $analysis;
    }

    /**
     * Analyze approval times
     */
    private function analyze_approval_times($approvalTimes)
    {
        $analysis = [
            'total_approvals' => count($approvalTimes),
            'avg_approval_time_days' => 0,
            'approval_time_distribution' => [
                'same_day' => 0,
                '1_3_days' => 0,
                '4_7_days' => 0,
                '8_14_days' => 0,
                'over_14_days' => 0
            ],
            'fastest_approvals' => [],
            'slowest_approvals' => []
        ];
        
        if (empty($approvalTimes)) {
            return $analysis;
        }
        
        $totalDays = 0;
        $sortedTimes = $approvalTimes;
        
        // Sort by days ascending for fastest, descending for slowest
        usort($sortedTimes, function($a, $b) {
            return $a['days'] - $b['days'];
        });
        
        foreach ($approvalTimes as $approval) {
            $days = $approval['days'];
            $totalDays += $days;
            
            // Time distribution
            if ($days == 0) {
                $analysis['approval_time_distribution']['same_day']++;
            } elseif ($days <= 3) {
                $analysis['approval_time_distribution']['1_3_days']++;
            } elseif ($days <= 7) {
                $analysis['approval_time_distribution']['4_7_days']++;
            } elseif ($days <= 14) {
                $analysis['approval_time_distribution']['8_14_days']++;
            } else {
                $analysis['approval_time_distribution']['over_14_days']++;
            }
        }
        
        $analysis['avg_approval_time_days'] = round($totalDays / count($approvalTimes), 2);
        
        // Get fastest approvals (top 5)
        $fastest = array_slice($sortedTimes, 0, min(5, count($sortedTimes)));
        foreach ($fastest as $approval) {
            $analysis['fastest_approvals'][] = [
                'company' => $approval['company'],
                'days' => $approval['days'],
                'proposal_id' => $approval['proposal_id']
            ];
        }
        
        // Get slowest approvals (top 5)
        $slowest = array_slice(array_reverse($sortedTimes), 0, min(5, count($sortedTimes)));
        foreach ($slowest as $approval) {
            $analysis['slowest_approvals'][] = [
                'company' => $approval['company'],
                'days' => $approval['days'],
                'proposal_id' => $approval['proposal_id']
            ];
        }
        
        return $analysis;
    }

    /**
     * Analyze completion times
     */
    private function analyze_completion_times($completionTimes)
    {
        $analysis = [
            'total_completions' => count($completionTimes),
            'avg_completion_time_hours' => 0,
            'completion_time_distribution' => [
                'under_1_hour' => 0,
                '1_4_hours' => 0,
                '4_8_hours' => 0,
                '8_24_hours' => 0,
                'over_24_hours' => 0
            ],
            'fastest_completions' => [],
            'slowest_completions' => []
        ];
        
        if (empty($completionTimes)) {
            return $analysis;
        }
        
        $totalHours = 0;
        $sortedTimes = $completionTimes;
        
        // Sort by hours ascending for fastest, descending for slowest
        usort($sortedTimes, function($a, $b) {
            return $a['hours'] - $b['hours'];
        });
        
        foreach ($completionTimes as $completion) {
            $hours = $completion['hours'];
            $totalHours += $hours;
            
            // Time distribution
            if ($hours < 1) {
                $analysis['completion_time_distribution']['under_1_hour']++;
            } elseif ($hours <= 4) {
                $analysis['completion_time_distribution']['1_4_hours']++;
            } elseif ($hours <= 8) {
                $analysis['completion_time_distribution']['4_8_hours']++;
            } elseif ($hours <= 24) {
                $analysis['completion_time_distribution']['8_24_hours']++;
            } else {
                $analysis['completion_time_distribution']['over_24_hours']++;
            }
        }
        
        $analysis['avg_completion_time_hours'] = round($totalHours / count($completionTimes), 2);
        
        // Get fastest completions (top 5)
        $fastest = array_slice($sortedTimes, 0, min(5, count($sortedTimes)));
        foreach ($fastest as $completion) {
            $analysis['fastest_completions'][] = [
                'hours' => $completion['hours'],
                'display_time' => $completion['display_time'],
                'proposal_id' => $completion['proposal_id']
            ];
        }
        
        // Get slowest completions (top 5)
        $slowest = array_slice(array_reverse($sortedTimes), 0, min(5, count($sortedTimes)));
        foreach ($slowest as $completion) {
            $analysis['slowest_completions'][] = [
                'hours' => $completion['hours'],
                'display_time' => $completion['display_time'],
                'proposal_id' => $completion['proposal_id']
            ];
        }
        
        return $analysis;
    }

    /**
     * Estimate response times
     */
    private function estimate_response_times($proposalTimes, $approvalTimes)
    {
        $analysis = [
            'avg_initial_response_time_hours' => 24, // Estimated
            'avg_follow_up_time_days' => 3, // Estimated
            'response_time_distribution' => [
                'immediate' => 0,
                'within_24_hours' => 0,
                'within_48_hours' => 0,
                'within_week' => 0,
                'over_week' => 0
            ],
            'response_efficiency' => 'Moderate' // Would need actual response data
        ];
        
        // This is a simplified estimation
        // In a real system, you would track actual response times
        
        return $analysis;
    }

    /**
     * Calculate efficiency metrics
     */
    private function calculate_efficiency_metrics($proposalTimes, $approvalTimes, $completionTimes)
    {
        $metrics = [
            'proposal_efficiency_score' => 0,
            'approval_efficiency_score' => 0,
            'overall_efficiency_score' => 0,
            'bottlenecks_identified' => [],
            'improvement_opportunities' => []
        ];
        
        // Calculate proposal efficiency based on distribution
        if (!empty($proposalTimes)) {
            $peakHourPercentage = 0;
            if (isset($metrics['proposal_creation_times']['peak_hours'][0]['percentage'])) {
                $peakHourPercentage = $metrics['proposal_creation_times']['peak_hours'][0]['percentage'];
            }
            
            // More concentrated peak hours might indicate efficiency
            $metrics['proposal_efficiency_score'] = min(100, $peakHourPercentage * 2);
        }
        
        // Calculate approval efficiency
        if (!empty($approvalTimes)) {
            $avgApprovalTime = 0;
            if (isset($metrics['approval_times']['avg_approval_time_days'])) {
                $avgApprovalTime = $metrics['approval_times']['avg_approval_time_days'];
            }
            
            // Lower approval time = higher efficiency
            if ($avgApprovalTime <= 1) {
                $metrics['approval_efficiency_score'] = 90;
            } elseif ($avgApprovalTime <= 3) {
                $metrics['approval_efficiency_score'] = 70;
            } elseif ($avgApprovalTime <= 7) {
                $metrics['approval_efficiency_score'] = 50;
            } elseif ($avgApprovalTime <= 14) {
                $metrics['approval_efficiency_score'] = 30;
            } else {
                $metrics['approval_efficiency_score'] = 10;
            }
        }
        
        // Calculate completion efficiency
        if (!empty($completionTimes)) {
            $avgCompletionTime = 0;
            if (isset($metrics['completion_times']['avg_completion_time_hours'])) {
                $avgCompletionTime = $metrics['completion_times']['avg_completion_time_hours'];
            }
            
            // Lower completion time = higher efficiency
            if ($avgCompletionTime <= 2) {
                $completionScore = 80;
            } elseif ($avgCompletionTime <= 8) {
                $completionScore = 60;
            } elseif ($avgCompletionTime <= 24) {
                $completionScore = 40;
            } else {
                $completionScore = 20;
            }
            
            $metrics['proposal_efficiency_score'] = ($metrics['proposal_efficiency_score'] + $completionScore) / 2;
        }
        
        // Overall efficiency score
        $metrics['overall_efficiency_score'] = round(
            ($metrics['proposal_efficiency_score'] + $metrics['approval_efficiency_score']) / 2, 
            2
        );
        
        // Identify bottlenecks
        if ($metrics['approval_efficiency_score'] < 50) {
            $metrics['bottlenecks_identified'][] = 'Approval process too slow';
        }
        
        if ($metrics['proposal_efficiency_score'] < 50) {
            $metrics['bottlenecks_identified'][] = 'Proposal creation needs optimization';
        }
        
        // Improvement opportunities
        if ($metrics['overall_efficiency_score'] < 60) {
            $metrics['improvement_opportunities'][] = 'Implement process automation';
            $metrics['improvement_opportunities'][] = 'Set clear SLAs for each stage';
        }
        
        if ($metrics['overall_efficiency_score'] >= 80) {
            $metrics['improvement_opportunities'][] = 'Maintain current efficient processes';
            $metrics['improvement_opportunities'][] = 'Share best practices across team';
        }
        
        return $metrics;
    }

    /**
     * Generate Priority Clients Table
     */
    private function generate_priority_clients_table($companyDetails)
    {
        $table = [
            'high_potential_clients' => [],
            'key_accounts' => [],
            'repeat_clients' => [],
            'at_risk_clients' => [],
            'new_opportunities' => [],
            'priority_matrix' => []
        ];
        
        $clientData = [];
        $companyProposals = [];
        
        // Group proposals by company
        foreach ($companyDetails as $proposal) {
            $companyId = $proposal->cid ?? 0;
            $companyName = $proposal->compname ?? '';
            $budget = $proposal->propasal_budgetme ?? 0;
            $aprStatus = intval($proposal->apr_status ?? 0);
            $potential = $proposal->potential ?? '';
            $keycompany = $proposal->keycompany ?? '';
            $upsellClient = $proposal->upsell_client ?? '';
            $priorityc = $proposal->priorityc ?? '';
            
            if (!isset($clientData[$companyId])) {
                $clientData[$companyId] = [
                    'company_name' => $companyName,
                    'total_proposals' => 0,
                    'total_budget' => 0,
                    'approved_budget' => 0,
                    'pending_budget' => 0,
                    'rejected_budget' => 0,
                    'last_proposal_date' => '',
                    'first_proposal_date' => '',
                    'priority_flags' => [],
                    'proposals' => []
                ];
            }
            
            $clientData[$companyId]['total_proposals']++;
            $clientData[$companyId]['total_budget'] += $budget;
            
            if ($aprStatus === 1) {
                $clientData[$companyId]['approved_budget'] += $budget;
            } elseif ($aprStatus === 2) {
                $clientData[$companyId]['rejected_budget'] += $budget;
            } else {
                $clientData[$companyId]['pending_budget'] += $budget;
            }
            
            // Update dates
            $proposalDate = $proposal->appointmentdatetime ?? '';
            if (empty($clientData[$companyId]['first_proposal_date']) || 
                strtotime($proposalDate) < strtotime($clientData[$companyId]['first_proposal_date'])) {
                $clientData[$companyId]['first_proposal_date'] = $proposalDate;
            }
            
            if (empty($clientData[$companyId]['last_proposal_date']) || 
                strtotime($proposalDate) > strtotime($clientData[$companyId]['last_proposal_date'])) {
                $clientData[$companyId]['last_proposal_date'] = $proposalDate;
            }
            
            // Add priority flags
            if ($potential === 'yes' && !in_array('High Potential', $clientData[$companyId]['priority_flags'])) {
                $clientData[$companyId]['priority_flags'][] = 'High Potential';
            }
            
            if ($keycompany === 'yes' && !in_array('Key Account', $clientData[$companyId]['priority_flags'])) {
                $clientData[$companyId]['priority_flags'][] = 'Key Account';
            }
            
            if ($upsellClient === 'yes' && !in_array('Upsell Opportunity', $clientData[$companyId]['priority_flags'])) {
                $clientData[$companyId]['priority_flags'][] = 'Upsell Opportunity';
            }
            
            if ($priorityc === 'yes' && !in_array('Priority Client', $clientData[$companyId]['priority_flags'])) {
                $clientData[$companyId]['priority_flags'][] = 'Priority Client';
            }
            
            // Track proposals
            $clientData[$companyId]['proposals'][] = [
                'date' => $proposalDate,
                'budget' => $budget,
                'status' => $aprStatus,
                'product' => $proposal->propasal_types ?? ''
            ];
            
            // Track for company proposals count
            if (!isset($companyProposals[$companyId])) {
                $companyProposals[$companyId] = 0;
            }
            $companyProposals[$companyId]++;
        }
        
        // Categorize clients
        foreach ($clientData as $companyId => $client) {
            $daysSinceLast = $this->calculate_days_since_last_proposal($client['last_proposal_date']);
            $approvalRate = $client['total_budget'] > 0 ? 
                round(($client['approved_budget'] / $client['total_budget']) * 100, 2) : 0;
            
            $clientScore = $this->calculate_client_priority_score(
                $client['total_proposals'],
                $client['total_budget'],
                $approvalRate,
                $daysSinceLast,
                count($client['priority_flags'])
            );
            
            $clientEntry = [
                'company_name' => $client['company_name'],
                'total_proposals' => $client['total_proposals'],
                'total_budget' => $client['total_budget'],
                'approved_budget' => $client['approved_budget'],
                'approval_rate' => $approvalRate,
                'last_proposal_date' => $client['last_proposal_date'],
                'days_since_last_proposal' => $daysSinceLast,
                'priority_flags' => $client['priority_flags'],
                'client_score' => $clientScore,
                'priority_level' => $this->get_client_priority_level($clientScore)
            ];
            
            // Categorize based on criteria
            
            // High Potential Clients
            if (in_array('High Potential', $client['priority_flags']) && 
                $client['total_budget'] > 500000) {
                $table['high_potential_clients'][] = $clientEntry;
            }
            
            // Key Accounts
            if (in_array('Key Account', $client['priority_flags']) || 
                $client['total_proposals'] >= 3) {
                $table['key_accounts'][] = $clientEntry;
            }
            
            // Repeat Clients
            if ($client['total_proposals'] >= 2 && $daysSinceLast <= 90) {
                $table['repeat_clients'][] = $clientEntry;
            }
            
            // At Risk Clients
            if ($daysSinceLast > 180 || ($approvalRate < 30 && $client['total_proposals'] > 0)) {
                $table['at_risk_clients'][] = $clientEntry;
            }
            
            // New Opportunities (single proposal, recent, high budget)
            if ($client['total_proposals'] == 1 && 
                $daysSinceLast <= 30 && 
                $client['total_budget'] > 200000) {
                $table['new_opportunities'][] = $clientEntry;
            }
            
            // Add to priority matrix
            $table['priority_matrix'][] = $clientEntry;
        }
        
        // Sort priority matrix by client score
        usort($table['priority_matrix'], function($a, $b) {
            return $b['client_score'] - $a['client_score'];
        });
        
        // Sort each category by appropriate metric
        $this->sort_client_categories($table);
        
        return $table;
    }

    /**
     * Calculate client priority score
     */
    private function calculate_client_priority_score($proposalCount, $totalBudget, $approvalRate, $daysSinceLast, $priorityFlagsCount)
    {
        $score = 0;
        
        // Proposal count (max 25 points)
        if ($proposalCount >= 5) $score += 25;
        elseif ($proposalCount >= 3) $score += 20;
        elseif ($proposalCount >= 2) $score += 15;
        elseif ($proposalCount > 0) $score += 10;
        
        // Budget size (max 30 points)
        if ($totalBudget >= 1000000) $score += 30;
        elseif ($totalBudget >= 500000) $score += 25;
        elseif ($totalBudget >= 100000) $score += 20;
        elseif ($totalBudget >= 50000) $score += 15;
        elseif ($totalBudget > 0) $score += 10;
        
        // Approval rate (max 20 points)
        if ($approvalRate >= 80) $score += 20;
        elseif ($approvalRate >= 60) $score += 15;
        elseif ($approvalRate >= 40) $score += 10;
        elseif ($approvalRate >= 20) $score += 5;
        
        // Recency (max 15 points)
        if ($daysSinceLast <= 7) $score += 15;
        elseif ($daysSinceLast <= 30) $score += 12;
        elseif ($daysSinceLast <= 90) $score += 8;
        elseif ($daysSinceLast <= 180) $score += 4;
        
        // Priority flags (max 10 points)
        $score += min($priorityFlagsCount * 3, 10);
        
        return $score;
    }

    /**
     * Get client priority level
     */
    private function get_client_priority_level($score)
    {
        if ($score >= 80) return 'Tier 1 - Critical';
        if ($score >= 60) return 'Tier 2 - High';
        if ($score >= 40) return 'Tier 3 - Medium';
        if ($score >= 20) return 'Tier 4 - Low';
        return 'Tier 5 - Monitor';
    }

    /**
     * Sort client categories
     */
    private function sort_client_categories(&$table)
    {
        // Sort high potential clients by budget
        usort($table['high_potential_clients'], function($a, $b) {
            return $b['total_budget'] - $a['total_budget'];
        });
        
        // Sort key accounts by proposal count
        usort($table['key_accounts'], function($a, $b) {
            return $b['total_proposals'] - $a['total_proposals'];
        });
        
        // Sort repeat clients by days since last (most recent first)
        usort($table['repeat_clients'], function($a, $b) {
            return $a['days_since_last_proposal'] - $b['days_since_last_proposal'];
        });
        
        // Sort at risk clients by risk level (highest days first)
        usort($table['at_risk_clients'], function($a, $b) {
            return $b['days_since_last_proposal'] - $a['days_since_last_proposal'];
        });
        
        // Sort new opportunities by budget
        usort($table['new_opportunities'], function($a, $b) {
            return $b['total_budget'] - $a['total_budget'];
        });
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
        
        // Add proposal count and budget
        $justification .= ". Has {$company['proposal_count']} proposal(s) with total budget of ₹" . 
            number_format($company['total_budget']) . " (₹" . 
            number_format($company['approved_budget']) . " approved).";
        
        return $justification;
    }

    /**
     * Generate response based on message
     */
    private function generate_response_based_on_message($message, $analysisResults, $topCount, $startDate, $endDate)
    {
        $lowerMessage = strtolower(trim($message));
        
        $response = "📊 **PROPOSAL ANALYSIS REPORT**\n\n";
        $response .= "**Period:** {$startDate} to {$endDate}\n";
        $response .= "**Total Proposals Analyzed:** {$analysisResults['total_proposals']}\n";
        $response .= "**Total Users:** {$analysisResults['total_users']}\n";
        $response .= "**Total Companies:** {$analysisResults['total_companies']}\n\n";
        
        // Add summary based on message intent
        if (strpos($lowerMessage, 'top compan') !== false) {
            $response .= $this->generate_top_companies_summary($analysisResults, $topCount);
        }
        
        if (strpos($lowerMessage, 'top user') !== false) {
            $response .= $this->generate_top_users_summary($analysisResults, $topCount);
        }
        
        if (strpos($lowerMessage, 'performance') !== false || 
            strpos($lowerMessage, 'metric') !== false) {
            $response .= $this->generate_performance_summary($analysisResults);
        }
        
        if (strpos($lowerMessage, 'financial') !== false || 
            strpos($lowerMessage, 'budget') !== false) {
            $response .= $this->generate_financial_summary($analysisResults);
        }
        
        if (strpos($lowerMessage, 'product') !== false || 
            strpos($lowerMessage, 'service') !== false) {
            $response .= $this->generate_product_summary($analysisResults);
        }
        
        if (strpos($lowerMessage, 'approval') !== false) {
            $response .= $this->generate_approval_summary($analysisResults);
        }
        
        if (strpos($lowerMessage, 'recommend') !== false || 
            strpos($lowerMessage, 'suggestion') !== false) {
            $response .= $this->generate_recommendations_summary($analysisResults);
        }
        
        if (strpos($lowerMessage, 'table') !== false || 
            strpos($lowerMessage, 'matrix') !== false) {
            $response .= $this->generate_table_data_summary($analysisResults);
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
            $response .= "**#{$company['rank']}. {$company['company_name']} - CID: {$company['company_id']}**\n";
            $response .= "   📊 Score: {$company['score']}\n";
            $response .= "   📄 Proposals: {$company['proposal_count']}\n";
            $response .= "   💰 Total Budget: ₹" . number_format($company['total_budget']) . "\n";
            $response .= "   ✅ Approved Budget: ₹" . number_format($company['approved_budget']) . "\n";
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
            $response .= "   📊 Performance Score: {$user['score']}\n";
            $response .= "   📄 Total Proposals: {$user['total_proposals']}\n";
            $response .= "   ✅ Approval Rate: {$user['approval_rate']}%\n";
            $response .= "   💰 Total Budget: ₹" . number_format($user['total_budget']) . "\n";
            $response .= "   📈 Completion Rate: {$user['completion_rate']}%\n";
            
            if (!empty($user['reasons'])) {
                $topReasons = array_slice($user['reasons'], 0, 2);
                $response .= "   🎯 Key Strengths: " . implode(', ', $topReasons) . "\n";
            }
            $response .= "\n";
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
        $response .= "**Overall Performance:**\n";
        $response .= "   - Total Proposals: {$metrics['total_proposals']}\n";
        $response .= "   - Completion Rate: {$metrics['completion_rate']}%\n";
        $response .= "   - Approval Rate: {$metrics['approval_rate']}%\n";
        $response .= "   - Rejection Rate: {$metrics['rejection_rate']}%\n\n";
        
        $response .= "**Proposal Distribution:**\n";
        $response .= "   - Proposals per User: {$metrics['proposals_per_user']}\n";
        $response .= "   - Proposals per Company: {$metrics['proposals_per_company']}\n";
        $response .= "   - Avg Budget per Proposal: ₹" . number_format($metrics['avg_budget_per_proposal']) . "\n\n";
        
        $response .= "**Status Distribution:**\n";
        foreach ($metrics['status_distribution'] as $status => $count) {
            if ($count > 0) {
                $percentage = $metrics['total_proposals'] > 0 ? 
                    round(($count / $metrics['total_proposals']) * 100, 2) : 0;
                $response .= "   - " . ucfirst(str_replace('_', ' ', $status)) . ": {$count} ({$percentage}%)\n";
            }
        }
        $response .= "\n";
        
        $response .= "**Client Segment Distribution:**\n";
        foreach ($metrics['client_segment_distribution'] as $segment => $count) {
            if ($count > 0) {
                $response .= "   - " . ucfirst($segment) . ": {$count}\n";
            }
        }
        $response .= "\n";
        
        return $response;
    }

    /**
     * Generate financial summary
     */
    private function generate_financial_summary($analysisResults)
    {
        $financials = $analysisResults['financial_analysis'];
        
        $response = "💰 **FINANCIAL ANALYSIS**\n\n";
        $response .= "**Budget Overview:**\n";
        $response .= "   - Total Budget: ₹" . number_format($financials['total_budget']) . "\n";
        $response .= "   - Approved Budget: ₹" . number_format($financials['approved_budget']) . "\n";
        $response .= "   - Pending Budget: ₹" . number_format($financials['pending_budget']) . "\n";
        $response .= "   - Rejected Budget: ₹" . number_format($financials['rejected_budget']) . "\n";
        $response .= "   - Avg Budget per Proposal: ₹" . number_format($financials['avg_budget_per_proposal']) . "\n\n";
        
        $response .= "**Budget Distribution by Size:**\n";
        foreach ($financials['budget_distribution'] as $range => $count) {
            if ($count > 0) {
                $rangeLabel = str_replace('_', ' ', ucfirst($range));
                $response .= "   - {$rangeLabel}: {$count}\n";
            }
        }
        $response .= "\n";
        
        if (!empty($financials['highest_budget_proposal'])) {
            $high = $financials['highest_budget_proposal'];
            $response .= "**Highest Budget Proposal:**\n";
            $response .= "   - Company: {$high['company']}\n";
            $response .= "   - Budget: ₹" . number_format($high['budget']) . "\n";
            $response .= "   - Product: {$high['product']}\n";
            $response .= "   - User: {$high['user']}\n\n";
        }
        
        if (!empty($financials['lowest_budget_proposal'])) {
            $low = $financials['lowest_budget_proposal'];
            $response .= "**Lowest Budget Proposal:**\n";
            $response .= "   - Company: {$low['company']}\n";
            $response .= "   - Budget: ₹" . number_format($low['budget']) . "\n";
            $response .= "   - Product: {$low['product']}\n";
            $response .= "   - User: {$low['user']}\n\n";
        }
        
        if (!empty($financials['avg_budget_per_user'])) {
            $response .= "**Top 3 Users by Average Budget:**\n";
            $topUsers = array_slice($financials['avg_budget_per_user'], 0, 3);
            foreach ($topUsers as $user) {
                $response .= "   - {$user['username']}: ₹" . number_format($user['avg_budget']) . "\n";
            }
            $response .= "\n";
        }
        
        return $response;
    }

    /**
     * Generate product summary
     */
    private function generate_product_summary($analysisResults)
    {
        $products = $analysisResults['product_analysis'];
        
        $response = "📦 **PRODUCT ANALYSIS**\n\n";
        $response .= "**Proposals by Product Type:**\n";
        foreach ($products['total_by_type'] as $type => $count) {
            if ($count > 0) {
                $approved = $products['approved_by_type'][$type] ?? 0;
                $rejected = $products['rejected_by_type'][$type] ?? 0;
                $approvalRate = $products['approval_rate_by_type'][$type] ?? 0;
                
                $response .= "   - **{$type}:** {$count} proposals\n";
                $response .= "     ✓ Approved: {$approved}\n";
                $response .= "     ✗ Rejected: {$rejected}\n";
                $response .= "     📈 Approval Rate: {$approvalRate}%\n";
            }
        }
        $response .= "\n";
        
        if (!empty($products['top_products'])) {
            $response .= "**Top Performing Products (by approval rate):**\n";
            foreach ($products['top_products'] as $product => $rate) {
                $response .= "   - {$product}: {$rate}% approval rate\n";
            }
            $response .= "\n";
        }
        
        if (!empty($products['weak_products'])) {
            $response .= "**Products Needing Improvement:**\n";
            foreach ($products['weak_products'] as $product => $rate) {
                if ($rate < 50) {
                    $response .= "   - {$product}: {$rate}% approval rate\n";
                }
            }
            $response .= "\n";
        }
        
        return $response;
    }

    /**
     * Generate approval summary
     */
    private function generate_approval_summary($analysisResults)
    {
        $approvals = $analysisResults['approval_analysis'];
        
        $response = "✅ **APPROVAL PROCESS ANALYSIS**\n\n";
        $response .= "**Approval Statistics:**\n";
        $response .= "   - Total Approved: {$approvals['total_approved']}\n";
        $response .= "   - Total Rejected: {$approvals['total_rejected']}\n";
        $response .= "   - Total Pending: {$approvals['total_pending']}\n";
        $response .= "   - Approval Rate: {$approvals['approval_rate']}%\n";
        $response .= "   - Rejection Rate: {$approvals['rejection_rate']}%\n";
        $response .= "   - Avg Approval Time: {$approvals['avg_approval_time']}\n\n";
        
        if (!empty($approvals['checker_distribution'])) {
            $response .= "**Approvals by Checker:**\n";
            foreach ($approvals['checker_distribution'] as $checker => $count) {
                $response .= "   - {$checker}: {$count} approvals\n";
            }
            $response .= "\n";
        }
        
        if (!empty($approvals['rejection_reasons'])) {
            $response .= "**Top Rejection Reasons:**\n";
            $topReasons = array_slice($approvals['rejection_reasons'], 0, 3, true);
            foreach ($topReasons as $reason => $count) {
                $response .= "   - " . substr($reason, 0, 50) . (strlen($reason) > 50 ? '...' : '') . ": {$count}\n";
            }
            $response .= "\n";
        }
        
        if (!empty($approvals['fastest_approval'])) {
            $fast = $approvals['fastest_approval'];
            $response .= "**Fastest Approval:**\n";
            $response .= "   - Company: {$fast['company']}\n";
            $response .= "   - Duration: {$fast['duration']}\n";
            $response .= "   - Checker: {$fast['checker']}\n\n";
        }
        
        if (!empty($approvals['slowest_approval'])) {
            $slow = $approvals['slowest_approval'];
            $response .= "**Slowest Approval:**\n";
            $response .= "   - Company: {$slow['company']}\n";
            $response .= "   - Duration: {$slow['duration']}\n";
            $response .= "   - Checker: {$slow['checker']}\n\n";
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
                    $priorityIcon = $rec['priority'] === 'High' ? '🔴' : ($rec['priority'] === 'Medium' ? '🟡' : '🟢');
                    $response .= "{$priorityIcon} {$rec['message']}\n";
                }
                $response .= "\n";
            }
        }
        
        if (!empty($recommendations['sales_strategy'])) {
            $response .= "**Sales Strategy Recommendations:**\n";
            foreach ($recommendations['sales_strategy'] as $strategy) {
                $response .= "**🎯 {$strategy['strategy']}**\n";
                $response .= "   - Details: {$strategy['details']}\n";
                $response .= "   - Recommended Action: {$strategy['action']}\n\n";
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
     * Generate table data summary
     */
    private function generate_table_data_summary($analysisResults)
    {
        $tableData = $analysisResults['table_data_analysis'];
        
        $response = "📋 **TABLE DATA ANALYSIS SUMMARY**\n\n";
        
        // User Performance Matrix
        if (!empty($tableData['user_performance_matrix'])) {
            $response .= "**👥 USER PERFORMANCE MATRIX**\n";
            $response .= "Total Users Analyzed: " . count($tableData['user_performance_matrix']) . "\n";
            
            $topPerformers = array_slice($tableData['user_performance_matrix'], 0, 3);
            $response .= "Top 3 Performers:\n";
            foreach ($topPerformers as $user) {
                $response .= "   - {$user['username']}: Score {$user['performance_score']} ({$user['performance_rating']})\n";
            }
            $response .= "\n";
        }
        
        // Company Engagement Matrix
        if (!empty($tableData['company_engagement_matrix'])) {
            $response .= "**🏢 COMPANY ENGAGEMENT MATRIX**\n";
            $response .= "Total Companies Analyzed: " . count($tableData['company_engagement_matrix']) . "\n";
            
            $topCompanies = array_slice($tableData['company_engagement_matrix'], 0, 3);
            $response .= "Top 3 Engaged Companies:\n";
            foreach ($topCompanies as $company) {
                $response .= "   - {$company['company_name']}: Score {$company['engagement_score']} ({$company['engagement_level']})\n";
            }
            $response .= "\n";
        }
        
        // Product Performance Matrix
        if (!empty($tableData['product_performance_matrix'])) {
            $response .= "**📦 PRODUCT PERFORMANCE MATRIX**\n";
            $response .= "Total Products Analyzed: " . count($tableData['product_performance_matrix']) . "\n";
            
            $topProducts = array_slice($tableData['product_performance_matrix'], 0, 3);
            $response .= "Top 3 Performing Products:\n";
            foreach ($topProducts as $product) {
                $response .= "   - {$product['product_name']}: {$product['total_proposals']} proposals, {$product['approval_rate']}% approval\n";
            }
            $response .= "\n";
        }
        
        // Priority Clients
        if (!empty($tableData['priority_clients_table']['priority_matrix'])) {
            $response .= "**🎯 PRIORITY CLIENTS ANALYSIS**\n";
            
            $highPotentialCount = count($tableData['priority_clients_table']['high_potential_clients'] ?? []);
            $keyAccountsCount = count($tableData['priority_clients_table']['key_accounts'] ?? []);
            $atRiskCount = count($tableData['priority_clients_table']['at_risk_clients'] ?? []);
            
            $response .= "   - High Potential Clients: {$highPotentialCount}\n";
            $response .= "   - Key Accounts: {$keyAccountsCount}\n";
            $response .= "   - At Risk Clients: {$atRiskCount}\n";
            $response .= "\n";
        }
        
        // Financial Summary
        if (!empty($tableData['financial_summary_table']['overview'])) {
            $financials = $tableData['financial_summary_table']['overview'];
            $response .= "**💰 FINANCIAL SUMMARY**\n";
            $response .= "   - Total Budget: ₹" . number_format($financials['total_budget']) . "\n";
            $response .= "   - Approval Rate: {$financials['approval_rate']}%\n";
            $response .= "   - Average Proposal Value: ₹" . number_format($financials['avg_proposal_value']) . "\n\n";
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
        $response .= $this->generate_financial_summary($analysisResults);
        $response .= $this->generate_product_summary($analysisResults);
        $response .= $this->generate_approval_summary($analysisResults);
        $response .= $this->generate_top_companies_summary($analysisResults, $topCount);
        $response .= $this->generate_top_users_summary($analysisResults, $topCount);
        $response .= $this->generate_table_data_summary($analysisResults);
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
                'key_clients_count' => 'Key Clients',
                'repeat_clients_count' => 'Repeat Clients',
                'rejected_proposals_count' => 'Rejected Proposals',
                'delayed_approvals_count' => 'Delayed Approvals'
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
    private function prepare_detailed_frontend_data($proposalData, $topCount, $startDate, $endDate)
    {
        $response = [
            'status' => 'success',
            'message' => 'Proposal analysis data prepared successfully',
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
        
        $userSummary = $proposalData['totalProposalCountByUser'] ?? [];
        $companyDetails = $proposalData['totalProposalCompanyByUser'] ?? [];
        
        if (empty($userSummary) && empty($companyDetails)) {
            $response['status'] = 'empty';
            $response['message'] = 'No proposal data available for analysis';
            return $response;
        }
        
        // Analyze data
        $analysis = $this->analyze_proposal_data_deep($proposalData, $topCount);
        
        // Prepare summary
        $response['summary'] = [
            'total_proposals' => $analysis['total_proposals'],
            'total_users' => $analysis['total_users'],
            'total_companies' => $analysis['total_companies'],
            'approval_rate' => $analysis['performance_metrics']['approval_rate'] ?? 0,
            'rejection_rate' => $analysis['performance_metrics']['rejection_rate'] ?? 0,
            'total_budget' => $analysis['financial_analysis']['total_budget'] ?? 0,
            'approved_budget' => $analysis['financial_analysis']['approved_budget'] ?? 0,
            'top_products_count' => count($analysis['product_analysis']['top_products'] ?? []),
            'priority_alerts_count' => $this->count_priority_alerts($analysis['priority_flags']),
            'table_insights_count' => $this->count_table_insights($analysis['table_data_analysis'])
        ];
        
        // Prepare detailed analysis
        $response['detailed_analysis'] = [
            'performance_metrics' => $analysis['performance_metrics'],
            'financial_analysis' => $analysis['financial_analysis'],
            'product_analysis' => $analysis['product_analysis'],
            'approval_analysis' => $analysis['approval_analysis'],
            'priority_flags' => $this->simplify_priority_flags($analysis['priority_flags']),
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
        $response['tables'] = $this->prepare_tables_data($analysis);
        
        return $response;
    }

    /**
     * Count priority alerts
     */
    private function count_priority_alerts($flags)
    {
        $count = 0;
        foreach ($flags as $key => $value) {
            if (strpos($key, '_count') !== false && $value > 0) {
                $count += $value;
            }
        }
        return $count;
    }

    /**
     * Count table insights
     */
    private function count_table_insights($tableData)
    {
        $count = 0;
        
        if (!empty($tableData['proposal_trends'])) $count++;
        if (!empty($tableData['user_performance_matrix'])) $count++;
        if (!empty($tableData['company_engagement_matrix'])) $count++;
        if (!empty($tableData['product_performance_matrix'])) $count++;
        if (!empty($tableData['approval_process_matrix'])) $count++;
        if (!empty($tableData['financial_summary_table'])) $count++;
        if (!empty($tableData['status_distribution_table'])) $count++;
        if (!empty($tableData['client_segment_table'])) $count++;
        if (!empty($tableData['time_metrics_table'])) $count++;
        if (!empty($tableData['priority_clients_table'])) $count++;
        
        return $count;
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
            'upsell_clients_count' => 'Upsell Clients',
            'key_clients_count' => 'Key Clients',
            'priority_clients_count' => 'Priority Clients',
            'repeat_clients_count' => 'Repeat Clients',
            'large_budget_proposals_count' => 'Large Budget Proposals',
            'fast_turnaround_proposals_count' => 'Fast Turnaround Proposals',
            'delayed_approvals_count' => 'Delayed Approvals',
            'rejected_proposals_count' => 'Rejected Proposals'
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
            'rejected_proposals_count',
            'delayed_approvals_count'
        ];
        
        $mediumPriorityFlags = [
            'high_potential_clients_count',
            'key_clients_count',
            'priority_clients_count'
        ];
        
        if (in_array($flagKey, $highPriorityFlags)) {
            return $count > 3 ? 'High' : ($count > 1 ? 'Medium' : 'Low');
        } elseif (in_array($flagKey, $mediumPriorityFlags)) {
            return $count > 5 ? 'High' : ($count > 2 ? 'Medium' : 'Low');
        } else {
            return 'Info'; // Positive flags
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
            'labels' => ['Completion Rate', 'Approval Rate', 'Rejection Rate'],
            'datasets' => [[
                'label' => 'Percentage (%)',
                'data' => [
                    $metrics['completion_rate'] ?? 0,
                    $metrics['approval_rate'] ?? 0,
                    $metrics['rejection_rate'] ?? 0
                ],
                'backgroundColor' => ['#4CAF50', '#2196F3', '#FF9800']
            ]]
        ];
        
        // Product distribution chart
        $products = $analysis['product_analysis']['total_by_type'] ?? [];
        if (!empty($products)) {
            $filteredProducts = array_filter($products, function($count) {
                return $count > 0;
            });
            
            if (!empty($filteredProducts)) {
                $charts['product_distribution'] = [
                    'type' => 'pie',
                    'title' => 'Proposals by Product Type',
                    'labels' => array_keys($filteredProducts),
                    'datasets' => [[
                        'data' => array_values($filteredProducts),
                        'backgroundColor' => $this->generate_chart_colors(count($filteredProducts))
                    ]]
                ];
            }
        }
        
        // Budget distribution chart
        $financials = $analysis['financial_analysis']['budget_distribution'] ?? [];
        if (!empty($financials)) {
            $budgetLabels = [
                'under_10k' => 'Under ₹10K',
                '10k_50k' => '₹10K-50K',
                '50k_100k' => '₹50K-100K',
                '100k_500k' => '₹100K-500K',
                '500k_1m' => '₹500K-1M',
                'over_1m' => 'Over ₹1M'
            ];
            
            $budgetData = [];
            $budgetLabelsFormatted = [];
            
            foreach ($budgetLabels as $key => $label) {
                if (isset($financials[$key]) && $financials[$key] > 0) {
                    $budgetData[] = $financials[$key];
                    $budgetLabelsFormatted[] = $label;
                }
            }
            
            if (!empty($budgetData)) {
                $charts['budget_distribution'] = [
                    'type' => 'doughnut',
                    'title' => 'Proposals by Budget Range',
                    'labels' => $budgetLabelsFormatted,
                    'datasets' => [[
                        'data' => $budgetData,
                        'backgroundColor' => $this->generate_chart_colors(count($budgetData))
                    ]]
                ];
            }
        }
        
        // Top companies score chart
        $topCompanies = array_slice($analysis['top_companies'] ?? [], 0, 10);
        if (!empty($topCompanies)) {
            $companyNames = [];
            $companyScores = [];
            
            foreach ($topCompanies as $company) {
                $companyNames[] = substr($company['company_name'], 0, 15) . '...';
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
        
        // Approval timeline chart
        $approvals = $analysis['approval_analysis']['approval_timeline'] ?? [];
        if (!empty($approvals)) {
            $timelineDates = [];
            $timelineDurations = [];
            
            foreach ($approvals as $approval) {
                $timelineDates[] = $approval['date'];
                $timelineDurations[] = $approval['duration_days'];
            }
            
            $charts['approval_timeline'] = [
                'type' => 'line',
                'title' => 'Approval Time Trend',
                'labels' => $timelineDates,
                'datasets' => [[
                    'label' => 'Approval Time (Days)',
                    'data' => $timelineDurations,
                    'borderColor' => '#2196F3',
                    'fill' => false
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
    private function prepare_tables_data($analysis)
    {
        $tables = [];
        
        // User Performance Table
        if (!empty($analysis['table_data_analysis']['user_performance_matrix'])) {
            $tables['user_performance'] = [
                'title' => 'User Performance Matrix',
                'headers' => ['Rank', 'Username', 'Total Proposals', 'Approval Rate', 'Total Budget', 'Performance Score', 'Rating'],
                'rows' => [],
                'key' => 'user_performance'
            ];
            
            $rank = 1;
            foreach ($analysis['table_data_analysis']['user_performance_matrix'] as $user) {
                $tables['user_performance']['rows'][] = [
                    'rank' => $rank,
                    'username' => $user['username'],
                    'total_proposals' => $user['total_proposals'],
                    'approval_rate' => $user['approval_rate'] . '%',
                    'total_budget' => '₹' . number_format($user['total_budget']),
                    'performance_score' => $user['performance_score'],
                    'performance_rating' => $user['performance_rating']
                ];
                $rank++;
            }
        }
        
        // Top Companies Table
        if (!empty($analysis['top_companies'])) {
            $tables['top_companies'] = [
                'title' => "Top " . count($analysis['top_companies']) . " Companies",
                'headers' => ['Rank', 'Company Name', 'Proposal Count', 'Total Budget', 'Approved Budget', 'Engagement Score'],
                'rows' => [],
                'key' => 'top_companies'
            ];
            
            foreach ($analysis['top_companies'] as $company) {
                $tables['top_companies']['rows'][] = [
                    'rank' => $company['rank'],
                    'company_name' => $company['company_name'],
                    'proposal_count' => $company['proposal_count'],
                    'total_budget' => '₹' . number_format($company['total_budget']),
                    'approved_budget' => '₹' . number_format($company['approved_budget']),
                    'engagement_score' => $company['score']
                ];
            }
        }
        
        // Top Users Table
        if (!empty($analysis['top_users'])) {
            $tables['top_users'] = [
                'title' => "Top " . count($analysis['top_users']) . " Performers",
                'headers' => ['Rank', 'Username', 'Total Proposals', 'Approval Rate', 'Total Budget', 'Performance Score'],
                'rows' => [],
                'key' => 'top_users'
            ];
            
            foreach ($analysis['top_users'] as $user) {
                $tables['top_users']['rows'][] = [
                    'rank' => $user['rank'],
                    'username' => $user['username'],
                    'total_proposals' => $user['total_proposals'],
                    'approval_rate' => $user['approval_rate'] . '%',
                    'total_budget' => '₹' . number_format($user['total_budget']),
                    'performance_score' => $user['score']
                ];
            }
        }
        
        // Product Performance Table
        if (!empty($analysis['table_data_analysis']['product_performance_matrix'])) {
            $tables['product_performance'] = [
                'title' => 'Product Performance',
                'headers' => ['Product', 'Total Proposals', 'Approved', 'Rejected', 'Approval Rate', 'Avg Budget', 'Performance'],
                'rows' => [],
                'key' => 'product_performance'
            ];
            
            foreach ($analysis['table_data_analysis']['product_performance_matrix'] as $product) {
                $tables['product_performance']['rows'][] = [
                    'product_name' => $product['product_name'],
                    'total_proposals' => $product['total_proposals'],
                    'approved' => $product['approved_proposals'],
                    'rejected' => $product['rejected_proposals'],
                    'approval_rate' => $product['approval_rate'] . '%',
                    'avg_budget' => '₹' . number_format($product['avg_budget_per_proposal']),
                    'performance_category' => $product['performance_category']
                ];
            }
        }
        
        // Priority Clients Table
        if (!empty($analysis['table_data_analysis']['priority_clients_table']['priority_matrix'])) {
            $priorityClients = $analysis['table_data_analysis']['priority_clients_table']['priority_matrix'];
            $tables['priority_clients'] = [
                'title' => 'Priority Clients Analysis',
                'headers' => ['Priority Level', 'Company Name', 'Proposals', 'Total Budget', 'Last Proposal', 'Days Since Last'],
                'rows' => [],
                'key' => 'priority_clients'
            ];
            
            foreach ($priorityClients as $client) {
                $tables['priority_clients']['rows'][] = [
                    'priority_level' => $client['priority_level'],
                    'company_name' => $client['company_name'],
                    'proposals' => $client['total_proposals'],
                    'total_budget' => '₹' . number_format($client['total_budget']),
                    'last_proposal_date' => $client['last_proposal_date'],
                    'days_since_last' => $client['days_since_last_proposal']
                ];
            }
        }
        
        // Financial Summary Table
        if (!empty($analysis['table_data_analysis']['financial_summary_table']['overview'])) {
            $financials = $analysis['table_data_analysis']['financial_summary_table']['overview'];
            $tables['financial_summary'] = [
                'title' => 'Financial Overview',
                'headers' => ['Metric', 'Amount'],
                'rows' => [],
                'key' => 'financial_summary'
            ];
            
            $metrics = [
                ['label' => 'Total Budget', 'value' => '₹' . number_format($financials['total_budget'])],
                ['label' => 'Approved Budget', 'value' => '₹' . number_format($financials['approved_budget'])],
                ['label' => 'Pending Budget', 'value' => '₹' . number_format($financials['pending_budget'])],
                ['label' => 'Rejected Budget', 'value' => '₹' . number_format($financials['rejected_budget'])],
                ['label' => 'Approval Rate', 'value' => $financials['approval_rate'] . '%'],
                ['label' => 'Average Proposal Value', 'value' => '₹' . number_format($financials['avg_proposal_value'])],
            ];
            
            foreach ($metrics as $metric) {
                $tables['financial_summary']['rows'][] = $metric;
            }
        }
        
        // Company Engagement Table
        if (!empty($analysis['table_data_analysis']['company_engagement_matrix'])) {
            $companies = $analysis['table_data_analysis']['company_engagement_matrix'];
            $tables['company_engagement'] = [
                'title' => 'Company Engagement Matrix',
                'headers' => ['Company', 'Engagement Level', 'Proposals', 'Total Budget', 'Approval Rate', 'Last Activity', 'Recommended Action'],
                'rows' => [],
                'key' => 'company_engagement'
            ];
            
            foreach ($companies as $company) {
                $tables['company_engagement']['rows'][] = [
                    'company_name' => $company['company_name'],
                    'engagement_level' => $company['engagement_level'],
                    'proposals' => $company['total_proposals'],
                    'total_budget' => '₹' . number_format($company['total_budget']),
                    'approval_rate' => $company['approval_rate'] . '%',
                    'last_proposal_date' => $company['last_proposal_date'],
                    'next_action_recommended' => $company['next_action_recommended']
                ];
            }
        }
        
        // Status Distribution Table
        if (!empty($analysis['table_data_analysis']['status_distribution_table']['overall_distribution'])) {
            $statuses = $analysis['table_data_analysis']['status_distribution_table']['overall_distribution'];
            $tables['status_distribution'] = [
                'title' => 'Status Distribution',
                'headers' => ['Status Category', 'Count', 'Percentage'],
                'rows' => [],
                'key' => 'status_distribution'
            ];
            
            foreach ($statuses as $status) {
                $tables['status_distribution']['rows'][] = [
                    'category' => $status['category'],
                    'count' => $status['count'],
                    'percentage' => $status['percentage'] . '%'
                ];
            }
        }
        
        // Client Segment Table
        if (!empty($analysis['table_data_analysis']['client_segment_table']['segment_distribution'])) {
            $segments = $analysis['table_data_analysis']['client_segment_table']['segment_distribution'];
            $tables['client_segments'] = [
                'title' => 'Client Segment Analysis',
                'headers' => ['Segment', 'Client Count', 'Market Share', 'Avg Proposal Value', 'Growth Potential'],
                'rows' => [],
                'key' => 'client_segments'
            ];
            
            foreach ($segments as $segment) {
                $tables['client_segments']['rows'][] = [
                    'segment' => $segment['segment'],
                    'client_count' => $segment['client_count'],
                    'market_share' => $segment['market_share'] . '%',
                    'avg_proposal_value' => '₹' . number_format($segment['avg_proposal_value']),
                    'growth_potential' => $segment['growth_potential']
                ];
            }
        }
        
        // Time Metrics Table
        if (!empty($analysis['table_data_analysis']['time_metrics_table'])) {
            $timeMetrics = $analysis['table_data_analysis']['time_metrics_table'];
            $tables['time_metrics'] = [
                'title' => 'Time Metrics Analysis',
                'headers' => ['Metric', 'Value'],
                'rows' => [],
                'key' => 'time_metrics'
            ];
            
            $metrics = [];
            
            // Proposal Creation Times
            if (isset($timeMetrics['proposal_creation_times']['total_proposals'])) {
                $metrics[] = [
                    'label' => 'Total Proposals Created',
                    'value' => $timeMetrics['proposal_creation_times']['total_proposals']
                ];
            }
            
            if (isset($timeMetrics['proposal_creation_times']['avg_proposals_per_day'])) {
                $metrics[] = [
                    'label' => 'Average Proposals Per Day',
                    'value' => $timeMetrics['proposal_creation_times']['avg_proposals_per_day']
                ];
            }
            
            // Approval Times
            if (isset($timeMetrics['approval_times']['avg_approval_time_days'])) {
                $metrics[] = [
                    'label' => 'Average Approval Time (Days)',
                    'value' => $timeMetrics['approval_times']['avg_approval_time_days']
                ];
            }
            
            // Completion Times
            if (isset($timeMetrics['completion_times']['avg_completion_time_hours'])) {
                $metrics[] = [
                    'label' => 'Average Completion Time (Hours)',
                    'value' => $timeMetrics['completion_times']['avg_completion_time_hours']
                ];
            }
            
            // Efficiency Metrics
            if (isset($timeMetrics['efficiency_metrics']['overall_efficiency_score'])) {
                $metrics[] = [
                    'label' => 'Overall Efficiency Score',
                    'value' => $timeMetrics['efficiency_metrics']['overall_efficiency_score']
                ];
            }
            
            foreach ($metrics as $metric) {
                $tables['time_metrics']['rows'][] = $metric;
            }
        }
        
        return $tables;
    }
}