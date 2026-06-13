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
                        <!-- <a href="<?= site_url('travel/create') ?>" class="btn btn-primary">Add New Request</a> -->

                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body table-responsive">

                            <?php if ($this->session->flashdata('success')): ?>
                                <div class="alert alert-success"><?= $this->session->flashdata('success'); ?></div>
                            <?php elseif ($this->session->flashdata('error')): ?>
                                <div class="alert alert-danger"><?= $this->session->flashdata('error'); ?></div>
                            <?php endif; ?>

                            <h4 class="header-title mb-4">Travel Requests</h4>
                            <table class="table mb-0">
                                <thead>
                                    <tr>
                                        <!-- <th>ID</th> -->
                                        <th>Employee Name</th>
                                        <th>Purpose</th>
                                        <th>Destination</th>
                                        <th>Departure</th>
                                        <th>Return</th>
                                        <th>Status</th>
                                        <th>Date Encoded</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($requests as $row): ?>
                                        <tr>
                                            <!-- <td><?= $row->id ?></td> -->
                                            <td><?= $row->staff_name ?></td>
                                            <td><?= $row->purpose ?></td>
                                            <td><?= $row->destination ?></td>
                                            <td><?= $row->departure_date ?></td>
                                            <td><?= $row->return_date ?></td>
                                            <td><?= $row->status ?></td>
                                            <td><?= $row->date_created ?></td>
                                            <td>

                                                <a href="<?= site_url('travel/edit_stat/' . $row->id) ?>" class="btn btn-sm btn-primary">Approve</a>
                                                <!-- Button to trigger modal -->
                                                <button class="btn btn-sm btn-danger return-btn" data-id="<?= $row->id ?>">Return</button>

                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                            <!-- Return Modal -->
                            <div class="modal fade" id="returnModal" tabindex="-1" role="dialog" aria-labelledby="returnModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <form id="returnForm">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="returnModalLabel">Return Request</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" name="request_id" id="request_id">
                                                <div class="form-group">
                                                    <label for="remarks">Reason for return</label>
                                                    <textarea name="remarks" id="remarks" class="form-control" rows="4" required></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-danger">Submit</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
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

    <script>
        $(document).ready(function() {
            $('.return-btn').click(function() {
                if (confirm('Return this request?')) {
                    var id = $(this).data('id');
                    $('#request_id').val(id);
                    $('#returnModal').modal('show');
                }
            });

            $('#returnForm').submit(function(e) {
                e.preventDefault();
                var formData = $(this).serialize();

                $.post("<?= site_url('travel/return_travel') ?>", formData, function(response) {
                    if (response.status === 'success') {
                        alert('Request returned successfully.');
                        location.reload(); // or redirect if needed
                    } else {
                        alert('Failed to return request.');
                    }
                }, 'json');
            });
        });
    </script>