
<?php

date_default_timezone_set("Asia/Calcutta");
defined('BASEPATH') OR exit('No direct script access allowed');

class Graphs extends CI_Controller {

    public function __construct() {
        parent::__construct();
        // Load the URL helper
        $this->load->helper('common_helper');
    }

public function FGraphs(){
    if(isset($_POST['sdate'])){
        $sdate = $_POST['sdate'];
        $edate = $_POST['edate'];
        }
        else{
            $sdate = "2023-04-01";
            $edate = date('Y-m-d');
        }
        $sd=$sdate;
        $ed=$edate;
        $sdate = new DateTime($sdate);
        $edate = new DateTime($edate);
        $user           = $this->session->userdata('user');
        $data['user']   = $user;
        $uid            = $user['user_id'];
        $uyid           = $user['type_id'];
        $this->load->model('Menu_model');
        $this->load->model('Graph_model');
        $dt             = $this->Menu_model->get_utype($uyid);
        $fgraphcount    =  $this->Graph_model->graphCountPerCategory($dt[0]->id,'Funnel');
        $dep_name       = $dt[0]->name;
       
        if(!empty($user)){
            $this->load->view($dep_name.'/FGraphs',['uid'=>$uid,'data'=>$dt, 'user'=>$user, 'sdate'=>$sdate,'edate'=>$edate,'sd'=>$sd,'ed'=>$ed]);
        }else{
            redirect('Menu/main');
        }
}


public function FGraph1($stid,$code){
    if(!empty($stid) && !empty($code)){
        if(isset($_POST['sdate'])){
            $sdate = $_POST['sdate'];
            $edate = $_POST['edate'];
            }
            else{
                $sdate = "2023-04-01";
                $edate = date('Y-m-d');
            }
            $sd=$sdate;
            $ed=$edate;
            $sdate = new DateTime($sdate);
            $edate = new DateTime($edate);
    
            $user = $this->session->userdata('user');
           
            $data['user'] = $user;
            $uid = $user['user_id'];
            $uyid =  $user['type_id'];
            $this->load->model('Menu_model');
            // $this->load->model('Graph_model');
            // $graph_name = $this->graph_model->graphlist('FGraph1');
          
            $dt=$this->Menu_model->get_utype($uyid);
            $dep_name = $dt[0]->name;
            if(!empty($user)){
                $this->load->view($dep_name.'/FGraph1',['stid'=>$stid,'code'=>$code,'uid'=>$uid,'data'=>$dt, 'user'=>$user, 'sdate'=>$sdate,'edate'=>$edate,'sd'=>$sd,'ed'=>$ed]);
            }else{
                redirect('Menu/main');
            }
    }
    else{
        redirect('Menu/main');

    }
    
}
}
?>