<?php include('templates/head.php'); ?>
<link href="<?= base_url(); ?>assets/libs/custombox/custombox.min.css" rel="stylesheet" type="text/css">
<?php include('templates/header.php'); ?>

<?php
$escape = function ($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};

$hasSelected = function ($stored, $needle) {
    $stored = trim((string) $stored);
    if ($stored === '') {
        return false;
    }

    if ($needle === 'Others') {
        return (bool) preg_match('/(^|,\s*)Others(?:\s*:|(?=,\s*|$))/i', $stored);
    }

    return (bool) preg_match('/(^|,\s*)' . preg_quote($needle, '/') . '(?=,\s*|$)/', $stored);
};

$extractOtherValue = function ($stored) {
    $stored = trim((string) $stored);
    if ($stored === '') {
        return '';
    }

    if (preg_match('/(?:^|,\s*)Others\s*:\s*(.+)$/i', $stored, $matches)) {
        return trim($matches[1]);
    }

    return '';
};

$serologyOther = $extractOtherValue($row->serology);
$thyroidOther = $extractOtherValue($row->thyroid);
$xrayOther = $extractOtherValue($row->x_ray);
$ultrasoundOther = $extractOtherValue($row->ultrasound);
$labTestOther = $extractOtherValue($row->lab_test);
$patientName = trim(preg_replace('/\s+/', ' ', ((string) $row->FirstName . ' ' . (string) $row->MiddleName . ' ' . (string) $row->LastName)));
?>

<style>
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

