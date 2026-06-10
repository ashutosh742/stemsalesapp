<?php
date_default_timezone_set("Asia/Calcutta");
defined('BASEPATH') or exit('No direct script access allowed');

class Graph_Model extends CI_Model
{

    public function get_utype($uyid)
    {
        $query = $this->db->query("SELECT id,name FROM user_type WHERE id='$uyid'");
     
        return $query->result();
    }

    public function get_fannalstwise($uid, $userTypeid, $sdate, $edate)
    {

        // need to add status id in this

        $this->db->select('status.id stid');
        $this->db->select('status.name stname');
        $this->db->select('COUNT(*) cont');
        $this->db->from('init_call ic');
        $this->db->join('status', 'status.id = ic.cstatus', 'left');
        $this->db->join('user_details', 'user_details.user_id = ic.mainbd', 'left');

        if ($userTypeid == 2) {
            //if admin is loggedin
            $this->db->where('admin_id', $uid);
        } elseif ($userTypeid == 4) {
            //if PST is loggedin
            $this->db->where('pst_co', $uid);

            # code...
        } elseif ($userTypeid == 9) {
            //if BDPST is loggedin
            $this->db->where('aadmin', $uid);

            # code...
        } elseif ($userTypeid == 13) {
            //if CLM is loggedin
            // $this->db->where('admin_id', $uid);

            # code...
        } else {
            //if BD is loggedin
            $this->db->where('mainbd', $uid);
        }

        $this->db->where('user_details.status', 'active');

        $this->db->where('CAST(ic.createDate AS DATE) >=', "'$sdate'", FALSE);
        $this->db->where('CAST(ic.createDate AS DATE) <=', "'$edate'", FALSE);
        $this->db->group_by('status.name');
        $this->db->group_by('status.id');
        // $this->db->order_by('nom_dept', 'asc');
        $query = $this->db->get();
        //    echo $this->db->last_query();die;

        return $query->result();
    }

    public function get_fannalbycode_OG($uid, $userTypeid, $sdate, $edate, $userType = null, $cluster = null, $partnerType = null, $category = null, $users = null)
    {

        $this->db->select('status.id stid');
        $this->db->select('status.name stname');
        $this->db->select('company_master.compname company_name');
        $this->db->select('company_master.address company_address');
        $this->db->select('city.city city');
        $this->db->select('states.state state');
        $this->db->select('partner_master.name partner_type');
        $this->db->select("CASE WHEN ic.focus_funnel = 'yes' THEN 'Focus Funnel' END as focus_funnel");
        $this->db->select("CASE WHEN ic.keycompany = 'yes' THEN 'Key Company' END as keycompany");
        $this->db->select("CASE WHEN ic.upsell_client = 'yes' THEN 'Upsell Client' END as upsell_client");
        $this->db->select('latest_remarks.remarks remark');
        $this->db->select('tblcallevents_counts.updatedt as latest_updated_date');
        $this->db->select('tblcallevents_counts.cont as total_count');

        // Calculate and format the difference
        $this->db->select(
            "CONCAT(
                TIMESTAMPDIFF(DAY, tblcallevents_counts.updatedt, CURDATE()), ' day ',
                MOD(TIMESTAMPDIFF(HOUR, tblcallevents_counts.updatedt, NOW()), 24), ' hour ',
                MOD(TIMESTAMPDIFF(MINUTE, tblcallevents_counts.updatedt, NOW()), 60), ' min ',
                MOD(TIMESTAMPDIFF(SECOND, tblcallevents_counts.updatedt, NOW()), 60), ' sec'
            ) AS time_since_last_update"
        );


        $this->db->from('init_call ic');

        $this->db->join('company_master', 'company_master.id = ic.cmpid_id', 'left');
        $this->db->join('city', 'city.id = company_master.city', 'left');
        $this->db->join('states', 'states.id = company_master.state', 'left');
        $this->db->join('tblcallevents', 'tblcallevents.cid_id = company_master.id', 'left');
        $this->db->join('partner_master', 'partner_master.id = company_master.partnerType_id', 'left');
        $this->db->join('status', 'status.id = ic.cstatus', 'left');

        $this->db->join('user_details', 'user_details.user_id = ic.mainbd', 'left');

