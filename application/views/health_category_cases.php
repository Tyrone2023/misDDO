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
                        <div class="page-title-right d-flex justify-content-end align-items-center">
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#addDiseaseModal">Add Disease</button>
                                <a href="<?= base_url(); ?>Page/Health" class="btn btn-sm btn-secondary">Back to Dashboard</a>
                            </div>
                        </div>
                        <h4 class="page-title">Diseases by Category</h4>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <?php if ($this->session->flashdata('danger')) : ?>
                <?= '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>'
                    . $this->session->flashdata('danger') .
                    '</div>';
                ?>
            <?php endif;  ?>
            <?php if ($this->session->flashdata('success')) : ?>
                <?= '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>'
                    . $this->session->flashdata('success') .
                    '</div>';
                ?>
            <?php endif;  ?>

            <?php $returnUrl = base_url('Page/health_category?category=' . rawurlencode($category)); ?>

            <div class="row">
                <div class="col-md-6 col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="text-muted">Category</div>
                            <h4 class="mb-0"><?= htmlspecialchars($category); ?></h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="text-muted">Total Diseases</div>
                            <h4 class="mb-0">
                                <span class="badge badge-primary badge-pill"><?= (int) $categoryTotal; ?></span>
                            </h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title mb-3">Disease List</h4>
                            <div class="table-responsive">
                                <table class="table table-sm table-striped table-hover mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th style="width: 80px;">No.</th>
                                            <th>Disease</th>
                                            <th style="width: 140px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($caseDiseaseTally)): ?>
                                            <?php $idx = 1; ?>
                                            <?php foreach ($caseDiseaseTally as $it): ?>
                                                <tr>
                                                    <td><?= $idx; ?></td>
                                                    <td><?= htmlspecialchars($it->disease); ?></td>
                                                    <td>
                                                        <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editDiseaseModal<?= $it->dID; ?>">Edit</button>
                                                        <a href="<?= base_url('Page/delete_medDisease/' . $it->dID . '?return_url=' . rawurlencode($returnUrl)); ?>"
                                                           class="btn btn-danger btn-sm"
                                                           onclick="return confirm('Are you sure you want to delete this disease?');">Delete</a>
                                                    </td>
                                                </tr>
                                                <div id="editDiseaseModal<?= $it->dID; ?>" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
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
                                                                    <input type="hidden" name="dID" value="<?= $it->dID; ?>">
                                                                    <input type="hidden" name="category" value="<?= htmlspecialchars($category); ?>">
                                                                    <input type="hidden" name="return_url" value="<?= htmlspecialchars($returnUrl); ?>">
                                                                    <div class="form-group">
                                                                        <label for="category">Category</label>
                                                                        <input type="text" class="form-control" value="<?= htmlspecialchars($category); ?>" readonly>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="disease">Disease</label>
                                                                        <input type="text" class="form-control" name="disease" value="<?= htmlspecialchars($it->disease); ?>" required>
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
                                                <?php $idx++; ?>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="3" class="text-center text-muted">No diseases found for selected category.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end row -->

            <div id="addDiseaseModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Add Disease</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="<?= base_url('Page/save_medDisease'); ?>" method="post">
                            <div class="modal-body">
                                <input type="hidden" name="category" value="<?= htmlspecialchars($category); ?>">
                                <input type="hidden" name="return_url" value="<?= htmlspecialchars($returnUrl); ?>">
                                <div class="form-group">
                                    <label for="category">Category</label>
                                    <input type="text" class="form-control" value="<?= htmlspecialchars($category); ?>" readonly>
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

        </div>
        <!-- end container-fluid -->

    </div>
    <!-- end content -->

    <?php include('templates/footer.php'); ?>
