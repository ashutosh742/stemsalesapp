<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>STEM Operation | WebAPP</title>
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
  
   <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

  <style>
      body {
          padding: 1rem;
        }
        #contentcard {position:fixed;
        margin-right:1rem;}
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
            <div class="col-md-12">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                  <li class="nav-item">
                    <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">FAQ</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Corporate PPT</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="salespitch-tab" data-toggle="tab" href="#salespitch" role="tab" aria-controls="salespitch" aria-selected="false">Sales Pitch</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="presentation-tab" data-toggle="tab" href="#presentation" role="tab" aria-controls="presentation" aria-selected="false">Presentation / Meeting Conduct</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="proposal-tab" data-toggle="tab" href="#proposal" role="tab" aria-controls="proposal" aria-selected="false">Proposal Of Unique</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="email-tab" data-toggle="tab" href="#email" role="tab" aria-controls="email" aria-selected="false">Email Draft</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="whatsapp-tab" data-toggle="tab" href="#whatsapp" role="tab" aria-controls="whatsapp" aria-selected="false">9 Whats App</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="regional-tab" data-toggle="tab" href="#regional" role="tab" aria-controls="regional" aria-selected="false">Regional Case Study</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="quarter-tab" data-toggle="tab" href="#quarter" role="tab" aria-controls="quarter" aria-selected="false">Quarter 1 PPT</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="annual-tab" data-toggle="tab" href="#annual" role="tab" aria-controls="annual" aria-selected="false">Annual Review</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="district-tab" data-toggle="tab" href="#district" role="tab" aria-controls="district" aria-selected="false">District Permission Letter</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="school-tab" data-toggle="tab" href="#school" role="tab" aria-controls="school" aria-selected="false">Add School Details</a>
                  </li>
                </ul>
            </div>
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->




