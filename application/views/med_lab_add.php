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
    height: calc(2.25rem + 2px) !important; /* Matches Bootstrap input height */
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

.lab-option {
    display: block;
    border-radius: 4px;
    padding: 2px 6px;
    margin-bottom: 2px;
    cursor: pointer;
}

.lab-option input[type="checkbox"] {
    margin-right: 6px;
    accent-color: #d90429;
}

.lab-option.is-selected {
    background: #ffe1e6;
    color: #9f1239;
    font-weight: 600;
}

.other-entry {
    display: none;
    margin-top: 6px;
}

</style>

<script type="text/javascript">
    function toggleOtherInput(checkbox) {
        if (!checkbox) {
            return;
        }

        var target = checkbox.getAttribute('data-other-target');
        if (!target) {
            return;
        }

        var wrap = document.querySelector(target);
        if (!wrap) {
            return;
        }

        if (checkbox.checked) {
            wrap.style.display = 'block';
        } else {
            wrap.style.display = 'none';
            var input = wrap.querySelector('input');
            if (input) {
                input.value = '';
            }
        }
    }

    function submitBday() {
        var bdayInput = document.getElementById('bday').value;
        if (!bdayInput) {
            document.getElementById('resultBday').value = '';
            return;
        }

        var birthDate = new Date(bdayInput);
        var today = new Date();

        var age = today.getFullYear() - birthDate.getFullYear();
        var monthDiff = today.getMonth() - birthDate.getMonth();
        var dayDiff = today.getDate() - birthDate.getDate();

        if (monthDiff < 0 || (monthDiff === 0 && dayDiff < 0)) {
            age--;
        }

        document.getElementById('resultBday').value = age;
    }
</script>

        <!-- Start Content-->
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <!-- <button type="button" class="btn btn-primary waves-effect waves-light" data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target=".bs-example-modal-lg">Add New</button> -->

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
                            <h4 class="header-title mb-4">ADD LABORATORY REQUEST<br /></h4><br />


                            <form method="post" class="parsley-examples" action="<?= base_url(); ?>Page/save_request">
                                <div class="modal-body
                            <div class="form-row">
    <div class="form-group col-md-4">
        <label class="col-form-label font-weight-bold"> Select Patient Type</label>
        <div class="btn-group btn-group-toggle d-flex" data-toggle="buttons">
            <label class="btn btn-outline-primary w-50">
                <input type="radio" name="patientType" value="Employee" id="employeeRadio"> Employee
            </label>
            <label class="btn btn-outline-success w-50">
                <input type="radio" name="patientType" value="Student" id="studentRadio"> Student
            </label>
        </div>
    </div>
</div>

    <div class="form-row">

    <div class="form-group col-md-12">
        <label class="col-form-label">Select Patient</label>
        <select id="patientSelect" class="form-control select2" style="width: 100%;" disabled>
            <option value="">Select Patient</option>
        </select>
    </div>
    </div>

<div class="form-row">
    <div class="form-group col-md-4">
        <input type="hidden" class="form-control" required name="FirstName" id="FirstName">
    </div>
    <div class="form-group col-md-4">
        <input type="hidden" class="form-control" name="MiddleName">
    </div>
    <div class="form-group col-md-4">
        <input type="hidden" required name="LastName" class="form-control">
    </div>
</div>



<script src="<?= base_url(); ?>assets/libs/select2/select2.min.js"></script>
<script src="<?= base_url(); ?>assets/libs/select2/jquery.min.js"></script>
<link href="<?= base_url(); ?>assets/libs/select2/select2.min.css" rel="stylesheet" type="text/css" />
<link href="<?= base_url(); ?>assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" id="bootstrap-stylesheet" />


