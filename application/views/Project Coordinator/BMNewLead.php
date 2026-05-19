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
      .scrollme {
      overflow-x: auto;
      }
      .contact-group {
            border: 1px solid #ddd;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .removeContact {
            margin-top: 10px;
        }
    </style>
  </head>
  <body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
      <!-- Preloader -->
      <!-- Navbar -->
      <?php require('nav.php');?>
      <?php 
        // require('addlog.php');
        ?>
      <!-- /.navbar -->
      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
          <div class="container-fluid">
            <div class="row mb-2">
              <div class="col-sm-6">
                <h1 class="m-0"></h1>
              </div>
              <!-- /.col -->
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                  <h4><?php $uid=$user['user_id']?></h4>
                </ol>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
          </div>
          <!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->
        <!-- Main content -->
        <section class="content">
          <div class="container-fluid">
            <?php  
              $inid       = $bmdata[0]->inid;
              $cmpid_id       = $bmdata[0]->cid;
              $intData    = $this->Menu_model->get_initcallby_id($inid);
              
              $cluster_id = $intData[0]->cluster_id;
              $topspender   = $intData[0]->topspender;
              $upsell_client   = $intData[0]->upsell_client;
              $focus_funnel   = $intData[0]->focus_funnel;
              $pkclient   = $intData[0]->pkclient;
              $potential   = $intData[0]->potential;
              $interventions   = $intData[0]->interventions;
              $support   = $intData[0]->support;
              
              $cmpData    = $this->Menu_model->get_cdbyid($cmpid_id);
              
              $compname   = $cmpData[0]->compname;
              $address    = $cmpData[0]->address;
              $website    = $cmpData[0]->website;
              $country    = $cmpData[0]->country;
              $state      = $cmpData[0]->state;
              $city       = $cmpData[0]->city;
              $draft      = $cmpData[0]->draft;
              $cityname   = $cmpData[0]->cityname;
            
              if (is_numeric($city)) {
                $city = $this->Menu_model->get_citybyid($city);
              }elseif (ctype_alpha(str_replace(' ', '', $city))) {
                  $city = $city;
              }else {
                  $city =  $city;
              }

              $partnerType_id = $cmpData[0]->partnerType_id;
              $country        = $cmpData[0]->country;
              $budget        = $cmpData[0]->budget;
              
              ?>
            <div class="row">
              <div class="col-lg-12 col-md-12 col-12 m-auto">
                <!-- Default box -->
                <div class="card card-primary">
                  <div class="card-header">
                    <div class="text-center ">
                      <h3>Update Client Details (CID - 61584)</h3>
                    </div>
                  </div>
                  <!-- /.card-header -->
                  <div class="card-body box-profile p-5">
                    <div class="col-12 col-md-12 mb-12">
                      <label for="validationSample03">Select Special Person Of Company</label>
                      <select type="text" class="form-control" id="selectPerson" name="selectPerson">
                        <!-- <option value="Same Person" selected>Select Special Person Of Company</option> -->
                        <option value="Same Person">Same Person</option>
                        <option value="Other Person">Other Person</option>
                      </select>
                      <div class="invalid-feedback">Please provide a valid city.</div>
                      <div class="valid-feedback">Looks good!</div>
                    </div>
                    <hr>
                    <form method="post" id="myUpdateForm" action="<?=base_url();?>Menu/addbmcompany">
                      <input type="hidden" name="uid" value="<?=$uid?>">
                      <input type="hidden" name="bmid" value="<?=$bmid?>">
                      <input type="hidden" name="cid" value="<?=$bmdata[0]->cid?>">
                      <input type="hidden" name="ccid" value="<?=$bmdata[0]->ccid?>">
                      <input type="hidden" name="inid" value="<?=$bmdata[0]->inid?>">
                      <input type="hidden" name="tid" value="<?=$bmdata[0]->tid?>">
                      <div class="form-row">
                        <div class="col-sm-12 col-lg-6 p-3">
                          <div class="was-validated">
                            <div class="form-row">
                              <div class="col-12 col-md-12">
                                <label for="validationSample01">Company Name</label>
                                <input type="text" class="form-control"  placeholder="Company Name" value="<?=$compname?>" required=""  id="compname" name="compname" >
                                <div class="invalid-feedback">Please provide a Company name.</div>
                                <div class="valid-feedback">Looks good!</div>
                              </div>
                             
                              <div class="col-12 col-md-12 mb-12">
                              <hr>
                                <label for="validationSample02">Website Link</label>
                                <input type="text" class="form-control" id="website" value="<?=$website ?>" placeholder="Website Link" name="website"  required="">
                                <div class="invalid-feedback">Please provide a company's website.</div>
                                <div class="valid-feedback">Looks good!</div>
                              </div>
                            </div>
                            <hr>
                            <div class="form-row">
                              <div class="col-12 col-md-12 mb-12">
                                <label for="validationSample03">Country</label>
                                <select type="text" class="form-control" id="country" name="country" placeholder="City" required="">
                                  <option value="1" selected>India</option>
                                </select>
                                <div class="invalid-feedback">Please provide a valid city.</div>
                                <div class="valid-feedback">Looks good!</div>
                              </div>
                              <div class="col-12 col-md-12 mb-12">
                                <label for="validationSample04">State</label>
                                <?php 
                                  // dd($states);
                                  ?>
                                <select name="state" id="id_state" required class="form-control">
                                  <option value="">Select State</option>
                                  <?php
                                    if (is_numeric($state)) {
                                      foreach($states as $st){ 
                                        if ($st->id == $state) {
                                          ?>
                                  <option value="<?= $st->id ?>" selected><?= $st->state ?></option>
                                  <?php
                                    } else {
                                        ?>
                                  <option value="<?= $st->id ?>"><?= $st->state ?></option>
                                  <?php
                                    }
                                    }
                                    }elseif (ctype_alpha(str_replace([' ', '(', ')'], '', $state))) {
                                    
                                      foreach($states as $st){ 
                                        if ($st->state == $state) {
                                          ?>
                                  <option value="<?= $st->id ?>" selected><?= $st->state ?></option>
                                  <?php
                                    } else {
                                        ?>
                                  <option value="<?= $st->id ?>"><?= $st->state ?></option>
                                  <?php
                                    }
                                    }
                                    } else {
                                    foreach($states as $st){ 
                                    
                                      ?>
                                  <option value="<?= $st->id ?>"><?= $st->state ?></option>
                                  <?php
                                    }
                                    }
                                    ?>
                                </select>
                                <div class="invalid-feedback">Please provide a valid state.</div>
                                <div class="valid-feedback">Looks good!</div>
                              </div>
                              <div class="col-12 col-md-12 mb-12">
                             
                                <label for="validationSample04">District</label>
                                <?php if(isset($city) && !empty($city)) { ?>
                                <select id="city1" class="form-control" name="city1" required>
                                  <option value="<?= $city?>" selected><?=$city?></option>
                                </select>
                                <?php }
                                  else{ ?>
                                <select id="city1" class="form-control" name="city" required>
                                </select>
                                <?php  }  ?>
                                <div class="invalid-feedback">Please provide a valid city.</div>
                                <div class="valid-feedback">Looks good!</div>
                              </div>
                            </div>
                          </div>
                          <hr>
                          <div class="form-row">
                            <div class="col-12 col-md-12">
                              <label for="validationSample01">Your observation on Quick Comments about the company.</label>
                              <textarea type="text" name="draft" id="draft" class="form-control"  placeholder="write observation on Quick Comments about the company" ><?= $draft ?></textarea>
                            </div>
                            <div class="col-12 col-md-12 mb-12">
                            <hr>
                              <label for="validationSample02">Address</label>
                              <textarea class="form-control" name="address" id="address" placeholder="Full Address" required=""><?=$address?></textarea>
                              <div class="invalid-feedback">Please provide a company's address.</div>
                              <div class="valid-feedback">Looks good!</div>
                            </div>
                          </div>
                          <hr>
                          <div class="col-12 col-md-12 mb-12">
                            <label for="validationSample04">Add Travel Cluster</label>
                            <?php $clusterData = $this->Menu_model->getClusterByUserId($uid);  ?>
                            <select id="cluster" class="form-control" name="cluster" required>
                              <option selected disabled>Select Cluster</option>
                              <?php foreach($clusterData as $cdata):
                                if($cluster_id == $cdata->id){$selected = 'selected';}else{$selected = '';}
                                ?>
                              <option <?= $selected ?> value="<?=$cdata->id?>"><?=$cdata->clustername ?></option>
                              <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Please add a valid cluster.</div>
                            <div class="valid-feedback">Looks good!</div>
                          </div>
                        </div>
                        <div class="col-sm-12 col-lg-6 p-3">
                          <div class="was-validated">
                            <div class="form-row">
                              <div class="col-12 col-md-12 mb-12">
                                <label for="validationSample02">Company Type</label>
                                <select id="ctype" name="ctype" class="form-control" required>
                                  <option value="">Select Partner Type</option>
                                  <?php foreach($partner as $p){
                                    if($partnerType_id === $p->id){$selected = 'selected';}else{$selected = '';}
                                    ?>
                                  <option <?=$selected ?> value="<?=$p->id?>"><?=$p->name?></option>
                                  <?php }?>
                                </select>
                                <div class="invalid-feedback">Please provide a valid Company Type.</div>
                                <div class="valid-feedback">Looks good!</div>
                              </div>
                              
                              <div class="col-12 col-md-12" id="budgetdiv">
                              <hr>
                                <label for="validationSample01">Budget</label>
                                <input type="text" name="budget" class="form-control" id="budget" placeholder="Budget" value="<?=$budget ?>" required=""  >
                                <div class="invalid-feedback">Please provide a Company's budget.</div>
                                <div class="valid-feedback">Looks good!</div>
                              </div>
                            </div>
                            <hr>
                            <div class="form-row">
                              <div class="col-12 col-md-12">
                                <label for="validationSample01">Contact person name</label>
                                <input type="text" class="form-control"  placeholder="Contact person name" value="<?=$bmdata[0]->contactperson?>" required=""  id="contactperson" name="compconname" >
                                <div class="invalid-feedback">Please provide a contact person name.</div>
                                <div class="valid-feedback">Looks good!</div>
                              </div>
                              <div class="form-row">
                                <div class="col-12 col-md-12 mb-12">
                                  <label for="validationSample01">Designation</label>
                                  <input type="text" name="designation" class="form-control" id="designation" placeholder="Designation" value="<?=$bmdata[0]->designation?>" required=""  >
                                  <div class="invalid-feedback">Please provide a designation.</div>
                                  <div class="valid-feedback">Looks good!</div>
                                </div>
                              </div>
                              <div class="col-12 col-md-12 mb-12">
                                <label for="validationSample02">Email ID</label>
                                <input type="email" class="form-control" id="emailid" placeholder="Email Id" value="<?=$bmdata[0]->emailid?>" name="emailid"  required="">
                                <div class="invalid-feedback">Please provide a email id.</div>
                                <div class="valid-feedback">Looks good!</div>
                              </div>
                            </div>
                            <div class="form-row">
                              <div class="col-12 col-md-12 mb-12">
                                <label for="validationSample03">Mobile Number</label>
                                <input type="text" minlength="10" maxlength="10"  class="form-control" id="phoneno" value="<?= $bmdata[0]->phoneno ?>" name="phoneno" placeholder="Mobile Number" required="">
                                <div class="invalid-feedback">Please provide a valid number.</div>
                                <div class="valid-feedback">Looks good!</div>
                              </div>
                            </div>
                            <hr>
                            <div class="form-row">
                                  <div class="col-12 col-md-12 mb-12">
                                    <div class="form-check">
                                    <input class="form-check-input" type="radio" name="contact_type" id="exampleRadios1" value="primary" checked>
                                    <label class="form-check-label" for="exampleRadios1">
                                    Primary Contact
                                    </label>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <input class="form-check-input" type="radio" name="contact_type" id="exampleRadios2" value="alternate">
                                    <label class="form-check-label" for="exampleRadios2">
                                    Secondary Contact
                                    </label>
                                  </div>
                             
                                </div>
                            </div>
                          </div>
                          <hr>
                          <div class="form-row">
                            <div class="col-12 col-md-12">
                              <label for="validationSample01">Top Spender</label> &nbsp;
                              <?php 
                                $yesChecked = $topspender === "yes" ? "checked" : "";
                                $noChecked = $topspender === "no" ? "checked" : "";
                                ?>
                              <input
                                type="radio"
                                name="top_spender"
                                value="yes"
                                <?php echo $yesChecked; ?>
                                />
                              YES
                              <input
                                type="radio"
                                name="top_spender"
                                value="no"
                                <?php echo $noChecked; ?>
                                />
                              &nbsp;NO &nbsp;
                              <div class="invalid-feedback">
                                Please provide a Company's budget.
                              </div>
                              <div class="valid-feedback">Looks good!</div>
                            </div>
                          </div>
                          <div class="form-row">
                            <div class="col-12 col-md-12" style="margin-top: 10px">
                              <label for="validationSample01">Upsell Client</label> &nbsp;
                              <?php 
                                $yesChecked = $upsell_client === "yes" ? "checked" : "";
                                $noChecked = $upsell_client === "no" ? "checked" : "";
                                ?>
                              <input
                                type="radio"
                                name="upsell_client"
                                value="yes"
                                <?php echo $yesChecked; ?>
                                />
                              YES 
                              <input
                                type="radio"
                                name="upsell_client"
                                value="no"
                                <?php echo $noChecked; ?>
                                />
                              &nbsp;NO &nbsp;
                              <div class="invalid-feedback">
                                Please provide a Company's budget.
                              </div>
                              <div class="valid-feedback">Looks good!</div>
                            </div>
                          </div>
                          <div class="form-row">
                            <div class="col-12 col-md-12" style="margin-top: 10px">
                              <label for="validationSample01">Focus Funnel</label> &nbsp;
                              <?php 
                                $yesChecked = $focus_funnel === "yes" ? "checked" : "";
                                $noChecked = $focus_funnel === "no" ? "checked" : "";
                                ?>
                              <input
                                type="radio"
                                name="focus_funnel"
                                value="yes"
                                <?php echo $yesChecked; ?>
                                />
                              YES
                              <input
                                type="radio"
                                name="focus_funnel"
                                value="no"
                                <?php echo $noChecked; ?>
                                />
                              &nbsp;NO &nbsp;
                              <div class="invalid-feedback">
                                Please provide a Company's budget.
                              </div>
                              <div class="valid-feedback">Looks good!</div>
                            </div>
                          </div>
                          <div class="form-row">
                            <div class="col-12 col-md-12" style="margin-top: 10px">
                              <label for="validationSample01">Key Client</label> &nbsp;&nbsp;
                              &nbsp;&nbsp;
                              <?php 
                                $yesChecked = $pkclient === "yes" ? "checked" : "";
                                $noChecked = $pkclient === "no" ? "checked" : "";
                                ?>
                              <input
                                type="radio"
                                name="key_client"
                                value="yes"
                                <?php echo $yesChecked; ?>
                                />
                              &nbsp;YES &nbsp;
                              <input
                                type="radio"
                                name="key_client"
                                value="no"
                                <?php echo $noChecked; ?>
                                />
                              &nbsp;&nbsp;NO &nbsp;&nbsp;
                              <div class="invalid-feedback">
                                Select value for Key Company
                              </div>
                              <div class="valid-feedback">Looks good!</div>
                            </div>
                          </div>
                          <div class="form-row">
                            <div class="col-12 col-md-12" style="margin-top: 10px">
                              <label for="validationSample01">Potential Client</label> &nbsp;&nbsp;
                              &nbsp;&nbsp;
                              <?php 
                                $yesChecked = $comp_business_potential === "yes" ? "checked" : "";
                                $noChecked = ($comp_business_potential === "no"  ||  $comp_business_potential === NULL)? "checked" : "";
                                ?>
                              <input
                                type="radio"
                                name="potential_company"
                                value="yes"
                                <?php echo $yesChecked; ?>
                                />
                              &nbsp;YES &nbsp;
                              <input
                                type="radio"
                                name="potential_company"
                                value="no"
                                <?php echo $noChecked; ?>
                                />
                              &nbsp;&nbsp;NO &nbsp;&nbsp;
                              <div class="invalid-feedback">
                                Please provide a Company's budget.
                              </div>
                              <div class="valid-feedback">Looks good!</div>
                            </div>
                          </div>
                              
                          <hr>
                          <div class="form-row">
                            <div class="col-12 col-md-12">

                             <div class="maincontactcard">
                              
                             <div class="text-center">
                             <h5 class="text-center">Add More Contact Details</h5>
                              <button type="button" id="addContact" class="btn btn-primary mt-3">Add New Contact</button>
                            </div>
                             </div>
                              <hr>
                              <div id="contactContainer">
                              </div>
                            </div>
                            </div>


                        </div>
                       <center style="width: 100%;">
                        <hr>
                          <button class="btn btn-primary" style="width:150px;height: 60px;" type="submit" id="submitButton">Submit</button>
                        <hr>
                       </center>
                    </form>
                    </div>
                    <!-- /.card -->
                  </div>
                </div>
                <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
               <script>
                $(document).ready(function () {
                    var countdown = 60;
                    var countdownInterval;

                    $("#myUpdateForm").on("submit", function (e) {
                        var $button = $("#submitButton");

                        // Disable the button immediately on form submission
                        $button.prop("disabled", true);
                        $button.text(countdown + " seconds");

                        // Start countdown
                        countdownInterval = setInterval(function () {
                            countdown--;
                            $button.text(countdown + " seconds");

                            if (countdown <= 0) {
                                clearInterval(countdownInterval);
                                $button.prop("disabled", false);
                                $button.text("Submit");
                                countdown = 60; // Reset countdown
                            }
                        }, 1000);
                        // Allow form to submit normally
                    });
                });
                </script>
                <script>
        $(document).ready(function() {
            $('#addContact').click(function() {
                var contactGroup = `
                    <div class="contact-group">
                        <div class="form-row">
                            <div class="col-12 col-md-6">
                                <label for="contactperson">Contact Person Name</label>
                                <input type="text" class="form-control" placeholder="Contact person name" required name="new_compconname[]">
                                <div class="invalid-feedback">Please provide a contact person name.</div>
                                <div class="valid-feedback">Looks good!</div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="designation">Designation</label>
                                <input type="text" name="new_designation[]" class="form-control" placeholder="Designation" required>
                                <div class="invalid-feedback">Please provide a designation.</div>
                                <div class="valid-feedback">Looks good!</div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-12 col-md-6">
                                <label for="emailid">Email ID</label>
                                <input type="email" class="form-control" placeholder="Email Id" name="new_emailid[]" required>
                                <div class="invalid-feedback">Please provide an email id.</div>
                                <div class="valid-feedback">Looks good!</div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="phoneno">Mobile Number</label>
                                <input type="text" minlength="10" maxlength="10" class="form-control" placeholder="Mobile Number" name="new_phoneno[]" required>
                                <div class="invalid-feedback">Please provide a valid number.</div>
                                <div class="valid-feedback">Looks good!</div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-12">
                                <label for="selectPersonType">Contact Type</label>
                                <select type="text" class="form-control" id="selectPersonType" name="new_contact_type[]">
                                  <option value="" disabled selected>Select Contact Type </option>
                                  <option value="primary">Primary</option>
                                  <option value="alternate">Alternate</option>
                                </select>
                                <div class="invalid-feedback">Please provide an person type.</div>
                                <div class="valid-feedback">Looks good!</div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-danger mt-3 removeContact">Remove</button>
                    </div>
                `;
                $('#contactContainer').append(contactGroup);
            });

            $('#contactContainer').on('click', '.removeContact', function() {
                $(this).closest('.contact-group').remove();
            });
        });
    </script>
                <script type='text/javascript'>
                  $('#currentStatus').on('change', function b() {
                  var sid = document.getElementById("currentStatus").value;
                  $.ajax({
                  url:'<?=base_url();?>Menu/getremark',
                  type: "POST",
                  data: {
                  sid: sid
                  },
                  cache: false,
                  success: function a(result){
                  $("#remarks").html(result);
                  }
                  });
                  });
                  
                  $('#action_type').on('change', function b() {
                  var aid = document.getElementById("action_type").value;
                  var sid = document.getElementById("currentStatus").value;
                  $.ajax({
                  url:'<?=base_url();?>Menu/getpurpose',
                  type: "POST",
                  data: {
                  aid: aid,
                  sid: sid
                  },
                  cache: false,
                  success: function a(result){
                  $("#purpose").html(result);
                  }
                  });
                  });
                  
                  
                  $('#purpose').on('change', function b() {
                  var pid = document.getElementById("purpose").value;
                  $.ajax({
                  url:'<?=base_url();?>Menu/getnextaction',
                  type: "POST",
                  data: {
                  pid: pid
                  },
                  cache: false,
                  success: function a(result){
                  $("#next_action").html(result);
                  }
                  });
                  });
                  
                  
                  $('#id_state').on('change', function b() {
                  var stid = document.getElementById("id_state").value;
                  $.ajax({
                  url:'<?=base_url();?>Menu/getcitybystate',
                  type: "POST",
                  data: {
                  stid: stid
                  },
                  cache: false,
                  success: function a(result){
                  $("#city1").html(result);
                  }
                  });
                  });
                  function replaceBudget(){                    
                      var budgetdiv= document.getElementById('budgetdiv');
                      var id_partnerType= document.getElementById('id_partnerType').value;
                      if(id_partnerType=="4"){
                      budgetdiv.innerHTML='<label for="validationSample01">Category</label><select id="budget" class="form-control" name="budget" required><option>A</option><option>B</option><option>C</option></select>';    
                      }
                      alert('budget checking '+id_partnerType);
                  }
                  var id_partnerType=document.getElementById('id_partnerType');
                  id_partnerType.addEventListener("change", replaceBudget);
                </script>
                <script>
                  $(document).ready(function() {

                      // $('#myUpdateForm').find('textarea,input[type="email"]').prop('readonly', true);
                      // $('#myUpdateForm').find('input[type="radio"], select').prop('disabled', true);

                            var compname      = $("#compname").val();
                            var website       = $("#website").val();
                            var address       = $("#address").val();
                            var cluster       = $("#cluster").val();
                            var ctype         = $("#ctype").val();
                            var budget        = $("#budget").val();
                            var country       = $("#country").val();

                            var contactperson = $("#contactperson").val();
                            var designation   = $("#designation").val();
                            var emailid       = $("#emailid").val();
                            var phoneno       = $("#phoneno").val();
                            
                            function disableNonEmptyFields(fields) {
                                fields.forEach(field => {
                                    if (field.value !== '' && field.value !== null) {
                                        $(`#${field.id}`).prop('disabled', true);
                                    }
                                });
                            }

                            // Call the function with the list of field objects
                            disableNonEmptyFields([
                                { id: 'contactperson', value: contactperson },
                                { id: 'designation', value: designation },
                                { id: 'emailid', value: emailid },
                                { id: 'phoneno', value: phoneno },
                                { id: 'compname', value: compname },
                                { id: 'website', value: website },
                                { id: 'address', value: address },
                                { id: 'ctype', value: ctype },
                                { id: 'cluster', value: cluster },
                                { id: 'country', value: country }
                            ]);


                            function disableCheckedInputs(names) {
                                names.forEach(name => {
                                    if ($(`input[name="${name}"]:checked`).length > 0) {
                                        $(`input[name="${name}"]`).prop('disabled', true);
                                    }
                                });
                            }

                            // Call the function with the list of input names
                              disableCheckedInputs([
                                  "top_spender",
                                  "upsell_client",
                                  "focus_funnel",
                                  "key_client",
                                  "potential_company"
                              ]);



                      $('#selectPerson').change(function() {
                          var selectedValue = $(this).val();



                          if (selectedValue === 'Same Person') {
                            
                              // $('#myUpdateForm').find('input[type="text"], textarea,input[type="email"]').prop('readonly', true);
                              // $('#myUpdateForm').find('input[type="radio"], select').prop('disabled', true);

                            var compname      = $("#compname").val();
                            var website       = $("#website").val();
                            var address       = $("#address").val();
                            var cluster       = $("#cluster").val();
                            var ctype         = $("#ctype").val();
                            var budget        = $("#budget").val();
                            var country       = $("#country").val();

                            var contactperson = $("#contactperson").val();
                            var designation   = $("#designation").val();
                            var emailid       = $("#emailid").val();
                            var phoneno       = $("#phoneno").val();
                            
                            function disableNonEmptyFields(fields) {
                                fields.forEach(field => {
                                    if (field.value !== '' && field.value !== null) {
                                        $(`#${field.id}`).prop('disabled', true);
                                    }
                                });
                            }

                            // Call the function with the list of field objects
                            disableNonEmptyFields([
                                { id: 'contactperson', value: contactperson },
                                { id: 'designation', value: designation },
                                { id: 'emailid', value: emailid },
                                { id: 'phoneno', value: phoneno },
                                { id: 'compname', value: compname },
                                { id: 'website', value: website },
                                { id: 'address', value: address },
                                { id: 'ctype', value: ctype },
                                { id: 'cluster', value: cluster },
                                { id: 'country', value: country }
                            ]);


                            function disableCheckedInputs(names) {
                                names.forEach(name => {
                                    if ($(`input[name="${name}"]:checked`).length > 0) {
                                        $(`input[name="${name}"]`).prop('disabled', true);
                                    }
                                });
                            }

                            // Call the function with the list of input names
                              disableCheckedInputs([
                                  "top_spender",
                                  "upsell_client",
                                  "focus_funnel",
                                  "key_client",
                                  "potential_company"
                              ]);

                          } else if (selectedValue === 'Other Person') {
                            $('#myUpdateForm').find('input[type="text"], textarea,input[type="email"]').prop('readonly', false);
                            $('#myUpdateForm').find('input[type="radio"],select').prop('disabled', false);


                            var compname      = $("#compname").val();
                            var website       = $("#website").val();
                            var address       = $("#address").val();
                            var cluster       = $("#cluster").val();
                            var ctype         = $("#ctype").val();
                            var budget        = $("#budget").val();
                            var country       = $("#country").val();

                            var contactperson = $("#contactperson").val();
                            var designation   = $("#designation").val();
                            var emailid       = $("#emailid").val();
                            var phoneno       = $("#phoneno").val();
                            
                            function disableNonEmptyFields(fields) {
                                fields.forEach(field => {
                                    if (field.value !== '' && field.value !== null) {
                                        $(`#${field.id}`).prop('disabled', true);
                                    }
                                });
                            }

                            // Call the function with the list of field objects
                            disableNonEmptyFields([
                                { id: 'contactperson', value: contactperson },
                                { id: 'designation', value: designation },
                                { id: 'emailid', value: emailid },
                                { id: 'phoneno', value: phoneno },
                                { id: 'compname', value: compname },
                                { id: 'website', value: website },
                                { id: 'address', value: address },
                                { id: 'ctype', value: ctype },
                                { id: 'cluster', value: cluster },
                                { id: 'country', value: country }
                            ]);


                            function disableCheckedInputs(names) {
                                names.forEach(name => {
                                    if ($(`input[name="${name}"]:checked`).length > 0) {
                                        $(`input[name="${name}"]`).prop('disabled', true);
                                    }
                                });
                            }

                            // Call the function with the list of input names
                              disableCheckedInputs([
                                  "top_spender",
                                  "upsell_client",
                                  "focus_funnel",
                                  "key_client",
                                  "potential_company"
                              ]);

                          }




                      });
                      $('#myUpdateForm').submit(function(event) {
                          $('#myUpdateForm').find(':disabled').prop('disabled', false);
                      });
                  });
                </script>
                <!-- /.row (main row) -->
              </div>
              <!-- /.container-fluid -->
        </section>
        </div></div>
      </div>
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
      $("#example1").DataTable({
        "responsive": false, "lengthChange": false, "autoWi$dth": false,
        "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
      }).buttons().container().appen$dto('#example1_wrapper .col-md-6:eq(0)');
    </script>
  </body>
</html>