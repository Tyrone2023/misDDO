
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
                                    <button type="button" class="btn btn-success waves-effect waves-light" data-toggle="modal" data-target=".aa">Search</button>
                                    <!-- <?php echo '<a href="<?= base_url(); ?><?= $link; ?>" class="btn btn-success waves-effect width-md waves-light"><?= $pn; ?></a>' ?> -->
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body table-responsive">
                                    <h4 class="header-title mb-4"><?= $title; ?></h4>

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

                                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">

                                            <thead>
                                                <tr>
                                                    <th>Username</th> 
                                                    <th>Used Password</th> 
                                                    <th>Date</th>
                                                    <th>Acct Level</th> 
                                                    <th>Log Type</th> 
                                                    <th>Status</th> 
                                                    
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <?php foreach($page as $row){ 
                                                   ?>
                                                    <td><?= $row->username; ?></td> 
                                                    <td><?= $row->used_pass; ?></td> 
                                                    <td><?= $row->transDate; ?></td> 
                                                    <td><?= $row->acctLevel; ?></td> 
                                                    <td><?= $row->logType; ?></td> 
                                                    <td><?= $row->logStat; ?></td> 
                                                  
                                                    
                                                </tr>
                                                <?php } ?>
                                           
                                                
                                            </tbody>
                                        </table>

                                        


                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end row -->



                    </div>
                    <!-- end container-fluid -->

                </div>
                <!-- end content -->

                <!--  Search Applicantion -->
                <div class="modal fade aa" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="display: none;" aria-hidden="true">
                                                    <div class="modal-dialog modal-lg">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="myLargeModalLabel">Search</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                            </div>
                                                            <div class="modal-body">
                                                                
                                                            <?php 
                                                                $attributes = array('class' => 'parsley-examples');
                                                                echo form_open('Pages/mis_logs', $attributes);
                                                            ?>
                                                              
                                                                <div class="form-group row">
                                                                    <label for="inputPassword5" class="col-md-3 col-form-label">Username</label>
                                                                    <div class="col-md-9">
                                                                        <input type="text" class="form-control" name="username" >
                                                                    </div>
                                                                </div>


                                                                <?php  $position = $this->session->position; if($position==='Evaluator'): ?>

                                                                <div class="form-group row">
                                                                    <label for="inputPassword5" class="col-md-3 col-form-label">Year</label>
                                                                    <div class="col-md-9">
                                                                    <select class="form-control" name="fy" required>
                                                                        <option></option>
                                                                    <?php 
                                                                    $firstYear = (int)date('Y');
                                                                    $lastYear = $firstYear + 5;
                                                                    
                                                                    for($i=$firstYear;$i<=$lastYear;$i++)
                                                                    { 
                                                                        echo '<option';
                                                                        if($i == date('Y')){echo " selected ";}
                                                                        echo ' value='.$i.'>'.$i.'</option>';
                                                                    }
                                                                    ?>
                                                                    </select>
                                                                    </div>
                                                                </div>

                                                                <?php endif; ?>
                                                            
                                                                <div class="form-group mb-0 justify-content-end row">
                                                                    <div class="col-md-9">
                                                                        <input type="submit" name="submit" value="Submit" class="btn btn-primary waves-effect waves-light">
                                                                        <!-- <button type="submit" class="btn btn-info waves-effect waves-light">Submit</button> -->
                                                                    </div>
                                                                </div>
                                                            </form>

                                                            </div>
                                                        </div>
                                                        <!-- /.modal-content -->
                                                    </div>
                                                    <!-- /.modal-dialog -->
                                            </div>
                                        <!-- /.modal -->

             
 