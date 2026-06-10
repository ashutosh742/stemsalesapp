<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TaskConversionSummary_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Menu_model');
        $this->load->model('Report_model');
        $this->load->model('ChatAI_model');
    }

    public function process_TaskConversionSummary_model($message, $analysisType, $sdate, $edate) {
        // Get conversion data (based on your provided array structure)
        $bdconversions = $this->Menu_model->GetTeamConversionBetweenDatesDatas($this->uid, $sdate, $edate, 'all', $this->uid, 'all');
        
        

        $totalTasksUserByDatas   = $this->Menu_model->GetTeamTaskOnSelfOrOtherFunnelTaskListsDats($this->uid,$sdate,$edate,'total_status_change_task',$this->uid,'all',NULL);

        $bdconversionsDatas = [
            'bdconversions' => $bdconversions,
            'totalTasksUserByDatas' => $totalTasksUserByDatas,
        ];


        // If we have conversion data, analyze it
        if (!empty($bdconversions)) {
            $conversionData = $this->prepare_conversion_analysis_data($bdconversions);
            
            // Generate prompt for ChatGPT analysis
            $prompt = $this->generate_conversion_analysis_prompt($message, $conversionData);
            
            // Get ChatGPT response
            $chatgptResponse = $this->ChatAI_model->call_chatgpt_api($prompt, []);

             
                
            return [
                'content' => $chatgptResponse,
                'data' => $bdconversionsDatas
            ];
        } else {
            return [
                'content' => 'No conversion data available for the selected period.',
                'data' => [
                    'status' => 'empty',
                    'message' => 'No conversion data found',
                    'conversionData' => null
                ]
            ];
        }
    }

    public function prepare_conversion_analysis_data($conversionArray) {
        $preparedData = [
            'status' => 'success',
            'message' => 'Conversion data analyzed successfully',
            'conversionData' => [],
            'tableData' => null,
            'chartData' => null,
            'summaryData' => [],
            'flowData' => []
        ];
        
        if (empty($conversionArray) || !is_array($conversionArray)) {
            $preparedData['status'] = 'empty';
            $preparedData['message'] = 'No conversion data available';
            return $preparedData;
        }
        
        // Process conversion data
        $conversionData = [];
        $totalChanges = 0;
        $uniqueConversions = [];
        
        foreach ($conversionArray as $conversion) {
            if (!is_object($conversion)) continue;
            
            // Extract data from object
            $statusChange = $conversion->status_change ?? '';
            $totalChangesValue = $conversion->total_changes ?? 0;
            $statusChangeId = $conversion->status_change_id ?? '';
            
            // Parse status change (e.g., "Positive-NAP → Positive")
            $statusParts = explode(' → ', $statusChange);
            $fromStatus = isset($statusParts[0]) ? trim($statusParts[0]) : '';
            $toStatus = isset($statusParts[1]) ? trim($statusParts[1]) : '';
            
            $conversionItem = [
                'status_change' => $statusChange,
                'from_status' => $fromStatus,
                'to_status' => $toStatus,
                'total_changes' => (int)$totalChangesValue,
                'status_change_id' => $statusChangeId
            ];
            
            $conversionData[] = $conversionItem;
            $totalChanges += (int)$totalChangesValue;
            
            // Track unique conversions for flow analysis
            $uniqueKey = $fromStatus . '_' . $toStatus;
            $uniqueConversions[$uniqueKey] = $conversionItem;
        }
        
        // Sort by total_changes (descending)
        usort($conversionData, function($a, $b) {
            return $b['total_changes'] - $a['total_changes'];
        });
        
        // Prepare table data
        $tableRows = [];
        foreach ($conversionData as $item) {
            $percentage = $totalChanges > 0 ? round(($item['total_changes'] / $totalChanges) * 100, 2) : 0;
            
            $tableRows[] = [
                $item['status_change'],
                $item['from_status'],
                $item['to_status'],
                $item['total_changes'],
                $percentage . '%',
                $item['status_change_id']
            ];
        }
        
        $preparedData['tableData'] = [
            'title' => 'Task Status Conversions',
            'headers' => ['Status Change', 'From Status', 'To Status', 'Count', 'Percentage', 'Change ID'],
            'rows' => $tableRows
        ];
        
        // Prepare chart data
        $chartLabels = [];
        $chartDataValues = [];
        $chartBackgroundColors = [];
        
        $colors = ['#10a37f', '#5436da', '#ffa726', '#ff6b6b', '#26c6da', '#8e44ad', '#f39c12', '#1abc9c', '#3498db', '#e74c3c'];
        
        foreach ($conversionData as $index => $item) {
            $chartLabels[] = $item['status_change'];
            $chartDataValues[] = $item['total_changes'];
            $chartBackgroundColors[] = $colors[$index % count($colors)] ?? '#5436da';
        }
        
        $preparedData['chartData'] = [
            'labels' => $chartLabels,
            'datasets' => [
                [
                    'label' => 'Number of Conversions',
                    'data' => $chartDataValues,
                    'backgroundColor' => $chartBackgroundColors,
                    'borderColor' => '#2a2a2a',
                    'borderWidth' => 1
                ]
            ]
        ];
        
        // Prepare flow chart data (for Sankey diagram visualization)
        $flowNodes = [];
        $flowLinks = [];
        $nodeIndex = 0;
        $nodeMap = [];
        
        // Collect all unique statuses
        $allStatuses = [];
        foreach ($conversionData as $item) {
            if (!in_array($item['from_status'], $allStatuses)) {
                $allStatuses[] = $item['from_status'];
            }
            if (!in_array($item['to_status'], $allStatuses)) {
                $allStatuses[] = $item['to_status'];
            }
        }
        
        // Create nodes
        foreach ($allStatuses as $status) {
            $flowNodes[] = [
                'name' => $status,
                'id' => $nodeIndex
            ];
            $nodeMap[$status] = $nodeIndex;
            $nodeIndex++;
        }
        
        // Create links
        foreach ($conversionData as $item) {
            if (isset($nodeMap[$item['from_status']]) && isset($nodeMap[$item['to_status']])) {
                $flowLinks[] = [
                    'source' => $nodeMap[$item['from_status']],
                    'target' => $nodeMap[$item['to_status']],
                    'value' => $item['total_changes'],
                    'label' => $item['status_change']
                ];
            }
        }
        
        $preparedData['flowData'] = [
            'nodes' => $flowNodes,
            'links' => $flowLinks
        ];
        
        // Calculate summary statistics
        $topConversion = !empty($conversionData) ? $conversionData[0] : null;
        $uniqueStatuses = array_unique(array_merge(
            array_column($conversionData, 'from_status'),
            array_column($conversionData, 'to_status')
        ));
        
        // Find most common source and target statuses
        $sourceCounts = [];
        $targetCounts = [];
        
        foreach ($conversionData as $item) {
            $sourceCounts[$item['from_status']] = ($sourceCounts[$item['from_status']] ?? 0) + $item['total_changes'];
            $targetCounts[$item['to_status']] = ($targetCounts[$item['to_status']] ?? 0) + $item['total_changes'];
        }
        
        arsort($sourceCounts);
        arsort($targetCounts);
        
        $mostCommonSource = !empty($sourceCounts) ? $this->array_key_first($sourceCounts) : 'N/A';
        $mostCommonTarget = !empty($targetCounts) ? $this->array_key_first($targetCounts) : 'N/A';
        
        $preparedData['summaryData'] = [
            'total_conversions' => $totalChanges,
            'unique_conversion_paths' => count($conversionData),
            'unique_statuses' => count($uniqueStatuses),
            'top_conversion' => $topConversion ? $topConversion['status_change'] : 'N/A',
            'top_conversion_count' => $topConversion ? $topConversion['total_changes'] : 0,
            'top_conversion_percentage' => $totalChanges > 0 && $topConversion ? 
                round(($topConversion['total_changes'] / $totalChanges) * 100, 2) : 0,
            'most_common_source_status' => $mostCommonSource,
            'most_common_target_status' => $mostCommonTarget,
            'conversion_data' => $conversionData
        ];
        
        $preparedData['conversionData'] = $conversionData;
        
        return $preparedData;
    }

    public function generate_conversion_analysis_prompt($message, $conversionData) {
        $formattedData = "TASK STATUS CONVERSION ANALYSIS\n\n";
        
        $formattedData .= "OVERALL SUMMARY:\n";
        $formattedData .= "- Total Conversions: {$conversionData['summaryData']['total_conversions']}\n";
        $formattedData .= "- Unique Conversion Paths: {$conversionData['summaryData']['unique_conversion_paths']}\n";
        $formattedData .= "- Unique Status Types: {$conversionData['summaryData']['unique_statuses']}\n";
        $formattedData .= "- Most Common Source Status: {$conversionData['summaryData']['most_common_source_status']}\n";
        $formattedData .= "- Most Common Target Status: {$conversionData['summaryData']['most_common_target_status']}\n\n";
        
        $formattedData .= "TOP CONVERSION:\n";
        $formattedData .= "- Path: {$conversionData['summaryData']['top_conversion']}\n";
        $formattedData .= "- Count: {$conversionData['summaryData']['top_conversion_count']} conversions\n";
        $formattedData .= "- Percentage: {$conversionData['summaryData']['top_conversion_percentage']}% of all conversions\n\n";
        
        $formattedData .= "DETAILED CONVERSION PATHS:\n";
        $conversionList = $conversionData['conversionData'];
        foreach ($conversionList as $item) {
            $percentage = $conversionData['summaryData']['total_conversions'] > 0 ? 
                round(($item['total_changes'] / $conversionData['summaryData']['total_conversions']) * 100, 2) : 0;
            
            $formattedData .= "- {$item['status_change']}: {$item['total_changes']} conversions ({$percentage}%)\n";
        }
        
        // Analyze conversion patterns
        $formattedData .= "\nCONVERSION PATTERN ANALYSIS:\n";
        
        // Group by from_status
        $fromStatusGroups = [];
        foreach ($conversionList as $item) {
            $fromStatus = $item['from_status'];
            if (!isset($fromStatusGroups[$fromStatus])) {
                $fromStatusGroups[$fromStatus] = [];
            }
            $fromStatusGroups[$fromStatus][] = $item;
        }
        
        foreach ($fromStatusGroups as $fromStatus => $conversions) {
            $totalFromThisStatus = array_sum(array_column($conversions, 'total_changes'));
            $formattedData .= "\nConversions FROM '{$fromStatus}':\n";
            
            foreach ($conversions as $conv) {
                $percFrom = $totalFromThisStatus > 0 ? 
                    round(($conv['total_changes'] / $totalFromThisStatus) * 100, 2) : 0;
                $formattedData .= "  → {$conv['to_status']}: {$conv['total_changes']} ({$percFrom}% of conversions from {$fromStatus})\n";
            }
        }
        
        // Group by to_status
        $toStatusGroups = [];
        foreach ($conversionList as $item) {
            $toStatus = $item['to_status'];
            if (!isset($toStatusGroups[$toStatus])) {
                $toStatusGroups[$toStatus] = [];
            }
            $toStatusGroups[$toStatus][] = $item;
        }
        
        $formattedData .= "\n\nCONVERSIONS TO SPECIFIC STATUSES:\n";
        foreach ($toStatusGroups as $toStatus => $conversions) {
            $totalToThisStatus = array_sum(array_column($conversions, 'total_changes'));
            $formattedData .= "\nConversions TO '{$toStatus}' (Total: {$totalToThisStatus}):\n";
            
            foreach ($conversions as $conv) {
                $percTo = $totalToThisStatus > 0 ? 
                    round(($conv['total_changes'] / $totalToThisStatus) * 100, 2) : 0;
                $formattedData .= "  ← {$conv['from_status']}: {$conv['total_changes']} ({$percTo}% of conversions to {$toStatus})\n";
            }
        }
        
        $prompt = "You are a business analytics AI specializing in task and sales funnel analysis. Analyze the following task status conversion data and provide insights:\n\n";
        $prompt .= $formattedData . "\n\n";
        $prompt .= "User Query: {$message}\n\n";
        $prompt .= "Please provide a comprehensive analysis with:\n";
        $prompt .= "1. Key findings about the most common conversion paths\n";
        $prompt .= "2. Analysis of successful conversions (to Positive status)\n";
        $prompt .= "3. Patterns in status progression through the funnel\n";
        $prompt .= "4. Bottlenecks or problematic status transitions\n";
        $prompt .= "5. Recommendations for improving conversion rates\n";
        $prompt .= "6. Insights about customer journey through statuses\n";
        $prompt .= "7. Which statuses are most likely to convert to positive outcomes\n";
        $prompt .= "8. Which status transitions indicate potential issues or drop-offs\n";
        $prompt .= "9. Suggestions for optimizing the task management workflow\n";
        $prompt .= "10. Predictions about future trends based on current patterns\n\n";
        
        $prompt .= "Format your response with clear sections and use specific numbers from the data. Include actionable insights for sales team management.";
        
        return $prompt;
    }


  public  function array_key_first(array $array)
{
    // For empty arrays, return null
    if (empty($array)) {
        return null;
    }
    
    // Reset array pointer and get first key
    reset($array);
    return key($array);
}

}