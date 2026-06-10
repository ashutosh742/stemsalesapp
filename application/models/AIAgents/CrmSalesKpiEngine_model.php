<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CRM KPI + Sales Logic Engine with AI Analysis
 * Works directly with your "LineManagerCallingonRPLeadsLists" task rows.
 */
class CrmSalesKpiEngine_model extends CI_Model
{
    // Controls for huge datasets
    private $MAX_ROWS_PROCESS   = 80000;
    private $MAX_ROWS_SAMPLES   = 30;
    private $TOP_N_USERS        = 20;
    private $TOP_N_COMPANIES    = 30;
    private $TOP_N_TRANSITIONS  = 20;
    private $TOP_N_ALERTS       = 25;
    
    // AI Response Templates
    private $AI_TEMPLATES = [
        'overall_summary' => [
            'title' => "📊 Overall Performance Summary",
            'sections' => [
                'introduction' => "Based on the analysis of {total_tasks} tasks across {unique_users} users and {unique_companies} companies during {period}:",
                'key_metrics' => "**Key Metrics:**",
                'performance_insights' => "**Performance Insights:**",
                'recommendations' => "**Recommendations:**"
            ]
        ],
        'user_performance' => [
            'title' => "👥 User Performance Analysis",
            'sections' => [
                'top_performers' => "**Top Performers ({top_n} users):**",
                'performance_breakdown' => "**Performance Breakdown:**",
                'insights' => "**Key Insights:**"
            ]
        ],
        'company_analysis' => [
            'title' => "🏢 Company Engagement Analysis",
            'sections' => [
                'most_engaged' => "**Most Engaged Companies:**",
                'stuck_companies' => "**Companies Needing Attention:**",
                'insights' => "**Analysis Insights:**"
            ]
        ],
        'call_patterns' => [
            'title' => "📞 Call Patterns & Success Rates",
            'sections' => [
                'success_patterns' => "**Call Success Patterns:**",
                'optimal_timing' => "**Optimal Calling Times:**",
                'insights' => "**Call Strategy Insights:**"
            ]
        ],
        'alerts' => [
            'title' => "⚠️ Critical Alerts & Action Items",
            'sections' => [
                'priority_alerts' => "**Priority Alerts:**",
                'recommendations' => "**Immediate Actions Required:**"
            ]
        ]
    ];

    public function __construct()
    {
        parent::__construct();
        $this->load->model('ChatAI_model'); // Load AI model for enhanced analysis
    }

    /**
     * Main entry: analyze LM calling dataset with AI-enhanced responses
     */
    public function analyze_line_manager_calls(array $rawTasks, string $sdate, string $edate, array $filters = [], bool $enableAI = true) : array
    {
        // Normalize + cap
        $rawTasks = array_slice($rawTasks, 0, $this->MAX_ROWS_PROCESS);
        $rows = $this->normalize_rows($rawTasks);

        // Optional filters
        if (!empty($filters)) {
            $rows = $this->apply_filters($rows, $filters);
        }

        $meta = [
            'sdate' => $sdate,
            'edate' => $edate,
            'rows_processed' => count($rows),
            'filters' => $filters,
            'analysis_date' => date('Y-m-d H:i:s'),
            'period' => $this->format_period($sdate, $edate)
        ];

        // Core KPIs
        $overall = $this->overall_kpis($rows, $edate);
        $userWise = $this->user_wise_kpis($rows, $edate);
        $companyWise = $this->company_wise_kpis($rows, $edate);
        $geoWise = $this->geo_wise_kpis($rows, $edate);
        $taskConversion = $this->task_conversion_kpis($rows);
        $statusConversion = $this->status_conversion_matrix($rows);
        $closingTimeline = $this->closing_timeline_kpis($rows);
        $pipelineFlags = $this->pipeline_flags_kpis($rows);
        $stagnancy = $this->stagnancy_company($rows, $edate);
        $alerts = $this->alerts_sales_logic($rows, $edate);

        // Enhanced Analyses
        $callPatterns = $this->call_success_patterns($rows);
        $proposalAnalysis = $this->proposal_conversion_analysis($rows);
        $clientSegmentation = $this->client_segmentation_analysis($rows);
        $performanceBenchmarks = $this->performance_benchmarks($rows, $edate);
        $remarksSentiment = $this->remarks_sentiment_analysis($rows);

        // Generate AI-enhanced responses
        $aiResponses = [];
        $structuredTables = [];
        
        if ($enableAI) {
            $aiResponses = $this->generate_ai_analysis_responses([
                'overall' => $overall,
                'userWise' => $userWise,
                'companyWise' => $companyWise,
                'callPatterns' => $callPatterns,
                'alerts' => $alerts,
                'taskConversion' => $taskConversion,
                'stagnancy' => $stagnancy,
                'meta' => $meta
            ]);
            
            $structuredTables = $this->generate_structured_tables([
                'userWise' => $userWise,
                'companyWise' => $companyWise,
                'alerts' => $alerts,
                'callPatterns' => $callPatterns
            ]);
        }

        // Samples
        $samples = array_slice($rows, 0, $this->MAX_ROWS_SAMPLES);

        return [
            'meta' => $meta,
            'ai_responses' => $aiResponses,
            'structured_tables' => $structuredTables,
            
            // Raw Data
            'raw_kpis' => [
                'overall' => $overall,
                'task_conversion' => $taskConversion,
                'status_conversion' => $statusConversion,
                'closing_timeline' => $closingTimeline,
                'pipeline_flags' => $pipelineFlags,
                'stagnancy' => $stagnancy,
                'user_wise' => $userWise,
                'company_wise' => $companyWise,
                'geo_wise' => $geoWise,
                'alerts' => $alerts,
                'call_patterns' => $callPatterns,
                'proposal_analysis' => $proposalAnalysis,
                'client_segmentation' => $clientSegmentation,
                'performance_benchmarks' => $performanceBenchmarks,
                'remarks_sentiment' => $remarksSentiment
            ],
            
            'samples' => $samples,
            'summary' => $this->generate_executive_summary($overall, $userWise, $alerts)
        ];
    }

    /* =========================================================
     * AI-Enhanced Response Generation
     * ========================================================= */

    private function generate_ai_analysis_responses(array $data) : array
    {
        $responses = [];
        
        // 1. Overall Summary
        $responses['overall_summary'] = $this->generate_overall_summary_response($data['overall'], $data['meta']);
        
        // 2. User Performance Analysis
        $responses['user_performance'] = $this->generate_user_performance_response($data['userWise']);
        
        // 3. Company Analysis
        $responses['company_analysis'] = $this->generate_company_analysis_response($data['companyWise']);
        
        // 4. Call Patterns
        $responses['call_patterns'] = $this->generate_call_patterns_response($data['callPatterns']);
        
        // 5. Alerts
        $responses['alerts'] = $this->generate_alerts_response($data['alerts']);
        
        // 6. Conversion Analysis
        $responses['conversion_analysis'] = $this->generate_conversion_analysis_response($data['taskConversion']);
        
        // 7. Stagnancy Analysis
        $responses['stagnancy_analysis'] = $this->generate_stagnancy_analysis_response($data['stagnancy']);
        
        // 8. Executive Summary (Combined AI)
        $responses['executive_summary'] = $this->generate_executive_ai_summary($data);
        
        return $responses;
    }

    private function generate_overall_summary_response(array $overall, array $meta) : array
    {
        $successRate = $overall['purpose_success_rate_pct'];
        $completionRate = $overall['completion_rate_pct'];
        $lateRate = $overall['late_rate_pct'];
        
        // Performance rating
        if ($successRate >= 70) $rating = "Excellent";
        elseif ($successRate >= 50) $rating = "Good";
        elseif ($successRate >= 30) $rating = "Fair";
        else $rating = "Needs Improvement";
        
        // Key highlights
        $highlights = [];
        if ($successRate >= 60) {
            $highlights[] = "High purpose achievement rate indicates effective call strategies";
        }
        if ($overall['overdue_scheduled_calls'] > 0) {
            $highlights[] = "{$overall['overdue_scheduled_calls']} overdue scheduled calls need attention";
        }
        if ($lateRate > 20) {
            $highlights[] = "High late rate ({$lateRate}%) suggests scheduling or time management issues";
        }
        
        $response = [
            'title' => "📊 Overall Performance Summary - {$rating}",
            'content' => "**Period:** {$meta['period']}\n" .
                        "**Total Tasks:** {$overall['total_tasks']} | **Unique Users:** {$overall['unique_users']} | **Unique Companies:** {$overall['unique_companies']}\n\n" .
                        
                        "**🎯 Key Performance Indicators:**\n" .
                        "```\n" .
                        "Purpose Success Rate: {$successRate}%\n" .
                        "Task Completion Rate: {$completionRate}%\n" .
                        "Late Task Rate: {$lateRate}%\n" .
                        "Avg Call Duration: " . gmdate("H:i:s", $overall['avg_duration_sec']) . "\n" .
                        "Completed Tasks: {$overall['completed_tasks']}/{$overall['total_tasks']}\n" .
                        "```\n\n" .
                        
                        "**📈 Performance Insights:**\n" .
                        "• Success Rate Analysis: {$this->get_success_analysis($successRate)}\n" .
                        "• Completion Efficiency: {$this->get_completion_analysis($completionRate)}\n" .
                        "• Time Management: Average task duration of " . $this->format_duration($overall['avg_duration_sec']) . "\n\n" .
                        
                        "**🔍 Critical Observations:**\n" .
                        implode("\n", array_map(function($h) { return "• {$h}"; }, $highlights)) . "\n\n" .
                        
                        "**💡 Recommendations:**\n" .
                        $this->get_overall_recommendations($overall)
        ];
        
        return $response;
    }

    private function generate_user_performance_response(array $userWise) : array
    {
        $topUsers = $userWise['top_users'];
        if (empty($topUsers)) {
            return [
                'title' => "👥 User Performance Analysis",
                'content' => "No user performance data available for analysis."
            ];
        }
        
        // Identify top 3 performers
        $top3 = array_slice($topUsers, 0, 3);
        $bottom3 = array_slice($userWise['top_users'], -3);
        
        $topPerformersTable = "| Rank | User | Tasks | Success Rate | Avg Duration |\n";
        $topPerformersTable .= "|------|------|-------|--------------|--------------|\n";
        
        foreach ($top3 as $index => $user) {
            $rank = $index + 1;
            $duration = $this->format_duration_minutes($user['avg_duration_sec']?? 0);
            $topPerformersTable .= "| {$rank} | {$user['user_name']} | {$user['total_tasks']} | {$user['purpose_success_rate_pct']}% | {$duration} |\n";
        }
        
        $insights = [];
        
        // Calculate team averages
        $totalTasks = array_sum(array_column($topUsers, 'total_tasks'));
        $avgSuccessRate = array_sum(array_column($topUsers, 'purpose_success_rate_pct')) / count($topUsers);
        $avgDuration = array_sum(array_column($topUsers, 'avg_duration_sec')) / count($topUsers);
        
        // Identify patterns
        if ($avgSuccessRate > 60) {
            $insights[] = "Team maintains strong average success rate of " . round($avgSuccessRate, 1) . "%";
        }
        
        // Identify outliers
        foreach ($bottom3 as $user) {
            if ($user['purpose_success_rate_pct'] < 30) {
                $insights[] = "{$user['user_name']} needs coaching (success rate: {$user['purpose_success_rate_pct']}%)";
            }
        }
        
        $response = [
            'title' => "👥 User Performance Analysis",
            'content' => "**Total Users Analyzed:** {$userWise['total_users']}\n\n" .
                        
                        "**🏆 Top 3 Performers:**\n" .
                        $topPerformersTable . "\n" .
                        
                        "**📊 Team Performance Overview:**\n" .
                        "• Average Success Rate: " . round($avgSuccessRate, 1) . "%\n" .
                        "• Total Tasks Across Team: {$totalTasks}\n" .
                        "• Average Call Duration: " . $this->format_duration($avgDuration) . "\n\n" .
                        
                        "**🔍 Key Insights:**\n" .
                        implode("\n", array_map(function($i) { return "• {$i}"; }, $insights)) . "\n\n" .
                        
                        "**🎯 Action Items:**\n" .
                        "1. Recognize top performers for their achievements\n" .
                        "2. Schedule coaching sessions for users below 30% success rate\n" .
                        "3. Share best practices from top performers with the team"
        ];
        
        return $response;
    }

    private function generate_company_analysis_response(array $companyWise) : array
    {
        $topCompanies = $companyWise['top_companies_by_tasks'];
        $stuckCompanies = $companyWise['top_stuck_companies'];
        
        if (empty($topCompanies)) {
            return [
                'title' => "🏢 Company Engagement Analysis",
                'content' => "No company data available for analysis."
            ];
        }
        
        $topCompaniesTable = "| Company | Tasks | Success Rate | Last Activity | Status |\n";
        $topCompaniesTable .= "|---------|-------|--------------|---------------|--------|\n";
        
        foreach (array_slice($topCompanies, 0, 5) as $company) {
            $daysAgo = isset($company['days_stagnant']) ? $company['days_stagnant'] . ' days' : 'Recent';
            $status = $company['purpose_success_rate_pct'] >= 50 ? '✅ Active' : '⚠️ Needs Follow-up';
            $topCompaniesTable .= "| " . substr($company['company_name'], 0, 20) . " | {$company['total_tasks']} | {$company['purpose_success_rate_pct']}% | {$daysAgo} | {$status} |\n";
        }
        
        $stuckCompaniesList = "";
        if (!empty($stuckCompanies)) {
            $stuckCompaniesList = "\n**🚨 Companies Needing Immediate Attention:**\n";
            foreach (array_slice($stuckCompanies, 0, 3) as $company) {
                $stuckCompaniesList .= "• **{$company['company_name']}** - {$company['days_stagnant']} days stagnant, {$company['pending_tasks']} pending tasks\n";
            }
        }
        
        // Calculate engagement metrics
        $avgTasksPerCompany = array_sum(array_column($topCompanies, 'total_tasks')) / count($topCompanies);
        $highEngagementCount = count(array_filter($topCompanies, function($c) { 
            return $c['total_tasks'] >= 5; 
        }));
        
        $response = [
            'title' => "🏢 Company Engagement Analysis",
            'content' => "**Total Companies Analyzed:** {$companyWise['total_companies']}\n\n" .
                        
                        "**🏆 Most Engaged Companies (Top 5):**\n" .
                        $topCompaniesTable . "\n" .
                        
                        $stuckCompaniesList . "\n" .
                        
                        "**📈 Engagement Metrics:**\n" .
                        "• Average tasks per company: " . round($avgTasksPerCompany, 1) . "\n" .
                        "• Companies with high engagement (5+ tasks): {$highEngagementCount}\n" .
                        "• Stagnant companies (>7 days): " . count($stuckCompanies) . "\n\n" .
                        
                        "**💡 Strategic Insights:**\n" .
                        "1. Focus resources on top 20% of companies generating 80% of engagement\n" .
                        "2. Reactivation plan needed for stagnant companies\n" .
                        "3. Consider account tiering based on engagement levels"
        ];
        
        return $response;
    }

