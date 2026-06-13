<?php include('templates/head.php'); ?>
<link href="<?= base_url(); ?>assets/libs/custombox/custombox.min.css" rel="stylesheet" type="text/css">
<?php include('templates/header.php'); ?>

<!-- ============================================================== -->
<!-- Start Page Content here -->
<!-- ============================================================== -->

<div class="content-page">
    <div class="content">

        <style>
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

            /* ✅ Nurse: equal width + equal height (3 boxes) */
            .nurse-equal-3 .form-group {
                display: flex;
                flex-direction: column;
            }

            .nurse-equal-3 .form-group .form-control {
                width: 100% !important;
                flex: 1 1 auto;
                min-height: 260px;
                resize: vertical;
            }

            .attachment-dropzone {
                border: 2px dashed #ced4da;
                border-radius: .5rem;
                padding: .85rem 1rem;
                background: #f8fafc;
                cursor: pointer;
                transition: border-color .15s ease, background-color .15s ease;
            }

            .attachment-dropzone.dragover {
                border-color: #007bff;
                background: #e9f2ff;
            }
        </style>

        <!-- NOTE: Load jQuery BEFORE select2 -->
        <script src="<?= base_url(); ?>assets/libs/select2/jquery.min.js"></script>
        <script src="<?= base_url(); ?>assets/libs/select2/select2.min.js"></script>
        <link href="<?= base_url(); ?>assets/libs/select2/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="<?= base_url(); ?>assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" id="bootstrap-stylesheet" />

        <?php
        // Nurse role detection (same as your other forms)
        $pos = '';
        if (isset($sp) && isset($sp->position)) {
            $pos = trim((string)$sp->position);
        } else {
            $pos = trim((string)$this->session->userdata('position'));
        }
        $posNorm = strtolower($pos);
        $isNurseRole = in_array($posNorm, ['nurse', 'division nurse'], true);
        $old = (isset($old) && is_array($old)) ? $old : [];
        $oldJson = json_encode($old, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);

        $user_sp = $this->session->userdata('sp');
        ?>

        <script type="text/javascript">
            function submitBday() {
                var bdayInput = document.getElementById('bday').value;
                if (!bdayInput) return;

                var birthDate = new Date(bdayInput);
                var today = new Date();

                var age = today.getFullYear() - birthDate.getFullYear();
                var monthDiff = today.getMonth() - birthDate.getMonth();
                var dayDiff = today.getDate() - birthDate.getDate();

                if (monthDiff < 0 || (monthDiff === 0 && dayDiff < 0)) age--;

                document.getElementById('resultBday').value = age;
            }

            function submitForm(printPermission) {
                document.getElementById("printPermInput").value = printPermission;

                // Enable fields to ensure data is submitted (if disabled anywhere)
                $('input[name="FirstName"], input[name="MiddleName"], input[name="LastName"], input[name="IDNumber"], input[name="LRN"]').prop('disabled', false);

                setTimeout(function() {
                    document.getElementById("medPatientForm").submit();
                }, 300);
            }

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
        </script>

        <!-- Start Content-->
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>

            <?php if ($this->session->flashdata('success')) : ?>
                <?= '<div class="alert alert-success alert-dismissible fade show" role="alert">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>'
                    . $this->session->flashdata('success') .
                    '</div>'; ?>
            <?php endif; ?>

            <?php if ($this->session->flashdata('danger')) : ?>
                <?= '<div class="alert alert-danger alert-dismissible fade show" role="alert">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>'
                    . $this->session->flashdata('danger') .
                    '</div>'; ?>
            <?php endif; ?>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body table-responsive">
                            <h4 class="header-title mb-4">ADD CONSULATION<br /></h4><br />

                            <!-- ✅ ONE FORM ONLY -->
                            <form method="post" action="<?= base_url('Page/add_medPatient1'); ?>" class="parsley-examples" enctype="multipart/form-data" id="medPatientForm">

                                <input type="hidden" name="patientType" value="Student">

                                <div class="form-row">
                                    <div class="form-group col-md-7">
                                        <label class="col-form-label font-weight-bold">ATTACHMENT</label>
                                        <div id="attachmentDropzone" class="attachment-dropzone">
                                            <input type="file" name="attachment[]" id="attachmentInput" class="d-none" multiple accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                                            <div class="font-weight-bold">Drag and drop files here, or click to browse</div>
                                            <small class="text-muted">Up to 3 files. Allowed: JPG, JPEG, PNG, PDF, DOC, DOCX</small>
                                        </div>
                                        <small id="attachmentPreview" class="form-text text-muted">No files selected.</small>
                                    </div>

                                    <div class="col-md-5">
                                        <div class="card border-secondary shadow-sm">
                                            <div class="card-body">
                                                <h6 class="card-title text-secondary font-weight-bold">Patient History</h6>
                                                <p id="patientHistory" class="text-muted medium">No history found.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- ✅ Hidden system keys (single source of truth) -->
                                <input type="hidden" name="IDNumber" id="IDNumber" value="">
                                <input type="hidden" name="username" id="username" value="<?= $this->session->userdata('username'); ?>">
                                <input type="hidden" name="position" id="position" value="<?= $this->session->userdata('position'); ?>">
                                <input type="hidden" name="disposition_time" id="disposition_time" value="">
                                <input type="hidden" name="appdate" id="date" value="">
                                <input type="hidden" id="printPermInput" name="print_Perm" value="">

                                <!-- ✅ Student info (ONLY these visible ones; no duplicate hidden with same names) -->
                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <label class="col-form-label">LRN</label>
                                        <input type="text" class="form-control" name="LRN" id="LRN" required>
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label class="col-form-label">FIRST NAME</label>
                                        <input type="text" class="form-control" name="FirstName" id="FirstName" required>
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label class="col-form-label">MIDDLE NAME</label>
                                        <input type="text" class="form-control" name="MiddleName" id="MiddleName">
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label class="col-form-label">LAST NAME</label>
                                        <input type="text" class="form-control" name="LastName" id="LastName" required>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label class="col-form-label">DISTRICT</label>
                                        <select name="district" id="districts" class="form-control" required>
                                            <option value="">Select District</option>
                                            <?php foreach ($district as $d): ?>
                                                <option value="<?= $d->discription; ?>"><?= $d->discription; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label class="col-form-label">SCHOOL</label>
                                        <select name="school" id="school" class="form-control" required>
                                            <option value="">Select School</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-2">
                                        <label class="col-form-label">CONTACT NUMBER</label>
                                        <input type="text" class="form-control" name="contact">
                                    </div>

                                    <div class="form-group col-md-2">
                                        <label class="col-form-label">SEX</label>
                                        <select name="sex" class="form-control" required>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                        </select>
                                    </div>

                                    <div class="form-group col-md-2">
                                        <label class="col-form-label">BIRTH DATE</label>
                                        <input type="date" required id="bday" name="birthdate" class="form-control" onchange="submitBday()">
                                        <input type="hidden" readonly id="resultBday" name="age" class="form-control">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label class="col-form-label">ADDRESS</label>
                                        <input type="text" required name="address" class="form-control">
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-2">
                                        <label class="col-form-label">BP</label>
                                        <input type="text" class="form-control" required name="bp">
                                    </div>

                                    <div class="form-group col-md-2">
                                        <label class="col-form-label">CARDIAC RATE</label>
                                        <input type="text" class="form-control" name="cardiac">
                                    </div>

                                    <div class="form-group col-md-2">
                                        <label class="col-form-label">RESPIRATORY RATE</label>
                                        <input type="text" name="respiratory" class="form-control">
                                    </div>

                                    <div class="form-group col-md-2">
                                        <label class="col-form-label">TEMPERATURE</label>
                                        <input type="text" class="form-control" required name="temp">
                                    </div>

                                    <div class="form-group col-md-2">
                                        <label class="col-form-label">HEIGHT</label>
                                        <input type="text" class="form-control" name="height">
                                    </div>

                                    <div class="form-group col-md-2">
                                        <label class="col-form-label">WEIGHT</label>
                                        <input type="text" name="weight" class="form-control">
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-2">
                                        <label class="col-form-label">CIVIL STATUS</label>
                                        <select name="cstat" class="form-control">
                                            <option value="">Select Status</option>
                                            <option value="Single">Single</option>
                                            <option value="Married">Married</option>
                                            <option value="Widow">Widow</option>
                                        </select>
                                    </div>

                                    <div class="form-group col-md-1">
                                        <label class="col-form-label">02 SAT</label>
                                        <input type="text" class="form-control" name="sat">
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label class="col-form-label">CHIEF COMPLAINT</label>
                                        <input type="text" required class="form-control" name="complaint">
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label class="col-form-label">OTHER SYMPTOMS</label>
                                        <input type="text" class="form-control" name="others_symp">
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label class="col-form-label">ALLERGIES</label>
                                        <input type="text" name="allergies" class="form-control">
                                    </div>
                                </div>

                                <?php if (!$isNurseRole): ?>
                                    <div class="form-row">
                                        <div class="form-group col-md-4">
                                            <label class="col-form-label">CATEGORY</label>
                                            <select name="category" id="category" class="form-control">
                                                <option value="">Select Category</option>
                                                <?php foreach ($category as $d): ?>
                                                    <option value="<?= $d->category; ?>"><?= $d->category; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class="form-group col-md-8">
                                            <label class="col-form-label">DISEASE</label>
                                            <select name="disease" id="disease" class="form-control" data-selected="<?= htmlspecialchars((string)($old['disease'] ?? '')); ?>">
                                                <option value="">Select Disease</option>
                                            </select>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <input type="hidden" name="category" value="">
                                    <input type="hidden" name="disease" value="">
                                <?php endif; ?>

                                <script>
                                    $(document).ready(function() {
                                        function loadDiseases(selectedDisease) {
                                            var category = $("#category").val();
                                            var diseaseSelect = $("#disease");
                                            diseaseSelect.empty().append('<option value="">Select Disease</option>');
                                            if (!category) {
                                                return;
                                            }
                                            $.ajax({
                                                url: "<?= base_url('Page/getDiseasesByCategory'); ?>",
                                                type: "POST",
                                                data: {
                                                    category: category
                                                },
                                                dataType: "json",
                                                success: function(data) {
                                                    $.each(data, function(index, item) {
                                                        var selected = (selectedDisease && selectedDisease === item.disease) ? ' selected' : '';
                                                        diseaseSelect.append('<option value="' + item.disease + '"' + selected + '>' + item.disease + '</option>');
                                                    });
                                                }
                                            });
                                        }

                                        $("#category").off('change').on('change', function() {
                                            loadDiseases('');
                                        });

                                        var initialDisease = ($("#disease").data('selected') || '').toString();
                                        if ($("#category").val()) {
                                            loadDiseases(initialDisease);
                                        }
                                    });
                                </script>

                                <script>
                                    $(document).ready(function() {
                                        function toggleReferralFields() {
                                            var disposition = $('select[name="disposition"]').val();
                                            if (disposition === 'Transferred/Referred') {
                                                $('#referralFields').show();
                                            } else {
                                                $('#referralFields').hide();
                                                $('#referral_facility').val('');
                                                $('#referral_reason').val('');
                                                $('#referral_guardian').val('');
                                            }
                                        }

                                        $('select[name="disposition"]').on('change', toggleReferralFields);
                                        toggleReferralFields();
                                    });
                                </script>

                                <?php if (!$isNurseRole): ?>
                                    <div class="form-row">
                                        <div class="form-group col-md-7">
                                            <label class="col-form-label">CURRENT MEDICATION</label>
                                            <input type="text" class="form-control" name="current_med">
                                        </div>

                                        <div class="form-group col-md-3">
                                            <label class="col-form-label">DISPOSITION</label>
                                            <select name="disposition" class="form-control" required>
                                                <?php foreach ($disposition as $d): ?>
                                                    <option value="<?= $d->disposition; ?>"><?= $d->disposition; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class="form-group col-md-2">
                                            <label class="col-form-label">REST REQUIRED (DAYS)</label>
                                            <input type="number" name="rest_no" class="form-control">
                                        </div>
                                    </div>
                                    <div id="referralFields" style="display: none;">
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label class="col-form-label">REFERRAL FACILITY</label>
                                                <input type="text" class="form-control" name="referral_facility" id="referral_facility">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label class="col-form-label">REASONS FOR REFERRAL</label>
                                                <textarea class="form-control" name="referral_reason" id="referral_reason" rows="2"></textarea>
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label class="col-form-label">PARENTS/GUARDIAN</label>
                                                <input type="text" class="form-control" name="referral_guardian" id="referral_guardian">
                                            </div>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="form-row">
                                        <div class="form-group col-md-12">
                                            <label class="col-form-label">CURRENT MEDICATION</label>
                                            <input type="text" class="form-control" name="current_med">
                                        </div>
                                    </div>
                                    <input type="hidden" name="disposition" value="">
                                    <input type="hidden" name="rest_no" value="">
                                    <input type="hidden" name="referral_facility" value="">
                                    <input type="hidden" name="referral_reason" value="">
                                    <input type="hidden" name="referral_guardian" value="">
                                <?php endif; ?>

                                <?php if ($isNurseRole): ?>
                                    <!-- ✅ Nurse: equal width boxes -->
                                    <div class="form-row nurse-equal-3">
                                        <div class="form-group col-md-4">
                                            <label class="col-form-label">PURPOSE OF CONSULTATION</label>
                                            <textarea name="purpose" class="form-control" rows="12"></textarea>
                                        </div>

                                        <div class="form-group col-md-4">
                                            <label class="col-form-label">HISTORY OF PRESENT ILLNESS</label>
                                            <textarea name="illness_history" class="form-control" rows="12"></textarea>
                                        </div>

                                        <div class="form-group col-md-4">
                                            <label class="col-form-label">PHYSICAL EXAM</label>
                                            <textarea name="phy_exam" class="form-control" rows="12"></textarea>
                                        </div>
                                    </div>

                                    <input type="hidden" name="diagnosis" value="">
                                    <input type="hidden" name="treatment" value="">
                                    <input type="hidden" name="remarks" value="">
                                <?php else: ?>
                                    <div class="form-row">
                                        <div class="form-group col-md-12">
                                            <label class="col-form-label">PURPOSE OF CONSULTATION</label>
                                            <input type="text" name="purpose" class="form-control">
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group col-md-12">
                                            <label class="col-form-label">HISTORY OF PRESENT ILLNESS</label>
                                            <textarea name="illness_history" class="form-control" rows="12"></textarea>
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label class="col-form-label">PHYSICAL EXAM</label>
                                            <textarea name="phy_exam" class="form-control" rows="4"></textarea>
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label class="col-form-label">DIAGNOSIS</label>
                                            <textarea name="diagnosis" class="form-control" rows="4"></textarea>
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label class="col-form-label">TREATMENT/MANAGEMENT</label>
                                            <textarea name="treatment" class="form-control" rows="6"></textarea>
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label class="col-form-label">REMARKS</label>
                                            <textarea name="remarks" class="form-control" rows="6"></textarea>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <div class="modal-footer">
                                    <?php if ($user_sp != 0): ?>
                                        <input type="submit" name="submit" value="Submit" class="btn btn-primary waves-effect waves-light">
                                    <?php else: ?>
                                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#printModal">Submit</button>
                                    <?php endif; ?>
                                </div>

                                <?php if ($user_sp == 0): ?>
                                    <div class="modal fade" id="printModal" tabindex="-1" role="dialog" aria-labelledby="printModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content shadow-md rounded-md">
                                                <div class="modal-header bg-primary text-white">
                                                    <h5 class="modal-title font-weight-bold" id="printModalLabel">
                                                        <i class="fas fa-print"></i> Confirm Printing Permission
                                                    </h5>
                                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body text-center">
                                                    <i class="fas fa-question-circle fa-4x text-warning mb-3"></i>
                                                    <p class="lead font-weight-bold">Do you allow the nurse to print this patient's record?</p>
                                                </div>
                                                <div class="modal-footer d-flex justify-content-center">
                                                    <button type="button" class="btn btn-danger px-4 py-2" data-dismiss="modal">
                                                        <i class="fas fa-times"></i> Cancel
                                                    </button>
                                                    <button type="button" class="btn btn-success px-4 py-2" onclick="submitForm('Yes')">
                                                        <i class="fas fa-check-circle"></i> Yes, Allow
                                                    </button>
                                                    <button type="button" class="btn btn-secondary px-4 py-2" onclick="submitForm('No')">
                                                        <i class="fas fa-ban"></i> No, Deny
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                            </form>
                            <!-- ✅ END SINGLE FORM -->

                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!-- end container-fluid -->

    </div>
    <!-- end content -->
