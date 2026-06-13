<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8" />
        <title>QAME</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="Responsive bootstrap 4 admin template" name="description" />
        <meta content="Coderthemes" name="author" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <!-- App favicon -->
        <link rel="shortcut icon" href="<?= base_url(); ?>assets/images/favicon.ico">

        <!-- App css -->
        <link href="<?= base_url(); ?>assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" id="bootstrap-stylesheet" />
        <link href="<?= base_url(); ?>assets/css/icons.min.css" rel="stylesheet" type="text/css" />
        <link href="<?= base_url(); ?>assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-stylesheet" />

    </head>

    <body class="authentication-page">

        <div class="account-pages my-5">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6 col-xl-5">
                        <div class="card mt-4">

                            <div class="card-header p-4 bg-primary">
                                <h4 class="text-white text-center mb-0 mt-0"><a href="<?= base_url(); ?>"><img  width="100%" src="<?= base_url(); ?>assets/images/logo-dark.png" alt=""></a></h4><br />
                            </div>
                            <div class="card-body">
                            <?php if($this->session->flashdata('success')) : ?>

                                <?= '<br /><div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>'
                                        .$this->session->flashdata('success'). 
                                    '</div>'; 
                                ?>
                                <?php endif; ?>

                                <?php if($this->session->flashdata('danger')) : ?>
                                <?= '<br /><div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>'
                                        .$this->session->flashdata('danger'). 
                                    '</div>'; 
                                ?>
                            <?php endif;  ?>
                           
                                <h6 class="text-justify mb-0 mt-0"><?= $data->act_name; ?></h6>
                                <hr />
                            <?php 
                                $attributes = array('class' => 'parsley-examples');
                                echo form_open('pages/mcea/'.$data->id, $attributes);
                            ?>

                                            

                                            <div class="form-group row">
                                                <div class="col-md-12">
                                                <input required type="text" onkeyup="this.value = this.value.toUpperCase();"  name="fname"  required placeholder="First Name" class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-md-12">
                                                <input required type="text" onkeyup="this.value = this.value.toUpperCase();"  name="mname"  required placeholder="Middle Name" class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-md-12">
                                                <input  onkeyup="this.value = this.value.toUpperCase();" required type="text" name="lname"  required placeholder="Last Name" class="form-control">
                                                </div>
                                            </div>



                                    <div class="form-group row text-center mt-4 mb-4">
                                        <div class="col-12">
                                            <button class="btn btn-md btn-block btn-primary waves-effect waves-light" type="submit">SUBMIT</button>
                                        </div>
                                    </div>

                                    
                                </form>

                            </div>
                            <!-- end card-body -->
                        </div>
                        <!-- end card -->

                        <!-- end row -->

                    </div>
                    <!-- end col -->
                </div>
                <!-- end row -->

            </div>
        </div>

        <!-- Vendor js -->
        <script src="<?= base_url(); ?>assets/js/vendor.min.js"></script>

        <!-- App js -->
        <script src="<?= base_url(); ?>assets/js/app.min.js"></script>

      

    </body>

</html>