    private function generate_call_patterns_response(array $callPatterns) : array
    {
        $successByHour = $callPatterns['success_by_hour'];
        $successByDay = $callPatterns['success_by_day'];
        
        // Find best calling hours
        $bestHours = [];
        foreach ($successByHour as $hour => $data) {
            if ($data['total'] >= 5 && $data['success_rate'] >= 50) {
                $bestHours[] = [
                    'hour' => $hour . ':00',
                    'success_rate' => $data['success_rate'],
                    'total_calls' => $data['total']
                ];
            }
        }
        
        // Sort by success rate
        usort($bestHours, function($a, $b) {
            return $b['success_rate'] <=> $a['success_rate'];
        });
        
        $bestHoursTable = "| Time Slot | Success Rate | Sample Size |\n";
        $bestHoursTable .= "|-----------|--------------|-------------|\n";
        
        foreach (array_slice($bestHours, 0, 5) as $hour) {
            $bestHoursTable .= "| {$hour['hour']} | {$hour['success_rate']}% | {$hour['total_calls']} calls |\n";
        }
        
        // Find best days
        $bestDays = [];
        foreach ($successByDay as $day => $data) {
            if ($data['total'] >= 3) {
                $bestDays[$day] = $data['success_rate'];
            }
        }
        arsort($bestDays);
        
        $firstCallSuccess = $callPatterns['first_call_success_rate'];
        
        $response = [
            'title' => "📞 Optimal Calling Strategy Analysis",
            'content' => "**First Call Success Rate:** {$firstCallSuccess}%\n" .
                        "**Multiple Calls Needed Rate:** {$callPatterns['multiple_calls_needed_rate']}%\n\n" .
                        
                        "**⏰ Best Calling Hours (High Success Rates):**\n" .
                        (empty($bestHours) ? "Insufficient data for hour analysis\n" : $bestHoursTable) . "\n" .
                        
                        "**📅 Best Days for Calling:**\n" .
                        $this->format_best_days($bestDays) . "\n\n" .
                        
                        "**🎯 Call Strategy Recommendations:**\n" .
                        "1. **Schedule calls during peak success hours** (9-11 AM, 2-4 PM typically work best)\n" .
                        "2. **Follow-up strategy**: " . ($callPatterns['multiple_calls_needed_rate'] > 40 ? 
                           "Multiple follow-ups are common, establish 3-touch follow-up system" : 
                           "Most conversions happen in first call, focus on quality initial contact") . "\n" .
                        "3. **Call duration optimization**: " . $this->get_duration_insights($callPatterns['duration_vs_success']) . "\n\n" .
                        
                        "**📊 Key Finding:** " . 
                        ($firstCallSuccess >= 50 ? 
                         "Strong first-call success indicates effective qualification and preparation" :
                         "Low first-call success suggests need for better lead qualification")
        ];
        
        return $response;
    }

    private function generate_alerts_response(array $alerts) : array
    {
        if (empty($alerts['items'])) {
            return [
                'title' => "✅ System Status - No Critical Alerts",
                'content' => "**Status:** All systems operational\n\n" .
                            "No critical alerts detected in the current analysis period. " .
                            "This indicates good data quality and process adherence."
            ];
        }
        
        $alertCounts = [];
        foreach ($alerts['items'] as $alert) {
            $type = $alert['type'];
            $alertCounts[$type] = ($alertCounts[$type] ?? 0) + 1;
        }
        
        $alertsTable = "| Priority | Alert Type | Count | Description |\n";
        $alertsTable .= "|----------|------------|-------|-------------|\n";
        
        $priorityMap = [
            'OVERDUE_SCHEDULED' => '🔴 High',
            'PROPOSAL_REQUIRED_NOT_CREATED' => '🟡 Medium',
            'CONFIRMED_CLOSURE_MISSING_TIMELINE' => '🟡 Medium',
            'STATUS_PURPOSE_MISMATCH' => '🔴 High'
        ];
        
        $descriptions = [
            'OVERDUE_SCHEDULED' => 'Scheduled calls not completed',
            'PROPOSAL_REQUIRED_NOT_CREATED' => 'Proposal required but not created',
            'CONFIRMED_CLOSURE_MISSING_TIMELINE' => 'Confirmed closure without timeline',
            'STATUS_PURPOSE_MISMATCH' => 'Status/Purpose data mismatch'
        ];
        
        foreach ($alertCounts as $type => $count) {
            $priority = $priorityMap[$type] ?? '⚪ Low';
            $desc = $descriptions[$type] ?? 'Unknown issue';
            $alertsTable .= "| {$priority} | {$type} | {$count} | {$desc} |\n";
        }
        
        // Get specific examples
        $examples = [];
        foreach (array_slice($alerts['items'], 0, 3) as $alert) {
            $examples[] = "• **{$alert['type']}**: {$alert['user']} - {$alert['company']}";
        }
        
        $response = [
            'title' => "⚠️ Critical Alerts Requiring Action",
            'content' => "**Total Alerts Detected:** {$alerts['count_sample']}\n\n" .
                        
                        "**📋 Alert Summary:**\n" .
                        $alertsTable . "\n" .
                        
                        "**🔍 Top Alert Examples:**\n" .
                        implode("\n", $examples) . "\n\n" .
                        
                        "**🎯 Immediate Action Plan:**\n" .
                        "1. **Review overdue scheduled calls** - Follow up within 24 hours\n" .
                        "2. **Address proposal gaps** - Ensure required proposals are created\n" .
                        "3. **Fix data mismatches** - Update status/purpose alignment\n" .
                        "4. **Set closure timelines** - For confirmed closures without timelines\n\n" .
                        
                        "**📈 Impact Assessment:**\n" .
                        "Resolving these alerts could improve conversion rates by 15-25%"
        ];
        
        return $response;
    }

    private function generate_conversion_analysis_response(array $conversion) : array
    {
        $actionToSuccess = $conversion['action_to_success_rate_pct'];
        $overallSuccess = $conversion['overall_success_rate_pct'];
        
        // Analyze conversion patterns
        $conversionTable = "| Scenario | Count | Percentage | Analysis |\n";
        $conversionTable .= "|----------|-------|------------|----------|\n";
        $conversionTable .= "| Action Taken + Purpose Achieved | {$conversion['AY_PY']} | " . 
                           round(($conversion['AY_PY']/$conversion['total_tasks'])*100, 1) . "% | ✅ Optimal outcome |\n";
        $conversionTable .= "| Action Taken + Purpose Not Achieved | {$conversion['AY_PN']} | " . 
                           round(($conversion['AY_PN']/$conversion['total_tasks'])*100, 1) . "% | ⚠️ Needs improvement |\n";
        $conversionTable .= "| No Action + Purpose Not Achieved | {$conversion['AN_PN']} | " . 
                           round(($conversion['AN_PN']/$conversion['total_tasks'])*100, 1) . "% | ❌ Inactive leads |\n";
        $conversionTable .= "| No Action + Purpose Achieved | {$conversion['AN_PY']} | " . 
                           round(($conversion['AN_PY']/$conversion['total_tasks'])*100, 1) . "% | ⚠️ Data anomaly |\n";
        
        $efficiencyRating = $this->calculate_efficiency_rating($actionToSuccess);
        
        $response = [
            'title' => "🔄 Conversion Funnel Analysis - {$efficiencyRating}",
            'content' => "**Total Tasks in Funnel:** {$conversion['total_tasks']}\n" .
                        "**Action Taken Rate:** " . round(($conversion['actontaken_yes']/$conversion['total_tasks'])*100, 1) . "%\n\n" .
                        
                        "**📊 Conversion Matrix:**\n" .
                        $conversionTable . "\n" .
                        
                        "**🎯 Key Conversion Metrics:**\n" .
                        "```\n" .
                        "Action → Success Rate: {$actionToSuccess}%\n" .
                        "Overall Success Rate: {$overallSuccess}%\n" .
                        "Efficiency Score: " . $this->calculate_efficiency_score($conversion) . "/10\n" .
                        "```\n\n" .
                        
                        "**🔍 Conversion Insights:**\n" .
                        $this->get_conversion_insights($conversion) . "\n\n" .
                        
                        "**💡 Optimization Opportunities:**\n" .
                        "1. **Improve AY_PN ratio** (Action without purpose): Focus on call quality and preparation\n" .
                        "2. **Reduce AN_PN** (Inactive leads): Implement lead reactivation campaigns\n" .
                        "3. **Investigate AN_PY** (Data anomalies): Improve data entry processes"
        ];
        
        return $response;
    }

    private function generate_stagnancy_analysis_response(array $stagnancy) : array
    {
        $buckets = $stagnancy['buckets'];
        $stuckCompanies = $stagnancy['top_stuck_companies'];
        
        $stagnancyTable = "| Stagnancy Period | Company Count | Risk Level |\n";
        $stagnancyTable .= "|------------------|---------------|------------|\n";
        
        $riskLevels = [
            '0-3' => '🟢 Low',
            '4-7' => '🟡 Medium',
            '8-14' => '🟠 High',
            '15-30' => '🔴 Critical',
            '31-60' => '🔴 Critical+',
            '61+' => '⚫ Lost'
        ];
        
        foreach ($buckets as $period => $count) {
            $risk = $riskLevels[$period] ?? '⚪ Unknown';
            $stagnancyTable .= "| {$period} days | {$count} | {$risk} |\n";
        }
        
        $criticalCount = ($buckets['15-30'] ?? 0) + ($buckets['31-60'] ?? 0) + ($buckets['61+'] ?? 0);
        
        $stuckCompaniesList = "";
        if (!empty($stuckCompanies)) {
            $stuckCompaniesList = "\n**🚨 Most Stagnant Companies:**\n";
            foreach (array_slice($stuckCompanies, 0, 5) as $company) {
                $stuckCompaniesList .= "• **{$company['company_name']}** - {$company['days_stagnant']} days stagnant, " .
                                      "Owner: {$company['last_owner_user']}, Status: {$company['status_bucket']}\n";
            }
        }
        
        $response = [
            'title' => "⏳ Lead Stagnancy Analysis",
            'content' => "**Total Companies Tracked:** {$stagnancy['companies_tracked']}\n" .
                        "**Critical Stagnancy (>15 days):** {$criticalCount} companies\n\n" .
                        
                        "**📊 Stagnancy Distribution:**\n" .
                        $stagnancyTable . "\n" .
                        
                        $stuckCompaniesList . "\n" .
                        
                        "**📈 Stagnancy Risk Analysis:**\n" .
                        "• **Low Risk (0-7 days):** " . (($buckets['0-3'] ?? 0) + ($buckets['4-7'] ?? 0)) . " companies - Normal follow-up cycle\n" .
                        "• **High Risk (8-30 days):** " . (($buckets['8-14'] ?? 0) + ($buckets['15-30'] ?? 0)) . " companies - Urgent attention needed\n" .
                        "• **Critical Risk (30+ days):** " . (($buckets['31-60'] ?? 0) + ($buckets['61+'] ?? 0)) . " companies - Reactivation campaigns required\n\n" .
                        
                        "**🎯 Reactivation Strategy:**\n" .
                        "1. **0-7 days**: Regular follow-up schedule\n" .
                        "2. **8-14 days**: Escalate to manager, special offers\n" .
                        "3. **15-30 days**: Executive outreach, re-qualification\n" .
                        "4. **30+ days**: Consider lead recycling or closure"
        ];
        
        return $response;
    }

