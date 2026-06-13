

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
                                                        echo form_open_multipart('Ps/private_report_update', $attributes);
                                                        ?>
                                                        <div class="form-group row">
                                                            <label class="col-lg-8 col-form-label">Fiscal Year</label>
                                                            <div class="col-lg-4">
                                                                <?php
                                                                $currentYear = date("Y")+1; 
                                                                $earliestYear = $currentYear - 6; 
                                                                ?>
                                                                <select name="year" class="form-control" required>
                                                                <option value="" disabled selected>Select Year</option>
                                                                <?php
                                                                    for ($year = $currentYear; $year >= $earliestYear; $year--) {
                                                                        echo "<option";
                                                                        if($data->year == $year){echo ' selected'; }
                                                                        echo " value=\"$year\">$year</option>";
                                                                    }
                                                                ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label class="col-lg-8 col-form-label">Quarter</label>
                                                            <div class="col-lg-4">
                                                                <select class="form-control" name="quarter" required>
                                                                    <option value="" disabled selected>Select Quarter</option>
                                                                    <option <?= ($data->quarter == 1) ? 'selected' : '' ?> value="1">First Quarter</option>
                                                                    <option <?= ($data->quarter == 2) ? 'selected' : '' ?> value="2">Second Quarter</option>
                                                                    <option <?= ($data->quarter == 3) ? 'selected' : '' ?> value="3">Third Quarter</option>
                                                                    <option <?= ($data->quarter == 4) ? 'selected' : '' ?> value="4">Fourth Quarter</option>
                                                                    </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label class="col-lg-8 col-form-label">Percentage of the school leaver rate attributed to teenage pregnancy</label>
                                                            <div class="col-lg-2">
                                                                <input type="text" name="mpregnancy" class="form-control number-only" value="<?= $data->mpregnancy; ?>" placeholder='Number of Male'>
                                                            </div>
                                                            <div class="col-lg-2">
                                                                <input type="text" name="fpregnancy" class="form-control number-only" value="<?= $data->fpregnancy; ?>" placeholder='Number of Female'>
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label class="col-lg-8 col-form-label">Teacher's in Private Schools Subsidy</label>
                                                            <div class="col-lg-2">
                                                                <input type="text" name="msubsidy" class="form-control number-only" value="<?= $data->msubsidy; ?>" placeholder='Number of Male'>
                                                            </div>
                                                            <div class="col-lg-2">
                                                                <input type="text" name="fsubsidy" class="form-control number-only" value="<?= $data->msubsidy; ?>" placeholder='Number of Female'>
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label class="col-lg-8 col-form-label">number of Teachers Let Passers</label>
                                                            <div class="col-lg-2">
                                                                <input type="text" name="mletpass" class="form-control number-only" value="<?= $data->mletpass; ?>" placeholder='Number of Male'>
                                                            </div>
                                                            <div class="col-lg-2">
                                                                <input type="text" name="fletpass" class="form-control number-only" value="<?= $data->fletpass; ?>" placeholder='Number of Female'>
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label class="col-lg-8 col-form-label">number of Teachers Not-Let Passers</label>
                                                            <div class="col-lg-2">
                                                                <input type="text" name="mnotletpass" class="form-control number-only" value="<?= $data->fnotletpass; ?>" placeholder='Number of Male'>
                                                            </div>
                                                            <div class="col-lg-2">
                                                                <input type="text" name="fnotletpass" class="form-control number-only" value="<?= $data->fnotletpass; ?>" placeholder='Number of Female'>
                                                            </div>
                                                        </div>
                                                       

                                                        <div class="form-group row">
                                                            <label class="col-lg-8 col-form-label">No. of policies reviewed and implemented in all areas and levels of educational services delivery</label>
                                                            <div class="col-lg-4">
                                                                <input type="text" name="delivery" class="form-control number-only" value="<?= $data->delivery; ?>" placeholder=''>
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label class="col-lg-8 col-form-label">Schools implementing full in-persons classes</label>
                                                            <div class="col-lg-4">
                                                                <input type="text" name="full_persons_classes" class="form-control number-only" value="<?= $data->full_persons_classes; ?>" placeholder=''>
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label class="col-lg-8 col-form-label">Amount Allocation from PEAC</label>
                                                            <div class="col-lg-4">
                                                                <input type="text" name="peac" class="form-control number-only" value="<?= $data->peac; ?>" placeholder=''>
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label class="col-lg-8 col-form-label">Amount Allocation for ESC</label>
                                                            <div class="col-lg-4">
                                                                <input type="text" name="esc" class="form-control number-only" value="<?= $data->esc; ?>" placeholder=''>
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label class="col-lg-8 col-form-label">Amount Allocation for GASTPE</label>
                                                            <div class="col-lg-4">
                                                                <input type="text" name="gastpe" class="form-control number-only" value="<?= $data->gastpe; ?>" placeholder=''>
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label class="col-lg-8 col-form-label">Schools implementing blended learning</label>
                                                            <div class="col-lg-4">
                                                                <input type="text" name="blended_learning" class="form-control number-only" value="<?= $data->blended_learning; ?>" placeholder=''>
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label class="col-lg-8 col-form-label">Schools implementing full distance learning</label>
                                                            <div class="col-lg-4">
                                                                <input type="text" name="distrance_learning" class="form-control number-only" value="<?= $data->distrance_learning; ?>" placeholder=''>
                                                            </div>
                                                        </div>

                                                        

                                                        <input type="hidden" name="id" value="<?= $data->id; ?>">

                                                        <button type="submit" class="btn btn-primary">Update</button>
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

   

 