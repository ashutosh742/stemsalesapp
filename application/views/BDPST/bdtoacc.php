<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="wi$dth=device-wi$dth, initial-scale=1">
  <title>STEM APP | WebAPP</title>
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
    .scrollme {overflow-x: auto;}

  .preview-image {
    max-width: 100%;
    max-height: 200px;
    margin-bottom: 10px;
}
.preview-pdf {
    width: 100%;
    height: 400px; /* Adjust height as needed */
    border: 1px solid #ccc;
    margin-bottom: 10px;
}
div#previewContainer img {
  height: 100px;
    padding: 5px;
    margin: 4px;
    border: 1px solid black;
}

div#previewContainer embed{
  margin-top: 10px;
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
            <h1 class="m-0"></h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <h4>HI! <?php $uid=$user['user_id']; ?></h4>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->


    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
    <div class="card">
              <div class="card-header">
                <h3 class="card-title">Handover to Accounts</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form action="<?=base_url();?>Menu/bdaccount" method="post" enctype="multipart/form-data">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6 p-3">
                            <?php foreach($data as $d){ $prot = $d->project_tenure;?>
                              <div class="form-group">
                                <label for="client_name">Client Name</label>
                                <input type="hidden" class="form-control" name="id" id="id" value="<?=$d->id?>" readonly>
                                <input type="text" class="form-control" name="client_name" id="client_name" value="<?=$d->client_name?>" readonly>
                              </div>
                              <div class="form-group">
                                <label for="mediator">NGO / Mediator if any</label>
                                <input type="text" class="form-control" name="mediator" id="mediator" value="<?=$d->mediator?>" readonly>
                              </div>
                              <div class="form-group">
                                <label for="noofschool">Number of Schools</label>
                                <input type="text" class="form-control" name="noofschool" id="noofschool" value="<?=$d->noofschool?>" readonly>
                              </div>
                              <div class="form-group">
                                <label for="location">Location / Village</label>
                                <textarea class="form-control" name="location" id="location"readonly><?=$d->location?></textarea>
                              </div>
                              <div class="form-group">
                                <label for="city">City</label>
                                <input type="text" class="form-control" name="city" id="city" value="<?=$d->city?>" readonly>
                              </div>
                              <div class="form-group">
                                <label for="state">State</label>
                                <input type="text" class="form-control" name="state" id="state" value="<?=$d->state?>" readonly>
                              </div>
                              <div class="form-group">
                                <label for="spd_identify_by">School Identification by</label>
                                <input type="text" class="form-control" name="spd_identify_by" id="spd_identify_by" value="<?=$d->spd_identify_by?>" readonly>
                              </div>
                              <div class="form-group">
                                <label for="infrastructure">School Infrastructure for MSC (platform)</label>
                                <input type="text" class="form-control" name="infrastructure" id="infrastructure" value="<?=$d->infrastructure?>" readonly>
                              </div>
                              <div class="form-group">
                                <label for="contact_person">Contact Person</label>
                                <input type="text" class="form-control" name="contact_person" id="contact_person" value="<?=$d->contact_person?>" readonly>
                              </div>
                              <div class="form-group">
                                <label for="cp_mno">Contact Person Mobile No</label>
                                <input type="text" class="form-control" name="cp_mno" id="cp_mno" value="<?=$d->cp_mno?>" readonly>
                              </div>
                              <div class="form-group">
                                <label for="acontact_person">Alternate Contact Person</label>
                                <input type="text" class="form-control" name="acontact_person" id="acontact_person" value="<?=$d->acontact_person?>" readonly>
                              </div>
                              <div class="form-group">
                                <label for="acp_mno">Alternate Contact Person Mobile No</label>
                                <input type="text" class="form-control" name="acp_mno" id="acp_mno" value="<?=$d->acp_mno?>" readonly>
                              </div>
                              <div class="form-group">
                                <label for="project_tenure">Project Tenure</label>
                                <input type="text" class="form-control" name="project_tenure" id="project_tenure" value="<?=$d->project_tenure?>" readonly>
                              </div>
                              <div class="form-group">
                                <label for="comments">Special Requirements / Comments :</label>
                                <textarea class="form-control" name="comments" id="comments" readonly><?=$d->comments?></textarea>
                              </div>
                              <?php } ?>
                        </div>
                        <div class="col-lg-6 p-3">
                              <div class="form-group">
                                <label for="wono">Work Order No</label>
                                <input type="text" required class="form-control" name="wono" id="wono" placeholder="Work Order No" required>
                              </div>
                              <div class="form-group">
                                <label for="porno">Purchase Order No</label>
                                <input type="text" class="form-control" name="porno" id="porno" placeholder="Purchase Order No" required>
                              </div>
                              <div class="form-group">
                                <label for="gstno">GST Number</label>
                                <input type="text" class="form-control" name="gstno" id="gstno" placeholder="GST Number" required>
                              </div>
                              <div class="form-group">
                                <label for="panno">Pan Number</label>
                                <input type="text" class="form-control" name="panno" id="panno" placeholder="Pan Number" required>
                              </div>
                              <div class="form-group">
                                <label for="panno">Total Budget</label>
                                <input type="number" class="form-control" name="tbudget" id="tbudget" placeholder="Total Budget" required onchange="Budgetper()">
                              </div>
                              <div class="form-group">
                                <label for="payterm">PAYMENT TERMS</label>
                                <textarea type="text" class="form-control" name="payterm" id="payterm" placeholder="PAYMENT TERMS"></textarea>
                              </div>
                              <div class="form-group">
                                <label for="pwosdate">Purchase Order & Work Order Signed Date</label>
                                <input type="date" class="form-control" name="pwosdate" id="pwosdate">
                              </div>
                              <div class="form-group">
                                <label for="moudate">MoU Signed Date</label>
                                <input type="date" class="form-control" name="moudate" id="moudate" >
                              </div>
                              <div class="form-group">
                                <label for="srfinovice">Special Requirement for Invoice</label>
                                <textarea class="form-control" name="srfinovice" id="srfinovice" placeholder="Special Requirement for Invoice"></textarea>
                              </div>
                              <div class="form-group">
                           
                                <label for="mou">Attach MoU/Budget (multiple file)</label>
                                <input type="file" class="form-control-file" id="fileInput" data-max_length="20" multiple accept=".jpg, .jpeg, .png, .pdf" name="filname[]" required multiple>                            
                              
                                <div id="previewContainer"></div>        
                              </div>
                        </div>
                    </div>

                    <h5>MSC COSTINING AS PER PROPOSAL</h5>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label"><b>BUDGET</b></label>
                        <label class="col-sm-2 col-form-label"><b>BASIC</b></label>
                        <label class="col-sm-2 col-form-label"><b>GST</b></label>
                        <label class="col-sm-2 col-form-label"><b>TOTAL</b></label>
                        <?php if($prot>=1){?>
                        <label class="col-sm-1 col-form-label"><b>1 YEAR</b></label>
                        <?php } if($prot>=2){?>
                        <label class="col-sm-1 col-form-label"><b>2 YEAR </b></label>
                        <?php } if($prot>=3){?>
                        <label class="col-sm-1 col-form-label"><b>3 YEAR </b></label>
                        <?php }?>
                    </div>    
                    
                    <div class="form-group row">
                        <input type="hidden" class="form-control" name="bname[]" value="1 SET OFF MODEL">
                        <label class="col-sm-3 col-form-label">1 SET OFF MODEL</label>
                        <div class="col-sm-2">
                        <input type="text" class="form-control" name="basic[]">
                        </div>
                        <div class="col-sm-2">
                        <input type="text" class="form-control" name="gst[]">
                        </div>
                        <div class="col-sm-2">
                        <input type="text" class="form-control" name="total[]">
                        </div>
                        <?php if($prot>=1){?>
                        <div class="col-sm-1">
                        <input type="text" class="form-control" name="oney[]">
                        </div>
                        <?php } if($prot>=2){?>
                        <div class="col-sm-1">
                        <input type="text" class="form-control" name="twoy[]">
                        </div>
                        <?php } if($prot>=3){?>
                        <div class="col-sm-1">
                        <input type="text" class="form-control" name="threey[]">
                        </div>
                        <?php }?>
                        
                    </div>
                    <div class="form-group row">
                        <input type="hidden" class="form-control" name="bname[]" value="TTP">
                        <label class="col-sm-3 col-form-label">TTP</label>
                        <div class="col-sm-2">
                        <input type="text" class="form-control" name="basic[]">
                        </div>
                        <div class="col-sm-2">
                        <input type="text" class="form-control" name="gst[]">
                        </div>
                        <div class="col-sm-2">
                        <input type="text" class="form-control" name="total[]">
                        </div>
                        <?php if($prot>=1){?>
                        <div class="col-sm-1">
                        <input type="text" class="form-control" name="oney[]">
                        </div>
                        <?php } if($prot>=2){?>
                        <div class="col-sm-1">
                        <input type="text" class="form-control" name="twoy[]">
                        </div>
                        <?php } if($prot>=3){?>
                        <div class="col-sm-1">
                        <input type="text" class="form-control" name="threey[]">
                        </div>
                        <?php }?>
                        
                    </div>
                    <div class="form-group row">
                        <input type="hidden" class="form-control" name="bname[]" value="M&E">
                        <label class="col-sm-3 col-form-label">M&E</label>
                        <div class="col-sm-2">
                        <input type="text" class="form-control" name="basic[]">
                        </div>
                        <div class="col-sm-2">
                        <input type="text" class="form-control" name="gst[]">
                        </div>
                        <div class="col-sm-2">
                        <input type="text" class="form-control" name="total[]">
                        </div>
                        <?php if($prot>=1){?>
                        <div class="col-sm-1">
                        <input type="text" class="form-control" name="oney[]">
                        </div>
                        <?php } if($prot>=2){?>
                        <div class="col-sm-1">
                        <input type="text" class="form-control" name="twoy[]">
                        </div>
                        <?php } if($prot>=3){?>
                        <div class="col-sm-1">
                        <input type="text" class="form-control" name="threey[]">
                        </div>
                        <?php }?>
                        
                    </div>
                    <div class="form-group row">
                        <input type="hidden" class="form-control" name="bname[]" value="ANNUAL">
                        <label class="col-sm-3 col-form-label">ANNUAL</label>
                        <div class="col-sm-2">
                        <input type="text" class="form-control" name="basic[]">
                        </div>
                        <div class="col-sm-2">
                        <input type="text" class="form-control" name="gst[]">
                        </div>
                        <div class="col-sm-2">
                        <input type="text" class="form-control" name="total[]">
                        </div>
                        <?php if($prot>=1){?>
                        <div class="col-sm-1">
                        <input type="text" class="form-control" name="oney[]">
                        </div>
                        <?php } if($prot>=2){?>
                        <div class="col-sm-1">
                        <input type="text" class="form-control" name="twoy[]">
                        </div>
                        <?php } if($prot>=3){?>
                        <div class="col-sm-1">
                        <input type="text" class="form-control" name="threey[]">
                        </div>
                        <?php }?>
                        
                    </div>
                    <div class="form-group row">
                        <input type="hidden" class="form-control" name="bname[]" value="MAINTENANCE">
                        <label class="col-sm-3 col-form-label">MAINTENANCE</label>
                        <div class="col-sm-2">
                        <input type="text" class="form-control" name="basic[]">
                        </div>
                        <div class="col-sm-2">
                        <input type="text" class="form-control" name="gst[]">
                        </div>
                        <div class="col-sm-2">
                        <input type="text" class="form-control" name="total[]">
                        </div>
                        <?php if($prot>=1){?>
                        <div class="col-sm-1">
                        <input type="text" class="form-control" name="oney[]">
                        </div>
                        <?php } if($prot>=2){?>
                        <div class="col-sm-1">
                        <input type="text" class="form-control" name="twoy[]">
                        </div>
                        <?php } if($prot>=3){?>
                        <div class="col-sm-1">
                        <input type="text" class="form-control" name="threey[]">
                        </div>
                        <?php }?>
                        
                    </div>
                    <div class="form-group row">
                        <input type="hidden" class="form-control" name="bname[]" value="FURNITURE">
                        <label class="col-sm-3 col-form-label">FURNITURE</label>
                        <div class="col-sm-2">
                        <input type="text" class="form-control" name="basic[]">
                        </div>
                        <div class="col-sm-2">
                        <input type="text" class="form-control" name="gst[]">
                        </div>
                        <div class="col-sm-2">
                        <input type="text" class="form-control" name="total[]">
                        </div>
                        <?php if($prot>=1){?>
                        <div class="col-sm-1">
                        <input type="text" class="form-control" name="oney[]">
                        </div>
                        <?php } if($prot>=2){?>
                        <div class="col-sm-1">
                        <input type="text" class="form-control" name="twoy[]">
                        </div>
                        <?php } if($prot>=3){?>
                        <div class="col-sm-1">
                        <input type="text" class="form-control" name="threey[]">
                        </div>
                        <?php }?>
                        
                    </div>
                    <div class="form-group row">
                        <input type="hidden" class="form-control" name="bname[]" value="TOTAL">
                        <label class="col-sm-3 col-form-label">TOTAL</label>
                        <div class="col-sm-2">
                        <input type="text" class="form-control" name="basic[]">
                        </div>
                        <div class="col-sm-2">
                        <input type="text" class="form-control" name="gst[]">
                        </div>
                        <div class="col-sm-2">
                        <input type="text" class="form-control" name="total[]">
                        </div>
                        <?php if($prot>=1){?>
                        <div class="col-sm-1">
                        <input type="text" class="form-control" name="oney[]">
                        </div>
                        <?php } if($prot>=2){?>
                        <div class="col-sm-1">
                        <input type="text" class="form-control" name="twoy[]">
                        </div>
                        <?php } if($prot>=3){?>
                        <div class="col-sm-1">
                        <input type="text" class="form-control" name="threey[]">
                        </div>
                        <?php }?>
                        
                    </div>
                  
                  <h5>TOTAL PROJECT AMT</h5>
                    
                    <div class="form-group row">
                        <div class="col-sm-3"><label class="col-form-label">Payment Part</label></div>
                        <div class="col-sm-3"><label class="col-form-label">Milestone</label></div>
                        <div class="col-sm-3"><label class="col-form-label">Percentage</label></div>
                        <div class="col-sm-3"><label class="col-form-label">Amount</label></div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-3"><label class="col-form-label">Total Project Cost</label></div>
                        <div class="col-sm-3"><label class="col-form-label">ALL</label></div>
                        <div class="col-sm-3"><input type="number" class="form-control" readonly value="100"></div>
                        <div class="col-sm-3"><input type="text" class="form-control" readonly id="tprojectcost"></div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-3"><label class="col-form-label">1st Payment</label></div>
                        <div class="col-sm-3">
                            <select class="form-control">
                                <option>Time of Work Order</option>
                                <option>After Submission of Installation Report</option>
                                <option>After Submission of TTP Report</option>
                                <option>After Submission of M&E Report</option>
                                <option>After Submission of Annual Report</option>
                            </select>
                        </div>
                        <div class="col-sm-3"><input type="number" class="form-control" id="fstper" onchange="fstpert()"></div>
                        <div class="col-sm-3"><input type="text" class="form-control" readonly id="fstpay"></div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-3"><label class="col-form-label">2nd Payment</label></div>
                        <div class="col-sm-3">
                            <select class="form-control">
                                <option>Time of Work Order</option>
                                <option>After Submission of Installation Report</option>
                                <option>After Submission of TTP Report</option>
                                <option>After Submission of M&E Report</option>
                                <option>After Submission of Annual Report</option>
                            </select>
                        </div>
                        <div class="col-sm-3"><input type="number" class="form-control" id="sndper" onchange="sndpert()"></div>
                        <div class="col-sm-3"><input type="text" class="form-control" readonly id="sndpay"></div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-3"><label class="col-form-label">3rd Payment</label></div>
                        <div class="col-sm-3">
                            <select class="form-control">
                                <option>Time of Work Order</option>
                                <option>After Submission of Installation Report</option>
                                <option>After Submission of TTP Report</option>
                                <option>After Submission of M&E Report</option>
                                <option>After Submission of Annual Report</option>
                            </select>
                        </div>
                        <div class="col-sm-3"><input type="number" class="form-control" id="trdper" onchange="trdpert()"></div>
                        <div class="col-sm-3"><input type="text" class="form-control" readonly id="trdpay"></div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-3"><label class="col-form-label">4th Payment</label></div>
                        <div class="col-sm-3">
                            <select class="form-control">
                                <option>Time of Work Order</option>
                                <option>After Submission of Installation Report</option>
                                <option>After Submission of TTP Report</option>
                                <option>After Submission of M&E Report</option>
                                <option>After Submission of Annual Report</option>
                            </select>
                        </div>
                        <div class="col-sm-3"><input type="number" class="form-control" id="fthper" onchange="fthpert()"></div>
                        <div class="col-sm-3"><input type="text" class="form-control" readonly id="fthpay"></div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-3"><label class="col-form-label">5th Payment</label></div>
                        <div class="col-sm-3">
                            <select class="form-control">
                                <option>Time of Work Order</option>
                                <option>After Submission of Installation Report</option>
                                <option>After Submission of TTP Report</option>
                                <option>After Submission of M&E Report</option>
                                <option>After Submission of Annual Report</option>
                            </select>
                        </div>
                        <div class="col-sm-3"><input type="number" class="form-control" id="fithper" onchange="fithpert()"></div>
                        <div class="col-sm-3"><input type="text" class="form-control" readonly id="fithpay"></div>
                    </div>
                    
                    
                    <div class="form-group row">
                        <label class="col-sm-6 col-form-label">Proforma Invoice Depends on Payment terms (soft copy + hardcopy mention it if hard copy required) if yes mention with Expected date</label>
                        <div class="col-sm-3">
                         <Select class="form-control">
                             <option>No</option>
                             <option>Yes</option>
                         </Select>
                        </div>
                        <div class="col-sm-3">
                        <input type="text" class="form-control" name="proformadate" value=" ">
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label class="col-sm-6 col-form-label">Tax Invoice (soft copy + hardcopy mention it if hard copy required) if yes mention with Expected date</label>
                        <div class="col-sm-3">
                            <Select class="form-control">
                             <option>No</option>
                             <option>Yes</option>
                         </Select>
                        </div>
                        <div class="col-sm-3">
                        <input type="text" class="form-control" name="taxinvoicedate" value=" ">
                        </div>
                    </div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type='text/javascript'>
