<!DOCTYPE html>
<html lang="en">

    <head>
    <meta charset="utf-8" />
        <title>Management Information System (MIS)</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="Responsive bootstrap 4 admin template" name="description" />
        <meta content="Coderthemes" name="author" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <!-- App favicon -->
        <link rel="shortcut icon" href="<?= base_url(); ?>assets/images/hris.ico">

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
                            <div class="card-header text-center p-4 bg-primary">
                            <h4 class="text-white text-center mb-0 mt-0"><img  width="100%" src="<?= base_url(); ?>assets/images/logo.png" alt=""></h4>
                            </div>
                            <div class="card-body">

                            <div class="table-responsive">
                                            <table class="table mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>Job Title</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach($data as $row){?>
                                                    <tr>
                                                        <th><?= $row->jobTitle?></th>
                                                        <td>
                                                            <?php if($row->job_type == 1){ ?>
                                                                <a target="_blank" href="<?= base_url(); ?>Pages/rqa_clusterv3/<?= $row->jobID; ?>" class="btn btn-primary btn-sm">RQA List</a>
                                                            <?php }elseif($row->job_type == 2){ ?>
                                                                <a target="_blank" href="<?= base_url(); ?>Pages/rqa_cluster_jhsv3/<?= $row->jobID; ?>" class="btn btn-primary btn-sm">RQA List</a>
                                                            <?php }else{ ?>
                                                                <a target="_blank" href="<?= base_url(); ?>Pages/rqa_cluster_shsv3/<?= $row->jobID; ?>" class="btn btn-primary btn-sm">RQA List</a>
                                                            <?php } ?>
                                                        </td>
                                                    </tr>
                                                    <?php } ?>
                                                    
                                                </tbody>
                                            </table>
                                        </div>
                                

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