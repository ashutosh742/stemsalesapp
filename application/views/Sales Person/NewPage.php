<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="wi$dth=device-wi$dth, initial-scale=1">
  <title>STEM APP | WebAPP</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">
  <link rel="stylesheet" href="<?=base_url();?>assets/css/all.min.css">
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <link rel="stylesheet" href="<?=base_url();?>assets/css/tempusdominus-bootstrap-4.min.css">
  <link rel="stylesheet" href="<?=base_url();?>assets/css/icheck-bootstrap.min.css">
  <link rel="stylesheet" href="<?=base_url();?>assets/css/jqvmap.min.css">
  <link rel="stylesheet" href="<?=base_url();?>assets/css/adminlte.min.css">
  <link rel="stylesheet" href="<?=base_url();?>assets/css/OverlayScrollbars.min.css">
  <link rel="stylesheet" href="<?=base_url();?>assets/css/daterangepicker.css">
  <link rel="stylesheet" href="<?=base_url();?>assets/css/summernote-bs4.min.css">
  <link rel="stylesheet" href="<?=base_url();?>assets/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="<?=base_url();?>assets/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="<?=base_url();?>assets/css/buttons.bootstrap4.min.css">
  <style>
      .scrollme {
    overflow-x: auto;
}
  </style>
  

  
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <?php require('nav.php');?>

  

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
              <h4></h4>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
    

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
          <div class="card col-12  p-3 text-center">
              <center><h5>My Funnel</h5></center><hr>
              
              
              
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Show/Hide Dataset</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        canvas {
            max-width: 100%;
            height: auto !important;
        }
    </style>
