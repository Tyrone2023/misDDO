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
                <div class="col-md-6">
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
                    <div class="card">
                        <div class="card-body">

                            <?= validation_errors(); ?>

                            <?php
                            $attributes = array('class' => 'parsley-examples');
                            echo form_open('Brigada/partner', $attributes);
                            ?>

                            <div class="form-row">
                                <!-- <div class="form-group col-md-6">
                                    <label for="inputUsername" class="col-form-label">PARTNER</label>
                                    <input type="text" required value="" name="Username" class="form-control">
                                </div> -->
                                <input type="hidden" value="<?= $this->session->username; ?>" name="school_id">
                                <div class="form-group col-md-12">
                                    <label for="inputUsername" class="col-form-label">PARTNER</label>
                                    <select class="form-control" required name="dtype">
                                        <option></option>
                                        <option value="1">GOVERNMENT PARTNER</option>
                                        <option value="2">PRIVATE PARTNER</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="inputUsername" class="col-form-label">INTERVENTION</label>
                                    <input type="text" required value="" name="intervention" class="form-control">
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="inputUsername" class="col-form-label">AMOUNT</label>
                                    <input type="text" required value="" name="amount" class="form-control">
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="inputUsername" class="col-form-label">REMARKS</label>
                                    <input type="text" required value="" name="remarks" class="form-control">
                                </div>

                            </div>



                            <button type="submit" value="submit" class="btn btn-primary">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- end row -->

            </div>
            <!-- end container-fluid -->

        </div>
        <!-- end content -->