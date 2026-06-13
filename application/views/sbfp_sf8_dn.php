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

                                                    <?= form_open('page/sbfp_sf8_report_dn'); ?>

                                                        <div class="form-group row">
                                                            <div class="col-lg-5">
                                                                <label >Fiscal Year  </label>
                                                                <select class="form-control" name="sy" required>
                                                                    <option></option>
                                                                    <?php foreach($sy as $row){?>
                                                                        <option value="<?= $row->SY; ?>"><?= $row->SY; ?></option>
                                                                    <?php }?>
                                                                    
                                                                </select>
                                                            </div>
                                                        </div>



                                                    <div class="form-group row">
                                                        <div class="col-lg-5">
                                                            <label>Grade Level</label>
                                                            <select class="form-control" name="YearLevel" required>
                                                                <option></option>
                                                                <?php foreach ($yl as $row) { ?>
                                                                    <option value="<?= $row->YearLevel; ?>"><?= $row->YearLevel; ?></option>
                                                                <?php } ?>

                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-lg-5">
                                                            <label>Section</label>
                                                            <select class="form-control" name="Section" required>
                                                                <option></option>
                                                                <?php foreach ($section as $row) { ?>
                                                                    <option value="<?= $row->Section; ?>"><?= $row->Section; ?></option>
                                                                <?php } ?>

                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">

                                                        <div class="col-lg-5">
                                                            <label>Track/Strand(SHS)</label>
                                                            <select class="form-control" name="track" required>
                                                                <option></option>
                                                                <?php foreach ($track as $row) { ?>
                                                                    <option value="<?= $row->Track; ?>"><?= $row->Track; ?></option>
                                                                <?php } ?>

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