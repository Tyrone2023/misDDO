                    

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

                                    <h4>IMPLEMENTATION PLANS</h4>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->


                         <div class="row">
                            <div class="col-xl-12">
                                <div class="card">
                                    <div class="card-body">

                                        <div class="button-list">
                                        <a data-toggle="modal" data-field="" data-id="" class="open-AddBookDialog btn btn-success" href="#aip">ANNUAL IMPLEMENTATION PLAN (AIP)</a>
                                            <?php if(isset($_SESSION['aip'])){?>
                                                <a href="<?= base_url(); ?>Page/sop" class="open-AddBookDialog btn btn-info" >SCHOOL OPERATIONAL PLAN (SOP)</a>
                                                <a href="<?= base_url(); ?>Page/view_app" class="open-AddBookDialog btn btn-secondary" >ANNUAL PROCUREMENT PLAN (APP)</a><br />
                                                
                                                <a data-toggle="modal" data-field="" data-id="" class="open-AddBookDialog btn btn-purple" href="#rca">REQUEST FOR CASH ADVANCE (RCA)</a>
                                                <a href="<?= base_url(); ?>Page/generate_ppmp" class="open-AddBookDialog btn btn-primary" >PROJECT PROCUREMENT MANAGEMENT PLAN (PPMP)</a>
                                                <a href="<?= base_url(); ?>Page/smeav2" class="open-AddBookDialog btn btn-success" >SMEA</a>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php if(isset($_SESSION['aip'])){?>
                        <!-- start page title -->
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box">

                                    <h4>LINKS TO THE GENERATED IMPLEMENTATION PLANS</h4>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->

                        <?php $sap = $this->SGODModel->two_cond_row('sgod_app_percentage','b_code',$_SESSION['aip'],'fy',$_SESSION['fy']); ?>
                         <div class="row">
                            <div class="col-xl-12">
                                <div class="card">
                                    <div class="card-body">

                                        <div class="button-list">
                                                <a href="<?= base_url(); ?>Page/generate_aip" class="open-AddBookDialog btn btn-success" target="_blank">ANNUAL IMPLEMENTATION PLAN (AIP)</a>
                                                <a href="<?= base_url(); ?>Page/generate_sop" class="open-AddBookDialog btn btn-info" target="_blank">SCHOOL OPERATIONAL PLAN (SOP)</a><br />
                                                <?php if(!isset($sap->id)){?>
                                                    <a class="btn btn-primary" href="#">ANNUAL PROCUREMENT PLAN (APP)</a>
                                                <?php }else{ ?>
                                                    <a class="btn btn-primary" href="<?= base_url(); ?>Page/generate_app"  target="_blank">ANNUAL PROCUREMENT PLAN (APP)</a>
                                                <?php } ?>  
                                                <a href="<?= base_url(); ?>Page/generate_ppmp" class="open-AddBookDialog btn btn-primary" target="_blank">PROJECT PROCUREMENT MANAGEMENT PLAN (PPMP)</a>
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php } ?>

                        <div id="aip" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="myModalLabel">FILTER ANNUAL IMPLEMENTATION PLAN </h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <?= form_open('Page/implementation_plans'); ?>
                                                         <div class="form-group">
                                                            <label>School ID</label>
                                                            <input type="text" name="school_id" readonly value="<?= $this->session->username; ?>" required class="form-control">
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Fiscal  Year</label>
                                                            <input type="text" name="fy" value="<?= $fys; ?>" required class="form-control">
                                                        </div>
                                                        <div class="form-group">
                                                            <label>BATCH CODE</label>
                                                            <select class="form-control" name="code" required>
                                                                <option></option>
                                                                <?php foreach($ssa as $row){
                                                                    echo "<option value='".$row->alloc_batch."'>".$row->alloc_batch." - ". $row->alloc_group." : PHP ".number_format($row->alloc_amount)."</option>";
                                                                   }
                                                                ?>
                                                                
                                                            </select>
                                                        </div>

                                                        
                                                    <div class="modal-footer">
                                                        <input type="submit" name="aip" class="btn btn-primary waves-effect waves-light" value="Submit" />
                                                    </div>
                                                    </form>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                         </div>


                                         <div id="rca" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="myModalLabel">FILTER ANNUAL IMPLEMENTATION PLAN </h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <?= form_open('Page/generate_rca', ['target' => '_blank']); ?>
                                                         

                                                        <div class="form-group">
                                                            <label>SELECT MONTH</label>
                                                            <select class="form-control" name="month" required>
                                                                <option></option>
                                                                <?php 
                                                                    $month = array('January' => 'jan', 'February' => 'feb', 'March' => 'mar', 'April'=> 'april', 'May' => 'may', 'June' => 'june', 'July' => 'july', 'August' => 'aug', 'September' => 'sept', 'October' => 'oct', 'November' => 'nov', 'December' => 'ddec'); 
                                                                    foreach($month as $m => $val){
                                                                ?>
                                                                <option value="<?= $val; ?>"><?= $m; ?></option>
                                                                <?php } ?> 
                                                                
                                                            </select>
                                                        </div>

                                                        
                                                    <div class="modal-footer">
                                                        <input type="submit" name="aip" class="btn btn-primary waves-effect waves-light" value="Submit" />
                                                    </div>
                                                    </form>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                         </div>

                        

                    </div>
                    <!-- end container-fluid -->

                </div>
                <!-- end content -->

                <?php include('templates/footer.php'); ?>       

             
 