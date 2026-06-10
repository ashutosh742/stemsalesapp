<?php
date_default_timezone_set("Asia/Calcutta");
defined('BASEPATH') or exit('No direct script access allowed');
// require_once('Menu.php');
class GraphNew extends CI_Controller
{

    private $user;
    private $uid;
    private $uyid;
    private $dep_name;
    private $dt;

    public function __construct()
    {
        parent::__construct();
        // Load common libraries, helpers, or models here
        $this->load->helper('url');
        // $this->load->helper('SameStatusTillDate_helper');
        // $this->load->helper('getFunnelDetails_helper');
        $this->load->helper('funnel_helper');

        $this->load->library('session');
        $this->load->library('pagination');
        $this->load->model('Graph_Model');
        $this->load->model('Menu_model');

        $this->user = $this->session->userdata('user');
        $this->uid = $this->user['user_id'];
        $this->uyid = $this->user['type_id'];

        $this->dt = $this->Menu_model->get_utype($this->uyid);

        $this->dep_name = $this->dt[0]->name;

        if (in_array(!$this->uyid, [15, 13, 2, 4])) {
            echo "Stem Learning Pvt Ltd";
            echo "<br/>";
            exit;
        }
    }

    public function StatusWiseFunnelGraph($code)
    {

        // var_dump($_POST);die;
        if (isset($_POST['startDate']) && isset($_POST['endDate'])) {

            $sdate = $_POST['startDate'];
            $edate = $_POST['endDate'];
        } else {
            $sdate = date('Y-m-d');
            $edate = date('Y-m-d');
        }

        // var_dump($sdate,$edate);die;
        $user = $this->session->userdata('user');
        $data['user'] = $user;
        $uid = $user['user_id'];
        $userTypeid = $user['type_id'];
        $dt = $this->Graph_Model->get_utype($userTypeid);
        $dep_name = $dt[0]->name;

        $chartData = $this->Graph_Model->get_fannalstwise($uid, $userTypeid, $sdate, $edate);

        $TableData = $this->Graph_Model->get_fannalbycode_OG($uid, $userTypeid, $sdate, $edate);

        // var_dump($chartData);die;
        if (!empty($user)) {
            $this->load->view('include/header');
            $this->load->view($dep_name . '/nav', ['uid' => $uid, 'user' => $user]);
            $this->load->view('Graphs/FGraph1_New', ['code' => $code, 'uid' => $uid, 'user' => $user, 'sdate' => $sdate, 'edate' => $edate, 'chartData' => $chartData, 'TableData' => $TableData]);
            $this->load->view('include/footer');
        } else {
            redirect('Menu/main');
        }
    }

    public function FunnelAnalysis()
    {

        if (isset($_POST['startDate']) && isset($_POST['endDate'])) {

            $sdate = $_POST['startDate'];
            $edate = $_POST['endDate'];
        } else {
            $sdate = date('Y-m-d');
            $edate = date('Y-m-d');
        }

        if (isset($_POST['userType'])) {

            $userType = array_filter($_POST['userType'], function ($value) {
                return $value !== 'select_all';
            });
            // $userType = implode(',', ($userType));

        } else {
            $userType = [];
        }

        if (isset($_POST['cluster'])) {

            $cluster = array_filter($_POST['cluster'], function ($value) {
                return $value !== 'select_all';
            });
            // $cluster = implode(',', ($cluster));
        } else {
            $cluster = [];
        }

        if (isset($_POST['user'])) {

            $users = array_filter($_POST['user'], function ($value) {
                return $value !== 'select_all';
            });
            // $users = implode(',', ($users));
        } else {

            $users = [];
        }

        if (isset($_POST['partnerType'])) {

            $partnerType = array_filter($_POST['partnerType'], function ($value) {
                return $value !== 'select_all';
            });
            // $partnerType = implode(',', ($partnerType));

        } else {

            $partnerType = [];
        }

        if (isset($_POST['category'])) {

            $category = array_filter($_POST['category'], function ($value) {
                return $value !== 'select_all';
            });
            // $category = implode(',', ($category));

        } else {

            $category = [];
        }

        // $sdate = '2024-03-01';
        // var_dump($sdate,$edate);die;
        $user = $this->session->userdata('user');
        $data['user'] = $user;
        $uid = $user['user_id'];
        $userTypeid = $user['type_id'];
        $dt = $this->Graph_Model->get_utype($userTypeid);
        $dep_name = $dt[0]->name;

        $roles = $this->Graph_Model->getRoles($dt[0]->id);

        $partner_type = $this->Graph_Model->getPartnerType();

        $get_cluster = $this->Graph_Model->get_clusters();

        $TableData = $this->Graph_Model->get_TableData($uid, $userTypeid, $sdate, $edate, $userType, $cluster, $partnerType, $category, $users);

        $FunnelData = $this->Graph_Model->getGraphDetails($uid, $userTypeid, $sdate, $edate, $userType, $cluster, $partnerType, $category, $users);
        // var_dump($FunnelData);die;

        if(!empty($_POST['startDate']) && !empty($_POST['endDate'])){

            $SankeyData = $this->Graph_Model->getSankeyGraphData($uid, $userTypeid, $sdate, $edate, $userType, $cluster, $partnerType, $category, $users);

            // var_dump($SankeyData);die;

            $sankeyGraphData = [];

// Loop through the original data and format it
            foreach ($SankeyData as $item) {
                $sankeyGraphData[] = [
                    $item->fromStatus,  // Adjust as needed
                    $item->toStatus,    // Adjust as needed
                    (float)$item->count // Convert count to float
                ];
            }

        }else {

            // $nodesJson = [];
            // $linksJson = [];
            $sankeyGraphData = [];
        }

        if (!empty($user)) {
            $this->load->view('include/header');
            $this->load->view($dep_name . '/nav', ['uid' => $uid, 'user' => $user]);
            $this->load->view('Graphs/FunnelAnalysis', ['uid' => $uid, 'user' => $user, 'sdate' => $sdate, 'edate' => $edate, 'TableData' => $TableData, 'roles' => $roles, 'partner_type' => $partner_type, 'clusters' => $get_cluster, 'FunnelData' => $FunnelData, 'selected_partnerType' => $partnerType, 'selected_category' => $category, 'selected_cluster' => $cluster, 'selected_users' => $users, 'userType' => $userType,'jsonSankeyData' => $sankeyGraphData]);
            $this->load->view('include/footer');
        } else {
            redirect('Menu/main');
        }
    }

    public function StatusWiseFunnelGraphData()
    {


        $stid = isset($_POST['stid']) ? $_POST['stid'] : null;
        $sdate = isset($_POST['sdate']) ? $_POST['sdate'] : null;
        $edate = isset($_POST['edate']) ? $_POST['edate'] : null;
        $selected_partnerType = isset($_POST['selected_partnerType']) ? $_POST['selected_partnerType'] : null;
        $selected_userType = isset($_POST['selected_userType']) ? $_POST['selected_userType'] : null;
        $selected_cluster = isset($_POST['selected_cluster']) ? $_POST['selected_cluster'] : null;
        $selected_users = isset($_POST['selected_users']) ? $_POST['selected_users'] : null;
        $selected_category = isset($_POST['selected_category']) ? $_POST['selected_category'] : null;

        $selected_partnerType = (array) json_decode($selected_partnerType);
        $selected_userType = (array) json_decode($selected_userType);
        $selected_cluster = (array) json_decode($selected_cluster);
        $selected_users = (array) json_decode($selected_users);
        $selected_category = (array) json_decode($selected_category);
        
        //var_dump(($selected_users));die;

        $user = $this->session->userdata('user');
        $data['user'] = $user;
        $uid = $user['user_id'];
        $userTypeid = $user['type_id'];
        $dt = $this->Graph_Model->get_utype($userTypeid);
        $dep_name = $dt[0]->name;

        $TableData = $this->Graph_Model->getTableDataByStatus($uid, $userTypeid, $stid, $sdate, $edate, $selected_partnerType, $selected_category, $selected_userType, $selected_users, $selected_cluster);

        // var_dump($TableData);die;
        if (!empty($user)) {
            $this->load->view('include/header');
            $this->load->view($dep_name . '/nav', ['uid' => $uid, 'user' => $user]);
            $this->load->view('Graphs/DataByStatus', ['uid' => $uid, 'user' => $user, 'sdate' => $sdate, 'edate' => $edate, 'TableData' => $TableData]);
            $this->load->view('include/footer');
        } else {
            redirect('Menu/main');
        }
    }

