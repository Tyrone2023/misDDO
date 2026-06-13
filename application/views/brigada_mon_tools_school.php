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

    .modern-hero {
        background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 55%, #0ea5e9 100%);
        border-radius: 22px;
        padding: 28px 30px;
        margin-bottom: 24px;
        color: #fff;
        box-shadow: 0 18px 40px rgba(37, 99, 235, 0.18);
        position: relative;
        overflow: hidden;
    }

    .modern-hero::before {
        content: '';
        position: absolute;
        top: -60px;
        right: -60px;
        width: 180px;
        height: 180px;
        border-radius: 50%;
        background: rgba(255,255,255,0.10);
    }

    .modern-hero::after {
        content: '';
        position: absolute;
        bottom: -70px;
        left: -40px;
        width: 140px;
        height: 140px;
        border-radius: 50%;
        background: rgba(255,255,255,0.08);
    }

    .modern-hero-content {
        position: relative;
        z-index: 2;
    }

    .modern-hero h2 {
        margin: 0;
        font-size: 28px;
        font-weight: 800;
        letter-spacing: -0.02em;
        color: #fff;
    }

    .modern-hero p {
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

    .modern-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        border: 1px solid #e5edf8;
        border-radius: 18px;
        overflow: hidden;
        background: #fff;
        margin-bottom: 0;
    }

    .modern-table thead th {
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

    .modern-table tbody td {
        padding: 14px;
        vertical-align: middle;
        border-top: 1px solid #edf2f7;
        color: #1e293b;
        font-size: 14px;
        background: #fff;
    }

    .modern-table tbody tr {
        transition: all 0.18s ease;
    }

    .modern-table tbody tr:hover td {
        background: #f8fbff;
    }

    .modern-table tbody td:first-child {
        width: 80px;
        font-weight: 700;
        color: #2563eb;
    }

    .school-name {
        font-weight: 600;
        color: #0f172a;
    }

    .action-center {
        text-align: center;
    }

    .btn-submit-modern,
    .btn-view-modern {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 96px;
        padding: 9px 16px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none !important;
        transition: all 0.18s ease;
        border: none;
        color: #fff !important;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.10);
    }

    .btn-submit-modern {
        background: linear-gradient(135deg, #16a34a, #15803d);
    }

    .btn-submit-modern:hover {
        transform: translateY(-1px);
        box-shadow: 0 12px 22px rgba(22, 163, 74, 0.22);
        color: #fff !important;
    }

    .btn-view-modern {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
    }

    .btn-view-modern:hover {
        transform: translateY(-1px);
        box-shadow: 0 12px 22px rgba(37, 99, 235, 0.24);
        color: #fff !important;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
    }

    .status-submitted {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .status-pending {
        background: #dcfce7;
        color: #15803d;
    }

    @media (max-width: 767px) {
        .modern-hero {
            padding: 22px 18px;
            border-radius: 18px;
        }

        .modern-hero h2 {
            font-size: 22px;
        }

        .modern-card-header {
            padding: 18px;
        }

        .modern-table-wrap {
            padding: 14px;
        }

        .modern-table thead th,
        .modern-table tbody td {
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
                    <div class="modern-hero">
                        <div class="modern-hero-content">
                            <h2>
                                School List
                                <?php if ($this->session->position === 'socmob') { ?>
                                    - <?= $district->discription; ?> District
                                <?php } ?>
                            </h2>
                            <p>Manage school submissions and quickly access submission records in a cleaner interface.</p>
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

                            <div class="modern-card-header">
                                <div>
                                    <h4>School Submission Directory</h4>
                                    <p>View the schools under this district and check whether their reports are already submitted.</p>
                                </div>
                                <div class="modern-badge">
                                    Total Schools: <?= count($school); ?>
                                </div>
                            </div>

                            <div class="modern-table-wrap table-responsive">
                                <table class="table modern-table mb-0">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>School Name</th>
                                            <th class="text-center">Submitted</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                            $c = 1;
                                            foreach($school as $row){
                                                $spc_submit = $this->Common->two_cond_row('brigada_monitored','school_id',$row->schoolID,'fy',date('Y'));
                                        ?>
                                            <tr>
                                                <td><?= $c++; ?></td>
                                                <td>
                                                    <span class="school-name"><?= $row->schoolName; ?></span>
                                                </td>
                                                <td class="action-center">
                                                    <?php if(empty($spc_submit)){ ?>
                                                        <a target="_blank"
                                                           href="<?= base_url(); ?>Brigada/brigada_mon_tools_submit/<?= $row->schoolID; ?>/<?= $this->uri->segment(3); ?>"
                                                           class="btn-submit-modern">
                                                            Submit
                                                        </a>
                                                    <?php } else { ?>
                                                        <a target="_blank"
                                                           href="<?= base_url(); ?>Brigada/brigada_mon_tools_submit/<?= $row->schoolID; ?>/<?= $this->uri->segment(3); ?>"
                                                           class="btn-view-modern">
                                                            View
                                                        </a>
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
            <!-- end row -->

        </div>
        <!-- end container-fluid -->

    </div>
    <!-- end content -->

    <?php include('templates/footer.php'); ?>
</div>