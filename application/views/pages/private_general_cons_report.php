

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



                                        <div class="row">
                                            <div class="col-12">
                                                <div class="">
                                                        <?= validation_errors(); ?>

                                                        <?php
                                                        $attributes = array('class' => 'parsley-examples form-horizontal');
                                                        echo form_open_multipart('Ps/private_report_add', $attributes);
                                                        ?>

                                                      
                                                        <div class="form-group row">
                                                            <label class="col-lg-8 col-form-label"></label>
                                                            <div class="col-lg-2">
                                                                <label class="col-form-label">Number of Male</label>
                                                            </div>
                                                            <div class="col-lg-2">
                                                                <label class="col-form-label">Number of Female</label>
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label class="col-lg-8 col-form-label">Percentage of the school leaver rate attributed to teenage pregnancy</label>
                                                            <div class="col-lg-2">
                                                                <input type="text" name="mpregnancy" disabled class="form-control number-only" value="<?= $total->mpregnancy_total; ?>" placeholder='Number of Male'>
                                                            </div>
                                                            <div class="col-lg-2">
                                                                <input type="text" name="fpregnancy" disabled class="form-control number-only" value="<?= $total->fpregnancy_total; ?>" placeholder='Number of Female'>
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label class="col-lg-8 col-form-label">Teacher's in Private Schools Subsidy</label>
                                                            <div class="col-lg-2">
                                                                <input type="text" name="msubsidy" disabled class="form-control number-only" value="<?= $total->msubsidy_total; ?>" placeholder='Number of Male'>
                                                            </div>
                                                            <div class="col-lg-2">
                                                                <input type="text" name="fsubsidy" disabled class="form-control number-only" value="<?= $total->fsubsidy_total; ?>" placeholder='Number of Female'>
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label class="col-lg-8 col-form-label">number of Teachers Let Passers</label>
                                                            <div class="col-lg-2">
                                                                <input type="text" name="mletpass" disabled class="form-control number-only" value="<?= $total->mletpass_total; ?>" placeholder='Number of Male'>
                                                            </div>
                                                            <div class="col-lg-2">
                                                                <input type="text" name="fletpass" disabled class="form-control number-only" value="<?= $total->mletpass_total; ?>" placeholder='Number of Female'>
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label class="col-lg-8 col-form-label">number of Teachers Not-Let Passers</label>
                                                            <div class="col-lg-2">
                                                                <input type="text" name="mnotletpass" disabled class="form-control number-only" value="<?= $total->mnotletpass_total; ?>" placeholder='Number of Male'>
                                                            </div>
                                                            <div class="col-lg-2">
                                                                <input type="text" name="fnotletpass" disabled class="form-control number-only" value="<?= $total->fnotletpass_total; ?>" placeholder='Number of Female'>
                                                            </div>
                                                        </div>
                                                       

                                                        
                                                      <input type="hidden" name="delivery" class="form-control number-only" value="0" placeholder=''>
                                                        

                                                        <div class="form-group row">
                                                            <label class="col-lg-8 col-form-label">Schools implementing full in-persons classes( 1 if full in-person and 0 if Not)</label>
                                                            <div class="col-lg-4">
                                                                <input type="text" disabled name="full_persons_classes" class="form-control number-only" value="<?= $total->full_persons_classes_total; ?>" placeholder=''>
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label class="col-lg-8 col-form-label">Amount Allocation from PEAC(SHS VP)</label>
                                                            <div class="col-lg-4">
                                                                <input type="text" disabled name="peac" class="form-control number-only" value="<?= ($total->peac_total != 0) ? number_format($total->peac_total) : ''; ?>" placeholder=''>
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label class="col-lg-8 col-form-label">Amount Allocation for ESC</label>
                                                            <div class="col-lg-4">
                                                                <input type="text" disabled name="esc" class="form-control number-only" value="<?= ($total->esc_total != 0) ? number_format($total->esc_total) : ''; ?>" placeholder=''>
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label class="col-lg-8 col-form-label">Amount Allocation for GASTPE(TSS)</label>
                                                            <div class="col-lg-4">
                                                                <input type="text" disabled name="gastpe" class="form-control number-only" value="<?= ($total->gastpe_total != 0) ? number_format($total->gastpe_total) : ''; ?>" placeholder=''>
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label class="col-lg-8 col-form-label">Schools implementing blended learning</label>
                                                            <div class="col-lg-4">
                                                                <input type="text" disabled name="blended_learning" class="form-control number-only" value="<?= $total->blended_learning_total; ?>" placeholder=''>
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label class="col-lg-8 col-form-label">Schools implementing full distance learning</label>
                                                            <div class="col-lg-4">
                                                                <input type="text" disabled name="distrance_learning" class="form-control number-only" value="<?= $total->distance_learning_total; ?>" placeholder=''>
                                                            </div>
                                                        </div>

                                                        

                                                    </form>
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

                <script>
                    const inputs = document.querySelectorAll('.number-only');

                    inputs.forEach(input => {
                        input.addEventListener('input', function () {
                        this.value = this.value.replace(/[^0-9]/g, '');
                        });
                    });
                </script>

   

 