    public function getUserByCluster()
    {

        $clusterID = array_filter($_POST['clusterId'], function ($value) {
            return $value !== 'select_all';
        });

        // $clusterID = implode(',', ($clusterID));

        $getClusterManagerByCluster = $this->Graph_Model->getClusterManagerByCluster($clusterID);

        echo $data = '<option value="select_all">Select All</option>';
        foreach ($getClusterManagerByCluster as $SignleUser) {
            echo $data = '<option value=' . $SignleUser->user_id . '>' . $SignleUser->name . '</option>';
        }
    }

    public function StatusWiseFunnelData()
    {

        $status_id = $this->input->post('stid');
        // var_dump($stid);die;
        $sdate = $_POST['sdate'];
        $edate = $_POST['edate'];
        $selected_partnerType = isset($_POST['selected_partnerType']) ? $_POST['selected_partnerType'] : null;
        $selected_category = isset($_POST['arrayselected_category']) ? $_POST['arrayselected_category'] : null;
        $selected_userType = isset($_POST['arrayselected_userType']) ? $_POST['arrayselected_userType'] : null;
        $selected_users = isset($_POST['arrayselected_user']) ? $_POST['arrayselected_user'] : null;
        $arrayselected_cluster = isset($_POST['selected_users']) ? $_POST['selected_users'] : null;

        // var_dump($_POST);die;
        $selected_partnerType = (array) json_decode($selected_partnerType);
        $selected_category = (array) json_decode($selected_category);
        $selected_userType = (array) json_decode($selected_userType);
        $selected_users = (array) json_decode($selected_users);
        $arrayselected_cluster = (array) json_decode($arrayselected_cluster);
        // var_dump($selected_partnerType);die;

        $user = $this->session->userdata('user');
        $data['user'] = $user;
        $uid = $user['user_id'];
        $userTypeid = $user['type_id'];
        $dt = $this->Graph_Model->get_utype($userTypeid);
        $dep_name = $dt[0]->name;

        $StatusWiseFunnelData = $this->Graph_Model->getStatusWiseFunnelData($uid, $sdate, $edate, $selected_partnerType, $arrayselected_cluster, $selected_users, $status_id, $selected_userType, $selected_category);

        // var_dump($StatusWiseFunnelData);die;

        if (!empty($user)) {
            $this->load->view('include/header');
            $this->load->view($dep_name . '/nav', ['uid' => $uid, 'user' => $user]);
            $this->load->view('Graphs/StatusWiseFunnelAnalysis', ['uid' => $uid, 'user' => $user, 'sdate' => $sdate, 'edate' => $edate, 'StatusWiseFunnelData' => $StatusWiseFunnelData]);
            $this->load->view('include/footer');
        } else {
            redirect('Menu/main');
        }
    }

    public function CityWiseFunnelAnalysis()
    {

        if (isset($_POST['startDate']) && isset($_POST['endDate'])) {

            $sdate = $_POST['startDate'];
            $edate = $_POST['endDate'];
        } else {

            $sdate = date('Y-m-d');
            $edate = date('Y-m-d');
        }


        if (isset($_POST['cluster'])) {

            $cluster = array_filter($_POST['cluster'], function ($value) {
                return $value !== 'select_all';
            });

        } else {

            $cluster = [];
        }
        
        if (isset($_POST['userType'])) {

            $userType = array_filter($_POST['userType'], function ($value) {
                return $value !== 'select_all';
            });
            // $userType = implode(',', ($userType));

        } else {
            $userType = [];
        }

        if (isset($_POST['user'])) {

            $users = array_filter($_POST['user'], function ($value) {
                return $value !== 'select_all';
            });
            // $users = implode(',', ($users));
        } else {

            $users = [];
        }

        // $sdate = '2024-03-01';
        // var_dump($sdate,$edate);die;
        $user = $this->session->userdata('user');
        $data['user'] = $user;
        $uid = $user['user_id'];
        $userTypeid = $user['type_id'];
        $dt = $this->Graph_Model->get_utype($userTypeid);
        $dep_name = $dt[0]->name;

        $roles = $this->Graph_Model->getRoles($dt[0]->id);

        // $TableData = $this->Graph_Model->get_TableData($uid,$userTypeid,$sdate,$edate,$cluster);
        $TableData = $this->Graph_Model->getCityWiseTableDetails($users, $userType, $sdate, $edate);
        $GraphData = $this->Graph_Model->getCityWiseGraphDetails($users, $userType, $sdate, $edate);
        // $TableData = '';
        // var_dump($GraphData);die;


        // var_dump($data);die;
        if (!empty($user)) {
            $this->load->view('include/header');
            $this->load->view($dep_name . '/nav', ['uid' => $uid, 'user' => $user]);
            $this->load->view('Graphs/CityWiseFunnelAnalysis', ['uid' => $uid, 'user' => $user, 'sdate' => $sdate, 'edate' => $edate, 'TableData' => $TableData, 'roles' => $roles, 'GraphData' => $GraphData]);
            $this->load->view('include/footer');
        } else {
            redirect('Menu/main');
        }
    }

    public function CityWiseFunnelGraphData()
    {

        $cityId = $this->uri->segment('3');
        $sdate = $this->uri->segment('4');
        $edate = $this->uri->segment('5');

        // var_dump(($selected_partnerType));die;
        $user = $this->session->userdata('user');
        $data['user'] = $user;
        $uid = $user['user_id'];
        $userTypeid = $user['type_id'];
        $dt = $this->Graph_Model->get_utype($userTypeid);
        $dep_name = $dt[0]->name;

        $TableData = $this->Graph_Model->getTableDataByCity($uid, $userTypeid, $cityId, $sdate, $edate);

        // var_dump($TableData);die;
        if (!empty($user)) {
            $this->load->view('include/header');
            $this->load->view($dep_name . '/nav', ['uid' => $uid, 'user' => $user]);
            $this->load->view('Graphs/DataByCity', ['uid' => $uid, 'user' => $user, 'sdate' => $sdate, 'edate' => $edate, 'TableData' => $TableData]);
            $this->load->view('include/footer');
        } else {
            redirect('Menu/main');
        }
    }

    public function PartnerWiseFunnelAnalysis()
    {

        if (isset($_POST['startDate']) && isset($_POST['endDate'])) {

            $sdate = $_POST['startDate'];
            $edate = $_POST['endDate'];
        } else {

            $sdate = date('Y-m-d');
            $edate = date('Y-m-d');
        }


        if (isset($_POST['partnerType'])) {

            $partnerType = array_filter($_POST['partnerType'], function ($value) {
                return $value !== 'select_all';
            });

        } else {

            $partnerType = [];
        }
        
        if (isset($_POST['userType'])) {

            $userType = array_filter($_POST['userType'], function ($value) {
                return $value !== 'select_all';
            });
            // $userType = implode(',', ($userType));

        } else {
            $userType = [];
        }

        if (isset($_POST['user'])) {

            $users = array_filter($_POST['user'], function ($value) {
                return $value !== 'select_all';
            });
            // $users = implode(',', ($users));
        } else {

            $users = [];
        }
        

        $partner_type = $this->Graph_Model->getPartnerType();
        $user = $this->session->userdata('user');
        $data['user'] = $user;
        $uid = $user['user_id'];
        $userTypeid = $user['type_id'];
        $dt = $this->Graph_Model->get_utype($userTypeid);
        $dep_name = $dt[0]->name;

        $roles = $this->Graph_Model->getRoles($dt[0]->id);


        $GraphData = $this->Graph_Model->getPartnerWiseGraphDetails($users, $userType, $sdate, $edate, $partnerType);
        $TableData = $this->Graph_Model->getPartnerWiseTableDetails($users, $userType, $sdate, $edate, $partnerType);
        // var_dump($GraphData);die;

        if (!empty($user)) {
            $this->load->view('include/header');
            $this->load->view($dep_name . '/nav', ['uid' => $uid, 'user' => $user]);
            $this->load->view('Graphs/PartnerWiseFunnelAnalysis', ['uid' => $uid, 'user' => $user, 'sdate' => $sdate, 'edate' => $edate, 'partner_type' => $partner_type,'partnerType' => $partnerType, 'TableData' => $TableData, 'roles' => $roles, 'GraphData' => $GraphData]);
            $this->load->view('include/footer');
        } else {
            redirect('Menu/main');
        }
    }

