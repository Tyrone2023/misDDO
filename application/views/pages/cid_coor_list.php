

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
                                    <a href="#" class="btn btn-primary waves-effect waves-light openModalBtn"data-toggle="modal" data-target="#myModal">Assign a Coordinator</a>
                              
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
                                                    <th>Categories</th>
                                                    <th>Coordinatorship</th>
                                                    <th>Personnel</th>
                                                    <th>Proficiency Level</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <?php foreach($data as $row){
                                                    $person = $this->Common->one_cond_row_select('hris_staff','IDNumber,FirstName,MiddleName,LastName','IDNumber',$row->staff_id);
                                                    $coors = $this->Common->one_cond_row_select('cid_coor_list','name,id','id',$row->coor_id);
                                                    $cats = $this->Common->one_cond_row_select('cid_coor_type','name,id','id',$row->type_id);
                                                ?>
                                                <tr>
                                                    <td><?= $cats->name; ?></td>
                                                    <td><span class="badge badge-info"><?= $coors->name; ?></span></td>
                                                    <td><?= $person->FirstName.' '.$person->LastName; ?></td>
                                                    <td class="text-center"><a href="<?= base_url(); ?>Coor/coor_entry_view_school/<?= $row->coor_id; ?>" class="btn btn-info btn-sm waves-effect waves-light">View</a></td>
                                                    <td class="text-center">
                                                        <a onclick="return confirm('Are you sure?')" href="<?= base_url(); ?>Coor/report_delete/<?= $row->id; ?>" class="btn btn-danger btn-sm waves-effect waves-light">Delete</a>
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
                <!-- end content -->

                <!-- sample modal content -->
                                        <div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-info">
                                                        <h5 class="modal-title" id="modalTitle">Coordinator Designation Panel</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">

                                                    <div class="row">
                                            <div class="col-12">
                                                <div class="">
                                                        <?= validation_errors(); ?>

                                                        <?php
                                                        $attributes = array('class' => 'parsley-examples form-horizontal');
                                                        echo form_open('Coor/coor_personnel_add', $attributes);
                                                        ?>
                                                        <div class="form-group row">
                                                            <label class="col-lg-4 col-form-label">Categories</label>
                                                            <div class="col-lg-8">
                                                               
                                                                <select name="type" class="form-control" id="categorySelect" required>
                                                                    <option value="" disabled selected>Select Category</option>
                                                                    <?php foreach($cat as $row){ ?>
                                                                        <option value="<?= $row->id; ?>"><?= $row->name; ?></option>
                                                                    <?php } ?>
                                                                </select>

                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label class="col-lg-4 col-form-label">Coordinatorships</label>
                                                            <div class="col-lg-8">
                                                               <select name="coor" class="form-control" id="coorSelect" required>
                                                                    <option value="" disabled selected>Select Coordinatorship</option>
                                                                    <!-- Will be loaded via AJAX -->
                                                                </select>
                                                            </div>
                                                        </div>

                                                       

                                                        <div class="form-group row">
                                                            <label class="col-lg-4 col-form-label">Personnel</label>
                                                            <div class="col-lg-8">
                                                               
                                                                <select name="staff" class="form-control" required>
                                                                    <option value="" disabled selected>Select Personnel</option>
                                                                    <?php foreach($staff as $row){?>
                                                                        <option value="<?= $row->IDNumber; ?>"><?= $row->LastName.', '.$row->FirstName.' '.$row->MiddleName; ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <input type="hidden" value="<?= $this->session->username; ?>" name="school_id">

                                                        
                                                    
                                                </div>
                                            </div>

                                        </div>
                                        <!-- end row -->
                                                        
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-info waves-effect waves-light">Save</button>
                                                    </div>
                                                    </form>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->


                                        <script src="<?= base_url(); ?>assets/js/jquery-3.6.0.min.js"></script>
                                        <script>
                                            $(document).ready(function() {
                                                $('#categorySelect').on('change', function() {
                                                    var catID = $(this).val();

                                                    if (catID) {
                                                        $.ajax({
                                                            url: '<?= base_url("Coor/get_coordinatorships_by_category") ?>',
                                                            type: 'POST',
                                                            data: { category_id: catID },
                                                            dataType: 'json',
                                                            success: function(response) {
                                                                $('#coorSelect').html('<option value="" disabled selected>Select Coordinatorship</option>');
                                                                $.each(response, function(i, item) {
                                                                    $('#coorSelect').append('<option value="' + item.id + '">' + item.name + '</option>');
                                                                });
                                                            }
                                                        });
                                                    } else {
                                                        $('#coorSelect').html('<option value="" disabled selected>Select Coordinatorship</option>');
                                                    }
                                                });
                                            });
                                        </script>

   

 