    private function generate_executive_ai_summary(array $data) : array
    {
        $overall = $data['overall'];
        $userWise = $data['userWise'];
        $alerts = $data['alerts'];
        $meta = $data['meta'];
        
        $successRate = $overall['purpose_success_rate_pct'];
        $completionRate = $overall['completion_rate_pct'];
        
        // Generate performance score
        $performanceScore = $this->calculate_performance_score([
            'success_rate' => $successRate,
            'completion_rate' => $completionRate,
            'late_rate' => $overall['late_rate_pct'],
            'alert_count' => $alerts['count_sample'],
            'unique_companies' => $overall['unique_companies'],
            'total_tasks' => $overall['total_tasks']
        ]);
        
        $scoreOutOf100 = round($performanceScore * 100);
        
        // Performance tier
        if ($scoreOutOf100 >= 80) {
            $tier = "Excellent";
            $emoji = "🏆";
            $color = "green";
        } elseif ($scoreOutOf100 >= 60) {
            $tier = "Good";
            $emoji = "✅";
            $color = "blue";
        } elseif ($scoreOutOf100 >= 40) {
            $tier = "Fair";
            $emoji = "⚠️";
            $color = "orange";
        } else {
            $tier = "Needs Improvement";
            $emoji = "🔴";
            $color = "red";
        }
        
        // Top 3 insights
        $insights = [];
        
        if ($successRate >= 60) {
            $insights[] = "Strong purpose achievement rate indicates effective sales execution";
        } elseif ($successRate < 40) {
            $insights[] = "Low success rate suggests need for sales training or process improvement";
        }
        
        if ($overall['overdue_scheduled_calls'] > 10) {
            $insights[] = "High number of overdue calls indicates scheduling or follow-up issues";
        }
        
        if ($userWise['total_users'] > 0) {
            $topUser = $userWise['top_users'][0] ?? null;
            if ($topUser && $topUser['purpose_success_rate_pct'] > 70) {
                $insights[] = "Top performer {$topUser['user_name']} achieving {$topUser['purpose_success_rate_pct']}% success rate";
            }
        }
        
        // Recommendations
        $recommendations = [];
        
        if ($successRate < 50) {
            $recommendations[] = "Implement sales training focused on call effectiveness";
        }
        
        if ($overall['late_rate_pct'] > 20) {
            $recommendations[] = "Review and optimize scheduling processes";
        }
        
        if ($alerts['count_sample'] > 10) {
            $recommendations[] = "Address data quality and process adherence issues";
        }
        
        $response = [
            'title' => "{$emoji} Executive Summary - Performance: {$tier}",
            'content' => "**📅 Analysis Period:** {$meta['period']}\n" .
                        "**📊 Performance Score:** {$scoreOutOf100}/100 ({$tier})\n\n" .
                        
                        "**🎯 Key Metrics at a Glance:**\n" .
                        "```\n" .
                        "Purpose Success Rate: {$successRate}%\n" .
                        "Task Completion Rate: {$completionRate}%\n" .
                        "Total Tasks: {$overall['total_tasks']}\n" .
                        "Unique Companies: {$overall['unique_companies']}\n" .
                        "Active Users: {$userWise['total_users']}\n" .
                        "Critical Alerts: {$alerts['count_sample']}\n" .
                        "```\n\n" .
                        
                        "**🔍 Top Insights:**\n" .
                        implode("\n", array_map(function($i) { return "• {$i}"; }, array_slice($insights, 0, 3))) . "\n\n" .
                        
                        "**🚀 Quick Wins & Opportunities:**\n" .
                        "1. **Immediate Focus**: " . ($alerts['count_sample'] > 0 ? 
                           "Resolve {$alerts['count_sample']} critical alerts" : 
                           "Maintain current processes") . "\n" .
                        "2. **Performance Boost**: " . 
                           ($successRate < 60 ? "Increase success rate through targeted training" :
                           "Leverage high performers' strategies across team") . "\n" .
                        "3. **Efficiency Gain**: " . 
                           ($overall['late_rate_pct'] > 15 ? "Reduce late tasks by optimizing scheduling" :
                           "Maintain current time management practices") . "\n\n" .
                        
                        "**📈 Next 30-Day Focus Areas:**\n" .
                        implode("\n", array_map(function($r) { return "• {$r}"; }, array_slice($recommendations, 0, 3))) . "\n\n" .
                        
                        "**💬 Final Assessment:**\n" .
                        $this->get_final_assessment($scoreOutOf100, $overall, $alerts)
        ];
        
        return [
            'response' => $response,
            'score' => $scoreOutOf100,
            'tier' => $tier,
            'color' => $color
        ];
    }

    /* =========================================================
     * Structured Table Generation
     * ========================================================= */

    private function generate_structured_tables(array $data) : array
    {
        $tables = [];
        
        // 1. Top Users Table
        if (!empty($data['userWise']['top_users'])) {
            $tables['top_users'] = [
                'title' => '🏆 Top Performing Users',
                'headers' => ['Rank', 'User Name', 'Total Tasks', 'Completed', 'Success Rate', 'Avg Duration', 'AY_PY', 'AY_PN'],
                'rows' => $this->generate_top_users_table($data['userWise']['top_users'])
            ];
        }
        
        // 2. Company Engagement Table
        if (!empty($data['companyWise']['top_companies_by_tasks'])) {
            $tables['top_companies'] = [
                'title' => '🏢 Most Engaged Companies',
                'headers' => ['Company', 'Total Tasks', 'Completed', 'Success Rate', 'Days Stagnant', 'Closing Timeline', 'Status'],
                'rows' => $this->generate_companies_table($data['companyWise']['top_companies_by_tasks'])
            ];
        }
        
        // 3. Alerts Table
        if (!empty($data['alerts']['items'])) {
            $tables['alerts'] = [
                'title' => '⚠️ Critical Alerts',
                'headers' => ['Type', 'User', 'Company', 'Details', 'Priority'],
                'rows' => $this->generate_alerts_table($data['alerts']['items'])
            ];
        }
        
        // 4. Call Patterns Table
        if (!empty($data['callPatterns']['success_by_hour'])) {
            $tables['call_patterns'] = [
                'title' => '📞 Best Calling Times',
                'headers' => ['Time Slot', 'Total Calls', 'Successful Calls', 'Success Rate', 'Recommendation'],
                'rows' => $this->generate_call_patterns_table($data['callPatterns']['success_by_hour'])
            ];
        }
        
        // 5. Stagnancy Distribution Table
        if (!empty($data['stagnancy']['buckets'] ?? [])) {
            $tables['stagnancy'] = [
                'title' => '⏳ Lead Stagnancy Distribution',
                'headers' => ['Stagnancy Period', 'Company Count', 'Percentage', 'Risk Level', 'Action Required'],
                'rows' => $this->generate_stagnancy_table($data['stagnancy']['buckets'])
            ];
        }
        
        return $tables;
    }

    private function generate_top_users_table(array $users) : array
    {
        $rows = [];
        foreach (array_slice($users, 0, 10) as $index => $user) {
            $rank = $index + 1;
            $successColor = $user['purpose_success_rate_pct'] >= 60 ? '🟢' : 
                          ($user['purpose_success_rate_pct'] >= 40 ? '🟡' : '🔴');
            $duration = $this->format_duration_minutes($user['avg_duration_sec'] ?? 0);
            
            $rows[] = [
                $rank,
                $user['user_name'],
                $user['total_tasks'],
                $user['completed_tasks'],
                $successColor . ' ' . $user['purpose_success_rate_pct'] . '%',
                $duration,
                $user['AY_PY'],
                $user['AY_PN']
            ];
        }
        return $rows;
    }

    private function generate_companies_table(array $companies) : array
    {
        $rows = [];
        foreach (array_slice($companies, 0, 10) as $company) {
            $successRate = $company['purpose_success_rate_pct'];
            $successIcon = $successRate >= 60 ? '✅' : ($successRate >= 40 ? '⚠️' : '❌');
            $stagnantIcon = ($company['days_stagnant'] ?? 0) > 7 ? '⏳' : '🕐';
            $status = $successRate >= 50 ? 'Active' : 'Needs Follow-up';
            
            $rows[] = [
                substr($company['company_name'], 0, 25),
                $company['total_tasks'],
                $company['completed_tasks'],
                $successIcon . ' ' . $successRate . '%',
                $stagnantIcon . ' ' . ($company['days_stagnant'] ?? 0),
                $company['closing_timeline'] ?: 'Not set',
                $status
            ];
        }
        return $rows;
    }

    private function generate_alerts_table(array $alerts) : array
    {
        $rows = [];
        $priorityIcons = [
            'OVERDUE_SCHEDULED' => '🔴',
            'PROPOSAL_REQUIRED_NOT_CREATED' => '🟡',
            'CONFIRMED_CLOSURE_MISSING_TIMELINE' => '🟡',
            'STATUS_PURPOSE_MISMATCH' => '🔴'
        ];
        
        $priorityText = [
            'OVERDUE_SCHEDULED' => 'High',
            'PROPOSAL_REQUIRED_NOT_CREATED' => 'Medium',
            'CONFIRMED_CLOSURE_MISSING_TIMELINE' => 'Medium',
            'STATUS_PURPOSE_MISMATCH' => 'High'
        ];
        
        foreach (array_slice($alerts, 0, 10) as $alert) {
            $icon = $priorityIcons[$alert['type']] ?? '⚪';
            $details = $this->short($alert['remarks'] ?? $alert['closure_level'] ?? 'No details', 30);
            
            $rows[] = [
                $alert['type'],
                substr($alert['user'], 0, 15),
                substr($alert['company'], 0, 20),
                $details,
                $icon . ' ' . $priorityText[$alert['type']]
            ];
        }
        return $rows;
    }

    private function generate_call_patterns_table(array $successByHour) : array
    {
        $rows = [];
        ksort($successByHour);
        
        foreach ($successByHour as $hour => $data) {
            if ($data['total'] < 3) continue;
            
            $successRate = $data['success_rate'];
            $recommendation = '';
            
            if ($successRate >= 60) {
                $recommendation = 'Optimal time';
            } elseif ($successRate >= 40) {
                $recommendation = 'Good time';
            } elseif ($successRate >= 20) {
                $recommendation = 'Average';
            } else {
                $recommendation = 'Avoid if possible';
            }
            
            $timeSlot = str_pad($hour, 2, '0', STR_PAD_LEFT) . ':00-' . 
                       str_pad(($hour + 1), 2, '0', STR_PAD_LEFT) . ':00';
            
            $rows[] = [
                $timeSlot,
                $data['total'],
                $data['success'],
                $successRate . '%',
                $recommendation
            ];
        }
        
        return array_slice($rows, 0, 10);
    }

    private function generate_stagnancy_table(array $buckets) : array
    {
        $rows = [];
        $total = array_sum($buckets);
        
        $riskActions = [
            '0-3' => 'Regular follow-up',
            '4-7' => 'Schedule call this week',
            '8-14' => 'Escalate to manager',
            '15-30' => 'Executive outreach',
            '31-60' => 'Reactivation campaign',
            '61+' => 'Consider lead closure'
        ];
        
        $riskLevels = [
            '0-3' => '🟢 Low',
            '4-7' => '🟡 Medium',
            '8-14' => '🟠 High',
            '15-30' => '🔴 Critical',
            '31-60' => '🔴 Critical+',
            '61+' => '⚫ Lost'
        ];
        
        foreach ($buckets as $period => $count) {
            if ($total > 0) {
                $percentage = round(($count / $total) * 100, 1);
                $rows[] = [
                    $period . ' days',
                    $count,
                    $percentage . '%',
                    $riskLevels[$period] ?? 'Unknown',
                    $riskActions[$period] ?? 'Review'
                ];
            }
        }
        
        return $rows;
    }

    /* =========================================================
     * Helper Methods for AI Responses
     * ========================================================= */

    private function get_success_analysis(float $successRate) : string
    {
        if ($successRate >= 70) {
            return "Excellent performance! Consistently achieving objectives indicates strong sales execution.";
        } elseif ($successRate >= 50) {
            return "Good performance. Room for improvement but maintaining healthy conversion rates.";
        } elseif ($successRate >= 30) {
            return "Average performance. Consider reviewing call scripts and qualification processes.";
        } else {
            return "Needs immediate attention. Low success rates suggest fundamental issues in sales approach.";
        }
    }

    private function get_completion_analysis(float $completionRate) : string
    {
        if ($completionRate >= 80) {
            return "High completion rate indicates good task management and follow-through.";
        } elseif ($completionRate >= 60) {
            return "Moderate completion rate. Some tasks may be slipping through the cracks.";
        } else {
            return "Low completion rate. Consider implementing better task tracking and accountability.";
        }
    }

    private function get_overall_recommendations(array $overall) : string
    {
        $recommendations = [];
        
        if ($overall['purpose_success_rate_pct'] < 50) {
            $recommendations[] = "Implement sales training focused on call effectiveness and objection handling";
        }
        
        if ($overall['late_rate_pct'] > 20) {
            $recommendations[] = "Review scheduling processes and implement reminders for upcoming calls";
        }
        
        if ($overall['overdue_scheduled_calls'] > 5) {
            $recommendations[] = "Address overdue calls immediately and implement follow-up escalation process";
        }
        
        if ($overall['avg_duration_sec'] > 1800) { // More than 30 minutes
            $recommendations[] = "Optimize call durations - aim for 15-20 minute focused conversations";
        }
        
        if (empty($recommendations)) {
            return "Current processes are working well. Maintain current practices and focus on continuous improvement.";
        }
        
        return implode("\n", array_map(function($r) { return "• {$r}"; }, $recommendations));
    }

    private function format_duration(int $seconds) : string
    {
        if ($seconds < 60) {
            return "{$seconds} seconds";
        } elseif ($seconds < 3600) {
            $minutes = floor($seconds / 60);
            return "{$minutes} minutes";
        } else {
            $hours = floor($seconds / 3600);
            $minutes = floor(($seconds % 3600) / 60);
            return "{$hours}h {$minutes}m";
        }
    }

    private function format_duration_minutes(int $seconds) : string
    {
        $minutes = round($seconds / 60, 1);
        return "{$minutes}m";
    }

    private function format_best_days(array $bestDays) : string
    {
        if (empty($bestDays)) return "Insufficient data for day analysis";
        
        $output = [];
        foreach ($bestDays as $day => $rate) {
            $emoji = $rate >= 60 ? '✅' : ($rate >= 40 ? '⚠️' : '❌');
            $output[] = "{$emoji} {$day}: {$rate}% success";
        }
        return implode("\n", $output);
    }

    private function get_duration_insights(array $durationData) : string
    {
        if (empty($durationData)) return "Insufficient duration data for analysis";
        
        // Find optimal duration range (highest success rate)
        $bestRange = '';
        $bestRate = 0;
        
        foreach ($durationData as $range => $data) {
            if ($data['total'] >= 5 && $data['success_rate'] > $bestRate) {
                $bestRate = $data['success_rate'];
                $bestRange = $range;
            }
        }
        
        if ($bestRange && $bestRate > 0) {
            return "Calls around {$bestRange} show highest success rate ({$bestRate}%)";
        }
        
        return "Aim for 10-20 minute calls for optimal engagement";
    }

    private function calculate_efficiency_rating(float $actionToSuccess) : string
    {
        if ($actionToSuccess >= 70) return "Highly Efficient";
        if ($actionToSuccess >= 50) return "Efficient";
        if ($actionToSuccess >= 30) return "Moderately Efficient";
        return "Needs Optimization";
    }

