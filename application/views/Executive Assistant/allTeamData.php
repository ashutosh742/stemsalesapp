<?php include('header.php');?>
<form class="p-3" method="POST" action="<?php echo $type;?>">
<input type="date" name="sdate" class="mr-2" value="<?=$sd?>">
<input type="date" name="edate" class="mr-2" value="<?=$ed?>">
<button type="submit" class="bg-primary text-light">Filter</button>
</form>
<!-- Main content -->
<section class="content">
<div class="container-fluid">
<div class="row">
<div class="col-12">
<div class="card">
<div class="card-header">
</div>
<!-- /.card-header -->
<div class="card-body">
<div class="container-fluid body-content">
<div class="page-header">
<fieldset>

<div class="table-responsive">
<div class="table-responsive">
<div class="pdf-viwer">
<table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
<thead>
<tr>
    <th>Sr.No.</th>
    <th>User Name</th>
    <?php if($type == 'all'){ ?>
        <th>User Role</th>
        <?php } ?>
    <th>Total Assigned Companies</th>
    <!-- <th>Total Tasks per Companies<th> -->
    <!-- <th>Total Conversions per User<th> -->
</tr>
</thead>
    <tbody>
    <?php  $i=1; 
    //   dd($totalTeamdata);exit;
    if($type =="3"){
        $ahreflinkcmp              = '../BDAssignedCmpList/';
        $ahreflinktask             = '../BDAssignedTaskList/';
        $ahreflinkconversion       = '../BDConversionsList/';
    }
    else if($type =="4"){
        $ahreflinkcmp              = '../PSTAssignedCmpList/';
        $ahreflinktask             = '../PSTAssignedTaskList/';
        $ahreflinkconversion       = '../PSTConversionsList/';
    }
    else if($type =="13"){
        $ahreflinkcmp              = '../CMAssignedCmpList/';
        $ahreflinktask             = '../CMAssignedTaskList/';
        $ahreflinkconversion       = '../CMConversionsList/';
    }
    else if($type =='9'){
        $ahreflinkcmp              = '../BDPSTAssignedCmpList/';
        $ahreflinktask             = '../BDPSTAssignedTaskList/';
        $ahreflinkconversion       = '../BDPSTConversionsList/';
    }
    
if(isset($totalTeamdata)){
    //  dd($totalTeamdata);exit;
    foreach($totalTeamdata as $key=>$val) 
    { 
        $uid = $val['uid'];
        // dd($val);exit;
        // $totallistlink = ;
       ?> 
        <tr>
        <td><?=$i?></td>
        <td><?php echo $val['name'];?></td>
        <?php if($type=='all'){
            ?>
            <td><?php echo $val['user_role'];?></td>
            <?php
           
            if($val['type_id'] == '3'){
                $ahreflinkcmp              = '../BDAssignedCmpList/';
                $ahreflinktask             = '../BDAssignedTaskList/';
                $ahreflinkconversion       = '../BDConversionsList/';
            }
            else if($val['type_id']=='4'){
                $ahreflinkcmp              = '../PSTAssignedCmpList/';
                $ahreflinktask             = '../PSTAssignedTaskList/';
                $ahreflinkconversion       = '../PSTConversionsList/';
            }
            else if($val['type_id']== '13'){
                $ahreflinkcmp              = '../CMAssignedCmpList/';
                $ahreflinktask             = '../CMAssignedTaskList/';
                $ahreflinkconversion       = '../CMConversionsList/';
            }
            else if($val['type_id'] =='9'){
                $ahreflinkcmp              = '../BDPSTAssignedCmpList/';
                $ahreflinktask             = '../BDPSTAssignedTaskList/';
                $ahreflinkconversion       = '../BDPSTConversionsList/';
            }
        }
            ?>
      
        <td><a target="_blank" href='<?php echo $ahreflinkcmp;?>/<?php echo $uid;?>/<?php echo $sd;?>/<?php echo $ed;?>'><?php echo $val['totalcompanies'];?></a></td>
        <td></td>
        <!-- <td><a target="_blank" href='<?php //echo $ahreflinktask;?>/<?php //echo $uid;?>'><?php // echo $val['totaltasks'];?></a></td> -->
        <!-- <td><a target="_blank" href='<?php // echo $ahreflinkconversion;?>/<?php // echo $uid;?>'><?php // echo $val['totalconversions'];?></a></td> -->
        <tr>
    <?php
    $i++;
    }
}
   
    ?>
    </tbody>
</table>
 
    
                  </div>
            </div>
</form>            <!--END OF FORM ^^-->
  </fieldset>

</div>
</div></div></div></div>
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

<?php include('footer.php');?>