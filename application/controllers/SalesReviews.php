<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set("Asia/Kolkata");
/**
 * @property CI_Config $config
 * @property CI_Session $session
 * @property CI_Email $email
 * @property CI_Input $input
 * @property CI_DB_query_builder $db
 * @property Menu_model $Menu_model
 * @property Report_model $Report_model
 */
class SalesReviews extends CI_Controller {
/**
 * Constructor
 *
 * This constructor is used to load models, libraries, helpers, etc. that are required by the MasterReset controller.
 */
    public function __construct() {
        parent::__construct();
        // Load models, libraries, helpers, etc.
        $this->load->model('Menu_model');
        $this->load->model('Report_model');
        $this->load->model('SalesReviews_model');
        $this->load->helper('common_helper');
        $this->load->helper('samestatustilldate_helper');
        $this->load->helper('taskPlanner_helper');
        $this->load->helper('taskPlanner_helper');
    }
  
/**
 * Index
 *
 * This function is used to render the Master Reset view.
 * It checks if the user has permission to access the page and if not,
 * it redirects to the dashboard with an error message.
 *
 * @return void
 */
    public function index() {

        $user = $this->session->userdata('user');

        if (empty($user)) {
            $this->session->set_flashdata('error_message'," You must be logged in to view this page.");
            redirect('Menu/main');
        }
        $uid        = $user['user_id'];
        $uyid       = $user['type_id'];
        $this->load->library('session');

        $this->load->model('Menu_model');
        $this->load->model('Report_model');
        $this->load->model('SalesReviews_model');

        $dt         = $this->Menu_model->get_utype($uyid);
        $dep_name   = isset($dt[0]->name) ? $dt[0]->name : 'default';
        $cdate          = date("Y-m-d");
        $reviews_type   = $this->SalesReviews_model->GetAllReviewTypesonTable1();
        $startReviews   = $this->SalesReviews_model->GetOnGoingBDReviews($uid,'Started');

        // getAllCompanyBYRollesMaiingClosurePipeLine($userid,$admin_id_filter,$rm_filter,$sdate,$edate){

        if(sizeof($startReviews) > 0){

            $review_types_name  = $startReviews[0]->review_types_name;
            $start_date         = $startReviews[0]->start_date;
            $review_type_id     = $startReviews[0]->review_type_id;
            $to_uid             = $startReviews[0]->to_uid;
            $by_name            = $startReviews[0]->by_name;
            $to_name            = $startReviews[0]->to_name;
            $review_id          = $startReviews[0]->id;
            $allteams           = $this->SalesReviews_model->GetAllTeamByLineManager($uid,$review_id);

            $this->session->set_flashdata('success_message'," You have already started the $review_types_name review. Please complete it first.");
            $edate          = date('Y-m-d');
            // $started_date   = $edate;

            if($review_type_id == 1){       // Weekly review
                    $beforeDate         = date('Y-m-d', strtotime($started_date . ' -7 days'));
            }else if($review_type_id == 2){ // Fortnightly review
                    $beforeDate         = date('Y-m-d', strtotime($started_date . ' -15 days'));
            }else if($review_type_id == 3){ // Monthly review
                    $beforeDate         = date('Y-m-d', strtotime($started_date . ' -30 days'));
            }else if($review_type_id == 4){ // Quarterly review
                    $beforeDate         = date('Y-m-d', strtotime($started_date . ' -90 days'));
            }else if($review_type_id == 5){ // Half yearly review
                    $beforeDate         = date('Y-m-d', strtotime($started_date . ' -180 days'));
            }else if($review_type_id == 6){ // Annual review
                    $beforeDate         = date('Y-m-d', strtotime($started_date . ' -365 days'));
            }else{
                    $beforeDate         = date('Y-m-d', strtotime($started_date . ' -7 days'));
            }

            $sdate  = $beforeDate;

            if(!empty($to_uid)){

                $worksDatas                         = $this->SalesReviews_model->getAllCompanyBYRollesMaiingClosurePipeLineOnReviesPage($to_uid,$sdate,$edate);
                $totalClosurePipeLineDatasByuser    = $worksDatas['totalClosurePipeLineDatasByuser'];
                $clusterDatas                       = $this->SalesReviews_model->getAllCompanyBYRollesMaiingBYMeetingByClusterID($to_uid,$sdate,$edate);
                $totalClusterVisitDetailsByuser     = $clusterDatas['totalClusterBYClusterNameBYBDDatas'];

            }else{
                $totalClosurePipeLineDatasByuser    = [];
                $totalClusterVisitDetailsByuser     = [];
            }

            // dd($totalClusterVisitDetailsByuser);

            $this->load->view('SalesReviews/StartSalesReviews', [
                'uid'                               => $uid,
                'user'                              => $user,
                'dep_name'                          => $dep_name,
                'cdate'                             => $cdate,
                'sdate'                             => $sdate,
                'edate'                             => $edate,
                'allteams'                          => $allteams,
                'startReviews'                      => $startReviews,
                'totalClosurePipeLineDatasByuser'   => $totalClosurePipeLineDatasByuser,
                'totalClusterVisitDetailsByuser'    => $totalClusterVisitDetailsByuser,
            ]);


        } else {

                $this->load->view('SalesReviews/index', [
                    'uid'                       => $uid,
                    'user'                      => $user,
                    'dep_name'                  => $dep_name,
                    'cdate'                     => $cdate,
                    'reviews_type'              => $reviews_type,
                    'allteams'                  => $allteams,
                ]);

        }
    }
    public function PlannedSalesReviews() {

        $user = $this->session->userdata('user');

        if (empty($user)) {
            $this->session->set_flashdata('error_message'," You must be logged in to view this page.");
            redirect('Menu/main');
        }
        $uid        = $user['user_id'];
        $uyid       = $user['type_id'];
        $this->load->library('session');

        $this->load->model('Menu_model');
        $this->load->model('Report_model');
        $this->load->model('SalesReviews_model');

        $reviews_type           = $this->input->post('reviews_type');
        $planned_date           = date('Y-m-d');

        $data = [
            'review_type_id' => $reviews_type,
            'by_uid'         => $uid,
            'planned_date'   => $planned_date,
            'start_date'     => date('Y-m-d H:i:s'),
            'review_status'  => "Started"
        ];

        $this->db->insert('bd_wise_reviews', $data);
        // get inserted ID (if you care about it)
        $insert_id = $this->db->insert_id();

        $this->session->set_flashdata('success_message'," Sales Review Planned Successfully.");
        redirect('SalesReviews/index');

    }



