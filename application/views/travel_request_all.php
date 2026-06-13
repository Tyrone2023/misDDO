<?php include('templates/head.php'); ?>
<?php include('templates/header.php'); ?>
<?php 
function smartTitleCase($string) {
            $words = explode(' ', strtolower($string));
            $result = [];

            foreach ($words as $word) {
                if (preg_match('/^(?=[MDCLXVI\d]+$)(M{0,4}(CM|CD|D?C{0,3})(XC|XL|L?X{0,3})(IX|IV|V?I{0,3})|\d+)$/i', $word)) {
                    $result[] = strtoupper($word); 
                } else {
                    $result[] = ucfirst($word);
                }
            }

            return implode(' ', $result);
        }
?>

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
                            <h4 class="header-title mb-4">Travel Requests</h4>


                            <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Employee No.</th>
                                        <th>Fullname</th>
                                        <th>Position</th>
                                        <th>Request Count</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $ic=1; foreach($requests as $row){?>
                                    <tr>
                                        <td><?= $ic++; ?></td>
                                        <td><?= $row->IDNumber; ?></td>
                                        <td><?= ucwords(strtolower($row->FirstName.' '. $row->LastName)); ?></td>
                                        <td><?= strtoupper($row->empPosition); ?></td>
                                        <td><span class="badge badge-info"><?= $row->request_count; ?></span></td>
                                        <td><a target="_blank" class="btn btn-success btn-sm" href="<?= base_url(); ?>Travel/travel_by_staff/<?= $row->IDNumber; ?>">View</a></td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>





                        </div>
                    </div>
                </div>
            </div>
            <!-- end row -->

        </div>
        <!-- end container-fluid -->

    </div>
    <!-- end content -->

    <?php include('templates/footer.php'); ?>