    public function getRoleUser_New(){

        $RoleId= $this->input->post('RoleId');
        $user = $this->session->userdata('user');
        $data['user'] = $user;
        $uid = $user['user_id'];
        $userTypeid = $user['type_id'];
        // var_dump($user);die;

        $this->db->select('*');
        $this->db->from('user_details');
        $this->db->where('status', 'active');

        if ($userTypeid == 2) {

            $this->db->where('user_details.admin_id', $uid);

        } elseif ($userTypeid == 4) {

            $this->db->where_in('pst_co', $uid);
        } elseif ($userTypeid == 9 || $userTypeid == 13) {

            $this->db->where_in('aadmin', $uid);
        }elseif ($userTypeid == 15 ) {

            $this->db->where_in('sales_co', $uid);
        } else {
            $this->db->where('user_details.user_id', $uid);
        }

        // $this->db->where('sales_co', $uid );

        $this->db->where_in('type_id', $RoleId);
        $this->db->order_by('name','ASC');

        $query = $this->db->get(); 
        // echo $this->db->last_query();die;
        
        $user_new =  $query->result();

        echo $data = '<option value="select_all">Select All</option>';

        foreach($user_new as $d){
            echo  $data = '<option value='.$d->user_id.'>'.$d->name.'</option>';
        }
    }
    public function PartnerWiseFunnelGraphData()
    {

        if (isset($_POST['partnetType_id'])) {

            $PartnerMasterID = isset($_POST['partnetType_id']) ? $_POST['partnetType_id'] : null;
            $sdate = isset($_POST['sdate']) ? $_POST['sdate'] : null;
            $edate = isset($_POST['edate']) ? $_POST['edate'] : null;
        } else {

            $PartnerMasterID = $this->uri->segment('3');
            $sdate = $this->uri->segment('4');
            $edate = $this->uri->segment('5');
        }

        // var_dump(($selected_partnerType));die;
        $user = $this->session->userdata('user');
        $data['user'] = $user;
        $uid = $user['user_id'];
        $userTypeid = $user['type_id'];
        $dt = $this->Graph_Model->get_utype($userTypeid);
        $dep_name = $dt[0]->name;

        $TableData = $this->Graph_Model->getTableDataByPartnerType($uid, $userTypeid, $PartnerMasterID, $sdate, $edate);

        // var_dump($TableData);die;
        if (!empty($user)) {
            $this->load->view('include/header');
            $this->load->view($dep_name . '/nav', ['uid' => $uid, 'user' => $user]);
            $this->load->view('Graphs/DataByPartnerType', ['uid' => $uid, 'user' => $user, 'sdate' => $sdate, 'edate' => $edate, 'TableData' => $TableData]);
            $this->load->view('include/footer');
        } else {
            redirect('Menu/main');
        }
    }

    public function CategoryWiseFunnelAnalysis()
    {

        // var_dump($_POST);die;
        if (isset($_POST['startDate']) && isset($_POST['endDate'])) {

            $sdate = $_POST['startDate'];
            $edate = $_POST['endDate'];
        } else {

            $sdate = date('Y-m-d');
            $edate = date('Y-m-d');
        }

//dd($_POST);

        if (isset($_POST['category'])) {

            $category = array_filter($_POST['category'], function ($value) {
                return $value !== 'select_all';
            });

            // $category = implode(',', ($category));

        } else {

            $category = '';
        }
        
        if (isset($_POST['userType'])) {

            $userType = array_filter($_POST['userType'], function ($value) {
                return $value !== 'select_all';
            });
            // $userType = implode(',', ($userType));

        } else {
            $userType = [];
        }

        if (isset($_POST['user'])) {

            $users = array_filter($_POST['user'], function ($value) {
                return $value !== 'select_all';
            });
            // $users = implode(',', ($users));
        } else {

            $users = [];
        }
        
        // var_dump($category);die;
        $user = $this->session->userdata('user');
        $data['user'] = $user;
        $uid = $user['user_id'];
        $userTypeid = $user['type_id'];
        $dt = $this->Graph_Model->get_utype($userTypeid);
        $dep_name = $dt[0]->name;

        $roles = $this->Graph_Model->getRoles($dt[0]->id);

        // var_dump($roles);die;
        $GraphData = $this->Graph_Model->getCategoryWiseGraphDetails($users, $userType, $sdate, $edate, $category);
        $TableData = $this->Graph_Model->getCategoryWiseTableDetails($users, $userType, $sdate, $edate, $category);
        // dd($GraphData);exit;
        // $GraphData = '';
        // $TableData = '';
        // var_dump($TableData);die;

        if (!empty($user)) {
            $this->load->view('include/header');
            $this->load->view($dep_name . '/nav', ['uid' => $uid, 'user' => $user]);
            $this->load->view('Graphs/CategoryWiseFunnelAnalysis', ['uid' => $uid, 'user' => $user, 'sdate' => $sdate, 'edate' => $edate,'roles' => $roles, 'TableData' => $TableData, 'category' => $category, 'GraphData' => $GraphData]);
            $this->load->view('include/footer');
        } else {
            redirect('Menu/main');
        }
    }

    public function CategoryWiseFunnelGraphData()
    {

        $Category = $this->uri->segment('3');
        $sdate = $this->uri->segment('4');
        $edate = $this->uri->segment('5');

        // var_dump(($Category));die;
        $user = $this->session->userdata('user');
        $data['user'] = $user;
        $uid = $user['user_id'];
        $userTypeid = $user['type_id'];
        $dt = $this->Graph_Model->get_utype($userTypeid);
        $dep_name = $dt[0]->name;

        $TableData = $this->Graph_Model->getTableDataByCategory($uid, $userTypeid, $Category, $sdate, $edate);

        if (!empty($user)) {
            $this->load->view('include/header');
            $this->load->view($dep_name . '/nav', ['uid' => $uid, 'user' => $user]);
            $this->load->view('Graphs/DataByCategory', ['uid' => $uid, 'user' => $user, 'sdate' => $sdate, 'edate' => $edate, 'TableData' => $TableData, 'category' => $Category]);
            $this->load->view('include/footer');
        } else {
            redirect('Menu/main');
        }
    }

    public function CompanyWithSameStatusSinceFunnleAnalysis()
    {

        // var_dump($_POST);die;
        if (isset($_POST['startDate']) && isset($_POST['endDate'])) {

            $sdate = $_POST['startDate'];
            $edate = $_POST['endDate'];
        } else {

            $sdate = date('Y-m-d');
            $edate = date('Y-m-d');
        }

        if (isset($_POST['status'])) {

            $SelectedStatus = array_filter($_POST['status'], function ($value) {
                return $value !== 'select_all';
            });
        } else {

            $SelectedStatus = [];
        }

        if (isset($_POST['partnerType'])) {

            $SelectedpartnerType = array_filter($_POST['partnerType'], function ($value) {
                return $value !== 'select_all';
            });
        } else {

            $SelectedpartnerType = [];
        }

        if (isset($_POST['cluster'])) {

            $SelectedCluster = array_filter($_POST['cluster'], function ($value) {
                return $value !== 'select_all';
            });
        } else {

            $SelectedCluster = [];
        }
        
        if (isset($_POST['userType'])) {

            $userType = array_filter($_POST['userType'], function ($value) {
                return $value !== 'select_all';
            });
            // $userType = implode(',', ($userType));

        } else {
            $userType = [];
        }
        
        if (isset($_POST['category'])) {

            $SelectedCategory = array_filter($_POST['category'], function ($value) {
                return $value !== 'select_all';
            });
        } else {

            $SelectedCategory = [];
        }

        if (isset($_POST['user'])) {

            $SelectedUsers = array_filter($_POST['user'], function ($value) {
                return $value !== 'select_all';
            });
        } else {

            $SelectedUsers = [];
        }

        $partner_type = $this->Graph_Model->getPartnerType();

        $user = $this->session->userdata('user');
        $data['user'] = $user;
        $uid = $user['user_id'];
        $userTypeid = $user['type_id'];
        $dt = $this->Graph_Model->get_utype($userTypeid);
        $dep_name = $dt[0]->name;

        $get_cluster = $this->Graph_Model->get_clusters();
        $status = $this->Graph_Model->getStatus();
        $roles = $this->Graph_Model->getRoles($dt[0]->id);
 
        // $GraphData = $this->Graph_Model->getCompanyWithSameStatusGraphDetails($uid,$userTypeid,$sdate,$edate,$SelectedStatus);
        

        $TableData = $this->Graph_Model->getCompanyWithSameStatusTableDetails($SelectedUsers, $userTypeid, $sdate, $edate, $SelectedStatus, $userType, $SelectedCategory, $SelectedUsers, $SelectedpartnerType);

        $GraphData = '';
        // var_dump($TableData);die;

        if (!empty($user)) {
            $this->load->view('include/header');
            $this->load->view($dep_name . '/nav', ['uid' => $uid, 'user' => $user]);
            $this->load->view('Graphs/CompanyWithSameStatusSinceFunnleAnalysis', ['uid' => $uid, 'user' => $user, 'sdate' => $sdate, 'edate' => $edate, 'SelectedStatus' => $SelectedStatus, 'status' => $status, 'SelectedpartnerType' => $SelectedpartnerType, 'partner_type' => $partner_type, 'SelectedCluster' => $SelectedCluster, 'clusters' => $get_cluster, 'SelectedCategory' => $SelectedCategory, 'SelectedUsers' => $SelectedUsers, 'TableData' => $TableData, 'userTypeid' => $userTypeid, 'GraphData' => $GraphData,'roles'=>$roles]);
            $this->load->view('include/footer');
        } else {
            redirect('Menu/main');
        }
    }