</head>
<body>
    
     
           
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.0.1/dist/chart.umd.min.js"></script>

    <div style="width: 80%; margin: auto;">
        <canvas id="stackedChartID1"></canvas>
    </div>

    <script>
        var myContext = document.getElementById("stackedChartID1").getContext('2d');
        var myChart1 = new Chart(myContext, {
            type: 'bar',
            data: {
                labels: [
                    
                    <?php   $currentDate = new DateTime();
                            $financialYear = ($currentDate->format('m') >= 4) ? $currentDate->format('Y') : ($currentDate->format('Y') - 1);
                            for ($month = 4; $month <= 15; $month++) {
                                $adjustedMonth = ($month <= 12) ? $month : ($month - 12);
                                $year = ($month <= 12) ? $financialYear : ($financialYear + 1);
                                $monthName = DateTime::createFromFormat('!m', $adjustedMonth)->format('F');?>
                    "<?=$monthName?> (<?=$year?>)", <?php } ?>],
                datasets: [
                    
                    <?php 
                    $colors = array('red','blue','green','yellow','purple','orange','pink','brown','cyan','magenta','teal','lime','violet','indigo','gray');

                    $action = $this->Menu_model->get_action(); $i=0; foreach($action as $ac){
                        $acid = $ac->id; ?>
                    {
                        label: '<?=$ac->name?>',
                        backgroundColor: "<?=$colors[$i]?>",
                        data: [
                            
                            <?php $currentDate = new DateTime();
                        $financialYear = ($currentDate->format('m') >= 4) ? $currentDate->format('Y') : ($currentDate->format('Y') - 1);
                        for ($month = 4; $month <= 15; $month++) {
                            $adjustedMonth = ($month <= 12) ? $month : ($month - 12);
                            $year = ($month <= 12) ? $financialYear : ($financialYear + 1);
                            $accont = $this->Menu_model->get_mywisetaskanpn($uid,$month,$year,$acid);?>
                            <?=$accont[0]->cont?>,<?php } ?>],
                        stack: 'Stack 0',
                    },
                    <?php $i++;} ?>
                    
                     <?php $status = $this->Menu_model->get_status(); $i=0; foreach($status as $st){
                        $stid = $st->id; ?>
                    
                    {
                        label: '<?=$st->name?>',
                        backgroundColor: "<?=$colors[$i]?>",
                        data: [
                            <?php $currentDate = new DateTime();
                        $financialYear = ($currentDate->format('m') >= 4) ? $currentDate->format('Y') : ($currentDate->format('Y') - 1);
                        for ($month = 4; $month <= 15; $month++) {
                            $adjustedMonth = ($month <= 12) ? $month : ($month - 12);
                            $year = ($month <= 12) ? $financialYear : ($financialYear + 1);
                            $accont = $this->Menu_model->get_mywisestatusanpn($uid,$month,$year,$stid);?>
                            <?=$accont[0]->cont?>,<?php } ?>
                            
                            ],
                        stack: 'Stack 1'
                    },
                    
                    <?php $i++;} ?>
                ],
            },
            options: {
                
                plugins: {
                    title: {
                        display: true,
                        text: 'Month Wise Type of Task Count And Status Change Count by it Self (ANPN)'
                    },
                    
                
            },
            onClick: function (evt, activeElements) {
                if (activeElements && activeElements.length) {
                    const clickedDatasetIndex = activeElements[0].datasetIndex;

                    let allOthersHidden = true;
                    for (let i = 0; i < myChart1.data.datasets.length; i++) {
                        if (i !== clickedDatasetIndex && !myChart1.getDatasetMeta(i).hidden) {
                            allOthersHidden = false;
                            break;
                        }
                    }

                    if (allOthersHidden) {
                        for (let i = 0; i < myChart1.data.datasets.length; i++) {
                            myChart1.getDatasetMeta(i).hidden = false;
                        }
                    } else {
                        for (let i = 0; i < myChart1.data.datasets.length; i++) {
                            myChart1.getDatasetMeta(i).hidden = (i !== clickedDatasetIndex);
                        }
                    }

                    myChart1.update();
                }
            }
        }
    });
    </script>
    
    
    
    
    <div style="width: 80%; margin: auto;">
        <canvas id="stackedChartID2"></canvas>
    </div>

    <script>
        var myContext = document.getElementById("stackedChartID2").getContext('2d');
        var myChart2 = new Chart(myContext, {
            type: 'bar',
            data: {
                labels: [
                    
                    <?php   $currentDate = new DateTime();
                            $financialYear = ($currentDate->format('m') >= 4) ? $currentDate->format('Y') : ($currentDate->format('Y') - 1);
                            for ($month = 4; $month <= 15; $month++) {
                                $adjustedMonth = ($month <= 12) ? $month : ($month - 12);
                                $year = ($month <= 12) ? $financialYear : ($financialYear + 1);
                                $monthName = DateTime::createFromFormat('!m', $adjustedMonth)->format('F');?>
                    "<?=$monthName?> (<?=$year?>)", <?php } ?>],
                datasets: [
                    
                    <?php 
                    $colors = array('red','blue','green','yellow','purple','orange','pink','brown','cyan','magenta','teal','lime','violet','indigo','gray');

                    $action = $this->Menu_model->get_action(); $i=0; foreach($action as $ac){
                        $acid = $ac->id; ?>
                    {
                        label: '<?=$ac->name?>',
                        backgroundColor: "<?=$colors[$i]?>",
                        data: [
                            
                            <?php $currentDate = new DateTime();
                        $financialYear = ($currentDate->format('m') >= 4) ? $currentDate->format('Y') : ($currentDate->format('Y') - 1);
                        for ($month = 4; $month <= 15; $month++) {
                            $adjustedMonth = ($month <= 12) ? $month : ($month - 12);
                            $year = ($month <= 12) ? $financialYear : ($financialYear + 1);
                            $accont = $this->Menu_model->get_mywisetaskaypn($uid,$month,$year,$acid);?>
                            <?=$accont[0]->cont?>,<?php } ?>],
                        stack: 'Stack 0',
                    },
                    <?php $i++;} ?>
                    
                     <?php $status = $this->Menu_model->get_status(); $i=0; foreach($status as $st){
                        $stid = $st->id; ?>
                    
                    {
                        label: '<?=$st->name?>',
                        backgroundColor: "<?=$colors[$i]?>",
                        data: [
                            <?php $currentDate = new DateTime();
                        $financialYear = ($currentDate->format('m') >= 4) ? $currentDate->format('Y') : ($currentDate->format('Y') - 1);
                        for ($month = 4; $month <= 15; $month++) {
                            $adjustedMonth = ($month <= 12) ? $month : ($month - 12);
                            $year = ($month <= 12) ? $financialYear : ($financialYear + 1);
                            $accont = $this->Menu_model->get_mywisestatusaypn($uid,$month,$year,$stid);?>
                            <?=$accont[0]->cont?>,<?php } ?>
                            
                            ],
                        stack: 'Stack 1'
                    },
                    
                    <?php $i++;} ?>
                ],
            },
            options: {
                
                plugins: {
                    title: {
                        display: true,
                        text: 'Month Wise Type of Task Count And Status Change Count by it Self (AYPN)'
                    },
                    
                
            },
            onClick: function (evt, activeElements) {
                if (activeElements && activeElements.length) {
                    const clickedDatasetIndex = activeElements[0].datasetIndex;

                    let allOthersHidden = true;
                    for (let i = 0; i < myChart2.data.datasets.length; i++) {
                        if (i !== clickedDatasetIndex && !myChart2.getDatasetMeta(i).hidden) {
                            allOthersHidden = false;
                            break;
                        }
                    }

                    if (allOthersHidden) {
                        for (let i = 0; i < myChart2.data.datasets.length; i++) {
                            myChart2.getDatasetMeta(i).hidden = false;
                        }
                    } else {
                        for (let i = 0; i < myChart2.data.datasets.length; i++) {
                            myChart2.getDatasetMeta(i).hidden = (i !== clickedDatasetIndex);
                        }
                    }

                    myChart2.update();
                }
            }
        }
    });
    </script>
    
    
    
    
    <div style="width: 80%; margin: auto;">
        <canvas id="stackedChartID3"></canvas>
    </div>

    <script>
        var myContext = document.getElementById("stackedChartID3").getContext('2d');
        var myChart3 = new Chart(myContext, {
            type: 'bar',
            data: {
                labels: [
                    
                    <?php   $currentDate = new DateTime();
                            $financialYear = ($currentDate->format('m') >= 4) ? $currentDate->format('Y') : ($currentDate->format('Y') - 1);
                            for ($month = 4; $month <= 15; $month++) {
                                $adjustedMonth = ($month <= 12) ? $month : ($month - 12);
                                $year = ($month <= 12) ? $financialYear : ($financialYear + 1);
                                $monthName = DateTime::createFromFormat('!m', $adjustedMonth)->format('F');?>
                    "<?=$monthName?> (<?=$year?>)", <?php } ?>],
                datasets: [
                    
                    <?php 
                    $colors = array('red','blue','green','yellow','purple','orange','pink','brown','cyan','magenta','teal','lime','violet','indigo','gray');

                    $action = $this->Menu_model->get_action(); $i=0; foreach($action as $ac){
                        $acid = $ac->id; ?>
                    {
                        label: '<?=$ac->name?>',
                        backgroundColor: "<?=$colors[$i]?>",
                        data: [
                            
                            <?php $currentDate = new DateTime();
                        $financialYear = ($currentDate->format('m') >= 4) ? $currentDate->format('Y') : ($currentDate->format('Y') - 1);
                        for ($month = 4; $month <= 15; $month++) {
                            $adjustedMonth = ($month <= 12) ? $month : ($month - 12);
                            $year = ($month <= 12) ? $financialYear : ($financialYear + 1);
                            $accont = $this->Menu_model->get_mywisetaskaypy($uid,$month,$year,$acid);?>
                            <?=$accont[0]->cont?>,<?php } ?>],
                        stack: 'Stack 0',
                    },
                    <?php $i++;} ?>
                    
                     <?php $status = $this->Menu_model->get_status(); $i=0; foreach($status as $st){
                        $stid = $st->id; ?>
                    
                    {
                        label: '<?=$st->name?>',
                        backgroundColor: "<?=$colors[$i]?>",
                        data: [
                            <?php $currentDate = new DateTime();
                        $financialYear = ($currentDate->format('m') >= 4) ? $currentDate->format('Y') : ($currentDate->format('Y') - 1);
                        for ($month = 4; $month <= 15; $month++) {
                            $adjustedMonth = ($month <= 12) ? $month : ($month - 12);
                            $year = ($month <= 12) ? $financialYear : ($financialYear + 1);
                            $accont = $this->Menu_model->get_mywisestatusaypy($uid,$month,$year,$stid);?>
                            <?=$accont[0]->cont?>,<?php } ?>
                            
                            ],
                        stack: 'Stack 1'
                    },
                    
                    <?php $i++;} ?>
                ],
            },
            options: {
                
                plugins: {
                    title: {
                        display: true,
                        text: 'Month Wise Type of Task Count And Status Change Count by it Self (AYPY)'
                    },
                    
                
            },
            onClick: function (evt, activeElements) {
                if (activeElements && activeElements.length) {
                    const clickedDatasetIndex = activeElements[0].datasetIndex;

                    let allOthersHidden = true;
                    for (let i = 0; i < myChart3.data.datasets.length; i++) {
                        if (i !== clickedDatasetIndex && !myChart3.getDatasetMeta(i).hidden) {
                            allOthersHidden = false;
                            break;
                        }
                    }

                    if (allOthersHidden) {
                        for (let i = 0; i < myChart3.data.datasets.length; i++) {
                            myChart3.getDatasetMeta(i).hidden = false;
                        }
                    } else {
                        for (let i = 0; i < myChart3.data.datasets.length; i++) {
                            myChart3.getDatasetMeta(i).hidden = (i !== clickedDatasetIndex);
                        }
                    }

                    myChart3.update();
                }
            }
        }
    });
    </script>
    
    
   
   
   
    <div style="width: 80%; margin: auto;">
        <canvas id="stackedChartID4"></canvas>
    </div>

    <script>
        var myContext = document.getElementById("stackedChartID4").getContext('2d');
        var myChart4 = new Chart(myContext, {
            type: 'bar',
            data: {
                labels: [
                   
                    <?php   $currentDate = new DateTime();
                            $financialYear = ($currentDate->format('m') >= 4) ? $currentDate->format('Y') : ($currentDate->format('Y') - 1);
                            for ($month = 4; $month <= 15; $month++) {
                                $adjustedMonth = ($month <= 12) ? $month : ($month - 12);
                                $year = ($month <= 12) ? $financialYear : ($financialYear + 1);
                                $monthName = DateTime::createFromFormat('!m', $adjustedMonth)->format('F');?>
                    "<?=$monthName?> (<?=$year?>)", <?php } ?>],
                datasets: [
                   
                    <?php
                    $colors = array('red','blue','green','yellow','purple','orange','pink','brown','cyan','magenta','teal','lime','violet','indigo','gray');

                    $action = $this->Menu_model->get_action(); $i=0; foreach($action as $ac){
                        $acid = $ac->id; ?>
                    {
                        label: '<?=$ac->name?>',
                        backgroundColor: "<?=$colors[$i]?>",
                        data: [
                           
                        <?php $currentDate = new DateTime();
                        $financialYear = ($currentDate->format('m') >= 4) ? $currentDate->format('Y') : ($currentDate->format('Y') - 1);
                        for ($month = 4; $month <= 15; $month++) {
                            $adjustedMonth = ($month <= 12) ? $month : ($month - 12);
                            $year = ($month <= 12) ? $financialYear : ($financialYear + 1);
                            $accont = $this->Menu_model->get_mywisetaskbyotheranpn($uid,$month,$year,$acid);?>
                            <?=$accont[0]->cont?>,<?php } ?>],
                        stack: 'Stack 0',
                    },
                    <?php $i++;} ?>
                   
                     <?php $status = $this->Menu_model->get_status(); $i=0; foreach($status as $st){
                        $stid = $st->id; ?>
                   
                    {
                        label: '<?=$st->name?>',
                        backgroundColor: "<?=$colors[$i]?>",
                        data: [
                            <?php $currentDate = new DateTime();
                        $financialYear = ($currentDate->format('m') >= 4) ? $currentDate->format('Y') : ($currentDate->format('Y') - 1);
                        for ($month = 4; $month <= 15; $month++) {
                            $adjustedMonth = ($month <= 12) ? $month : ($month - 12);
                            $year = ($month <= 12) ? $financialYear : ($financialYear + 1);
                            $accont = $this->Menu_model->get_mywisestatusbyotheranpn($uid,$month,$year,$stid);?>
                            <?=$accont[0]->cont?>,<?php } ?>
                           
                            ],
                        stack: 'Stack 1'
                    },
                   
                    <?php $i++;} ?>
                ],
            },
            options: {
               
                plugins: {
                    title: {
                        display: true,
                        text: 'Month Wise Type of Task Count And Status Change Count by Other (ANPN)'
                    },
                   
               
            },
            onClick: function (evt, activeElements) {
                if (activeElements && activeElements.length) {
                    const clickedDatasetIndex = activeElements[0].datasetIndex;

                    let allOthersHidden = true;
                    for (let i = 0; i < myChart4.data.datasets.length; i++) {
                        if (i !== clickedDatasetIndex && !myChart4.getDatasetMeta(i).hidden) {
                            allOthersHidden = false;
                            break;
                        }
                    }

                    if (allOthersHidden) {
                        for (let i = 0; i < myChart4.data.datasets.length; i++) {
                            myChart4.getDatasetMeta(i).hidden = false;
                        }
                    } else {
                        for (let i = 0; i < myChart4.data.datasets.length; i++) {
                            myChart4.getDatasetMeta(i).hidden = (i !== clickedDatasetIndex);
                        }
                    }

                    myChart4.update();
                }
            }
        }
    });
    </script> 
    
    
    
    
    
    <div style="width: 80%; margin: auto;">
        <canvas id="stackedChartID5"></canvas>
    </div>

    <script>
        var myContext = document.getElementById("stackedChartID5").getContext('2d');
        var myChart5 = new Chart(myContext, {
            type: 'bar',
            data: {
                labels: [
                   
                    <?php   $currentDate = new DateTime();
                            $financialYear = ($currentDate->format('m') >= 4) ? $currentDate->format('Y') : ($currentDate->format('Y') - 1);
                            for ($month = 4; $month <= 15; $month++) {
                                $adjustedMonth = ($month <= 12) ? $month : ($month - 12);
                                $year = ($month <= 12) ? $financialYear : ($financialYear + 1);
                                $monthName = DateTime::createFromFormat('!m', $adjustedMonth)->format('F');?>
                    "<?=$monthName?> (<?=$year?>)", <?php } ?>],
                datasets: [
                   
                    <?php
                    $colors = array('red','blue','green','yellow','purple','orange','pink','brown','cyan','magenta','teal','lime','violet','indigo','gray');

                    $action = $this->Menu_model->get_action(); $i=0; foreach($action as $ac){
                        $acid = $ac->id; ?>
                    {
                        label: '<?=$ac->name?>',
                        backgroundColor: "<?=$colors[$i]?>",
                        data: [
                           
                        <?php $currentDate = new DateTime();
                        $financialYear = ($currentDate->format('m') >= 4) ? $currentDate->format('Y') : ($currentDate->format('Y') - 1);
                        for ($month = 4; $month <= 15; $month++) {
                            $adjustedMonth = ($month <= 12) ? $month : ($month - 12);
                            $year = ($month <= 12) ? $financialYear : ($financialYear + 1);
                            $accont = $this->Menu_model->get_mywisetaskbyotheraypn($uid,$month,$year,$acid);?>
                            <?=$accont[0]->cont?>,<?php } ?>],
                        stack: 'Stack 0',
                    },
                    <?php $i++;} ?>
                   
                     <?php $status = $this->Menu_model->get_status(); $i=0; foreach($status as $st){
                        $stid = $st->id; ?>
                   
                    {
                        label: '<?=$st->name?>',
                        backgroundColor: "<?=$colors[$i]?>",
                        data: [
                            <?php $currentDate = new DateTime();
                        $financialYear = ($currentDate->format('m') >= 4) ? $currentDate->format('Y') : ($currentDate->format('Y') - 1);
                        for ($month = 4; $month <= 15; $month++) {
                            $adjustedMonth = ($month <= 12) ? $month : ($month - 12);
                            $year = ($month <= 12) ? $financialYear : ($financialYear + 1);
                            $accont = $this->Menu_model->get_mywisestatusbyotheraypn($uid,$month,$year,$stid);?>
                            <?=$accont[0]->cont?>,<?php } ?>
                           
                            ],
                        stack: 'Stack 1'
                    },
                   
                    <?php $i++;} ?>
                ],
            },
            options: {
               
                plugins: {
                    title: {
                        display: true,
                        text: 'Month Wise Type of Task Count And Status Change Count by Other (AYPN)'
                    },
                   
               
            },
            onClick: function (evt, activeElements) {
                if (activeElements && activeElements.length) {
                    const clickedDatasetIndex = activeElements[0].datasetIndex;

                    let allOthersHidden = true;
                    for (let i = 0; i < myChart5.data.datasets.length; i++) {
                        if (i !== clickedDatasetIndex && !myChart5.getDatasetMeta(i).hidden) {
                            allOthersHidden = false;
                            break;
                        }
                    }

                    if (allOthersHidden) {
                        for (let i = 0; i < myChart5.data.datasets.length; i++) {
                            myChart5.getDatasetMeta(i).hidden = false;
                        }
                    } else {
                        for (let i = 0; i < myChart5.data.datasets.length; i++) {
                            myChart5.getDatasetMeta(i).hidden = (i !== clickedDatasetIndex);
                        }
                    }

                    myChart5.update();
                }
            }
        }
    });
    </script> 
    
    
    
    
    <div style="width: 80%; margin: auto;">
        <canvas id="stackedChartID6"></canvas>
    </div>

    <script>
        var myContext = document.getElementById("stackedChartID6").getContext('2d');
        var myChart6 = new Chart(myContext, {
            type: 'bar',
            data: {
                labels: [
                   
                    <?php   $currentDate = new DateTime();
                            $financialYear = ($currentDate->format('m') >= 4) ? $currentDate->format('Y') : ($currentDate->format('Y') - 1);
                            for ($month = 4; $month <= 15; $month++) {
                                $adjustedMonth = ($month <= 12) ? $month : ($month - 12);
                                $year = ($month <= 12) ? $financialYear : ($financialYear + 1);
                                $monthName = DateTime::createFromFormat('!m', $adjustedMonth)->format('F');?>
                    "<?=$monthName?> (<?=$year?>)", <?php } ?>],
                datasets: [
                   
                    <?php
                    $colors = array('red','blue','green','yellow','purple','orange','pink','brown','cyan','magenta','teal','lime','violet','indigo','gray');

                    $action = $this->Menu_model->get_action(); $i=0; foreach($action as $ac){
                        $acid = $ac->id; ?>
                    {
                        label: '<?=$ac->name?>',
                        backgroundColor: "<?=$colors[$i]?>",
                        data: [
                           
                        <?php $currentDate = new DateTime();
                        $financialYear = ($currentDate->format('m') >= 4) ? $currentDate->format('Y') : ($currentDate->format('Y') - 1);
                        for ($month = 4; $month <= 15; $month++) {
                            $adjustedMonth = ($month <= 12) ? $month : ($month - 12);
                            $year = ($month <= 12) ? $financialYear : ($financialYear + 1);
                            $accont = $this->Menu_model->get_mywisetaskbyotheraypy($uid,$month,$year,$acid);?>
                            <?=$accont[0]->cont?>,<?php } ?>],
                        stack: 'Stack 0',
                    },
                    <?php $i++;} ?>
                   
                     <?php $status = $this->Menu_model->get_status(); $i=0; foreach($status as $st){
                        $stid = $st->id; ?>
                   
                    {
                        label: '<?=$st->name?>',
                        backgroundColor: "<?=$colors[$i]?>",
                        data: [
                            <?php $currentDate = new DateTime();
                        $financialYear = ($currentDate->format('m') >= 4) ? $currentDate->format('Y') : ($currentDate->format('Y') - 1);
                        for ($month = 4; $month <= 15; $month++) {
                            $adjustedMonth = ($month <= 12) ? $month : ($month - 12);
                            $year = ($month <= 12) ? $financialYear : ($financialYear + 1);
                            $accont = $this->Menu_model->get_mywisestatusbyotheraypy($uid,$month,$year,$stid);?>
                            <?=$accont[0]->cont?>,<?php } ?>
                           
                            ],
                        stack: 'Stack 1'
                    },
                   
                    <?php $i++;} ?>
                ],
            },
            options: {
               
                plugins: {
                    title: {
                        display: true,
                        text: 'Month Wise Type of Task Count And Status Change Count by Other (AYPY)'
                    },
                   
               
            },
            onClick: function (evt, activeElements) {
                if (activeElements && activeElements.length) {
                    const clickedDatasetIndex = activeElements[0].datasetIndex;

                    let allOthersHidden = true;
                    for (let i = 0; i < myChart6.data.datasets.length; i++) {
                        if (i !== clickedDatasetIndex && !myChart4.getDatasetMeta(i).hidden) {
                            allOthersHidden = false;
                            break;
                        }
                    }

                    if (allOthersHidden) {
                        for (let i = 0; i < myChart6.data.datasets.length; i++) {
                            myChart6.getDatasetMeta(i).hidden = false;
                        }
                    } else {
                        for (let i = 0; i < myChart6.data.datasets.length; i++) {
                            myChart6.getDatasetMeta(i).hidden = (i !== clickedDatasetIndex);
                        }
                    }

                    myChart6.update();
                }
            }
        }
    });
    </script> 
    
    
    
    
    
    
    <div style="width: 80%; margin: auto;">
        <canvas id="stackedChartID7"></canvas>
    </div>

    <script>
        var myContext = document.getElementById("stackedChartID7").getContext('2d');
        var myChart7 = new Chart(myContext, {
            type: 'bar',
            data: {
                labels: [
                    
                    <?php   $currentDate = new DateTime();
                            $financialYear = ($currentDate->format('m') >= 4) ? $currentDate->format('Y') : ($currentDate->format('Y') - 1);
                            for ($month = 4; $month <= 15; $month++) {
                                $adjustedMonth = ($month <= 12) ? $month : ($month - 12);
                                $year = ($month <= 12) ? $financialYear : ($financialYear + 1);
                                $monthName = DateTime::createFromFormat('!m', $adjustedMonth)->format('F');?>
                    "<?=$monthName?> (<?=$year?>)", <?php } ?>],
                
                datasets: [
                    
                    <?php 
                    $colors = array('red','blue','green','yellow','purple','orange','pink','brown','cyan','magenta','teal','lime','violet','indigo','gray');?>

                   
                    {
                        label: 'Not Done',
                        backgroundColor: "red",
                        data: [
                            
                        <?php $currentDate = new DateTime();
                        $financialYear = ($currentDate->format('m') >= 4) ? $currentDate->format('Y') : ($currentDate->format('Y') - 1);
                        for ($month = 4; $month <= 15; $month++) {
                            $adjustedMonth = ($month <= 12) ? $month : ($month - 12);
                            $year = ($month <= 12) ? $financialYear : ($financialYear + 1);
                            $rabys = $this->Menu_model->get_reviewactionbyself($uid,$month,$year);?>
                            <?=$rabys[0]->tndone?>,<?php } ?>],
                        stack: 'Stack 0',
                    },
                    
                    {
                        label: 'Done But Purpose not achived',
                        backgroundColor: "yellow",
                        data: [
                            
                        <?php $currentDate = new DateTime();
                        $financialYear = ($currentDate->format('m') >= 4) ? $currentDate->format('Y') : ($currentDate->format('Y') - 1);
                        for ($month = 4; $month <= 15; $month++) {
                            $adjustedMonth = ($month <= 12) ? $month : ($month - 12);
                            $year = ($month <= 12) ? $financialYear : ($financialYear + 1);
                            $rabys = $this->Menu_model->get_reviewactionbyself($uid,$month,$year);?>
                            <?=$rabys[0]->tdonewn?>,<?php } ?>],
                        stack: 'Stack 0',
                    },
                    
                    {
                        label: 'Done With Purpose Achived',
                        backgroundColor: "green",
                        data: [
                            
                        <?php $currentDate = new DateTime();
                        $financialYear = ($currentDate->format('m') >= 4) ? $currentDate->format('Y') : ($currentDate->format('Y') - 1);
                        for ($month = 4; $month <= 15; $month++) {
                            $adjustedMonth = ($month <= 12) ? $month : ($month - 12);
                            $year = ($month <= 12) ? $financialYear : ($financialYear + 1);
                            $rabys = $this->Menu_model->get_reviewactionbyself($uid,$month,$year);?>
                            <?=$rabys[0]->tdonewy?>,<?php } ?>],
                        stack: 'Stack 0',
                    },
                    
                    <?php $status = $this->Menu_model->get_status(); $i=0; foreach($status as $st){
                        $stid = $st->id; ?>
                    
                    {
                        label: '<?=$st->name?>',
                        backgroundColor: "<?=$colors[$i]?>",
                        data: [
                            <?php $currentDate = new DateTime();
                        $financialYear = ($currentDate->format('m') >= 4) ? $currentDate->format('Y') : ($currentDate->format('Y') - 1);
                        for ($month = 4; $month <= 15; $month++) {
                            $adjustedMonth = ($month <= 12) ? $month : ($month - 12);
                            $year = ($month <= 12) ? $financialYear : ($financialYear + 1);
                            $accont = $this->Menu_model->get_reviewstatusbyself($uid,$month,$year,$stid);?>
                            <?=$accont[0]->cont?>,<?php } ?>
                            
                            ],
                        stack: 'Stack 1'
                    },
                    
                    <?php $i++;} ?>
                    
                    
                    
                    
                ],
            },
            options: {
                
                plugins: {
                    title: {
                        display: true,
                        text: 'Review Vs Task Done and Status Achive (Review by Self)'
                    },
                    
                
            },
            onClick: function (evt, activeElements) {
                if (activeElements && activeElements.length) {
                    const clickedDatasetIndex = activeElements[0].datasetIndex;

                    let allOthersHidden = true;
                    for (let i = 0; i < myChart7.data.datasets.length; i++) {
                        if (i !== clickedDatasetIndex && !myChart7.getDatasetMeta(i).hidden) {
                            allOthersHidden = false;
                            break;
                        }
                    }

                    if (allOthersHidden) {
                        for (let i = 0; i < myChart7.data.datasets.length; i++) {
                            myChart7.getDatasetMeta(i).hidden = false;
                        }
                    } else {
                        for (let i = 0; i < myChart7.data.datasets.length; i++) {
                            myChart7.getDatasetMeta(i).hidden = (i !== clickedDatasetIndex);
                        }
                    }

                    myChart7.update();
                }
            }
        }
    });
    </script>
    
    
    
    
    <div style="width: 80%; margin: auto;">
        <canvas id="stackedChartID8"></canvas>
    </div>

    <script>
        var myContext = document.getElementById("stackedChartID8").getContext('2d');
        var myChart8 = new Chart(myContext, {
            type: 'bar',
            data: {
                labels: [
                    
                    <?php   $currentDate = new DateTime();
                            $financialYear = ($currentDate->format('m') >= 4) ? $currentDate->format('Y') : ($currentDate->format('Y') - 1);
                            for ($month = 4; $month <= 15; $month++) {
                                $adjustedMonth = ($month <= 12) ? $month : ($month - 12);
                                $year = ($month <= 12) ? $financialYear : ($financialYear + 1);
                                $monthName = DateTime::createFromFormat('!m', $adjustedMonth)->format('F');?>
                    "<?=$monthName?> (<?=$year?>)", <?php } ?>],
                
                datasets: [
                    
                    <?php 
                    $colors = array('red','blue','green','yellow','purple','orange','pink','brown','cyan','magenta','teal','lime','violet','indigo','gray');?>

                   
                    {
                        label: 'Not Done',
                        backgroundColor: "red",
                        data: [
                            
                        <?php $currentDate = new DateTime();
                        $financialYear = ($currentDate->format('m') >= 4) ? $currentDate->format('Y') : ($currentDate->format('Y') - 1);
                        for ($month = 4; $month <= 15; $month++) {
                            $adjustedMonth = ($month <= 12) ? $month : ($month - 12);
                            $year = ($month <= 12) ? $financialYear : ($financialYear + 1);
                            $rabys = $this->Menu_model->get_reviewactionbyself($uid,$month,$year);?>
                            <?=$rabys[0]->tndone?>,<?php } ?>],
                        stack: 'Stack 0',
                    },
                    
                    {
                        label: 'Done But Purpose not achived',
                        backgroundColor: "yellow",
                        data: [
                            
                        <?php $currentDate = new DateTime();
                        $financialYear = ($currentDate->format('m') >= 4) ? $currentDate->format('Y') : ($currentDate->format('Y') - 1);
                        for ($month = 4; $month <= 15; $month++) {
                            $adjustedMonth = ($month <= 12) ? $month : ($month - 12);
                            $year = ($month <= 12) ? $financialYear : ($financialYear + 1);
                            $rabys = $this->Menu_model->get_reviewactionbyself($uid,$month,$year);?>
                            <?=$rabys[0]->tdonewn?>,<?php } ?>],
                        stack: 'Stack 0',
                    },
                    
                    {
                        label: 'Done With Purpose Achived',
                        backgroundColor: "green",
                        data: [
                            
                        <?php $currentDate = new DateTime();
                        $financialYear = ($currentDate->format('m') >= 4) ? $currentDate->format('Y') : ($currentDate->format('Y') - 1);
                        for ($month = 4; $month <= 15; $month++) {
                            $adjustedMonth = ($month <= 12) ? $month : ($month - 12);
                            $year = ($month <= 12) ? $financialYear : ($financialYear + 1);
                            $rabys = $this->Menu_model->get_reviewactionbyother($uid,$month,$year);?>
                            <?=$rabys[0]->tdonewy?>,<?php } ?>],
                        stack: 'Stack 0',
                    },
                    
                    
                    <?php $status = $this->Menu_model->get_status(); $i=0; foreach($status as $st){
                        $stid = $st->id; ?>
                    
                    {
                        label: '<?=$st->name?>',
                        backgroundColor: "<?=$colors[$i]?>",
                        data: [
                            <?php $currentDate = new DateTime();
                        $financialYear = ($currentDate->format('m') >= 4) ? $currentDate->format('Y') : ($currentDate->format('Y') - 1);
                        for ($month = 4; $month <= 15; $month++) {
                            $adjustedMonth = ($month <= 12) ? $month : ($month - 12);
                            $year = ($month <= 12) ? $financialYear : ($financialYear + 1);
                            $accont = $this->Menu_model->get_reviewstatusbyother($uid,$month,$year,$stid);?>
                            <?=$accont[0]->cont?>,<?php } ?>
                            
                            ],
                        stack: 'Stack 1'
                    },
                    
                    <?php $i++;} ?>
                    
                    
                    
                    
                ],
            },
            options: {
                
                plugins: {
                    title: {
                        display: true,
                        text: 'Review Vs Task Done and Status Achive (Review by Other)'
                    },
                    
                
            },
            onClick: function (evt, activeElements) {
                if (activeElements && activeElements.length) {
                    const clickedDatasetIndex = activeElements[0].datasetIndex;

                    let allOthersHidden = true;
                    for (let i = 0; i < myChart8.data.datasets.length; i++) {
                        if (i !== clickedDatasetIndex && !myChart3.getDatasetMeta(i).hidden) {
                            allOthersHidden = false;
                            break;
                        }
                    }

                    if (allOthersHidden) {
                        for (let i = 0; i < myChart8.data.datasets.length; i++) {
                            myChart8.getDatasetMeta(i).hidden = false;
                        }
                    } else {
                        for (let i = 0; i < myChart8.data.datasets.length; i++) {
                            myChart8.getDatasetMeta(i).hidden = (i !== clickedDatasetIndex);
                        }
                    }

                    myChart8.update();
                }
            }
        }
    });
    </script>
    
    
    
    
    
    
    
    
