<?php 
$pages = [
    'baseline_dn' => 'Baseline Weighing',
    'baseline2nd_dn' => 'Second Weighing',
    'baseline3nd_dn' => 'Third Weighing',
    'sbfp_form1' => 'Form 1',
    'sbfp_form2' => 'Form 2',
    'sbfp_sf8_dn' => 'SF 8'
    ];
?>
<?php include('templates/head.php'); ?>
<link href="<?= base_url(); ?>assets/libs/custombox/custombox.min.css" rel="stylesheet" type="text/css">
<?php include('templates/header.php'); ?>

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

                        

            <div class="row">
                            <div class="col-lg-10">
                                <div class="card">
                                    <div class="card-body">

                                        <form class="form-inline" method="post" action="<?= base_url('Sbfp/sbfp_bmi_dn'); ?>">
                                    <div class="form-group mr-3">
                                        <label class="mr-2">SY</label>
                                        <input type="text" class="form-control" name="sy" placeholder="2025-2026" required>
                                    </div>
                                    <div class="form-group mr-3">
                                        <label class="mr-2">Year Level</label>
                                        <select class="form-control" name="YearLevel" id="YearLevel" required>
                                            <option disabled selected>Select Year Level</option>
                                        <?php foreach($gl as $row) { ?>
                                            <option value="<?= $row->Major; ?>"><?= $row->Major; ?></option>
                                        <?php } ?>
                                        </select>
                                    </div>
                                    <div class="form-group mr-3">
                                        <label class="mr-2">Section</label>
                                        <select class="form-control" name="Section" id="Section" required>
                                            <option value="">Select Section</option>
                                            <?php foreach($section as $row){?>
                                                <option value="<?= $row->Section; ?>" data-grade="<?= $row->YearLevel; ?>"><?= $row->Section; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="form-group mr-3">
                                        <label class="mr-2">Weighing Group</label>
                                        <select class="form-control" name="w_group" required>
                                            <option value="">Select Group</option>
                                            <option value="Baseline">1st</option>
                                            <option value="2nd">2nd</option>
                                            <option value="3rd">3rd</option>
                                        </select>
                                    </div>
                                    <input type="submit" name="view" value="View" class="btn btn-info">
                                </form>

                                    </div>
                                </div>
                                <!-- end card -->
                            </div>
                            <!-- end col -->

                            <div class="col-lg-2">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="btn-group">
                                                <button type="button" class="btn btn-secondary dropdown-toggle waves-effect" data-toggle="dropdown" aria-expanded="false"> SBFP Report <i class="mdi mdi-chevron-down"></i> </button>
                                                <div class="dropdown-menu">
                                                    <?php
                                                        $hasSession = isset($this->session->bmi_sy);
                                                        foreach ($pages as $page => $label) {
                                                            $url = $hasSession ? base_url("Page/{$page}") : '#';
                                                            $target = $hasSession ? ' target="_blank"' : '';
                                                            echo "<a class='dropdown-item' href='{$url}'{$target}>{$label}</a>";
                                                        }
                                                    ?>
                                                </div>
                                            </div>
                                    </div>
                                </div>
                                <!-- end card -->
                            </div>
                            <!-- end col -->
                        </div>
                        <!-- end row -->

            






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

            <?php if (!empty($data)) : ?>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body table-responsive">
                                <h4 class="header-title mb-4">NUTRITIONAL STATUS</h4>
                                <div class="table-responsive">
                                    <table id="datatable" class="table table-bordered dt-responsive nowrap">
                                        <thead>
                                            <tr>
                                                <th>Student Name</th>
                                                <th>Student No.</th>
                                                <th>Year Level</th>
                                                <th>Section</th>
                                                <th>Year-Month</th>
                                                <th>Months</th>
                                                <th>Weighing Group</th>
                                                <th>Height<br><small>(in kg)</small></th>
                                                <th>Weight<br><small>(in meters)</small></th>
                                                <th>BMI</th>
                                                <th>Manage</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($data as $row) : ?>
                                                <tr>
                                                    <td><?= $row->LastName . ', ' . $row->FirstName; ?></td>
                                                    <td><?= $row->StudentNumber; ?></td>
                                                    <td><?= $row->YearLevel; ?></td>
                                                    <td><?= $row->Section; ?></td>
                                                    <td><?= $row->y_mo; ?></td>
                                                    <td><?= $row->months; ?></td>
                                                    <td><?= $row->w_group; ?></td>
                                                    <td><?= $row->height; ?></td>
                                                    <td><?= $row->weight; ?></td>
                                                    <td><?= $row->bmi; ?></td>
                                                    <td>
                                                        <!-- <a href="<?= base_url(); ?>Sbfp/sbfp_med_bmi_update/<?= $row->sID; ?>" class="btn btn-warning btn-sm">Edit</a> -->
                                                        <button type="button" class="btn btn-sm btn-success waves-effect waves-light open-AddBookDialog" data-toggle="modal" data-backdrop="static" data-keyboard="false" data-appid="<?= $row->FirstName.' '.$row->LastName; ?>" data-id="<?= $row->sID; ?>" data-item="<?= $row->weight; ?>" data-job="<?= $row->height; ?>" data-target="#edd"><i class="mdi mdi-pencil"></i> Edit</button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>


                                <!-- Give buttons proper spacing and container -->
                                <div class="text-right mt-4 mb-2">
                                    <a href="<?= base_url('Sbfp/calculate_bmi'); ?>" class="btn btn-success">
                                        Calculate BMI
                                    </a>
                                    <a href="<?= base_url('Sbfp/calculate_bmi_eqv'); ?>" class="btn btn-info">
                                        Calculate BMI Equivalent
                                    </a>

                                    <a href="<?= base_url('Sbfp/calculate_bmi_girls'); ?>" class="btn btn-purple">
                                        Calculate BMI Girls
                                    </a>
                                    <a href="<?= base_url('Sbfp/calculate_bmi_eqv_girls'); ?>" class="btn btn-primary">
                                        Calculate BMI Equivalent Girls
                                    </a>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>

                                                        <!-- Add Update BMI Modal -->
                                                        <div id="edd" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="modal-lastname">Get Data from Students' Enrollment</h5>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <form action="<?= base_url(); ?>Sbfp/sbfp_bmi_data_update" method="post">
                                                                        <div class="modal-body">

                                                                            <input style="border:0; font-size:20px; margin-bottom:20px; font-family:Montserrat,sans-serif" type="text" id="appid">
                                  

                                                                            <input type="hidden" name="id" id="id">

                                                                            <div class="form-group">
                                                                                <label for="sbfp_date">Height (in meters)</label>
                                                                                <input type="text" id="job" class="form-control" name="height" required>
                                                                            </div>

                                                                            <div class="form-group">
                                                                                <label for="sy">Weight</label>
                                                                                <input type="text" id="item" class="form-control" name="weight" required>
                                                                            </div>
                                                                            
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                            <!-- Uncomment below if needed -->
                                                                            <button type="submit" class="btn btn-primary">Submit</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>


                                                        


    <!-- Add New Disease Modal -->
    <div id="addDiseaseModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Get Data from Students' Enrollment</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="<?= base_url(); ?>Sbfp/getEnrolees" method="post">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="sy">SY</label>
                            <input type="text" id="sy" class="form-control" name="sy" required placeholder="2025-2026">
                        </div>
                        <div class="form-group">
                            <label for="sbfp_date">Weighing Date</label>
                            <input type="date" id="sbfp_date" class="form-control" name="sbfp_date" required>
                        </div>
                        <div class="form-group">
                            <label for="w_group">Weighing Group</label>
                            <select id="w_group" class="form-control" name="w_group" required>
                                <option value="">Select group</option>
                                <option value="Baseline">Baseline</option>
                                <option value="2nd">2nd</option>
                                <option value="3rd">3rd</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="w_group">Year Level</label>
                            <select class="form-control" name="YearLevel" id="YearLevels" required>
                                            <option disabled selected>Select Year Level</option>
                                        <?php foreach($gl as $row) { ?>
                                            <option value="<?= $row->Major; ?>"><?= $row->Major; ?></option>
                                        <?php } ?>
                                        </select>
                        </div>
                        <div class="form-group">
                            <label for="w_group">Section</label>
                            <select class="form-control" name="Section" id="Sections" required>
                                            <option value="">Select Section</option>
                                            <?php foreach($section as $row){?>
                                                <option value="<?= $row->Section; ?>" data-grade="<?= $row->YearLevel; ?>"><?= $row->Section; ?></option>
                                            <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <!-- Uncomment below if needed -->
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    




    <?php include('templates/footer.php'); ?>

    <script>
        $(document).ready(function() {
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>

    <script>
        const yearLevel = document.getElementById("YearLevel");
        const section = document.getElementById("Section");

        // Store all section options and remove them from the DOM except the default one
        const defaultOption = section.querySelector('option[value=""]');
        const sectionOptions = Array.from(section.querySelectorAll('option[data-grade]'));
        sectionOptions.forEach(opt => opt.remove());

        yearLevel.addEventListener("change", function () {
            // Clear current section options except default
            section.querySelectorAll('option[data-grade]').forEach(opt => opt.remove());

            // Append only matching options
            sectionOptions.forEach(opt => {
                if (opt.dataset.grade === this.value) {
                    section.appendChild(opt);
                }
            });

            // Reset the selection
            section.value = "";
        });
    </script>

        <script>
        document.addEventListener('DOMContentLoaded', function () {
            const yearLevelSelect = document.getElementById('YearLevels');
            const sectionSelect = document.getElementById('Sections');
            const allSectionOptions = Array.from(sectionSelect.options).filter(opt => opt.value !== '');

            // On load: remove all section options except the first (default)
            sectionSelect.innerHTML = '<option value="">Select Section</option>';

            yearLevelSelect.addEventListener('change', function () {
                const selectedYearLevel = this.value;

                // Reset and add default option
                sectionSelect.innerHTML = '<option value="">Select Section</option>';

                // Append matching options
                allSectionOptions.forEach(option => {
                    if (option.dataset.grade === selectedYearLevel) {
                        sectionSelect.appendChild(option);
                    }
                });
            });
        });
        </script>

