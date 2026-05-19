<?php include_once('header.php');?>

<section class="content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white text-center py-3">
                        <?php $username  = $review_user_id;?>
                        <h4 class="mb-0">Target Data for <?php echo $username;?> - <?php echo $reviewType." Review "; ?></h4>
                    </div>
                     <?php
        if($this->session->flashdata('success_message')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong> <?php echo $this->session->flashdata('success_message'); ?></strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <?php endif; ?>
                    <div class="card-body">
                        <!-- Form Starts Here -->
                        <form class="row g-4" method="post" name="targetQandA" action="targetQandA">
                            <!-- First Row: Prospective and Partner Type -->
                            <div class="col-md-6">
                                <label for="prospecting" class="form-label">How Many No. Of Prospecting Companies ?</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-building"></i></span>
                                    <input type="number" class="form-control" name="prospecting_companies" id="prospecting_companies" placeholder="Enter No. of Companies">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="partner_type" class="form-label">Select Partner Type</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-people-fill"></i></span>
                                    <select class="form-select" name="partner_type" id="partner_type">
                                        <option value="">Choose Partner Type</option>
                                        <?php foreach($partnerType as $partnerValue){ ?>
                                            <option value="<?php echo $partnerValue->id; ?>">
                                                <?php echo $partnerValue->name; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Second Row: Proposal and Proposal Revenue -->
                            <div class="col-md-6">
                                <label for="proposal" class="form-lab+el">New Proposals To Be Handed Over?</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-file-earmark-text"></i></span>
                                    <input type="number" class="form-control" id="proposal_no_of_schools" name="proposal_no_of_schools" placeholder="Enter No. of Schools">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="proposal_revenue" class="form-label">Proposal Revenue</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-currency-dollar"></i></span>
                                    <input type="number" class="form-control" id="proposal_revenue" name="proposal_revenue" placeholder="Enter Revenue Amount">
                                </div>
                            </div>
                            <hr><hr>
                            <!-- Third Row: Closure Details -->
                            <div class="col-md-12"><label for="no_of_clients" class="form-label">Closure</label></div>
                           
                            <div class="col-md-4">
                                <label for="no_of_clients" class="form-label">No. of Clients</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person-check"></i></span>
                                    <input type="number" class="form-control" id="closure_no_of_clients" name="closure_no_of_clients" placeholder="Enter No. of Clients">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="closure_no_of_Schools" class="form-label">No. of Schools</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-house"></i></span>
                                    <input type="number" class="form-control" id="closure_no_of_Schools" name="closure_no_of_Schools" placeholder="Enter No. of Schools">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="closure_revenue" class="form-label">Revenue</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-cash-stack"></i></span>
                                    <input type="number" class="form-control" id="closure_revenue" name="closure_revenue" placeholder="Enter Revenue Amount">
                                </div>
                            </div>
                            <!-- Submit Button -->
                            <div class="col-12 text-center mt-4">
                                <button type="submit" class="btn btn-success btn-lg px-5">Submit <i class="bi bi-check-circle"></i></button>
                            </div>
                        </form>
                        <!-- Form Ends Here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include_once('footer.php');?>