    public function SameStatusSinceFunnelGraphData()
    {

        $selectedStatus = isset($_POST['selectedStatus']) ? $_POST['selectedStatus'] : null;
        $sdate = isset($_POST['sdate']) ? $_POST['sdate'] : null;
        $edate = isset($_POST['edate']) ? $_POST['edate'] : null;
        $selected_partnerType = isset($_POST['selected_partnerType']) ? $_POST['selected_partnerType'] : null;
        $selected_userType = isset($_POST['selected_userType']) ? $_POST['selected_userType'] : null;
        $selected_cluster = isset($_POST['selected_cluster']) ? $_POST['selected_cluster'] : null;
        $selected_users = isset($_POST['selected_users']) ? $_POST['selected_users'] : null;
        $selected_category = isset($_POST['selected_category']) ? $_POST['selected_category'] : null;

        $selected_partnerType = (array) json_decode($selected_partnerType);
        $selected_userType = (array) json_decode($selected_userType);
        $selected_cluster = (array) json_decode($selected_cluster);
        $selected_users = (array) json_decode($selected_users);
        $selected_category = (array) json_decode($selected_category);

        $user = $this->session->userdata('user');
        $data['user'] = $user;
        $uid = $user['user_id'];
        $userTypeid = $user['type_id'];
        $dt = $this->Graph_Model->get_utype($userTypeid);
        $dep_name = $dt[0]->name;

        $TableData = $this->Graph_Model->getTableDataOfCompanyWithSameStatus($uid, $userTypeid, $selectedStatus, $sdate, $edate, $selected_partnerType, $selected_category, $selected_userType, $selected_users, $selected_cluster);

        if (!empty($user)) {

            $this->load->view('include/header');
            $this->load->view($dep_name . '/nav', ['uid' => $uid, 'user' => $user]);
            $this->load->view('Graphs/DataOfCompanyWithSameStatus', ['uid' => $uid, 'user' => $user, 'sdate' => $sdate, 'edate' => $edate, 'TableData' => $TableData]);
            $this->load->view('include/footer');
        } else {

            redirect('Menu/main');
        }
    }

    public function StageWiseFunnleAnalysis()
    {
        
        // var_dump($_POST);die;

        if (isset($_POST['userType'])) {

            $userType = array_filter($_POST['userType'], function ($value) {
                return $value !== 'select_all';
            });
            // $userType = implode(',', ($userType));

        } else {
            $userType = [];
        }

        if (isset($_POST['startDate']) && isset($_POST['endDate'])) {

            $sdate = $_POST['startDate'];
            $edate = $_POST['endDate'];
        } else {

            $sdate = date('Y-m-d');
            $edate = date('Y-m-d');
        }

        if (isset($_POST['status'])) {

            $SelectedStatus = array_filter($_POST['status'], function ($value) {
                return $value !== 'select_all';
            });
        } else {

            $SelectedStatus = [];
        }

        if (isset($_POST['partnerType'])) {

            $SelectedpartnerType = array_filter($_POST['partnerType'], function ($value) {
                return $value !== 'select_all';
            });
        } else {

            $SelectedpartnerType = [];
        }

        if (isset($_POST['cluster'])) {

            $SelectedCluster = array_filter($_POST['cluster'], function ($value) {
                return $value !== 'select_all';
            });
        } else {

            $SelectedCluster = [];
        }

        if (isset($_POST['category'])) {

            $SelectedCategory = array_filter($_POST['category'], function ($value) {
                return $value !== 'select_all';
            });
        } else {

            $SelectedCategory = [];
        }

        if (isset($_POST['user'])) {

            $SelectedUsers = array_filter($_POST['user'], function ($value) {
                return $value !== 'select_all';
            });
        } else {

            $SelectedUsers = [];
        }

        $partner_type = $this->Graph_Model->getPartnerType();
        // var_dump($category);die;
        $user = $this->session->userdata('user');
        $data['user'] = $user;
        $uid = $user['user_id'];
        $userTypeid = $user['type_id'];
        $dt = $this->Graph_Model->get_utype($userTypeid);
        $dep_name = $dt[0]->name;

        $roles = $this->Graph_Model->getRoles($dt[0]->id);
        
        $get_cluster = $this->Graph_Model->get_clusters();
        $status = $this->Graph_Model->getStatus();


        $FunnelData = $this->Graph_Model->getStageWiseFunnelGraphDetails($uid, $userTypeid, $sdate, $edate, $SelectedStatus, $SelectedCluster, $SelectedCategory, $SelectedUsers, $SelectedpartnerType,$userType);
        $TableData = $this->Graph_Model->getStageWiseFunnelTableDetails($uid, $userTypeid, $sdate, $edate, $SelectedStatus, $SelectedCluster, $SelectedCategory, $SelectedUsers, $SelectedpartnerType,$userType);

        // $GraphData = '';
        // $TableData = '';
        // var_dump($FunnelData);die;

        if (!empty($user)) {
            $this->load->view('include/header');
            $this->load->view($dep_name . '/nav', ['uid' => $uid, 'user' => $user]);
            $this->load->view('Graphs/StageWiseFunnelAnalysis', ['uid' => $uid, 'user' => $user, 'sdate' => $sdate, 'edate' => $edate, 'SelectedStatus' => $SelectedStatus, 'status' => $status, 'SelectedpartnerType' => $SelectedpartnerType, 'partner_type' => $partner_type, 'SelectedCluster' => $SelectedCluster, 'clusters' => $get_cluster, 'SelectedCategory' => $SelectedCategory, 'SelectedUsers' => $SelectedUsers, 'TableData' => $TableData, 'userTypeid' => $userTypeid, 'FunnelData' => $FunnelData,'roles' => $roles,'userType'=>$userType]);
            $this->load->view('include/footer');
        } else {
            redirect('Menu/main');
        }
    }