</div>

<?php include('templates/footer.php'); ?>

<script>
    $(document).ready(function() {
        const oldData = <?= $oldJson ?: '{}' ?>;

        // Select2 (if you ever add patientSelect later)
        $('.select2').select2();

        function setFieldValue(name, value) {
            if (value === null || typeof value === 'undefined') {
                return;
            }
            var $fields = $('[name="' + name + '"]');
            if (!$fields.length) {
                return;
            }
            var $first = $fields.first();
            if ($first.is(':checkbox') || $first.is(':radio')) {
                $fields.filter('[value="' + value + '"]').prop('checked', true);
                return;
            }
            $fields.val(value);
        }

        function loadSchools(district, selectedSchool) {
            $('#school').empty().append('<option value="">Select School</option>');
            if (!district) {
                return;
            }

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
                    if (selectedSchool) {
                        $('#school').val(selectedSchool);
                    }
                }
            });
        }

        function loadDiseases(category, selectedDisease) {
            var diseaseSelect = $('#disease');
            if (!diseaseSelect.length) {
                return;
            }
            diseaseSelect.empty().append('<option value="">Select Disease</option>');
            if (!category) {
                return;
            }
            $.ajax({
                url: "<?= base_url('Page/getDiseasesByCategory'); ?>",
                type: "POST",
                data: {
                    category: category
                },
                dataType: "json",
                success: function(data) {
                    $.each(data, function(index, item) {
                        var selected = (selectedDisease && selectedDisease === item.disease) ? ' selected' : '';
                        diseaseSelect.append('<option value="' + item.disease + '"' + selected + '>' + item.disease + '</option>');
                    });
                }
            });
        }

        $('#districts').change(function() {
            loadSchools($(this).val(), '');
        });

        $('#category').off('change').on('change', function() {
            loadDiseases($(this).val(), '');
        });

        // LRN history checker (works without patientSelect)
        $('#LRN').on('keyup change', function() {
            let lrn = $(this).val().trim();
            if (!lrn) return;

            $.ajax({
                url: '<?= base_url("Page/get_patient_history") ?>',
                type: 'POST',
                data: {
                    LRN: lrn
                },
                dataType: 'json',
                success: function(response) {
                    if (response.history_count > 0) {
                        $('#patientHistory').html(`Patient has <strong>${response.history_count}</strong> record(s) in history.`);
                    } else {
                        $('#patientHistory').html('No history found.');
                    }
                },
                error: function() {
                    $('#patientHistory').html('Error retrieving history.');
                }
            });
        });

        if (oldData && Object.keys(oldData).length) {
            $.each(oldData, function(name, value) {
                if (['district', 'school', 'category', 'disease'].indexOf(name) !== -1) {
                    return;
                }
                setFieldValue(name, value);
            });

            if (oldData.district) {
                $('#districts').val(oldData.district);
                loadSchools(oldData.district, oldData.school || '');
            }

            if ($('#category').length && oldData.category) {
                $('#category').val(oldData.category);
                loadDiseases(oldData.category, oldData.disease || '');
            }

            if ($('#bday').val()) {
                submitBday();
            }
            if (oldData.LRN) {
                $('#LRN').trigger('change');
            }
            $('select[name="disposition"]').trigger('change');
        }

        if (!$('#disposition_time').val()) {
            $('#disposition_time').val(formatDateTimeInManila());
        }
        if (!$('#date').val()) {
            $('#date').val(formatDateInManila());
        }

    });
