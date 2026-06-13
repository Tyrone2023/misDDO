
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
                                    <h4 class="page-title"><?= $title; ?></h4>
                                 
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->

                        


                        <!-- Form row -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-body">
                                    
                                        <?= validation_errors(); ?>

                                        <?= form_open('company_edit/'. $id); ?>

                                        <div class="form-row">
                                                <div class="form-group col-md-4">
                                                    <label for="inputname" class="col-form-label">Company Name</label>
                                                    <input type="text" value="<?= $name; ?>" name="name" class="form-control">
                                                </div>
                                                <div class="form-group col-md-8">
                                                    <label for="inputname" class="col-form-label">Company Address</label>
                                                    <input type="text" value="<?= $address; ?>" name="address" class="form-control">
                                                    <input type="hidden" value="<?= $id; ?>" name="id">
                                                </div>
                                            <button type="submit" value="submit" class="btn btn-primary"><?= $title; ?></button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end row -->

                    </div>
                    <!-- end container-fluid -->

                </div>
                <!-- end content -->

                

              