<?php include(APPPATH.'views/templates/head.php'); ?>
<?php include(APPPATH.'views/templates/header.php'); ?>
<?php $can_manage_partners = isset($can_manage_partners) ? $can_manage_partners : ($this->session->userdata('position') !== 'School'); ?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

    body {
        font-family: 'Inter', sans-serif;
        background: linear-gradient(180deg, #f4f8ff 0%, #eef5ff 100%);
    }

    .content-page {
        background: transparent;
    }

    .modern-toolbar {
        background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 55%, #0ea5e9 100%);
        border-radius: 22px;
        padding: 26px 28px;
        margin-bottom: 24px;
        color: #fff;
        box-shadow: 0 18px 40px rgba(37, 99, 235, 0.18);
        position: relative;
        overflow: hidden;
    }

    .modern-toolbar::before {
        content: '';
        position: absolute;
        top: -60px;
        right: -60px;
        width: 180px;
        height: 180px;
        border-radius: 50%;
        background: rgba(255,255,255,0.10);
    }

    .modern-toolbar::after {
        content: '';
        position: absolute;
        bottom: -70px;
        left: -40px;
        width: 140px;
        height: 140px;
        border-radius: 50%;
        background: rgba(255,255,255,0.08);
    }

    .modern-toolbar-content {
        position: relative;
        z-index: 2;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }

    .modern-toolbar h2 {
        margin: 0;
        font-size: 28px;
        font-weight: 800;
        letter-spacing: -0.02em;
        color: #fff;
    }

    .modern-toolbar p {
        margin: 8px 0 0;
        font-size: 14px;
        color: rgba(255,255,255,0.88);
    }

    .btn-modern-add {
        border: none;
        background: rgba(255,255,255,0.16);
        color: #fff !important;
        padding: 11px 18px;
        border-radius: 14px;
        font-size: 14px;
        font-weight: 700;
        text-decoration: none !important;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.12);
        backdrop-filter: blur(8px);
        transition: all 0.18s ease;
    }

    .btn-modern-add:hover {
        transform: translateY(-1px);
        background: rgba(255,255,255,0.22);
        color: #fff !important;
    }

    .modern-alert {
        border: none;
        border-radius: 14px;
        padding: 14px 18px;
        font-size: 14px;
        font-weight: 500;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
    }

    .modern-card {
        border: none;
        border-radius: 22px;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 16px 45px rgba(15, 23, 42, 0.08);
    }

    .modern-card .card-body {
        padding: 0;
    }

    .modern-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
        padding: 22px 24px;
        border-bottom: 1px solid #e9f0fb;
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        flex-wrap: wrap;
    }

    .modern-card-header h4 {
        margin: 0;
        font-size: 20px;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: -0.02em;
    }

    .modern-card-header p {
        margin: 6px 0 0;
        color: #64748b;
        font-size: 13px;
    }

    .modern-badge {
        display: inline-flex;
        align-items: center;
        padding: 8px 14px;
        border-radius: 999px;
        background: #dbeafe;
        color: #1d4ed8;
        font-size: 12px;
        font-weight: 700;
    }

    .modern-table-wrap {
        padding: 20px 24px 24px;
    }

    #datatable {
        width: 100% !important;
        border-collapse: separate !important;
        border-spacing: 0;
        border: 1px solid #e5edf8;
        border-radius: 18px;
        overflow: hidden;
        background: #fff;
        margin-bottom: 0;
    }

    #datatable thead th {
        background: linear-gradient(135deg, #1e3a8a, #2563eb);
        color: #fff;
        border: none !important;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 15px 14px;
        vertical-align: middle;
    }

    #datatable tbody td {
        padding: 14px;
        vertical-align: middle;
        border-top: 1px solid #edf2f7 !important;
        color: #1e293b;
        font-size: 14px;
        background: #fff;
    }

    #datatable tbody tr {
        transition: all 0.18s ease;
    }

    #datatable tbody tr:hover td {
        background: #f8fbff;
    }

    #datatable tbody td:first-child {
        width: 60px;
        font-weight: 700;
        color: #2563eb;
    }

    .partner-name {
        font-weight: 700;
        color: #0f172a;
    }

    .partner-meta {
        color: #64748b;
        font-size: 12px;
        margin-top: 3px;
    }

    .type-badge {
        display: inline-block;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
        line-height: 1.2;
    }

    .type-general {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .type-specific {
        background: #e0f2fe;
        color: #0369a1;
    }

    .logo-thumb {
        width: 54px;
        height: 54px;
        border-radius: 14px;
        border: 1px solid #dbe7f5;
        background: #f8fbff;
        object-fit: cover;
        padding: 4px;
        box-shadow: 0 6px 16px rgba(15, 23, 42, 0.06);
    }

    .no-logo {
        width: 54px;
        height: 54px;
        border-radius: 14px;
        background: #eff6ff;
        color: #94a3b8;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 700;
        border: 1px dashed #bfd6f6;
    }

    .action-buttons {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .btn-action {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none !important;
        color: #fff !important;
        transition: all 0.18s ease;
        box-shadow: 0 8px 18px rgba(15, 23, 42, 0.10);
    }

    .btn-action-edit {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
    }

    .btn-action-delete {
        background: linear-gradient(135deg, #ef4444, #dc2626);
    }

    .btn-action:hover {
        transform: translateY(-1px);
        color: #fff !important;
    }

    .dataTables_wrapper .dataTables_filter input {
        border: 1px solid #cbd5e1;
        border-radius: 12px;
        padding: 8px 12px;
        outline: none;
        margin-left: 8px;
        background: #fff;
    }

    .dataTables_wrapper .dataTables_filter input:focus {
        border-color: #60a5fa;
        box-shadow: 0 0 0 4px rgba(59,130,246,0.10);
    }

    .dataTables_wrapper .dataTables_length select {
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        padding: 6px 10px;
        background: #fff;
        outline: none;
    }

    .dataTables_wrapper .dataTables_info {
        color: #64748b;
        font-size: 13px;
        padding-top: 16px !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        border-radius: 10px !important;
        border: none !important;
        margin: 0 3px;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current,
    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
        background: linear-gradient(135deg, #2563eb, #1d4ed8) !important;
        color: #fff !important;
        border: none !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #dbeafe !important;
        color: #1d4ed8 !important;
        border: none !important;
    }

    /* MODAL */
    .modal-content {
        border: none;
        border-radius: 22px;
        overflow: hidden;
        box-shadow: 0 22px 60px rgba(15, 23, 42, 0.18);
    }

    .modal-header {
        background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 55%, #0ea5e9 100%) !important;
        border-bottom: none;
        padding: 18px 22px;
    }

    .modal-title {
        color: #fff;
        font-weight: 800;
        font-size: 18px;
    }

    .modal-header .close {
        color: #fff;
        opacity: 1;
        text-shadow: none;
    }

    .modal-body {
        padding: 24px 24px 10px;
        background: #f8fbff;
    }

    .modal-footer {
        border-top: 1px solid #e5edf8;
        padding: 16px 24px 22px;
        background: #fff;
    }

    .form-group label {
        font-weight: 700;
        color: #1e293b;
        font-size: 13px;
        margin-bottom: 8px;
    }

    .form-control,
    select.form-control {
        border: 1px solid #cbd5e1;
        border-radius: 12px;
        min-height: 44px;
        box-shadow: none;
        font-size: 14px;
        color: #0f172a;
        background: #fff;
    }

    .form-control:focus,
    select.form-control:focus {
        border-color: #60a5fa;
        box-shadow: 0 0 0 4px rgba(59,130,246,0.10);
    }

    .btn-save-modern {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        border: none;
        color: #fff;
        font-weight: 700;
        padding: 10px 20px;
        border-radius: 12px;
        box-shadow: 0 10px 20px rgba(37, 99, 235, 0.18);
        transition: all 0.18s ease;
    }

    .btn-save-modern:hover {
        transform: translateY(-1px);
        color: #fff;
    }

    .validation-errors-wrap {
        margin-bottom: 16px;
    }

    .validation-errors-wrap p,
    .validation-errors-wrap li,
    .validation-errors-wrap div {
        color: #b91c1c;
        font-size: 13px;
    }

    @media (max-width: 767px) {
        .modern-toolbar {
            padding: 22px 18px;
            border-radius: 18px;
        }

        .modern-toolbar h2 {
            font-size: 22px;
        }

        .modern-card-header {
            padding: 18px;
        }

        .modern-table-wrap {
            padding: 14px;
        }

        #datatable thead th,
        #datatable tbody td {
            white-space: nowrap;
        }

        .modal-body,
        .modal-footer {
            padding-left: 16px;
            padding-right: 16px;
        }
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
                    <div class="modern-toolbar">
                        <div class="modern-toolbar-content">
                            <div>
                                <h2><?= $title; ?></h2>
                                <p>Manage partner records, contact details, types, and logo uploads in one place.</p>
                            </div>

                            <a href="#" class="btn-modern-add openModalBtn" data-toggle="modal" data-target="#myModal">
                                <i class="fas fa-plus-circle"></i> Add New
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <div class="col-12">

                    <?php if($this->session->flashdata('success')) : ?>
                        <div class="alert alert-success alert-dismissible fade show modern-alert" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <?= $this->session->flashdata('success'); ?>
                        </div>
                    <?php endif; ?>

                    <?php if($this->session->flashdata('danger')) : ?>
                        <div class="alert alert-danger alert-dismissible fade show modern-alert" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <?= $this->session->flashdata('danger'); ?>
                        </div>
                    <?php endif; ?>

                    <div class="card modern-card">
                        <div class="card-body">

                            <div class="modern-card-header">
                                <div>
                                    <h4>Partners Directory</h4>
                                    <p>View and maintain all registered company and individual partners.</p>
                                </div>
                                <div class="modern-badge">
                                    Total Partners: <?= count($data); ?>
                                </div>
                            </div>

                            <div class="modern-table-wrap table-responsive">
                                <table id="datatable" class="table table-bordered dt-responsive nowrap" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name of Partner<br>(Company or Individual)</th>
                                            <th>Address</th>
                                            <th>Contact Person</th>
                                            <th>Contact Number</th>
                                            <th>General Partner Type</th>
                                            <th>Specific Partner Type</th>
                                            <th>Logo</th>
                                            <?php if($can_manage_partners){ ?>
                                                <th>Action</th>
                                            <?php } ?>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php $c = 1; foreach($data as $row){ ?>
                                            <tr>
                                                <td><?= $c++; ?></td>
                                                <td>
                                                    <div class="partner-name"><?= $row->name; ?></div>
                                                </td>
                                                <td><?= $row->address; ?></td>
                                                <td><?= $row->contact_person; ?></td>
                                                <td><?= $row->contact; ?></td>
                                                <td>
                                                    <span class="type-badge type-general">
                                                        <?= str_replace('_', ' ', $row->general_type); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="type-badge type-specific">
                                                        <?= $row->specific_type; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if(!empty($row->file)){ ?>
                                                        <img class="logo-thumb" src="<?= base_url(); ?>uploads/brigada_partners_logo/<?= $row->file; ?>" alt="Partner Logo">
                                                    <?php } else { ?>
                                                        <div class="no-logo">No Logo</div>
                                                    <?php } ?>
                                                </td>
                                                <?php if($can_manage_partners){ ?>
                                                    <td>
                                                        <div class="action-buttons">
                                                            <a href="<?= base_url(); ?>Brigada/partners_update/<?= $row->id; ?>" class="btn-action btn-action-edit" title="Edit">
                                                                <i class="fas fa-pencil-alt"></i>
                                                            </a>
                                                            <a href="<?= base_url(); ?>Brigada/partners_delete/<?= $row->id; ?>" class="btn-action btn-action-delete" title="Delete" onclick="return confirm('Are you sure?');">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                <?php } ?>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
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

    <!-- sample modal content -->
    <div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content modal-lg">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Add New Partner</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>

                <?php
                    $attributes = array('class' => 'parsley-examples form-horizontal');
                    echo form_open_multipart('Brigada/settings_partners', $attributes);
                ?>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="validation-errors-wrap">
                                <?= validation_errors(); ?>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label">Name of Partner</label>
                                <div class="col-lg-8">
                                    <input type="text" name="name" class="form-control" placeholder="Enter partner name">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label">Address</label>
                                <div class="col-lg-8">
                                    <input type="text" name="address" class="form-control" placeholder="Enter address">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label">Contact Person</label>
                                <div class="col-lg-8">
                                    <input type="text" name="contact_person" class="form-control" placeholder="Enter contact person">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label">Contact Number</label>
                                <div class="col-lg-8">
                                    <input type="text" name="contact" class="form-control" placeholder="Enter contact number">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label">General Partner Type</label>
                                <div class="col-lg-8">
                                    <?php
                                        $arg = array(
                                            'Private_Sector' => 'Private Sector',
                                            'Public_Sector' => 'Public Sector',
                                            'Civil_Society_Organizations' => 'Civil Society Organizations',
                                            'International' => 'International'
                                        );
                                    ?>
                                    <select name="general_type" class="form-control" required>
                                        <option value="" disabled selected>Select General Partner Type</option>
                                        <?php foreach($arg as $key => $typeRow){ ?>
                                            <option value="<?= $key; ?>"><?= $typeRow; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label">Specific Partner Type</label>
                                <div class="col-lg-8">
                                    <?php $arg = array('Government', 'INGO-International Non-Government Organizations', 'Others'); ?>
                                    <select name="specific_type" class="form-control" required>
                                        <option value="" disabled selected>Select Specific Partner Type</option>
                                        <?php foreach($arg as $typeRow){ ?>
                                            <option value="<?= $typeRow; ?>"><?= $typeRow; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label">Logo</label>
                                <div class="col-lg-8">
                                    <input type="file" name="file" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end row -->
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-save-modern waves-effect waves-light">Save Partner</button>
                </div>

                </form>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->

<?php include(APPPATH.'views/templates/footer.php'); ?>