    public function StageWiseFunnelData()
    {

        // var_dump($_POST);die;
        $selectedStatus = isset($_POST['selectedStatus']) ? $_POST['selectedStatus'] : null;
        $sdate = isset($_POST['sdate']) ? $_POST['sdate'] : null;
        $edate = isset($_POST['edate']) ? $_POST['edate'] : null;
        $selected_partnerType = isset($_POST['selected_partnerType']) ? $_POST['selected_partnerType'] : null;
        // $selectedStatus = isset($_POST['selectedStatus']) ? $_POST['selectedStatus'] : null;
        $selected_cluster = isset($_POST['selected_cluster']) ? $_POST['selected_cluster'] : null;
        $selected_users = isset($_POST['selected_users']) ? $_POST['selected_users'] : null;
        $selected_category = isset($_POST['selected_category']) ? $_POST['selected_category'] : null;

        $selected_partnerType = (array) json_decode($selected_partnerType);
        $selectedStatus = (array) json_decode($selectedStatus);
        $selected_cluster = (array) json_decode($selected_cluster);
        $selected_users = (array) json_decode($selected_users);
        $selected_category = (array) json_decode($selected_category);

        $user = $this->session->userdata('user');
        $data['user'] = $user;
        $uid = $user['user_id'];
        $userTypeid = $user['type_id'];
        $dt = $this->Graph_Model->get_utype($userTypeid);
        $dep_name = $dt[0]->name;

        $TableData = $this->Graph_Model->getTableDataStageWiseFunnel($uid, $userTypeid, $selectedStatus, $sdate, $edate, $selected_partnerType, $selected_category, $selected_users, $selected_cluster);

        // var_dump($TableData);die;
        if (!empty($user)) {

            $this->load->view('include/header');
            $this->load->view($dep_name . '/nav', ['uid' => $uid, 'user' => $user]);
            $this->load->view('Graphs/StageWiseFunnelData', ['uid' => $uid, 'user' => $user, 'sdate' => $sdate, 'edate' => $edate, 'TableData' => $TableData]);
            $this->load->view('include/footer');
        } else {

            redirect('Menu/main');
        }
    }

    public function StatusWiseTaskAnalysis()
    {
// echo "test";exit;
        if (isset($_POST['startDate']) && isset($_POST['endDate'])) {

            $sdate = $_POST['startDate'];
            $edate = $_POST['endDate'];
        } else {
            $sdate = date('Y-m-d');
            $edate = date('Y-m-d');
        }

        if (isset($_POST['userType'])) {

            $userType = array_filter($_POST['userType'], function ($value) {
                return $value !== 'select_all';
            });

        } else {

            $userType = [];
        }

        if (isset($_POST['cluster'])) {

            $cluster = array_filter($_POST['cluster'], function ($value) {
                return $value !== 'select_all';
            });

        } else {

            $cluster = [];
        }

        if (isset($_POST['user'])) {

            $users = array_filter($_POST['user'], function ($value) {
                return $value !== 'select_all';
            });

        } else {

            $users = [];
        }


        if (isset($_POST['partnerType'])) {

            $partnerType = array_filter($_POST['partnerType'], function ($value) {
                return $value !== 'select_all';
            });

        } else {

            $partnerType = [];
        }

        if (isset($_POST['category'])) {

            $category = array_filter($_POST['category'], function ($value) {
                return $value !== 'select_all';
            });

        } else {

            $category = [];
        }

        // $sdate = '2024-03-01';
        // var_dump($sdate,$edate);die;
        $user = $this->session->userdata('user');
        $data['user'] = $user;
        $uid = $user['user_id'];
        $userTypeid = $user['type_id'];
        $dt = $this->Graph_Model->get_utype($userTypeid);
        $dep_name = $dt[0]->name;

        $roles = $this->Graph_Model->getRoles($dt[0]->id);

        $partner_type = $this->Graph_Model->getPartnerType();

        $get_cluster = $this->Graph_Model->get_clusters();

        $get_status = $this->Graph_Model->getStatus();

        $TableData = $this->Graph_Model->StatusWiseTaskTableDetails($uid, $userTypeid, $sdate, $edate, $userType, $cluster, $partnerType, $category, $users);
       
        $FunnelData = [];
        // $TableData = '';
        if (!empty($user)) {

            $this->load->view('include/header');
            $this->load->view($dep_name . '/nav', ['uid' => $uid, 'user' => $user]);
            $this->load->view('Graphs/StatusWiseTaskAnalysis', ['uid' => $uid, 'user' => $user, 'sdate' => $sdate, 'edate' => $edate, 'TableData' => $TableData, 'userTypeid' => $userTypeid, 'roles' => $roles, 'partner_type' => $partner_type, 'clusters' => $get_cluster, 'FunnelData' => $FunnelData, 'selected_partnerType' => $partnerType, 'selected_category' => $category, 'selected_cluster' => $cluster, 'selected_users' => $users, 'Selected_userType' => $userType, 'getStatus' => $get_status]);
            $this->load->view('include/footer');
        } else {

            redirect('Menu/main');
        }
    }

    public function StatusWiseTaskFunnelGraphData()
    {

        var_dump($_POST);
        die;
    }

    // public function MonthWiseFunnelAnalysis(){


    //     if (isset($_POST['startDate']) && isset($_POST['endDate'])) {

    //         $sdate = $_POST['startDate'];
    //         $edate = $_POST['endDate'];
    //     } else {
    //         $sdate = date('Y-m-d');
    //         $edate = date('Y-m-d');
    //     }

    //     if (isset($_POST['userType'])) {

    //         $userType = array_filter($_POST['userType'], function ($value) {
    //             return $value !== 'select_all';
    //         });

    //         // $userType = implode(',', ($userType));

    //     } else {

    //         $userType = [];
    //     }

    //     if (isset($_POST['cluster'])) {

    //         $cluster = array_filter($_POST['cluster'], function ($value) {
    //             return $value !== 'select_all';
    //         });

    //         $cluster = implode(',', ($cluster));
    //     } else {

    //         $cluster = [];
    //     }

    //     if (isset($_POST['user'])) {

    //         $users = array_filter($_POST['user'], function ($value) {
    //             return $value !== 'select_all';
    //         });

    //         // $users = implode(',', ($users));
    //     } else {

    //         $users = [];
    //     }


    //     if (isset($_POST['partnerType'])) {

    //         $partnerType = array_filter($_POST['partnerType'], function ($value) {
    //             return $value !== 'select_all';
    //         });
    //         // $partnerType = implode(',', ($partnerType));

    //     } else {

    //         $partnerType = [];
    //     }

    //     if (isset($_POST['category'])) {

    //         $category = array_filter($_POST['category'], function ($value) {
    //             return $value !== 'select_all';
    //         });
    //         // $category = implode(',', ($category));

    //     } else {

    //         $category = [];
    //     }

    //     // $sdate = '2024-03-01';
    //     // var_dump($sdate,$edate);die;
    //     $user = $this->session->userdata('user');
    //     $data['user'] = $user;
    //     $uid = $user['user_id'];
    //     $userTypeid = $user['type_id'];
    //     $dt = $this->Graph_Model->get_utype($userTypeid);
    //     $dep_name = $dt[0]->name;

    //     $roles = $this->Graph_Model->getRoles($dt[0]->id);

    //     $partner_type = $this->Graph_Model->getPartnerType();

    //     $get_cluster = $this->Graph_Model->get_clusters();

    //     $get_status = $this->Graph_Model->getStatus();

    //     $TableData = $this->Graph_Model->StatusWiseTaskTableDetails($uid, $userTypeid, $sdate, $edate, $userType, $cluster, $partnerType, $category, $users);

    //     // $FunnelData = $this->Graph_Model->getGraphDetails($uid, $userTypeid, $sdate, $edate, $userType, $cluster, $partnerType, $category, $users);
    //     // var_dump($TableData);die;

    //     $FunnelData = '';
    //     if (!empty($user)) {

    //         $this->load->view('include/header');
    //         $this->load->view($dep_name . '/nav', ['uid' => $uid, 'user' => $user]);
    //         $this->load->view('Graphs/MonthWiseFunnelAnalysis', ['uid' => $uid, 'user' => $user, 'sdate' => $sdate, 'edate' => $edate, 'TableData' => $TableData,'userTypeid'=>$userTypeid ,'roles' => $roles, 'partner_type' => $partner_type, 'clusters' => $get_cluster, 'FunnelData' => $FunnelData, 'selected_partnerType' => $partnerType, 'selected_category' => $category, 'selected_cluster' => $cluster, 'selected_users' => $users, 'Selected_userType' => $userType,'getStatus'=>$get_status]);
    //         $this->load->view('include/footer');

    //     } else {

    //         redirect('Menu/main');
    //     }
    // }