<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>

            <?php if ($this->session->flashdata('success')) : ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <?= $this->session->flashdata('success'); ?>
                </div>
            <?php endif; ?>

            <?php if ($this->session->flashdata('danger')) : ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <?= $this->session->flashdata('danger'); ?>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body table-responsive">
                            <h4 class="header-title mb-4">EDIT LABORATORY REQUEST<br /></h4><br />

                            <form method="post" class="parsley-examples" action="<?= base_url('Page/update_labReq/' . (int) $row->labID); ?>">
                                <input type="hidden" name="patientType" id="patientType" value="<?= $escape($row->patientType); ?>">

                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label class="col-form-label">Patient Name</label>
                                        <input type="text" class="form-control" value="<?= $escape($patientName); ?>" readonly>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <input type="hidden" class="form-control" required name="FirstName" value="<?= $escape($row->FirstName); ?>">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <input type="hidden" class="form-control" name="MiddleName" value="<?= $escape($row->MiddleName); ?>">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <input type="hidden" required name="LastName" class="form-control" value="<?= $escape($row->LastName); ?>">
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-2">
                                        <label class="col-form-label">Sex</label>
                                        <select name="sex" class="form-control" required>
                                            <option value="Male" <?= ((string) $row->sex === 'Male') ? 'selected' : ''; ?>>Male</option>
                                            <option value="Female" <?= ((string) $row->sex === 'Female') ? 'selected' : ''; ?>>Female</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label class="col-form-label">Birth Date</label>
                                        <input type="date" required id="bday" onchange="submitBday()" value="<?= $escape($row->birthdate); ?>" name="birthdate" class="form-control">
                                        <input type="hidden" readonly id="resultBday" value="<?= $escape($row->age); ?>" name="age" class="form-control">
                                    </div>
                                    <div class="form-group col-md-8">
                                        <label class="col-form-label">Address</label>
                                        <input type="text" required name="address" class="form-control" value="<?= $escape($row->address); ?>">
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label class="col-form-label">District</label>
                                        <select name="district" id="districts" class="form-control" required>
                                            <option value="">Select District</option>
                                            <?php foreach ($district as $d): ?>
                                                <?php $districtName = (string) $d->discription; ?>
                                                <option value="<?= $escape($districtName); ?>" <?= ((string) $row->district === $districtName) ? 'selected' : ''; ?>>
                                                    <?= $escape($districtName); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label class="col-form-label">School</label>
                                        <select name="school" id="school" class="form-control" required>
                                            <option value="">Select School</option>
                                            <?php if (!empty($row->school)) : ?>
                                                <option value="<?= $escape($row->school); ?>" selected><?= $escape($row->school); ?></option>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-2">
                                        <label class="col-form-label">Laboratory Test</label>
                                        <div class="test-options">
                                            <input type="checkbox" name="lab_test[]" value="Urinalysis" <?= $hasSelected($row->lab_test, 'Urinalysis') ? 'checked' : ''; ?>> Urinalysis<br>
                                            <input type="checkbox" name="lab_test[]" value="Fecalysis" <?= $hasSelected($row->lab_test, 'Fecalysis') ? 'checked' : ''; ?>> Fecalysis<br>
                                            <input type="checkbox" name="lab_test[]" value="CBC w/ Platelet" <?= $hasSelected($row->lab_test, 'CBC w/ Platelet') ? 'checked' : ''; ?>> CBC w/ Platelet<br>
                                            <input type="checkbox" name="lab_test[]" value="Retic Count" <?= $hasSelected($row->lab_test, 'Retic Count') ? 'checked' : ''; ?>> Retic Count<br>
                                            <input type="checkbox" name="lab_test[]" value="Pregnancy Test(Urine)" <?= $hasSelected($row->lab_test, 'Pregnancy Test(Urine)') ? 'checked' : ''; ?>> Pregnancy Test(Urine)<br>
                                            <input type="checkbox" name="lab_test[]" value="Pregnancy Test(Serum)" <?= $hasSelected($row->lab_test, 'Pregnancy Test(Serum)') ? 'checked' : ''; ?>> Pregnancy Test(Serum)<br>
                                            <input type="checkbox" name="lab_test[]" value="Widal Test" <?= $hasSelected($row->lab_test, 'Widal Test') ? 'checked' : ''; ?>> Widal Test<br>
                                            <input type="checkbox" name="lab_test[]" value="Blood Typing/Rh" <?= $hasSelected($row->lab_test, 'Blood Typing/Rh') ? 'checked' : ''; ?>> Blood Typing/Rh<br>
                                            <input type="checkbox" name="lab_test[]" value="Others" data-other-target="#lab_test_other_wrap" onchange="toggleOtherInput(this)" <?= $hasSelected($row->lab_test, 'Others') ? 'checked' : ''; ?>> Others<br>
                                        </div>
                                        <div id="lab_test_other_wrap" class="other-entry">
                                            <input type="text" name="lab_test_other" class="form-control form-control-sm" placeholder="Specify other laboratory test" value="<?= $escape($labTestOther); ?>">
                                        </div>
                                    </div>

                                    <div class="form-group col-md-2">
                                        <label class="col-form-label">Bleeding Parameters</label>
                                        <div class="test-options">
                                            <input type="checkbox" name="bleed_test[]" value="Bleeding Time" <?= $hasSelected($row->bleed_test, 'Bleeding Time') ? 'checked' : ''; ?>> Bleeding Time<br>
                                            <input type="checkbox" name="bleed_test[]" value="Clotting Time" <?= $hasSelected($row->bleed_test, 'Clotting Time') ? 'checked' : ''; ?>> Clotting Time<br>
                                            <input type="checkbox" name="bleed_test[]" value="Prothrombin (PT)" <?= $hasSelected($row->bleed_test, 'Prothrombin (PT)') ? 'checked' : ''; ?>> Prothrombin (PT)<br>
                                            <input type="checkbox" name="bleed_test[]" value="Partial Thromboplastin (aPTT)" <?= $hasSelected($row->bleed_test, 'Partial Thromboplastin (aPTT)') ? 'checked' : ''; ?>> Partial Thromboplastin (aPTT)<br>
                                        </div>
                                    </div>

                                    <div class="form-group col-md-2">
                                        <label class="col-form-label">Hepatitis Markers</label>
                                        <div class="test-options">
                                            <input type="checkbox" name="hepatitis_test[]" value="HBsag" <?= $hasSelected($row->hepatitis_test, 'HBsag') ? 'checked' : ''; ?>> HBsag<br>
                                            <input type="checkbox" name="hepatitis_test[]" value="Anti-HBs" <?= $hasSelected($row->hepatitis_test, 'Anti-HBs') ? 'checked' : ''; ?>> Anti-HBs<br>
                                            <input type="checkbox" name="hepatitis_test[]" value="Anti-HBc" <?= $hasSelected($row->hepatitis_test, 'Anti-HBc') ? 'checked' : ''; ?>> Anti-HBc<br>
                                            <input type="checkbox" name="hepatitis_test[]" value="Anti-HBe" <?= $hasSelected($row->hepatitis_test, 'Anti-HBe') ? 'checked' : ''; ?>> Anti-HBe<br>
                                            <input type="checkbox" name="hepatitis_test[]" value="Hepa-A Profile" <?= $hasSelected($row->hepatitis_test, 'Hepa-A Profile') ? 'checked' : ''; ?>> Hepa-A Profile<br>
                                            <input type="checkbox" name="hepatitis_test[]" value="HBV DNA" <?= $hasSelected($row->hepatitis_test, 'HBV DNA') ? 'checked' : ''; ?>> HBV DNA<br>
                                            <input type="checkbox" name="hepatitis_test[]" value="HBeAg" <?= $hasSelected($row->hepatitis_test, 'HBeAg') ? 'checked' : ''; ?>> HBeAg<br>
                                        </div>
                                    </div>

                                    <div class="form-group col-md-2">
                                        <label class="col-form-label">Cardiac Function</label>
                                        <div class="test-options">
                                            <input type="checkbox" name="cardiac[]" value="Total CPK" <?= $hasSelected($row->cardiac, 'Total CPK') ? 'checked' : ''; ?>> Total CPK<br>
                                            <input type="checkbox" name="cardiac[]" value="LDH" <?= $hasSelected($row->cardiac, 'LDH') ? 'checked' : ''; ?>> LDH<br>
                                            <input type="checkbox" name="cardiac[]" value="Troponin I" <?= $hasSelected($row->cardiac, 'Troponin I') ? 'checked' : ''; ?>> Troponin I<br>
                                            <input type="checkbox" name="cardiac[]" value="Troponin T" <?= $hasSelected($row->cardiac, 'Troponin T') ? 'checked' : ''; ?>> Troponin T<br>
                                        </div>
                                    </div>

                                    <div class="form-group col-md-2">
                                        <label class="col-form-label">Blood Test</label>
                                        <div class="test-options">
                                            <input type="checkbox" name="blood_test[]" value="HbA1c" <?= $hasSelected($row->blood_test, 'HbA1c') ? 'checked' : ''; ?>>HbA1c<br>
                                            <input type="checkbox" name="blood_test[]" value="FBS" <?= $hasSelected($row->blood_test, 'FBS') ? 'checked' : ''; ?>> FBS<br>
                                            <input type="checkbox" name="blood_test[]" value="Serum Uric Acid" <?= $hasSelected($row->blood_test, 'Serum Uric Acid') ? 'checked' : ''; ?>> Serum Uric Acid<br>
                                            <input type="checkbox" name="blood_test[]" value="Lipid Profile" <?= $hasSelected($row->blood_test, 'Lipid Profile') ? 'checked' : ''; ?>> Lipid Profile<br>
                                            <input type="checkbox" name="blood_test[]" value="Electrolyte" <?= $hasSelected($row->blood_test, 'Electrolyte') ? 'checked' : ''; ?>>Electrolyte<br>
                                        </div>
                                    </div>

                                    <div class="form-group col-md-2">
                                        <label class="col-form-label">Liver Profile</label>
                                        <div class="test-options">
                                            <input type="checkbox" name="liver_profile[]" value="SGOT/AST" <?= $hasSelected($row->liver_profile, 'SGOT/AST') ? 'checked' : ''; ?>>SGOT/AST<br>
                                            <input type="checkbox" name="liver_profile[]" value="SGPT/ALT" <?= $hasSelected($row->liver_profile, 'SGPT/ALT') ? 'checked' : ''; ?>> SGPT/ALT<br>
                                            <input type="checkbox" name="liver_profile[]" value="Bilirubin Panel" <?= $hasSelected($row->liver_profile, 'Bilirubin Panel') ? 'checked' : ''; ?>> Bilirubin Panel<br>
                                            <input type="checkbox" name="liver_profile[]" value="Alkaline Phosphatase" <?= $hasSelected($row->liver_profile, 'Alkaline Phosphatase') ? 'checked' : ''; ?>> Alkaline Phosphatase<br>
                                            <input type="checkbox" name="liver_profile[]" value="TPAG, A/G Ratio" <?= $hasSelected($row->liver_profile, 'TPAG, A/G Ratio') ? 'checked' : ''; ?>>TPAG, A/G Ratio<br>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-2">
                                        <label class="col-form-label">Renal Function</label>
                                        <div class="test-options">
                                            <input type="checkbox" name="renal_func[]" value="BUA" <?= $hasSelected($row->renal_func, 'BUA') ? 'checked' : ''; ?>> BUA<br>
                                            <input type="checkbox" name="renal_func[]" value="BUN" <?= $hasSelected($row->renal_func, 'BUN') ? 'checked' : ''; ?>> BUN<br>
                                            <input type="checkbox" name="renal_func[]" value="Creatinine" <?= $hasSelected($row->renal_func, 'Creatinine') ? 'checked' : ''; ?>> Creatinine<br>
                                        </div>
                                    </div>

                                    <div class="form-group col-md-2">
                                        <label class="col-form-label">SEROLOGY</label>
                                        <div class="test-options">
                                            <input type="checkbox" name="serology[]" value="VDRL/RPR" <?= $hasSelected($row->serology, 'VDRL/RPR') ? 'checked' : ''; ?>> VDRL/RPR<br>
                                            <input type="checkbox" name="serology[]" value="CD4 Count" <?= $hasSelected($row->serology, 'CD4 Count') ? 'checked' : ''; ?>> CD4 Count<br>
                                            <input type="checkbox" name="serology[]" value="Others" data-other-target="#serology_other_wrap" onchange="toggleOtherInput(this)" <?= $hasSelected($row->serology, 'Others') ? 'checked' : ''; ?>> Others<br>
                                        </div>
                                        <div id="serology_other_wrap" class="other-entry">
                                            <input type="text" name="serology_other" class="form-control form-control-sm" placeholder="Specify other serology test" value="<?= $escape($serologyOther); ?>">
                                        </div>
                                    </div>

                                    <div class="form-group col-md-2">
                                        <label class="col-form-label">Thyroid Function</label>
                                        <div class="test-options">
                                            <input type="checkbox" name="thyroid[]" value="FT3" <?= $hasSelected($row->thyroid, 'FT3') ? 'checked' : ''; ?>> FT3<br>
                                            <input type="checkbox" name="thyroid[]" value="FT4" <?= $hasSelected($row->thyroid, 'FT4') ? 'checked' : ''; ?>> FT4<br>
                                            <input type="checkbox" name="thyroid[]" value="TSH" <?= $hasSelected($row->thyroid, 'TSH') ? 'checked' : ''; ?>> TSH<br>
                                            <input type="checkbox" name="thyroid[]" value="Others" data-other-target="#thyroid_other_wrap" onchange="toggleOtherInput(this)" <?= $hasSelected($row->thyroid, 'Others') ? 'checked' : ''; ?>> Others<br>
                                        </div>
                                        <div id="thyroid_other_wrap" class="other-entry">
                                            <input type="text" name="thyroid_other" class="form-control form-control-sm" placeholder="Specify other thyroid test" value="<?= $escape($thyroidOther); ?>">
                                        </div>
                                    </div>

                                    <div class="form-group col-md-2">
                                        <label class="col-form-label">X - Ray</label>
                                        <div class="test-options">
                                            <input type="checkbox" name="x_ray[]" value="Chest AP/L" <?= $hasSelected($row->x_ray, 'Chest AP/L') ? 'checked' : ''; ?>> Chest AP/L<br>
                                            <input type="checkbox" name="x_ray[]" value="Chest PA/L" <?= $hasSelected($row->x_ray, 'Chest PA/L') ? 'checked' : ''; ?>> Chest PA/L<br>
                                            <input type="checkbox" name="x_ray[]" value="Abdomen Supine" <?= $hasSelected($row->x_ray, 'Abdomen Supine') ? 'checked' : ''; ?>> Abdomen Supine<br>
                                            <input type="checkbox" name="x_ray[]" value="Electrocardiogram" <?= $hasSelected($row->x_ray, 'Electrocardiogram') ? 'checked' : ''; ?>> Electrocardiogram<br>
                                            <input type="checkbox" name="x_ray[]" value="2D Echo" <?= $hasSelected($row->x_ray, '2D Echo') ? 'checked' : ''; ?>> 2D Echo<br>
                                            <input type="checkbox" name="x_ray[]" value="Others" data-other-target="#xray_other_wrap" onchange="toggleOtherInput(this)" <?= $hasSelected($row->x_ray, 'Others') ? 'checked' : ''; ?>> Others<br>
                                        </div>
                                        <div id="xray_other_wrap" class="other-entry">
                                            <input type="text" name="x_ray_other" class="form-control form-control-sm" placeholder="Specify other X-ray test" value="<?= $escape($xrayOther); ?>">
                                        </div>
                                    </div>

                                    <div class="form-group col-md-2">
                                        <label class="col-form-label">Ultrasound</label>
                                        <div class="test-options">
                                            <input type="checkbox" name="ultrasound[]" value="Breast" <?= $hasSelected($row->ultrasound, 'Breast') ? 'checked' : ''; ?>>Breast<br>
                                            <input type="checkbox" name="ultrasound[]" value="Transvaginal" <?= $hasSelected($row->ultrasound, 'Transvaginal') ? 'checked' : ''; ?>> Transvaginal<br>
                                            <input type="checkbox" name="ultrasound[]" value="Whole Abdomen" <?= $hasSelected($row->ultrasound, 'Whole Abdomen') ? 'checked' : ''; ?>> Whole Abdomen<br>
                                            <input type="checkbox" name="ultrasound[]" value="Lower Abdomen" <?= $hasSelected($row->ultrasound, 'Lower Abdomen') ? 'checked' : ''; ?>> Lower Abdomen<br>
                                            <input type="checkbox" name="ultrasound[]" value="Upper Abdomen" <?= $hasSelected($row->ultrasound, 'Upper Abdomen') ? 'checked' : ''; ?>>Upper Abdomen<br>
                                            <input type="checkbox" name="ultrasound[]" value="Thyroid" <?= $hasSelected($row->ultrasound, 'Thyroid') ? 'checked' : ''; ?>>Thyroid<br>
                                            <input type="checkbox" name="ultrasound[]" value="CT scan Plain/Contrast" <?= $hasSelected($row->ultrasound, 'CT scan Plain/Contrast') ? 'checked' : ''; ?>>CT scan Plain/Contrast<br>
                                        </div>
                                    </div>

                                    <div class="form-group col-md-2">
                                        <label class="col-form-label">Ultrasound</label>
                                        <div class="test-options">
                                            <input type="checkbox" name="ultrasound[]" value="KUB" <?= $hasSelected($row->ultrasound, 'KUB') ? 'checked' : ''; ?>>KUB<br>
                                            <input type="checkbox" name="ultrasound[]" value="Prostate" <?= $hasSelected($row->ultrasound, 'Prostate') ? 'checked' : ''; ?>> Prostate<br>
                                            <input type="checkbox" name="ultrasound[]" value="Pelvic" <?= $hasSelected($row->ultrasound, 'Pelvic') ? 'checked' : ''; ?>> Pelvic<br>
                                            <input type="checkbox" name="ultrasound[]" value="HBT" <?= $hasSelected($row->ultrasound, 'HBT') ? 'checked' : ''; ?>> HBT<br>
                                            <input type="checkbox" name="ultrasound[]" value="Inguinoscrotal" <?= $hasSelected($row->ultrasound, 'Inguinoscrotal') ? 'checked' : ''; ?>> Inguinoscrotal<br>
                                            <input type="checkbox" name="ultrasound[]" value="Others" data-other-target="#ultrasound_other_wrap" onchange="toggleOtherInput(this)" <?= $hasSelected($row->ultrasound, 'Others') ? 'checked' : ''; ?>> Others<br>
                                        </div>
                                        <div id="ultrasound_other_wrap" class="other-entry">
                                            <input type="text" name="ultrasound_other" class="form-control form-control-sm" placeholder="Specify other ultrasound test" value="<?= $escape($ultrasoundOther); ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="modal-footer px-0 pb-0">
                                    <a href="<?= base_url('Page/lab_request'); ?>" class="btn btn-secondary">Cancel</a>
                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('templates/footer.php'); ?>

