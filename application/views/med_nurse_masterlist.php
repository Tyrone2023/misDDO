<?php include('templates/head.php'); ?>
<link href="<?= base_url(); ?>assets/libs/custombox/custombox.min.css" rel="stylesheet" type="text/css">
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
                        <div class="clearfix"></div>
                        <br>
                        <div class="row">
                            <div class="col-12">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <?php if ($this->session->flashdata('success')) : ?>

                <?= '<div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>'
                    . $this->session->flashdata('success') .
                    '</div>';
                ?>
            <?php endif; ?>

            <?php if ($this->session->flashdata('danger')) : ?>
                <?= '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>'
                    . $this->session->flashdata('danger') .
                    '</div>';
                ?>
            <?php endif;  ?>

            <?php
            $groups = [];
            foreach ($data as $row) {
                $district = $row->district_name ?: 'Unassigned';
                if (!isset($groups[$district])) {
                    $groups[$district] = [];
                }
                $groups[$district][] = $row;
            }
            if (!empty($groups)) {
                ksort($groups, SORT_NATURAL | SORT_FLAG_CASE);
            }
            $districtAnchors = [];
            foreach ($groups as $district => $rows) {
                $anchor = strtolower(trim($district));
                $anchor = preg_replace('/[^a-z0-9]+/', '-', $anchor);
                $anchor = trim($anchor, '-');
                if ($anchor === '') {
                    $anchor = 'unassigned';
                }
                $districtAnchors[$district] = $anchor;
            }
            $selectedDistrict = isset($selectedDistrict) ? $selectedDistrict : $this->input->get('district', true);
            if ($selectedDistrict && !isset($groups[$selectedDistrict])) {
                $selectedDistrict = null;
            }
            $selectedPatientType = isset($divisionPatientType) ? $divisionPatientType : $this->input->get('patient_type', true);
            if (!empty($selectedPatientType)) {
                $normalizedType = ucfirst(strtolower($selectedPatientType));
                if (!in_array($normalizedType, ['Employee', 'Student', 'All'], true)) {
                    $selectedPatientType = null;
                } else {
                    $selectedPatientType = $normalizedType;
                }
            }
            if (empty($selectedDistrict)) {
                $selectedPatientType = null;
            }
            $divisionNurseUsernames = isset($divisionNurseUsernames) ? $divisionNurseUsernames : [];
            $divisionPatientCounts = isset($divisionPatientCounts) ? $divisionPatientCounts : ['Employee' => 0, 'Student' => 0, 'Total' => 0];
            $divisionPatients = isset($divisionPatients) ? $divisionPatients : [];
            ?>

            <?php if (!$selectedDistrict) : ?>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body table-responsive">
                                <h4 class="header-title mb-4">Nurse Counts by District</h4>

                                <?php if (empty($groups)) : ?>
                                    <p class="text-muted">No nurse accounts found.</p>
                                <?php else : ?>
                                    <div class="table-responsive">
                                        <table class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th>District</th>
                                                    <th>Count</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($groups as $district => $rows) : ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($district, ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td>
                                                            <a href="<?= base_url('Page/med_nurse_masterlist?district=' . rawurlencode($district)); ?>" class="badge badge-primary">
                                                                <?= count($rows); ?>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <?php if ($selectedDistrict && !$selectedPatientType) : ?>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body table-responsive">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <h4 class="header-title mb-0">District: <?= htmlspecialchars($selectedDistrict, ENT_QUOTES, 'UTF-8'); ?></h4>
                                    <a href="<?= base_url('Page/med_nurse_masterlist'); ?>" class="btn btn-secondary btn-sm">Back</a>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Username</th>
                                                <th>Role</th>
                                                <th>Sex</th>
                                                <th>Address</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($groups[$selectedDistrict] as $row) : ?>
                                                <?php
                                                $name = trim($row->fname . ' ' . $row->mname . ' ' . $row->lname);
                                                $sex = $row->sex;
                                                if ($sex === '0' || $sex === 0) {
                                                    $sex = 'Male';
                                                } elseif ($sex === '1' || $sex === 1) {
                                                    $sex = 'Female';
                                                }
                                                ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?></td>
                                                    <td><?= htmlspecialchars($row->username, ENT_QUOTES, 'UTF-8'); ?></td>
                                                    <td><?= htmlspecialchars($row->sp_position, ENT_QUOTES, 'UTF-8'); ?></td>
                                                    <td><?= htmlspecialchars($sex, ENT_QUOTES, 'UTF-8'); ?></td>
                                                    <td><?= htmlspecialchars($row->address, ENT_QUOTES, 'UTF-8'); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <?php if ($selectedDistrict && !$selectedPatientType && !empty($divisionNurseUsernames)) : ?>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body table-responsive">
                                <h4 class="header-title mb-4">Division Nurse Patient Counts</h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>Patient Type</th>
                                                <th>Count</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Employee</td>
                                                <td>
                                                    <a href="<?= base_url('Page/med_nurse_masterlist?district=' . rawurlencode($selectedDistrict) . '&patient_type=Employee'); ?>" class="badge badge-info">
                                                        <?= (int) $divisionPatientCounts['Employee']; ?>
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Student</td>
                                                <td>
                                                    <a href="<?= base_url('Page/med_nurse_masterlist?district=' . rawurlencode($selectedDistrict) . '&patient_type=Student'); ?>" class="badge badge-success">
                                                        <?= (int) $divisionPatientCounts['Student']; ?>
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Total</td>
                                                <td>
                                                    <a href="<?= base_url('Page/med_nurse_masterlist?district=' . rawurlencode($selectedDistrict) . '&patient_type=All'); ?>" class="badge badge-primary">
                                                        <?= (int) $divisionPatientCounts['Total']; ?>
                                                    </a>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <?php if ($selectedDistrict && $selectedPatientType) : ?>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body table-responsive">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <h4 class="header-title mb-0">
                                        Patients (<?= htmlspecialchars($selectedPatientType, ENT_QUOTES, 'UTF-8'); ?>) - <?= htmlspecialchars($selectedDistrict, ENT_QUOTES, 'UTF-8'); ?>
                                    </h4>
                                    <a href="<?= base_url('Page/med_nurse_masterlist?district=' . rawurlencode($selectedDistrict)); ?>" class="btn btn-secondary btn-sm">Back</a>
                                </div>
                                <?php if (empty($divisionPatients)) : ?>
                                    <p class="text-muted">No patients found.</p>
                                <?php else : ?>
                                    <div class="table-responsive">
                                        <table class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Patient Type</th>
                                                    <th>District</th>
                                                    <th>Complaint</th>
                                                    <th>Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($divisionPatients as $patient) : ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars(trim($patient->FirstName . ' ' . $patient->MiddleName . ' ' . $patient->LastName), ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?= htmlspecialchars($patient->patientType, ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?= htmlspecialchars($patient->district, ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?= htmlspecialchars($patient->complaint, ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?= htmlspecialchars($patient->appdate, ENT_QUOTES, 'UTF-8'); ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <!-- end row -->

        </div>
        <!-- end container-fluid -->

    </div>
    <!-- end content -->


    <?php include('templates/footer.php'); ?>

    <script>
    $(document).ready(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
    </script>
    <!-- jQuery and Bootstrap JS -->