    public function PlannedUpdatesSalesReviews() {

        $user = $this->session->userdata('user');

        if (empty($user)) {
            $this->session->set_flashdata('error_message'," You must be logged in to view this page.");
            redirect('Menu/main');
        }
        $uid        = $user['user_id'];
        $uyid       = $user['type_id'];
        $this->load->library('session');

        $this->load->model('Menu_model');
        $this->load->model('Report_model');
        $this->load->model('SalesReviews_model');

        $review_id           = $this->input->post('review_id');
        $userSelect          = $this->input->post('userSelect');
        $planned_date        = date('Y-m-d');

        $data = [
            'to_uid'  => "$userSelect",
        ];

        // where condition (VERY important unless you enjoy chaos)
        $this->db->where('id', $review_id);
        $this->db->update('bd_wise_reviews', $data);

        $this->session->set_flashdata('success_message'," Sales Review Updated Successfully.");
        redirect('SalesReviews/index');

    }


    // ************************************** Sales Reviews Start Here ****************************************


/*
public function saveReviewRatings() {

   $user = $this->session->userdata('user');

    if (empty($user)) {
        $this->session->set_flashdata('error_message'," You must be logged in to view this page.");
        redirect('Menu/main');
    }
    $uid        = $user['user_id'];
    $uyid       = $user['type_id'];

    $this->load->library('session');
    $this->load->model('Menu_model');
    $this->load->model('Report_model');
    $this->load->model('SalesReviews_model');

    // Get posted data
    $ratings    = $this->input->post('rating');    
    $remarks    = $this->input->post('remarks');   
    $review_id  = $this->input->post('review_id'); 
    $message    = $this->input->post('message');

    $sdate      = $this->input->post('sdate');
    $edate      = $this->input->post('edate');

    $session_duration_seconds       = $this->input->post('session_duration_seconds');
    $session_duration_minutes       = floor($session_duration_seconds / 60);

    // Get logged-in user id (reviewer)
    $reviewed_by = $this->session->userdata('user_id'); // Adjust based on your session key

    if (empty($ratings) || !is_array($ratings)) {
        $this->session->set_flashdata('error_message', 'No ratings data submitted.');
        redirect($_SERVER['HTTP_REFERER']);
        return;
    }

    $getBDWiseReviewByReviewID      = $this->SalesReviews_model->GetBDWiseReviewByReviewID($review_id);
    $getBDWiseReviewByReviewID      = $this->SalesReviews_model->GetBDWiseReviewByReviewID($review_id);
    $exits_comments                 = $getBDWiseReviewByReviewID[0]->comments;
    $to_uid                         = $getBDWiseReviewByReviewID[0]->to_uid;
    $to_name                        = $getBDWiseReviewByReviewID[0]->to_name;

    if(empty($exits_comments)){
        $new_comments                   = $to_name.' - '.$message[$to_uid];
    }else{
        $new_comments                   = $exits_comments. "<br/>". $to_name.' - '.$message[$to_uid];
    }

    $success_count = 0;
    $error_count = 0;

    // Loop through each user
    foreach ($ratings as $user_id => $metrics) {
        if (!is_array($metrics)) continue;

        // Loop through each metric for this user
        foreach ($metrics as $metric_key => $rating) {
            // Rating must be between 1 and 5
            $rating = intval($rating);
            if ($rating < 1 || $rating > 5) {
                $error_count++;
                continue;
            }

            // Get remarks for this user and metric if any
            $remark = isset($remarks[$user_id][$metric_key]) ? trim($remarks[$user_id][$metric_key]) : '';

            // Prepare data for model
            $data = [
                'bd_wise_reviews_id'        => $review_id,
                'user_id'                   => $user_id,
                'metric_key'                => $metric_key,
                'rating'                    => $rating,
                'remarks'                   => $remark,
                'reviewed_by'               => $uid,
                'sdate'                     => $sdate,
                'edate'                     => $edate,
                'review_date'               => date('Y-m-d') // today's date
            ];

            // Save
            if ($this->SalesReviews_model->save_review($data)) {
                $success_count++;
            } else {
                $error_count++;
            }
        }
    }

    $allteams                       = $this->SalesReviews_model->GetAllTeamByLineManager($uid,$review_id);

    if(sizeof($allteams) == 0){

        $updateData = [
            'to_uid'            => "",
            'end_date'          => date('Y-m-d H:i:s'),
            'review_status'     => "Closed",
            'close_reamrks'     => "$message[$to_uid]",
            'session_time'      => $session_duration_minutes,
            'comments'          => $new_comments
        ];

        // where condition (VERY important unless you enjoy chaos)
        $this->db->where('id', $review_id);
        $this->db->update('bd_wise_reviews', $updateData);

        $this->session->set_flashdata('success_message', " Sales Review Updated Successfully & Reviews Closed by Line Manager.");

    }else{
            $updateData = [
                'to_uid'        => "",
                'session_time'  => $session_duration_minutes,
                'comments'      => $new_comments
            ];
            $this->db->where('id', $review_id);
            $this->db->update('bd_wise_reviews', $updateData);
        }

    // Set flash message
    if ($success_count > 0) {
        $this->session->set_flashdata('success_message', "Saved $success_count ratings. Errors: $error_count.");
    } else {
        $this->session->set_flashdata('error_message', 'Failed to save ratings. Please try again.');
    }

    // Redirect back to the review page
     redirect('SalesReviews/index');
}

*/




public function saveReviewRatings() {

    $user = $this->session->userdata('user');

    if (empty($user)) {
        $this->session->set_flashdata('error_message', " You must be logged in to view this page.");
        redirect('Menu/main');
    }
    $uid        = $user['user_id'];
    $uyid       = $user['type_id'];

    $this->load->library('session');
    $this->load->model('Menu_model');
    $this->load->model('Report_model');
    $this->load->model('SalesReviews_model');

    // Get posted data
    $ratings        = $this->input->post('rating');    
    $remarks        = $this->input->post('remarks');   
    $metric_values  = $this->input->post('metric_value') ?: array(); // new: metric values for cluster metrics
    $review_id      = $this->input->post('review_id'); 
    $message        = $this->input->post('message');

    $sdate          = $this->input->post('sdate');
    $edate          = $this->input->post('edate');

    $session_duration_seconds   = $this->input->post('session_duration_seconds');
    $session_duration_minutes   = floor($session_duration_seconds / 60);

    // Get logged-in user id (reviewer)
    $reviewed_by = $this->session->userdata('user_id'); // Adjust based on your session key

    if (empty($ratings) || !is_array($ratings)) {
        $this->session->set_flashdata('error_message', 'No ratings data submitted.');
        redirect($_SERVER['HTTP_REFERER']);
        return;
    }

    $getBDWiseReviewByReviewID = $this->SalesReviews_model->GetBDWiseReviewByReviewID($review_id);
    $exits_comments = $getBDWiseReviewByReviewID[0]->comments;
    $to_uid = $getBDWiseReviewByReviewID[0]->to_uid;
    $to_name = $getBDWiseReviewByReviewID[0]->to_name;

    if (empty($exits_comments)) {
        $new_comments = $to_name.' - '.$message[$to_uid];
    } else {
        $new_comments = $exits_comments. "<br/>". $to_name.' - '.$message[$to_uid];
    }

    $success_count = 0;
    $error_count = 0;

    // Loop through each user (team members) for all ratings
    foreach ($ratings as $user_id => $metrics) {
        if (!is_array($metrics)) continue;

        foreach ($metrics as $metric_key => $rating) {
            $rating = intval($rating);
            if ($rating < 1 || $rating > 5) {
                $error_count++;
                continue;
            }

            $remark = isset($remarks[$user_id][$metric_key]) ? trim($remarks[$user_id][$metric_key]) : '';

            // Check if this is a cluster metric (key starts with "cluster_")
            if (strpos($metric_key, 'cluster_') === 0) {
                // Format: cluster_<metric_key>_<cluster_id>
                $parts = explode('_', $metric_key);
                array_shift($parts); // remove 'cluster'
                $cluster_id = array_pop($parts); // last part is cluster_id
                $actual_metric_key = implode('_', $parts); // remaining parts form the metric key

                // Get the metric value from the hidden input (snapshot)
                $metric_value = isset($metric_values[$user_id][$metric_key]) 
                                ? $metric_values[$user_id][$metric_key] 
                                : null;

                // Prepare data for cluster_metrics table
                $cluster_data = [
                    'review_id'   => $review_id,
                    'user_id'     => $user_id,
                    'cluster_id'  => $cluster_id,
                    'metric_key'  => $actual_metric_key,
                    'metric_value'=> $metric_value,
                    'rating'      => $rating,
                    'remarks'     => $remark,
                    'created_at'  => date('Y-m-d H:i:s'),
                    'updated_at'  => date('Y-m-d H:i:s')
                ];

                // Delete any existing record for this review/user/cluster/metric
                $this->db->where('review_id', $review_id)
                         ->where('user_id', $user_id)
                         ->where('cluster_id', $cluster_id)
                         ->where('metric_key', $actual_metric_key)
                         ->delete('review_cluster_metrics');

                // Insert new record
                if ($this->db->insert('review_cluster_metrics', $cluster_data)) {
                    $success_count++;
                } else {
                    $error_count++;
                }
            } else {
                // Standard metric (non-cluster) – use existing model method
                $data = [
                    'bd_wise_reviews_id' => $review_id,
                    'user_id'            => $user_id,
                    'metric_key'         => $metric_key,
                    'rating'             => $rating,
                    'remarks'            => $remark,
                    'reviewed_by'        => $uid,
                    'sdate'              => $sdate,
                    'edate'              => $edate,
                    'review_date'        => date('Y-m-d')
                ];

                if ($this->SalesReviews_model->save_review($data)) {
                    $success_count++;
                } else {
                    $error_count++;
                }
            }
        }
    }

    // Process overall cluster ratings (single rating per cluster, if used)
    $cluster_ratings = $this->input->post('cluster_rating');
    $cluster_remarks = $this->input->post('cluster_remarks') ?: array();

    if (!empty($cluster_ratings) && is_array($cluster_ratings)) {
        foreach ($cluster_ratings as $user_id => $clusters) {
            if (!is_array($clusters)) continue;
            foreach ($clusters as $cluster_id => $rating) {
                $rating = intval($rating);
                if ($rating < 1 || $rating > 5) continue;

                $remark = isset($cluster_remarks[$user_id][$cluster_id]) 
                          ? trim($cluster_remarks[$user_id][$cluster_id]) 
                          : '';

                // Delete any existing overall cluster rating for this review/user/cluster
                $this->db->delete('review_cluster_ratings', [
                    'review_id' => $review_id,
                    'user_id'   => $user_id,
                    'cluster_id'=> $cluster_id
                ]);

                $insert_data = [
                    'review_id'  => $review_id,
                    'user_id'    => $user_id,
                    'cluster_id' => $cluster_id,
                    'rating'     => $rating,
                    'remarks'    => $remark,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                if ($this->db->insert('review_cluster_ratings', $insert_data)) {
                    $success_count++;
                } else {
                    $error_count++;
                }
            }
        }
    }

    $allteams = $this->SalesReviews_model->GetAllTeamByLineManager($uid, $review_id);

    if (sizeof($allteams) == 0) {
        $updateData = [
            'to_uid'        => "",
            'end_date'      => date('Y-m-d H:i:s'),
            'review_status' => "Closed",
            'close_reamrks' => "$message[$to_uid]",
            'session_time'  => $session_duration_minutes,
            'comments'      => $new_comments
        ];
        $this->db->where('id', $review_id);
        $this->db->update('bd_wise_reviews', $updateData);
        $this->session->set_flashdata('success_message', " Sales Review Updated Successfully & Reviews Closed by Line Manager.");
    } else {
        $updateData = [
            'to_uid'        => "",
            'session_time'  => $session_duration_minutes,
            'comments'      => $new_comments
        ];
        $this->db->where('id', $review_id);
        $this->db->update('bd_wise_reviews', $updateData);
    }

    // Set flash message
    if ($success_count > 0) {
        $this->session->set_flashdata('success_message', "Saved $success_count ratings. Errors: $error_count.");
    } else {
        $this->session->set_flashdata('error_message', 'Failed to save ratings. Please try again.');
    }

    redirect('SalesReviews/index');
}

    // ************************************** Sales Reviews End Here ******************************************

}