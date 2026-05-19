<!DOCTYPE html>

<html lang="en">

<head>

        <meta charset="utf-8">

        <meta name="viewport" content="wi$dth=device-wi$dth, initial-scale=1">

        <?php foreach ($cd as $ch) { ?>

                <title><?= $ch->compname ?> - Company Details - STEM APP | WebAPP</title>

        <?php } ?>

        <!-- Google Font: Source Sans Pro -->

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">

        <!-- Font Awesome -->

        <link rel="stylesheet" href="<?= base_url(); ?>assets/css/all.min.css">

        <!-- Ionicons -->

        <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">

        <!-- Tempusdominus Bootstrap 4 -->

        <link rel="stylesheet" href="<?= base_url(); ?>assets/css/tempusdominus-bootstrap-4.min.css">

        <!-- iCheck -->

        <link rel="stylesheet" href="<?= base_url(); ?>assets/css/icheck-bootstrap.min.css">

        <!-- JQVMap -->

        <link rel="stylesheet" href="<?= base_url(); ?>assets/css/jqvmap.min.css">

        <!-- Theme style -->

        <link rel="stylesheet" href="<?= base_url(); ?>assets/css/adminlte.min.css">

        <!-- overlayScrollbars -->

        <link rel="stylesheet" href="<?= base_url(); ?>assets/css/OverlayScrollbars.min.css">

        <!-- Daterange picker -->

        <link rel="stylesheet" href="<?= base_url(); ?>assets/css/daterangepicker.css">

        <!-- summernote -->

        <link rel="stylesheet" href="<?= base_url(); ?>assets/css/summernote-bs4.min.css">

        <!-- DataTables -->

        <link rel="stylesheet" href="<?= base_url(); ?>assets/css/dataTables.bootstrap4.min.css">

        <link rel="stylesheet" href="<?= base_url(); ?>assets/css/responsive.bootstrap4.min.css">

        <link rel="stylesheet" href="<?= base_url(); ?>assets/css/buttons.bootstrap4.min.css">

<style>
.cat1::after,
.cat2::after,
.cat3::after,
.cat4::after,
.cat5::after,
.cat6::after,
.cat7::after,
.cat8::after {
content: '';
position: absolute;
top: 6px;
left: 6px;
right: 6px;
bottom: 6px;
display: block
}

.cat1 a,
.cat2 a,
.cat3 a,
.cat4 a,
.cat5 a,
.cat6 a,
.cat8 a {
        text-decoration: none;
        z-index: 10
}

.scrollme {
        overflow-x: auto
}

.mainBlock {
        margin-top: 10px;
        padding: 10px;
        border-radius: 5px;
        min-height: 80px;
        min-width: 150px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: .5s;
        position: relative
}

.mainBlock.contact {
        min-width: 400px;
        display: block
}

.cat1::after {
        background: 0 0;
        border: 1px solid #1ab6a9
}

.cat1:hover::after,
.cat2:hover::after,
.cat3:hover::after,
.cat4:hover::after,
.cat5:hover::after,
.cat6:hover::after,
.cat7:hover::after,
.cat8:hover::after {
        border-color: #fff;
        color: #fff
}

.cat1:hover {
        background: #1ab6a9 !important
}

.cat1 {
        background: #effaf8 !important
}

.cat1 a {
        color: #1ab6a9;
}

.cat2::after {
        background: 0 0;
        border: 1px solid #f06378
}

.cat2:hover {
        background: #f06378 !important
}

.cat2 {
        background: #fef2f4 !important
}

.cat2 a {
        color: #f06378
}

.cat3::after {
        background: 0 0;
        border: 1px solid #f8b81f
}

.cat3:hover {
        background: #f8b81f !important
}

.cat3 {
        background: #fffaef !important
}

.cat3 a {
        color: #f8b81f
}

.cat4::after {
        background: 0 0;
        border: 1px solid #0ecd73
}

.cat4:hover {
        background: #0ecd73 !important
}

.cat4 {
        background: #eefbf5 !important
}

.cat4 a {
        color: #0ecd73
}

.cat5::after {
        background: 0 0;
        border: 1px solid #8e56ff
}

