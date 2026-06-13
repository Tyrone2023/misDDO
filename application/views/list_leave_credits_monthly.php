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
                                    <!-- <a href="<?= $link; ?>" class="btn btn-success waves-effect width-md waves-light"><?= $pn; ?></a> -->
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->



                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body table-responsive">
                                        <h4 class="header-title mb-4"></h4>
                                        <?php $currentYear = date("Y"); ?>
                                        <form class="form-inline parsley-examples" method="post" accept-charset="utf-8">
                                            <div class="form-row">
                                                <!-- Month Select -->
                                                <div class="form-group mx-sm-3 mb-2">
                                                    <label for="semester" class="mr-2">Month</label>
                                                    <select class="form-control" name="month" required>
                                                        <option></option>
                                                        <option value="01">January</option>
                                                        <option value="02">February</option>
                                                        <option value="03">March</option>
                                                        <option value="04">April</option>
                                                        <option value="05">May</option>
                                                        <option value="06">June</option>
                                                        <option value="07">July</option>
                                                        <option value="08">August</option>
                                                        <option value="09">September</option>
                                                        <option value="10">October</option>
                                                        <option value="11">November</option>
                                                        <option value="12">December</option>
                                                    </select>
                                                </div>

                                                <!-- Year Input -->
                                                <div class="form-group mx-sm-3 mb-2">
                                                    <label for="year" class="mr-2">Year</label>
                                                    <input type="text" name="year" class="form-control" value="<?php echo $currentYear; ?>" required />
                                                </div>

                                                <!-- Submit Button -->
                                                <button type="submit" name="submit" class="btn btn-primary mb-2">View</button>
                                            </div>
                                        </form>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end row -->

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body table-responsive">
                                        <h4 class="header-title mb-4">Leave Credits</h4>

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
                                        <!-- <a href="monthlyLeaveCredits"><button type="button" class="btn btn-info">Generate Monthly Leave Credits</button></a>
                                        <br /> <br /> -->
                                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">

                                            <thead>
                                                <tr>
                                                    <th>Last Name</th>
                                                    <th>First Name</th>
                                                    <th>Middle Name</th>
                                                    <th>Employee No.</th>
                                                    <th>Position</th>
                                                    <th style='text-align:center'>VL</th>
                                                    <th style='text-align:center'>SL</th>
                                                    <th style='text-align:center'>Month/Year</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $i = 1;
                                                foreach ($data as $row) {
                                                    echo "<tr>";
                                                    echo "<td>" . $row->LastName . "</td>";
                                                    echo "<td>" . $row->FirstName . "</td>";
                                                    echo "<td>" . $row->MiddleName . "</td>";
                                                    echo "<td>" . $row->IDNumber . "</td>";
                                                    echo "<td>" . $row->empPosition . "</td>";
                                                    echo "<td style='text-align:center'>" . $row->vlCredit . "</td>";
                                                    echo "<td style='text-align:center'>" . $row->slCredit . "</td>";
                                                    echo "<td style='text-align:center'>" . $row->vlMonth . ' ' . $row->vlYear . "</td>";
                                                    echo "</tr>";
                                                }
                                                ?>
                                            </tbody>

                                        </table>
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