</script>

<script>
    (function() {
        const maxFiles = 3;
        const dropzone = document.getElementById('attachmentDropzone');
        const input = document.getElementById('attachmentInput');
        const preview = document.getElementById('attachmentPreview');

        if (!dropzone || !input || !preview) return;

        function renderPreview(files) {
            if (!files || files.length === 0) {
                preview.textContent = 'No files selected.';
                return;
            }
            const names = [];
            for (let i = 0; i < files.length; i++) {
                names.push(files[i].name);
            }
            preview.textContent = names.join(', ');
        }

        function setFilesFromList(fileList) {
            if (!fileList || fileList.length === 0) {
                input.value = '';
                renderPreview([]);
                return;
            }

            if (fileList.length > maxFiles) {
                alert('You can upload up to 3 files only.');
                input.value = '';
                renderPreview([]);
                return;
            }

            if (typeof DataTransfer !== 'undefined') {
                const dt = new DataTransfer();
                for (let i = 0; i < fileList.length; i++) {
                    dt.items.add(fileList[i]);
                }
                input.files = dt.files;
            }
            renderPreview(input.files);
        }

        dropzone.addEventListener('click', function() {
            input.click();
        });

        ['dragenter', 'dragover'].forEach(function(evtName) {
            dropzone.addEventListener(evtName, function(e) {
                e.preventDefault();
                e.stopPropagation();
                dropzone.classList.add('dragover');
            });
        });

        ['dragleave', 'dragend', 'drop'].forEach(function(evtName) {
            dropzone.addEventListener(evtName, function(e) {
                e.preventDefault();
                e.stopPropagation();
                dropzone.classList.remove('dragover');
            });
        });

        dropzone.addEventListener('drop', function(e) {
            if (e.dataTransfer && e.dataTransfer.files) {
                setFilesFromList(e.dataTransfer.files);
            }
        });

        input.addEventListener('change', function() {
            if (input.files.length > maxFiles) {
                alert('You can upload up to 3 files only.');
                input.value = '';
            }
            renderPreview(input.files);
        });
    })();
</script>
