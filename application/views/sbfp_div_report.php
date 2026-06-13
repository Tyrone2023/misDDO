                    

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

                                    <h4></h4>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->

                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box">
                                <div class="card">
                                    <div class="card-body">

                                                    <?= form_open('Sbfp/sbfp_nut_stat', ['target' => '_blank']); ?>

                                                        <div class="form-group row">
                                                            <div class="col-lg-4">
                                                                <label >District Name </label>
                                                                <select class="form-control" name="d_id" data-toggle="select2" required >
                                                                    <option></option>
                                                                    <?php foreach($district as $row){?>
                                                                        <option value="<?= $row->id; ?>"><?= $row->discription; ?></option>
                                                                    <?php }?>
                                                                    
                                                                </select>
                                                            </div>
                                                        </div>
                                                        
                                        

                                                        
                                                        
   
                                                        <div class="form-group row">
                                                            <div class="col-lg-12">
                                                                <input type="submit" name="submit" class="btn btn-primary waves-effect waves-light" value="Submit" />
                                                            </div>
                                                        </div>
                                                    </form>
                                    </div>
                                </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                                    


                      

                        

                    </div>
                    <!-- end container-fluid -->

                </div>
                <!-- end content -->   

             
 