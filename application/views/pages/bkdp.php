
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
                                    <h2 class="text-center"><?= $title; ?><br /><?= $sub; ?></h2>
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
                        <?= form_open('Pdopage/bkdp', $att); ?>


                        <?php 
                             $school = $this->Common->one_cond_row_select('schools','schoolID, district', 'schoolID', $this->session->c_id);
                             $dis = $this->Common->one_cond_row_select('district','id, discription', 'discription', $school->district);
                        ?>

                        <input type="hidden" name="school_id" value="<?= $this->session->c_id; ?>">
                        <input type="hidden" name="fy" value="<?= date('Y'); ?>">
                        <input type="hidden" name="district" value="<?= $dis->id; ?>">


                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body table-responsive">
                                        <h4>Please respond to the following questions by placing a check mark (√) in the space provided that corresponds to your answers and/ or fill in the blank where indicated.</h4>
                                        <br />
                                        <table class="table table-bordered mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Name/Title of PPAs</th>
                                                    <th>Evident</th>
                                                    <th>Not Evident</th>
                                                    <th>Remarks / Observations</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach($data as $row){
                                                    $remarks = 'r'.$row->id;
                                                    $q = 'q'.$row->id;
                                                    ?>
                                                <tr>
                                                    <td><?= $row->description; ?></td>
                                                    <td class="text-center"><input type="radio" name="q<?= $row->id; ?>" <?php if(!empty($exist)){if($exist->$q == 1){echo " checked ";}} ?> value="1" ></td>
                                                    <td class="text-center"><input  type="radio" name="q<?= $row->id; ?>"  <?php if(!empty($exist)){if($exist->$q == 2){echo " checked ";}} ?> value="2" ></td>
                                                    <td><textarea class="form-control" name="r<?= $row->id; ?>" rows="1" id="example-textarea"><?php if(!empty($exist)){echo $exist->$remarks;} ?></textarea></td>
                                                </tr>
                                                <?php } ?>
                                                
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body table-responsive">
                                        <h5>Best Practices:</h5>
                                        <textarea class="form-control" name="bp" rows="3" id="example-textarea"><?php if(!empty($exist)){echo $exist->bp;} ?></textarea>
                                      
                                        
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body table-responsive">
                                        <h5>Issues and Concerns:</h5>
                                        <textarea class="form-control" name="ic" rows="3" id="example-textarea"><?php if(!empty($exist)){echo $exist->ic;} ?></textarea>
                                       
                                        
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body table-responsive">
                                        
                                        <h5>Suggestions/Recommendations:</h5>
                                        <textarea class="form-control" name="sr" rows="3" id="example-textarea"><?php if(!empty($exist)){echo $exist->sr;} ?></textarea>
                                        <br />
                                        
                                        
                                        <div class="form-group text-left mb-0">
                                            <?php if(empty($exist)){?>
                                            <input type="submit" name="submit" value="Submit" class="btn btn-primary waves-effect waves-light mr-1">
                                            <?php }else{?>
                                                <input type="submit" name="update" value="Update" class="btn btn-primary waves-effect waves-light mr-1">
                                            <?php } ?>
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



            