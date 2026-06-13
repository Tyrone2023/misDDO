<?php include('templates/head.php'); ?> 
<link href="<?= base_url(); ?>assets/libs/custombox/custombox.min.css" rel="stylesheet" type="text/css">

<?php include('templates/header.php'); ?>     

<style>
    /* Modal Header */
.modal-header {
    background-color: #007bff;
    color: white;
    border-bottom: 2px solid #0056b3;
    font-weight: 600;
}

/* Modal Body */
.modal-body {
    font-size: 16px;
    font-family: 'Arial', sans-serif;
    color: #333;
}

.modal-body p {
    font-size: 18px;
    margin-bottom: 20px;
}

/* Buttons */
.btn-success {
    background-color:rgb(45, 178, 212);
    border-color:rgb(95, 40, 167);
    transition: all 0.3s ease;
}

.btn-success:hover {
    background-color:rgb(73, 87, 234);
    border-color:rgb(30, 75, 126);
}

.btn-danger {
    background-color: #dc3545;
    border-color: #dc3545;
    transition: all 0.3s ease;
}

.btn-danger:hover {
    background-color: #c82333;
    border-color: #bd2130;
}

.btn-outline-secondary {
    border-color: #ccc;
    color: #333;
}

.btn-outline-secondary:hover {
    background-color: #f8f9fa;
    color: #333;
}

/* Modal Footer */
.modal-footer {
    border-top: 2px solid #f1f1f1;
}

/* Center modal */
.modal-dialog-centered {
    display: flex;
    align-items: center;
    min-height: calc(100% - 1rem);
}

</style>

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

            <div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body table-responsive">
                <?php if (!empty($data)): ?>
                    <!-- Header section with APPROVE and DISAPPROVE links together -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="header-title">Service Record of: 
                            <strong>
                                <?php echo $data[0]->LastName; ?> 
                                <?php echo $data[0]->FirstName; ?> 
                                <?php echo $data[0]->MiddleName; ?>
                            </strong>
                        </h4>

                        <!-- APPROVE Button -->
                        <div class="btn-group">
    <button class="btn btn-success" data-toggle="modal" data-target="#approveModal">
        <i class="mdi mdi-file-document-box-check-outline"></i> APPROVE
    </button>

<!-- Modal for Approve -->
<div class="modal fade" id="approveModal" tabindex="-1" role="dialog" aria-labelledby="approveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0 rounded">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="approveModalLabel">Approval Confirmation</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="approveForm" method="POST">
                <div class="modal-body text-center">
                    <p class="mb-4">The request for the service record has been approved. Do you want to allow the employee to print it?</p>
                    <div class="form-group mt-4">
                        <button type="button" class="btn btn-lg btn-success mr-3" id="btnYes">Yes</button>
                        <button type="button" class="btn btn-lg btn-danger" id="btnNo">No</button>
                    </div>
                </div>
            </form>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


                            <!-- DISAPPROVE Button -->
                            <button class="btn btn-danger" data-toggle="modal" data-target="#disapproveModal">
                                <i class="mdi mdi-delete-forever"></i> DISAPPROVE
                            </button>
                        </div>
                    </div>

                    <!-- Table for service records -->
                    <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th colspan="2">SERVICE<p>(Inclusive Dates)</p></th>
                                <th colspan="3">RECORD OF APPOINTMENT</th>
                                <th colspan="1">OFFICE ENTITY</th>
                                <th>LV/ABS w/out Pay</th>
                                <th>Separation Cause/d</th>
                            </tr>
                            <tr>
                                <td>FROM</td>
                                <td>TO</td>
                                <td>Designation</td>
                                <td>Status</td>
                                <td>Annual Salary</td>
                                <td>(Station/Place of Assignment)</td>
                                <td></td>
                                <td></td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data as $row): ?>
                                <tr>
                                    <td><?php echo $row->appointDate; ?></td>
                                    <td><?php echo $row->endDate; ?></td>
                                    <td><?php echo $row->empPosition; ?></td>
                                    <td><?php echo $row->empStatus; ?></td>
                                    <td><?php echo $row->salary; ?></td>
                                    <td><?php echo $row->empStation; ?></td>
                                    <td><?php echo $row->lvwithoutpay; ?></td>
                                    <td><?php echo $row->separation; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No service record found for the provided ID.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Disapproval -->
<div class="modal fade" id="disapproveModal" tabindex="-1" role="dialog" aria-labelledby="disapproveModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="disapproveModalLabel">Reason for Disapproval</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="<?= base_url('Page/disapprove_request/' . $data[0]->id); ?>">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="disapprovalReason">Enter reason for disapproval:</label>
                        <textarea id="disapprovalReason" name="disapprovalReason" class="form-control" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Submit Disapproval</button>
                </div>
            </form>
        </div>
    </div>
</div>

        <!-- end container-fluid -->

    </div>
    <!-- end content -->

    <?php include('templates/footer.php'); ?>  
</div>
<script>
   // On Yes button click
$('#btnYes').click(function() {
    // Close the modal
    $('#approveModal').modal('hide');
    
    // Send AJAX request to update the data
    $.ajax({
        url: '<?= base_url('Page/update_purpose/' . $data[0]->id); ?>',
        type: 'POST',
        data: {
            forPrint: 1 // Yes means it's available for printing
        },
        success: function(response) {
            // Handle success
            var result = JSON.parse(response);
            if(result.status == 'success') {
                alert('Document has been approved and marked for printing!');
                // Redirect to sr_request_list page
                window.location.href = '<?= base_url('Page/sr_request_list'); ?>'; // Redirect after success
            } else {
                alert('Failed to update the record.');
            }
        }
    });
});

// On No button click
$('#btnNo').click(function() {
    // Close the modal
    $('#approveModal').modal('hide');
    
    // Send AJAX request to update the data
    $.ajax({
        url: '<?= base_url('Page/update_purpose/' . $data[0]->id); ?>',
        type: 'POST',
        data: {
            forPrint: 0 // No means it's not available for printing
        },
        success: function(response) {
            // Handle success
            var result = JSON.parse(response);
            if(result.status == 'success') {
                alert('Document has been approved but not marked for printing.');
                // Redirect to sr_request_list page
                window.location.href = '<?= base_url('Page/sr_request_list'); ?>'; // Redirect after success
            } else {
                alert('Failed to update the record.');
            }
        }
    });
});

</script>
