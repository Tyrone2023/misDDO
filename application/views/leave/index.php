<div class="main-content" style="padding-bottom: 50px;">
    <div class="page-content">
        <div class="container-fluid">
            <div style="margin-left: 250px;">
                <div class="page-title-box">
                    <h4 class="mb-0 font-size-18">Leave Application</h4>
                </div>

                <?php if ($this->session->flashdata('success')): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($this->session->flashdata('success')); ?></div>
                <?php endif; ?>

                <?php if ($this->session->flashdata('error')): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($this->session->flashdata('error')); ?></div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-body">
                        <h4 style="display: inline; font-weight: bold;"><?= htmlspecialchars($staff_info->LastName . ', ' . $staff_info->FirstName . ' ' . $staff_info->MiddleName); ?></h4>
                        <p class="mb-1"><strong>ID Number:</strong> <?= htmlspecialchars($staff_info->IDNumber); ?></p>
                        <p class="mb-1"><strong>Department:</strong> <?= htmlspecialchars($staff_info->Department); ?></p>
                        <p class="mb-0"><strong>Position:</strong> <?= htmlspecialchars($staff_info->empPosition); ?></p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <!-- <pre><?php print_r($balances); ?></pre> -->
                        <h3 style="display: inline; font-weight: bold;">Leave Balances</h3>
                            <div class="d-flex flex-wrap" style="gap: 10px 25px;">
                                <div><strong>VL:</strong> <?= htmlspecialchars($balances['vl_balance'] ?? 0); ?></div>
                                <div><strong>SL:</strong> <?= htmlspecialchars($balances['sl_balance'] ?? 0); ?></div>
                                <div><strong>SPL:</strong> <?= htmlspecialchars($balances['spl_balance'] ?? 0); ?></div>
                                <div><strong>Force Leave:</strong> <?= htmlspecialchars($balances['fl_balance'] ?? $balances['fl_balance'] ?? 0); ?></div>
                                <div><strong>Wellness:</strong> <?= htmlspecialchars($balances['wellness_balance'] ?? 0); ?></div>
                                <div><strong>Solo Parent:</strong> <?= htmlspecialchars($balances['solo_parent_balance'] ?? 0); ?></div>
                                <div><strong>COC Hours:</strong> <?= htmlspecialchars($balances['coc_balance_hours'] ?? $balances['coc_balance'] ?? 0); ?></div>
                                <div><strong>VSC Days:</strong> <?= htmlspecialchars($balances['vsc_balance_days'] ?? 0); ?></div>
                            </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h3 style="display: inline; font-weight: bold;">Leave Filing</h3>

                            <div class="alert alert-info mt-3">
                                Please select what you want to file.
                            </div>

                            <div class="mb-3">
                                <button type="button" class="btn btn-primary" id="btnFileLeave">
                                    File Leave
                                </button>

                                <button type="button" class="btn btn-outline-success" id="btnPrivilegeLeave">
                                    Request Privilege Leave Points
                                </button>
                            </div>
                            <?php
                            $sex_value = '';
                                if (isset($staff_info->Sex)) {
                                    $sex_value = strtoupper(trim($staff_info->Sex));
                                } elseif (isset($staff_info->sex)) {
                                    $sex_value = strtoupper(trim($staff_info->sex));
                                } elseif (isset($staff_info->Gender)) {
                                    $sex_value = strtoupper(trim($staff_info->Gender));
                                } elseif (isset($staff_info->gender)) {
                                    $sex_value = strtoupper(trim($staff_info->gender));
                                }

                                $is_male = in_array($sex_value, array('M', 'MALE'), true);
                                $is_female = in_array($sex_value, array('F', 'FEMALE'), true);
                            ?>


                        <div id="leaveApplicationSection">
                        <form method="post" action="<?= base_url('leave/store'); ?>" enctype="multipart/form-data" id="leaveForm">
                            <div class="row">
                                <div class="col-md-4">
                                    <label>Leave Type</label>
                                    <select name="leave_type_code" id="leave_type_code" class="form-control" required>
                                        <option value="">Select leave type</option>
                                        <?php foreach ($leave_types as $code => $label): ?>
                                            <option value="<?= htmlspecialchars($code); ?>"><?= htmlspecialchars($label); ?></option>
                                        <?php endforeach; ?>
                                        <?php if (!isset($leave_types['COC'])): ?><option value="COC">COC</option><?php endif; ?>
                                        <?php if (!isset($leave_types['WELLNESS'])): ?><option value="WELLNESS">Wellness</option><?php endif; ?>
                                        <?php if (!isset($leave_types['PERSONAL'])): ?><option value="PERSONAL">Personal</option><?php endif; ?>
                                    </select>



                                </div>
                                <div id="entitlement-warning" class="alert alert-warning" style="display:none;">
                                    This leave type requires HR approval before application.
                                </div>

                                <div class="col-md-3">
                                    <label>Date From</label>
                                    <input type="date" name="date_from" class="form-control" required>
                                </div>

                                <div class="col-md-3">
                                    <label>Date To</label>
                                    <input type="date" name="date_to" class="form-control" required>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-3">
                                    <label>Commutation</label>
                                    <select name="commutation" class="form-control" required>
                                        <option value="">Select commutation</option>
                                        <option value="REQUESTED">Requested</option>
                                        <option value="NOT_REQUESTED">Not Requested</option>
                                    </select>
                                </div>

                                <div class="col-md-9">
                                    <label>Reason</label>
                                    <textarea name="reason" rows="3" class="form-control" ></textarea>
                                </div>
                            </div>
                           
                            <div id="vacation_fields" class="leave-detail-section border rounded p-3 mt-3" style="display:none;">
                                <label class="font-weight-bold d-block">Vacation / Special Privilege Leave Details</label>
                                <div class="form-check">
                                    <input class="form-check-input vacation-scope" type="radio" name="vacation_scope" id="vacation_within_ph" value="WITHIN_PH">
                                    <label class="form-check-label" for="vacation_within_ph">Within the Philippines</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input vacation-scope" type="radio" name="vacation_scope" id="vacation_abroad" value="ABROAD">
                                    <label class="form-check-label" for="vacation_abroad">Abroad</label>
                                </div>
                                <div class="mt-2">
                                    <input type="text" name="vacation_abroad_specify" id="vacation_abroad_specify" placeholder="If abroad, specify" class="form-control">
                                </div>
                            </div>

                                    <div id="sick_fields" class="leave-detail-section border rounded p-3 mt-3" style="display:none;">
                                        <label class="font-weight-bold d-block">Sick Leave Details</label>

                                        <div class="form-check">
                                            <input class="form-check-input sick-scope"
                                                type="radio"
                                                name="sick_scope"
                                                id="sick_in_hospital"
                                                value="IN_HOSPITAL">

                                            <label class="form-check-label" for="sick_in_hospital">
                                                In Hospital
                                            </label>
                                        </div>

                                        <div class="form-check">
                                            <input class="form-check-input sick-scope"
                                                type="radio"
                                                name="sick_scope"
                                                id="sick_out_patient"
                                                value="OUT_PATIENT">

                                            <label class="form-check-label" for="sick_out_patient">
                                                Out Patient
                                            </label>
                                        </div>

                                        <input type="text"
                                            name="sick_illness_specify"
                                            id="sick_illness_specify"
                                            class="form-control mt-2"
                                            placeholder="Specify illness">
                                    </div>

                            <div id="study_fields" class="leave-detail-section border rounded p-3 mt-3" style="display:none;">
                                <label class="font-weight-bold d-block">Study Leave Details</label>
                                <div class="form-check">
                                    <input class="form-check-input study-purpose" type="radio" name="study_purpose" id="study_masters" value="MASTERS_COMPLETION">
                                    <label class="form-check-label" for="study_masters">Completion of Master's Degree</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input study-purpose" type="radio" name="study_purpose" id="study_bar_board" value="BAR_BOARD_REVIEW">
                                    <label class="form-check-label" for="study_bar_board">BAR/Board Examination Review</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input study-purpose" type="radio" name="study_purpose" id="study_other" value="OTHER">
                                    <label class="form-check-label" for="study_other">Other</label>
                                </div>
                                <div class="mt-2">
                                    <input type="text" name="study_other_specify" id="study_other_specify" placeholder="If other, specify" class="form-control">
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="alert alert-warning mb-2">
                                        Check this only if the leave is due to emergency or urgent circumstances.
                                    </div>

                                    <div class="form-check">
                                        <input type="checkbox"
                                            name="is_emergency"
                                            value="1"
                                            class="form-check-input"
                                            id="is_emergency">

                                        <label class="form-check-label font-weight-bold" for="is_emergency">
                                            Emergency / Urgent Filing
                                        </label>
                                    </div>

                                    <small class="text-muted">
                                        This may allow filing even if the required advance filing period is not met, subject to HR approval.
                                    </small>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <label>Attachment (PDF only, max 2 MB, optional)</label>
                                    <input type="file" name="attachment_pdf" class="form-control" accept="application/pdf">
                                    <small class="text-muted">Attach supporting document such as medical certificate, solo parent ID, etc.</small>
                                </div>
                            </div>

                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary">Submit Leave Application</button>
                            </div>
                        </form>
                        </div>

                       

                        
                    </div>
                </div>

                        <div class="card mb-5" id="privilegeLeaveSection" style="margin-bottom: 80px; display:none;">            
                            <div class="card-body">
                                <h3 style="display: inline; font-weight: bold;">Apply for Leave Entitlement</h3>
                                <form method="post" 
                                    action="<?= base_url('leave/request_entitlement'); ?>" 
                                    enctype="multipart/form-data">

                                    <div class="form-group">
                                        <label>Leave Type</label>
                                        <select name="leave_type_code" id="privilege_leave_type_code" class="form-control" required>
                                            <option value="">Select</option>

                                            <?php if (!$is_male): ?>
                                                <option value="MATERNITY">Maternity Leave</option>
                                            <?php endif; ?>

                                            <?php if (!$is_female): ?>
                                                <option value="PATERNITY">Paternity Leave</option>
                                            <?php endif; ?>

                                            <option value="SOLO_PARENT">Solo Parent Leave</option>
                                            <option value="VAWC">VAWC Leave</option>
                                            <option value="WELLNESS">Wellness Leave</option>
                                            <option value="REHABILITATION">Rehabilitation Leave</option>
                                            <option value="CALAMITY">Special Emergency Leave</option>
                                            <option value="PERSONAL">Personal Leave</option>
                                        </select>

                                        <div class="form-group" id="maternity_subtype_group" style="display:none;">
                                            <label>Maternity Leave Type</label>
                                            <select name="leave_subtype" id="maternity_subtype" class="form-control">
                                                <option value="">Select maternity type</option>
                                                <option value="LIVE_CHILDBIRTH">Live Childbirth - 105 days</option>
                                                <option value="LIVE_CHILDBIRTH_SOLO_PARENT">Live Childbirth / Solo Parent - 120 days</option>
                                                <option value="MISCARRIAGE">Miscarriage / Emergency Termination - 60 days</option>
                                            </select>
                                        </div>


                                    </div>

                                    <div class="form-group">
                                        <label>Requested Credits (Days)</label>
                                       
                                        <input type="number"
                                            id="privilege_credits_display"
                                            class="form-control"
                                            min="0.5"
                                            step="0.5"
                                            disabled
                                            required>

                                        <input type="hidden" name="credits" id="privilege_credits">

                                        <small class="text-muted" id="privilege_credit_note">
                                            Credits will be automatically assigned based on selected privilege type.
                                        </small>
                                    </div>


                                    

                                    <div class="form-group">
                                        <label>Supporting Document (PDF only, max 2MB)</label>
                                        <input type="file" name="attachment_pdf" class="form-control" accept="application/pdf">
                                    </div>

                                    <button type="submit" class="btn btn-primary">
                                        Submit Request
                                    </button>

                                </form>

                            </div>
                        </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | MAIN SECTIONS
    |--------------------------------------------------------------------------
    */
    var leaveApplicationSection = document.getElementById('leaveApplicationSection');
    var privilegeLeaveSection   = document.getElementById('privilegeLeaveSection');

    var btnFileLeave      = document.getElementById('btnFileLeave');
    var btnPrivilegeLeave = document.getElementById('btnPrivilegeLeave');

    /*
    |--------------------------------------------------------------------------
    | LEAVE TYPE
    |--------------------------------------------------------------------------
    */
    var leaveType = document.getElementById('leave_type_code');

    var vacationFields = document.getElementById('vacation_fields');
    var sickFields     = document.getElementById('sick_fields');
    var studyFields    = document.getElementById('study_fields');

    var warning = document.getElementById('entitlement-warning');

    /*
    |--------------------------------------------------------------------------
    | PRIVILEGE ELEMENTS
    |--------------------------------------------------------------------------
    */
    var privilegeType    = document.getElementById('privilege_leave_type_code');
    var maternityGroup   = document.getElementById('maternity_subtype_group');
    var maternitySubtype = document.getElementById('maternity_subtype');

    var creditsDisplay = document.getElementById('privilege_credits_display');
    var creditsHidden  = document.getElementById('privilege_credits');
    var privilegeNote  = document.getElementById('privilege_credit_note');

    /*
    |--------------------------------------------------------------------------
    | REQUIRED FIELD TOGGLER
    |--------------------------------------------------------------------------
    */
    function setFormRequired(section, required) {

        if (!section) return;

        section.querySelectorAll('input, select, textarea').forEach(function (field) {

            if (
                field.type === 'hidden' ||
                field.type === 'file' ||
                field.type === 'checkbox' ||
                field.type === 'button' ||
                field.type === 'submit'
            ) {
                return;
            }

            if (field.dataset.originalRequired === undefined) {
                field.dataset.originalRequired = field.required ? '1' : '0';
            }

            field.required =
                required &&
                field.dataset.originalRequired === '1';
        });
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW MODE
    |--------------------------------------------------------------------------
    */
    function showFilingMode(mode) {

        if (mode === 'PRIVILEGE') {

            if (leaveApplicationSection) {
                leaveApplicationSection.style.display = 'none';
            }

            if (privilegeLeaveSection) {
                privilegeLeaveSection.style.display = 'block';
            }

            if (btnFileLeave) {
                btnFileLeave.className = 'btn btn-outline-primary';
            }

            if (btnPrivilegeLeave) {
                btnPrivilegeLeave.className = 'btn btn-success';
            }

            setFormRequired(leaveApplicationSection, false);
            setFormRequired(privilegeLeaveSection, true);

        } else {

            if (leaveApplicationSection) {
                leaveApplicationSection.style.display = 'block';
            }

            if (privilegeLeaveSection) {
                privilegeLeaveSection.style.display = 'none';
            }

            if (btnFileLeave) {
                btnFileLeave.className = 'btn btn-primary';
            }

            if (btnPrivilegeLeave) {
                btnPrivilegeLeave.className = 'btn btn-outline-success';
            }

            setFormRequired(leaveApplicationSection, true);
            setFormRequired(privilegeLeaveSection, false);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | MODE BUTTON EVENTS
    |--------------------------------------------------------------------------
    */
    if (btnFileLeave) {
        btnFileLeave.addEventListener('click', function () {
            showFilingMode('LEAVE');
        });
    }

    if (btnPrivilegeLeave) {
        btnPrivilegeLeave.addEventListener('click', function () {
            showFilingMode('PRIVILEGE');
        });
    }

    /*
    |--------------------------------------------------------------------------
    | PRIVILEGE CREDIT MAP
    |--------------------------------------------------------------------------
    */
    var creditMap = {
        'PATERNITY': 7,
        'SOLO_PARENT': 7,
        'VAWC': 10,
        'WELLNESS': 5,
        'REHABILITATION': 180,
        'CALAMITY': 5

    };

    var maternityCreditMap = {
        'LIVE_CHILDBIRTH': 105,
        'LIVE_CHILDBIRTH_SOLO_PARENT': 120,
        'MISCARRIAGE': 60
    };

    /*
    |--------------------------------------------------------------------------
    | SET CREDITS
    |--------------------------------------------------------------------------
    */
    function setCredits(value, note) {

        var finalValue = '';

        if (
            value !== undefined &&
            value !== null &&
            value !== ''
        ) {
            finalValue = parseFloat(value);
        }

        if (creditsDisplay) {
            creditsDisplay.value = finalValue;
            creditsDisplay.disabled = true;
        }

        if (creditsHidden) {
            creditsHidden.value = finalValue;
        }

        if (privilegeNote) {
            privilegeNote.innerHTML =
                note || 'Credits automatically assigned.';
        }
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE PRIVILEGE CREDITS
    |--------------------------------------------------------------------------
    */
    function updatePrivilegeCredits() {

        if (!privilegeType) return;

        var selected = privilegeType.value;

        if (selected === 'MATERNITY') {

            if (maternityGroup) {
                maternityGroup.style.display = 'block';
            }

            if (maternitySubtype) {
                maternitySubtype.required = true;
            }

            var subtype =
                maternitySubtype
                    ? maternitySubtype.value
                    : '';

            if (maternityCreditMap[subtype] !== undefined) {

                setCredits(
                    maternityCreditMap[subtype],
                    'Credits based on selected maternity subtype.'
                );

            } else {

                setCredits(
                    '',
                    'Please select maternity subtype.'
                );
            }

            return;
        }

        if (maternityGroup) {
            maternityGroup.style.display = 'none';
        }

        if (maternitySubtype) {
            maternitySubtype.required = false;
            maternitySubtype.value = '';
        }

        if (creditMap[selected] !== undefined) {

            setCredits(
                creditMap[selected],
                'Credits automatically assigned.'
            );

        } else {

            setCredits(
                '',
                'Credits automatically assigned.'
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | TOGGLE LEAVE DETAILS
    |--------------------------------------------------------------------------
    */
    function toggleLeaveDetails() {

        var selectedLeave =
            leaveType
                ? leaveType.value
                : '';

        /*
        |--------------------------------------------------------------------------
        | HIDE ALL DETAIL SECTIONS
        |--------------------------------------------------------------------------
        */
        document.querySelectorAll('.leave-detail-section')
            .forEach(function (section) {

                section.style.display = 'none';

                section.querySelectorAll('input, select, textarea')
                    .forEach(function (field) {

                        field.required = false;
                    });
            });

        /*
        |--------------------------------------------------------------------------
        | VACATION / SPL
        |--------------------------------------------------------------------------
        */
        if (
            selectedLeave === 'VL' ||
            selectedLeave === 'VACATION' ||
            selectedLeave === 'SPL'
        ) {

            if (vacationFields) {
                vacationFields.style.display = 'block';
            }

            document.querySelectorAll('input[name="vacation_scope"]')
                .forEach(function (field) {

                    field.required = true;
                });
        }

        /*
        |--------------------------------------------------------------------------
        | SICK
        |--------------------------------------------------------------------------
        */
        if (
            selectedLeave === 'SL' ||
            selectedLeave === 'SICK'
        ) {

            if (sickFields) {
                sickFields.style.display = 'block';
            }

            document.querySelectorAll('input[name="sick_scope"]')
                .forEach(function (field) {

                    field.required = true;
                });
        }

        /*
        |--------------------------------------------------------------------------
        | STUDY
        |--------------------------------------------------------------------------
        */
        if (selectedLeave === 'STUDY') {

            if (studyFields) {
                studyFields.style.display = 'block';
            }

            document.querySelectorAll('input[name="study_purpose"]')
                .forEach(function (field) {

                    field.required = true;
                });
        }

        /*
        |--------------------------------------------------------------------------
        | ENTITLEMENT WARNING
        |--------------------------------------------------------------------------
        */
        if (warning) {

            var nonMandatory = [
                'PATERNITY',
                'SOLO_PARENT',
                'WELLNESS',
                'MATERNITY',
                'VAWC',
                'REHABILITATION',
                'CALAMITY'
            ];

            warning.style.display =
                nonMandatory.includes(selectedLeave)
                    ? 'block'
                    : 'none';
        }
    }

    /*
    |--------------------------------------------------------------------------
    | EVENTS
    |--------------------------------------------------------------------------
    */
    if (leaveType) {
        leaveType.addEventListener('change', toggleLeaveDetails);
    }

    if (privilegeType) {
        privilegeType.addEventListener('change', updatePrivilegeCredits);
    }

    if (maternitySubtype) {
        maternitySubtype.addEventListener('change', updatePrivilegeCredits);
    }

    /*
    |--------------------------------------------------------------------------
    | INITIALIZE
    |--------------------------------------------------------------------------
    */
    showFilingMode('LEAVE');
    toggleLeaveDetails();
    updatePrivilegeCredits();

});
</script>


