<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class New_Report_model extends CI_Model {
    
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }
    
    /**
     * Get All Users by Reporting Manager
     */
    public function GetAllUserByReportingManager($uid, $admin_id_filter = null, $rm_filter = null, $sdate = null) {
        $this->db->select('ud.*, ut.type_name as user_type, um.name as reporting_manager');
        $this->db->from('user_details ud');
        $this->db->join('user_type ut', 'ud.user_type_id = ut.id', 'left');
        $this->db->join('user_details um', 'ud.reporting_manager_id = um.id', 'left');
        $this->db->where('ud.id !=', $uid);
        
        if (!empty($admin_id_filter)) {
            $this->db->where('ud.id', $admin_id_filter);
        }
        
        if (!empty($rm_filter)) {
            $this->db->where('ud.reporting_manager_id', $rm_filter);
        }
        
        if (!empty($sdate)) {
            $this->db->where('DATE(ud.created_at) >=', $sdate);
        }
        
        $this->db->order_by('ud.name', 'ASC');
        
        $query = $this->db->get();
        return $query->result_array();
    }
    
    /**
     * Get Today's Meetings
     */
    public function GetTodaysMeetings($user_id = null) {
        $this->db->select('bm.*, ud.name as user_name, cm.company_name');
        $this->db->from('barginmeeting bm');
        $this->db->join('user_details ud', 'bm.user_id = ud.id', 'left');
        $this->db->join('company_master cm', 'bm.company_id = cm.id', 'left');
        $this->db->where('DATE(bm.meeting_date)', date('Y-m-d'));
        
        if (!empty($user_id)) {
            $this->db->where('bm.user_id', $user_id);
        }
        
        $this->db->order_by('bm.meeting_time', 'ASC');
        
        $query = $this->db->get();
        return $query->result_array();
    }
    
    /**
     * Get Pending Leave Requests
     */
    public function GetPendingLeaveRequests($user_id = null) {
        $this->db->select('lr.*, ud.name as user_name, lm.leave_type, ud2.name as approved_by');
        $this->db->from('leave_requests lr');
        $this->db->join('user_details ud', 'lr.user_id = ud.id', 'left');
        $this->db->join('leave_master lm', 'lr.leave_type_id = lm.id', 'left');
        $this->db->join('user_details ud2', 'lr.approved_by = ud2.id', 'left');
        $this->db->where('lr.status', 'pending');
        
        if (!empty($user_id)) {
            $this->db->where('lr.user_id', $user_id);
        }
        
        $this->db->order_by('lr.request_date', 'DESC');
        
        $query = $this->db->get();
        return $query->result_array();
    }
    
    /**
     * Get Active Users Today
     */
    public function GetActiveUsersToday() {
        $this->db->select('DISTINCT(ua.user_id), ud.name, ud.email, ud.phone, MAX(ua.activity_time) as last_activity');
        $this->db->from('user_activity ua');
        $this->db->join('user_details ud', 'ua.user_id = ud.id', 'left');
        $this->db->where('DATE(ua.activity_time)', date('Y-m-d'));
        $this->db->group_by('ua.user_id');
        $this->db->order_by('last_activity', 'DESC');
        
        $query = $this->db->get();
        return $query->result_array();
    }
    
    /**
     * Get Recent Reviews
     */
    public function GetRecentReviews($limit = 50) {
        $this->db->select('ar.*, ud.name as user_name, ud2.name as reviewed_by_name, rt.review_type');
        $this->db->from('allreview ar');
        $this->db->join('user_details ud', 'ar.user_id = ud.id', 'left');
        $this->db->join('user_details ud2', 'ar.reviewed_by = ud2.id', 'left');
        $this->db->join('review_type rt', 'ar.review_type_id = rt.id', 'left');
        $this->db->order_by('ar.review_date', 'DESC');
        $this->db->limit($limit);
        
        $query = $this->db->get();
        return $query->result_array();
    }
    
    /**
     * Get Today's Tasks
     */
    public function GetTodaysTasks($user_id = null) {
        $this->db->select('tpt.*, ud.name as assigned_to_name, ud2.name as assigned_by_name, mt.task_name');
        $this->db->from('task_plan_for_today tpt');
        $this->db->join('user_details ud', 'tpt.assigned_to = ud.id', 'left');
        $this->db->join('user_details ud2', 'tpt.assigned_by = ud2.id', 'left');
        $this->db->join('main_task mt', 'tpt.task_id = mt.id', 'left');
        $this->db->where('DATE(tpt.created_date)', date('Y-m-d'));
        
        if (!empty($user_id)) {
            $this->db->where('tpt.assigned_to', $user_id);
        }
        
        $this->db->order_by('tpt.priority', 'DESC');
        
        $query = $this->db->get();
        return $query->result_array();
    }
    
    /**
     * Get Company List
     */
    public function GetCompanyList($city = null, $state = null) {
        $this->db->select('cm.*, c.city_name, s.state_name');
        $this->db->from('company_master cm');
        $this->db->join('city c', 'cm.city_id = c.id', 'left');
        $this->db->join('states s', 'cm.state_id = s.id', 'left');
        
        if (!empty($city)) {
            $this->db->where('cm.city_id', $city);
        }
        
        if (!empty($state)) {
            $this->db->where('cm.state_id', $state);
        }
        
        $this->db->order_by('cm.company_name', 'ASC');
        $this->db->limit(100);
        
        $query = $this->db->get();
        return $query->result_array();
    }
    
    /**
     * Get Sales Proposals
     */
    public function GetSalesProposals($start_date = null, $end_date = null) {
        $this->db->select('p.*, ud.name as sales_person, cm.company_name, pt.proposal_type as type_name');
        $this->db->from('proposal p');
        $this->db->join('user_details ud', 'p.sales_person_id = ud.id', 'left');
        $this->db->join('company_master cm', 'p.company_id = cm.id', 'left');
        $this->db->join('proposal_type pt', 'p.proposal_type_id = pt.id', 'left');
        
        if (!empty($start_date)) {
            $this->db->where('DATE(p.created_date) >=', $start_date);
        }
        
        if (!empty($end_date)) {
            $this->db->where('DATE(p.created_date) <=', $end_date);
        }
        
        $this->db->order_by('p.created_date', 'DESC');
        $this->db->limit(100);
        
        $query = $this->db->get();
        return $query->result_array();
    }
    
    /**
     * Get Cluster Mapping
     */
    public function GetClusterMapping() {
        $this->db->select('cm.*, cl.cluster_name, zm.zone_name, ud.name as user_name');
        $this->db->from('cluster_mapping cm');
        $this->db->join('cluster_master cl', 'cm.cluster_id = cl.id', 'left');
        $this->db->join('zone_master zm', 'cm.zone_id = zm.id', 'left');
        $this->db->join('user_details ud', 'cm.user_id = ud.id', 'left');
        $this->db->order_by('cm.created_date', 'DESC');
        
        $query = $this->db->get();
        return $query->result_array();
    }
    
    /**
     * Get Recent Cash Expenses
     */
    public function GetRecentCashExpenses($limit = 50) {
        $this->db->select('ce.*, ud.name as user_name, cet.expense_type');
        $this->db->from('cash_expense ce');
        $this->db->join('user_details ud', 'ce.user_id = ud.id', 'left');
        $this->db->join('travel_expense_type cet', 'ce.expense_type_id = cet.id', 'left');
        $this->db->order_by('ce.expense_date', 'DESC');
        $this->db->limit($limit);
        
        $query = $this->db->get();
        return $query->result_array();
    }
    
    /**
     * Get User Activity Logs
     */
    public function GetUserActivityLogs($user_id = null, $limit = 100) {
        $this->db->select('ua.*, ud.name as user_name');
        $this->db->from('user_activity ua');
        $this->db->join('user_details ud', 'ua.user_id = ud.id', 'left');
        
        if (!empty($user_id)) {
            $this->db->where('ua.user_id', $user_id);
        }
        
        $this->db->order_by('ua.activity_time', 'DESC');
        $this->db->limit($limit);
        
        $query = $this->db->get();
        return $query->result_array();
    }
    
    /**
     * Get Star Ratings Summary
     */
    public function GetStarRatingsSummary() {
        $this->db->select('sr.*, ud.name as user_name, ud2.name as rated_by_name');
        $this->db->from('star_rating sr');
        $this->db->join('user_details ud', 'sr.user_id = ud.id', 'left');
        $this->db->join('user_details ud2', 'sr.rated_by = ud2.id', 'left');
        $this->db->order_by('sr.created_date', 'DESC');
        $this->db->limit(100);
        
        $query = $this->db->get();
        return $query->result_array();
    }
    
    /**
     * Get Leave Balance
     */
    public function GetLeaveBalance($user_id = null) {
        $this->db->select('ud.id, ud.name, ud.email, lm.leave_type, 
                          COALESCE(SUM(lr.days), 0) as taken_days,
                          lm.max_days_per_year as allowed_days,
                          (lm.max_days_per_year - COALESCE(SUM(lr.days), 0)) as balance_days');
        $this->db->from('user_details ud');
        $this->db->join('leave_master lm', '1=1', 'left');
        $this->db->join('leave_requests lr', 'lr.user_id = ud.id AND lr.leave_type_id = lm.id AND YEAR(lr.leave_date) = YEAR(CURDATE()) AND lr.status = "approved"', 'left');
        
        if (!empty($user_id)) {
            $this->db->where('ud.id', $user_id);
        }
        
        $this->db->group_by('ud.id, lm.id');
        $this->db->order_by('ud.name', 'ASC');
        
        $query = $this->db->get();
        return $query->result_array();
    }
    
    /**
     * Get Upcoming Events
     */
    public function GetUpcomingEvents($days = 7) {
        $this->db->select('te.*, ud.name as created_by');
        $this->db->from('tblcallevents te');
        $this->db->join('user_details ud', 'te.created_by = ud.id', 'left');
        $this->db->where('DATE(te.start) >=', date('Y-m-d'));
        $this->db->where('DATE(te.start) <=', date('Y-m-d', strtotime("+$days days")));
        $this->db->order_by('te.start', 'ASC');
        
        $query = $this->db->get();
        return $query->result_array();
    }
    
    /**
     * Get Sales Performance
     */
    public function GetSalesPerformance($month = null, $year = null) {
        $current_month = $month ?: date('m');
        $current_year = $year ?: date('Y');
        
        $this->db->select('ud.id, ud.name, 
                          COUNT(p.id) as total_proposals,
                          SUM(CASE WHEN p.status = "won" THEN 1 ELSE 0 END) as won_proposals,
                          SUM(CASE WHEN p.status = "lost" THEN 1 ELSE 0 END) as lost_proposals,
                          COALESCE(SUM(p.amount), 0) as total_amount');
        $this->db->from('user_details ud');
        $this->db->join('proposal p', 'p.sales_person_id = ud.id AND MONTH(p.created_date) = '.$current_month.' AND YEAR(p.created_date) = '.$current_year, 'left');
        $this->db->where('ud.user_type_id', 3);
        $this->db->group_by('ud.id');
        $this->db->order_by('total_amount', 'DESC');
        
        $query = $this->db->get();
        return $query->result_array();
    }
    
    /**
     * Get Task Completion Rate
     */
    public function GetTaskCompletionRate($user_id = null) {
        $this->db->select('ud.id, ud.name,
                          COUNT(tpt.id) as total_tasks,
                          SUM(CASE WHEN tpt.status = "completed" THEN 1 ELSE 0 END) as completed_tasks,
                          ROUND((SUM(CASE WHEN tpt.status = "completed" THEN 1 ELSE 0 END) * 100.0 / COUNT(tpt.id)), 2) as completion_rate');
        $this->db->from('user_details ud');
        $this->db->join('task_plan_for_today tpt', 'tpt.assigned_to = ud.id AND DATE(tpt.created_date) = CURDATE()', 'left');
        
        if (!empty($user_id)) {
            $this->db->where('ud.id', $user_id);
        }
        
        $this->db->group_by('ud.id');
        $this->db->order_by('completion_rate', 'DESC');
        
        $query = $this->db->get();
        return $query->result_array();
    }
    
    /**
     * Get Company Contacts
     */
    public function GetCompanyContacts($company_id = null) {
        $this->db->select('ccm.*, cm.company_name');
        $this->db->from('company_contact_master ccm');
        $this->db->join('company_master cm', 'ccm.company_id = cm.id', 'left');
        
        if (!empty($company_id)) {
            $this->db->where('ccm.company_id', $company_id);
        }
        
        $this->db->order_by('ccm.contact_name', 'ASC');
        $this->db->limit(100);
        
        $query = $this->db->get();
        return $query->result_array();
    }
    
    /**
     * Get Pending Meetings
     */
    public function GetPendingMeetings() {
        $this->db->select('pmr.*, ud.name as requested_by, cm.company_name');
        $this->db->from('pending_meetings_request pmr');
        $this->db->join('user_details ud', 'pmr.requested_by = ud.id', 'left');
        $this->db->join('company_master cm', 'pmr.company_id = cm.id', 'left');
        $this->db->where('pmr.status', 'pending');
        $this->db->order_by('pmr.requested_date', 'DESC');
        
        $query = $this->db->get();
        return $query->result_array();
    }
    
    /**
     * Get Recent Login Sessions
     */
    public function GetRecentLoginSessions($limit = 50) {
        $this->db->select('ls.*, ud.name as user_name');
        $this->db->from('login_session ls');
        $this->db->join('user_details ud', 'ls.user_id = ud.id', 'left');
        $this->db->order_by('ls.login_time', 'DESC');
        $this->db->limit($limit);
        
        $query = $this->db->get();
        return $query->result_array();
    }
    
    /**
     * Get Cash Flow Summary
     */
    public function GetCashFlowSummary($start_date = null, $end_date = null) {
        $start = $start_date ?: date('Y-m-01');
        $end = $end_date ?: date('Y-m-d');
        
        $this->db->select("DATE(expense_date) as date,
                          SUM(CASE WHEN expense_type = 'income' THEN amount ELSE 0 END) as income,
                          SUM(CASE WHEN expense_type = 'expense' THEN amount ELSE 0 END) as expense,
                          SUM(CASE WHEN expense_type = 'income' THEN amount ELSE -amount END) as net_flow");
        $this->db->from('cash_log');
        $this->db->where('DATE(expense_date) >=', $start);
        $this->db->where('DATE(expense_date) <=', $end);
        $this->db->group_by('DATE(expense_date)');
        $this->db->order_by('date', 'ASC');
        
        $query = $this->db->get();
        return $query->result_array();
    }
    
    /**
     * Get User Productivity
     */
    public function GetUserProductivity($user_id = null, $days = 30) {
        $start_date = date('Y-m-d', strtotime("-$days days"));
        
        $this->db->select('ud.id, ud.name,
                          COUNT(DISTINCT DATE(ua.activity_time)) as active_days,
                          COUNT(ua.id) as total_activities,
                          COUNT(tpt.id) as tasks_assigned,
                          COUNT(CASE WHEN tpt.status = "completed" THEN 1 END) as tasks_completed');
        $this->db->from('user_details ud');
        $this->db->join('user_activity ua', "ua.user_id = ud.id AND DATE(ua.activity_time) >= '$start_date'", 'left');
        $this->db->join('task_plan_for_today tpt', "tpt.assigned_to = ud.id AND DATE(tpt.created_date) >= '$start_date'", 'left');
        
        if (!empty($user_id)) {
            $this->db->where('ud.id', $user_id);
        }
        
        $this->db->group_by('ud.id');
        $this->db->order_by('total_activities', 'DESC');
        
        $query = $this->db->get();
        return $query->result_array();
    }
    
    /**
     * Get Dashboard Data
     */
    public function get_dashboard_data() {
        $data = [];
        
        // User stats
        $data['user_stats'] = [
            ['label' => 'Total Users', 'value' => $this->get_count('user_details')],
            ['label' => 'Active Today', 'value' => count($this->GetActiveUsersToday())],
            ['label' => 'Managers', 'value' => $this->get_count_where('user_details', ['user_type_id' => 2])]
        ];
        
        // Task stats
        $data['task_stats'] = [
            ['label' => "Today's Tasks", 'value' => $this->get_count_where('task_plan_for_today', ['DATE(created_date)' => date('Y-m-d')])],
            ['label' => 'Completed', 'value' => $this->get_count_where('task_plan_for_today', ['status' => 'completed', 'DATE(created_date)' => date('Y-m-d')])],
            ['label' => 'Pending', 'value' => $this->get_count_where('task_plan_for_today', ['status !=' => 'completed', 'DATE(created_date)' => date('Y-m-d')])]
        ];
        
        // Meeting stats
        $data['meeting_stats'] = [
            ['label' => "Today's Meetings", 'value' => count($this->GetTodaysMeetings())],
            ['label' => 'Upcoming (7 days)', 'value' => count($this->GetUpcomingEvents(7))],
            ['label' => 'Pending Requests', 'value' => count($this->GetPendingMeetings())]
        ];
        
        return $data;
    }
    
    /**
     * Get count from table
     */
    private function get_count($table) {
        return $this->db->count_all($table);
    }
    
    /**
     * Get count with where clause
     */
    private function get_count_where($table, $where) {
        $this->db->where($where);
        return $this->db->count_all_results($table);
    }
    
    /**
     * Get function keywords mapping
     */
    public function get_function_keywords() {
        return [
            'user' => [
                'label' => '👤 User Information',
                'functions' => [
                    'Show all users' => 'GetAllUserByReportingManager',
                    'Active users today' => 'GetActiveUsersToday',
                    'User login sessions' => 'GetRecentLoginSessions',
                    'User activity logs' => 'GetUserActivityLogs',
                    'User productivity' => 'GetUserProductivity'
                ]
            ],
            'review' => [
                'label' => '⭐ Reviews & Ratings',
                'functions' => [
                    'Recent reviews' => 'GetRecentReviews',
                    'Star ratings summary' => 'GetStarRatingsSummary'
                ]
            ],
            'task' => [
                'label' => '📋 Tasks & Assignments',
                'functions' => [
                    "Today's tasks" => 'GetTodaysTasks',
                    'Task completion rate' => 'GetTaskCompletionRate'
                ]
            ],
            'meeting' => [
                'label' => '📅 Meetings & Events',
                'functions' => [
                    "Today's meetings" => 'GetTodaysMeetings',
                    'Upcoming events' => 'GetUpcomingEvents',
                    'Pending meetings' => 'GetPendingMeetings'
                ]
            ],
            'leave' => [
                'label' => '🏖️ Leaves & Requests',
                'functions' => [
                    'Pending leave requests' => 'GetPendingLeaveRequests',
                    'Leave balance' => 'GetLeaveBalance'
                ]
            ],
            'company' => [
                'label' => '🏢 Company & Contacts',
                'functions' => [
                    'Company list' => 'GetCompanyList',
                    'Company contacts' => 'GetCompanyContacts'
                ]
            ],
            'sales' => [
                'label' => '💰 Sales & Proposals',
                'functions' => [
                    'Sales proposals' => 'GetSalesProposals',
                    'Sales performance' => 'GetSalesPerformance'
                ]
            ],
            'cluster' => [
                'label' => '🗺️ Clusters & Zones',
                'functions' => [
                    'Cluster mapping' => 'GetClusterMapping'
                ]
            ],
            'financial' => [
                'label' => '💵 Financial Reports',
                'functions' => [
                    'Recent cash expenses' => 'GetRecentCashExpenses',
                    'Cash flow summary' => 'GetCashFlowSummary'
                ]
            ],
            'dashboard' => [
                'label' => '📊 Dashboard',
                'functions' => [
                    'Dashboard summary' => 'get_dashboard_data'
                ]
            ]
        ];
    }
    
    /**
     * Get all available functions
     */
    public function get_all_functions() {
        $keywords = $this->get_function_keywords();
        $all_functions = [];
        
        foreach ($keywords as $category => $category_data) {
            foreach ($category_data['functions'] as $label => $function) {
                $all_functions[$label] = $function;
            }
        }
        
        return $all_functions;
    }
    
    /**
     * Execute function by name
     */
    public function execute_function($function_name, $params = []) {
        if (method_exists($this, $function_name)) {
            return call_user_func_array([$this, $function_name], $params);
        }
        return ['error' => "Function $function_name not found"];
    }
}