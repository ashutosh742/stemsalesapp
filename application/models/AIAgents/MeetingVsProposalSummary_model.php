<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MeetingVsProposalSummary_model extends CI_Model {

      public function __construct()
    {
        parent::__construct();
        $this->load->model('Menu_model');
        $this->load->model('Report_model');
        $this->load->model('ChatAI_model');
        $this->load->helper('url');
    }


    public function process_MeetingVsProposalSummary($message, $analysisType, $sdate, $edate) {
        
        // Get meeting vs proposal data
        $meetingProposalData = $this->Menu_model->GetTotalMeetingDoneProposalDoneOrNot(
            $this->uid, 
            $sdate, 
            $edate, 
            $this->uid, 
            'all'
        );

        // If no data returned
        if (empty($meetingProposalData)) {
            return [
                'content' => "No meeting vs proposal data available for the selected period {$sdate} to {$edate}.",
                'data' => null
            ];
        }

        // Extract the data object
        $data = $meetingProposalData[0];
        
        // Generate AI prompt for analysis
        $prompt = $this->generate_meeting_proposal_analysis_prompt($message, $data, $sdate, $edate);
        
        // Get AI response
        $chatgptResponse = $this->ChatAI_model->call_chatgpt_api($prompt, []);
        
        // Prepare frontend data
        $frontendData = $this->prepare_meeting_proposal_data($data, $sdate, $edate);
        
        return [
            'content' => $chatgptResponse,
            'data' => $frontendData
        ];
    }

    private function generate_meeting_proposal_analysis_prompt($message, $data, $sdate, $edate) {
        
        $formattedData = "MEETING VS PROPOSAL ANALYSIS\n";
        $formattedData .= "Period: {$sdate} to {$edate}\n\n";
        
        $formattedData .= "KEY METRICS:\n";
        $formattedData .= "- Total Meetings Conducted: {$data->total_meetings}\n";
        $formattedData .= "- Proposals Sent: {$data->proposal_sent}\n";
        $formattedData .= "- Proposals NOT Sent: {$data->proposal_not_sent}\n";
        $formattedData .= "- BD Potential Client (Proposal Not Sent): {$data->bd_potentional_client_proposal_not_sent}\n";
        $formattedData .= "- Proposal Sent (Closure Pending): {$data->proposal_sent_but_closure_not_done}\n";
        $formattedData .= "- Proposal Sent (Closure Done): {$data->proposal_sent_but_closure_done}\n\n";
        
        // Calculate percentages
        $proposalRate = $data->total_meetings > 0 ? 
            round(($data->proposal_sent / $data->total_meetings) * 100, 1) : 0;
        
        $closureRate = $data->proposal_sent > 0 ? 
            round(($data->proposal_sent_but_closure_done / $data->proposal_sent) * 100, 1) : 0;
        
        $conversionRate = $data->total_meetings > 0 ? 
            round(($data->proposal_sent_but_closure_done / $data->total_meetings) * 100, 1) : 0;
        
        $bdPotentialRate = $data->proposal_not_sent > 0 ? 
            round(($data->bd_potentional_client_proposal_not_sent / $data->proposal_not_sent) * 100, 1) : 0;
        
        $formattedData .= "CALCULATED METRICS:\n";
        $formattedData .= "- Proposal Rate: {$proposalRate}% (Proposals sent / Total meetings)\n";
        $formattedData .= "- Closure Rate: {$closureRate}% (Closed deals / Proposals sent)\n";
        $formattedData .= "- Overall Conversion: {$conversionRate}% (Closed deals / Total meetings)\n";
        $formattedData .= "- BD Potential in Non-Proposal: {$bdPotentialRate}% (BD potential / Meetings without proposal)\n";
        
        // Calculate missed opportunities
        $missedOpportunities = $data->proposal_not_sent - $data->bd_potentional_client_proposal_not_sent;
        $missedRate = $data->proposal_not_sent > 0 ? 
            round(($missedOpportunities / $data->proposal_not_sent) * 100, 1) : 0;
        
        $formattedData .= "- Missed Opportunities: {$missedOpportunities} meetings ({$missedRate}% of non-proposal meetings)\n\n";
        
        $prompt = "You are a sales analytics AI. Analyze the following meeting vs proposal data and provide insights:\n\n";
        $prompt .= $formattedData . "\n\n";
        $prompt .= "User Query: {$message}\n\n";
        $prompt .= "Please provide a comprehensive analysis covering:\n";
        $prompt .= "1. Overall performance assessment\n";
        $prompt .= "2. Proposal conversion rate analysis\n";
        $prompt .= "3. Closure effectiveness\n";
        $prompt .= "4. Missed opportunities and their impact\n";
        $prompt .= "5. BD potential analysis\n";
        $prompt .= "6. Recommendations for improvement\n";
        $prompt .= "7. Key areas of strength and concern\n\n";
        $prompt .= "Format your response with clear sections and use specific numbers from the data.";
        
        return $prompt;
    }

    private function prepare_meeting_proposal_data($data, $sdate, $edate) {
        
        // Calculate percentages
        $proposalRate = $data->total_meetings > 0 ? 
            round(($data->proposal_sent / $data->total_meetings) * 100, 1) : 0;
        
        $closureRate = $data->proposal_sent > 0 ? 
            round(($data->proposal_sent_but_closure_done / $data->proposal_sent) * 100, 1) : 0;
        
        $conversionRate = $data->total_meetings > 0 ? 
            round(($data->proposal_sent_but_closure_done / $data->total_meetings) * 100, 1) : 0;
        
        $bdPotentialRate = $data->proposal_not_sent > 0 ? 
            round(($data->bd_potentional_client_proposal_not_sent / $data->proposal_not_sent) * 100, 1) : 0;
        
        $missedOpportunities = $data->proposal_not_sent - $data->bd_potentional_client_proposal_not_sent;
        
        // Prepare summary metrics
        $summaryData = [
            'period' => "{$sdate} to {$edate}",
            'total_meetings' => $data->total_meetings,
            'proposal_sent' => $data->proposal_sent,
            'proposal_not_sent' => $data->proposal_not_sent,
            'proposal_rate' => $proposalRate,
            'closure_rate' => $closureRate,
            'conversion_rate' => $conversionRate,
            'bd_potential' => $data->bd_potentional_client_proposal_not_sent,
            'bd_potential_rate' => $bdPotentialRate,
            'pending_closure' => $data->proposal_sent_but_closure_not_done,
            'successful_closures' => $data->proposal_sent_but_closure_done,
            'missed_opportunities' => $missedOpportunities
        ];
        
        // Prepare chart data
        $chartData = [
            'meeting_distribution' => [
                'labels' => ['Proposals Sent', 'Proposals Not Sent'],
                'datasets' => [[
                    'label' => 'Meetings',
                    'data' => [$data->proposal_sent, $data->proposal_not_sent],
                    'backgroundColor' => ['#10a37f', '#ff6b6b']
                ]]
            ],
            'proposal_status' => [
                'labels' => ['Closure Done', 'Closure Pending'],
                'datasets' => [[
                    'label' => 'Proposals',
                    'data' => [
                        $data->proposal_sent_but_closure_done,
                        $data->proposal_sent_but_closure_not_done
                    ],
                    'backgroundColor' => ['#5436da', '#ffa726']
                ]]
            ],
            'non_proposal_breakdown' => [
                'labels' => ['BD Potential', 'Missed Opportunities'],
                'datasets' => [[
                    'label' => 'Meetings without Proposal',
                    'data' => [
                        $data->bd_potentional_client_proposal_not_sent,
                        $missedOpportunities
                    ],
                    'backgroundColor' => ['#26c6da', '#ff6b6b']
                ]]
            ]
        ];
        
        // Prepare table data
        $tableData = [
            'summary' => [
                'headers' => ['Metric', 'Count', 'Percentage'],
                'rows' => [
                    ['Total Meetings', $data->total_meetings, '100%'],
                    ['Proposals Sent', $data->proposal_sent, "{$proposalRate}%"],
                    ['Proposals Not Sent', $data->proposal_not_sent, (100 - $proposalRate) . "%"],
                    ['Successful Closures', $data->proposal_sent_but_closure_done, "{$conversionRate}%"],
                    ['Pending Closures', $data->proposal_sent_but_closure_not_done, "-"],
                    ['BD Potential Clients', $data->bd_potentional_client_proposal_not_sent, "{$bdPotentialRate}%"]
                ]
            ],
            'conversion_funnel' => [
                'headers' => ['Stage', 'Count', 'Conversion Rate'],
                'rows' => [
                    ['Meetings to Proposal', $data->proposal_sent, "{$proposalRate}%"],
                    ['Proposal to Closure', $data->proposal_sent_but_closure_done, "{$closureRate}%"],
                    ['Overall Conversion', $data->proposal_sent_but_closure_done, "{$conversionRate}%"]
                ]
            ]
        ];
        
        return [
            'summaryData' => $summaryData,
            'chartData' => $chartData,
            'tableData' => $tableData,
            'status' => 'success',
            'message' => 'Meeting vs proposal data prepared successfully'
        ];
    }
}