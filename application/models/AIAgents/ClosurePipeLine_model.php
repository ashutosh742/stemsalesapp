<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ClosurePipeLine_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Menu_model');
        $this->load->model('Report_model');
        $this->load->model('ChatAI_model');
    }

    public function process_ClosurePipeLine_analysis($message, $analysisType, $sdate, $edate) {
        
        // If same date, go back 200 days for comparison
        if($sdate == $edate) {
            $sdate = date('Y-m-d', strtotime($edate . ' -200 day'));
        }

        // Get closure pipeline data
        $closurePipelineData = $this->Report_model->getAllCompanyBYRollesMaiingClosurePipeLine($this->uid,$this->uid,'all',$sdate,$edate);

        // Check if user is asking for specific user details
        $specificUser = $this->extract_specific_user_request($message, $closurePipelineData);
        
        if ($specificUser) {
            // Generate prompt for specific user analysis
            $prompt = $this->generate_specific_user_analysis_prompt($message, $specificUser, $closurePipelineData);
        } else {
            // Generate prompt for general closure pipeline analysis
            $prompt = $this->generate_closure_pipeline_analysis_prompt($message, $closurePipelineData);
        }
        
        // Get ChatGPT response
        $chatgptResponse = $this->ChatAI_model->call_chatgpt_api($prompt, []);
        
        // Prepare data for frontend
        $frontendData = $this->prepare_closure_pipeline_data($closurePipelineData, $specificUser);
        
        return [
            'content' => $chatgptResponse,
            'data' => $closurePipelineData
        ];
    }

    private function extract_specific_user_request($message, $pipelineData) {
        // Convert message to lowercase for easier matching
        $lowerMessage = strtolower($message);
        
        // Check for specific user mentions in user data
        if (isset($pipelineData['totalClosurePipeLineDatasByuser'])) {
            foreach ($pipelineData['totalClosurePipeLineDatasByuser'] as $user) {
                $userName = strtolower($user->username);
                
                // Check if username is in message
                if (strpos($lowerMessage, $userName) !== false) {
                    return ['type' => 'user', 'data' => $user];
                }
            }
        }
        
        // Check for specific metrics mentions
        $metricKeywords = [
            'rp meeting' => ['rp meeting', 'rp meetings', 'meeting done'],
            'proposal sent' => ['proposal sent', 'proposals sent'],
            'closure' => ['closure', 'closed', 'closures'],
            'call connected' => ['call connected', 'connected calls'],
            'budget' => ['budget', 'revenue', 'value', 'rupee', '₹', 'inr', 'crore', 'lakh'],
            'pending' => ['pending', 'pending meetings', 'pending proposals']
        ];
        
        foreach ($metricKeywords as $metric => $keywords) {
            foreach ($keywords as $keyword) {
                if (strpos($lowerMessage, $keyword) !== false) {
                    return ['type' => 'metric', 'data' => ['metric' => $metric, 'keyword' => $keyword]];
                }
            }
        }
        
        return null;
    }

    // Helper function to format INR currency
    private function format_inr($amount) {
        if ($amount == null || $amount == 0) return '₹0';
        
        // Convert to float if it's a string
        $amount = floatval($amount);
        
        // Format with Indian numbering system
        if ($amount >= 10000000) { // Crores
            $in_crore = $amount / 10000000;
            return '₹' . number_format($in_crore, 2) . ' Cr';
        } elseif ($amount >= 100000) { // Lakhs
            $in_lakh = $amount / 100000;
            return '₹' . number_format($in_lakh, 2) . ' L';
        } else {
            return '₹' . number_format($amount, 2);
        }
    }

    // Helper function to get crore/lakh breakdown
    private function get_inr_breakdown($amount) {
        if ($amount == null || $amount == 0) return ['crore' => 0, 'lakh' => 0, 'rupees' => 0];
        
        $amount = floatval($amount);
        
        $crore = floor($amount / 10000000);
        $remaining_after_crore = $amount % 10000000;
        
        $lakh = floor($remaining_after_crore / 100000);
        $remaining_after_lakh = $remaining_after_crore % 100000;
        
        $rupees = round($remaining_after_lakh, 2);
        
        return [
            'crore' => $crore,
            'lakh' => $lakh,
            'rupees' => $rupees
        ];
    }

    private function generate_specific_user_analysis_prompt($message, $specificData, $pipelineData) {
        $prompt = "You are a sales analytics AI for an Indian company. All monetary values are in Indian Rupees (INR). Analyze the following user's closure pipeline data and provide detailed insights:\n\n";
        
        if ($specificData['type'] === 'user') {
            $user = $specificData['data'];
            $formattedData = "SPECIFIC USER CLOSURE PIPELINE ANALYSIS - {$user->username}\n\n";
            
            // Overall Performance Metrics
            $formattedData .= "OVERALL PERFORMANCE:\n";
            $formattedData .= "- Total Funnels in Pipeline: {$user->total_funnel}\n";
            $formattedData .= "- User ID: {$user->user_id}\n\n";
            
            // RP Meeting Metrics
            $rpMeetingRate = $user->total_funnel > 0 ? round(($user->total_funnel_complete_rp_meeting / $user->total_funnel) * 100, 2) : 0;
            $formattedData .= "RP MEETING METRICS:\n";
            $formattedData .= "- RP Meetings Completed: {$user->total_funnel_complete_rp_meeting}\n";
            $formattedData .= "- Pending for RP Meetings: {$user->total__funnel_pending_for_rp_meeting}\n";
            $formattedData .= "- RP Meeting Rate: {$rpMeetingRate}%\n";
            $formattedData .= "- By Current BD: {$user->total_funnel_complete_rp_meeting_by_current_bd}\n";
            $formattedData .= "- By Line Manager: {$user->total_funnel_complete_rp_meeting_by_line_manager}\n\n";
            
            // Call Connection Metrics
            $callConnectRateRP = $user->total_funnel_complete_rp_meeting > 0 ? round(($user->total_call_connected_after_rp_meeting / $user->total_funnel_complete_rp_meeting) * 100, 2) : 0;
            $formattedData .= "CALL CONNECTION METRICS (Post-RP Meeting):\n";
            $formattedData .= "- Calls Connected: {$user->total_call_connected_after_rp_meeting}\n";
            $formattedData .= "- Calls Not Connected: {$user->total_call_not_connected_after_rp_meeting}\n";
            $formattedData .= "- Connection Rate: {$callConnectRateRP}%\n";
            $formattedData .= "- Line Manager Connected: {$user->total_line_manager_call_connected_after_rp_meeting}\n\n";
            
            // Proposal Metrics
            $proposalRate = $user->total_funnel_complete_rp_meeting > 0 ? round(($user->total_proposal_sent / $user->total_funnel_complete_rp_meeting) * 100, 2) : 0;
            $formattedData .= "PROPOSAL METRICS:\n";
            $formattedData .= "- Proposals Sent: {$user->total_proposal_sent}\n";
            $formattedData .= "- Pending for Proposals: {$user->total_pending_for_proposal_sent}\n";
            $formattedData .= "- Proposal Rate (of RP Meetings): {$proposalRate}%\n";
            $formattedData .= "- Without Meeting: {$user->total_proposal_sent_without_meeting}\n";
            $formattedData .= "- By Current BD: {$user->total_proposal_sent_by_current_bd}\n\n";
            
            // Call Connection Post-Proposal
            $callConnectRateProposal = $user->total_proposal_sent > 0 ? round(($user->total_call_connected_after_proposal_sent / $user->total_proposal_sent) * 100, 2) : 0;
            $formattedData .= "POST-PROPOSAL CALL METRICS:\n";
            $formattedData .= "- Calls Connected: {$user->total_call_connected_after_proposal_sent}\n";
            $formattedData .= "- Calls Not Connected: {$user->total_call_not_connected_after_proposal_sent}\n";
            $formattedData .= "- Connection Rate: {$callConnectRateProposal}%\n";
            $formattedData .= "- Without Meeting: {$user->total_call_connected_after_proposal_sent_without_meeting}\n";
            $formattedData .= "- Line Manager Connected: {$user->total_line_manager_call_connected_after_proposal_sent}\n\n";
            
            // Closure Metrics
            $closureRate = $user->total_funnel > 0 ? round(($user->total_closure / $user->total_funnel) * 100, 2) : 0;
            $closureRateFromProposal = $user->total_proposal_sent > 0 ? round(($user->total_closure_after_proposal_sent / $user->total_proposal_sent) * 100, 2) : 0;
            $formattedData .= "CLOSURE METRICS:\n";
            $formattedData .= "- Total Closures: {$user->total_closure}\n";
            $formattedData .= "- Direct Closure (No Proposal): {$user->total_direct_closure_without_proposal_sent}\n";
            $formattedData .= "- Closure After Proposal: {$user->total_closure_after_proposal_sent}\n";
            $formattedData .= "- Not Closed After Proposal: {$user->total_not_closure_after_proposal_sent}\n";
            $formattedData .= "- Overall Closure Rate: {$closureRate}%\n";
            $formattedData .= "- Closure Rate from Proposals: {$closureRateFromProposal}%\n\n";
            
            // Budget Metrics in INR
            $formattedData .= "BUDGET METRICS (INR):\n";
            $totalBudget = $this->format_inr($user->total_budget_where_proposal_sent);
            $closedBudget = $this->format_inr($user->total_closure_budget_where__proposal_sent);
            $notClosedBudget = $this->format_inr($user->total_not_closure_budget_where__proposal_sent);
            
            $formattedData .= "- Total Proposal Budget: {$totalBudget}\n";
            $formattedData .= "- Closed Budget: {$closedBudget}\n";
            $formattedData .= "- Not Closed Budget: {$notClosedBudget}\n";
            
            if ($user->total_proposal_sent > 0) {
                $avgProposalValue = $user->total_budget_where_proposal_sent / $user->total_proposal_sent;
                $avgClosedValue = $user->total_closure_after_proposal_sent > 0 ? $user->total_closure_budget_where__proposal_sent / $user->total_closure_after_proposal_sent : 0;
                
                $formattedData .= "- Average Proposal Value: " . $this->format_inr($avgProposalValue) . "\n";
                $formattedData .= "- Average Closed Deal Value: " . $this->format_inr($avgClosedValue) . "\n";
            }
            
            // Get INR breakdown for better understanding
            $budgetBreakdown = $this->get_inr_breakdown($user->total_budget_where_proposal_sent);
            if ($budgetBreakdown['crore'] > 0) {
                $formattedData .= "- Budget Breakdown: {$budgetBreakdown['crore']} Crore, {$budgetBreakdown['lakh']} Lakh, " . number_format($budgetBreakdown['rupees'], 2) . " Rupees\n";
            }
            
            // Re-meeting Metrics
            $reMeetingRate = $user->total_proposal_sent > 0 ? round(($user->total_proposal_sent_remeeting_done / $user->total_proposal_sent) * 100, 2) : 0;
            $formattedData .= "\nRE-MEETING METRICS:\n";
            $formattedData .= "- Re-meetings Done: {$user->total_proposal_sent_remeeting_done}\n";
            $formattedData .= "- Re-meetings Pending: {$user->total_proposal_sent_remeeting_not_done}\n";
            $formattedData .= "- Re-meeting Rate: {$reMeetingRate}%\n";
            
            $prompt .= $formattedData;
            $prompt .= "\nPlease provide a comprehensive sales analysis with:\n";
            $prompt .= "1. Overall performance summary and key metrics\n";
            $prompt .= "2. Sales pipeline health analysis (RP Meetings → Calls → Proposals → Closures)\n";
            $prompt .= "3. Conversion rates at each stage with bottleneck identification\n";
            $prompt .= "4. Budget performance in INR with crore/lakh analysis\n";
            $prompt .= "5. Comparison with team averages (if possible)\n";
            $prompt .= "6. Recommendations for improvement at each stage\n";
            $prompt .= "7. Focus areas for increasing closures and revenue\n";
            $prompt .= "8. Call connection efficiency analysis\n";
            $prompt .= "9. Revenue potential from pending proposals\n";
            
        } elseif ($specificData['type'] === 'metric') {
            $metric = $specificData['data'];
            $overallData = $pipelineData['totalClosurePipeLineDatas'][0];
            
            $formattedData = "SPECIFIC METRIC ANALYSIS - {$metric['metric']}\n\n";
            
            if ($metric['metric'] === 'rp meeting') {
                $formattedData .= "RP MEETING METRICS OVERVIEW:\n";
                $formattedData .= "- Total Funnels: {$overallData->total_funnel}\n";
                $formattedData .= "- RP Meetings Completed: {$overallData->total_funnel_complete_rp_meeting}\n";
                $formattedData .= "- Pending for RP Meetings: {$overallData->total__funnel_pending_for_rp_meeting}\n";
                
                $rpMeetingRate = $overallData->total_funnel > 0 ? round(($overallData->total_funnel_complete_rp_meeting / $overallData->total_funnel) * 100, 2) : 0;
                $formattedData .= "- Overall RP Meeting Rate: {$rpMeetingRate}%\n";
                
                $formattedData .= "\nBREAKDOWN:\n";
                $formattedData .= "- By Current BD: {$overallData->total_funnel_complete_rp_meeting_by_current_bd}\n";
                $formattedData .= "- By Other BD: {$overallData->total_funnel_complete_rp_meeting_by_other_bd}\n";
                $formattedData .= "- By Line Manager: {$overallData->total_funnel_complete_rp_meeting_by_line_manager}\n\n";
                
                $formattedData .= "TOP USERS BY RP MEETINGS COMPLETED:\n";
                if (isset($pipelineData['totalClosurePipeLineDatasByuser'])) {
                    $users = $pipelineData['totalClosurePipeLineDatasByuser'];
                    usort($users, function($a, $b) {
                        return $b->total_funnel_complete_rp_meeting - $a->total_funnel_complete_rp_meeting;
                    });
                    
                    $topUsers = array_slice($users, 0, 5);
                    foreach ($topUsers as $index => $user) {
                        $rank = $index + 1;
                        $userRate = $user->total_funnel > 0 ? round(($user->total_funnel_complete_rp_meeting / $user->total_funnel) * 100, 2) : 0;
                        $formattedData .= "{$rank}. {$user->username}: {$user->total_funnel_complete_rp_meeting} meetings ({$userRate}% of their funnels)\n";
                    }
                }
                
            } elseif ($metric['metric'] === 'proposal sent') {
                $formattedData .= "PROPOSAL METRICS OVERVIEW:\n";
                $formattedData .= "- Total RP Meetings: {$overallData->total_funnel_complete_rp_meeting}\n";
                $formattedData .= "- Proposals Sent: {$overallData->total_proposal_sent}\n";
                $formattedData .= "- Pending for Proposals: {$overallData->total_pending_for_proposal_sent}\n";
                
                $proposalRate = $overallData->total_funnel_complete_rp_meeting > 0 ? round(($overallData->total_proposal_sent / $overallData->total_funnel_complete_rp_meeting) * 100, 2) : 0;
                $formattedData .= "- Proposal Conversion Rate: {$proposalRate}%\n";
                
                $formattedData .= "\nBREAKDOWN:\n";
                $formattedData .= "- Without Meeting: {$overallData->total_proposal_sent_without_meeting}\n";
                $formattedData .= "- By Current BD: {$overallData->total_proposal_sent_by_current_bd}\n";
                $formattedData .= "- By Other BD: {$overallData->total_proposal_sent_by_other_bd}\n";
                
                $formattedData .= "\nTOP USERS BY PROPOSALS SENT:\n";
                if (isset($pipelineData['totalClosurePipeLineDatasByuser'])) {
                    $users = $pipelineData['totalClosurePipeLineDatasByuser'];
                    usort($users, function($a, $b) {
                        return $b->total_proposal_sent - $a->total_proposal_sent;
                    });
                    
                    $topUsers = array_slice($users, 0, 5);
                    foreach ($topUsers as $index => $user) {
                        $rank = $index + 1;
                        $userRate = $user->total_funnel_complete_rp_meeting > 0 ? round(($user->total_proposal_sent / $user->total_funnel_complete_rp_meeting) * 100, 2) : 0;
                        $formattedData .= "{$rank}. {$user->username}: {$user->total_proposal_sent} proposals ({$userRate}% of their meetings)\n";
                    }
                }
                
            } elseif ($metric['metric'] === 'closure') {
                $formattedData .= "CLOSURE METRICS OVERVIEW:\n";
                $formattedData .= "- Total Funnels: {$overallData->total_funnel}\n";
                $formattedData .= "- Total Closures: {$overallData->total_closure}\n";
                $formattedData .= "- Proposals Sent: {$overallData->total_proposal_sent}\n";
                
                $overallClosureRate = $overallData->total_funnel > 0 ? round(($overallData->total_closure / $overallData->total_funnel) * 100, 2) : 0;
                $proposalClosureRate = $overallData->total_proposal_sent > 0 ? round(($overallData->total_closure_after_proposal_sent / $overallData->total_proposal_sent) * 100, 2) : 0;
                $formattedData .= "- Overall Closure Rate: {$overallClosureRate}%\n";
                $formattedData .= "- Closure Rate from Proposals: {$proposalClosureRate}%\n";
                
                $formattedData .= "\nBREAKDOWN:\n";
                $formattedData .= "- Direct Closures (No Proposal): {$overallData->total_direct_closure_without_proposal_sent}\n";
                $formattedData .= "- Closures After Proposal: {$overallData->total_closure_after_proposal_sent}\n";
                $formattedData .= "- Not Closed After Proposal: {$overallData->total_not_closure_after_proposal_sent}\n";
                
                $formattedData .= "\nBUDGET ANALYSIS (INR):\n";
                $totalBudget = $this->format_inr($overallData->total_budget_where_proposal_sent);
                $closedBudget = $this->format_inr($overallData->total_closure_budget_where__proposal_sent);
                $notClosedBudget = $this->format_inr($overallData->total_not_closure_budget_where__proposal_sent);
                
                $formattedData .= "- Total Proposal Budget: {$totalBudget}\n";
                $formattedData .= "- Closed Budget: {$closedBudget}\n";
                $formattedData .= "- Not Closed Budget: {$notClosedBudget}\n";
                
                if ($overallData->total_closure_after_proposal_sent > 0) {
                    $avgClosedValue = $overallData->total_closure_budget_where__proposal_sent / $overallData->total_closure_after_proposal_sent;
                    $formattedData .= "- Average Closed Deal Value: " . $this->format_inr($avgClosedValue) . "\n";
                }
                
                // Calculate revenue per closure
                $revenuePerClosure = $overallData->total_closure > 0 ? $overallData->total_closure_budget_where__proposal_sent / $overallData->total_closure : 0;
                $formattedData .= "- Average Revenue per Closure: " . $this->format_inr($revenuePerClosure) . "\n";
                
                $formattedData .= "\nTOP USERS BY CLOSURES:\n";
                if (isset($pipelineData['totalClosurePipeLineDatasByuser'])) {
                    $users = $pipelineData['totalClosurePipeLineDatasByuser'];
                    usort($users, function($a, $b) {
                        return $b->total_closure - $a->total_closure;
                    });
                    
                    $topUsers = array_slice($users, 0, 5);
                    foreach ($topUsers as $index => $user) {
                        $rank = $index + 1;
                        $userRate = $user->total_funnel > 0 ? round(($user->total_closure / $user->total_funnel) * 100, 2) : 0;
                        $userRevenue = $this->format_inr($user->total_closure_budget_where__proposal_sent);
                        $formattedData .= "{$rank}. {$user->username}: {$user->total_closure} closures ({$userRate}%), Revenue: {$userRevenue}\n";
                    }
                }
                
            } elseif ($metric['metric'] === 'call connected') {
                $formattedData .= "CALL CONNECTION METRICS OVERVIEW:\n\n";
                
                $formattedData .= "POST-RP MEETING CALLS:\n";
                $formattedData .= "- RP Meetings Completed: {$overallData->total_funnel_complete_rp_meeting}\n";
                $formattedData .= "- Calls Connected: {$overallData->total_call_connected_after_rp_meeting}\n";
                $formattedData .= "- Calls Not Connected: {$overallData->total_call_not_connected_after_rp_meeting}\n";
                
                $rpCallRate = $overallData->total_funnel_complete_rp_meeting > 0 ? round(($overallData->total_call_connected_after_rp_meeting / $overallData->total_funnel_complete_rp_meeting) * 100, 2) : 0;
                $formattedData .= "- Connection Rate: {$rpCallRate}%\n";
                $formattedData .= "- Line Manager Connected: {$overallData->total_line_manager_call_connected_after_rp_meeting}\n\n";
                
                $formattedData .= "POST-PROPOSAL CALLS:\n";
                $formattedData .= "- Proposals Sent: {$overallData->total_proposal_sent}\n";
                $formattedData .= "- Calls Connected: {$overallData->total_call_connected_after_proposal_sent}\n";
                $formattedData .= "- Calls Not Connected: {$overallData->total_call_not_connected_after_proposal_sent}\n";
                
                $proposalCallRate = $overallData->total_proposal_sent > 0 ? round(($overallData->total_call_connected_after_proposal_sent / $overallData->total_proposal_sent) * 100, 2) : 0;
                $formattedData .= "- Connection Rate: {$proposalCallRate}%\n";
                $formattedData .= "- Without Meeting: {$overallData->total_call_connected_after_proposal_sent_without_meeting}\n";
                $formattedData .= "- Line Manager Connected: {$overallData->total_line_manager_call_connected_after_proposal_sent}\n";
                
            } elseif ($metric['metric'] === 'budget') {
                $formattedData .= "BUDGET & REVENUE METRICS OVERVIEW (INR):\n\n";
                
                $totalBudget = $this->format_inr($overallData->total_budget_where_proposal_sent);
                $closedBudget = $this->format_inr($overallData->total_closure_budget_where__proposal_sent);
                $notClosedBudget = $this->format_inr($overallData->total_not_closure_budget_where__proposal_sent);
                
                $formattedData .= "PROPOSAL BUDGET:\n";
                $formattedData .= "- Total Proposal Budget: {$totalBudget}\n";
                $formattedData .= "- Closed Budget: {$closedBudget}\n";
                $formattedData .= "- Not Closed Budget: {$notClosedBudget}\n";
                
                $budgetClosedRate = $overallData->total_budget_where_proposal_sent > 0 ? round(($overallData->total_closure_budget_where__proposal_sent / $overallData->total_budget_where_proposal_sent) * 100, 2) : 0;
                $formattedData .= "- Budget Closure Rate: {$budgetClosedRate}%\n\n";
                
                if ($overallData->total_proposal_sent > 0) {
                    $avgProposalValue = $overallData->total_budget_where_proposal_sent / $overallData->total_proposal_sent;
                    $formattedData .= "- Average Proposal Value: " . $this->format_inr($avgProposalValue) . "\n";
                }
                
                if ($overallData->total_closure_after_proposal_sent > 0) {
                    $avgClosedValue = $overallData->total_closure_budget_where__proposal_sent / $overallData->total_closure_after_proposal_sent;
                    $formattedData .= "- Average Closed Deal Value: " . $this->format_inr($avgClosedValue) . "\n";
                }
                
                // Get breakdown in crore/lakh
                $budgetBreakdown = $this->get_inr_breakdown($overallData->total_budget_where_proposal_sent);
                $closedBreakdown = $this->get_inr_breakdown($overallData->total_closure_budget_where__proposal_sent);
                
                if ($budgetBreakdown['crore'] > 0) {
                    $formattedData .= "\nBUDGET BREAKDOWN:\n";
                    $formattedData .= "- Total: {$budgetBreakdown['crore']} Crore, {$budgetBreakdown['lakh']} Lakh\n";
                    $formattedData .= "- Closed: {$closedBreakdown['crore']} Crore, {$closedBreakdown['lakh']} Lakh\n";
                }
                
                // Calculate potential revenue from not closed proposals
                $potentialRevenue = $overallData->total_not_closure_budget_where__proposal_sent;
                $potentialRevenueFormatted = $this->format_inr($potentialRevenue);
                $formattedData .= "- Potential Revenue (Not Closed): {$potentialRevenueFormatted}\n";
                
                $formattedData .= "\nTOP USERS BY PROPOSAL BUDGET:\n";
                if (isset($pipelineData['totalClosurePipeLineDatasByuser'])) {
                    $users = $pipelineData['totalClosurePipeLineDatasByuser'];
                    usort($users, function($a, $b) {
                        return $b->total_budget_where_proposal_sent - $a->total_budget_where_proposal_sent;
                    });
                    
                    $topUsers = array_slice($users, 0, 5);
                    foreach ($topUsers as $index => $user) {
                        $rank = $index + 1;
                        $userBudget = $this->format_inr($user->total_budget_where_proposal_sent);
                        $formattedData .= "{$rank}. {$user->username}: {$userBudget}\n";
                    }
                }
                
            } elseif ($metric['metric'] === 'pending') {
                $formattedData .= "PENDING ACTIVITIES OVERVIEW:\n\n";
                
                $formattedData .= "PENDING RP MEETINGS:\n";
                $formattedData .= "- Total Funnels: {$overallData->total_funnel}\n";
                $formattedData .= "- RP Meetings Completed: {$overallData->total_funnel_complete_rp_meeting}\n";
                $formattedData .= "- Pending RP Meetings: {$overallData->total__funnel_pending_for_rp_meeting}\n";
                $pendingRPPercentage = $overallData->total_funnel > 0 ? round(($overallData->total__funnel_pending_for_rp_meeting / $overallData->total_funnel) * 100, 2) : 0;
                $formattedData .= "- Pending Rate: {$pendingRPPercentage}%\n\n";
                
                $formattedData .= "PENDING PROPOSALS:\n";
                $formattedData .= "- RP Meetings Completed: {$overallData->total_funnel_complete_rp_meeting}\n";
                $formattedData .= "- Proposals Sent: {$overallData->total_proposal_sent}\n";
                $formattedData .= "- Pending Proposals: {$overallData->total_pending_for_proposal_sent}\n";
                $pendingProposalPercentage = $overallData->total_funnel_complete_rp_meeting > 0 ? round(($overallData->total_pending_for_proposal_sent / $overallData->total_funnel_complete_rp_meeting) * 100, 2) : 0;
                $formattedData .= "- Pending Rate: {$pendingProposalPercentage}%\n\n";
                
                // Estimate potential revenue from pending activities
                $avgProposalValue = $overallData->total_proposal_sent > 0 ? $overallData->total_budget_where_proposal_sent / $overallData->total_proposal_sent : 0;
                $potentialRevenueFromPending = $overallData->total_pending_for_proposal_sent * $avgProposalValue;
                
                if ($potentialRevenueFromPending > 0) {
                    $formattedData .= "POTENTIAL REVENUE FROM PENDING (INR):\n";
                    $formattedData .= "- Estimated from Pending Proposals: " . $this->format_inr($potentialRevenueFromPending) . "\n";
                }
                
                $formattedData .= "\nTOP USERS WITH PENDING ACTIVITIES:\n";
                if (isset($pipelineData['totalClosurePipeLineDatasByuser'])) {
                    $users = $pipelineData['totalClosurePipeLineDatasByuser'];
                    
                    // Sort by pending RP meetings
                    usort($users, function($a, $b) {
                        return $b->total__funnel_pending_for_rp_meeting - $a->total__funnel_pending_for_rp_meeting;
                    });
                    
                    $formattedData .= "By Pending RP Meetings:\n";
                    $topPendingRP = array_slice($users, 0, 3);
                    foreach ($topPendingRP as $index => $user) {
                        $rank = $index + 1;
                        $pendingRate = $user->total_funnel > 0 ? round(($user->total__funnel_pending_for_rp_meeting / $user->total_funnel) * 100, 2) : 0;
                        $formattedData .= "{$rank}. {$user->username}: {$user->total__funnel_pending_for_rp_meeting} pending ({$pendingRate}%)\n";
                    }
                    
                    // Sort by pending proposals
                    usort($users, function($a, $b) {
                        return $b->total_pending_for_proposal_sent - $a->total_pending_for_proposal_sent;
                    });
                    
                    $formattedData .= "\nBy Pending Proposals:\n";
                    $topPendingProposal = array_slice($users, 0, 3);
                    foreach ($topPendingProposal as $index => $user) {
                        $rank = $index + 1;
                        $pendingRate = $user->total_funnel_complete_rp_meeting > 0 ? round(($user->total_pending_for_proposal_sent / $user->total_funnel_complete_rp_meeting) * 100, 2) : 0;
                        $formattedData .= "{$rank}. {$user->username}: {$user->total_pending_for_proposal_sent} pending ({$pendingRate}%)\n";
                    }
                }
            }
            
            $prompt .= $formattedData;
            $prompt .= "\nPlease provide a comprehensive analysis with:\n";
            $prompt .= "1. Key performance indicators for this metric\n";
            $prompt .= "2. Trends and patterns observed\n";
            $prompt .= "3. Comparison with ideal benchmarks\n";
            $prompt .= "4. Impact on overall sales pipeline\n";
            $prompt .= "5. Recommendations for improvement\n";
            $prompt .= "6. Best practices from top performers\n";
            if (in_array($metric['metric'], ['budget', 'closure', 'pending'])) {
                $prompt .= "7. Revenue implications in INR\n";
            }
        }
        
        $prompt .= "\nUser Query: {$message}\n\n";
        $prompt .= "Format your response with clear sections, use specific numbers from the data, and provide actionable sales insights. All monetary values should be discussed in Indian Rupees (INR).";
        
        return $prompt;
    }

    private function generate_closure_pipeline_analysis_prompt($message, $pipelineData) {
        $overallData = $pipelineData['totalClosurePipeLineDatas'][0];
        $userData = $pipelineData['totalClosurePipeLineDatasByuser'] ?? [];
        
        $formattedData = "CLOSURE PIPELINE ANALYSIS - Period: {$pipelineData['period']}\n\n";
        
        // Overall Summary
        $formattedData .= "OVERALL PIPELINE SUMMARY:\n";
        $formattedData .= "- Total Funnels in Pipeline: {$overallData->total_funnel}\n";
        $formattedData .= "- Total Users/BDs in Analysis: " . count($userData) . "\n\n";
        
        // Pipeline Stage Analysis
        $formattedData .= "PIPELINE STAGE ANALYSIS:\n";
        
        // RP Meetings
        $rpMeetingRate = $overallData->total_funnel > 0 ? round(($overallData->total_funnel_complete_rp_meeting / $overallData->total_funnel) * 100, 2) : 0;
        $formattedData .= "1. RP Meetings Stage:\n";
        $formattedData .= "   - Completed: {$overallData->total_funnel_complete_rp_meeting} ({$rpMeetingRate}% of funnels)\n";
        $formattedData .= "   - Pending: {$overallData->total__funnel_pending_for_rp_meeting}\n";
        $formattedData .= "   - Breakdown: Current BD ({$overallData->total_funnel_complete_rp_meeting_by_current_bd}), Line Manager ({$overallData->total_funnel_complete_rp_meeting_by_line_manager})\n\n";
        
        // Post-RP Calls
        $callConnectRateRP = $overallData->total_funnel_complete_rp_meeting > 0 ? round(($overallData->total_call_connected_after_rp_meeting / $overallData->total_funnel_complete_rp_meeting) * 100, 2) : 0;
        $formattedData .= "2. Post-RP Meeting Calls:\n";
        $formattedData .= "   - Connected: {$overallData->total_call_connected_after_rp_meeting} ({$callConnectRateRP}% of meetings)\n";
        $formattedData .= "   - Not Connected: {$overallData->total_call_not_connected_after_rp_meeting}\n";
        $formattedData .= "   - Line Manager Connected: {$overallData->total_line_manager_call_connected_after_rp_meeting}\n\n";
        
        // Proposals
        $proposalRate = $overallData->total_funnel_complete_rp_meeting > 0 ? round(($overallData->total_proposal_sent / $overallData->total_funnel_complete_rp_meeting) * 100, 2) : 0;
        $formattedData .= "3. Proposal Stage:\n";
        $formattedData .= "   - Sent: {$overallData->total_proposal_sent} ({$proposalRate}% of meetings)\n";
        $formattedData .= "   - Pending: {$overallData->total_pending_for_proposal_sent}\n";
        $formattedData .= "   - Without Meeting: {$overallData->total_proposal_sent_without_meeting}\n";
        $formattedData .= "   - By Current BD: {$overallData->total_proposal_sent_by_current_bd}\n\n";
        
        // Post-Proposal Calls
        $callConnectRateProposal = $overallData->total_proposal_sent > 0 ? round(($overallData->total_call_connected_after_proposal_sent / $overallData->total_proposal_sent) * 100, 2) : 0;
        $formattedData .= "4. Post-Proposal Calls:\n";
        $formattedData .= "   - Connected: {$overallData->total_call_connected_after_proposal_sent} ({$callConnectRateProposal}% of proposals)\n";
        $formattedData .= "   - Not Connected: {$overallData->total_call_not_connected_after_proposal_sent}\n";
        $formattedData .= "   - Line Manager Connected: {$overallData->total_line_manager_call_connected_after_proposal_sent}\n\n";
        
        // Closures
        $overallClosureRate = $overallData->total_funnel > 0 ? round(($overallData->total_closure / $overallData->total_funnel) * 100, 2) : 0;
        $proposalClosureRate = $overallData->total_proposal_sent > 0 ? round(($overallData->total_closure_after_proposal_sent / $overallData->total_proposal_sent) * 100, 2) : 0;
        $formattedData .= "5. Closure Stage:\n";
        $formattedData .= "   - Total Closures: {$overallData->total_closure} ({$overallClosureRate}% of funnels)\n";
        $formattedData .= "   - Direct (No Proposal): {$overallData->total_direct_closure_without_proposal_sent}\n";
        $formattedData .= "   - After Proposal: {$overallData->total_closure_after_proposal_sent} ({$proposalClosureRate}% of proposals)\n";
        $formattedData .= "   - Not Closed After Proposal: {$overallData->total_not_closure_after_proposal_sent}\n";
        $formattedData .= "   - WDL/NI After Proposal: {$overallData->total_wdl_or_ni_after_proposal_sent}\n\n";
        
        // Budget Analysis in INR
        $formattedData .= "BUDGET & REVENUE ANALYSIS (INR):\n";
        
        $totalBudget = $this->format_inr($overallData->total_budget_where_proposal_sent);
        $closedBudget = $this->format_inr($overallData->total_closure_budget_where__proposal_sent);
        $notClosedBudget = $this->format_inr($overallData->total_not_closure_budget_where__proposal_sent);
        
        $formattedData .= "- Total Proposal Budget: {$totalBudget}\n";
        $formattedData .= "- Closed Budget: {$closedBudget}\n";
        $formattedData .= "- Not Closed Budget: {$notClosedBudget}\n";
        
        $budgetClosedRate = $overallData->total_budget_where_proposal_sent > 0 ? round(($overallData->total_closure_budget_where__proposal_sent / $overallData->total_budget_where_proposal_sent) * 100, 2) : 0;
        $formattedData .= "- Budget Closure Rate: {$budgetClosedRate}%\n\n";
        
        // Get crore/lakh breakdown
        $budgetBreakdown = $this->get_inr_breakdown($overallData->total_budget_where_proposal_sent);
        $closedBreakdown = $this->get_inr_breakdown($overallData->total_closure_budget_where__proposal_sent);
        
        if ($budgetBreakdown['crore'] > 0) {
            $formattedData .= "BUDGET BREAKDOWN:\n";
            $formattedData .= "- Total Pipeline: {$budgetBreakdown['crore']} Crore, {$budgetBreakdown['lakh']} Lakh\n";
            $formattedData .= "- Closed Revenue: {$closedBreakdown['crore']} Crore, {$closedBreakdown['lakh']} Lakh\n";
        }
        
        // Calculate average values
        if ($overallData->total_proposal_sent > 0) {
            $avgProposalValue = $overallData->total_budget_where_proposal_sent / $overallData->total_proposal_sent;
            $formattedData .= "- Average Proposal Value: " . $this->format_inr($avgProposalValue) . "\n";
        }
        
        if ($overallData->total_closure_after_proposal_sent > 0) {
            $avgClosedValue = $overallData->total_closure_budget_where__proposal_sent / $overallData->total_closure_after_proposal_sent;
            $formattedData .= "- Average Closed Deal Value: " . $this->format_inr($avgClosedValue) . "\n";
        }
        
        // Calculate revenue per funnel
        $revenuePerFunnel = $overallData->total_funnel > 0 ? $overallData->total_closure_budget_where__proposal_sent / $overallData->total_funnel : 0;
        $formattedData .= "- Average Revenue per Funnel: " . $this->format_inr($revenuePerFunnel) . "\n\n";
        
        // Team Performance Summary
        if (!empty($userData)) {
            $formattedData .= "TEAM PERFORMANCE SUMMARY:\n";
            
            // Top performers by closures
            usort($userData, function($a, $b) {
                return $b->total_closure - $a->total_closure;
            });
            
            $topClosers = array_slice($userData, 0, 5);
            $formattedData .= "Top 5 by Closures:\n";
            foreach ($topClosers as $index => $user) {
                $rank = $index + 1;
                $closureRate = $user->total_funnel > 0 ? round(($user->total_closure / $user->total_funnel) * 100, 2) : 0;
                $userRevenue = $this->format_inr($user->total_closure_budget_where__proposal_sent);
                $formattedData .= "{$rank}. {$user->username}: {$user->total_closure} closures ({$closureRate}%), Revenue: {$userRevenue}\n";
            }
            
            // Top performers by proposals
            usort($userData, function($a, $b) {
                return $b->total_proposal_sent - $a->total_proposal_sent;
            });
            
            $topProposers = array_slice($userData, 0, 5);
            $formattedData .= "\nTop 5 by Proposals Sent:\n";
            foreach ($topProposers as $index => $user) {
                $rank = $index + 1;
                $proposalRate = $user->total_funnel_complete_rp_meeting > 0 ? round(($user->total_proposal_sent / $user->total_funnel_complete_rp_meeting) * 100, 2) : 0;
                $formattedData .= "{$rank}. {$user->username}: {$user->total_proposal_sent} proposals ({$proposalRate}%)\n";
            }
            
            // Top performers by budget
            usort($userData, function($a, $b) {
                return $b->total_budget_where_proposal_sent - $a->total_budget_where_proposal_sent;
            });
            
            $topBudget = array_slice($userData, 0, 5);
            $formattedData .= "\nTop 5 by Proposal Budget (INR):\n";
            foreach ($topBudget as $index => $user) {
                $rank = $index + 1;
                $userBudget = $this->format_inr($user->total_budget_where_proposal_sent);
                $formattedData .= "{$rank}. {$user->username}: {$userBudget}\n";
            }
            
            // Calculate team averages
            $totalTeamFunnels = array_sum(array_column($userData, 'total_funnel'));
            $totalTeamClosures = array_sum(array_column($userData, 'total_closure'));
            $totalTeamRevenue = array_sum(array_column($userData, 'total_closure_budget_where__proposal_sent'));
            
            $teamAverageClosureRate = $totalTeamFunnels > 0 ? round(($totalTeamClosures / $totalTeamFunnels) * 100, 2) : 0;
            $averageRevenuePerUser = count($userData) > 0 ? $totalTeamRevenue / count($userData) : 0;
            
            $formattedData .= "\nTEAM AVERAGES:\n";
            $formattedData .= "- Average Closure Rate: {$teamAverageClosureRate}%\n";
            $formattedData .= "- Average Revenue per User: " . $this->format_inr($averageRevenuePerUser) . "\n";
        }
        
        // Re-meeting Analysis
        $reMeetingRate = $overallData->total_proposal_sent > 0 ? round(($overallData->total_proposal_sent_remeeting_done / $overallData->total_proposal_sent) * 100, 2) : 0;
        $formattedData .= "\nRE-MEETING ANALYSIS:\n";
        $formattedData .= "- Re-meetings Done: {$overallData->total_proposal_sent_remeeting_done}\n";
        $formattedData .= "- Re-meetings Pending: {$overallData->total_proposal_sent_remeeting_not_done}\n";
        $formattedData .= "- Re-meeting Rate: {$reMeetingRate}%\n";
        
        // Calculate overall conversion funnel
        $formattedData .= "\nOVERALL CONVERSION FUNNEL:\n";
        $formattedData .= "Funnels → RP Meetings: {$rpMeetingRate}%\n";
        $formattedData .= "RP Meetings → Calls Connected: {$callConnectRateRP}%\n";
        $formattedData .= "Calls Connected → Proposals: {$proposalRate}%\n";
        $formattedData .= "Proposals → Calls Connected: {$callConnectRateProposal}%\n";
        $formattedData .= "Proposals → Closures: {$proposalClosureRate}%\n";
        $formattedData .= "Overall Funnel → Closure: {$overallClosureRate}%\n";
        
        // Calculate potential revenue from pipeline
        $potentialRevenueFromPendingProposals = $overallData->total_pending_for_proposal_sent * ($avgProposalValue ?? 0);
        $potentialRevenueFromPendingMeetings = $overallData->total__funnel_pending_for_rp_meeting * ($avgProposalValue ?? 0) * ($proposalRate / 100);
        
        $formattedData .= "\nPOTENTIAL REVENUE PIPELINE (INR):\n";
        $formattedData .= "- From Pending Proposals: " . $this->format_inr($potentialRevenueFromPendingProposals) . "\n";
        $formattedData .= "- From Pending Meetings: " . $this->format_inr($potentialRevenueFromPendingMeetings) . "\n";
        $formattedData .= "- Total Potential Pipeline: " . $this->format_inr($potentialRevenueFromPendingProposals + $potentialRevenueFromPendingMeetings) . "\n";
        
        $prompt = "You are a sales analytics AI for an Indian company. Analyze the following closure pipeline data and provide comprehensive sales insights. All monetary values are in Indian Rupees (INR):\n\n";
        $prompt .= $formattedData . "\n\n";
        $prompt .= "User Query: {$message}\n\n";
        $prompt .= "Please provide a comprehensive sales closure pipeline analysis with:\n";
        $prompt .= "1. Overall pipeline health and stage-wise conversion rates\n";
        $prompt .= "2. Bottleneck identification in the sales process\n";
        $prompt .= "3. Budget performance and revenue analysis in INR (with crore/lakh breakdown)\n";
        $prompt .= "4. Team performance rankings and standout performers\n";
        $prompt .= "5. Call connection efficiency analysis at different stages\n";
        $prompt .= "6. Re-meeting effectiveness and follow-up analysis\n";
        $prompt .= "7. Risk areas and opportunities for improvement\n";
        $prompt .= "8. Recommendations for optimizing the closure pipeline\n";
        $prompt .= "9. Sales coaching insights based on performance data\n";
        $prompt .= "10. Forecast potential revenue from pending activities in INR\n";
        $prompt .= "11. Revenue per funnel and per user analysis\n";
        $prompt .= "12. Indian market specific insights and recommendations\n\n";
        
        $prompt .= "Format your response with clear sections, use specific numbers from the data, and provide actionable sales strategies. All monetary values should be presented in Indian Rupees format (using ₹ symbol and crore/lakh terminology where appropriate).";
        
        return $prompt;
    }

    private function prepare_closure_pipeline_data($pipelineData, $specificData = null) {
        $overallData = $pipelineData['totalClosurePipeLineDatas'][0] ?? null;
        $userData = $pipelineData['totalClosurePipeLineDatasByuser'] ?? [];
        
        $preparedData = [
            'overallData' => null,
            'userData' => [],
            'specificData' => null,
            'chartData' => [],
            'summaryMetrics' => [],
            'currency' => 'INR',
            'status' => 'empty',
            'message' => 'No closure pipeline data available'
        ];
        
        if (!$overallData) {
            return $preparedData;
        }
        
        // Prepare overall data with formatted INR values
        $preparedData['overallData'] = [
            'total_funnel' => $overallData->total_funnel,
            'total_funnel_complete_rp_meeting' => $overallData->total_funnel_complete_rp_meeting,
            'total__funnel_pending_for_rp_meeting' => $overallData->total__funnel_pending_for_rp_meeting,
            'total_call_connected_after_rp_meeting' => $overallData->total_call_connected_after_rp_meeting,
            'total_call_not_connected_after_rp_meeting' => $overallData->total_call_not_connected_after_rp_meeting,
            'total_proposal_sent' => $overallData->total_proposal_sent,
            'total_pending_for_proposal_sent' => $overallData->total_pending_for_proposal_sent,
            'total_closure' => $overallData->total_closure,
            'total_direct_closure_without_proposal_sent' => $overallData->total_direct_closure_without_proposal_sent,
            'total_closure_after_proposal_sent' => $overallData->total_closure_after_proposal_sent,
            'total_not_closure_after_proposal_sent' => $overallData->total_not_closure_after_proposal_sent,
            
            // Budget fields with formatted INR values
            'total_budget_where_proposal_sent' => $this->format_inr($overallData->total_budget_where_proposal_sent),
            'total_closure_budget_where__proposal_sent' => $this->format_inr($overallData->total_closure_budget_where__proposal_sent),
            'total_not_closure_budget_where__proposal_sent' => $this->format_inr($overallData->total_not_closure_budget_where__proposal_sent),
            
            // Raw values for calculations
            'raw_budget_where_proposal_sent' => floatval($overallData->total_budget_where_proposal_sent),
            'raw_closure_budget' => floatval($overallData->total_closure_budget_where__proposal_sent),
            'raw_not_closure_budget' => floatval($overallData->total_not_closure_budget_where__proposal_sent),
        ];
        
        // Calculate rates
        $preparedData['summaryMetrics'] = [
            'rp_meeting_rate' => $overallData->total_funnel > 0 ? round(($overallData->total_funnel_complete_rp_meeting / $overallData->total_funnel) * 100, 2) : 0,
            'call_connect_rate_rp' => $overallData->total_funnel_complete_rp_meeting > 0 ? round(($overallData->total_call_connected_after_rp_meeting / $overallData->total_funnel_complete_rp_meeting) * 100, 2) : 0,
            'proposal_rate' => $overallData->total_funnel_complete_rp_meeting > 0 ? round(($overallData->total_proposal_sent / $overallData->total_funnel_complete_rp_meeting) * 100, 2) : 0,
            'call_connect_rate_proposal' => $overallData->total_proposal_sent > 0 ? round(($overallData->total_call_connected_after_proposal_sent / $overallData->total_proposal_sent) * 100, 2) : 0,
            'overall_closure_rate' => $overallData->total_funnel > 0 ? round(($overallData->total_closure / $overallData->total_funnel) * 100, 2) : 0,
            'proposal_closure_rate' => $overallData->total_proposal_sent > 0 ? round(($overallData->total_closure_after_proposal_sent / $overallData->total_proposal_sent) * 100, 2) : 0,
            'budget_closure_rate' => $overallData->total_budget_where_proposal_sent > 0 ? round(($overallData->total_closure_budget_where__proposal_sent / $overallData->total_budget_where_proposal_sent) * 100, 2) : 0,
        ];
        
        // Calculate average values in INR
        $avgProposalValue = $overallData->total_proposal_sent > 0 ? $overallData->total_budget_where_proposal_sent / $overallData->total_proposal_sent : 0;
        $avgClosedValue = $overallData->total_closure_after_proposal_sent > 0 ? $overallData->total_closure_budget_where__proposal_sent / $overallData->total_closure_after_proposal_sent : 0;
        
        $preparedData['averageValues'] = [
            'avg_proposal_value' => $this->format_inr($avgProposalValue),
            'avg_closed_deal_value' => $this->format_inr($avgClosedValue),
            'revenue_per_funnel' => $this->format_inr($overallData->total_funnel > 0 ? $overallData->total_closure_budget_where__proposal_sent / $overallData->total_funnel : 0),
        ];
        
        // INR Breakdown
        $preparedData['inrBreakdown'] = [
            'total_budget' => $this->get_inr_breakdown($overallData->total_budget_where_proposal_sent),
            'closed_budget' => $this->get_inr_breakdown($overallData->total_closure_budget_where__proposal_sent),
            'not_closed_budget' => $this->get_inr_breakdown($overallData->total_not_closure_budget_where__proposal_sent),
        ];
        
        // Prepare user data with formatted INR values
        foreach ($userData as $user) {
            $userMetrics = [
                'user_id' => $user->user_id,
                'username' => $user->username,
                'total_funnel' => $user->total_funnel,
                'total_funnel_complete_rp_meeting' => $user->total_funnel_complete_rp_meeting,
                'total__funnel_pending_for_rp_meeting' => $user->total__funnel_pending_for_rp_meeting,
                'total_call_connected_after_rp_meeting' => $user->total_call_connected_after_rp_meeting,
                'total_proposal_sent' => $user->total_proposal_sent,
                'total_closure' => $user->total_closure,
                
                // Budget fields with formatted INR values
                'total_budget_where_proposal_sent' => $this->format_inr($user->total_budget_where_proposal_sent),
                'total_closure_budget_where__proposal_sent' => $this->format_inr($user->total_closure_budget_where__proposal_sent),
                
                // Raw values for calculations
                'raw_budget_where_proposal_sent' => floatval($user->total_budget_where_proposal_sent),
                'raw_closure_budget' => floatval($user->total_closure_budget_where__proposal_sent),
                
                // Calculated metrics
                'rp_meeting_rate' => $user->total_funnel > 0 ? round(($user->total_funnel_complete_rp_meeting / $user->total_funnel) * 100, 2) : 0,
                'proposal_rate' => $user->total_funnel_complete_rp_meeting > 0 ? round(($user->total_proposal_sent / $user->total_funnel_complete_rp_meeting) * 100, 2) : 0,
                'closure_rate' => $user->total_funnel > 0 ? round(($user->total_closure / $user->total_funnel) * 100, 2) : 0,
                
                // INR breakdown
                'inr_breakdown' => $this->get_inr_breakdown($user->total_budget_where_proposal_sent),
            ];
            
            $preparedData['userData'][] = $userMetrics;
        }
        
        // Prepare specific data if requested
        if ($specificData) {
            $preparedData['specificData'] = $this->prepare_specific_data_frontend($specificData, $pipelineData);
        }
        
        // Prepare chart data
        $preparedData['chartData'] = $this->prepare_chart_data($pipelineData);
        
        // Calculate potential revenue
        $preparedData['potentialRevenue'] = [
            'from_pending_proposals' => $this->format_inr($overallData->total_pending_for_proposal_sent * $avgProposalValue),
            'from_pending_meetings' => $this->format_inr($overallData->total__funnel_pending_for_rp_meeting * $avgProposalValue * ($preparedData['summaryMetrics']['proposal_rate'] / 100)),
            'total_potential' => $this->format_inr(($overallData->total_pending_for_proposal_sent * $avgProposalValue) + 
                                ($overallData->total__funnel_pending_for_rp_meeting * $avgProposalValue * ($preparedData['summaryMetrics']['proposal_rate'] / 100)))
        ];
        
        $preparedData['status'] = 'success';
        $preparedData['message'] = 'Closure pipeline data prepared successfully';
        
        return $preparedData;
    }

    private function prepare_specific_data_frontend($specificData, $pipelineData) {
        $specificFrontendData = [];
        
        if ($specificData['type'] === 'user') {
            $user = $specificData['data'];
            $specificFrontendData = [
                'type' => 'user',
                'username' => $user->username,
                'user_id' => $user->user_id,
                'user_cluster_zone' => $user->user_cluster_zone,
                'currency' => 'INR',
                
                // Funnel Metrics
                'funnel_metrics' => [
                    'total_funnel' => $user->total_funnel,
                    'total_funnel_complete_rp_meeting' => $user->total_funnel_complete_rp_meeting,
                    'total__funnel_pending_for_rp_meeting' => $user->total__funnel_pending_for_rp_meeting,
                ],
                
                // Meeting Metrics
                'meeting_metrics' => [
                    'total_funnel_complete_rp_meeting_by_current_bd' => $user->total_funnel_complete_rp_meeting_by_current_bd,
                    'total_funnel_complete_rp_meeting_by_line_manager' => $user->total_funnel_complete_rp_meeting_by_line_manager,
                ],
                
                // Call Metrics
                'call_metrics' => [
                    'total_call_connected_after_rp_meeting' => $user->total_call_connected_after_rp_meeting,
                    'total_call_not_connected_after_rp_meeting' => $user->total_call_not_connected_after_rp_meeting,
                    'total_line_manager_call_connected_after_rp_meeting' => $user->total_line_manager_call_connected_after_rp_meeting,
                ],
                
                // Proposal Metrics
                'proposal_metrics' => [
                    'total_proposal_sent' => $user->total_proposal_sent,
                    'total_pending_for_proposal_sent' => $user->total_pending_for_proposal_sent,
                    'total_proposal_sent_without_meeting' => $user->total_proposal_sent_without_meeting,
                    'total_proposal_sent_by_current_bd' => $user->total_proposal_sent_by_current_bd,
                ],
                
                // Post-Proposal Call Metrics
                'post_proposal_call_metrics' => [
                    'total_call_connected_after_proposal_sent' => $user->total_call_connected_after_proposal_sent,
                    'total_call_not_connected_after_proposal_sent' => $user->total_call_not_connected_after_proposal_sent,
                    'total_line_manager_call_connected_after_proposal_sent' => $user->total_line_manager_call_connected_after_proposal_sent,
                ],
                
                // Closure Metrics
                'closure_metrics' => [
                    'total_closure' => $user->total_closure,
                    'total_direct_closure_without_proposal_sent' => $user->total_direct_closure_without_proposal_sent,
                    'total_closure_after_proposal_sent' => $user->total_closure_after_proposal_sent,
                    'total_not_closure_after_proposal_sent' => $user->total_not_closure_after_proposal_sent,
                ],
                
                // Budget Metrics in INR
                'budget_metrics' => [
                    'total_budget_where_proposal_sent' => $this->format_inr($user->total_budget_where_proposal_sent),
                    'total_closure_budget_where__proposal_sent' => $this->format_inr($user->total_closure_budget_where__proposal_sent),
                    'total_not_closure_budget_where__proposal_sent' => $this->format_inr($user->total_not_closure_budget_where__proposal_sent),
                ],
                
                // INR Breakdown
                'inr_breakdown' => [
                    'total_budget' => $this->get_inr_breakdown($user->total_budget_where_proposal_sent),
                    'closed_budget' => $this->get_inr_breakdown($user->total_closure_budget_where__proposal_sent),
                ],
                
                // Re-meeting Metrics
                'remeeting_metrics' => [
                    'total_proposal_sent_remeeting_done' => $user->total_proposal_sent_remeeting_done,
                    'total_proposal_sent_remeeting_not_done' => $user->total_proposal_sent_remeeting_not_done,
                ],
                
                // Calculated Rates
                'calculated_rates' => [
                    'rp_meeting_rate' => $user->total_funnel > 0 ? round(($user->total_funnel_complete_rp_meeting / $user->total_funnel) * 100, 2) : 0,
                    'call_connect_rate_rp' => $user->total_funnel_complete_rp_meeting > 0 ? round(($user->total_call_connected_after_rp_meeting / $user->total_funnel_complete_rp_meeting) * 100, 2) : 0,
                    'proposal_rate' => $user->total_funnel_complete_rp_meeting > 0 ? round(($user->total_proposal_sent / $user->total_funnel_complete_rp_meeting) * 100, 2) : 0,
                    'call_connect_rate_proposal' => $user->total_proposal_sent > 0 ? round(($user->total_call_connected_after_proposal_sent / $user->total_proposal_sent) * 100, 2) : 0,
                    'closure_rate' => $user->total_funnel > 0 ? round(($user->total_closure / $user->total_funnel) * 100, 2) : 0,
                    'proposal_closure_rate' => $user->total_proposal_sent > 0 ? round(($user->total_closure_after_proposal_sent / $user->total_proposal_sent) * 100, 2) : 0,
                ],
                
                // Average Values in INR
                'average_values' => [
                    'avg_proposal_value' => $user->total_proposal_sent > 0 ? $this->format_inr($user->total_budget_where_proposal_sent / $user->total_proposal_sent) : '₹0',
                    'avg_closed_deal_value' => $user->total_closure_after_proposal_sent > 0 ? $this->format_inr($user->total_closure_budget_where__proposal_sent / $user->total_closure_after_proposal_sent) : '₹0',
                    'revenue_per_funnel' => $user->total_funnel > 0 ? $this->format_inr($user->total_closure_budget_where__proposal_sent / $user->total_funnel) : '₹0',
                ]
            ];
        }
        
        return $specificFrontendData;
    }

    private function prepare_chart_data($pipelineData) {
        $overallData = $pipelineData['totalClosurePipeLineDatas'][0];
        $chartData = [];
        
        // Pipeline Stage Chart
        $chartData['pipelineStages'] = [
            'labels' => ['Total Funnels', 'RP Meetings', 'Proposals Sent', 'Closures'],
            'datasets' => [[
                'label' => 'Pipeline Stages',
                'data' => [
                    $overallData->total_funnel,
                    $overallData->total_funnel_complete_rp_meeting,
                    $overallData->total_proposal_sent,
                    $overallData->total_closure
                ],
                'backgroundColor' => ['#4A90E2', '#50E3C2', '#F5A623', '#7ED321'],
                'borderColor' => '#2a2a2a',
                'borderWidth' => 1
            ]]
        ];
        
        // Conversion Rates Chart
        $rpMeetingRate = $overallData->total_funnel > 0 ? round(($overallData->total_funnel_complete_rp_meeting / $overallData->total_funnel) * 100, 2) : 0;
        $callConnectRateRP = $overallData->total_funnel_complete_rp_meeting > 0 ? round(($overallData->total_call_connected_after_rp_meeting / $overallData->total_funnel_complete_rp_meeting) * 100, 2) : 0;
        $proposalRate = $overallData->total_funnel_complete_rp_meeting > 0 ? round(($overallData->total_proposal_sent / $overallData->total_funnel_complete_rp_meeting) * 100, 2) : 0;
        $closureRate = $overallData->total_funnel > 0 ? round(($overallData->total_closure / $overallData->total_funnel) * 100, 2) : 0;
        
        $chartData['conversionRates'] = [
            'labels' => ['RP Meeting Rate', 'Call Connect Rate', 'Proposal Rate', 'Closure Rate'],
            'datasets' => [[
                'label' => 'Conversion Rates (%)',
                'data' => [$rpMeetingRate, $callConnectRateRP, $proposalRate, $closureRate],
                'backgroundColor' => '#5436da',
                'borderColor' => '#4529b5',
                'borderWidth' => 1
            ]]
        ];
        
        // Budget Distribution Chart in INR (converted to crore/lakh for readability)
        $totalBudgetCrore = $overallData->total_budget_where_proposal_sent / 10000000;
        $closedBudgetCrore = $overallData->total_closure_budget_where__proposal_sent / 10000000;
        $notClosedBudgetCrore = $overallData->total_not_closure_budget_where__proposal_sent / 10000000;
        
        $chartData['budgetDistribution'] = [
            'labels' => ['Closed Budget', 'Not Closed Budget'],
            'datasets' => [[
                'label' => 'Budget Distribution (INR in Crore)',
                'data' => [$closedBudgetCrore, $notClosedBudgetCrore],
                'backgroundColor' => ['#10a37f', '#ff6b6b'],
                'borderColor' => '#2a2a2a',
                'borderWidth' => 1
            ]]
        ];
        
        // User Performance Chart (Top 10 by revenue in INR)
        if (isset($pipelineData['totalClosurePipeLineDatasByuser'])) {
            $users = $pipelineData['totalClosurePipeLineDatasByuser'];
            usort($users, function($a, $b) {
                return $b->total_closure_budget_where__proposal_sent - $a->total_closure_budget_where__proposal_sent;
            });
            
            $topUsers = array_slice($users, 0, 10);
            $userLabels = [];
            $userRevenueCrore = []; // Store in crore for better visualization
            $userClosures = [];
            
            foreach ($topUsers as $user) {
                $userLabels[] = $user->username;
                $userRevenueCrore[] = ($user->total_closure_budget_where__proposal_sent / 10000000); // Convert to crore
                $userClosures[] = $user->total_closure;
            }
            
            $chartData['topUsers'] = [
                'labels' => $userLabels,
                'datasets' => [
                    [
                        'label' => 'Revenue (Crore INR)',
                        'data' => $userRevenueCrore,
                        'backgroundColor' => '#5436da',
                        'borderColor' => '#4529b5',
                        'borderWidth' => 1,
                        'yAxisID' => 'y'
                    ],
                    [
                        'label' => 'Closures',
                        'data' => $userClosures,
                        'backgroundColor' => '#10a37f',
                        'borderColor' => '#0d8a6a',
                        'borderWidth' => 1,
                        'yAxisID' => 'y1'
                    ]
                ]
            ];
        }
        
        // Stage-wise Budget Chart
        $chartData['stageBudget'] = [
            'labels' => ['Proposal Budget', 'Closed Budget', 'Potential Revenue'],
            'datasets' => [[
                'label' => 'Budget in INR (Crore)',
                'data' => [
                    $totalBudgetCrore,
                    $closedBudgetCrore,
                    $notClosedBudgetCrore
                ],
                'backgroundColor' => ['#4A90E2', '#7ED321', '#F5A623'],
                'borderColor' => '#2a2a2a',
                'borderWidth' => 1
            ]]
        ];
        
        return $chartData;
    }
}