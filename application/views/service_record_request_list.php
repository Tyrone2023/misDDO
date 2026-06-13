            <?php include('templates/head.php'); ?> 
            <link href="<?= base_url(); ?>assets/libs/custombox/custombox.min.css" rel="stylesheet" type="text/css">
            <?php include('templates/header.php'); ?>          

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
                                <div class="card">
                                    <div class="card-body table-responsive">
                                    <h4 class="header-title mb-4">Service Record Request List</h4>
                                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th>Employee No.</th>
                                                    <th>Employee Name</th>
                                                    <th>Purpose</th>
                                                    <th>Date Reqested</th>
                                                    <th>Message</th>
                                                    <th style='text-align:center'>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
    <?php
    $i = 1;
    foreach ($data as $row) {
        echo "<tr>";
        echo "<td>" . $row->IDNumber . "</td>";
        echo "<td>" . $row->FirstName . ' ' . $row->LastName . "</td>";
        echo "<td>" . $row->purpose . "</td>";
        echo "<td>" . $row->dateReq . "</td>";
        echo "<td>" . $row->message . "</td>";
        ?>
        <td style='text-align:center'> 
         <a href="<?= base_url('Page/ServiceRecorddisplay/' . $row->IDNumber . '/' . $row->id); ?>"  
       class="text-success open-AddBookDialog">
        <i class="mdi mdi-file-document-box-check-outline"></i> View
    </a>&nbsp;&nbsp;&nbsp;&nbsp;

        </td>
        <?php echo "</tr>"; 
    } ?>
</tbody>

                                            </table>
                                        
                                    

                                    </div>
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

                <?php include('templates/footer.php'); ?>  

 