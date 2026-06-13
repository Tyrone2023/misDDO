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
                                    <h2 class="text-center">School-Based Management (SBM) Self-Assessment Checklist </h2>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->

                        <div class="row">
                            <div class="col-12">
                                <div class="card">

                                    <div class="card-body">

                                        
                                        <p class="text-justify">
                                            The SBM Self-Assessment Checklist provides a comprehensive understanding of the status of continuous improvement in the various areas of school operation. The school assesses the six (6) SBM Dimensions and determines the degree of manifestation for each SBM Indicator.
                                            These indicators are listed as observable school practices and attainable learning outcomes. The extent by which the indicators are manifested is describe as follows: <i>not yet manifested</i>, <i>rarely manifested</i>, <i>frequently manifested</i>, and <i>always manifested</i>.
                                        </p>

                                        <div class="clearfix"></div>

                                        
                                    </div>
                                </div>
                            </div>
                        </div>


                        

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
                        <?= form_open('Page/sbm_checklist_district', $att); ?>

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body table-responsive">
                                        
                                    <div id="accordion" class="mb-3">

                                        <?php $ivy=1; $ivan=1; foreach($sbm as $row){?>
                                        <div class="card mb-0">
                                            <div class="card-header" id="headingOne">
                                                <h6 class="m-0">
                                                    <a href="#collapse<?= $row->id; ?>" class="text-dark" data-toggle="collapse"
                                                            aria-expanded="true"
                                                            aria-controls="collapse<?= $row->id; ?>">
                                                        <?= $row->indicator; ?>
                                                    </a>
                                                </h6>
                                                
                                            </div>

                                            <div id="collapse<?= $row->id; ?>" class="collapse <?php if($row->id == 1){echo 'show';} ?>" aria-labelledby="headingOne" data-parent="#accordion">
                                                <div class="card-body">
                                                                <blockquote class="blockquote border-0">
                                                                <p><?= $row->description; ?></p>
                                                                </blockquote>

                                                                <div class="table-responsive">
                                                                        <table class="table table-borderless mb-0">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th rowspan="2" colspan="2"><h4 class="header-title mb-4">SBM Indicator</h4></th>
                                                                                    <th colspan="4" class="text-center">Degree of Manifestation<br /><cite title="Source Title">(Please tick the box that best describes your school)</cite></th>
                                                                                    <?php if($this->session->position == 'District' || $this->session->position == 'smme'){?><th rowspan="2" class="text-center">Remarks</th><?php } ?>
                                                                                </tr>
                                                                                <tr>
                                                                                    <th class="text-center">Not Yet Manifested</th>
                                                                                    <th class="text-center">Rarely Manifested</th>
                                                                                    <th class="text-center">Frequently Manifested</th>
                                                                                    <th class="text-center">Always Manifested</th>
                                                                                </tr>
                                                                            </thead>
                                                                            
                                                                            <tbody>
                                                                                <?php 
                                                                                    $question = $this->Common->one_cond('sbm_sub_indicator','priciple_id',$row->id);
                                                                                    $no = 1;
                                                                                    $c = 0;
                                                                                    $n = $sbmc->q1;
                                                                                    foreach($question as $sub_row){
                                                                                        $name = 'q'.$sub_row->i_no; 
                                                                                        

                                                                                ?>
                                                                                <tr <?php echo (++$c%2 ? "" : "class='table-active'"); ?>>
                                                                                    <td><?= $sub_row->i_no; ?></td>
                                                                                    <td><?= $sub_row->description; ?></td>
                                                                                    <td class="text-center"><input type="radio" name="q<?=$sub_row->i_no; ?>" <?php if($sbmc->$name == 1){echo " checked ";} ?> value="1" required></td>
                                                                                    <td class="text-center"><input type="radio" name="q<?=$sub_row->i_no; ?>" <?php if($sbmc->$name == 2){echo " checked ";} ?> value="2" required></td>
                                                                                    <td class="text-center"><input type="radio" name="q<?=$sub_row->i_no; ?>" <?php if($sbmc->$name == 3){echo " checked ";} ?> value="3" required></td>
                                                                                    <td class="text-center"><input type="radio" name="q<?=$sub_row->i_no; ?>" <?php if($sbmc->$name == 4){echo " checked ";} ?> value="4" required></td>
                                                                                    <?php if($this->session->position == 'District' || $this->session->position == 'smme'){?><td><textarea class="form-control" name="r<?=$sub_row->i_no; ?>" rows="2" id="example-textarea"></textarea></td><?php } ?>
                                                                                </tr>
                                                                                <?php } ?>
                                                                                
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                    


                                                </div>
                                            </div>
                                        </div>
                                        <?php } ?>
                                        <input type="hidden" value="<?= $sbmc->id; ?>" name="id">
                                        <?php if($this->session->position != 'School'){?>
                                        <input type="hidden" value="<?= $this->uri->segment(3); ?>" name="school_id">
                                        <?php } ?>
                                    
                                   
                                    </div>


                                    <div class="form-group text-left mb-0">
                                               <input type="submit" name="submit" value="Update" class="btn btn-primary waves-effect waves-light mr-1">
                                                
                                               
                                            </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end row -->
                                                                                    </form>

                    </div>
                    <!-- end container-fluid -->

                </div>
                <!-- end content -->

                



            <?php include('templates/footer.php'); ?>


            