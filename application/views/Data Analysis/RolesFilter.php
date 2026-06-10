  <?php $clusterPstDatas = $this->Menu_model->GetAdminActiveTeam($user['user_id']); ?>
  <form action="<?=base_url()?>SalesGraph/TodaysTaskPlannerManagementAnalysis" method="post">
            <div class="row mb-4">
                <div class="col-md-3">
                    <label for="selectedDate">Select Date</label>
                    <input type="date" value="<?=$cdate;?>" id="selectedDate" name="selectedDate" class="form-control">
                </div>
                <div class="col-md-3">
                    <label for="selectedDate">Select Admin</label>
                    <select id="admin_id_filter" name="admin_id_filter" class="form-control">
                        <option value="all">All</option>
                        <?php foreach ($clusterPstDatas as $clusterPstData) { ?>
                            <option value="<?= $clusterPstData->user_id; ?>" <?= ($clusterPstData->user_id == $admin_id_filter) ? 'selected' : ''; ?>>
                                <?= $clusterPstData->name; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <?php $getTeamsDatas = $this->Menu_model->GetClusterAndPSTActiveTeam($admin_id_filter); ?>
                    <label for="selectedDate">Select RM</label>
                    <select id="rm_filter" name="rm_filter" class="form-control">
                        <option value="all">All</option>
                        <?php foreach ($getTeamsDatas as $getTeamsData) { ?>
                            <option value="<?= $getTeamsData->user_id; ?>" <?= ($getTeamsData->user_id == $uid) ? 'selected' : ''; ?>>
                                <?= $getTeamsData->name; ?>
                            </option>
                        <?php }?>
                    </select>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary p-1" style="margin-top: 36px; width: 100px;">Filter</button>
                    </div>
                </div>
            </div>
        </form>