<script>
$(document).ready(function() {
    $('.select2').select2(); // Initialize Select2
    if ($('#bday').val()) {
        submitBday();
    }

    $('input[name="patientType"]').change(function() {
        let type = $(this).val();
        $('#patientSelect').prop('disabled', false).empty().append('<option value="">Select Patient</option>');

        $.ajax({
            url: '<?= base_url("Page/get_patients") ?>',
            type: 'POST',
            data: { patientType: type },
            dataType: 'json',
            success: function(response) {
                $.each(response, function(index, item) {
                    $('#patientSelect').append(`<option value="${item.id}" 
                        data-firstname="${item.FirstName}" 
                        data-middlename="${item.MiddleName}" 
                        data-lastname="${item.LastName}">${item.name}</option>`);
                });
                $('#patientSelect').trigger('change'); // Refresh Select2
            }
        });
    });

    // Auto-fill First Name, Middle Name, Last Name when selecting a patient
    $('#patientSelect').change(function() {
        let selected = $(this).find(':selected');

        $('input[name="FirstName"]').val(selected.data('firstname') || '');
        $('input[name="MiddleName"]').val(selected.data('middlename') || '');
        $('input[name="LastName"]').val(selected.data('lastname') || '');
    });

    function applyOptionShade($checkbox) {
        var $label = $checkbox.closest('.lab-option');
        if ($label.length) {
            $label.toggleClass('is-selected', $checkbox.is(':checked'));
        }
    }

    function enhanceOptionGroups() {
        $('.test-options').each(function() {
            var html = $(this).html() || '';
            var lines = html.split(/<br\s*\/?>/i);
            var rebuilt = '';

            $.each(lines, function(_, line) {
                var trimmed = $.trim(line);
                if (trimmed === '') {
                    return;
                }
                rebuilt += '<label class="lab-option">' + trimmed + '</label>';
            });

            $(this).html(rebuilt);
        });

        $('.lab-option input[type="checkbox"]').on('change', function() {
            var $checkbox = $(this);
            applyOptionShade($checkbox);
            syncOtherInput($checkbox);
        }).each(function() {
            var $checkbox = $(this);
            applyOptionShade($checkbox);
            syncOtherInput($checkbox);
        });
    }

    function syncOtherInput($checkbox) {
        toggleOtherInput($checkbox.get(0));
    }

    function refreshOtherInputVisibility() {
        $('input[type="checkbox"][data-other-target"]').each(function() {
            syncOtherInput($(this));
        });
    }

    enhanceOptionGroups();
    refreshOtherInputVisibility();

    $(document).on('change', 'input[type="checkbox"][data-other-target]', function() {
        syncOtherInput($(this));
    });
});


</script>


                                <div class="form-row">

                                    <div class="form-group col-md-2">
                                        <label class="col-form-label">Sex</label>
                                        <select name="sex" class="form-control" required>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label class="col-form-label">Birth Date</label>
                                        <input type="date" required id="bday" onchange="submitBday()" value="<?= set_value('bd'); ?>" name="birthdate" class="form-control">
                                        <input type="hidden" readonly id="resultBday" value="<?= set_value('age'); ?>" name="age" class="form-control">
                                    </div>

                                    
                                    <div class="form-group col-md-8">
                                        <label class="col-form-label">Address</label>
                                        <input type="text" required name="address" class="form-control">
                                    </div>
                                </div>
                       
                                <div class="form-row">
    <div class="form-group col-md-6">
        <label class="col-form-label">District</label>
        <select name="district" id="districts" class="form-control" required>
            <option value="">Select District</option>
            <?php foreach ($district as $d): ?>
                <option value="<?= $d->discription; ?>"><?= $d->discription; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group col-md-6">
        <label class="col-form-label">School</label>
        <select name="school" id="school" class="form-control" required>
            <option value="">Select School</option>
        </select>
    </div>
