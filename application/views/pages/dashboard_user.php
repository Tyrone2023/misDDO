<style>
/* ---- Shared shell for the HR recruitment screens (Job Vacancies / Positions Settings) ---- */

.hrp-hero {
    background: linear-gradient(120deg, #1f3a5f 0%, #2c5282 55%, #3182ce 100%);
    border-radius: 14px;
    padding: 1.6rem 1.9rem;
    margin-bottom: 1.4rem;
    color: #fff;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 1.25rem;
    box-shadow: 0 10px 26px rgba(31, 58, 95, .22);
}
.hrp-hero-eyebrow {
    display: inline-flex; align-items: center; gap: .35rem;
    background: rgba(255,255,255,.18);
    border-radius: 999px;
    padding: .22rem .7rem;
    font-size: .7rem; font-weight: 600;
    text-transform: uppercase; letter-spacing: .06em;
}
.hrp-hero-title { margin: .6rem 0 .3rem; font-weight: 600; color: #fff; display: flex; align-items: center; gap: .5rem; font-size: 1.4rem; }
.hrp-hero-sub { margin: 0; opacity: .85; font-size: .875rem; max-width: 62ch; }
.hrp-hero-sub strong { color: #fff; }
.hrp-hero-stats { display: flex; gap: .75rem; flex-wrap: wrap; }
.hrp-stat {
    background: rgba(255,255,255,.13);
    border: 1px solid rgba(255,255,255,.18);
    border-radius: 11px;
    padding: .7rem 1.1rem;
    min-width: 104px;
    text-align: center;
}
.hrp-stat-value { display: block; font-size: 1.45rem; font-weight: 600; line-height: 1.15; }
.hrp-stat-label { display: block; font-size: .68rem; text-transform: uppercase; letter-spacing: .06em; opacity: .8; }
.hrp-dotsep { margin: 0 .4rem; opacity: .45; }

.hrp-card {
    background: #fff;
    border: 1px solid #e9edf2;
    border-radius: 14px;
    padding: 1.35rem;
    box-shadow: 0 2px 10px rgba(16, 30, 54, .05);
    margin-bottom: 1.4rem;
}
.hrp-card-head {
    display: flex; align-items: flex-start; justify-content: space-between;
    flex-wrap: wrap; gap: .75rem;
    margin-bottom: 1.1rem;
}
.hrp-card-title { margin: 0; font-size: 1.05rem; font-weight: 600; color: #313a46; }
.hrp-card-sub { margin: .25rem 0 0; font-size: .82rem; color: #98a6ad; }
.hrp-card-actions { display: flex; gap: .45rem; flex-wrap: wrap; align-items: center; }

/* ---- buttons ---- */
.hrp-btn {
    display: inline-flex; align-items: center; gap: .4rem;
    border: 1px solid #e4e9f0;
    background: #f7f9fc;
    color: #5c6873;
    border-radius: 8px;
    padding: .45rem .9rem;
    font-size: .82rem;
    font-weight: 500;
    line-height: 1.3;
    cursor: pointer;
    transition: all .15s ease;
}
.hrp-btn:hover { background: #eef2f8; color: #313a46; text-decoration: none; }
.hrp-btn-primary { background: #2c5282; border-color: #2c5282; color: #fff; }
.hrp-btn-primary:hover { background: #24446c; border-color: #24446c; color: #fff; }
.hrp-btn-success { background: #1e7d44; border-color: #1e7d44; color: #fff; }
.hrp-btn-success:hover { background: #196a39; border-color: #196a39; color: #fff; }
.hrp-btn-info { background: #1a7f8e; border-color: #1a7f8e; color: #fff; }
.hrp-btn-info:hover { background: #146874; border-color: #146874; color: #fff; }
.hrp-btn-teal { background: #0f766e; border-color: #0f766e; color: #fff; }
.hrp-btn-teal:hover { background: #0c5f59; border-color: #0c5f59; color: #fff; }
.hrp-btn-purple { background: #5b3fb0; border-color: #5b3fb0; color: #fff; }
.hrp-btn-purple:hover { background: #4a3390; border-color: #4a3390; color: #fff; }
.hrp-btn-warning { background: #c07d15; border-color: #c07d15; color: #fff; }
.hrp-btn-warning:hover { background: #a06811; border-color: #a06811; color: #fff; }
.hrp-btn-danger { background: #b03a3a; border-color: #b03a3a; color: #fff; }
.hrp-btn-danger:hover { background: #932e2e; border-color: #932e2e; color: #fff; }
.hrp-btn-ghost-danger { color: #b03a3a; border-color: #f0dcdc; background: #fdf5f5; }
.hrp-btn-ghost-danger:hover { background: #f8e7e7; color: #8f2c2c; }
.hrp-btn-sm { padding: .3rem .65rem; font-size: .78rem; border-radius: 7px; }

/* ---- tables ---- */
.hrp-table { border-collapse: separate !important; border-spacing: 0; width: 100%; }
.hrp-table thead th {
    border: none !important;
    border-bottom: 1px solid #e9edf2 !important;
    background: transparent;
    color: #7b8794;
    font-size: .7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .06em;
    padding: .7rem .75rem !important;
    white-space: nowrap;
}
.hrp-table tbody td {
    border: none !important;
    border-bottom: 1px solid #f1f4f8 !important;
    padding: .75rem !important;
    vertical-align: middle;
}
.hrp-table tbody tr:hover { background: #f9fbfd; }

.hrp-chip {
    display: inline-flex; align-items: center; gap: .3rem;
    background: #f1f4f8;
    border: 1px solid #e4e9f0;
    border-radius: 999px;
    padding: .16rem .6rem;
    font-size: .74rem;
    font-weight: 500;
    color: #4a5568;
    white-space: nowrap;
}
.hrp-chip-blue   { background: #e8f0fb; border-color: #d6e4f7; color: #2c5282; }
.hrp-chip-green  { background: #e6f4ec; border-color: #d2ebdd; color: #1e7d44; }
.hrp-chip-amber  { background: #fdf3e2; border-color: #f7e5c6; color: #a86c14; }
.hrp-chip-purple { background: #f0ebfb; border-color: #e2d9f7; color: #5b3fb0; }
.hrp-chip-grey   { background: #f1f4f8; border-color: #e4e9f0; color: #7b8794; }
.hrp-chip-red    { background: #fdf5f5; border-color: #f0dcdc; color: #b03a3a; }

@media (max-width: 767.98px) {
    .hrp-hero { padding: 1.25rem; }
    .hrp-hero-title { font-size: 1.15rem; }
    .hrp-stat { min-width: 88px; padding: .55rem .8rem; }
}
</style>

            <!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->

            <div class="content-page">
                <div class="content">

                    <!-- Start Content-->
                    <div class="container-fluid">
<br>
                        <!-- start page header -->
                        <div class="row">
                            <div class="col-12">
                                <div class="hrp-hero">
                                    <div class="hrp-hero-text">
                                        <!-- <span class="hrp-hero-eyebrow"><i class="mdi mdi-account-outline"></i> Dashboard</span> -->
                                         
                                        <h3 class="hrp-hero-title"><i class="mdi mdi-view-dashboard-outline"></i> User Dashboard</h3>
                                        <p class="hrp-hero-sub">
                                            Welcome to your dashboard. View job vacancies and manage your applications from here.
                                        </p>
                                    </div>
                                    <div class="hrp-hero-stats">
                                        <div class="hrp-stat">
                                            <span class="hrp-stat-value"><?= number_format($data1->num_rows()); ?></span>
                                            <span class="hrp-stat-label">Job Vacancies</span>
                                        </div>
                                        <div class="hrp-stat">
                                            <span class="hrp-stat-value"><?= number_format($data2->num_rows()); ?></span>
                                            <span class="hrp-stat-label">My Applications</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end page header -->

                            

                        <?php if ($is_endorser): ?>
<div class="row">
    <div class="col-md-12">
        <div class="hrp-card">
            <div class="hrp-card-head">
                <div>
                    <h4 class="hrp-card-title">Pending Task</h4>
                    <p class="hrp-card-sub">Leave applications awaiting your recommendation</p>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table hrp-table">
                    <thead>
                        <tr>
                            <th>Task</th>
                            <th class="text-center">Counts</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Leave Applications for Recommendation</td>
                            <td class="text-center">
                                <span class="hrp-chip hrp-chip-blue">
                                    <?= $data5; ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="<?= base_url(); ?>Page/pendingLeave" class="hrp-btn hrp-btn-primary hrp-btn-sm">
                                    <i class="mdi mdi-file-document-box-check-outline"></i> View List
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if ($is_approver): ?>
<div class="row">
    <div class="col-md-12">
        <div class="hrp-card">
            <div class="hrp-card-head">
                <div>
                    <h4 class="hrp-card-title">Pending Task</h4>
                    <p class="hrp-card-sub">Leave applications awaiting your approval</p>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table hrp-table">
                    <thead>
                        <tr>
                            <th>Task</th>
                            <th class="text-center">Counts</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Leave Applications for Approval</td>
                            <td class="text-center">
                                <span class="hrp-chip hrp-chip-blue">
                                    <?= $data7; ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="<?= base_url(); ?>Page/pendingLeave" class="hrp-btn hrp-btn-primary hrp-btn-sm">
                                    <i class="mdi mdi-file-document-box-check-outline"></i> View List
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
                      
                    </div>
                </div>
                        
                    

                </div>
                <!-- end content -->

                