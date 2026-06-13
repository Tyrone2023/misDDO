<?php include('templates/head.php'); ?>
<?php include('templates/header.php'); ?>

<!-- ============================================================== -->
<!-- Start Page Content here -->
<!-- ============================================================== -->
 <style>
  label {
    display: block;
    margin-top: 16px;
    font-weight: bold;
  }

  .badge {
    display: inline-block;
    background-color: #e0e0e0;
    color: #333;
    padding: 2px 6px;
    margin-left: 6px;
    font-size: 0.75em;
    border-radius: 4px;
    cursor: help;
    transition: background-color 0.3s ease;
    background-color:red;
    color:#fff;
  }

  .badge:hover {
    background-color: #d0d0d0;
  }

</style>

<div class="content-page">
    <div class="content">

        <!-- Start Content-->
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <!-- <a class="btn btn-success" href="<?= base_url(); ?>Pages/district_report" target="_blank">View List</a> -->

                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body table-responsive">
                            <!-- <h4 class="header-title mb-4">Travel Requests Form</h4> -->

                            <?php if($this->session->flashdata('success')) : ?>

                                        <?= '<div class="alert alert-success alert-dismissible fade show" role="alert">
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>'
                                                .$this->session->flashdata('success'). 
                                            '</div>'; 
                                        ?>
                                        <?php endif; ?>

                                        <?php if($this->session->flashdata('danger')) : ?>
                                        <?= '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>'
                                                .$this->session->flashdata('danger'). 
                                            '</div>'; 
                                        ?>
                                        <?php endif;  ?>


                            <div class="container mt-4">
                                <h2 class="mb-4"><?= isset($request) ? 'Edit' : 'Create' ?> Travel Request</h2>
                                <!-- <form method="post" class="needs-validation" novalidate> -->
                                <form method="post" enctype="multipart/form-data" action="<?= isset($request) ? site_url('travel/update/' . $request->id) : site_url('travel/create') ?>" class="needs-validation">

                                    <?php if (isset($request)): ?>
                                        <input type="hidden" name="id" value="<?= $request->id ?>">
                                    <?php endif; ?>

                                    <input type="hidden" class="form-control" id="asds_comments" name="asds_comments" required>

                                    <!-- <input type="hidden" class="form-control" id="status" name="status" value="<?= $request->status ?? 'Pending' ?>" required> -->

                                    <div class="row mb-3">

                                        <div class="col-md-3">
                                            <label for="departure_date">Inclusive Dates (From)</label>
                                            <input type="date" class="form-control" id="date_start" name="date_start" value="<?= $request->date_start ?? '' ?>" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="departure_date">Inclusive Dates (To)</label>
                                            <input type="date" class="form-control" id="date_end" name="date_end" value="<?= $request->date_end ?? '' ?>" required>
                                        </div>
                                        <div class="col-md-2">
                                            <label for="departure_date">Type </label>
                                            <select class="form-control" name="ttype" required>
                                                <option disabled selected></option>
                                                <option <?= set_select('ttype', '0', isset($request) && $request->ttype == '0') ?> value="0">Within the Division</option>
                                                <option <?= set_select('ttype', '1', isset($request) && $request->ttype == '1') ?> value="1">Outside the Division</option>
                                            </select>
                                        </div>

                                        
                                        <div class="form-group col-md-4">
                                            <label for="destination"><small class="text-danger" >Max file size: 2 MB | Supported formats: JPG, PNG, PDF.</small></label>
                                            <input  type="file" class="form-control" id="file_url" name="file_url" value="<?= $request->file_url ?? '' ?>">
                                            <!-- <?php if (isset($request->file_url)): ?>
                                                <small>Current file: <a href="<?= base_url('uploads/travel/' . $request->file_url) ?>" target="_blank"><?= $request->file_url ?></a></small>
                                            <?php endif; ?> -->
                                        </div>
                                       
                                    </div>

                                    

                                    <div class="form-group mb-3">
                                        <label for="purpose">Purpose</label>
                                        <textarea class="form-control" id="purpose" name="purpose" rows="3" required><?= $request->purpose ?? '' ?></textarea>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-md-8">
                                            <label for="destination">Destination</label>
                                            <input type="text" class="form-control" id="destination" name="destination" value="<?= $request->destination ?? '' ?>" required>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="destination">Permanent Station</label>
                                            <input type="text" class="form-control" id="permanent_station" name="permanent_station" value="<?= $request->permanent_station ?? '' ?>" required>
                                        </div>
                                    </div>

                                    

                                    

                                    <div class="row mb-3">
                                        <div class="col-md-5">
                                            <label for="activity_host">Host of the Activity</label>
                                            <input type="text" class="form-control" id="activity_host" name="activity_host" value="<?= $request->activity_host ?? '' ?>" required>
                                        </div>

                                        <div class="col-md-5">
                                            <label for="fund_source">Fund Source</label>
                                            <input list="fund_source_list" id="fund_source" name="fund_source" class="form-control"
                                                value="<?= isset($request) ? $request->fund_source : set_value('fund_source'); ?>"
                                                placeholder="Please choose an option or enter your specific source of funds" required>

                                            <datalist id="fund_source_list">
                                                <option value="MOOE">MOOE (Maintenance and Other Operating Expenses)</option>
                                                <option value="program">Program/Project Funds (e.g., LAC, Brigada Eskwela)</option>
                                                <option value="central_office">Central/Regional/Division Office Funds</option>
                                                <option value="sef">Special Education Fund (SEF)</option>
                                                <option value="external">External/Donor-Funded Projects (e.g., UNICEF, USAID)</option>
                                            </datalist>
                                        </div>

                                        <div class="col-md-2">
                                            <label for="fund_source">office/s to be visited?</label>
                                            <select class="form-control" name="div_office" required>
                                                <option disabled selected></option>
                                                <option <?= set_select('div_office', '0', isset($request) && $request->div_office == '0') ?> value="0">Not Applicable</option>
                                                <option <?= set_select('div_office', '1', isset($request) && $request->div_office == '1') ?> value="1">OSDS</option>
                                                <option <?= set_select('div_office', '1', isset($request) && $request->div_office == '1') ?> value="1">CID</option>
                                                <option <?= set_select('div_office', '1', isset($request) && $request->div_office == '1') ?> value="1">SGOD</option>
                                            </select>
                                        </div>
                                    </div>
                                    


                                    <!-- Row for inline date fields -->
                                    <!-- <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="departure_date">Start Date</label>
                                            <input type="date" class="form-control" id="departure_date" name="departure_date" value="<?= $request->departure_date ?? '' ?>" required>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="return_date">End Date</label>
                                            <input type="date" class="form-control" id="return_date" name="return_date" value="<?= $request->return_date ?? '' ?>" required>
                                        </div>
                                    </div> -->

                                    


                                    


                                    <button type="submit" class="btn btn-primary">Save</button>
                                    <a href="<?= site_url('travel') ?>" class="btn btn-secondary">Cancel</a>
                                </form>

                            </div>


                        </div>
                    </div>
                </div>
            </div>
            <!-- end row -->

        </div>
        <!-- end container-fluid -->

    </div>
    <!-- end content -->

    <?php include('templates/footer.php'); ?>