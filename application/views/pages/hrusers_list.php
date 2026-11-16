<!-- ============================================================== -->
<!-- Manage Users (HR): personnel accounts, with applicant accounts on a tab -->
<!-- ============================================================== -->

<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <a href="<?= base_url(); ?>eval_user_add" class="btn btn-success waves-effect width-md waves-light">Add New User</a>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body table-responsive">
                            <h4 class="header-title mb-3"><?= html_escape($title); ?></h4>

                            <?php if ($this->session->flashdata('success')) : ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <?= $this->session->flashdata('success'); ?>
                                </div>
                            <?php endif; ?>
                            <?php if ($this->session->flashdata('danger')) : ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <?= $this->session->flashdata('danger'); ?>
                                </div>
                            <?php endif; ?>

                            <ul class="nav nav-tabs mb-3">
                                <li class="nav-item">
                                    <a class="nav-link <?= $tab === 'personnel' ? 'active' : ''; ?>" href="<?= base_url(); ?>hrusers">Personnel accounts</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?= $tab === 'applicants' ? 'active' : ''; ?>" href="<?= base_url(); ?>hrusers?tab=applicants">Applicant accounts</a>
                                </li>
                            </ul>

                            <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Username</th>
                                        <th>Position</th>
                                        <th>Manage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $row) { ?>
                                    <tr>
                                        <td><?= html_escape(trim(ucfirst($row->fname) . ' ' . ($row->mname !== '' ? ucfirst(mb_substr($row->mname, 0, 1)) . '. ' : '') . ucfirst($row->lname))); ?></td>
                                        <td><?= html_escape($row->username); ?></td>
                                        <td><?= html_escape($row->position); ?></td>
                                        <td>
                                            <a href="<?= base_url(); ?>hrusers/edit/<?= (int) $row->id; ?>" class="text-success"><i class="mdi mdi-account-edit"></i> Edit / Change password</a>
                                            <?php if ($tab === 'applicants') { ?>
                                            &nbsp;&nbsp;
                                            <a href="<?= base_url(); ?>user_delete/<?= (int) $row->id; ?>" onclick="return confirm('Delete this applicant account?');" class="text-danger"><i class="mdi mdi-delete-outline"></i> Delete</a>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
