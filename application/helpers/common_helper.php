<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('getGraphName')) {
    function getGraphName($graphname){
        $CI =& get_instance();
        // Load the model if not already loaded
        $CI->load->model('Graph_model');
        $graphtitle = $CI->Graph_model->graphlist($graphname);
        return $graphtitle['detail'];
    }
}

if (!function_exists('getUserNameById')) {
    function getUserNameById($usedId){
    
        $CI =& get_instance();
        // Load the model if not already loaded
        $CI->load->model('Menu_model');
        $userName = $CI->Menu_model->getUserNameById($usedId);
        return $userName;
    }
}
if (!function_exists('getCompanyNameByCmpid')) {
    function getCompanyNameByCmpid($company_id){
        $CI =& get_instance();
        // Load the model if not already loaded
        $CI->load->model('Menu_model');
        $companyName = $CI->Menu_model->getCompanyNameByCmpid($company_id);
        return $companyName;
    }
}
if (!function_exists('getUserRoleById')) {
    function getUserRoleById($typeRole){
        $CI =& get_instance();
        // Load the model if not already loaded
        $CI->load->model('Menu_model');
        $userTypeData = $CI->Menu_model->get_utype($typeRole);
        return $userTypeData[0]->name;
    }
}
if (!function_exists('getCStatusBystatusId')) {
    function getCStatusBystatusId($cstatus){
        $CI =& get_instance();
        // Load the model if not already loaded
        $CI->load->model('Menu_model');
        $cStatusName = $CI->Menu_model->getCStatusBystatusId($cstatus);
        return $cStatusName[0]->name;
    }
}

if (!function_exists('getWeeklyDates')) {
function getWeeklyDates($startDate) {
    $startOfWeek = date('Y-m-d', strtotime('monday this week', strtotime($startDate)));
    $endOfWeek = date('Y-m-d', strtotime('sunday this week', strtotime($startDate)));
    return ['start' => $startOfWeek, 'end' => $endOfWeek];
}
}

if (!function_exists('getMonthlyDates')) {

function getMonthlyDates($startDate) {
    $startOfMonth = date('Y-m-01', strtotime($startDate)); // First day of the month
    $endOfMonth = date('Y-m-t', strtotime($startDate)); // Last day of the month
    return ['start' => $startOfMonth, 'end' => $endOfMonth];
}
}

if (!function_exists('getQuarterlyDates')) {

function getQuarterlyDates($startDate) {
    $currentMonth = date('n', strtotime($startDate));
    $currentYear = date('Y', strtotime($startDate));
    
    if ($currentMonth >= 1 && $currentMonth <= 3) {
        $startOfQuarter = "$currentYear-01-01";
        $endOfQuarter = "$currentYear-03-31";
    } elseif ($currentMonth >= 4 && $currentMonth <= 6) {
        $startOfQuarter = "$currentYear-04-01";
        $endOfQuarter = "$currentYear-06-30";
    } elseif ($currentMonth >= 7 && $currentMonth <= 9) {
        $startOfQuarter = "$currentYear-07-01";
        $endOfQuarter = "$currentYear-09-30";
    } else {
        $startOfQuarter = "$currentYear-10-01";
        $endOfQuarter = "$currentYear-12-31";
    }

    return ['start' => $startOfQuarter, 'end' => $endOfQuarter];
}
}

if (!function_exists('getFortnightlyDates')) {
function getFortnightlyDates($startDate) {
    $currentDayOfMonth = date('d', strtotime($startDate));
    $startOfFortnight = ($currentDayOfMonth <= 15) ? date('Y-m-01', strtotime($startDate)) : date('Y-m-16', strtotime($startDate));
    $endOfFortnight = ($currentDayOfMonth <= 15) ? date('Y-m-15', strtotime($startDate)) : date('Y-m-t', strtotime($startDate));
    
    return ['start' => $startOfFortnight, 'end' => $endOfFortnight];
}
}

if (!function_exists('getHalfYearlyDates')) {
function getHalfYearlyDates($startDate) {
    $currentMonth = date('n', strtotime($startDate));
    $currentYear = date('Y', strtotime($startDate));
    
    if ($currentMonth <= 6) {
        $startOfHalfYear = "$currentYear-01-01";
        $endOfHalfYear = "$currentYear-06-30";
    } else {
        $startOfHalfYear = "$currentYear-07-01";
        $endOfHalfYear = "$currentYear-12-31";
    }
    return ['start' => $startOfHalfYear, 'end' => $endOfHalfYear];
}
}
if (!function_exists('getUserZone')) {
    function getUserZone($zoneId) {
        $CI =& get_instance();
        // Load the model if not already loaded
        $CI->load->model('Menu_model');
        $zoneName = $CI->Menu_model->getUserZone($zoneId);
        return $zoneName; 
    }
    }

// if (!function_exists('format_date')) {
//     function format_date($date) {
//         return date('F j, Y, g:i a', strtotime($date));
//     }
// }


if(!function_exists('getDateTimeDiff')){

    function getDateTimeDiff($sdate,$edate){
        $datetime1 = new DateTime($sdate);
        $datetime2 = new DateTime($edate);
        
        // Get the difference
        $interval = $datetime1->diff($datetime2);
        
        // Format the difference as hours:minutes:seconds
        $formattedDifference = $interval->format('%H:%I:%S');
        
       return $formattedDifference;
    }
}

if(!function_exists('getLocationByLatlong')){
    function getLocationByLatlong($lat,$long){
        $CI =& get_instance();
        // Load the model if not already loaded
        $CI->load->model('Menu_model');
        $location = $CI->Menu_model->getLocationByLatlong($lat,$long);
        return $location->city_name; 
    }
	if(!function_exists('getLocationsfromURL')){
    		function getLocationsfromURL($lat,$long){
        	$CI =& get_instance();
        	// Load the model if not already loaded
        	$CI->load->model('Menu_model');
        	$location = $CI->Menu_model->getLocationsfromURL($lat,$long);
        	return $location->city_name; 
    	}
	}
}

?>