    private function calculate_efficiency_score(array $conversion) : float
    {
        $score = 0;
        
        // Action taken rate weight
        $actionRate = ($conversion['actontaken_yes'] / $conversion['total_tasks']) * 100;
        $score += ($actionRate / 100) * 3; // 3 points max
        
        // Action to success rate weight
        $successRate = $conversion['action_to_success_rate_pct'];
        $score += ($successRate / 100) * 4; // 4 points max
        
        // AY_PY ratio weight
        $aypyRatio = ($conversion['AY_PY'] / $conversion['total_tasks']) * 100;
        $score += ($aypyRatio / 100) * 3; // 3 points max
        
        return round($score, 1);
    }

    private function get_conversion_insights(array $conversion) : string
    {
        $insights = [];
        
        $aypyRatio = ($conversion['AY_PY'] / $conversion['total_tasks']) * 100;
        $aypnRatio = ($conversion['AY_PN'] / $conversion['total_tasks']) * 100;
        $anpnRatio = ($conversion['AN_PN'] / $conversion['total_tasks']) * 100;
        
        if ($aypyRatio >= 40) {
            $insights[] = "Strong conversion rate - good alignment between action and results";
        }
        
        if ($aypnRatio > 30) {
            $insights[] = "High action without purpose - suggests need for better qualification or call focus";
        }
        
        if ($anpnRatio > 20) {
            $insights[] = "Significant inactive leads - consider reactivation or lead recycling";
        }
        
        if ($conversion['AN_PY'] > 0) {
            $insights[] = "Data anomalies detected - some tasks marked as purpose achieved without action";
        }
        
        return empty($insights) ? "Standard conversion patterns observed" : implode("\n", $insights);
    }

    private function calculate_performance_score(array $metrics) : float
    {
        $score = 0;
        $maxScore = 100;
        
        // Success rate weight: 30%
        $successRate = $metrics['success_rate'];
        $score += ($successRate / 100) * 30;
        
        // Completion rate weight: 25%
        $completionRate = $metrics['completion_rate'];
        $score += ($completionRate / 100) * 25;
        
        // Late rate penalty: -15% max
        $lateRate = $metrics['late_rate'];
        $score -= min($lateRate / 100 * 15, 15);
        
        // Alert penalty: -10% max (5 alerts = -10%)
        $alertCount = $metrics['alert_count'];
        $alertPenalty = min($alertCount * 2, 10);
        $score -= $alertPenalty;
        
        // Engagement bonus: +10% max (based on tasks per company)
        $tasksPerCompany = $metrics['total_tasks'] / max($metrics['unique_companies'], 1);
        $engagementBonus = min($tasksPerCompany * 2, 10);
        $score += $engagementBonus;
        
        // Normalize to 0-100
        $score = max(0, min($score, $maxScore));
        
        return $score / 100; // Return as decimal 0-1
    }

    private function get_final_assessment(float $score, array $overall, array $alerts) : string
    {
        if ($score >= 80) {
            return "Outstanding performance across all key metrics. The team is operating at peak efficiency with minimal issues. Maintain current strategies while looking for incremental improvements.";
        } elseif ($score >= 60) {
            return "Solid performance with room for targeted improvements. Focus on the specific areas identified in the alerts and recommendations to reach the next level.";
        } elseif ($score >= 40) {
            return "Average performance requiring attention to several areas. Prioritize addressing critical alerts and improving success rates through training and process adjustments.";
        } else {
            return "Performance needs significant improvement. Immediate action required on multiple fronts. Start with the highest priority alerts and implement structured training programs.";
        }
    }

    private function format_period(string $sdate, string $edate) : string
    {
        return date('M j', strtotime($sdate)) . ' - ' . date('M j, Y', strtotime($edate));
    }

