<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	
	if (!function_exists('SameStatusTillDate')) {
	    function SameStatusTillDate($uid,$userTypeid,$sdate,$edate,$status,$SelectedCluster,$SelectedCategory,$SelectedUsers,$SelectedpartnerType){
	        $CI =& get_instance();
	        // Load the model if not already loaded
	        $CI->load->model('Graph_model');
	        $DataSet = $CI->Graph_model->SameStatusTillDateByStatus($uid,$userTypeid,$sdate,$edate,$status,$SelectedCluster,$SelectedCategory,$SelectedUsers,$SelectedpartnerType);
            
	        return $DataSet;
	    }
	}

	if (!function_exists('getLastActionDetails')) {
	    function getLastActionDetails($cmp_ID,$userID,$cdate){
	        $CI =& get_instance();
	        // Load the model if not already loaded
	        $CI->load->model('Menu_model');
	        $DataSet = $CI->Menu_model->getLastActionDetails($cmp_ID,$userID,$cdate);
            
	        return $DataSet;
	    }
	}

	if (!function_exists('get_CompanyStatus')) {
	    function get_CompanyStatus($company_id,$cmp_ID){
	        $CI =& get_instance();
	        // Load the model if not already loaded
	        $CI->load->model('Menu_model');
	        $DataSet = $CI->Menu_model->get_CompanyStatus($company_id,$cmp_ID);
            
	        return $DataSet;
	    }
	}

	if (!function_exists('getSameStatusSince')) {
	    function getSameStatusSince($id,$date){
	        $CI =& get_instance();
	        // Load the model if not already loaded
	        $CI->load->model('Menu_model');
	        $DataSet = $CI->Menu_model->getSameStatusSince($id,$date);
            
	        return $DataSet;
	    }
	}

	if (!function_exists('getFunnelTaskforCLM')) {
	    function getFunnelTaskforCLM($id){
	        $CI =& get_instance();
	        // Load the model if not already loaded
	        $CI->load->model('Management_model');
	        $DataSet = $CI->Management_model->getFunnelTaskforCLM($id);
            
	        return $DataSet;
	    }
	}


	if (!function_exists('get_AvgTime')) {
	    function get_AvgTime($action_id){
	        $CI =& get_instance();
	        // Load the model if not already loaded
	        $CI->load->model('Management_model');
	        $DataSet = $CI->Management_model->get_AvgTime($action_id);
            
	        return $DataSet;
	    }
	}

	if (!function_exists('getMomData')) {
	    function getMomData($taskID){
	        $CI =& get_instance();
	        // Load the model if not already loaded
	        $CI->load->model('Menu_model');
	        $DataSet = $CI->Menu_model->getMomDetails($taskID);
            
	        return $DataSet;
	    }
	}

	if (!function_exists('getProposalData')) {
	    function getProposalData($taskID){
	        $CI =& get_instance();
	        // Load the model if not already loaded
	        $CI->load->model('Menu_model');
	        $DataSet = $CI->Menu_model->getProposalData($taskID);
            
	        return $DataSet;
	    }
	}

if (!function_exists('get_AvgMeetingTime')) {
	    function get_AvgMeetingTime($action_id){
	        $CI =& get_instance();
	        // Load the model if not already loaded
	        $CI->load->model('Management_model');
	        $DataSet = $CI->Management_model->get_AvgMeetingTime($action_id);
	        return $DataSet;
	    }
	}
?>