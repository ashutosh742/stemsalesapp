<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ai_query_model extends CI_Model {
    
    // Define your specific tables
    private $allowed_tables = [
        'allreview',
        'allreviewdata',
        'annual_main_review',
        'annual_main_review_log',
        'annual_review_answer',
        'annual_review_session',
        'assign_user_by_date',
        'auto_assign_task',
        'autotask',
        'autotask_time',
        'barginmeeting',
        'cash_expense',
        'cash_log',
        'categories',
        'change_user_day_request',
        'city',
        'close_your_day_request',
        'cluster',
        'cluster_mapping',
        'cluster_master',
        'cluster_zone',
        'clustermaster',
        'company_contact_master',
        'company_contact_master_new',
        'company_log',
        'company_master',
        'compulsive_log',
        'compulsive_need_your_reset',
        'contact_change_request',
        'country_db',
        'create_planner_request',
        'day_reminder_comments',
        'daystartrequest',
        'early_planner_request',
        'email_validate',
        'funnel_transfer_log',
        'in_city',
        'in_district',
        'in_state',
        'init_call',
        'leave_management',
        'leave_master',
        'leave_requests',
        'login_session',
        'main_review',
        'main_task',
        'manage_leave',
        'mandatory_restriction',
        'mom_data',
        'need_to_assign_bd',
        'next_folloup_have_date',
        'partner_master',
        'pending_meetings_request',
        'proposal',
        'proposal_type',
        'purpose',
        'quater_strategy',
        'reminder_comments',
        'reminder_message',
        'remove_company_log',
        'request_old_pend_task',
        'review_answer',
        'review_session',
        'review_type',
        'sales_star_rating',
        'sales_status_change_task_star_rating',
        'sales_task_star_rating',
        'session_plan_time',
        'spcl_rest_task_planner',
        'special_bdrequest',
        'special_bdrequest_task',
        'special_bdrequest_type',
        'special_request_for_leave',
        'star_rating',
        'states',
        'status',
        'target_vs_achievement',
        'task_check_session',
        'task_execution_details',
        'task_plan_for_today',
        'taskcheck_star_rating',
        'tblcallevents',
        'tblcallevents_attachments',
        'timeslot',
        'travel_advance',
        'travel_cluster_edit_request',
        'travel_expense_type',
        'types_of_proposal',
        'update_leads',
        'user_activity',
        'user_cluster_mapping',
        'user_day',
        'user_details',
        'user_type',
        'user_zone',
        'user_zone_master',
        'userworkfrom',
        'work_order',
        'zone_master'
    ];
    
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }
    
    /**
     * Get all table schemas for AI understanding
     */
    public function get_all_table_schemas() {
        $schemas = [];
        
        foreach ($this->allowed_tables as $table) {
            if (!$this->table_exists($table)) {
                continue; // Skip if table doesn't exist
            }
            
            $schema = "Table: $table\nColumns:\n";
            
            // Get table fields
            try {
                $fields = $this->db->field_data($table);
                
                if ($fields) {
                    foreach ($fields as $field) {
                        $primary_key = (isset($field->primary_key) && $field->primary_key == 1) ? ' (PRIMARY KEY)' : '';
                        $max_length = (isset($field->max_length) && $field->max_length) ? " max: {$field->max_length}" : '';
                        
                        $schema .= "- {$field->name} ({$field->type}{$max_length}){$primary_key}\n";
                    }
                }
            } catch (Exception $e) {
                $schema .= "Error getting columns: " . $e->getMessage() . "\n";
                continue;
            }
            
            // Get sample data structure
            try {
                $this->db->select('*');
                $this->db->from($table);
                $this->db->limit(1);
                $query = $this->db->get();
                
                if ($query && $query->num_rows() > 0) {
                    $sample = $query->row_array();
                    $schema .= "Sample data: " . json_encode($sample) . "\n";
                }
            } catch (Exception $e) {
                $schema .= "Could not retrieve sample data\n";
            }
            
            // Get row count
            $row_count = $this->get_table_row_count($table);
            $schema .= "Total rows: $row_count\n";
            
            $schema .= "\n";
            $schemas[$table] = $schema;
        }
        
        return $schemas;
    }
    
    /**
     * Get database summary
     */
    public function get_database_summary() {
        $summary = "DATABASE SUMMARY - SALES & CRM SYSTEM\n";
        $summary .= "=======================================\n\n";
        
        $summary .= "Total Tables: " . count($this->allowed_tables) . "\n\n";
        
        // Group tables by category
        $categories = [
            'User Management' => ['user_details', 'user_type', 'user_day', 'user_activity', 'login_session', 'userworkfrom', 'user_cluster_mapping'],
            'Reviews & Ratings' => ['allreview', 'allreviewdata', 'main_review', 'annual_main_review', 'review_answer', 'review_session', 'review_type', 'star_rating', 'sales_star_rating', 'taskcheck_star_rating'],
            'Tasks & Planning' => ['main_task', 'auto_assign_task', 'autotask', 'task_plan_for_today', 'task_execution_details', 'task_check_session', 'session_plan_time', 'spcl_rest_task_planner'],
            'Leaves & Requests' => ['leave_management', 'leave_master', 'leave_requests', 'manage_leave', 'special_request_for_leave', 'change_user_day_request', 'close_your_day_request'],
            'Meetings & Events' => ['barginmeeting', 'pending_meetings_request', 'tblcallevents', 'mom_data', 'init_call'],
            'Sales & Proposals' => ['proposal', 'proposal_type', 'types_of_proposal', 'sales_status_change_task_star_rating', 'sales_task_star_rating'],
            'Company & Contacts' => ['company_master', 'company_contact_master', 'company_log', 'contact_change_request', 'partner_master'],
            'Clusters & Zones' => ['cluster', 'cluster_master', 'cluster_mapping', 'cluster_zone', 'clustermaster', 'zone_master', 'user_zone'],
            'Financial' => ['cash_expense', 'cash_log', 'travel_advance'],
            'Location' => ['country_db', 'states', 'city', 'in_city', 'in_state', 'in_district'],
            'System Logs' => ['funnel_transfer_log', 'compulsive_log', 'remove_company_log']
        ];
        
        foreach ($categories as $category => $tables) {
            $category_tables = array_intersect($tables, $this->allowed_tables);
            if (!empty($category_tables)) {
                $summary .= "$category:\n";
                foreach ($category_tables as $table) {
                    if ($this->table_exists($table)) {
                        $row_count = $this->get_table_row_count($table);
                        $summary .= "  - $table ($row_count records)\n";
                    }
                }
                $summary .= "\n";
            }
        }
        
        // Common keywords and their meanings
        $summary .= "COMMON KEYWORDS AND THEIR MEANINGS:\n";
        $summary .= "====================================\n";
        $summary .= "User/Employee: user_details table\n";
        $summary .= "Review/Rating: allreview, star_rating tables\n";
        $summary .= "Task/Assignment: main_task, auto_assign_task tables\n";
        $summary .= "Meeting/Event: barginmeeting, tblcallevents tables\n";
        $summary .= "Leave/Holiday: leave_requests, manage_leave tables\n";
        $summary .= "Company/Client: company_master, company_contact_master tables\n";
        $summary .= "Sales/Proposal: proposal, sales_star_rating tables\n";
        $summary .= "Cluster/Zone: cluster_master, zone_master tables\n";
        $summary .= "Date/Time: Look for date/datetime fields in relevant tables\n";
        $summary .= "Status: status table\n\n";
        
        return $summary;
    }
    
    /**
     * Get table row count safely
     */
    private function get_table_row_count($table) {
        try {
            $this->db->select('COUNT(*) as total');
            $this->db->from($table);
            $query = $this->db->get();
            
            if ($query && $query->num_rows() > 0) {
                $row = $query->row();
                return $row->total;
            }
            return 0;
        } catch (Exception $e) {
            return 0;
        }
    }
    
    /**
     * Check if table exists
     */
    private function table_exists($table) {
        try {
            return $this->db->table_exists($table);
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Execute SQL query safely
     */
    public function execute_query($sql) {
        // Basic safety check
        $trimmed_sql = strtoupper(trim($sql));
        
        // Only allow SELECT queries for safety
        if (strpos($trimmed_sql, 'SELECT') !== 0) {
            return ['error' => 'Only SELECT queries are allowed for security'];
        }
        
        // Check if query uses only allowed tables
        $used_tables = $this->extract_tables_from_sql($sql);
        foreach ($used_tables as $table) {
            if (!in_array(strtolower($table), array_map('strtolower', $this->allowed_tables))) {
                return ['error' => "Table '$table' is not in the allowed list"];
            }
        }
        
        // Blacklist dangerous operations
        $dangerous_keywords = [
            'DROP', 'DELETE', 'UPDATE', 'INSERT', 'ALTER',
            'EXEC', 'EXECUTE', 'TRUNCATE', 'CREATE', 'GRANT'
        ];
        
        foreach ($dangerous_keywords as $keyword) {
            if (stripos($sql, $keyword) !== false) {
                return ['error' => "Query contains prohibited keyword: $keyword"];
            }
        }
        
        try {
            // Execute query
            $query = $this->db->query($sql);
            
            if (!$query) {
                $error = $this->db->error();
                return ['error' => 'Database Error: ' . $error['message']];
            }
            
            $results = $query->result_array();
            $count = count($results);
            
            // Get column names
            $columns = [];
            if ($count > 0 && $query->num_fields() > 0) {
                $fields = $query->field_data();
                foreach ($fields as $field) {
                    $columns[] = $field->name;
                }
            }
            
            return [
                'success' => true,
                'data' => $results,
                'count' => $count,
                'columns' => $columns,
                'query' => $sql
            ];
            
        } catch (Exception $e) {
            return ['error' => 'Query execution failed: ' . $e->getMessage()];
        }
    }
    
    /**
     * Extract table names from SQL
     */
    private function extract_tables_from_sql($sql) {
        $tables = [];
        $sql = strtoupper($sql);
        
        // Simple extraction - can be enhanced
        if (preg_match_all('/FROM\s+(\w+)/', $sql, $matches)) {
            $tables = array_merge($tables, $matches[1]);
        }
        if (preg_match_all('/JOIN\s+(\w+)/', $sql, $matches)) {
            $tables = array_merge($tables, $matches[1]);
        }
        
        return array_unique($tables);
    }
    
    /**
     * Get table names for dropdown
     */
    public function get_table_names() {
        $existing_tables = [];
        foreach ($this->allowed_tables as $table) {
            if ($this->table_exists($table)) {
                $existing_tables[] = $table;
            }
        }
        return $existing_tables;
    }
    
    /**
     * Get table sample data
     */
    public function get_table_sample($table_name, $limit = 5) {
        if (!in_array($table_name, $this->allowed_tables) || !$this->table_exists($table_name)) {
            return ['error' => 'Table not found or not allowed'];
        }
        
        try {
            $this->db->select('*');
            $this->db->from($table_name);
            $this->db->limit($limit);
            $query = $this->db->get();
            
            if ($query) {
                return $query->result_array();
            }
            
            return [];
        } catch (Exception $e) {
            return ['error' => 'Error reading table: ' . $e->getMessage()];
        }
    }
    
    /**
     * Search across all tables with keywords
     */
    public function smart_search($keyword, $limit_per_table = 10) {
        $results = [];
        
        foreach ($this->allowed_tables as $table) {
            if (!$this->table_exists($table)) {
                continue;
            }
            
            try {
                $fields = $this->db->field_data($table);
                $search_fields = [];
                
                // Identify searchable fields
                foreach ($fields as $field) {
                    if (in_array(strtolower($field->type), ['varchar', 'char', 'text', 'mediumtext', 'longtext'])) {
                        $search_fields[] = $field->name;
                    }
                }
                
                if (!empty($search_fields)) {
                    $this->db->group_start();
                    foreach ($search_fields as $index => $field) {
                        if ($index == 0) {
                            $this->db->like($field, $keyword);
                        } else {
                            $this->db->or_like($field, $keyword);
                        }
                    }
                    $this->db->group_end();
                    
                    $query = $this->db->get($table, $limit_per_table);
                    
                    if ($query->num_rows() > 0) {
                        $results[$table] = [
                            'count' => $query->num_rows(),
                            'data' => $query->result_array(),
                            'columns' => $search_fields
                        ];
                    }
                }
                
                $this->db->reset_query();
                
            } catch (Exception $e) {
                continue;
            }
        }
        
        return $results;
    }
    
    /**
     * Get predefined keyword queries
     */
    // public function get_predefined_queries() {
    //     return [
    //         'user' => [
    //             'label' => '👤 User Information',
    //             'queries' => [
    //                 'Show all users' => 'SELECT name, email, mobile FROM user_details LIMIT 50',
    //                 'Active users today' => 'SELECT * FROM user_activity WHERE DATE(event_time) = CURDATE()',
    //                 'Users by type' => 'SELECT user_type.name as department, COUNT(user_details.id) as user_count FROM user_details JOIN user_type ON user_details.user_type_id = user_type.id GROUP BY user_type.id',
    //                 'User login sessions' => 'SELECT user_details.name, login_session.* FROM login_session JOIN user_details ON login_session.user_id = user_details.id ORDER BY login_time DESC LIMIT 50'
    //             ]
    //         ],
    //         'review' => [
    //             'label' => '⭐ Reviews & Ratings',
    //             'queries' => [
    //                 'All reviews' => 'SELECT * FROM allreview LIMIT 50',
    //                 'Star ratings' => 'SELECT * FROM star_rating ORDER BY created_date DESC LIMIT 50',
    //                 'Review sessions' => 'SELECT * FROM review_session ORDER BY session_date DESC LIMIT 50',
    //                 'Review types' => 'SELECT * FROM review_type'
    //             ]
    //         ],
    //         'task' => [
    //             'label' => '📋 Tasks & Assignments',
    //             'queries' => [
    //                 'Today\'s tasks' => 'SELECT * FROM task_plan_for_today WHERE DATE(created_date) = CURDATE() LIMIT 50',
    //                 'Main tasks' => 'SELECT * FROM main_task ORDER BY created_date DESC LIMIT 50',
    //                 'Auto assigned tasks' => 'SELECT * FROM auto_assign_task ORDER BY assign_date DESC LIMIT 50',
    //                 'Task execution details' => 'SELECT * FROM task_execution_details ORDER BY execution_date DESC LIMIT 50'
    //             ]
    //         ],
    //         'meeting' => [
    //             'label' => '📅 Meetings & Events',
    //             'queries' => [
    //                 'All meetings' => 'SELECT * FROM barginmeeting ORDER BY meeting_date DESC LIMIT 50',
    //                 'Calendar events' => 'SELECT * FROM tblcallevents ORDER BY start DESC LIMIT 50',
    //                 'Pending meetings' => 'SELECT * FROM pending_meetings_request WHERE status = "pending"',
    //                 'MOM data' => 'SELECT * FROM mom_data ORDER BY created_date DESC LIMIT 50'
    //             ]
    //         ],
    //         'leave' => [
    //             'label' => '🏖️ Leaves & Requests',
    //             'queries' => [
    //                 'Leave requests' => 'SELECT * FROM leave_requests ORDER BY request_date DESC LIMIT 50',
    //                 'Leave types' => 'SELECT * FROM leave_master',
    //                 'Manage leave' => 'SELECT * FROM manage_leave ORDER BY leave_date DESC LIMIT 50',
    //                 'Special leave requests' => 'SELECT * FROM special_request_for_leave WHERE status = "pending"'
    //             ]
    //         ],
    //         'company' => [
    //             'label' => '🏢 Company & Contacts',
    //             'queries' => [
    //                 'All companies' => 'SELECT * FROM company_master LIMIT 50',
    //                 'Company contacts' => 'SELECT * FROM company_contact_master LIMIT 50',
    //                 'Company logs' => 'SELECT * FROM company_log ORDER BY log_date DESC LIMIT 50',
    //                 'Partners' => 'SELECT * FROM partner_master'
    //             ]
    //         ],
    //         'sales' => [
    //             'label' => '💰 Sales & Proposals',
    //             'queries' => [
    //                 'All proposals' => 'SELECT * FROM proposal ORDER BY created_date DESC LIMIT 50',
    //                 'Proposal types' => 'SELECT * FROM proposal_type',
    //                 'Sales ratings' => 'SELECT * FROM sales_star_rating ORDER BY rating_date DESC LIMIT 50',
    //                 'Sales tasks' => 'SELECT * FROM sales_task_star_rating ORDER BY task_date DESC LIMIT 50'
    //             ]
    //         ],
    //         'cluster' => [
    //             'label' => '🗺️ Clusters & Zones',
    //             'queries' => [
    //                 'All clusters' => 'SELECT * FROM cluster_master',
    //                 'Cluster mappings' => 'SELECT * FROM cluster_mapping',
    //                 'Zones' => 'SELECT * FROM zone_master',
    //                 'User zones' => 'SELECT * FROM user_zone LIMIT 50'
    //             ]
    //         ],
    //         'recent' => [
    //             'label' => '🕐 Recent Activities',
    //             'queries' => [
    //                 'Recent user activities' => 'SELECT * FROM user_activity ORDER BY activity_time DESC LIMIT 50',
    //                 'Recent cash logs' => 'SELECT * FROM cash_log ORDER BY log_date DESC LIMIT 50',
    //                 'Recent login sessions' => 'SELECT * FROM login_session ORDER BY login_time DESC LIMIT 50',
    //                 'Recent task checks' => 'SELECT * FROM task_check_session ORDER BY check_time DESC LIMIT 50'
    //             ]
    //         ]
    //     ];
    // }


    // In Ai_query_model.php - Update get_predefined_queries()
public function get_predefined_queries() {
    return [
        'user' => [
            'label' => '👤 User Information',
            'queries' => [
                'Show all users' => 'function:get_all_users,get_all_active_users,get_all_inactive_users,get_all_present_users',
                'Active users today' => 'function:get_active_users_today,get_recent_active_users',
                'Users by type' => 'function:get_users_by_type,get_department_summary',
                'User login sessions' => 'function:get_user_login_sessions,get_recent_logins,get_active_sessions'
            ]
        ],
        'review' => [
            'label' => '⭐ Reviews & Ratings',
            'queries' => [
                'All reviews' => 'function:get_all_reviews,get_recent_reviews',
                'Star ratings' => 'function:get_star_ratings,get_average_ratings',
                'Review sessions' => 'function:get_review_sessions,get_upcoming_reviews',
                'Review types' => 'function:get_review_types'
            ]
        ],
        // ... other categories
    ];
}

// Add these user-related functions to the model
public function get_all_users($limit = 50) {
    $this->db->select('id, name, email, mobile, status, created_date');
    $this->db->limit($limit);
    return $this->db->get('user_details')->result_array();
}

public function get_all_active_users($limit = 50) {
    $this->db->select('id, name, email, mobile, status');
    $this->db->where('status', 'active');
    $this->db->limit($limit);
    return $this->db->get('user_details')->result_array();
}

public function get_all_inactive_users($limit = 50) {
    $this->db->select('id, name, email, mobile, status');
    $this->db->where('status', 'inactive');
    $this->db->limit($limit);
    return $this->db->get('user_details')->result_array();
}

public function get_all_present_users($limit = 50) {
    // Assuming present users are those who logged in today
    $this->db->select('DISTINCT user_details.id, user_details.name, user_details.email, user_details.mobile');
    $this->db->from('user_details');
    $this->db->join('login_session', 'user_details.id = login_session.user_id');
    $this->db->where('DATE(login_session.login_time)', date('Y-m-d'));
    $this->db->limit($limit);
    return $this->db->get()->result_array();
}

public function get_active_users_today($limit = 50) {
    $this->db->where('DATE(event_time)', date('Y-m-d'));
    $this->db->limit($limit);
    return $this->db->get('user_activity')->result_array();
}

public function get_recent_active_users($limit = 50) {
    $this->db->select('user_details.*');
    $this->db->from('user_details');
    $this->db->join('user_activity', 'user_details.id = user_activity.user_id');
    $this->db->where('user_details.status', 'active');
    $this->db->order_by('user_activity.event_time', 'DESC');
    $this->db->group_by('user_details.id');
    $this->db->limit($limit);
    return $this->db->get()->result_array();
}

}