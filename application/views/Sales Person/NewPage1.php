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
                    
                     <?php $partner = $this->Menu_model->get_partner(); $i=0; foreach($partner as $pt){
                        $ptid = $pt->id; ?>
                    
                    {
                        label: '<?=$pt->name?>',
                        backgroundColor: "<?=$colors[$i]?>",
                        data: [
                            <?php $currentDate = new DateTime();
                        $financialYear = ($currentDate->format('m') >= 4) ? $currentDate->format('Y') : ($currentDate->format('Y') - 1);
                        for ($month = 4; $month <= 15; $month++) {
                            $adjustedMonth = ($month <= 12) ? $month : ($month - 12);
                            $year = ($month <= 12) ? $financialYear : ($financialYear + 1);
                            $accont = $this->Menu_model->get_mywisepartneranpn($uid,$month,$year,$ptid);?>
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
                        text: 'Month Wise Type of Task Count And Partner Type Count by it Self (ANPN)'
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
                    
                     <?php $partner = $this->Menu_model->get_partner(); $i=0; foreach($partner as $pt){
                        $ptid = $pt->id; ?>
                    
                    {
                        label: '<?=$pt->name?>',
                        backgroundColor: "<?=$colors[$i]?>",
                        data: [
                            <?php $currentDate = new DateTime();
                        $financialYear = ($currentDate->format('m') >= 4) ? $currentDate->format('Y') : ($currentDate->format('Y') - 1);
                        for ($month = 4; $month <= 15; $month++) {
                            $adjustedMonth = ($month <= 12) ? $month : ($month - 12);
                            $year = ($month <= 12) ? $financialYear : ($financialYear + 1);
                            $accont = $this->Menu_model->get_mywisepartneraypn($uid,$month,$year,$ptid);?>
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
                        text: 'Month Wise Type of Task Count And Partner Type Count by it Self (AYPN)'
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
                    
                     <?php $partner = $this->Menu_model->get_partner(); $i=0; foreach($partner as $pt){
                        $ptid = $pt->id; ?>
                    
                    {
                        label: '<?=$pt->name?>',
                        backgroundColor: "<?=$colors[$i]?>",
                        data: [
                            <?php $currentDate = new DateTime();
                        $financialYear = ($currentDate->format('m') >= 4) ? $currentDate->format('Y') : ($currentDate->format('Y') - 1);
                        for ($month = 4; $month <= 15; $month++) {
                            $adjustedMonth = ($month <= 12) ? $month : ($month - 12);
                            $year = ($month <= 12) ? $financialYear : ($financialYear + 1);
                            $accont = $this->Menu_model->get_mywisepartneraypy($uid,$month,$year,$ptid);?>
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
                        text: 'Month Wise Type of Task Count And Partner Type Count by it Self (AYPY)'
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
                    
                     <?php $partner = $this->Menu_model->get_partner(); $i=0; foreach($partner as $pt){
                        $ptid = $pt->id; ?>
                    
                    {
                        label: '<?=$pt->name?>',
                        backgroundColor: "<?=$colors[$i]?>",
                        data: [
                            <?php $currentDate = new DateTime();
                        $financialYear = ($currentDate->format('m') >= 4) ? $currentDate->format('Y') : ($currentDate->format('Y') - 1);
                        for ($month = 4; $month <= 15; $month++) {
                            $adjustedMonth = ($month <= 12) ? $month : ($month - 12);
                            $year = ($month <= 12) ? $financialYear : ($financialYear + 1);
                            $accont = $this->Menu_model->get_mywisepartneranpn($uid,$month,$year,$ptid);?>
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
                        text: 'Month Wise Type of Task Count And Partner Type Count by Other (ANPN)'
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
                    
                     <?php $partner = $this->Menu_model->get_partner(); $i=0; foreach($partner as $pt){
                        $ptid = $pt->id; ?>
                    
                    {
                        label: '<?=$pt->name?>',
                        backgroundColor: "<?=$colors[$i]?>",
                        data: [
                            <?php $currentDate = new DateTime();
                        $financialYear = ($currentDate->format('m') >= 4) ? $currentDate->format('Y') : ($currentDate->format('Y') - 1);
                        for ($month = 4; $month <= 15; $month++) {
                            $adjustedMonth = ($month <= 12) ? $month : ($month - 12);
                            $year = ($month <= 12) ? $financialYear : ($financialYear + 1);
                            $accont = $this->Menu_model->get_mywisepartneranpn($uid,$month,$year,$ptid);?>
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
                        text: 'Month Wise Type of Task Count And Partner Type Count by Other (AYPN)'
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
                    
                     <?php $partner = $this->Menu_model->get_partner(); $i=0; foreach($partner as $pt){
                        $ptid = $pt->id; ?>
                    
                    {
                        label: '<?=$pt->name?>',
                        backgroundColor: "<?=$colors[$i]?>",
                        data: [
                            <?php $currentDate = new DateTime();
                        $financialYear = ($currentDate->format('m') >= 4) ? $currentDate->format('Y') : ($currentDate->format('Y') - 1);
                        for ($month = 4; $month <= 15; $month++) {
                            $adjustedMonth = ($month <= 12) ? $month : ($month - 12);
                            $year = ($month <= 12) ? $financialYear : ($financialYear + 1);
                            $accont = $this->Menu_model->get_mywisepartneranpn($uid,$month,$year,$ptid);?>
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
                        text: 'Month Wise Type of Task Count And Partner Type Count by Other (AYPY)'
                    },
                    
                
            },
            onClick: function (evt, activeElements) {
                if (activeElements && activeElements.length) {
                    const clickedDatasetIndex = activeElements[0].datasetIndex;

                    let allOthersHidden = true;
                    for (let i = 0; i < myChart6.data.datasets.length; i++) {
                        if (i !== clickedDatasetIndex && !myChart6.getDatasetMeta(i).hidden) {
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
        var sampleData = [
            ['LEAD', 'Sample Stage 1', 5],
            ['LEAD', 'Sample Stage 2', 7],
            ['Tentative', 'Positive', 3],
            ['Positive', 'Conversion', 2]
        ];
       
        data.addRows(sampleData);
        /*
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
        */
       
        var options = {
          width: 600,
          height: 400,
          sankey: {
            node: {
              colors: [
                '#a6cee3',
                '#b2df8a',
                '#33a02c',
                '#fb9a99',
                '#e31a1c',
                '#fdbf6f',
                '#ff7f00',
              ]
            },
            link: {
              colorMode: 'gradient',
              colors: [
                '#e41a1c',
                '#377eb8',
                '#4daf4a',
                '#984ea3',
                '#ff7f00',
                '#ffff33',
                '#a65628',
              ]
            }
          }
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