</body>
</html>



    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.0.1/dist/chart.umd.min.js"></script>


<div id="chart_div2"  class="col-12"></div>

             
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
google.charts.load("current", {packages:['corechart']});
google.charts.setOnLoadCallback(drawChart25);
function drawChart25() {
  var data = new google.visualization.DataTable();
  data.addColumn('string', 'All Action');
  data.addColumn('number', 'Call');
  data.addColumn({type: 'string', role: 'annotation'});
  data.addColumn('number', 'Email');
  data.addColumn({type: 'string', role: 'annotation'});
  data.addColumn('number', 'Scheduled Meeting');
  data.addColumn({type: 'string', role: 'annotation'});
  data.addColumn('number', 'Barg in Meeting');
  data.addColumn({type: 'string', role: 'annotation'});
  data.addColumn('number', 'Whatsapp Activity');
  data.addColumn({type: 'string', role: 'annotation'});
  data.addColumn('number', 'Write MOM');
  data.addColumn({type: 'string', role: 'annotation'});
  data.addColumn('number', 'Write Proposal');
  data.addColumn({type: 'string', role: 'annotation'});
  data.addColumn('number', 'Research');
  data.addColumn({type: 'string', role: 'annotation'});
  data.addColumn('number', 'Mail Check');
  data.addColumn({type: 'string', role: 'annotation'});
  data.addColumn('number', 'Social Networking');
  data.addColumn({type: 'string', role: 'annotation'});
  data.addColumn('number', 'Social Activity');
  data.addColumn({type: 'string', role: 'annotation'});
  data.addColumn({type: 'string', role: 'annotationText'});

  <?php
  $status = $this->Menu_model->get_status();
  foreach($status as $st){
    $stid = $st->id;
    $swtc = $this->Menu_model->get_statuswisetaskandaction($uid,$stid,$sd,$ed);
    foreach($swtc as $sw){
        $url="https://stemapp.in/Menu";
      ?>
      data.addRow(['<?=$st->name?> ( <?=$sw->cont?>)',
      <?=$sw->a?>, '<?php if($sw->a == 0){echo "";}else{ echo $sw->a;}?>',
      <?=$sw->b?>, '<?php if($sw->b == 0){echo "";}else{ echo $sw->b;}?>',
      <?=$sw->c?>, '<?php if($sw->c == 0){echo "";}else{ echo $sw->c;}?>',
      <?=$sw->d?>, '<?php if($sw->d == 0){echo "";}else{ echo $sw->d;}?>',
      <?=$sw->e?>, '<?php if($sw->e == 0){echo "";}else{ echo $sw->e;}?>',
      <?=$sw->f?>, '<?php if($sw->f == 0){echo "";}else{ echo $sw->f;}?>',
      <?=$sw->g?>, '<?php if($sw->g == 0){echo "";}else{ echo $sw->g;}?>',
      <?=$sw->h?>, '<?php if($sw->h == 0){echo "";}else{ echo $sw->h;}?>',
      <?=$sw->i?>, '<?php if($sw->i == 0){echo "";}else{ echo $sw->i;}?>',
      <?=$sw->j?>, '<?php if($sw->j == 0){echo "";}else{ echo $sw->j;}?>',
      <?=$sw->k?>, '<?php if($sw->k == 0){echo "";}else{ echo $sw->k;}?>','<?=$url?>']);
      <?php
    }
  }
  ?>

  var options_fullStacked = {
    isStacked: 'percent',
    height: 600,
    legend: {position: 'top', maxLines: 3},
    vAxis: {
      minValue: 0,
      ticks: [0, 0.3, 0.6, 0.9, 1]
    },
    annotations: {
      alwaysOutside: false,
      textStyle: {
        fontSize: 10,
      },
    },
  };

  var chart = new google.visualization.ColumnChart(document.getElementById('chart_div2'));
  google.visualization.events.addListener(chart, 'select', function() {
    var selection = chart.getSelection();
    if (selection.length > 0) {
      var rowIndex = selection[0].row;
      var hyperlink = data.getValue(rowIndex, 23);
      if (hyperlink) {
        window.location.href = hyperlink;
      }
    }
  });

  chart.draw(data, options_fullStacked);
}

