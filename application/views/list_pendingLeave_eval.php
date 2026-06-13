            <?php include('templates/head.php'); ?>
            <link href="<?= base_url(); ?>assets/libs/custombox/custombox.min.css" rel="stylesheet" type="text/css">
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

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body table-responsive">
                                    <h4 class="header-title mb-4">
                                    <?php
                                        $userPosition = $this->session->userdata('position');
                                        if ($userPosition === "Human Resource Admin") {
                                            echo "Leave for Evaluation";
                                        } elseif ($userPosition === "Endorser") {
                                            echo "Leave for Recommendation";
                                        } elseif ($userPosition === "asds") {
                                            echo "Leave for Approval";
                                        } else {
                                            echo "Leave Requests"; // default or fallback
                                        }
                                    ?>
                                </h4><br />

                                        <?php
                                        if (!$data) {
                                            //the value is null
                                            echo "No records for available leave credits.";
                                        } else {
                                        ?>

                                            <?php if (!empty($success_message)): ?>
                                                <script>
                                                    alert("<?= $success_message; ?>");
                                                </script>
                                            <?php endif; ?>

                                            <form method="post" class="parsley-examples">

                                                <div class="form-row">
                                                    <div class="form-group col-md-3">
                                                        <label class="col-form-label">Employee No.</label>
                                                        <input type="text" class="form-control" required value="<?php echo $data[0]->IDNumber; ?>" name="IDNumber" readonly>
                                                    </div>
                                                    <div class="form-group col-md-3">
                                                        <label class="col-form-label">First Name</label>
                                                        <input type="text" required class="form-control" value="<?php echo $data[0]->FirstName; ?>" name="FirstName" readonly>
                                                    </div>
                                                    <div class="form-group col-md-3">
                                                        <label class="col-form-label">Middle Name</label>
                                                        <input type="text" required name="MiddleName" value="<?php echo $data[0]->MiddleName; ?>" class="form-control" readonly>
                                                    </div>

                                                    <div class="form-group col-md-3">
                                                        <label class="col-form-label">Last Name</label>
                                                        <input type="text" required name="LastName" value="<?php echo $data[0]->LastName; ?>" class="form-control" readonly>
                                                    </div>
                                                </div>

                                                <div class="form-row">

                                                    <div class="form-group col-md-3">
                                                        <label class="col-form-label">Applied Date</label>
                                                        <input type="text" required name="empStatus" value="<?php echo $data[0]->appDate; ?>" class="form-control" readonly>
                                                    </div>

                                                    <div class="form-group col-md-3">
                                                        <label class="col-form-label">No. of Days</label>
                                                        <input type="text" required name="daysApplied" value="<?php echo $data[0]->daysApplied; ?>" class="form-control" readonly>
                                                    </div>

                                                    <div class="form-group col-md-3">
                                                        <label class="col-form-label">From</label>
                                                        <input type="date" required name="dateFrom" value="<?php echo $data[0]->dateFrom; ?>" class="form-control" readonly>
                                                    </div>

                                                    <div class="form-group col-md-3">
                                                        <label class="col-form-label">To</label>
                                                        <input type="date" required name="dateTo" value="<?php echo $data[0]->dateTo; ?>" class="form-control" readonly>
                                                    </div>

                                                </div>

                                                <hr />

                                                <div class="form-row">
                                                    <div class="form-group col-md-3">
                                                        <label class="col-form-label">Leave Type</label>
                                                        <input type="text" name="leaveType" value="<?php echo $data[0]->leaveType; ?>" class="form-control" readonly>
                                                    </div>
                                                    <div class="form-group col-md-3">
                                                        <label class="col-form-label">Available VL</label>
                                                        <input type="text" required name="slTotal" value="<?php echo $data[0]->vlTotal; ?>" class="form-control" readonly>
                                                    </div>

                                                    <div class="form-group col-md-3">
                                                        <label class="col-form-label">Available SL</label>
                                                        <input type="text" name="slTotal" value="<?php echo $data[0]->slTotal; ?>" class="form-control" readonly>
                                                    </div>

                                                    <div class="form-group col-md-3">
                                                        <label class="col-form-label">Available COC/Service Credits</label>
                                                        <input type="text" name="cocTotal" value="<?php echo $data[0]->cocTotal; ?>" class="form-control" readonly>
                                                    </div>
                                                </div>

                                                <input type="hidden" name="leaveID" value="<?php echo $data[0]->leaveID; ?>" class="form-control" readonly>

                                    </div>
                                    <div class="modal-footer">
                                    <?php
    $userPosition = $this->session->userdata('position');

    if ($userPosition === "Human Resource Admin") {
        $buttonLabel = "Evaluate";
    } elseif ($userPosition === "Endorser") {
        $buttonLabel = "Recommend";
    } elseif ($userPosition === "asds") {
        $buttonLabel = "Approve";
    } else {
        $buttonLabel = "Submit";
    }
?>

<input type="submit" name="submit" value="<?= $buttonLabel ?>" class="btn btn-primary waves-effect waves-light">

                                    </div>
                                </div>

                                </form>

                            <?php } ?>

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