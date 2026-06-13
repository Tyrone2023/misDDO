<?php include('templates/head.php'); ?>
<?php include('templates/header.php'); ?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

    body {
        font-family: 'Inter', sans-serif;
        background: linear-gradient(180deg, #f4f8ff 0%, #eef5ff 100%);
    }

    .content-page {
        background: transparent;
    }

    .page-hero {
        background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 55%, #0ea5e9 100%);
        border-radius: 22px;
        padding: 28px 30px;
        margin-bottom: 24px;
        color: #fff;
        box-shadow: 0 18px 40px rgba(37, 99, 235, 0.18);
        position: relative;
        overflow: hidden;
    }

    .page-hero::before {
        content: '';
        position: absolute;
        top: -60px;
        right: -60px;
        width: 180px;
        height: 180px;
        background: rgba(255,255,255,0.10);
        border-radius: 50%;
    }

    .page-hero::after {
        content: '';
        position: absolute;
        bottom: -70px;
        left: -40px;
        width: 140px;
        height: 140px;
        background: rgba(255,255,255,0.08);
        border-radius: 50%;
    }

    .page-hero-content {
        position: relative;
        z-index: 2;
    }

    .page-hero h2 {
        margin: 0;
        font-size: 28px;
        font-weight: 800;
        letter-spacing: -0.02em;
        color: #fff;
    }

    .page-hero p {
        margin: 8px 0 0;
        font-size: 14px;
        color: rgba(255,255,255,0.88);
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
        box-shadow: 0 16px 45px rgba(15, 23, 42, 0.08);
        background: #ffffff;
    }

    .modern-card .card-body {
        padding: 0;
    }

    .table-header-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
        padding: 22px 24px;
        border-bottom: 1px solid #e9f0fb;
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        flex-wrap: wrap;
    }

    .table-header-left h4 {
        margin: 0;
        font-size: 20px;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: -0.02em;
    }

    .table-header-left p {
        margin: 6px 0 0;
        color: #64748b;
        font-size: 13px;
    }

    .table-badge {
        display: inline-flex;
        align-items: center;
        padding: 8px 14px;
        border-radius: 999px;
        background: #dbeafe;
        color: #1d4ed8;
        font-size: 12px;
        font-weight: 700;
    }

    .table-responsive {
        padding: 20px 24px 24px;
    }

    #datatable {
        width: 100% !important;
        border-collapse: separate !important;
        border-spacing: 0;
        overflow: hidden;
        border-radius: 18px;
        border: 1px solid #e5edf8;
        background: #fff;
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
        font-weight: 700;
        color: #2563eb;
        width: 80px;
    }

    .district-name {
        font-weight: 600;
        color: #0f172a;
    }

    .btn-view-modern {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        border: none;
        color: #fff !important;
        padding: 9px 16px;
        font-size: 13px;
        font-weight: 700;
        border-radius: 12px;
        text-decoration: none !important;
        box-shadow: 0 8px 20px rgba(37, 99, 235, 0.20);
        transition: all 0.18s ease;
    }

    .btn-view-modern:hover {
        transform: translateY(-1px);
        box-shadow: 0 12px 24px rgba(37, 99, 235, 0.26);
        color: #fff !important;
    }

    .btn-view-modern i {
        font-size: 12px;
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
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.10);
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

    @media (max-width: 767px) {
        .page-hero {
            padding: 22px 18px;
            border-radius: 18px;
        }

        .page-hero h2 {
            font-size: 22px;
        }

        .table-header-bar {
            padding: 18px;
        }

        .table-responsive {
            padding: 14px;
        }

        #datatable thead th,
        #datatable tbody td {
            white-space: nowrap;
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

            <div class="row">
                <div class="col-12">
                    <div class="page-hero">
                        <div class="page-hero-content">
                            <h2>Monitoring Tool</h2>
                            <p>Manage and view district school records in a cleaner and more modern interface.</p>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($this->session->flashdata('success')) : ?>
                <div class="alert alert-success alert-dismissible fade show modern-alert" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <?= $this->session->flashdata('success'); ?>
                </div>
            <?php endif; ?>

            <?php if ($this->session->flashdata('danger')) : ?>
                <div class="alert alert-danger alert-dismissible fade show modern-alert" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <?= $this->session->flashdata('danger'); ?>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-12">
                    <div class="card modern-card">
                        <div class="card-body">

                            <div class="table-header-bar">
                                <div class="table-header-left">
                                    <h4>District Directory</h4>
                                    <p>Browse the list of districts and access their corresponding schools.</p>
                                </div>
                                <div class="table-badge">
                                    Total Districts: <?= count($district); ?>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table id="datatable" class="table dt-responsive nowrap" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>District</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                            $c = 1;
                                            foreach($district as $row){
                                                $ds = $this->Common->one_cond_count_row('schools','district',$row->discription);
                                        ?>
                                            <tr>
                                                <td><?= $c++; ?></td>
                                                <td>
                                                    <span class="district-name"><?= $row->discription; ?></span>
                                                </td>
                                                <td>
                                                    <a href="<?= base_url(); ?>Brigada/brigada_mon_tool_schools/<?= $row->id; ?>" class="btn-view-modern">
                                                        <i class="mdi mdi-eye-outline"></i> View
                                                    </a>
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
            <!-- end row -->

        </div>
        <!-- end container-fluid -->

    </div>
    <!-- end content -->

    <?php include('templates/footer.php'); ?>
</div>