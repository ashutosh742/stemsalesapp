<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class SameStatusWithTaskCount_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Menu_model');
        $this->load->model('Report_model');
        $this->load->model('ChatAI_model');
    }

    public function process_SameStatusWithTaskCount_analysis($message, $analysisType, $sdate, $edate) {
        
        // Calculate number of days
        if($sdate == $edate){
            $numberofdays = 100;
        } else {
            $date1 = new DateTime($sdate);
            $date2 = new DateTime($edate);
            $interval = $date1->diff($date2);
            $numberofdays = $interval->days;
        }

        // Get companies with same status for days data
        $sameStatusSinceDaysDatas = $this->Report_model->GetCompanyAfterLMAssignSameStatusSinceDaysTaskLogsCount($this->uid, $sdate, $edate, $this->uid, 'all', $numberofdays);

        // Check if data is empty or null
        if(empty($sameStatusSinceDaysDatas) || !is_array($sameStatusSinceDaysDatas)) {
            return [
                'content' => 'No company data found for the given period.',
                'data' => [],
                'frontendData' => [
                    'status' => 'empty',
                    'message' => 'No company data available for the selected date range.'
                ]
            ];
        }

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
            'data' => $sameStatusSinceDaysDatas,
        ];
    }

    private function extract_specific_request($message, $companiesData) {
        // Convert message to lowercase for easier matching
        $lowerMessage = strtolower($message);
        
        // Check for specific BD (Business Development) mentions
        $uniqueBDs = [];
        foreach ($companiesData as $company) {
            // Handle both object and array access
            $bdName = strtolower(is_object($company) ? $company->main_bd_name : $company['main_bd_name']);
            if (!in_array($bdName, $uniqueBDs)) {
                $uniqueBDs[] = $bdName;
                
                if (strpos($lowerMessage, $bdName) !== false) {
                    return ['type' => 'bd', 'data' => $company];
                }
            }
        }
        
        // Check for specific company mentions
        foreach ($companiesData as $company) {
            $companyName = strtolower(is_object($company) ? $company->compname : $company['compname']);
            if (strpos($lowerMessage, $companyName) !== false) {
                return ['type' => 'company', 'data' => $company];
            }
        }
        
        // Check for specific company ID mentions
        foreach ($companiesData as $company) {
            $cid = is_object($company) ? $company->cid : $company['cid'];
            if (strpos($lowerMessage, (string)$cid) !== false) {
                return ['type' => 'company_id', 'data' => $company];
            }
        }
        
        // Check for specific status mentions
        $statuses = ['positive', 'reachout', 'tentative', 'ttd-reachout', 'will do later', 'not interested', 'interested', 'contacted', 'follow up'];
        foreach ($statuses as $status) {
            if (strpos($lowerMessage, $status) !== false) {
                return ['type' => 'status', 'data' => $status];
            }
        }
        
        // Check for days threshold mentions
        if (preg_match('/\b(\d+)\s*days?\b/i', $lowerMessage, $matches)) {
            return ['type' => 'days_threshold', 'data' => (int)$matches[1]];
        }
        
        // Check for product mentions
        foreach ($companiesData as $company) {
            $product = strtolower(is_object($company) ? $company->pname : $company['pname']);
            if (strpos($lowerMessage, $product) !== false && !empty($product)) {
                return ['type' => 'product', 'data' => $product];
            }
        }
        
        return null;
    }

    private function generate_specific_analysis_prompt($message, $specificRequest, $companiesData) {
        $prompt = "You are a CRM analytics AI analyzing companies that have been in the same status for extended periods. Provide detailed insights:\n\n";
        
        if ($specificRequest['type'] === 'bd') {
            $bd = $specificRequest['data'];
            $bdName = is_object($bd) ? $bd->main_bd_name : $bd['main_bd_name'];
            
            // Get all companies for this BD
            $bdCompanies = array_filter($companiesData, function($company) use ($bdName) {
                $companyBD = is_object($company) ? $company->main_bd_name : $company['main_bd_name'];
                return $companyBD === $bdName;
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
                $compName = is_object($company) ? $company->compname : $company['compname'];
                $status = is_object($company) ? $company->current_status : $company['current_status'];
                $days = is_object($company) ? $company->days : $company['days'];
                $lastTask = is_object($company) ? $company->last_task_name : $company['last_task_name'];
                $lastTaskDate = is_object($company) ? $company->last_task_update : $company['last_task_update'];
                $lastReview = is_object($company) ? $company->main_review_date : $company['main_review_date'];
                $reviewType = is_object($company) ? $company->main_review_rtype : $company['main_review_rtype'];
                $nextReview = is_object($company) ? $company->review_date : $company['review_date'];
                
                $formattedData .= "{$companyCount}. {$compName}\n";
                $formattedData .= "   - Status: {$status}\n";
                $formattedData .= "   - Days in same status: {$days} days\n";
                $formattedData .= "   - Last task: {$lastTask} on {$lastTaskDate}\n";
                $formattedData .= "   - Last review: {$lastReview} ({$reviewType})\n";
                $formattedData .= "   - Next review: {$nextReview}\n";
                
                if ($companyCount >= 10) {
                    $remaining = $totalCompanies - 10;
                    $formattedData .= "... and {$remaining} more companies\n";
                    break;
                }
            }
            
            // Calculate statistics
            $daysArray = array_map(function($company) {
                return is_object($company) ? $company->days : $company['days'];
            }, $bdCompanies);
            
            $avgDays = $totalCompanies > 0 ? array_sum($daysArray) / $totalCompanies : 0;
            $maxDays = $totalCompanies > 0 ? max($daysArray) : 0;
            $minDays = $totalCompanies > 0 ? min($daysArray) : 0;
            
            $formattedData .= "\nSTATISTICS:\n";
            $formattedData .= "- Average days in same status: " . round($avgDays, 1) . "\n";
            $formattedData .= "- Maximum days: {$maxDays}\n";
            $formattedData .= "- Minimum days: {$minDays}\n";
            
            // Status distribution
            $statusCount = [];
            foreach ($bdCompanies as $company) {
                $status = is_object($company) ? $company->current_status : $company['current_status'];
                $statusCount[$status] = ($statusCount[$status] ?? 0) + 1;
            }
            
            $formattedData .= "\nSTATUS DISTRIBUTION:\n";
            foreach ($statusCount as $status => $count) {
                $percentage = round(($count / $totalCompanies) * 100, 1);
                $formattedData .= "- {$status}: {$count} companies ({$percentage}%)\n";
            }
            
            $prompt .= $formattedData;
            $prompt .= "\nPlease provide a comprehensive analysis with:\n";
            $prompt .= "1. BD's overall performance assessment\n";
            $prompt .= "2. Analysis of stagnant statuses and potential causes\n";
            $prompt .= "3. Impact on sales pipeline and conversion rates\n";
            $prompt .= "4. Recommendations for status progression\n";
            $prompt .= "5. Prioritization of companies needing attention\n";
            $prompt .= "6. Suggested follow-up strategies\n";
            
        } elseif ($specificRequest['type'] === 'company') {
            $company = $specificRequest['data'];
            $compName = is_object($company) ? $company->compname : $company['compname'];
            $cid = is_object($company) ? $company->cid : $company['cid'];
            $bdName = is_object($company) ? $company->main_bd_name : $company['main_bd_name'];
            $product = is_object($company) ? $company->pname : $company['pname'];
            $status = is_object($company) ? $company->current_status : $company['current_status'];
            $days = is_object($company) ? $company->days : $company['days'];
            $lastUpdateStatus = is_object($company) ? $company->last_update_status_name : $company['last_update_status_name'];
            $lastTask = is_object($company) ? $company->last_task_name : $company['last_task_name'];
            $taskUser = is_object($company) ? $company->task_username : $company['task_username'];
            $lastTaskDate = is_object($company) ? $company->last_task_update : $company['last_task_update'];
            $taskStatus = is_object($company) ? $company->last_task_status : $company['last_task_status'];
            $lastReview = is_object($company) ? $company->main_review_date : $company['main_review_date'];
            $reviewType = is_object($company) ? $company->main_review_rtype : $company['main_review_rtype'];
            $nextReview = is_object($company) ? $company->review_date : $company['review_date'];
            $totalLoginDays = is_object($company) ? $company->total_log_in_days : $company['total_log_in_days'];
            
            $formattedData = "SPECIFIC COMPANY ANALYSIS - {$compName}\n\n";
            
            $formattedData .= "COMPANY DETAILS:\n";
            $formattedData .= "- Company Name: {$compName}\n";
            $formattedData .= "- Company ID: {$cid}\n";
            $formattedData .= "- Assigned BD: {$bdName}\n";
            $formattedData .= "- Product: {$product}\n\n";
            
            $formattedData .= "STATUS HISTORY:\n";
            $formattedData .= "- Current Status: {$status}\n";
            $formattedData .= "- Days in same status: {$days} days\n";
            $formattedData .= "- Last status update: {$lastUpdateStatus}\n";
            $formattedData .= "- Last task performed: {$lastTask}\n";
            $formattedData .= "- Last task by: {$taskUser}\n";
            $formattedData .= "- Last task date: {$lastTaskDate}\n";
            $formattedData .= "- Task status: {$taskStatus}\n\n";
            
            $formattedData .= "REVIEW SCHEDULE:\n";
            $formattedData .= "- Last review: {$lastReview}\n";
            $formattedData .= "- Review type: {$reviewType}\n";
            $formattedData .= "- Next review due: {$nextReview}\n";
            $formattedData .= "- Total login days: {$totalLoginDays}\n\n";
            
            // Determine status severity
            $statusSeverity = 'Moderate';
            if ($days > 60) {
                $statusSeverity = 'Critical';
            } elseif ($days > 30) {
                $statusSeverity = 'High';
            }
            
            $formattedData .= "ANALYSIS:\n";
            $formattedData .= "- Status Stagnation: {$days} days ({$statusSeverity} severity)\n";
            $formattedData .= "- Last activity: " . $this->calculate_time_ago($lastTaskDate) . "\n";
            
            $prompt .= $formattedData;
            $prompt .= "\nPlease provide a comprehensive analysis with:\n";
            $prompt .= "1. Company profile and current engagement status\n";
            $prompt .= "2. Analysis of status stagnation causes\n";
            $prompt .= "3. Risk assessment for sales opportunity\n";
            $prompt .= "4. Recommended actions to progress status\n";
            $prompt .= "5. Timeline for follow-up and escalation\n";
            $prompt .= "6. Potential next steps for conversion\n";
            
        } elseif ($specificRequest['type'] === 'company_id') {
            $company = $specificRequest['data'];
            $cid = is_object($company) ? $company->cid : $company['cid'];
            $compName = is_object($company) ? $company->compname : $company['compname'];
            $inid = is_object($company) ? $company->inid : $company['inid'];
            $bdName = is_object($company) ? $company->main_bd_name : $company['main_bd_name'];
            $status = is_object($company) ? $company->current_status : $company['current_status'];
            $days = is_object($company) ? $company->days : $company['days'];
            $lastUpdateStatus = is_object($company) ? $company->last_update_status_name : $company['last_update_status_name'];
            $lastTask = is_object($company) ? $company->last_task_name : $company['last_task_name'];
            $taskId = is_object($company) ? $company->last_task_id : $company['last_task_id'];
            $taskUser = is_object($company) ? $company->task_username : $company['task_username'];
            $lastTaskDate = is_object($company) ? $company->last_task_update : $company['last_task_update'];
            $taskStatus = is_object($company) ? $company->last_task_status : $company['last_task_status'];
            $lastReview = is_object($company) ? $company->main_review_date : $company['main_review_date'];
            $reviewType = is_object($company) ? $company->main_review_rtype : $company['main_review_rtype'];
            $nextReview = is_object($company) ? $company->review_date : $company['review_date'];
            
            $formattedData = "SPECIFIC COMPANY ID ANALYSIS - Company ID: {$cid}\n\n";
            
            $formattedData .= "COMPANY DETAILS:\n";
            $formattedData .= "- Company Name: {$compName}\n";
            $formattedData .= "- Company ID: {$cid}\n";
            $formattedData .= "- Internal ID: {$inid}\n";
            $formattedData .= "- Assigned BD: {$bdName}\n\n";
            
            $formattedData .= "STATUS TIMELINE:\n";
            $formattedData .= "- Current Status: {$status}\n";
            $formattedData .= "- Status since: " . date('Y-m-d', strtotime("-{$days} days")) . " ({$days} days)\n";
            $formattedData .= "- Last status change: {$lastUpdateStatus}\n\n";
            
            $formattedData .= "RECENT ACTIVITIES:\n";
            $formattedData .= "- Last task: {$lastTask}\n";
            $formattedData .= "- Task ID: {$taskId}\n";
            $formattedData .= "- Task performer: {$taskUser}\n";
            $formattedData .= "- Task date: {$lastTaskDate}\n";
            $formattedData .= "- Task status: {$taskStatus}\n\n";
            
            $formattedData .= "REVIEW HISTORY:\n";
            $formattedData .= "- Last review date: {$lastReview}\n";
            $formattedData .= "- Review frequency: {$reviewType}\n";
            $formattedData .= "- Next review: {$nextReview}\n";
            
            $prompt .= $formattedData;
            $prompt .= "\nPlease provide a comprehensive analysis with:\n";
            $prompt .= "1. Company engagement timeline analysis\n";
            $prompt .= "2. Status progression barriers\n";
            $prompt .= "3. Activity pattern assessment\n";
            $prompt .= "4. Recommendations for re-engagement\n";
            $prompt .= "5. Escalation strategy if needed\n";
            $prompt .= "6. Success probability assessment\n";
            
        } elseif ($specificRequest['type'] === 'status') {
            $status = $specificRequest['data'];
            $statusCompanies = array_filter($companiesData, function($company) use ($status) {
                $companyStatus = strtolower(is_object($company) ? $company->current_status : $company['current_status']);
                return $companyStatus === $status;
            });
            
            $formattedData = "SPECIFIC STATUS ANALYSIS - " . ucfirst($status) . "\n\n";
            $formattedData .= "OVERVIEW:\n";
            $formattedData .= "- Status: " . ucfirst($status) . "\n";
            $formattedData .= "- Total companies: " . count($statusCompanies) . "\n\n";
            
            // Calculate statistics
            $daysArray = array_map(function($company) {
                return is_object($company) ? $company->days : $company['days'];
            }, $statusCompanies);
            
            $totalDays = array_sum($daysArray);
            $avgDays = count($statusCompanies) > 0 ? $totalDays / count($statusCompanies) : 0;
            $maxDays = count($statusCompanies) > 0 ? max($daysArray) : 0;
            
            $formattedData .= "STATISTICS:\n";
            $formattedData .= "- Average days in this status: " . round($avgDays, 1) . "\n";
            $formattedData .= "- Maximum days: {$maxDays}\n";
            $formattedData .= "- Total cumulative days: {$totalDays}\n\n";
            
            // BD distribution for this status
            $bdDistribution = [];
            foreach ($statusCompanies as $company) {
                $bdName = is_object($company) ? $company->main_bd_name : $company['main_bd_name'];
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
                $daysA = is_object($a) ? $a->days : $a['days'];
                $daysB = is_object($b) ? $b->days : $b['days'];
                return $daysB - $daysA;
            });
            
            $formattedData .= "\nTOP 5 COMPANIES WITH LONGEST TIME IN THIS STATUS:\n";
            $top5Companies = array_slice($statusCompanies, 0, 5);
            foreach ($top5Companies as $index => $company) {
                $rank = $index + 1;
                $compName = is_object($company) ? $company->compname : $company['compname'];
                $bdName = is_object($company) ? $company->main_bd_name : $company['main_bd_name'];
                $days = is_object($company) ? $company->days : $company['days'];
                $formattedData .= "{$rank}. {$compName} ({$bdName}): {$days} days\n";
            }
            
            // Product distribution
            $productDistribution = [];
            foreach ($statusCompanies as $company) {
                $product = is_object($company) ? $company->pname : $company['pname'];
                if (!empty($product)) {
                    $productDistribution[$product] = ($productDistribution[$product] ?? 0) + 1;
                }
            }
            
            if (!empty($productDistribution)) {
                $formattedData .= "\nPRODUCT DISTRIBUTION:\n";
                foreach ($productDistribution as $product => $count) {
                    $percentage = round(($count / count($statusCompanies)) * 100, 1);
                    $formattedData .= "- {$product}: {$count} companies ({$percentage}%)\n";
                }
            }
            
            $prompt .= $formattedData;
            $prompt .= "\nPlease provide a comprehensive analysis with:\n";
            $prompt .= "1. Status meaning and business implications\n";
            $prompt .= "2. Reasons for prolonged status duration\n";
            $prompt .= "3. Impact on sales pipeline\n";
            $prompt .= "4. Strategies for status progression\n";
            $prompt .= "5. Risk assessment for stagnant companies\n";
            $prompt .= "6. Recommended actions and timeline\n";
            
        } elseif ($specificRequest['type'] === 'days_threshold') {
            $threshold = $specificRequest['data'];
            $thresholdCompanies = array_filter($companiesData, function($company) use ($threshold) {
                $days = is_object($company) ? $company->days : $company['days'];
                return $days >= $threshold;
            });
            
            $formattedData = "DAYS THRESHOLD ANALYSIS - Companies in same status for {$threshold}+ days\n\n";
            
            $formattedData .= "OVERVIEW:\n";
            $formattedData .= "- Threshold: {$threshold} days\n";
            $formattedData .= "- Total companies: " . count($thresholdCompanies) . "\n";
            $formattedData .= "- Percentage of total: " . round((count($thresholdCompanies) / count($companiesData)) * 100, 1) . "%\n\n";
            
            // Status distribution
            $statusDistribution = [];
            foreach ($thresholdCompanies as $company) {
                $status = is_object($company) ? $company->current_status : $company['current_status'];
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
                $bdName = is_object($company) ? $company->main_bd_name : $company['main_bd_name'];
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
                $daysA = is_object($a) ? $a->days : $a['days'];
                $daysB = is_object($b) ? $b->days : $b['days'];
                return $daysB - $daysA;
            });
            
            $formattedData .= "\nTOP 5 LONGEST STAGNATING COMPANIES:\n";
            $top5Longest = array_slice($thresholdCompanies, 0, 5);
            foreach ($top5Longest as $index => $company) {
                $rank = $index + 1;
                $compName = is_object($company) ? $company->compname : $company['compname'];
                $bdName = is_object($company) ? $company->main_bd_name : $company['main_bd_name'];
                $status = is_object($company) ? $company->current_status : $company['current_status'];
                $days = is_object($company) ? $company->days : $company['days'];
                $lastTask = is_object($company) ? $company->last_task_name : $company['last_task_name'];
                $lastTaskDate = is_object($company) ? $company->last_task_update : $company['last_task_update'];
                
                $formattedData .= "{$rank}. {$compName} ({$bdName})\n";
                $formattedData .= "   - Status: {$status}\n";
                $formattedData .= "   - Days: {$days}\n";
                $formattedData .= "   - Last activity: {$lastTask} on {$lastTaskDate}\n";
            }
            
            // Average days by status
            $avgDaysByStatus = [];
            foreach ($thresholdCompanies as $company) {
                $status = is_object($company) ? $company->current_status : $company['current_status'];
                $days = is_object($company) ? $company->days : $company['days'];
                
                if (!isset($avgDaysByStatus[$status])) {
                    $avgDaysByStatus[$status] = ['total' => 0, 'count' => 0];
                }
                $avgDaysByStatus[$status]['total'] += $days;
                $avgDaysByStatus[$status]['count']++;
            }
            
            $formattedData .= "\nAVERAGE DAYS BY STATUS (for threshold companies):\n";
            foreach ($avgDaysByStatus as $status => $data) {
                $average = round($data['total'] / $data['count'], 1);
                $formattedData .= "- {$status}: {$average} days average\n";
            }
            
            $prompt .= $formattedData;
            $prompt .= "\nPlease provide a comprehensive analysis with:\n";
            $prompt .= "1. Criticality assessment of stagnant companies\n";
            $prompt .= "2. Root cause analysis for prolonged status\n";
            $prompt .= "3. Impact on business outcomes\n";
            $prompt .= "4. Prioritization strategy for intervention\n";
            $prompt .= "5. Recommended escalation procedures\n";
            $prompt .= "6. Success metrics for improvement\n";
            
        } elseif ($specificRequest['type'] === 'product') {
            $product = $specificRequest['data'];
            $productCompanies = array_filter($companiesData, function($company) use ($product) {
                $companyProduct = strtolower(is_object($company) ? $company->pname : $company['pname']);
                return $companyProduct === $product;
            });
            
            $formattedData = "SPECIFIC PRODUCT ANALYSIS - " . ucfirst($product) . "\n\n";
            $formattedData .= "OVERVIEW:\n";
            $formattedData .= "- Product: " . ucfirst($product) . "\n";
            $formattedData .= "- Total companies: " . count($productCompanies) . "\n\n";
            
            // Calculate statistics
            $daysArray = array_map(function($company) {
                return is_object($company) ? $company->days : $company['days'];
            }, $productCompanies);
            
            $totalDays = array_sum($daysArray);
            $avgDays = count($productCompanies) > 0 ? $totalDays / count($productCompanies) : 0;
            $maxDays = count($productCompanies) > 0 ? max($daysArray) : 0;
            
            $formattedData .= "STATISTICS:\n";
            $formattedData .= "- Average days in same status: " . round($avgDays, 1) . "\n";
            $formattedData .= "- Maximum days: {$maxDays}\n";
            $formattedData .= "- Total cumulative days: {$totalDays}\n\n";
            
            // Status distribution
            $statusDistribution = [];
            foreach ($productCompanies as $company) {
                $status = is_object($company) ? $company->current_status : $company['current_status'];
                $statusDistribution[$status] = ($statusDistribution[$status] ?? 0) + 1;
            }
            
            $formattedData .= "STATUS DISTRIBUTION:\n";
            foreach ($statusDistribution as $status => $count) {
                $percentage = round(($count / count($productCompanies)) * 100, 1);
                $formattedData .= "- {$status}: {$count} companies ({$percentage}%)\n";
            }
            
            $prompt .= $formattedData;
            $prompt .= "\nPlease provide a comprehensive analysis with:\n";
            $prompt .= "1. Product performance analysis\n";
            $prompt .= "2. Status stagnation patterns for this product\n";
            $prompt .= "3. Sales pipeline health for this product\n";
            $prompt .= "4. Recommendations for product-specific follow-ups\n";
            $prompt .= "5. Potential product-related issues causing stagnation\n";
            $prompt .= "6. Success metrics for product improvement\n";
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
        $formattedData .= "- Analysis Period: Companies in same status for extended periods\n\n";
        
        // Days Analysis
        $daysArray = array_map(function($company) {
            return is_object($company) ? $company->days : $company['days'];
        }, $companiesData);
        
        $totalDays = array_sum($daysArray);
        $avgDays = $totalCompanies > 0 ? round($totalDays / $totalCompanies, 1) : 0;
        $maxDays = $totalCompanies > 0 ? max($daysArray) : 0;
        $minDays = $totalCompanies > 0 ? min($daysArray) : 0;
        
        // Categorize by days ranges
        $ranges = [
            '1-30 days' => 0,
            '31-60 days' => 0,
            '61-90 days' => 0,
            '91-120 days' => 0,
            '121+ days' => 0
        ];
        
        foreach ($companiesData as $company) {
            $days = is_object($company) ? $company->days : $company['days'];
            if ($days <= 30) {
                $ranges['1-30 days']++;
            } elseif ($days <= 60) {
                $ranges['31-60 days']++;
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
            $status = is_object($company) ? $company->current_status : $company['current_status'];
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
            $bdName = is_object($company) ? $company->main_bd_name : $company['main_bd_name'];
            $days = is_object($company) ? $company->days : $company['days'];
            
            $bdDistribution[$bdName] = ($bdDistribution[$bdName] ?? 0) + 1;
            
            if (!isset($bdDays[$bdName])) {
                $bdDays[$bdName] = 0;
            }
            $bdDays[$bdName] += $days;
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
            $product = is_object($company) ? $company->pname : $company['pname'];
            if (!empty($product)) {
                $productDistribution[$product] = ($productDistribution[$product] ?? 0) + 1;
            }
        }
        
        if (!empty($productDistribution)) {
            $formattedData .= "\nPRODUCT DISTRIBUTION:\n";
            foreach ($productDistribution as $product => $count) {
                $percentage = round(($count / $totalCompanies) * 100, 1);
                $formattedData .= "- {$product}: {$count} companies ({$percentage}%)\n";
            }
        }
        
        // Task Analysis
        $taskDistribution = [];
        foreach ($companiesData as $company) {
            $task = is_object($company) ? $company->last_task_name : $company['last_task_name'];
            if (!empty($task)) {
                $taskDistribution[$task] = ($taskDistribution[$task] ?? 0) + 1;
            }
        }
        
        if (!empty($taskDistribution)) {
            arsort($taskDistribution);
            
            $formattedData .= "\nLAST TASK PERFORMED DISTRIBUTION (Top 10):\n";
            $topTasks = array_slice($taskDistribution, 0, 10, true);
            foreach ($topTasks as $task => $count) {
                $percentage = round(($count / $totalCompanies) * 100, 1);
                $formattedData .= "- {$task}: {$count} companies ({$percentage}%)\n";
            }
        }
        
        // Review Type Distribution
        $reviewDistribution = [];
        foreach ($companiesData as $company) {
            $reviewType = is_object($company) ? $company->main_review_rtype : $company['main_review_rtype'];
            if (!empty($reviewType)) {
                $reviewDistribution[$reviewType] = ($reviewDistribution[$reviewType] ?? 0) + 1;
            }
        }
        
        if (!empty($reviewDistribution)) {
            $formattedData .= "\nREVIEW TYPE DISTRIBUTION:\n";
            foreach ($reviewDistribution as $reviewType => $count) {
                $percentage = round(($count / $totalCompanies) * 100, 1);
                $formattedData .= "- {$reviewType}: {$count} companies ({$percentage}%)\n";
            }
        }
        
        // Companies with longest stagnation
        usort($companiesData, function($a, $b) {
            $daysA = is_object($a) ? $a->days : $a['days'];
            $daysB = is_object($b) ? $b->days : $b['days'];
            return $daysB - $daysA;
        });
        
        $formattedData .= "\nTOP 5 COMPANIES WITH LONGEST STATUS STAGNATION:\n";
        $top5Longest = array_slice($companiesData, 0, 5);
        foreach ($top5Longest as $index => $company) {
            $rank = $index + 1;
            $compName = is_object($company) ? $company->compname : $company['compname'];
            $bdName = is_object($company) ? $company->main_bd_name : $company['main_bd_name'];
            $status = is_object($company) ? $company->current_status : $company['current_status'];
            $days = is_object($company) ? $company->days : $company['days'];
            $lastTask = is_object($company) ? $company->last_task_name : $company['last_task_name'];
            $lastTaskDate = is_object($company) ? $company->last_task_update : $company['last_task_update'];
            
            $formattedData .= "{$rank}. {$compName}\n";
            $formattedData .= "   - BD: {$bdName}\n";
            $formattedData .= "   - Status: {$status}\n";
            $formattedData .= "   - Days: {$days}\n";
            $formattedData .= "   - Last task: {$lastTask} on {$lastTaskDate}\n";
        }
        
        $prompt = "You are a CRM and sales pipeline analytics AI. Analyze the following companies that have been in the same status for extended periods and provide comprehensive insights:\n\n";
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
        $daysArray = array_map(function($company) {
            return is_object($company) ? $company->days : $company['days'];
        }, $companiesData);
        
        $totalDays = array_sum($daysArray);
        $avgDays = round($totalDays / $totalCompanies, 1);
        $maxDays = max($daysArray);
        
        // Days ranges
        $daysRanges = [
            '1-30' => 0,
            '31-60' => 0,
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
            // Handle both object and array access
            $days = is_object($company) ? $company->days : $company['days'];
            
            // Days ranges
            if ($days <= 30) {
                $daysRanges['1-30']++;
            } elseif ($days <= 60) {
                $daysRanges['31-60']++;
            } elseif ($days <= 90) {
                $daysRanges['61-90']++;
            } elseif ($days <= 120) {
                $daysRanges['91-120']++;
            } else {
                $daysRanges['121+']++;
            }
            
            // Status distribution
            $status = is_object($company) ? $company->current_status : $company['current_status'];
            $statusDistribution[$status] = ($statusDistribution[$status] ?? 0) + 1;
            
            // BD distribution
            $bd = is_object($company) ? $company->main_bd_name : $company['main_bd_name'];
            $bdDistribution[$bd] = ($bdDistribution[$bd] ?? 0) + 1;
            
            // Product distribution
            $product = is_object($company) ? $company->pname : $company['pname'];
            if (!empty($product)) {
                $productDistribution[$product] = ($productDistribution[$product] ?? 0) + 1;
            }
            
            // Task distribution
            $task = is_object($company) ? $company->last_task_name : $company['last_task_name'];
            if (!empty($task)) {
                $taskDistribution[$task] = ($taskDistribution[$task] ?? 0) + 1;
            }
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
            'task_distribution' => array_slice($taskDistribution, 0, 10, true),
            'unique_bds' => count($bdDistribution),
            'unique_statuses' => count($statusDistribution),
            'unique_products' => count($productDistribution)
        ];
        
        // Prepare chart data for days distribution
        $preparedData['daysChartData'] = [
            'labels' => array_keys($daysRanges),
            'datasets' => [[
                'label' => 'Companies by Days in Same Status',
                'data' => array_values($daysRanges),
                'backgroundColor' => ['#ff6b6b', '#ffa726', '#26c6da', '#5436da', '#8e44ad'],
                'borderColor' => '#2a2a2a',
                'borderWidth' => 1
            ]]
        ];
        
        // Prepare chart data for status distribution
        $statusLabels = array_keys(array_slice($statusDistribution, 0, 10, true));
        $statusData = array_values(array_slice($statusDistribution, 0, 10, true));
        $statusColors = ['#5436da', '#10a37f', '#ffa726', '#ff6b6b', '#8e44ad', '#f39c12', '#1abc9c', '#e74c3c', '#3498db', '#9b59b6'];
        
        $preparedData['statusChartData'] = [
            'labels' => $statusLabels,
            'datasets' => [[
                'label' => 'Companies by Status (Top 10)',
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
                'label' => 'Companies per BD (Top 10)',
                'data' => $bdData,
                'backgroundColor' => '#5436da',
                'borderColor' => '#4529b5',
                'borderWidth' => 1
            ]]
        ];
        
        // Prepare table data
        $tableRows = [];
        foreach ($companiesData as $company) {
            $compName = is_object($company) ? $company->compname : $company['compname'];
            $bdName = is_object($company) ? $company->main_bd_name : $company['main_bd_name'];
            $status = is_object($company) ? $company->current_status : $company['current_status'];
            $days = is_object($company) ? $company->days : $company['days'];
            $lastTask = is_object($company) ? $company->last_task_name : $company['last_task_name'];
            $lastTaskDate = is_object($company) ? $company->last_task_update : $company['last_task_update'];
            $nextReview = is_object($company) ? $company->review_date : $company['review_date'];
            $product = is_object($company) ? $company->pname : $company['pname'];
            
            $tableRows[] = [
                $compName,
                $bdName,
                $status,
                $days,
                $lastTask,
                date('Y-m-d', strtotime($lastTaskDate)),
                $nextReview,
                $product
            ];
        }
        
        $preparedData['tableData'] = [
            'headers' => ['Company Name', 'BD Name', 'Current Status', 'Days', 'Last Task', 'Last Task Date', 'Next Review', 'Product'],
            'rows' => $tableRows
        ];
        
        $preparedData['status'] = 'success';
        $preparedData['message'] = "Analyzed {$totalCompanies} companies with status stagnation";
        
        return $preparedData;
    }

    private function prepare_specific_request_frontend_data($specificRequest, $companiesData) {
        $specificData = [];
        
        if ($specificRequest['type'] === 'bd') {
            $bd = $specificRequest['data'];
            $bdName = is_object($bd) ? $bd->main_bd_name : $bd['main_bd_name'];
            
            // Get all companies for this BD
            $bdCompanies = array_filter($companiesData, function($company) use ($bdName) {
                $companyBD = is_object($company) ? $company->main_bd_name : $company['main_bd_name'];
                return $companyBD === $bdName;
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
                $compName = is_object($company) ? $company->compname : $company['compname'];
                $cid = is_object($company) ? $company->cid : $company['cid'];
                $status = is_object($company) ? $company->current_status : $company['current_status'];
                $days = is_object($company) ? $company->days : $company['days'];
                $lastTask = is_object($company) ? $company->last_task_name : $company['last_task_name'];
                $lastTaskDate = is_object($company) ? $company->last_task_update : $company['last_task_update'];
                $nextReview = is_object($company) ? $company->review_date : $company['review_date'];
                $product = is_object($company) ? $company->pname : $company['pname'];
                
                $specificData['companies'][] = [
                    'company_name' => $compName,
                    'company_id' => $cid,
                    'status' => $status,
                    'days' => $days,
                    'last_task' => $lastTask,
                    'last_task_date' => $lastTaskDate,
                    'next_review' => $nextReview,
                    'product' => $product
                ];
                
                // Status count
                $statusCount[$status] = ($statusCount[$status] ?? 0) + 1;
                
                // Days analysis
                $totalDays += $days;
                if ($days > $maxDays) {
                    $maxDays = $days;
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
                'company_name' => is_object($company) ? $company->compname : $company['compname'],
                'company_id' => is_object($company) ? $company->cid : $company['cid'],
                'internal_id' => is_object($company) ? $company->inid : $company['inid'],
                'bd_name' => is_object($company) ? $company->main_bd_name : $company['main_bd_name'],
                'product' => is_object($company) ? $company->pname : $company['pname'],
                'current_status' => is_object($company) ? $company->current_status : $company['current_status'],
                'days_in_status' => is_object($company) ? $company->days : $company['days'],
                'last_status_update' => is_object($company) ? $company->last_update_status_name : $company['last_update_status_name'],
                'last_task' => is_object($company) ? $company->last_task_name : $company['last_task_name'],
                'last_task_by' => is_object($company) ? $company->task_username : $company['task_username'],
                'last_task_date' => is_object($company) ? $company->last_task_update : $company['last_task_update'],
                'last_task_status' => is_object($company) ? $company->last_task_status : $company['last_task_status'],
                'last_review_date' => is_object($company) ? $company->main_review_date : $company['main_review_date'],
                'review_type' => is_object($company) ? $company->main_review_rtype : $company['main_review_rtype'],
                'next_review_date' => is_object($company) ? $company->review_date : $company['review_date'],
                'total_login_days' => is_object($company) ? $company->total_log_in_days : $company['total_log_in_days'],
                'last_task_id' => is_object($company) ? $company->last_task_id : $company['last_task_id']
            ];
            
        } elseif ($specificRequest['type'] === 'company_id') {
            $company = $specificRequest['data'];
            
            $specificData = [
                'type' => 'company_id',
                'company_id' => is_object($company) ? $company->cid : $company['cid'],
                'company_name' => is_object($company) ? $company->compname : $company['compname'],
                'internal_id' => is_object($company) ? $company->inid : $company['inid'],
                'bd_name' => is_object($company) ? $company->main_bd_name : $company['main_bd_name'],
                'status_timeline' => [
                    'current_status' => is_object($company) ? $company->current_status : $company['current_status'],
                    'days_in_status' => is_object($company) ? $company->days : $company['days'],
                    'status_since_date' => date('Y-m-d', strtotime("-" . (is_object($company) ? $company->days : $company['days']) . " days")),
                    'last_status_change' => is_object($company) ? $company->last_update_status_name : $company['last_update_status_name']
                ],
                'activity_log' => [
                    'last_task' => is_object($company) ? $company->last_task_name : $company['last_task_name'],
                    'task_id' => is_object($company) ? $company->last_task_id : $company['last_task_id'],
                    'performed_by' => is_object($company) ? $company->task_username : $company['task_username'],
                    'task_date' => is_object($company) ? $company->last_task_update : $company['last_task_update'],
                    'task_status' => is_object($company) ? $company->last_task_status : $company['last_task_status']
                ],
                'review_schedule' => [
                    'last_review' => is_object($company) ? $company->main_review_date : $company['main_review_date'],
                    'review_frequency' => is_object($company) ? $company->main_review_rtype : $company['main_review_rtype'],
                    'next_review' => is_object($company) ? $company->review_date : $company['review_date']
                ]
            ];
            
        } elseif ($specificRequest['type'] === 'status') {
            $status = $specificRequest['data'];
            $statusCompanies = array_filter($companiesData, function($company) use ($status) {
                $companyStatus = strtolower(is_object($company) ? $company->current_status : $company['current_status']);
                return $companyStatus === $status;
            });
            
            // Calculate statistics
            $daysArray = array_map(function($company) {
                return is_object($company) ? $company->days : $company['days'];
            }, $statusCompanies);
            
            $totalDays = array_sum($daysArray);
            $avgDays = count($statusCompanies) > 0 ? round($totalDays / count($statusCompanies), 1) : 0;
            $maxDays = count($statusCompanies) > 0 ? max($daysArray) : 0;
            
            // BD distribution
            $bdDistribution = [];
            foreach ($statusCompanies as $company) {
                $bdName = is_object($company) ? $company->main_bd_name : $company['main_bd_name'];
                $bdDistribution[$bdName] = ($bdDistribution[$bdName] ?? 0) + 1;
            }
            
            arsort($bdDistribution);
            
            // Longest companies
            usort($statusCompanies, function($a, $b) {
                $daysA = is_object($a) ? $a->days : $a['days'];
                $daysB = is_object($b) ? $b->days : $b['days'];
                return $daysB - $daysA;
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
                $product = is_object($company) ? $company->pname : $company['pname'];
                if (!empty($product)) {
                    $productDistribution[$product] = ($productDistribution[$product] ?? 0) + 1;
                }
            }
            
            $specificData['product_distribution'] = $productDistribution;
            
        } elseif ($specificRequest['type'] === 'days_threshold') {
            $threshold = $specificRequest['data'];
            $thresholdCompanies = array_filter($companiesData, function($company) use ($threshold) {
                $days = is_object($company) ? $company->days : $company['days'];
                return $days >= $threshold;
            });
            
            // Status distribution
            $statusDistribution = [];
            foreach ($thresholdCompanies as $company) {
                $status = is_object($company) ? $company->current_status : $company['current_status'];
                $statusDistribution[$status] = ($statusDistribution[$status] ?? 0) + 1;
            }
            
            arsort($statusDistribution);
            
            // BD distribution
            $bdDistribution = [];
            foreach ($thresholdCompanies as $company) {
                $bdName = is_object($company) ? $company->main_bd_name : $company['main_bd_name'];
                $bdDistribution[$bdName] = ($bdDistribution[$bdName] ?? 0) + 1;
            }
            
            arsort($bdDistribution);
            
            // Longest companies
            usort($thresholdCompanies, function($a, $b) {
                $daysA = is_object($a) ? $a->days : $a['days'];
                $daysB = is_object($b) ? $b->days : $b['days'];
                return $daysB - $daysA;
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
                $status = is_object($company) ? $company->current_status : $company['current_status'];
                $days = is_object($company) ? $company->days : $company['days'];
                
                if (!isset($avgDaysByStatus[$status])) {
                    $avgDaysByStatus[$status] = ['total' => 0, 'count' => 0];
                }
                $avgDaysByStatus[$status]['total'] += $days;
                $avgDaysByStatus[$status]['count']++;
            }
            
            foreach ($avgDaysByStatus as $status => $data) {
                $specificData['average_days_by_status'][$status] = round($data['total'] / $data['count'], 1);
            }
            
        } elseif ($specificRequest['type'] === 'product') {
            $product = $specificRequest['data'];
            $productCompanies = array_filter($companiesData, function($company) use ($product) {
                $companyProduct = strtolower(is_object($company) ? $company->pname : $company['pname']);
                return $companyProduct === $product;
            });
            
            // Calculate statistics
            $daysArray = array_map(function($company) {
                return is_object($company) ? $company->days : $company['days'];
            }, $productCompanies);
            
            $totalDays = array_sum($daysArray);
            $avgDays = count($productCompanies) > 0 ? round($totalDays / count($productCompanies), 1) : 0;
            $maxDays = count($productCompanies) > 0 ? max($daysArray) : 0;
            
            // Status distribution
            $statusDistribution = [];
            foreach ($productCompanies as $company) {
                $status = is_object($company) ? $company->current_status : $company['current_status'];
                $statusDistribution[$status] = ($statusDistribution[$status] ?? 0) + 1;
            }
            
            $specificData = [
                'type' => 'product',
                'product' => ucfirst($product),
                'total_companies' => count($productCompanies),
                'statistics' => [
                    'total_days' => $totalDays,
                    'average_days' => $avgDays,
                    'maximum_days' => $maxDays
                ],
                'status_distribution' => $statusDistribution,
                'bd_distribution' => []
            ];
            
            // BD distribution
            $bdDistribution = [];
            foreach ($productCompanies as $company) {
                $bdName = is_object($company) ? $company->main_bd_name : $company['main_bd_name'];
                $bdDistribution[$bdName] = ($bdDistribution[$bdName] ?? 0) + 1;
            }
            
            arsort($bdDistribution);
            $specificData['bd_distribution'] = array_slice($bdDistribution, 0, 5, true);
        }
        
        return $specificData;
    }

    private function calculate_time_ago($datetime) {
        if (empty($datetime)) {
            return 'No recent activity';
        }
        
        $time = strtotime($datetime);
        $now = time();
        
        if ($time === false) {
            return 'Invalid date';
        }
        
        $time = $now - $time;
        
        if ($time < 1) {
            return 'Just now';
        }
        
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