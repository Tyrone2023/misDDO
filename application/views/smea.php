<?php include('templates/head.php'); ?>
<?php include('templates/header.php'); ?>



<!-- ============================================================== -->
<!-- Start Page Content here -->
<!-- ============================================================== -->

<div class="content-page">
    <div class="content">

        <!-- Start Content-->
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h2 class="text-center">School Monitoring, Evaluation and Adjustment (SMEA) <br /> CY <?= date('Y'); ?></h2>

                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
            <!-- end page title -->





            <?php if ($this->session->flashdata('success')) : ?>

                <?= '<div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>'
                    . $this->session->flashdata('success') .
                    '</div>';
                ?>
            <?php endif; ?>

            <?php if ($this->session->flashdata('danger')) : ?>
                <?= '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>'
                    . $this->session->flashdata('danger') .
                    '</div>';
                ?>
            <?php endif;  ?>

            <form class="parsley-examples" method="Post">

                <div class="row">
                    <div class="col-lg-12">
                        <div id="accordion" class="mb-3">
                            <div class="card mb-0">

                                <div class="card-header" id="headingOne">
                                    <h6 class="m-0">
                                        <a href="#collapseOne" class="text-dark" data-toggle="collapse"
                                            aria-expanded="true"
                                            aria-controls="collapseOne">
                                            PHYSICAL ACCOMPLISHMENTS
                                        </a>
                                    </h6>
                                </div>

                                <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
                                    <div class="card-body">

                                        <div class="form-row">
                                            <div class="form-group col-md-1">
                                                <label for="inputEmail4" class="col-form-label">Year</label>
                                                <input type="text" class="form-control" readonly value="<?= isset($sYear) && $sYear !== '' ? $sYear : date('Y'); ?>" required pattern="\d{4}" maxlength="4" name="sYear">

                                            </div>
                                            <div class="form-group col-md-2">
                                                <label for="inputEmail4" class="col-form-label">Quarter</label>
                                                <input type="text" class="form-control" name="sQtr" readonly value="<?= isset($sQtr) && $sQtr !== '' ? $sQtr : (isset($_GET['quarter']) ? $_GET['quarter'] : ''); ?>">
                                          

                                            </div>
                                            <div class="form-group col-md-3">
                                                <label for="inputEmail4" class="col-form-label">No. of Physical Targets</label>
                                                <input type="text" class="form-control" name="sPhyTargets" value="<?= isset($sPhyTargets) ? $sPhyTargets : ''; ?>" required>

                                            </div>
                                            <div class="form-group col-md-3">
                                                <label for="inputPassword4" class="col-form-label">Achieved </label>
                                                <input type="text" class="form-control" name="sAchieved" value="<?= isset($sAchieved) ? $sAchieved : ''; ?>" required>
                                            </div>

                                            <div class="form-group col-md-3">
                                                <label for="inputPassword4" class="col-form-label">Percentage of Accomplishment </label>
                                                <input type="text" class="form-control" name="sAccomplishmentsPercentage" value="<?= isset($sAccomplishmentsPercentage) ? $sAccomplishmentsPercentage : ''; ?>" required>
                                            </div>

                                        </div>

                                        <div class="form-row">
                                            <div class="form-group col-md-3">
                                                <label for="inputEmail4" class="col-form-label">Gain</label>
                                                <input type="text" class="form-control" name="sGain" value="<?= isset($sGain) ? $sGain : ''; ?>" required>
                                            </div>
                                            <div class="form-group col-md-3">
                                                <label for="inputPassword4" class="col-form-label">Percentage of Gain </label>
                                                <input type="text" class="form-control" name="sGainPercentage" value="<?= isset($sGainPercentage) ? $sGainPercentage : ''; ?>" required>
                                            </div>

                                            <div class="form-group col-md-3">
                                                <label for="inputPassword4" class="col-form-label">Gap/Balance</label>
                                                <input type="text" class="form-control" name="sGapBalance" value="<?= isset($sGapBalance) ? $sGapBalance : ''; ?>" required>
                                            </div>

                                            <div class="form-group col-md-3">
                                                <label for="inputPassword4" class="col-form-label">Percentage of Gap</label>
                                                <input type="text" class="form-control" name="sGapPercentage" value="<?= isset($sGapPercentage) ? $sGapPercentage : ''; ?>" required>
                                            </div>

                                        </div>


                                    </div>
                                </div>
                            </div>
                            <div class="card mb-0">
                                <div class="card-header" id="headingTwo">
                                    <h6 class="m-0">
                                        <a href="#collapseTwo" class="text-dark collapsed" data-toggle="collapse"
                                            aria-expanded="false"
                                            aria-controls="collapseTwo">
                                            FINANCIAL ACCOMPLISHMENTS (REGULAR FUNDS-MOOE)
                                        </a>
                                    </h6>
                                </div>
                                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion">
                                    <div class="card-body">
                                        <div class="form-row">
                                            <div class="form-group col-md-4">
                                                <label for="inputEmail4" class="col-form-label">Funds Allocated for Quarter</label>
                                                <input type="text" class="form-control" name="sFundAllocation" value="<?= isset($sFundAllocation) ? $sFundAllocation : ''; ?>" required>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="inputPassword4" class="col-form-label">Funds Utilized </label>
                                                <input type="text" class="form-control" name="sFundsUtilized" value="<?= isset($sFundsUtilized) ? $sFundsUtilized : ''; ?>" required>
                                            </div>

                                            <div class="form-group col-md-4">
                                                <label for="inputPassword4" class="col-form-label">Percentage of Utilization </label>
                                                <input type="text" class="form-control" name="sUtilizationPercentage" value="<?= isset($sUtilizationPercentage) ? $sUtilizationPercentage : ''; ?>" required>
                                            </div>
                                        </div>

                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label for="inputEmail4" class="col-form-label">Gap on Allocation versus Utilized (Amount)</label>
                                                <input type="text" class="form-control" name="sGapAmount" value="<?= isset($sGapAmount) ? $sGapAmount : ''; ?>" required>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="inputPassword4" class="col-form-label">Gap on Allocation versus Utilized (Percentage) </label>
                                                <input type="text" class="form-control" name="sGapPercentageUtilize" value="<?= isset($sGapPercentageUtilize) ? $sGapPercentageUtilize : ''; ?>" required>
                                            </div>
                                        </div>



                                    </div>
                                </div>

                            </div>

                            <div class="card mb-0">
                                <div class="card-header" id="headingThree">
                                    <h6 class="m-0">
                                        <a href="#collapseThree" class="text-dark collapsed" data-toggle="collapse"
                                            aria-expanded="false"
                                            aria-controls="collapseThree">
                                            FINANCIAL ACCOMPLISHMENTS (OTHER FUNDS-PTA/DONATIONS/IGP)
                                        </a>
                                    </h6>
                                </div>
                                <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordion">
                                    <div class="card-body">
                                        <div class="form-row">
                                            <div class="form-group col-md-4">
                                                <label for="inputEmail4" class="col-form-label">Funds Allocated for Quarter</label>
                                                <input type="text" class="form-control" name="fundsAllocatedQtr" value="<?= isset($fundsAllocatedQtr) ? $fundsAllocatedQtr : ''; ?>" required>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="inputPassword4" class="col-form-label">Funds Utilized </label>
                                                <input type="text" class="form-control" name="fundsUtilized" value="<?= isset($fundsUtilized) ? $fundsUtilized : ''; ?>" required>
                                            </div>

                                            <div class="form-group col-md-4">
                                                <label for="inputPassword4" class="col-form-label">Percentage of Utilization </label>
                                                <input type="text" class="form-control" name="fundsUtilizedPercentage" value="<?= isset($fundsUtilizedPercentage) ? $fundsUtilizedPercentage : ''; ?>" required>
                                            </div>
                                        </div>

                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label for="inputEmail4" class="col-form-label">Gap on Allocation versus Utilized (Amount)</label>
                                                <input type="text" class="form-control" name="gapUtilizedAmount" value="<?= isset($gapUtilizedAmount) ? $gapUtilizedAmount : ''; ?>" required>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="inputPassword4" class="col-form-label">Gap on Allocation versus Utilized (Percentage) </label>
                                                <input type="text" class="form-control" name="gapUtilizedPercentage" value="<?= isset($gapUtilizedPercentage) ? $gapUtilizedPercentage : ''; ?>" required>
                                            </div>
                                        </div>



                                    </div>
                                </div>

                            </div>



                            <div class="card mb-0">
                                <div class="card-header" id="headingFour">
                                    <h6 class="m-0">
                                        <a href="#collapseFour" class="text-dark collapsed" data-toggle="collapse"
                                            aria-expanded="false"
                                            aria-controls="collapseFour">
                                            VALUE-ADDED OUTPUT
                                        </a>
                                    </h6>
                                </div>
                                <div id="collapseFour" class="collapse" aria-labelledby="headingFour" data-parent="#accordion">
                                    <div class="card-body">
                                        <div class="form-row">
                                            <div class="form-group col-md-12">
                                                <label for="inputEmail4" class="col-form-label">Value-Added Output </label>
                                                <input type="text" class="form-control" name="valueAdded" value="<?= isset($valueAdded) ? $valueAdded : ''; ?>">
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card mb-0">
                                <div class="card-header" id="headingFive">
                                    <h6 class="m-0">
                                        <a href="#collapseFive" class="text-dark collapsed" data-toggle="collapse"
                                            aria-expanded="false"
                                            aria-controls="collapseFive">
                                            REASONS FOR ACCOMPLISHMENTS
                                        </a>
                                    </h6>
                                </div>
                                <div id="collapseFive" class="collapse" aria-labelledby="headingFive" data-parent="#accordion">
                                    <div class="card-body">
                                        <div class="form-row">
                                            <div class="form-group col-md-12">
                                                <label for="inputEmail4" class="col-form-label">Reasons for Accomplishment </label>
                                                <input type="text" class="form-control" name="reasons" value="<?= isset($reasons) ? $reasons : ''; ?>">
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>

                </div>
                <!-- end row -->
                <input type="submit" name="submit" value="Submit" class="btn btn-info waves-effect waves-light float-right">
            </form>

        </div>
        <!-- end container-fluid -->

    </div>
    <!-- end content -->











    <?php include('templates/footer.php'); ?>