        // Subquery to get latest remarks for each init_call
        $this->db->join(
            '(SELECT tblcallevents.cid_id, tblcallevents.remarks, ROW_NUMBER() OVER (PARTITION BY tblcallevents.cid_id ORDER BY tblcallevents.updated_at DESC) as rn
              FROM tblcallevents) as latest_remarks',
            'latest_remarks.cid_id = ic.cmpid_id AND latest_remarks.rn = 1',
            'left'
        );


        // Subquery to get max updateddate and count for each cid_id
        $this->db->join(
            '(SELECT cid_id, MAX(updateddate) as updatedt, COUNT(*) as cont
            FROM tblcallevents
            WHERE nextCFID != "0"
            GROUP BY cid_id) as tblcallevents_counts',
            'tblcallevents_counts.cid_id = ic.cmpid_id',
            'left'
        );

        if ($userTypeid == 2) {
            //if admin is loggedin
            $this->db->where('admin_id', $uid);
        } elseif ($userTypeid == 4) {
            //if PST is loggedin
            $this->db->where('pst_co', $uid);

            # code...
        } elseif ($userTypeid == 9) {
            //if BDPST is loggedin
            $this->db->where('aadmin', $uid);

            # code...
        } elseif ($userTypeid == 13) {
            //if CLM is loggedin
            // $this->db->where('admin_id', $uid);

            # code...
        } else {
            //if BD is loggedin
            $this->db->where('mainbd', $uid);
        }



        $this->db->where('user_details.status', 'active');

        $this->db->where('CAST(ic.createDate AS DATE) >=', "'$sdate'", FALSE);
        $this->db->where('CAST(ic.createDate AS DATE) <=', "'$edate'", FALSE);
        $this->db->where('tblcallevents.remarks !=', '');
        $this->db->group_by('company_name');
        $query = $this->db->get();

        //    echo $this->db->last_query();die;
        return $query->result();
    }

    public function getRoles($type_id)
    {

        $this->db->select('*');
        $this->db->from('user_type');
        // $this->db->where('Active_Flag', 0);
        if ($type_id == '15') {
            # code...
            $this->db->where_in('id', [3,4,13]);
        }
        else if($type_id == 2) {
            $this->db->where_in('id', [3,4,13,15]);
        }
        else if($type_id == 13) {
            $this->db->where_in('id', [3]);
        }
        else if($type_id == 4) {
            $this->db->where_in('id', [3,13]);
        }
        else{
            $this->db->where('id', $type_id);
        }
        $query = $this->db->get();
        // echo  $this->db->last_query(); die;
        return $query->result();
    }

    public function getPartnerType()
    {

        $this->db->select('*');
        $this->db->from('partner_master');

        $query = $this->db->get();
        return $query->result();
    }

    public function get_clusters()
    {

        $this->db->select('id');
        $this->db->select('cluster_name');
        $this->db->from('clustermaster');

        $query = $this->db->get();
        // echo  $this->db->last_query(); die;
        return $query->result();
    }

    public function getClusterManagerByCluster($clusterID)
    {

        $this->db->select('user_id');
        $this->db->select('name');
        $this->db->from('user_details');
        $this->db->join('clustermaster', 'user_details.user_id = clustermaster.clusterManager_id', 'left');
        $this->db->where_in('clustermaster.id', $clusterID);

        $query = $this->db->get();
        // echo $this->db->last_query();die;
        return $query->result();
    }

    public function getCategory()
    {

        $this->db->select('*');
        $this->db->from('categories');

        $query = $this->db->get();
        // echo  $this->db->last_query(); die;
        return $query->result();
    }

    public function getStatus()
    {

        $this->db->select('*');
        $this->db->from('status');

        $query = $this->db->get();
        // echo  $this->db->last_query(); die;
        return $query->result();
    }

    public function getAction()
    {

        $this->db->select('*');
        $this->db->from('action');

        $query = $this->db->get();
        // echo  $this->db->last_query(); die;
        return $query->result();
    }

    public function get_tblbyidwithremark($ciid)
    {
        // $query=$this->db->query("SELECT * FROM tblcallevents WHERE cid_id='$ciid' and remarks!='' ORDER BY tblcallevents.updateddate DESC");
        $this->db->select('cid_id');
        $this->db->select('remarks');
        $this->db->select('updateddate');
        $this->db->select('user_details.name last_updated_by');
        $this->db->from('tblcallevents');
        $this->db->join('user_details', 'user_details.user_id = tblcallevents.user_id', 'left');

        $this->db->where('cid_id', $ciid);
        $this->db->where('nextCFID !=', 0);
        $this->db->order_by('tblcallevents.updateddate DESC');
        $this->db->limit('1');

        $query = $this->db->get();
        // echo $this->db->last_query();die;
        return $query->result();
    }

    public function getGraphDetails($uid, $userTypeid, $sdate, $edate, $userType = null, $cluster = null, $partnerType = null, $category = null, $users = null)
    {

        // var_dump($category);die;
        $this->db->select('status.clr stclr');
        $this->db->select('status.id stid');
        $this->db->select('status.name stname');
        $this->db->select('COUNT(*) cont');
        $this->db->from('init_call ic');
        $this->db->join('status', 'status.id = ic.cstatus', 'left');
        $this->db->join('user_details', 'user_details.user_id = ic.mainbd', 'left');

        if (!empty($partnerType)) {

            $this->db->join('company_master', 'company_master.id = ic.cmpid_id', 'left');
            $this->db->where_in('company_master.partnerType_id', $partnerType);
        }

        if (!empty($category)) {
            // var_dump($category);die;
            $this->db->group_start();

            foreach ($category as $singleCategory) {

                if ($singleCategory == 'topspender') {

                    $this->db->or_where('topspender', 'yes');
                }
                if ($singleCategory == 'focus_funnel') {

                    $this->db->or_where('focus_funnel', 'yes');
                }
                if ($singleCategory == 'upsell_client') {

                    $this->db->or_where('upsell_client', 'yes');
                }
                if ($singleCategory == 'keycompany') {

                    $this->db->or_where('keycompany', 'yes');
                }
                if ($singleCategory == 'pkclient ') {

                    $this->db->or_where('pkclient', 'yes');
                }
                if ($singleCategory == 'priorityc ') {

                    $this->db->or_where('priorityc', 'yes');
                }
            }

            $this->db->group_end();
        }

        // var_dump($cluster);die;
        // if cluster is selected
        if (!empty($cluster)) {
            // echo 'hii';die;
            $this->db->where_in('aadmin', $users);
        }


        // if userType is selected
        if (!empty($userType)) {
            $this->db->group_start();
            foreach ($userType as $singleuserType) {
                if ($singleuserType == 4) {
                    $this->db->or_where_in('pst_co', $users);
                }
                if ($singleuserType == 9 || $singleuserType == 13) {
                    $this->db->or_where_in('aadmin', $users);
                }
                else{

                    $this->db->where_in('user_id', $users);
                }
            }
            $this->db->group_end();
        }

        // echo $userTypeid;die;
        if ($userTypeid == 2) {
            //if admin is loggedin
            $this->db->where('admin_id', $uid);
        } elseif ($userTypeid == 4) {
            //if PST is loggedin
            $this->db->where('pst_co', $uid);
        } elseif ($userTypeid == 9) {
            //if BDPST is loggedin
            $this->db->where('aadmin', $uid);
        } elseif ($userTypeid == 13) {
            //if CLM is loggedin
            $this->db->where('admin_id', $uid);
        }elseif ($userTypeid == 15) {
            //if CLM is loggedin
            $this->db->where('sales_co', $uid);
        }  
        else {
            //if BD is loggedin
            $this->db->where('mainbd', $uid);
        }

        $this->db->where('user_details.status', 'active');

        // $this->db->where('CAST(ic.createDate AS DATE) >=', "'$sdate'", FALSE);
        // $this->db->where('CAST(ic.createDate AS DATE) <=', "'$edate'", FALSE);
        $this->db->group_by('status.name');
        $this->db->group_by('status.id');
        // $this->db->group_by('ic.cmpid_id ');
        $query = $this->db->get();
        // echo $this->db->last_query();die;

        return $query->result();
    }

    public function get_TableData($uid, $userTypeid, $sdate, $edate, $userType = null, $cluster = null, $partnerType = null, $category = null, $users = null)
    {
        // var_dump($partnerType);die;
        $this->db->select('ic.id ic_id');
        $this->db->select('status.clr stclr');
        $this->db->select('status.id stid');
        $this->db->select('status.name stname');
        $this->db->select('company_master.compname company_name');
        $this->db->select('company_master.address company_address');
        $this->db->select('city.city city');
        $this->db->select('states.state state');
        $this->db->select('partner_master.name partner_type');
        $this->db->from('init_call ic');
        $this->db->join('company_master', 'company_master.id = ic.cmpid_id', 'left');
        $this->db->join('city', 'city.id = company_master.city', 'left');
        $this->db->join('states', 'states.id = company_master.state', 'left');
        $this->db->join('partner_master', 'partner_master.id = company_master.partnerType_id', 'left');
        $this->db->join('status', 'status.id = ic.cstatus', 'left');
        $this->db->join('user_details', 'user_details.user_id = ic.mainbd', 'left');

        if (!empty($partnerType)) {

            $this->db->where_in('company_master.partnerType_id', $partnerType);
        }

        if (!empty($category)) {
            $this->db->group_start();

            foreach ($category as $singleCategory) {

                if ($singleCategory == 'topspender') {

                    $this->db->or_where('topspender', 'yes');
                }
                if ($singleCategory == 'focus_funnel') {

                    $this->db->or_where('focus_funnel', 'yes');
                }
                if ($singleCategory == 'upsell_client') {

                    $this->db->or_where('upsell_client', 'yes');
                }
                if ($singleCategory == 'keycompany') {

                    $this->db->or_where('keycompany', 'yes');
                }
                if ($singleCategory == 'pkclient ') {

                    $this->db->or_where('pkclient', 'yes');
                }
                if ($singleCategory == 'priorityc ') {

                    $this->db->or_where('priorityc', 'yes');
                }
            }

            $this->db->group_end();
        }

        //if user type is selected
        if (!empty($userType)) {
            $this->db->group_start();
            foreach ($userType as $singleuserType) {
                if ($singleuserType == 4) {
                    $this->db->or_where_in('pst_co', $users);
                }
                if ($singleuserType == 9 || $singleuserType == 13) {
                    $this->db->or_where_in('aadmin', $users);
                }
                else{
                    $this->db->where_in('user_id', $users);
                }
            }
            $this->db->group_end();
        } 
        else {
            $this->db->where_in('admin_id', $uid);
        }

        // if cluster is selected
        if (!empty($cluster)) {
            // var_dump($cluster);die;
            $this->db->where_in('aadmin', $users);
        }


        if ($userTypeid == 2) {
            //if admin is loggedin
            $this->db->where('admin_id', $uid);
        } elseif ($userTypeid == 4) {
            //if PST is loggedin
            $this->db->where('pst_co', $uid);
        } elseif ($userTypeid == 9) {
            //if BDPST is loggedin
            $this->db->where('aadmin', $uid);
        } elseif ($userTypeid == 13) {
            //if CLM is loggedin
            $this->db->where('admin_id', $uid);
        } elseif ($userTypeid == 15) {
            //if CLM is loggedin
            $this->db->where('sales_co', $uid);
        } 
        // else {
        //     //if BD is loggedin
        //     $this->db->where('mainbd', $uid);
        // }

        $this->db->where('user_details.status', 'active');

        // $this->db->where('CAST(ic.createDate AS DATE) >=', "'$sdate'", FALSE);
        // $this->db->where('CAST(ic.createDate AS DATE) <=', "'$edate'", FALSE);
        // $this->db->where('tblcallevents.remarks !=', '');
        $this->db->group_by('ic.cmpid_id');
        // $this->db->group_by('stname');
        $query = $this->db->get();

        // echo $this->db->last_query();die;
        
        return $query->result();
    }

    public function getTableDataByStatus($uid, $userTypeid, $stid, $sdate, $edate, $selected_partnerType, $selected_category, $selected_userType, $selected_users, $selected_cluster)
    {
        // print_r($selected_cluster);die;
        $this->db->select('ic.id ic_id');
        $this->db->select('ic.focus_funnel focus_funnel');
        $this->db->select('ic.upsell_client upsell_client');
        $this->db->select('ic.keycompany keycompany');
        $this->db->select('ic.pkclient pkclient');
        $this->db->select('ic.priorityc priorityc');
        $this->db->select('status.clr stclr');
        $this->db->select('status.id stid');
        $this->db->select('status.name stname');
        $this->db->select('company_master.compname company_name');
        $this->db->select('company_master.address company_address');
        $this->db->select('city.city city');
        $this->db->select('states.state state');
        $this->db->select('partner_master.name partner_type');
        $this->db->from('init_call ic');
        $this->db->join('company_master', 'company_master.id = ic.cmpid_id', 'left');
        $this->db->join('city', 'city.id = company_master.city', 'left');
        $this->db->join('states', 'states.id = company_master.state', 'left');
        $this->db->join('partner_master', 'partner_master.id = company_master.partnerType_id', 'left');
        $this->db->join('status', 'status.id = ic.cstatus', 'left');
        $this->db->join('user_details', 'user_details.user_id = ic.mainbd', 'left');

        if (!empty($selected_partnerType)) {

            $this->db->where_in('company_master.partnerType_id', $selected_partnerType);
        }

        if (!empty($selected_category)) {
            // var_dump($category);die;
            $this->db->group_start();

            foreach ($selected_category as $singleCategory) {

                if ($singleCategory == 'topspender') {

                    $this->db->or_where('topspender', 'yes');
                }
                if ($singleCategory == 'focus_funnel') {

                    $this->db->or_where('focus_funnel', 'yes');
                }
                if ($singleCategory == 'upsell_client') {

                    $this->db->or_where('upsell_client', 'yes');
                }
                if ($singleCategory == 'keycompany') {

                    $this->db->or_where('keycompany', 'yes');
                }
                if ($singleCategory == 'pkclient ') {

                    $this->db->or_where('pkclient', 'yes');
                }
                if ($singleCategory == 'priorityc ') {

                    $this->db->or_where('priorityc', 'yes');
                }
            }

            $this->db->group_end();
        }

        //if user type is selected
        if (!empty($selected_userType)) {
            $this->db->group_start();
            foreach ($selected_userType as $singleuserType) {
                if ($singleuserType == 4) {
                    $this->db->or_where_in('pst_co', $selected_users);
                }
                if ($singleuserType == 9 || $singleuserType == 13) {
                    $this->db->or_where_in('aadmin', $selected_users);
                }else{

                    $this->db->where_in('user_id', $selected_users);
                }
                
            }
            $this->db->group_end();
        } 
        // else {
        //     $this->db->where_in('admin_id', $uid);
        // }

        // if cluster is selected
        // var_dump($selected_cluster);die;
        if (!empty($selected_cluster)) {
            if ($selected_cluster[0] != null) {

                $this->db->where_in('aadmin', $selected_users);
            }
        }

        if ($userTypeid == 2) {
            //if admin is loggedin
            $this->db->where('admin_id', $uid);
        } elseif ($userTypeid == 4) {
            //if PST is loggedin
            $this->db->where('pst_co', $uid);
        } elseif ($userTypeid == 9) {
            //if BDPST is loggedin
            $this->db->where('aadmin', $uid);
        } elseif ($userTypeid == 13) {
            //if CLM is loggedin
            $this->db->where('admin_id', $uid);
        } else {
            //if BD is loggedin
            $this->db->where('mainbd', $uid);
        }

        $this->db->where('user_details.status', 'active');
        $this->db->where('status.id', $stid);

        // $this->db->where('CAST(ic.createDate AS DATE) >=', "'$sdate'", FALSE);
        // $this->db->where('CAST(ic.createDate AS DATE) <=', "'$edate'", FALSE);
        // $this->db->where('tblcallevents.remarks !=', '');
        $this->db->group_by('ic.cmpid_id');
        // $this->db->group_by('stname');
        $query = $this->db->get();

        //    echo $this->db->last_query();die;
        return $query->result();
    }

    public function getStatusWiseFunnelData($uid, $sdate, $edate, $selected_partnerType, $arrayselected_cluster, $arrayselected_user, $status_id, $selected_userType, $selected_category)
    {

        // print_r($arrayselected_user);die;
        $this->db->select('status.id stid');
        $this->db->select('status.name stname');
        $this->db->select('company_master.compname company_name');
        $this->db->select('company_master.address company_address');
        $this->db->select('city.city city');
        $this->db->select('states.state state');
        $this->db->select('partner_master.name partner_type');
        $this->db->select('ic.cmpid_id cmpid_id');
        $this->db->select('user_details.name bd_name');
        $this->db->select('ud_bd.name pst_name');
        $this->db->from('init_call ic');
        $this->db->join('company_master', 'company_master.id = ic.cmpid_id', 'left');
        $this->db->join('city', 'city.id = company_master.city', 'left');
        $this->db->join('states', 'states.id = company_master.state', 'left');
        $this->db->join('tblcallevents', 'tblcallevents.cid_id = company_master.id', 'left');
        $this->db->join('partner_master', 'partner_master.id = company_master.partnerType_id', 'left');
        $this->db->join('status', 'status.id = ic.cstatus', 'left');
        $this->db->join('user_details', 'user_details.user_id = ic.mainbd', 'left');
        $this->db->join('user_details ud_bd', 'ud_bd.user_id = user_details.user_id', 'left');

        if (!empty($selected_partnerType)) {

            $this->db->where_in('company_master.partnerType_id', $selected_partnerType);
        }

        // if (!empty($arrayselected_user)) {

        //     $this->db->where_in('user_details.aadmin', $arrayselected_user);
        // }

        if (!empty($selected_category)) {
            // var_dump($category);die;
            $this->db->group_start();

            foreach ($selected_category as $singleCategory) {

                if ($singleCategory == 'topspender') {

                    $this->db->or_where('focus_funnel', 'yes');
                }
                if ($singleCategory == 'focus_funnel') {

                    $this->db->or_where('focus_funnel', 'yes');
                }
                if ($singleCategory == 'upsell_client') {

                    $this->db->or_where('upsell_client', 'yes');
                }
                if ($singleCategory == 'keycompany') {

                    $this->db->or_where('keycompany', 'yes');
                }
                if ($singleCategory == 'pkclient ') {

                    $this->db->or_where('pkclient', 'yes');
                }
                if ($singleCategory == 'priorityc ') {

                    $this->db->or_where('priorityc', 'yes');
                }
            }

            $this->db->group_end();
        }

        // var_dump($selected_userType);die;
        if (!empty($selected_userType)) {
            $this->db->group_start();
            foreach ($selected_userType as $singleuserType) {
                if ($singleuserType == 4) {
                    $this->db->or_where_in('user_details.pst_co', $arrayselected_user);
                }
                if ($singleuserType == 9 || $singleuserType == 13) {
                    $this->db->or_where_in('user_details.aadmin', $arrayselected_user);
                }
                else{

                    $this->db->where_in('user_details.user_id', $arrayselected_user);
                }
            }
            $this->db->group_end();
        }

        $this->db->where('user_details.status', 'active');
        // $this->db->where_in('company_master.partnerType_id', $selected_partnerType);
        $this->db->where('CAST(ic.createDate AS DATE) >=', "'$sdate'", FALSE);
        $this->db->where('CAST(ic.createDate AS DATE) <=', "'$edate'", FALSE);
        // $this->db->where('tblcallevents.remarks !=', '');
        $this->db->where_in('user_details.admin_id', $uid);
        $this->db->where_in('status.id', $status_id);
        $this->db->group_by('ic.cmpid_id');
        $query = $this->db->get();

        //    echo $this->db->last_query();die;
        return $query->result();
    }


    public function getSankeyGraphData($uid, $userTypeid, $sdate, $edate, $userType = null, $cluster = null, $partnerType = null, $category = null, $users = null)
    {

        $this->db->select('ic.id id');
        $this->db->from('init_call ic');
        $this->db->join('status', 'status.id = ic.cstatus', 'left');
        $this->db->join('user_details', 'user_details.user_id = ic.mainbd', 'left');

        if (!empty($partnerType)) {

            $this->db->join('company_master', 'company_master.id = ic.cmpid_id', 'left');
            $this->db->where_in('company_master.partnerType_id', $partnerType);
        }

        if (!empty($category)) {
            // var_dump($category);die;
            $this->db->group_start();

            foreach ($category as $singleCategory) {

                if ($singleCategory == 'topspender') {

                    $this->db->or_where('topspender', 'yes');
                }
                if ($singleCategory == 'focus_funnel') {

                    $this->db->or_where('focus_funnel', 'yes');
                }
                if ($singleCategory == 'upsell_client') {

                    $this->db->or_where('upsell_client', 'yes');
                }
                if ($singleCategory == 'keycompany') {

                    $this->db->or_where('keycompany', 'yes');
                }
                if ($singleCategory == 'pkclient ') {

                    $this->db->or_where('pkclient', 'yes');
                }
                if ($singleCategory == 'priorityc ') {

                    $this->db->or_where('priorityc', 'yes');
                }
            }

            $this->db->group_end();
        }

        // if cluster is selected
        if (!empty($cluster)) {
            // echo 'hii';die;
            $this->db->where_in('aadmin', $users);
        }

        // if userType is selected
        if (!empty($userType)) {
            $this->db->group_start();
            foreach ($userType as $singleuserType) {
                if ($singleuserType == 4) {
                    $this->db->or_where_in('pst_co', $users);
                }
                if ($singleuserType == 9 || $singleuserType == 13) {
                    $this->db->or_where_in('aadmin', $users);
                }
                else{

                    $this->db->where_in('user_id', $users);
                }
            }
            $this->db->group_end();
        }

        // echo $userTypeid;die;
        if ($userTypeid == 2) {
            //if admin is loggedin
            $this->db->where('admin_id', $uid);
        } elseif ($userTypeid == 4) {
            //if PST is loggedin
            $this->db->where('pst_co', $uid);
        } elseif ($userTypeid == 9) {
            //if BDPST is loggedin
            $this->db->where('aadmin', $uid);
        } elseif ($userTypeid == 13) {
            //if CLM is loggedin
            $this->db->where('aadmin', $uid);
        } elseif ($userTypeid == 15) {
            //if CLM is loggedin
            $this->db->where('sales_co', $uid);
        } 
        // else {
        //     //if BD is loggedin
        //     $this->db->where('mainbd', $uid);
        // }
        
        $this->db->where('user_details.status', 'active');

        $this->db->group_by('ic.cmpid_id ');
        $subquery = $this->db->get_compiled_select();


        $this->db->select('status_id from');
        $this->db->select('nstatus_id to');
        $this->db->select('st1.name fromStatus');
        $this->db->select('st2.name toStatus');
        $this->db->select('COUNT(*) count');
        $this->db->from('tblcallevents');
        $this->db->join('status st1', 'st1.id = tblcallevents.status_id', 'left');
        $this->db->join('status st2', 'st2.id = tblcallevents.nstatus_id', 'left');
        // $this->db->where('tblcallevents.cid_id',$status);
        $this->db->where_in('cid_id', $subquery, FALSE);
        $this->db->where('CAST(updateddate AS DATE) >=', "'$sdate'", FALSE);
        $this->db->where('CAST(updateddate AS DATE) <=', "'$edate'", FALSE);
        $this->db->where('nstatus_id IS NOT NULL');
        $this->db->where('nstatus_id !=','status_id');


        $this->db->group_by(' status_id, nstatus_id');
        $this->db->order_by(' status_id, nstatus_id');
        $query = $this->db->get();

        // echo $this->db->last_query();die;

        return $query->result();
    }

    public function getCityWiseGraphDetails($uid, $userTypeid, $sdate, $edate)
    {

        // $query=$this->db->query("Select city.id cityid, company_master.city,COUNT(*) cont from init_call LEFT JOIN user_details ON user_details.user_id=init_call.mainbd LEFT JOIN company_master ON company_master.id=init_call.cmpid_id left join city on city.city=company_master.city WHERE user_details.admin_id='$uid' and user_details.type_id='3' and user_details.status='active' GROUP BY company_master.city,city.id");
        // echo "hii";die;
        $this->db->select('city.id cityid');
        $this->db->select('city.city city');
        $this->db->select('COUNT(*) cont');
        $this->db->from('init_call ic');
        $this->db->join('company_master', 'company_master.id = ic.cmpid_id', 'left');
        $this->db->join('city', 'city.id = company_master.city', 'inner');
        $this->db->join('user_details', 'user_details.user_id = ic.mainbd');

        if (!empty($userTypeid)) {
            $this->db->group_start();
            foreach ($userTypeid as $singleuserType) {
                if ($singleuserType == 4) {
                    $this->db->or_where_in('user_details.pst_co', $uid);
                }
                if ($singleuserType == 9 || $singleuserType == 13) {
                    $this->db->or_where_in('user_details.aadmin', $uid);
                }
                else{

                    $this->db->where_in('user_details.user_id', $uid);
                }
            }
            $this->db->group_end();
        }

        $this->db->where('CAST(ic.createDate AS DATE) >=', "'$sdate'", FALSE);
        $this->db->where('CAST(ic.createDate AS DATE) <=', "'$edate'", FALSE);

        $this->db->group_by('city.city');
        $this->db->group_by('city.id');
        $query = $this->db->get();
        // echo $this->db->last_query();die;

        return $query->result();
    }

    public function getCityWiseTableDetails($uid, $selected_userType, $sdate, $edate)
    {

        $this->db->select('ic.id ic_id');
        $this->db->select('status.clr stclr');
        $this->db->select('status.id stid');
        $this->db->select('status.name stname');
        $this->db->select('company_master.compname company_name');
        $this->db->select('company_master.address company_address');
        $this->db->select('city.city city');
        $this->db->select('states.state state');
        $this->db->select('partner_master.name partner_type');
        $this->db->from('init_call ic');
        $this->db->join('company_master', 'company_master.id = ic.cmpid_id', 'left');
        $this->db->join('city', 'city.id = company_master.city', 'INNER');
        $this->db->join('states', 'states.id = company_master.state', 'left');
        $this->db->join('partner_master', 'partner_master.id = company_master.partnerType_id', 'left');
        $this->db->join('status', 'status.id = ic.cstatus', 'left');
        $this->db->join('user_details', 'user_details.user_id = ic.mainbd', 'left');

        if (!empty($selected_userType)) {
            $this->db->group_start();
            foreach ($selected_userType as $singleuserType) {
                if ($singleuserType == 4) {
                    $this->db->or_where_in('user_details.pst_co', $uid);
                }
                if ($singleuserType == 9 || $singleuserType == 13) {
                    $this->db->or_where_in('user_details.aadmin', $uid);
                }
                else{

                    $this->db->where_in('user_details.user_id', $uid);
                }
            }
            $this->db->group_end();
        }

        $this->db->where('CAST(ic.createDate AS DATE) >=', "'$sdate'", FALSE);
        $this->db->where('CAST(ic.createDate AS DATE) <=', "'$edate'", FALSE);


        $this->db->group_by('company_master.id');

        $query = $this->db->get();
        // echo $this->db->last_query();die;

        return $query->result();
    }

    public function getTableDataByCity($uid, $userTypeid, $cityId, $sdate, $edate)
    {
        // print_r($selected_partnerType[0]);die;
        $this->db->select('ic.id ic_id');
        $this->db->select('ic.focus_funnel focus_funnel');
        $this->db->select('ic.upsell_client upsell_client');
        $this->db->select('ic.keycompany keycompany');
        $this->db->select('ic.pkclient pkclient');
        $this->db->select('ic.priorityc priorityc');
        $this->db->select('status.clr stclr');
        $this->db->select('status.id stid');
        $this->db->select('status.name stname');
        $this->db->select('company_master.compname company_name');
        $this->db->select('company_master.address company_address');
        $this->db->select('city.city city');
        $this->db->select('states.state state');
        $this->db->select('partner_master.name partner_type');
        $this->db->from('init_call ic');
        $this->db->join('company_master', 'company_master.id = ic.cmpid_id', 'left');
        $this->db->join('city', 'city.id = company_master.city', 'left');
        $this->db->join('states', 'states.id = company_master.state', 'left');
        $this->db->join('partner_master', 'partner_master.id = company_master.partnerType_id', 'left');
        $this->db->join('status', 'status.id = ic.cstatus', 'left');
        $this->db->join('user_details', 'user_details.user_id = ic.mainbd', 'left');

        if ($userTypeid == 2) {
            //if admin is loggedin
            $this->db->where('admin_id', $uid);
        } elseif ($userTypeid == 4) {
            //if PST is loggedin
            $this->db->where('pst_co', $uid);
        } elseif ($userTypeid == 9) {
            //if BDPST is loggedin
            $this->db->where('aadmin', $uid);
        } elseif ($userTypeid == 13) {
            //if CLM is loggedin
            $this->db->where('admin_id', $uid);
        } elseif ($userTypeid == 15) {
            //if CLM is loggedin
            $this->db->where('sales_co', $uid);
        } else {
            //if BD is loggedin
            $this->db->where('mainbd', $uid);
        }

        $this->db->where('user_details.status', 'active');
        $this->db->where('company_master.city', $cityId);

        $this->db->where('CAST(ic.createDate AS DATE) >=', "'$sdate'", FALSE);
        $this->db->where('CAST(ic.createDate AS DATE) <=', "'$edate'", FALSE);
        // $this->db->where('tblcallevents.remarks !=', '');
        $this->db->group_by('ic.cmpid_id');
        // $this->db->group_by('stname');
        
        
        $query = $this->db->get();
        echo $this->db->last_query();die;

        return $query->result();
    }

    public function getPartnerWiseGraphDetails($uid, $userTypeid, $sdate, $edate, $partnerType)
    {

        $this->db->select('partner_master.id PartnerMasterID');
        $this->db->select('partner_master.name PartnerMasterName');
        $this->db->select('partner_master.clr PartnerMasterclr');
        $this->db->select('COUNT(*) cont');
        $this->db->from('init_call ic');
        $this->db->join('company_master', 'company_master.id = ic.cmpid_id', 'left');
        $this->db->join('partner_master', 'partner_master.id = company_master.partnerType_id', 'left');
        $this->db->join('user_details', 'user_details.user_id = ic.mainbd');

        // if ($userTypeid == 2) {

        //     $this->db->where('user_details.admin_id', $uid);
        // } elseif ($userTypeid == 4) {

        //     $this->db->where_in('pst_co', $uid);
        // } elseif ($userTypeid == 9 || $userTypeid == 13) {

        //     $this->db->where_in('aadmin', $uid);
        // } else {
        //     $this->db->where('user_id', $uid);
        // }

        if (!empty($partnerType)) {

            $this->db->where_in('company_master.partnerType_id', $partnerType);
        }
        
        
        if (!empty($userTypeid)) {
            $this->db->group_start();
            foreach ($userTypeid as $singleuserType) {
                if ($singleuserType == 4) {
                    $this->db->or_where_in('user_details.pst_co', $uid);
                }
                if ($singleuserType == 9 || $singleuserType == 13) {
                    $this->db->or_where_in('user_details.aadmin', $uid);
                }
                else{

                    $this->db->where_in('user_details.user_id', $uid);
                }
            }
            $this->db->group_end();
        }

        $this->db->where('CAST(ic.createDate AS DATE) >=', "'$sdate'", FALSE);
        $this->db->where('CAST(ic.createDate AS DATE) <=', "'$edate'", FALSE);

        // $this->db->group_by('PartnerMasterName');
        $this->db->group_by('PartnerMasterID');
        $query = $this->db->get();
        // echo $this->db->last_query();die;

        return $query->result();
    }

    public function getPartnerWiseTableDetails($uid, $userTypeid, $sdate, $edate, $partnerType)
    {

        // echo $partnerType;die;
        $this->db->select('ic.id ic_id');
        $this->db->select('status.clr stclr');
        $this->db->select('status.id stid');
        $this->db->select('status.name stname');
        $this->db->select('company_master.compname company_name');
        $this->db->select('company_master.address company_address');
        $this->db->select('company_master.id company_id');
        $this->db->select('city.city city');
        $this->db->select('states.state state');
        $this->db->select('partner_master.name partner_typeName');
        $this->db->select('partner_master.id partner_typeID');
        $this->db->select('partner_master.clr PartnerMasterclr');
        $this->db->from('init_call ic');
        $this->db->join('company_master', 'company_master.id = ic.cmpid_id', 'left');
        $this->db->join('city', 'city.id = company_master.city', 'left');
        $this->db->join('states', 'states.id = company_master.state', 'left');
        $this->db->join('partner_master', 'partner_master.id = company_master.partnerType_id', 'left');
        $this->db->join('status', 'status.id = ic.cstatus', 'left');
        $this->db->join('user_details', 'user_details.user_id = ic.mainbd', 'left');


        // if ($userTypeid == 2) {

        //     $this->db->where('user_details.admin_id', $uid);
        // } elseif ($userTypeid == 4) {

        //     $this->db->where_in('pst_co', $uid);
        // } elseif ($userTypeid == 9 || $userTypeid == 13) {

        //     $this->db->where_in('aadmin', $uid);
        // } else {
        //     $this->db->where('user_id', $uid);
        // }
    
        if (!empty($userTypeid)) {
            $this->db->group_start();
            foreach ($userTypeid as $singleuserType) {
                if ($singleuserType == 4) {
                    $this->db->or_where_in('user_details.pst_co', $uid);
                }
                if ($singleuserType == 9 || $singleuserType == 13) {
                    $this->db->or_where_in('user_details.aadmin', $uid);
                }
                else{

                    $this->db->where_in('user_details.user_id', $uid);
                }
            }
            $this->db->group_end();
        }

        if (!empty($partnerType)) {

            $this->db->where_in('company_master.partnerType_id', $partnerType);
        }

        $this->db->where('CAST(ic.createDate AS DATE) >=', "'$sdate'", FALSE);
        $this->db->where('CAST(ic.createDate AS DATE) <=', "'$edate'", FALSE);

        $query = $this->db->get();
        // echo $this->db->last_query();die;

        return $query->result();
    }

    public function getTableDataByPartnerType($uid, $userTypeid, $PartnerMasterID, $sdate, $edate)
    {
        // print_r($selected_partnerType[0]);die;
        $this->db->select('ic.id ic_id');
        $this->db->select('ic.focus_funnel focus_funnel');
        $this->db->select('ic.upsell_client upsell_client');
        $this->db->select('ic.keycompany keycompany');
        $this->db->select('ic.pkclient pkclient');
        $this->db->select('ic.priorityc priorityc');
        $this->db->select('status.clr stclr');
        $this->db->select('status.id stid');
        $this->db->select('status.name stname');
        $this->db->select('company_master.compname company_name');
        $this->db->select('company_master.address company_address');
        $this->db->select('city.city city');
        $this->db->select('states.state state');
        $this->db->select('partner_master.name partner_type');
        $this->db->from('init_call ic');
        $this->db->join('company_master', 'company_master.id = ic.cmpid_id', 'left');
        $this->db->join('city', 'city.id = company_master.city', 'left');
        $this->db->join('states', 'states.id = company_master.state', 'left');
        $this->db->join('partner_master', 'partner_master.id = company_master.partnerType_id', 'left');
        $this->db->join('status', 'status.id = ic.cstatus', 'left');
        $this->db->join('user_details', 'user_details.user_id = ic.mainbd', 'left');

        if (!empty($PartnerMasterID)) {

            $this->db->where_in('company_master.partnerType_id', $PartnerMasterID);
        }

        // if (!empty($selected_category)) {
        //     // var_dump($category);die;
        //     $this->db->group_start();

        //     foreach ($selected_category as $singleCategory) {

        //         if ($singleCategory == 'topspender') {

        //             $this->db->or_where('focus_funnel', 'yes');

        //         }
        //         if($singleCategory == 'focus_funnel') {

        //             $this->db->or_where('focus_funnel', 'yes');

        //         }
        //         if($singleCategory == 'upsell_client') {

        //             $this->db->or_where('upsell_client', 'yes');

        //         }
        //         if($singleCategory == 'keycompany') {

        //             $this->db->or_where('keycompany', 'yes');

        //         }
        //         if($singleCategory == 'pkclient ') {

        //             $this->db->or_where('pkclient', 'yes'); 
        //         }
        //         if($singleCategory == 'priorityc ') {

        //             $this->db->or_where('priorityc', 'yes');
        //         }
        //     }  

        //     $this->db->group_end(); 
        // }

        //if user type is selected
        // if (!empty($selected_userType)) {
        //     $this->db->group_start();
        //     foreach ($selected_userType as $singleuserType) {
        //         if ($singleuserType == 4) {
        //             $this->db->or_where_in('pst_co', $selected_users);
        //         } 
        //         if($singleuserType == 9 || $singleuserType == 13) {
        //             $this->db->or_where_in('aadmin', $selected_users);
        //         }
        //     } 
        //     $this->db->group_end();  
        // }else{
        //     $this->db->where_in('admin_id', $uid);
        // }

        // if cluster is selected


        if ($userTypeid == 2) {
            //if admin is loggedin
            $this->db->where('admin_id', $uid);
        } elseif ($userTypeid == 4) {
            //if PST is loggedin
            $this->db->where('pst_co', $uid);
        } elseif ($userTypeid == 9) {
            //if BDPST is loggedin
            $this->db->where('aadmin', $uid);
        } elseif ($userTypeid == 13) {
            //if CLM is loggedin
            $this->db->where('admin_id', $uid);
        } else {
            //if BD is loggedin
            $this->db->where('mainbd', $uid);
        }

        $this->db->where('user_details.status', 'active');
        $this->db->where('company_master.partnerType_id', $PartnerMasterID);

        $this->db->where('CAST(ic.createDate AS DATE) >=', "'$sdate'", FALSE);
        $this->db->where('CAST(ic.createDate AS DATE) <=', "'$edate'", FALSE);
        // $this->db->where('tblcallevents.remarks !=', '');
        $this->db->group_by('ic.cmpid_id');
        // $this->db->group_by('stname');
        $query = $this->db->get();

        // echo $this->db->last_query();die;
        return $query->result();
    }
    public function getCategoryWiseGraphDetails($uid, $userTypeid, $sdate, $edate, $category)
    {

            // print_r($category);die;

        // Initialize the base select part
            $select = " COUNT(CASE WHEN topspender != 'yes' AND focus_funnel != 'yes' AND upsell_client != 'yes' AND keycompany != 'yes' AND pkclient != 'yes' AND priorityc != 'yes' THEN 1 END) AS nocat ";

            // Add dynamic conditions based on the categories array
            foreach ($category as $Singlecategory) {
            $select .= ", COUNT(CASE WHEN $Singlecategory = 'yes' THEN 1 END) AS $Singlecategory";
            }

            // Set the select part of the query
            $this->db->select($select);


        // $this->db->select("
        //         COUNT(CASE WHEN topspender != 'yes' AND focus_funnel != 'yes' AND upsell_client != 'yes' AND keycompany != 'yes' AND pkclient != 'yes' AND priorityc != 'yes' THEN 1 END) AS nocat,
        //         COUNT(CASE WHEN topspender = 'yes' THEN 1 END) AS topspender,
        //         COUNT(CASE WHEN focus_funnel = 'yes' THEN 1 END) AS focus_funnel,
        //         COUNT(CASE WHEN upsell_client = 'yes' THEN 1 END) AS upsell_client,
        //         COUNT(CASE WHEN keycompany = 'yes' THEN 1 END) AS keycompany,
        //         COUNT(CASE WHEN pkclient = 'yes' THEN 1 END) AS pkclient,
        //         COUNT(CASE WHEN priorityc = 'yes' THEN 1 END) AS priorityc
        //     ");


        $this->db->from('init_call ic');
        $this->db->join('user_details', 'user_details.user_id = ic.mainbd', 'left');
        $this->db->join('company_master', 'company_master.id = ic.cmpid_id', 'left');
        $this->db->join('partner_master', 'partner_master.id = company_master.partnerType_id', 'left');
        
        // if ($userTypeid == 2) {

        //     $this->db->where('user_details.admin_id', $uid);
        // } elseif ($userTypeid == 4) {

        //     $this->db->where_in('pst_co', $uid);
        // } elseif ($userTypeid == 9 || $userTypeid == 13) {

        //     $this->db->where_in('aadmin', $uid);
        // } else {
        //     $this->db->where('user_id', $uid);
        // }
        
        if (!empty($userTypeid)) {
            $this->db->group_start();
            foreach ($userTypeid as $singleuserType) {
                if ($singleuserType == 4) {
                    $this->db->or_where_in('user_details.pst_co', $uid);
                }
                if ($singleuserType == 9 || $singleuserType == 13) {
                    $this->db->or_where_in('user_details.aadmin', $uid);
                }
                else{

                    $this->db->where_in('user_details.user_id', $uid);
                }
            }
            $this->db->group_end();
        }

        if ($category != '') {

            $this->db->group_start();

            foreach ($category as $singleCategory) {

                if ($singleCategory == 'topspender') {

                    $this->db->or_where('topspender', 'yes');
                }
                if ($singleCategory == 'focus_funnel') {

                    $this->db->or_where('focus_funnel', 'yes');
                }
                if ($singleCategory == 'upsell_client') {

                    $this->db->or_where('upsell_client', 'yes');
                }
                if ($singleCategory == 'keycompany') {

                    $this->db->or_where('keycompany', 'yes');
                }
                if ($singleCategory == 'pkclient ') {

                    $this->db->or_where('pkclient', 'yes');
                }
                if ($singleCategory == 'priorityc ') {

                    $this->db->or_where('priorityc', 'yes');
                }
            }

            $this->db->group_end();
        }


        $this->db->where('CAST(ic.createDate AS DATE) >=', "'$sdate'", FALSE);
        $this->db->where('CAST(ic.createDate AS DATE) <=', "'$edate'", FALSE);

        $query = $this->db->get();
        // echo $this->db->last_query();die;
        return $query->result();
    }

    public function getCategoryWiseTableDetails($uid, $userTypeid, $sdate, $edate, $category)
    {

        $this->db->select('ic.id ic_id');
        $this->db->select('ic.topspender topspender');
        $this->db->select('ic.focus_funnel focus_funnel');
        $this->db->select('ic.upsell_client upsell_client');
        $this->db->select('ic.keycompany keycompany');
        $this->db->select('ic.pkclient pkclient');
        $this->db->select('ic.priorityc priorityc');
        $this->db->select('status.clr stclr');
        $this->db->select('status.id stid');
        $this->db->select('status.name stname');
        $this->db->select('company_master.compname company_name');
        $this->db->select('company_master.address company_address');
        $this->db->select('city.city city');
        $this->db->select('states.state state');
        $this->db->select('partner_master.name partner_typeName');
        $this->db->select('partner_master.id partner_typeID');
        $this->db->select('partner_master.clr PartnerMasterclr');
        $this->db->from('init_call ic');
        $this->db->join('company_master', 'company_master.id = ic.cmpid_id', 'left');
        $this->db->join('city', 'city.id = company_master.city', 'left');
        $this->db->join('states', 'states.id = company_master.state', 'left');
        $this->db->join('partner_master', 'partner_master.id = company_master.partnerType_id', 'left');
        $this->db->join('status', 'status.id = ic.cstatus', 'left');
        $this->db->join('user_details', 'user_details.user_id = ic.mainbd', 'left');


        // if ($userTypeid == 2) {

        //     $this->db->where('user_details.admin_id', $uid);
        // } elseif ($userTypeid == 4) {

        //     $this->db->where_in('pst_co', $uid);
        // } elseif ($userTypeid == 9 || $userTypeid == 13) {

        //     $this->db->where_in('aadmin', $uid);
        // } else {
        //     $this->db->where('user_id', $uid);
        // }
        
        
        if (!empty($userTypeid)) {
            $this->db->group_start();
            foreach ($userTypeid as $singleuserType) {
                if ($singleuserType == 4) {
                    $this->db->or_where_in('user_details.pst_co', $uid);
                }
                if ($singleuserType == 9 || $singleuserType == 13) {
                    $this->db->or_where_in('user_details.aadmin', $uid);
                }
                else{

                    $this->db->where_in('user_details.user_id', $uid);
                }
            }
            $this->db->group_end();
        }

        
        if (!empty($category)) {

            $this->db->group_start();

            foreach ($category as $singleCategory) {

                if ($singleCategory == 'topspender') {

                    $this->db->or_where('topspender', 'yes');
                }
                if ($singleCategory == 'focus_funnel') {

                    $this->db->or_where('focus_funnel', 'yes');
                }
                if ($singleCategory == 'upsell_client') {

                    $this->db->or_where('upsell_client', 'yes');
                }
                if ($singleCategory == 'keycompany') {

                    $this->db->or_where('keycompany', 'yes');
                }
                if ($singleCategory == 'pkclient ') {

                    $this->db->or_where('pkclient', 'yes');
                }
                if ($singleCategory == 'priorityc ') {

                    $this->db->or_where('priorityc', 'yes');
                }
            }
            $this->db->group_end();
        }

        $this->db->where('CAST(ic.createDate AS DATE) >=', "'$sdate'", FALSE);
        $this->db->where('CAST(ic.createDate AS DATE) <=', "'$edate'", FALSE);

        $query = $this->db->get();
        // echo $this->db->last_query();die;

        return $query->result();
    }

    public function getTableDataByCategory($uid, $userTypeid, $Category, $sdate, $edate)
    {
        // print_r($selected_partnerType[0]);die;
        $this->db->select('ic.id ic_id');
        $this->db->select('ic.focus_funnel focus_funnel');
        $this->db->select('ic.upsell_client upsell_client');
        $this->db->select('ic.keycompany keycompany');
        $this->db->select('ic.pkclient pkclient');
        $this->db->select('ic.priorityc priorityc');
        $this->db->select('ic.topspender topspender');
        $this->db->select('status.clr stclr');
        $this->db->select('status.id stid');
        $this->db->select('status.name stname');
        $this->db->select('company_master.compname company_name');
        $this->db->select('company_master.address company_address');
        $this->db->select('city.city city');
        $this->db->select('states.state state');
        $this->db->select('partner_master.name partner_type');
        $this->db->from('init_call ic');
        $this->db->join('company_master', 'company_master.id = ic.cmpid_id', 'left');
        $this->db->join('city', 'city.id = company_master.city', 'left');
        $this->db->join('states', 'states.id = company_master.state', 'left');
        $this->db->join('partner_master', 'partner_master.id = company_master.partnerType_id', 'left');
        $this->db->join('status', 'status.id = ic.cstatus', 'left');
        $this->db->join('user_details', 'user_details.user_id = ic.mainbd', 'left');

        // if (!empty($selected_partnerType)) {

        //     $this->db->where_in('company_master.partnerType_id', $selected_partnerType);
        // }

        // if (!empty($selected_category)) {
        //     // var_dump($category);die;
        //     $this->db->group_start();

        //     foreach ($selected_category as $singleCategory) {

        //         if ($singleCategory == 'topspender') {

        //             $this->db->or_where('focus_funnel', 'yes');

        //         }
        //         if($singleCategory == 'focus_funnel') {

        //             $this->db->or_where('focus_funnel', 'yes');

        //         }
        //         if($singleCategory == 'upsell_client') {

        //             $this->db->or_where('upsell_client', 'yes');

        //         }
        //         if($singleCategory == 'keycompany') {

        //             $this->db->or_where('keycompany', 'yes');

        //         }
        //         if($singleCategory == 'pkclient ') {

        //             $this->db->or_where('pkclient', 'yes'); 
        //         }
        //         if($singleCategory == 'priorityc ') {

        //             $this->db->or_where('priorityc', 'yes');
        //         }
        //     }  

        //     $this->db->group_end(); 
        // }

        //if user type is selected
        // if (!empty($selected_userType)) {
        //     $this->db->group_start();
        //     foreach ($selected_userType as $singleuserType) {
        //         if ($singleuserType == 4) {
        //             $this->db->or_where_in('pst_co', $selected_users);
        //         } 
        //         if($singleuserType == 9 || $singleuserType == 13) {
        //             $this->db->or_where_in('aadmin', $selected_users);
        //         }
        //     } 
        //     $this->db->group_end();  
        // }else{
        //     $this->db->where_in('admin_id', $uid);
        // }

        // if cluster is selected

        
        if ($Category == 'focus_funnel') {

            $this->db->where('ic.focus_funnel', 'yes');
        } elseif ($Category == 'upsell_client') {

            $this->db->where('ic.upsell_client', 'yes');
        } elseif ($Category == 'keycompany') {

            $this->db->where('ic.keycompany', 'yes');
        } elseif ($Category == 'pkclient') {

            $this->db->where('ic.pkclient', 'yes');
        } elseif ($Category == 'topspender') {

            $this->db->where('ic.topspender', 'yes');
        } elseif ($Category == 'priorityc') {

            $this->db->where('ic.priorityc', 'yes');
        }


        if ($userTypeid == 2) {
            //if admin is loggedin
            $this->db->where('admin_id', $uid);
        } elseif ($userTypeid == 4) {
            //if PST is loggedin
            $this->db->where('pst_co', $uid);
        } elseif ($userTypeid == 9) {
            //if BDPST is loggedin
            $this->db->where('aadmin', $uid);
        } elseif ($userTypeid == 13) {
            //if CLM is loggedin
            $this->db->where('admin_id', $uid);
        } else {
            //if BD is loggedin
            $this->db->where('mainbd', $uid);
        }

        $this->db->where('user_details.status', 'active');
        // $this->db->where('company_master.partnerType_id', $PartnerMasterID );

        $this->db->where('CAST(ic.createDate AS DATE) >=', "'$sdate'", FALSE);
        $this->db->where('CAST(ic.createDate AS DATE) <=', "'$edate'", FALSE);
        // $this->db->where('tblcallevents.remarks !=', '');
        $this->db->group_by('ic.cmpid_id');
        // $this->db->group_by('stname');
        $query = $this->db->get();

        return $query->result();
    }

    public function getCompanyWithSameStatusTableDetails($uid, $userTypeid, $sdate, $edate, $SelectedStatus, $SelectedCluster, $SelectedCategory, $SelectedUsers)
    {

        $subquery = $this->db->select('ic.id')
            ->from('init_call ic')
            ->join('user_details', 'user_details.user_id = ic.mainbd', 'left')
            ->where('CAST(ic.createDate AS DATE) >=', "'$sdate'", FALSE)
            ->where('CAST(ic.createDate AS DATE) <=', "'$edate'", FALSE);

        // if ($userTypeid == 2) {

        //     $this->db->where('user_details.admin_id', $uid);
        // } elseif ($userTypeid == 4) {

        //     $this->db->where_in('pst_co', $uid);
        // }elseif ($userTypeid == 15) {

        //     $this->db->where_in('sales_co', $uid);
        // } elseif ($userTypeid == 9 || $userTypeid == 13) {

        //     $this->db->where_in('aadmin', $uid);
        // } else {

        //     $this->db->where('user_id', $uid);
        // }
        
        if ($userTypeid == 2) {

            $this->db->where('user_details.admin_id', $uid);
        } elseif ($userTypeid == 4) {

            $this->db->where_in('pst_co', $uid);
        } elseif ($userTypeid == 9 || $userTypeid == 13) {

            $this->db->where_in('aadmin', $uid);
        } else {
            $this->db->where_in('user_id', $uid);
        }

        $subquery = $subquery->get_compiled_select();

        $this->db->select('ic1.id ic_id');
        $this->db->select('ic1.topspender topspender');
        $this->db->select('ic1.focus_funnel focus_funnel');
        $this->db->select('ic1.upsell_client upsell_client');
        $this->db->select('ic1.keycompany keycompany');
        $this->db->select('ic1.pkclient pkclient');
        $this->db->select('ic1.priorityc priorityc');
        $this->db->select('status.clr stclr');
        $this->db->select('status.id stid');
        $this->db->select('status.name stname');
        $this->db->select('company_master.compname company_name');
        $this->db->select('company_master.address company_address');
        $this->db->select('city.city city');
        $this->db->select('states.state state');
        $this->db->select('partner_master.name partner_typeName');
        $this->db->select('partner_master.id partner_typeID');
        $this->db->select('partner_master.clr PartnerMasterclr');
        $this->db->from('init_call ic1');
        $this->db->join('company_master', 'company_master.id = ic1.cmpid_id', 'left');
        $this->db->join('city', 'city.id = company_master.city', 'left');
        $this->db->join('states', 'states.id = company_master.state', 'left');
        $this->db->join('partner_master', 'partner_master.id = company_master.partnerType_id', 'left');
        // $this->db->join('status', 'status.id = ic.cstatus', 'left');
        $this->db->join('user_details', 'user_details.user_id = ic1.mainbd', 'left');

        $this->db->join('tblcallevents', 'ic1.id = tblcallevents.cid_id', 'LEFT');
        $this->db->join('status', 'ic1.cstatus = status.id', 'LEFT');

        $this->db->where("tblcallevents.cid_id IN ($subquery)", NULL, FALSE);
        $this->db->where('tblcallevents.nextCFID !=', '0');

        if (!empty($SelectedStatus)) {

            $this->db->where_in('ic1.cstatus', $SelectedStatus);
        }

        if (!empty($SelectedpartnerType)) {

            $this->db->where_in('company_master.partnerType_id', $SelectedpartnerType);
        }

        // if (!empty($SelectedUsers)) {

        //     $this->db->where_in('user_details.aadmin', $SelectedUsers);
        // }

        if (!empty($SelectedCategory)) {

            $this->db->group_start();

            foreach ($SelectedCategory as $singleCategory) {

                if ($singleCategory == 'topspender') {

                    $this->db->or_where('ic1.topspender', 'yes');
                }
                if ($singleCategory == 'focus_funnel') {

                    $this->db->or_where('ic1.focus_funnel', 'yes');
                }
                if ($singleCategory == 'upsell_client') {

                    $this->db->or_where('ic1.upsell_client', 'yes');
                }
                if ($singleCategory == 'keycompany') {

                    $this->db->or_where('ic1.keycompany', 'yes');
                }
                if ($singleCategory == 'pkclient ') {

                    $this->db->or_where('ic1.pkclient', 'yes');
                }
                if ($singleCategory == 'priorityc ') {

                    $this->db->or_where('ic1.priorityc', 'yes');
                }
            }

            $this->db->group_end();
        }

        $this->db->group_by('ic1.id');

        $query = $this->db->get();
        // echo $this->db->last_query();die;
        return $query->result();
    }

    public function SameStatusTillDateByStatus($uid, $userTypeid, $sdate, $edate, $status, $SelectedCluster, $SelectedCategory, $SelectedUsers, $SelectedpartnerType)
    {

        $subquery = $this->db->select('ic.id')
            ->from('init_call ic')
            ->join('user_details', 'user_details.user_id = ic.mainbd', 'left')
            ->where('CAST(init_call.createDate AS DATE) >=', "'$sdate'", FALSE)
            ->where('CAST(init_call.createDate AS DATE) <=', "'$edate'", FALSE);

        if ($userTypeid == 2) {

            $this->db->where('user_details.admin_id', $uid);
        } elseif ($userTypeid == 4) {

            $this->db->where_in('pst_co', $uid);
        } elseif ($userTypeid == 15) {

            $this->db->where_in('sales_co', $uid);
        } elseif ($userTypeid == 9 || $userTypeid == 13) {

            $this->db->where_in('aadmin', $uid);
        } else {

            $this->db->where_in('user_id', $uid);
        }

        $subquery = $subquery->get_compiled_select();

        $this->db->select("TIMESTAMPDIFF(DAY, MAX(tblcallevents.updateddate), NOW()) AS opensday");
        $this->db->select("status.name");
        $this->db->from('tblcallevents');
        $this->db->join('init_call', 'init_call.id = tblcallevents.cid_id', 'LEFT');
        $this->db->join('status', 'init_call.cstatus = status.id', 'LEFT');
        $this->db->join('company_master', 'init_call.cmpid_id = company_master.id', 'LEFT');
        $this->db->join('user_details ud', 'ud.user_id = init_call.mainbd', 'left');
        $this->db->where("tblcallevents.cid_id IN ($subquery)", NULL, FALSE);
        $this->db->where('tblcallevents.nextCFID !=', '0');
        $this->db->where('init_call.cstatus', $status);

        if (!empty($SelectedpartnerType)) {

            $this->db->where_in('company_master.partnerType_id', $SelectedpartnerType);
        }

        // if (!empty($SelectedUsers)) {

        //     $this->db->where_in('ud.aadmin', $SelectedUsers);
        // }

        if (!empty($SelectedCategory)) {

            $this->db->group_start();

            foreach ($SelectedCategory as $singleCategory) {

                if ($singleCategory == 'topspender') {

                    $this->db->or_where('focus_funnel', 'yes');
                }
                if ($singleCategory == 'focus_funnel') {

                    $this->db->or_where('focus_funnel', 'yes');
                }
                if ($singleCategory == 'upsell_client') {

                    $this->db->or_where('upsell_client', 'yes');
                }
                if ($singleCategory == 'keycompany') {

                    $this->db->or_where('keycompany', 'yes');
                }
                if ($singleCategory == 'pkclient ') {

                    $this->db->or_where('pkclient', 'yes');
                }
                if ($singleCategory == 'priorityc ') {

                    $this->db->or_where('priorityc', 'yes');
                }
            }

            $this->db->group_end();
        }

        $this->db->group_by('init_call.id');

        // Execute the query
        $query = $this->db->get();
        // echo $this->db->last_query();die;
        return $query->result();
    }

    public function getTableDataOfCompanyWithSameStatus($uid, $userTypeid, $selectedStatus, $sdate, $edate, $selected_partnerType, $selected_category, $selected_userType, $selected_users, $selected_cluster)
    {

        $subquery = $this->db->select('ic.id')
            ->from('init_call ic')
            ->join('user_details', 'user_details.user_id = ic.mainbd', 'left')
            ->where('CAST(ic.createDate AS DATE) >=', "'$sdate'", FALSE)
            ->where('CAST(ic.createDate AS DATE) <=', "'$edate'", FALSE);

        // if ($userTypeid == 2) {

        //     $this->db->where('user_details.admin_id', $uid);
        // } elseif ($userTypeid == 4) {

        //     $this->db->where_in('pst_co', $uid);
        // } elseif ($userTypeid == 9 || $userTypeid == 13) {

        //     $this->db->where_in('aadmin', $uid);
        // } else {

        //     $this->db->where('user_id', $uid);
        // }
        
        if ($userTypeid == 2) {

            $this->db->where('user_details.admin_id', $uid);
        } elseif ($userTypeid == 4) {

            $this->db->where_in('pst_co', $uid);
        } elseif ($userTypeid == 9 || $userTypeid == 13) {

            $this->db->where_in('aadmin', $uid);
        } else {
            $this->db->where('user_id', $uid);
        }

        $subquery = $subquery->get_compiled_select();

        $this->db->select('ic1.id ic_id');
        $this->db->select('ic1.topspender topspender');
        $this->db->select('ic1.focus_funnel focus_funnel');
        $this->db->select('ic1.upsell_client upsell_client');
        $this->db->select('ic1.keycompany keycompany');
        $this->db->select('ic1.pkclient pkclient');
        $this->db->select('ic1.priorityc priorityc');
        $this->db->select('status.clr stclr');
        $this->db->select('status.id stid');
        $this->db->select('status.name stname');
        $this->db->select('company_master.compname company_name');
        $this->db->select('company_master.address company_address');
        $this->db->select('city.city city');
        $this->db->select('states.state state');
        $this->db->select('partner_master.name partner_typeName');
        $this->db->select('partner_master.id partner_typeID');
        $this->db->select('partner_master.clr PartnerMasterclr');
        $this->db->from('init_call ic1');
        $this->db->join('company_master', 'company_master.id = ic1.cmpid_id', 'left');
        $this->db->join('city', 'city.id = company_master.city', 'left');
        $this->db->join('states', 'states.id = company_master.state', 'left');
        $this->db->join('partner_master', 'partner_master.id = company_master.partnerType_id', 'left');
        // $this->db->join('status', 'status.id = ic.cstatus', 'left');
        $this->db->join('user_details', 'user_details.user_id = ic1.mainbd', 'left');

        $this->db->join('tblcallevents', 'ic1.id = tblcallevents.cid_id', 'LEFT');
        $this->db->join('status', 'ic1.cstatus = status.id', 'LEFT');

        $this->db->where("tblcallevents.cid_id IN ($subquery)", NULL, FALSE);
        $this->db->where('tblcallevents.nextCFID !=', '0');
        // $this->db->where('ic1.cstatus', $selectedStatus);

        if (!empty($selectedStatus)) {

            $this->db->where('ic1.cstatus', $selectedStatus);
        }

        if (!empty($selected_partnerType)) {

            $this->db->where_in('company_master.partnerType_id', $selected_partnerType);
        }

        // if (!empty($selected_users)) {

        //     $this->db->where_in('user_details.aadmin', $selected_users);
        // }

        if (!empty($selected_category)) {

            $this->db->group_start();

            foreach ($selected_category as $singleCategory) {

                if ($singleCategory == 'topspender') {

                    $this->db->or_where('ic1.focus_funnel', 'yes');
                }
                if ($singleCategory == 'focus_funnel') {

                    $this->db->or_where('ic1.focus_funnel', 'yes');
                }
                if ($singleCategory == 'upsell_client') {

                    $this->db->or_where('ic1.upsell_client', 'yes');
                }
                if ($singleCategory == 'keycompany') {

                    $this->db->or_where('ic1.keycompany', 'yes');
                }
                if ($singleCategory == 'pkclient ') {

                    $this->db->or_where('ic1.pkclient', 'yes');
                }
                if ($singleCategory == 'priorityc ') {

                    $this->db->or_where('ic1.priorityc', 'yes');
                }
            }

            $this->db->group_end();
        }

        $this->db->group_by('ic1.id');

        $query = $this->db->get();

        // echo $this->db->last_query();die;
        return $query->result();
    }

    public function getStageWiseFunnelGraphDetails($uid, $userTypeid, $sdate, $edate, $status, $SelectedCluster, $SelectedCategory, $SelectedUsers, $SelectedpartnerType,$userType)
    {

        $this->db->select("
            SUM(CASE WHEN init_call.cstatus = '1' THEN 1 ELSE 0 END) +
            SUM(CASE WHEN init_call.cstatus = '8' THEN 1 ELSE 0 END) +
            SUM(CASE WHEN init_call.cstatus = '2' THEN 1 ELSE 0 END) +
            SUM(CASE WHEN init_call.cstatus = '10' THEN 1 ELSE 0 END) +
            SUM(CASE WHEN init_call.cstatus = '11' THEN 1 ELSE 0 END) AS stage1, 
            SUM(CASE WHEN init_call.cstatus = '3' THEN 1 ELSE 0 END) AS stage2,
            SUM(CASE WHEN init_call.cstatus = '6' THEN 1 ELSE 0 END) +
            SUM(CASE WHEN init_call.cstatus = '9' THEN 1 ELSE 0 END) +
            SUM(CASE WHEN init_call.cstatus = '12' THEN 1 ELSE 0 END) +
            SUM(CASE WHEN init_call.cstatus = '13' THEN 1 ELSE 0 END) AS stage3,
            SUM(CASE WHEN init_call.cstatus = '7' THEN 1 ELSE 0 END) AS stage4
        ");

        //stage 1 -> Open,Reachout,OPEN RPEM,TTD-Reachout,WNO-Reachout
        //stage 2 -> tentative
        //stage 3 -> positive
        //stage 4 -> Closuer

        $this->db->from('init_call');
        $this->db->join('user_details', 'user_details.user_id = init_call.mainbd', 'LEFT');
        $this->db->join('status', 'status.id = init_call.cstatus', 'LEFT');
        $this->db->join('company_master', 'init_call.cmpid_id = company_master.id', 'LEFT');

        if (!empty($SelectedCategory)) {

            $this->db->group_start();

            foreach ($SelectedCategory as $singleCategory) {

                if ($singleCategory == 'topspender') {

                    $this->db->or_where('focus_funnel', 'yes');
                }
                if ($singleCategory == 'focus_funnel') {

                    $this->db->or_where('focus_funnel', 'yes');
                }
                if ($singleCategory == 'upsell_client') {

                    $this->db->or_where('upsell_client', 'yes');
                }
                if ($singleCategory == 'keycompany') {

                    $this->db->or_where('keycompany', 'yes');
                }
                if ($singleCategory == 'pkclient ') {

                    $this->db->or_where('pkclient', 'yes');
                }
                if ($singleCategory == 'priorityc ') {

                    $this->db->or_where('priorityc', 'yes');
                }
            }

            $this->db->group_end();
        }

        if (!empty($SelectedpartnerType)) {

            $this->db->where_in('company_master.partnerType_id', $SelectedpartnerType);
        }

        if (!empty($userType)) {
            $this->db->group_start();
            foreach ($userType as $singleuserType) {

                if ($singleuserType == 4) {

                    $this->db->or_where_in('pst_co', $SelectedUsers);
                }
                if ($singleuserType == 9 || $singleuserType == 13) {

                    $this->db->or_where_in('aadmin', $SelectedUsers);
                }
                if ($singleuserType == 15) {

                    $this->db->or_where_in('sales_co', $SelectedUsers);
                }
                else{

                    $this->db->where_in('user_id', $SelectedUsers);
                }
            }
            $this->db->group_end();
        }

        if ($userTypeid == 2) {
            //if admin is loggedin
            $this->db->where('admin_id', $uid);
        } elseif ($userTypeid == 4) {
            //if PST is loggedin
            $this->db->where('pst_co', $uid);
        } elseif ($userTypeid == 9) {
            //if BDPST is loggedin
            $this->db->where('aadmin', $uid);
        } elseif ($userTypeid == 13) {
            //if CLM is loggedin
            $this->db->where('aadmin', $uid);
        } elseif ($userTypeid == 15) {
            //if CLM is loggedin
            $this->db->where('sales_co', $uid);
        } 
        else {
            //if BD is loggedin
            $this->db->where('mainbd', $uid);
        }
        
        // if (!empty($SelectedUsers)) {

        //     $this->db->where_in('user_details.aadmin', $SelectedUsers);
        // }

        $this->db->where('CAST(init_call.createDate AS DATE) >=', "'$sdate'", FALSE);
        $this->db->where('CAST(init_call.createDate AS DATE) <=', "'$edate'", FALSE);
        $query = $this->db->get();

        // echo $this->db->last_query();die;
        return $query->result();
    }

     public function getFunnelDetails($uid, $userTypeid, $sdate, $edate, $status, $SelectedCluster, $SelectedCategory, $SelectedUsers, $SelectedpartnerType, $statusArray)
    {
        // var_dump($statusArray);die;
        $this->db->select('status.name statusName');
        $this->db->select('status.id statusId');
        $this->db->select('COUNT(*) cont');

        $this->db->from('init_call');
        $this->db->join('user_details', 'user_details.user_id = init_call.mainbd', 'left');
        $this->db->join('status', 'status.id = init_call.cstatus', 'left');
        $this->db->join('company_master', 'init_call.cmpid_id = company_master.id', 'LEFT');


        if (!empty($SelectedCategory)) {

            $this->db->group_start();

            foreach ($SelectedCategory as $singleCategory) {

                if ($singleCategory == 'topspender') {

                    $this->db->or_where('focus_funnel', 'yes');
                }
                if ($singleCategory == 'focus_funnel') {

                    $this->db->or_where('focus_funnel', 'yes');
                }
                if ($singleCategory == 'upsell_client') {

                    $this->db->or_where('upsell_client', 'yes');
                }
                if ($singleCategory == 'keycompany') {

                    $this->db->or_where('keycompany', 'yes');
                }
                if ($singleCategory == 'pkclient ') {

                    $this->db->or_where('pkclient', 'yes');
                }
                if ($singleCategory == 'priorityc ') {

                    $this->db->or_where('priorityc', 'yes');
                }
            }

            $this->db->group_end();
        }

        if (!empty($SelectedpartnerType)) {

            $this->db->where_in('company_master.partnerType_id', $SelectedpartnerType);
        }

        if ($userTypeid == 2) {

            $this->db->where('user_details.admin_id', $uid);
        } elseif ($userTypeid == 4) {

            $this->db->where_in('pst_co', $uid);
        } elseif ($userTypeid == 9 || $userTypeid == 13) {

            $this->db->where_in('aadmin', $uid);
        }elseif ($userTypeid == 15) {

            $this->db->or_where_in('sales_co', $uid);
        } else {

            $this->db->where('user_id', $uid);
        }

        // if (!empty($SelectedUsers)) {

        //     $this->db->where_in('user_details.aadmin', $SelectedUsers);
        // }


        $this->db->where_in('init_call.cstatus', $statusArray);
        $this->db->where('CAST(init_call.createDate AS DATE) >=', "'$sdate'", FALSE);
        $this->db->where('CAST(init_call.createDate AS DATE) <=', "'$edate'", FALSE);
        $this->db->group_by('statusName');
        $query = $this->db->get();
        // echo $this->db->last_query();
        return $query->result();
    }

    public function getStageWiseFunnelTableDetails($uid, $userTypeid, $sdate, $edate, $SelectedStatus, $SelectedCluster, $SelectedCategory, $SelectedUsers, $SelectedpartnerType,$userType)
    {

        $this->db->select('ic.id ic_id');
        $this->db->select('ic.topspender topspender');
        $this->db->select('ic.focus_funnel focus_funnel');
        $this->db->select('ic.upsell_client upsell_client');
        $this->db->select('ic.keycompany keycompany');
        $this->db->select('ic.pkclient pkclient');
        $this->db->select('ic.priorityc priorityc');
        $this->db->select('status.clr stclr');
        $this->db->select('status.id stid');
        $this->db->select('status.name stname');
        $this->db->select('company_master.compname company_name');
        $this->db->select('company_master.address company_address');
        $this->db->select('city.city city');
        $this->db->select('states.state state');
        $this->db->select('partner_master.name partner_typeName');
        $this->db->select('partner_master.id partner_typeID');
        $this->db->select('partner_master.clr PartnerMasterclr');
        $this->db->from('init_call ic');
        $this->db->join('company_master', 'company_master.id = ic.cmpid_id', 'left');
        $this->db->join('city', 'city.id = company_master.city', 'left');
        $this->db->join('states', 'states.id = company_master.state', 'left');
        $this->db->join('partner_master', 'partner_master.id = company_master.partnerType_id', 'left');
        $this->db->join('status', 'status.id = ic.cstatus', 'left');
        $this->db->join('user_details', 'user_details.user_id = ic.mainbd', 'left');

        if (!empty($SelectedCategory)) {

            $this->db->group_start();

            foreach ($SelectedCategory as $singleCategory) {

                if ($singleCategory == 'topspender') {

                    $this->db->or_where('focus_funnel', 'yes');
                }
                if ($singleCategory == 'focus_funnel') {

                    $this->db->or_where('focus_funnel', 'yes');
                }
                if ($singleCategory == 'upsell_client') {

                    $this->db->or_where('upsell_client', 'yes');
                }
                if ($singleCategory == 'keycompany') {

                    $this->db->or_where('keycompany', 'yes');
                }
                if ($singleCategory == 'pkclient ') {

                    $this->db->or_where('pkclient', 'yes');
                }
                if ($singleCategory == 'priorityc ') {

                    $this->db->or_where('priorityc', 'yes');
                }
            }

            $this->db->group_end();
        }

        if (!empty($SelectedpartnerType)) {

            $this->db->where_in('company_master.partnerType_id', $SelectedpartnerType);
        }


        if ($userTypeid == 2) {

            $this->db->where('user_details.admin_id', $uid);
        } elseif ($userTypeid == 4) {

            $this->db->where_in('pst_co', $uid);
        } elseif ($userTypeid == 9 || $userTypeid == 13) {

            $this->db->where_in('aadmin', $uid);
        }elseif ($userTypeid == 15) {

            $this->db->or_where_in('sales_co', $uid);
        } else {
            $this->db->where('user_id', $uid);
        }


        if (!empty($userType)) {
            $this->db->group_start();
            foreach ($userType as $singleuserType) {
                if ($singleuserType == 4) {
                    $this->db->or_where_in('pst_co', $SelectedUsers);
                }
                if ($singleuserType == 9 || $singleuserType == 13) {
                    $this->db->or_where_in('aadmin', $SelectedUsers);
                }
                else{

                    $this->db->where_in('user_id', $SelectedUsers);
                }
            }
            $this->db->group_end();
        }


        // if (!empty($SelectedUsers)) {

        //     $this->db->where_in('user_details.aadmin', $SelectedUsers);
        // }


        $this->db->where('CAST(ic.createDate AS DATE) >=', "'$sdate'", FALSE);
        $this->db->where('CAST(ic.createDate AS DATE) <=', "'$edate'", FALSE);

        $query = $this->db->get();
        // echo $this->db->last_query();die;
        return $query->result();
    }

    
    public function getTableDataStageWiseFunnel($uid, $userTypeid, $selectedStatus, $sdate, $edate, $selected_partnerType, $selected_category, $selected_users, $selected_cluster)
    {

        $this->db->select('ic.id ic_id');
        $this->db->select('ic.topspender topspender');
        $this->db->select('ic.focus_funnel focus_funnel');
        $this->db->select('ic.upsell_client upsell_client');
        $this->db->select('ic.keycompany keycompany');
        $this->db->select('ic.pkclient pkclient');
        $this->db->select('ic.priorityc priorityc');
        $this->db->select('status.clr stclr');
        $this->db->select('status.id stid');
        $this->db->select('status.name stname');
        $this->db->select('company_master.compname company_name');
        $this->db->select('company_master.address company_address');
        $this->db->select('city.city city');
        $this->db->select('states.state state');
        $this->db->select('partner_master.name partner_typeName');
        $this->db->select('partner_master.id partner_typeID');
        $this->db->select('partner_master.clr PartnerMasterclr');
        $this->db->from('init_call ic');
        $this->db->join('company_master', 'company_master.id = ic.cmpid_id', 'left');
        $this->db->join('city', 'city.id = company_master.city', 'left');
        $this->db->join('states', 'states.id = company_master.state', 'left');
        $this->db->join('partner_master', 'partner_master.id = company_master.partnerType_id', 'left');
        $this->db->join('status', 'status.id = ic.cstatus', 'left');
        $this->db->join('user_details', 'user_details.user_id = ic.mainbd', 'left');

        if (!empty($selected_category)) {

            $this->db->group_start();

            foreach ($selected_category as $singleCategory) {

                if ($singleCategory == 'topspender') {

                    $this->db->or_where('focus_funnel', 'yes');
                }
                if ($singleCategory == 'focus_funnel') {

                    $this->db->or_where('focus_funnel', 'yes');
                }
                if ($singleCategory == 'upsell_client') {

                    $this->db->or_where('upsell_client', 'yes');
                }
                if ($singleCategory == 'keycompany') {

                    $this->db->or_where('keycompany', 'yes');
                }
                if ($singleCategory == 'pkclient ') {

                    $this->db->or_where('pkclient', 'yes');
                }
                if ($singleCategory == 'priorityc ') {

                    $this->db->or_where('priorityc', 'yes');
                }
            }

            $this->db->group_end();
        }

        if (!empty($selected_partnerType)) {

            $this->db->where_in('company_master.partnerType_id', $selected_partnerType);
        }

        $this->db->where_in('ic.cstatus', $selectedStatus);

        if ($userTypeid == 2) {

            $this->db->where('user_details.admin_id', $uid);
        } elseif ($userTypeid == 4) {

            $this->db->where_in('pst_co', $uid);
        } elseif ($userTypeid == 9 || $userTypeid == 13) {

            $this->db->where_in('aadmin', $uid);
        }elseif ($userTypeid == 15) {

            $this->db->where_in('sales_co', $uid);
        } else {
            $this->db->where('user_id', $uid);
        }

        if (!empty($selected_users)) {

            $this->db->where_in('user_details.aadmin', $selected_users);
        }

        $this->db->where('CAST(ic.createDate AS DATE) >=', "'$sdate'", FALSE);
        $this->db->where('CAST(ic.createDate AS DATE) <=', "'$edate'", FALSE);

        $query = $this->db->get();
        // echo $this->db->last_query();die;
        return $query->result();
    }

    public function StatusWiseTaskTableDetails($uid, $userTypeid, $sdate, $edate, $Selected_userType, $Selected_cluster, $Selected_partnerType, $Selected_category, $Selected_users)
    {

        $this->db->select('tblcallevents.*, company_master.id as cid,company_master.compname as compname, action.clr as aclr, s1.clr as bsclr, s2.clr as asclr, partner_master.clr as pclr, s1.name as bstatus, s1.name as astatus, action.name as acname, user_details.name as uname, partner_master.name as pname');
        $this->db->from('tblcallevents');
        $this->db->join('init_call', 'init_call.id = tblcallevents.cid_id', 'left');
        $this->db->join('status s3', 's3.id = init_call.cstatus', 'left');
        $this->db->join('user_details', 'user_details.user_id = init_call.mainbd', 'left');
        $this->db->join('company_master', 'company_master.id = init_call.cmpid_id', 'left');
        $this->db->join('status s1', 's1.id = tblcallevents.status_id', 'left');
        $this->db->join('status s2', 's2.id = tblcallevents.nstatus_id', 'left');
        $this->db->join('action', 'action.id = tblcallevents.actiontype_id', 'left');
        $this->db->join('partner_master', 'partner_master.id = company_master.partnerType_id', 'left');

        if (!empty($Selected_category)) {

            $this->db->group_start();

            foreach ($Selected_category as $singleCategory) {

                if ($singleCategory == 'topspender') {

                    $this->db->or_where('focus_funnel', 'yes');
                }
                if ($singleCategory == 'focus_funnel') {

                    $this->db->or_where('focus_funnel', 'yes');
                }
                if ($singleCategory == 'upsell_client') {

                    $this->db->or_where('upsell_client', 'yes');
                }
                if ($singleCategory == 'keycompany') {

                    $this->db->or_where('keycompany', 'yes');
                }
                if ($singleCategory == 'pkclient ') {

                    $this->db->or_where('pkclient', 'yes');
                }
                if ($singleCategory == 'priorityc ') {

                    $this->db->or_where('priorityc', 'yes');
                }
            }

            $this->db->group_end();
        }

        if (!empty($Selected_partnerType)) {

            $this->db->where_in('company_master.partnerType_id', $Selected_partnerType);
        }

        // $this->db->where('tblcallevents.user_id', $uid);
        // if (!empty($Selected_users)) {

        //     $this->db->where_in('user_details.aadmin', $Selected_users);
        // }

        // var_dump($Selected_users);die;
        if (!empty($Selected_userType)) {

            if ($Selected_userType == 2) {

                $this->db->where_in('user_details.admin_id', $Selected_users);
    
            } elseif ($Selected_userType == 4) {
    
                $this->db->where_in('user_details.pst_co', $Selected_users);

            } elseif ($Selected_userType == 9 || $Selected_userType == 13) {
    
                $this->db->where_in('user_details.aadmin', $Selected_users);

            } else {
                $this->db->where_in('user_details.user_id', $Selected_users);
            }
        }

        // echo $userTypeid;die;
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
        $this->db->where('CAST(updateddate AS DATE) >=', $sdate);
        $this->db->where('CAST(updateddate AS DATE) <=', $edate);
        // $this->db->where('tblcallevents.updateddate >', '2023-03-31');
        $this->db->where('tblcallevents.nextCFID !=', '0');
        $this->db->order_by('tblcallevents.updateddate', 'DESC');
        $query = $this->db->get();
        // echo $this->db->last_query();die;
        return $query->result();
    }

    public function getStatusWiseTask($uid, $sdate, $edate, $selected_category, $selected_partnerType, $selected_userType, $selected_cluster, $selected_users, $status, $userTypeid)
    {

        $this->db->select('init_call.id');
        $this->db->from('init_call');
        $this->db->join('company_master', 'company_master.id = init_call.cmpid_id', 'left');
        $this->db->join('partner_master', 'partner_master.id = company_master.partnerType_id', 'left');

        if (!empty($selected_category)) {

            $this->db->group_start();

            foreach ($selected_category as $singleCategory) {

                if ($singleCategory == 'topspender') {

                    $this->db->or_where('focus_funnel', 'yes');
                }
                if ($singleCategory == 'focus_funnel') {

                    $this->db->or_where('focus_funnel', 'yes');
                }
                if ($singleCategory == 'upsell_client') {

                    $this->db->or_where('upsell_client', 'yes');
                }
                if ($singleCategory == 'keycompany') {

                    $this->db->or_where('keycompany', 'yes');
                }
                if ($singleCategory == 'pkclient ') {

                    $this->db->or_where('pkclient', 'yes');
                }
                if ($singleCategory == 'priorityc ') {

                    $this->db->or_where('priorityc', 'yes');
                }
            }

            $this->db->group_end();
        }

        if (!empty($selected_partnerType)) {

            $this->db->where_in('company_master.partnerType_id', $selected_partnerType);
        }

        // if (!empty($selected_users)) {

        //     $this->db->where_in('user_details.aadmin', $selected_users);
        // }
        // var_dmp($selected_userType);die;
        if (!empty($selected_userType)) {

            if ($selected_userType == 2) {

                $this->db->where_in('user_details.admin_id', $selected_users);
    
            } elseif ($selected_userType == 4) {
    
                $this->db->where_in('user_details.pst_co', $selected_users);

            } elseif ($selected_userType == 9 || $selected_userType == 13) {
    
                $this->db->where_in('user_details.aadmin', $selected_users);

            } else {
                $this->db->where_in('user_details.user_id', $selected_users);
            }
        }

        // if ($userTypeid == 2) {

        //     $this->db->where('user_details.admin_id', $uid);
        // } elseif ($userTypeid == 4) {

        //     $this->db->where_in('pst_co', $uid);
        // } elseif ($userTypeid == 9 || $userTypeid == 13) {

        //     $this->db->where_in('aadmin', $uid);
        // } else {
        //     $this->db->where('user_id', $uid);
        // }

        // $this->db->where('user_details admin_id', $uid);
        $this->db->join('user_details', 'user_details.user_id = init_call.mainbd', 'left');
        $this->db->where('cstatus', $status);
        // if (condition) {
        //     # code...
        // }
        // $this->db->get_compiled_select();

        $subquery = $this->db->get_compiled_select();

        $this->db->select(
            'COUNT(*) as cont,
                COUNT(CASE WHEN actiontype_id=1 THEN 1 END) as a,
                COUNT(CASE WHEN actiontype_id=2 THEN 1 END) as b,
                COUNT(CASE WHEN actiontype_id=3 THEN 1 END) as c,
                COUNT(CASE WHEN actiontype_id=4 THEN 1 END) as d,
                COUNT(CASE WHEN actiontype_id=5 THEN 1 END) as e,
                COUNT(CASE WHEN actiontype_id=6 THEN 1 END) as f,
                COUNT(CASE WHEN actiontype_id=7 THEN 1 END) as g,
                COUNT(CASE WHEN actiontype_id=8 THEN 1 END) as h,
                COUNT(CASE WHEN actiontype_id=9 THEN 1 END) as i,
                COUNT(CASE WHEN actiontype_id=10 THEN 1 END) as j,
                COUNT(CASE WHEN actiontype_id=11 THEN 1 END) as k'
        );
        $this->db->select('nstatus_id');
        $this->db->from('tblcallevents');

        // $this->db->where('tblcallevents.cid_id',$status);
        $this->db->where_in('cid_id', $subquery, FALSE);
        $this->db->where('nextCFID !=', '0');
        $this->db->where('CAST(updateddate AS DATE) >=', $sdate);
        $this->db->where('CAST(updateddate AS DATE) <=', $edate);

        $query = $this->db->get();

        // echo $this->db->last_query();die;
        return $query->result();
    }

    // public function getMonthWiseFunnel($uid,$sdate,$edate,$selected_category,$selected_partnerType,$selected_userType,$selected_cluster,$selected_users,$status,$userTypeid){
    // }

    public function getActionWiseFunnel($uid, $sdate, $edate, $selected_category, $selected_partnerType, $selected_userType, $selected_cluster, $selected_users, $status, $userTypeid, $action)
    {

        $this->db->select('init_call.id');
        $this->db->from('init_call');
        $this->db->join('company_master', 'company_master.id = init_call.cmpid_id', 'left');
        $this->db->join('partner_master', 'partner_master.id = company_master.partnerType_id', 'left');

        if (!empty($selected_category)) {

            $this->db->group_start();

            foreach ($selected_category as $singleCategory) {

                if ($singleCategory == 'topspender') {

                    $this->db->or_where('focus_funnel', 'yes');
                }
                if ($singleCategory == 'focus_funnel') {

                    $this->db->or_where('focus_funnel', 'yes');
                }
                if ($singleCategory == 'upsell_client') {

                    $this->db->or_where('upsell_client', 'yes');
                }
                if ($singleCategory == 'keycompany') {

                    $this->db->or_where('keycompany', 'yes');
                }
                if ($singleCategory == 'pkclient ') {

                    $this->db->or_where('pkclient', 'yes');
                }
                if ($singleCategory == 'priorityc ') {

                    $this->db->or_where('priorityc', 'yes');
                }
            }

            $this->db->group_end();
        }

        if (!empty($selected_partnerType)) {

            $this->db->where_in('company_master.partnerType_id', $selected_partnerType);
        }

        if (!empty($selected_users)) {

            if ($selected_userType == 2) {

                $this->db->where_in('user_details.admin_id', $selected_users);
            } elseif ($selected_userType == 4) {

                $this->db->where_in('pst_co', $selected_users);
            } elseif ($selected_userType == 9 || $selected_userType == 13) {

                $this->db->where_in('aadmin', $selected_users);
            } else {
                $this->db->where_in('user_id', $selected_users);
            }

            // $this->db->where_in('user_details.aadmin', $selected_users);
        }

        if ($userTypeid == 2) {

            $this->db->where('user_details.admin_id', $uid);
        } elseif ($userTypeid == 4) {

            $this->db->where_in('pst_co', $uid);
        } elseif ($userTypeid == 9 || $userTypeid == 13) {

            $this->db->where_in('aadmin', $uid);
        } elseif ($userTypeid == 15) {

            $this->db->where_in('sales_co', $uid);
        } else {
            $this->db->where('user_id', $uid);
        }

        // $this->db->where('user_details admin_id', $uid);
        $this->db->join('user_details', 'user_details.user_id = init_call.mainbd', 'left');
        $this->db->where('cstatus', $status);

        $subquery = $this->db->get_compiled_select();

        $this->db->select(
            'COUNT(*) as cont,
            action.name as name,
            COUNT(CASE WHEN initiateddt > updateddate THEN 1 END) as a,
            COUNT(CASE WHEN initiateddt < updateddate THEN 1 END) as b,
            nstatus_id'
        );

        $this->db->from('tblcallevents');
        $this->db->join('action', 'action.id = tblcallevents.actiontype_id', 'left');

        // Add the WHERE conditions
        $this->db->where('tblcallevents.cid_id IN (' . $subquery . ')', NULL, FALSE);
        $this->db->where('actiontype_id', $action);
        $this->db->where('nextCFID !=', '0');
        $this->db->where('CAST(updateddate AS DATE) >=', $sdate);
        $this->db->where('CAST(updateddate AS DATE) <=', $edate);
        $this->db->where('tblcallevents.initiateddt != ', NULL);
        // $this->db->group_by('cstatus');
        $query = $this->db->get();

        // echo $this->db->last_query();die;
        return $query->result();
    }

    public function ActionWiseTableDetails($uid, $sdate, $edate, $selected_category, $selected_partnerType, $selected_userType, $selected_cluster, $selected_users, $userTypeid, $action)
    {

        $this->db->select('tblcallevents.*, company_master.id as cid,company_master.compname as compname, action.clr as aclr, s1.clr as bsclr, s2.clr as asclr, partner_master.clr as pclr, s1.name as bstatus, s1.name as astatus, action.name as acname, user_details.name as uname, partner_master.name as pname');
        $this->db->from('tblcallevents');
        $this->db->join('init_call', 'init_call.id = tblcallevents.cid_id', 'left');
        $this->db->join('status s3', 's3.id = init_call.cstatus', 'left');
        $this->db->join('user_details', 'user_details.user_id = init_call.mainbd', 'left');
        $this->db->join('company_master', 'company_master.id = init_call.cmpid_id', 'left');
        $this->db->join('status s1', 's1.id = tblcallevents.status_id', 'left');
        $this->db->join('status s2', 's2.id = tblcallevents.nstatus_id', 'left');
        $this->db->join('action', 'action.id = tblcallevents.actiontype_id', 'left');
        $this->db->join('partner_master', 'partner_master.id = company_master.partnerType_id', 'left');

        if (!empty($selected_category)) {

            $this->db->group_start();

            foreach ($selected_category as $singleCategory) {

                if ($singleCategory == 'topspender') {

                    $this->db->or_where('focus_funnel', 'yes');
                }
                if ($singleCategory == 'focus_funnel') {

                    $this->db->or_where('focus_funnel', 'yes');
                }
                if ($singleCategory == 'upsell_client') {

                    $this->db->or_where('upsell_client', 'yes');
                }
                if ($singleCategory == 'keycompany') {

                    $this->db->or_where('keycompany', 'yes');
                }
                if ($singleCategory == 'pkclient ') {

                    $this->db->or_where('pkclient', 'yes');
                }
                if ($singleCategory == 'priorityc ') {

                    $this->db->or_where('priorityc', 'yes');
                }
            }

            $this->db->group_end();
        }

        if (!empty($selected_partnerType)) {

            $this->db->where_in('company_master.partnerType_id', $selected_partnerType);
        }


        if (!empty($selected_users)) {

            if ($selected_userType == 2) {

                $this->db->where_in('user_details.admin_id', $selected_users);
            } elseif ($selected_userType == 4) {

                $this->db->where_in('pst_co', $selected_users);
            } elseif ($selected_userType == 9 || $selected_userType == 13) {

                $this->db->where_in('aadmin', $selected_users);
            } else {
                $this->db->where_in('user_details.user_id', $selected_users);
            }

            // $this->db->where_in('user_details.aadmin', $selected_users);
        }

        if ($userTypeid == 2) {

            $this->db->where('user_details.admin_id', $uid);
        } elseif ($userTypeid == 4) {

            $this->db->where_in('pst_co', $uid);
        } elseif ($userTypeid == 9 || $userTypeid == 13) {

            $this->db->where_in('aadmin', $uid);
        } elseif ($userTypeid == 15) {

            $this->db->where_in('sales_co', $uid);
        } else {
            $this->db->where('user_details.user_id', $uid);
        }

        if (!empty($action)) {

            $this->db->where_in('actiontype_id', $action);
        }
        $this->db->where('tblcallevents.nextCFID !=', '0');
        $this->db->where('CAST(updateddate AS DATE) >=', $sdate);
        $this->db->where('CAST(updateddate AS DATE) <=', $edate);
        $this->db->where('tblcallevents.initiateddt != ', NULL);
        $this->db->order_by('tblcallevents.cid_id', 'DESC');
        $this->db->order_by('tblcallevents.updateddate', 'ASC');
        $query = $this->db->get();
        // echo $this->db->last_query();die;
        return $query->result();
    }

    public function get_OtherUserOnMyFunnelGraph($uid,$month,$financialYear,$sdate,$edate,$selected_category,$selected_partnerType,$Selected_userType,$selected_cluster,$selected_users,$userTypeid,$Selected_action,$Selected_purpose){

        // echo "action:" .$Selected_action;
        // echo "<br>";
        // echo "purpose:" .$Selected_purpose;die;

        $this->db->select('init_call.id');
        $this->db->from('init_call');
        $this->db->join('company_master', 'company_master.id = init_call.cmpid_id', 'left');
        $this->db->join('partner_master', 'partner_master.id = company_master.partnerType_id', 'left');

        if (!empty($selected_category)) {

            $this->db->group_start();

            foreach ($selected_category as $singleCategory) {

                if ($singleCategory == 'topspender') {

                    $this->db->or_where('topspender', 'yes');
                }
                if ($singleCategory == 'focus_funnel') {

                    $this->db->or_where('focus_funnel', 'yes');
                }
                if ($singleCategory == 'upsell_client') {

                    $this->db->or_where('upsell_client', 'yes');
                }
                if ($singleCategory == 'keycompany') {

                    $this->db->or_where('keycompany', 'yes');
                }
                if ($singleCategory == 'pkclient ') {

                    $this->db->or_where('pkclient', 'yes');
                }
                if ($singleCategory == 'priorityc ') {

                    $this->db->or_where('priorityc', 'yes');
                }
            }

            $this->db->group_end();
        }

        if (!empty($selected_partnerType)) {

            $this->db->where_in('company_master.partnerType_id', $selected_partnerType);
        }

        if (!empty($Selected_userType)) {

            if ($Selected_userType == 2) {

                $this->db->where_in('user_details.admin_id', $selected_users);

            } elseif ($Selected_userType == 4) {

                $this->db->where_in('pst_co', $selected_users);

            } elseif ($Selected_userType == 9 || $Selected_userType == 13) {

                $this->db->where_in('aadmin', $selected_users);

            } else {
                
                $this->db->where_in('user_details.user_id', $selected_users);
            }
        }

        if ($userTypeid == 2) {

            $this->db->where('user_details.admin_id', $uid);
            
        } elseif ($userTypeid == 4) {

            $this->db->where_in('pst_co', $uid);
        } elseif ($userTypeid == 9 || $userTypeid == 13) {

            $this->db->where_in('aadmin', $uid);
        } elseif ($userTypeid == 15) {

            $this->db->where_in('sales_co', $uid);
        } else {
            $this->db->where('user_id', $uid);
        }

        // $this->db->where('user_details admin_id', $uid);
        $this->db->join('user_details', 'user_details.user_id = init_call.mainbd', 'left');
        $this->db->where('CAST(updateddate AS DATE) >=', $sdate);
        $this->db->where('CAST(updateddate AS DATE) <=', $edate);

        $subquery = $this->db->get_compiled_select();


        $this->db->select('id');
        $this->db->from('init_call');
        $this->db->where_in('mainbd',$selected_users);
        $subquery2 = $this->db->get_compiled_select();
        // SELECT id FROM init_call WHERE mainbd IN('.$selected_users.'))'
        // echo $subquery;die;
        // if ($Selected_action == null) {
        //     $Selected_action = 'Yes';
        // }

            $this->db->select('COUNT(*) as cont');
            $this->db->select('COUNT(case when actiontype_id = 1 then 1 end) as a');
            $this->db->select('COUNT(case when actiontype_id = 2 then 1 end) as b');
            $this->db->select('COUNT(case when actiontype_id = 3 then 1 end) as c');
            $this->db->select('COUNT(case when actiontype_id = 4 then 1 end) as d');
            $this->db->select('COUNT(case when actiontype_id = 5 then 1 end) as e');
            $this->db->select('COUNT(case when actiontype_id = 6 then 1 end) as f');
            $this->db->select('COUNT(case when actiontype_id = 7 then 1 end) as g');
            $this->db->select('COUNT(case when actiontype_id = 8 then 1 end) as h');
            $this->db->select('COUNT(case when actiontype_id = 9 then 1 end) as i');
            $this->db->select('COUNT(case when actiontype_id = 10 then 1 end) as j');
            $this->db->select('COUNT(case when actiontype_id = 11 then 1 end) as k');
            $this->db->from('tblcallevents');
            // ->where_in('cid_id', $subquery, FALSE)

            if (!empty($selected_users)) {
                # code...
                $this->db->where_in('tblcallevents.user_id', $selected_users);
                $this->db->where('tblcallevents.cid_id IN ('.$subquery2.')');
            }
            $this->db->where('actontaken', $Selected_action);
            $this->db->where('purpose_achieved', $Selected_purpose);
            // $this->db->where('nextCFID !=', '0');
            // ->where('CAST(updateddate AS DATE) >=', $sdate)
            // ->where('CAST(updateddate AS DATE) <=', $edate)
            $this->db->where('MONTH(updateddate) >=', $month);
            $this->db->where('YEAR(updateddate) >=', $financialYear);


        $query = $this->db->get();

        // echo $this->db->last_query();
        return $query->result();
    }


    public function APTableDetails($uid, $sdate, $edate, $selected_category, $selected_partnerType, $selected_userType, $selected_users, $userTypeid,$actionType, $purposeType,$status)
    {

        $this->db->select('tblcallevents.*, company_master.id as cid,company_master.compname as compname, action.clr as aclr, s1.clr as bsclr, s2.clr as asclr, partner_master.clr as pclr, s1.name as bstatus, s1.name as astatus, action.name as acname, user_details.name as uname, partner_master.name as pname');
        $this->db->from('tblcallevents');
        $this->db->join('init_call', 'init_call.id = tblcallevents.cid_id', 'left');
        $this->db->join('status s3', 's3.id = init_call.cstatus', 'left');
        $this->db->join('user_details', 'user_details.user_id = init_call.mainbd', 'left');
        $this->db->join('company_master', 'company_master.id = init_call.cmpid_id', 'left');
        $this->db->join('status s1', 's1.id = tblcallevents.status_id', 'left');
        $this->db->join('status s2', 's2.id = tblcallevents.nstatus_id', 'left');
        $this->db->join('action', 'action.id = tblcallevents.actiontype_id', 'left');
        $this->db->join('partner_master', 'partner_master.id = company_master.partnerType_id', 'left');

        // var_dump($selected_category);die;
        if (!empty($selected_category)) {

            $this->db->group_start();

            foreach ($selected_category as $singleCategory) {

                if ($singleCategory == 'topspender') {

                    $this->db->or_where('topspender', 'yes');
                }
                if ($singleCategory == 'focus_funnel') {

                    $this->db->or_where('focus_funnel', 'yes');
                }
                if ($singleCategory == 'upsell_client') {

                    $this->db->or_where('upsell_client', 'yes');
                }
                if ($singleCategory == 'keycompany') {

                    $this->db->or_where('keycompany', 'yes');
                }
                if ($singleCategory == 'pkclient ') {

                    $this->db->or_where('pkclient', 'yes');
                }
                if ($singleCategory == 'priorityc ') {

                    $this->db->or_where('priorityc', 'yes');
                }
            }

            $this->db->group_end();
        }

        if (!empty($selected_partnerType)) {

            $this->db->where_in('company_master.partnerType_id', $selected_partnerType);
        }


        if (!empty($selected_userType)) {
            $this->db->group_start();

            foreach ($selected_userType as $singleuserType) {
                
                if ($singleuserType == 2) {

                    $this->db->where_in('user_details.admin_id', $selected_users);
                }
                else if ($singleuserType == 4) {
                    $this->db->or_where_in('user_details.pst_co', $selected_users);
                }
                else if ($singleuserType == 9 || $singleuserType == 13) {
                    $this->db->or_where_in('user_details.aadmin', $selected_users);
                }
                else if ($singleuserType == 15) {
                    $this->db->or_where_in('user_details.sales_co', $selected_users);
                }
                else{

                    $this->db->where_in('user_details.user_id', $selected_users);
                }
            }
            $this->db->group_end();
        }

        if ($userTypeid == 2) {

            $this->db->where('user_details.admin_id', $uid);
        } elseif ($userTypeid == 4) {

            $this->db->where_in('pst_co', $uid);
        } elseif ($userTypeid == 9 || $userTypeid == 13) {

            $this->db->where_in('user_details.aadmin', $uid);
        } elseif ($userTypeid == 15) {

            $this->db->where_in('user_details.sales_co', $uid);
        }else {
            $this->db->where('user_details.user_id', $uid);
        }

        $this->db->where('actontaken', $actionType);
        $this->db->where('purpose_achieved', $purposeType);

        // if ($status == 1) {

        //     $this->db->where('tblcallevents.nextCFID !=', '0');
        //     $this->db->where('tblcallevents.initiateddt != ', NULL);

        // }elseif ($status == 0) {
            
        //     $this->db->where('tblcallevents.nextCFID ', '0');
        // }

        $this->db->where('CAST(updateddate AS DATE) >=', $sdate);
        $this->db->where('CAST(updateddate AS DATE) <=', $edate);
        $this->db->order_by('tblcallevents.cid_id', 'DESC');
        $this->db->order_by('tblcallevents.updateddate', 'ASC');
        $query = $this->db->get();
        // echo $this->db->last_query();die;
        return $query->result();
    }

    public function get_taskplannedSlotwise($selected_users,$selected_usersType,$sdate,$edate,$t1,$t2,$selected_partnerType,$selected_category){

        $this->db->select('init_call.id');
        $this->db->from('init_call');
        $this->db->join('company_master', 'company_master.id = init_call.cmpid_id', 'left');
        $this->db->join('partner_master', 'partner_master.id = company_master.partnerType_id', 'left');

        if (!empty($selected_category)) {

            $this->db->group_start();

            foreach ($selected_category as $singleCategory) {

                if ($singleCategory == 'topspender') {

                    $this->db->or_where('focus_funnel', 'yes');
                }
                if ($singleCategory == 'focus_funnel') {

                    $this->db->or_where('focus_funnel', 'yes');
                }
                if ($singleCategory == 'upsell_client') {

                    $this->db->or_where('upsell_client', 'yes');
                }
                if ($singleCategory == 'keycompany') {

                    $this->db->or_where('keycompany', 'yes');
                }
                if ($singleCategory == 'pkclient ') {

                    $this->db->or_where('pkclient', 'yes');
                }
                if ($singleCategory == 'priorityc ') {

                    $this->db->or_where('priorityc', 'yes');
                }
            }

            $this->db->group_end();
        }

        if (!empty($selected_partnerType)) {

            $this->db->where_in('company_master.partnerType_id', $selected_partnerType);
        }

        if (!empty($selected_usersType)) {
            $this->db->group_start();

            foreach ($selected_usersType as $singleuserType) {
                
                if ($singleuserType == 2) {

                    $this->db->where_in('user_details.admin_id', $selected_users);
                }
                else if ($singleuserType == 4) {
                    $this->db->or_where_in('user_details.pst_co', $selected_users);
                }
                else if ($singleuserType == 9 || $singleuserType == 13) {
                    $this->db->or_where_in('user_details.aadmin', $selected_users);
                }
                else{

                    $this->db->where_in('user_details.user_id', $selected_users);
                }
            }
            $this->db->group_end();
        }

        // if ($userTypeid == 2) {

        //     $this->db->where('user_details.admin_id', $uid);
            
        // } elseif ($userTypeid == 4) {

        //     $this->db->where_in('pst_co', $uid);
        // } elseif ($userTypeid == 9 || $userTypeid == 13) {

        //     $this->db->where_in('aadmin', $uid);
        // } else {
        //     $this->db->where('user_id', $uid);
        // }

        // $this->db->where('user_details admin_id', $uid);
        $this->db->join('user_details', 'user_details.user_id = init_call.mainbd', 'left');

        $this->db->where('CAST(updateddate AS DATE) >=', $sdate);
        $this->db->where('CAST(updateddate AS DATE) <=', $edate);
        
        $subquery = $this->db->get_compiled_select();

        $this->db->select('COUNT(*) count');
        $this->db->from('tblcallevents');
        $this->db->where('tblcallevents.cid_id IN ('.$subquery.')');
        $this->db->where('plan', 1);
        $this->db->where('CAST(appointmentdatetime AS DATE) >=', $sdate);
        $this->db->where('CAST(appointmentdatetime AS DATE) <=', $edate);
        $this->db->where('CAST(appointmentdatetime AS TIME) >=', $t1);
        $this->db->where('CAST(appointmentdatetime AS TIME) <=', $t2);

        $query = $this->db->get();

        // echo $this->db->last_query();die;
        return $query->result();

    }

    public function get_taskinitiatedSlotwise($selected_users,$selected_usersType,$sdate,$edate,$t1,$t2,$selected_partnerType,$selected_category){

        $this->db->select('init_call.id');
        $this->db->from('init_call');
        $this->db->join('company_master', 'company_master.id = init_call.cmpid_id', 'left');
        $this->db->join('partner_master', 'partner_master.id = company_master.partnerType_id', 'left');

        if (!empty($selected_category)) {

            $this->db->group_start();

            foreach ($selected_category as $singleCategory) {

                if ($singleCategory == 'topspender') {

                    $this->db->or_where('focus_funnel', 'yes');
                }
                if ($singleCategory == 'focus_funnel') {

                    $this->db->or_where('focus_funnel', 'yes');
                }
                if ($singleCategory == 'upsell_client') {

                    $this->db->or_where('upsell_client', 'yes');
                }
                if ($singleCategory == 'keycompany') {

                    $this->db->or_where('keycompany', 'yes');
                }
                if ($singleCategory == 'pkclient ') {

                    $this->db->or_where('pkclient', 'yes');
                }
                if ($singleCategory == 'priorityc ') {

                    $this->db->or_where('priorityc', 'yes');
                }
            }

            $this->db->group_end();
        }

        if (!empty($selected_partnerType)) {

            $this->db->where_in('company_master.partnerType_id', $selected_partnerType);
        }

        if (!empty($selected_usersType)) {
            $this->db->group_start();

            foreach ($selected_usersType as $singleuserType) {
                
                if ($singleuserType == 2) {

                    $this->db->where_in('user_details.admin_id', $selected_users);
                }
                else if ($singleuserType == 4) {
                    $this->db->or_where_in('user_details.pst_co', $selected_users);
                }
                else if ($singleuserType == 9 || $singleuserType == 13) {
                    $this->db->or_where_in('user_details.aadmin', $selected_users);
                }
                else{

                    $this->db->where_in('user_details.user_id', $selected_users);
                }
            }
            $this->db->group_end();
        }

        // if ($userTypeid == 2) {

        //     $this->db->where('user_details.admin_id', $uid);
            
        // } elseif ($userTypeid == 4) {

        //     $this->db->where_in('pst_co', $uid);
        // } elseif ($userTypeid == 9 || $userTypeid == 13) {

        //     $this->db->where_in('aadmin', $uid);
        // } else {
        //     $this->db->where('user_id', $uid);
        // }

        // $this->db->where('user_details admin_id', $uid);
        $this->db->join('user_details', 'user_details.user_id = init_call.mainbd', 'left');
        
        $this->db->where('CAST(updateddate AS DATE) >=', $sdate);
        $this->db->where('CAST(updateddate AS DATE) <=', $edate);

        $subquery = $this->db->get_compiled_select();
        // var_dump($subquery);die;


        $this->db->select('COUNT(*) count');
        $this->db->from('tblcallevents');
        $this->db->where('tblcallevents.cid_id IN ('.$subquery.')');
        $this->db->where('plan', 1);
        $this->db->where('CAST(appointmentdatetime AS DATE) >=', $sdate);
        $this->db->where('CAST(appointmentdatetime AS DATE) <=', $edate);
        $this->db->where('CAST(initiateddt AS TIME) >=', $t1);
        $this->db->where('CAST(initiateddt AS TIME) <=', $t2);

        $query = $this->db->get();
        return $query->result();

    }

    public function get_taskupdatedSlotwise($selected_users,$selected_usersType,$sdate,$edate,$t1,$t2,$selected_partnerType,$selected_category){

        // $query=$this->db->query("SELECT count(*) cont FROM tblcallevents WHERE user_id='$uid' and  cast(appointmentdatetime as DATE) BETWEEN '$sd' and '$ed' and cast(appointmentdatetime as TIME) BETWEEN '$t1' and '$t2' and plan='1'");

        $this->db->select('init_call.id');
        $this->db->from('init_call');
        $this->db->join('company_master', 'company_master.id = init_call.cmpid_id', 'left');
        $this->db->join('partner_master', 'partner_master.id = company_master.partnerType_id', 'left');

        if (!empty($selected_category)) {

            $this->db->group_start();

            foreach ($selected_category as $singleCategory) {

                if ($singleCategory == 'topspender') {

                    $this->db->or_where('focus_funnel', 'yes');
                }
                if ($singleCategory == 'focus_funnel') {

                    $this->db->or_where('focus_funnel', 'yes');
                }
                if ($singleCategory == 'upsell_client') {

                    $this->db->or_where('upsell_client', 'yes');
                }
                if ($singleCategory == 'keycompany') {

                    $this->db->or_where('keycompany', 'yes');
                }
                if ($singleCategory == 'pkclient ') {

                    $this->db->or_where('pkclient', 'yes');
                }
                if ($singleCategory == 'priorityc ') {

                    $this->db->or_where('priorityc', 'yes');
                }
            }

            $this->db->group_end();
        }

        if (!empty($selected_partnerType)) {

            $this->db->where_in('company_master.partnerType_id', $selected_partnerType);
        }

        if (!empty($selected_usersType)) {
            $this->db->group_start();

            foreach ($selected_usersType as $singleuserType) {

                if ($singleuserType == 2) {

                    $this->db->where_in('user_details.admin_id', $selected_users);
                }
                else if ($singleuserType == 4) {
                    $this->db->or_where_in('user_details.pst_co', $selected_users);
                }
                else if ($singleuserType == 9 || $singleuserType == 13) {
                    $this->db->or_where_in('user_details.aadmin', $selected_users);
                }
                else{

                    $this->db->where_in('user_details.user_id', $selected_users);
                }
            }
            $this->db->group_end();
        }

        // if ($userTypeid == 2) {

        //     $this->db->where('user_details.admin_id', $uid);
            
        // } elseif ($userTypeid == 4) {

        //     $this->db->where_in('pst_co', $uid);
        // } elseif ($userTypeid == 9 || $userTypeid == 13) {

        //     $this->db->where_in('aadmin', $uid);
        // } else {
        //     $this->db->where('user_id', $uid);
        // }

        // $this->db->where('user_details admin_id', $uid);
        $this->db->join('user_details', 'user_details.user_id = init_call.mainbd', 'left');
        
        $this->db->where('CAST(updateddate AS DATE) >=', $sdate);
        $this->db->where('CAST(updateddate AS DATE) <=', $edate);

        $subquery = $this->db->get_compiled_select();

        $this->db->select('COUNT(*) count');
        $this->db->from('tblcallevents');
        $this->db->where('tblcallevents.cid_id IN ('.$subquery.')');
        $this->db->where('plan', 1);
        $this->db->where('CAST(appointmentdatetime AS DATE) >=', $sdate);
        $this->db->where('CAST(appointmentdatetime AS DATE) <=', $edate);
        $this->db->where('CAST(updateddate AS TIME) >=', $t1);
        $this->db->where('CAST(updateddate AS TIME) <=', $t2);

        $query = $this->db->get();
        return $query->result();

    }

    public function getTimeSlotWiseTaskTable($users,$userType,$sdate,$edate,$partnerType,$category){

        $this->db->select('tblcallevents.*, company_master.id as cid,company_master.compname as compname, action.clr as aclr, s1.clr as bsclr, s2.clr as asclr, partner_master.clr as pclr, s1.name as bstatus, s1.name as astatus, action.name as acname, user_details.name as uname, partner_master.name as pname');
        $this->db->from('tblcallevents');
        $this->db->join('init_call', 'init_call.id = tblcallevents.cid_id', 'left');
        $this->db->join('status s3', 's3.id = init_call.cstatus', 'left');
        $this->db->join('user_details', 'user_details.user_id = init_call.mainbd', 'left');
        $this->db->join('company_master', 'company_master.id = init_call.cmpid_id', 'left');
        $this->db->join('status s1', 's1.id = tblcallevents.status_id', 'left');
        $this->db->join('status s2', 's2.id = tblcallevents.nstatus_id', 'left');
        $this->db->join('action', 'action.id = tblcallevents.actiontype_id', 'left');
        $this->db->join('partner_master', 'partner_master.id = company_master.partnerType_id', 'left');

        if (!empty($category)) {

            $this->db->group_start();

            foreach ($category as $singleCategory) {

                if ($singleCategory == 'topspender') {

                    $this->db->or_where('focus_funnel', 'yes');
                }
                if ($singleCategory == 'focus_funnel') {

                    $this->db->or_where('focus_funnel', 'yes');
                }
                if ($singleCategory == 'upsell_client') {

                    $this->db->or_where('upsell_client', 'yes');
                }
                if ($singleCategory == 'keycompany') {

                    $this->db->or_where('keycompany', 'yes');
                }
                if ($singleCategory == 'pkclient ') {

                    $this->db->or_where('pkclient', 'yes');
                }
                if ($singleCategory == 'priorityc ') {

                    $this->db->or_where('priorityc', 'yes');
                }
            }

            $this->db->group_end();
        }

        if (!empty($partnerType)) {

            $this->db->where_in('company_master.partnerType_id', $partnerType);
        }

        if (!empty($userType)) {
            $this->db->group_start();

            foreach ($userType as $singleuserType) {
                if ($singleuserType == 2) {

                    $this->db->where_in('user_details.admin_id', $users);
                }
                else if ($singleuserType == 4) {
                    $this->db->or_where_in('user_details.pst_co', $users);
                }
                else if ($singleuserType == 9 || $singleuserType == 13) {
                    $this->db->or_where_in('user_details.aadmin', $users);
                }
                else{

                    $this->db->where_in('user_details.user_id', $users);
                }
            }
            $this->db->group_end();
        }

        $this->db->where('CAST(updateddate AS DATE) >=', $sdate);
        $this->db->where('CAST(updateddate AS DATE) <=', $edate);
        // $this->db->where('tblcallevents.initiateddt != ', NULL);
        $this->db->order_by('user_details.name', 'ASC');
        $this->db->order_by('tblcallevents.cid_id', 'DESC');
        $this->db->order_by('tblcallevents.updateddate', 'ASC');
        $query = $this->db->get();
        // echo $this->db->last_query();die;
        return $query->result();

    }

    public function get_taskTypeConversion($uid,$startDate,$endDate,$action_id,$userType,$users){

        // $query=$this->db->query("SELECT count(*) cont FROM tblcallevents WHERE user_id='$uid' and nextCFID!='0' and cast(updateddate as DATE) BETWEEN '$sd' and '$ed' and actiontype_id='$ac' and status_id!=nstatus_id");
        // return $query->result();


        $this->db->select("COUNT(*) count");
    
        $this->db->from('tblcallevents');
        $this->db->join('user_details', 'user_details.user_id=tblcallevents.user_id', 'left');

        if (!empty($userType)) {
            $this->db->group_start();

            foreach ($userType as $singleuserType) {
                if ($singleuserType == 2) {

                    $this->db->where_in('user_details.admin_id', $users);
                }
                else if ($singleuserType == 4) {
                    $this->db->or_where_in('user_details.pst_co', $users);
                }
                else if ($singleuserType == 9 || $singleuserType == 13) {
                    $this->db->or_where_in('user_details.aadmin', $users);
                }
                else{

                    $this->db->where_in('user_details.user_id', $users);
                }
            }
            $this->db->group_end();
        }

        $this->db->where('nextCFID !=','0');
        $this->db->where('actiontype_id ',$action_id);
        $this->db->where('status_id !=',' nstatus_id');

        $this->db->where('CAST(updateddate AS DATE) BETWEEN "'.$startDate.'" AND "'.$endDate.'"', NULL, FALSE);
        // LEFT JOIN  ON user_details.user_id=user_day.user_id
        // $this->db->group_by('wffo');
        $query = $this->db->get();
        // echo $this->db->last_query();die;
        return $query->result();
    }

    public function get_taskTypeConversionByStatus($uid,$startDate,$endDate,$action_id,$stid,$userType,$users){
        // $query=$this->db->query("SELECT count(*) cont FROM tblcallevents WHERE user_id='$uid' and nextCFID!='0' and cast(updateddate as DATE) BETWEEN '$sd' and '$ed' and actiontype_id='$ac' and status_id!=nstatus_id and nstatus_id='$st'");
        // return $query->result();


        $this->db->select("COUNT(*) count");
    
        $this->db->from('tblcallevents');
        $this->db->join('user_details', 'user_details.user_id=tblcallevents.user_id', 'left');

        if (!empty($userType)) {
            $this->db->group_start();

            foreach ($userType as $singleuserType) {
                if ($singleuserType == 2) {

                    $this->db->where_in('user_details.admin_id', $users);
                }
                else if ($singleuserType == 4) {
                    $this->db->or_where_in('user_details.pst_co', $users);
                }
                else if ($singleuserType == 9 || $singleuserType == 13) {
                    $this->db->or_where_in('user_details.aadmin', $users);
                }
                else{

                    $this->db->where_in('user_details.user_id', $users);
                }
            }
            $this->db->group_end();
        }

        $this->db->where('nextCFID !=','0');
        $this->db->where('actiontype_id ',$action_id);
        $this->db->where('status_id !=',' nstatus_id');
        $this->db->where('nstatus_id ',$stid);


        $this->db->where('CAST(updateddate AS DATE) BETWEEN "'.$startDate.'" AND "'.$endDate.'"', NULL, FALSE);
        // LEFT JOIN  ON user_details.user_id=user_day.user_id
        // $this->db->group_by('wffo');
        $query = $this->db->get();
        // echo $this->db->last_query();die;
        return $query->result();

    }

    public function getActionWiseTaskConversionTable($users,$userType,$sdate,$edate,$partnerType,$category){

        // var_dump($userType);die;
        $this->db->select('tblcallevents.*, company_master.id as cid,company_master.compname as compname, action.clr as aclr, s1.clr as bsclr, s2.clr as asclr, partner_master.clr as pclr, s1.name as bstatus, s1.name as astatus, action.name as acname, user_details.name as uname, partner_master.name as pname');
        $this->db->from('tblcallevents');
        $this->db->join('init_call', 'init_call.id = tblcallevents.cid_id', 'left');
        $this->db->join('status s3', 's3.id = init_call.cstatus', 'left');
        $this->db->join('user_details', 'user_details.user_id = init_call.mainbd', 'left');
        $this->db->join('company_master', 'company_master.id = init_call.cmpid_id', 'left');
        $this->db->join('status s1', 's1.id = tblcallevents.status_id', 'left');
        $this->db->join('status s2', 's2.id = tblcallevents.nstatus_id', 'left');
        $this->db->join('action', 'action.id = tblcallevents.actiontype_id', 'left');
        $this->db->join('partner_master', 'partner_master.id = company_master.partnerType_id', 'left');

        if (!empty($category)) {

            $this->db->group_start();

            foreach ($category as $singleCategory) {

                if ($singleCategory == 'topspender') {

                    $this->db->or_where('focus_funnel', 'yes');
                }
                if ($singleCategory == 'focus_funnel') {

                    $this->db->or_where('focus_funnel', 'yes');
                }
                if ($singleCategory == 'upsell_client') {

                    $this->db->or_where('upsell_client', 'yes');
                }
                if ($singleCategory == 'keycompany') {

                    $this->db->or_where('keycompany', 'yes');
                }
                if ($singleCategory == 'pkclient ') {

                    $this->db->or_where('pkclient', 'yes');
                }
                if ($singleCategory == 'priorityc ') {

                    $this->db->or_where('priorityc', 'yes');
                }
            }

            $this->db->group_end();
        }

        if (!empty($partnerType)) {

            $this->db->where_in('company_master.partnerType_id', $partnerType);
        }


        if (!empty($userType)) {
            $this->db->group_start();

            foreach ($userType as $singleuserType) {
                if ($singleuserType == 2) {

                    $this->db->where_in('user_details.admin_id', $users);
                }
                else if ($singleuserType == 4) {
                    $this->db->or_where_in('user_details.pst_co', $users);
                }
                else if ($singleuserType == 9 || $singleuserType == 13) {
                    $this->db->or_where_in('user_details.aadmin', $users);
                }
                else{

                    $this->db->where_in('user_details.user_id', $users);
                }
            }
            $this->db->group_end();
        }

        $this->db->where('CAST(updateddate AS DATE) >=', $sdate);
        $this->db->where('CAST(updateddate AS DATE) <=', $edate);
        // $this->db->where('tblcallevents.initiateddt != ', NULL);
        $this->db->order_by('user_details.name', 'ASC');
        $this->db->order_by('tblcallevents.cid_id', 'DESC');
        $this->db->order_by('tblcallevents.updateddate', 'ASC');
        $query = $this->db->get();
        // echo $this->db->last_query();die;
        return $query->result();

    }

    public function getStatusTaskTypeConversion($uid,$startDate,$endDate,$status_id,$userType,$users){

        // $query=$this->db->query("SELECT count(*) cont FROM tblcallevents WHERE user_id='$uid' and nextCFID!='0' and cast(updateddate as DATE) BETWEEN '$sd' and '$ed' and actiontype_id='$ac' and status_id!=nstatus_id");
        // return $query->result();


        $this->db->select("COUNT(*) count");
    
        $this->db->from('tblcallevents');
        $this->db->join('user_details', 'user_details.user_id=tblcallevents.user_id', 'left');

        if (!empty($userType)) {
            $this->db->group_start();

            foreach ($userType as $singleuserType) {
                if ($singleuserType == 2) {

                    $this->db->where_in('user_details.admin_id', $users);
                }
                else if ($singleuserType == 4) {
                    $this->db->or_where_in('user_details.pst_co', $users);
                }
                else if ($singleuserType == 9 || $singleuserType == 13) {
                    $this->db->or_where_in('user_details.aadmin', $users);
                }
                else{

                    $this->db->where_in('user_details.user_id', $users);
                }
            }
            $this->db->group_end();
        }

        $this->db->where('nextCFID !=','0');
        $this->db->where('status_id ',$status_id);
        // $this->db->where('status_id !=',' nstatus_id');

        $this->db->where('CAST(updateddate AS DATE) BETWEEN "'.$startDate.'" AND "'.$endDate.'"', NULL, FALSE);
        // LEFT JOIN  ON user_details.user_id=user_day.user_id
        // $this->db->group_by('wffo');
        $query = $this->db->get();
        // echo $this->db->last_query();die;
        return $query->result();
    }

    public function get_StatustaskTypeConversionByStatus($uid,$startDate,$endDate,$status_id,$status_id1,$userType,$users){

        // echo $status_id1;die;

        $this->db->select("COUNT(*) count");
    
        $this->db->from('tblcallevents');
        $this->db->join('user_details', 'user_details.user_id=tblcallevents.user_id', 'left');

        if (!empty($userType)) {
            $this->db->group_start();

            foreach ($userType as $singleuserType) {
                if ($singleuserType == 2) {

                    $this->db->where_in('user_details.admin_id', $users);
                }
                else if ($singleuserType == 4) {
                    $this->db->or_where_in('user_details.pst_co', $users);
                }
                else if ($singleuserType == 9 || $singleuserType == 13) {
                    $this->db->or_where_in('user_details.aadmin', $users);
                }
                else{

                    $this->db->where_in('user_details.user_id', $users);
                }
            }
            $this->db->group_end();
        }

        $this->db->where('nextCFID !=','0');
        $this->db->where('status_id ',$status_id);
        $this->db->where('status_id !=',' nstatus_id');
        $this->db->where('nstatus_id ',$status_id1);


        $this->db->where('CAST(updateddate AS DATE) BETWEEN "'.$startDate.'" AND "'.$endDate.'"', NULL, FALSE);

        $query = $this->db->get();
        // echo $this->db->last_query();die;
        return $query->result();

    }

    public function getTaskwiseDetails($selected_users,$Selected_userType,$sdate,$edate,$selectedTask,$selected_partnerType,$selected_category){

        // var_dump($selectedTask);die;
        $this->db->select('init_call.id');
        $this->db->from('init_call');
        $this->db->join('company_master', 'company_master.id = init_call.cmpid_id', 'left');
        $this->db->join('partner_master', 'partner_master.id = company_master.partnerType_id', 'left');
        

        if (!empty($selected_category)) {

            $this->db->group_start();

            foreach ($selected_category as $singleCategory) {

                if ($singleCategory == 'topspender') {

                    $this->db->or_where('focus_funnel', 'yes');
                }
                if ($singleCategory == 'focus_funnel') {

                    $this->db->or_where('focus_funnel', 'yes');
                }
                if ($singleCategory == 'upsell_client') {

                    $this->db->or_where('upsell_client', 'yes');
                }
                if ($singleCategory == 'keycompany') {

                    $this->db->or_where('keycompany', 'yes');
                }
                if ($singleCategory == 'pkclient ') {

                    $this->db->or_where('pkclient', 'yes');
                }
                if ($singleCategory == 'priorityc ') {

                    $this->db->or_where('priorityc', 'yes');
                }
            }

            $this->db->group_end();
        }

        if (!empty($selected_partnerType)) {

            $this->db->where_in('company_master.partnerType_id', $selected_partnerType);
        }

    
        $subquery = $this->db->get_compiled_select();

        $this->db->select('COUNT(*) as cont');
        $this->db->select('COUNT(CASE WHEN actontaken = "no" AND purpose_achieved = "no" THEN 1 END) as anpn');
        $this->db->select('COUNT(CASE WHEN actontaken = "yes" AND purpose_achieved = "no" THEN 1 END) as aypn');
        $this->db->select('COUNT(CASE WHEN actontaken = "yes" AND purpose_achieved = "yes" THEN 1 END) as aypy');
        $this->db->from('tblcallevents');
        $this->db->join('user_details', 'user_details.user_id = tblcallevents.user_id', 'left');
        $this->db->where("tblcallevents.cid_id IN ($subquery)", null, false);

        if (!empty($Selected_userType)) {
            $this->db->group_start();

            foreach ($Selected_userType as $singleuserType) {
                
                if ($singleuserType == 2) {

                    $this->db->where_in('user_details.admin_id', $selected_users);
                }
                else if ($singleuserType == 4) {
                    $this->db->or_where_in('user_details.pst_co', $selected_users);
                }
                else if ($singleuserType == 9 || $singleuserType == 13) {
                    $this->db->or_where_in('user_details.aadmin', $selected_users);
                }
                else{

                    $this->db->where_in('user_details.user_id', $selected_users);
                }
            }
            $this->db->group_end();
        }


        $this->db->where('nextCFID !=', '0');
        $this->db->where('CAST(updateddate AS DATE) >=', $sdate);
        $this->db->where('CAST(updateddate AS DATE) <=', $edate);
        $this->db->where('actiontype_id', $selectedTask);
        
        // Execute the query
        $query = $this->db->get();
        // echo $this->db->last_query();die;
        return $query->result();
    }

    public function getMeetingwiseDetails($selected_users,$Selected_userType,$sdate,$edate,$selectedmeetingType,$selected_partnerType,$selected_category){

        $this->db->select('init_call.id');
        $this->db->from('init_call');
        $this->db->join('company_master', 'company_master.id = init_call.cmpid_id', 'left');
        $this->db->join('partner_master', 'partner_master.id = company_master.partnerType_id', 'left');
        $this->db->join('user_details', 'user_details.user_id = init_call.mainbd', 'left');

        if (!empty($selected_category)) {

            $this->db->group_start();

            foreach ($selected_category as $singleCategory) {

                if ($singleCategory == 'topspender') {

                    $this->db->or_where('focus_funnel', 'yes');
                }
                if ($singleCategory == 'focus_funnel') {

                    $this->db->or_where('focus_funnel', 'yes');
                }
                if ($singleCategory == 'upsell_client') {

                    $this->db->or_where('upsell_client', 'yes');
                }
                if ($singleCategory == 'keycompany') {

                    $this->db->or_where('keycompany', 'yes');
                }
                if ($singleCategory == 'pkclient ') {

                    $this->db->or_where('pkclient', 'yes');
                }
                if ($singleCategory == 'priorityc ') {

                    $this->db->or_where('priorityc', 'yes');
                }
            }

            $this->db->group_end();
        }

        if (!empty($selected_partnerType)) {

            $this->db->where_in('company_master.partnerType_id', $selected_partnerType);
        }

        if (!empty($Selected_userType)) {
            $this->db->group_start();

            foreach ($Selected_userType as $singleuserType) {
                
                if ($singleuserType == 2) {

                    $this->db->where_in('user_details.admin_id', $selected_users);
                }
                else if ($singleuserType == 4) {
                    $this->db->or_where_in('user_details.pst_co', $selected_users);
                }
                else if ($singleuserType == 9 || $singleuserType == 13) {
                    $this->db->or_where_in('user_details.aadmin', $selected_users);
                }
                else{

                    $this->db->where_in('user_details.user_id', $selected_users);
                }
            }
            $this->db->group_end();
        }


        $subquery = $this->db->get_compiled_select();

        // echo $subquery;die;

        $this->db->select('remarks');
        $this->db->select('COUNT(*) count');
        $this->db->from('tblcallevents');
        $this->db->join('status', 'status.id = tblcallevents.status_id', 'left');
        $this->db->where('remarks !=', '');
        // $this->db->where('tblcallevents.cid_id IN ('.$subquery.')');
        $this->db->where("tblcallevents.cid_id IN ($subquery)", null, false); // Note: false prevents escaping

        $this->db->where('nextCFID != ', 0);
        $this->db->where('actiontype_id', $selectedmeetingType);
        $this->db->where('CAST(updateddate AS DATE) >=', $sdate);
        $this->db->where('CAST(updateddate AS DATE) <=', $edate);
        $this->db->group_by('remarks');

        $query = $this->db->get();

        // echo $this->db->last_query();die;
        return $query->result();


        // $query=$this->db->query("SELECT remarks,count(*) cont FROM tblcallevents LEFT JOIN status ON status.id=tblcallevents.status_id WHERE remarks!='' and tblcallevents.cid_id IN (Select id from init_call WHERE init_call.mainbd='$uid') and nextCFID!='0' and cast(updateddate as DATE) BETWEEN '$sdate' and '$edate' and (actiontype_id='4' or actiontype_id='3') Group By remarks");
    }

    public function getActionWiseTaskTable($selected_users,$Selected_userType,$sdate,$edate,$selected_partnerType,$selected_category,$selectedAction){

        $this->db->select('tblcallevents.*, company_master.id as cid,company_master.compname as compname, action.clr as aclr, s1.clr as bsclr, s2.clr as asclr, partner_master.clr as pclr, s1.name as bstatus, s1.name as astatus, action.name as acname, user_details.name as uname, partner_master.name as pname');
        $this->db->from('tblcallevents');
        $this->db->join('init_call', 'init_call.id = tblcallevents.cid_id', 'left');
        $this->db->join('status s3', 's3.id = init_call.cstatus', 'left');
        $this->db->join('user_details', 'user_details.user_id = init_call.mainbd', 'left');
        $this->db->join('company_master', 'company_master.id = init_call.cmpid_id', 'left');
        $this->db->join('status s1', 's1.id = tblcallevents.status_id', 'left');
        $this->db->join('status s2', 's2.id = tblcallevents.nstatus_id', 'left');
        $this->db->join('action', 'action.id = tblcallevents.actiontype_id', 'left');
        $this->db->join('partner_master', 'partner_master.id = company_master.partnerType_id', 'left');

        if (!empty($selected_category)) {

            $this->db->group_start();

            foreach ($selected_category as $singleCategory) {

                if ($singleCategory == 'topspender') {

                    $this->db->or_where('topspender', 'yes');
                }
                if ($singleCategory == 'focus_funnel') {

                    $this->db->or_where('focus_funnel', 'yes');
                }
                if ($singleCategory == 'upsell_client') {

                    $this->db->or_where('upsell_client', 'yes');
                }
                if ($singleCategory == 'keycompany') {

                    $this->db->or_where('keycompany', 'yes');
                }
                if ($singleCategory == 'pkclient ') {

                    $this->db->or_where('pkclient', 'yes');
                }
                if ($singleCategory == 'priorityc ') {

                    $this->db->or_where('priorityc', 'yes');
                }
            }

            $this->db->group_end();
        }

        if (!empty($selected_partnerType)) {

            $this->db->where_in('company_master.partnerType_id', $selected_partnerType);
        }


        if (!empty($Selected_userType)) {
            $this->db->group_start();

            foreach ($Selected_userType as $singleuserType) {
                if ($singleuserType == 2) {
                    $this->db->where_in('user_details.admin_id', $selected_users);
                }
                else if ($singleuserType == 4) {
                    $this->db->or_where_in('user_details.pst_co', $selected_users);
                }
                else if ($singleuserType == 9 || $singleuserType == 13) {
                    $this->db->or_where_in('user_details.aadmin', $selected_users);
                }
                else{
                    $this->db->where_in('user_details.user_id', $selected_users);
                }
            }
            $this->db->group_end();
        }

        $this->db->where('CAST(updateddate AS DATE) >=', $sdate);
        $this->db->where('CAST(updateddate AS DATE) <=', $edate);
        $this->db->where('actiontype_id', $selectedAction);
        // $this->db->where('tblcallevents.initiateddt != ', NULL);
        if ($selectedAction == 3 || $selectedAction == 4) {
            
            $this->db->where('remarks !=', '');
        }
        $this->db->where('nextCFID !=', '0');
        // $this->db->where('remarks !=', '');
        $this->db->order_by('user_details.name', 'ASC');
        $this->db->order_by('tblcallevents.cid_id', 'DESC');
        $this->db->order_by('tblcallevents.updateddate', 'ASC');
        $query = $this->db->get();
        // echo $this->db->last_query();die;
        return $query->result();
    }

    public function getTableDataTaskWiseDetail($selectedaction, $sdate, $edate, $selected_users, $selected_category, $selected_partnerType,$selected_userType,$actionAP)
    {

        // echo $sdate;die;
        // var_dump($selected_category);die;
        $this->db->select('ic.id ic_id');
        $this->db->select('ic.topspender topspender');
        $this->db->select('ic.focus_funnel focus_funnel');
        $this->db->select('ic.upsell_client upsell_client');
        $this->db->select('ic.keycompany keycompany');
        $this->db->select('ic.pkclient pkclient');
        $this->db->select('ic.priorityc priorityc');
        $this->db->select('status.clr stclr');
        $this->db->select('status.id stid');
        $this->db->select('status.name stname');
        $this->db->select('company_master.compname company_name');
        $this->db->select('company_master.address company_address');
        $this->db->select('city.city city');
        $this->db->select('states.state state');
        $this->db->select('partner_master.name partner_typeName');
        $this->db->select('partner_master.id partner_typeID');
        $this->db->select('partner_master.clr PartnerMasterclr');
        $this->db->from('init_call ic');
        $this->db->join('company_master', 'company_master.id = ic.cmpid_id', 'left');
        $this->db->join('tblcallevents', 'tblcallevents.cid_id = ic.id', 'left');

        $this->db->join('city', 'city.id = company_master.city', 'left');
        $this->db->join('states', 'states.id = company_master.state', 'left');
        $this->db->join('partner_master', 'partner_master.id = company_master.partnerType_id', 'left');
        $this->db->join('status', 'status.id = ic.cstatus', 'left');
        $this->db->join('user_details', 'user_details.user_id = ic.mainbd', 'left');

        if (!empty($selected_category)) {

            $this->db->group_start();

            foreach ($selected_category as $singleCategory) {

                if ($singleCategory == 'topspender') {

                    $this->db->or_where('focus_funnel', 'yes');
                }
                if ($singleCategory == 'focus_funnel') {

                    $this->db->or_where('focus_funnel', 'yes');
                }
                if ($singleCategory == 'upsell_client') {

                    $this->db->or_where('upsell_client', 'yes');
                }
                if ($singleCategory == 'keycompany') {

                    $this->db->or_where('keycompany', 'yes');
                }
                if ($singleCategory == 'pkclient ') {

                    $this->db->or_where('pkclient', 'yes');
                }
                if ($singleCategory == 'priorityc ') {

                    $this->db->or_where('priorityc', 'yes');
                }
            }

            $this->db->group_end();
        }

        if (!empty($selected_partnerType)) {

            $this->db->where_in('company_master.partnerType_id', $selected_partnerType);
        }

        // $this->db->where_in('ic.cstatus', $selectedStatus);

        if ($actionAP == 'ANPN') {
            
            $this->db->where('actontaken', 'no');
            $this->db->where('purpose_achieved', 'no');

        }elseif ($actionAP == 'AYPN') {
            
            $this->db->where('actontaken', 'yes');
            $this->db->where('purpose_achieved', 'no');

        }elseif($actionAP == 'AYPY') {
            
            $this->db->where('actontaken', 'yes');
            $this->db->where('purpose_achieved', 'yes');
        }

        if ($selected_userType == 2) {

            $this->db->where_in('user_details.admin_id', $selected_users);
        } elseif ($selected_userType == 4) {

            $this->db->where_in('pst_co', $selected_users);
        } elseif ($selected_userType == 9 || $selected_userType == 13) {

            $this->db->where_in('user_details.aadmin', $selected_users);
        } else {
            $this->db->where_in('user_details.user_id', $selected_users);
        }

        // if (!empty($selected_users)) {

        //     $this->db->where_in('user_details.aadmin', $selected_users);
        // }

        $this->db->where('CAST(tblcallevents.updateddate AS DATE) >=', "'$sdate'", FALSE);
        $this->db->where('CAST(tblcallevents.updateddate AS DATE) <=', "'$edate'", FALSE);
        $this->db->where('tblcallevents.actiontype_id', $selectedaction, FALSE);

        $query = $this->db->get();
        // echo $this->db->last_query();die;
        return $query->result();
    }

    public function get_bdRequest(){

        $db3 = $this->load->database('db3', TRUE);

        $query=$db3->query("SELECT DISTINCT `request_type` FROM bdrequest");
        return $query->result();

    }

    public function get_bdRequestByType($sdate,$edate,$selected_users,$rtype){

        $db3 = $this->load->database('db3', TRUE);

        // var_dump($selected_users);die;
        $db3->select('COUNT(*) count');
        
        $db3->from('bdrequest');

        if (!empty($selected_users)) {

            $db3->where_in('bd_id', $selected_users);
        }

        $db3->where('request_type', $rtype);
        $db3->where('CAST(sdatet AS DATE) >=', $sdate);
        $db3->where('CAST(sdatet AS DATE) <=', $edate);

        $query = $db3->get();

        return $query->result();
    }

    public function get_bdRequestByTypeandStage($sdate,$edate,$selected_users,$rtype,$j){
        
        $db3 = $this->load->database('db3', TRUE);
        
        if($j==0){

            $db3->select('COUNT(*) count');
            $db3->from('bdrequest');
            if (!empty($selected_users)) {

                $db3->where_in('bd_id', $selected_users);
            }

            // $db3->where_in('bd_id',$selected_users);
            $db3->where_in('request_type',$rtype);
            // $db3->where_not_in('id',"SELECT tid FROM bdtask");
            $db3->where('id NOT IN (SELECT tid FROM bdtask)', NULL, FALSE);

            $query = $db3->get();

            return $query->result();
        }

        if($j==1){
            // echo "hii";die;

            if (empty($selected_users)) {
                return []; // Handle the case where no users are selected
            }
            
            // Start building the main query
            $db3->select('COUNT(DISTINCT bdtask.tid) AS count');
            $db3->from('bdtask');
            
            // Join with bdrequest to filter by bd_id and request_type
            $db3->join('bdrequest', 'bdrequest.id = bdtask.tid', 'inner'); // Adjust the join condition as necessary
            
            // Add conditions
            $db3->where_in('bdrequest.bd_id', $selected_users);
            $db3->where('bdrequest.request_type', $rtype);
            $db3->where('bdtask.startt IS NULL');

            $db3->where('CAST(bdrequest.sdatet AS DATE) >=', $sdate);
            $db3->where('CAST(bdrequest.sdatet AS DATE) <=', $edate);

            $query = $db3->get();
            // echo $db3->last_query();die;
            return $query->result();
        }

        if($j==2){

            if (empty($selected_users)) {
                return []; // Handle the case where no users are selected
            }
            
            // Start building the main query
            $db3->select('COUNT(DISTINCT bdtask.tid) AS count');
            $db3->from('bdtask');
            
            // Join with bdrequest to filter by bd_id and request_type
            $db3->join('bdrequest', 'bdrequest.id = bdtask.tid', 'inner');
            
            // Add conditions
            $db3->where_in('bdrequest.bd_id', $selected_users);
            $db3->where('bdrequest.request_type', $rtype);
            $db3->where('bdtask.startt IS NOT NULL');
            $db3->where('bdtask.closet IS NULL');

            $db3->where('CAST(bdtask.sdatet AS DATE) >=', $sdate);
            $db3->where('CAST(bdtask.sdatet AS DATE) <=', $edate);

            // Execute the query
            $query = $db3->get();
            return $query->result();
        }
        if($j==3){

            if (empty($selected_users)) {
                return []; // Handle the case where no users are selected
            }
            
            // Start building the main query
            $db3->select('COUNT(*) AS count');
            $db3->from('bdrequest');
            
            // Join with bdtask
            $db3->join('bdtask', 'bdtask.tid = bdrequest.id', 'inner');
            
            // Add filtering conditions
            $db3->where_in('bdrequest.bd_id', $selected_users);
            $db3->where('bdrequest.request_type', $rtype);
            $db3->where('bdtask.startt IS NOT NULL');
            $db3->where('bdtask.closet IS NOT NULL');
            $db3->where('bdrequest.status', '0'); // Assuming status is from bdrequest

            $db3->where('CAST(bdrequest.sdatet AS DATE) >=', $sdate);
            $db3->where('CAST(bdrequest.sdatet AS DATE) <=', $edate);
            // Execute the query
            $query = $db3->get();

            
            return $query->result();
        }

        // return $query->result();
    }

    public function get_bdRequestTableData($users,$sdate,$edate){
        $db3 = $this->load->database('db3', TRUE);


        $db3->select('bdrequest.*, GROUP_CONCAT(user_detail.fullname) AS pia');
        $db3->from('bdrequest');

        // Join with bdtask and user_detail
        $db3->join('bdtask', 'bdtask.tid = bdrequest.id', 'left'); // Left join to get all bdrequest records
        $db3->join('user_detail', 'user_detail.id = bdtask.uid', 'left'); // Left join to get user details

        // Add filtering conditions
        // $db3->where_in('bdrequest.request_type', "SELECT DISTINCT `request_type` FROM bdrequest");
        $db3->where('bdrequest.request_type IN (SELECT DISTINCT request_type FROM bdrequest)', NULL, FALSE);

        if (!empty($users)) {
            # code...
            $db3->where_in('bdrequest.bd_id', $users);
        }

        // Group by bdrequest.id to ensure GROUP_CONCAT works correctly
        $db3->group_by('bdrequest.id');

        // Order by sdatet
        $db3->order_by('bdrequest.sdatet', 'DESC');

        // Execute the query
        $query = $db3->get();
        // echo $db3->last_query();die;

        return $query->result();
    }

    public function get_RIDDayWise($sdate,$edate,$selected_user,$selected_status){

        $db3 = $this->load->database('db3', TRUE);

        $db3->select("CASE 
                        WHEN DATEDIFF(CURDATE(), replacereq.sdatet) < 5 THEN 'Less than 5 days' 
                        WHEN DATEDIFF(CURDATE(), replacereq.sdatet) = 5 THEN '5 days' 
                        WHEN DATEDIFF(CURDATE(), replacereq.sdatet) <= 10 THEN '10 days' 
                        WHEN DATEDIFF(CURDATE(), replacereq.sdatet) <= 20 THEN '20 days' 
                        WHEN DATEDIFF(CURDATE(), replacereq.sdatet) <= 30 THEN '30 days' 
                        WHEN DATEDIFF(CURDATE(), replacereq.sdatet) <= 60 THEN '60 days' 
                        WHEN DATEDIFF(CURDATE(), replacereq.sdatet) <= 120 THEN '120 days' 
                        WHEN DATEDIFF(CURDATE(), replacereq.sdatet) <= 180 THEN '180 days' 
                        ELSE 'More than 180 days' 
                        END AS day_category, 
                    COUNT(*) AS count");
            
              $db3->from('replacereq');

            // Join with spd and client_handover
            $db3->join('spd', 'spd.id = replacereq.sid', 'left');
            $db3->join('client_handover', 'client_handover.projectcode = spd.project_code', 'left');

            $db3->where_in('client_handover.bd_id', $selected_user);

            if(!empty($selected_status)){

                $db3->where('replacereq.status', $selected_status);
            }

            $db3->where('CAST(replacereq.sdatet AS DATE) >=', $sdate);
            $db3->where('CAST(replacereq.sdatet AS DATE) <=', $edate);
            // Group by the case statement result
            $db3->group_by('day_category');

            // Execute the query
            $query = $db3->get();

            // echo $db3->last_query();die;
            return $query->result();
    }

public function getActionByID($id){

        $this->db->select('*');
        $this->db->from('action');
        $this->db->where('id',$id);
        // $this->db->where('CAST(cdate AS DATE) >=', $date);
        // $this->db->where('approved_status','Reject');

        $query = $this->db->get();
            //  echo  $this->db->last_query(); die;
        return $query->result();

    }


}