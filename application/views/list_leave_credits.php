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
                                        <h4 class="header-title mb-4">Monthly Leave Credits</h4>

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
                                            <div class="col-md-12">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <!-- <h4 class="header-title mb-4">Monthly Leave Credits</h4> -->

                                                        <form id="leaveCreditsForm" class="form-inline">
                                                            <div class="form-group mr-3">
                                                                <label for="inputMonth" class="col-form-label mr-2">Month</label>
                                                                <select id="inputMonth" name="month" class="form-control">
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

                                                            <div class="form-group mr-3">
                                                                <label for="inputYear" class="col-form-label mr-2">Year</label>
                                                                <input type="text" id="inputYear" name="year" class="form-control" value="<?php echo date('Y'); ?>" placeholder="Enter Year">
                                                            </div>

                                                            <!-- <button type="button" class="btn btn-info mr-2" id="generateButton">Generate Monthly Leave Credits</button> -->
                                                            <button type="button" class="btn btn-info mr-2" id="generateButton"
                                                                data-toggle="tooltip" data-placement="top"
                                                                title="If some employees are not included, but you believe they should be, ensure that their profile is updated, especially the field 'With Monthly Leave Credits (VL and SL)?' and that their Current Status is set to 'Active.'">
                                                                Generate Monthly Leave Credits
                                                            </button>
                                                            <script>
                                                                $(document).ready(function() {
                                                                    $('[data-toggle="tooltip"]').tooltip();
                                                                });
                                                            </script>
                                                        </form>


                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- end row -->

                                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">

                                            <thead>
                                                <tr>
                                                    <th>Last Name</th>
                                                    <th>First Name</th>
                                                    <th>Middle Name</th>
                                                    <th>Employee No.</th>
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

                <script>
                    document.getElementById('generateButton').addEventListener('click', function() {
                        // Get the values from the form fields
                        const month = document.getElementById('inputMonth').value; // Dropdown for months
                        const year = document.getElementById('inputYear').value; // Text input for the year

                        // Validate inputs
                        if (!month || !year) {
                            alert('Please select a valid month and year.');
                            return;
                        }

                        // Construct the URL for the controller function
                        const baseUrl = '<?php echo site_url("Page/monthlyLeaveCredits"); ?>'; // Replace 'ControllerName' with your actual controller name
                        const url = `${baseUrl}?month=${month}&year=${year}`;

                        // Redirect to the constructed URL
                        window.location.href = url;
                    });
                </script>


                <?php include('templates/footer.php'); ?>