</script>
 
 
             
         
<html>
  <head>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['sankey']});
      google.charts.setOnLoadCallback(drawChart);
     
      function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'From');
        data.addColumn('string', 'To');
        data.addColumn('number', 'Conversion');
        <?php $sankey1 = $this->Menu_model->get_sankey1($uid,'1');?>
        <?php $sankey2 = $this->Menu_model->get_sankey2($uid);?>
        <?php $sankey3 = $this->Menu_model->get_sankey3($uid);?>
        <?php $sankey4 = $this->Menu_model->get_sankey4($uid);?>
        data.addRows([
            <?php foreach($sankey2 as $san2){?>
                ['LEAD', '<?=$san2->sname?>', <?=$san2->cont?>],
            <?php } ?>
           
            <?php foreach($sankey3 as $san3){
                $name = $san3->sname;
                if($name=='Positive' || $name=='Positive-NAP' || $name=='Very Positive' || $name=='Very Positive-NAP'){$name='Positive';} ?>
                ['Tentative', '<?=$name?>', <?=$san3->cont?>],
            <?php } ?>
           
            <?php foreach($sankey4 as $san4){?>
                ['Positive', '<?=$san4->sname?>', <?=$san4->cont?>],
            <?php } ?>
        ]);

        var options = {
          width: 600,
          height: 400,
        };
        var chart = new google.visualization.Sankey(document.getElementById('sankey_basic'));
        chart.draw(data, options);
      }
    </script>
  </head>
  <body>
    <div id="sankey_basic" style="width: 900px; height: 300px;"></div>
  </body>
