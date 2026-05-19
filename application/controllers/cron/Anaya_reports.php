<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Anaya_reports extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library(array('AnayaAgent', 'Crm_emailer'));
        $this->database = $this->load->database();
    }

    /**
     * Daily BD Email
     * php index.php cron/Anaya_reports/daily_bd
     */
    public function daily_bd()
    {
        $today = date('Y-m-d');

        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

    
        $bds = $this->db
            ->select('ud.id AS user_details_id, ud.name, ud.email')
            ->from('user_details ud')
            ->join('user_type ut', 'ut.id = ud.type_id', 'left')
            // ->where('ut.name', 'BD')
            ->where('ud.status', 'active')
            ->where_in('ud.type_id','3,24')
            ->get()
            ->result_array();

        // dd($bds);

        foreach ($bds as $bd) {

            $pack = $this->anayaagent->bd_daily_pack($bd['user_details_id'], $today);

            $html = $this->load->view(
                'emails/anaya_bd_daily',
                array(
                    'bd'      => $bd,
                    'pack'    => $pack,
                    'metrics' => $pack['metrics']
                ),
                true
            );

            $this->crm_emailer->send_and_log(
                'Anaya – Your Day Plan & Funnel | ' . $today,
                $html,
                $bd['email'],
                array(
                    'from_email' => 'anaya@yourcrm.com',
                    'from_name'  => 'Anaya - CRM Agent',
                    'for_user'   => $bd['user_details_id'],
                    'type'       => 'daily_bd'
                )
            );
        }

        $this->crm_emailer->log_cron_run('daily_bd', array('date' => $today), 1);
    }






public function daily_bd111()
{
    // Show fatal error even if server hides it
    register_shutdown_function(function () {
        $err = error_get_last();
        if (!empty($err)) {
            echo "<pre>FATAL ERROR:\n";
            print_r($err);
            echo "</pre>";
        }
    });

    // Also show normal warnings
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    echo "START<br>";

    $today = date('Y-m-d');

    $bds = $this->db
        ->select('ud.id AS user_details_id, ud.name, ud.email')
        ->from('user_details ud')
        ->join('user_type ut', 'ut.id = ud.type_id', 'left')
        // ->where('ut.name', 'BD')
        ->where('ud.status', 'active')
        ->where_in('ud.type_id', '24')
        ->get()
        ->result_array();

    echo "BD Count: " . count($bds) . "<br>";

    if (empty($bds)) {
        echo "No BD found. Check user_type name = 'BD'<br>";
        return;
    }

    // TEMP: only 1 BD for testing
    $bds = array_slice($bds, 0, 1);

    foreach ($bds as $bd) {
        echo "Processing: " . $bd['email'] . "<br>";
        echo "Processing: " . $bd['email'] . "<br>";

        $pack = $this->anayaagent->bd_daily_pack($bd['user_details_id'], $today);
        echo "Pack ready<br>";

        // TEMP: comment email sending and only render view
        $html = $this->load->view('emails/anaya_bd_daily', array(
            'bd'      => $bd,
            'pack'    => $pack,
            'metrics' => $pack['metrics']
        ), true);

        echo "HTML length: " . strlen($html) . "<br>";

        // Uncomment after HTML renders successfully
        
        $this->crm_emailer->send_and_log(
            'Anaya – Your Day Plan & Funnel | ' . $today,
            $html,
            'deepak.kumar@stemlearning.in',
            array(
                'from_email' => 'anaya@yourcrm.com',
                'from_name'  => 'Anaya - CRM Agent',
                'for_user'   => $bd['user_details_id'],
                'type'       => 'daily_bd'
            )
        );
        echo "Email sent<br>";
        
    }

    echo "DONE<br>";
}














}