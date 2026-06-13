<?php include('templates/head.php'); ?>
<link href="<?= base_url(); ?>assets/libs/custombox/custombox.min.css" rel="stylesheet" type="text/css">
<?php include('templates/header.php'); ?>

<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <button type="button" class="btn btn-primary waves-effect waves-light" data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="#addDiseaseModal">Add New</button>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>

            <?php if ($this->session->flashdata('success')) : ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <?= $this->session->flashdata('success'); ?>
                </div>
            <?php endif; ?>

            <?php if ($this->session->flashdata('error_msg')) : ?> <!-- Updated key to 'error_msg' -->
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <?= $this->session->flashdata('error_msg'); ?> <!-- Display the error message -->
                </div>
            <?php endif; ?>


            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body table-responsive">
                            <h4 class="header-title mb-4">Diseases Settings</h4>
                            <div class="table-responsive">
                                <table id="datatable" class="table table-bordered dt-responsive nowrap">
                                    <thead>
                                        <tr>
                                            <th>Category</th>
                                            <th>Diseases</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($data as $row) : ?>
                                            <tr>
                                                <td><?= $row->category; ?></td>
                                                <td><?= $row->disease; ?></td>
                                                <td>
                                                    <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editDiseaseModal<?= $row->dID; ?>">Edit</button>
                                                    <a href="<?= base_url('Page/delete_medDisease/' . $row->dID); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this disease?');">Delete</a>
                                                </td>
                                            </tr>

                                            <!-- Edit Modal -->
                                            <div id="editDiseaseModal<?= $row->dID; ?>" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Edit Disease</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <form action="<?= base_url('Page/update_medDisease'); ?>" method="post">
                                                            <div class="modal-body">
                                                                <input type="hidden" name="dID" value="<?= $row->dID; ?>">
                                                                <div class="form-group">
                                                                    <label for="category">Category</label>
                                                                    <input type="text" class="form-control" name="category" value="<?= $row->category; ?>" required>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="disease">Disease</label>
                                                                    <input type="text" class="form-control" name="disease" value="<?= $row->disease; ?>" required>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                <button type="submit" class="btn btn-primary">Update</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add New Disease Modal -->
<div id="addDiseaseModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Disease</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('Page/save_medDisease'); ?>" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="category">Category</label>
                        <input type="text" class="form-control" name="category" required>
                    </div>
                    <div class="form-group">
                        <label for="disease">Disease</label>
                        <input type="text" class="form-control" name="disease" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include('templates/footer.php'); ?>

<script>
    $(document).ready(function() {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>