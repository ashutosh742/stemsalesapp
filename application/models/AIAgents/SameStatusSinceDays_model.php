<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class SameStatusSinceDays_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Menu_model');
        $this->load->model('Report_model');
        $this->load->model('ChatAI_model');
    }

    public function process_SameStatusSinceDays_analysis($message, $analysisType, $sdate, $edate) {
        

        if($sdate == $edate){
            $numberofdays = 30;
        }else{
            $date1 = new DateTime($sdate);
            $date2 = new DateTime($edate);
            $interval = $date1->diff($date2);
            $numberofdays = $interval->days;
        }

        // if($numberofdays < 8){
        //     $numberofdays = 8;
        // }

        // Get companies with same status for days data
        $sameStatusSinceDaysDatas = $this->Report_model->GetCompanySameStatusSinceDays($this->uid, $sdate, $edate, $this->uid, 'all', $numberofdays);


        // Check if user is asking for specific company details
        $specificRequest = $this->extract_specific_request($message, $sameStatusSinceDaysDatas);
        
        if ($specificRequest) {
            // Generate prompt for specific analysis
            $prompt = $this->generate_specific_analysis_prompt($message, $specificRequest, $sameStatusSinceDaysDatas);
        } else {
            // Generate prompt for general analysis
            $prompt = $this->generate_general_analysis_prompt($message, $sameStatusSinceDaysDatas);
        }
        
        // Get ChatGPT response
        $chatgptResponse = $this->ChatAI_model->call_chatgpt_api($prompt, []);
        
        // Prepare data for frontend
        $frontendData = $this->prepare_analysis_data($sameStatusSinceDaysDatas, $specificRequest);
        
        return [
            'content' => $chatgptResponse,
            'data' => $sameStatusSinceDaysDatas
        ];
    }

    private function extract_specific_request($message, $companiesData) {
        // Convert message to lowercase for easier matching
        $lowerMessage = strtolower($message);
        
        // Check for specific BD (Business Development) mentions
        $uniqueBDs = [];
        foreach ($companiesData as $company) {
            $bdName = strtolower($company->main_bd_name);
            if (!in_array($bdName, $uniqueBDs)) {
                $uniqueBDs[] = $bdName;
                
                if (strpos($lowerMessage, $bdName) !== false) {
                    return ['type' => 'bd', 'data' => $company];
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
        
        // Check for specific company ID mentions
        foreach ($companiesData as $company) {
            if (strpos($lowerMessage, (string)$company->cid) !== false) {
                return ['type' => 'company_id', 'data' => $company];
            }
        }
        
        // Check for specific status mentions
        $statuses = ['positive', 'reachout', 'tentative', 'ttd-reachout', 'will do later'];
        foreach ($statuses as $status) {
            if (strpos($lowerMessage, $status) !== false) {
                return ['type' => 'status', 'data' => $status];
            }
        }
        
        // Check for days threshold mentions
        if (preg_match('/\b(\d+)\s*days?\b/i', $lowerMessage, $matches)) {
            return ['type' => 'days_threshold', 'data' => (int)$matches[1]];
        }
        
        return null;
    }

    private function generate_specific_analysis_prompt($message, $specificRequest, $companiesData) {
        $prompt = "You are a CRM analytics AI analyzing companies that have been in the same status for extended periods. Provide detailed insights:\n\n";
        
        if ($specificRequest['type'] === 'bd') {
            $bd = $specificRequest['data'];
            $bdName = $bd->main_bd_name;
            
            // Get all companies for this BD
            $bdCompanies = array_filter($companiesData, function($company) use ($bdName) {
                return $company->main_bd_name === $bdName;
            });
            
            $formattedData = "SPECIFIC BD ANALYSIS - {$bdName}\n\n";
            $formattedData .= "BD DETAILS:\n";
            $formattedData .= "- BD Name: {$bdName}\n\n";
            
            $formattedData .= "COMPANIES WITH SAME STATUS FOR EXTENDED PERIODS:\n";
            $totalCompanies = count($bdCompanies);
            $formattedData .= "- Total Companies: {$totalCompanies}\n\n";
            
            $companyCount = 0;
            foreach ($bdCompanies as $company) {
                $companyCount++;
                $formattedData .= "{$companyCount}. {$company->compname}\n";
                $formattedData .= "   - Status: {$company->current_status}\n";
                $formattedData .= "   - Days in same status: {$company->days} days\n";
                $formattedData .= "   - Last task: {$company->last_task_name} on {$company->last_task_update}\n";
                $formattedData .= "   - Last review: {$company->main_review_date} ({$company->main_review_rtype})\n";
                $formattedData .= "   - Next review: {$company->review_date}\n";
                
                if ($companyCount >= 10) {
                    $remaining = $totalCompanies - 10;
                    $formattedData .= "... and {$remaining} more companies\n";
                    break;
                }
            }
            
            // Calculate statistics
            $avgDays = array_sum(array_column($bdCompanies, 'days')) / $totalCompanies;
            $maxDays = max(array_column($bdCompanies, 'days'));
            $minDays = min(array_column($bdCompanies, 'days'));
            
            $formattedData .= "\nSTATISTICS:\n";
            $formattedData .= "- Average days in same status: " . round($avgDays, 1) . "\n";
            $formattedData .= "- Maximum days: {$maxDays}\n";
            $formattedData .= "- Minimum days: {$minDays}\n";
            
            // Status distribution
            $statusCount = [];
            foreach ($bdCompanies as $company) {
                $status = $company->current_status;
                $statusCount[$status] = ($statusCount[$status] ?? 0) + 1;
            }
            
            $formattedData .= "\nSTATUS DISTRIBUTION:\n";
            foreach ($statusCount as $status => $count) {
                $percentage = round(($count / $totalCompanies) * 100, 1);
                $formattedData .= "- {$status}: {$count} companies ({$percentage}%)\n";
            }
            
            $prompt .= $formattedData;
            $prompt .= "Please provide a comprehensive analysis with:\n";
            $prompt .= "1. BD's overall performance assessment\n";
            $prompt .= "2. Analysis of stagnant statuses and potential causes\n";
            $prompt .= "3. Impact on sales pipeline and conversion rates\n";
            $prompt .= "4. Recommendations for status progression\n";
            $prompt .= "5. Prioritization of companies needing attention\n";
            $prompt .= "6. Suggested follow-up strategies\n";
            
        } elseif ($specificRequest['type'] === 'company') {
            $company = $specificRequest['data'];
            $formattedData = "SPECIFIC COMPANY ANALYSIS - {$company->compname}\n\n";
            
            $formattedData .= "COMPANY DETAILS:\n";
            $formattedData .= "- Company Name: {$company->compname}\n";
            $formattedData .= "- Company ID: {$company->cid}\n";
            $formattedData .= "- Assigned BD: {$company->main_bd_name}\n";
            $formattedData .= "- Product: {$company->pname}\n\n";
            
            $formattedData .= "STATUS HISTORY:\n";
            $formattedData .= "- Current Status: {$company->current_status}\n";
            $formattedData .= "- Days in same status: {$company->days} days\n";
            $formattedData .= "- Last status update: {$company->last_update_status_name}\n";
            $formattedData .= "- Last task performed: {$company->last_task_name}\n";
            $formattedData .= "- Last task by: {$company->task_username}\n";
            $formattedData .= "- Last task date: {$company->last_task_update}\n";
            $formattedData .= "- Task status: {$company->last_task_status}\n\n";
            
            $formattedData .= "REVIEW SCHEDULE:\n";
            $formattedData .= "- Last review: {$company->main_review_date}\n";
            $formattedData .= "- Review type: {$company->main_review_rtype}\n";
            $formattedData .= "- Next review due: {$company->review_date}\n";
            $formattedData .= "- Total login days: {$company->total_log_in_days}\n\n";
            
            // Determine status severity
            $statusSeverity = 'Moderate';
            if ($company->days > 60) {
                $statusSeverity = 'Critical';
            } elseif ($company->days > 30) {
                $statusSeverity = 'High';
            }
            
            $formattedData .= "ANALYSIS:\n";
            $formattedData .= "- Status Stagnation: {$company->days} days ({$statusSeverity} severity)\n";
            $formattedData .= "- Last activity: " . $this->calculate_time_ago($company->last_task_update) . "\n";
            
            $prompt .= $formattedData;
            $prompt .= "Please provide a comprehensive analysis with:\n";
            $prompt .= "1. Company profile and current engagement status\n";
            $prompt .= "2. Analysis of status stagnation causes\n";
            $prompt .= "3. Risk assessment for sales opportunity\n";
            $prompt .= "4. Recommended actions to progress status\n";
            $prompt .= "5. Timeline for follow-up and escalation\n";
            $prompt .= "6. Potential next steps for conversion\n";
            
        } elseif ($specificRequest['type'] === 'company_id') {
            $company = $specificRequest['data'];
            $companyId = $company->cid;
            
            $formattedData = "SPECIFIC COMPANY ID ANALYSIS - Company ID: {$companyId}\n\n";
            
            $formattedData .= "COMPANY DETAILS:\n";
            $formattedData .= "- Company Name: {$company->compname}\n";
            $formattedData .= "- Company ID: {$companyId}\n";
            $formattedData .= "- Internal ID: {$company->inid}\n";
            $formattedData .= "- Assigned BD: {$company->main_bd_name}\n\n";
            
            $formattedData .= "STATUS TIMELINE:\n";
            $formattedData .= "- Current Status: {$company->current_status}\n";
            $formattedData .= "- Status since: " . date('Y-m-d', strtotime("-{$company->days} days")) . " ({$company->days} days)\n";
            $formattedData .= "- Last status change: {$company->last_update_status_name}\n\n";
            
            $formattedData .= "RECENT ACTIVITIES:\n";
            $formattedData .= "- Last task: {$company->last_task_name}\n";
            $formattedData .= "- Task ID: {$company->last_task_id}\n";
            $formattedData .= "- Task performer: {$company->task_username}\n";
            $formattedData .= "- Task date: {$company->last_task_update}\n";
            $formattedData .= "- Task status: {$company->last_task_status}\n\n";
            
            $formattedData .= "REVIEW HISTORY:\n";
            $formattedData .= "- Last review date: {$company->main_review_date}\n";
            $formattedData .= "- Review frequency: {$company->main_review_rtype}\n";
            $formattedData .= "- Next review: {$company->review_date}\n";
            
            $prompt .= $formattedData;
            $prompt .= "Please provide a comprehensive analysis with:\n";
            $prompt .= "1. Company engagement timeline analysis\n";
            $prompt .= "2. Status progression barriers\n";
            $prompt .= "3. Activity pattern assessment\n";
            $prompt .= "4. Recommendations for re-engagement\n";
            $prompt .= "5. Escalation strategy if needed\n";
            $prompt .= "6. Success probability assessment\n";
            
        } elseif ($specificRequest['type'] === 'status') {
            $status = $specificRequest['data'];
            $statusCompanies = array_filter($companiesData, function($company) use ($status) {
                return strtolower($company->current_status) === $status;
            });
            
            $formattedData = "SPECIFIC STATUS ANALYSIS - " . ucfirst($status) . "\n\n";
            $formattedData .= "OVERVIEW:\n";
            $formattedData .= "- Status: " . ucfirst($status) . "\n";
            $formattedData .= "- Total companies: " . count($statusCompanies) . "\n\n";
            
            // Calculate statistics
            $totalDays = array_sum(array_column($statusCompanies, 'days'));
            $avgDays = count($statusCompanies) > 0 ? $totalDays / count($statusCompanies) : 0;
            $maxDays = count($statusCompanies) > 0 ? max(array_column($statusCompanies, 'days')) : 0;
            
            $formattedData .= "STATISTICS:\n";
            $formattedData .= "- Average days in this status: " . round($avgDays, 1) . "\n";
            $formattedData .= "- Maximum days: {$maxDays}\n";
            $formattedData .= "- Total cumulative days: {$totalDays}\n\n";
            
            // BD distribution for this status
            $bdDistribution = [];
            foreach ($statusCompanies as $company) {
                $bdName = $company->main_bd_name;
                $bdDistribution[$bdName] = ($bdDistribution[$bdName] ?? 0) + 1;
            }
            
            arsort($bdDistribution);
            
            $formattedData .= "TOP 5 BDs WITH MOST COMPANIES IN THIS STATUS:\n";
            $top5BDs = array_slice($bdDistribution, 0, 5, true);
            $rank = 1;
            foreach ($top5BDs as $bdName => $count) {
                $formattedData .= "{$rank}. {$bdName}: {$count} companies\n";
                $rank++;
            }
            
            // Companies with longest time in this status
            usort($statusCompanies, function($a, $b) {
                return $b->days - $a->days;
            });
            
            $formattedData .= "\nTOP 5 COMPANIES WITH LONGEST TIME IN THIS STATUS:\n";
            $top5Companies = array_slice($statusCompanies, 0, 5);
            foreach ($top5Companies as $index => $company) {
                $rank = $index + 1;
                $formattedData .= "{$rank}. {$company->compname} ({$company->main_bd_name}): {$company->days} days\n";
            }
            
            // Product distribution
            $productDistribution = [];
            foreach ($statusCompanies as $company) {
                $product = $company->pname;
                $productDistribution[$product] = ($productDistribution[$product] ?? 0) + 1;
            }
            
            $formattedData .= "\nPRODUCT DISTRIBUTION:\n";
            foreach ($productDistribution as $product => $count) {
                $percentage = round(($count / count($statusCompanies)) * 100, 1);
                $formattedData .= "- {$product}: {$count} companies ({$percentage}%)\n";
            }
            
            $prompt .= $formattedData;
            $prompt .= "Please provide a comprehensive analysis with:\n";
            $prompt .= "1. Status meaning and business implications\n";
            $prompt .= "2. Reasons for prolonged status duration\n";
            $prompt .= "3. Impact on sales pipeline\n";
            $prompt .= "4. Strategies for status progression\n";
            $prompt .= "5. Risk assessment for stagnant companies\n";
            $prompt .= "6. Recommended actions and timeline\n";
            
        } elseif ($specificRequest['type'] === 'days_threshold') {
            $threshold = $specificRequest['data'];
            $thresholdCompanies = array_filter($companiesData, function($company) use ($threshold) {
                return $company->days >= $threshold;
            });
            
            $formattedData = "DAYS THRESHOLD ANALYSIS - Companies in same status for {$threshold}+ days\n\n";
            
            $formattedData .= "OVERVIEW:\n";
            $formattedData .= "- Threshold: {$threshold} days\n";
            $formattedData .= "- Total companies: " . count($thresholdCompanies) . "\n";
            $formattedData .= "- Percentage of total: " . round((count($thresholdCompanies) / count($companiesData)) * 100, 1) . "%\n\n";
            
            // Status distribution
            $statusDistribution = [];
            foreach ($thresholdCompanies as $company) {
                $status = $company->current_status;
                $statusDistribution[$status] = ($statusDistribution[$status] ?? 0) + 1;
            }
            
            arsort($statusDistribution);
            
            $formattedData .= "STATUS DISTRIBUTION:\n";
            foreach ($statusDistribution as $status => $count) {
                $percentage = round(($count / count($thresholdCompanies)) * 100, 1);
                $formattedData .= "- {$status}: {$count} companies ({$percentage}%)\n";
            }
            
            // BD distribution
            $bdDistribution = [];
            foreach ($thresholdCompanies as $company) {
                $bdName = $company->main_bd_name;
                $bdDistribution[$bdName] = ($bdDistribution[$bdName] ?? 0) + 1;
            }
            
            arsort($bdDistribution);
            
            $formattedData .= "\nTOP 5 BDs WITH MOST COMPANIES ABOVE THRESHOLD:\n";
            $top5BDs = array_slice($bdDistribution, 0, 5, true);
            $rank = 1;
            foreach ($top5BDs as $bdName => $count) {
                $formattedData .= "{$rank}. {$bdName}: {$count} companies\n";
                $rank++;
            }
            
            // Longest stagnating companies
            usort($thresholdCompanies, function($a, $b) {
                return $b->days - $a->days;
            });
            
            $formattedData .= "\nTOP 5 LONGEST STAGNATING COMPANIES:\n";
            $top5Longest = array_slice($thresholdCompanies, 0, 5);
            foreach ($top5Longest as $index => $company) {
                $rank = $index + 1;
                $formattedData .= "{$rank}. {$company->compname} ({$company->main_bd_name})\n";
                $formattedData .= "   - Status: {$company->current_status}\n";
                $formattedData .= "   - Days: {$company->days}\n";
                $formattedData .= "   - Last activity: {$company->last_task_name} on {$company->last_task_update}\n";
            }
            
            // Average days by status
            $avgDaysByStatus = [];
            foreach ($thresholdCompanies as $company) {
                $status = $company->current_status;
                if (!isset($avgDaysByStatus[$status])) {
                    $avgDaysByStatus[$status] = ['total' => 0, 'count' => 0];
                }
                $avgDaysByStatus[$status]['total'] += $company->days;
                $avgDaysByStatus[$status]['count']++;
            }
            
            $formattedData .= "\nAVERAGE DAYS BY STATUS (for threshold companies):\n";
            foreach ($avgDaysByStatus as $status => $data) {
                $average = round($data['total'] / $data['count'], 1);
                $formattedData .= "- {$status}: {$average} days average\n";
            }
            
            $prompt .= $formattedData;
            $prompt .= "Please provide a comprehensive analysis with:\n";
            $prompt .= "1. Criticality assessment of stagnant companies\n";
            $prompt .= "2. Root cause analysis for prolonged status\n";
            $prompt .= "3. Impact on business outcomes\n";
            $prompt .= "4. Prioritization strategy for intervention\n";
            $prompt .= "5. Recommended escalation procedures\n";
            $prompt .= "6. Success metrics for improvement\n";
        }
        
        $prompt .= "\nUser Query: {$message}\n\n";
        $prompt .= "Format your response with clear sections, use specific numbers from the data, and provide actionable insights.";
        
        return $prompt;
    }

    private function generate_general_analysis_prompt($message, $companiesData) {
        $totalCompanies = count($companiesData);
        
        $formattedData = "SAME STATUS SINCE DAYS ANALYSIS\n\n";
        
        // Overall Summary
        $formattedData .= "OVERALL SUMMARY:\n";
        $formattedData .= "- Total Companies Analyzed: {$totalCompanies}\n";
        $formattedData .= "- Analysis Criteria: Companies in same status for 30+ days\n\n";
        
        // Days Analysis
        $totalDays = array_sum(array_column($companiesData, 'days'));
        $avgDays = $totalCompanies > 0 ? round($totalDays / $totalCompanies, 1) : 0;
        $maxDays = $totalCompanies > 0 ? max(array_column($companiesData, 'days')) : 0;
        $minDays = $totalCompanies > 0 ? min(array_column($companiesData, 'days')) : 0;
        
        // Categorize by days ranges
        $ranges = [
            '30-60 days' => 0,
            '61-90 days' => 0,
            '91-120 days' => 0,
            '121+ days' => 0
        ];
        
        foreach ($companiesData as $company) {
            $days = $company->days;
            if ($days <= 60) {
                $ranges['30-60 days']++;
            } elseif ($days <= 90) {
                $ranges['61-90 days']++;
            } elseif ($days <= 120) {
                $ranges['91-120 days']++;
            } else {
                $ranges['121+ days']++;
            }
        }
        
        $formattedData .= "DAYS ANALYSIS:\n";
        $formattedData .= "- Average days in same status: {$avgDays}\n";
        $formattedData .= "- Maximum days: {$maxDays}\n";
        $formattedData .= "- Minimum days: {$minDays}\n";
        $formattedData .= "- Total cumulative days: {$totalDays}\n\n";
        
        $formattedData .= "DAYS DISTRIBUTION:\n";
        foreach ($ranges as $range => $count) {
            $percentage = round(($count / $totalCompanies) * 100, 1);
            $formattedData .= "- {$range}: {$count} companies ({$percentage}%)\n";
        }
        
        // Status Distribution
        $statusDistribution = [];
        foreach ($companiesData as $company) {
            $status = $company->current_status;
            $statusDistribution[$status] = ($statusDistribution[$status] ?? 0) + 1;
        }
        
        arsort($statusDistribution);
        
        $formattedData .= "\nSTATUS DISTRIBUTION:\n";
        foreach ($statusDistribution as $status => $count) {
            $percentage = round(($count / $totalCompanies) * 100, 1);
            $formattedData .= "- {$status}: {$count} companies ({$percentage}%)\n";
        }
        
        // BD Distribution
        $bdDistribution = [];
        $bdDays = [];
        foreach ($companiesData as $company) {
            $bdName = $company->main_bd_name;
            $bdDistribution[$bdName] = ($bdDistribution[$bdName] ?? 0) + 1;
            
            if (!isset($bdDays[$bdName])) {
                $bdDays[$bdName] = 0;
            }
            $bdDays[$bdName] += $company->days;
        }
        
        arsort($bdDistribution);
        
        $formattedData .= "\nTOP 10 BDs WITH MOST STAGNANT COMPANIES:\n";
        $top10BDs = array_slice($bdDistribution, 0, 10, true);
        $rank = 1;
        foreach ($top10BDs as $bdName => $count) {
            $avgBdDays = $count > 0 ? round(($bdDays[$bdName] ?? 0) / $count, 1) : 0;
            $percentage = round(($count / $totalCompanies) * 100, 1);
            $formattedData .= "{$rank}. {$bdName}: {$count} companies ({$percentage}%)\n";
            $formattedData .= "   - Average days per company: {$avgBdDays}\n";
            $rank++;
        }
        
        // Product Distribution
        $productDistribution = [];
        foreach ($companiesData as $company) {
            $product = $company->pname;
            $productDistribution[$product] = ($productDistribution[$product] ?? 0) + 1;
        }
        
        $formattedData .= "\nPRODUCT DISTRIBUTION:\n";
        foreach ($productDistribution as $product => $count) {
            $percentage = round(($count / $totalCompanies) * 100, 1);
            $formattedData .= "- {$product}: {$count} companies ({$percentage}%)\n";
        }
        
        // Task Analysis
        $taskDistribution = [];
        foreach ($companiesData as $company) {
            $task = $company->last_task_name;
            $taskDistribution[$task] = ($taskDistribution[$task] ?? 0) + 1;
        }
        
        arsort($taskDistribution);
        
        $formattedData .= "\nLAST TASK PERFORMED DISTRIBUTION:\n";
        foreach ($taskDistribution as $task => $count) {
            $percentage = round(($count / $totalCompanies) * 100, 1);
            $formattedData .= "- {$task}: {$count} companies ({$percentage}%)\n";
        }
        
        // Review Type Distribution
        $reviewDistribution = [];
        foreach ($companiesData as $company) {
            $reviewType = $company->main_review_rtype;
            $reviewDistribution[$reviewType] = ($reviewDistribution[$reviewType] ?? 0) + 1;
        }
        
        $formattedData .= "\nREVIEW TYPE DISTRIBUTION:\n";
        foreach ($reviewDistribution as $reviewType => $count) {
            $percentage = round(($count / $totalCompanies) * 100, 1);
            $formattedData .= "- {$reviewType}: {$count} companies ({$percentage}%)\n";
        }
        
        // Companies with longest stagnation
        usort($companiesData, function($a, $b) {
            return $b->days - $a->days;
        });
        
        $formattedData .= "\nTOP 5 COMPANIES WITH LONGEST STATUS STAGNATION:\n";
        $top5Longest = array_slice($companiesData, 0, 5);
        foreach ($top5Longest as $index => $company) {
            $rank = $index + 1;
            $formattedData .= "{$rank}. {$company->compname}\n";
            $formattedData .= "   - BD: {$company->main_bd_name}\n";
            $formattedData .= "   - Status: {$company->current_status}\n";
            $formattedData .= "   - Days: {$company->days}\n";
            $formattedData .= "   - Last task: {$company->last_task_name} on {$company->last_task_update}\n";
        }
        
        $prompt = "You are a CRM and sales pipeline analytics AI. Analyze the following companies that have been in the same status for extended periods (30+ days) and provide comprehensive insights:\n\n";
        $prompt .= $formattedData . "\n\n";
        $prompt .= "User Query: {$message}\n\n";
        $prompt .= "Please provide a comprehensive analysis with:\n";
        $prompt .= "1. Overall pipeline health assessment\n";
        $prompt .= "2. Status stagnation patterns and root causes\n";
        $prompt .= "3. BD performance and accountability analysis\n";
        $prompt .= "4. Impact on conversion rates and revenue\n";
        $prompt .= "5. Risk assessment for stagnant opportunities\n";
        $prompt .= "6. Prioritization strategy for intervention\n";
        $prompt .= "7. Recommended actions and escalation procedures\n";
        $prompt .= "8. Process improvement suggestions\n";
        $prompt .= "9. Success metrics for improvement tracking\n\n";
        
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
        $totalDays = array_sum(array_column($companiesData, 'days'));
        $avgDays = round($totalDays / $totalCompanies, 1);
        $maxDays = max(array_column($companiesData, 'days'));
        
        // Days ranges
        $daysRanges = [
            '30-60' => 0,
            '61-90' => 0,
            '91-120' => 0,
            '121+' => 0
        ];
        
        // Status distribution
        $statusDistribution = [];
        $bdDistribution = [];
        $productDistribution = [];
        $taskDistribution = [];
        
        foreach ($companiesData as $company) {
            // Days ranges
            $days = $company->days;
            if ($days <= 60) {
                $daysRanges['30-60']++;
            } elseif ($days <= 90) {
                $daysRanges['61-90']++;
            } elseif ($days <= 120) {
                $daysRanges['91-120']++;
            } else {
                $daysRanges['121+']++;
            }
            
            // Status distribution
            $status = $company->current_status;
            $statusDistribution[$status] = ($statusDistribution[$status] ?? 0) + 1;
            
            // BD distribution
            $bd = $company->main_bd_name;
            $bdDistribution[$bd] = ($bdDistribution[$bd] ?? 0) + 1;
            
            // Product distribution
            $product = $company->pname;
            $productDistribution[$product] = ($productDistribution[$product] ?? 0) + 1;
            
            // Task distribution
            $task = $company->last_task_name;
            $taskDistribution[$task] = ($taskDistribution[$task] ?? 0) + 1;
        }
        
        arsort($statusDistribution);
        arsort($bdDistribution);
        
        // Prepare summary data
        $preparedData['summaryData'] = [
            'total_companies' => $totalCompanies,
            'total_days' => $totalDays,
            'average_days' => $avgDays,
            'maximum_days' => $maxDays,
            'days_ranges' => $daysRanges,
            'status_distribution' => $statusDistribution,
            'bd_distribution' => array_slice($bdDistribution, 0, 10, true),
            'product_distribution' => $productDistribution,
            'task_distribution' => $taskDistribution,
            'unique_bds' => count($bdDistribution),
            'unique_statuses' => count($statusDistribution)
        ];
        
        // Prepare chart data for days distribution
        $preparedData['daysChartData'] = [
            'labels' => array_keys($daysRanges),
            'datasets' => [[
                'label' => 'Companies by Days in Same Status',
                'data' => array_values($daysRanges),
                'backgroundColor' => ['#ff6b6b', '#ffa726', '#26c6da', '#5436da'],
                'borderColor' => '#2a2a2a',
                'borderWidth' => 1
            ]]
        ];
        
        // Prepare chart data for status distribution
        $statusLabels = array_keys($statusDistribution);
        $statusData = array_values($statusDistribution);
        $statusColors = ['#5436da', '#10a37f', '#ffa726', '#ff6b6b', '#8e44ad', '#f39c12'];
        
        $preparedData['statusChartData'] = [
            'labels' => $statusLabels,
            'datasets' => [[
                'label' => 'Companies by Status',
                'data' => $statusData,
                'backgroundColor' => array_slice($statusColors, 0, count($statusLabels)),
                'borderColor' => '#2a2a2a',
                'borderWidth' => 1
            ]]
        ];
        
        // Prepare chart data for top BDs
        $topBDs = array_slice($bdDistribution, 0, 10, true);
        $bdLabels = array_keys($topBDs);
        $bdData = array_values($topBDs);
        
        $preparedData['bdChartData'] = [
            'labels' => $bdLabels,
            'datasets' => [[
                'label' => 'Companies per BD',
                'data' => $bdData,
                'backgroundColor' => '#5436da',
                'borderColor' => '#4529b5',
                'borderWidth' => 1
            ]]
        ];
        
        // Prepare table data
        $tableRows = [];
        foreach ($companiesData as $company) {
            $tableRows[] = [
                $company->compname,
                $company->main_bd_name,
                $company->current_status,
                $company->days,
                $company->last_task_name,
                date('Y-m-d', strtotime($company->last_task_update)),
                $company->review_date,
                $company->pname
            ];
        }
        
        $preparedData['tableData'] = [
            'headers' => ['Company Name', 'BD Name', 'Current Status', 'Days', 'Last Task', 'Last Task Date', 'Next Review', 'Product'],
            'rows' => $tableRows
        ];
        
        $preparedData['status'] = 'success';
        $preparedData['message'] = "Analyzed {$totalCompanies} companies in same status for 30+ days";
        
        return $preparedData;
    }

    private function prepare_specific_request_frontend_data($specificRequest, $companiesData) {
        $specificData = [];
        
        if ($specificRequest['type'] === 'bd') {
            $bd = $specificRequest['data'];
            $bdName = $bd->main_bd_name;
            
            // Get all companies for this BD
            $bdCompanies = array_filter($companiesData, function($company) use ($bdName) {
                return $company->main_bd_name === $bdName;
            });
            
            $specificData = [
                'type' => 'bd',
                'bd_name' => $bdName,
                'total_companies' => count($bdCompanies),
                'companies' => [],
                'status_analysis' => [],
                'days_analysis' => [
                    'total_days' => 0,
                    'average_days' => 0,
                    'maximum_days' => 0
                ]
            ];
            
            $statusCount = [];
            $totalDays = 0;
            $maxDays = 0;
            
            foreach ($bdCompanies as $company) {
                $specificData['companies'][] = [
                    'company_name' => $company->compname,
                    'company_id' => $company->cid,
                    'status' => $company->current_status,
                    'days' => $company->days,
                    'last_task' => $company->last_task_name,
                    'last_task_date' => $company->last_task_update,
                    'next_review' => $company->review_date,
                    'product' => $company->pname
                ];
                
                // Status count
                $status = $company->current_status;
                $statusCount[$status] = ($statusCount[$status] ?? 0) + 1;
                
                // Days analysis
                $totalDays += $company->days;
                if ($company->days > $maxDays) {
                    $maxDays = $company->days;
                }
            }
            
            $specificData['status_analysis'] = $statusCount;
            $specificData['days_analysis']['total_days'] = $totalDays;
            $specificData['days_analysis']['average_days'] = count($bdCompanies) > 0 ? round($totalDays / count($bdCompanies), 1) : 0;
            $specificData['days_analysis']['maximum_days'] = $maxDays;
            
        } elseif ($specificRequest['type'] === 'company') {
            $company = $specificRequest['data'];
            $specificData = [
                'type' => 'company',
                'company_name' => $company->compname,
                'company_id' => $company->cid,
                'internal_id' => $company->inid,
                'bd_name' => $company->main_bd_name,
                'product' => $company->pname,
                'current_status' => $company->current_status,
                'days_in_status' => $company->days,
                'last_status_update' => $company->last_update_status_name,
                'last_task' => $company->last_task_name,
                'last_task_by' => $company->task_username,
                'last_task_date' => $company->last_task_update,
                'last_task_status' => $company->last_task_status,
                'last_review_date' => $company->main_review_date,
                'review_type' => $company->main_review_rtype,
                'next_review_date' => $company->review_date,
                'total_login_days' => $company->total_log_in_days,
                'last_task_id' => $company->last_task_id
            ];
            
        } elseif ($specificRequest['type'] === 'company_id') {
            $company = $specificRequest['data'];
            $specificData = [
                'type' => 'company_id',
                'company_id' => $company->cid,
                'company_name' => $company->compname,
                'internal_id' => $company->inid,
                'bd_name' => $company->main_bd_name,
                'status_timeline' => [
                    'current_status' => $company->current_status,
                    'days_in_status' => $company->days,
                    'status_since_date' => date('Y-m-d', strtotime("-{$company->days} days")),
                    'last_status_change' => $company->last_update_status_name
                ],
                'activity_log' => [
                    'last_task' => $company->last_task_name,
                    'task_id' => $company->last_task_id,
                    'performed_by' => $company->task_username,
                    'task_date' => $company->last_task_update,
                    'task_status' => $company->last_task_status
                ],
                'review_schedule' => [
                    'last_review' => $company->main_review_date,
                    'review_frequency' => $company->main_review_rtype,
                    'next_review' => $company->review_date
                ]
            ];
            
        } elseif ($specificRequest['type'] === 'status') {
            $status = $specificRequest['data'];
            $statusCompanies = array_filter($companiesData, function($company) use ($status) {
                return strtolower($company->current_status) === $status;
            });
            
            // Calculate statistics
            $totalDays = array_sum(array_column($statusCompanies, 'days'));
            $avgDays = count($statusCompanies) > 0 ? round($totalDays / count($statusCompanies), 1) : 0;
            $maxDays = count($statusCompanies) > 0 ? max(array_column($statusCompanies, 'days')) : 0;
            
            // BD distribution
            $bdDistribution = [];
            foreach ($statusCompanies as $company) {
                $bdName = $company->main_bd_name;
                $bdDistribution[$bdName] = ($bdDistribution[$bdName] ?? 0) + 1;
            }
            
            arsort($bdDistribution);
            
            // Longest companies
            usort($statusCompanies, function($a, $b) {
                return $b->days - $a->days;
            });
            
            $specificData = [
                'type' => 'status',
                'status' => ucfirst($status),
                'total_companies' => count($statusCompanies),
                'statistics' => [
                    'total_days' => $totalDays,
                    'average_days' => $avgDays,
                    'maximum_days' => $maxDays
                ],
                'bd_distribution' => array_slice($bdDistribution, 0, 5, true),
                'longest_companies' => array_slice($statusCompanies, 0, 5),
                'product_distribution' => []
            ];
            
            // Product distribution
            $productDistribution = [];
            foreach ($statusCompanies as $company) {
                $product = $company->pname;
                $productDistribution[$product] = ($productDistribution[$product] ?? 0) + 1;
            }
            
            $specificData['product_distribution'] = $productDistribution;
            
        } elseif ($specificRequest['type'] === 'days_threshold') {
            $threshold = $specificRequest['data'];
            $thresholdCompanies = array_filter($companiesData, function($company) use ($threshold) {
                return $company->days >= $threshold;
            });
            
            // Status distribution
            $statusDistribution = [];
            foreach ($thresholdCompanies as $company) {
                $status = $company->current_status;
                $statusDistribution[$status] = ($statusDistribution[$status] ?? 0) + 1;
            }
            
            arsort($statusDistribution);
            
            // BD distribution
            $bdDistribution = [];
            foreach ($thresholdCompanies as $company) {
                $bdName = $company->main_bd_name;
                $bdDistribution[$bdName] = ($bdDistribution[$bdName] ?? 0) + 1;
            }
            
            arsort($bdDistribution);
            
            // Longest companies
            usort($thresholdCompanies, function($a, $b) {
                return $b->days - $a->days;
            });
            
            $specificData = [
                'type' => 'days_threshold',
                'threshold' => $threshold,
                'total_companies' => count($thresholdCompanies),
                'percentage_of_total' => round((count($thresholdCompanies) / count($companiesData)) * 100, 1),
                'status_distribution' => $statusDistribution,
                'bd_distribution' => array_slice($bdDistribution, 0, 5, true),
                'longest_companies' => array_slice($thresholdCompanies, 0, 5),
                'average_days_by_status' => []
            ];
            
            // Average days by status
            $avgDaysByStatus = [];
            foreach ($thresholdCompanies as $company) {
                $status = $company->current_status;
                if (!isset($avgDaysByStatus[$status])) {
                    $avgDaysByStatus[$status] = ['total' => 0, 'count' => 0];
                }
                $avgDaysByStatus[$status]['total'] += $company->days;
                $avgDaysByStatus[$status]['count']++;
            }
            
            foreach ($avgDaysByStatus as $status => $data) {
                $specificData['average_days_by_status'][$status] = round($data['total'] / $data['count'], 1);
            }
        }
        
        return $specificData;
    }

    private function calculate_time_ago($datetime) {
        $time = strtotime($datetime);
        $time = time() - $time;
        
        $tokens = [
            31536000 => 'year',
            2592000 => 'month',
            604800 => 'week',
            86400 => 'day',
            3600 => 'hour',
            60 => 'minute',
            1 => 'second'
        ];
        
        foreach ($tokens as $unit => $text) {
            if ($time < $unit) continue;
            $numberOfUnits = floor($time / $unit);
            return $numberOfUnits . ' ' . $text . (($numberOfUnits > 1) ? 's' : '') . ' ago';
        }
        
        return 'Just now';
    }
}