    private function generate_executive_summary(array $overall, array $userWise, array $alerts) : array
    {
        return [
            'performance_score' => $this->calculate_performance_score([
                'success_rate' => $overall['purpose_success_rate_pct'],
                'completion_rate' => $overall['completion_rate_pct'],
                'late_rate' => $overall['late_rate_pct'],
                'alert_count' => $alerts['count_sample'],
                'unique_companies' => $overall['unique_companies'],
                'total_tasks' => $overall['total_tasks']
            ]),
            'key_metrics' => [
                'success_rate' => $overall['purpose_success_rate_pct'],
                'completion_rate' => $overall['completion_rate_pct'],
                'active_users' => $userWise['total_users'],
                'critical_alerts' => $alerts['count_sample']
            ],
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }

    /* =========================================================
     * Original KPI Methods (from previous code)
     * ========================================================= */

  
 /* =========================================================
       NORMALIZATION
       ========================================================= */

    private function normalize_rows(array $rawTasks) : array
    {
        $out = [];

        foreach ($rawTasks as $t) {
            if (!is_object($t) && !is_array($t)) continue;
            $a = (array)$t;

            $userId   = $a['task_user_id'] ?? ($a['assignedto_id'] ?? null);
            $userName = (string)($a['task_username'] ?? '');

            $cid      = $a['cid'] ?? null;
            $cname    = (string)($a['compname'] ?? '');

            $taskId   = $a['task_id'] ?? null;
            $taskName = (string)($a['task_name'] ?? '');

            $createdAt   = $a['fwd_date'] ?? null;
            $scheduledAt = $a['appointmentdatetime'] ?? null;
            $initiatedAt = $a['initiateddt'] ?? null;
            $updatedAt   = $a['updated_at'] ?? null;

            $actTaken    = $this->yn($a['actontaken'] ?? null);
            $purpose     = $this->yn($a['purpose_achieved'] ?? null);

            $proposalReq = $this->yn($a['proposal_require'] ?? null);
            $mtype       = (string)($a['mtype'] ?? '');
            $meetingType = (string)($a['meeting_type'] ?? '');

            $currentStatus = (string)($a['current_status'] ?? '');
            $prevStatus    = (string)($a['task_time_status'] ?? '');
            $newStatus     = (string)($a['task_time_new_status'] ?? '');

            $remarks        = trim((string)($a['remarks'] ?? ''));
            $specialRemarks = trim((string)($a['special_remarks'] ?? ''));
            $closingTimeline= trim((string)($a['closing_timeline'] ?? ''));

            $lateMsg = trim((string)($a['late_remarks_message'] ?? ''));

            $specialJson = null;
            if ($specialRemarks !== '' && (strpos($specialRemarks, '{') === 0 || strpos($specialRemarks, '[') === 0)) {
                $parsed = json_decode($specialRemarks, true);
                if (is_array($parsed) && json_last_error() === JSON_ERROR_NONE) {
                    $specialJson = $parsed;
                }
            }

            $closureLevel = is_array($specialJson)
                ? ($specialJson['Level Categorization for Closure Timeline - Post-Manager Call'] ?? null)
                : null;

            if ($closingTimeline === '' && is_array($specialJson)) {
                $closingTimeline = (string)($specialJson['Closing Timeline'] ?? '');
            }

            $geo = $this->derive_geo($a);

            $durationSec = $this->parse_duration_to_seconds((string)($a['total_time_taken'] ?? ''));

            $createdTs   = $this->to_ts($createdAt);
            $scheduledTs = $this->to_ts($scheduledAt);
            $initiatedTs = $this->to_ts($initiatedAt);
            $updatedTs   = $this->to_ts($updatedAt);

            $lastTs = $updatedTs ?: ($initiatedTs ?: ($scheduledTs ?: $createdTs));

            $tn = strtolower($taskName);
            $isCallMeeting = (strpos($tn, 'call') !== false || strpos($tn, 'meeting') !== false);

            $out[] = [
                'user_id' => $userId ? (int)$userId : null,
                'user_name' => $userName,

                'company_id' => $cid ? (int)$cid : null,
                'company_name' => $cname,

                'geo' => $geo,

                'task_id' => $taskId ? (int)$taskId : null,
                'task_name' => $taskName,

                'created_at' => $createdAt,
                'scheduled_at' => $scheduledAt,
                'initiated_at' => $initiatedAt,
                'updated_at' => $updatedAt,
                'last_activity_ts' => $lastTs,

                'duration_sec' => $durationSec,
                'is_late' => ($lateMsg !== ''),

                'actontaken' => $actTaken,
                'purpose_achieved' => $purpose,
                'proposal_require' => $proposalReq,

                'current_status' => $currentStatus,
                'prev_status' => $prevStatus,
                'new_status' => $newStatus,
                'status_bucket' => $this->status_bucket($currentStatus, $purpose, $actTaken),

                'potential' => $this->yn($a['potential'] ?? null),
                'topspender' => $this->yn($a['topspender'] ?? null),
                'upsell_client' => $this->yn($a['upsell_client'] ?? null),
                'focus_funnel' => $this->yn($a['focus_funnel'] ?? null),
                'keycompany' => $this->yn($a['keycompany'] ?? null),
                'pkclient' => $this->yn($a['pkclient'] ?? null),
                'priorityc' => $this->yn($a['priorityc'] ?? null),

                'meeting_type' => $meetingType,
                'mtype' => $mtype,

                'remarks' => $remarks,
                'special_remarks' => $specialRemarks,
                'special_remarks_json' => $specialJson,
                'closure_level' => $closureLevel,
                'closing_timeline' => $closingTimeline,

                'is_call_meeting' => $isCallMeeting,

                'main_bd_name' => (string)($a['main_bd_name'] ?? ''),
                'main_bd_manager_name' => (string)($a['main_bd_manager_name'] ?? ''),
                'acm_name' => (string)($a['acm_co_id_name'] ?? '')
            ];
        }

        return $out;
    }

    private function apply_filters(array $rows, array $filters) : array
    {
        return array_values(array_filter($rows, function($r) use ($filters) {
            if (!empty($filters['user_id']) && (int)$r['user_id'] !== (int)$filters['user_id']) return false;
            if (!empty($filters['company_id']) && (int)$r['company_id'] !== (int)$filters['company_id']) return false;
            if (!empty($filters['status_bucket']) && (string)$r['status_bucket'] !== (string)$filters['status_bucket']) return false;
            if (!empty($filters['geo']) && stripos((string)$r['geo'], (string)$filters['geo']) === false) return false;
            return true;
        }));
    }

    /* =========================================================
       OVERALL KPIs
       ========================================================= */

    private function overall_kpis(array $rows, string $edate) : array
    {
        $total = count($rows);
        $completed = 0;
        $pending = 0;

        $purposeYes = 0;
        $purposeNo = 0;

        $late = 0;
        $durationSum = 0;

        $uniqueCompanies = [];
        $uniqueUsers = [];

        $statusBucketDist = [];
        $statusDist = [];

        foreach ($rows as $r) {
            if ($r['company_id']) $uniqueCompanies[$r['company_id']] = true;
            if ($r['user_id']) $uniqueUsers[$r['user_id']] = true;

            if ($r['actontaken'] === true) $completed++; else $pending++;

            if ($r['purpose_achieved'] === true) $purposeYes++;
            if ($r['purpose_achieved'] === false) $purposeNo++;

            if ($r['is_late']) $late++;
            $durationSum += (int)$r['duration_sec'];

            $sb = (string)$r['status_bucket'];
            $statusBucketDist[$sb] = (int)($statusBucketDist[$sb] ?? 0) + 1;

            $st = trim((string)$r['current_status']);
            if ($st === '') $st = 'OPEN';
            $statusDist[$st] = (int)($statusDist[$st] ?? 0) + 1;
        }

        arsort($statusDist);

        $completionRate = $total ? round(($completed/$total)*100, 2) : 0;
        $purposeRate = ($purposeYes + $purposeNo) ? round(($purposeYes/($purposeYes + $purposeNo))*100, 2) : 0;

        return [
            'total_tasks' => $total,
            'unique_users' => count($uniqueUsers),
            'unique_companies' => count($uniqueCompanies),

            'completed_tasks' => $completed,
            'pending_tasks' => $pending,
            'completion_rate_pct' => $completionRate,

            'purpose_yes' => $purposeYes,
            'purpose_no' => $purposeNo,
            'purpose_success_rate_pct' => $purposeRate,

            'late_tasks' => $late,
            'late_rate_pct' => $total ? round(($late/$total)*100, 2) : 0,

            'total_duration_sec' => $durationSum,
            'avg_duration_sec' => $total ? round($durationSum/$total, 2) : 0,

            'status_bucket_distribution' => $statusBucketDist,
            'status_distribution_top' => array_slice($statusDist, 0, 20, true)
        ];
    }

    /* =========================================================
       MEETING KPIs (from your task rows)
       ========================================================= */

    private function meeting_kpis(array $rows, string $edate) : array
    {
        $refTs = strtotime($edate . ' 23:59:59');

        $totalCallMeeting = 0;
        $scheduled = 0;
        $initiated = 0;
        $completed = 0;

        $success = 0;
        $failed = 0;

        $overdueScheduledNotDone = 0;
        $overdueScheduledNotInitiated = 0;

        $delaySum = 0;
        $delayCnt = 0;

        foreach ($rows as $r) {
            if (!$r['is_call_meeting']) continue;

            $totalCallMeeting++;

            $schedTs = $this->to_ts($r['scheduled_at']);
            $initTs  = $this->to_ts($r['initiated_at']);

            if ($schedTs) $scheduled++;
            if ($initTs) $initiated++;

            if ($r['actontaken'] === true) $completed++;

            if ($r['purpose_achieved'] === true) $success++;
            if ($r['purpose_achieved'] === false) $failed++;

            if ($schedTs && $schedTs < $refTs && $r['actontaken'] !== true) $overdueScheduledNotDone++;
            if ($schedTs && $schedTs < $refTs && !$initTs) $overdueScheduledNotInitiated++;

            if ($schedTs && $initTs && $initTs >= $schedTs) {
                $delaySum += ($initTs - $schedTs);
                $delayCnt++;
            }
        }

        return [
            'total_call_meeting_tasks' => $totalCallMeeting,
            'scheduled_count' => $scheduled,
            'initiated_count' => $initiated,
            'completed_count' => $completed,

            'success_count' => $success,
            'failed_count' => $failed,
            'success_rate_pct' => ($success + $failed) ? round(($success/($success+$failed))*100, 2) : 0,

            'overdue_scheduled_not_completed' => $overdueScheduledNotDone,
            'overdue_scheduled_not_initiated' => $overdueScheduledNotInitiated,

            'avg_delay_scheduled_to_initiated_sec' => $delayCnt ? round($delaySum/$delayCnt, 2) : 0
        ];
    }

    /* =========================================================
       TASK CONVERSION KPIs (AY_PY etc)
       ========================================================= */

    private function task_conversion_kpis(array $rows) : array
    {
        $ay_py=0; $ay_pn=0; $an_pn=0; $an_py=0;
        $actYes=0; $actNo=0;

        foreach ($rows as $r) {
            $a = $r['actontaken'];
            $p = $r['purpose_achieved'];

            if ($a === true) $actYes++;
            if ($a === false) $actNo++;

            if ($a === true && $p === true) $ay_py++;
            elseif ($a === true && $p === false) $ay_pn++;
            elseif ($a === false && $p === false) $an_pn++;
            elseif ($a === false && $p === true) $an_py++;
        }

        $total = count($rows);

        return [
            'total_tasks' => $total,
            'actontaken_yes' => $actYes,
            'actontaken_no' => $actNo,

            'AY_PY' => $ay_py,
            'AY_PN' => $ay_pn,
            'AN_PN' => $an_pn,
            'AN_PY' => $an_py,

            'action_to_success_rate_pct' => ($actYes > 0) ? round(($ay_py/$actYes)*100, 2) : 0,
            'overall_success_rate_pct' => ($total > 0) ? round(($ay_py/$total)*100, 2) : 0
        ];
    }

    /* =========================================================
       STATUS CONVERSION MATRIX
       ========================================================= */

    private function status_conversion_matrix(array $rows) : array
    {
        $matrix = [];
        $changes = 0;

        foreach ($rows as $r) {
            $from = trim((string)$r['prev_status']);
            $to   = trim((string)$r['new_status']);
            if ($from === '' || $to === '') continue;

            if (strcasecmp($from, $to) !== 0) $changes++;

            if (!isset($matrix[$from])) $matrix[$from] = [];
            $matrix[$from][$to] = (int)($matrix[$from][$to] ?? 0) + 1;
        }

        $list = [];
        foreach ($matrix as $from => $tos) {
            foreach ($tos as $to => $cnt) {
                $list[] = ['from' => $from, 'to' => $to, 'count' => (int)$cnt];
            }
        }
        usort($list, function($a,$b){ return (int)$b['count'] <=> (int)$a['count']; });

        return [
            'status_changes_count' => $changes,
            'matrix' => $matrix,
            'top_transitions' => array_slice($list, 0, $this->TOP_N_TRANSITIONS)
        ];
    }

    /* =========================================================
       CLOSING TIMELINE + SPECIAL REMARKS
       ========================================================= */

    private function closing_timeline_kpis(array $rows) : array
    {
        $withSpecial = 0;
        $withClosing = 0;
        $timelineDist = [];
        $closureLevel = [];
        $confirmed = 0;

        // question-level stats from JSON (your exact structure)
        $questions = [
            'Decision Readiness' => 0,
            'Approval Stage' => 0,
            'Funding Window' => 0,
            'Partner Prioritization' => 0,
            'Next Steps Clarity' => 0
        ];
        $questionsNA = $questions;
        $questionsFilled = $questions;

        foreach ($rows as $r) {
            if (trim((string)$r['special_remarks']) !== '') $withSpecial++;

            $ct = trim((string)$r['closing_timeline']);
            if ($ct !== '') {
                $withClosing++;
                $timelineDist[$ct] = (int)($timelineDist[$ct] ?? 0) + 1;
            }

            $lvl = trim((string)($r['closure_level'] ?? ''));
            if ($lvl !== '') {
                $closureLevel[$lvl] = (int)($closureLevel[$lvl] ?? 0) + 1;
                if (stripos($lvl, 'confirmed') !== false) $confirmed++;
            }

            // JSON question completion
            if (is_array($r['special_remarks_json'])) {
                $j = $r['special_remarks_json'];

                foreach ($j as $k => $v) {
                    $vv = strtolower(trim((string)$v));

                    if (stripos($k, 'Decision Readiness') !== false) {
                        $questions['Decision Readiness']++;
                        if ($vv === 'na' || $vv === '') $questionsNA['Decision Readiness']++; else $questionsFilled['Decision Readiness']++;
                    }
                    if (stripos($k, 'Approval Stage') !== false) {
                        $questions['Approval Stage']++;
                        if ($vv === 'na' || $vv === '') $questionsNA['Approval Stage']++; else $questionsFilled['Approval Stage']++;
                    }
                    if (stripos($k, 'Funding Window') !== false) {
                        $questions['Funding Window']++;
                        if ($vv === 'na' || $vv === '') $questionsNA['Funding Window']++; else $questionsFilled['Funding Window']++;
                    }
                    if (stripos($k, 'Partner Prioritization') !== false) {
                        $questions['Partner Prioritization']++;
                        if ($vv === 'na' || $vv === '') $questionsNA['Partner Prioritization']++; else $questionsFilled['Partner Prioritization']++;
                    }
                    if (stripos($k, 'Next Steps Clarity') !== false || stripos($k, 'timeframe') !== false) {
                        $questions['Next Steps Clarity']++;
                        if ($vv === 'na' || $vv === '') $questionsNA['Next Steps Clarity']++; else $questionsFilled['Next Steps Clarity']++;
                    }
                }
            }
        }

        arsort($timelineDist);
        arsort($closureLevel);

        $total = count($rows);

        return [
            'special_remarks_tasks' => $withSpecial,
            'special_remarks_rate_pct' => $total ? round(($withSpecial/$total)*100, 2) : 0,

            'closing_timeline_tasks' => $withClosing,
            'closing_timeline_rate_pct' => $total ? round(($withClosing/$total)*100, 2) : 0,

            'closing_timeline_distribution' => $timelineDist,
            'closure_level_distribution' => $closureLevel,
            'confirmed_closure_mentions' => $confirmed,

            'special_json_question_stats' => [
                'asked_counts' => $questions,
                'na_counts' => $questionsNA,
                'filled_counts' => $questionsFilled
            ]
        ];
    }

    /* =========================================================
       PIPELINE FLAGS (potential/topspender/etc) unique-company basis
       ========================================================= */

    private function pipeline_flags_kpis(array $rows) : array
    {
        $byCompany = [];

        foreach ($rows as $r) {
            $cid = $r['company_id'] ?: 0;
            if (!$cid) continue;

            if (!isset($byCompany[$cid])) {
                $byCompany[$cid] = [
                    'potential' => false,
                    'topspender' => false,
                    'upsell_client' => false,
                    'focus_funnel' => false,
                    'keycompany' => false,
                    'pkclient' => false,
                    'priorityc' => false
                ];
            }

            foreach (['potential','topspender','upsell_client','focus_funnel','keycompany','pkclient','priorityc'] as $k) {
                if ($r[$k] === true) $byCompany[$cid][$k] = true;
            }
        }

        $tot = count($byCompany);
        $counts = [
            'companies_total' => $tot,
            'potential_yes' => 0,
            'topspender_yes' => 0,
            'upsell_yes' => 0,
            'focus_funnel_yes' => 0,
            'keycompany_yes' => 0,
            'pkclient_yes' => 0,
            'priority_yes' => 0
        ];

        foreach ($byCompany as $cid => $f) {
            if ($f['potential']) $counts['potential_yes']++;
            if ($f['topspender']) $counts['topspender_yes']++;
            if ($f['upsell_client']) $counts['upsell_yes']++;
            if ($f['focus_funnel']) $counts['focus_funnel_yes']++;
            if ($f['keycompany']) $counts['keycompany_yes']++;
            if ($f['pkclient']) $counts['pkclient_yes']++;
            if ($f['priorityc']) $counts['priority_yes']++;
        }

        $rates = [];
        foreach ($counts as $k => $v) {
            if ($k === 'companies_total') continue;
            $rates[$k.'_pct'] = $tot ? round(($v/$tot)*100, 2) : 0;
        }

        return ['counts' => $counts, 'rates' => $rates];
    }

    /* =========================================================
       STAGNANCY (company-level last activity)
       ========================================================= */

    private function stagnancy_company(array $rows, string $edate) : array
    {
        $refTs = strtotime($edate . ' 23:59:59');

        $lastByCompany = [];
        foreach ($rows as $r) {
            $cid = $r['company_id'] ?: 0;
            if (!$cid) continue;

            $ts = $r['last_activity_ts'] ?: null;
            if (!$ts) continue;

            if (!isset($lastByCompany[$cid]) || $ts > $lastByCompany[$cid]['ts']) {
                $lastByCompany[$cid] = [
                    'ts' => $ts,
                    'company_id' => $cid,
                    'company_name' => $r['company_name'],
                    'user_name' => $r['user_name'],
                    'status_bucket' => $r['status_bucket'],
                    'current_status' => $r['current_status']
                ];
            }
        }

        $bucketsDef = [
            '0-3' => [0,3],
            '4-7' => [4,7],
            '8-14' => [8,14],
            '15-30' => [15,30],
            '31-60' => [31,60],
            '61+' => [61,999999]
        ];
        $buckets = array_fill_keys(array_keys($bucketsDef), 0);

        $list = [];
        foreach ($lastByCompany as $cid => $info) {
            $days = (int)floor(($refTs - $info['ts']) / 86400);
            if ($days < 0) $days = 0;

            foreach ($bucketsDef as $bn => $rg) {
                if ($days >= $rg[0] && $days <= $rg[1]) { $buckets[$bn]++; break; }
            }

            $list[] = [
                'company_id' => $info['company_id'],
                'company_name' => $info['company_name'],
                'last_activity' => date('Y-m-d H:i:s', $info['ts']),
                'days_stagnant' => $days,
                'last_owner_user' => $info['user_name'],
                'status_bucket' => $info['status_bucket'],
                'current_status' => $info['current_status']
            ];
        }

        usort($list, function($a,$b){ return (int)$b['days_stagnant'] <=> (int)$a['days_stagnant']; });

        return [
            'buckets' => $buckets,
            'top_stuck_companies' => array_slice($list, 0, 20),
            'companies_tracked' => count($lastByCompany)
        ];
    }

    /* =========================================================
       USER / COMPANY / GEO TOP LISTS
       ========================================================= */

    private function user_wise_kpis(array $rows) : array
    {
        $users = [];

        foreach ($rows as $r) {
            $uid = $r['user_id'] ?: 0;
            if (!$uid) continue;

            if (!isset($users[$uid])) {
                $users[$uid] = [
                    'user_id' => $uid,
                    'user_name' => $r['user_name'],
                    'total_tasks' => 0,
                    'completed_tasks' => 0,
                    'pending_tasks' => 0,
                    'purpose_yes' => 0,
                    'purpose_no' => 0,
                    'late_tasks' => 0,
                    'status_bucket_dist' => [],
                    'unique_companies_set' => []
                ];
            }

            $users[$uid]['total_tasks']++;
            if ($r['actontaken'] === true) $users[$uid]['completed_tasks']++; else $users[$uid]['pending_tasks']++;
            if ($r['purpose_achieved'] === true) $users[$uid]['purpose_yes']++;
            if ($r['purpose_achieved'] === false) $users[$uid]['purpose_no']++;
            if ($r['is_late']) $users[$uid]['late_tasks']++;

            $sb = (string)$r['status_bucket'];
            $users[$uid]['status_bucket_dist'][$sb] = (int)($users[$uid]['status_bucket_dist'][$sb] ?? 0) + 1;

            if ($r['company_id']) $users[$uid]['unique_companies_set'][$r['company_id']] = true;
        }

        $list = array_values($users);
        foreach ($list as &$u) {
            $u['completion_rate_pct'] = $u['total_tasks'] ? round(($u['completed_tasks']/$u['total_tasks'])*100, 2) : 0;
            $u['purpose_success_rate_pct'] = ($u['purpose_yes']+$u['purpose_no']) ? round(($u['purpose_yes']/($u['purpose_yes']+$u['purpose_no']))*100, 2) : 0;
            $u['unique_companies'] = count($u['unique_companies_set']);
            unset($u['unique_companies_set']);
        }
        unset($u);

        usort($list, function($a,$b){ return (int)$b['total_tasks'] <=> (int)$a['total_tasks']; });

        return [
            'top_users' => array_slice($list, 0, $this->TOP_N_USERS),
            'total_users' => count($list)
        ];
    }

    private function company_wise_kpis(array $rows, string $edate) : array
    {
        $companies = [];
        $refTs = strtotime($edate . ' 23:59:59');

        foreach ($rows as $r) {
            $cid = $r['company_id'] ?: 0;
            if (!$cid) continue;

            if (!isset($companies[$cid])) {
                $companies[$cid] = [
                    'company_id' => $cid,
                    'company_name' => $r['company_name'],
                    'geo' => $r['geo'],
                    'total_tasks' => 0,
                    'completed_tasks' => 0,
                    'pending_tasks' => 0,
                    'purpose_yes' => 0,
                    'purpose_no' => 0,
                    'has_special_remarks' => false,
                    'closing_timeline' => '',
                    'closure_level' => '',
                    'last_activity_ts' => $r['last_activity_ts'] ?: null,
                    'last_status' => $r['current_status']
                ];
            }

            $c =& $companies[$cid];

            $c['total_tasks']++;
            if ($r['actontaken'] === true) $c['completed_tasks']++; else $c['pending_tasks']++;
            if ($r['purpose_achieved'] === true) $c['purpose_yes']++;
            if ($r['purpose_achieved'] === false) $c['purpose_no']++;

            if (!$c['has_special_remarks'] && trim((string)$r['special_remarks']) !== '') $c['has_special_remarks'] = true;
            if ($c['closing_timeline'] === '' && trim((string)$r['closing_timeline']) !== '') $c['closing_timeline'] = (string)$r['closing_timeline'];
            if ($c['closure_level'] === '' && trim((string)$r['closure_level']) !== '') $c['closure_level'] = (string)$r['closure_level'];

            if ($r['last_activity_ts'] && (!$c['last_activity_ts'] || $r['last_activity_ts'] > $c['last_activity_ts'])) {
                $c['last_activity_ts'] = $r['last_activity_ts'];
                $c['last_status'] = $r['current_status'];
            }
        }

        $list = [];
        foreach ($companies as $cid => $c) {
            $days = 0;
            if ($c['last_activity_ts']) {
                $days = (int)floor(($refTs - $c['last_activity_ts']) / 86400);
                if ($days < 0) $days = 0;
            }
            $c['days_stagnant'] = $days;
            $c['completion_rate_pct'] = $c['total_tasks'] ? round(($c['completed_tasks']/$c['total_tasks'])*100, 2) : 0;
            $c['purpose_success_rate_pct'] = ($c['purpose_yes']+$c['purpose_no']) ? round(($c['purpose_yes']/($c['purpose_yes']+$c['purpose_no']))*100, 2) : 0;
            unset($c['last_activity_ts']);
            $list[] = $c;
        }

        usort($list, function($a,$b){ return (int)$b['total_tasks'] <=> (int)$a['total_tasks']; });
        $topByTasks = array_slice($list, 0, $this->TOP_N_COMPANIES);

        $list2 = $list;
        usort($list2, function($a,$b){
            $sa = ((int)$a['pending_tasks'] > 0) ? (int)$a['days_stagnant'] : -1;
            $sb = ((int)$b['pending_tasks'] > 0) ? (int)$b['days_stagnant'] : -1;
            return $sb <=> $sa;
        });
        $topStuck = array_slice($list2, 0, 20);

        return [
            'top_companies_by_tasks' => $topByTasks,
            'top_stuck_companies' => $topStuck,
            'total_companies' => count($list)
        ];
    }

    private function geo_wise_kpis(array $rows) : array
    {
        $geo = [];

        foreach ($rows as $r) {
            $g = trim((string)$r['geo']);
            if ($g === '') $g = 'Unknown';

            if (!isset($geo[$g])) {
                $geo[$g] = [
                    'geo' => $g,
                    'total_tasks' => 0,
                    'completed_tasks' => 0,
                    'pending_tasks' => 0,
                    'purpose_yes' => 0,
                    'purpose_no' => 0
                ];
            }

            $geo[$g]['total_tasks']++;
            if ($r['actontaken'] === true) $geo[$g]['completed_tasks']++; else $geo[$g]['pending_tasks']++;
            if ($r['purpose_achieved'] === true) $geo[$g]['purpose_yes']++;
            if ($r['purpose_achieved'] === false) $geo[$g]['purpose_no']++;
        }

        $list = array_values($geo);
        foreach ($list as &$g) {
            $g['completion_rate_pct'] = $g['total_tasks'] ? round(($g['completed_tasks']/$g['total_tasks'])*100, 2) : 0;
            $g['purpose_success_rate_pct'] = ($g['purpose_yes']+$g['purpose_no']) ? round(($g['purpose_yes']/($g['purpose_yes']+$g['purpose_no']))*100, 2) : 0;
        }
        unset($g);

        usort($list, function($a,$b){ return (int)$b['total_tasks'] <=> (int)$a['total_tasks']; });

        return [
            'top_geos' => array_slice($list, 0, $this->TOP_N_GEOS),
            'total_geos' => count($list)
        ];
    }

    /* =========================================================
       GROUPINGS (permutation combinations) - limited top results
       ========================================================= */

    private function build_groupings(array $rows, string $edate) : array
    {
        $groupings = [];

        // Standard permutations (safe + useful)
        $groupings['user_geo']          = $this->group_by($rows, ['user_name','geo'], $edate);
        $groupings['user_status']       = $this->group_by($rows, ['user_name','status_bucket'], $edate);
        $groupings['geo_status']        = $this->group_by($rows, ['geo','status_bucket'], $edate);
        $groupings['company_status']    = $this->group_by($rows, ['company_name','status_bucket'], $edate);
        $groupings['company_user']      = $this->group_by($rows, ['company_name','user_name'], $edate);

        return $groupings;
    }

    private function group_by(array $rows, array $dims, string $edate) : array
    {
        $refTs = strtotime($edate . ' 23:59:59');
        $map = [];

        foreach ($rows as $r) {
            $parts = [];
            foreach ($dims as $d) $parts[] = (string)($r[$d] ?? '');
            $key = implode('||', $parts);

            if (!isset($map[$key])) {
                $map[$key] = [
                    'dims' => array_combine($dims, $parts),
                    'total_tasks' => 0,
                    'completed_tasks' => 0,
                    'pending_tasks' => 0,
                    'purpose_yes' => 0,
                    'purpose_no' => 0,
                    'positive_bucket' => 0,
                    'negative_bucket' => 0,
                    'open_bucket' => 0,
                    'max_last_ts' => $r['last_activity_ts'] ?: null
                ];
            }

            $g =& $map[$key];
            $g['total_tasks']++;

            if ($r['actontaken'] === true) $g['completed_tasks']++; else $g['pending_tasks']++;
            if ($r['purpose_achieved'] === true) $g['purpose_yes']++;
            if ($r['purpose_achieved'] === false) $g['purpose_no']++;

            if ($r['status_bucket'] === 'POSITIVE') $g['positive_bucket']++;
            if ($r['status_bucket'] === 'NEGATIVE') $g['negative_bucket']++;
            if ($r['status_bucket'] === 'OPEN/TO_WORK' || $r['status_bucket'] === 'TENTATIVE/TO_WORK') $g['open_bucket']++;

            if ($r['last_activity_ts'] && (!$g['max_last_ts'] || $r['last_activity_ts'] > $g['max_last_ts'])) {
                $g['max_last_ts'] = $r['last_activity_ts'];
            }
        }

        $list = [];
        foreach ($map as $k => $g) {
            $g['completion_rate_pct'] = $g['total_tasks'] ? round(($g['completed_tasks']/$g['total_tasks'])*100, 2) : 0;
            $den = ($g['purpose_yes']+$g['purpose_no']);
            $g['purpose_success_rate_pct'] = $den ? round(($g['purpose_yes']/$den)*100, 2) : 0;

            $days = 0;
            if (!empty($g['max_last_ts'])) {
                $days = (int)floor(($refTs - $g['max_last_ts']) / 86400);
                if ($days < 0) $days = 0;
            }
            $g['days_since_last_activity'] = $days;
            unset($g['max_last_ts']);

            $list[] = $g;
        }

        usort($list, function($a,$b){ return (int)$b['total_tasks'] <=> (int)$a['total_tasks']; });
        return array_slice($list, 0, $this->TOP_N_GROUPS);
    }

    /* =========================================================
       OPPORTUNITY + RISK SCORING (sales logic)
       ========================================================= */

    private function opportunity_and_risk_scoring(array $rows, string $edate) : array
    {
        // Aggregate per company
        $refTs = strtotime($edate . ' 23:59:59');
        $c = [];

        foreach ($rows as $r) {
            $cid = $r['company_id'] ?: 0;
            if (!$cid) continue;

            if (!isset($c[$cid])) {
                $c[$cid] = [
                    'company_id' => $cid,
                    'company_name' => $r['company_name'],
                    'geo' => $r['geo'],
                    'owner' => $r['user_name'],
                    'total' => 0,
                    'pending' => 0,
                    'positive' => 0,
                    'negative' => 0,
                    'purpose_yes' => 0,
                    'purpose_no' => 0,
                    'confirmed_closure' => 0,
                    'has_closing_timeline' => 0,
                    'has_special_remarks' => 0,
                    'flags' => [
                        'potential' => false,
                        'topspender' => false,
                        'priorityc' => false,
                        'keycompany' => false,
                        'focus_funnel' => false,
                        'upsell_client' => false,
                        'pkclient' => false
                    ],
                    'last_ts' => $r['last_activity_ts'] ?: null
                ];
            }

            $c[$cid]['total']++;
            if ($r['actontaken'] !== true) $c[$cid]['pending']++;
            if ($r['status_bucket'] === 'POSITIVE') $c[$cid]['positive']++;
            if ($r['status_bucket'] === 'NEGATIVE') $c[$cid]['negative']++;
            if ($r['purpose_achieved'] === true) $c[$cid]['purpose_yes']++;
            if ($r['purpose_achieved'] === false) $c[$cid]['purpose_no']++;

            if (trim((string)$r['closing_timeline']) !== '') $c[$cid]['has_closing_timeline'] = 1;
            if (trim((string)$r['special_remarks']) !== '') $c[$cid]['has_special_remarks'] = 1;
            if (stripos((string)($r['closure_level'] ?? ''), 'confirmed') !== false) $c[$cid]['confirmed_closure'] = 1;

            foreach (array_keys($c[$cid]['flags']) as $k) {
                if ($r[$k] === true) $c[$cid]['flags'][$k] = true;
            }

            if ($r['last_activity_ts'] && (!$c[$cid]['last_ts'] || $r['last_activity_ts'] > $c[$cid]['last_ts'])) {
                $c[$cid]['last_ts'] = $r['last_activity_ts'];
            }
        }

        $opps = [];
        $risks = [];

        foreach ($c as $cid => $x) {
            $days = 0;
            if (!empty($x['last_ts'])) {
                $days = (int)floor(($refTs - $x['last_ts']) / 86400);
                if ($days < 0) $days = 0;
            }

            $flagScore = 0;
            $reasons = [];

            // opportunity weights (tune as needed)
            if ($x['flags']['potential']) { $flagScore += 10; $reasons[] = 'Potential'; }
            if ($x['flags']['topspender']) { $flagScore += 10; $reasons[] = 'TopSpender'; }
            if ($x['flags']['priorityc']) { $flagScore += 10; $reasons[] = 'Priority'; }
            if ($x['flags']['keycompany']) { $flagScore += 8; $reasons[] = 'KeyCompany'; }
            if ($x['flags']['focus_funnel']) { $flagScore += 8; $reasons[] = 'FocusFunnel'; }
            if ($x['flags']['upsell_client']) { $flagScore += 6; $reasons[] = 'Upsell'; }
            if ($x['flags']['pkclient']) { $flagScore += 5; $reasons[] = 'PKClient'; }

            $posRatio = $x['total'] ? ($x['positive'] / $x['total']) : 0;
            $negRatio = $x['total'] ? ($x['negative'] / $x['total']) : 0;

            $oppScore = 0;
            $oppScore += $flagScore;
            $oppScore += round($posRatio * 20, 2);
            $oppScore += $x['confirmed_closure'] ? 20 : 0;
            $oppScore += $x['has_closing_timeline'] ? 5 : 0;
            $oppScore += min(10, $x['purpose_yes'] * 1.5);

            // stagnancy penalty for opportunities if pending and old
            if ($x['pending'] > 0) $oppScore -= min(15, $days * 0.4);

            $riskScore = 0;
            $riskReasons = [];

            $riskScore += round($negRatio * 25, 2);
            if ($x['pending'] > 0) { $riskScore += min(25, $days * 0.5); $riskReasons[] = "Stagnant {$days}d"; }
            if (!$x['has_special_remarks']) { $riskScore += 8; $riskReasons[] = 'No Special Remarks'; }
            if (!$x['has_closing_timeline']) { $riskScore += 8; $riskReasons[] = 'No Closing Timeline'; }
            if ($x['purpose_no'] > 0) { $riskScore += min(10, $x['purpose_no'] * 1.5); $riskReasons[] = 'Purpose No'; }

            $opps[] = [
                'company_id' => $x['company_id'],
                'company_name' => $x['company_name'],
                'geo' => $x['geo'],
                'owner' => $x['owner'],
                'days_stagnant' => $days,
                'score' => round(max($oppScore, 0), 2),
                'reasons' => array_slice($reasons, 0, 6),
                'positive_tasks' => $x['positive'],
                'pending_tasks' => $x['pending'],
                'confirmed_closure' => (int)$x['confirmed_closure'],
                'closing_timeline_set' => (int)$x['has_closing_timeline']
            ];

            $risks[] = [
                'company_id' => $x['company_id'],
                'company_name' => $x['company_name'],
                'geo' => $x['geo'],
                'owner' => $x['owner'],
                'days_stagnant' => $days,
                'score' => round(max($riskScore, 0), 2),
                'reasons' => array_slice($riskReasons, 0, 6),
                'negative_tasks' => $x['negative'],
                'pending_tasks' => $x['pending']
            ];
        }

        usort($opps, function($a,$b){ return (float)$b['score'] <=> (float)$a['score']; });
        usort($risks, function($a,$b){ return (float)$b['score'] <=> (float)$a['score']; });

        return [
            'opportunities' => array_slice($opps, 0, $this->TOP_N_OPPORTUNITY),
            'risks' => array_slice($risks, 0, $this->TOP_N_RISK)
        ];
    }

    /* =========================================================
       TEXT INSIGHTS (simple keyword signals for AI)
       ========================================================= */

    private function text_insights(array $rows) : array
    {
        $remarksBlob = '';
        $specialBlob = '';

        $limit = 2000; // safety
        $count = 0;

        foreach ($rows as $r) {
            if ($count >= $limit) break;
            if (!empty($r['remarks'])) { $remarksBlob .= ' ' . strtolower($r['remarks']); $count++; }
        }

        $count = 0;
        foreach ($rows as $r) {
            if ($count >= $limit) break;
            if (!empty($r['special_remarks'])) { $specialBlob .= ' ' . strtolower($r['special_remarks']); $count++; }
        }

        return [
            'top_keywords_remarks' => $this->top_keywords($remarksBlob),
            'top_keywords_special_remarks' => $this->top_keywords($specialBlob)
        ];
    }

    private function top_keywords(string $text) : array
    {
        $text = preg_replace('/[^a-z0-9 ]/i', ' ', $text);
        $words = preg_split('/\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);

        $stop = [
            'the','and','to','of','in','a','is','it','for','on','with','that','this','as','are',
            'we','they','he','she','you','i','was','were','be','been','have','has','had',
            'will','would','can','could','should','may','might','not','no','yes','na',
            'client','call','called','meeting','connect','connected','spoke','spoken'
        ];

        $cnt = [];
        foreach ($words as $w) {
            if (strlen($w) < 3) continue;
            if (in_array($w, $stop, true)) continue;
            $cnt[$w] = (int)($cnt[$w] ?? 0) + 1;
        }

        arsort($cnt);
        return array_slice($cnt, 0, 15, true);
    }

    /* =========================================================
       ALERTS (data quality + sales logic gaps)
       ========================================================= */

    private function alerts_sales_logic(array $rows, string $edate) : array
    {
        $refTs = strtotime($edate . ' 23:59:59');
        $alerts = [];

        foreach ($rows as $r) {
            if (count($alerts) >= $this->TOP_N_ALERTS) break;

            // overdue scheduled but not completed
            $sched = $this->to_ts($r['scheduled_at']);
            if ($sched && $sched < $refTs && $r['actontaken'] !== true) {
                $alerts[] = [
                    'type' => 'OVERDUE_SCHEDULED',
                    'user' => $r['user_name'],
                    'company' => $r['company_name'],
                    'scheduled_at' => $r['scheduled_at'],
                    'status' => $r['current_status'],
                    'remarks' => $this->short($r['remarks'], 120)
                ];
                continue;
            }

            // status positive but purpose no (your sample has this)
            if (stripos((string)$r['current_status'], 'positive') !== false && $r['purpose_achieved'] === false) {
                $alerts[] = [
                    'type' => 'STATUS_PURPOSE_MISMATCH',
                    'user' => $r['user_name'],
                    'company' => $r['company_name'],
                    'current_status' => $r['current_status'],
                    'purpose_achieved' => 'no',
                    'remarks' => $this->short($r['remarks'], 120)
                ];
                continue;
            }

            // confirmed closure but missing closing timeline
            $lvl = strtolower((string)($r['closure_level'] ?? ''));
            if ($lvl !== '' && strpos($lvl, 'confirmed') !== false && trim((string)$r['closing_timeline']) === '') {
                $alerts[] = [
                    'type' => 'CONFIRMED_CLOSURE_MISSING_TIMELINE',
                    'user' => $r['user_name'],
                    'company' => $r['company_name'],
                    'closure_level' => $r['closure_level'],
                    'status' => $r['current_status']
                ];
                continue;
            }
        }

        return [
            'count_sample' => count($alerts),
            'items' => $alerts
        ];
    }

    /* =========================================================
       HELPERS
       ========================================================= */

    private function yn($v)
    {
        if ($v === null) return null;
        $s = strtolower(trim((string)$v));
        if ($s === '') return null;
        if ($s === 'yes' || $s === 'y' || $s === '1' || $s === 'true') return true;
        if ($s === 'no'  || $s === 'n' || $s === '0' || $s === 'false') return false;
        return null;
    }

    private function to_ts($dt)
    {
        $dt = trim((string)$dt);
        if ($dt === '') return null;
        $ts = strtotime($dt);
        return ($ts === false) ? null : $ts;
    }

    private function parse_duration_to_seconds(string $timeString) : int
    {
        $timeString = strtolower($timeString);
        $seconds = 0;

        if (preg_match('/(\d+)\s*hours?/', $timeString, $m)) $seconds += ((int)$m[1]) * 3600;
        if (preg_match('/(\d+)\s*minutes?/', $timeString, $m)) $seconds += ((int)$m[1]) * 60;
        if (preg_match('/(\d+)\s*seconds?/', $timeString, $m)) $seconds += ((int)$m[1]);

        return (int)$seconds;
    }

    private function status_bucket(string $currentStatus, $purposeAchieved, $actTaken) : string
    {
        $st = strtoupper(trim($currentStatus));
        if ($st === '' || $st === 'OPEN') return 'OPEN/TO_WORK';
        if (strpos($st, 'TENTATIVE') !== false) return 'TENTATIVE/TO_WORK';
        if (strpos($st, 'NOT INTERESTED') !== false) return 'NEGATIVE';
        if (strpos($st, 'NEGATIVE') !== false) return 'NEGATIVE';
        if (strpos($st, 'POSITIVE') !== false) return 'POSITIVE';

        if ($purposeAchieved === true) return 'POSITIVE';
        if ($purposeAchieved === false) return 'NEGATIVE';
        if ($actTaken === false) return 'OPEN/TO_WORK';

        return 'OTHER';
    }

    private function derive_geo(array $a) : string
    {
        $geoCandidates = [
            $a['rm_north_co_id_name'] ?? '',
            $a['rm_east_co_id_name'] ?? '',
            $a['rm_west_co_id_name'] ?? '',
            $a['rm_south_co_id_name'] ?? '',
            $a['ash_nae_co_id_name'] ?? '',
            $a['ash_w_co_id_name'] ?? '',
            $a['ash_s_co_id_name'] ?? '',
            $a['pst_name'] ?? '',
            $a['partner_name'] ?? ''
        ];
        foreach ($geoCandidates as $g) {
            $g = trim((string)$g);
            if ($g !== '') return $g;
        }
        return 'Unknown';
    }

    private function short(string $s, int $max) : string
    {
        $s = trim($s);
        if (mb_strlen($s) <= $max) return $s;
        return mb_substr($s, 0, $max) . '...';
    }



    /* =========================================================
 * NEW: Call Success Patterns Analysis
 * ========================================================= */

private function call_success_patterns(array $rows) : array
{
    $patterns = [
        'first_call_success' => 0,
        'multiple_calls_needed' => 0,
        'first_call_success_rate' => 0,
        'multiple_calls_needed_rate' => 0,
        'success_by_hour' => [],
        'success_by_day' => [],
        'duration_vs_success' => [],
        'top_calling_companies' => [],
        'total_company_call_groups' => 0
    ];
    
    // Group by company and user to see call patterns
    $companyCalls = [];
    
    foreach ($rows as $r) {
        // Only analyze call tasks
        $taskName = strtolower((string)$r['task_name']);
        if (strpos($taskName, 'call') === false) continue;
        
        $cid = $r['company_id'];
        $uid = $r['user_id'];
        $key = $cid . '_' . $uid;
        
        if (!isset($companyCalls[$key])) {
            $companyCalls[$key] = [
                'company_id' => $cid,
                'company_name' => $r['company_name'],
                'user_id' => $uid,
                'user_name' => $r['user_name'],
                'total_calls' => 0,
                'successful_calls' => 0,
                'call_sequence' => [],
                'first_call_success' => false
            ];
        }
        
        $companyCalls[$key]['total_calls']++;
        if ($r['purpose_achieved'] === true) {
            $companyCalls[$key]['successful_calls']++;
            $companyCalls[$key]['call_sequence'][] = [
                'date' => $r['created_at'],
                'success' => true,
                'attempt' => $companyCalls[$key]['total_calls']
            ];
            
            // Check if first call was successful
            if ($companyCalls[$key]['total_calls'] === 1) {
                $companyCalls[$key]['first_call_success'] = true;
                $patterns['first_call_success']++;
            }
        } else {
            $companyCalls[$key]['call_sequence'][] = [
                'date' => $r['created_at'],
                'success' => false,
                'attempt' => $companyCalls[$key]['total_calls']
            ];
        }
    }
    
    // Analyze patterns
    foreach ($companyCalls as $call) {
        if ($call['total_calls'] > 1 && $call['successful_calls'] > 0) {
            $patterns['multiple_calls_needed']++;
        }
    }
    
    // Calculate rates
    if (count($companyCalls) > 0) {
        $patterns['first_call_success_rate'] = round(($patterns['first_call_success']/count($companyCalls))*100, 2);
        $patterns['multiple_calls_needed_rate'] = round(($patterns['multiple_calls_needed']/count($companyCalls))*100, 2);
    }
    
    // Time-based analysis
    foreach ($rows as $r) {
        $taskName = strtolower((string)$r['task_name']);
        if (strpos($taskName, 'call') === false) continue;
        
        if ($r['scheduled_at']) {
            $hour = date('H', strtotime($r['scheduled_at']));
            $day = date('l', strtotime($r['scheduled_at']));
            
            if (!isset($patterns['success_by_hour'][$hour])) {
                $patterns['success_by_hour'][$hour] = ['total' => 0, 'success' => 0];
            }
            if (!isset($patterns['success_by_day'][$day])) {
                $patterns['success_by_day'][$day] = ['total' => 0, 'success' => 0];
            }
            
            $patterns['success_by_hour'][$hour]['total']++;
            $patterns['success_by_day'][$day]['total']++;
            
            if ($r['purpose_achieved'] === true) {
                $patterns['success_by_hour'][$hour]['success']++;
                $patterns['success_by_day'][$day]['success']++;
            }
            
            // Duration vs success
            $durationBucket = floor($r['duration_sec'] / 60); // in minutes
            $bucketKey = $durationBucket . ' min';
            if (!isset($patterns['duration_vs_success'][$bucketKey])) {
                $patterns['duration_vs_success'][$bucketKey] = ['total' => 0, 'success' => 0];
            }
            $patterns['duration_vs_success'][$bucketKey]['total']++;
            if ($r['purpose_achieved'] === true) {
                $patterns['duration_vs_success'][$bucketKey]['success']++;
            }
        }
    }
    
    // Calculate success rates
    foreach ($patterns['success_by_hour'] as $hour => &$data) {
        $data['success_rate'] = $data['total'] ? round(($data['success']/$data['total'])*100, 2) : 0;
    }
    
    foreach ($patterns['success_by_day'] as $day => &$data) {
        $data['success_rate'] = $data['total'] ? round(($data['success']/$data['total'])*100, 2) : 0;
    }
    
    foreach ($patterns['duration_vs_success'] as $bucket => &$data) {
        $data['success_rate'] = $data['total'] ? round(($data['success']/$data['total'])*100, 2) : 0;
    }
    
    ksort($patterns['success_by_hour']);
    
    // Get top calling companies
    $topCompanies = [];
    foreach ($companyCalls as $call) {
        $topCompanies[] = [
            'company_name' => $call['company_name'],
            'user_name' => $call['user_name'],
            'total_calls' => $call['total_calls'],
            'successful_calls' => $call['successful_calls'],
            'success_rate' => $call['total_calls'] ? round(($call['successful_calls']/$call['total_calls'])*100, 2) : 0,
            'first_call_success' => $call['first_call_success']
        ];
    }
    
    usort($topCompanies, function($a, $b) {
        return $b['total_calls'] <=> $a['total_calls'];
    });
    
    $patterns['top_calling_companies'] = array_slice($topCompanies, 0, 10);
    $patterns['total_company_call_groups'] = count($companyCalls);
    
    return $patterns;
}

/* =========================================================
 * NEW: Proposal Conversion Analysis
 * ========================================================= */

private function proposal_conversion_analysis(array $rows) : array
{
    $analysis = [
        'proposal_required' => 0,
        'proposal_created' => 0,
        'proposal_success_rate' => 0,
        'proposal_to_closure_timeline' => [],
        'proposal_types' => []
    ];
    
    $proposalTasks = [];
    
    foreach ($rows as $r) {
        // Check if proposal required
        if ($r['proposal_require'] === true) {
            $analysis['proposal_required']++;
            
            // Check if proposal was created
            $taskName = strtolower($r['task_name']);
            $mtype = strtoupper($r['mtype']);
            if (strpos($taskName, 'proposal') !== false || $mtype === 'PROPOSAL') {
                $analysis['proposal_created']++;
                
                $proposalTasks[] = [
                    'company' => $r['company_name'],
                    'user' => $r['user_name'],
                    'date' => $r['created_at'],
                    'purpose_achieved' => $r['purpose_achieved'],
                    'closing_timeline' => $r['closing_timeline'],
                    'closure_level' => $r['closure_level'],
                    'status' => $r['current_status']
                ];
            }
        }
        
        // Track proposal types
        if (strpos(strtolower($r['task_name']), 'proposal') !== false) {
            $analysis['proposal_types'][$r['task_name']] = ($analysis['proposal_types'][$r['task_name']] ?? 0) + 1;
        }
    }
    
    // Calculate success rate
    $analysis['proposal_success_rate'] = $analysis['proposal_created'] ? 
        round(($analysis['proposal_created']/$analysis['proposal_required'])*100, 2) : 0;
    
    // Analyze proposal to closure timeline
    foreach ($proposalTasks as $task) {
        if ($task['purpose_achieved'] === true && $task['closing_timeline'] !== '') {
            $analysis['proposal_to_closure_timeline'][$task['closing_timeline']] = 
                ($analysis['proposal_to_closure_timeline'][$task['closing_timeline']] ?? 0) + 1;
        }
    }
    
    arsort($analysis['proposal_types']);
    arsort($analysis['proposal_to_closure_timeline']);
    
    // Get proposal success by user
    $proposalByUser = [];
    foreach ($proposalTasks as $task) {
        $user = $task['user'];
        if (!isset($proposalByUser[$user])) {
            $proposalByUser[$user] = ['total' => 0, 'success' => 0];
        }
        $proposalByUser[$user]['total']++;
        if ($task['purpose_achieved'] === true) {
            $proposalByUser[$user]['success']++;
        }
    }
    
    // Calculate user success rates
    $userProposalStats = [];
    foreach ($proposalByUser as $user => $stats) {
        $userProposalStats[] = [
            'user' => $user,
            'total_proposals' => $stats['total'],
            'successful_proposals' => $stats['success'],
            'success_rate' => $stats['total'] ? round(($stats['success']/$stats['total'])*100, 2) : 0
        ];
    }
    
    usort($userProposalStats, function($a, $b) {
        return $b['total_proposals'] <=> $a['total_proposals'];
    });
    
    return [
        'summary' => $analysis,
        'top_proposal_users' => array_slice($userProposalStats, 0, 10),
        'recent_proposal_tasks' => array_slice($proposalTasks, 0, 10)
    ];
}

/* =========================================================
 * NEW: Client Segmentation Analysis
 * ========================================================= */

private function client_segmentation_analysis(array $rows) : array
{
    $segments = [
        'high_value' => [],
        'growth_potential' => [],
        'at_risk' => [],
        'new_leads' => []
    ];
    
    $companyData = [];
    
    // Aggregate company data
    foreach ($rows as $r) {
        $cid = $r['company_id'];
        if (!$cid) continue;
        
        if (!isset($companyData[$cid])) {
            $companyData[$cid] = [
                'company_id' => $cid,
                'company_name' => $r['company_name'],
                'total_tasks' => 0,
                'successful_tasks' => 0,
                'pending_tasks' => 0,
                'last_activity' => $r['last_activity_ts'],
                'flags' => [
                    'potential' => $r['potential'],
                    'topspender' => $r['topspender'],
                    'upsell_client' => $r['upsell_client'],
                    'focus_funnel' => $r['focus_funnel'],
                    'keycompany' => $r['keycompany'],
                    'priorityc' => $r['priorityc']
                ],
                'closure_level' => $r['closure_level'],
                'closing_timeline' => $r['closing_timeline']
            ];
        }
        
        $companyData[$cid]['total_tasks']++;
        if ($r['purpose_achieved'] === true) {
            $companyData[$cid]['successful_tasks']++;
        }
        if ($r['actontaken'] === false) {
            $companyData[$cid]['pending_tasks']++;
        }
        
        // Update last activity if newer
        if ($r['last_activity_ts'] > $companyData[$cid]['last_activity']) {
            $companyData[$cid]['last_activity'] = $r['last_activity_ts'];
        }
    }
    
    // Segment companies
    foreach ($companyData as $cid => $company) {
        $successRate = $company['total_tasks'] ? 
            round(($company['successful_tasks']/$company['total_tasks'])*100, 2) : 0;
        
        // High Value: topspender + potential + high success rate
        if ($company['flags']['topspender'] === true && 
            $company['flags']['potential'] === true && 
            $successRate >= 60) {
            $segments['high_value'][] = [
                'company_name' => $company['company_name'],
                'success_rate' => $successRate,
                'total_tasks' => $company['total_tasks'],
                'closing_timeline' => $company['closing_timeline']
            ];
        }
        
        // Growth Potential: potential=true but low/moderate engagement
        if ($company['flags']['potential'] === true && 
            $company['total_tasks'] <= 3 && 
            $successRate < 60) {
            $segments['growth_potential'][] = [
                'company_name' => $company['company_name'],
                'success_rate' => $successRate,
                'total_tasks' => $company['total_tasks'],
                'pending_tasks' => $company['pending_tasks']
            ];
        }
        
        // At Risk: priority client with pending tasks and no recent activity
        $daysInactive = $company['last_activity'] ? 
            floor((time() - $company['last_activity']) / 86400) : 999;
        
        if ($company['flags']['priorityc'] === true && 
            $company['pending_tasks'] > 0 && 
            $daysInactive > 7) {
            $segments['at_risk'][] = [
                'company_name' => $company['company_name'],
                'pending_tasks' => $company['pending_tasks'],
                'days_inactive' => $daysInactive,
                'last_activity' => date('Y-m-d', $company['last_activity'])
            ];
        }
        
        // New Leads: Only 1-2 tasks total
        if ($company['total_tasks'] <= 2) {
            $segments['new_leads'][] = [
                'company_name' => $company['company_name'],
                'total_tasks' => $company['total_tasks'],
                'success_rate' => $successRate,
                'flags' => array_filter($company['flags'])
            ];
        }
    }
    
    // Sort segments
    usort($segments['high_value'], function($a, $b) {
        return $b['success_rate'] <=> $a['success_rate'];
    });
    
    usort($segments['growth_potential'], function($a, $b) {
        return $a['success_rate'] <=> $b['success_rate'];
    });
    
    usort($segments['at_risk'], function($a, $b) {
        return $b['days_inactive'] <=> $a['days_inactive'];
    });
    
    return [
        'segment_counts' => [
            'high_value' => count($segments['high_value']),
            'growth_potential' => count($segments['growth_potential']),
            'at_risk' => count($segments['at_risk']),
            'new_leads' => count($segments['new_leads'])
        ],
        'high_value_clients' => array_slice($segments['high_value'], 0, 10),
        'growth_potential_clients' => array_slice($segments['growth_potential'], 0, 10),
        'at_risk_clients' => array_slice($segments['at_risk'], 0, 10),
        'recent_new_leads' => array_slice($segments['new_leads'], 0, 10)
    ];
}

/* =========================================================
 * NEW: Performance Benchmarks
 * ========================================================= */

private function performance_benchmarks(array $rows, string $edate) : array
{
    $refTs = strtotime($edate . ' 23:59:59');
    
    $benchmarks = [
        'response_time' => [],
        'follow_up_frequency' => [],
        'closure_velocity' => []
    ];
    
    // Group tasks by company-user pairs
    $companyUserTasks = [];
    
    foreach ($rows as $r) {
        $cid = $r['company_id'];
        $uid = $r['user_id'];
        if (!$cid || !$uid) continue;
        
        $key = $cid . '_' . $uid;
        if (!isset($companyUserTasks[$key])) {
            $companyUserTasks[$key] = [];
        }
        
        $companyUserTasks[$key][] = [
            'task_id' => $r['task_id'],
            'task_name' => $r['task_name'],
            'created_at' => $r['created_at'],
            'created_ts' => $this->to_ts($r['created_at']),
            'scheduled_at' => $r['scheduled_at'],
            'scheduled_ts' => $this->to_ts($r['scheduled_at']),
            'completed_at' => $r['actontaken'] === true ? $r['updated_at'] : null,
            'completed_ts' => $r['actontaken'] === true ? $this->to_ts($r['updated_at']) : null,
            'purpose_achieved' => $r['purpose_achieved'],
            'closure_level' => $r['closure_level'],
            'closing_timeline' => $r['closing_timeline']
        ];
    }
    
    // Calculate response times (time from creation to first action)
    foreach ($companyUserTasks as $key => $tasks) {
        if (count($tasks) < 2) continue;
        
        // Sort by creation date
        usort($tasks, function($a, $b) {
            return $a['created_ts'] <=> $b['created_ts'];
        });
        
        $firstTask = $tasks[0];
        if ($firstTask['completed_ts']) {
            $responseTime = $firstTask['completed_ts'] - $firstTask['created_ts'];
            $responseHours = floor($responseTime / 3600);
            
            $benchmarks['response_time'][] = [
                'company_user_key' => $key,
                'response_hours' => $responseHours,
                'first_task_date' => $firstTask['created_at']
            ];
        }
        
        // Calculate follow-up frequency
        for ($i = 1; $i < count($tasks); $i++) {
            $timeBetween = $tasks[$i]['created_ts'] - $tasks[$i-1]['created_ts'];
            $daysBetween = floor($timeBetween / 86400);
            
            if ($daysBetween > 0) {
                $benchmarks['follow_up_frequency'][] = [
                    'company_user_key' => $key,
                    'days_between' => $daysBetween,
                    'task_count' => $i + 1
                ];
            }
        }
        
        // Calculate closure velocity for successful cases
        $successfulTasks = array_filter($tasks, function($t) {
            return $t['purpose_achieved'] === true && 
                   $t['closure_level'] && 
                   stripos($t['closure_level'], 'confirmed') !== false;
        });
        
        if (count($successfulTasks) > 0) {
            $firstTaskTs = $tasks[0]['created_ts'];
            $lastSuccess = max(array_column($successfulTasks, 'completed_ts'));
            
            if ($lastSuccess > $firstTaskTs) {
                $closureDays = floor(($lastSuccess - $firstTaskTs) / 86400);
                
                $benchmarks['closure_velocity'][] = [
                    'company_user_key' => $key,
                    'closure_days' => $closureDays,
                    'total_tasks' => count($tasks),
                    'successful_tasks' => count($successfulTasks)
                ];
            }
        }
    }
    
    // Calculate averages
    $averages = [
        'avg_response_hours' => 0,
        'avg_follow_up_days' => 0,
        'avg_closure_days' => 0
    ];
    
    if (!empty($benchmarks['response_time'])) {
        $totalResponse = array_sum(array_column($benchmarks['response_time'], 'response_hours'));
        $averages['avg_response_hours'] = round($totalResponse / count($benchmarks['response_time']), 1);
    }
    
    if (!empty($benchmarks['follow_up_frequency'])) {
        $totalFollowUp = array_sum(array_column($benchmarks['follow_up_frequency'], 'days_between'));
        $averages['avg_follow_up_days'] = round($totalFollowUp / count($benchmarks['follow_up_frequency']), 1);
    }
    
    if (!empty($benchmarks['closure_velocity'])) {
        $totalClosure = array_sum(array_column($benchmarks['closure_velocity'], 'closure_days'));
        $averages['avg_closure_days'] = round($totalClosure / count($benchmarks['closure_velocity']), 1);
    }
    
    // Get top performers
    $quickResponders = $benchmarks['response_time'];
    usort($quickResponders, function($a, $b) {
        return $a['response_hours'] <=> $b['response_hours'];
    });
    
    $consistentFollowers = $benchmarks['follow_up_frequency'];
    usort($consistentFollowers, function($a, $b) {
        return $a['days_between'] <=> $b['days_between'];
    });
    
    $fastClosers = $benchmarks['closure_velocity'];
    usort($fastClosers, function($a, $b) {
        return $a['closure_days'] <=> $b['closure_days'];
    });
    
    return [
        'averages' => $averages,
        'quickest_responders' => array_slice($quickResponders, 0, 5),
        'most_consistent_follow_ups' => array_slice($consistentFollowers, 0, 5),
        'fastest_closers' => array_slice($fastClosers, 0, 5),
        'sample_size' => [
            'response_time' => count($benchmarks['response_time']),
            'follow_up_frequency' => count($benchmarks['follow_up_frequency']),
            'closure_velocity' => count($benchmarks['closure_velocity'])
        ]
    ];
}

/* =========================================================
 * NEW: Remarks Sentiment Analysis
 * ========================================================= */

private function remarks_sentiment_analysis(array $rows) : array
{
    $sentiment = [
        'positive_keywords' => [],
        'negative_keywords' => [],
        'common_phrases' => [],
        'call_outcomes' => []
    ];
    
    // Define keyword lists (simplified sentiment analysis)
    $positiveKeywords = [
        'interested', 'discuss', 'positive', 'follow up', 'connect', 'update',
        'proceed', 'happy', 'good', 'great', 'excellent', 'approved', 'confirmed',
        'schedule', 'meeting', 'opportunity', 'potential', 'promising'
    ];
    
    $negativeKeywords = [
        'not interested', 'busy', 'occupied', 'audit', 'later', 'postpone',
        'no time', 'not available', 'declined', 'rejected', 'not picked',
        'call back', 'try later', 'occupied', 'auditing'
    ];
    
    $outcomePhrases = [
        'call connected' => 'connected',
        'not picked' => 'not_picked',
        'busy' => 'busy',
        'follow up' => 'follow_up',
        'schedule' => 'scheduled',
        'meeting' => 'meeting_set',
        'discuss' => 'discussion',
        'proposal' => 'proposal_discussed'
    ];
    
    foreach ($rows as $r) {
        $remarks = strtolower(trim($r['remarks']));
        if (empty($remarks)) continue;
        
        // Check for positive keywords
        foreach ($positiveKeywords as $keyword) {
            if (strpos($remarks, $keyword) !== false) {
                $sentiment['positive_keywords'][$keyword] = 
                    ($sentiment['positive_keywords'][$keyword] ?? 0) + 1;
            }
        }
        
        // Check for negative keywords
        foreach ($negativeKeywords as $keyword) {
            if (strpos($remarks, $keyword) !== false) {
                $sentiment['negative_keywords'][$keyword] = 
                    ($sentiment['negative_keywords'][$keyword] ?? 0) + 1;
            }
        }
        
        // Identify common phrases/outcomes
        foreach ($outcomePhrases as $phrase => $category) {
            if (strpos($remarks, $phrase) !== false) {
                $sentiment['call_outcomes'][$category] = 
                    ($sentiment['call_outcomes'][$category] ?? 0) + 1;
            }
        }
        
        // Extract short phrases (2-4 word combinations)
        $words = preg_split('/\s+/', $remarks);
        for ($i = 0; $i < count($words) - 2; $i++) {
            $phrase = $words[$i] . ' ' . $words[$i+1] . ' ' . $words[$i+2];
            if (strlen($phrase) > 10 && strlen($phrase) < 30) {
                $sentiment['common_phrases'][$phrase] = 
                    ($sentiment['common_phrases'][$phrase] ?? 0) + 1;
            }
        }
    }
    
    // Sort and limit results
    arsort($sentiment['positive_keywords']);
    arsort($sentiment['negative_keywords']);
    arsort($sentiment['common_phrases']);
    arsort($sentiment['call_outcomes']);
    
    // Calculate sentiment ratio
    $totalPositive = array_sum($sentiment['positive_keywords']);
    $totalNegative = array_sum($sentiment['negative_keywords']);
    $sentimentRatio = ($totalPositive + $totalNegative) > 0 ? 
        round(($totalPositive / ($totalPositive + $totalNegative)) * 100, 2) : 0;
    
    // Get most common remarks patterns
    $allRemarks = array_column($rows, 'remarks');
    $remarkLengths = array_map('strlen', $allRemarks);
    
    return [
        'sentiment_ratio_pct' => $sentimentRatio,
        'total_positive_mentions' => $totalPositive,
        'total_negative_mentions' => $totalNegative,
        'top_positive_keywords' => array_slice($sentiment['positive_keywords'], 0, 10),
        'top_negative_keywords' => array_slice($sentiment['negative_keywords'], 0, 10),
        'common_outcomes' => array_slice($sentiment['call_outcomes'], 0, 10),
        'frequent_phrases' => array_slice($sentiment['common_phrases'], 0, 15),
        'remarks_stats' => [
            'avg_length' => count($allRemarks) ? round(array_sum($remarkLengths) / count($allRemarks), 0) : 0,
            'empty_remarks' => count(array_filter($allRemarks, function($r) { return empty(trim($r)); })),
            'total_with_remarks' => count(array_filter($allRemarks, function($r) { return !empty(trim($r)); }))
        ]
    ];
}


}