<script>
$(document).ready(function() {
    if ($('#bday').val()) {
        submitBday();
    }

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

    function loadSchoolsByDistrict(district, selectedSchool) {
        var $school = $('#school');
        $school.empty().append('<option value="">Select School</option>');

        if (!district) {
            if (selectedSchool) {
                $school.append($('<option/>', {
                    value: selectedSchool,
                    text: selectedSchool,
                    selected: true
                }));
            }
            return;
        }

        $.ajax({
            url: '<?= base_url('Page/get_schools_by_district'); ?>',
            type: 'POST',
            data: { district: district },
            dataType: 'json',
            success: function(response) {
                var hasSelectedSchool = false;

                $.each(response || [], function(_, item) {
                    var schoolName = item.schoolName || '';
                    if (!schoolName) {
                        return;
                    }

                    var isSelected = selectedSchool && schoolName === selectedSchool;
                    if (isSelected) {
                        hasSelectedSchool = true;
                    }

                    $school.append($('<option/>', {
                        value: schoolName,
                        text: schoolName,
                        selected: isSelected
                    }));
                });

                if (selectedSchool && !hasSelectedSchool) {
                    $school.append($('<option/>', {
                        value: selectedSchool,
                        text: selectedSchool,
                        selected: true
                    }));
                }
            },
            error: function() {
                if (selectedSchool) {
                    $school.append($('<option/>', {
                        value: selectedSchool,
                        text: selectedSchool,
                        selected: true
                    }));
                }
            }
        });
    }

    var currentSchool = <?= json_encode((string) $row->school); ?>;
    var currentDistrict = $('#districts').val();
    loadSchoolsByDistrict(currentDistrict, currentSchool);

    $('#districts').on('change', function() {
        loadSchoolsByDistrict($(this).val(), '');
    });

    enhanceOptionGroups();
    refreshOtherInputVisibility();

    $(document).on('change', 'input[type="checkbox"][data-other-target]', function() {
        syncOtherInput($(this));
    });
});
</script>
