

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
                                    <h2 class="text-center"><?= $title; ?></h2>
                                    <h5 class="text-center">Summary of the Achievement of PPST Indicators</h5>
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

                        <?php 
                            
                        ?>

                        

                        <?php $att = array('class' => 'parsley-examples'); ?>
                        <?= form_open('Pages/rftp_update', $att); ?>

                        <input type="hidden" value="<?= $check->id; ?>" name="id">

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body table-responsive">

                        <div class="row">
                            <div class="col-lg-12">
                                <div id="accordion" class="mb-3">

                                <?php $count=1; $ivy=1; foreach($rftp as $row){ 
                                    $indicator = $this->Common->one_cond('hris_rftp_domain_indicators','domain_id',$row->id);
                                    ?>
                                    <div class="card mb-0">
                                        <div class="card-header" id="headingOne">
                                            <h6 class="m-0">
                                                <a href="#collapseOne<?=$row->id; ?>" class="text-dark" data-toggle="collapse"
                                                        aria-expanded="true"
                                                        aria-controls="collapseOne">
                                                    <?= $row->description; ?>
                                                </a>
                                            </h6>
                                        </div>
                                        <div id="collapseOne<?=$row->id; ?>" class="collapse <?php if($row->id == 1){echo 'show';} ?>" aria-labelledby="headingOne" data-parent="#accordion">
                                            <div class="card-body">
                                        

                                            <table class="table mb-0">
                                                                            <thead>
                                                                                <tr>
                                                                                    <td><br /><b>No.</b></td>
                                                                                    <td><br /><b>Domain/ Strand/ Indicators</b></td>
                                                                                    <td class="text-center"><br /><b>SY</b></td>
                                                                                    <td><br /><b>Type</b></td>
                                                                                    <td class="text-center"><br /><b>Performance Ratings</b></td>
                                                                                </tr>
                                                                            </thead>
                                                                            
                                                                            <tbody>
                                                                                <?php foreach($indicator as $srow){ 
                                                                                    $field = 'q' . $srow->id;
                                                                                    ?>
                                                                                <tr>
                                                                                    <td><?= $ivy++; ?></td>
                                                                                    <td><?= $srow->description; ?></td>
                                                                                    <td><?= $srow->sy; ?></td>
                                                                                    <td><?= $srow->type; ?></td>
                                                                                    <td class="text-center">
                                                                                        <select class="form-control" name="q<?= $srow->id; ?>">
                                                                                            <option <?php if ((int)$check->$field === 0) { echo "selected";} ?> value="0"></option>
                                                                                            <option <?php if ((int)$check->$field === 5) { echo "selected";} ?> value="5">Outstanding</option>
                                                                                            <option <?php if ((int)$check->$field === 4) { echo "selected";} ?> value="4">Very Satisfactory</option>
                                                                                            <option <?php if ((int)$check->$field === 3) { echo "selected";} ?> value="3">Satisfactory</option>
                                                                                            <option <?php if ((int)$check->$field === 2) { echo "selected";} ?> value="2">Unsatisfactory</option>
                                                                                            <option <?php if ((int)$check->$field === 1) { echo "selected";} ?> value="1">Poor</option>
                                                                                        </select>
                                                                                    </td>
                                                                                </tr>
                                                                                <?php } ?>
                                                                                
                                                                                
                                                                                
                                                                            </tbody>
                                                                        </table>


                                            

                                            
                                        </div>
                                        </div>
                                    </div>
                                <?php } ?>

                                
                                  
                                    
                                </div>

                                

                            </div>
                            
                        </div>
                        <!-- end row -->
                        <div class="form-group text-left mb-0">
                            <input type="submit" name="submit" value="Update" class="btn btn-primary waves-effect waves-light mr-1">
                            <?php if($this->session->position == 'asds'){?>
                                <a target="_blank" href="<?= base_url(); ?>Pages/rftp_print_admin/<?= $this->uri->segment(3); ?>/<?= $this->uri->segment(4); ?>" class="btn btn-dark waves-effect waves-light mr-1"><i class="fa fa-print"></i></a>
                            <?php }else{ ?>
                                <a target="_blank" href="<?= base_url(); ?>Pages/rftp_print" class="btn btn-dark waves-effect waves-light mr-1"><i class="fa fa-print"></i></a>
                            <?php } ?>
                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        </form>


                        <div class="row">
    <div class="col-12">
        <div class="card">
            <div class="table-responsive">

                <?php
                // Totals from your objects
                $coi_out  = (int)($coi->total_1 ?? 0);
                $coi_vs   = (int)($coi->total_2 ?? 0);
                $ncoi_out = (int)($ncoi->total_1 ?? 0);
                $ncoi_vs  = (int)($ncoi->total_2 ?? 0);

                // Very Satisfactory OR Outstanding
                $coi_vs_or_out  = $coi_vs + $coi_out;
                $ncoi_vs_or_out = $ncoi_vs + $ncoi_out;

                /*
                 * CURRENT STAFF SALARY GRADE
                 * Replace this with your actual DB field.
                 *
                 * Example:
                 * $current_sg = (int)($staff->sg ?? 0);
                 * $current_sg = (int)($data->sg ?? 0);
                 */
                $current_sg = 20;

                /*
                 * NEXT-IN-RANK MATRIX USING SG
                 * Position to be filled => allowed current SG/s
                 */
                $next_in_rank_sg_matrix = [
                    'Teacher I' => [
                        0 // Open
                    ],

                    'Teacher II' => [
                        11
                    ],

                    'Teacher III' => [
                        12,
                        11
                    ],

                    'Teacher IV' => [
                        0, // Open
                        13,
                        12,
                        11
                    ],

                    'Teacher V' => [
                        14
                    ],

                    'Teacher VI' => [
                        15,
                        14
                    ],

                    'Teacher VII' => [
                        16,
                        15,
                        14
                    ],

                    'Master Teacher I' => [
                        17,
                        16,
                        15,
                        14
                    ],

                    'Master Teacher II' => [
                        18
                    ],

                    'Master Teacher III' => [
                        19,
                        18
                    ],

                    'Master Teacher IV' => [
                        20,
                        19,
                        18
                    ],

                    'Master Teacher V' => [
                        21,
                        20,
                        19,
                        18
                    ],

                    'School Principal I' => [
                        18
                    ],

                    'School Principal II' => [
                        19
                    ],

                    'School Principal III' => [
                        20,
                        19
                    ],

                    'School Principal IV' => [
                        21,
                        20,
                        19
                    ],
                ];

                // Salary Grade of position to be filled
                $position_sg = [
                    'Teacher I'            => 11,
                    'Teacher II'           => 12,
                    'Teacher III'          => 13,
                    'Teacher IV'           => 14,
                    'Teacher V'            => 15,
                    'Teacher VI'           => 16,
                    'Teacher VII'          => 17,
                    'Master Teacher I'     => 18,
                    'Master Teacher II'    => 19,
                    'Master Teacher III'   => 20,
                    'Master Teacher IV'    => 21,
                    'Master Teacher V'     => 22,
                    'School Principal I'   => 19,
                    'School Principal II'  => 20,
                    'School Principal III' => 21,
                    'School Principal IV'  => 22,
                ];

                function is_next_in_rank_sg_allowed($position_applied, $current_sg, $matrix)
                {
                    if (!isset($matrix[$position_applied])) {
                        return false;
                    }

                    return in_array((int)$current_sg, $matrix[$position_applied], true);
                }

                function render_apply($ok)
                {
                    return $ok
                        ? '<a href="javascript:void(0)" class="badge badge-success">Apply</a>'
                        : '<span class="badge badge-danger">Not Qualified</span>';
                }

                /*
                 * POSITION DATA
                 * Rows not allowed by current SG will be hidden.
                 */
                $positions = [
                    [
                        'position' => 'Teacher II',
                        'requirement' => 'At least 6 Proficient COIs at Very Satisfactory; and At least 4 Proficient NCOIs at Very Satisfactory',
                        'ok' => ($coi_vs_or_out >= 6) && ($ncoi_vs_or_out >= 4),
                    ],
                    [
                        'position' => 'Teacher III',
                        'requirement' => 'At least 12 Proficient COIs at Very Satisfactory; and At least 8 Proficient NCOIs at Very Satisfactory',
                        'ok' => ($coi_vs_or_out >= 12) && ($ncoi_vs_or_out >= 8),
                    ],
                    [
                        'position' => 'Teacher IV',
                        'requirement' => '21 Proficient COIs at Very Satisfactory; and 16 Proficient NCOIs at Very Satisfactory',
                        'ok' => ($coi_vs_or_out >= 21) && ($ncoi_vs_or_out >= 16),
                    ],
                    [
                        'position' => 'Teacher V',
                        'requirement' => 'At least 6 Proficient COIs at Outstanding; and At least 4 Proficient NCOIs at Outstanding',
                        'ok' => ($coi_out >= 6) && ($ncoi_out >= 4),
                    ],
                    [
                        'position' => 'Teacher VI',
                        'requirement' => 'At least 12 Proficient COIs at Outstanding; and At least 4 Proficient NCOIs at Very Satisfactory; and 4 Proficient NCOIs at Outstanding',
                        'ok' => ($coi_out >= 12) && ($ncoi_vs_or_out >= 4) && ($ncoi_out >= 4),
                    ],
                    [
                        'position' => 'Teacher VII',
                        'requirement' => 'At least 18 Proficient COIs at Outstanding; and At least 6 Proficient NCOIs at Very Satisfactory; and 6 Proficient NCOIs at Outstanding',
                        'ok' => ($coi_out >= 18) && ($ncoi_vs_or_out >= 6) && ($ncoi_out >= 6),
                    ],
                    [
                        'position' => 'Master Teacher I',
                        'requirement' => '21 Proficient COIs at Outstanding; 8 Proficient NCOIs at Very Satisfactory or Outstanding or both; and 8 Proficient NCOIs at Outstanding',
                        'ok' => ($coi_out >= 21) && ($ncoi_vs_or_out >= 8) && ($ncoi_out >= 8),
                    ],
                    [
                        'position' => 'Master Teacher II',
                        'requirement' => 'At least 10 Highly Proficient COIs at Outstanding; At least 5 Highly Proficient NCOIs at Very Satisfactory or Outstanding or both; and 5 Highly Proficient NCOIs at Outstanding',
                        'ok' => ($coi_out >= 10) && ($ncoi_vs_or_out >= 5) && ($ncoi_out >= 5),
                    ],
                    [
                        'position' => 'Master Teacher III',
                        'requirement' => '21 Highly Proficient COIs at Outstanding; 8 Highly Proficient NCOIs at Very Satisfactory or Outstanding or both; and 8 Highly Proficient NCOIs at Outstanding',
                        'ok' => ($coi_out >= 21) && ($ncoi_vs_or_out >= 8) && ($ncoi_out >= 8),
                    ],
                    [
                        'position' => 'Master Teacher IV',
                        'requirement' => 'At least 10 Distinguished COIs at Outstanding; at least 5 Distinguished NCOIs at Very Satisfactory or Outstanding or both; and 5 Distinguished NCOIs at Outstanding',
                        'ok' => ($coi_out >= 10) && ($ncoi_vs_or_out >= 5) && ($ncoi_out >= 5),
                    ],
                    [
                        'position' => 'Master Teacher V',
                        'requirement' => '21 Distinguished COIs at Outstanding; 8 Distinguished NCOIs at Very Satisfactory or Outstanding or both; and 8 Distinguished NCOIs at Outstanding',
                        'ok' => ($coi_out >= 21) && ($ncoi_vs_or_out >= 8) && ($ncoi_out >= 8),
                    ],
                ];
                ?>

                <table class="table table-bordered mb-0">
                    <thead>
                        <tr>
                            <th colspan="4">
                                PERFORMANCE REQUIREMENTS <br>

                                <span class="badge badge-purple">
                                    Total Outstanding : <?= (int)($res->total_1 ?? 0); ?>
                                </span>

                                <span class="badge badge-primary">
                                    Total Very Satisfactory : <?= (int)($res->total_2 ?? 0); ?>
                                </span>

                                &nbsp; &nbsp; &nbsp;

                                <span class="badge badge-primary">
                                    COI Outstanding : <?= (int)($coi->total_1 ?? 0); ?>
                                </span>

                                <span class="badge badge-info">
                                    COI Very Satisfactory : <?= (int)($coi->total_2 ?? 0); ?>
                                </span>

                                &nbsp; &nbsp; &nbsp;

                                <span class="badge badge-warning">
                                    NCOI Outstanding : <?= (int)($ncoi->total_1 ?? 0); ?>
                                </span>

                                <span class="badge badge-secondary">
                                    NCOI Very Satisfactory : <?= (int)($ncoi->total_2 ?? 0); ?>
                                </span>

                                &nbsp; &nbsp; &nbsp;

                                <span class="badge badge-dark">
                                    Current SG : <?= (int)$current_sg; ?>
                                </span>
                            </th>
                        </tr>

                        <tr>
                            <th class="text-center">Position Applied</th>
                            <th class="text-center" style="width:70px;">SG</th>
                            <th class="text-center">Performance Requirements</th>
                            <th class="text-center" style="width:120px;">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        $has_visible_row = false;

                        foreach ($positions as $row):

                            $position = $row['position'];

                            // HIDE ROW IF CURRENT SG IS NOT ALLOWED BY MATRIX
                            if (!is_next_in_rank_sg_allowed($position, $current_sg, $next_in_rank_sg_matrix)) {
                                continue;
                            }

                            $has_visible_row = true;
                        ?>
                            <tr>
                                <th class="text-center">
                                    <?= htmlspecialchars($position, ENT_QUOTES, 'UTF-8'); ?>
                                </th>

                                <td class="text-center">
                                    <?= (int)($position_sg[$position] ?? 0); ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($row['requirement'], ENT_QUOTES, 'UTF-8'); ?>
                                </td>

                                <td class="text-center">
                                    <?= render_apply($row['ok']); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <?php if (!$has_visible_row): ?>
                            <tr>
                                <td colspan="4" class="text-center text-danger">
                                    No position available based on your current salary grade.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>


                        

                    </div>
                    <!-- end container-fluid -->

                </div>
                <!-- end content -->

                

                



                <script>
                // Get all the checkboxes with the same name
                const checkboxes = document.querySelectorAll('input[name="option"]');

                checkboxes.forEach((checkbox) => {
                    checkbox.addEventListener('change', () => {
                        // If a checkbox is checked, uncheck others in the same group
                        checkboxes.forEach((otherCheckbox) => {
                            if (otherCheckbox !== checkbox) {
                                otherCheckbox.checked = false;
                            }
                        });
                    });
                });
            </script>                           






            