function Budgetper() {
  var tbudget = document.getElementById("tbudget").value;
  document.getElementById("tprojectcost").value=tbudget;
}
function fstpert() {
var tbudget = document.getElementById("tbudget").value;
var fstper = document.getElementById("fstper").value;
document.getElementById("fstpay").value=tbudget*(fstper/100);
}

function sndpert() {
var tbudget = document.getElementById("tbudget").value;
var sndper = document.getElementById("sndper").value;
document.getElementById("sndpay").value=tbudget*(sndper/100);
}

function trdpert() {
var tbudget = document.getElementById("tbudget").value;
var trdper = document.getElementById("trdper").value;
document.getElementById("trdpay").value=tbudget*(trdper/100);
}

function fthpert() {
var tbudget = document.getElementById("tbudget").value;
var fthper = document.getElementById("fthper").value;
document.getElementById("fthpay").value=tbudget*(fthper/100);
}

function fithpert() {
var tbudget = document.getElementById("tbudget").value;
var fithper = document.getElementById("fithper").value;
document.getElementById("fithpay").value=tbudget*(fithper/100);
}
          
</script>                 
                  
                  
                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                  <button type="submit" class="btn btn-primary" value="submit" onclick="this.form.submit(); this.disabled = true;">Submit</button>
                </div>
              </form>
            </div>
            <!-- /.card -->
  </div>
  </div>


          
        <!-- /.row (main row) -->
      </div><!-- /.container-fluid -->
    </section>
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


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type='text/javascript'>
function schoolFun() {
  var x = document.getElementById("noofschool").value;
  var ideby = document.getElementById("spd_identify_by").value;
  if(ideby=='Client'){
  for(var i=1;i<x;i++){
      var container = document.getElementById("container");
      var section = document.getElementById("mainsection");
      container.appendChild(section.cloneNode(true));
  }
  }
}

