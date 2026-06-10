<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CompanyCreatedBetweenDates_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Menu_model');
        $this->load->model('Report_model');
        $this->load->model('ChatAI_model');
    }

    public function process_CompanyCreatedBetweenDates_analysis($message, $analysisType, $sdate, $edate) {
        
        // Calculate number of days
        if($sdate == $edate){
            $numberofdays = 100;
        } else {
            $date1 = new DateTime($sdate);
            $date2 = new DateTime($edate);
            $interval = $date1->diff($date2);
            $numberofdays = $interval->days;
        }

        // Get company creation data between dates
        $companyInfoCBD = $this->Menu_model->GetCompanyInfoCreateBetweenDate($this->uid, $sdate, $edate);

        // Check if data is empty or null
        if(empty($companyInfoCBD) || !is_array($companyInfoCBD)) {
            return [
                'content' => 'No company creation data found for the given period.',
                'data' => [],
                'frontendData' => [
                    'status' => 'empty',
                    'message' => 'No company creation data available for the selected date range.'
                ]
            ];
        }

        // Check if user is asking for specific sales person details
        $specificRequest = $this->extract_specific_request($message, $companyInfoCBD);
        
        if ($specificRequest) {
            // Generate prompt for specific analysis
            $prompt = $this->generate_specific_analysis_prompt($message, $specificRequest, $companyInfoCBD);
        } else {
            // Generate prompt for general analysis
            $prompt = $this->generate_general_analysis_prompt($message, $companyInfoCBD);
        }
        
        // Get ChatGPT response
        $chatgptResponse = $this->ChatAI_model->call_chatgpt_api($prompt, []);
        
        // Prepare data for frontend
        $frontendData = $this->prepare_analysis_data($companyInfoCBD, $specificRequest);
        
        return [
            'content' => $chatgptResponse,
            'data' => $companyInfoCBD,
        ];
    }

    private function extract_specific_request($message, $salesData) {
        // Convert message to lowercase for easier matching
        $lowerMessage = strtolower($message);
        
        // Check for specific sales person mentions
        foreach ($salesData as $person) {
            $userName = strtolower(is_object($person) ? $person->user_name : $person['user_name']);
            if (strpos($lowerMessage, $userName) !== false) {
                return ['type' => 'sales_person', 'data' => $person];
            }
        }
        
        // Check for specific admin mentions
        foreach ($salesData as $person) {
            $adminName = strtolower(is_object($person) ? $person->admin_name : $person['admin_name']);
            if (!empty($adminName) && strpos($lowerMessage, $adminName) !== false) {
                return ['type' => 'admin', 'data' => $person];
            }
        }
        
        // Check for specific role/type mentions
        $roles = ['sales person', 'assistant cluster manager', 'manager', 'admin'];
        foreach ($roles as $role) {
            if (strpos($lowerMessage, $role) !== false) {
                return ['type' => 'role', 'data' => $role];
            }
        }
        
        // Check for specific user ID mentions
        foreach ($salesData as $person) {
            $userId = is_object($person) ? $person->tar_user_id : $person['tar_user_id'];
            if (strpos($lowerMessage, (string)$userId) !== false) {
                return ['type' => 'user_id', 'data' => $person];
            }
        }
        
        // Check for performance metric mentions
        $metrics = ['total create company', 'total approved', 'pending for approved', 'need to update', 'form barg in meeting', 'form research'];
        foreach ($metrics as $metric) {
            $metricLower = strtolower(str_replace('_', ' ', $metric));
            if (strpos($lowerMessage, $metricLower) !== false) {
                return ['type' => 'metric', 'data' => $metric];
            }
        }
        
        return null;
    }

    private function generate_specific_analysis_prompt($message, $specificRequest, $salesData) {
        $prompt = "You are a CRM analytics AI analyzing company creation performance between dates. Provide detailed insights:\n\n";
        
        if ($specificRequest['type'] === 'sales_person') {
            $person = $specificRequest['data'];
            $userName = is_object($person) ? $person->user_name : $person['user_name'];
            $typeName = is_object($person) ? $person->type_name : $person['type_name'];
            $adminName = is_object($person) ? $person->admin_name : $person['admin_name'];
            
            $formattedData = "SPECIFIC SALES PERSON ANALYSIS - {$userName}\n\n";
            $formattedData .= "PERSON DETAILS:\n";
            $formattedData .= "- Name: {$userName}\n";
            $formattedData .= "- Role: {$typeName}\n";
            $formattedData .= "- User ID: " . (is_object($person) ? $person->tar_user_id : $person['tar_user_id']) . "\n";
            if (!empty($adminName)) {
                $formattedData .= "- Admin/Supervisor: {$adminName}\n";
            }
            
            $formattedData .= "\nPERFORMANCE METRICS:\n";
            $totalCreated = is_object($person) ? $person->total_create_company : $person['total_create_company'];
            $totalApproved = is_object($person) ? $person->total_approved : $person['total_approved'];
            $pendingApproved = is_object($person) ? $person->pending_for_approved : $person['pending_for_approved'];
            $needToUpdate = is_object($person) ? $person->need_to_update : $person['need_to_update'];
            $formBargMeeting = is_object($person) ? $person->form_barg_in_meeting : $person['form_barg_in_meeting'];
            $formResearch = is_object($person) ? $person->form_research : $person['form_research'];
            
            $approvalRate = $totalCreated > 0 ? round(($totalApproved / $totalCreated) * 100, 1) : 0;
            $meetingRate = $totalCreated > 0 ? round(($formBargMeeting / $totalCreated) * 100, 1) : 0;
            
            $formattedData .= "- Total Companies Created: {$totalCreated}\n";
            $formattedData .= "- Total Approved: {$totalApproved}\n";
            $formattedData .= "- Pending for Approval: {$pendingApproved}\n";
            $formattedData .= "- Need to Update: {$needToUpdate}\n";
            $formattedData .= "- Forms in Bargaining/Meeting: {$formBargMeeting}\n";
            $formattedData .= "- Forms in Research: {$formResearch}\n";
            $formattedData .= "- Approval Rate: {$approvalRate}%\n";
            $formattedData .= "- Meeting/Bargaining Rate: {$meetingRate}%\n";
            
            // Calculate performance score
            $performanceScore = 0;
            if ($totalCreated > 0) {
                $performanceScore = ($approvalRate * 0.4) + ($meetingRate * 0.3) + 
                                  ((1 - ($pendingApproved / $totalCreated)) * 100 * 0.2) + 
                                  ((1 - ($needToUpdate / $totalCreated)) * 100 * 0.1);
            }
            
            $formattedData .= "- Performance Score: " . round($performanceScore, 1) . "/100\n\n";
            
            // Get other sales people for comparison
            $otherSalesData = array_filter($salesData, function($p) use ($userName) {
                return (is_object($p) ? $p->user_name : $p['user_name']) !== $userName;
            });
            
            if (count($otherSalesData) > 0) {
                $avgCreated = array_sum(array_map(function($p) {
                    return is_object($p) ? $p->total_create_company : $p['total_create_company'];
                }, $otherSalesData)) / count($otherSalesData);
                
                $avgApproved = array_sum(array_map(function($p) {
                    return is_object($p) ? $p->total_approved : $p['total_approved'];
                }, $otherSalesData)) / count($otherSalesData);
                
                $formattedData .= "COMPARISON WITH PEERS:\n";
                $formattedData .= "- Peer Average Created: " . round($avgCreated, 1) . "\n";
                $formattedData .= "- Peer Average Approved: " . round($avgApproved, 1) . "\n";
                $formattedData .= "- Your Created: {$totalCreated} (" . round(($totalCreated - $avgCreated), 1) . " difference)\n";
                $formattedData .= "- Your Approved: {$totalApproved} (" . round(($totalApproved - $avgApproved), 1) . " difference)\n";
            }
            
            $prompt .= $formattedData;
            $prompt .= "\nPlease provide a comprehensive analysis with:\n";
            $prompt .= "1. Performance evaluation and strengths\n";
            $prompt .= "2. Areas for improvement\n";
            $prompt .= "3. Approval rate analysis\n";
            $prompt .= "4. Meeting/bargaining effectiveness\n";
            $prompt .= "5. Recommendations for increasing approvals\n";
            $prompt .= "6. Suggestions for reducing pending approvals\n";
            
        } elseif ($specificRequest['type'] === 'admin') {
            $person = $specificRequest['data'];
            $adminName = is_object($person) ? $person->admin_name : $person['admin_name'];
            
            // Get all team members under this admin
            $teamMembers = array_filter($salesData, function($p) use ($adminName) {
                $pAdminName = is_object($p) ? $p->admin_name : $p['admin_name'];
                return $pAdminName === $adminName;
            });
            
            $formattedData = "TEAM ANALYSIS FOR ADMIN/SUPERVISOR - {$adminName}\n\n";
            $formattedData .= "TEAM OVERVIEW:\n";
            $formattedData .= "- Admin/Supervisor: {$adminName}\n";
            $formattedData .= "- Team Size: " . count($teamMembers) . " members\n\n";
            
            // Calculate team totals
            $teamCreated = 0;
            $teamApproved = 0;
            $teamPending = 0;
            $teamNeedUpdate = 0;
            $teamMeetings = 0;
            $teamResearch = 0;
            
            $formattedData .= "TEAM MEMBERS PERFORMANCE:\n";
            $memberCount = 0;
            foreach ($teamMembers as $member) {
                $memberCount++;
                $userName = is_object($member) ? $member->user_name : $member['user_name'];
                $created = is_object($member) ? $member->total_create_company : $member['total_create_company'];
                $approved = is_object($member) ? $member->total_approved : $member['total_approved'];
                $approvalRate = $created > 0 ? round(($approved / $created) * 100, 1) : 0;
                
                $teamCreated += $created;
                $teamApproved += $approved;
                $teamPending += is_object($member) ? $member->pending_for_approved : $member['pending_for_approved'];
                $teamNeedUpdate += is_object($member) ? $member->need_to_update : $member['need_to_update'];
                $teamMeetings += is_object($member) ? $member->form_barg_in_meeting : $member['form_barg_in_meeting'];
                $teamResearch += is_object($member) ? $member->form_research : $member['form_research'];
                
                $formattedData .= "{$memberCount}. {$userName}\n";
                $formattedData .= "   - Created: {$created}\n";
                $formattedData .= "   - Approved: {$approved} ({$approvalRate}% rate)\n";
                
                if ($memberCount >= 10 && count($teamMembers) > 10) {
                    $remaining = count($teamMembers) - 10;
                    $formattedData .= "... and {$remaining} more team members\n";
                    break;
                }
            }
            
            $teamApprovalRate = $teamCreated > 0 ? round(($teamApproved / $teamCreated) * 100, 1) : 0;
            $teamMeetingRate = $teamCreated > 0 ? round(($teamMeetings / $teamCreated) * 100, 1) : 0;
            
            $formattedData .= "\nTEAM TOTALS:\n";
            $formattedData .= "- Total Created by Team: {$teamCreated}\n";
            $formattedData .= "- Total Approved by Team: {$teamApproved}\n";
            $formattedData .= "- Total Pending: {$teamPending}\n";
            $formattedData .= "- Total Need Update: {$teamNeedUpdate}\n";
            $formattedData .= "- Total Meetings/Bargaining: {$teamMeetings}\n";
            $formattedData .= "- Total in Research: {$teamResearch}\n";
            $formattedData .= "- Team Approval Rate: {$teamApprovalRate}%\n";
            $formattedData .= "- Team Meeting Rate: {$teamMeetingRate}%\n";
            
            // Compare with other admins
            $otherAdmins = [];
            foreach ($salesData as $person) {
                $admin = is_object($person) ? $person->admin_name : $person['admin_name'];
                if (!empty($admin) && $admin !== $adminName && !isset($otherAdmins[$admin])) {
                    $otherAdmins[$admin] = ['count' => 0, 'created' => 0, 'approved' => 0];
                }
                if (!empty($admin) && $admin !== $adminName) {
                    $otherAdmins[$admin]['count']++;
                    $otherAdmins[$admin]['created'] += is_object($person) ? $person->total_create_company : $person['total_create_company'];
                    $otherAdmins[$admin]['approved'] += is_object($person) ? $person->total_approved : $person['total_approved'];
                }
            }
            
            if (!empty($otherAdmins)) {
                $formattedData .= "\nCOMPARISON WITH OTHER ADMINS/TEAMS:\n";
                foreach ($otherAdmins as $admin => $stats) {
                    $adminApprovalRate = $stats['created'] > 0 ? round(($stats['approved'] / $stats['created']) * 100, 1) : 0;
                    $avgPerPerson = $stats['count'] > 0 ? round($stats['created'] / $stats['count'], 1) : 0;
                    $formattedData .= "- {$admin}: {$stats['count']} members, {$stats['created']} created, {$stats['approved']} approved ({$adminApprovalRate}%), Avg per person: {$avgPerPerson}\n";
                }
            }
            
            // Top performers
            usort($teamMembers, function($a, $b) {
                $approvedA = is_object($a) ? $a->total_approved : $a['total_approved'];
                $approvedB = is_object($b) ? $b->total_approved : $b['total_approved'];
                return $approvedB - $approvedA;
            });
            
            $formattedData .= "\nTOP PERFORMERS IN TEAM:\n";
            $topPerformers = array_slice($teamMembers, 0, 3);
            foreach ($topPerformers as $index => $member) {
                $rank = $index + 1;
                $userName = is_object($member) ? $member->user_name : $member['user_name'];
                $created = is_object($member) ? $member->total_create_company : $member['total_create_company'];
                $approved = is_object($member) ? $member->total_approved : $member['total_approved'];
                $approvalRate = $created > 0 ? round(($approved / $created) * 100, 1) : 0;
                $formattedData .= "{$rank}. {$userName}: {$approved} approved ({$approvalRate}%)\n";
            }
            
            $prompt .= $formattedData;
            $prompt .= "\nPlease provide a comprehensive analysis with:\n";
            $prompt .= "1. Team performance assessment\n";
            $prompt .= "2. Strengths and weaknesses analysis\n";
            $prompt .= "3. Approval rate optimization strategies\n";
            $prompt .= "4. Team management recommendations\n";
            $prompt .= "5. Training and coaching needs\n";
            $prompt .= "6. Performance improvement plan\n";
            
        } elseif ($specificRequest['type'] === 'role') {
            $role = $specificRequest['data'];
            $roleData = array_filter($salesData, function($person) use ($role) {
                $personRole = strtolower(is_object($person) ? $person->type_name : $person['type_name']);
                return strpos($personRole, $role) !== false;
            });
            
            $formattedData = "ROLE ANALYSIS - " . ucfirst($role) . "\n\n";
            $formattedData .= "OVERVIEW:\n";
            $formattedData .= "- Role: " . ucfirst($role) . "\n";
            $formattedData .= "- Total People: " . count($roleData) . "\n\n";
            
            // Calculate statistics
            $totalCreated = 0;
            $totalApproved = 0;
            $totalPending = 0;
            $totalNeedUpdate = 0;
            $totalMeetings = 0;
            $totalResearch = 0;
            
            $performanceData = [];
            foreach ($roleData as $person) {
                $created = is_object($person) ? $person->total_create_company : $person['total_create_company'];
                $approved = is_object($person) ? $person->total_approved : $person['total_approved'];
                $pending = is_object($person) ? $person->pending_for_approved : $person['pending_for_approved'];
                $needUpdate = is_object($person) ? $person->need_to_update : $person['need_to_update'];
                $meetings = is_object($person) ? $person->form_barg_in_meeting : $person['form_barg_in_meeting'];
                $research = is_object($person) ? $person->form_research : $person['form_research'];
                
                $totalCreated += $created;
                $totalApproved += $approved;
                $totalPending += $pending;
                $totalNeedUpdate += $needUpdate;
                $totalMeetings += $meetings;
                $totalResearch += $research;
                
                $userName = is_object($person) ? $person->user_name : $person['user_name'];
                $approvalRate = $created > 0 ? round(($approved / $created) * 100, 1) : 0;
                $performanceData[$userName] = [
                    'created' => $created,
                    'approved' => $approved,
                    'approval_rate' => $approvalRate,
                    'meetings' => $meetings
                ];
            }
            
            $avgCreated = count($roleData) > 0 ? round($totalCreated / count($roleData), 1) : 0;
            $avgApproved = count($roleData) > 0 ? round($totalApproved / count($roleData), 1) : 0;
            $overallApprovalRate = $totalCreated > 0 ? round(($totalApproved / $totalCreated) * 100, 1) : 0;
            $meetingRate = $totalCreated > 0 ? round(($totalMeetings / $totalCreated) * 100, 1) : 0;
            
            $formattedData .= "AGGREGATE PERFORMANCE:\n";
            $formattedData .= "- Total Companies Created: {$totalCreated}\n";
            $formattedData .= "- Total Companies Approved: {$totalApproved}\n";
            $formattedData .= "- Total Pending Approval: {$totalPending}\n";
            $formattedData .= "- Total Need Update: {$totalNeedUpdate}\n";
            $formattedData .= "- Total in Meetings/Bargaining: {$totalMeetings}\n";
            $formattedData .= "- Total in Research: {$totalResearch}\n";
            $formattedData .= "- Overall Approval Rate: {$overallApprovalRate}%\n";
            $formattedData .= "- Meeting/Bargaining Rate: {$meetingRate}%\n";
            $formattedData .= "- Average Created per Person: {$avgCreated}\n";
            $formattedData .= "- Average Approved per Person: {$avgApproved}\n\n";
            
            // Top performers in this role
            uasort($performanceData, function($a, $b) {
                return $b['approved'] - $a['approved'];
            });
            
            $formattedData .= "TOP PERFORMERS IN THIS ROLE:\n";
            $topPerformers = array_slice($performanceData, 0, 5, true);
            $rank = 1;
            foreach ($topPerformers as $userName => $data) {
                $formattedData .= "{$rank}. {$userName}\n";
                $formattedData .= "   - Created: {$data['created']}\n";
                $formattedData .= "   - Approved: {$data['approved']}\n";
                $formattedData .= "   - Approval Rate: {$data['approval_rate']}%\n";
                $formattedData .= "   - Meetings: {$data['meetings']}\n";
                $rank++;
            }
            
            // Calculate distribution of performance
            $performanceRanges = [
                'Excellent (80-100%)' => 0,
                'Good (60-79%)' => 0,
                'Average (40-59%)' => 0,
                'Below Average (<40%)' => 0
            ];
            
            foreach ($performanceData as $user => $data) {
                if ($data['approval_rate'] >= 80) {
                    $performanceRanges['Excellent (80-100%)']++;
                } elseif ($data['approval_rate'] >= 60) {
                    $performanceRanges['Good (60-79%)']++;
                } elseif ($data['approval_rate'] >= 40) {
                    $performanceRanges['Average (40-59%)']++;
                } else {
                    $performanceRanges['Below Average (<40%)']++;
                }
            }
            
            $formattedData .= "\nPERFORMANCE DISTRIBUTION:\n";
            foreach ($performanceRanges as $range => $count) {
                $percentage = count($roleData) > 0 ? round(($count / count($roleData)) * 100, 1) : 0;
                $formattedData .= "- {$range}: {$count} people ({$percentage}%)\n";
            }
            
            $prompt .= $formattedData;
            $prompt .= "\nPlease provide a comprehensive analysis with:\n";
            $prompt .= "1. Role-specific performance evaluation\n";
            $prompt .= "2. Strengths and weaknesses of this role\n";
            $prompt .= "3. Approval rate analysis\n";
            $prompt .= "4. Comparison with expected role performance\n";
            $prompt .= "5. Training and development needs\n";
            $prompt .= "6. Performance improvement recommendations\n";
            
        } elseif ($specificRequest['type'] === 'user_id') {
            $person = $specificRequest['data'];
            
            $userName = is_object($person) ? $person->user_name : $person['user_name'];
            $userId = is_object($person) ? $person->tar_user_id : $person['tar_user_id'];
            $typeName = is_object($person) ? $person->type_name : $person['type_name'];
            $adminName = is_object($person) ? $person->admin_name : $person['admin_name'];
            
            $formattedData = "USER ID ANALYSIS - User ID: {$userId}\n\n";
            
            $formattedData .= "USER DETAILS:\n";
            $formattedData .= "- Name: {$userName}\n";
            $formattedData .= "- User ID: {$userId}\n";
            $formattedData .= "- Role: {$typeName}\n";
            if (!empty($adminName)) {
                $formattedData .= "- Admin/Supervisor: {$adminName}\n";
            }
            
            $formattedData .= "\nPERFORMANCE METRICS:\n";
            $created = is_object($person) ? $person->total_create_company : $person['total_create_company'];
            $approved = is_object($person) ? $person->total_approved : $person['total_approved'];
            $pending = is_object($person) ? $person->pending_for_approved : $person['pending_for_approved'];
            $needUpdate = is_object($person) ? $person->need_to_update : $person['need_to_update'];
            $meetings = is_object($person) ? $person->form_barg_in_meeting : $person['form_barg_in_meeting'];
            $research = is_object($person) ? $person->form_research : $person['form_research'];
            
            $approvalRate = $created > 0 ? round(($approved / $created) * 100, 1) : 0;
            $meetingRate = $created > 0 ? round(($meetings / $created) * 100, 1) : 0;
            $pendingRate = $created > 0 ? round(($pending / $created) * 100, 1) : 0;
            $updateRate = $created > 0 ? round(($needUpdate / $created) * 100, 1) : 0;
            
            $formattedData .= "- Total Companies Created: {$created}\n";
            $formattedData .= "- Total Approved: {$approved}\n";
            $formattedData .= "- Pending for Approval: {$pending}\n";
            $formattedData .= "- Need to Update: {$needUpdate}\n";
            $formattedData .= "- Forms in Bargaining/Meeting: {$meetings}\n";
            $formattedData .= "- Forms in Research: {$research}\n\n";
            
            $formattedData .= "PERFORMANCE RATES:\n";
            $formattedData .= "- Approval Rate: {$approvalRate}%\n";
            $formattedData .= "- Meeting/Bargaining Rate: {$meetingRate}%\n";
            $formattedData .= "- Pending Approval Rate: {$pendingRate}%\n";
            $formattedData .= "- Need Update Rate: {$updateRate}%\n";
            
            // Performance assessment
            $performanceLevel = '';
            if ($approvalRate >= 80) {
                $performanceLevel = 'Excellent';
            } elseif ($approvalRate >= 60) {
                $performanceLevel = 'Good';
            } elseif ($approvalRate >= 40) {
                $performanceLevel = 'Average';
            } else {
                $performanceLevel = 'Needs Improvement';
            }
            
            $formattedData .= "\nPERFORMANCE ASSESSMENT:\n";
            $formattedData .= "- Level: {$performanceLevel}\n";
            $formattedData .= "- Key Strength: ";
            if ($approvalRate >= 70) {
                $formattedData .= "High approval conversion rate\n";
            } elseif ($meetingRate >= 50) {
                $formattedData .= "Good meeting/bargaining engagement\n";
            } elseif ($pendingRate <= 10) {
                $formattedData .= "Low pending approval rate\n";
            } else {
                $formattedData .= "Consistent company creation\n";
            }
            
            $formattedData .= "- Area for Improvement: ";
            if ($approvalRate < 50) {
                $formattedData .= "Increase approval conversion\n";
            } elseif ($pendingRate > 20) {
                $formattedData .= "Reduce pending approvals\n";
            } elseif ($updateRate > 15) {
                $formattedData .= "Complete pending updates\n";
            } else {
                $formattedData .= "Maintain current performance\n";
            }
            
            $prompt .= $formattedData;
            $prompt .= "\nPlease provide a comprehensive analysis with:\n";
            $prompt .= "1. Individual performance evaluation\n";
            $prompt .= "2. Detailed metric analysis\n";
            $prompt .= "3. Strengths and achievements\n";
            $prompt .= "4. Improvement areas and action plan\n";
            $prompt .= "5. Goal setting recommendations\n";
            $prompt .= "6. Performance optimization strategies\n";
            
        } elseif ($specificRequest['type'] === 'metric') {
            $metric = $specificRequest['data'];
            $metricName = str_replace('_', ' ', $metric);
            
            // Sort data by this metric
            usort($salesData, function($a, $b) use ($metric) {
                $valueA = is_object($a) ? $a->$metric : $a[$metric];
                $valueB = is_object($b) ? $b->$metric : $b[$metric];
                return $valueB - $valueA;
            });
            
            $formattedData = "METRIC ANALYSIS - " . ucwords($metricName) . "\n\n";
            
            $formattedData .= "OVERVIEW:\n";
            $formattedData .= "- Metric: " . ucwords($metricName) . "\n";
            $formattedData .= "- Total People Analyzed: " . count($salesData) . "\n\n";
            
            // Calculate statistics
            $values = array_map(function($person) use ($metric) {
                return is_object($person) ? $person->$metric : $person[$metric];
            }, $salesData);
            
            $total = array_sum($values);
            $average = round($total / count($salesData), 1);
            $maximum = max($values);
            $minimum = min($values);
            $median = $this->calculate_median($values);
            
            $formattedData .= "STATISTICS:\n";
            $formattedData .= "- Total: {$total}\n";
            $formattedData .= "- Average: {$average}\n";
            $formattedData .= "- Maximum: {$maximum}\n";
            $formattedData .= "- Minimum: {$minimum}\n";
            $formattedData .= "- Median: {$median}\n\n";
            
            // Top performers for this metric
            $formattedData .= "TOP 10 PERFORMERS FOR THIS METRIC:\n";
            $top10 = array_slice($salesData, 0, 10);
            $rank = 1;
            foreach ($top10 as $person) {
                $userName = is_object($person) ? $person->user_name : $person['user_name'];
                $value = is_object($person) ? $person->$metric : $person[$metric];
                $role = is_object($person) ? $person->type_name : $person['type_name'];
                $admin = is_object($person) ? $person->admin_name : $person['admin_name'];
                
                $formattedData .= "{$rank}. {$userName}\n";
                $formattedData .= "   - Value: {$value}\n";
                $formattedData .= "   - Role: {$role}\n";
                if (!empty($admin)) {
                    $formattedData .= "   - Admin: {$admin}\n";
                }
                $rank++;
            }
            
            // Distribution analysis
            $ranges = $this->get_metric_ranges($metric, $values);
            $formattedData .= "\nDISTRIBUTION ANALYSIS:\n";
            foreach ($ranges as $range => $count) {
                $percentage = round(($count / count($salesData)) * 100, 1);
                $formattedData .= "- {$range}: {$count} people ({$percentage}%)\n";
            }
            
            // Correlation with other metrics
            if ($metric !== 'total_approved') {
                $formattedData .= "\nCORRELATION WITH APPROVAL RATE:\n";
                $correlationData = [];
                foreach ($salesData as $person) {
                    $metricValue = is_object($person) ? $person->$metric : $person[$metric];
                    $created = is_object($person) ? $person->total_create_company : $person['total_create_company'];
                    $approved = is_object($person) ? $person->total_approved : $person['total_approved'];
                    $approvalRate = $created > 0 ? round(($approved / $created) * 100, 1) : 0;
                    
                    if (!isset($correlationData[$metricValue])) {
                        $correlationData[$metricValue] = ['total_rate' => 0, 'count' => 0];
                    }
                    $correlationData[$metricValue]['total_rate'] += $approvalRate;
                    $correlationData[$metricValue]['count']++;
                }
                
                ksort($correlationData);
                $formattedData .= "As {$metricName} increases, approval rate tends to: ";
                
                // Simple trend analysis
                $trend = $this->analyze_trend($correlationData);
                $formattedData .= "{$trend}\n";
            }
            
            $prompt .= $formattedData;
            $prompt .= "\nPlease provide a comprehensive analysis with:\n";
            $prompt .= "1. Metric importance and interpretation\n";
            $prompt .= "2. Performance benchmarking\n";
            $prompt .= "3. Distribution analysis\n";
            $prompt .= "4. Correlation with other performance indicators\n";
            $prompt .= "5. Recommendations for improving this metric\n";
            $prompt .= "6. Best practices and strategies\n";
        }
        
        $prompt .= "\nUser Query: {$message}\n\n";
        $prompt .= "Format your response with clear sections, use specific numbers from the data, and provide actionable insights.";
        
        return $prompt;
    }

    private function generate_general_analysis_prompt($message, $salesData) {
        $totalPeople = count($salesData);
        
        $formattedData = "COMPANY CREATION PERFORMANCE ANALYSIS\n\n";
        
        // Overall Summary
        $formattedData .= "OVERALL SUMMARY:\n";
        $formattedData .= "- Total People Analyzed: {$totalPeople}\n";
        $formattedData .= "- Analysis Period: Company creation performance data\n\n";
        
        // Calculate totals
        $totalCreated = 0;
        $totalApproved = 0;
        $totalPending = 0;
        $totalNeedUpdate = 0;
        $totalMeetings = 0;
        $totalResearch = 0;
        
        foreach ($salesData as $person) {
            $totalCreated += is_object($person) ? $person->total_create_company : $person['total_create_company'];
            $totalApproved += is_object($person) ? $person->total_approved : $person['total_approved'];
            $totalPending += is_object($person) ? $person->pending_for_approved : $person['pending_for_approved'];
            $totalNeedUpdate += is_object($person) ? $person->need_to_update : $person['need_to_update'];
            $totalMeetings += is_object($person) ? $person->form_barg_in_meeting : $person['form_barg_in_meeting'];
            $totalResearch += is_object($person) ? $person->form_research : $person['form_research'];
        }
        
        $overallApprovalRate = $totalCreated > 0 ? round(($totalApproved / $totalCreated) * 100, 1) : 0;
        $meetingRate = $totalCreated > 0 ? round(($totalMeetings / $totalCreated) * 100, 1) : 0;
        $pendingRate = $totalCreated > 0 ? round(($totalPending / $totalCreated) * 100, 1) : 0;
        $updateRate = $totalCreated > 0 ? round(($totalNeedUpdate / $totalCreated) * 100, 1) : 0;
        
        $formattedData .= "AGGREGATE PERFORMANCE:\n";
        $formattedData .= "- Total Companies Created: {$totalCreated}\n";
        $formattedData .= "- Total Companies Approved: {$totalApproved}\n";
        $formattedData .= "- Total Pending Approval: {$totalPending}\n";
        $formattedData .= "- Total Need Update: {$totalNeedUpdate}\n";
        $formattedData .= "- Total in Meetings/Bargaining: {$totalMeetings}\n";
        $formattedData .= "- Total in Research: {$totalResearch}\n";
        $formattedData .= "- Overall Approval Rate: {$overallApprovalRate}%\n";
        $formattedData .= "- Meeting/Bargaining Rate: {$meetingRate}%\n";
        $formattedData .= "- Pending Rate: {$pendingRate}%\n";
        $formattedData .= "- Need Update Rate: {$updateRate}%\n\n";
        
        // Role Distribution
        $roleDistribution = [];
        $rolePerformance = [];
        foreach ($salesData as $person) {
            $role = is_object($person) ? $person->type_name : $person['type_name'];
            $created = is_object($person) ? $person->total_create_company : $person['total_create_company'];
            $approved = is_object($person) ? $person->total_approved : $person['total_approved'];
            
            $roleDistribution[$role] = ($roleDistribution[$role] ?? 0) + 1;
            
            if (!isset($rolePerformance[$role])) {
                $rolePerformance[$role] = ['created' => 0, 'approved' => 0, 'count' => 0];
            }
            $rolePerformance[$role]['created'] += $created;
            $rolePerformance[$role]['approved'] += $approved;
            $rolePerformance[$role]['count']++;
        }
        
        $formattedData .= "ROLE DISTRIBUTION:\n";
        foreach ($roleDistribution as $role => $count) {
            $percentage = round(($count / $totalPeople) * 100, 1);
            $avgCreated = round($rolePerformance[$role]['created'] / $count, 1);
            $avgApproved = round($rolePerformance[$role]['approved'] / $count, 1);
            $approvalRate = $rolePerformance[$role]['created'] > 0 ? 
                round(($rolePerformance[$role]['approved'] / $rolePerformance[$role]['created']) * 100, 1) : 0;
            
            $formattedData .= "- {$role}: {$count} people ({$percentage}%)\n";
            $formattedData .= "  Average Created: {$avgCreated}, Average Approved: {$avgApproved}, Approval Rate: {$approvalRate}%\n";
        }
        
        // Admin Distribution
        $adminDistribution = [];
        foreach ($salesData as $person) {
            $admin = is_object($person) ? $person->admin_name : $person['admin_name'];
            if (!empty($admin)) {
                $adminDistribution[$admin] = ($adminDistribution[$admin] ?? 0) + 1;
            }
        }
        
        if (!empty($adminDistribution)) {
            arsort($adminDistribution);
            
            $formattedData .= "\nADMIN/SUPERVISOR DISTRIBUTION:\n";
            $topAdmins = array_slice($adminDistribution, 0, 10, true);
            $rank = 1;
            foreach ($topAdmins as $admin => $count) {
                $percentage = round(($count / $totalPeople) * 100, 1);
                $formattedData .= "{$rank}. {$admin}: {$count} people ({$percentage}%)\n";
                $rank++;
            }
        }
        
        // Top Performers
        usort($salesData, function($a, $b) {
            $approvedA = is_object($a) ? $a->total_approved : $a['total_approved'];
            $approvedB = is_object($b) ? $b->total_approved : $b['total_approved'];
            return $approvedB - $approvedA;
        });
        
        $formattedData .= "\nTOP 10 PERFORMERS (BY APPROVED COMPANIES):\n";
        $top10 = array_slice($salesData, 0, 10);
        $rank = 1;
        foreach ($top10 as $person) {
            $userName = is_object($person) ? $person->user_name : $person['user_name'];
            $created = is_object($person) ? $person->total_create_company : $person['total_create_company'];
            $approved = is_object($person) ? $person->total_approved : $person['total_approved'];
            $approvalRate = $created > 0 ? round(($approved / $created) * 100, 1) : 0;
            $role = is_object($person) ? $person->type_name : $person['type_name'];
            
            $formattedData .= "{$rank}. {$userName} ({$role})\n";
            $formattedData .= "   - Created: {$created}\n";
            $formattedData .= "   - Approved: {$approved} ({$approvalRate}% rate)\n";
            $rank++;
        }
        
        // Approval Rate Distribution
        $approvalRates = [];
        foreach ($salesData as $person) {
            $created = is_object($person) ? $person->total_create_company : $person['total_create_company'];
            $approved = is_object($person) ? $person->total_approved : $person['total_approved'];
            $approvalRate = $created > 0 ? round(($approved / $created) * 100, 1) : 0;
            $approvalRates[] = $approvalRate;
        }
        
        $avgApprovalRate = round(array_sum($approvalRates) / count($approvalRates), 1);
        $maxApprovalRate = max($approvalRates);
        $minApprovalRate = min($approvalRates);
        
        $formattedData .= "\nAPPROVAL RATE ANALYSIS:\n";
        $formattedData .= "- Average Approval Rate: {$avgApprovalRate}%\n";
        $formattedData .= "- Maximum Approval Rate: {$maxApprovalRate}%\n";
        $formattedData .= "- Minimum Approval Rate: {$minApprovalRate}%\n\n";
        
        // Distribution of approval rates
        $rateRanges = [
            'Excellent (80-100%)' => 0,
            'Good (60-79%)' => 0,
            'Average (40-59%)' => 0,
            'Below Average (20-39%)' => 0,
            'Poor (0-19%)' => 0
        ];
        
        foreach ($approvalRates as $rate) {
            if ($rate >= 80) {
                $rateRanges['Excellent (80-100%)']++;
            } elseif ($rate >= 60) {
                $rateRanges['Good (60-79%)']++;
            } elseif ($rate >= 40) {
                $rateRanges['Average (40-59%)']++;
            } elseif ($rate >= 20) {
                $rateRanges['Below Average (20-39%)']++;
            } else {
                $rateRanges['Poor (0-19%)']++;
            }
        }
        
        $formattedData .= "APPROVAL RATE DISTRIBUTION:\n";
        foreach ($rateRanges as $range => $count) {
            $percentage = round(($count / $totalPeople) * 100, 1);
            $formattedData .= "- {$range}: {$count} people ({$percentage}%)\n";
        }
        
        // Meeting/Bargaining Analysis
        $meetingRates = [];
        foreach ($salesData as $person) {
            $created = is_object($person) ? $person->total_create_company : $person['total_create_company'];
            $meetings = is_object($person) ? $person->form_barg_in_meeting : $person['form_barg_in_meeting'];
            $meetingRate = $created > 0 ? round(($meetings / $created) * 100, 1) : 0;
            $meetingRates[] = $meetingRate;
        }
        
        $avgMeetingRate = round(array_sum($meetingRates) / count($meetingRates), 1);
        
        $formattedData .= "\nMEETING/BARGAINING ANALYSIS:\n";
        $formattedData .= "- Average Meeting Rate: {$avgMeetingRate}%\n";
        $formattedData .= "- Correlation with approval: ";
        
        // Calculate correlation between meeting rate and approval rate
        $correlation = $this->calculate_correlation($meetingRates, $approvalRates);
        if ($correlation > 0.3) {
            $formattedData .= "Positive correlation - higher meeting rates tend to lead to higher approval rates\n";
        } elseif ($correlation < -0.3) {
            $formattedData .= "Negative correlation - higher meeting rates tend to lead to lower approval rates\n";
        } else {
            $formattedData .= "Weak correlation - meeting rate doesn't strongly predict approval rate\n";
        }
        
        $prompt = "You are a sales performance and CRM analytics AI. Analyze the following company creation performance data and provide comprehensive insights:\n\n";
        $prompt .= $formattedData . "\n\n";
        $prompt .= "User Query: {$message}\n\n";
        $prompt .= "Please provide a comprehensive analysis with:\n";
        $prompt .= "1. Overall performance assessment\n";
        $prompt .= "2. Key strengths and weaknesses across the team\n";
        $prompt .= "3. Approval rate analysis and optimization strategies\n";
        $prompt .= "4. Role-based performance insights\n";
        $prompt .= "5. Top performers and best practices\n";
        $prompt .= "6. Areas requiring intervention or improvement\n";
        $prompt .= "7. Recommendations for process improvement\n";
        $prompt .= "8. Goal setting and performance targets\n";
        $prompt .= "9. Training and development recommendations\n\n";
        
        $prompt .= "Format your response with clear sections, use specific numbers from the data, and provide actionable insights.";
        
        return $prompt;
    }

    private function prepare_analysis_data($salesData, $specificRequest = null) {
        $totalPeople = count($salesData);
        
        $preparedData = [
            'tableData' => null,
            'summaryData' => [],
            'chartData' => null,
            'specificRequestData' => null,
            'status' => 'empty',
            'message' => 'No sales data available'
        ];
        
        if ($totalPeople === 0) {
            return $preparedData;
        }
        
        // If specific request, prepare that data
        if ($specificRequest) {
            $preparedData['specificRequestData'] = $this->prepare_specific_request_frontend_data($specificRequest, $salesData);
        }
        
        // Calculate overall statistics
        $totalCreated = 0;
        $totalApproved = 0;
        $totalPending = 0;
        $totalNeedUpdate = 0;
        $totalMeetings = 0;
        $totalResearch = 0;
        
        $approvalRates = [];
        $meetingRates = [];
        
        // Role distribution
        $roleDistribution = [];
        $adminDistribution = [];
        
        foreach ($salesData as $person) {
            $created = is_object($person) ? $person->total_create_company : $person['total_create_company'];
            $approved = is_object($person) ? $person->total_approved : $person['total_approved'];
            $pending = is_object($person) ? $person->pending_for_approved : $person['pending_for_approved'];
            $needUpdate = is_object($person) ? $person->need_to_update : $person['need_to_update'];
            $meetings = is_object($person) ? $person->form_barg_in_meeting : $person['form_barg_in_meeting'];
            $research = is_object($person) ? $person->form_research : $person['form_research'];
            $role = is_object($person) ? $person->type_name : $person['type_name'];
            $admin = is_object($person) ? $person->admin_name : $person['admin_name'];
            
            $totalCreated += $created;
            $totalApproved += $approved;
            $totalPending += $pending;
            $totalNeedUpdate += $needUpdate;
            $totalMeetings += $meetings;
            $totalResearch += $research;
            
            // Calculate rates
            $approvalRate = $created > 0 ? round(($approved / $created) * 100, 1) : 0;
            $meetingRate = $created > 0 ? round(($meetings / $created) * 100, 1) : 0;
            $approvalRates[] = $approvalRate;
            $meetingRates[] = $meetingRate;
            
            // Role distribution
            $roleDistribution[$role] = ($roleDistribution[$role] ?? 0) + 1;
            
            // Admin distribution
            if (!empty($admin)) {
                $adminDistribution[$admin] = ($adminDistribution[$admin] ?? 0) + 1;
            }
        }
        
        $overallApprovalRate = $totalCreated > 0 ? round(($totalApproved / $totalCreated) * 100, 1) : 0;
        $overallMeetingRate = $totalCreated > 0 ? round(($totalMeetings / $totalCreated) * 100, 1) : 0;
        $avgApprovalRate = round(array_sum($approvalRates) / count($approvalRates), 1);
        $avgMeetingRate = round(array_sum($meetingRates) / count($meetingRates), 1);
        
        // Prepare summary data
        $preparedData['summaryData'] = [
            'total_people' => $totalPeople,
            'total_created' => $totalCreated,
            'total_approved' => $totalApproved,
            'total_pending' => $totalPending,
            'total_need_update' => $totalNeedUpdate,
            'total_meetings' => $totalMeetings,
            'total_research' => $totalResearch,
            'overall_approval_rate' => $overallApprovalRate,
            'overall_meeting_rate' => $overallMeetingRate,
            'average_approval_rate' => $avgApprovalRate,
            'average_meeting_rate' => $avgMeetingRate,
            'role_distribution' => $roleDistribution,
            'admin_distribution' => array_slice($adminDistribution, 0, 10, true),
            'unique_roles' => count($roleDistribution),
            'unique_admins' => count($adminDistribution)
        ];
        
        // Prepare chart data for role distribution
        $preparedData['roleChartData'] = [
            'labels' => array_keys($roleDistribution),
            'datasets' => [[
                'label' => 'People by Role',
                'data' => array_values($roleDistribution),
                'backgroundColor' => ['#5436da', '#10a37f', '#ffa726', '#ff6b6b', '#8e44ad', '#f39c12', '#1abc9c'],
                'borderColor' => '#2a2a2a',
                'borderWidth' => 1
            ]]
        ];
        
        // Prepare chart data for approval rate distribution
        $rateRanges = [
            '80-100%' => 0,
            '60-79%' => 0,
            '40-59%' => 0,
            '20-39%' => 0,
            '0-19%' => 0
        ];
        
        foreach ($approvalRates as $rate) {
            if ($rate >= 80) {
                $rateRanges['80-100%']++;
            } elseif ($rate >= 60) {
                $rateRanges['60-79%']++;
            } elseif ($rate >= 40) {
                $rateRanges['40-59%']++;
            } elseif ($rate >= 20) {
                $rateRanges['20-39%']++;
            } else {
                $rateRanges['0-19%']++;
            }
        }
        
        $preparedData['approvalRateChartData'] = [
            'labels' => array_keys($rateRanges),
            'datasets' => [[
                'label' => 'People by Approval Rate',
                'data' => array_values($rateRanges),
                'backgroundColor' => ['#10a37f', '#1abc9c', '#f39c12', '#ffa726', '#ff6b6b'],
                'borderColor' => '#2a2a2a',
                'borderWidth' => 1
            ]]
        ];
        
        // Prepare chart data for performance comparison
        usort($salesData, function($a, $b) {
            $approvedA = is_object($a) ? $a->total_approved : $a['total_approved'];
            $approvedB = is_object($b) ? $b->total_approved : $b['total_approved'];
            return $approvedB - $approvedA;
        });
        
        $top10 = array_slice($salesData, 0, 10);
        $topNames = [];
        $topApproved = [];
        $topCreated = [];
        
        foreach ($top10 as $person) {
            $topNames[] = is_object($person) ? $person->user_name : $person['user_name'];
            $topApproved[] = is_object($person) ? $person->total_approved : $person['total_approved'];
            $topCreated[] = is_object($person) ? $person->total_create_company : $person['total_create_company'];
        }
        
        $preparedData['performanceChartData'] = [
            'labels' => $topNames,
            'datasets' => [
                [
                    'label' => 'Created',
                    'data' => $topCreated,
                    'backgroundColor' => '#5436da',
                    'borderColor' => '#4529b5',
                    'borderWidth' => 1
                ],
                [
                    'label' => 'Approved',
                    'data' => $topApproved,
                    'backgroundColor' => '#10a37f',
                    'borderColor' => '#0d8a6a',
                    'borderWidth' => 1
                ]
            ]
        ];
        
        // Prepare table data
        $tableRows = [];
        foreach ($salesData as $person) {
            $userName = is_object($person) ? $person->user_name : $person['user_name'];
            $userId = is_object($person) ? $person->tar_user_id : $person['tar_user_id'];
            $role = is_object($person) ? $person->type_name : $person['type_name'];
            $created = is_object($person) ? $person->total_create_company : $person['total_create_company'];
            $approved = is_object($person) ? $person->total_approved : $person['total_approved'];
            $pending = is_object($person) ? $person->pending_for_approved : $person['pending_for_approved'];
            $needUpdate = is_object($person) ? $person->need_to_update : $person['need_to_update'];
            $meetings = is_object($person) ? $person->form_barg_in_meeting : $person['form_barg_in_meeting'];
            $research = is_object($person) ? $person->form_research : $person['form_research'];
            $admin = is_object($person) ? $person->admin_name : $person['admin_name'];
            
            $approvalRate = $created > 0 ? round(($approved / $created) * 100, 1) : 0;
            
            $tableRows[] = [
                $userName,
                $userId,
                $role,
                $created,
                $approved,
                $approvalRate . '%',
                $pending,
                $needUpdate,
                $meetings,
                $research,
                $admin
            ];
        }
        
        $preparedData['tableData'] = [
            'headers' => ['Name', 'User ID', 'Role', 'Created', 'Approved', 'Approval Rate', 'Pending', 'Need Update', 'Meetings', 'Research', 'Admin'],
            'rows' => $tableRows
        ];
        
        $preparedData['status'] = 'success';
        $preparedData['message'] = "Analyzed performance data for {$totalPeople} people";
        
        return $preparedData;
    }

    private function prepare_specific_request_frontend_data($specificRequest, $salesData) {
        $specificData = [];
        
        if ($specificRequest['type'] === 'sales_person') {
            $person = $specificRequest['data'];
            
            $userName = is_object($person) ? $person->user_name : $person['user_name'];
            $userId = is_object($person) ? $person->tar_user_id : $person['tar_user_id'];
            $role = is_object($person) ? $person->type_name : $person['type_name'];
            $admin = is_object($person) ? $person->admin_name : $person['admin_name'];
            $created = is_object($person) ? $person->total_create_company : $person['total_create_company'];
            $approved = is_object($person) ? $person->total_approved : $person['total_approved'];
            $pending = is_object($person) ? $person->pending_for_approved : $person['pending_for_approved'];
            $needUpdate = is_object($person) ? $person->need_to_update : $person['need_to_update'];
            $meetings = is_object($person) ? $person->form_barg_in_meeting : $person['form_barg_in_meeting'];
            $research = is_object($person) ? $person->form_research : $person['form_research'];
            
            $approvalRate = $created > 0 ? round(($approved / $created) * 100, 1) : 0;
            $meetingRate = $created > 0 ? round(($meetings / $created) * 100, 1) : 0;
            $pendingRate = $created > 0 ? round(($pending / $created) * 100, 1) : 0;
            
            // Get comparison data
            $otherSalesData = array_filter($salesData, function($p) use ($userName) {
                return (is_object($p) ? $p->user_name : $p['user_name']) !== $userName;
            });
            
            $avgCreated = 0;
            $avgApproved = 0;
            if (count($otherSalesData) > 0) {
                $avgCreated = round(array_sum(array_map(function($p) {
                    return is_object($p) ? $p->total_create_company : $p['total_create_company'];
                }, $otherSalesData)) / count($otherSalesData), 1);
                
                $avgApproved = round(array_sum(array_map(function($p) {
                    return is_object($p) ? $p->total_approved : $p['total_approved'];
                }, $otherSalesData)) / count($otherSalesData), 1);
            }
            
            $specificData = [
                'type' => 'sales_person',
                'details' => [
                    'name' => $userName,
                    'user_id' => $userId,
                    'role' => $role,
                    'admin' => $admin
                ],
                'performance' => [
                    'created' => $created,
                    'approved' => $approved,
                    'approval_rate' => $approvalRate,
                    'pending' => $pending,
                    'pending_rate' => $pendingRate,
                    'need_update' => $needUpdate,
                    'meetings' => $meetings,
                    'meeting_rate' => $meetingRate,
                    'research' => $research
                ],
                'comparison' => [
                    'peer_avg_created' => $avgCreated,
                    'peer_avg_approved' => $avgApproved,
                    'vs_avg_created' => round($created - $avgCreated, 1),
                    'vs_avg_approved' => round($approved - $avgApproved, 1)
                ],
                'performance_assessment' => $this->assess_performance($approvalRate, $created, $approved)
            ];
            
        } elseif ($specificRequest['type'] === 'admin') {
            $person = $specificRequest['data'];
            $adminName = is_object($person) ? $person->admin_name : $person['admin_name'];
            
            // Get all team members
            $teamMembers = array_filter($salesData, function($p) use ($adminName) {
                $pAdminName = is_object($p) ? $p->admin_name : $p['admin_name'];
                return $pAdminName === $adminName;
            });
            
            // Calculate team totals
            $teamCreated = 0;
            $teamApproved = 0;
            $teamPending = 0;
            $teamNeedUpdate = 0;
            $teamMeetings = 0;
            $teamResearch = 0;
            
            $teamPerformance = [];
            foreach ($teamMembers as $member) {
                $userName = is_object($member) ? $member->user_name : $member['user_name'];
                $created = is_object($member) ? $member->total_create_company : $member['total_create_company'];
                $approved = is_object($member) ? $member->total_approved : $member['total_approved'];
                $approvalRate = $created > 0 ? round(($approved / $created) * 100, 1) : 0;
                
                $teamCreated += $created;
                $teamApproved += $approved;
                $teamPending += is_object($member) ? $member->pending_for_approved : $member['pending_for_approved'];
                $teamNeedUpdate += is_object($member) ? $member->need_to_update : $member['need_to_update'];
                $teamMeetings += is_object($member) ? $member->form_barg_in_meeting : $member['form_barg_in_meeting'];
                $teamResearch += is_object($member) ? $member->form_research : $member['form_research'];
                
                $teamPerformance[$userName] = [
                    'created' => $created,
                    'approved' => $approved,
                    'approval_rate' => $approvalRate
                ];
            }
            
            $teamApprovalRate = $teamCreated > 0 ? round(($teamApproved / $teamCreated) * 100, 1) : 0;
            $teamMeetingRate = $teamCreated > 0 ? round(($teamMeetings / $teamCreated) * 100, 1) : 0;
            
            // Sort team members by performance
            uasort($teamPerformance, function($a, $b) {
                return $b['approved'] - $a['approved'];
            });
            
            $specificData = [
                'type' => 'admin',
                'admin_name' => $adminName,
                'team_size' => count($teamMembers),
                'team_performance' => [
                    'total_created' => $teamCreated,
                    'total_approved' => $teamApproved,
                    'approval_rate' => $teamApprovalRate,
                    'total_pending' => $teamPending,
                    'total_need_update' => $teamNeedUpdate,
                    'total_meetings' => $teamMeetings,
                    'meeting_rate' => $teamMeetingRate,
                    'total_research' => $teamResearch
                ],
                'team_members' => $teamPerformance,
                'top_performers' => array_slice($teamPerformance, 0, 3, true)
            ];
            
        } elseif ($specificRequest['type'] === 'role') {
            $role = $specificRequest['data'];
            $roleData = array_filter($salesData, function($person) use ($role) {
                $personRole = strtolower(is_object($person) ? $person->type_name : $person['type_name']);
                return strpos($personRole, $role) !== false;
            });
            
            // Calculate role statistics
            $totalCreated = 0;
            $totalApproved = 0;
            $performanceData = [];
            
            foreach ($roleData as $person) {
                $userName = is_object($person) ? $person->user_name : $person['user_name'];
                $created = is_object($person) ? $person->total_create_company : $person['total_create_company'];
                $approved = is_object($person) ? $person->total_approved : $person['total_approved'];
                $approvalRate = $created > 0 ? round(($approved / $created) * 100, 1) : 0;
                
                $totalCreated += $created;
                $totalApproved += $approved;
                
                $performanceData[$userName] = [
                    'created' => $created,
                    'approved' => $approved,
                    'approval_rate' => $approvalRate
                ];
            }
            
            $avgCreated = count($roleData) > 0 ? round($totalCreated / count($roleData), 1) : 0;
            $avgApproved = count($roleData) > 0 ? round($totalApproved / count($roleData), 1) : 0;
            $overallApprovalRate = $totalCreated > 0 ? round(($totalApproved / $totalCreated) * 100, 1) : 0;
            
            // Sort by performance
            uasort($performanceData, function($a, $b) {
                return $b['approved'] - $a['approved'];
            });
            
            $specificData = [
                'type' => 'role',
                'role_name' => ucfirst($role),
                'total_people' => count($roleData),
                'statistics' => [
                    'total_created' => $totalCreated,
                    'total_approved' => $totalApproved,
                    'overall_approval_rate' => $overallApprovalRate,
                    'avg_created_per_person' => $avgCreated,
                    'avg_approved_per_person' => $avgApproved
                ],
                'performance_data' => $performanceData,
                'top_performers' => array_slice($performanceData, 0, 5, true)
            ];
            
        } elseif ($specificRequest['type'] === 'user_id') {
            $person = $specificRequest['data'];
            
            $specificData = [
                'type' => 'user_id',
                'user_details' => [
                    'name' => is_object($person) ? $person->user_name : $person['user_name'],
                    'user_id' => is_object($person) ? $person->tar_user_id : $person['tar_user_id'],
                    'role' => is_object($person) ? $person->type_name : $person['type_name'],
                    'admin' => is_object($person) ? $person->admin_name : $person['admin_name']
                ],
                'performance_metrics' => [
                    'created' => is_object($person) ? $person->total_create_company : $person['total_create_company'],
                    'approved' => is_object($person) ? $person->total_approved : $person['total_approved'],
                    'pending' => is_object($person) ? $person->pending_for_approved : $person['pending_for_approved'],
                    'need_update' => is_object($person) ? $person->need_to_update : $person['need_to_update'],
                    'meetings' => is_object($person) ? $person->form_barg_in_meeting : $person['form_barg_in_meeting'],
                    'research' => is_object($person) ? $person->form_research : $person['form_research']
                ]
            ];
            
            // Calculate rates
            $created = $specificData['performance_metrics']['created'];
            $approved = $specificData['performance_metrics']['approved'];
            
            if ($created > 0) {
                $specificData['performance_rates'] = [
                    'approval_rate' => round(($approved / $created) * 100, 1),
                    'meeting_rate' => round(($specificData['performance_metrics']['meetings'] / $created) * 100, 1),
                    'pending_rate' => round(($specificData['performance_metrics']['pending'] / $created) * 100, 1),
                    'update_rate' => round(($specificData['performance_metrics']['need_update'] / $created) * 100, 1)
                ];
            }
            
        } elseif ($specificRequest['type'] === 'metric') {
            $metric = $specificRequest['data'];
            
            // Sort by metric
            usort($salesData, function($a, $b) use ($metric) {
                $valueA = is_object($a) ? $a->$metric : $a[$metric];
                $valueB = is_object($b) ? $b->$metric : $b[$metric];
                return $valueB - $valueA;
            });
            
            // Get values
            $values = array_map(function($person) use ($metric) {
                return is_object($person) ? $person->$metric : $person[$metric];
            }, $salesData);
            
            $specificData = [
                'type' => 'metric',
                'metric_name' => str_replace('_', ' ', $metric),
                'statistics' => [
                    'total' => array_sum($values),
                    'average' => round(array_sum($values) / count($values), 1),
                    'maximum' => max($values),
                    'minimum' => min($values),
                    'median' => $this->calculate_median($values)
                ],
                'top_performers' => []
            ];
            
            // Get top 10 performers for this metric
            $top10 = array_slice($salesData, 0, 10);
            foreach ($top10 as $person) {
                $specificData['top_performers'][] = [
                    'name' => is_object($person) ? $person->user_name : $person['user_name'],
                    'value' => is_object($person) ? $person->$metric : $person[$metric],
                    'role' => is_object($person) ? $person->type_name : $person['type_name'],
                    'admin' => is_object($person) ? $person->admin_name : $person['admin_name']
                ];
            }
        }
        
        return $specificData;
    }

    private function assess_performance($approvalRate, $created, $approved) {
        $assessment = [
            'level' => '',
            'strengths' => [],
            'improvements' => []
        ];
        
        if ($approvalRate >= 80) {
            $assessment['level'] = 'Excellent';
            $assessment['strengths'] = ['High approval conversion', 'Effective qualification process'];
            $assessment['improvements'] = ['Maintain consistency', 'Share best practices'];
        } elseif ($approvalRate >= 60) {
            $assessment['level'] = 'Good';
            $assessment['strengths'] = ['Solid performance', 'Good approval rate'];
            $assessment['improvements'] = ['Increase approval rate', 'Reduce pending approvals'];
        } elseif ($approvalRate >= 40) {
            $assessment['level'] = 'Average';
            $assessment['strengths'] = ['Adequate performance', 'Consistent activity'];
            $assessment['improvements'] = ['Improve qualification', 'Increase approvals'];
        } else {
            $assessment['level'] = 'Needs Improvement';
            $assessment['strengths'] = ['Active in company creation'];
            $assessment['improvements'] = ['Focus on approval rate', 'Improve qualification process', 'Seek training'];
        }
        
        if ($created >= 10) {
            $assessment['strengths'][] = 'High activity level';
        }
        
        if ($approved >= 8) {
            $assessment['strengths'][] = 'Good absolute results';
        }
        
        return $assessment;
    }

    private function calculate_median($values) {
        sort($values);
        $count = count($values);
        $middle = floor(($count - 1) / 2);
        
        if ($count % 2) {
            return $values[$middle];
        } else {
            return ($values[$middle] + $values[$middle + 1]) / 2;
        }
    }

    private function get_metric_ranges($metric, $values) {
        $max = max($values);
        
        if ($max <= 10) {
            $ranges = [
                '0-2' => 0,
                '3-5' => 0,
                '6-8' => 0,
                '9-10' => 0
            ];
        } elseif ($max <= 20) {
            $ranges = [
                '0-5' => 0,
                '6-10' => 0,
                '11-15' => 0,
                '16-20' => 0
            ];
        } else {
            $ranges = [
                '0-25%' => 0,
                '26-50%' => 0,
                '51-75%' => 0,
                '76-100%' => 0
            ];
        }
        
        foreach ($values as $value) {
            if ($max <= 10) {
                if ($value <= 2) $ranges['0-2']++;
                elseif ($value <= 5) $ranges['3-5']++;
                elseif ($value <= 8) $ranges['6-8']++;
                else $ranges['9-10']++;
            } elseif ($max <= 20) {
                if ($value <= 5) $ranges['0-5']++;
                elseif ($value <= 10) $ranges['6-10']++;
                elseif ($value <= 15) $ranges['11-15']++;
                else $ranges['16-20']++;
            } else {
                $percentile = ($value / $max) * 100;
                if ($percentile <= 25) $ranges['0-25%']++;
                elseif ($percentile <= 50) $ranges['26-50%']++;
                elseif ($percentile <= 75) $ranges['51-75%']++;
                else $ranges['76-100%']++;
            }
        }
        
        return $ranges;
    }

    private function analyze_trend($correlationData) {
        if (count($correlationData) < 2) {
            return "stay relatively constant";
        }
        
        $firstKey = array_key_first($correlationData);
        $lastKey = array_key_last($correlationData);
        
        $firstRate = $correlationData[$firstKey]['total_rate'] / $correlationData[$firstKey]['count'];
        $lastRate = $correlationData[$lastKey]['total_rate'] / $correlationData[$lastKey]['count'];
        
        if ($lastRate > $firstRate + 5) {
            return "increase";
        } elseif ($lastRate < $firstRate - 5) {
            return "decrease";
        } else {
            return "stay relatively constant";
        }
    }

    private function calculate_correlation($array1, $array2) {
        if (count($array1) !== count($array2) || count($array1) < 2) {
            return 0;
        }
        
        $n = count($array1);
        $sum1 = array_sum($array1);
        $sum2 = array_sum($array2);
        
        $sum1Sq = 0;
        $sum2Sq = 0;
        $pSum = 0;
        
        for ($i = 0; $i < $n; $i++) {
            $sum1Sq += $array1[$i] * $array1[$i];
            $sum2Sq += $array2[$i] * $array2[$i];
            $pSum += $array1[$i] * $array2[$i];
        }
        
        $num = $pSum - ($sum1 * $sum2 / $n);
        $den = sqrt(($sum1Sq - pow($sum1, 2) / $n) * ($sum2Sq - pow($sum2, 2) / $n));
        
        if ($den == 0) {
            return 0;
        }
        
        return $num / $den;
    }
}