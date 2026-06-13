
        <!-- ============================================================== -->
        <!-- Start Page Content here -->
        <!-- ============================================================== -->

        

        <div class="content-page" style="margin-left:0">
            <div class="content">

                <!-- Start Content-->
                <div class="container-fluid">

                    <!-- start page title -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <h4 class="page-title">Confession</h4>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>
                    

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
                    <!-- end page title -->

                    <?= validation_errors(); ?>
                    <?php $att = array('class' => 'parsley-examples'); ?>
                    <?= form_open('Pages/davor_confession', array_merge($att)); ?>




                    <!-- Form row -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">

                                    <br />
                                    <input type="hidden" valu="" name="renren">
                                    <input type="hidden" valu="" name="ivykate">
                                    <input type="hidden" valu="" name="ivankyle">
                                    <input type="hidden" valu="" name="ic">

                                    <div class="form-row">

                                        <div class="form-group col-md-12">
                                            <textarea class="form-control" rows="10" name="con" id="example-textarea" required></textarea>
                                        </div>

                                      
                                    </div>


                                    <div class="form-row">
                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label for="school">School Name</label>
                                                <select id="school" name="school" class="form-control" required>
                                                    <option value="">Select School</option>
                                                    <?php foreach($school as $row){?>
                                                    <option value="<?= $row->schoolID; ?>"><?= $row->schoolName; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>

                                    </div>

                                    

                                    <div class="form-group">
                                        <div class="g-recaptcha" data-sitekey="<?= trim($mis_settings->site_key); ?>"></div>
                                    </div>


                                    

                                    <br />
                                    <button type="submit" value="submit" class="btn btn-primary">My Confession</button>

                                    </form>
                                    <br />
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end row -->




                    <!-- end container-fluid -->

                </div>
                <!-- end content -->
                 <script src="https://www.google.com/recaptcha/api.js" async defer></script>
                 <script>
                    function validateCaptcha(event) {
                        var response = grecaptcha.getResponse();
                        if (response.length == 0) {
                            alert("Please verify that you are not a robot.");
                            event.preventDefault(); // prevent form submission
                            return false;
                        }
                    }
                </script>