.cat5:hover {
        background: #8e56ff !important
}

.cat5 {
        background: #f7f3ff !important
}

.cat5 a {
        color: #8e56ff
}

.cat6::after {
        background: 0 0;
        border: 1px solid #f8941f
}

.cat6:hover {
        background: #f8941f !important
}

.cat6 {
        background: #fff7ef !important
}

.cat6 a {
        color: #f8941f
}

.cat7::after {
        background: 0 0;
        border: 1px solid #48c5fa
}

.cat7:hover {
        background: #48c5fa !important
}

.cat7 {
        background: #f1fbff !important
}

.cat8::after {
        background: 0 0;
        border: 1px solid #f92596
}

.cat8:hover {
        background: #f92596 !important
}

.cat8 {
        background: #fff0f8 !important
}

.cat8 a {
        color: #f92596
}

.mainBlock:hover {
        box-shadow: 0 0 5px #aaa;
        background-image: linear-gradient(0deg, #fff, #ffffffe0), url(../img/backGround/landing-banner1.png);
        background-attachment: fixed;
        background-size: 100%;
        transform: scale(1.001);
        background-position: center;
        -webkit-animation: 15s linear forwards serviceBgAnim;
        -o-animation: 15s linear forwards serviceBgAnim;
        animation: 15s linear forwards serviceBgAnim
}

.mainBlock:hover>a {
        color: #fff !important;
}

.mainBlock:hover {
        color: #fff !important;
}

.mainBlock a h5 {
        font-size: 14px;
        margin: 0
}

.cat1.contactdt::after,
.cat2.contactdt::after,
.cat3.contactdt::after,
.cat4.contactdt::after,
.cat5.contactdt::after,
.cat6.contactdt::after,
.cat7.contactdt::after,
.cat8.contactdt::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        display: none;
}

.mainBlock a h5 {

        font-size: 16px;

        padding: 4px;

}

.mainBlock.status {

        display: flex;

        min-height: 40px;

}

.dflex {

        display: -ms-flexbox !important;

        display: flex !important;

        align-items: center;

}

.mainBlock table h5 {
        font-size: 14px;
        margin: 0
}

.mainBlock.tabletar:hover {

        color: #000 !important;

}

.cat1.mainBlockdemo::after {

        content: '';

        position: absolute;

        top: 0px;

        left: 0px;

        right: 0px;

        bottom: 0px;

        display: ruby;

}
        </style>

</head>

<body class="hold-transition sidebar-mini layout-fixed">

