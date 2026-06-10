<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class FunnelTransferLogs_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Menu_model');
        $this->load->model('Report_model');
        $this->load->model('ChatAI_model');
    }

    public function process_FunnelTransferLogs_analysis($message, $analysisType, $sdate, $edate) {
        
        // Calculate number of days
        if($sdate == $edate){
            $numberofdays = 100;
        } else {
            $date1 = new DateTime($sdate);
            $date2 = new DateTime($edate);
            $interval = $date1->diff($date2);
            $numberofdays = $interval->days;
        }

        // Fetch funnel transfer logs data between dates
        $funneltransferLogs = $this->Report_model->GetFunnelTransferLogDatas($this->uid,$sdate,$edate,$this->uid,'all');

        // Check if data is empty or null
        if(empty($funneltransferLogs) || !is_array($funneltransferLogs)) {
            return [
                'content' => 'No funnel transfer logs data found for the given period.',
                'data' => [],
                'frontendData' => [
                    'status' => 'empty',
                    'message' => 'No funnel transfer data available for the selected date range.'
                ]
            ];
        }

        // Check if user is asking for specific user details
        $specificRequest = $this->extract_specific_request($message, $funneltransferLogs);
        
        if ($specificRequest) {
            // Generate prompt for specific analysis
            $prompt = $this->generate_specific_analysis_prompt($message, $specificRequest, $funneltransferLogs);
        } else {
            // Generate prompt for general analysis
            $prompt = $this->generate_general_analysis_prompt($message, $funneltransferLogs);
        }
        
        // Get ChatGPT response
        $chatgptResponse = $this->ChatAI_model->call_chatgpt_api($prompt, []);
        
        // Prepare data for frontend
        $frontendData = $this->prepare_analysis_data($funneltransferLogs, $specificRequest);
        
        return [
            'content' => $chatgptResponse,
            'data' => $frontendData
        ];
    }

    private function extract_specific_request($message, $transferData) {
        // Convert message to lowercase for easier matching
        $lowerMessage = strtolower($message);
        
        // Check for specific "to_name" mentions
        $toNames = [];
        foreach ($transferData as $transfer) {
            $toName = strtolower($transfer->to_name);
            if (strpos($lowerMessage, $toName) !== false && !in_array($toName, $toNames)) {
                $toNames[] = $toName;
            }
        }
        
        if (!empty($toNames)) {
            $filteredData = array_filter($transferData, function($transfer) use ($toNames) {
                return in_array(strtolower($transfer->to_name), $toNames);
            });
            return ['type' => 'to_name', 'data' => array_values($filteredData)];
        }
        
        // Check for specific "by_name" mentions
        $byNames = [];
        foreach ($transferData as $transfer) {
            $byName = strtolower($transfer->by_name);
            if (strpos($lowerMessage, $byName) !== false && !in_array($byName, $byNames)) {
                $byNames[] = $byName;
            }
        }
        
        if (!empty($byNames)) {
            $filteredData = array_filter($transferData, function($transfer) use ($byNames) {
                return in_array(strtolower($transfer->by_name), $byNames);
            });
            return ['type' => 'by_name', 'data' => array_values($filteredData)];
        }
        
        // Check for specific "compname" (company) mentions
        $companyNames = [];
        foreach ($transferData as $transfer) {
            $compname = strtolower($transfer->compname);
            if (strpos($lowerMessage, $compname) !== false && !in_array($compname, $companyNames)) {
                $companyNames[] = $compname;
            }
        }
        
        if (!empty($companyNames)) {
            $filteredData = array_filter($transferData, function($transfer) use ($companyNames) {
                return in_array(strtolower($transfer->compname), $companyNames);
            });
            return ['type' => 'company', 'data' => array_values($filteredData)];
        }
        
        // Check for "form_name" mentions
        $formNames = [];
        foreach ($transferData as $transfer) {
            $formName = strtolower($transfer->form_name);
            if (strpos($lowerMessage, $formName) !== false && !in_array($formName, $formNames)) {
                $formNames[] = $formName;
            }
        }
        
        if (!empty($formNames)) {
            $filteredData = array_filter($transferData, function($transfer) use ($formNames) {
                return in_array(strtolower($transfer->form_name), $formNames);
            });
            return ['type' => 'form', 'data' => array_values($filteredData)];
        }
        
        // Check for status mentions
        $statuses = [];
        foreach ($transferData as $transfer) {
            $status = strtolower($transfer->to_status);
            if (strpos($lowerMessage, $status) !== false && !in_array($status, $statuses)) {
                $statuses[] = $status;
            }
        }
        
        if (!empty($statuses)) {
            $filteredData = array_filter($transferData, function($transfer) use ($statuses) {
                return in_array(strtolower($transfer->to_status), $statuses);
            });
            return ['type' => 'status', 'data' => array_values($filteredData)];
        }
        
        // Check for "most transfers" or "top transferrers" mentions
        if (strpos($lowerMessage, 'most transfers') !== false || 
            strpos($lowerMessage, 'highest transfer') !== false ||
            strpos($lowerMessage, 'top transfer') !== false) {
            return ['type' => 'top_transferrers', 'data' => null];
        }
        
        // Check for "least transfers" mentions
        if (strpos($lowerMessage, 'least transfers') !== false || 
            strpos($lowerMessage, 'lowest transfer') !== false) {
            return ['type' => 'low_transferrers', 'data' => null];
        }
        
        return null;
    }

    private function generate_specific_analysis_prompt($message, $specificRequest, $transferData) {
        $prompt = "You are a CRM analytics AI analyzing funnel transfer patterns between dates. Provide detailed insights about lead transfers in the sales funnel:\n\n";
        
        if ($specificRequest['type'] === 'to_name') {
            $filteredData = $specificRequest['data'];
            $toName = $filteredData[0]->to_name;
            
            $formattedData = "SPECIFIC RECIPIENT ANALYSIS - {$toName}\n\n";
            $formattedData .= "RECIPIENT DETAILS:\n";
            $formattedData .= "- Name: {$toName}\n";
            
            $formattedData .= "\nTRANSFER METRICS:\n";
            $formattedData .= "- Total Transfers Received: " . count($filteredData) . "\n";
            
            // Get unique transferrers
            $transferrers = [];
            $companies = [];
            $statuses = [];
            $forms = [];
            
            foreach ($filteredData as $transfer) {
                if (!in_array($transfer->by_name, $transferrers)) {
                    $transferrers[] = $transfer->by_name;
                }
                if (!in_array($transfer->compname, $companies)) {
                    $companies[] = $transfer->compname;
                }
                if (!in_array($transfer->to_status, $statuses)) {
                    $statuses[] = $transfer->to_status;
                }
                if (!in_array($transfer->form_name, $forms)) {
                    $forms[] = $transfer->form_name;
                }
            }
            
            $formattedData .= "\nANALYSIS BREAKDOWN:\n";
            $formattedData .= "- Transferrers: " . count($transferrers) . " unique users\n";
            $formattedData .= "- Companies: " . count($companies) . " unique companies\n";
            $formattedData .= "- Statuses: " . implode(', ', $statuses) . "\n";
            $formattedData .= "- Sources: " . implode(', ', $forms) . "\n";
            
            // Get date range
            $dates = array_column($filteredData, 'created_at');
            usort($dates, function($a, $b) {
                return strtotime($a) - strtotime($b);
            });
            
            if (!empty($dates)) {
                $formattedData .= "\nTIME FRAME:\n";
                $formattedData .= "- First Transfer: " . $dates[0] . "\n";
                $formattedData .= "- Last Transfer: " . $dates[count($dates)-1] . "\n";
            }
            
            $prompt .= $formattedData;
            $prompt .= "\nPlease provide a comprehensive analysis with:\n";
            $prompt .= "1. Transfer pattern analysis for this recipient\n";
            $prompt .= "2. Types of leads being transferred to them\n";
            $prompt .= "3. Potential role/specialization based on transfers\n";
            $prompt .= "4. Workload implications\n";
            $prompt .= "5. Recommendations for managing transferred leads\n";
            
        } elseif ($specificRequest['type'] === 'by_name') {
            $filteredData = $specificRequest['data'];
            $byName = $filteredData[0]->by_name;
            
            $formattedData = "SPECIFIC TRANSFERRER ANALYSIS - {$byName}\n\n";
            $formattedData .= "TRANSFERRER DETAILS:\n";
            $formattedData .= "- Name: {$byName}\n";
            
            $formattedData .= "\nTRANSFER METRICS:\n";
            $formattedData .= "- Total Transfers Made: " . count($filteredData) . "\n";
            
            // Get unique recipients
            $recipients = [];
            $companies = [];
            $statuses = [];
            
            foreach ($filteredData as $transfer) {
                if (!in_array($transfer->to_name, $recipients)) {
                    $recipients[] = $transfer->to_name;
                }
                if (!in_array($transfer->compname, $companies)) {
                    $companies[] = $transfer->compname;
                }
                if (!in_array($transfer->to_status, $statuses)) {
                    $statuses[] = $transfer->to_status;
                }
            }
            
            $formattedData .= "\nANALYSIS BREAKDOWN:\n";
            $formattedData .= "- Recipients: " . count($recipients) . " unique users\n";
            $formattedData .= "- Companies: " . count($companies) . " unique companies\n";
            $formattedData .= "- Statuses Applied: " . implode(', ', $statuses) . "\n";
            $formattedData .= "- Primary Recipients: " . implode(', ', array_slice($recipients, 0, 3)) . "\n";
            
            // Get date range
            $dates = array_column($filteredData, 'created_at');
            usort($dates, function($a, $b) {
                return strtotime($a) - strtotime($b);
            });
            
            if (!empty($dates)) {
                $formattedData .= "\nTIME FRAME:\n";
                $formattedData .= "- First Transfer: " . $dates[0] . "\n";
                $formattedData .= "- Last Transfer: " . $dates[count($dates)-1] . "\n";
            }
            
            $prompt .= $formattedData;
            $prompt .= "\nPlease provide a comprehensive analysis with:\n";
            $prompt .= "1. Transfer pattern analysis for this user\n";
            $prompt .= "2. Types of leads being transferred\n";
            $prompt .= "3. Potential role in lead distribution\n";
            $prompt .= "4. Coordination and delegation patterns\n";
            $prompt .= "5. Recommendations for transfer practices\n";
            
        } elseif ($specificRequest['type'] === 'company') {
            $filteredData = $specificRequest['data'];
            $companyName = $filteredData[0]->compname;
            
            $formattedData = "SPECIFIC COMPANY ANALYSIS - {$companyName}\n\n";
            $formattedData .= "COMPANY DETAILS:\n";
            $formattedData .= "- Name: {$companyName}\n";
            $formattedData .= "- Company ID: {$filteredData[0]->cid}\n";
            
            $formattedData .= "\nTRANSFER HISTORY:\n";
            $formattedData .= "- Total Transfers: " . count($filteredData) . "\n";
            
            $recipients = [];
            $transferrers = [];
            $statuses = [];
            
            foreach ($filteredData as $transfer) {
                if (!in_array($transfer->to_name, $recipients)) {
                    $recipients[] = $transfer->to_name;
                }
                if (!in_array($transfer->by_name, $transferrers)) {
                    $transferrers[] = $transfer->by_name;
                }
                if (!in_array($transfer->to_status, $statuses)) {
                    $statuses[] = $transfer->to_status;
                }
            }
            
            $formattedData .= "\nANALYSIS BREAKDOWN:\n";
            $formattedData .= "- Recipients: " . count($recipients) . " unique users\n";
            $formattedData .= "- Transferrers: " . count($transferrers) . " unique users\n";
            $formattedData .= "- Status Changes: " . implode(', ', $statuses) . "\n";
            $formattedData .= "- Source: {$filteredData[0]->form_name}\n";
            
            // Get chronological order
            usort($filteredData, function($a, $b) {
                return strtotime($a->created_at) - strtotime($b->created_at);
            });
            
            $formattedData .= "\nTRANSFER TIMELINE:\n";
            foreach ($filteredData as $index => $transfer) {
                $formattedData .= ($index+1) . ". {$transfer->created_at} - From: {$transfer->by_name}, To: {$transfer->to_name}, Status: {$transfer->to_status}\n";
            }
            
            $prompt .= $formattedData;
            $prompt .= "\nPlease provide a comprehensive analysis with:\n";
            $prompt .= "1. Company transfer pattern analysis\n";
            $prompt .= "2. Lead handoff efficiency\n";
            $prompt .= "3. Team coordination around this lead\n";
            $prompt .= "4. Status progression analysis\n";
            $prompt .= "5. Recommendations for lead management\n";
            
        } elseif ($specificRequest['type'] === 'form') {
            $filteredData = $specificRequest['data'];
            $formName = $filteredData[0]->form_name;
            
            $formattedData = "SPECIFIC SOURCE ANALYSIS - {$formName}\n\n";
            $formattedData .= "SOURCE DETAILS:\n";
            $formattedData .= "- Form/Source: {$formName}\n";
            
            $formattedData .= "\nTRANSFER METRICS:\n";
            $formattedData .= "- Total Transfers from Source: " . count($filteredData) . "\n";
            
            $companies = [];
            $recipients = [];
            $transferrers = [];
            $statuses = [];
            
            foreach ($filteredData as $transfer) {
                if (!in_array($transfer->compname, $companies)) {
                    $companies[] = $transfer->compname;
                }
                if (!in_array($transfer->to_name, $recipients)) {
                    $recipients[] = $transfer->to_name;
                }
                if (!in_array($transfer->by_name, $transferrers)) {
                    $transferrers[] = $transfer->by_name;
                }
                if (!in_array($transfer->to_status, $statuses)) {
                    $statuses[] = $transfer->to_status;
                }
            }
            
            $formattedData .= "\nANALYSIS BREAKDOWN:\n";
            $formattedData .= "- Companies: " . count($companies) . " unique companies\n";
            $formattedData .= "- Recipients: " . count($recipients) . " unique users\n";
            $formattedData .= "- Transferrers: " . count($transferrers) . " unique users\n";
            $formattedData .= "- Statuses Applied: " . implode(', ', $statuses) . "\n";
            
            // Get most common recipient
            $recipientCounts = array_count_values(array_column($filteredData, 'to_name'));
            arsort($recipientCounts);
            $topRecipient = key($recipientCounts);
            $topCount = current($recipientCounts);
            
            $formattedData .= "\nDISTRIBUTION PATTERNS:\n";
            $formattedData .= "- Most Common Recipient: {$topRecipient} ({$topCount} transfers)\n";
            $formattedData .= "- Top 3 Companies: " . implode(', ', array_slice($companies, 0, 3)) . "\n";
            
            $prompt .= $formattedData;
            $prompt .= "\nPlease provide a comprehensive analysis with:\n";
            $prompt .= "1. Source-specific transfer patterns\n";
            $prompt .= "2. Quality assessment of leads from this source\n";
            $prompt .= "3. Distribution efficiency\n";
            $prompt .= "4. Team specialization based on source\n";
            $prompt .= "5. Recommendations for source management\n";
            
        } elseif ($specificRequest['type'] === 'status') {
            $filteredData = $specificRequest['data'];
            $statusName = $filteredData[0]->to_status;
            
            $formattedData = "SPECIFIC STATUS ANALYSIS - {$statusName}\n\n";
            $formattedData .= "STATUS DETAILS:\n";
            $formattedData .= "- Status: {$statusName}\n";
            
            $formattedData .= "\nTRANSFER METRICS:\n";
            $formattedData .= "- Total Transfers with this Status: " . count($filteredData) . "\n";
            
            $companies = [];
            $recipients = [];
            $transferrers = [];
            
            foreach ($filteredData as $transfer) {
                if (!in_array($transfer->compname, $companies)) {
                    $companies[] = $transfer->compname;
                }
                if (!in_array($transfer->to_name, $recipients)) {
                    $recipients[] = $transfer->to_name;
                }
                if (!in_array($transfer->by_name, $transferrers)) {
                    $transferrers[] = $transfer->by_name;
                }
            }
            
            $formattedData .= "\nANALYSIS BREAKDOWN:\n";
            $formattedData .= "- Companies: " . count($companies) . " unique companies\n";
            $formattedData .= "- Recipients: " . count($recipients) . " unique users\n";
            $formattedData .= "- Transferrers: " . count($transferrers) . " unique users\n";
            
            // Get most common remarks pattern
            $remarks = array_column($filteredData, 'remarks');
            $remarkPatterns = [];
            foreach ($remarks as $remark) {
                $cleanRemark = preg_replace('/because of.*$/i', '', $remark);
                $cleanRemark = trim($cleanRemark);
                if (!isset($remarkPatterns[$cleanRemark])) {
                    $remarkPatterns[$cleanRemark] = 0;
                }
                $remarkPatterns[$cleanRemark]++;
            }
            
            if (!empty($remarkPatterns)) {
                arsort($remarkPatterns);
                $topRemark = key($remarkPatterns);
                $formattedData .= "\nCOMMON TRANSFER REASONS:\n";
                $formattedData .= "- Most Common Reason: {$topRemark} ({$remarkPatterns[$topRemark]} times)\n";
            }
            
            $prompt .= $formattedData;
            $prompt .= "\nPlease provide a comprehensive analysis with:\n";
            $prompt .= "1. Status-specific transfer patterns\n";
            $prompt .= "2. Workflow stage analysis\n";
            $prompt .= "3. Team coordination at this stage\n";
            $prompt .= "4. Process efficiency indicators\n";
            $prompt .= "5. Recommendations for status management\n";
            
        } elseif ($specificRequest['type'] === 'top_transferrers') {
            // Group by transferrer
            $transferrerGroups = [];
            foreach ($transferData as $transfer) {
                $transferrer = $transfer->by_name;
                if (!isset($transferrerGroups[$transferrer])) {
                    $transferrerGroups[$transferrer] = [];
                }
                $transferrerGroups[$transferrer][] = $transfer;
            }
            
            // Sort by count
            uasort($transferrerGroups, function($a, $b) {
                return count($b) - count($a);
            });
            
            $topTransferrers = array_slice($transferrerGroups, 0, 5, true);
            
            $formattedData = "TOP TRANSFERRERS ANALYSIS\n\n";
            $formattedData .= "TOP 5 USERS MAKING MOST TRANSFERS:\n\n";
            
            $rank = 1;
            foreach ($topTransferrers as $transferrer => $transfers) {
                $count = count($transfers);
                
                // Get unique recipients
                $recipients = [];
                foreach ($transfers as $transfer) {
                    if (!in_array($transfer->to_name, $recipients)) {
                        $recipients[] = $transfer->to_name;
                    }
                }
                
                $formattedData .= "{$rank}. {$transferrer}\n";
                $formattedData .= "   - Total Transfers: {$count}\n";
                $formattedData .= "   - Unique Recipients: " . count($recipients) . "\n";
                $formattedData .= "   - Most Common Recipient: " . $this->get_most_common(array_column($transfers, 'to_name')) . "\n";
                $rank++;
            }
            
            // Total statistics
            $totalTransfers = count($transferData);
            $top5Total = 0;
            foreach ($topTransferrers as $transfers) {
                $top5Total += count($transfers);
            }
            
            $top5Percentage = round(($top5Total / $totalTransfers) * 100, 1);
            
            $formattedData .= "\nANALYSIS:\n";
            $formattedData .= "- Total Transfers (Top 5): {$top5Total}\n";
            $formattedData .= "- Total Transfers (All Users): {$totalTransfers}\n";
            $formattedData .= "- Top 5 Share: {$top5Percentage}% of all transfers\n";
            
            $prompt .= $formattedData;
            $prompt .= "\nPlease provide a comprehensive analysis with:\n";
            $prompt .= "1. Pattern analysis of top transferrers\n";
            $prompt .= "2. Coordination and delegation patterns\n";
            $prompt .= "3. Workload distribution analysis\n";
            $prompt .= "4. Recommendations for transfer management\n";
            $prompt .= "5. Best practices for lead distribution\n";
            
        } elseif ($specificRequest['type'] === 'low_transferrers') {
            // Group by transferrer
            $transferrerGroups = [];
            foreach ($transferData as $transfer) {
                $transferrer = $transfer->by_name;
                if (!isset($transferrerGroups[$transferrer])) {
                    $transferrerGroups[$transferrer] = [];
                }
                $transferrerGroups[$transferrer][] = $transfer;
            }
            
            // Sort by count ascending
            uasort($transferrerGroups, function($a, $b) {
                return count($a) - count($b);
            });
            
            $lowTransferrers = array_slice($transferrerGroups, 0, 5, true);
            
            $formattedData = "LOWEST TRANSFERRERS ANALYSIS\n\n";
            $formattedData .= "TOP 5 USERS MAKING FEWEST TRANSFERS:\n\n";
            
            $rank = 1;
            foreach ($lowTransferrers as $transferrer => $transfers) {
                $count = count($transfers);
                
                $formattedData .= "{$rank}. {$transferrer}\n";
                $formattedData .= "   - Total Transfers: {$count}\n";
                if ($count > 0) {
                    $formattedData .= "   - Recipients: " . implode(', ', array_unique(array_column($transfers, 'to_name'))) . "\n";
                }
                $rank++;
            }
            
            $prompt .= $formattedData;
            $prompt .= "\nPlease provide a comprehensive analysis with:\n";
            $prompt .= "1. Pattern analysis of low transfer activity\n";
            $prompt .= "2. Potential reasons for low transfers\n";
            $prompt .= "3. Role implications\n";
            $prompt .= "4. Recommendations for engagement\n";
        }
        
        $prompt .= "\nUser Query: {$message}\n\n";
        $prompt .= "Format your response with clear sections, use specific numbers from the data, and provide actionable insights.";
        
        return $prompt;
    }

    private function generate_general_analysis_prompt($message, $transferData) {
        $totalTransfers = count($transferData);
        
        $formattedData = "FUNNEL TRANSFER LOGS ANALYSIS REPORT\n\n";
        
        // Overall Summary
        $formattedData .= "OVERALL SUMMARY:\n";
        $formattedData .= "- Total Transfers Analyzed: {$totalTransfers}\n\n";
        
        // Calculate unique counts
        $uniqueCompanies = count(array_unique(array_column($transferData, 'compname')));
        $uniqueTransferrers = count(array_unique(array_column($transferData, 'by_name')));
        $uniqueRecipients = count(array_unique(array_column($transferData, 'to_name')));
        $uniqueStatuses = count(array_unique(array_column($transferData, 'to_status')));
        $uniqueSources = count(array_unique(array_column($transferData, 'form_name')));
        
        $formattedData .= "UNIQUE COUNTS:\n";
        $formattedData .= "- Companies: {$uniqueCompanies}\n";
        $formattedData .= "- Transferrers (Senders): {$uniqueTransferrers}\n";
        $formattedData .= "- Recipients: {$uniqueRecipients}\n";
        $formattedData .= "- Statuses: {$uniqueStatuses}\n";
        $formattedData .= "- Sources/Forms: {$uniqueSources}\n\n";
        
        // Top statistics
        $formattedData .= "TOP STATISTICS:\n";
        
        // Top transferrers
        $transferrerCounts = array_count_values(array_column($transferData, 'by_name'));
        arsort($transferrerCounts);
        $topTransferrer = key($transferrerCounts);
        $topTransferrerCount = current($transferrerCounts);
        $formattedData .= "- Top Transferrer: {$topTransferrer} ({$topTransferrerCount} transfers)\n";
        
        // Top recipients
        $recipientCounts = array_count_values(array_column($transferData, 'to_name'));
        arsort($recipientCounts);
        $topRecipient = key($recipientCounts);
        $topRecipientCount = current($recipientCounts);
        $formattedData .= "- Top Recipient: {$topRecipient} ({$topRecipientCount} transfers)\n";
        
        // Most transferred company
        $companyCounts = array_count_values(array_column($transferData, 'compname'));
        arsort($companyCounts);
        $topCompany = key($companyCounts);
        $topCompanyCount = current($companyCounts);
        $formattedData .= "- Most Transferred Company: {$topCompany} ({$topCompanyCount} transfers)\n";
        
        // Most common status
        $statusCounts = array_count_values(array_column($transferData, 'to_status'));
        arsort($statusCounts);
        $topStatus = key($statusCounts);
        $topStatusCount = current($statusCounts);
        $formattedData .= "- Most Common Status: {$topStatus} ({$topStatusCount} times)\n\n";
        
        // Date range
        $dates = array_column($transferData, 'created_at');
        usort($dates, function($a, $b) {
            return strtotime($a) - strtotime($b);
        });
        
        if (!empty($dates)) {
            $formattedData .= "TIME ANALYSIS:\n";
            $formattedData .= "- First Transfer: " . $dates[0] . "\n";
            $formattedData .= "- Last Transfer: " . $dates[count($dates)-1] . "\n";
            
            // Daily average
            $firstDate = strtotime($dates[0]);
            $lastDate = strtotime($dates[count($dates)-1]);
            $daysDiff = ceil(($lastDate - $firstDate) / (60 * 60 * 24)) + 1;
            $dailyAverage = round($totalTransfers / max(1, $daysDiff), 1);
            $formattedData .= "- Daily Average: {$dailyAverage} transfers/day\n";
        }
        
        // Transfer patterns
        $formattedData .= "\nTRANSFER PATTERNS:\n";
        
        // Most common transfer pair
        $transferPairs = [];
        foreach ($transferData as $transfer) {
            $pair = "{$transfer->by_name} → {$transfer->to_name}";
            if (!isset($transferPairs[$pair])) {
                $transferPairs[$pair] = 0;
            }
            $transferPairs[$pair]++;
        }
        arsort($transferPairs);
        $topPair = key($transferPairs);
        $topPairCount = current($transferPairs);
        $formattedData .= "- Most Common Transfer Pair: {$topPair} ({$topPairCount} times)\n";
        
        // Source analysis
        $sourceCounts = array_count_values(array_column($transferData, 'form_name'));
        arsort($sourceCounts);
        $topSource = key($sourceCounts);
        $topSourceCount = current($sourceCounts);
        $topSourcePercentage = round(($topSourceCount / $totalTransfers) * 100, 1);
        $formattedData .= "- Most Common Source: {$topSource} ({$topSourceCount} transfers, {$topSourcePercentage}%)\n\n";
        
        // Status distribution
        $formattedData .= "STATUS DISTRIBUTION:\n";
        foreach ($statusCounts as $status => $count) {
            $percentage = round(($count / $totalTransfers) * 100, 1);
            $formattedData .= "- {$status}: {$count} transfers ({$percentage}%)\n";
        }
        
        // Top 10 transferrers
        $formattedData .= "\nTOP 10 TRANSFERRERS:\n";
        $counter = 1;
        foreach (array_slice($transferrerCounts, 0, 10, true) as $name => $count) {
            $percentage = round(($count / $totalTransfers) * 100, 1);
            $formattedData .= "{$counter}. {$name}: {$count} transfers ({$percentage}%)\n";
            $counter++;
        }
        
        // Top 10 recipients
        $formattedData .= "\nTOP 10 RECIPIENTS:\n";
        $counter = 1;
        foreach (array_slice($recipientCounts, 0, 10, true) as $name => $count) {
            $percentage = round(($count / $totalTransfers) * 100, 1);
            $formattedData .= "{$counter}. {$name}: {$count} transfers ({$percentage}%)\n";
            $counter++;
        }
        
        $prompt = "You are a CRM analytics AI specializing in sales funnel and lead transfer analysis. Analyze the following funnel transfer data and provide comprehensive insights:\n\n";
        $prompt .= $formattedData . "\n\n";
        $prompt .= "User Query: {$message}\n\n";
        $prompt .= "Please provide a comprehensive analysis with:\n";
        $prompt .= "1. Overall transfer pattern analysis\n";
        $prompt .= "2. Key findings and trends in lead distribution\n";
        $prompt .= "3. Team coordination and workflow efficiency\n";
        $prompt .= "4. Status progression patterns\n";
        $prompt .= "5. Recommendations for process improvement\n";
        $prompt .= "6. Workload balancing suggestions\n";
        $prompt .= "7. Best practices for lead transfers\n";
        $prompt .= "8. Action items for management\n";
        $prompt .= "9. Monitoring and follow-up recommendations\n\n";
        
        $prompt .= "Format your response with clear sections, use specific numbers from the data, and provide actionable insights.";
        
        return $prompt;
    }

    private function prepare_analysis_data($transferData, $specificRequest = null) {
        $totalTransfers = count($transferData);
        
        $preparedData = [
            'tableData' => null,
            'summaryData' => [],
            'chartData' => null,
            'specificRequestData' => null,
            'status' => 'empty',
            'message' => 'No transfer data available'
        ];
        
        if ($totalTransfers === 0) {
            return $preparedData;
        }
        
        // If specific request, prepare that data
        if ($specificRequest) {
            $preparedData['specificRequestData'] = $this->prepare_specific_request_frontend_data($specificRequest, $transferData);
        }
        
        // Calculate overall statistics
        $uniqueCompanies = count(array_unique(array_column($transferData, 'compname')));
        $uniqueTransferrers = count(array_unique(array_column($transferData, 'by_name')));
        $uniqueRecipients = count(array_unique(array_column($transferData, 'to_name')));
        $uniqueStatuses = count(array_unique(array_column($transferData, 'to_status')));
        $uniqueSources = count(array_unique(array_column($transferData, 'form_name')));
        
        // Get date range
        $dates = array_column($transferData, 'created_at');
        usort($dates, function($a, $b) {
            return strtotime($a) - strtotime($b);
        });
        
        $firstDate = !empty($dates) ? $dates[0] : null;
        $lastDate = !empty($dates) ? $dates[count($dates)-1] : null;
        
        // Calculate daily average
        $dailyAverage = 0;
        if ($firstDate && $lastDate) {
            $firstTimestamp = strtotime($firstDate);
            $lastTimestamp = strtotime($lastDate);
            $daysDiff = ceil(($lastTimestamp - $firstTimestamp) / (60 * 60 * 24)) + 1;
            $dailyAverage = round($totalTransfers / max(1, $daysDiff), 1);
        }
        
        // Prepare summary data
        $preparedData['summaryData'] = [
            'total_transfers' => $totalTransfers,
            'unique_companies' => $uniqueCompanies,
            'unique_transferrers' => $uniqueTransferrers,
            'unique_recipients' => $uniqueRecipients,
            'unique_statuses' => $uniqueStatuses,
            'unique_sources' => $uniqueSources,
            'first_transfer' => $firstDate,
            'last_transfer' => $lastDate,
            'daily_average' => $dailyAverage
        ];
        
        // Prepare transferrers chart
        $transferrerCounts = array_count_values(array_column($transferData, 'by_name'));
        arsort($transferrerCounts);
        $topTransferrers = array_slice($transferrerCounts, 0, 10, true);
        
        $preparedData['transferrersChartData'] = [
            'labels' => array_keys($topTransferrers),
            'datasets' => [[
                'label' => 'Transfers Made',
                'data' => array_values($topTransferrers),
                'backgroundColor' => '#3498db',
                'borderColor' => '#2980b9',
                'borderWidth' => 1
            ]]
        ];
        
        // Prepare recipients chart
        $recipientCounts = array_count_values(array_column($transferData, 'to_name'));
        arsort($recipientCounts);
        $topRecipients = array_slice($recipientCounts, 0, 10, true);
        
        $preparedData['recipientsChartData'] = [
            'labels' => array_keys($topRecipients),
            'datasets' => [[
                'label' => 'Transfers Received',
                'data' => array_values($topRecipients),
                'backgroundColor' => '#2ecc71',
                'borderColor' => '#27ae60',
                'borderWidth' => 1
            ]]
        ];
        
        // Prepare status distribution chart
        $statusCounts = array_count_values(array_column($transferData, 'to_status'));
        arsort($statusCounts);
        
        $preparedData['statusChartData'] = [
            'labels' => array_keys($statusCounts),
            'datasets' => [[
                'label' => 'Status Distribution',
                'data' => array_values($statusCounts),
                'backgroundColor' => ['#e74c3c', '#f39c12', '#9b59b6', '#1abc9c', '#34495e', '#3498db', '#2ecc71'],
                'borderColor' => '#2a2a2a',
                'borderWidth' => 1
            ]]
        ];
        
        // Prepare source distribution chart
        $sourceCounts = array_count_values(array_column($transferData, 'form_name'));
        arsort($sourceCounts);
        
        $preparedData['sourceChartData'] = [
            'labels' => array_keys($sourceCounts),
            'datasets' => [[
                'label' => 'Source Distribution',
                'data' => array_values($sourceCounts),
                'backgroundColor' => ['#f1c40f', '#e67e22', '#d35400', '#c0392b', '#16a085', '#8e44ad'],
                'borderColor' => '#2a2a2a',
                'borderWidth' => 1
            ]]
        ];
        
        // Prepare table data (last 50 transfers)
        $recentTransfers = array_slice($transferData, 0, 50);
        $tableRows = [];
        
        foreach ($recentTransfers as $transfer) {
            $tableRows[] = [
                $transfer->compname,
                $transfer->form_name,
                $transfer->by_name,
                $transfer->to_name,
                $transfer->to_status,
                $transfer->created_at,
                substr($transfer->remarks, 0, 50) . (strlen($transfer->remarks) > 50 ? '...' : '')
            ];
        }
        
        $preparedData['tableData'] = [
            'headers' => ['Company', 'Source', 'From', 'To', 'Status', 'Date', 'Remarks'],
            'rows' => $tableRows
        ];
        
        // Prepare timeline chart (transfers per day)
        $dailyTransfers = [];
        foreach ($transferData as $transfer) {
            $date = date('Y-m-d', strtotime($transfer->created_at));
            if (!isset($dailyTransfers[$date])) {
                $dailyTransfers[$date] = 0;
            }
            $dailyTransfers[$date]++;
        }
        
        ksort($dailyTransfers);
        
        $preparedData['timelineChartData'] = [
            'labels' => array_keys($dailyTransfers),
            'datasets' => [[
                'label' => 'Transfers per Day',
                'data' => array_values($dailyTransfers),
                'backgroundColor' => 'rgba(52, 152, 219, 0.2)',
                'borderColor' => 'rgba(52, 152, 219, 1)',
                'borderWidth' => 1,
                'fill' => true
            ]]
        ];
        
        $preparedData['status'] = 'success';
        $preparedData['message'] = "Analyzed {$totalTransfers} funnel transfers";
        
        return $preparedData;
    }

    private function prepare_specific_request_frontend_data($specificRequest, $transferData) {
        $specificData = [];
        
        if ($specificRequest['type'] === 'to_name') {
            $filteredData = $specificRequest['data'];
            $toName = $filteredData[0]->to_name;
            
            // Calculate statistics
            $transferCount = count($filteredData);
            $uniqueTransferrers = count(array_unique(array_column($filteredData, 'by_name')));
            $uniqueCompanies = count(array_unique(array_column($filteredData, 'compname')));
            $uniqueStatuses = count(array_unique(array_column($filteredData, 'to_status')));
            
            // Get most common transferrer
            $transferrerCounts = array_count_values(array_column($filteredData, 'by_name'));
            arsort($transferrerCounts);
            $topTransferrer = key($transferrerCounts);
            $topTransferrerCount = current($transferrerCounts);
            
            $specificData = [
                'type' => 'to_name',
                'details' => [
                    'name' => $toName,
                    'transfer_count' => $transferCount,
                    'unique_transferrers' => $uniqueTransferrers,
                    'unique_companies' => $uniqueCompanies,
                    'unique_statuses' => $uniqueStatuses
                ],
                'top_transferrer' => [
                    'name' => $topTransferrer,
                    'count' => $topTransferrerCount,
                    'percentage' => round(($topTransferrerCount / $transferCount) * 100, 1)
                ]
            ];
            
        } elseif ($specificRequest['type'] === 'by_name') {
            $filteredData = $specificRequest['data'];
            $byName = $filteredData[0]->by_name;
            
            // Calculate statistics
            $transferCount = count($filteredData);
            $uniqueRecipients = count(array_unique(array_column($filteredData, 'to_name')));
            $uniqueCompanies = count(array_unique(array_column($filteredData, 'compname')));
            $uniqueStatuses = count(array_unique(array_column($filteredData, 'to_status')));
            
            // Get most common recipient
            $recipientCounts = array_count_values(array_column($filteredData, 'to_name'));
            arsort($recipientCounts);
            $topRecipient = key($recipientCounts);
            $topRecipientCount = current($recipientCounts);
            
            $specificData = [
                'type' => 'by_name',
                'details' => [
                    'name' => $byName,
                    'transfer_count' => $transferCount,
                    'unique_recipients' => $uniqueRecipients,
                    'unique_companies' => $uniqueCompanies,
                    'unique_statuses' => $uniqueStatuses
                ],
                'top_recipient' => [
                    'name' => $topRecipient,
                    'count' => $topRecipientCount,
                    'percentage' => round(($topRecipientCount / $transferCount) * 100, 1)
                ]
            ];
            
        } elseif ($specificRequest['type'] === 'company') {
            $filteredData = $specificRequest['data'];
            $companyName = $filteredData[0]->compname;
            $companyId = $filteredData[0]->cid;
            
            // Calculate statistics
            $transferCount = count($filteredData);
            $uniqueTransferrers = count(array_unique(array_column($filteredData, 'by_name')));
            $uniqueRecipients = count(array_unique(array_column($filteredData, 'to_name')));
            $uniqueStatuses = count(array_unique(array_column($filteredData, 'to_status')));
            
            // Get timeline
            $dates = array_column($filteredData, 'created_at');
            usort($dates, function($a, $b) {
                return strtotime($a) - strtotime($b);
            });
            
            $specificData = [
                'type' => 'company',
                'details' => [
                    'name' => $companyName,
                    'company_id' => $companyId,
                    'transfer_count' => $transferCount,
                    'unique_transferrers' => $uniqueTransferrers,
                    'unique_recipients' => $uniqueRecipients,
                    'unique_statuses' => $uniqueStatuses,
                    'first_transfer' => !empty($dates) ? $dates[0] : null,
                    'last_transfer' => !empty($dates) ? $dates[count($dates)-1] : null
                ]
            ];
            
        } elseif ($specificRequest['type'] === 'form') {
            $filteredData = $specificRequest['data'];
            $formName = $filteredData[0]->form_name;
            
            // Calculate statistics
            $transferCount = count($filteredData);
            $uniqueCompanies = count(array_unique(array_column($filteredData, 'compname')));
            $uniqueTransferrers = count(array_unique(array_column($filteredData, 'by_name')));
            $uniqueRecipients = count(array_unique(array_column($filteredData, 'to_name')));
            
            // Get most common status
            $statusCounts = array_count_values(array_column($filteredData, 'to_status'));
            arsort($statusCounts);
            $topStatus = key($statusCounts);
            $topStatusCount = current($statusCounts);
            
            $specificData = [
                'type' => 'form',
                'details' => [
                    'name' => $formName,
                    'transfer_count' => $transferCount,
                    'unique_companies' => $uniqueCompanies,
                    'unique_transferrers' => $uniqueTransferrers,
                    'unique_recipients' => $uniqueRecipients
                ],
                'top_status' => [
                    'name' => $topStatus,
                    'count' => $topStatusCount,
                    'percentage' => round(($topStatusCount / $transferCount) * 100, 1)
                ]
            ];
            
        } elseif ($specificRequest['type'] === 'status') {
            $filteredData = $specificRequest['data'];
            $statusName = $filteredData[0]->to_status;
            
            // Calculate statistics
            $transferCount = count($filteredData);
            $uniqueCompanies = count(array_unique(array_column($filteredData, 'compname')));
            $uniqueTransferrers = count(array_unique(array_column($filteredData, 'by_name')));
            $uniqueRecipients = count(array_unique(array_column($filteredData, 'to_name')));
            
            // Get most common transferrer
            $transferrerCounts = array_count_values(array_column($filteredData, 'by_name'));
            arsort($transferrerCounts);
            $topTransferrer = key($transferrerCounts);
            $topTransferrerCount = current($transferrerCounts);
            
            $specificData = [
                'type' => 'status',
                'details' => [
                    'name' => $statusName,
                    'transfer_count' => $transferCount,
                    'unique_companies' => $uniqueCompanies,
                    'unique_transferrers' => $uniqueTransferrers,
                    'unique_recipients' => $uniqueRecipients
                ],
                'top_transferrer' => [
                    'name' => $topTransferrer,
                    'count' => $topTransferrerCount,
                    'percentage' => round(($topTransferrerCount / $transferCount) * 100, 1)
                ]
            ];
            
        } elseif ($specificRequest['type'] === 'top_transferrers') {
            // Group by transferrer
            $transferrerGroups = [];
            foreach ($transferData as $transfer) {
                $transferrer = $transfer->by_name;
                if (!isset($transferrerGroups[$transferrer])) {
                    $transferrerGroups[$transferrer] = [];
                }
                $transferrerGroups[$transferrer][] = $transfer;
            }
            
            // Sort by count
            uasort($transferrerGroups, function($a, $b) {
                return count($b) - count($a);
            });
            
            $topTransferrers = array_slice($transferrerGroups, 0, 5, true);
            
            $topData = [];
            foreach ($topTransferrers as $name => $transfers) {
                $uniqueRecipients = count(array_unique(array_column($transfers, 'to_name')));
                $uniqueCompanies = count(array_unique(array_column($transfers, 'compname')));
                
                $topData[] = [
                    'name' => $name,
                    'transfer_count' => count($transfers),
                    'unique_recipients' => $uniqueRecipients,
                    'unique_companies' => $uniqueCompanies
                ];
            }
            
            $specificData = [
                'type' => 'top_transferrers',
                'top_transferrers' => $topData
            ];
            
        } elseif ($specificRequest['type'] === 'low_transferrers') {
            // Group by transferrer
            $transferrerGroups = [];
            foreach ($transferData as $transfer) {
                $transferrer = $transfer->by_name;
                if (!isset($transferrerGroups[$transferrer])) {
                    $transferrerGroups[$transferrer] = [];
                }
                $transferrerGroups[$transferrer][] = $transfer;
            }
            
            // Sort by count ascending
            uasort($transferrerGroups, function($a, $b) {
                return count($a) - count($b);
            });
            
            $lowTransferrers = array_slice($transferrerGroups, 0, 5, true);
            
            $lowData = [];
            foreach ($lowTransferrers as $name => $transfers) {
                $uniqueRecipients = count(array_unique(array_column($transfers, 'to_name')));
                
                $lowData[] = [
                    'name' => $name,
                    'transfer_count' => count($transfers),
                    'unique_recipients' => $uniqueRecipients
                ];
            }
            
            $specificData = [
                'type' => 'low_transferrers',
                'low_transferrers' => $lowData
            ];
        }
        
        return $specificData;
    }

    private function get_most_common($array) {
        if (empty($array)) return 'N/A';
        
        $counts = array_count_values($array);
        arsort($counts);
        return key($counts);
    }
}