function prot(){
    var x = document.getElementById("projectt").value;
    if(x!='Other'){
        document.getElementById("project_type").value = x;
        document.getElementById("project_type").readOnly = true;
    }else{
        document.getElementById("project_type").value = '';
        document.getElementById("project_type").readOnly = false;
    }
}
      

$('#fileInput').on('change', function(e) {
        var files = e.target.files; // Get the selected files
        
        $('#previewContainer').empty(); // Clear the preview container
        
        // Loop through each selected file
        for (var i = 0; i < files.length; i++) {
            var file = files[i];
            var reader = new FileReader();
            
            // Closure to capture the file information
            reader.onload = (function(file) {
                return function(e) {
                    // Determine the file type
                    var fileType = file.type;
                    
                    // Check if the file is an image
                    if (fileType.startsWith('image/')) {
                        $('#previewContainer').append('<img src="' + e.target.result + '" alt="' + file.name + '">');
                    }
                    // Check if the file is a PDF
                    else if (fileType == 'application/pdf') {
                        $('#previewContainer').append('<embed src="' + e.target.result + '" type="application/pdf" width="200" height="200" />');
                    }
                };
            })(file);
            
            // Read the file as a data URL
            reader.readAsDataURL(file);
        }
    });
    

    function removeImage(name) {
        selectedfiles = document.getElementById("files").files;
        var final = [];
        $.each(selectedfiles, function (index, value) {
            if (value.name !== name) {
                console.log(value);
                final.push(value);
            }
        });
        console.log('List', final);
        // document.getElementById("files").files = new FileListItem(final);
    }

    function FileListItem(a) {
        a = [].slice.call(Array.isArray(a) ? a : arguments)
        for (var c, b = c = a.length, d = !0; b-- && d;) d = a[b] instanceof File
        if (!d) throw new TypeError("expected argument to FileList is File or array of File objects")
        for (b = (new ClipboardEvent("")).clipboardData || new DataTransfer; c--;) b.items.add(a[c])
        return b.files
    }










</script>

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
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
</script>
</body>
</html>
