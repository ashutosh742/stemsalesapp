<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DeletedCompanyBetweenDates_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Menu_model');
        $this->load->model('Report_model');
        $this->load->model('ChatAI_model');
    }

    public function process_DeletedCompanyBetweenDates_analysis($message, $analysisType, $sdate, $edate) {
        
        // Calculate number of days
        if($sdate == $edate){
            $numberofdays = 100;
        } else {
            $date1 = new DateTime($sdate);
            $date2 = new DateTime($edate);
            $interval = $date1->diff($date2);
            $numberofdays = $interval->days;
        }

        // Fetch deleted company data between dates
        $deletedCompanyData = $this->Report_model->GetRemoveCompanyLogs($this->uid, $sdate, $edate);

        // Check if data is empty or null
        if(empty($deletedCompanyData) || !is_array($deletedCompanyData)) {
            return [
                'content' => 'No deleted company data found for the given period.',
                'data' => [],
                'frontendData' => [
                    'status' => 'empty',
                    'message' => 'No company deletion data available for the selected date range.'
                ]
            ];
        }

        // Check if user is asking for specific user details
        $specificRequest = $this->extract_specific_request($message, $deletedCompanyData);
        
        if ($specificRequest) {
            // Generate prompt for specific analysis
            $prompt = $this->generate_specific_analysis_prompt($message, $specificRequest, $deletedCompanyData);
        } else {
            // Generate prompt for general analysis
            $prompt = $this->generate_general_analysis_prompt($message, $deletedCompanyData);
        }
        
        // Get ChatGPT response
        $chatgptResponse = $this->ChatAI_model->call_chatgpt_api($prompt, []);
        
        // Prepare data for frontend
        $frontendData = $this->prepare_analysis_data($deletedCompanyData, $specificRequest);
        
        return [
            'content' => $chatgptResponse,
            'data' => $frontendData
        ];
    }

    private function extract_specific_request($message, $deletedData) {
        // Convert message to lowercase for easier matching
        $lowerMessage = strtolower($message);
        
        // Check for specific user mentions
        foreach ($deletedData as $user) {
            $userName = strtolower(is_object($user) ? $user->username : $user['username']);
            if (strpos($lowerMessage, $userName) !== false) {
                return ['type' => 'user', 'data' => $user];
            }
        }
        
        // Check for specific user ID mentions
        foreach ($deletedData as $user) {
            $userId = is_object($user) ? $user->user_id : $user['user_id'];
            if (strpos($lowerMessage, (string)$userId) !== false) {
                return ['type' => 'user_id', 'data' => $user];
            }
        }
        
        // Check for deletion count mentions
        if (strpos($lowerMessage, 'most deleted') !== false || 
            strpos($lowerMessage, 'highest deletion') !== false ||
            strpos($lowerMessage, 'top deleters') !== false) {
            return ['type' => 'top_deleters', 'data' => null];
        }
        
        // Check for low deletion mentions
        if (strpos($lowerMessage, 'least deleted') !== false || 
            strpos($lowerMessage, 'lowest deletion') !== false) {
            return ['type' => 'low_deleters', 'data' => null];
        }
        
        return null;
    }

    private function generate_specific_analysis_prompt($message, $specificRequest, $deletedData) {
        $prompt = "You are a CRM analytics AI analyzing company deletion patterns between dates. Provide detailed insights:\n\n";
        
        if ($specificRequest['type'] === 'user') {
            $user = $specificRequest['data'];
            $userName = is_object($user) ? $user->username : $user['username'];
            $userId = is_object($user) ? $user->user_id : $user['user_id'];
            $deletedCount = is_object($user) ? $user->total_deleted : $user['total_deleted'];
            
            $formattedData = "SPECIFIC USER DELETION ANALYSIS - {$userName}\n\n";
            $formattedData .= "USER DETAILS:\n";
            $formattedData .= "- Name: {$userName}\n";
            $formattedData .= "- User ID: {$userId}\n";
            
            $formattedData .= "\nDELETION METRICS:\n";
            $formattedData .= "- Total Companies Deleted: {$deletedCount}\n";
            
            // Get other users for comparison
            $otherUsers = array_filter($deletedData, function($u) use ($userName) {
                $uName = is_object($u) ? $u->username : $u['username'];
                return $uName !== $userName;
            });
            
            if (count($otherUsers) > 0) {
                $avgDeleted = array_sum(array_map(function($u) {
                    return is_object($u) ? $u->total_deleted : $u['total_deleted'];
                }, $otherUsers)) / count($otherUsers);
                
                $maxDeleted = max(array_map(function($u) {
                    return is_object($u) ? $u->total_deleted : $u['total_deleted'];
                }, $otherUsers));
                
                $formattedData .= "\nCOMPARISON WITH PEERS:\n";
                $formattedData .= "- Peer Average Deletions: " . round($avgDeleted, 1) . "\n";
                $formattedData .= "- Highest Among Peers: {$maxDeleted}\n";
                $formattedData .= "- Your Deletions: {$deletedCount} (" . round(($deletedCount - $avgDeleted), 1) . " difference)\n";
                $formattedData .= "- Rank among " . (count($deletedData)) . " users: ";
                
                // Calculate rank
                usort($deletedData, function($a, $b) {
                    $deletedA = is_object($a) ? $a->total_deleted : $a['total_deleted'];
                    $deletedB = is_object($b) ? $b->total_deleted : $b['total_deleted'];
                    return $deletedB - $deletedA;
                });
                
                $rank = 1;
                foreach ($deletedData as $u) {
                    $uName = is_object($u) ? $u->username : $u['username'];
                    if ($uName === $userName) {
                        $formattedData .= "#{$rank}\n";
                        break;
                    }
                    $rank++;
                }
                
                // Percentile calculation
                $percentile = round((($rank - 1) / count($deletedData)) * 100, 1);
                $formattedData .= "- Percentile: {$percentile}% (higher means more deletions)\n";
            }
            
            $prompt .= $formattedData;
            $prompt .= "\nPlease provide a comprehensive analysis with:\n";
            $prompt .= "1. Deletion pattern analysis for this user\n";
            $prompt .= "2. Comparison with peer average\n";
            $prompt .= "3. Potential reasons for high/low deletion count\n";
            $prompt .= "4. Quality control implications\n";
            $prompt .= "5. Recommendations for improving data quality\n";
            $prompt .= "6. Suggestions for user training if needed\n";
            
        } elseif ($specificRequest['type'] === 'user_id') {
            $user = $specificRequest['data'];
            
            $userName = is_object($user) ? $user->username : $user['username'];
            $userId = is_object($user) ? $user->user_id : $user['user_id'];
            $deletedCount = is_object($user) ? $user->total_deleted : $user['total_deleted'];
            
            $formattedData = "USER ID ANALYSIS - User ID: {$userId}\n\n";
            
            $formattedData .= "USER DETAILS:\n";
            $formattedData .= "- Name: {$userName}\n";
            $formattedData .= "- User ID: {$userId}\n";
            
            $formattedData .= "\nDELETION METRICS:\n";
            $formattedData .= "- Total Companies Deleted: {$deletedCount}\n";
            
            // Get statistics
            $allDeletions = array_map(function($u) {
                return is_object($u) ? $u->total_deleted : $u['total_deleted'];
            }, $deletedData);
            
            $averageDeletions = round(array_sum($allDeletions) / count($allDeletions), 1);
            $maxDeletions = max($allDeletions);
            $minDeletions = min($allDeletions);
            
            $formattedData .= "\nSTATISTICAL COMPARISON:\n";
            $formattedData .= "- Average Deletions (All Users): {$averageDeletions}\n";
            $formattedData .= "- Maximum Deletions: {$maxDeletions}\n";
            $formattedData .= "- Minimum Deletions: {$minDeletions}\n";
            $formattedData .= "- Your Deletions: {$deletedCount}\n\n";
            
            // Performance assessment
            $deviationFromAvg = $deletedCount - $averageDeletions;
            $percentOfMax = $maxDeletions > 0 ? round(($deletedCount / $maxDeletions) * 100, 1) : 0;
            
            $formattedData .= "PERFORMANCE ASSESSMENT:\n";
            $formattedData .= "- Deviation from Average: " . ($deviationFromAvg >= 0 ? "+" : "") . round($deviationFromAvg, 1) . "\n";
            $formattedData .= "- Percentage of Maximum: {$percentOfMax}%\n";
            
            if ($deletedCount > $averageDeletions * 1.5) {
                $formattedData .= "- Status: Above Average (High deletion rate)\n";
                $formattedData .= "- Implication: May need quality control review\n";
            } elseif ($deletedCount < $averageDeletions * 0.5) {
                $formattedData .= "- Status: Below Average (Low deletion rate)\n";
                $formattedData .= "- Implication: Good data quality or low activity\n";
            } else {
                $formattedData .= "- Status: Average (Normal deletion rate)\n";
                $formattedData .= "- Implication: Standard performance\n";
            }
            
            // Ranking
            usort($deletedData, function($a, $b) {
                $deletedA = is_object($a) ? $a->total_deleted : $a['total_deleted'];
                $deletedB = is_object($b) ? $b->total_deleted : $b['total_deleted'];
                return $deletedB - $deletedA;
            });
            
            $rank = 1;
            foreach ($deletedData as $u) {
                $uId = is_object($u) ? $u->user_id : $u['user_id'];
                if ($uId == $userId) {
                    $formattedData .= "- Rank: #{$rank} out of " . count($deletedData) . " users\n";
                    break;
                }
                $rank++;
            }
            
            $prompt .= $formattedData;
            $prompt .= "\nPlease provide a comprehensive analysis with:\n";
            $prompt .= "1. Individual deletion pattern analysis\n";
            $prompt .= "2. Statistical positioning\n";
            $prompt .= "3. Quality implications\n";
            $prompt .= "4. Training or process improvement recommendations\n";
            $prompt .= "5. Risk assessment\n";
            $prompt .= "6. Actionable insights\n";
            
        } elseif ($specificRequest['type'] === 'top_deleters') {
            // Sort by deletion count descending
            usort($deletedData, function($a, $b) {
                $deletedA = is_object($a) ? $a->total_deleted : $a['total_deleted'];
                $deletedB = is_object($b) ? $b->total_deleted : $b['total_deleted'];
                return $deletedB - $deletedA;
            });
            
            $topUsers = array_slice($deletedData, 0, 5);
            
            $formattedData = "TOP DELETERS ANALYSIS\n\n";
            $formattedData .= "TOP 5 USERS WITH MOST COMPANY DELETIONS:\n\n";
            
            $rank = 1;
            $totalDeletions = 0;
            foreach ($topUsers as $user) {
                $userName = is_object($user) ? $user->username : $user['username'];
                $userId = is_object($user) ? $user->user_id : $user['user_id'];
                $deletedCount = is_object($user) ? $user->total_deleted : $user['total_deleted'];
                
                $totalDeletions += $deletedCount;
                $formattedData .= "{$rank}. {$userName} (User ID: {$userId})\n";
                $formattedData .= "   - Deletions: {$deletedCount}\n";
                
                $rank++;
            }
            
            // Calculate total deletions from all users
            $allDeletions = array_sum(array_map(function($u) {
                return is_object($u) ? $u->total_deleted : $u['total_deleted'];
            }, $deletedData));
            
            $top5Percentage = $allDeletions > 0 ? round(($totalDeletions / $allDeletions) * 100, 1) : 0;
            
            $formattedData .= "\nANALYSIS:\n";
            $formattedData .= "- Total Deletions (Top 5): {$totalDeletions}\n";
            $formattedData .= "- Total Deletions (All Users): {$allDeletions}\n";
            $formattedData .= "- Top 5 Share: {$top5Percentage}% of all deletions\n";
            
            // Calculate average
            $averageDeletions = round($allDeletions / count($deletedData), 1);
            $formattedData .= "- Average Deletions per User: {$averageDeletions}\n";
            
            // Top deleter vs average
            $topDeleterCount = is_object($topUsers[0]) ? $topUsers[0]->total_deleted : $topUsers[0]['total_deleted'];
            $vsAverage = round($topDeleterCount / $averageDeletions, 1);
            $formattedData .= "- Top Deleter vs Average: {$vsAverage}x higher than average\n";
            
            $prompt .= $formattedData;
            $prompt .= "\nPlease provide a comprehensive analysis with:\n";
            $prompt .= "1. Pattern analysis of top deleters\n";
            $prompt .= "2. Potential reasons for high deletion rates\n";
            $prompt .= "3. Quality control concerns\n";
            $prompt .= "4. Recommendations for top deleters\n";
            $prompt .= "5. Process improvement suggestions\n";
            $prompt .= "6. Training needs assessment\n";
            
        } elseif ($specificRequest['type'] === 'low_deleters') {
            // Sort by deletion count ascending
            usort($deletedData, function($a, $b) {
                $deletedA = is_object($a) ? $a->total_deleted : $a['total_deleted'];
                $deletedB = is_object($b) ? $b->total_deleted : $b['total_deleted'];
                return $deletedA - $deletedB;
            });
            
            $lowUsers = array_slice($deletedData, 0, 5);
            
            $formattedData = "LOWEST DELETERS ANALYSIS\n\n";
            $formattedData .= "TOP 5 USERS WITH LEAST COMPANY DELETIONS:\n\n";
            
            $rank = 1;
            $totalDeletions = 0;
            foreach ($lowUsers as $user) {
                $userName = is_object($user) ? $user->username : $user['username'];
                $userId = is_object($user) ? $user->user_id : $user['user_id'];
                $deletedCount = is_object($user) ? $user->total_deleted : $user['total_deleted'];
                
                $totalDeletions += $deletedCount;
                $formattedData .= "{$rank}. {$userName} (User ID: {$userId})\n";
                $formattedData .= "   - Deletions: {$deletedCount}\n";
                
                $rank++;
            }
            
            // Calculate statistics
            $allDeletions = array_sum(array_map(function($u) {
                return is_object($u) ? $u->total_deleted : $u['total_deleted'];
            }, $deletedData));
            
            $averageDeletions = round($allDeletions / count($deletedData), 1);
            
            $formattedData .= "\nANALYSIS:\n";
            $formattedData .= "- Total Deletions (Lowest 5): {$totalDeletions}\n";
            $formattedData .= "- Average Deletions per User: {$averageDeletions}\n";
            $formattedData .= "- Lowest vs Average: " . round(($lowUsers[0]->total_deleted ?? $lowUsers[0]['total_deleted']) / $averageDeletions, 1) . "x of average\n";
            
            // Interpretation
            $lowestDeletion = is_object($lowUsers[0]) ? $lowUsers[0]->total_deleted : $lowUsers[0]['total_deleted'];
            if ($lowestDeletion === 0) {
                $formattedData .= "- Note: Some users have 0 deletions\n";
            }
            
            $prompt .= $formattedData;
            $prompt .= "\nPlease provide a comprehensive analysis with:\n";
            $prompt .= "1. Analysis of low deletion patterns\n";
            $prompt .= "2. Potential reasons for low deletion rates\n";
            $prompt .= "3. Quality implications (positive and negative)\n";
            $prompt .= "4. Activity level assessment\n";
            $prompt .= "5. Recommendations for low deleters\n";
            $prompt .= "6. Best practices sharing opportunities\n";
        }
        
        $prompt .= "\nUser Query: {$message}\n\n";
        $prompt .= "Format your response with clear sections, use specific numbers from the data, and provide actionable insights.";
        
        return $prompt;
    }

    private function generate_general_analysis_prompt($message, $deletedData) {
        $totalUsers = count($deletedData);
        
        $formattedData = "COMPANY DELETION ANALYSIS REPORT\n\n";
        
        // Overall Summary
        $formattedData .= "OVERALL SUMMARY:\n";
        $formattedData .= "- Total Users Analyzed: {$totalUsers}\n";
        $formattedData .= "- Analysis Period: Company deletion data\n\n";
        
        // Calculate totals and statistics
        $deletionCounts = array_map(function($user) {
            return is_object($user) ? $user->total_deleted : $user['total_deleted'];
        }, $deletedData);
        
        $totalDeletions = array_sum($deletionCounts);
        $averageDeletions = round($totalDeletions / $totalUsers, 1);
        $maxDeletions = max($deletionCounts);
        $minDeletions = min($deletionCounts);
        
        $formattedData .= "DELETION STATISTICS:\n";
        $formattedData .= "- Total Companies Deleted: {$totalDeletions}\n";
        $formattedData .= "- Average Deletions per User: {$averageDeletions}\n";
        $formattedData .= "- Maximum Deletions by a User: {$maxDeletions}\n";
        $formattedData .= "- Minimum Deletions by a User: {$minDeletions}\n\n";
        
        // Distribution analysis
        $formattedData .= "DELETION DISTRIBUTION:\n";
        
        // Calculate ranges based on data
        $ranges = $this->get_deletion_ranges($deletionCounts);
        foreach ($ranges as $range => $count) {
            $percentage = round(($count / $totalUsers) * 100, 1);
            $formattedData .= "- {$range}: {$count} users ({$percentage}%)\n";
        }
        
        // Top 10 deleters
        usort($deletedData, function($a, $b) {
            $deletedA = is_object($a) ? $a->total_deleted : $a['total_deleted'];
            $deletedB = is_object($b) ? $b->total_deleted : $b['total_deleted'];
            return $deletedB - $deletedA;
        });
        
        $formattedData .= "\nTOP 10 USERS BY DELETIONS:\n";
        $top10 = array_slice($deletedData, 0, 10);
        $rank = 1;
        $top10Total = 0;
        
        foreach ($top10 as $user) {
            $userName = is_object($user) ? $user->username : $user['username'];
            $userId = is_object($user) ? $user->user_id : $user['user_id'];
            $deletedCount = is_object($user) ? $user->total_deleted : $user['total_deleted'];
            
            $top10Total += $deletedCount;
            $percentageOfTotal = $totalDeletions > 0 ? round(($deletedCount / $totalDeletions) * 100, 1) : 0;
            $vsAverage = round($deletedCount / $averageDeletions, 1);
            
            $formattedData .= "{$rank}. {$userName} (ID: {$userId})\n";
            $formattedData .= "   - Deletions: {$deletedCount}\n";
            $formattedData .= "   - % of Total: {$percentageOfTotal}%\n";
            $formattedData .= "   - vs Average: {$vsAverage}x\n";
            $rank++;
        }
        
        $top10Percentage = $totalDeletions > 0 ? round(($top10Total / $totalDeletions) * 100, 1) : 0;
        $formattedData .= "\nTOP 10 SUMMARY:\n";
        $formattedData .= "- Total Deletions by Top 10: {$top10Total}\n";
        $formattedData .= "- % of All Deletions: {$top10Percentage}%\n";
        
        // Users with 0 deletions
        $zeroDeletionUsers = array_filter($deletedData, function($user) {
            $deleted = is_object($user) ? $user->total_deleted : $user['total_deleted'];
            return $deleted === 0;
        });
        
        $zeroCount = count($zeroDeletionUsers);
        if ($zeroCount > 0) {
            $zeroPercentage = round(($zeroCount / $totalUsers) * 100, 1);
            $formattedData .= "\nUSERS WITH 0 DELETIONS:\n";
            $formattedData .= "- Count: {$zeroCount} users\n";
            $formattedData .= "- Percentage: {$zeroPercentage}%\n";
        }
        
        // High deleters analysis (above average + 50%)
        $highThreshold = $averageDeletions * 1.5;
        $highDeleters = array_filter($deletedData, function($user) use ($highThreshold) {
            $deleted = is_object($user) ? $user->total_deleted : $user['total_deleted'];
            return $deleted >= $highThreshold;
        });
        
        $highCount = count($highDeleters);
        if ($highCount > 0) {
            $highPercentage = round(($highCount / $totalUsers) * 100, 1);
            $formattedData .= "\nHIGH DELETERS (Above " . round($highThreshold, 1) . " deletions):\n";
            $formattedData .= "- Count: {$highCount} users\n";
            $formattedData .= "- Percentage: {$highPercentage}%\n";
            $formattedData .= "- Threshold: " . round($highThreshold, 1) . " deletions (1.5x average)\n";
        }
        
        // Standard deviation calculation
        $variance = 0;
        foreach ($deletionCounts as $count) {
            $variance += pow($count - $averageDeletions, 2);
        }
        $variance = $variance / $totalUsers;
        $stdDev = round(sqrt($variance), 1);
        
        $formattedData .= "\nVARIABILITY ANALYSIS:\n";
        $formattedData .= "- Standard Deviation: {$stdDev}\n";
        $formattedData .= "- Coefficient of Variation: " . round(($stdDev / $averageDeletions) * 100, 1) . "%\n";
        
        if ($stdDev > $averageDeletions) {
            $formattedData .= "- Interpretation: High variability in deletion patterns\n";
        } else {
            $formattedData .= "- Interpretation: Moderate variability in deletion patterns\n";
        }
        
        $prompt = "You are a CRM analytics AI specializing in data quality and process analysis. Analyze the following company deletion data and provide comprehensive insights:\n\n";
        $prompt .= $formattedData . "\n\n";
        $prompt .= "User Query: {$message}\n\n";
        $prompt .= "Please provide a comprehensive analysis with:\n";
        $prompt .= "1. Overall deletion pattern analysis\n";
        $prompt .= "2. Key findings and trends\n";
        $prompt .= "3. Quality control implications\n";
        $prompt .= "4. Risk assessment for high deleters\n";
        $prompt .= "5. Recommendations for process improvement\n";
        $prompt .= "6. Training needs identification\n";
        $prompt .= "7. Best practices for data entry\n";
        $prompt .= "8. Action items for management\n";
        $prompt .= "9. Monitoring and follow-up recommendations\n\n";
        
        $prompt .= "Format your response with clear sections, use specific numbers from the data, and provide actionable insights.";
        
        return $prompt;
    }

    private function prepare_analysis_data($deletedData, $specificRequest = null) {
        $totalUsers = count($deletedData);
        
        $preparedData = [
            'tableData' => null,
            'summaryData' => [],
            'chartData' => null,
            'specificRequestData' => null,
            'status' => 'empty',
            'message' => 'No deletion data available'
        ];
        
        if ($totalUsers === 0) {
            return $preparedData;
        }
        
        // If specific request, prepare that data
        if ($specificRequest) {
            $preparedData['specificRequestData'] = $this->prepare_specific_request_frontend_data($specificRequest, $deletedData);
        }
        
        // Calculate overall statistics
        $deletionCounts = array_map(function($user) {
            return is_object($user) ? $user->total_deleted : $user['total_deleted'];
        }, $deletedData);
        
        $totalDeletions = array_sum($deletionCounts);
        $averageDeletions = round($totalDeletions / $totalUsers, 1);
        $maxDeletions = max($deletionCounts);
        $minDeletions = min($deletionCounts);
        
        // Calculate standard deviation
        $variance = 0;
        foreach ($deletionCounts as $count) {
            $variance += pow($count - $averageDeletions, 2);
        }
        $variance = $variance / $totalUsers;
        $stdDev = round(sqrt($variance), 1);
        
        // Prepare summary data
        $preparedData['summaryData'] = [
            'total_users' => $totalUsers,
            'total_deletions' => $totalDeletions,
            'average_deletions' => $averageDeletions,
            'max_deletions' => $maxDeletions,
            'min_deletions' => $minDeletions,
            'std_deviation' => $stdDev,
            'coefficient_variation' => round(($stdDev / $averageDeletions) * 100, 1),
            'users_with_zero' => count(array_filter($deletionCounts, function($count) {
                return $count === 0;
            }))
        ];
        
        // Prepare chart data for distribution
        $ranges = $this->get_deletion_ranges($deletionCounts);
        
        $preparedData['distributionChartData'] = [
            'labels' => array_keys($ranges),
            'datasets' => [[
                'label' => 'Users by Deletion Range',
                'data' => array_values($ranges),
                'backgroundColor' => ['#10a37f', '#1abc9c', '#f39c12', '#ffa726', '#ff6b6b', '#8e44ad', '#5436da'],
                'borderColor' => '#2a2a2a',
                'borderWidth' => 1
            ]]
        ];
        
        // Prepare top deleters chart
        usort($deletedData, function($a, $b) {
            $deletedA = is_object($a) ? $a->total_deleted : $a['total_deleted'];
            $deletedB = is_object($b) ? $b->total_deleted : $b['total_deleted'];
            return $deletedB - $deletedA;
        });
        
        $top10 = array_slice($deletedData, 0, 10);
        $topNames = [];
        $topDeletions = [];
        
        foreach ($top10 as $user) {
            $topNames[] = is_object($user) ? $user->username : $user['username'];
            $topDeletions[] = is_object($user) ? $user->total_deleted : $user['total_deleted'];
        }
        
        $preparedData['topDeletersChartData'] = [
            'labels' => $topNames,
            'datasets' => [[
                'label' => 'Deletions',
                'data' => $topDeletions,
                'backgroundColor' => '#ff6b6b',
                'borderColor' => '#ff4757',
                'borderWidth' => 1
            ]]
        ];
        
        // Prepare table data
        $tableRows = [];
        foreach ($deletedData as $user) {
            $userName = is_object($user) ? $user->username : $user['username'];
            $userId = is_object($user) ? $user->user_id : $user['user_id'];
            $deletedCount = is_object($user) ? $user->total_deleted : $user['total_deleted'];
            
            $vsAverage = round($deletedCount - $averageDeletions, 1);
            $percentageOfAvg = $averageDeletions > 0 ? round(($deletedCount / $averageDeletions) * 100, 1) : 0;
            
            $tableRows[] = [
                $userName,
                $userId,
                $deletedCount,
                $vsAverage >= 0 ? "+" . $vsAverage : $vsAverage,
                $percentageOfAvg . "%",
                $this->get_deletion_level($deletedCount, $averageDeletions)
            ];
        }
        
        $preparedData['tableData'] = [
            'headers' => ['Name', 'User ID', 'Deletions', 'vs Avg', '% of Avg', 'Level'],
            'rows' => $tableRows
        ];
        
        // Prepare comparison chart
        $preparedData['comparisonChartData'] = [
            'labels' => ['Average', 'Maximum', 'Minimum'],
            'datasets' => [[
                'label' => 'Deletion Statistics',
                'data' => [$averageDeletions, $maxDeletions, $minDeletions],
                'backgroundColor' => ['#f39c12', '#ff6b6b', '#10a37f'],
                'borderColor' => '#2a2a2a',
                'borderWidth' => 1
            ]]
        ];
        
        $preparedData['status'] = 'success';
        $preparedData['message'] = "Analyzed deletion data for {$totalUsers} users";
        
        return $preparedData;
    }

    private function prepare_specific_request_frontend_data($specificRequest, $deletedData) {
        $specificData = [];
        
        if ($specificRequest['type'] === 'user') {
            $user = $specificRequest['data'];
            
            $userName = is_object($user) ? $user->username : $user['username'];
            $userId = is_object($user) ? $user->user_id : $user['user_id'];
            $deletedCount = is_object($user) ? $user->total_deleted : $user['total_deleted'];
            
            // Calculate statistics
            $allDeletions = array_map(function($u) {
                return is_object($u) ? $u->total_deleted : $u['total_deleted'];
            }, $deletedData);
            
            $averageDeletions = round(array_sum($allDeletions) / count($allDeletions), 1);
            $maxDeletions = max($allDeletions);
            
            // Calculate rank
            usort($deletedData, function($a, $b) {
                $deletedA = is_object($a) ? $a->total_deleted : $a['total_deleted'];
                $deletedB = is_object($b) ? $b->total_deleted : $b['total_deleted'];
                return $deletedB - $deletedA;
            });
            
            $rank = 1;
            foreach ($deletedData as $u) {
                $uName = is_object($u) ? $u->username : $u['username'];
                if ($uName === $userName) {
                    break;
                }
                $rank++;
            }
            
            $specificData = [
                'type' => 'user',
                'details' => [
                    'name' => $userName,
                    'user_id' => $userId,
                    'deletions' => $deletedCount
                ],
                'comparison' => [
                    'average' => $averageDeletions,
                    'maximum' => $maxDeletions,
                    'vs_average' => round($deletedCount - $averageDeletions, 1),
                    'percentage_of_max' => $maxDeletions > 0 ? round(($deletedCount / $maxDeletions) * 100, 1) : 0,
                    'rank' => $rank,
                    'total_users' => count($deletedData),
                    'percentile' => round((($rank - 1) / count($deletedData)) * 100, 1)
                ],
                'assessment' => $this->assess_deletion_performance($deletedCount, $averageDeletions)
            ];
            
        } elseif ($specificRequest['type'] === 'user_id') {
            $user = $specificRequest['data'];
            
            $specificData = [
                'type' => 'user_id',
                'user_details' => [
                    'name' => is_object($user) ? $user->username : $user['username'],
                    'user_id' => is_object($user) ? $user->user_id : $user['user_id'],
                    'deletions' => is_object($user) ? $user->total_deleted : $user['total_deleted']
                ]
            ];
            
            // Add comparison data
            $allDeletions = array_map(function($u) {
                return is_object($u) ? $u->total_deleted : $u['total_deleted'];
            }, $deletedData);
            
            $averageDeletions = round(array_sum($allDeletions) / count($allDeletions), 1);
            $specificData['comparison'] = [
                'average' => $averageDeletions,
                'deviation' => round($specificData['user_details']['deletions'] - $averageDeletions, 1),
                'ratio' => $averageDeletions > 0 ? round($specificData['user_details']['deletions'] / $averageDeletions, 1) : 0
            ];
            
        } elseif ($specificRequest['type'] === 'top_deleters') {
            // Sort by deletion count descending
            usort($deletedData, function($a, $b) {
                $deletedA = is_object($a) ? $a->total_deleted : $a['total_deleted'];
                $deletedB = is_object($b) ? $b->total_deleted : $b['total_deleted'];
                return $deletedB - $deletedA;
            });
            
            $top5 = array_slice($deletedData, 0, 5);
            
            $topDeleters = [];
            foreach ($top5 as $user) {
                $topDeleters[] = [
                    'name' => is_object($user) ? $user->username : $user['username'],
                    'user_id' => is_object($user) ? $user->user_id : $user['user_id'],
                    'deletions' => is_object($user) ? $user->total_deleted : $user['total_deleted']
                ];
            }
            
            // Calculate total deletions
            $allDeletions = array_map(function($u) {
                return is_object($u) ? $u->total_deleted : $u['total_deleted'];
            }, $deletedData);
            
            $totalDeletions = array_sum($allDeletions);
            $top5Total = array_sum(array_column($topDeleters, 'deletions'));
            
            $specificData = [
                'type' => 'top_deleters',
                'top_deleters' => $topDeleters,
                'statistics' => [
                    'top5_total' => $top5Total,
                    'all_total' => $totalDeletions,
                    'percentage' => $totalDeletions > 0 ? round(($top5Total / $totalDeletions) * 100, 1) : 0,
                    'average' => round($totalDeletions / count($deletedData), 1)
                ]
            ];
            
        } elseif ($specificRequest['type'] === 'low_deleters') {
            // Sort by deletion count ascending
            usort($deletedData, function($a, $b) {
                $deletedA = is_object($a) ? $a->total_deleted : $a['total_deleted'];
                $deletedB = is_object($b) ? $b->total_deleted : $b['total_deleted'];
                return $deletedA - $deletedB;
            });
            
            $low5 = array_slice($deletedData, 0, 5);
            
            $lowDeleters = [];
            foreach ($low5 as $user) {
                $lowDeleters[] = [
                    'name' => is_object($user) ? $user->username : $user['username'],
                    'user_id' => is_object($user) ? $user->user_id : $user['user_id'],
                    'deletions' => is_object($user) ? $user->total_deleted : $user['total_deleted']
                ];
            }
            
            $specificData = [
                'type' => 'low_deleters',
                'low_deleters' => $lowDeleters
            ];
        }
        
        return $specificData;
    }

    private function assess_deletion_performance($deletionCount, $averageDeletions) {
        $assessment = [
            'level' => '',
            'implication' => '',
            'recommendation' => ''
        ];
        
        if ($deletionCount === 0) {
            $assessment['level'] = 'Zero';
            $assessment['implication'] = 'No deletions - could indicate careful data entry or low activity';
            $assessment['recommendation'] = 'Review data quality and activity levels';
        } elseif ($deletionCount < $averageDeletions * 0.5) {
            $assessment['level'] = 'Low';
            $assessment['implication'] = 'Below average deletions - generally positive for data quality';
            $assessment['recommendation'] = 'Continue current practices, share best practices with team';
        } elseif ($deletionCount <= $averageDeletions * 1.5) {
            $assessment['level'] = 'Average';
            $assessment['implication'] = 'Standard deletion rate - normal data maintenance';
            $assessment['recommendation'] = 'Monitor patterns, ensure proper deletion procedures';
        } else {
            $assessment['level'] = 'High';
            $assessment['implication'] = 'Above average deletions - may indicate quality issues or high activity';
            $assessment['recommendation'] = 'Review deletion reasons, provide training if needed';
        }
        
        return $assessment;
    }

    private function get_deletion_ranges($deletionCounts) {
        $max = max($deletionCounts);
        
        if ($max <= 5) {
            $ranges = [
                '0' => 0,
                '1' => 0,
                '2' => 0,
                '3' => 0,
                '4-5' => 0
            ];
            
            foreach ($deletionCounts as $count) {
                if ($count === 0) $ranges['0']++;
                elseif ($count === 1) $ranges['1']++;
                elseif ($count === 2) $ranges['2']++;
                elseif ($count === 3) $ranges['3']++;
                else $ranges['4-5']++;
            }
        } elseif ($max <= 10) {
            $ranges = [
                '0' => 0,
                '1-2' => 0,
                '3-4' => 0,
                '5-6' => 0,
                '7-10' => 0
            ];
            
            foreach ($deletionCounts as $count) {
                if ($count === 0) $ranges['0']++;
                elseif ($count <= 2) $ranges['1-2']++;
                elseif ($count <= 4) $ranges['3-4']++;
                elseif ($count <= 6) $ranges['5-6']++;
                else $ranges['7-10']++;
            }
        } else {
            $ranges = [
                '0' => 0,
                '1-3' => 0,
                '4-6' => 0,
                '7-9' => 0,
                '10+' => 0
            ];
            
            foreach ($deletionCounts as $count) {
                if ($count === 0) $ranges['0']++;
                elseif ($count <= 3) $ranges['1-3']++;
                elseif ($count <= 6) $ranges['4-6']++;
                elseif ($count <= 9) $ranges['7-9']++;
                else $ranges['10+']++;
            }
        }
        
        return $ranges;
    }

    private function get_deletion_level($deletionCount, $averageDeletions) {
        if ($deletionCount === 0) {
            return 'Zero';
        } elseif ($deletionCount < $averageDeletions * 0.5) {
            return 'Low';
        } elseif ($deletionCount <= $averageDeletions * 1.5) {
            return 'Average';
        } else {
            return 'High';
        }
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