</div>
<script>
$(document).ready(function() {
    $('#districts').change(function() {
        let district = $(this).val();

        $('#school').empty().append('<option value="">Select School</option>'); // Reset schools

        if (district) {
            $.ajax({
                url: '<?= base_url("Page/get_schools_by_district") ?>',
                type: 'POST',
                data: { district: district },
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

<div class="form-row">
    <div class="form-group col-md-2">
        <label class="col-form-label">Laboratory Test</label>
        <div class="test-options">
            <input type="checkbox" name="lab_test[]" value="Urinalysis"> Urinalysis<br>
            <input type="checkbox" name="lab_test[]" value="Fecalysis"> Fecalysis<br>
            <input type="checkbox" name="lab_test[]" value="CBC w/ Platelet"> CBC w/ Platelet<br>
            <input type="checkbox" name="lab_test[]" value="Retic Count"> Retic Count<br>
            <input type="checkbox" name="lab_test[]" value="Pregnancy Test(Urine)"> Pregnancy Test(Urine)<br>
            <input type="checkbox" name="lab_test[]" value="Pregnancy Test(Serum)"> Pregnancy Test(Serum)<br>
            <input type="checkbox" name="lab_test[]" value="Widal Test"> Widal Test<br>
            <input type="checkbox" name="lab_test[]" value="Blood Typing/Rh"> Blood Typing/Rh<br>
            <input type="checkbox" name="lab_test[]" value="Others" data-other-target="#lab_test_other_wrap" onchange="toggleOtherInput(this)"> Others<br>
        </div>
        <div id="lab_test_other_wrap" class="other-entry">
            <input type="text" name="lab_test_other" class="form-control form-control-sm" placeholder="Specify other laboratory test">
        </div>
    </div>

    <div class="form-group col-md-2">
        <label class="col-form-label">Bleeding Parameters</label>
        <div class="test-options">
            <input type="checkbox" name="bleed_test[]" value="Bleeding Time"> Bleeding Time<br>
            <input type="checkbox" name="bleed_test[]" value="Clotting Time"> Clotting Time<br>
            <input type="checkbox" name="bleed_test[]" value="Prothrombin (PT)"> Prothrombin (PT)<br>
            <input type="checkbox" name="bleed_test[]" value="Partial Thromboplastin (aPTT)"> Partial Thromboplastin (aPTT)<br>
        </div>
    </div>

    <div class="form-group col-md-2">
        <label class="col-form-label">Hepatitis Markers</label>
        <div class="test-options">
            <input type="checkbox" name="hepatitis_test[]" value="HBsag"> HBsag<br>
            <input type="checkbox" name="hepatitis_test[]" value="Anti-HBs"> Anti-HBs<br>
            <input type="checkbox" name="hepatitis_test[]" value="Anti-HBc"> Anti-HBc<br>
            <input type="checkbox" name="hepatitis_test[]" value="Anti-HBe"> Anti-HBe<br>
            <input type="checkbox" name="hepatitis_test[]" value="Hepa-A Profile"> Hepa-A Profile<br>
            <input type="checkbox" name="hepatitis_test[]" value="HBV DNA"> HBV DNA<br>
            <input type="checkbox" name="hepatitis_test[]" value="HBeAg"> HBeAg<br>
        </div>
    </div>

    <div class="form-group col-md-2">
        <label class="col-form-label">Cardiac Function</label>
        <div class="test-options">
            <input type="checkbox" name="cardiac[]" value="Total CPK"> Total CPK<br>
            <input type="checkbox" name="cardiac[]" value="LDH"> LDH<br>
            <input type="checkbox" name="cardiac[]" value="Troponin I"> Troponin I<br>
            <input type="checkbox" name="cardiac[]" value="Troponin T"> Troponin T<br>
        </div>
    </div>

    <div class="form-group col-md-2">
        <label class="col-form-label">Blood Test</label>
        <div class="test-options">
            <input type="checkbox" name="blood_test[]" value="HbA1c">HbA1c<br>
            <input type="checkbox" name="blood_test[]" value="FBS"> FBS<br>
            <input type="checkbox" name="blood_test[]" value="Serum Uric Acid"> Serum Uric Acid<br>
            <input type="checkbox" name="blood_test[]" value="Lipid Profile"> Lipid Profile<br>
            <input type="checkbox" name="blood_test[]" value="Electrolyte">Electrolyte<br>
        </div>
    </div>

    <div class="form-group col-md-2">
        <label class="col-form-label">Liver Profile</label>
        <div class="test-options">
            <input type="checkbox" name="liver_profile[]" value="SGOT/AST">SGOT/AST<br>
            <input type="checkbox" name="liver_profile[]" value="SGPT/ALT"> SGPT/ALT<br>
            <input type="checkbox" name="liver_profile[]" value="Bilirubin Panel"> Bilirubin Panel<br>
            <input type="checkbox" name="liver_profile[]" value="Alkaline Phosphatase"> Alkaline Phosphatase<br>
            <input type="checkbox" name="liver_profile[]" value="TPAG, A/G Ratio">TPAG, A/G Ratio<br>
        </div>
    </div>
</div>





<div class="form-row">
    <div class="form-group col-md-2">
        <label class="col-form-label">Renal Function</label>
        <div class="test-options">
            <input type="checkbox" name="renal_func[]" value="BUA"> BUA<br>
            <input type="checkbox" name="renal_func[]" value="BUN"> BUN<br>
            <input type="checkbox" name="renal_func[]" value="Creatinine"> Creatinine<br>
        </div>
    </div>

    <div class="form-group col-md-2">
        <label class="col-form-label">SEROLOGY</label>
        <div class="test-options">
            <input type="checkbox" name="serology[]" value="VDRL/RPR"> VDRL/RPR<br>
            <input type="checkbox" name="serology[]" value="CD4 Count"> CD4 Count<br>
            <input type="checkbox" name="serology[]" value="Others" data-other-target="#serology_other_wrap" onchange="toggleOtherInput(this)"> Others<br>
        </div>
        <div id="serology_other_wrap" class="other-entry">
            <input type="text" name="serology_other" class="form-control form-control-sm" placeholder="Specify other serology test">
        </div>
    </div>

    <div class="form-group col-md-2">
        <label class="col-form-label">Thyroid Function</label>
        <div class="test-options">
            <input type="checkbox" name="thyroid[]" value="FT3"> FT3<br>
            <input type="checkbox" name="thyroid[]" value="FT4"> FT4<br>
            <input type="checkbox" name="thyroid[]" value="TSH"> TSH<br>
            <input type="checkbox" name="thyroid[]" value="Others" data-other-target="#thyroid_other_wrap" onchange="toggleOtherInput(this)"> Others<br>
        </div>
        <div id="thyroid_other_wrap" class="other-entry">
            <input type="text" name="thyroid_other" class="form-control form-control-sm" placeholder="Specify other thyroid test">
        </div>
    </div>

    <div class="form-group col-md-2">
        <label class="col-form-label">X - Ray</label>
        <div class="test-options">
            <input type="checkbox" name="x_ray[]" value="Chest AP/L"> Chest AP/L<br>
            <input type="checkbox" name="x_ray[]" value="Chest PA/L"> Chest PA/L<br>
            <input type="checkbox" name="x_ray[]" value="Abdomen Supine"> Abdomen Supine<br>
            <input type="checkbox" name="x_ray[]" value="Electrocardiogram"> Electrocardiogram<br>
            <input type="checkbox" name="x_ray[]" value="2D Echo"> 2D Echo<br>
            <input type="checkbox" name="x_ray[]" value="Others" data-other-target="#xray_other_wrap" onchange="toggleOtherInput(this)"> Others<br>
        </div>
        <div id="xray_other_wrap" class="other-entry">
            <input type="text" name="x_ray_other" class="form-control form-control-sm" placeholder="Specify other X-ray test">
        </div>
    </div>

    <div class="form-group col-md-2">
        <label class="col-form-label">Ultrasound</label>
        <div class="test-options">
            <input type="checkbox" name="ultrasound[]" value="Breast">Breast<br>
            <input type="checkbox" name="ultrasound[]" value="Transvaginal"> Transvaginal<br>
            <input type="checkbox" name="ultrasound[]" value="Whole Abdomen"> Whole Abdomen<br>
            <input type="checkbox" name="ultrasound[]" value="Lower Abdomen"> Lower Abdomen<br>
            <input type="checkbox" name="ultrasound[]" value="Upper Abdomen">Upper Abdomen<br>
            <input type="checkbox" name="ultrasound[]" value="Thyroid">Thyroid<br>
            <input type="checkbox" name="ultrasound[]" value="CT scan Plain/Contrast">CT scan Plain/Contrast<br>
        </div>
    </div>

    <div class="form-group col-md-2">
        <label class="col-form-label">Ultrasound</label>
        <div class="test-options">
            <input type="checkbox" name="ultrasound[]" value="KUB">KUB<br>
            <input type="checkbox" name="ultrasound[]" value="Prostate"> Prostate<br>
            <input type="checkbox" name="ultrasound[]" value="Pelvic"> Pelvic<br>
            <input type="checkbox" name="ultrasound[]" value="HBT"> HBT<br>
            <input type="checkbox" name="ultrasound[]" value="Inguinoscrotal"> Inguinoscrotal<br>
            <input type="checkbox" name="ultrasound[]" value="Others" data-other-target="#ultrasound_other_wrap" onchange="toggleOtherInput(this)"> Others<br>
        </div>
        <div id="ultrasound_other_wrap" class="other-entry">
            <input type="text" name="ultrasound_other" class="form-control form-control-sm" placeholder="Specify other ultrasound test">
        </div>
    </div>

</div>


                             
                        </div>
                        <div class="modal-footer">
                            <input type="submit" name="submit" value="Submit" class="btn btn-primary waves-effect waves-light">
                        </div>
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
