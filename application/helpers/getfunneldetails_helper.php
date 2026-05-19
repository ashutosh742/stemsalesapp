<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	
	if (!function_exists('getFunnelDetails')) {
	    function getFunnelDetails($uid,$userTypeid,$sdate,$edate,$status,$SelectedCluster,$SelectedCategory,$SelectedUsers,$SelectedpartnerType,$statusArray){
	        $CI =& get_instance();
	        // Load the model if not already loaded
	        $CI->load->model('Graph_model');
	        $DataSet = $CI->Graph_model->getFunnelDetails($uid,$userTypeid,$sdate,$edate,$status,$SelectedCluster,$SelectedCategory,$SelectedUsers,$SelectedpartnerType,$statusArray);
            
	        return $DataSet;
	    }
	}


?>