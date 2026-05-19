           <style>
            .card-height-control{
                min-height: 300px;
            }
            .hoverthumb :hover{
                cursor: pointer;
            }
            span.hoverthumbData :hover{
                cursor: pointer;
            }
            .hoverthumb, .hoverthumbData, .hoverthumbDataCatrgory{
                font-weight: 500;
            }
         
        #map {
            height: 400px;
            width: 100%;
        }
        .custom-btn {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .custom-btn:hover {
            background-color: #45a049;
        }
        #address-input-container {
            margin-bottom: 10px;
        }
    </style>
           
           <div class="card shadow-sm rounded-4 mb-4" style="background: linear-gradient(to right, #e8f5e9, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px; border: 1px solid navy;">
            <div class="card-header text-center" style="background: linear-gradient(to right, #f6e7f3, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                <h5 class="mb-0">⚙️ Master Control Setting</h5>
                <small class="d-block">Master Control Setting ⚙️ is a centralized system that empowers administrators to manage key settings, permissions, and functionalities across an entire platform. It acts as the command center 🧠 for controlling operations, enforcing policies 📜, and ensuring consistency throughout the system. From user access 🔐 to feature toggles 🎚️ and system preferences 🛠️, everything can be monitored and adjusted in one place for smooth, secure, and efficient performance 🚀.</small>
            </div>
            <hr>
            <div class="card-body" style="background: linear-gradient(to right, #fce4ec, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px; border: 2px solid green;">
                <div class="row" style="background: azure; padding: 25px; border-radius: 20px;">
                <div class="col-md-3">
                    <div class="card-height-control" style="background: linear-gradient(to right, #dfe9f3, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;border: 2px solid green;">
                    <div class="text-center">
                        <h4 class="mb-0">📊 Change Setting</h4>
                    </div>
                    <hr>
                     <div style="background: linear-gradient(to right, #e0f7fa, #ffffff); border-radius: 12px; padding: 5px;box-shadow: rgba(9, 30, 66, 0.25) 0px 1px 1px, rgba(9, 30, 66, 0.13) 0px 0px 1px 1px; margin: 5px;">
                       <div class="p-1">
                        <span class="hoverthumb" data-toggle="modal" data-target="#currentStatusModalCenter"><strong> ✅ Current Status</strong></span>
                        </div>
                    </div>

                    <hr>
                     <div style="background: linear-gradient(to right, #e0f7fa, #ffffff); border-radius: 12px; padding: 5px;box-shadow: rgba(9, 30, 66, 0.25) 0px 1px 1px, rgba(9, 30, 66, 0.13) 0px 0px 1px 1px; margin: 5px;">
                       <div class="p-1">
                            <span class="hoverthumb" data-toggle="modal" data-target="#MarkedNeedToBeMonitored">
                                <strong>✔️ Marked Need To Be Monitored</strong>
                            </span>
                        </div>
                    </div>
                    
                     <div style="background: linear-gradient(to right, #e0f7fa, #ffffff); border-radius: 12px; padding: 5px; box-shadow: rgba(9, 30, 66, 0.25) 0px 1px 1px, rgba(9, 30, 66, 0.13) 0px 0px 1px 1px; margin: 5px;">
                        <div class="p-1">
                            <span class="hoverthumb" data-toggle="modal" data-target="#currentTravelClusterModalCenter">
                                <strong>✈️ Travel Cluster</strong>
                            </span>
                        </div>
                    </div>

                    <div style="background: linear-gradient(to right, #e0f7fa, #ffffff); border-radius: 12px; padding: 5px; box-shadow: rgba(9, 30, 66, 0.25) 0px 1px 1px, rgba(9, 30, 66, 0.13) 0px 0px 1px 1px; margin: 5px;">
                        <div class="p-1">
                            <span class="hoverthumb" data-toggle="modal" data-target="#changeStateDistrictCityAddress">
                                <strong>🗺️ State | District | City | Address</strong>
                            </span>
                        </div>
                    </div>

                   

                    
                    <hr>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card-height-control" style="background: linear-gradient(to right, #e0f7fa, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;border: 2px solid green;">
                    <div class="text-center">
                        <h4 class="mb-0">👥 Members Setting</h4>
                    </div>
                    <hr>
                    <!-- <p class="hoverthumb" data-toggle="modal" data-target="#assignUserModalCenter"><strong>🧑&zwj;💼 Change User</strong></p> -->
                     <div class="row">
                       
                                <!-- <div class="col-md-6">
                                    <div style="background: linear-gradient(to right, #e0f7fa, #ffffff); border-radius: 12px; padding: 5px;">
                                        <div class="p-1">
                                           🤵‍♂️ <span  class="hoverthumbData" data-toggle="modal" data-target="#assignUserModalCenter">Change Main BD </span>
                                        </div>
                                    </div>
                                </div> -->
                                <div class="col-md-6">
                                    <div style="background: linear-gradient(to right, #e0f7fa, #ffffff); border-radius: 12px; padding: 5px;box-shadow: rgba(9, 30, 66, 0.25) 0px 1px 1px, rgba(9, 30, 66, 0.13) 0px 0px 1px 1px; margin: 5px;">
                                        <div class="p-1">
                                           🤵‍♂️ <span class="hoverthumbData" data-toggle="modal" data-target="#assignUserModalCenter">Change Assistant Cluster Manager </span>
                                        </div>
                                    </div>
                                </div>
                                 <div class="col-md-6">
                                    <div style="background: linear-gradient(to right, #e0f7fa, #ffffff); border-radius: 12px; padding: 5px;box-shadow: rgba(9, 30, 66, 0.25) 0px 1px 1px, rgba(9, 30, 66, 0.13) 0px 0px 1px 1px; margin: 5px;">
                                        <div class="p-1">
                                           🤵‍♂️ <span class="hoverthumbData" data-toggle="modal" data-target="#assignUserModalCenter">Change Cluster Manager</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div style="background: linear-gradient(to right, #e0f7fa, #ffffff); border-radius: 12px; padding: 5px;box-shadow: rgba(9, 30, 66, 0.25) 0px 1px 1px, rgba(9, 30, 66, 0.13) 0px 0px 1px 1px; margin: 5px;">
                                        <div class="p-1">
                                           🤵‍♂️ <span class="hoverthumbData" data-toggle="modal" data-target="#assignUserModalCenter">Change PST </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div style="background: linear-gradient(to right, #e0f7fa, #ffffff); border-radius: 12px; padding: 5px;box-shadow: rgba(9, 30, 66, 0.25) 0px 1px 1px, rgba(9, 30, 66, 0.13) 0px 0px 1px 1px; margin: 5px;">
                                        <div class="p-1">
                                           🤵‍♂️ <span class="hoverthumbData" data-toggle="modal" data-target="#assignUserModalCenter">Change RM East</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div style="background: linear-gradient(to right, #e0f7fa, #ffffff); border-radius: 12px; padding: 5px;box-shadow: rgba(9, 30, 66, 0.25) 0px 1px 1px, rgba(9, 30, 66, 0.13) 0px 0px 1px 1px; margin: 5px;">
                                        <div class="p-1">
                                          🤵‍♂️  <span class="hoverthumbData" data-toggle="modal" data-target="#assignUserModalCenter">Change RM North </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div style="background: linear-gradient(to right, #e0f7fa, #ffffff); border-radius: 12px; padding: 5px;box-shadow: rgba(9, 30, 66, 0.25) 0px 1px 1px, rgba(9, 30, 66, 0.13) 0px 0px 1px 1px; margin: 5px;">
                                        <div class="p-1">
                                          🤵‍♂️  <span class="hoverthumbData" data-toggle="modal" data-target="#assignUserModalCenter">Assistant Sales Head (NAE)</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div style="background: linear-gradient(to right, #e0f7fa, #ffffff); border-radius: 12px; padding: 5px;box-shadow: rgba(9, 30, 66, 0.25) 0px 1px 1px, rgba(9, 30, 66, 0.13) 0px 0px 1px 1px; margin: 5px;">
                                        <div class="p-1">
                                           🤵‍♂️ <span class="hoverthumbData" data-toggle="modal" data-target="#assignUserModalCenter">Assistant Sales Head (W)</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div style="background: linear-gradient(to right, #e0f7fa, #ffffff); border-radius: 12px; padding: 5px;box-shadow: rgba(9, 30, 66, 0.25) 0px 1px 1px, rgba(9, 30, 66, 0.13) 0px 0px 1px 1px; margin: 5px;">
                                        <div class="p-1">
                                           🤵‍♂️ <span class="hoverthumbData" data-toggle="modal" data-target="#assignUserModalCenter">Assistant Sales Head (S)</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div style="background: linear-gradient(to right, #e0f7fa, #ffffff); border-radius: 12px; padding: 5px;box-shadow: rgba(9, 30, 66, 0.25) 0px 1px 1px, rgba(9, 30, 66, 0.13) 0px 0px 1px 1px; margin: 5px;">
                                        <div class="p-1">
                                           🤵‍♂️ <span class="hoverthumbData" data-toggle="modal" data-target="#assignUserModalCenter">Assign Support</span>
                                        </div>
                                    </div>
                                </div>
                        </div>
                    <hr>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card-height-control" style="background: linear-gradient(to right, #fce4ec, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;border: 2px solid green;">
                    <div class="text-center">
                        <h4 class="mb-0">🏷️ Category Setting</h4>
                    </div>
                    <hr>
                     <div style="background: linear-gradient(to right, #e0f7fa, #ffffff); border-radius: 12px; padding: 5px;box-shadow: rgba(9, 30, 66, 0.25) 0px 1px 1px, rgba(9, 30, 66, 0.13) 0px 0px 1px 1px; margin: 5px;">
                       <div class="p-1">
                        💎 <span class="hoverthumbDataCatrgory" data-toggle="modal" data-target="#categoryModalCenter">Old Category</span>
                        </div>
                    </div>
                     <div style="background: linear-gradient(to right, #e0f7fa, #ffffff); border-radius: 12px; padding: 5px;box-shadow: rgba(9, 30, 66, 0.25) 0px 1px 1px, rgba(9, 30, 66, 0.13) 0px 0px 1px 1px; margin: 5px;">
                       <div class="p-1">
                        💎 <span class="hoverthumbDataCatrgory" data-toggle="modal" data-target="#categoryModalCenter">Financial Year Category</span>
                        </div>
                    </div>

                     <div style="background: linear-gradient(to right, #e0f7fa, #ffffff); border-radius: 12px; padding: 5px;box-shadow: rgba(9, 30, 66, 0.25) 0px 1px 1px, rgba(9, 30, 66, 0.13) 0px 0px 1px 1px; margin: 5px;">
                       <div class="p-1">
                        💎 <span class="hoverthumbDataCatrgory" data-toggle="modal" data-target="#categoryModalCenter">Current Quarter Category</span>
                        </div>
                    </div>

                     <div style="background: linear-gradient(to right, #e0f7fa, #ffffff); border-radius: 12px; padding: 5px;box-shadow: rgba(9, 30, 66, 0.25) 0px 1px 1px, rgba(9, 30, 66, 0.13) 0px 0px 1px 1px; margin: 5px;">
                       <div class="p-1">
                        💎 <span class="hoverthumbDataCatrgory" data-toggle="modal" data-target="#categoryModalCenter">Marked Anchor Client </span>
                        </div>
                    </div>
                 
                    <hr>
                    </div>
                </div>
            
                </div>
            </div>
            </div>



                <!-- Modal -->
                <div class="modal fade" id="currentTravelClusterModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                    
                    <div class="col-lg-12" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                        <center><h5>✈️ Change Travel Cluster </h5></center><hr>
                        <form action="<?=base_url()?>Menu/ChangeCompanyBDTravelCluster" method="post">
                        <input type="hidden" value="<?=$cid?>" name="company_id">

                        <?php if($cluster_id !==''){ ?> 
                          <hr>
                      <p><strong>🆔 Cluster ID:</strong> 
                        <?php if(!empty($cluster_id)){ ?> 
                        <a target="_BLANK" style="color:hsl(174, 95%, 32%)!important" href="<?=base_url()?>Menu/TravelClusterDetailsByID/<?=$cluster_id?>">
                        <?=$cluster_id;?>
                        </a>
                        <?php }else{
                          echo "N/A";
                          } ?>
                      </p>
                      <p><strong>📌 Cluster Name:</strong> 
                        <?php if(!empty($clustername)){ ?> 
                        <a target="_BLANK" style="color:hsl(174, 95%, 32%)!important" href="<?=base_url()?>Menu/TravelClusterDetailsByID/<?=$cluster_id?>">
                        <?=$clustername;?>
                        </a>
                        <?php }else{
                          echo "N/A";
                          } ?>
                      </p>
                      <p><strong>🚗 Travel Type:</strong> <?= !empty($travelType) ? $travelType : 'N/A' ?></p>
                      <p><strong>👤 Travel Cluster Created By:</strong> <?= !empty($travel_cluster_create_name) ? $travel_cluster_create_name : 'N/A' ?></p>
                      <hr>
                    <?php }else{echo "<b>urrent Travel Cluster</b> : NA <hr>";} 
                    
                     $clusterDatas   = $this->Menu_model->getBDClusterByUserId($user['user_id']);
                    
                    ?>
                        <lable>Select Travel Cluster</lable>
                        <select id="changeTravelClusterIDs" name="changeTravelClusterIDs" class="form-control" required>
                            <option value="">--Change Travele Cluster--</option>
                            <?php foreach($clusterDatas as $clusterData){ ?> 
                            <option value="<?=$clusterData->id?>"><?=$clusterData->clustername?></option>
                            <?php } ?>
                        </select>

                        <center>
                            <div id="travelclusterLink">

                            </div>
                            <button type="submit" class="custom-btn btn-11 mt-3">Submit</button>
                        </center>
                        </form>
                        </div>
                    </div>
                    </div>
                </div>
                </div>




                <!-- Modal -->
                <div class="modal fade" id="currentStatusModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                    
                    <div class="col-lg-12" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                        <center><h5>Change Current Status</h5></center><hr>
                        <form action="<?=base_url()?>Menu/ChangeCompanyStatus" method="post">
                        <input type="hidden" value="<?=$cid?>" name="company_id">
                        <span> <mark>Current Status : <?=$current_status;?></mark> </span> <hr>
                            <?php $getAllStatuss = $this->Menu_model->GetAllCompanyStatus(); ?>
                        <lable>Select Status</lable>
                        <select id="changestatus" name="changestatus" class="form-control" required>
                            <option value="">--Change Status--</option>
                            <?php foreach($getAllStatuss as $getAllStatus){ ?> 
                            <option value="<?=$getAllStatus->id?>"><?=$getAllStatus->name?></option>
                            <?php } ?>
                        </select>
                        <center>
                            <button type="submit" class="custom-btn btn-11 mt-3">Submit</button>
                        </center>
                        </form>
                        </div>
                    </div>
                    </div>
                </div>
                </div>






                    <!-- Modal -->
                <div class="modal fade" id="MarkedNeedToBeMonitored" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                    
                    <div class="col-lg-12" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                        <center><h5>✔️ Company Marked as Need to Be Monitored</h5></center><hr>
                        <form action="<?=base_url()?>Menu/UpdateMarkedNeedToBeMonitored" method="post">
                        <input type="hidden" value="<?=$cid?>" name="company_id">
                        <span> <mark>✔️ Company Is Already Marked as Need to Be Monitored : <?=$need_to_be_monitored;?></mark> </span> <hr>
                            <?php $getAllStatuss = $this->Menu_model->GetAllCompanyStatus(); ?>
                        <lable>✔️ Company Marked as Need to Be Monitored</lable>
                        <select id="need_to_be_monitored" name="need_to_be_monitored" class="form-control" required>
                            <option value="">-- Select Monitoring Status --</option>
                            <option value="1">✔ Need to Be Monitored</option>
                            <option value="0">✖ Remove Monitoring</option>
                        </select>

                        <center>
                            <button type="submit" class="custom-btn btn-11 mt-3">Submit</button>
                        </center>
                        </form>
                        </div>
                    </div>
                    </div>
                </div>
                </div>

              


                <!-- Modal -->
                <div class="modal fade" id="changeStateDistrictCityAddress" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                    
                    <div class="col-lg-12" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                        <center><h5>Change State | District | City | Address</h5></center><hr>
                        <form action="<?=base_url()?>Menu/ChangeCompanyStateDistrictCity" method="post">
                        <input type="hidden" value="<?=$cid?>" name="company_id">

                        <div class="card" style="background: linear-gradient(to right, #ede7f6, #ffffff);">
                          <p><strong>🏙️ State:</strong> <?= !empty($company_state) ? $company_state : 'N/A' ?></p>
                          <p><strong>🏙️ District:</strong> <?= !empty($company_district) ? $company_district : 'N/A' ?></p>
                          <p><strong>🌆 City:</strong> <?= !empty($company_city) ? $company_city : 'N/A' ?></p>
                          <p><strong>📍 Address:</strong> <?= !empty($company_address) ? $company_address : 'N/A' ?></p>
                        </div>

                        <?php $getStatesDatas =  $this->Menu_model->GetState(); ?>
                        <div class="col-12 col-md-12 mb-12">
                            <label>State</label>
                            <select name="state" id="id_state" required class="form-control">
                                <option value="" selected> -- Select --</option>
                                <?php foreach($getStatesDatas as $getStatesData): ?>
                                    <option value="<?=$getStatesData->state_id;?>"> <?=$getStatesData->state_title;?> </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Please provide a valid state.</div>
                            <div class="valid-feedback">Looks good!</div>
                        </div>
                        <hr>
                        <div class="col-12 col-md-12 mb-12">
                            <label>District</label>
                            <select name="id_district" id="id_district" required class="form-control">
                            </select>
                            <div class="invalid-feedback">Please provide a valid state.</div>
                            <div class="valid-feedback">Looks good!</div>
                        </div>
                        <hr>
                        <div class="col-12 col-md-12 mb-12">
                            <label>City</label>
                            <select name="id_city" id="id_city" required class="form-control">
                            </select>
                            <div class="invalid-feedback">Please provide a valid state.</div>
                            <div class="valid-feedback">Looks good!</div>
                        </div>
                         <hr>           
                        <div class="col-12 col-md-12 mb-12">
                            <label for="exampleFormControlTextarea1" required class="form-label">Address</label>
                            <textarea class="form-control" name="address" id="exampleFormControlTextarea1" rows="3"><?=$company_address?></textarea>
                        </div>
                        <hr>            



                        <center>
                            <button type="submit" class="custom-btn btn-11 mt-3">Submit</button>
                        </center>
                        </form>
                        </div>
                    </div>
                    </div>
                </div>
                </div>



                <div class="modal fade" id="assignUserModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                    
                    <div class="col-lg-12" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                        <center><h5 id="titleMessage">Change User</h5></center><hr>
                        <form action="<?=base_url()?>Menu/ChangeCompanyChangeUser" method="post">

                        <input type="hidden" value="<?=$cid?>" name="company_id">

                        <input type="hidden" value="" id="assign_user_message" name="assign_user_message">

                       <div id="assign_user_card">

                       </div>
                        <center>
                            <button type="submit" class="custom-btn btn-11 mt-3">Submit</button>
                        </center>
                        </form>
                        </div>
                    </div>
                    </div>
                </div>
                </div>


                <div class="modal fade" id="categoryModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document" >
                    <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                    
                    <div class="col-lg-12" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                        <center><h5 id="titleMessage1">Change Category</h5></center><hr>
                        <form action="<?=base_url()?>Menu/ChangeCompanyCategoryByCID" method="post">

                        <input type="hidden" value="<?=$cid?>" name="company_id">

                        <input type="hidden" value="" id="assign_category_message" name="assign_category_message">
                        
                        
                        <div id="category_assign_user">

                        </div>
                       
                        <center>
                            <button type="submit" class="custom-btn btn-11 mt-3">Submit</button>
                        </center>
                        </form>
                        </div>
                    </div>
                    </div>
                </div>
                </div>

            <script src="https://code.jquery.com/jquery-3.7.1.js" ></script>
                <script>

                    $(document).ready(function() {
                        $("#travelclusterLink").hide();

                            $('#changeTravelClusterIDs').on('change', function() {
                                var changeTravelClusterID = $(this).val();
                                var baseURL ='<?=base_url()?>' +'Menu/TravelClusterDetailsByID/';

                                if (changeTravelClusterID !== '') {
                                    $("#travelclusterLink").show();
                                // Build the full URL
                                var fullURL = baseURL + changeTravelClusterID;

                                // Create or update the link
                                var linkHTML = '<a id="travelLink" href="' + fullURL + '" target="_blank" style="display:inline-block;">🔗 View Cluster Details</a>';

                                // Inject the link inside the div
                                $('#travelclusterLink').html(linkHTML);
                                } else {
                                // Clear the link if no selection
                                $('#travelclusterLink').html('');
                                }
                            });


                            $('#id_state').on('change', function() {
                                    var stid = $(this).val(); // Correct: wrap 'this' in $()
                                    $("#id_district").html('<option value="">Select District</option>');
                                    $("#id_city").html('<option value="">Select City</option>');

                                    $.ajax({
                                        url: '<?=base_url();?>Menu/GetDistrictByStateID',
                                        type: "POST",
                                        data: {
                                            stid: stid
                                        },
                                        cache: false,
                                        success: function(result) {
                                            $("#id_district").html(result);
                                        }
                                    });
                                });
                            $('#id_district').on('change', function() {
                                    var districtid = $(this).val(); // Correct: wrap 'this' in $()
                                     $("#id_city").html('<option value="">Select City</option>');
                                    $.ajax({
                                        url: '<?=base_url();?>Menu/GetCityInDistrictByDID',
                                        type: "POST",
                                        data: {
                                            districtid: districtid
                                        },
                                        cache: false,
                                        success: function(result) {
                                            $("#id_city").html(result);
                                        }
                                    });
                                });


                    });


                    $(document).on('click', '.hoverthumbData', function () {
                        // Get the text inside the span
                        var textData = $(this).text().trim();
                        $("#titleMessage").val(textData);
                        $("#assign_user_message").val(textData);
                        
                        // Fire AJAX request
                        $.ajax({
                            url: '<?=base_url()?>/Menu/GetUserByTextInSuperAdmin', // Change this to your endpoint
                            method: 'POST',
                            data: { clicked_text: textData,cid:'<?=$cid?>' },
                            success: function (response) {
                                $("#assign_user_card").html(response);
                            }
                        });
                    });
                    $(document).on('click', '.hoverthumbDataCatrgory', function () {
                        // Get the text inside the span
                        var textData = $(this).text().trim();
                        $("#titleMessage1").val(textData);
                        $("#assign_category_message").val(textData);
                        
                        // Fire AJAX request
                        $.ajax({
                            url: '<?=base_url()?>/Menu/GetUserByCategoryTextInSuperAdmin', // Change this to your endpoint
                            method: 'POST',
                            data: { clicked_text: textData,cid:'<?=$cid?>' },
                            success: function (response) {
                                $("#category_assign_user").html(response);
                            }
                        });
                    });

                </script>

              <!-- Leaflet JS -->
<!-- In <head> OR before the form -->

   

    