    public function ActionWiseFunnelAnalysis()
    {

        if (isset($_POST['startDate']) && isset($_POST['endDate'])) {

            $sdate = $_POST['startDate'];
            $edate = $_POST['endDate'];
        } else {
            $sdate = date('Y-m-d');
            $edate = date('Y-m-d');
        }

        if (isset($_POST['userType'])) {

            $userType = array_filter($_POST['userType'], function ($value) {
                return $value !== 'select_all';
            });

            // $userType = implode(',', ($userType));

        } else {

            $userType = [];
        }

        if (isset($_POST['cluster'])) {

            $cluster = array_filter($_POST['cluster'], function ($value) {
                return $value !== 'select_all';
            });

            $cluster = implode(',', ($cluster));
        } else {

            $cluster = [];
        }

        if (isset($_POST['user'])) {

            $users = array_filter($_POST['user'], function ($value) {
                return $value !== 'select_all';
            });

            // $users = implode(',', ($users));
        } else {

            $users = [];
        }


        if (isset($_POST['partnerType'])) {

            $partnerType = array_filter($_POST['partnerType'], function ($value) {
                return $value !== 'select_all';
            });
            // $partnerType = implode(',', ($partnerType));

        } else {

            $partnerType = [];
        }

        if (isset($_POST['category'])) {

            $category = array_filter($_POST['category'], function ($value) {
                return $value !== 'select_all';
            });
            // $category = implode(',', ($category));

        } else {

            $category = [];
        }


        if (isset($_POST['action'])) {

            $action = array_filter($_POST['action'], function ($value) {
                return $value !== 'select_all';
            });
            // $category = implode(',', ($category));

        } else {

            $action = [];
        }
        // $Selected_action
        // $sdate = '2024-03-01';
        // var_dump($sdate,$edate);die;
        $user = $this->session->userdata('user');
        $data['user'] = $user;
        $uid = $user['user_id'];
        $userTypeid = $user['type_id'];
        $dt = $this->Graph_Model->get_utype($userTypeid);
        $dep_name = $dt[0]->name;

        $roles = $this->Graph_Model->getRoles($dt[0]->id);

        $partner_type = $this->Graph_Model->getPartnerType();

        $get_cluster = $this->Graph_Model->get_clusters();

        $get_status = $this->Graph_Model->getStatus();

        $get_action = $this->Graph_Model->getAction();

        $TableData = $this->Graph_Model->ActionWiseTableDetails($uid, $sdate, $edate, $category, $partnerType, $userType, $cluster, $users,  $userTypeid, $action);

        // public function ActionWiseTableDetails($uid,$sdate,$edate,$selected_category,$selected_partnerType,$selected_userType,$selected_cluster,$selected_users,$userTypeid,$action)
        // $FunnelData = $this->Graph_Model->getGraphDetails($uid, $userTypeid, $sdate, $edate, $userType, $cluster, $partnerType, $category, $users);
        // var_dump($TableData);die;

        $FunnelData = '';
        if (!empty($user)) {

            $this->load->view('include/header');
            $this->load->view($dep_name . '/nav', ['uid' => $uid, 'user' => $user]);
            $this->load->view('Graphs/ActionWiseFunnelAnalysis', ['uid' => $uid, 'user' => $user, 'sdate' => $sdate, 'edate' => $edate, 'TableData' => $TableData, 'userTypeid' => $userTypeid, 'roles' => $roles, 'partner_type' => $partner_type, 'clusters' => $get_cluster, 'get_action' => $get_action, 'FunnelData' => $FunnelData, 'selected_partnerType' => $partnerType, 'selected_category' => $category, 'selected_cluster' => $cluster, 'Selected_action' => $action, 'selected_users' => $users, 'Selected_userType' => $userType, 'getStatus' => $get_status]);
            $this->load->view('include/footer');
        } else {

            redirect('Menu/main');
        }
    }

    public function OtherUserOnFunnelAnalysis()
    {   
        // var_dump($_POST);die;

        if (isset($_POST['startDate']) && isset($_POST['endDate'])) {

            $sdate = $_POST['startDate'];
            $edate = $_POST['endDate'];
        } else {
            $sdate = date('Y-m-d');
            $edate = date('Y-m-d');
        }

        if (isset($_POST['userType'])) {

            $userType = array_filter($_POST['userType'], function ($value) {
                return $value !== 'select_all';
            });

            // $userType = implode(',', ($userType));

        } else {

            $userType = [];
        }

        if (isset($_POST['cluster'])) {

            $cluster = array_filter($_POST['cluster'], function ($value) {
                return $value !== 'select_all';
            });

            $cluster = implode(',', ($cluster));
        } else {

            $cluster = [];
        }

        if (isset($_POST['user'])) {

            $users = array_filter($_POST['user'], function ($value) {
                return $value !== 'select_all';
            });

            // $users = implode(',', ($users));
        } else {

            $users = [];
        }

        if (isset($_POST['partnerType'])) {

            $partnerType = array_filter($_POST['partnerType'], function ($value) {
                return $value !== 'select_all';
            });
            // $partnerType = implode(',', ($partnerType));

        } else {

            $partnerType = [];
        }

        if (isset($_POST['category'])) {

            $category = array_filter($_POST['category'], function ($value) {
                return $value !== 'select_all';
            });
            // $category = implode(',', ($category));

        } else {

            $category = [];
        }

        if (isset($_POST['status'])) {

            $status = $_POST['status'];
           
            // $category = implode(',', ($category));

        } else {

            $status = '';
        }

        $actionType = isset($_POST['action']) ? $_POST['action'] : null;
        $purposeType = isset($_POST['purpose']) ? $_POST['purpose'] : null;

        if ($status == '1') {

            $actionType = 'yes';
            $purposeType = 'yes';

        }

        // var_dump($purposeType);die;
        // $actionType = $this->input->post('action');
        // $purposeType = $this->input->post('purpose');

        $user = $this->session->userdata('user');
        $data['user'] = $user;
        $uid = $user['user_id'];
        $userTypeid = $user['type_id'];
        $dt = $this->Graph_Model->get_utype($userTypeid);
        $dep_name = $dt[0]->name;

        $roles = $this->Graph_Model->getRoles($dt[0]->id);

        $partner_type = $this->Graph_Model->getPartnerType();

        $get_cluster = $this->Graph_Model->get_clusters();

        $get_status = $this->Graph_Model->getStatus();

        $get_action = $this->Graph_Model->getAction();

        $currentDate = new DateTime();
        $month = date('m');

        $financialYear = ($currentDate->format('m') >= 4) ? $currentDate->format('Y') : ($currentDate->format('Y') - 1);

        $TableData = $this->Graph_Model->APTableDetails($uid,$sdate,$edate,$category,$partnerType,$userType,$users,$userTypeid,$actionType,$purposeType,$status);

        $FunnelData = '';

        if (!empty($user)) {

            $this->load->view('include/header');
            $this->load->view($dep_name . '/nav', ['uid' => $uid, 'user' => $user]);
            $this->load->view('Graphs/OtherUserOnMyFunnelAnalysis', ['uid' => $uid, 'user' => $user, 'sdate' => $sdate, 'edate' => $edate, 'TableData' => $TableData, 'userTypeid' => $userTypeid, 'roles' => $roles, 'partner_type' => $partner_type, 'clusters' => $get_cluster, 'get_action' => $get_action, 'FunnelData' => $FunnelData, 'selected_partnerType' => $partnerType, 'selected_category' => $category, 'selected_cluster' => $cluster, 'Selected_action' => $actionType, 'Selected_purpose' => $purposeType, 'selected_users' => $users, 'Selected_userType' => $userType, 'getStatus' => $get_status]);
            $this->load->view('include/footer');
        } else {

            redirect('Menu/main');
        }
    }