<!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            
            
              <?php if ($this->session->flashdata('success_message')): ?>
          <div class="container-fluid">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              <strong> <?php echo $this->session->flashdata('success_message'); ?></strong> 
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
          </div>
        <?php endif; ?>
            
            <div class="row p-3">
                <div class="col-sm col-md-12 col-lg-12">
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                            <div class="card card-primary card-outline">
                                <div class="card-header">
                                    <h3>Add FAQ</h3>
                                </div>
                                <div class="card-body box-profile">
                                    <form action="<?=base_url();?>Menu/addAllResource" method="post" enctype="multipart/form-data">
                                        <input type="hidden" name="type" value="faq">
                                            <input type="hidden" name="uid" value="<?=$uid?>">
                                            <div class="form-group">
                                                <label for="task_type">Please Type Your Question</label><br>
                                                <input type="text" placeholder='Please Type Your Question' class="form-control" name="faq" id="faq" required>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                    </form>
                                </div>
                            </div>
                            
                             
                        </div>
                        <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                            <div class="card card-primary card-outline">
                                <div class="card-header">
                                    <h3>Corporate PPT</h3>
                                </div>
                                <div class="card-body box-profile">
                                    <form action="<?=base_url();?>Menu/addAllResource" method="post" enctype="multipart/form-data">
                                        <div class="was-validated">
                                            <input type="hidden" name="uid" value="<?=$uid?>">
                                            <input type="hidden" name="type" value="salesppt">
                                            <div class="form-group">
                                                <label>Partner Type</label><br>
                                                <select name="partnership" id="partnership" class="form-control">
                                                <?php $ptype = $this->Menu_model->get_partner();
                                                foreach($ptype as $p){?>
                                                <option value="<?=$p->id?>"><?=$p->name?></option>
                                                <?php }?>
                                            </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="task_type">What changes do you suggest in the PPT?</label><br>
                                                <!--<input type="text" placeholder='What All Unique Points You will Cover in Your PPT' class="form-control" name="pointscover" id="ppt" required>-->
                                                <textarea  placeholder='What changes do you suggest in the PPT' class="form-control" name="pointscover" id="ppt" rows="10" required></textarea>
                                            </div>
                                            <div class="form-group">
                                                <label>Please Mention The Slide Number</label><br>
                                                <input type="text" class="form-control" name="slideno" id="slideno" placeholder="Please Mention The Slide Number" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Attach PPT</label><br>
                                                <input type="file" class="form-control" name="filname[]" id="fileInput" required multiple>
                                            </div>
                                            <div class="form-group">
                                                <label>Reference You Tube Or Video Link Which you reffered</label><br>
                                                <div class="input-group">
                                                <input type="text" placeholder='Reference You Tube Or Video Link Which you reffered' class="form-control" name="reference[]" id="linkInput" required>
                                                <span class="input-group-text plus-icon"><i class="fas fa-plus"></i></span>
                                            </div>
                                            <div class="more"></div>
                                        </div>
                                            <div id="validationMessage"></div>
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                            <div id="preview"></div>
                                    </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="salespitch" role="tabpanel" aria-labelledby="salespitch-tab">
                            <div class="card card-primary card-outline">
                                <div class="card-header">
                                    <h3>sales Pitch</h3>
                                </div>
                                <div class="card-body box-profile">
                                    <form action="<?=base_url();?>Menu/addAllResource" method="post" enctype="multipart/form-data">
                                        <div class="was-validated">
                                            <input type="hidden" name="uid" value="<?=$uid?>">
                                            <input type="hidden" name="type" value="salespitch">
                                            <div class="form-group">
                                                <label for="task_type">Write Your Pitch</label><br>
                                                <!--<input type="text" placeholder='Write Your Pitch' class="form-control" name="message" id="pitch" required>-->
                                                
                                                <textarea  placeholder='Write Your Pitch' class="form-control" name="message" id="pitch" rows="10" required></textarea>
                                            </div>
                                            <div class="form-group">
                                                <label>Add Your Voice Note</label><br>
                                                <input type="file" class="form-control" name="filname[]" id="voicenote" required multiple>
                                            </div>
                                            <div class="form-group">
                                                <label>Partner Type</label><br>
                                                <select name="partnership" id="partnership" class="form-control">
                                                <?php $ptype = $this->Menu_model->get_partner();
                                                foreach($ptype as $p){?>
                                                <option value="<?=$p->id?>"><?=$p->name?></option>
                                                <?php }?>
                                            </select>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="presentation" role="tabpanel" aria-labelledby="presentation-tab">
                            <div class="card card-primary card-outline">
                                <div class="card-header">
                                    <h3>Presentation / Meeting Conduct</h3>
                                </div>
                                <div class="card-body box-profile">
                                    <form action="<?=base_url();?>Menu/addAllResource" method="post" enctype="multipart/form-data">
                                        <div class="was-validated">
                                            <input type="hidden" name="uid" value="<?=$uid?>">
                                            <input type="hidden" name="type" value="presentation">
                                            <div class="form-group">
                                                <label for="task_type">What changes do you suggest in the Presentation?</label><br>
                                                <!--<input type="text" placeholder='What All Points You will Cover in Your Presentation?' class="form-control" name="pointscover" id="presentation" required>-->
                                                <textarea  placeholder='What changes do you suggest in the Presentation' class="form-control" name="pointscover" id="presentation" rows="10" required></textarea>
                                            </div>
                                            <div class="form-group">
                                                <label for="task_type">Presentation Product Type</label><br>
                                                <input type="text" placeholder='Presentation Product Type' class="form-control" name="presentationtype" id="presentationtype" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Upload Role Play Video</label><br>
                                                <input type="file" class="form-control" name="filname[]" id="voicenote" required multiple>
                                            </div>
                                            <div class="form-group">
                                                <label>Add Learning Reference Link</label><br>
                                                <!--<input type="text" placeholder='Add Learning Reference Link' class="form-control" name="reference" id="linkInput" required>-->
                                                
                                                <div class="input-group">
                                                <input type="text" placeholder='Add Learning Reference Link' class="form-control" name="reference[]" id="linkInput1" required>
                                                <span class="input-group-text plus-icon1"><i class="fas fa-plus"></i></span>
                                            </div>
                                            <div class="more1"></div>
                                                <div id="validationMessage"></div>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        
                        <div class="tab-pane fade" id="proposal" role="tabpanel" aria-labelledby="proposal-tab">
                            <div class="card card-primary card-outline">
                                <div class="card-header">
                                    <h3>Proposal Of Unique</h3>
                                </div>
                                <div class="card-body box-profile">
                                    <form action="<?=base_url();?>Menu/addAllResource" method="post" enctype="multipart/form-data">
                                        <div class="was-validated">
                                            <input type="hidden" name="uid" value="<?=$uid?>">
                                            <input type="hidden" name="type" value="proposal">
                                            <div class="form-group">
                                                <label for="task_type">Proposal Name</label><br>
                                                <input type="text" placeholder='Proposal Name' class="form-control" name="proposal" id="proposal" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Upload Proposal</label><br>
                                                <input type="file" class="form-control" name="filname[]" id="uploadproposal" required multiple>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    
                        
                        <div class="tab-pane fade" id="email" role="tabpanel" aria-labelledby="email-tab">
                            <div class="card card-primary card-outline">
                                <div class="card-header">
                                    <!--<h3>9 Email Draft</h3>-->
                                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                                      <li class="nav-item">
                                        <a class="nav-link active nav-reset" id="email-tab" data-toggle="tab" href="#email" role="tab" aria-controls="email" aria-selected="true">Email 1</a>
                                      </li>
                                      <li class="nav-item">
                                        <a class="nav-link nav-reset" id="email-tab" data-toggle="tab" href="#email" role="tab" aria-controls="email" aria-selected="false">Email 2</a>
                                      </li>
                                      <li class="nav-item">
                                        <a class="nav-link nav-reset" id="email-tab" data-toggle="tab" href="#email" role="tab" aria-controls="email" aria-selected="false">Email 3</a>
                                      </li>
                                      <li class="nav-item">
                                        <a class="nav-link nav-reset" id="email-tab" data-toggle="tab" href="#email" role="tab" aria-controls="email" aria-selected="false">Email 4</a>
                                      </li>
                                      <li class="nav-item">
                                        <a class="nav-link nav-reset" id="email-tab" data-toggle="tab" href="#email" role="tab" aria-controls="email" aria-selected="false">Email 5</a>
                                      </li>
                                      <li class="nav-item">
                                        <a class="nav-link nav-reset" id="email-tab" data-toggle="tab" href="#email" role="tab" aria-controls="email" aria-selected="false">Email 6</a>
                                      </li>
                                      <li class="nav-item">
                                        <a class="nav-link nav-reset" id="email-tab" data-toggle="tab" href="#email" role="tab" aria-controls="email" aria-selected="false">Email 7</a>
                                      </li>
                                      <li class="nav-item">
                                        <a class="nav-link nav-reset" id="email-tab" data-toggle="tab" href="#email" role="tab" aria-controls="email" aria-selected="false">Email 8</a>
                                      </li>
                                      <li class="nav-item">
                                        <a class="nav-link nav-reset" id="email-tab" data-toggle="tab" href="#email" role="tab" aria-controls="email" aria-selected="false">Email 9</a>
                                      </li>
                                    </ul>
                                </div>
                                <div class="card-body box-profile">
                                    <form action="<?=base_url();?>Menu/addAllResource" method="post" enctype="multipart/form-data" id="d">
                                        <div class="was-validated">
                                            <input type="hidden" name="uid" value="<?=$uid?>">
                                            <input type="hidden" name="type" value="email">
                                            <div class="form-group">
                                                <label for="task_type">Write Email Draft Text</label><br>
                                                <textarea placeholder='Write Email Draft Text' class="form-control" name="message" id="email" rows="10" required></textarea>
                                            </div>
                                            <div class="form-group">
                                                <label>Upload Email Draft</label><br>
                                                <input type="file" class="form-control" name="filname[]" id="uploademail" required multiple>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <div class="tab-pane fade" id="whatsapp" role="tabpanel" aria-labelledby="whatsapp-tab">
                            <div class="card card-primary card-outline">
                                <div class="card-header">
                                    <!--<h3>9 Whats App</h3>-->
                                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                                      <li class="nav-item">
                                        <a class="nav-link active nav-reset" id="whatsapp-tab" data-toggle="tab" href="#whatsapp" role="tab" aria-controls="whatsapp" aria-selected="true">Whats app 1</a>
                                      </li>
                                      <li class="nav-item">
                                        <a class="nav-link nav-reset" id="whatsapp-tab" data-toggle="tab" href="#whatsapp" role="tab" aria-controls="whatsapp" aria-selected="false">Whats app 2</a>
                                      </li>
                                      <li class="nav-item">
                                        <a class="nav-link nav-reset" id="whatsapp-tab" data-toggle="tab" href="#whatsapp" role="tab" aria-controls="whatsapp" aria-selected="false">Whats app 3</a>
                                      </li>
                                      <li class="nav-item">
                                        <a class="nav-link nav-reset" id="whatsapp-tab" data-toggle="tab" href="#whatsapp" role="tab" aria-controls="whatsapp" aria-selected="false">Whats app 4</a>
                                      </li>
                                      <li class="nav-item">
                                        <a class="nav-link nav-reset" id="whatsapp-tab" data-toggle="tab" href="#whatsapp" role="tab" aria-controls="whatsapp" aria-selected="false">Whats app 5</a>
                                      </li>
                                      <li class="nav-item">
                                        <a class="nav-link nav-reset" id="whatsapp-tab" data-toggle="tab" href="#whatsapp" role="tab" aria-controls="whatsapp" aria-selected="false">Whats app 6</a>
                                      </li>
                                      <li class="nav-item">
                                        <a class="nav-link nav-reset" id="whatsapp-tab" data-toggle="tab" href="#whatsapp" role="tab" aria-controls="whatsapp" aria-selected="false">Whats app 7</a>
                                      </li>
                                      <li class="nav-item">
                                        <a class="nav-link nav-reset" id="whatsapp-tab" data-toggle="tab" href="#whatsapp" role="tab" aria-controls="whatsapp" aria-selected="false">Whats app 8</a>
                                      </li>
                                      <li class="nav-item">
                                        <a class="nav-link nav-reset" id="whatsapp-tab" data-toggle="tab" href="#whatsapp" role="tab" aria-controls="whatsapp" aria-selected="false">Whats app 9</a>
                                      </li>
                                    </ul>
                                </div>
                                <div class="card-body box-profile">
                                    <form action="<?=base_url();?>Menu/addAllResource" method="post" enctype="multipart/form-data" id="d">
                                        <div class="was-validated">
                                            <input type="hidden" name="uid" value="<?=$uid?>">
                                            <input type="hidden" name="type" value="whatsapp">
                                            <div class="form-group">
                                                <label for="task_type">Write Whats App Message</label><br>
                                                <textarea placeholder='Write Whats App Message' class="form-control" name="message" id="whatsapp" rows="10" required></textarea>
                                            </div>
                                            <div class="form-group">
                                                <label>Upload File</label><br>
                                                <input type="file" class="form-control" name="filname[]" id="uploadwhatsapp" required multiple>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <div class="tab-pane fade" id="regional" role="tabpanel" aria-labelledby="regional-tab">
                            <div class="card card-primary card-outline">
                                <div class="card-header">
                                    <h3>Regional Case Study</h3>
                                </div>
                                <div class="card-body box-profile">
                                    <form action="<?=base_url();?>Menu/addAllResource" method="post" enctype="multipart/form-data">
                                        <div class="was-validated">
                                            <input type="hidden" name="uid" value="<?=$uid?>">
                                            <input type="hidden" name="type" value="regional-case-study">
                                            <div class="form-group">
                                                <label for="task_type">Write Regional Case Study</label><br>
                                                <textarea placeholder='Write Regional Case Study' class="form-control" name="message" id="regional" rows="10" required></textarea>
                                            </div>
                                            <div class="form-group">
                                                <label>Upload Case Study</label><br>
                                                <input type="file" class="form-control" name="filname[]" id="uploadregional" required multiple>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <div class="tab-pane fade" id="quarter" role="tabpanel" aria-labelledby="quarter-tab">
                            <div class="card card-primary card-outline">
                                <div class="card-header">
                                    <h3>Quarter 1 PPT</h3>
                                </div>
                                <div class="card-body box-profile">
                                    <form action="<?=base_url();?>Menu/addAllResource" method="post" enctype="multipart/form-data">
                                        <div class="was-validated">
                                            <input type="hidden" name="uid" value="<?=$uid?>">
                                            <input type="hidden" name="type" value="quarter1-ppt">
                                            
                                            <div class="form-group">
                                                <label>Upload PPT</label><br>
                                                <input type="file" class="form-control" name="filname[]" id="uploadquarterppt" required multiple>
                                            </div>
                                            <div class="form-group">
                                                <label>Please Mention The Slide Number</label><br>
                                                <input type="text" class="form-control" name="slideno" id="slideno" placeholder="Please Mention The Slide Number" required>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <div class="tab-pane fade" id="annual" role="tabpanel" aria-labelledby="annual-tab">
                            <div class="card card-primary card-outline">
                                <div class="card-header">
                                    <h3>Annual Review</h3>
                                </div>
                                <div class="card-body box-profile">
                                    <form action="<?=base_url();?>Menu/addAllResource" method="post" enctype="multipart/form-data">
                                        <div class="was-validated">
                                            <input type="hidden" name="uid" value="<?=$uid?>">
                                            <input type="hidden" name="type" value="annualreview">
                                            <div class="form-group">
                                                <label>Partner Type</label><br>
                                                <select name="partnership" id="partnership" class="form-control">
                                                <?php $ptype = $this->Menu_model->get_partner();
                                                foreach($ptype as $p){?>
                                                <option value="<?=$p->id?>"><?=$p->name?></option>
                                                <?php }?>
                                            </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="task_type">What changes do you suggest in the PPT? </label><br>
                                                <!--<input type="text" placeholder='What All Unique Points You will Cover in Your PPT' class="form-control" name="pointscover" id="ppt" required>-->
                                                <textarea  placeholder='What changes do you suggest in the PPT' class="form-control" name="pointscover" id="ppt" rows="10" required></textarea>
                                            </div>
                                            <div class="form-group">
                                                <label>Please Mention The Slide Number</label><br>
                                                <input type="text" class="form-control" name="slideno" id="slideno" placeholder="Please Mention The Slide Number" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Attach PPT</label><br>
                                                <input type="file" class="form-control" name="filname[]" id="fileInput" required multiple>
                                            </div>
                                            <div class="form-group">
                                                <label>Reference You Tube Or Video Link Which you reffered</label><br>
                                                <div class="input-group">
                                                <input type="text" placeholder='Reference You Tube Or Video Link Which you reffered' class="form-control" name="reference[]" id="linkInput2" required>
                                                <span class="input-group-text plus-icon2"><i class="fas fa-plus"></i></span>
                                            </div>
                                            <div class="more2"></div>
                                        </div>
                                            <div id="validationMessage"></div>
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                            <div id="preview"></div>
                                    </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="district" role="tabpanel" aria-labelledby="district-tab">
                            <div class="card card-primary card-outline">
                                <div class="card-header">
                                    <h3>District Permission Letter</h3>
                                </div>
                                <div class="card-body box-profile">
                                    <form action="<?=base_url();?>Menu/addAllResource" method="post" enctype="multipart/form-data">
                                        <div class="was-validated">
                                            <input type="hidden" name="uid" value="<?=$uid?>">
                                            <input type="hidden" name="type" value="district-permission">
                                            
                                            <div class="form-group">
                                                <label for="task_type">Department details</label><br>
                                                <!--<input type="text" placeholder='What All Unique Points You will Cover in Your PPT' class="form-control" name="pointscover" id="ppt" required>-->
                                                <textarea  placeholder='Enter Department details' class="form-control" name="deptdetail" id="ppt" rows="10" required></textarea>
                                            </div>
                                            <div class="form-group">
                                                <label>Department Type</label><br>
                                                <input type="text" placeholder='Department Type' class="form-control" name="depttype"  required>
                                        </div>
                                            <div class="form-group">
                                                <label>Upload Letter</label><br>
                                                <input type="file" class="form-control" name="filname[]" id="fileInput" required multiple>
                                            </div>
                                            
                                            
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                            <div id="preview"></div>
                                    </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <div class="tab-pane fade" id="school" role="tabpanel" aria-labelledby="school-tab">
                            <div class="card card-primary card-outline">
                                <div class="card-header">
                                    <h3>Add School Details</h3>
                                </div>
                                <div class="card-body box-profile">
                                    <form action="<?=base_url();?>Menu/insertSchoolDetails" method="post" enctype="multipart/form-data">
                                        <input type="hidden" name="uid" value="<?=$uid?>">
                                        <div class="was-validated">
                                            <div class="form-group">
                                                <label for="task_date">Client Name</label>
                                                <input type="text" class="form-control" name="clientname" id="clientname" placeholder="Enter Client Name" required>
                                              </div>
                                              <div class="form-group">
                                                <label for="">District</label>
                                                <input type="text" class="form-control" name="district" id="district"  placeholder="Enter District" required>
                                              </div>
                                              <div class="form-group">
                                                <label for="">School Location</label>
                                                <input type="text" class="form-control" name="slocation" id="slocation"  placeholder="Enter School Location" required>
                                              </div>
                                              <div class="form-group">
                                                <label for="">No Of Schools</label>
                                                <input type="number" class="form-control" name="noofschool" id="noofschool"  placeholder="Enter No Of Schools" required>
                                              </div>
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                </div>   
            </div>     
    </section>
  
  
  
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script type='text/javascript'>
$(document).ready(function(){
    $('#v-pills-salesppt').click(function(){
        $('#v-pills-faq').hide();
    });
    
    $('.nav-reset').click(function(){
        $("#d").get(0).reset(); 
    });
    
    $('#linkInput').on('input', function() {
        var link = $(this).val();
        var urlRegex = /^(https?|ftp):\/\/[^\s/$.?#].[^\s]*$/;
        if (urlRegex.test(link)) {
            $('#validationMessage').text('Valid URL');
            $('#validationMessage').css('color', 'green');
        } else {
            $('#validationMessage').text('Invalid URL');
            $('#validationMessage').css('color', 'red');
        }
    });
    
    $('#linkInput1').on('input', function() {
        var link = $(this).val();
        var urlRegex = /^(https?|ftp):\/\/[^\s/$.?#].[^\s]*$/;
        if (urlRegex.test(link)) {
            $('#validationMessage').text('Valid URL');
            $('#validationMessage').css('color', 'green');
        } else {
            $('#validationMessage').text('Invalid URL');
            $('#validationMessage').css('color', 'red');
        }
    });
    
    $('#linkInput2').on('input', function() {
        var link = $(this).val();
        var urlRegex = /^(https?|ftp):\/\/[^\s/$.?#].[^\s]*$/;
        if (urlRegex.test(link)) {
            $('#validationMessage').text('Valid URL');
            $('#validationMessage').css('color', 'green');
        } else {
            $('#validationMessage').text('Invalid URL');
            $('#validationMessage').css('color', 'red');
        }
    });
    
    
    $('.plus-icon').click(function() {
        // $('.more').append('<div class="row"><div class="col-md-12"><div class="form-group"><input type="text" placeholder="Reference YouTube Or Video Link Which you referred" class="form-control" name="reference" required></div></div></div>');
        $('.more').append('<div class="form-group"><div class="input-group"><input type="text" placeholder="Reference YouTube Or Video Link Which you referred" class="form-control" name="reference[]" required><span class="input-group-text minus-icon"><i class="fas fa-minus"></i></span></div></div>');
    });
    $('.more').on('click', '.minus-icon', function() {
        $(this).closest('.form-group').remove();
    });
    
    $('.plus-icon1').click(function() {
        // $('.more').append('<div class="row"><div class="col-md-12"><div class="form-group"><input type="text" placeholder="Reference YouTube Or Video Link Which you referred" class="form-control" name="reference" required></div></div></div>');
        $('.more1').append('<div class="form-group"><div class="input-group"><input type="text" placeholder="Reference YouTube Or Video Link Which you referred" class="form-control" name="reference[]" required><span class="input-group-text minus-icon"><i class="fas fa-minus"></i></span></div></div>');
    });
    
    $('.more1').on('click', '.minus-icon', function() {
        $(this).closest('.form-group').remove();
    });
    
    $('.plus-icon2').click(function() {
        // $('.more').append('<div class="row"><div class="col-md-12"><div class="form-group"><input type="text" placeholder="Reference YouTube Or Video Link Which you referred" class="form-control" name="reference" required></div></div></div>');
        $('.more2').append('<div class="form-group"><div class="input-group"><input type="text" placeholder="Reference YouTube Or Video Link Which you referred" class="form-control" name="reference[]" required><span class="input-group-text minus-icon"><i class="fas fa-minus"></i></span></div></div>');
    });
    
    $('.more2').on('click', '.minus-icon', function() {
        $(this).closest('.form-group').remove();
    });
    
  });


</script>

          
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
<!-- AdminLTE App -->
<script src="<?=base_url();?>assets/js/adminlte.js"></script>

<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="<?=base_url();?>assets/js/dashboard.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js"></script>

<script>
 
            
            

    $("#example1").DataTable({
      "responsive": false, "lengthChange": false, "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
</script>



</body>
</html>