</html>



      
          </div>
      </div>
    </section>
   </div>
</div> 
    
  <footer class="main-footer">
    <strong>Copyright &copy; 2021-2022 <a href="<?=base_url();?>">Stemlearning</a>.</strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
      <b>Version</b> 1.0
    </div>
  </footer>
  <aside class="control-sidebar control-sidebar-dark">
  </aside>
</div>
<script src="<?=base_url();?>assets/js/jquery.min.js"></script>
<script src="<?=base_url();?>assets/js/jquery-ui.min.js"></script>
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<script src="<?=base_url();?>assets/js/bootstrap.bundle.min.js"></script>
<script src="<?=base_url();?>assets/js/Chart.min.js"></script>
<script src="<?=base_url();?>assets/js/sparkline.js"></script>
<script src="<?=base_url();?>assets/js/jquery.vmap.min.js"></script>
<script src="<?=base_url();?>assets/js/jquery.vmap.usa.js"></script>
<script src="plugins/jquery-knob/jquery.knob.min.js"></script>
<script src="<?=base_url();?>assets/js/moment.min.js"></script>
<script src="<?=base_url();?>assets/js/daterangepicker.js"></script>
<script src="<?=base_url();?>assets/js/tempusdominus-bootstrap-4.min.js"></script>
<script src="<?=base_url();?>assets/js/summernote-bs4.min.js"></script>
<script src="<?=base_url();?>assets/js/jquery.overlayScrollbars.min.js"></script>
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
<script src="<?=base_url();?>assets/js/adminlte.js"></script>
<script src="<?=base_url();?>assets/js/dashboard.js"></script>

<script>
    $("#example1").DataTable({
      "responsive": false, "lengthChange": false, "autoWi$dth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appen$dto('#example1_wrapper .col-md-6:eq(0)');
</script>
</body>
</html>
