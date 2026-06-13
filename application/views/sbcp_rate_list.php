<?php include('templates/head.php'); ?>
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
                                    <h2 class="text-center"><?= $title; ?></h2>
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

                        <?php $att = array('class' => 'parsley-examples'); ?>
                        <?= form_open('Page/sbcp_list', $att); ?>

                        <input type="hidden" value="<?= $this->session->username; ?>" name="school_id">
                        <input type="hidden" value="<?= date('Y'); ?>" name="fy">
                        <input type="hidden" value="<?= date('Y-m-d'); ?>" name="cdate">
                        <input type="hidden" value="<?= $district->id; ?>" name="district">

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                        <p>This is a self-assessment tool designed to monitor and help strengthen the child protection committee in the school/s in accordance with the Department of Education’s Child protection and Anti-Bullying policy. To answer the tool, put a check mark ( &#10003; ) on the appropriate box/space: Yes, if the Indicator is met; No, if the indicator is not met; Not Sure if status of indicator is not known or is neither Yes nor No. Findings will be based on the scores and their corresponding functionality level. These will also be the basis for the plan of action by the school and the assistance from the different levels of DepEd.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body table-responsive">

                        <div class="row">
                            <div class="col-lg-12">
                                <div id="accordion" class="mb-3">

                                <?php $count=1; foreach($sbcp as $row){ ?>
                                    <div class="card mb-0">
                                        <div class="card-header" id="headingOne">
                                            <h6 class="m-0">
                                                <a href="#collapseOne<?=$row->id; ?>" class="text-dark" data-toggle="collapse"
                                                        aria-expanded="true"
                                                        aria-controls="collapseOne">
                                                    <?= $count++.'. '. $row->indicator; ?>
                                                </a>
                                            </h6>
                                        </div>

                                        <div id="collapseOne<?=$row->id; ?>" class="collapse <?php if($row->id == 1){echo 'show';} ?>" aria-labelledby="headingOne" data-parent="#accordion">
                                        <?php 
                                            $smcp_sub = $this->Common->one_cond('sbcp_sub_indicators', 'indicator_id', $row->id); 
                                            foreach($smcp_sub as $srow){
                                                $smcp_func = $this->Common->two_cond('sbcp_func_indicators', 'i_id', $row->id,'si_id',$srow->id); 
                                        ?>
                                            
                                        <div class="card-body">

                                            <table class="table mb-0">
                                                                            <thead>
                                                                                <tr class="text-center">
                                                                                    <th colspan="2" rowspan="2" style="vertical-align: middle;"><h3>Functionality Indicators<h3></th>
                                                                                    <th colspan="3">Indicator is met:</th>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td class="text-center">YES</td>
                                                                                    <td class="text-center">NO</td>
                                                                                    <td class="text-center">NOT SURE</td>
                                                                                </tr>
                                                                            </thead>
                                                                            
                                                                            <tbody>
                                                                                <tr>
                                                                                    <td><b><?= strtoupper($srow->indicator_letter); ?></b></td>
                                                                                    <td><b><?= $srow->description; ?></b></td>
                                                                                    <td></td>
                                                                                    <td></td>
                                                                                    <td></td>
                                                                                </tr>
                                                                                
                                                                                <?php $ivy=1; $ren=1; $r=0; foreach($smcp_func as $frow){
                                                                                    $c = $row->id.''.$srow->indicator_letter.''.$ivy++;
                                                                                    ?>
                                                                                    <tr <?php echo (++$r%2 ? "" : "class='table-info'"); ?>>
                                                                                        <td><?= $ren++; ?></td>
                                                                                        <td><?= $frow->description; ?> </td>

                                                                                        <td class="text-center">
                                                                                            <input type="hidden" name="q<?= $c; ?>" value="0" >
                                                                                            <input type="radio" name="q<?= $c; ?>" value="1" >
                                                                                        </td>
                                                                                        <td class="text-center"><input type="radio" name="q<?= $c; ?>" value="2" ></td>
                                                                                        <td class="text-center"><input type="radio" name="q<?= $c; ?>" value="3" ></td>
                                                                                    </tr>
                                                                                <?php } ?>
                                                                                
                                                                                
                                                                            </tbody>
                                                                        </table>


                                            

                                            </div>
                                            <?php } ?>
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





            <?php include('templates/footer.php'); ?>


            