    public function TimeSlotWiseTaskAnalysis(){


        if (isset($_POST['startDate']) && isset($_POST['endDate'])) {

            $sdate = $_POST['startDate'];
            $edate = $_POST['endDate'];
        } else {
            $sdate = date('Y-m-d');
            $edate = date('Y-m-d');
        }

        if (isset($_POST['userType'])) {

            $userType = array_filter($_POST['userType'], function ($value) {
                return $value !== 'select_all';
            });

        } else {

            $userType = [];
        }

        if (isset($_POST['cluster'])) {

            $cluster = array_filter($_POST['cluster'], function ($value) {
                return $value !== 'select_all';
            });

            $cluster = implode(',', ($cluster));
        } else {

            $cluster = [];
        }

        if (isset($_POST['user'])) {

            $users = array_filter($_POST['user'], function ($value) {
                return $value !== 'select_all';
            });

        } else {

            $users = [];
        }

        if (isset($_POST['partnerType'])) {

            $partnerType = array_filter($_POST['partnerType'], function ($value) {
                return $value !== 'select_all';
            });

        } else {

            $partnerType = [];
        }

        if (isset($_POST['category'])) {

            $category = array_filter($_POST['category'], function ($value) {
                return $value !== 'select_all';
            });
        } else {

            $category = [];
        }


        $user = $this->session->userdata('user');
        $data['user'] = $user;
        $uid = $user['user_id'];
        $userTypeid = $user['type_id'];
        $dt = $this->Graph_Model->get_utype($userTypeid);
        $dep_name = $dt[0]->name;


        $roles = $this->Graph_Model->getRoles($dt[0]->id);

        $partner_type = $this->Graph_Model->getPartnerType();

        $get_cluster = $this->Graph_Model->get_clusters();

        $TableData =  $FunnelData = [];
        
        $TableData =  $this->Graph_Model->getTimeSlotWiseTaskTable($users,$userType,$sdate,$edate,$partnerType,$category);
        // var_dump($TableData);die;
        if (!empty($user)) {

            $this->load->view('include/header');
            $this->load->view($dep_name . '/nav', ['uid' => $uid, 'user' => $user]);
            $this->load->view('Graphs/TimeWiseTaskGraph', ['uid' => $uid, 'user' => $user, 'sdate' => $sdate, 'edate' => $edate, 'TableData' => $TableData, 'userTypeid' => $userTypeid, 'roles' => $roles, 'partner_type' => $partner_type, 'clusters' => $get_cluster, 'FunnelData' => $FunnelData, 'selected_partnerType' => $partnerType, 'selected_category' => $category, 'selected_cluster' => $cluster,  'selected_users' => $users, 'Selected_userType' => $userType, ]);
            $this->load->view('include/footer');
        } else {

            redirect('Menu/main');
        }
    }

    public function ActionWiseTaskConversion(){


        if (isset($_POST['startDate']) && isset($_POST['endDate'])) {

            $sdate = $_POST['startDate'];
            $edate = $_POST['endDate'];
        } else {
            $sdate = date('Y-m-d');
            $edate = date('Y-m-d');
        }

        if (isset($_POST['userType'])) {

            $userType = array_filter($_POST['userType'], function ($value) {
                return $value !== 'select_all';
            });

        } else {

            $userType = [];
        }

        if (isset($_POST['cluster'])) {

            $cluster = array_filter($_POST['cluster'], function ($value) {
                return $value !== 'select_all';
            });

            $cluster = implode(',', ($cluster));
        } else {

            $cluster = [];
        }

        if (isset($_POST['user'])) {

            $users = array_filter($_POST['user'], function ($value) {
                return $value !== 'select_all';
            });

        } else {

            $users = [];
        }

        if (isset($_POST['partnerType'])) {

            $partnerType = array_filter($_POST['partnerType'], function ($value) {
                return $value !== 'select_all';
            });

        } else {

            $partnerType = [];
        }

        if (isset($_POST['category'])) {

            $category = array_filter($_POST['category'], function ($value) {
                return $value !== 'select_all';
            });
        } else {

            $category = [];
        }


        $user = $this->session->userdata('user');
        $data['user'] = $user;
        $uid = $user['user_id'];
        $userTypeid = $user['type_id'];
        $dt = $this->Graph_Model->get_utype($userTypeid);
        $dep_name = $dt[0]->name;


        $roles = $this->Graph_Model->getRoles($dt[0]->id);

        $partner_type = $this->Graph_Model->getPartnerType();

        $get_cluster = $this->Graph_Model->get_clusters();

        $TableData =  $FunnelData = [];
        
        $TableData =  $this->Graph_Model->getActionWiseTaskConversionTable($users,$userType,$sdate,$edate,$partnerType,$category);
        // var_dump($TableData);die;
        if (!empty($user)) {

            $this->load->view('include/header');
            $this->load->view($dep_name . '/nav', ['uid' => $uid, 'user' => $user]);
            $this->load->view('Graphs/ActionWiseTaskConversion', ['uid' => $uid, 'user' => $user, 'sdate' => $sdate, 'edate' => $edate, 'TableData' => $TableData, 'userTypeid' => $userTypeid, 'roles' => $roles, 'partner_type' => $partner_type, 'clusters' => $get_cluster, 'FunnelData' => $FunnelData, 'selected_partnerType' => $partnerType, 'selected_category' => $category, 'selected_cluster' => $cluster,  'selected_users' => $users, 'Selected_userType' => $userType, ]);
            $this->load->view('include/footer');
        } else {

            redirect('Menu/main');
        }
    }

    public function StatusWiseTaskConversion(){


        if (isset($_POST['startDate']) && isset($_POST['endDate'])) {

            $sdate = $_POST['startDate'];
            $edate = $_POST['endDate'];
        } else {
            $sdate = date('Y-m-d');
            $edate = date('Y-m-d');
        }

        if (isset($_POST['userType'])) {

            $userType = array_filter($_POST['userType'], function ($value) {
                return $value !== 'select_all';
            });

        } else {

            $userType = [];
        }

        if (isset($_POST['cluster'])) {

            $cluster = array_filter($_POST['cluster'], function ($value) {
                return $value !== 'select_all';
            });

            $cluster = implode(',', ($cluster));
        } else {

            $cluster = [];
        }

        if (isset($_POST['user'])) {

            $users = array_filter($_POST['user'], function ($value) {
                return $value !== 'select_all';
            });

        } else {

            $users = [];
        }

        if (isset($_POST['partnerType'])) {

            $partnerType = array_filter($_POST['partnerType'], function ($value) {
                return $value !== 'select_all';
            });

        } else {

            $partnerType = [];
        }

        if (isset($_POST['category'])) {

            $category = array_filter($_POST['category'], function ($value) {
                return $value !== 'select_all';
            });
        } else {

            $category = [];
        }


        $user = $this->session->userdata('user');
        $data['user'] = $user;
        $uid = $user['user_id'];
        $userTypeid = $user['type_id'];
        $dt = $this->Graph_Model->get_utype($userTypeid);
        $dep_name = $dt[0]->name;


        $roles = $this->Graph_Model->getRoles($dt[0]->id);

        $partner_type = $this->Graph_Model->getPartnerType();

        $get_cluster = $this->Graph_Model->get_clusters();

        $TableData =  $FunnelData = [];
        
        $TableData =  $this->Graph_Model->getActionWiseTaskConversionTable($users,$userType,$sdate,$edate,$partnerType,$category);
        // var_dump($TableData);die;
        if (!empty($user)) {

            $this->load->view('include/header');
            $this->load->view($dep_name . '/nav', ['uid' => $uid, 'user' => $user]);
            $this->load->view('Graphs/StatusWiseTaskConversionAnalysis', ['uid' => $uid, 'user' => $user, 'sdate' => $sdate, 'edate' => $edate, 'TableData' => $TableData, 'userTypeid' => $userTypeid, 'roles' => $roles, 'partner_type' => $partner_type, 'clusters' => $get_cluster, 'FunnelData' => $FunnelData, 'selected_partnerType' => $partnerType, 'selected_category' => $category, 'selected_cluster' => $cluster,  'selected_users' => $users, 'Selected_userType' => $userType, ]);
            $this->load->view('include/footer');
        } else {

            redirect('Menu/main');
        }
    }

