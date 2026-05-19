<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TeamDetails_model extends CI_Model {

     public function __construct()
    {
        parent::__construct();
        $this->load->model('Menu_model');
        $this->load->model('Report_model');
        $this->load->model('ChatAI_model');
    }


   public function process_Team_Details_analysis($message,$analysisType,$sdate,$edate) {


    $mdata                  = $this->Report_model->GetAllUserByReportingManager($this->uid,$this->uid,'all',$sdate);
    $userdetailsData        = $mdata['data1'];  // User information
    $presentUser            = $this->Report_model->GetTodaysAllUserByReportingManager('total_present',$this->uid,$sdate);  // Attendance data
    

    // Process user data into a structured format
    $usersArray = [];
    foreach ($userdetailsData as $user) {
        // Find matching attendance data for this user
        $attendanceData = null;
        foreach ($presentUser as $present) {
            if ($present->user_id == $user->user_id) {
                // In your process_Team_Details_analysis function
                $attendanceData = [
                    'start_from' => $present->start_from ?? 'Not Present',
                    'start_date' => !empty($present->start_date) ? $present->start_date : null,
                    'uclose_date' => !empty($present->uclose_date) ? $present->uclose_date : null,
                    'start_image' => !empty($present->start_image) ? $this->construct_image_url($present->start_image) : null,
                    'close_image' => !empty($present->close_image) ? $this->construct_image_url($present->close_image) : null,
                    'has_start_image' => !empty($present->start_image),
                    'has_close_image' => !empty($present->close_image),
                    'start_latitude' => $present->start_slatitude ?? null,
                    'start_longitude' => $present->start_slongitude ?? null,
                    'close_latitude' => $present->close_clatitude ?? null,
                    'close_longitude' => $present->close_clongitude ?? null,
                    'type_name' => $present->type_name ?? 'Unknown'
                ];
                break;
            }
        }
        
        $usersArray[] = [
            'user_id' => $user->user_id ?? 0,
            'username' => $user->username ?? 'Unknown User',
            'name' => $user->name ?? 'Unknown',
            'email' => $user->email ?? '',
            'user_roles' => $user->user_roles ?? 'Sales Person',
            'city' => $user->city ?? '',
            'state' => $user->state ?? '',
            'zone_id' => $user->zone_id ?? 0,
            'zone_name' => $user->user_zone_name ?? 'Unknown Zone',
            'status' => $user->status ?? 'inactive',
            'total_funnel' => $user->total_funnel ?? 0,
            'updated_at' => $user->updated_at ?? '',
            'photo' => $user->photo ?? '',
            'phoneno' => $user->phoneno ?? '',
            'address' => $user->address ?? '',
            'attendance' => $attendanceData,
            'acm_co' => $user->acm_co ?? 0,
            'rm_north_co' => $user->rm_north_co ?? 0,
            'ea_co' => $user->ea_co ?? 0,
            'sales_co' => $user->sales_co ?? 0,
            'admin_id' => $user->admin_id ?? 0,
            'sadmin_id' => $user->sadmin_id ?? 0,
            'ucash' => $user->ucash ?? 0,
            'cluster_master_id' => $user->cluster_master_id ?? 0
        ];
    }

    // Calculate attendance statistics
    $total_users = count($userdetailsData);
    $present_count = count($presentUser);
    $absent_count = $total_users - $present_count;
    
    // Analyze work modes
    $work_modes = [
        'Work From Field + Office' => 0,
        'Work From Office' => 0,
        'Work From Field' => 0,
        'Not Present' => 0
    ];
    
    foreach ($usersArray as $user) {
        if ($user['attendance']) {
            $work_mode = $user['attendance']['start_from'] ?? 'Not Present';
            if (isset($work_modes[$work_mode])) {
                $work_modes[$work_mode]++;
            } else {
                $work_modes['Work From Field + Office']++; // Default if unknown
            }
        } else {
            $work_modes['Not Present']++;
        }
    }

    // Combine all data
    $analysisData = [
        'period' => $this->sdate . ' to ' . $this->edate,
        'users' => $usersArray,
        'attendance_stats' => [
            'total_users' => $total_users,
            'present_count' => $present_count,
            'absent_count' => $absent_count,
            'present_percentage' => $total_users > 0 ? round(($present_count / $total_users) * 100, 1) : 0,
            'work_modes' => $work_modes
        ],
        'query_date' => $sdate
    ];

    // Check if user is asking for specific user details
    $specificUser = $this->extract_specific_user_request($message, $usersArray);
    
    if ($specificUser) {
        // Generate prompt for specific user analysis
        $prompt = $this->generate_specific_user_analysis_prompt($message, $specificUser, $analysisData);
    } else {
        // Generate prompt for general team analysis
        $prompt = $this->generate_team_details_analysis_prompt($message, $analysisData);
    }
    
    // Get ChatGPT response
    $chatgptResponse = $this->ChatAI_model->call_chatgpt_api($prompt, []);
    
    // Prepare data for frontend
    $frontendData = $this->prepare_team_details_analysis_data($analysisData, $specificUser);
    
    return [
        'content' => $chatgptResponse,
        'data' => $frontendData
    ];
}

public function extract_specific_user_request($message, $usersArray) {
    // Convert message to lowercase for easier matching
    $lowerMessage = strtolower($message);
    
    foreach ($usersArray as $user) {
        $userName = strtolower($user['name']);
        $userUsername = strtolower($user['username']);
        
        // Check if message contains user's name or username
        if (strpos($lowerMessage, $userName) !== false || 
            strpos($lowerMessage, $userUsername) !== false) {
            return $user;
        }
        
        // Also check for partial matches
        $nameParts = explode(' ', $userName);
        foreach ($nameParts as $part) {
            if (strlen($part) > 3 && strpos($lowerMessage, $part) !== false) {
                return $user;
            }
        }
    }
    
    return null;
}

public function generate_specific_user_analysis_prompt($message, $user, $data) {
    $formattedData = "SPECIFIC USER ANALYSIS - {$user['name']}\n\n";
    
    $formattedData .= "BASIC INFORMATION:\n";
    $formattedData .= "- User ID: {$user['user_id']}\n";
    $formattedData .= "- Username: {$user['username']}\n";
    $formattedData .= "- Email: {$user['email']}\n";
    $formattedData .= "- Phone: {$user['phoneno']}\n";
    $formattedData .= "- Role: {$user['user_roles']}\n";
    $formattedData .= "- Status: {$user['status']}\n";
    $formattedData .= "- Location: {$user['city']}, {$user['state']}\n";
    $formattedData .= "- Zone: {$user['zone_name']} (ID: {$user['zone_id']})\n";
    $formattedData .= "- Address: " . (strlen($user['address']) > 100 ? substr($user['address'], 0, 100) . "..." : $user['address']) . "\n";
    $formattedData .= "- U-Cash Balance: ₹{$user['ucash']}\n";
    $formattedData .= "- Last Updated: {$user['updated_at']}\n\n";
    
    $formattedData .= "PERFORMANCE METRICS:\n";
    $formattedData .= "- Total Funnel: {$user['total_funnel']}\n";
    
    // Calculate user's rank based on funnel
    $userFunnel = $user['total_funnel'];
    $higherCount = 0;
    $lowerCount = 0;
    $sameCount = 0;
    
    foreach ($data['users'] as $otherUser) {
        if ($otherUser['user_id'] == $user['user_id']) continue;
        
        if ($otherUser['total_funnel'] > $userFunnel) {
            $higherCount++;
        } elseif ($otherUser['total_funnel'] < $userFunnel) {
            $lowerCount++;
        } else {
            $sameCount++;
        }
    }
    
    $totalComparable = $higherCount + $lowerCount + $sameCount;
    if ($totalComparable > 0) {
        $percentile = round(($lowerCount / $totalComparable) * 100, 1);
        $formattedData .= "- Performance Rank: Better than {$percentile}% of team members\n";
    }
    
    $formattedData .= "\nATTENDANCE DETAILS for " . ($data['query_date'] ?? 'current date') . ":\n";
    if ($user['attendance']) {
        $att = $user['attendance'];
        $formattedData .= "- Status: PRESENT\n";
        $formattedData .= "- Work Mode: {$att['start_from']}\n";
        $formattedData .= "- Start Time: {$att['start_date']}\n";
        $formattedData .= "- End Time: {$att['uclose_date']}\n";
        
        if ($att['start_latitude'] && $att['start_longitude']) {
            $formattedData .= "- Start Location: Latitude {$att['start_latitude']}, Longitude {$att['start_longitude']}\n";
        }
        if ($att['close_latitude'] && $att['close_longitude']) {
            $formattedData .= "- End Location: Latitude {$att['close_latitude']}, Longitude {$att['close_longitude']}\n";
        }
        
        if ($att['start_image']) {
            $formattedData .= "- Check-in Image: Available\n";
        }
        if ($att['close_image']) {
            $formattedData .= "- Check-out Image: Available\n";
        }
    } else {
        $formattedData .= "- Status: ABSENT\n";
    }
    
    $formattedData .= "\nMANAGEMENT HIERARCHY:\n";
    $formattedData .= "- Admin ID: {$user['admin_id']}\n";
    $formattedData .= "- Sales Coordinator: {$user['sales_co']}\n";
    $formattedData .= "- ACM Coordinator: {$user['acm_co']}\n";
    $formattedData .= "- RM North Coordinator: {$user['rm_north_co']}\n";
    $formattedData .= "- EA Coordinator: {$user['ea_co']}\n";
    
    $formattedData .= "\nTEAM CONTEXT:\n";
    $formattedData .= "- Team Size: {$data['attendance_stats']['total_users']} users\n";
    $formattedData .= "- Present Today: {$data['attendance_stats']['present_count']} users\n";
    $formattedData .= "- Overall Present Percentage: {$data['attendance_stats']['present_percentage']}%\n";
    
    $prompt = "You are a business analytics AI. Analyze the following specific user data and provide detailed insights:\n\n";
    $prompt .= $formattedData . "\n\n";
    $prompt .= "User Query: {$message}\n\n";
    $prompt .= "Please provide a comprehensive analysis with:\n";
    $prompt .= "1. User profile summary and key details\n";
    $prompt .= "2. Performance analysis based on funnel numbers\n";
    $prompt .= "3. Attendance and work pattern analysis\n";
    $prompt .= "4. Geographic information and coverage area\n";
    $prompt .= "5. Position in team hierarchy and reporting structure\n";
    $prompt .= "6. Comparison with team averages and performance\n";
    $prompt .= "7. Recommendations for this specific user\n\n";
    
    $prompt .= "Format your response with clear sections and use specific numbers from the data.";
    
    return $prompt;
}

public function generate_team_details_analysis_prompt($message, $data) {
    // Format the data in a readable way for ChatGPT
    $formattedData = "TEAM DETAILS ANALYSIS for Period: {$data['period']}\n\n";
    $formattedData .= "ATTENDANCE SUMMARY for " . ($data['query_date'] ?? 'current date') . ":\n";
    $formattedData .= "- Total Team Members: {$data['attendance_stats']['total_users']}\n";
    $formattedData .= "- Present Today: {$data['attendance_stats']['present_count']}\n";
    $formattedData .= "- Absent Today: {$data['attendance_stats']['absent_count']}\n";
    $formattedData .= "- Present Percentage: {$data['attendance_stats']['present_percentage']}%\n\n";
    
    $formattedData .= "WORK MODE DISTRIBUTION:\n";
    foreach ($data['attendance_stats']['work_modes'] as $mode => $count) {
        $percentage = $data['attendance_stats']['total_users'] > 0 ? 
            round(($count / $data['attendance_stats']['total_users']) * 100, 1) : 0;
        $formattedData .= "- {$mode}: {$count} users ({$percentage}%)\n";
    }
    
    $formattedData .= "\nTEAM MEMBER DETAILS:\n";
    
    // Calculate statistics
    $activeUsers = 0;
    $inactiveUsers = 0;
    $totalFunnel = 0;
    $topPerformers = [];
    $roleDistribution = [];
    $zoneDistribution = [];
    $stateDistribution = [];
    
    foreach ($data['users'] as $user) {
        // Count active/inactive
        if ($user['status'] === 'active') {
            $activeUsers++;
        } else {
            $inactiveUsers++;
        }
        
        $totalFunnel += $user['total_funnel'];
        
        // Track top performers (active users with highest funnel)
        if ($user['status'] === 'active' && $user['total_funnel'] > 0) {
            $topPerformers[] = [
                'name' => $user['name'],
                'funnel' => $user['total_funnel'],
                'role' => $user['user_roles']
            ];
        }
        
        // Role distribution
        $role = $user['user_roles'];
        $roleDistribution[$role] = ($roleDistribution[$role] ?? 0) + 1;
        
        // Zone distribution
        $zone = $user['zone_name'];
        $zoneDistribution[$zone] = ($zoneDistribution[$zone] ?? 0) + 1;
        
        // State distribution
        $state = $user['state'] ?: 'Unknown';
        $stateDistribution[$state] = ($stateDistribution[$state] ?? 0) + 1;
    }
    
    // Sort top performers
    usort($topPerformers, function($a, $b) {
        return $b['funnel'] - $a['funnel'];
    });
    
    $averageFunnelPerUser = count($data['users']) > 0 ? round($totalFunnel / count($data['users']), 1) : 0;
    $averageFunnelPerActiveUser = $activeUsers > 0 ? round($totalFunnel / $activeUsers, 1) : 0;
    
    $formattedData .= "\nTEAM STATISTICS:\n";
    $formattedData .= "- Active Users: {$activeUsers}\n";
    $formattedData .= "- Inactive Users: {$inactiveUsers}\n";
    $formattedData .= "- Total Funnel Across Team: {$totalFunnel}\n";
    $formattedData .= "- Average Funnel per User: {$averageFunnelPerUser}\n";
    $formattedData .= "- Average Funnel per Active User: {$averageFunnelPerActiveUser}\n\n";
    
    $formattedData .= "ROLE DISTRIBUTION:\n";
    foreach ($roleDistribution as $role => $count) {
        $percentage = count($data['users']) > 0 ? round(($count / count($data['users'])) * 100, 1) : 0;
        $formattedData .= "- {$role}: {$count} users ({$percentage}%)\n";
    }
    
    $formattedData .= "\nZONE DISTRIBUTION:\n";
    foreach ($zoneDistribution as $zone => $count) {
        $percentage = count($data['users']) > 0 ? round(($count / count($data['users'])) * 100, 1) : 0;
        $formattedData .= "- {$zone}: {$count} users ({$percentage}%)\n";
    }
    
    $formattedData .= "\nTOP 5 (by funnel):\n";
    $top5 = array_slice($topPerformers, 0, 5);
    foreach ($top5 as $index => $performer) {
        $rank = $index + 1;
        $formattedData .= "{$rank}. {$performer['name']} - Funnel: {$performer['funnel']}, Role: {$performer['role']}\n";
    }
    
    $formattedData .= "\nGEOGRAPHIC DISTRIBUTION:\n";
    foreach ($stateDistribution as $state => $count) {
        $percentage = count($data['users']) > 0 ? round(($count / count($data['users'])) * 100, 1) : 0;
        $formattedData .= "- {$state}: {$count} users ({$percentage}%)\n";
    }
    
    $formattedData .= "\nATTENDANCE LISTS:\n";
    
    // Identify present and absent users
    $presentUsers = [];
    $absentUsers = [];
    
    foreach ($data['users'] as $user) {
        if ($user['attendance']) {
            $presentUsers[] = [
                'name' => $user['name'],
                'work_mode' => $user['attendance']['start_from'] ?? 'Unknown',
                'start_time' => $user['attendance']['start_date'] ?? '',
                'role' => $user['user_roles']
            ];
        } else {
            $absentUsers[] = [
                'name' => $user['name'],
                'role' => $user['user_roles'],
                'status' => $user['status'],
                'last_updated' => $user['updated_at']
            ];
        }
    }
    
    $formattedData .= "PRESENT USERS ({$data['attendance_stats']['present_count']}):\n";
    foreach ($presentUsers as $presentUser) {
        $formattedData .= "- {$presentUser['name']} ({$presentUser['role']}) - Work Mode: {$presentUser['work_mode']}, Check-in: {$presentUser['start_time']}\n";
    }
    
    $formattedData .= "\nABSENT USERS ({$data['attendance_stats']['absent_count']}):\n";
    foreach ($absentUsers as $absentUser) {
        $formattedData .= "- {$absentUser['name']} ({$absentUser['role']}) - Status: {$absentUser['status']}, Last Active: {$absentUser['last_updated']}\n";
    }
    
    $prompt = "You are a business analytics AI. Analyze the following team details data and provide insights:\n\n";
    $prompt .= $formattedData . "\n\n";
    $prompt .= "User Query: {$message}\n\n";
    $prompt .= "Please provide a comprehensive analysis with:\n";
    $prompt .= "1. Team attendance and work pattern analysis\n";
    $prompt .= "2. Analyze attendance data for the selected date range. Identify users name who have no day start, no attendance entry, or no task activity. Return the absent users as a name list. Do not include present or partially active users.\n";
    $prompt .= "3. Performance analysis based on funnel numbers\n";
    $prompt .= "4. Team composition and role distribution\n";
    $prompt .= "5. Geographic coverage and distribution\n";
    $prompt .= "6. Top User By Funnel and their characteristics\n";
    $prompt .= "7. Areas needing attention or improvement\n";
    $prompt .= "8. Recommendations for team optimization\n";
    $prompt .= "9. Insights about inactive users and their impact\n";
    $prompt .= "10. Analysis of present and absent user lists - identify patterns, common characteristics among absent users\n\n";
    
    $prompt .= "Format your response with clear sections and use specific numbers from the data.";
    
    return $prompt;
}

public function prepare_team_details_analysis_data($data, $specificUser = null) {
    // Initialize with default structure
    $preparedData = [
        'tableData' => null,
        'presentUserList' => null,
        'absentUserList' => null,
        'chartData' => null,
        'summaryData' => [],
        'specificUserData' => null,
        'status' => 'empty',
        'message' => 'No team data available'
    ];
    
    // Check if users data exists
    if (empty($data['users']) || !is_array($data['users'])) {
        return $preparedData;
    }
    
    // If specific user requested, prepare their data separately
    if ($specificUser) {
        $preparedData['specificUserData'] = $this->prepare_specific_user_data($specificUser, $data);
    }
    
    // Prepare present and absent user lists
    $presentUsers = [];
    $absentUsers = [];
    
    foreach ($data['users'] as $user) {
        if ($user['attendance']) {
            $presentUsers[] = $user;
        } else {
            $absentUsers[] = $user;
        }
    }
    
    // Prepare present user list table
    $presentTableRows = [];
    foreach ($presentUsers as $user) {
        $att = $user['attendance'];
        $presentTableRows[] = [
            $user['name'],
            $user['user_roles'],
            $att['start_from'] ?? 'N/A',
            $att['start_date'] ?? 'N/A',
            $att['uclose_date'] ?? 'N/A',
            $user['city'] . ', ' . $user['state'],
            $user['zone_name'],
            $user['total_funnel']
        ];
    }
    
    $preparedData['presentUserList'] = [
        'title' => 'Present Users (' . count($presentUsers) . ' users)',
        'headers' => ['Name', 'Role', 'Work Mode', 'Start Time', 'End Time', 'Location', 'Zone', 'Funnel'],
        'rows' => $presentTableRows
    ];
    
    // Prepare absent user list table
    $absentTableRows = [];
    foreach ($absentUsers as $user) {
        $absentTableRows[] = [
            $user['name'],
            $user['user_roles'],
            $user['status'],
            $user['city'] . ', ' . $user['state'],
            $user['zone_name'],
            $user['total_funnel'],
            $user['updated_at'],
            $user['phoneno'] ?: 'N/A'
        ];
    }
    
    $preparedData['absentUserList'] = [
        'title' => 'Absent Users (' . count($absentUsers) . ' users)',
        'headers' => ['Name', 'Role', 'Status', 'Location', 'Zone', 'Funnel', 'Last Updated', 'Phone'],
        'rows' => $absentTableRows
    ];
    
    // Prepare main table data for all users
    $tableRows = [];
    $funnelData = [];
    $userNames = [];
    
    foreach ($data['users'] as $user) {
        // Prepare attendance status
        $attendanceStatus = $user['attendance'] ? 'Present' : 'Absent';
        $workMode = $user['attendance']['start_from'] ?? 'N/A';
        
        // Add to table rows
        $tableRows[] = [
            $user['name'],
            $user['user_roles'],
            $user['status'],
            $attendanceStatus,
            $workMode,
            $user['city'] . ', ' . $user['state'],
            $user['zone_name'],
            $user['total_funnel'],
            $user['updated_at']
        ];
        
        // Add to chart data
        if ($user['status'] === 'active') {
            $userNames[] = $user['name'];
            $funnelData[] = $user['total_funnel'];
        }
    }
    
    // Prepare table data structure
    $tableData = [
        'headers' => ['Name', 'Role', 'Status', 'Attendance', 'Work Mode', 'Location', 'Zone', 'Total Funnel', 'Last Updated'],
        'rows' => $tableRows
    ];
    
    // Calculate statistics for summary
    $activeUsers = array_filter($data['users'], function($user) {
        return $user['status'] === 'active';
    });
    
    $totalFunnel = array_sum(array_column($data['users'], 'total_funnel'));
    $activeFunnel = array_sum(array_column($activeUsers, 'total_funnel'));
    
    $averageFunnelAll = count($data['users']) > 0 ? round($totalFunnel / count($data['users']), 1) : 0;
    $averageFunnelActive = count($activeUsers) > 0 ? round($activeFunnel / count($activeUsers), 1) : 0;
    
    // Prepare summary data
    $preparedData['summaryData'] = [
        'period' => $data['period'],
        'total_users' => count($data['users']),
        'active_users' => count($activeUsers),
        'inactive_users' => count($data['users']) - count($activeUsers),
        'present_today' => $data['attendance_stats']['present_count'],
        'absent_today' => $data['attendance_stats']['absent_count'],
        'present_percentage' => $data['attendance_stats']['present_percentage'],
        'total_funnel' => $totalFunnel,
        'average_funnel_all' => $averageFunnelAll,
        'average_funnel_active' => $averageFunnelActive,
        'work_modes' => $data['attendance_stats']['work_modes'],
        'query_date' => $data['query_date'] ?? date('Y-m-d')
    ];
    
    // Prepare chart data
    if (!empty($userNames) && !empty($funnelData)) {
        $chartData = [
            'labels' => $userNames,
            'datasets' => [
                [
                    'label' => 'Funnel Performance',
                    'data' => $funnelData,
                    'backgroundColor' => '#5436da',
                    'borderColor' => '#4529b5',
                    'borderWidth' => 1
                ]
            ]
        ];
        
        $preparedData['chartData'] = $chartData;
    }
    
    // Prepare role distribution chart
    $roleCounts = [];
    foreach ($data['users'] as $user) {
        $role = $user['user_roles'];
        $roleCounts[$role] = ($roleCounts[$role] ?? 0) + 1;
    }
    
    if (!empty($roleCounts)) {
        $roleChartData = [
            'labels' => array_keys($roleCounts),
            'datasets' => [
                [
                    'label' => 'Role Distribution',
                    'data' => array_values($roleCounts),
                    'backgroundColor' => ['#10a37f', '#ffa726', '#5436da', '#26c6da', '#ff6b6b', '#8e44ad', '#f39c12'],
                    'borderColor' => '#2a2a2a',
                    'borderWidth' => 1
                ]
            ]
        ];
        
        $preparedData['roleChartData'] = $roleChartData;
    }
    
    // Prepare attendance chart
    $attendanceChartData = [
        'labels' => ['Present', 'Absent'],
        'datasets' => [
            [
                'label' => 'Attendance',
                'data' => [
                    $data['attendance_stats']['present_count'],
                    $data['attendance_stats']['absent_count']
                ],
                'backgroundColor' => ['#10a37f', '#ff6b6b'],
                'borderColor' => '#2a2a2a',
                'borderWidth' => 1
            ]
        ]
    ];
    
    $preparedData['attendanceChartData'] = $attendanceChartData;
    
    // Prepare work mode chart
    $workModeLabels = [];
    $workModeData = [];
    foreach ($data['attendance_stats']['work_modes'] as $mode => $count) {
        $workModeLabels[] = $mode;
        $workModeData[] = $count;
    }
    
    $workModeChartData = [
        'labels' => $workModeLabels,
        'datasets' => [
            [
                'label' => 'Work Mode Distribution',
                'data' => $workModeData,
                'backgroundColor' => ['#5436da', '#26c6da', '#ffa726', '#ff6b6b'],
                'borderColor' => '#2a2a2a',
                'borderWidth' => 1
            ]
        ]
    ];
    
    $preparedData['workModeChartData'] = $workModeChartData;
    
    // Update prepared data
    $preparedData['tableData'] = $tableData;
    $preparedData['status'] = 'success';
    $preparedData['message'] = 'Team data prepared successfully';
    
    return $preparedData;
}

public function prepare_specific_user_data($user, $teamData) {
    $specificData = [
        'basic_info' => [
            'name' => $user['name'],
            'user_id' => $user['user_id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'phone' => $user['phoneno'],
            'role' => $user['user_roles'],
            'status' => $user['status'],
            'photo' => $user['photo'] ? base_url($user['photo']) : base_url('assets/images/default-avatar.png'),
            'address' => $user['address'],
            'location' => $user['city'] . ', ' . $user['state'],
            'zone' => $user['zone_name'] . ' (ID: ' . $user['zone_id'] . ')',
            'ucash_balance' => '₹' . $user['ucash'],
            'last_updated' => $user['updated_at']
        ],
        'performance' => [
            'total_funnel' => $user['total_funnel'],
            'funnel_rank' => $this->calculate_user_rank($user, $teamData['users']),
            'attendance_status' => $user['attendance'] ? 'Present' : 'Absent'
        ],
        'attendance_details' => $user['attendance'] ? [
            'work_mode' => $user['attendance']['start_from'],
            'start_time' => $user['attendance']['start_date'],
            'end_time' => $user['attendance']['uclose_date'],
            'start_image' => $user['attendance']['start_image'] ? base_url($user['attendance']['start_image']) : null,
            'close_image' => $user['attendance']['close_image'] ? base_url($user['attendance']['close_image']) : null,
            'start_location' => $user['attendance']['start_latitude'] && $user['attendance']['start_longitude'] ? 
                ['lat' => $user['attendance']['start_latitude'], 'lng' => $user['attendance']['start_longitude']] : null,
            'end_location' => $user['attendance']['close_latitude'] && $user['attendance']['close_longitude'] ? 
                ['lat' => $user['attendance']['close_latitude'], 'lng' => $user['attendance']['close_longitude']] : null
        ] : null,
        'hierarchy' => [
            'admin_id' => $user['admin_id'],
            'sales_coordinator' => $user['sales_co'],
            'acm_coordinator' => $user['acm_co'],
            'rm_north_coordinator' => $user['rm_north_co'],
            'ea_coordinator' => $user['ea_co']
        ]
    ];
    
    return $specificData;
}

public function calculate_user_rank($user, $allUsers) {
    $userFunnel = $user['total_funnel'];
    $higherCount = 0;
    $lowerCount = 0;
    
    foreach ($allUsers as $otherUser) {
        if ($otherUser['user_id'] == $user['user_id'] || $otherUser['status'] !== 'active') continue;
        
        if ($otherUser['total_funnel'] > $userFunnel) {
            $higherCount++;
        } elseif ($otherUser['total_funnel'] < $userFunnel) {
            $lowerCount++;
        }
    }
    
    $totalActive = count(array_filter($allUsers, function($u) { return $u['status'] === 'active'; }));
    if ($totalActive > 1) {
        $percentile = round(($lowerCount / ($totalActive - 1)) * 100, 1);
        $position = $higherCount + 1; // Calculate position first
        return [
            'position' => $position,
            'total_active' => $totalActive,
            'percentile' => $percentile,
            'description' => "Rank {$position} out of {$totalActive} active users"
        ];
    }
    
    return null;
}




public function construct_image_url($image_path) {
    // Check if image_path is null or empty
    if (empty($image_path) || $image_path === 'null' || $image_path === null) {
        return null;
    }
    
    // If it's already a full URL, return as is
    if (filter_var($image_path, FILTER_VALIDATE_URL)) {
        return $image_path;
    }
    
    // Check if it starts with common path prefixes
    if (strpos($image_path, 'http') === 0) {
        return $image_path;
    }
    
    // Remove leading slash if exists
    $image_path = ltrim($image_path, '/');
    
    // Construct the full URL
    // Assuming your images are stored in an uploads directory
    $base_url = base_url(); // Or use your specific base URL
    $uploads_path = 'uploads/attendance/'; // Adjust this to your actual path
    
    return $base_url . $uploads_path . $image_path;
}


}

