<?php include('templates/head.php'); ?>
<link href="<?= base_url(); ?>assets/libs/custombox/custombox.min.css" rel="stylesheet" type="text/css">
<?php include('templates/header.php'); ?>

<!-- ============================================================== -->
<!-- Start Page Content here -->
<!-- ============================================================== -->

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

</style>

<div class="content-page">
    <div class="content">

        <!-- Start Content-->
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <!-- <button type="button" class="btn btn-primary waves-effect waves-light" data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target=".bs-example-modal-lg">Add New</button> -->
                        <!-- <a href="<?= base_url(); ?>Page/add_medPatient" class="btn btn-primary waves-effect waves-light">Add New</a> -->
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

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body table-responsive">
                        <form action="<?= base_url(); ?>Page/saveRecord" method="post">

  <h4 class="mb-4">Health Examination Record</h4>

  <div class="form-row">
  <div class="form-group col-md-12">
    <label>NAME:</label>
    <select class="form-control select2" style="width: 100%;" name="fullName" id="fullName" required>
      <option value="">Select</option>
      <?php foreach ($staff as $d): ?>
        <option 
          value="<?= $d->FirstName . ' ' . $d->MiddleName . ' ' . $d->LastName; ?>"
        >
          <?= $d->FirstName; ?> <?= $d->MiddleName; ?> <?= $d->LastName; ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>
</div>


<div class="form-row">
  <div class="form-group col-md-4">
    <label>DEPARTMENT:</label>
    <input type="text" name="department" class="form-control" value="Department of Education" readonly>
  </div>
  <div class="form-group col-md-4">
    <label>SCHOOL:</label>
    <select class="form-control select2" style="width: 100%;" name="school" id="school">
      <option value="">Select</option>
      <?php foreach ($school as $d): ?>
        <option value="<?= $d->schoolName; ?>">
          <?= $d->schoolName; ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="form-group col-md-4">
    <label>DISTRICT:</label>
    <select class="form-control select2" style="width: 100%;" name="district">
      <option value="">Select</option>
      <?php foreach ($district as $d): ?>
        <option value="<?= $d->district; ?>">
          <?= $d->district; ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>
</div>


    
    <div class="form-row">
    <div class="form-group col-md-4">
      <label>DATE OF BIRTH:</label>
      <input type="date" name="birthdate" class="form-control">
    </div>
    <div class="form-group col-md-4">
      <label>SEX:</label>
      <select name="sex" class="form-control">
        <option value="Male">Male</option>
        <option value="Female">Female</option>
      </select>
    </div>

    <div class="form-group col-md-4">
  <label>CIVIL STATUS:</label>
  <select name="civil_status" class="form-control">
    <option value="" selected disabled>-- Select Status --</option>
    <option value="Single">Single</option>
    <option value="Married">Married</option>
    <option value="Widow">Widow</option>
  </select>
</div>

  </div>

  <hr>

  <h5 class="mb-3">Physical Exam</h5>
  <div class="form-row">
    <div class="form-group col-md-3">
      <label>Date:</label>
      <input type="date" name="exam_date" class="form-control">
    </div>
    <div class="form-group col-md-2">
    <label>Height (cm):</label>
    <input type="text" id="height" name="height" class="form-control" oninput="calculateBMI()">
</div>
<div class="form-group col-md-2">
    <label>Weight (kg):</label>
    <input type="text" id="weight" name="weight" class="form-control" oninput="calculateBMI()">
</div>
<div class="form-group col-md-2">
    <label>BMI:</label>
    <input type="text" id="bmi" name="bmi" class="form-control" readonly>
</div>
<div class="form-group col-md-3">
    <label>Classification (Asian):</label>
    <input type="text" id="classification" name="classification" class="form-control" readonly>
</div>

