<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CompaniesWithoutPrimaryContact_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Menu_model');
        $this->load->model('Report_model');
        $this->load->model('ChatAI_model');
    }

    public function process_CompaniesWithoutPrimaryContact_analysis($message, $analysisType, $sdate, $edate) {
        
        // Get companies without primary contact data
        $companyInfoCBD = $this->Menu_model->GetAllCompaniesThatDoNotHavePrimaryContactDetails($this->uid);

        // Check if user is asking for specific company details
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
        
        // dd($frontendData);

        return [
            'content' => $chatgptResponse,
            'data' => $companyInfoCBD
        ];
    }

    private function extract_specific_request($message, $companiesData) {
        // Convert message to lowercase for easier matching
        $lowerMessage = strtolower($message);
        
        // Check for specific user mentions
        $uniqueUsers = [];
        foreach ($companiesData as $company) {
            $userName = strtolower($company->user_name);
            if (!in_array($userName, $uniqueUsers)) {
                $uniqueUsers[] = $userName;
                
                if (strpos($lowerMessage, $userName) !== false) {
                    return ['type' => 'user', 'data' => $company];
                }
            }
        }
        
        // Check for specific company mentions
        foreach ($companiesData as $company) {
            $companyName = strtolower($company->compname);
            if (strpos($lowerMessage, $companyName) !== false) {
                return ['type' => 'company', 'data' => $company];
            }
        }
        
        // Check for specific tar_user_id mentions
        foreach ($companiesData as $company) {
            if (strpos($lowerMessage, (string)$company->tar_user_id) !== false) {
                return ['type' => 'user_id', 'data' => $company];
            }
        }
        
        // Check for contact count mentions
        if (strpos($lowerMessage, 'contact count') !== false || strpos($lowerMessage, 'no contacts') !== false) {
            return ['type' => 'contact_count', 'data' => 'summary'];
        }
        
        return null;
    }

    private function generate_specific_analysis_prompt($message, $specificRequest, $companiesData) {
        $prompt = "You are a CRM analytics AI. Analyze the following company data with missing primary contacts and provide detailed insights:\n\n";
        
        if ($specificRequest['type'] === 'user') {
            $user = $specificRequest['data'];
            $userName = $user->user_name;
            
            // Get all companies for this user
            $userCompanies = array_filter($companiesData, function($company) use ($userName) {
                return $company->user_name === $userName;
            });
            
            $formattedData = "SPECIFIC USER ANALYSIS - {$userName}\n\n";
            $formattedData .= "USER DETAILS:\n";
            $formattedData .= "- User Name: {$userName}\n";
            $formattedData .= "- User ID: {$user->tar_user_id}\n\n";
            
            $formattedData .= "COMPANIES WITHOUT PRIMARY CONTACTS ({$user->tar_user_id}):\n";
            $totalCompanies = count($userCompanies);
            $formattedData .= "- Total Companies: {$totalCompanies}\n\n";
            
            $companyCount = 0;
            foreach ($userCompanies as $company) {
                $companyCount++;
                $contactStatus = $company->contact_count > 0 ? "Has {$company->contact_count} contacts" : "No contacts";
                $formattedData .= "{$companyCount}. {$company->compname}\n";
                $formattedData .= "   - Company ID: {$company->cid}\n";
                $formattedData .= "   - Contact Status: {$contactStatus}\n";
                $formattedData .= "   - Created: {$company->created_at}\n";
                
                if ($companyCount >= 10) {
                    $remaining = $totalCompanies - 10;
                    $formattedData .= "... and {$remaining} more companies\n";
                    break;
                }
            }
            
            $prompt .= $formattedData;
            $prompt .= "Please provide a comprehensive analysis with:\n";
            $prompt .= "1. User's company portfolio overview\n";
            $prompt .= "2. Severity of missing contact information\n";
            $prompt .= "3. Impact on sales and outreach capabilities\n";
            $prompt .= "4. Recommendations for contact data collection\n";
            $prompt .= "5. Prioritization of companies needing contacts\n";
            $prompt .= "6. Estimated effort required to fix\n";
            
        } elseif ($specificRequest['type'] === 'company') {
            $company = $specificRequest['data'];
            $formattedData = "SPECIFIC COMPANY ANALYSIS - {$company->compname}\n\n";
            
            $formattedData .= "COMPANY DETAILS:\n";
            $formattedData .= "- Company Name: {$company->compname}\n";
            $formattedData .= "- Company ID: {$company->cid}\n";
            $formattedData .= "- Assigned User: {$company->user_name}\n";
            $formattedData .= "- User ID: {$company->tar_user_id}\n";
            $formattedData .= "- Created Date: {$company->created_at}\n\n";
            
            $formattedData .= "CONTACT INFORMATION STATUS:\n";
            $formattedData .= "- Current Contact Count: {$company->contact_count}\n";
            $formattedData .= "- Primary Contact: " . ($company->contact_count > 0 ? "Exists" : "MISSING") . "\n";
            $formattedData .= "- Admin Approval Status: " . ($company->is_admin_approved ? "Approved" : "Pending") . "\n\n";
            
            // Find similar companies (same user)
            $similarCompanies = array_filter($companiesData, function($comp) use ($company) {
                return $comp->user_name === $company->user_name && $comp->cid != $company->cid;
            });
            
            if (!empty($similarCompanies)) {
                $formattedData .= "OTHER COMPANIES FOR SAME USER:\n";
                $similarCount = 0;
                foreach ($similarCompanies as $similar) {
                    $similarCount++;
                    $contactStatus = $similar->contact_count > 0 ? "Has contacts" : "No contacts";
                    $formattedData .= "{$similarCount}. {$similar->compname} - {$contactStatus}\n";
                    
                    if ($similarCount >= 5) break;
                }
            }
            
            $prompt .= $formattedData;
            $prompt .= "Please provide a comprehensive analysis with:\n";
            $prompt .= "1. Company profile and importance\n";
            $prompt .= "2. Impact of missing primary contact\n";
            $prompt .= "3. Risk assessment for sales pipeline\n";
            $prompt .= "4. Suggested actions to obtain contact information\n";
            $prompt .= "5. Timeline for resolution\n";
            $prompt .= "6. Potential outreach strategies\n";
            
        } elseif ($specificRequest['type'] === 'user_id') {
            $company = $specificRequest['data'];
            $userId = $company->tar_user_id;
            
            // Get all companies for this user ID
            $userCompanies = array_filter($companiesData, function($comp) use ($userId) {
                return $comp->tar_user_id == $userId;
            });
            
            $formattedData = "SPECIFIC USER ID ANALYSIS - User ID: {$userId}\n\n";
            
            // Get user name(s) for this ID
            $userNames = array_unique(array_map(function($comp) {
                return $comp->user_name;
            }, $userCompanies));
            
            $userNameStr = implode(", ", $userNames);
            $formattedData .= "User Name(s): {$userNameStr}\n";
            $formattedData .= "Total Companies: " . count($userCompanies) . "\n\n";
            
            // Analyze contact distribution
            $companiesWithContacts = 0;
            $companiesWithoutContacts = 0;
            $totalContacts = 0;
            
            foreach ($userCompanies as $comp) {
                if ($comp->contact_count > 0) {
                    $companiesWithContacts++;
                    $totalContacts += $comp->contact_count;
                } else {
                    $companiesWithoutContacts++;
                }
            }
            
            $formattedData .= "CONTACT ANALYSIS:\n";
            $formattedData .= "- Companies with contacts: {$companiesWithContacts}\n";
            $formattedData .= "- Companies without contacts: {$companiesWithoutContacts}\n";
            $formattedData .= "- Total contacts across all companies: {$totalContacts}\n";
            
            if (count($userCompanies) > 0) {
                $percentageWithContacts = round(($companiesWithContacts / count($userCompanies)) * 100, 1);
                $percentageWithoutContacts = round(($companiesWithoutContacts / count($userCompanies)) * 100, 1);
                $formattedData .= "- % with contacts: {$percentageWithContacts}%\n";
                $formattedData .= "- % without contacts: {$percentageWithoutContacts}%\n";
            }
            
            $formattedData .= "\nTOP COMPANIES (by contact count):\n";
            
            // Sort by contact count descending
            usort($userCompanies, function($a, $b) {
                return $b->contact_count - $a->contact_count;
            });
            
            $top5 = array_slice($userCompanies, 0, 5);
            foreach ($top5 as $index => $comp) {
                $rank = $index + 1;
                $formattedData .= "{$rank}. {$comp->compname}: {$comp->contact_count} contacts\n";
            }
            
            $prompt .= $formattedData;
            $prompt .= "Please provide a comprehensive analysis with:\n";
            $prompt .= "1. User's overall data quality assessment\n";
            $prompt .= "2. Contact completeness metrics\n";
            $prompt .= "3. Impact on sales operations\n";
            $prompt .= "4. Data collection strategy recommendations\n";
            $prompt .= "5. Priority companies to focus on\n";
            $prompt .= "6. Training needs assessment\n";
            
        } elseif ($specificRequest['type'] === 'contact_count') {
            $formattedData = "CONTACT COUNT ANALYSIS - Companies Without Primary Contacts\n\n";
            
            // Group by contact count
            $contactCountDistribution = [];
            $totalCompanies = count($companiesData);
            
            foreach ($companiesData as $company) {
                $count = $company->contact_count;
                if (!isset($contactCountDistribution[$count])) {
                    $contactCountDistribution[$count] = 0;
                }
                $contactCountDistribution[$count]++;
            }
            
            ksort($contactCountDistribution);
            
            $formattedData .= "CONTACT COUNT DISTRIBUTION:\n";
            foreach ($contactCountDistribution as $count => $companyCount) {
                $percentage = round(($companyCount / $totalCompanies) * 100, 1);
                $status = $count > 0 ? "Has {$count} contacts" : "No contacts";
                $formattedData .= "- {$status}: {$companyCount} companies ({$percentage}%)\n";
            }
            
            // Companies with absolutely no contacts
            $zeroContactCompanies = array_filter($companiesData, function($company) {
                return $company->contact_count === 0;
            });
            
            $formattedData .= "\nCRITICAL ISSUE - COMPANIES WITH ZERO CONTACTS:\n";
            $formattedData .= "- Total: " . count($zeroContactCompanies) . " companies\n";
            $formattedData .= "- Percentage: " . round((count($zeroContactCompanies) / $totalCompanies) * 100, 1) . "%\n\n";
            
            // Top users with most companies without contacts
            $userStats = [];
            foreach ($companiesData as $company) {
                $userName = $company->user_name;
                if (!isset($userStats[$userName])) {
                    $userStats[$userName] = ['total' => 0, 'zero_contact' => 0];
                }
                $userStats[$userName]['total']++;
                if ($company->contact_count === 0) {
                    $userStats[$userName]['zero_contact']++;
                }
            }
            
            // Sort by zero contact count descending
            uasort($userStats, function($a, $b) {
                return $b['zero_contact'] - $a['zero_contact'];
            });
            
            $formattedData .= "TOP 5 USERS WITH MOST COMPANIES HAVING ZERO CONTACTS:\n";
            $top5Users = array_slice($userStats, 0, 5, true);
            $rank = 1;
            foreach ($top5Users as $userName => $stats) {
                $percentage = $stats['total'] > 0 ? round(($stats['zero_contact'] / $stats['total']) * 100, 1) : 0;
                $formattedData .= "{$rank}. {$userName}: {$stats['zero_contact']} of {$stats['total']} companies ({$percentage}%)\n";
                $rank++;
            }
            
            $prompt .= $formattedData;
            $prompt .= "Please provide a comprehensive analysis with:\n";
            $prompt .= "1. Overall data quality assessment\n";
            $prompt .= "2. Severity analysis by contact count\n";
            $prompt .= "3. Impact on business operations\n";
            $prompt .= "4. Root cause analysis\n";
            $prompt .= "5. Remediation plan and timeline\n";
            $prompt .= "6. Prioritization strategy\n";
            $prompt .= "7. Success metrics for improvement\n";
        }
        
        $prompt .= "\nUser Query: {$message}\n\n";
        $prompt .= "Format your response with clear sections, use specific numbers from the data, and provide actionable insights.";
        
        return $prompt;
    }

    private function generate_general_analysis_prompt($message, $companiesData) {
        $totalCompanies = count($companiesData);
        
        $formattedData = "COMPANIES WITHOUT PRIMARY CONTACT ANALYSIS\n\n";
        
        // Overall Summary
        $formattedData .= "OVERALL SUMMARY:\n";
        $formattedData .= "- Total Companies Analyzed: {$totalCompanies}\n";
        $formattedData .= "- Analysis Period: Current Data\n\n";
        
        // Contact Count Analysis
        $zeroContactCount = 0;
        $someContactCount = 0;
        $totalContacts = 0;
        
        foreach ($companiesData as $company) {
            if ($company->contact_count === 0) {
                $zeroContactCount++;
            } else {
                $someContactCount++;
                $totalContacts += $company->contact_count;
            }
        }
        
        $zeroContactPercentage = round(($zeroContactCount / $totalCompanies) * 100, 1);
        $someContactPercentage = round(($someContactCount / $totalCompanies) * 100, 1);
        
        $formattedData .= "CONTACT ANALYSIS:\n";
        $formattedData .= "- Companies with ZERO contacts: {$zeroContactCount} ({$zeroContactPercentage}%)\n";
        $formattedData .= "- Companies with SOME contacts: {$someContactCount} ({$someContactPercentage}%)\n";
        $formattedData .= "- Total contacts across all companies: {$totalContacts}\n";
        $formattedData .= "- Average contacts per company (excluding zero): " . 
                         ($someContactCount > 0 ? round($totalContacts / $someContactCount, 1) : 0) . "\n\n";
        
        // User Distribution Analysis
        $userStats = [];
        foreach ($companiesData as $company) {
            $userName = $company->user_name;
            if (!isset($userStats[$userName])) {
                $userStats[$userName] = [
                    'total' => 0,
                    'zero_contact' => 0,
                    'some_contact' => 0,
                    'total_contacts' => 0
                ];
            }
            $userStats[$userName]['total']++;
            if ($company->contact_count === 0) {
                $userStats[$userName]['zero_contact']++;
            } else {
                $userStats[$userName]['some_contact']++;
                $userStats[$userName]['total_contacts'] += $company->contact_count;
            }
        }
        
        // Sort users by total companies
        uasort($userStats, function($a, $b) {
            return $b['total'] - $a['total'];
        });
        
        $formattedData .= "TOP 10 USERS WITH MOST COMPANIES WITHOUT PRIMARY CONTACTS:\n";
        $top10Users = array_slice($userStats, 0, 10, true);
        $rank = 1;
        foreach ($top10Users as $userName => $stats) {
            $zeroPercentage = $stats['total'] > 0 ? round(($stats['zero_contact'] / $stats['total']) * 100, 1) : 0;
            $avgContacts = $stats['some_contact'] > 0 ? round($stats['total_contacts'] / $stats['some_contact'], 1) : 0;
            $formattedData .= "{$rank}. {$userName}: {$stats['total']} total companies\n";
            $formattedData .= "   - Zero contact: {$stats['zero_contact']} ({$zeroPercentage}%)\n";
            $formattedData .= "   - With contacts: {$stats['some_contact']}\n";
            $formattedData .= "   - Avg contacts: {$avgContacts}\n";
            $rank++;
        }
        
        // Date Analysis
        $dateCounts = [];
        foreach ($companiesData as $company) {
            $date = substr($company->created_at, 0, 10); // Get YYYY-MM-DD
            if (!isset($dateCounts[$date])) {
                $dateCounts[$date] = 0;
            }
            $dateCounts[$date]++;
        }
        
        // Get top 5 dates
        arsort($dateCounts);
        $top5Dates = array_slice($dateCounts, 0, 5, true);
        
        $formattedData .= "\nTOP 5 DATES WITH MOST ENTRIES:\n";
        $dateRank = 1;
        foreach ($top5Dates as $date => $count) {
            $formattedData .= "{$dateRank}. {$date}: {$count} companies\n";
            $dateRank++;
        }
        
        // Contact Distribution Details
        $contactDistribution = [];
        foreach ($companiesData as $company) {
            $count = $company->contact_count;
            if (!isset($contactDistribution[$count])) {
                $contactDistribution[$count] = 0;
            }
            $contactDistribution[$count]++;
        }
        
        ksort($contactDistribution);
        
        $formattedData .= "\nDETAILED CONTACT COUNT DISTRIBUTION:\n";
        foreach ($contactDistribution as $count => $companyCount) {
            $percentage = round(($companyCount / $totalCompanies) * 100, 1);
            $formattedData .= "- {$count} contacts: {$companyCount} companies ({$percentage}%)\n";
        }
        
        // Sample Companies with Zero Contacts
        $formattedData .= "\nSAMPLE COMPANIES WITH ZERO CONTACTS (First 5):\n";
        $zeroContactCompanies = array_filter($companiesData, function($company) {
            return $company->contact_count === 0;
        });
        
        $sampleZero = array_slice($zeroContactCompanies, 0, 5);
        foreach ($sampleZero as $index => $company) {
            $rank = $index + 1;
            $formattedData .= "{$rank}. {$company->compname} (Assigned to: {$company->user_name})\n";
        }
        
        $prompt = "You are a CRM and data quality analytics AI. Analyze the following companies with missing primary contact information and provide comprehensive insights:\n\n";
        $prompt .= $formattedData . "\n\n";
        $prompt .= "User Query: {$message}\n\n";
        $prompt .= "Please provide a comprehensive analysis with:\n";
        $prompt .= "1. Overall data quality assessment and severity\n";
        $prompt .= "2. User performance and responsibility analysis\n";
        $prompt .= "3. Impact on sales and marketing operations\n";
        $prompt .= "4. Root cause analysis for missing data\n";
        $prompt .= "5. Prioritization strategy for data collection\n";
        $prompt .= "6. Recommended actions and timeline\n";
        $prompt .= "7. Success metrics and KPIs for improvement\n";
        $prompt .= "8. Training and process improvement suggestions\n";
        $prompt .= "9. Risk assessment for business operations\n\n";
        
        $prompt .= "Format your response with clear sections, use specific numbers from the data, and provide actionable insights.";
        
        return $prompt;
    }

    private function prepare_analysis_data($companiesData, $specificRequest = null) {
        $totalCompanies = count($companiesData);
        
        $preparedData = [
            'tableData' => null,
            'summaryData' => [],
            'chartData' => null,
            'specificRequestData' => null,
            'status' => 'empty',
            'message' => 'No company data available'
        ];
        
        if ($totalCompanies === 0) {
            return $preparedData;
        }
        
        // If specific request, prepare that data
        if ($specificRequest) {
            $preparedData['specificRequestData'] = $this->prepare_specific_request_frontend_data($specificRequest, $companiesData);
        }
        
        // Calculate overall statistics
        $zeroContactCount = 0;
        $someContactCount = 0;
        $totalContacts = 0;
        $uniqueUsers = [];
        $userStats = [];
        
        foreach ($companiesData as $company) {
            // Count zero vs some contacts
            if ($company->contact_count === 0) {
                $zeroContactCount++;
            } else {
                $someContactCount++;
                $totalContacts += $company->contact_count;
            }
            
            // Track unique users
            if (!in_array($company->user_name, $uniqueUsers)) {
                $uniqueUsers[] = $company->user_name;
            }
            
            // Build user statistics
            if (!isset($userStats[$company->user_name])) {
                $userStats[$company->user_name] = [
                    'total_companies' => 0,
                    'zero_contact_companies' => 0,
                    'total_contacts' => 0
                ];
            }
            
            $userStats[$company->user_name]['total_companies']++;
            if ($company->contact_count === 0) {
                $userStats[$company->user_name]['zero_contact_companies']++;
            }
            $userStats[$company->user_name]['total_contacts'] += $company->contact_count;
        }
        
        // Contact distribution
        $contactDistribution = [];
        foreach ($companiesData as $company) {
            $count = $company->contact_count;
            if (!isset($contactDistribution[$count])) {
                $contactDistribution[$count] = 0;
            }
            $contactDistribution[$count]++;
        }
        
        ksort($contactDistribution);
        
        // Date distribution
        $dateDistribution = [];
        foreach ($companiesData as $company) {
            $date = substr($company->created_at, 0, 10); // YYYY-MM-DD
            if (!isset($dateDistribution[$date])) {
                $dateDistribution[$date] = 0;
            }
            $dateDistribution[$date]++;
        }
        
        arsort($dateDistribution);
        
        // Prepare summary data
        $preparedData['summaryData'] = [
            'total_companies' => $totalCompanies,
            'unique_users' => count($uniqueUsers),
            'zero_contact_companies' => $zeroContactCount,
            'some_contact_companies' => $someContactCount,
            'zero_contact_percentage' => round(($zeroContactCount / $totalCompanies) * 100, 1),
            'some_contact_percentage' => round(($someContactCount / $totalCompanies) * 100, 1),
            'total_contacts' => $totalContacts,
            'avg_contacts_per_company' => $someContactCount > 0 ? round($totalContacts / $someContactCount, 1) : 0,
            'user_statistics' => $userStats,
            'contact_distribution' => $contactDistribution,
            'date_distribution' => array_slice($dateDistribution, 0, 10, true)
        ];
        
        // Prepare chart data for contact distribution
        if (!empty($contactDistribution)) {
            $contactLabels = [];
            $contactData = [];
            $contactColors = [];
            
            $colorPalette = ['#ff6b6b', '#ffa726', '#26c6da', '#5436da', '#10a37f', '#8e44ad', '#f39c12', '#2c3e50'];
            
            $i = 0;
            foreach ($contactDistribution as $count => $companyCount) {
                $contactLabels[] = $count === 0 ? "No contacts" : "{$count} contacts";
                $contactData[] = $companyCount;
                $contactColors[] = $count === 0 ? '#ff6b6b' : $colorPalette[$i % count($colorPalette)];
                $i++;
            }
            
            $preparedData['contactChartData'] = [
                'labels' => $contactLabels,
                'datasets' => [[
                    'label' => 'Companies by Contact Count',
                    'data' => $contactData,
                    'backgroundColor' => $contactColors,
                    'borderColor' => '#2a2a2a',
                    'borderWidth' => 1
                ]]
            ];
        }
        
        // Prepare chart data for top users
        if (!empty($userStats)) {
            // Sort by total companies
            uasort($userStats, function($a, $b) {
                return $b['total_companies'] - $a['total_companies'];
            });
            
            $top10Users = array_slice($userStats, 0, 10, true);
            
            $userLabels = [];
            $userTotalData = [];
            $userZeroContactData = [];
            
            foreach ($top10Users as $userName => $stats) {
                $userLabels[] = $userName;
                $userTotalData[] = $stats['total_companies'];
                $userZeroContactData[] = $stats['zero_contact_companies'];
            }
            
            $preparedData['userChartData'] = [
                'labels' => $userLabels,
                'datasets' => [
                    [
                        'label' => 'Total Companies',
                        'data' => $userTotalData,
                        'backgroundColor' => '#5436da',
                        'borderColor' => '#4529b5',
                        'borderWidth' => 1
                    ],
                    [
                        'label' => 'Companies with Zero Contacts',
                        'data' => $userZeroContactData,
                        'backgroundColor' => '#ff6b6b',
                        'borderColor' => '#e05555',
                        'borderWidth' => 1
                    ]
                ]
            ];
        }
        
        // Prepare table data
        $tableRows = [];
        foreach ($companiesData as $company) {
            $contactStatus = $company->contact_count === 0 ? 'No contacts' : "Has {$company->contact_count} contacts";
            
            $tableRows[] = [
                $company->compname,
                $company->user_name,
                $company->tar_user_id,
                $contactStatus,
                $company->cid,
                $company->created_at,
                $company->is_admin_approved ? 'Approved' : 'Pending'
            ];
        }
        
        $preparedData['tableData'] = [
            'headers' => ['Company Name', 'Assigned User', 'User ID', 'Contact Status', 'Company ID', 'Created Date', 'Admin Status'],
            'rows' => $tableRows
        ];
        
        $preparedData['status'] = 'success';
        $preparedData['message'] = "Analyzed {$totalCompanies} companies without primary contacts";
        
        return $preparedData;
    }

    private function prepare_specific_request_frontend_data($specificRequest, $companiesData) {
        $specificData = [];
        
        if ($specificRequest['type'] === 'user') {
            $user = $specificRequest['data'];
            $userName = $user->user_name;
            
            // Get all companies for this user
            $userCompanies = array_filter($companiesData, function($company) use ($userName) {
                return $company->user_name === $userName;
            });
            
            $specificData = [
                'type' => 'user',
                'user_name' => $userName,
                'user_id' => $user->tar_user_id,
                'total_companies' => count($userCompanies),
                'companies' => [],
                'contact_analysis' => [
                    'zero_contact' => 0,
                    'some_contact' => 0,
                    'total_contacts' => 0
                ]
            ];
            
            foreach ($userCompanies as $company) {
                $specificData['companies'][] = [
                    'company_name' => $company->compname,
                    'company_id' => $company->cid,
                    'contact_count' => $company->contact_count,
                    'created_date' => $company->created_at,
                    'admin_approved' => $company->is_admin_approved
                ];
                
                if ($company->contact_count === 0) {
                    $specificData['contact_analysis']['zero_contact']++;
                } else {
                    $specificData['contact_analysis']['some_contact']++;
                    $specificData['contact_analysis']['total_contacts'] += $company->contact_count;
                }
            }
            
            // Calculate percentages
            $total = count($userCompanies);
            if ($total > 0) {
                $specificData['contact_analysis']['zero_contact_percentage'] = 
                    round(($specificData['contact_analysis']['zero_contact'] / $total) * 100, 1);
                $specificData['contact_analysis']['some_contact_percentage'] = 
                    round(($specificData['contact_analysis']['some_contact'] / $total) * 100, 1);
                $specificData['contact_analysis']['avg_contacts'] = 
                    $specificData['contact_analysis']['some_contact'] > 0 ? 
                    round($specificData['contact_analysis']['total_contacts'] / $specificData['contact_analysis']['some_contact'], 1) : 0;
            }
            
        } elseif ($specificRequest['type'] === 'company') {
            $company = $specificRequest['data'];
            $specificData = [
                'type' => 'company',
                'company_name' => $company->compname,
                'company_id' => $company->cid,
                'assigned_user' => $company->user_name,
                'user_id' => $company->tar_user_id,
                'contact_count' => $company->contact_count,
                'contact_status' => $company->contact_count === 0 ? 'No contacts' : "Has {$company->contact_count} contacts",
                'created_date' => $company->created_at,
                'admin_approved' => $company->is_admin_approved,
                'init_id' => $company->init_id,
                'mainbd' => $company->mainbd
            ];
            
        } elseif ($specificRequest['type'] === 'user_id') {
            $company = $specificRequest['data'];
            $userId = $company->tar_user_id;
            
            // Get all companies for this user ID
            $userCompanies = array_filter($companiesData, function($comp) use ($userId) {
                return $comp->tar_user_id == $userId;
            });
            
            // Get unique user names for this ID
            $userNames = array_unique(array_map(function($comp) {
                return $comp->user_name;
            }, $userCompanies));
            
            $specificData = [
                'type' => 'user_id',
                'user_id' => $userId,
                'user_names' => $userNames,
                'total_companies' => count($userCompanies),
                'companies' => [],
                'contact_distribution' => []
            ];
            
            $contactDistribution = [];
            foreach ($userCompanies as $comp) {
                $specificData['companies'][] = [
                    'company_name' => $comp->compname,
                    'contact_count' => $comp->contact_count,
                    'created_date' => $comp->created_at
                ];
                
                if (!isset($contactDistribution[$comp->contact_count])) {
                    $contactDistribution[$comp->contact_count] = 0;
                }
                $contactDistribution[$comp->contact_count]++;
            }
            
            ksort($contactDistribution);
            $specificData['contact_distribution'] = $contactDistribution;
            
        } elseif ($specificRequest['type'] === 'contact_count') {
            // Overall contact count analysis
            $contactDistribution = [];
            $zeroContactCompanies = [];
            $userContactStats = [];
            
            foreach ($companiesData as $company) {
                $count = $company->contact_count;
                if (!isset($contactDistribution[$count])) {
                    $contactDistribution[$count] = 0;
                }
                $contactDistribution[$count]++;
                
                if ($count === 0) {
                    $zeroContactCompanies[] = $company;
                }
                
                // User stats
                if (!isset($userContactStats[$company->user_name])) {
                    $userContactStats[$company->user_name] = [
                        'total' => 0,
                        'zero_contact' => 0
                    ];
                }
                $userContactStats[$company->user_name]['total']++;
                if ($count === 0) {
                    $userContactStats[$company->user_name]['zero_contact']++;
                }
            }
            
            ksort($contactDistribution);
            
            // Sort users by zero contact count
            uasort($userContactStats, function($a, $b) {
                return $b['zero_contact'] - $a['zero_contact'];
            });
            
            $specificData = [
                'type' => 'contact_count',
                'total_companies' => count($companiesData),
                'contact_distribution' => $contactDistribution,
                'zero_contact_companies_count' => count($zeroContactCompanies),
                'zero_contact_percentage' => round((count($zeroContactCompanies) / count($companiesData)) * 100, 1),
                'top_users_zero_contacts' => array_slice($userContactStats, 0, 10, true),
                'sample_zero_contact_companies' => array_slice($zeroContactCompanies, 0, 10)
            ];
        }
        
        return $specificData;
    }
}