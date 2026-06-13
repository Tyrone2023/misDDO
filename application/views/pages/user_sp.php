

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
                                    <a href="#" class="btn btn-primary waves-effect waves-light" data-toggle="modal" data-target=".position">Add Position</a>
                                    <div class="page-title-right">
                                        <ol class="breadcrumb p-0 m-0">
                                            <!-- <li class="breadcrumb-item"><a href="#">Download the School Accounts Template</a></li> -->
                                            <!-- <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                                            <li class="breadcrumb-item active">Dashboard 3</li> -->
                                        </ol>
                                    </div>
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

                                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">

                                            <thead>
                                                <tr>
                                                    <th>Main Position</th>
                                                    <th>Sub Position</th>
                                                    <th>Manage</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach($users as $row){?>
                                                <tr>
                                                    <td><?= $row->main_position; ?></td>
                                                    <td><?= $row->position; ?></td>
                                                    <td>
                                                        <a href="#" class="open-AddBookDialog" data-id="<?= $row->id; ?>" data-item="<?= $row->position; ?>" data-toggle="modal" data-target=".edit_position"><i class=" fas fa-pencil-alt text-info tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Edit"></i></a> &nbsp; &nbsp; &nbsp; &nbsp;
                                                        <a onclick="return confirm('Are you sure?')" href="<?= base_url(); ?>Users/user_sp_delete/<?= $row->id; ?>" class="tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Delete"><i class="fas fa-trash-alt text-danger"></i></a>
                                                    </td>
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


                                        <div class="modal fade position" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabelcenter" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="mySmallModalLabelcenter">Add New Position</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">

                                                             <?php 
                                                                    $attributes = array('class' => 'parsley-examples');
                                                                    echo form_open('Users/users_sp', $attributes);
                                                                ?>
                                                                    
                                                                    
                                                                    <div class="form-group">
                                                                        <label>Sub-position</label>
                                                                        <input type="text" class="form-control" name="position"  value="" >
                                                                    </div>	

                                                                    <div class="form-group">
                                                                    <label for="inputPosition" class="col-form-label">Main Position</label>
                                                                        <select name="mp" class="form-control" required>
                                                                            <option></option>
                                                                            <?php foreach($pos as $row){?>
                                                                            <option <?php 
                                                                            ?> value="<?= $row->position; ?>"><?= $row->position; ?></option>
                                                                            <?php } ?>
                                                                        </select>

                                                       
                                                        
                                                    </div>
                                                    <div class="modal-footer">
                                                    <button class="btn btn-primary waves-effect waves-light mr-1" type="submit">Submit</button>
                                                    </div>
                                                    </form>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal --></div>


                                        <div class="modal fade edit_position" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabelcenter" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="mySmallModalLabelcenter">Add New Position</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">

                                                             <?php 
                                                                    $attributes = array('class' => 'parsley-examples');
                                                                    echo form_open('Users/user_sp_edit', $attributes);
                                                                ?>
                                                                    
                                                                    <input type="hidden" id="id" name="id">
                                                                    <div class="form-group">
                                                                        <label>Position</label>
                                                                        <input type="text" class="form-control" id="item" name="position"  value="" >
                                                                    </div>	

                                                                    <div class="form-group">
                                                                    <label for="inputPosition" class="col-form-label">Position</label>
                                                                        <select name="mp" class="form-control" required>
                                                                            <option></option>
                                                                            <?php foreach($pos as $row){?>
                                                                            <option <?php 
                                                                            ?> value="<?= $row->position; ?>"><?= $row->position; ?></option>
                                                                            <?php } ?>
                                                                        </select>

                                                                
                                                                </div>  

                                                       
                                                        
                                                    </div>
                                                    <div class="modal-footer">
                                                    <button class="btn btn-primary waves-effect waves-light mr-1" type="submit">Submit</button>
                                                    </div>
                                                    </form>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->
   

 