<div class="wrapper">
                <?php require('nav.php'); ?>

                <?php include 'addlog.php'; ?>

        <div class="content-wrapper">
                        <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-12 text-center">
                                <h1 class="m-0">Company Details</h1>
                        </div>
                    </div>
                        <!-- /.row -->
                </div>
                    <!-- /.container-fluid -->
            </div>
                        <!-- /.content-header -->

                        <!-- Main content -->

            <section class="content">
                <div class="container-fluid">
                    <div class="row p-3">
                        <div class="col-sm col-lg-8">
                            <div class="card card-primary card-outline">
                                <div class="card-body box-profile">
                                    <?php foreach ($cd as $c) { ?>

                                        <div class="row p-2" style="background: #f92596;">
                                                <div class="col-sm col-lg-6 dflex text-white">
                                                        <h5><?= $c->compname ?></h5>
                                                </div>

                                                <div class="col-sm col-lg-3 dflex text-white">
                                                        <h5><?= $cstatus = $cstatus[0]->name ?></h5>
                                                </div>

                                                <div class="col-sm col-lg-3 dflex text-white">
                                                        <button type="button" id="add_createact" class="btn btn-light"><b>+</b> Create Task</button>
                                                </div>
                                        </div>

                                        <div class="row">
                                            <table id="examplestatus12" class="table table-striped" cellspacing="0" width="100%">
                                                <thead>
                                                        <tr>
                                                                <th></th>
                                                                <th></th>
                                                        </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>Total Logs</td>
                                                        <td><?= sizeof($tblc) ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>BD Assigned</td>
                                                        <td><?php $mbd = $this->Menu_model->get_userbyid($mainbd);
                                                                echo $mbd[0]->name ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>PST Assigned</td>
                                                        <td><?php 
                                                            $apst =$mbd[0]->pst_co;
								if ($apst) {
								$apst = $this->Menu_model->get_userbyid($apst);
                                                                echo $apst[0]->name;
                                                            }else {
                                                                echo '';
                                                            } ?>
                                                        </td>
                                                    </tr>
                                                    <?php

                                                        if (sizeof($ccd) > 0) {
                                                            $contactperson = $ccd[0]->contactperson;
                                                            $phoneno = $ccd[0]->phoneno;
                                                            $emailid = $ccd[0]->emailid;
                                                            $designation = $ccd[0]->designation;
                                                        } else {
                                                            $contactperson = "";
                                                            $phoneno = "";
                                                            $emailid = "";
                                                            $designation = "";
                                                        }

                                                    ?>
                                                    <tr>
                                                        <td>Email</td>
                                                        <td><?= $emailid ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Address</td>
                                                        <td><?= $c->address ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>State</td>
                                                        <td><?= $c->state ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>City</td>
                                                        <td><?= $c->city ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Country</td>
                                                        <td><?= $c->country ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Website</td>
                                                        <td><?= $c->website ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Phone Number</td>
                                                        <td><?= $phoneno ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                            <span>
                                                                <a href="tel:+91<?= $phoneno ?>" target="_blank" style="padding:7px;border-radius:20px;">
                                                                    <img src="https://stemlearning.in/opp/assets/image/icon/call.png" style="height:30px;">
                                                                </a>

                                                                <a href="https://wa.me/91<?= $phoneno ?>" target="_blank" style="padding:7px;border-radius:20px;">
                                                                    <img src="https://stemlearning.in/opp/assets/image/icon/whatsapp.png" style="height:30px;">

                                                                </a>
                                                            </span>

                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Updated Location</td>
                                                        <td>
                                                            <?php

                                                                foreach ($init as $in) {
                                                                    if ($in->location !== '') {
                                                                        echo $in->location;
                                                                    }
                                                                } ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Reveiw Details</td>
                                                        <td>
                                                                <a href="<?= base_url(); ?>Menu/AnnualReviewDetailsData/<?= $init[0]->cmpid_id ?>/<?= $init[0]->mainbd ?>">Click Here</a>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="card-footer text-muted p-3">

                                        <b>Logs</b>&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;<a href="../PrimaryContact/<?= $c->id ?>"><b>Edit Contact</b></a>

                                    </div>
                            </div>

                            <div class="card card-success card-outline">
                                <div class="card-body box-profile">
                                    <h5>Conversion Details:</h5>
                                        <div class="row">

                                            <?php
                                                $cat = 1;
                                                //echo"<pre>status ";print_r($status);exit;

                                                foreach ($merged as $st) {
                                                        if ($cat > 8) {
                                                                $cat = 1;
                                                        }
                                                        $statusname1 = $st->name;
                                                        $count = $st->count;
                                                        $color = $st->color;
                                            ?>
                                            <div class="col">
                                                    <div class="col text-center mainBlock status cat<?= $cat ?> shadow p-2 bg-white rounded">
                                                            <a href="javascript:void(0)">
                                                                    <h5><?= $statusname1 ?> [<?= $count ?>]</h5>
                                                            </a>
                                                    </div>

                                            </div>

                                        <?php $cat++;
                                            } ?>

                                        </div>
                                </div>
                            </div>

                            <style>
                                    .mainBlock {
                                            display: flow;
                                    }
                            </style>
                            <div class="card card-success card-outline">
                                <div class="card-body box-profile">
                                    <div class="text-center" style="width:100%">
                                            <h5>Positive Status</h5>
                                    </div>

                                <hr/>

                                <div class="row">
                                    <?php foreach ($init as $in) { ?>

                                        <div class="col">
                                            <div class="col text-left mainBlock cat1 mainBlockdemo  p-3 mb-2 bg-white rounded">

                                                    <table id="examplestatus1" class="table table-striped" cellspacing="0" width="100%">

                                                            <thead>

                                                                    <tr>

                                                                            <th></th>

                                                                            <th></th>

                                                                    </tr>

                                                            </thead>

                                                            <tbody>

                                                                    <tr>

                                                                            <td>
                                                                                    <h5>Financial Year</h5>
                                                                            </td>

                                                                            <td><?= $in->fyear ?></td>

                                                                    </tr>

                                                                    <tr>

                                                                            <td>
                                                                                    <h5>No of School</h5>
                                                                            </td>

                                                                            <td><?= $in->noofschools ?></td>

                                                                    </tr>

                                                                    <tr>

                                                                            <td>
                                                                                    <h5>Current Status</h5>
                                                                            </td>

                                                                            <td>
                                                                                    <h5><?= $this->Menu_model->get_statusbyid($in->cstatus)[0]->name; ?></h5>
                                                                            </td>

                                                                    </tr>

                                                                    <tr>

                                                                            <td>
                                                                                    <h5>Topspender</h5>
                                                                            </td>

                                                                            <td>
                                                                                    <h5><?= $in->topspender ?></h5>
                                                                            </td>

                                                                    </tr>

                                                                    <tr>

                                                                            <td>
                                                                                    <h5>Upsell Client</h5>
                                                                            </td>

                                                                            <td>
                                                                                    <h5><?= $in->upsell_client ?></h5>
                                                                            </td>

                                                                    </tr>

                                                                    <tr>

                                                                            <td>
                                                                                    <h5>Focus Funnel</h5>
                                                                            </td>

                                                                            <td>
                                                                                    <h5><?= $in->focus_funnel ?></h5>
                                                                            </td>

                                                                    </tr>

                                                                    <tr>

                                                                            <td>
                                                                                    <h5>Expected Revenue</h5>
                                                                            </td>

                                                                            <td>
                                                                                    <h5><?= $in->exrevenue ?> (?)</h5>
                                                                            </td>

                                                                    </tr>

                                                                    <tr>

                                                                            <td>
                                                                                    <h5>Virtual Meeting</h5>
                                                                            </td>

                                                                            <td>
                                                                                    <h5><?= $in->vmeeting ?></h5>
                                                                            </td>

                                                                    </tr>

                                                                    <tr>

                                                                            <td>
                                                                                    <h5>Focus Positive</h5>
                                                                            </td>

                                                                            <td>
                                                                                    <h5><?= $in->focuspositive ?></h5>
                                                                            </td>

                                                                    </tr>

                                                                    <tr>

                                                                            <td>
                                                                                    <h5>interventions</h5>
                                                                            </td>

                                                                            <td>
                                                                                    <h5><?= $this->Menu_model->get_userbyid($in->interventions)[0]->name ?></h5>
                                                                            </td>

                                                                    </tr>

                                                                    <tr>

                                                                            <td>
                                                                                    <h5>Cluster Name</h5>
                                                                            </td>

                                                                            <td>
                                                                                    <h5><?= $this->Menu_model->getClusterByid($in->cluster_id)[0]->clustername ?></h5>
                                                                            </td>

                                                                    </tr>

                                                                    <tr>

                                                                            <td>
                                                                                    <h5>Support</h5>
                                                                            </td>

                                                                            <td>
                                                                                    <h5><?= $in->support ?></h5>
                                                                            </td>

                                                                    </tr>

                                                            </tbody>

                                                    </table>



                                            </div>
                                            <div class="col text-left mainBlock tabletar cat1 mainBlockdemo p-3 mb-2 bg-white rounded">
                                                <div class="text-center" style="width:100%">
                                                        <h5>Current year Status Target</h5>
                                                </div>
                                            <hr/>
                                            <table id="examplestatus" class="table table-striped" cellspacing="0" width="100%">
                                                <thead class="thead-dark">
                                                    <tr>
                                                        <th>From Status </th>
                                                        <th>To Status</th>
                                                        <th>Target Date</th>
                                                        <th>Days Difference</th>
                                                    </tr>

                                                </thead>

                                                <tbody>

                                                    <?php if ($in->open !== '') { ?>

                                                        <tr class="table-primary">
                                                            <td>Open</td>
                                                            <td>Open RPEM</td>
                                                            <td><?= $in->open ?></td>
                                                            <td>
                                                                <?php
                                                                    $date1 = new DateTime();
                                                                    $date2 = new DateTime($in->open);
                                                                    $interval = $date1->diff($date2);
                                                                    echo $interval->format('%R%a days');
                                                                ?>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>

                                                        <tr class="table-secondary">
                                                            <td>Open RPEM</td>
                                                            <td>Reachout</td>
                                                            <td><?= $in->reachout ?></td>
                                                            <td>
                                                                <?php

                                                                    $date1 = new DateTime();
                                                                    $date2 = new DateTime($in->reachout);
                                                                    $interval = $date1->diff($date2);
                                                                    echo $interval->format('%R%a days');
                                                                ?>
                                                            </td>
                                                        </tr>
                                                        <tr class="table-success">
                                                            <td>Reachout</td>
                                                            <td>Tentative</td>
                                                            <td><?= $in->positive ?></td>
                                                            <td>
                                                                <?php
                                                                    $date1 = new DateTime();

                                                                    $date2 = new DateTime($in->positive);

                                                                    $interval = $date1->diff($date2);

                                                                    echo $interval->format('%R%a days');
                                                                ?>
                                                            </td>
                                                        </tr>

                                                        <tr class="table-danger">

                                                            <td>Tentative</td>
                                                            <td>Positive NAP</td>
                                                            <td><?= $in->positivenap ?></td>
                                                            <td>
                                                                <?php
                                                                    $date1 = new DateTime();
                                                                    $date2 = new DateTime($in->positivenap);
                                                                    $interval = $date1->diff($date2);
                                                                    echo $interval->format('%R%a days');
                                                                ?>
                                                            </td>
                                                        </tr>

                                                        <tr class="table-warning">

                                                            <td>Positive NAP</td>
                                                            <td>Positive</td>
                                                            <td><?= $in->positive ?></td>
                                                            <td>
                                                                <?php

                                                                    $date1 = new DateTime();

                                                                    $date2 = new DateTime($in->positive);

                                                                    $interval = $date1->diff($date2);

                                                                    echo $interval->format('%R%a days');

                                                                ?>
                                                            </td>

                                                        </tr>

                                                        <tr class="table-info">

                                                            <td>Positive</td>
                                                            <td>Very Positive</td>
                                                            <td><?= $in->verypositive ?></td>
                                                            <td>
                                                                <?php

                                                                    $date1 = new DateTime();

                                                                    $date2 = new DateTime($in->verypositive);

                                                                    $interval = $date1->diff($date2);

                                                                    echo $interval->format('%R%a days');

                                                                ?>
                                                            </td>
                                                        </tr>

                                                        <tr class="table-light">
                                                            <td>Very Positive</td>
                                                            <td>Closure</td>
                                                            <td><?= $in->closure ?></td>
                                                            <td>
                                                                <?php

                                                                    $date1 = new DateTime();
                                                                    $date2 = new DateTime($in->closure);
                                                                    $interval = $date1->diff($date2);
                                                                    echo $interval->format('%R%a days');

                                                                ?>
                                                            </td>
                                                        </tr>
                                                </tbody>

                                            </table>

                                        </div>

                                    </div>

                                <?php } ?>

                            </div>





                        </div>
                    </div>
                </div>
            <?php } ?>

                <div class="col-sm col-lg-4 showdata">
                    <div class="card card-primary card-outline">
                        <div class="card-body box-profile">
                            <div class="row p-2" style="background:#0ecd73;">
                                <div class="col-sm col-lg-6 text-white dflex">
                                    <h4>Contact - </h4>
                                </div>

                                <div class="col-sm col-lg-6 text-white dflex">
                                    <button type="button" id="add_cont" class="btn btn-light" value="<?= $c->id ?>" style="float:right;"><b>+</b> Add Contact</button>
                                </div>

                            </div>

                        <hr>

                        <div class="row">
                            <?php

                                $cat = 'cat';
                                $k = 1;

                                foreach ($pccd as $d) {

                                    $contactperson = $d->contactperson;
                                    $phoneno = $d->phoneno;
                                    $emailid = $d->emailid;
                                    $designation = $d->designation;
                                    $type = $d->type;

                            ?>

                            <div class="col">
                                <div class="col text-left mainBlock contact <?= $cat . $k ?> contactdt shadow p-3 mb-3 bg-white rounded">
                                    <span><b>(<?= $type ?>)</b></span>
                                <hr/>

                                    <span>Name : <?= $contactperson ?></span> <br />
                                    <span>Designation : <?= $designation ?></span><br />
                                    <span>Email id : <?= $emailid ?></span><br />
                                    <span>Phone No : <?= $phoneno ?>

                                        <a href="tel:+91<?= $phoneno ?>" target="_blank" style="padding:7px;border-radius:20px;">
                                            <img src="https://stemlearning.in/opp/assets/image/icon/call.png" style="height:30px;">\
                                        </a>

                                        <a href="https://wa.me/91<?= $phoneno ?>" target="_blank" style="padding:7px;border-radius:20px;">
                                            <img src="https://stemlearning.in/opp/assets/image/icon/whatsapp.png" style="height:30px;">
                                        </a>

                                    </span>
                                <br>

                                    <a class='text-primary' href="../EditContact/<?= $d->id ?>/<?= $c->id ?>">Edit Contact</a>
                                </div>

                            </div>

                            <?php $k++;
                                } ?>

                        </div>

                    </div>
                </div>

                        <!-- /.card-header -->


                        <!-- /.card-header -->

            </div>
        <!-- /.card -->
        </div>

            <div class="btn btn-success colapsboxsha col-md-12 col-lg-12 mt-2 d-flex justify-content-between align-items-center" data-toggle="collapse" href="#collapseExample<?= $k ?>" role="button" aria-expanded="false" aria-controls="collapseExample<?= $k ?>">

                Completed Task Logs :

            </div>

            <div class="collapse show mt-3" id="collapseExample<?= $k ?>">
                <div class="card card-body" style="background: azure;">
                    <div class="ApprovedStatus">
                        <div class="table-responsive">
                            <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                    <tr>

                                        <th>Sr Number</th>
                                        <th>MOM</th>
                                        <th>Remarks</th>
                                        <th>Current Action</th>
                                        <th>Action Taken</th>
                                        <th>Purpose Achieved</th>
                                        <th>Updated Date</th>
                                        <th>Updated By</th>
                                        <th>Last Status</th>
                                        <th>Updated Status</th>
                                        <th>Task Reviewed By</th>
                                        <th>Task Review Remark</th>
                                    </tr>

                                </thead>

                                <tbody>

                                    <?php

                                        $i = 1;
                                        foreach ($tblc as $tb) {
                                          //  if ($tb->nextCFID != '0' && $tb->nstatus_id != '') {

                                                $tid = $tb->id;

                                                $uid = $tb->user_id;

                                                $aid = $tb->actiontype_id;

                                                $lastsid = $tb->status_id;

                                                $upsid = $tb->nstatus_id;

                                                $ui = $this->Menu_model->get_userbyid($uid);

                                                $ai = $this->Menu_model->get_actionbyid($aid);

                                                $lsi = $this->Menu_model->get_statusbyid($lastsid);

                                                $usi = $this->Menu_model->get_statusbyid($upsid);

                                    ?>

                                    <tr>

                                        <td><?= $i ?></td>
                                        <td><?= $tb->mom ?></td>
                                        <td><?= $tb->remarks ?></td>
                                        <td><?= $ai[0]->name ?></td>
                                        <td><?= $tb->actontaken ?></td>
                                        <td><?= $tb->purpose_achieved ?></td>
                                        <td><?= $tb->updateddate ?></td>
                                        <td><?= $ui[0]->name ?></td>
                                        <td><?= $lsi[0]->name; ?></td>
                                        <td><?= $usi[0]->name; ?></td>
                                        <td><?= $tb->reviewedBy; ?></td>
                                        <td><?= $tb->reviewRemark; ?></td>
                                    </tr>
                                    <?php $i++;
                                       // }
                                    } ?>

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <br>
                </div>
            </div>
        <br/>
        <div class="btn btn-primary colapsboxsha col-md-12 col-lg-12 mt-2 d-flex justify-content-between align-items-center" data-toggle="collapse" href="#collapseExample<?= $k ?>" role="button" aria-expanded="false" aria-controls="collapseExample<?= $k ?>">

               Company Review Logs :

            </div>

            <div class="collapse show mt-3" id="collapseExample<?= $k ?>">
                <div class="card card-body" style="background: azure;">
                    <div class="ApprovedStatus">
                        <div class="table-responsive">
                            <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>Sr Number</th>
                                        <th>Review Type</th>
                                        <th>Remarks</th>
                                        <th>Review Assigned Task Type</th>
                                        <th>Task Expected Date</th>
                                        <th>Current Company Status</th>
                                        <th>Expected Status</th>
                                        <th>Review Done For</th>
                                        <th>Review Done By</th>
                                        <th>Review Done On</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $i = 1;
                                       // dd($reviewLogs);
                                    foreach($reviewLogs as $tb) {
                                        if($tb->selfreview == 'SELF'){
                                                $self = '( Self Review )';
                                        }
                                        else{
                                                $self = '';
                                        }
                                    ?>
                                    <tr>
                                        <td><?= $i ?></td>
                                        <td><?= $tb->rtype ?></td>
                                        <td><?= $tb->remarks; ?></td>
                                        <td><?= $tb->taskaction ; ?></td>
                                        <td><?= $tb->exdate ?></td>
                                        <td><?= getCStatusBystatusId($tb->csid) ?></td>
                                        <td><?= getCStatusBystatusId($tb->exsid) ?></td>
                                        <td><?= getUserNameById($tb->for_uid); ?></td>
                                        <td><?= getUserNameById($tb->by_uid); echo  $self;?></td>
                                        <td><?= $tb->cdate ?></td>

                                    </tr>
                                    <?php $i++;   } 
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <br>
                </div>
            </div>
        <br/>

        <div class="btn btn-danger colapsboxsha col-md-12 col-lg-12 d-flex justify-content-between align-items-center" data-toggle="collapse" href="#collapseExampledata" role="button" aria-expanded="false" aria-controls="collapseExample<?= $k ?>">
            Pending Task Logs :
        </div>

            <div class="collapse show mt-3" id="collapseExampledata">
                <div class="card card-body" style="background: azure;">
                    <div class="ApprovedStatus">
                        <div class="table-responsive">
                            <table id="example3" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>Sr Number</th>
                                        <th>Current Action</th>
                                        <th>Created Date</th>
                                        <th>Task For</th>
                                    </tr>

                                </thead>

                                <tbody>
                                    <?php $i = 1;
                                        foreach ($tblc as $tb) {
                                            $tid = $tb->id;
                                            $uid = $tb->user_id;
                                            $aid = $tb->actiontype_id;
                                            $ui = $this->Menu_model->get_userbyid($uid);
                                            $ai = $this->Menu_model->get_actionbyid($aid);
                                        if ($tb->nextCFID == '0') {
                                    ?>

                                    <tr>

                                        <td><?= $i ?></td>
                                        <td><?= $ai[0]->name ?></td>
                                        <td><?= $tb->updateddate ?></td>
                                        <td><?= $ui[0]->name ?></td>

                                    </tr>

                                <?php $i++;
                                        }
                                } ?>

                                </tbody>

                            </table>

                        </div>

                    </div>

                <br>

            </div>

        </div>
                                        <!-- /.container-fluid -->
    </section>
</div>

    <footer class="main-footer">

        <strong>Copyright &copy; 2021-2022 <a href="<?= base_url(); ?>">Stemlearning</a>.</strong>

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

        <script src="<?= base_url(); ?>assets/js/jquery.min.js"></script>

        <!-- jQuery UI 1.11.4 -->

        <script src="<?= base_url(); ?>assets/js/jquery-ui.min.js"></script>

        <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->

        <script>
                $.widget.bridge('uibutton', $.ui.button)
        </script>

        <!-- Bootstrap 4 -->

        <script src="<?= base_url(); ?>assets/js/bootstrap.bundle.min.js"></script>

        <!-- ChartJS -->

        <script src="<?= base_url(); ?>assets/js/Chart.min.js"></script>

        <!-- Sparkline -->

        <script src="<?= base_url(); ?>assets/js/sparkline.js"></script>

        <!-- JQVMap -->

        <script src="<?= base_url(); ?>assets/js/jquery.vmap.min.js"></script>

        <script src="<?= base_url(); ?>assets/js/jquery.vmap.usa.js"></script>

        <!-- jQuery Knob Chart -->

        <script src="plugins/jquery-knob/jquery.knob.min.js"></script>

        <!-- daterangepicker -->

        <script src="<?= base_url(); ?>assets/js/moment.min.js"></script>

        <script src="<?= base_url(); ?>assets/js/daterangepicker.js"></script>

        <!-- Tempusdominus Bootstrap 4 -->

        <script src="<?= base_url(); ?>assets/js/tempusdominus-bootstrap-4.min.js"></script>

        <!-- Summernote -->

        <script src="<?= base_url(); ?>assets/js/summernote-bs4.min.js"></script>

        <!-- overlayScrollbars -->

        <script src="<?= base_url(); ?>assets/js/jquery.overlayScrollbars.min.js"></script>

        <!-- DataTables  & Plugins -->

        <script src="<?= base_url(); ?>assets/js/jquery.dataTables.min.js"></script>

        <script src="<?= base_url(); ?>assets/js/dataTables.bootstrap4.min.js"></script>

        <script src="<?= base_url(); ?>assets/js/dataTables.responsive.min.js"></script>

        <script src="<?= base_url(); ?>assets/js/responsive.bootstrap4.min.js"></script>

        <script src="<?= base_url(); ?>assets/js/dataTables.buttons.min.js"></script>

        <script src="<?= base_url(); ?>assets/js/buttons.bootstrap4.min.js"></script>

        <script src="<?= base_url(); ?>assets/js/jszip.min.js"></script>

        <script src="<?= base_url(); ?>assets/js/pdfmake.min.js"></script>

        <script src="<?= base_url(); ?>assets/js/vfs_fonts.js"></script>

        <script src="<?= base_url(); ?>assets/js/buttons.html5.min.js"></script>

        <script src="<?= base_url(); ?>assets/js/buttons.print.min.js"></script>

        <script src="<?= base_url(); ?>assets/js/buttons.colVis.min.js"></script>

        <!-- AdminLTE App -->

        <script src="<?= base_url(); ?>assets/js/adminlte.js"></script>

        <script src="<?= base_url(); ?>assets/js/myjs.js"></script>

        <!-- AdminLTE dashboard demo (This is only for demo purposes) -->

        <script src="<?= base_url(); ?>assets/js/dashboard.js"></script>

        <script>
                $("#example1").DataTable({

                        "responsive": false,
                        "lengthChange": false,
                        "autoWidth": false,

                        "buttons": ["csv", "excel", "pdf", "print", ]

                }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');



                $("#example2").DataTable({

                        "responsive": false,
                        "lengthChange": false,
                        "autoWidth": false,

                        "buttons": ["csv", "excel", "pdf", "print", ]

                }).buttons().container().appendTo('#example2_wrapper .col-md-6:eq(0)');



                $("#example3").DataTable({

                        "responsive": false,
                        "lengthChange": false,
                        "autoWidth": false,

                        "buttons": ["csv", "excel", "pdf", "print", ]

                }).buttons().container().appendTo('#example3_wrapper .col-md-6:eq(0)');



                $("#examplestatus").DataTable({

                        "responsive": false,
                        "lengthChange": false,
                        "autoWidth": false,

                        "buttons": ["csv", "excel", "pdf", "print", ]

                }).buttons().container().appendTo('#examplestatus_wrapper .col-md-6:eq(0)');



                $("#examplestatus1").DataTable({

                        "responsive": false,
                        "lengthChange": false,
                        "autoWidth": false,
                        "ordering": false,

                        "buttons": ["csv", "pdf", "print", ]

                }).buttons().container().appendTo('#examplestatus1_wrapper .col-md-6:eq(0)');
        </script>

</body>

</html>