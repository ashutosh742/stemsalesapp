<!DOCTYPE html>

<html lang="en">

  <head>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Annual Company Report | STEM APP| WebAPP</title>

    <!-- Google Font: Source Sans Pro -->

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">

    <!-- Font Awesome -->

    <link rel="stylesheet" href="<?=base_url();?>assets/css/all.min.css">

    <!-- Ionicons -->

    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">

    <!-- Tempusdominus Bootstrap 4 -->

    <link rel="stylesheet" href="<?=base_url();?>assets/css/tempusdominus-bootstrap-4.min.css">

    <!-- iCheck -->

    <link rel="stylesheet" href="<?=base_url();?>assets/css/icheck-bootstrap.min.css">

    <!-- JQVMap -->

    <link rel="stylesheet" href="<?=base_url();?>assets/css/jqvmap.min.css">

    <!-- Theme style -->

    <link rel="stylesheet" href="<?=base_url();?>assets/css/adminlte.min.css">

    <!-- overlayScrollbars -->

    <link rel="stylesheet" href="<?=base_url();?>assets/css/OverlayScrollbars.min.css">

    <!-- Daterange picker -->

    <link rel="stylesheet" href="<?=base_url();?>assets/css/daterangepicker.css">

    <!-- summernote -->

    <link rel="stylesheet" href="<?=base_url();?>assets/css/summernote-bs4.min.css">

    <style>

      .ApprovedStatus{

      padding:6px 10px;

      border-radius:4px;

      }

      .ApprovedStatus.Pending{

      background:orange;

      color:white;

      }

      .ApprovedStatus.Reject{

      background:red;

      color:white;

      }

      .ApprovedStatus.approved{

      background:green;

      color:white;

      }

       .colapsboxsha{

         border-radius:22px;

         box-shadow: rgba(0, 0, 0, 0.25) 0px 0.0625em 0.0625em, rgba(0, 0, 0, 0.25) 0px 0.125em 0.5em, rgba(255, 255, 255, 0.1) 0px 0px 0px 1px inset; 

      }

     .modal.fade.show{

      background: rgba(100, 200, 150, 0.4);

     }

      

    </style>

  </head>

  <body class="hold-transition sidebar-mini layout-fixed">

    <div class="wrapper">

      <!-- Preloader -->

      <!-- Navbar -->

      <?php require('nav.php');?>

      <!-- /.navbar -->

      <!-- Content Wrapper. Contains page content -->

      <div class="content-wrapper">

        <!-- Content Header (Page header) -->

        <div class="content-header">

          <div class="container-fluid">

            <div class="row mb-2">

              <div class="col-sm-6">

              </div>

              <!-- /.col -->

              <div class="col-sm-6">

                <ol class="breadcrumb float-sm-right">

                  <h4></h4>

                </ol>

              </div>

              <!-- /.col -->

            </div>

            <!-- /.row -->

          </div>

          <!-- /.container-fluid -->

        </div>

        <!-- /.content-header -->

        <section class="content">

          <div class="container-fluid">

            <div class="row p-3">

              <div class="col-sm col-md-12 col-lg-12 m-auto">

                <?php if ($this->session->flashdata('action_message')): ?>

                <div class="alert alert-success alert-dismissible fade show" role="alert">

                  <strong> <?php echo $this->session->flashdata('action_message'); ?></strong> 

                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">

                  <span aria-hidden="true">&times;</span>

                  </button>

                </div>

                

                <?php endif; ?>

                <div class="card card-primary card-outline">

                  <div class="card-body box-profile">

                        <?php $utype = $this->Menu_model->get_userbyid($userid); ?>



                        <div class="bg-warning colapsboxsha text-center mt-2 mb-2">

                          <h3><i><?= $utype[0]->name .' - '.$userid ?> - and his team's report</i></h3>

                        </div>

                        

                        <?php

                        // echo $teamid;

                        
                        if($uid == 2){
                          $getbdpstname = $this->Menu_model->get_userbyid($userid);
                          $bdpstname = $getbdpstname[0]->name;
                          $getbdteamData = $this->Menu_model->companyreviebdwiseDataGroup3($teamid,$bdpstname);
                          // echo $str = $this->db->last_query();
                          // die;
                         }else{
                          $getbdteamData = $this->Menu_model->companyreviebdwiseDataGroup2($teamid);
                         }


                        

                          $groupedByCompanyId = [];

                          

                          $cn = 1;

                          $cnt = 1;

                         foreach ($getbdteamData as $item) {

                                $groupedByCompanyId[$item->company_id][] = $item;

                            }

                            

                            

                            $objectscnt = 1;

                            $dataarray = [];

                             foreach ($groupedByCompanyId as $objectsArray) {

                                    $objectsArraycnt =  sizeof($objectsArray);

                                        if($objectsArraycnt == 1){

                                           foreach($objectsArray as $d){

                                               $dataarray[] = $d;

                                           }

                                        }

                                    }

                              

                            //   foreach($dataarray as $ds){

                            //                   echo "<pre>";

                            //                 print_r($ds->id);

                            //               }

                        

                            // die;

                          

                        $k = 1;

                       ?>

                        <div class="btn btn-primary colapsboxsha col-md-12 col-lg-12 mt-2 d-flex justify-content-between align-items-center" data-toggle="collapse" href="#collapseExample<?=$k?>" role="button" aria-expanded="false" aria-controls="collapseExample<?=$k?>">

                         [<?= $k ?>] &nbsp;&nbsp; <?=$this->Menu_model->get_cdbyid($companyId)[0]->compname .' - '.$companyId ?>  - Annual Company Report: 

                        </div>

                        <div class="collapse mt-3" id="collapseExample<?=$k?>">

                          <div class="card card-body" style="background: azure;"  >

                            <div class="ApprovedStatus">

                              <div class="table-responsive">

                                <table id="exampledata<?=$k?>" class="table table-striped table-bordered" cellspacing="0" width="100%">

                                  <thead>

                                    <tr>

                                      <th>sr. no</th>

                                      <th>tid</th>

                                      <th>Company Name</th>

                                      <th>Company Id</th>

                                      <th>Review User Names</th>

                                      <th>Review User ID</th>

                                      <th>Last Status</th>

                                      <th>Current Status</th>

                                      <th>Current year Status</th>

                                      <th>Current year Status ID</th>

                                      <th>Is this Location is Right?</th>

                                      <th>Updated Location is</th>

                                      <th>Will be keeping this company with you?</th>

                                      <th>Remark why to not keeping company</th>

                                      <th>Did you have any face to face or virtual meeting with client?</th>

                                      <th>Cluster Name</th>

                                      <th>Cluster ID</th>

                                      <th>Focus And Positive Key Client For FY 2024-25</th>

                                      <th>Focus Lead For this Quarter 1?</th>

                                      <th>Top Spender For this Year?</th>

                                      <th>Is This Upsell Client?</th>

                                      <th> Do You Need Any Intervention or Support From ?</th>

                                      <th>Intervention ID</th>

                                      <th>What Support You Required In Detail?</th>

                                      <th> No of school</th>

                                      <th>Expected Revenue</th>

                                      <th>Reachout</th>

                                      <th>Tentative</th>

                                      <th>Positive</th>

                                      <th>Very Positive</th>

                                      <th>Positive nap</th>

                                      <th>Closure</th>

                                      <th>PST Approved</th>

                                      <th>PST Name</th>

                                      <th>PST Remark</th>

                                      <th>Cluster Manager Approved</th>

                                      <th>Cluster Manager Name</th>

                                      <th>Cluster Manager Remark</th>

                                      <!--<th>Is Admin Approved</th>-->

                                      <th>Actions</th>

                                    </tr>

                                  </thead>

                                  <tbody>

                                    <?php 

                                    $j=1; 

                           

                                        foreach ($dataarray as $object) {

                                            

                                            // echo "<pre>";

                                            // print_r($object);

                                            // die;

                                            

                                        $companyStatus = $this->Menu_model->companyStatusInitcall($object->company_id);

                                        // $revTarget = $this->Menu_model->companyannualreviewtarget($object->company_id,$object->user_id);

                                        $cstatus = $companyStatus->cstatus;

                                        $lstatus = $companyStatus->lstatus;

                                        $noofschools = $companyStatus->noofschools;

                                        $topspender = $companyStatus->topspender;

                                        $upsell_client = $companyStatus->upsell_client;

                                        $keycompany = $companyStatus->keycompany;

                                        $focus_funnel = $companyStatus->focus_funnel;

                                        $currentStatus = $this->Menu_model->get_statusbyid($cstatus);

                                        $currentStatusname = $currentStatus[0]->name; 

                                        $lastStatus = $this->Menu_model->get_statusbyid($lstatus);

                                        $lastStatusname = $lastStatus[0]->name; 

                                        ?>

                                    <tr>

                                      <td><?= $j; ?></td>

                                       <td>

                                       <?= $object->id ?>

                                      </td>

                                      <td>

                                        <a target="_BLANK" href="https://stemapp.in/Menu/AnnualReviewDetails1/<?=$object->company_id ?>/<?=$object->user_id ?>">

                                        <?=$this->Menu_model->get_cdbyid($object->company_id)[0]->compname .' - '.$object->company_id?> 

                                        </a>

                                      </td>

                                      <td>

                                       <?= $object->company_id ?>

                                      </td>

                                        <td>

                                           <?php  $userName = $this->Menu_model->get_userbyid($object->user_id); ?>

                                            <?= $userName[0]->name; ?>

                                            

                                        </td>

                                         <td>

                                           <?= $object->user_id ?>

                                        </td>

                                      <td><?= $lastStatusname ?></td>

                                      <td><?= $currentStatusname ?></td>

                                      

                                      <td><?= $this->Menu_model->get_statusbyid($object->status)[0]->name ?></td>

                                      <td><?= $object->status ?></td>

                                      <td> <?= $this->Menu_model->getlocationinfoOfAnRev($object->user_id,$object->company_id)[0]->answer; ?></td>

                                       <td>

                                          <?php 

                                            $locanswer =  $this->Menu_model->getlocationinfoOfAnRev($object->user_id,$object->company_id)[0]->answer; 

                                          if($locanswer == 'no' or $locanswer == 'No' or $locanswer == 'NO'){

                                              $uplocanswer =  $this->Menu_model->getlocationinfoOfAnRevew($object->user_id,$object->company_id)[0]->location;

                                              echo $uplocanswer;

                                          }

                                          ?>

                                          </td>

                                      <td>

                                            <?php if($object->keep_company == 'yes'){ ?>

                                            <span class="p-2 bg-success mr-2"><?= $object->keep_company; ?></span>

                                            <?php }else{ ?>

                                            <span class="p-2 bg-danger mr-2"><?= $object->keep_company; ?></span>

                                            <?php }?>

                                     </td>

                                    

                                      <td>

                                          

                                          <?php 

                                          if($object->keep_company == 'no'){

                                              if($object->keepremark !==''){

                                                  echo $object->keepremark;

                                              }else{

                                                  echo "not put remarks";

                                              }

                                          }else{

                                              echo $object->keepremark;

                                          } 

                                          

                                          ?>

                                           </td>

                                    

                                      <td>

                                            <?php if($object->vmeeting == 'yes'){ ?>

                                            <span class="p-2 bg-success mr-2"><?= $object->vmeeting; ?></span>

                                            <?php }else{ ?>

                                            <span class="p-2 bg-danger mr-2"><?= $object->vmeeting; ?></span>

                                            <?php }?>

                                      </td>

                                      <td>

                                        <?php

                                          if($object->cluster_id != 0){

                                              $clusterData = $this->Menu_model->getClusterByid($object->cluster_id);

                                            echo $clusterData[0]->clustername;

                                          }else{ ?>

                                          <span class="p-2 bg-danger mr-2">no&nbsp;required</span>

                                         <?php } ?>

                                      </td>

                                      <td><?= $object->cluster_id ?></td>

                                      <td>

                                          <?php if($object->keep_company == 'yes'){ ?>

                                          

                                             <?php if($object->focuspositive == 'yes'){ ?>

                                            <span class="p-2 bg-success mr-2"><?= $object->focuspositive; ?></span>

                                            <?php }else{ ?>

                                            <span class="p-2 bg-danger mr-2"><?= $object->focuspositive; ?></span>

                                            <?php }?>

                                        <?php } ?>

                                    </td>

                                    <td>

                                          <?php if($object->keep_company == 'yes'){ ?>

                                          

                                             <?php if($object->focusfunnel == 'yes'){ ?>

                                            <span class="p-2 bg-success mr-2"><?= $object->focusfunnel; ?></span>

                                            <?php }else{ ?>

                                            <span class="p-2 bg-danger mr-2"><?= $object->focusfunnel; ?></span>

                                            <?php }?>

                                        <?php } ?>

                                      </td>

                                      <td>

                                          <?php if($object->keep_company == 'yes'){ ?>

                                          

                                             <?php if($object->topspender == 'yes'){ ?>

                                            <span class="p-2 bg-success mr-2"><?= $object->topspender; ?></span>

                                            <?php }else{ ?>

                                            <span class="p-2 bg-danger mr-2"><?= $object->topspender; ?></span>

                                            <?php }?>

                                        <?php } ?>

                                      </td>

                                       <td>

                                          <?php if($object->keep_company == 'yes'){ ?>

                                          

                                             <?php if($object->upsell == 'yes'){ ?>

                                            <span class="p-2 bg-success mr-2"><?= $object->upsell; ?></span>

                                            <?php }else{ ?>

                                            <span class="p-2 bg-danger mr-2"><?= $object->upsell; ?></span>

                                            <?php }?>

                                        <?php } ?>

                                      </td>

                                       <td><?php if($object->keep_company == 'yes'){

                                        $utype = $this->Menu_model->get_userbyid($object->intervention);

                                        echo $utype[0]->name;}

                                        ?></td>

                                        <td><?= $object->intervention ?></td>

                                        <td>

                                          <?php if($object->keep_company == 'yes'){ ?>

                                           <span class="p-2 text-success mr-2"><?= $object->support; ?></span>

                                           <?php } ?>

                                      </td>

                                       <td><?=  $object->noofschool ?></td>

                                      <td><?=  $object->revenue ?> ₹ </td>

                                      <td><?= $object->reachout ?></td>

                                      <td><?= $object->tentative ?></td>

                                      <td><?= $object->positive ?></td>

                                      <td><?= $object->very_positive ?></td>

                                      <td><?= $object->positivenap ?></td>

                                      <td><?= $object->closure ?></td>

                                       <td>

                                           <?php

                                            if($object->pst_approve == ''){ ?>

                                            <span class="p-2 bg-warning mr-2">Pending</span>

                                            <?php }else if($object->pst_approve == 'Approve'){ ?>

                                            <span class="p-2 bg-success mr-2">Approved</span>

                                            <?php }else{ ?>

                                            <span class="p-2 bg-danger mr-2">Reject</span>

                                            <?php }?>

                                        </td>

                                      <td><?= $object->pst_name ?></td>

                                      <td><?= $object->pst_remark ?></td>

                                      <td>

                                           <?php if($object->cluster_approve == ''){ ?>

                                            <span class="p-2 bg-warning mr-2">Pending</span>

                                            <?php }else if($object->cluster_approve == 'Approve'){ ?>

                                            <span class="p-2 bg-success mr-2">Approved</span>

                                            <?php }else{ ?>

                                            <span class="p-2 bg-danger mr-2">Reject</span>

                                            <?php }?>

                                    </td>

                                      <td><?= $object->cluster_name ?></td>

                                      <td><?= $object->cluster_remark ?></td>

                                    <td>

                                        Pending

                                      </td>

                                    </tr>

                                    <?php $j++;

                                    

                                    }

             

                                    ?>

                                  </tbody>

                                </table>

                              </div>



                            </div>

                            <br>

                          </div>

                                    

                        </div>

              

                  </div>

                </div>

              </div>

            </div>

          </div>

        </section>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

        <script type='text/javascript'>

            function RejectButton(mid,id,val){

            $('#exampleModalCenter'+mid).modal('show');

            $('#exampleModalCenter'+mid+' #rejectid').val(id);

            }

            

            function Reject(mid,id,val){

                // alert(mid);

                // alert('#exampleModalCenterdata'+mid);

                $('#exampleModalCenterdata').modal('show');

                $('#rejectid').val(id);

            // $('#exampleModalCenterdata'+mid).modal('show');

            // $('#exampleModalCenterdata'+mid+' #rejectid').val(id);

            }

        </script>

        <!-- /.row (main row) -->

      </div>

      <!-- /.container-fluid -->

      <!-- </section> -->

      <!-- /.content -->

    </div>

    <!-- /.content-wrapper -->

    <footer class="main-footer">

      <strong>Copyright &copy; 2021-2022 <a href="<?=base_url();?>">Stemlearning</a>.</strong>

      All rights reserved.

      <div class="float-right d-none d-sm-inline-block">

        <b>Version</b> 1.0

      </div>

    </footer>

    <!-- Control Sidebar -->

    <aside class="control-sidebar control-sidebar-dark">

      <!-- Control sidebar content goes here -->

    </aside>

    <!-- /.control-sidebar -->

    </div>

    <!-- ./wrapper -->

    <!-- jQuery -->

    <script src="<?=base_url();?>assets/js/jquery.min.js"></script>

    <!-- jQuery UI 1.11.4 -->

    <script src="<?=base_url();?>assets/js/jquery-ui.min.js"></script>

    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->

    <script>

      $.widget.bridge('uibutton', $.ui.button)

    </script>

    <!-- Bootstrap 4 -->

    <script src="<?=base_url();?>assets/js/bootstrap.bundle.min.js"></script>

    <!-- ChartJS -->

    <script src="<?=base_url();?>assets/js/Chart.min.js"></script>

    <!-- Sparkline -->

    <script src="<?=base_url();?>assets/js/sparkline.js"></script>

    <!-- JQVMap -->

    <script src="<?=base_url();?>assets/js/jquery.vmap.min.js"></script>

    <script src="<?=base_url();?>assets/js/jquery.vmap.usa.js"></script>

    <!-- jQuery Knob Chart -->

    <script src="plugins/jquery-knob/jquery.knob.min.js"></script>

    <!-- daterangepicker -->

    <script src="<?=base_url();?>assets/js/moment.min.js"></script>

    <script src="<?=base_url();?>assets/js/daterangepicker.js"></script>

    <!-- Tempusdominus Bootstrap 4 -->

    <script src="<?=base_url();?>assets/js/tempusdominus-bootstrap-4.min.js"></script>

    <!-- Summernote -->

    <script src="<?=base_url();?>assets/js/summernote-bs4.min.js"></script>

    <!-- overlayScrollbars -->

    <script src="<?=base_url();?>assets/js/jquery.overlayScrollbars.min.js"></script>

    <!-- DataTables  & Plugins -->

    <script src="<?=base_url();?>assets/js/jquery.dataTables.min.js"></script>

    <script src="<?=base_url();?>assets/js/dataTables.bootstrap4.min.js"></script>

    <script src="<?=base_url();?>assets/js/dataTables.responsive.min.js"></script>

    <script src="<?=base_url();?>assets/js/responsive.bootstrap4.min.js"></script>

    <script src="<?=base_url();?>assets/js/dataTables.buttons.min.js"></script>

    <script src="<?=base_url();?>assets/js/buttons.bootstrap4.min.js"></script>

    <script src="<?=base_url();?>assets/js/jszip.min.js"></script>

    <script src="<?=base_url();?>assets/js/pdfmake.min.js"></script>

    <script src="<?=base_url();?>assets/js/vfs_fonts.js"></script>

    <script src="<?=base_url();?>assets/js/buttons.html5.min.js"></script>

    <script src="<?=base_url();?>assets/js/buttons.print.min.js"></script>

    <script src="<?=base_url();?>assets/js/buttons.colVis.min.js"></script>

    <!-- AdminLTE App -->

    <script src="<?=base_url();?>assets/js/adminlte.js"></script>

    <!-- AdminLTE dashboard demo (This is only for demo purposes) -->

    <script src="<?=base_url();?>assets/js/dashboard.js"></script>

    <script>



//  $( document ).ready(function() {

//       var compcount = $("#compcount").val();

      

//       for(var i = 1; i <= compcount; i++)

//       {

//       var str = 'exampledata'+i+'_wrapper';

//       $("#exampledata"+i).DataTable({

//       "responsive": true, 

//       "lengthChange": true, 

//       "autoWidth": true,

//       "searching": true,

//       "ordering": true,

//       "info": true,

//       "paging": true,

//       "dom": 'Bfrtip', 

//       "buttons": [

//       'csv', 'excel'

//       ]

//       }).buttons().container().appendTo('#'+str+' .col-md-6:eq(0)');

//       }

      

//       });

      

      

      $("#exampledata").DataTable({

      "responsive": false, "lengthChange": false, "autoWidth": false,

      "buttons": ["copy", "csv", "excel", "pdf", "print"]

      }).buttons().container().appendTo('#exampledata_wrapper .col-md-6:eq(0)');

      

      $("#exampledata1").DataTable({

      "responsive": false, "lengthChange": false, "autoWidth": false,

      "buttons": ["copy", "csv", "excel", "pdf", "print"]

      }).buttons().container().appendTo('#exampledata1_wrapper .col-md-6:eq(0)');

      

    </script>

  </body>

</html>