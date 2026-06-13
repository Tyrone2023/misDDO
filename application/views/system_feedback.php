            <?php include('templates/head.php'); ?> 
            <link href="<?= base_url(); ?>assets/libs/custombox/custombox.min.css" rel="stylesheet" type="text/css">
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
                                      
                                                
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->

                        <?php if($this->session->flashdata('success')) : ?>

                            <?= '<div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>'
                                    .$this->session->flashdata('success'). 
                                '</div>'; 
                            ?>
                            <?php endif; ?>

                            <?php if($this->session->flashdata('danger')) : ?>
                            <?= '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>'
                                    .$this->session->flashdata('danger'). 
                                '</div>'; 
                            ?>
                            <?php endif;  ?>

                            <form method="post">

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body table-responsive">

                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                    <div class="table-responsive">
                                        <h4 class="header-title mb-4">User Experience Feedback</h4>
                                            <table class="table table-borderless mb-0">
                                                <thead>
                                                    <tr>
                                                        <th></th>
                                                        <th class="text-center">Extremely satisfied</th>
                                                        <th class="text-center">Very satisfied</th>
                                                        <th class="text-center">Somewhat satisfied</th>
                                                        <th class="text-center">Not so satisfied</th>
                                                        <th class="text-center">Not at all satisfied</th>
                                                    </tr>
                                                </thead>
                                                
                                                <tbody>
                                                    <?php $q = array("How satisfied are you with this software's ease of use?",
                                                                    "How satisfied are you with the look and feel of this software?",
                                                                    "How satisfied are you with the value for the money of this software?",
                                                                    "How satisfied are you with the security of this software?"

                                                    ); 
                                                    $c = 1;
                                                    foreach($q as $row){
                                                    $count = $c++;
                                                    ?>
                                                    <tr>
                                                        <td><?= $row; ?><input type="radio" name="q<?= $count; ?>Ans" checked value="0" style="opacity: 0;"></td>
                                                        <td class="text-center"><input type="radio" name="q<?= $count; ?>Ans" value="5"></td>
                                                        <td class="text-center"><input type="radio" name="q<?= $count; ?>Ans" value="4"></td>
                                                        <td class="text-center"><input type="radio" name="q<?= $count; ?>Ans" value="3"></td>
                                                        <td class="text-center"><input type="radio" name="q<?= $count; ?>Ans" value="2"></td>
                                                        <td class="text-center"><input type="radio" name="q<?= $count; ?>Ans" value="1"></td>
                                                    </tr>
                                                    <?php } ?>
                                                    
                                                                                                        
                                                </tbody>
                                            </table>

                                            
                                        </div>
                                        
                                    </div>

                      
                                </div>
                                <!-- end card -->

                                        
                                            
                                            <!-- <div class="form-group">
                                                <label for="inputAddress" class="col-form-label" name="q1">How satisfied are you with this software's ease of use?</label>
                                                <select class="form-control" name="q1Ans" required> 
                                                    <option></option>
                                                    <option>Extremely satisfied</option>
                                                    <option>Very satisfied</option>
                                                    <option>Somewhat satisfied</option>
                                                    <option>Not so satisfied</option>
                                                    <option>Not at all satisfied</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="inputAddress" class="col-form-label" name="q2">How satisfied are you with the look and feel of this software?</label>
                                                <select class="form-control" name="q2Ans" required> 
                                                    <option></option>
                                                    <option>Extremely satisfied</option>
                                                    <option>Very satisfied</option>
                                                    <option>Somewhat satisfied</option>
                                                    <option>Not so satisfied</option>
                                                    <option>Not at all satisfied</option>
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label for="inputAddress" class="col-form-label" name="q3">How satisfied are you with the value for the money of this software?</label>
                                                <select class="form-control" name="q3Ans" required> 
                                                    <option></option>
                                                    <option>Extremely satisfied</option>
                                                    <option>Very satisfied</option>
                                                    <option>Somewhat satisfied</option>
                                                    <option>Not so satisfied</option>
                                                    <option>Not at all satisfied</option>
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label for="inputAddress" class="col-form-label" name="q4">How satisfied are you with the security of this software?</label>
                                                <select class="form-control" name="q4Ans" required> 
                                                    <option></option>
                                                    <option>Extremely satisfied</option>
                                                    <option>Very satisfied</option>
                                                    <option>Somewhat satisfied</option>
                                                    <option>Not so satisfied</option>
                                                    <option>Not at all satisfied</option>
                                                </select>
                                            </div> -->

                                            <div class="form-group">
                                                <label for="inputAddress" class="col-form-label" name="q5">Do you have any thoughts on how to improve this software?</label>
                                                <textarea class="form-control" name="q5Ans" rows="4" cols="50" ></textarea>
                                            </div>
                                           
                                           
                                            <input type="submit" name="submit" value="Submit" class="btn btn-primary">
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end row -->
                        

                                    

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

 