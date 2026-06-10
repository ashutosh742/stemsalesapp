<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="wi$dth=device-wi$dth, initial-scale=1">
    <title>Handover Details | STEM APP | WebAPP</title>
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
    <!-- DataTables -->
    <link rel="stylesheet" href="<?=base_url();?>assets/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="<?=base_url();?>assets/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="<?=base_url();?>assets/css/buttons.bootstrap4.min.css">
    <style>
      .scrollme {
      overflow-x: auto;
      }
      tr,td{
      font-weight: bold;
      }
      .card-header{
      background: floralwhite;
      }


         .custom-btn {
            /* width: 130px;
            height: 40px; */
            color: #fff;
            border-radius: 5px;
            /* padding: 10px 25px; */
            font-family: Lato, sans-serif;
            font-weight: 500;
            background: 0 0;
            cursor: pointer;
            transition: .3s;
            position: relative;
            display: inline-block;
            box-shadow: inset 2px 2px 2px 0 rgba(255,255,255,.5), 7px 7px 20px 0 rgba(0,0,0,.1), 4px 4px 5px 0 rgba(0,0,0,.1);
            outline: 0;
        }

        .btn-11 {
            border: none;
            background: #212ffb;
            background: linear-gradient(0deg, #3e21fb 0, #4c5cea 100%);
            color: #fff;
            overflow: hidden;
            width: fit-content;
        }

        .btn-11:hover {
            text-decoration: none;
            color: #fff;
            opacity: .7;
        }

        .btn-11:before {
            position: absolute;
            content: '';
            display: inline-block;
            top: -180px;
            left: 0;
            width: 30px;
            height: 100%;
            background-color: #fff;
            animation: 3s ease-in-out infinite shiny-btn1;
        }

        .btn-11:active {
            box-shadow: 4px 4px 6px 0 rgba(255,255,255,.3), -4px -4px 6px 0 rgba(116,125,136,.2), inset -4px -4px 6px 0 rgba(255,255,255,.2), inset 4px 4px 6px 0 rgba(0,0,0,.2);
        }

        @keyframes shiny-btn1 {
            0% { transform: scale(0) rotate(45deg); opacity: 0; }
            80% { transform: scale(0) rotate(45deg); opacity: .5; }
            81% { transform: scale(4) rotate(45deg); opacity: 1; }
            100% { transform: scale(50) rotate(45deg); opacity: 0; }
        }
      
        .btn-11.partnearray{
          background: navy!important;
        }
        .btn-11.categoryarray{
          background: #ff007f!important;
        }
    
    </style>
  </head>
  <body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
      <!-- Preloader -->
      <!-- Navbar -->
      <?php $this->load->view($dep_name.'/nav.php');?>
      <!-- /.navbar -->
      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <section class="content">
          <div class="container-fluid">
              <h5 class="card-header text-center">
                  <?php if ($this->session->flashdata('success_message')): ?>
                  <div class="alert alert-success alert-dismissible" role="alert">
                    <?= $this->session->flashdata('success_message'); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>
                  <?php endif; ?>
                  <?php if ($this->session->flashdata('error_message')): ?>
                  <div class="alert alert-danger alert-dismissible" role="alert">
                    <?= $this->session->flashdata('error_message'); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>
                  <?php endif; ?>
                </h5>
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                    <h3 class="text-center m-3">🏷️ All Handover Details </h3>
                    <hr style="width:25%;">
         
                 
                      <div class="table-responsive text-nowrap">
                  <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead class="thead-dark">
                        <tr>
                            <th>🔢 Sr No</th>
                            <th>🆔 Handover ID</th>
                            <th>📅 Date</th>
                            <th>👤 Client Name</th>
                            <th>🔑 Client ID</th>
                            <th>🏷️ Project Code</th>
                            <th>🔢 Project Code ID</th>
                            <th>🤝 Mediator</th>
                            <th>🏫 Schools</th>
                            <th>📝 School Remark</th>
                            <th>📍 Location</th>
                            <th>🏙️ City</th>
                            <th>🌍 State</th>
                            <th>👀 SPD Identify By</th>
                            <th>🏗️ Infrastructure</th>
                            <th>🖼️ Logo</th>
                            <th>👔 Contact Person</th>
                            <th>📞 Contact No</th>
                            <th>👥 Alt Contact Person</th>
                            <th>☎️ Alt Contact No</th>
                            <th>🌐 Language</th>
                            <th>🛠️ Expected Installation</th>
                            <th>📄 Project Tenure</th>
                            <th>🏗️ Project Type</th>
                            <th>💬 Comments</th>
                            
                            <th>📆 Project Year</th>
                            <th>🆔 BD Name</th>
                            <th>💳 Sales CID</th>
                            <th>🎭 Backdrop</th>
                            <th>🎨 Design</th>
                            <th>🖌️ Designed</th>
                            <th>🗒️ Remark</th>
                            
                            <th>📡 FTT Type</th>
                            <th>🚀 Install Date</th>
                            <th>♻️ UpdatedT</th>
 
                            <th>⏰ Handover Approved Date</th>

                            <?php if(in_array($user['type_id'],[1,2])){?> 
                              <th>⏰ PRO Name</th>
                              <th>⏰ Change PRO</th>
                            <?php } ?>


                            <th>🕒 Created At</th>
                            <th>♻️ Updated At</th>
                            <th>⚡ Approved Status</th>
                            <th>📌 BD Request</th>

                            <!-- <th>🚀 Handover To Account</th>
                            <th>🔥 Payment Terms</th> -->

                            <th>🔒 Handover Closed Status</th>
                            <th>📅 Handover Closed Date</th>
                            <th>📝 Handover Closed Remarks</th>
                            <th>👤 Handover Closed By</th>
                            <th>📈 Project Progress Timeline</th>

                            <th>📌 View Details</th>
                        </tr>
                    </thead>
                    <tbody>
                      <?php $j =1; foreach ($handoverDatas as $row): 
                      
                        $r = rand(150, 255);
                        $g = rand(150, 255);
                        $b = rand(150, 255);
                        $backgroundColor = "rgba($r, $g, $b,.2)";
                        $hue        = rand(0, 360); // Full color wheel
                        $saturation = rand(60, 100); // High saturation for rich colors
                        $lightness  = rand(30, 45); // Low lightness for a deeper tone
                        $backgroundColorNew = "hsl($hue, $saturation%, $lightness%)";

                        // $handoverAccount    = $this->Menu_model->GetHandoverToAccountDetailsBYHid($row->id);
                        // $handoverAccountCnt = sizeof($handoverAccount);
                        // if ($handoverAccountCnt > 0) {
                        //     $handoverAccountDone = '<span class="bg-success p-1">✅ Done</span>';
                        // } else {
                        //     $handoverAccountDone = '<span class="bg-warning p-1">⏳ Pending</span>';
                        // }

                        // $payterm      = '<span class="bg-warning p-1">⏳ Pending</span>';
                        //  if ($handoverAccountCnt > 0) {
                        //     $wono     = $handoverAccount[0]->wono;
                        //     $porno    = $handoverAccount[0]->porno;
                        //     $gstno    = $handoverAccount[0]->gstno;
                        //     $payterm  = $handoverAccount[0]->payterm;
                        //     $mou      = $handoverAccount[0]->mou;
                        //  }

                        $pro_uid      = $row->pro_uid;
                        // $operationUser =  $this->Menu_model->getOperationUserByUID($pro_uid);

                        ?>
                    <tr style="background-color: <?php echo $backgroundColor; ?>;color:<?=$backgroundColorNew;?>!important" class='text-center'>
                            <td><?= $j++ ?></td>
                            <td><?= $row->id ?></td>
                            <td><?= $row->sdatet ?></td>
                            <td><?= $row->client_name ?></td>
                            <td>
                              <?php
                                if(!empty($row->sales_cid)){
                                ?>
                                <a target='_blank' href="https://stemapp.in/Menu/CompanyDetails/<?= $row->sales_cid; ?>"><?= $row->sales_cid; ?></a>
                                <?php
                                }else{
                                ?>
                                <a target='_blank' href="https://stemapp.in/Menu/CompanyDetails/<?= $row->client_id; ?>"><?= $row->client_id; ?></a>
                                <?php 
                                }
                                ?>
                              </td>
                            <td>
                                <a target="_BLANK" href="https://stemoppapp.in/Menu/ProjectProfileDetails/<?=$row->id?>/<?=$user['user_id']?>"><?= $row->projectcode ?></a>
                            </td>
                            <td><?= $row->projectcode_id ?></td>
                            <td><?= $row->mediator ?: "❌ N/A" ?></td>
                            <td><?= $row->noofschool ?></td>
                            <td><?= $row->schoolremark ?: "❌ N/A" ?></td>
                            <td><?= $row->location ?: "❌ N/A" ?></td>
                            <td><?= $row->city ?: "❌ N/A" ?></td>
                            <td><?= $row->state ?: "❌ N/A" ?></td>
                            <td><?= $row->spd_identify_by ?: "❌ N/A" ?></td>
                            <td><?= $row->infrastructure ?: "❌ N/A" ?></td>

                            <td>
                               <?php if(!empty($row->logo) && $row->logo !='NA'){ ?>
                                    <a class="bg-success p-1 text-white" target="_BLANK" href="https://stemapp.in/<?=$row->logo?>">view</a>
                               <?php }else{ ?>
                                    ❌ N/A 
                              <?php } ?>
                            </td>

                            <td><?= $row->contact_person ?: "❌ N/A" ?></td>
                            <td><?= $row->cp_mno ?: "❌ N/A" ?></td>
                            <td><?= $row->acontact_person ?: "❌ N/A" ?></td>
                            <td><?= $row->acp_mno ?: "❌ N/A" ?></td>
                            <td><?= $row->language ?: "❌ N/A" ?></td>
                            <td><?= $row->expected_installation_date ?></td>
                            <td><?= $row->project_tenure ?: "❌ N/A" ?></td>
                            <td><?= $row->project_type ?: "❌ N/A" ?></td>
                            <td><?= $row->comments ?: "❌ N/A" ?></td>
                            <td><?= $row->project_year ?></td>
                            <td>
                                <?php 
                                    if(!empty($row->bd_id) && $row->bd_id !='NA' && $row->bd_id != 0){ 
                                        $bdDetails = $this->Menu_model->get_userbyid($row->bd_id);
                                        if(sizeof($bdDetails) > 0){
                                            echo $bdDetails[0]->name;
                                        }else{
                                            echo "❌ N/A";
                                        }
                                    }else{
                                        echo "❌ N/A";
                                    }
                                ?>
                            </td>
                              <td>
                              <?php
                                if(!empty($row->sales_cid)){
                                ?>
                                <a target='_blank' href="https://stemapp.in/Menu/CompanyDetails/<?= $row->sales_cid; ?>"><?= $row->sales_cid; ?></a>
                                <?php
                                }else{
                                ?>
                                <a target='_blank' href="https://stemapp.in/Menu/CompanyDetails/<?= $row->client_id; ?>"><?= $row->client_id; ?></a>
                                <?php 
                                }
                                ?>
                              </td>
                            <td>
                               <?php if(!empty($row->backdrop) && $row->backdrop !='NA'){ ?>
                                    <a class="bg-success p-1 text-white" target="_BLANK" href="https://stemfactory.in/<?=$row->backdrop?>">view</a>
                               <?php }else{ ?>
                                    ❌ N/A 
                              <?php } ?>
                            </td>
                            <td>
                                <?php if(!empty($row->design) && $row->design !='NA'){ ?>
                                    <a class="bg-success p-1 text-white" target="_BLANK" href="https://stemfactory.in/<?=$row->design?>">view</a>
                               <?php }else{ ?>
                                    ❌ N/A 
                              <?php } ?>
                            </td>
                            <td>
                                <?php if(!empty($row->designed) && $row->designed !='NA'){ ?>
                                    <a href="https://stemfactory.in/<?=$row->designed?>">view</a>
                               <?php }else{ ?>
                                    ❌ N/A 
                              <?php } ?>
                          
                        
                        </td>
                            <td><?= $row->remark ?: "❌ N/A" ?></td>
                            
                            <td><?= $row->fttptype ?: "❌ N/A" ?></td>
   
                            <td><?= $row->insdate ?></td>
                            <td><?= $row->updatedt ?></td>
                            <td><?= $row->haprdt ?></td>

                           <?php if(in_array($user['type_id'],[1,2])){?> 
                              <td>
                                <?php if(!empty($pro_uid) && !is_null($pro_uid)){
                                  $operationUser =  $this->Menu_model->getOperationUserByUID($pro_uid);
                                  if(sizeof($operationUser) > 0){
                                    echo $operationUser[0]->fullname;
                                  }else{
                                    echo "<span class='bg-warning p-1'> NA </span>";
                                  }
                                  ?> 
                                 <?php }else{
                                  echo "<span class='bg-warning p-1'> NA </span>";
                                 } ?>
                            </td>
                              <td>
                                 <span class='custom-btn btn-11 partnearray p-1' onclick="UpdatePROMapping(<?=$row->id?>)">Change PRO</span>
                              </td>
                            <?php } ?>

                            <td><?= $row->created_at ?></td>
                            <td><?= $row->updated_at ?></td>
                            
                            <td><?= $row->status == 1 ? "<span class='bg-success p-1 text-white'>✅ Approved</span>" : "<span class='bg-warning p-1 text-white'>⏳ Pending</span>"; ?></td>
                            <td><?php 
                            if(!empty($row->be_request) && $row->be_request !=='no' && $row->be_request !=='N/A'){
                              echo "YES";
                            }
                            ?></td>

                            <?php 
                            /*
                            <td><?= $handoverAccountDone ?></td>
                            <td><?php 
                              if ($handoverAccountCnt > 0) {
                                  echo $payterm; 
                              }
                            ?></td>

                            */ ?>

                            <td>
                              <?php 
                              
                                if(!empty($row->handover_closed_by)){
                                 echo "<span class='bg-success p-1 text-white'>✅ Closed</span>"; 
                                 }else{ 
                                  if($row->bd_id == $uid){?>
                                        <button type="button"
                                                class="btn btn-primary"
                                                onclick="return confirmCloseHandover(<?= $row->id ?>)">
                                            Close Handover
                                        </button>
                                 <?php }else{
                                    echo "<span class='bg-warning p-1 text-white'>⏳ Pending</span>"; 
                                  }
                                  }
                                  
                                  ?>
                            </td>
                          <td>
                              <?= !empty($row->handover_closed_date) ? $row->handover_closed_date : "-" ?>
                          </td>

                          <td>
                              <?= !empty($row->handover_closed_remarks) ? $row->handover_closed_remarks : "-" ?>
                          </td>

                          <td>
                              <?php
                              if (!empty($row->handover_closed_by)) {

                                  $handoverClosedBYUserdetail = $this->Menu_model->get_userbyid($row->handover_closed_by);

                                  if (!empty($handoverClosedBYUserdetail) && count($handoverClosedBYUserdetail) > 0) {
                                      echo $handoverClosedBYUserdetail[0]->name ?? "-";
                                  } else {
                                      echo "-";
                                  }

                              } else {
                                  echo "-";
                              }
                              ?>
                          </td>

                            <td><a class="bg-primary p-1 text-white" target="_BLANK" href="https://stemoppapp.in/Menu/HandoverCloseProccessView/<?=$row->id?>">View Progress Timeline</a></td>
                            
                            <td><a class="bg-primary p-1 text-white" target="_BLANK" href="https://stemoppapp.in/Menu/ProjectProfileDetails/<?=$row->id?>/<?=$user['user_id']?>">View Project Profile</a></td>
                            
                        </tr>
                    <?php $j++; endforeach; ?>
                    </tbody>
                  </table>
                </div>

                   </div>
                </div>
              </div>
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
    </section>


       <?php if(in_array($user['type_id'],[1,2])){?> 
                  <div class="modal fade" id="AssignYourselfCenterModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                              <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                          <div class="modal-header" style="background-color: #d1e7ff;">
                            <h5 class="modal-title text-center" style="color: red;" id="exampleModalLongTitle"><b><u>Change PRO </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                          <div class="modal-body" style="background-color: #ffc4c45c;">
                         <form id="assignForm" action="<?=base_url();?>Menu/ChangePROinProjectCode" method="post">
                              <input type="hidden" id="request_id" name="request_id" required value="">

                              <?php 
                              $allPROLists =  $this->Menu_model->getOperationDepartmentUserByUID(20);
                              ?>
                              <hr>
                              <label>Select PRO</label>
                              <select id="select_pro" class="form-control" name="select_pro" required>
                                  <option value="" disabled selected>--Select--</option>
                                  <?php foreach($allPROLists as $allPROList): ?>
                                    <option value="<?= $allPROList->id; ?>"><?= $allPROList->fullname; ?></option>
                                  <?php endforeach; ?>
                              </select>

                              <br>
                              <div class="form-group text-center">
                                  <button type="submit" class="custom-btn btn-11 p-2">Update PRO</button>
                              </div>
                          </form>
                          </div>
                        </div>
                      </div>
                    </div>
          <?php } ?>




               <div class="modal fade" id="closedHandoverCenterModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                              <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                          <div class="modal-header" style="background-color: #d1e7ff;">
                            <h5 class="modal-title text-center" style="color: red;" id="exampleModalLongTitle"><b><u> Close Handover </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                          <div class="modal-body" style="background-color: #ffc4c45c;">
                         <form id="assignForm" action="<?=base_url();?>Menu/ClosedHandover" method="post">
                              <input type="hidden" id="closed_request_id" name="request_id" required value="">
                              <label> Select </label>
                              <select id="closed_handover" class="form-control" name="closed_handover" required>
                                  <option value="1" selected>Closed</option>
                              </select>

                              <br>
                              <label> Remarks </label>
                              <textarea id="closed_remarks" class="form-control" name="closed_remarks" required></textarea>
                              <br>
                              <div class="form-group text-center">
                                  <button type="submit" class="custom-btn btn-11 p-2">Closed Handover</button>
                              </div>
                          </form>
                          </div>
                        </div>
                      </div>
                    </div>




    </div>    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
     <script>
              function UpdatePROMapping(id){
                $('#AssignYourselfCenterModal').modal('show');
                $('#request_id').val(id);
            }

function confirmCloseHandover(id) {
    if (confirm('Are you sure you want to close this handover? This action cannot be undone.')) {
          $('#closedHandoverCenterModal').modal('show');
          $('#closed_request_id').val(id);
    }
    return false;
}
</script>

    <footer class="main-footer">
      <strong>Copyright &copy; 2021-<?=date("Y")?> <a href="<?=base_url();?>">Stemlearning</a>.</strong>
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
      $("#example1").DataTable({
        "responsive": false, "lengthChange": false, "autoWidth": false,
        "buttons": ["csv", "excel", "pdf"]
      }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    </script>
  </body>
</html>