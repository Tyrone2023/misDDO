            <?php include('templates/head.php'); ?>  
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
                                    <!-- <a href="<?= $link; ?>" class="btn btn-success waves-effect width-md waves-light"><?= $pn; ?></a> -->
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->

                       <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body table-responsive">
                                    <h4 class="header-title mb-4">LIST PER DEPARTMENT</h4>
                                 
                                    <form class="parsley-examples" method="post" accept-charset="utf-8">
                                        <div class="form-row">
                                        <div class="form-group col-md-4">
                                                <label for="semester">Department</label>
                                                <select class="form-control" required name="Department" data-toggle="select2">
                                                                                                <option>Select</option>
                                                                                                <?php foreach($data1 as $row) { ?>
                                                                                                    <option value="<?= $row->Department; ?>"><?= $row->Department; ?></option>
                                                                                                <?php } ?>
                                                                                            </select> 
                                               
                                            </div> 
                                       
                                        </div>
                                        <input type="submit" name="submit" value="View" class="btn btn-primary waves-effect waves-light mr-1">

                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end row -->

                        <!-- Portlet card -->
                        <div class="card">
                                    <div class="card-header bg-info py-3 text-white">
                                        <div class="card-widgets">
                                            <a href="javascript:;" data-toggle="reload"><i class="mdi mdi-refresh"></i></a>
                                            <a data-toggle="collapse" href="#cardCollpase3" role="button" aria-expanded="false" aria-controls="cardCollpase2"><i class="mdi mdi-minus"></i></a>
                                            <a href="#" data-toggle="remove"><i class="mdi mdi-close"></i></a>
                                        </div>
                                        <h5 class="card-title mb-0 text-white">
                                                <?php if($this->input->post('submit')){
                                                          echo  $_POST['Department']; }
                                                            else {}; ?>
                                            </h5>
                                    </div>
                                    <div id="cardCollpase3" class="collapse show">
                                        <div class="card-body">
                                        <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">

                                                <thead>
                                                    <tr>
                                                        <th>Last Name</th>
                                                        <th>First Name</th>
                                                        <th>Middle Name</th>
                                                        <th>Employee No.</th>
                                                        <th>Position</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                $i=1;
                                                foreach($data as $row)
                                                {
                                                echo "<tr>";
                                                echo "<td>".$row->LastName."</td>";
                                                echo "<td>".$row->FirstName."</td>";
                                                echo "<td>".$row->MiddleName."</td>";
                                                echo "<td>".$row->IDNumber."</td>";
                                                echo "<td>".$row->empPosition."</td>";

                                                echo "</tr>";

                                                                    }
                                                ?>
                                                </tbody>

                                                </table>
                                        </div>
                                    </div>
                                </div>
                                <!-- end card-->


                    </div>
                    <!-- end container-fluid -->

                </div>
                <!-- end content -->

                <?php include('templates/footer.php'); ?>       

             
 