    public function TaskWiseDetailAnalysis(){


        if (isset($_POST['startDate']) && isset($_POST['endDate'])) {

            $sdate = $_POST['startDate'];
            $edate = $_POST['endDate'];
        } else {
            $sdate = date('Y-m-d');
            $edate = date('Y-m-d');
        }

        if (isset($_POST['userType'])) {

            $userType = array_filter($_POST['userType'], function ($value) {
                return $value !== 'select_all';
            });

        } else {

            $userType = [];
        }

        if (isset($_POST['cluster'])) {

            $cluster = array_filter($_POST['cluster'], function ($value) {
                return $value !== 'select_all';
            });

            $cluster = implode(',', ($cluster));
        } else {

            $cluster = [];
        }

        if (isset($_POST['user'])) {

            $users = array_filter($_POST['user'], function ($value) {
                return $value !== 'select_all';
            });

        } else {

            $users = [];
        }

        if (isset($_POST['partnerType'])) {

            $partnerType = array_filter($_POST['partnerType'], function ($value) {
                return $value !== 'select_all';
            });

        } else {

            $partnerType = [];
        }

        if (isset($_POST['category'])) {

            $category = array_filter($_POST['category'], function ($value) {
                return $value !== 'select_all';
            });
        } else {

            $category = [];
        }

        if (isset($_POST['actionType'])) {

            $selectedAction = $_POST['actionType'];
            $getActionName = $this->Graph_Model->getActionByID($selectedAction);

        } else {

            $selectedAction = '';
            $getActionName = '';
        }   

        $user = $this->session->userdata('user');
        $data['user'] = $user;
        $uid = $user['user_id'];
        $userTypeid = $user['type_id'];
        $dt = $this->Graph_Model->get_utype($userTypeid);
        $dep_name = $dt[0]->name;


        $roles = $this->Graph_Model->getRoles($dt[0]->id);

        $partner_type = $this->Graph_Model->getPartnerType();

        $get_cluster = $this->Graph_Model->get_clusters();

        $TableData =  $FunnelData = [];
        
        $actionTypes = $this->Menu_model->getAction();

        $TableData =  $this->Graph_Model->getActionWiseTaskTable($users,$userType,$sdate,$edate,$partnerType,$category,$selectedAction);
        // 
	if($uid == 100205){
//	var_dump($selectedAction);
// echo "<br>";
		//var_dump($TableData);die;
	}
        // $get
        if (!empty($user)) {

            $this->load->view('include/header');
            $this->load->view($dep_name . '/nav', ['uid' => $uid, 'user' => $user]);
            $this->load->view('Graphs/TaskWiseDetailAnalysis', ['uid' => $uid, 'user' => $user, 'sdate' => $sdate, 'edate' => $edate, 'TableData' => $TableData, 'userTypeid' => $userTypeid, 'roles' => $roles, 'partner_type' => $partner_type, 'clusters' => $get_cluster, 'FunnelData' => $FunnelData, 'selected_partnerType' => $partnerType, 'selected_category' => $category, 'selected_cluster' => $cluster,  'selected_users' => $users, 'Selected_userType' => $userType,'actionTypes'=>$actionTypes,'selectedAction'=>$selectedAction ,'getActionName'=>$getActionName]);
            $this->load->view('include/footer');
        } else {

            redirect('Menu/main');
        }
    }

    public function getTaskDetails(){
        // var_dump($_POST);die;

        $selectedaction = isset($_POST['action']) ? $_POST['action'] : null;
        $sdate = isset($_POST['sdate']) ? $_POST['sdate'] : null;
        $edate = isset($_POST['edate']) ? $_POST['edate'] : null;
        $actionAP = isset($_POST['actionAP']) ? $_POST['actionAP'] : null;

        $selected_partnerType = isset($_POST['partnerType']) ? $_POST['partnerType'] : null;
        // $selectedStatus = isset($_POST['selectedStatus']) ? $_POST['selectedStatus'] : null;
        // $selected_cluster = isset($_POST['selected_cluster']) ? $_POST['selected_cluster'] : null;
        $selected_users = isset($_POST['users']) ? $_POST['users'] : null;
        $selected_category = isset($_POST['category']) ? $_POST['category'] : null;
        $selected_userType = isset($_POST['userType']) ? $_POST['userType'] : null;


        // var_dump($selected_users);die;
        $selected_partnerType = (array) json_decode($selected_partnerType);
        // $selectedStatus = (array) json_decode($selectedStatus);
        // $selected_cluster = (array) json_decode($selected_cluster);
        $selected_users = (array) json_decode($selected_users);
        $selected_category = (array) json_decode($selected_category);
        $selected_userType = (array) json_decode($selected_userType);

        

        $user = $this->session->userdata('user');
        $data['user'] = $user;
        $uid = $user['user_id'];
        $userTypeid = $user['type_id'];
        $dt = $this->Graph_Model->get_utype($userTypeid);
        $dep_name = $dt[0]->name;

        $TableData = $this->Graph_Model->getTableDataTaskWiseDetail($selectedaction, $sdate, $edate, $selected_users, $selected_category, $selected_partnerType,$selected_userType,$actionAP);

        // var_dump($TableData);die;
        if (!empty($user)) {

            $this->load->view('include/header');
            $this->load->view($dep_name . '/nav', ['uid' => $uid, 'user' => $user]);
            $this->load->view('Graphs/TaskDetailsTableData', ['uid' => $uid, 'user' => $user, 'sdate' => $sdate, 'edate' => $edate, 'TableData' => $TableData]);
            $this->load->view('include/footer');
        } else {

            redirect('Menu/main');
        }

    }

    public function BDRequest(){

        // var_dump($_POST);die;
        if (isset($_POST['startDate']) && isset($_POST['endDate'])) {

            $sdate = $_POST['startDate'];
            $edate = $_POST['endDate'];
        } else {
            $sdate = date('Y-m-d');
            $edate = date('Y-m-d');
        }

        if (isset($_POST['userType'])) {

            $userType = array_filter($_POST['userType'], function ($value) {
                return $value !== 'select_all';
            });

        } else {

            $userType = [];
        }

        if (isset($_POST['user'])) {

            $users = array_filter($_POST['user'], function ($value) {
                return $value !== 'select_all';
            });

        } else {

            $users = [];
        }
  

        $user = $this->session->userdata('user');
        $data['user'] = $user;
        $uid = $user['user_id'];
        $userTypeid = $user['type_id'];
        $dt = $this->Graph_Model->get_utype($userTypeid);
        $dep_name = $dt[0]->name;


        $roles = $this->Graph_Model->getRoles($dt[0]->id);

        $TableData =  $this->Graph_Model->get_bdRequestTableData($users,$sdate,$edate);
        // var_dump($selectedAction);die;

        // $get
        if (!empty($user)) {

            $this->load->view('include/header');
            $this->load->view($dep_name . '/nav', ['uid' => $uid, 'user' => $user]);
            $this->load->view('Graphs/BDRequest', ['uid' => $uid, 'user' => $user, 'sdate' => $sdate, 'edate' => $edate, 'TableData' => $TableData, 'userTypeid' => $userTypeid, 'roles' => $roles,  'selected_users' => $users, 'Selected_userType' => $userType,]);
            $this->load->view('include/footer');
        } else {

            redirect('Menu/main');
        }
    }

    public function PendingRIDDayWise(){

        // var_dump($_POST);die;
        if (isset($_POST['startDate']) && isset($_POST['endDate'])) {

            $sdate = $_POST['startDate'];
            $edate = $_POST['endDate'];
        } else {
            $sdate = date('Y-m-d');
            $edate = date('Y-m-d');
        }

        if (isset($_POST['userType'])) {

            $userType = array_filter($_POST['userType'], function ($value) {
                return $value !== 'select_all';});

        }else{

            $userType = [];
        }
        
        if (isset($_POST['user'])) {

            $users = array_filter($_POST['user'], function ($value) {
                return $value !== 'select_all';});

        }else{
            $users = [];
        }
        
        if (isset($_POST['status'])) {

            $status = $_POST['status'];

        } else {
            $status = '';
        }

        $user = $this->session->userdata('user');
        $data['user'] = $user;
        $uid = $user['user_id'];
        $userTypeid = $user['type_id'];
        $dt = $this->Graph_Model->get_utype($userTypeid);
        $dep_name = $dt[0]->name;


        $roles = $this->Graph_Model->getRoles($dt[0]->id);

        // $TableData =  $this->Graph_Model->get_bdRequestTableData($users,$sdate,$edate);
        // var_dump($selectedAction);die;
        $TableData = [];
        // $get
        if (!empty($user)) {

            $this->load->view('include/header');
            $this->load->view($dep_name . '/nav', ['uid' => $uid, 'user' => $user]);
            $this->load->view('Graphs/PendingRIDDayWise', ['uid' => $uid, 'user' => $user, 'sdate' => $sdate, 'edate' => $edate, 'TableData' => $TableData, 'userTypeid' => $userTypeid, 'roles' => $roles,  'selected_users' => $users, 'Selected_userType' => $userType,'status'=>$status]);
            $this->load->view('include/footer');
            
        } else {

            redirect('Menu/main');
        }
    }
}