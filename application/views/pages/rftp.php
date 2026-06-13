

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

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="table-responsive">
                                            <table class="table table-bordered mb-0">
                                                <thead>
                                                    <tr>
                                                        <th colspan="2">PERFORMANCE REQUIRMENTS</th>
                                                    </tr>
                                                    <tr>
                                                        <th class="text-center">Position Applied</th>
                                                        <th class="text-center">Performance Requirments</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <th class="text-center">Teacher II</th>
                                                        <td>At least 6 Proficient COis at Very Satisfactory; and At least 4 Proficient NCOis at Very Satisfactory</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="text-center">Teacher III</th>
                                                        <td>At least 12 Proficient COis at Very Satisfactory; and At least 8 Proficient NCOis at Very Satisfactory</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="text-center">Teacher IV</th>
                                                        <td>21 Proficient COis at Very Satisfactory; and 16 Proficient NCOis at Very Satisfactory</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="text-center"> Teacher V</th>
                                                        <td>At least 6 Proficient COis at Outstanding; and At least 4 Proficient NCOis at Outstanding</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="text-center">Teacher VI</th>
                                                        <td>At least 12 Proficient COis at Outstanding; and At least 4 Proficient NCOis at Very Satisfactory and 4 Proficient NCOis at Outstanding</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="text-center">Teacher VII</th>
                                                        <td>At least 18 Proficient COis at Outstanding; and At least 6 Proficient NCOis at Very Satisfactory and 6 Proficient NCOis at Outstanding</td>
                                                    </tr>
                                                   
                                                </tbody>
                                            </table>
                                        </div>
                                </div>
                            </div>
                        </div>

                        <?php $att = array('class' => 'parsley-examples'); ?>
                        <?= form_open('Pages/rftp', $att); ?>

                        <input type="hidden" value="<?= $this->session->username; ?>" name="id">

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
                                                                                <?php foreach($indicator as $srow){?>
                                                                                <tr>
                                                                                    <td><?= $ivy++; ?></td>
                                                                                    <td><?= $srow->description; ?></td>
                                                                                    <td><?= $srow->sy; ?></td>
                                                                                    <td><?= $srow->type; ?></td>
                                                                                    <td class="text-center">
                                                                                        <select class="form-control" name="q<?= $srow->id; ?>">
                                                                                            <option value="0"></option>
                                                                                            <option value="5">Outstanding</option>
                                                                                            <option value="4">Very Satisfactory</option>
                                                                                            <option value="3">Satisfactory</option>
                                                                                            <option value="2">Unsatisfactory</option>
                                                                                            <option value="1">Poor</option>
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
                            <input type="submit" name="submit" value="Submit" class="btn btn-primary waves-effect waves-light mr-1">
                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        </form>

                        

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






            