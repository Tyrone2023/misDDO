<?php include('templates/head.php'); ?>
<link href="<?= base_url(); ?>assets/libs/custombox/custombox.min.css" rel="stylesheet" type="text/css">
<?php include('templates/header.php'); ?>

<!-- ============================================================== -->
<!-- Start Page Content here -->
<!-- ============================================================== -->

<div class="content-page">
    <div class="content">

        <style>
            /* Select2 Bootstrap height match */
            .select2-container .select2-selection--single {
                height: calc(2.25rem + 2px) !important;
                border: 1px solid #ced4da !important;
                border-radius: .25rem !important;
                padding: .375rem .75rem !important;
            }

            .select2-container--default .select2-selection--single .select2-selection__rendered {
                line-height: 1.5 !important;
                color: #495057 !important;
            }

            .select2-container--default .select2-selection--single .select2-selection__arrow {
                height: calc(2.25rem + 2px) !important;
            }

            /* ✅ Equal width + equal height for the last 3 boxes (nurse view) */
            .nurse-equal-3 .form-group {
                display: flex;
                flex-direction: column;
            }

            .nurse-equal-3 .form-group .form-control {
                width: 100% !important;
                flex: 1 1 auto;
                min-height: 260px;
                /* adjust if you want taller/shorter */
                resize: vertical;
            }
        </style>

        <!-- NOTE: Load jQuery BEFORE select2 -->
        <script src="<?= base_url(); ?>assets/libs/select2/jquery.min.js"></script>
        <script src="<?= base_url(); ?>assets/libs/select2/select2.min.js"></script>
        <link href="<?= base_url(); ?>assets/libs/select2/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="<?= base_url(); ?>assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" id="bootstrap-stylesheet" />

        <!-- Start Content-->
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="clearfix"></div>
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

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body table-responsive">
                            <h4 class="header-title mb-4">PENDING CONSULATION<br /></h4><br />

                            <?php
                            // Detect nurse roles (same logic)
                            $pos = '';
                            if (isset($sp) && isset($sp->position)) {
                                $pos = trim((string) $sp->position);
                            } else {
                                $pos = trim((string) $this->session->userdata('position'));
                            }
                            $posNorm = strtolower($pos);
                            $isNurseRole = in_array($posNorm, ['nurse', 'division nurse'], true);
                            ?>

                            <form method="post" action="<?= base_url('Page/med_patient_update?medID=' . urlencode($data[0]->medID)); ?>" class="parsley-examples">

                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label class="col-form-label">SELECT PATIENT</label>
                                        <select id="patientSelect" class="form-control select2" style="width: 100%;" disabled>
                                            <option value="">
                                                <?php echo $data[0]->FirstName; ?> <?php echo $data[0]->MiddleName; ?> <?php echo $data[0]->LastName; ?>
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Optional hidden identity keys (safe if your update needs them) -->
                                <input type="hidden" name="FirstName" value="<?php echo $data[0]->FirstName; ?>">
                                <input type="hidden" name="MiddleName" value="<?php echo $data[0]->MiddleName; ?>">
                                <input type="hidden" name="LastName" value="<?php echo $data[0]->LastName; ?>">
                                <input type="hidden" name="IDNumber" value="<?php echo isset($data[0]->IDNumber) ? $data[0]->IDNumber : ''; ?>">
                                <input type="hidden" name="LRN" value="<?php echo isset($data[0]->LRN) ? $data[0]->LRN : ''; ?>">

                                <div class="form-row">
                                    <div class="form-group col-md-2">
                                        <label class="col-form-label">CONTACT NUMBER</label>
                                        <input type="text" class="form-control" name="contact" value="<?php echo $data[0]->contact; ?>">
                                    </div>

                                    <div class="form-group col-md-2">
                                        <label class="col-form-label">SEX</label>
                                        <select name="sex" class="form-control">
                                            <option value="Male" <?php echo ($data[0]->sex == 'Male') ? 'selected' : ''; ?>>Male</option>
                                            <option value="Female" <?php echo ($data[0]->sex == 'Female') ? 'selected' : ''; ?>>Female</option>
                                        </select>
                                    </div>

                                    <div class="form-group col-md-2">
                                        <label class="col-form-label">BIRTH DATE</label>
                                        <input type="date" id="bday" name="birthdate" class="form-control" value="<?php echo $data[0]->birthdate; ?>">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label class="col-form-label">ADDRESS</label>
                                        <input type="text" name="address" class="form-control" value="<?php echo $data[0]->address; ?>">
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-2">
                                        <label class="col-form-label">BP</label>
                                        <input type="text" class="form-control" name="bp" value="<?php echo $data[0]->bp; ?>">
                                    </div>

                                    <div class="form-group col-md-2">
                                        <label class="col-form-label">CARDIAC RATE</label>
                                        <input type="text" class="form-control" name="cardiac" value="<?php echo $data[0]->cardiac; ?>">
                                    </div>

                                    <div class="form-group col-md-2">
                                        <label class="col-form-label">RESPIRATORY RATE</label>
                                        <input type="text" name="respiratory" class="form-control" value="<?php echo $data[0]->respiratory; ?>">
                                    </div>

                                    <div class="form-group col-md-2">
                                        <label class="col-form-label">TEMPERATURE</label>
                                        <input type="text" class="form-control" name="temp" value="<?php echo $data[0]->temp; ?>">
                                    </div>

                                    <div class="form-group col-md-2">
                                        <label class="col-form-label">HEIGHT</label>
                                        <input type="text" class="form-control" name="height" value="<?php echo $data[0]->height; ?>">
                                    </div>

                                    <div class="form-group col-md-2">
                                        <label class="col-form-label">WEIGHT</label>
                                        <input type="text" name="weight" class="form-control" value="<?php echo $data[0]->weight; ?>">
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-2">
                                        <label class="col-form-label">CIVIL STATUS</label>
                                        <select name="cstat" class="form-control">
                                            <option value="">Select Status</option>
                                            <option value="Single" <?= ($data[0]->cstat == 'Single') ? 'selected' : ''; ?>>Single</option>
                                            <option value="Married" <?= ($data[0]->cstat == 'Married') ? 'selected' : ''; ?>>Married</option>
                                            <option value="Widow" <?= ($data[0]->cstat == 'Widow') ? 'selected' : ''; ?>>Widow</option>
                                        </select>
                                    </div>

                                    <div class="form-group col-md-1">
                                        <label class="col-form-label">02 SAT</label>
                                        <input type="text" class="form-control" name="sat" value="<?php echo $data[0]->sat; ?>">
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label class="col-form-label">CHIEF COMPLAINT</label>
                                        <input type="text" class="form-control" name="complaint" value="<?php echo $data[0]->complaint; ?>">
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label class="col-form-label">OTHER SYMPTOMS</label>
                                        <input type="text" class="form-control" name="others_symp" value="<?php echo $data[0]->others_symp; ?>">
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label class="col-form-label">ALLERGIES</label>
                                        <input type="text" name="allergies" class="form-control" value="<?php echo $data[0]->allergies; ?>">
                                    </div>
                                </div>

                                <?php if (!$isNurseRole): ?>
                                    <div class="form-row">
                                        <div class="form-group col-md-4">
                                            <label class="col-form-label">CATEGORY</label>
                                            <select name="category" id="category" class="form-control">
                                                <option value="">Select Category</option>
                                                <?php foreach ($category as $d): ?>
                                                    <?php $catVal = (string) $d->category; ?>
                                                    <option value="<?= htmlspecialchars($catVal); ?>" <?= ($data[0]->category === $catVal) ? 'selected' : ''; ?>>
                                                        <?= htmlspecialchars($catVal); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-8">
                                            <label class="col-form-label">DISEASE</label>
                                            <select name="disease" id="disease" class="form-control" data-selected="<?= htmlspecialchars((string) ($data[0]->disease ?? '')); ?>">
                                                <option value="<?= htmlspecialchars((string) ($data[0]->disease ?? '')); ?>" selected>
                                                    <?= htmlspecialchars((string) ($data[0]->disease ?? 'Select Disease')); ?>
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <input type="hidden" name="category" value="<?= htmlspecialchars((string) ($data[0]->category ?? '')); ?>">
                                    <input type="hidden" name="disease" value="<?= htmlspecialchars((string) ($data[0]->disease ?? '')); ?>">
                                <?php endif; ?>

                                <?php if (!$isNurseRole): ?>
                                    <script>
                                        $(document).ready(function() {
                                            var $category = $('#category');
                                            var $disease = $('#disease');
                                            var initialDisease = ($disease.data('selected') || '').toString();

                                            function loadDiseases(selectedDisease) {
                                                var category = $category.val();
                                                $disease.empty().append('<option value="">Select Disease</option>');
                                                if (!category) return;

                                                $.ajax({
                                                    url: "<?= base_url('Page/getDiseasesByCategory'); ?>",
                                                    type: "POST",
                                                    data: {
                                                        category: category
                                                    },
                                                    dataType: "json",
                                                    success: function(data) {
                                                        $.each(data, function(index, item) {
                                                            var diseaseName = (item.disease || '').toString();
                                                            var selected = (selectedDisease !== '' && diseaseName === selectedDisease) ? ' selected' : '';
                                                            $disease.append('<option value="' + diseaseName + '"' + selected + '>' + diseaseName + '</option>');
                                                        });
                                                    }
                                                });
                                            }

                                            $category.on('change', function() {
                                                loadDiseases('');
                                            });

                                            if ($category.val()) {
                                                loadDiseases(initialDisease);
                                            }
                                        });
                                    </script>
                                <?php endif; ?>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label class="col-form-label">DISTRICT</label>
                                        <select name="district" id="districts" class="form-control" required>
                                            <option value="<?php echo $data[0]->district; ?>"><?php echo $data[0]->district; ?></option>
                                            <?php foreach ($district as $d): ?>
                                                <option value="<?= $d->discription; ?>"><?= $d->discription; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label class="col-form-label">SCHOOL</label>
                                        <select name="school" id="school" class="form-control" required>
                                            <option value="<?php echo $data[0]->school; ?>"><?php echo $data[0]->school; ?></option>
                                        </select>
                                    </div>
                                </div>

                                <script>
                                    $(document).ready(function() {
                                        $('.select2').select2();

                                        $('#districts').change(function() {
                                            let district = $(this).val();
                                            $('#school').empty().append('<option value="">Select School</option>');

                                            if (district) {
                                                $.ajax({
                                                    url: '<?= base_url("Page/get_schools_by_district") ?>',
                                                    type: 'POST',
                                                    data: {
                                                        district: district
                                                    },
                                                    dataType: 'json',
                                                    success: function(response) {
                                                        $.each(response, function(index, item) {
                                                            $('#school').append(`<option value="${item.schoolName}">${item.schoolName}</option>`);
                                                        });
                                                    }
                                                });
                                            }
                                        });
                                    });
                                </script>

                                <?php if (!$isNurseRole): ?>
                                    <div class="form-row">
                                        <div class="form-group col-md-7">
                                            <label class="col-form-label">CURRENT MEDICATION</label>
                                            <input type="text" class="form-control" name="current_med" value="<?php echo $data[0]->current_med; ?>">
                                        </div>

                                        <div class="form-group col-md-3">
                                            <label class="col-form-label">DISPOSITION</label>
                                            <select name="disposition" class="form-control">
                                                <option value="">Select Disposition</option>
                                                <?php foreach ($disposition as $d): ?>
                                                    <?php $val = (string) $d->disposition; ?>
                                                    <option value="<?= htmlspecialchars($val); ?>" <?= ($data[0]->disposition === $val) ? 'selected' : ''; ?>>
                                                        <?= htmlspecialchars($val); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class="form-group col-md-2">
                                            <label class="col-form-label">REST REQUIRED (DAYS)</label>
                                            <input type="number" name="rest_no" class="form-control" value="<?php echo $data[0]->rest_no; ?>">
                                        </div>
                                    </div>
                                    <div id="referralFields" style="display: none;">
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label class="col-form-label">REFERRAL FACILITY</label>
                                                <input type="text" class="form-control" name="referral_facility" id="referral_facility" value="<?php echo htmlspecialchars($data[0]->referral_facility ?? ''); ?>">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label class="col-form-label">REASONS FOR REFERRAL</label>
                                                <textarea class="form-control" name="referral_reason" id="referral_reason" rows="2"><?php echo htmlspecialchars($data[0]->referral_reason ?? ''); ?></textarea>
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label class="col-form-label">PARENTS/GUARDIAN</label>
                                                <input type="text" class="form-control" name="referral_guardian" id="referral_guardian" value="<?php echo htmlspecialchars($data[0]->referral_guardian ?? ''); ?>">
                                            </div>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <!-- Nurse: show only current medication; keep keys hidden -->
                                    <div class="form-row">
                                        <div class="form-group col-md-12">
                                            <label class="col-form-label">CURRENT MEDICATION</label>
                                            <input type="text" class="form-control" name="current_med" value="<?php echo $data[0]->current_med; ?>">
                                        </div>
                                    </div>

                                    <input type="hidden" name="disposition" value="<?php echo $data[0]->disposition; ?>">
                                    <input type="hidden" name="rest_no" value="<?php echo $data[0]->rest_no; ?>">
                                    <input type="hidden" name="referral_facility" value="<?php echo htmlspecialchars($data[0]->referral_facility ?? ''); ?>">
                                    <input type="hidden" name="referral_reason" value="<?php echo htmlspecialchars($data[0]->referral_reason ?? ''); ?>">
                                    <input type="hidden" name="referral_guardian" value="<?php echo htmlspecialchars($data[0]->referral_guardian ?? ''); ?>">
                                <?php endif; ?>

                                <?php if ($isNurseRole): ?>
                                    <!-- ✅ Nurse: last 3 boxes aligned and same height -->
                                    <div class="form-row nurse-equal-3">
                                        <div class="form-group col-md-4">
                                            <label class="col-form-label">PURPOSE OF CONSULTATION</label>
                                            <textarea name="purpose" class="form-control" rows="12"><?php echo htmlspecialchars($data[0]->purpose); ?></textarea>
                                        </div>

                                        <div class="form-group col-md-4">
                                            <label class="col-form-label">HISTORY OF PRESENT ILLNESS</label>
                                            <textarea name="illness_history" class="form-control" rows="12"><?php echo htmlspecialchars($data[0]->illness_history); ?></textarea>
                                        </div>

                                        <div class="form-group col-md-4">
                                            <label class="col-form-label">PHYSICAL EXAM</label>
                                            <textarea name="phy_exam" class="form-control" rows="12"><?php echo htmlspecialchars($data[0]->phy_exam); ?></textarea>
                                        </div>
                                    </div>

                                    <!-- Nurse: keep these keys hidden -->
                                    <input type="hidden" name="diagnosis" value="<?php echo htmlspecialchars($data[0]->diagnosis); ?>">
                                    <input type="hidden" name="treatment" value="<?php echo htmlspecialchars($data[0]->treatment); ?>">
                                    <input type="hidden" name="remarks" value="<?php echo htmlspecialchars($data[0]->remarks); ?>">
                                <?php else: ?>
                                    <div class="form-row">
                                        <div class="form-group col-md-12">
                                            <label class="col-form-label">PURPOSE OF CONSULTATION</label>
                                            <input type="text" name="purpose" class="form-control" value="<?php echo $data[0]->purpose; ?>">
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group col-md-12">
                                            <label class="col-form-label">HISTORY OF PRESENT ILLNESS</label>
                                            <textarea name="illness_history" class="form-control" rows="12"><?php echo htmlspecialchars($data[0]->illness_history); ?></textarea>
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label class="col-form-label">PHYSICAL EXAM</label>
                                            <textarea name="phy_exam" class="form-control" rows="4"><?php echo htmlspecialchars($data[0]->phy_exam); ?></textarea>
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label class="col-form-label">DIAGNOSIS</label>
                                            <textarea name="diagnosis" class="form-control" rows="4"><?php echo htmlspecialchars($data[0]->diagnosis); ?></textarea>
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label class="col-form-label">TREATMENT/MANAGEMENT</label>
                                            <textarea name="treatment" class="form-control" rows="6"><?php echo htmlspecialchars($data[0]->treatment); ?></textarea>
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label class="col-form-label">REMARKS</label>
                                            <textarea name="remarks" class="form-control" rows="6"><?php echo htmlspecialchars($data[0]->remarks); ?></textarea>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <input type="text" name="disposition_time" id="disposition_time" hidden>
                                <input type="text" name="appdate" id="date" hidden>
                                <input type="hidden" name="position" value="<?php echo $data[0]->position; ?>">

                                <script>
                                    function formatDateTimeInManila() {
                                        const now = new Date();
                                        const options = {
                                            timeZone: "Asia/Manila",
                                            year: "numeric",
                                            month: "long",
                                            day: "numeric",
                                            hour: "numeric",
                                            minute: "2-digit",
                                            hour12: true
                                        };
                                        return new Intl.DateTimeFormat("en-US", options).format(now).replace(" at", ", at");
                                    }

                                    function formatDateInManila() {
                                        const now = new Date();
                                        const options = {
                                            timeZone: "Asia/Manila",
                                            year: "numeric",
                                            month: "long",
                                            day: "numeric"
                                        };
                                        return new Intl.DateTimeFormat("en-US", options).format(now);
                                    }

                                    document.getElementById("disposition_time").value = formatDateTimeInManila();
                                    document.getElementById("date").value = formatDateInManila();
                                </script>

                                <script>
                                    function toggleReferralFields() {
                                        var disposition = document.querySelector('select[name="disposition"]');
                                        var referral = document.getElementById('referralFields');
                                        if (!disposition) {
                                            return;
                                        }
                                        if (!referral) {
                                            return;
                                        }
                                        if (disposition.value === 'Transferred/Referred') {
                                            referral.style.display = '';
                                        } else {
                                            referral.style.display = 'none';
                                            var facility = document.getElementById('referral_facility');
                                            var reason = document.getElementById('referral_reason');
                                            var guardian = document.getElementById('referral_guardian');
                                            if (facility) facility.value = '';
                                            if (reason) reason.value = '';
                                            if (guardian) guardian.value = '';
                                        }
                                    }

                                    document.addEventListener('DOMContentLoaded', function() {
                                        var disposition = document.querySelector('select[name="disposition"]');
                                        if (disposition) {
                                            disposition.addEventListener('change', toggleReferralFields);
                                            toggleReferralFields();
                                        }
                                    });
                                </script>

                                <div class="modal-footer">
                                    <input type="submit" name="submit" value="Submit" class="btn btn-primary waves-effect waves-light">
                                </div>

                            </form>

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