<script>
    function calculateBMI() {
        const height = parseFloat(document.getElementById('height').value);
        const weight = parseFloat(document.getElementById('weight').value);

        if (!isNaN(height) && !isNaN(weight) && height > 0 && weight > 0) {
            const bmi = weight / ((height / 100) ** 2);
            document.getElementById('bmi').value = bmi.toFixed(2);
            updateAsianClassification(bmi);
        } else {
            document.getElementById('bmi').value = '';
            document.getElementById('classification').value = '';
        }
    }

    function updateAsianClassification(bmi) {
        let classification = '';

        if (bmi <= 17.50) classification = 'Underweight';
        else if (bmi > 17.50 && bmi <= 22.99) classification = 'Normal weight';
        else if (bmi >= 23.00 && bmi <= 27.99) classification = 'Overweight';
        else if (bmi >= 28.00) classification = 'Obese';

        document.getElementById('classification').value = classification;
    }
</script>

  </div>

  <h5 class="mt-4 mb-3">Medical Findings</h5>
<div class="form-row">
  <?php
  $fields = [
    'temperature' => '1. Temperature',
    'respiratory' => '2. Respiratory System',
    'fluorography_result' => '3. Fluorography Result',
    'fluorography_where' => '3.1 Where',
    'film_no' => '3.2 Film #',
    'xray_date' => '3.3 Date Taken',
    'sputum' => '4. Sputum Analysis',
    'circulatory' => '5. Circulatory System',
    'blood_pressure' => '5.1 Blood Pressure',
    'digestive' => '6. Digestive System',
    'genitourinary' => '7. Genitourinary',
    'urinalysis' => '8. Urinalysis',
    'skin' => '9. Skin',
    'locomotor' => '10. Locomotor',
    'nervous_system' => '11. Nervous System',
    'eyes' => '12. Eyes, Conjunctiva, etc.',
    'color_perception' => '12.1 Color Perception',
    'vision_with_far' => '12.2 Vision (With Glasses) - Far',
    'vision_with_near' => '12.3 Vision (With Glasses) - Near',
    'vision_without_far' => '12.4 Vision (Without Glasses) - Far',
    'vision_without_near' => '12.5 Vision (Without Glasses) - Near',
    'nose' => '13. Nose',
    'ear' => '14. Ear',
    'hearing' => '15. Hearing',
    'throat' => '16. Throat',
    'immunization' => '17. Immunization',
    'teeth' => '18. Teeth and Gums',
    'recommendation' => '19. Recommendation'
  ];

  foreach ($fields as $name => $label) {
    // Assign custom col sizes
    if ($name == 'throat' || $name == 'immunization' || $name == 'teeth') {
      $col_class = 'col-md-4';
    } elseif ($name == 'recommendation') {
      $col_class = 'col-md-12';
    } else {
      $col_class = 'col-md-3';
    }

    echo '
    <div class="form-group ' . $col_class . '">
      <label>' . $label . ':</label>
      <input type="' . ($name === 'xray_date' ? 'date' : 'text') . '" name="' . $name . '" class="form-control">
    </div>';
  }
  ?>
</div>

<!-- 
  <h5 class="mt-4 mb-3">Signatures & Details</h5>
  <div class="form-row">
    <div class="form-group col-md-4">
      <label>Employee's Signature:</label>
      <input type="text" name="emp_signature" class="form-control">
    </div>
    <div class="form-group col-md-4">
      <label>Employee's Printed Name:</label>
      <input type="text" name="emp_name" class="form-control">
    </div>
    <div class="form-group col-md-4">
      <label>Physician's Signature:</label>
      <input type="text" name="physician_signature" class="form-control">
    </div>
  </div>

  <div class="form-row">
    <div class="form-group col-md-6">
      <label>Physician's Name:</label>
      <input type="text" name="physician_name" class="form-control">
    </div>
    <div class="form-group col-md-6">
      <label>License No.:</label>
      <input type="text" name="license_no" class="form-control">
    </div>
  </div> -->

  <button type="submit" class="btn btn-primary mt-3">Submit Record</button>
</form>


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
    <script src="<?= base_url(); ?>assets/libs/select2/select2.min.js"></script>
<script src="<?= base_url(); ?>assets/libs/select2/jquery.min.js"></script>
<link href="<?= base_url(); ?>assets/libs/select2/select2.min.css" rel="stylesheet" type="text/css" />
<link href="<?= base_url(); ?>assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" id="bootstrap-stylesheet" />

<script>
$(document).ready(function() {
    $('.select2').select2(); // Initialize Select2
    });
    </script>


    <?php include('templates/footer.php'); ?>


<!-- jQuery and Bootstrap JS -->

