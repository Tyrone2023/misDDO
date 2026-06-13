<?php
function h($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
$labels = $jobTypeLabels ?? [];
$assigned = $jobTypes ?? [];
$counts = $counts ?? ['validated' => 0, 'endorsed' => 0, 'rated' => 0, 'no_rater' => 0, 'dq' => 0];
?>

<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="page-title mb-0"><?= h($title ?? 'Secretariat Dashboard'); ?></h4>
                        <div>
                            <?php if (!empty($assigned)) : ?>
                                <span class="badge badge-primary">Assigned Levels: 
                                    <?php
                                        $names = [];
                                        foreach ($assigned as $jt) {
                                            $names[] = $labels[$jt] ?? $jt;
                                        }
                                        echo h(implode(', ', $names));
                                    ?>
                                </span>
                            <?php else : ?>
                                <span class="badge badge-warning">No level assigned</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (empty($assigned)) : ?>
                <div class="alert alert-warning">
                    You don't have any assigned levels yet. Please ask a Super Admin to assign levels on the <strong>Assign Secretariat Levels</strong> page.
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-3">
                    <div class="card mini-stat bg-primary text-white">
                        <div class="card-body">
                            <h6 class="text-uppercase mb-2">Validated</h6>
                            <h2 class="mb-0"><?= (int) $counts['validated']; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card mini-stat bg-info text-white">
                        <div class="card-body">
                            <h6 class="text-uppercase mb-2">Endorsed</h6>
                            <h2 class="mb-0"><?= (int) $counts['endorsed']; ?></h2>
                        </div>
                    </div>
                </div>
                <!-- <div class="col-md-3">
                    <div class="card mini-stat bg-success text-white">
                        <div class="card-body">
                            <h6 class="text-uppercase mb-2">Rated</h6>
                            <h2 class="mb-0"><?= (int) $counts['rated']; ?></h2>
                        </div>
                    </div>
                </div> -->
                <div class="col-md-3">
                    <div class="card mini-stat bg-warning text-white">
                        <div class="card-body">
                            <h6 class="text-uppercase mb-2">Endorsed (No Evaluator)</h6>
                            <h2 class="mb-0"><?= (int) $counts['no_rater']; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card mini-stat bg-danger text-white">
                        <div class="card-body">
                            <h6 class="text-uppercase mb-2">Disqualified</h6>
                            <h2 class="mb-0"><?= (int) $counts['dq']; ?></h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title">Quick Actions</h4>
                            <ul class="list-unstyled mb-0">
                                <li><a href="<?= base_url(); ?>Pages/validated_applicant" class="font-14">List of Validated Applicants</a></li>
                                <li><a href="<?= base_url(); ?>Pages/endorsed_applicants" class="font-14">Endorse Applicants</a></li>
                                <li><a href="<?= base_url(); ?>Pages/endorsed_applicants_unassigned" class="font-14">Endorsed (No Evaluator Applicants)</a></li>
                                <li><a href="<?= base_url(); ?>Pages/secretariat_dq_applicants" class="font-14">Disqualified Applicants</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title">Level Coverage</h4>
                            <?php if (empty($assigned)) : ?>
                                <p class="text-muted mb-0">Waiting for assignments.</p>
                            <?php else : ?>
                                <ul class="list-unstyled mb-0">
                                    <?php foreach ($assigned as $jt) : ?>
                                        <li><i class="mdi mdi-check text-success"></i> <?= h($labels[$jt] ?? $jt); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
