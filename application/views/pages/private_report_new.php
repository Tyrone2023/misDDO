

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
                                                                        echo "<option value=\"$year\">$year</option>";
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
                                                                    <option value="1">First Quarter</option>
                                                                    <option value="2">Second Quarter</option>
                                                                    <option value="3">Third Quarter</option>
                                                                    <option value="4">Fourth Quarter</option>
                                                                    </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label class="col-lg-8 col-form-label">Percentage of the school leaver rate attributed to teenage pregnancy</label>
                                                            <div class="col-lg-2">
                                                                <input type="text" name="mpregnancy" class="form-control number-only" value="" placeholder='Number of Male'>
                                                            </div>
                                                            <div class="col-lg-2">
                                                                <input type="text" name="fpregnancy" class="form-control number-only" value="" placeholder='Number of Female'>
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label class="col-lg-8 col-form-label">Teacher's in Private Schools Subsidy</label>
                                                            <div class="col-lg-2">
                                                                <input type="text" name="msubsidy" class="form-control number-only" value="" placeholder='Number of Male'>
                                                            </div>
                                                            <div class="col-lg-2">
                                                                <input type="text" name="fsubsidy" class="form-control number-only" value="" placeholder='Number of Female'>
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label class="col-lg-8 col-form-label">number of Teachers Let Passers</label>
                                                            <div class="col-lg-2">
                                                                <input type="text" name="mletpass" class="form-control number-only" value="" placeholder='Number of Male'>
                                                            </div>
                                                            <div class="col-lg-2">
                                                                <input type="text" name="fletpass" class="form-control number-only" value="" placeholder='Number of Female'>
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label class="col-lg-8 col-form-label">number of Teachers Not-Let Passers</label>
                                                            <div class="col-lg-2">
                                                                <input type="text" name="mnotletpass" class="form-control number-only" value="" placeholder='Number of Male'>
                                                            </div>
                                                            <div class="col-lg-2">
                                                                <input type="text" name="fnotletpass" class="form-control number-only" value="" placeholder='Number of Female'>
                                                            </div>
                                                        </div>
                                                       

                                                        
                                                      <input type="hidden" name="delivery" class="form-control number-only" value="0" placeholder=''>
                                                        

                                                        <div class="form-group row">
                                                            <label class="col-lg-8 col-form-label">Schools implementing full in-persons classes( 1 if full in-person and 0 if Not)</label>
                                                            <div class="col-lg-4">
                                                                <input type="text" name="full_persons_classes" class="form-control number-only" value="" placeholder=''>
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label class="col-lg-8 col-form-label">Amount Allocation from PEAC(SHS VP)</label>
                                                            <div class="col-lg-4">
                                                                <input type="text" name="peac" class="form-control number-only" value="" placeholder=''>
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label class="col-lg-8 col-form-label">Amount Allocation for ESC</label>
                                                            <div class="col-lg-4">
                                                                <input type="text" name="esc" class="form-control number-only" value="" placeholder=''>
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label class="col-lg-8 col-form-label">Amount Allocation for GASTPE(TSS)</label>
                                                            <div class="col-lg-4">
                                                                <input type="text" name="gastpe" class="form-control number-only" value="" placeholder=''>
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label class="col-lg-8 col-form-label">Schools implementing blended learning</label>
                                                            <div class="col-lg-4">
                                                                <input type="text" name="blended_learning" class="form-control number-only" value="" placeholder=''>
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label class="col-lg-8 col-form-label">Schools implementing full distance learning</label>
                                                            <div class="col-lg-4">
                                                                <input type="text" name="distrance_learning" class="form-control number-only" value="" placeholder=''>
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label class="col-lg-8 col-form-label">File</label>
                                                            <div class="col-lg-4">
                                                                <input type="file" name="file" class="form-control" placeholder="File">
                                                            </div>
                                                        </div>

                                                        <button type="submit" class="btn btn-primary">Submit</button>
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

   

 