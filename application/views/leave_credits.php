<?php include('templates/head.php'); ?>
<?php include('templates/header.php'); ?>

<div class="content-page">
    <div class="content">

        <!-- Start Content-->
        <div class="container-fluid">

            <!-- Page Title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body table-responsive">
                            <h4 class="header-title mb-4">COC Credits</h4>

                            <?php if ($this->session->flashdata('success')) : ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <?= $this->session->flashdata('success'); ?>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            <?php endif; ?>

                            <?php if ($this->session->flashdata('danger')) : ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <?= $this->session->flashdata('danger'); ?>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            <?php endif; ?>

                            <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#addCOCModal">Add COC Credit</button>

                            <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
    <tr>
        <th>Acitivy Title</th>
        <th>Activity Date(From)</th>
        <th>Activity Date(To)</th>
        <th style='text-align:center'>Number of Days</th>
        <th style='text-align:center'>File Attachment</th>
        <th style='text-align:center'>Action</th> <!-- New Column -->
    </tr>
</thead>
<tbody>
<?php foreach ($data as $row) : ?>
    <tr>
        <td><?= htmlspecialchars($row->act_attend) ?></td>
        <td><?= htmlspecialchars($row->act_date) ?></td>
        <td><?= htmlspecialchars($row->to_date) ?></td>
        <td style='text-align:center'><?= htmlspecialchars($row->noDays) ?></td>
        <td style='text-align:center'>
            <?php if (!empty($row->fileAttach)) : ?>
                <a href="<?= base_url('uploads/' . htmlspecialchars($row->fileAttach)) ?>" target='_blank' style='text-decoration:none; color:blue;'>View File</a>
            <?php else : ?>
                No File
            <?php endif; ?>
        </td>
        <td style='text-align:center'>

            <?php if ($row->cocStat === "For evaluation") : ?>
                <button class="btn btn-warning">For Evaluation</button>
                <button class="btn btn-danger cancel-btn" data-id="<?= $row->cocID ?>">Cancel</button>
            
            <?php elseif ($row->cocStat === "Approved") : ?>
                <button class="btn btn-success">Approved</button>
            
            <?php elseif ($row->cocStat === "Disapproved") : ?>
                <button class="btn btn-danger reason-btn" data-id="<?= $row->cocID ?>" data-reason="<?= htmlspecialchars($row->reason) ?>" data-toggle="modal" data-target="#reasonModal">Disapproved</button>
            <?php endif; ?>
            
        </td>
    </tr>
<?php endforeach; ?>
</tbody>

<!-- Modal for Disapproved Reason -->
<div class="modal fade" id="reasonModal" tabindex="-1" role="dialog" aria-labelledby="reasonModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reasonModalLabel">Disapproval Reason</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="reasonText"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Show reason in modal
    document.querySelectorAll('.reason-btn').forEach(button => {
        button.addEventListener('click', function () {
            let reason = this.getAttribute('data-reason');
            document.getElementById('reasonText').innerText = reason;
        });
    });

    // Handle Cancel Button
    document.querySelectorAll('.cancel-btn').forEach(button => {
        button.addEventListener('click', function () {
            let cocID = this.getAttribute('data-id');
            if (confirm("Are you sure you want to cancel this data?")) {
                fetch("<?= base_url('Page/delete_leave_coc') ?>", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: "cocID=" + cocID
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("Data canceled successfully!");
                        location.reload();
                    } else {
                        alert("Failed to cancel data.");
                    }
                });
            }
        });
    });
</script>


                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="modal fade" id="addCOCModal" tabindex="-1" role="dialog" aria-labelledby="addCOCModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document"> <!-- Added modal-lg for large modal -->
        <div class="modal-content">
            <form action="<?= base_url('Page/addCOC') ?>" method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCOCModalLabel">Add COC Credit</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                   
                           <input type="hidden" class="form-control" name="IDNumber" id="IDNumber" value="<?= $this->session->userdata('username') ?>" readonly>
                 

                    <div class="row">
                        <!-- Activity Attendance -->
                        <div class="col-md-8 form-group">
                            <label for="act_attend">Activity Attended</label>
                            <input type="text" class="form-control" name="act_attend" id="act_attend" required>
                        </div>

                        <!-- Number of Days -->
                        <div class="col-md-4 form-group">
                            <label for="noDays">Number of Days</label>
                            <input type="number" class="form-control" name="noDays" id="noDays" min="1" required>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Activity Date (From) -->
                        <div class="col-md-4 form-group">
                            <label for="act_date">Activity Date (From)</label>
                            <input type="date" class="form-control" name="act_date" id="act_date" required>
                        </div>

                        <!-- Activity Date (To) - Initially hidden -->
                        <div class="col-md-4 form-group" id="toDateContainer" style="display: none;">
                            <label for="to_date">Activity Date (To)</label>
                            <input type="date" class="form-control" name="to_date" id="to_date">
                        </div>

                        <!-- File Attachment -->
                        <div class="col-md-8 form-group" id="fileContainer">
                            <label for="fileAttach">Attachment</label>
                            <input type="file" class="form-control" name="fileAttach" id="fileAttach" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required>
                        </div>
                    </div>
                </div>
<input type="text" name="cocStat" value="For evaluation" hidden>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const noDays = document.getElementById("noDays");
    const actDate = document.getElementById("act_date");
    const toDateContainer = document.getElementById("toDateContainer");
    const toDate = document.getElementById("to_date");
    const fileContainer = document.getElementById("fileContainer");

    noDays.addEventListener("input", function () {
        let days = parseInt(noDays.value, 10);

        if (days > 1) {
            toDateContainer.style.display = "block"; // Show "To Date" field
            fileContainer.classList.replace("col-md-8", "col-md-4"); // Change attachment to 4 cols

            if (actDate.value) {
                let startDate = new Date(actDate.value);
                startDate.setDate(startDate.getDate() + (days - 1)); // Add (days - 1) to From Date
                toDate.value = startDate.toISOString().split("T")[0]; // Set To Date
            }
        } else {
            toDateContainer.style.display = "none"; // Hide "To Date"
            fileContainer.classList.replace("col-md-4", "col-md-8"); // Reset attachment to 8 cols
            toDate.value = ""; // Clear To Date
        }
    });

    // Update To Date when From Date changes
    actDate.addEventListener("change", function () {
        if (noDays.value > 1) {
            let startDate = new Date(actDate.value);
            startDate.setDate(startDate.getDate() + (parseInt(noDays.value, 10) - 1));
            toDate.value = startDate.toISOString().split("T")[0];
        }
    });
});
</script>





<link href="<?= base_url(); ?>assets/libs/select2/select2.min.css" rel="stylesheet" />
<script src="<?= base_url(); ?>assets/libs/select2/select2.min.js"></script>


<script>
  $(document).ready(function () {
    // Initialize Select2
    $('#IDNumber').select2({
        placeholder: "Select an employee",
        width: '100%'
    });
});
</script>

    <?php include('templates/footer.php'); ?>
</div>
