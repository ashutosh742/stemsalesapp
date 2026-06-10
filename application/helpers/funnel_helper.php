<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	
	if ( ! function_exists('date_diff_format'))
	{
		/**
		 * Calculate the difference between two dates and return in "days hours min" format.
		 *
		 * @param string $date1 The first date (in 'Y-m-d H:i:s' format)
		 * @param string $date2 The second date (in 'Y-m-d H:i:s' format)
		 * @return string The difference in "days hours min" format
		 */
		function date_diff_format($date1, $date2)
		{
			// Create DateTime objects from the input strings
			$datetime1 = new DateTime($date1);
			$datetime2 = new DateTime($date2);

			// Calculate the difference
			$interval = $datetime1->diff($datetime2);

			// Build the formatted difference string
			$result = '';

			if ($interval->days > 0) {
				$result .= $interval->days . ' days ';
			}
			if ($interval->h > 0) {
				$result .= $interval->h . ' hours ';
			}
			if ($interval->i > 0) {
				$result .= $interval->i . ' mins';
			}

			return trim($result);
		}
	}


	if (!function_exists('getFunnelDetails')) {
		function getFunnelDetails($uid,$userTypeid,$sdate,$edate,$status,$SelectedCluster,$SelectedCategory,$SelectedUsers,$SelectedpartnerType,$statusArray){
			$CI =& get_instance();
			// Load the model if not already loaded
			$CI->load->model('Graph_model');
			$DataSet = $CI->Graph_model->getFunnelDetails($uid,$userTypeid,$sdate,$edate,$status,$SelectedCluster,$SelectedCategory,$SelectedUsers,$SelectedpartnerType,$statusArray);
			
			return $DataSet;
		}
	}


	if (!function_exists('getLastRemark')) {
	    function getLastRemark($id){
	        $CI =& get_instance();
	        // Load the model if not already loaded
	        $CI->load->model('Graph_model');
	        $DataSet = $CI->Graph_model->get_tblbyidwithremark($id);
            
	        return $DataSet;
	    }
	}


	if (!function_exists('getStatusWiseTask')) {
	    function getStatusWiseTask($uid,$sdate,$edate,$selected_category,$selected_partnerType,$selected_userType,$selected_cluster,$selected_users,$SelectedSingleStatus,$userTypeid){
	        $CI =& get_instance();
	        // Load the model if not already loaded
	        $CI->load->model('Graph_model');
	        $DataSet = $CI->Graph_model->getStatusWiseTask($uid,$sdate,$edate,$selected_category,$selected_partnerType,$selected_userType,$selected_cluster,$selected_users,$SelectedSingleStatus,$userTypeid);
            
	        return $DataSet;
	    }
	}

	if (!function_exists('SameStatusTillDate')) {
	    function SameStatusTillDate($uid,$userTypeid,$sdate,$edate,$status,$SelectedCluster,$SelectedCategory,$SelectedUsers,$SelectedpartnerType){
	        $CI =& get_instance();
	        // Load the model if not already loaded
	        $CI->load->model('Graph_model');
	        $DataSet = $CI->Graph_model->SameStatusTillDateByStatus($uid,$userTypeid,$sdate,$edate,$status,$SelectedCluster,$SelectedCategory,$SelectedUsers,$SelectedpartnerType);
            
	        return $DataSet;
	    }
	}

	if (!function_exists('getMonthWiseFunnel')) {
	    function getMonthWiseFunnel($uid,$userTypeid,$sdate,$edate,$status,$SelectedCluster,$SelectedCategory,$SelectedUsers,$SelectedpartnerType){
	        $CI =& get_instance();
	        // Load the model if not already loaded
	        $CI->load->model('Graph_model');
	        $DataSet = $CI->Graph_model->getMonthWiseFunnel($uid,$userTypeid,$sdate,$edate,$status,$SelectedCluster,$SelectedCategory,$SelectedUsers,$SelectedpartnerType);
            
	        return $DataSet;
	    }
	}

	if (!function_exists('getActionWiseFunnel')) {
	    function getActionWiseFunnel($uid,$sdate,$edate,$selected_category,$selected_partnerType,$Selected_userType,$selected_cluster,$selected_users,$stid,$userTypeid,$action){
	        $CI =& get_instance();
	        // Load the model if not already loaded
	        $CI->load->model('Graph_model');
	        $DataSet = $CI->Graph_model->getActionWiseFunnel($uid,$sdate,$edate,$selected_category,$selected_partnerType,$Selected_userType,$selected_cluster,$selected_users,$stid,$userTypeid,$action);
            
	        return $DataSet;
	    }
	}

	if (!function_exists('get_OtherUserOnMyFunnelGraph')) {
	    function get_OtherUserOnMyFunnelGraph($uid,$month,$financialYear,$sdate,$edate,$selected_category,$selected_partnerType,$Selected_userType,$selected_cluster,$selected_users,$userTypeid,$Selected_action,$Selected_purpose){
	        $CI =& get_instance();
	        // Load the model if not already loaded
	        $CI->load->model('Graph_model');
	        $DataSet = $CI->Graph_model->get_OtherUserOnMyFunnelGraph($uid,$month,$financialYear,$sdate,$edate,$selected_category,$selected_partnerType,$Selected_userType,$selected_cluster,$selected_users,$userTypeid,$Selected_action,$Selected_purpose);
            
	        return $DataSet;
	    }
	}


	if (!function_exists('getTaskNameById')) {
	    function getTaskNameById($id){
	        $CI =& get_instance();
	        // Load the model if not already loaded
	        $CI->load->model('Menu_model');
	        $DataSet = $CI->Menu_model->getTaskNameById($id);
            
	        return $DataSet;
	    }
	}

	if (!function_exists('get_timeslot')) {
	    function get_timeslot(){
	        $CI =& get_instance();
	        // Load the model if not already loaded
	        $CI->load->model('Menu_model');
	        $DataSet = $CI->Menu_model->get_timeslot();
            
	        return $DataSet;
	    }
	}

	if (!function_exists('get_taskplannedSlotwise')) {
	    function get_taskplannedSlotwise($selected_users,$Selected_userType,$sdate,$edate,$t1,$t2,$selected_partnerType,$selected_category){
	        $CI =& get_instance();
	        // Load the model if not already loaded
	        $CI->load->model('Graph_model');
	        $DataSet = $CI->Graph_model->get_taskplannedSlotwise($selected_users,$Selected_userType,$sdate,$edate,$t1,$t2,$selected_partnerType,$selected_category);
            
	        return $DataSet;
	    }
	}

	if (!function_exists('get_taskinitiatedSlotwise')) {
	    function get_taskinitiatedSlotwise($selected_users,$Selected_userType,$sdate,$edate,$t1,$t2,$selected_partnerType,$selected_category){
	        $CI =& get_instance();
	        // Load the model if not already loaded
	        $CI->load->model('Graph_model');
	        $DataSet = $CI->Graph_model->get_taskinitiatedSlotwise($selected_users,$Selected_userType,$sdate,$edate,$t1,$t2,$selected_partnerType,$selected_category);
            
	        return $DataSet;
	    }
	}

	if (!function_exists('get_taskupdatedSlotwise')) {
	    function get_taskupdatedSlotwise($selected_users,$Selected_userType,$sdate,$edate,$t1,$t2,$selected_partnerType,$selected_category){
	        $CI =& get_instance();
	        // Load the model if not already loaded
	        $CI->load->model('Graph_model');
	        $DataSet = $CI->Graph_model->get_taskupdatedSlotwise($selected_users,$Selected_userType,$sdate,$edate,$t1,$t2,$selected_partnerType,$selected_category);
            
	        return $DataSet;
	    }
	}
	
	if (!function_exists('userWorkFrom')) {
	    function userWorkFrom(){
	        $CI =& get_instance();
	        // Load the model if not already loaded
	        $CI->load->model('Menu_model');
	        $DataSet = $CI->Menu_model->userWorkFrom();
            
	        return $DataSet;
	    }
	}

	if (!function_exists('actualDayStartFrom')) {
	    function actualDayStartFrom($uid,$startDate,$endDate,$tdate,$postUserType,$postUsers,$id){
	        $CI =& get_instance();
	        // Load the model if not already loaded
	        $CI->load->model('Menu_model');
	        $DataSet = $CI->Menu_model->actualDayStartFrom($uid,$startDate,$endDate,$tdate,$postUserType,$postUsers,$id);
            
	        return $DataSet;
	    }
	}

	if (!function_exists('plannedDayStartFrom')) {
	    function plannedDayStartFrom($uid,$startDate,$endDate,$tdate,$postUserType,$postUsers,$id){
	        $CI =& get_instance();
	        // Load the model if not already loaded
	        $CI->load->model('Menu_model');
	        $DataSet = $CI->Menu_model->plannedDayStartFrom($uid,$startDate,$endDate,$tdate,$postUserType,$postUsers,$id);
            
	        return $DataSet;
	    }
	}

	if (!function_exists('get_status')) {
	    function getAction(){
	        $CI =& get_instance();
	        // Load the model if not already loaded
	        $CI->load->model('Menu_model');
	        $DataSet = $CI->Menu_model->getAction();
            
	        return $DataSet;
	    }
	}

	if (!function_exists('getTaskTypeConversion')) {
	    function getTaskTypeConversion($uid,$startDate,$endDate,$action_id,$userType,$users){
	        $CI =& get_instance();
	        // Load the model if not already loaded
	        $CI->load->model('Graph_model');
	        $DataSet = $CI->Graph_model->get_taskTypeConversion($uid,$startDate,$endDate,$action_id,$userType,$users);
            
	        return $DataSet;
	    }
	}

	if (!function_exists('getStatus')) {
	    function getStatus(){
	        $CI =& get_instance();
	        // Load the model if not already loaded
	        $CI->load->model('Graph_model');
	        $DataSet = $CI->Graph_model->getStatus();
            
	        return $DataSet;
	    }
	}

	if (!function_exists('get_taskTypeConversionByStatus')) {
	    function get_taskTypeConversionByStatus($uid,$startDate,$endDate,$action_id,$stid,$Selected_userType,$selected_users){
	        $CI =& get_instance();
	        // Load the model if not already loaded
	        $CI->load->model('Graph_model');
	        $DataSet = $CI->Graph_model->get_taskTypeConversionByStatus($uid,$startDate,$endDate,$action_id,$stid,$Selected_userType,$selected_users);
            
	        return $DataSet;
	    }
	}


	if (!function_exists('getStatusTaskTypeConversion')) {
	    function getStatusTaskTypeConversion($uid,$startDate,$endDate,$status_id,$userType,$users){
	        $CI =& get_instance();
	        // Load the model if not already loaded
	        $CI->load->model('Graph_model');
	        $DataSet = $CI->Graph_model->getStatusTaskTypeConversion($uid,$startDate,$endDate,$status_id,$userType,$users);
            
	        return $DataSet;
	    }
	}


	if (!function_exists('get_StatustaskTypeConversionByStatus')) {
	    function get_StatustaskTypeConversionByStatus($uid,$startDate,$endDate,$status_id,$status_id1,$userType,$users){
	        $CI =& get_instance();
	        // Load the model if not already loaded
	        $CI->load->model('Graph_model');
	        $DataSet = $CI->Graph_model->get_StatustaskTypeConversionByStatus($uid,$startDate,$endDate,$status_id,$status_id1,$userType,$users);
            
	        return $DataSet;
	    }
	}

	if (!function_exists('getTaskwiseDetails')) {
	    function getTaskwiseDetails($selected_users,$Selected_userType,$sdate,$edate,$selectedTask,$selected_partnerType,$selected_category){
	        $CI =& get_instance();
	        // Load the model if not already loaded
	        $CI->load->model('Graph_model');
	        $DataSet = $CI->Graph_model->getTaskwiseDetails($selected_users,$Selected_userType,$sdate,$edate,$selectedTask,$selected_partnerType,$selected_category);
            
	        return $DataSet;
	    }
	}

	if (!function_exists('getMeetingwiseDetails')) {
	    function getMeetingwiseDetails($selected_users,$Selected_userType,$sdate,$edate,$selectedTask,$selected_partnerType,$selected_category){
	        $CI =& get_instance();
	        // Load the model if not already loaded
	        $CI->load->model('Graph_model');
	        $DataSet = $CI->Graph_model->getMeetingwiseDetails($selected_users,$Selected_userType,$sdate,$edate,$selectedTask,$selected_partnerType,$selected_category);
            
	        return $DataSet;
	    }
	}

	if (!function_exists('get_bdRequest')) {
	    function get_bdRequest(){
	        $CI =& get_instance();
	        // Load the model if not already loaded
	        $CI->load->model('Graph_model');
	        $DataSet = $CI->Graph_model->get_bdRequest();
            
	        return $DataSet;
	    }
	}

	if (!function_exists('get_bdRequestByType')) {
	    function get_bdRequestByType($sdate,$edate,$selected_users,$rtype){
	        $CI =& get_instance();
	        // Load the model if not already loaded
	        $CI->load->model('Graph_model');
	        $DataSet = $CI->Graph_model->get_bdRequestByType($sdate,$edate,$selected_users,$rtype);
            
	        return $DataSet;
	    }
	}

	if (!function_exists('get_bdRequestByTypeandStage')) {
	    function get_bdRequestByTypeandStage($sdate,$edate,$selected_users,$rtype,$j){
	        $CI =& get_instance();
	        // Load the model if not already loaded
	        $CI->load->model('Graph_model');
	        $DataSet = $CI->Graph_model->get_bdRequestByTypeandStage($sdate,$edate,$selected_users,$rtype,$j);
            
	        return $DataSet;
	    }
	}

	if (!function_exists('get_RIDDayWise')) {
	    function get_RIDDayWise($sdate,$edate,$selected_user,$status){
	        $CI =& get_instance();
	        // Load the model if not already loaded
	        $CI->load->model('Graph_model');
	        $DataSet = $CI->Graph_model->get_RIDDayWise($sdate,$edate,$selected_user,$status);
            
	        return $DataSet;
	    }
	}

?>