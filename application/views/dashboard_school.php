<?php include('templates/head.php'); ?>
<?php include('templates/header.php'); ?>

<?php
// Pages::school() hands us the `schools` row for the logged-in school account as
// a CI query object, which is empty when the account has no matching schoolID.
$school = (!empty($data) && $data->num_rows() > 0) ? $data->row() : null;

$adminName = '';
if ($school !== null) {
    $adminName = trim(
        trim((string) $school->adminFName) . ' ' .
        trim((string) $school->adminMName) . ' ' .
        trim((string) $school->adminLName)
    );
}

$address = '';
if ($school !== null) {
    $parts = array_filter(array(
        trim((string) $school->sitio),
        trim((string) $school->brgy),
        trim((string) $school->city),
        trim((string) $school->province),
    ), function ($v) {
        return $v !== '';
    });
    $address = implode(', ', $parts);
}

// label => value, rendered as a two-column detail list
$details = array();
if ($school !== null) {
    $details = array(
        'School ID'          => (string) $school->schoolID,
        'District'           => (string) $school->district,
        'Address'            => $address,
        'School Head'        => $adminName,
        'Designation'        => (string) $school->adminDesignation,
        'Permit No.'         => (string) $school->permitNo,
        'Recognition No.'    => (string) $school->recogNo,
    );
}

$quickLinks = array(
    array('link' => 'Page/schoolDashboard',     'icon' => 'mdi-view-dashboard-outline', 'label' => 'Dashboard',            'sub' => 'Personnel summary'),
    array('link' => 'Page/schoolProfile',       'icon' => 'mdi-school-outline',         'label' => 'School Profile',       'sub' => 'Update school information'),
    array('link' => 'Page/jobVacancy',          'icon' => 'mdi-briefcase-outline',      'label' => 'Job Vacancies',        'sub' => 'Posted positions'),
    array('link' => 'page/fy_setting_school',   'icon' => 'mdi-notebook-multiple',      'label' => 'Implementation Plans', 'sub' => 'Prepare and submit the AIP'),
);
?>

<!-- ============================================================== -->
<!-- Start Page Content here -->
<!-- ============================================================== -->

<div class="content-page">
    <div class="content">

        <!-- Start Content-->
        <div class="container-fluid mis-shell">

            <div class="mis-hero">
                <div class="mis-hero-text">
                    <span class="mis-hero-eyebrow"><i class="mdi mdi-school-outline"></i> School Dashboard</span>
                    <h3 class="mis-hero-title">
                        <?= $school !== null ? html_escape($school->schoolName) : 'School Dashboard'; ?>
                    </h3>
                    <p class="mis-hero-sub">
                        <?php if ($school !== null && $address !== '') : ?>
                            <?= html_escape($address); ?>
                        <?php else : ?>
                            School record overview and shortcuts to the screens you use most.
                        <?php endif; ?>
                    </p>
                </div>
                <?php if ($school !== null) : ?>
                    <div class="mis-hero-aside">
                        <span class="mis-pill"><i class="mdi mdi-identifier"></i> ID <?= html_escape($school->schoolID); ?></span>
                        <?php if (trim((string) $school->district) !== '') : ?>
                            <span class="mis-pill"><i class="mdi mdi-map-marker-outline"></i> <?= html_escape($school->district); ?></span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($school === null) : ?>
                <div class="mis-panel">
                    <div class="mis-panel-body">
                        <div class="mis-empty">
                            <i class="mdi mdi-school-outline"></i>
                            <p>No school record is linked to this account yet.</p>
                        </div>
                    </div>
                </div>
            <?php else : ?>
                <div class="mis-panel">
                    <div class="mis-panel-head">
                        <div>
                            <h5 class="mis-panel-title"><i class="mdi mdi-information-outline"></i> School information</h5>
                            <p class="mis-panel-sub">Registry details on file with the division office</p>
                        </div>
                        <a href="<?= base_url(); ?>Page/schoolProfile" class="mis-btn mis-btn-primary mis-btn-sm">
                            <i class="mdi mdi-pencil-outline"></i> Edit profile
                        </a>
                    </div>
                    <div class="mis-panel-body mis-panel-body-flush">
                        <div class="table-responsive">
                            <table class="table mis-table">
                                <tbody>
                                    <?php foreach ($details as $label => $value) : ?>
                                        <tr>
                                            <th scope="row" style="width: 220px;"><?= $label; ?></th>
                                            <td>
                                                <?php if (trim($value) === '') : ?>
                                                    <span class="mis-chip mis-chip-grey">Not set</span>
                                                <?php else : ?>
                                                    <?= html_escape($value); ?>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="mis-panel">
                <div class="mis-panel-head">
                    <div>
                        <h5 class="mis-panel-title"><i class="mdi mdi-flash-outline"></i> Quick access</h5>
                        <p class="mis-panel-sub">The screens this account uses most often</p>
                    </div>
                </div>
                <div class="mis-panel-body">
                    <div class="mis-links">
                        <?php foreach ($quickLinks as $q) : ?>
                            <a href="<?= base_url() . $q['link']; ?>" class="mis-link">
                                <i class="mdi <?= $q['icon']; ?>"></i>
                                <span><?= $q['label']; ?><small><?= $q['sub']; ?></small></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

        </div>
        <!-- end container-fluid -->

    </div>
    <!-- end content -->

    <?php include('templates/footer.php'); ?>
