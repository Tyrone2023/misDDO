<?php include('templates/head.php'); ?>
<?php include('templates/header.php'); ?>

<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>

            <!-- Flash Messages -->
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

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body table-responsive">
                            <h4 class="header-title mb-4">COC Credits</h4>

                            <table id="datatable" class="table table-bordered dt-responsive nowrap" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>Last Name</th>
                                        <th>First Name</th>
                                        <th>Middle Name</th>
                                        <th>Activity Date</th>
                                        <th style='text-align:center'>Number of Days</th>
                                        <th style='text-align:center'>File Attachment</th>
                                        <th style='text-align:center'>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($data as $row) : ?>
                                        <tr>
                                            <td><?= htmlspecialchars($row->LastName) ?></td>
                                            <td><?= htmlspecialchars($row->FirstName) ?></td>
                                            <td><?= htmlspecialchars($row->MiddleName) ?></td>
                                            <td><?= htmlspecialchars($row->act_date) ?></td>
                                            <td style='text-align:center'><?= htmlspecialchars($row->noDays) ?></td>
                                            <td style='text-align:center'>
                                                <?php if (!empty($row->fileAttach)) : ?>
                                                    <a href="<?= base_url('uploads/' . htmlspecialchars($row->fileAttach)) ?>" target='_blank' style='color:blue;'>View File</a>
                                                <?php else : ?>
                                                    No File
                                                <?php endif; ?>
                                            </td>
                                            <td style='text-align:center'>
    <?php if ($row->cocStat !== 'Approved' && $row->cocStat !== 'Disapproved') : ?>
        <!-- Approve Button -->
        <button class="btn btn-primary approve-btn" data-toggle="modal" data-target="#approveModal" 
                data-id="<?= $row->cocID; ?>">
            Approve
        </button>

        <!-- Disapprove Button -->
        <button class="btn btn-danger disapprove-btn" data-toggle="modal" data-target="#disapproveModal" 
                data-id="<?= $row->cocID; ?>">
            Disapprove
        </button>
    <?php endif; ?>
</td>

                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="approveModal" tabindex="-1" role="dialog" aria-labelledby="approveModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <form action="<?= base_url('Page/leave_credits_approval') ?>" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="approveModalLabel">COC APPROVAL</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="cocTotal">COC Total</label>
                            <input type="text" class="form-control" name="cocTotal" id="cocTotal" required>
                            <input type="hidden" name="cocID" id="modalCocID">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Approve</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="modal fade" id="disapproveModal" tabindex="-1" role="dialog" aria-labelledby="disapproveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document"> 
        <div class="modal-content">
            <form action="<?= base_url('Page/disapprove_leave_coc') ?>" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="disapproveModalLabel">Disapprove COC</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="cocID" id="disapproveCocID">

                    <div class="form-group">
                        <label for="reason">Reason for Disapproval</label>
                        <textarea class="form-control" name="reason" id="reason" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Disapprove</button>
                </div>
            </form>
        </div>
    </div>
</div>


    <?php include('templates/footer.php'); ?>
</div>

<link href="<?= base_url(); ?>assets/libs/select2/select2.min.css" rel="stylesheet" />
<script src="<?= base_url(); ?>assets/libs/select2/select2.min.js"></script>

<script>
$(document).ready(function () {
    $('#IDNumber').select2({
        placeholder: "Select an employee",
        width: '100%'
    });

    // Set COC ID dynamically when clicking "Approve"
    $('.approve-btn').on('click', function () {
        var cocID = $(this).data('id');
        $('#modalCocID').val(cocID);
    });
});

$(document).ready(function () {
      // Set COC ID for Disapprove Modal
      $('.disapprove-btn').click(function () {
          var cocID = $(this).data('id');
          $('#disapproveCocID').val(cocID);
      });
  });
</script>
