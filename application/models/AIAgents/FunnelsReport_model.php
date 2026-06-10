<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class FunnelsReport_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Menu_model');
        $this->load->model('Report_model');
        $this->load->model('ChatAI_model');
    }

    public function process_FunnelsReport_model_analysis($message, $analysisType, $sdate, $edate) {
        
        // Get funnel data
        $totalFunnels = $this->Report_model->getAllCompanyBYRollesMaiing($this->uid, $this->uid, 'all');
        $totalTasksUserByDatas = $this->Report_model->GetFunnelDetails($this->uid, 'total', NULL);

        $funnelDatasAnalysis = [
            'totalFunnels' => $totalFunnels,
            'totalTasksUserByDatas' => $totalTasksUserByDatas,
            'period' => $sdate . ' to ' . $edate
        ];

        // Check if user is asking for specific funnel details
        $specificFunnel = $this->extract_specific_funnel_request($message, $funnelDatasAnalysis);
        
        if ($specificFunnel) {
            // Generate prompt for specific funnel analysis
            $prompt = $this->generate_specific_funnel_analysis_prompt($message, $specificFunnel, $funnelDatasAnalysis);
        } else {
            // Generate prompt for general funnel analysis
            $prompt = $this->generate_funnel_analysis_prompt($message, $funnelDatasAnalysis);
        }
        
        // Get ChatGPT response
        $chatgptResponse = $this->ChatAI_model->call_chatgpt_api($prompt, []);
        
        // Prepare data for frontend
        $frontendData = $this->prepare_funnel_analysis_data($funnelDatasAnalysis, $specificFunnel);
        
        return [
            'content' => $chatgptResponse,
            'data' => $totalTasksUserByDatas
        ];
    }

    private function extract_specific_funnel_request($message, $funnelData) {
        // Convert message to lowercase for easier matching
        $lowerMessage = strtolower($message);
        
        // Check for specific user mentions in total_funnel_by_user
        if (isset($funnelData['totalFunnels']['total_funnel_by_user'])) {
            foreach ($funnelData['totalFunnels']['total_funnel_by_user'] as $user) {
                $userName = strtolower($user->user_name);
                
                if (strpos($lowerMessage, $userName) !== false) {
                    return ['type' => 'user', 'data' => $user];
                }
            }
        }
        
        // Check for specific cluster mentions
        if (isset($funnelData['totalFunnels']['totalClusterBYClusterNameDatas'])) {
            foreach ($funnelData['totalFunnels']['totalClusterBYClusterNameDatas'] as $cluster) {
                $clusterName = strtolower($cluster->clustername);
                
                if (strpos($lowerMessage, $clusterName) !== false) {
                    return ['type' => 'cluster', 'data' => $cluster];
                }
            }
        }
        
        // Check for specific partner type mentions
        if (isset($funnelData['totalFunnels']['total_funnels_partner'][0])) {
            $partnerData = (array)$funnelData['totalFunnels']['total_funnels_partner'][0];
            foreach ($partnerData as $key => $value) {
                if ($key !== 'total' && strpos($lowerMessage, strtolower(str_replace('_', ' ', $key))) !== false) {
                    return ['type' => 'partner', 'data' => ['type' => $key, 'count' => $value]];
                }
            }
        }
        
        // Check for specific status mentions
        if (isset($funnelData['totalFunnels']['total_funnels_status'][0])) {
            $statusData = (array)$funnelData['totalFunnels']['total_funnels_status'][0];
            $statusMap = [
                'open' => 'Open',
                'reachout' => 'Reachout',
                'tentative' => 'Tentative',
                'will_do_later' => 'Will do Later',
                'not_interested' => 'Not Interested',
                'positive' => 'Positive',
                'closure' => 'Closure',
                'open_rpem' => 'OPEN RPEM',
                'very_positive' => 'Very Positive',
                'ttd_reachout' => 'TTD-Reachout',
                'wno_reachout' => 'WNO-Reachout',
                'positive_nap' => 'Positive-NAP',
                'very_positive_nap' => 'Very Positive-NAP',
                'on_boarded' => 'On Boarded'
            ];
            
            foreach ($statusMap as $key => $displayName) {
                if (strpos($lowerMessage, strtolower($displayName)) !== false || 
                    strpos($lowerMessage, strtolower(str_replace('_', ' ', $key))) !== false) {
                    return ['type' => 'status', 'data' => ['status' => $displayName, 'count' => $statusData[$key] ?? 0]];
                }
            }
        }
        
        return null;
    }

    private function generate_specific_funnel_analysis_prompt($message, $specificFunnel, $data) {
        $prompt = "You are a sales analytics AI. Analyze the following specific funnel data and provide detailed insights:\n\n";
        
        if ($specificFunnel['type'] === 'user') {
            $user = $specificFunnel['data'];
            $formattedData = "SPECIFIC USER FUNNEL ANALYSIS - {$user->user_name}\n\n";
            
            $formattedData .= "OVERALL PERFORMANCE:\n";
            $formattedData .= "- Total Funnels: {$user->total}\n";
            $formattedData .= "- User ID: {$user->current_user_id}\n\n";
            
            $formattedData .= "STATUS BREAKDOWN:\n";
            $formattedData .= "- Open: {$user->open}\n";
            $formattedData .= "- Reachout: {$user->reachout}\n";
            $formattedData .= "- Tentative: {$user->tentative}\n";
            $formattedData .= "- Will do Later: {$user->will_do_later}\n";
            $formattedData .= "- Not Interested: {$user->not_interested}\n";
            $formattedData .= "- Positive: {$user->positive}\n";
            $formattedData .= "- Closure: {$user->closure}\n";
            $formattedData .= "- OPEN RPEM: {$user->open_rpem}\n";
            $formattedData .= "- Very Positive: {$user->very_positive}\n";
            $formattedData .= "- TTD-Reachout: {$user->ttd_reachout}\n";
            $formattedData .= "- WNO-Reachout: {$user->wno_reachout}\n";
            $formattedData .= "- Positive-NAP: {$user->positive_nap}\n";
            $formattedData .= "- Very Positive-NAP: {$user->very_positive_nap}\n";
            $formattedData .= "- On Boarded: {$user->on_boarded}\n\n";
            
            $formattedData .= "CATEGORY BREAKDOWN (Old):\n";
            $formattedData .= "- Focus Funnel: {$user->Focus_Funnel}\n";
            $formattedData .= "- Upsell Client: {$user->Upsell_Client}\n";
            $formattedData .= "- Key Client: {$user->Key_Client}\n";
            $formattedData .= "- Positive Key Client: {$user->Positive_Key_Client}\n";
            $formattedData .= "- Priority Calling: {$user->Priority_Calling}\n";
            $formattedData .= "- Top Spender: {$user->Top_Spender}\n";
            $formattedData .= "- Potential Client: {$user->Potential_Client}\n\n";
            
            $formattedData .= "ASSIGNMENT BREAKDOWN:\n";
            $formattedData .= "- Cluster Manager Assign: {$user->cluster_manager_assign_funnel}\n";
            $formattedData .= "- PST Assign: {$user->PST_assign_funnel}\n";
            $formattedData .= "- RM North Assign: {$user->RM_North_assign_funnel}\n";
            
            $prompt .= $formattedData;
            $prompt .= "Please provide a comprehensive analysis with:\n";
            $prompt .= "1. User performance summary and key metrics\n";
            $prompt .= "2. Sales pipeline health analysis\n";
            $prompt .= "3. Conversion rates at each stage\n";
            $prompt .= "4. Category distribution and focus areas\n";
            $prompt .= "5. Comparison with team averages\n";
            $prompt .= "6. Recommendations for improvement\n";
            $prompt .= "7. Potential bottlenecks and opportunities\n";
            
        } elseif ($specificFunnel['type'] === 'cluster') {
            $cluster = $specificFunnel['data'];
            $formattedData = "SPECIFIC CLUSTER ANALYSIS - {$cluster->clustername}\n\n";
            
            $formattedData .= "CLUSTER OVERVIEW:\n";
            $formattedData .= "- Total Funnels: {$cluster->total}\n";
            $formattedData .= "- Cluster ID: {$cluster->cluster_id}\n";
            $formattedData .= "- Base Location Funnels: {$cluster->base_location}\n";
            $formattedData .= "- OutStation Location Funnels: {$cluster->outStation_location}\n\n";
            
            // Add BDs working on this cluster
            $formattedData .= "BUSINESS DEVELOPERS IN THIS CLUSTER:\n";
            if (isset($data['totalFunnels']['totalClusterBYClusterNameBYBDDatas'])) {
                $bdCount = 0;
                foreach ($data['totalFunnels']['totalClusterBYClusterNameBYBDDatas'] as $bd) {
                    if ($bd->cluster_id == $cluster->cluster_id) {
                        $bdCount++;
                        $formattedData .= "- {$bd->username} (ID: {$bd->user_id}): {$bd->total} funnels\n";
                    }
                }
                $formattedData .= "Total BDs: {$bdCount}\n\n";
            }
            
            $prompt .= $formattedData;
            $prompt .= "Please provide a comprehensive analysis with:\n";
            $prompt .= "1. Cluster performance overview\n";
            $formattedData .= "2. Geographic distribution analysis\n";
            $formattedData .= "3. Team coverage and resource allocation\n";
            $formattedData .= "4. Travel pattern insights\n";
            $formattedData .= "5. Market penetration analysis\n";
            $formattedData .= "6. Recommendations for cluster optimization\n";
            
        } elseif ($specificFunnel['type'] === 'status') {
            $status = $specificFunnel['data'];
            $formattedData = "SPECIFIC STATUS ANALYSIS - {$status['status']}\n\n";
            
            $formattedData .= "STATUS OVERVIEW:\n";
            $formattedData .= "- Total Count: {$status['count']}\n";
            
            // Get percentage of total
            $totalFunnels = $data['totalFunnels']['total_funnels_status'][0]->total ?? 1006;
            $percentage = $totalFunnels > 0 ? round(($status['count'] / $totalFunnels) * 100, 2) : 0;
            $formattedData .= "- Percentage of Total: {$percentage}%\n\n";
            
            // Get users with this status
            $formattedData .= "TOP USERS WITH THIS STATUS:\n";
            if (isset($data['totalFunnels']['total_funnel_by_user'])) {
                $statusKey = strtolower(str_replace([' ', '-'], '_', $status['status']));
                $topUsers = [];
                
                foreach ($data['totalFunnels']['total_funnel_by_user'] as $user) {
                    if (isset($user->$statusKey) && $user->$statusKey > 0) {
                        $topUsers[] = [
                            'name' => $user->user_name,
                            'count' => $user->$statusKey,
                            'total' => $user->total
                        ];
                    }
                }
                
                // Sort by count descending
                usort($topUsers, function($a, $b) {
                    return $b['count'] - $a['count'];
                });
                
                $top5 = array_slice($topUsers, 0, 5);
                foreach ($top5 as $index => $user) {
                    $rank = $index + 1;
                    $userPercentage = $user['total'] > 0 ? round(($user['count'] / $user['total']) * 100, 1) : 0;
                    $formattedData .= "{$rank}. {$user['name']}: {$user['count']} ({$userPercentage}% of their total)\n";
                }
            }
            
            $prompt .= $formattedData;
            $prompt .= "Please provide a comprehensive analysis with:\n";
            $prompt .= "1. Status stage analysis and implications\n";
            $prompt .= "2. Conversion potential from this stage\n";
            $prompt .= "3. Average time in this stage\n";
            $prompt .= "4. Recommendations for moving to next stage\n";
            $prompt .= "5. Common characteristics of funnels in this stage\n";
            
        } elseif ($specificFunnel['type'] === 'partner') {
            $partner = $specificFunnel['data'];
            $partnerName = str_replace('_', ' ', $partner['type']);
            $formattedData = "SPECIFIC PARTNER TYPE ANALYSIS - {$partnerName}\n\n";
            
            $formattedData .= "PARTNER OVERVIEW:\n";
            $formattedData .= "- Total Count: {$partner['count']}\n";
            
            // Get percentage of total
            $totalPartners = $data['totalFunnels']['total_funnels_partner'][0]->total ?? 1006;
            $percentage = $totalPartners > 0 ? round(($partner['count'] / $totalPartners) * 100, 2) : 0;
            $formattedData .= "- Percentage of Total: {$percentage}%\n\n";
            
            // Get top users for this partner type
            $formattedData .= "TOP USERS FOR THIS PARTNER TYPE:\n";
            if (isset($data['totalFunnels']['total_funnels_partner_user'])) {
                $partnerKey = $partner['type'];
                $topUsers = [];
                
                foreach ($data['totalFunnels']['total_funnels_partner_user'] as $user) {
                    if (isset($user->$partnerKey) && $user->$partnerKey > 0) {
                        $topUsers[] = [
                            'name' => $user->username,
                            'count' => $user->$partnerKey,
                            'total' => $user->total
                        ];
                    }
                }
                
                // Sort by count descending
                usort($topUsers, function($a, $b) {
                    return $b['count'] - $a['count'];
                });
                
                $top5 = array_slice($topUsers, 0, 5);
                foreach ($top5 as $index => $user) {
                    $rank = $index + 1;
                    $userPercentage = $user['total'] > 0 ? round(($user['count'] / $user['total']) * 100, 1) : 0;
                    $formattedData .= "{$rank}. {$user['name']}: {$user['count']} ({$userPercentage}% of their total)\n";
                }
            }
            
            $prompt .= $formattedData;
            $prompt .= "Please provide a comprehensive analysis with:\n";
            $prompt .= "1. Partner type characteristics\n";
            $prompt .= "2. Conversion rates for this partner type\n";
            $prompt .= "3. Average deal size potential\n";
            $prompt .= "4. Best practices for engaging this partner type\n";
            $prompt .= "5. Growth opportunities\n";
        }
        
        $prompt .= "\nUser Query: {$message}\n\n";
        $prompt .= "Format your response with clear sections and use specific numbers from the data.";
        
        return $prompt;
    }

    private function generate_funnel_analysis_prompt($message, $data) {
        $totalFunnels = $data['totalFunnels'];
        
        $formattedData = "SALES FUNNEL ANALYSIS - Period: {$data['period']}\n\n";
        
        // Overall Summary
        $totalFunnelCount = $totalFunnels['total_funnels_status'][0]->total ?? 1006;
        $formattedData .= "OVERALL SUMMARY:\n";
        $formattedData .= "- Total Funnels: {$totalFunnelCount}\n";
        $formattedData .= "- Analysis Period: {$data['period']}\n\n";
        
        // Pipeline Status Analysis
        $formattedData .= "PIPELINE STATUS BREAKDOWN:\n";
        if (isset($totalFunnels['total_funnels_status'][0])) {
            $statusData = (array)$totalFunnels['total_funnels_status'][0];
            $importantStatuses = ['open', 'reachout', 'tentative', 'positive', 'closure', 'open_rpem'];
            
            foreach ($importantStatuses as $status) {
                if (isset($statusData[$status])) {
                    $count = $statusData[$status];
                    $percentage = $totalFunnelCount > 0 ? round(($count / $totalFunnelCount) * 100, 1) : 0;
                    $statusName = ucwords(str_replace('_', ' ', $status));
                    $formattedData .= "- {$statusName}: {$count} ({$percentage}%)\n";
                }
            }
            
            // Calculate conversion metrics
            $positivePlusClosure = ($statusData['positive'] ?? 0) + ($statusData['closure'] ?? 0) + ($statusData['on_boarded'] ?? 0);
            $conversionRate = $totalFunnelCount > 0 ? round(($positivePlusClosure / $totalFunnelCount) * 100, 1) : 0;
            $formattedData .= "- Total Positive/Closed/Onboarded: {$positivePlusClosure} ({$conversionRate}% conversion rate)\n";
        }
        
        $formattedData .= "\nCATEGORY ANALYSIS (Old Categories):\n";
        if (isset($totalFunnels['total_funnels_old_category'][0])) {
            $categoryData = (array)$totalFunnels['total_funnels_old_category'][0];
            arsort($categoryData);
            
            foreach ($categoryData as $key => $value) {
                if ($key !== 'total') {
                    $percentage = $totalFunnelCount > 0 ? round(($value / $totalFunnelCount) * 100, 1) : 0;
                    $categoryName = str_replace('_', ' ', $key);
                    $formattedData .= "- {$categoryName}: {$value} ({$percentage}%)\n";
                }
            }
        }
        
        $formattedData .= "\nPARTNER TYPE DISTRIBUTION:\n";
        if (isset($totalFunnels['total_funnels_partner'][0])) {
            $partnerData = (array)$totalFunnels['total_funnels_partner'][0];
            arsort($partnerData);
            
            $top5 = array_slice($partnerData, 1, 5); // Skip total
            foreach ($top5 as $key => $value) {
                $percentage = $totalFunnelCount > 0 ? round(($value / $totalFunnelCount) * 100, 1) : 0;
                $partnerName = str_replace('_', ' ', $key);
                $formattedData .= "- {$partnerName}: {$value} ({$percentage}%)\n";
            }
        }
        
        $formattedData .= "\nTEAM PERFORMANCE (Top 5 by total funnels):\n";
        if (isset($totalFunnels['total_funnel_by_user'])) {
            $users = $totalFunnels['total_funnel_by_user'];
            usort($users, function($a, $b) {
                return $b->total - $a->total;
            });
            
            $top5Users = array_slice($users, 0, 5);
            foreach ($top5Users as $index => $user) {
                $rank = $index + 1;
                $positiveRate = $user->total > 0 ? round(($user->positive / $user->total) * 100, 1) : 0;
                $formattedData .= "{$rank}. {$user->user_name}: {$user->total} funnels, {$user->positive} positive ({$positiveRate}%)\n";
            }
        }
        
        $formattedData .= "\nGEOGRAPHIC DISTRIBUTION:\n";
        if (isset($totalFunnels['totalClusterDatas'][0])) {
            $clusterData = $totalFunnels['totalClusterDatas'][0];
            $basePercentage = $totalFunnelCount > 0 ? round(($clusterData->base_location / $totalFunnelCount) * 100, 1) : 0;
            $outstationPercentage = $totalFunnelCount > 0 ? round(($clusterData->outStation_location / $totalFunnelCount) * 100, 1) : 0;
            
            $formattedData .= "- Base Location: {$clusterData->base_location} ({$basePercentage}%)\n";
            $formattedData .= "- OutStation Location: {$clusterData->outStation_location} ({$outstationPercentage}%)\n";
        }
        
        $formattedData .= "\nTOP CLUSTERS BY FUNNEL COUNT:\n";
        if (isset($totalFunnels['totalClusterBYClusterNameDatas'])) {
            $clusters = $totalFunnels['totalClusterBYClusterNameDatas'];
            usort($clusters, function($a, $b) {
                return $b->total - $a->total;
            });
            
            $top5Clusters = array_slice($clusters, 0, 5);
            foreach ($top5Clusters as $index => $cluster) {
                $rank = $index + 1;
                $basePerc = $cluster->total > 0 ? round(($cluster->base_location / $cluster->total) * 100, 1) : 0;
                $outPerc = $cluster->total > 0 ? round(($cluster->outStation_location / $cluster->total) * 100, 1) : 0;
                $formattedData .= "{$rank}. {$cluster->clustername}: {$cluster->total} total ({$cluster->base_location} base, {$cluster->outStation_location} outstation)\n";
            }
        }
        
        $formattedData .= "\nCURRENT QUARTER PIPELINE:\n";
        if (isset($totalFunnels['total_current_quarter'][0])) {
            $quarterData = (array)$totalFunnels['total_current_quarter'][0];
            $importantMetrics = [
                'current_quarter_twetenty_closure_funnel' => 'Q2 Closure Funnel',
                'total_prospecting_funnel' => 'Prospecting Funnel',
                'total_closure_pipeline' => 'Closure Pipeline',
                'total_proposal_to_be_sent_target' => 'Proposals Target'
            ];
            
            foreach ($importantMetrics as $key => $label) {
                if (isset($quarterData[$key])) {
                    $value = $quarterData[$key];
                    $percentage = $totalFunnelCount > 0 ? round(($value / $totalFunnelCount) * 100, 1) : 0;
                    $formattedData .= "- {$label}: {$value} ({$percentage}%)\n";
                }
            }
        }
        
        $prompt = "You are a sales analytics AI. Analyze the following funnel data and provide comprehensive insights:\n\n";
        $prompt .= $formattedData . "\n\n";
        $prompt .= "User Query: {$message}\n\n";
        $prompt .= "Please provide a comprehensive sales funnel analysis with:\n";
        $prompt .= "1. Overall pipeline health and conversion metrics\n";
        $prompt .= "2. Stage-by-stage analysis with bottleneck identification\n";
        $prompt .= "3. Top performing categories and partner types\n";
        $prompt .= "4. Team performance rankings and analysis\n";
        $prompt .= "5. Geographic distribution and cluster performance\n";
        $prompt .= "6. Current quarter pipeline analysis\n";
        $prompt .= "7. Risk areas and opportunities for improvement\n";
        $prompt .= "8. Recommendations for sales strategy optimization\n";
        $prompt .= "9. Identify the top 5 clusters and users with highest funnel count\n\n";
        
        $prompt .= "Format your response with clear sections, use specific numbers from the data, and provide actionable insights.";
        
        return $prompt;
    }

    private function prepare_funnel_analysis_data($data, $specificFunnel = null) {
        $totalFunnels = $data['totalFunnels'];
        $preparedData = [
            'tableData' => null,
            'summaryData' => [],
            'chartData' => null,
            'specificFunnelData' => null,
            'status' => 'empty',
            'message' => 'No funnel data available'
        ];
        
        // If specific funnel requested, prepare that data
        if ($specificFunnel) {
            $preparedData['specificFunnelData'] = $this->prepare_specific_funnel_frontend_data($specificFunnel, $data);
        }
        
        // Prepare overall summary data
        $totalFunnelCount = $totalFunnels['total_funnels_status'][0]->total ?? 1006;
        
        // Status breakdown
        $statusBreakdown = [];
        if (isset($totalFunnels['total_funnels_status'][0])) {
            $statusData = (array)$totalFunnels['total_funnels_status'][0];
            foreach ($statusData as $key => $value) {
                if ($key !== 'total') {
                    $statusBreakdown[] = [
                        'status' => ucwords(str_replace('_', ' ', $key)),
                        'count' => $value,
                        'percentage' => $totalFunnelCount > 0 ? round(($value / $totalFunnelCount) * 100, 1) : 0
                    ];
                }
            }
        }
        
        // Category breakdown
        $categoryBreakdown = [];
        if (isset($totalFunnels['total_funnels_old_category'][0])) {
            $categoryData = (array)$totalFunnels['total_funnels_old_category'][0];
            foreach ($categoryData as $key => $value) {
                if ($key !== 'total') {
                    $categoryBreakdown[] = [
                        'category' => str_replace('_', ' ', $key),
                        'count' => $value,
                        'percentage' => $totalFunnelCount > 0 ? round(($value / $totalFunnelCount) * 100, 1) : 0
                    ];
                }
            }
        }
        
        // Partner type breakdown
        $partnerBreakdown = [];
        if (isset($totalFunnels['total_funnels_partner'][0])) {
            $partnerData = (array)$totalFunnels['total_funnels_partner'][0];
            foreach ($partnerData as $key => $value) {
                if ($key !== 'total') {
                    $partnerBreakdown[] = [
                        'partner_type' => str_replace('_', ' ', $key),
                        'count' => $value,
                        'percentage' => $totalFunnelCount > 0 ? round(($value / $totalFunnelCount) * 100, 1) : 0
                    ];
                }
            }
        }
        
        // Team performance data
        $teamPerformance = [];
        if (isset($totalFunnels['total_funnel_by_user'])) {
            foreach ($totalFunnels['total_funnel_by_user'] as $user) {
                $positiveRate = $user->total > 0 ? round(($user->positive / $user->total) * 100, 1) : 0;
                $teamPerformance[] = [
                    'user_name' => $user->user_name,
                    'user_id' => $user->current_user_id,
                    'total_funnels' => $user->total,
                    'positive' => $user->positive,
                    'closure' => $user->closure,
                    'positive_rate' => $positiveRate,
                    'open' => $user->open,
                    'tentative' => $user->tentative
                ];
            }
        }
        
        // Cluster performance data
        $clusterPerformance = [];
        if (isset($totalFunnels['totalClusterBYClusterNameDatas'])) {
            foreach ($totalFunnels['totalClusterBYClusterNameDatas'] as $cluster) {
                $basePercentage = $cluster->total > 0 ? round(($cluster->base_location / $cluster->total) * 100, 1) : 0;
                $outstationPercentage = $cluster->total > 0 ? round(($cluster->outStation_location / $cluster->total) * 100, 1) : 0;
                
                $clusterPerformance[] = [
                    'cluster_name' => $cluster->clustername,
                    'cluster_id' => $cluster->cluster_id,
                    'total_funnels' => $cluster->total,
                    'base_location' => $cluster->base_location,
                    'outstation_location' => $cluster->outStation_location,
                    'base_percentage' => $basePercentage,
                    'outstation_percentage' => $outstationPercentage
                ];
            }
        }
        
        // Current quarter data
        $currentQuarter = [];
        if (isset($totalFunnels['total_current_quarter'][0])) {
            $quarterData = (array)$totalFunnels['total_current_quarter'][0];
            foreach ($quarterData as $key => $value) {
                if ($key !== 'total') {
                    $currentQuarter[] = [
                        'metric' => str_replace('_', ' ', $key),
                        'count' => $value,
                        'percentage' => $totalFunnelCount > 0 ? round(($value / $totalFunnelCount) * 100, 1) : 0
                    ];
                }
            }
        }
        
        // Prepare summary data
        $preparedData['summaryData'] = [
            'period' => $data['period'],
            'total_funnels' => $totalFunnelCount,
            'status_breakdown' => $statusBreakdown,
            'category_breakdown' => $categoryBreakdown,
            'partner_breakdown' => $partnerBreakdown,
            'team_performance' => $teamPerformance,
            'cluster_performance' => $clusterPerformance,
            'current_quarter' => $currentQuarter,
            'geographic_distribution' => isset($totalFunnels['totalClusterDatas'][0]) ? [
                'base_location' => $totalFunnels['totalClusterDatas'][0]->base_location,
                'outstation_location' => $totalFunnels['totalClusterDatas'][0]->outStation_location,
                'base_percentage' => $totalFunnelCount > 0 ? round(($totalFunnels['totalClusterDatas'][0]->base_location / $totalFunnelCount) * 100, 1) : 0,
                'outstation_percentage' => $totalFunnelCount > 0 ? round(($totalFunnels['totalClusterDatas'][0]->outStation_location / $totalFunnelCount) * 100, 1) : 0
            ] : null
        ];
        
        // Prepare chart data for status distribution
        if (!empty($statusBreakdown)) {
            $statusLabels = [];
            $statusData = [];
            $statusColors = [];
            
            $colorPalette = ['#5436da', '#10a37f', '#ffa726', '#ff6b6b', '#26c6da', '#8e44ad', '#f39c12', '#2c3e50', '#3498db', '#e74c3c'];
            
            $i = 0;
            foreach ($statusBreakdown as $status) {
                if ($status['count'] > 0) {
                    $statusLabels[] = $status['status'];
                    $statusData[] = $status['count'];
                    $statusColors[] = $colorPalette[$i % count($colorPalette)];
                    $i++;
                }
            }
            
            $preparedData['statusChartData'] = [
                'labels' => $statusLabels,
                'datasets' => [[
                    'label' => 'Funnel Status Distribution',
                    'data' => $statusData,
                    'backgroundColor' => $statusColors,
                    'borderColor' => '#2a2a2a',
                    'borderWidth' => 1
                ]]
            ];
        }
        
        // Prepare chart data for team performance
        if (!empty($teamPerformance)) {
            // Sort by total funnels
            usort($teamPerformance, function($a, $b) {
                return $b['total_funnels'] - $a['total_funnels'];
            });
            
            $top10Users = array_slice($teamPerformance, 0, 10);
            
            $userLabels = [];
            $userFunnelData = [];
            $userPositiveData = [];
            
            foreach ($top10Users as $user) {
                $userLabels[] = $user['user_name'];
                $userFunnelData[] = $user['total_funnels'];
                $userPositiveData[] = $user['positive'];
            }
            
            $preparedData['teamChartData'] = [
                'labels' => $userLabels,
                'datasets' => [
                    [
                        'label' => 'Total Funnels',
                        'data' => $userFunnelData,
                        'backgroundColor' => '#5436da',
                        'borderColor' => '#4529b5',
                        'borderWidth' => 1
                    ],
                    [
                        'label' => 'Positive Funnels',
                        'data' => $userPositiveData,
                        'backgroundColor' => '#10a37f',
                        'borderColor' => '#0d8a6a',
                        'borderWidth' => 1
                    ]
                ]
            ];
        }
        
        // Prepare table data for all funnels
        $tableRows = [];
        if (isset($data['totalTasksUserByDatas'])) {
            foreach ($data['totalTasksUserByDatas'] as $task) {
                $tableRows[] = [
                    $task->compname,
                    $task->current_status,
                    $task->mainbd_name,
                    $task->cluster_manager_name,
                    $task->partner_name,
                    $task->clustername,
                    $task->travelType,
                    $task->topspender,
                    $task->keycompany,
                    $task->in_quarter
                ];
            }
        }
        
        $preparedData['tableData'] = [
            'headers' => ['Company', 'Status', 'Main BD', 'Cluster Manager', 'Partner Type', 'Cluster', 'Travel Type', 'Top Spender', 'Key Company', 'Quarter Status'],
            'rows' => $tableRows
        ];
        
        $preparedData['status'] = 'success';
        $preparedData['message'] = 'Funnel data prepared successfully';
        
        return $preparedData;
    }

    private function prepare_specific_funnel_frontend_data($specificFunnel, $data) {
        $specificData = [];
        
        if ($specificFunnel['type'] === 'user') {
            $user = $specificFunnel['data'];
            $specificData = [
                'type' => 'user',
                'user_name' => $user->user_name,
                'user_id' => $user->current_user_id,
                'total_funnels' => $user->total,
                'status_breakdown' => [
                    ['status' => 'Open', 'count' => $user->open],
                    ['status' => 'Reachout', 'count' => $user->reachout],
                    ['status' => 'Tentative', 'count' => $user->tentative],
                    ['status' => 'Positive', 'count' => $user->positive],
                    ['status' => 'Closure', 'count' => $user->closure],
                    ['status' => 'On Boarded', 'count' => $user->on_boarded]
                ],
                'category_breakdown' => [
                    ['category' => 'Key Client', 'count' => $user->Key_Client],
                    ['category' => 'Top Spender', 'count' => $user->Top_Spender],
                    ['category' => 'Potential Client', 'count' => $user->Potential_Client],
                    ['category' => 'Positive Key Client', 'count' => $user->Positive_Key_Client],
                    ['category' => 'Focus Funnel', 'count' => $user->Focus_Funnel]
                ],
                'assignment_breakdown' => [
                    ['assignment' => 'Cluster Manager', 'count' => $user->cluster_manager_assign_funnel],
                    ['assignment' => 'PST', 'count' => $user->PST_assign_funnel],
                    ['assignment' => 'RM North', 'count' => $user->RM_North_assign_funnel]
                ],
                'conversion_metrics' => [
                    'positive_rate' => $user->total > 0 ? round(($user->positive / $user->total) * 100, 1) : 0,
                    'closure_rate' => $user->total > 0 ? round(($user->closure / $user->total) * 100, 1) : 0,
                    'onboarding_rate' => $user->total > 0 ? round(($user->on_boarded / $user->total) * 100, 1) : 0
                ]
            ];
            
        } elseif ($specificFunnel['type'] === 'cluster') {
            $cluster = $specificFunnel['data'];
            $specificData = [
                'type' => 'cluster',
                'cluster_name' => $cluster->clustername,
                'cluster_id' => $cluster->cluster_id,
                'total_funnels' => $cluster->total,
                'location_breakdown' => [
                    ['location' => 'Base Location', 'count' => $cluster->base_location],
                    ['location' => 'OutStation Location', 'count' => $cluster->outStation_location]
                ],
                'location_percentages' => [
                    'base_percentage' => $cluster->total > 0 ? round(($cluster->base_location / $cluster->total) * 100, 1) : 0,
                    'outstation_percentage' => $cluster->total > 0 ? round(($cluster->outStation_location / $cluster->total) * 100, 1) : 0
                ]
            ];
            
            // Add BDs for this cluster
            if (isset($data['totalFunnels']['totalClusterBYClusterNameBYBDDatas'])) {
                $clusterBDs = [];
                foreach ($data['totalFunnels']['totalClusterBYClusterNameBYBDDatas'] as $bd) {
                    if ($bd->cluster_id == $cluster->cluster_id) {
                        $clusterBDs[] = [
                            'user_name' => $bd->username,
                            'user_id' => $bd->user_id,
                            'total_funnels' => $bd->total,
                            'base_location' => $bd->base_location,
                            'outstation_location' => $bd->outStation_location
                        ];
                    }
                }
                $specificData['business_developers'] = $clusterBDs;
            }
            
        } elseif ($specificFunnel['type'] === 'status') {
            $status = $specificFunnel['data'];
            $totalFunnelCount = $data['totalFunnels']['total_funnels_status'][0]->total ?? 1006;
            
            $specificData = [
                'type' => 'status',
                'status_name' => $status['status'],
                'total_count' => $status['count'],
                'percentage_of_total' => $totalFunnelCount > 0 ? round(($status['count'] / $totalFunnelCount) * 100, 1) : 0
            ];
            
            // Add top users with this status
            if (isset($data['totalFunnels']['total_funnel_by_user'])) {
                $statusKey = strtolower(str_replace([' ', '-'], '_', $status['status']));
                $topUsers = [];
                
                foreach ($data['totalFunnels']['total_funnel_by_user'] as $user) {
                    if (isset($user->$statusKey) && $user->$statusKey > 0) {
                        $topUsers[] = [
                            'user_name' => $user->user_name,
                            'count' => $user->$statusKey,
                            'total_funnels' => $user->total,
                            'percentage_of_user_total' => $user->total > 0 ? round(($user->$statusKey / $user->total) * 100, 1) : 0
                        ];
                    }
                }
                
                // Sort by count descending
                usort($topUsers, function($a, $b) {
                    return $b['count'] - $a['count'];
                });
                
                $specificData['top_users'] = array_slice($topUsers, 0, 10);
            }
            
        } elseif ($specificFunnel['type'] === 'partner') {
            $partner = $specificFunnel['data'];
            $partnerName = str_replace('_', ' ', $partner['type']);
            $totalFunnelCount = $data['totalFunnels']['total_funnels_partner'][0]->total ?? 1006;
            
            $specificData = [
                'type' => 'partner',
                'partner_name' => $partnerName,
                'total_count' => $partner['count'],
                'percentage_of_total' => $totalFunnelCount > 0 ? round(($partner['count'] / $totalFunnelCount) * 100, 1) : 0
            ];
            
            // Add top users for this partner type
            if (isset($data['totalFunnels']['total_funnels_partner_user'])) {
                $partnerKey = $partner['type'];
                $topUsers = [];
                
                foreach ($data['totalFunnels']['total_funnels_partner_user'] as $user) {
                    if (isset($user->$partnerKey) && $user->$partnerKey > 0) {
                        $topUsers[] = [
                            'user_name' => $user->username,
                            'count' => $user->$partnerKey,
                            'total_funnels' => $user->total,
                            'percentage_of_user_total' => $user->total > 0 ? round(($user->$partnerKey / $user->total) * 100, 1) : 0
                        ];
                    }
                }
                
                // Sort by count descending
                usort($topUsers, function($a, $b) {
                    return $b['count'] - $a['count'];
                });
                
                $specificData['top_users'] = array_slice($topUsers, 0, 10);
            }
        }
        
        return $specificData;
    }
}