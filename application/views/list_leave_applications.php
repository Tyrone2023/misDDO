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
                                        <h4 class="header-title mb-4">Leave Applications</h4>

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
                                        <button type="button" class="btn btn-primary waves-effect waves-light" data-toggle="modal" data-target=".bs-example-modal-lg">New Leave Application</button>
                                        <br /> <br />
                                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">

                                            <thead>
                                                <tr>
                                                    <th style='text-align:center'>Date Applied</th>
                                                    <th style='text-align:center'>Leave Type</th>
                                                    <th style='text-align:center'>Duration</th>
                                                    <th style='text-align:center'>Total No. of Days</th>
                                                    <th style='text-align:center'>Leave Status</th>
                                                    <th style='text-align:center'>Leave Attachment</th>
                                                    <th style='text-align:center'>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                        <?php
                                        $i = 1;
                                        foreach ($data as $row) {
                                            echo "<tr>";
                                            echo "<td style='text-align:center'>" . $row->appDate . "</td>";
                                            echo "<td style='text-align:center'>" . $row->leaveType . "</td>";
                                            echo "<td style='text-align:center'>" . $row->dateFrom . ' to ' . $row->dateTo . "</td>";
                                            echo "<td style='text-align:center'>" . $row->daysApplied . "</td>";
                                            echo "<td style='text-align:center'>" . $row->leaveStatus . "</td>";
                                        ?>

                                        
<td style='text-align:center'>
    <?php if (!empty($row->leaveAttachment)) : ?>
        <a href="<?= base_url('uploads/leave_attachement/' . $row->leaveAttachment); ?>" target="_blank" class="text-primary">
            <i class="mdi mdi-file-pdf"></i> View Attachment
        </a>
    <?php else : ?>
        -
    <?php endif; ?>
</td>

<td style='text-align:center'>
    <?php if (!in_array($row->leaveStatus, ['For Approval', 'Evaluated', 'Recommended'])) : ?>
        <a href="<?= base_url(); ?>Page/printLeaveForm?id=<?= $row->leaveID; ?>" target="_blank" class="text-success">
            <i class="mdi mdi-printer"></i> Print Preview
        </a>
    <?php endif; ?>

    <?php if ($row->leaveStatus !== 'Approved') : ?>
        <a href="<?= base_url(); ?>Page/deleteLeave?id=<?= $row->leaveID; ?>" class="text-danger" onclick="return confirm('Are you sure you want to delete this leave application?');">
            <i class="mdi mdi-delete"></i> Delete
        </a>
    <?php endif; ?>
</td>

                                        <?php
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

                <!--  Modal content for the above example -->
                <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="display: none;" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="myLargeModalLabel">New Leave Application</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            </div>
                            <div class="modal-body">
                                <!-- Form row -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card">
                                            <div class="card-body">
                                                <form method="post" enctype="multipart/form-data">

                                                    <div class="form-row">
                                                        <div class="form-group col-md-12">
                                                            <label for="inputEmail4" class="col-form-label">Type of Leave</label>
                                                            <select name="leaveType" class="form-control" required>
                                                                <option></option>
                                                                <option value="Vacation Leave">Vacation Leave (Sec. 51, Rule XVI, Omnibus Rules Implementing E.O. No. 292)</option>
                                                                <option value="Mandatory/Forced Leave">Mandatory/Forced Leave(Sec. 25, Rule XVI, Omnibus Rules Implementing E.O. No. 292)</option>
                                                                <option value="Sick Leave">Sick Leave (Sec. 43, Rule XVI, Omnibus Rules Implementing E.O. No. 292)</option>
                                                                <option value="Maternity Leave">Maternity Leave (R.A. No. 11210 / IRR issued by CSC, DOLE and SSS)</option>
                                                                <option value="Paternity Leave">Paternity Leave (R.A. No. 8187 / CSC MC No. 71, s. 1998, as amended)</option>
                                                                <option value="Special Privilege Leave">Special Privilege Leave (Sec. 21, Rule XVI, Omnibus Rules Implementing E.O. No. 292)</option>
                                                                <option value="Solo Parent Leave">Solo Parent Leave (RA No. 8972 / CSC MC No. 8, s. 2004)</option>
                                                                <option value="Study Leave">Study Leave (Sec. 68, Rule XVI, Omnibus Rules Implementing E.O. No. 292)</option>
                                                                <option value="10-Day VAWC Leave">10-Day VAWC Leave (RA No. 9262 / CSC MC No. 15, s. 2005)</option>
                                                                <option value="Rehabilitation Privilege">Rehabilitation Privilege (Sec. 55, Rule XVI, Omnibus Rules Implementing E.O. No. 292)</option>
                                                                <option value="Special Leave Benefits for Women">Special Leave Benefits for Women (RA No. 9710 / CSC MC No. 25, s. 2010)</option>
                                                                <option value="Special Emergency">Special Emergency (Calamity) Leave (CSC MC No. 2, s. 2012, as amended)</option>
                                                                <option value="Adoption Leave">Adoption Leave (R.A. No. 8552)</option>
                                                            </select>
                                                        </div>

                                                    </div>



                                                   


<div id="vacationSpecialPrivilegeSection" style="display: none;">
    <div class="form-row">
        <div class="form-group col-md-6">
            <label class="col-form-label">In case of Vacation/Special Privilege Leave:</label>
            <select name="abroad" class="form-control">
                <option></option>
                <option value="Within the Philippines">Within the Philippines</option>
                <option value="Abroad">Abroad</option>
            </select>
        </div>
        <div class="form-group col-md-6">
            <label class="col-form-label">If abroad (Specify)</label>
            <input type="text" class="form-control" name="spentPlace">
        </div>
    </div>
</div>

<div id="sickLeaveSection" style="display: none;">
    <div class="form-row">
        <div class="form-group col-md-6">
            <label class="col-form-label">In case of Sick Leave:</label>
            <select name="sickLeave" class="form-control">
                <option></option>
                <option value="In Hospital">In Hospital</option>
                <option value="Out Patient">Out Patient</option>
            </select>
        </div>
        <div class="form-group col-md-6">
            <label class="col-form-label">Specify Illness</label>
            <input type="text" class="form-control" name="leaveStatReasons">
        </div>
    </div>
</div>

<div id="specialLeaveWomenSection" style="display: none;">
    <div class="form-row">
        <div class="form-group col-md-12">
            <label class="col-form-label">In case of Special Leave Benefits for Women:</label>
            <label class="col-form-label">Specify Reason</label>
            <input type="text" class="form-control" name="leaveStatReasons">
        </div>
    </div>
</div>

<div id="studyLeaveSection" style="display: none;">
    <div class="form-row">
        <div class="form-group col-md-6">
            <label class="col-form-label">In case of Study Leave:</label>
            <select name="sickLeave" class="form-control">
                <option></option>
                <option value="Completion of Masters Degree">Completion of Master's Degree</option>
                <option value="BAR/Board Examination Review Other">BAR/Board Examination Review Other</option>
            </select>
        </div>
        <div class="form-group col-md-6">
            <label class="col-form-label">Purpose:</label>
            <input type="text" class="form-control" name="leaveStatReasons">
        </div>
    </div>
</div>

<input type="text" name="vlTotal" value="<?php echo $data1[0]->vlTotal; ?>" hidden>
<input type="text" name="slTotal" value="<?php echo $data1[0]->slTotal; ?>" hidden>

<!-- Hidden fields to store values when sections are hidden -->
<input type="hidden" name="sickLeave" id="hiddenSickLeave">
<input type="hidden" name="leaveStatReasons" id="hiddenLeaveStatReasons">
<input type="hidden" name="spentPlace" id="hiddenSpentPlace">
<input type="hidden" name="abroad" id="hiddenAbroad">






                                                    <div class="form-row">
                                                <div class="form-group col-md-3">
                                                    <label for="daysApplied" class="col-form-label">No. of Days Applied</label>
                                                    <input type="number" id="daysApplied" class="form-control" min="1" required name="daysApplied">
                                                </div>

                                                <div class="form-group col-md-3">
                                                    <label for="dateFrom" class="col-form-label">From</label>
                                                    <input type="date" id="dateFrom" class="form-control" required name="dateFrom">
                                                </div>

                                                <div class="form-group col-md-3">
                                                    <label for="dateTo" class="col-form-label">To</label>
                                                    <input type="date" id="dateTo" class="form-control" required name="dateTo">
                                                </div>

                                                <div class="form-group col-md-3">
                                                <label for="inputEmail4" class="col-form-label">Commutation</label>
                                                    <select name="commutation" class="form-control" >
                                                        <option></option>
                                                        <option value="Requested">Requested</option>
                                                        <option value="Not Requested">Not Requested</option>
                                                        
													</select>
                                                </div>
                                            </div>


                                               <div class="form-row">
                                            <div class="form-group col-md-12">
                                                <label class="col-form-label">Leave Attachment</label>
                                                <input type="file" class="form-control" name="leaveAttachment">
                                            </div>
                                        </div>

                                            <script>
                                                const daysAppliedInput = document.getElementById('daysApplied');
                                                const dateFromInput = document.getElementById('dateFrom');
                                                const dateToInput = document.getElementById('dateTo');

                                                function calculateDateTo() {
                                                    const daysApplied = parseInt(daysAppliedInput.value, 10);
                                                    const dateFrom = new Date(dateFromInput.value);

                                                    if (!isNaN(daysApplied) && !isNaN(dateFrom.getTime())) {
                                                        // Include the start date in the count
                                                        dateFrom.setDate(dateFrom.getDate() + (daysApplied - 1));

                                                        // Format the date as YYYY-MM-DD
                                                        const year = dateFrom.getFullYear();
                                                        const month = String(dateFrom.getMonth() + 1).padStart(2, '0');
                                                        const day = String(dateFrom.getDate()).padStart(2, '0');
                                                        dateToInput.value = `${year}-${month}-${day}`;
                                                    }
                                                }

                                                daysAppliedInput.addEventListener('input', calculateDateTo);
                                                dateFromInput.addEventListener('input', calculateDateTo);
                                            </script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const leaveTypeSelect = document.querySelector("select[name='leaveType']");
        const vacationSpecialPrivilegeSection = document.getElementById("vacationSpecialPrivilegeSection");
        const sickLeaveSection = document.getElementById("sickLeaveSection");
        const specialLeaveWomenSection = document.getElementById("specialLeaveWomenSection");
        const studyLeaveSection = document.getElementById("studyLeaveSection");

        // Get all input fields
        const sickLeaveInput = document.querySelector("select[name='sickLeave']");
        const leaveStatReasonsInput = document.querySelector("input[name='leaveStatReasons']");
        const abroadInput = document.querySelector("select[name='abroad']");
        const spentPlaceInput = document.querySelector("input[name='spentPlace']");

        // Get hidden input fields
        const hiddenSickLeave = document.getElementById("hiddenSickLeave");
        const hiddenLeaveStatReasons = document.getElementById("hiddenLeaveStatReasons");
        const hiddenSpentPlace = document.getElementById("hiddenSpentPlace");
        const hiddenAbroad = document.getElementById("hiddenAbroad");

        function handleLeaveTypeChange() {
            const selectedLeave = leaveTypeSelect.value;

            // Show/hide sections based on selected leave type
            sickLeaveSection.style.display = selectedLeave === "Sick Leave" ? "block" : "none";
            specialLeaveWomenSection.style.display = selectedLeave === "Special Leave Benefits for Women" ? "block" : "none";
            studyLeaveSection.style.display = selectedLeave === "Study Leave" ? "block" : "none";

            vacationSpecialPrivilegeSection.style.display =
                selectedLeave !== "Sick Leave" && selectedLeave !== "Special Leave Benefits for Women" && selectedLeave !== "Study Leave"
                ? "block"
                : "none";

            // Update hidden input fields when sections are hidden
            hiddenSickLeave.value = sickLeaveSection.style.display === "none" ? "" : sickLeaveInput.value;
            hiddenLeaveStatReasons.value = leaveStatReasonsInput.value;
            hiddenSpentPlace.value = vacationSpecialPrivilegeSection.style.display === "none" ? "" : spentPlaceInput.value;
            hiddenAbroad.value = vacationSpecialPrivilegeSection.style.display === "none" ? "" : abroadInput.value;
        }

        function handleFieldChanges() {
            hiddenSickLeave.value = sickLeaveInput.value;
            hiddenLeaveStatReasons.value = leaveStatReasonsInput.value;
            hiddenSpentPlace.value = spentPlaceInput.value;
            hiddenAbroad.value = abroadInput.value;
        }

        // Event listeners
        leaveTypeSelect.addEventListener("change", handleLeaveTypeChange);
        sickLeaveInput.addEventListener("change", handleFieldChanges);
        leaveStatReasonsInput.addEventListener("input", handleFieldChanges);
        spentPlaceInput.addEventListener("input", handleFieldChanges);
        abroadInput.addEventListener("change", handleFieldChanges);

        // Run function on load
        handleLeaveTypeChange();
    });
</script>



                                                    <input type="submit" name="submit" class="btn btn-primary" value="Submit">
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- end row -->
                            </div>
                        </div>
                        <!-- /.modal-content -->
                    </div>
                    <!-- /.modal-dialog -->
                </div>
                <!-- /.modal -->