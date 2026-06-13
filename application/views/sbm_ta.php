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
                                    <h2 class="text-center">TECHNICAL ASSISSTANCE PROVISION REPORT FORM</h2>
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
                        <?= form_open('Page/tapr_form', $att); ?>

                        <input type="hidden" name="district" value="<?= $district->id;?>">

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
                                                                                <tr class="text-center">
                                                                                    <th colspan='2'>SBM Indicator</th>
                                                                                    <th>Degree Of Manifestation</th>
                                                                                    <th>Concerns, Issues, Gaps, Problems and Bottlenecks (CIGPB s) Encountered</th>
                                                                                    <th>Facilitating Factors for Indicators with always manifested </th>
                                                                                    <th>Category (Technical, Institutional, Financial, Political, Infrastructure, Social, Gender)</th>
                                                                                    <th>Proposed Resolutions/Commitment</th>
                                                                                </tr>
                                                                            </thead>
                                                                            
                                                                            <tbody>
                                                                                <?php 
                                                                                    $dm = array(
                                                                                        '1'=>'Not Yet Manifested',
                                                                                        '2'=>'Rarely Manifested',
                                                                                        '3'=>'Frequently Manifested',
                                                                                        '4'=>'Always Manifested'
                                                                                    );

                                                                                    $cat = array('Technical', 'Institutional', 'Financial', 'Political', 'Infrastructure', 'Social', 'Gender');

                                                                                    $question = $this->Common->one_cond('sbm_sub_indicator','priciple_id',$row->id);
                                                                                    $name = 'q'; 

                                                                                    $no = 1;
                                                                                    $c = 0;
                                                                                    
                                                                                    foreach($question as $sub_row){
                                                                                        $sbm = $this->Common->two_cond_row('sbm','school_id',$this->session->username,'fy',$_SESSION['sbm_fy']);

                                                                                        $sbm_col = 'q'.$sub_row->i_no;

                                                                                ?>
                                                                                <tr <?php echo (++$c%2 ? "" : "class='table-active'"); ?>>
                                                                                    <td><?= $sub_row->i_no; ?> </td>
                                                                                    <td><?= $sub_row->description; ?></td>
                                                                                    <td class="text-center">
                                                                                        <?php 
                                                                                        foreach($dm as $key => $row){
                                                                                            if($key == $sbm->$sbm_col){
                                                                                                echo $row;
                                                                                            }
                                                                                        }
                                                                                       ?>
                                                                                    </td>
                                                                                    <td><textarea class="form-control" name="q<?=$sub_row->i_no; ?>"  rows="2" id="example-textarea"></textarea></td>
                                                                                    <td><textarea class="form-control" name="qq<?=$sub_row->i_no; ?>" rows="2" id="example-textarea"></textarea></td>
                                                                                    <td>
                                                                                    <select class="form-control" name="a<?=$sub_row->i_no; ?>" >
                                                                                        <option></option>
                                                                                        <?php foreach($cat as $row){ ?>
                                                                                            <option value="<?= $row; ?>"><?= $row; ?></option>
                                                                                        <?php }?>
                                                                                    </select>
                                                                                    </td>
                                                                                    <td><textarea class="form-control" name="f<?=$sub_row->i_no; ?>" rows="2" id="example-textarea"></textarea></td>
                                                                                </tr>
                                                                                <?php } ?>
                                                                                
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                    


                                                </div>
                                            </div>
                                        </div>
                                        <?php } ?> 
                                    
                                   
                                    </div>


                                    <div class="form-group text-left mb-0">
                                               <input type="submit" name="submit" value="Submit" class="btn btn-primary waves-effect waves-light mr-1">
                                                
                                               
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


            