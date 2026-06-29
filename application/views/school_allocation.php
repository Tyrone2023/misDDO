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
                                    <a data-toggle="modal"  class="open-AddBookDialog btn btn-primary waves-effect waves-light" href="#add">Add New</a>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->


                        <!-- sample modal content -->
                        <div id="add" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">

                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="myModalLabel">Change Allocation</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    
                                                    <div class="modal-body">
                                                    <?= form_open('Page/fund_add'); ?>

                                                        
                                                        <div class="form-group col-md-12">
                                                            <label>School Name</label>
                                                            <select class="form-control" data-toggle="select2" name="schoolID" required>
                                                                <option></option>
                                                                <?php 
                                                                  foreach($school as $row){
                                                                ?>
                                                                <option value="<?= $row->schoolID; ?>"><?= $row->schoolID; ?> - <?= $row->schoolName; ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>

                                                        <div class="form-group col-md-12">
                                                            <label>Fund Allocation Amount</label>
                                                            <input type="text" required value="<?= set_value('alloc_amount'); ?>" id="alloc_amount" name="alloc_amount" class="form-control" inputmode="decimal" autocomplete="off">
                                                        </div>

                                                        <div class="form-group col-md-12">
                                                            <input type="hidden" required value="<?= $last->alloc_batch+1; ?>" name="bcode" class="form-control"> 
                                                        </div>

                                                        <div class="form-group col-md-12">
                                                            <label>Fascal Year</label>
                                                            <select class="form-control" name="fy" required>
                                                                <option></option>
                                                            <?php 
                                                            $firstYear = (int)date('Y');
                                                            $lastYear = $firstYear + 5;
                                                            for($i=$firstYear;$i<=$lastYear;$i++){ echo '<option value='.$i.'>'.$i.'</option>';}
                                                            ?>
                                                            </select>
                                                        </div>

                                                        
                                                        <div class="form-group col-md-12">
                                                            <label>Group</label>
                                                            <select class="form-control" name="group" required>
                                                                <option></option>
                                                                <?php 
                                                                $g = array('Senior HS','Junior HS','Elementary');
                                                                  foreach($g as $row){
                                                                ?>
                                                                <option value="<?= $row; ?>"><?= $row; ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>

                                                        
                                                        <div class="form-group col-md-12">
                                                            <label>Allocation Type</label>
                                                            <select class="form-control" data-toggle="select2" name="type" required>
                                                                <option></option>
                                                                <?php 
                                                                  foreach($bs as $row){
                                                                ?>
                                                                <option value="<?= $row->description; ?>"><?= $row->description; ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    
                                                        <div class="modal-footer">
                                                            <button type="submit" name="submit" class="btn btn-primary waves-effect waves-light">Submit</button>
                                                        </div>
                                                </form>
                                                </div>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->

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

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body table-responsive">
                                        <h4 class="header-title mb-4">SCHOOL ALLOCATION</h4><br />

                                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                            <!-- <table class="table mb-0"> -->
                                            <thead>
                                                <tr>
                                                    <th>School ID</th>
                                                    <th>Total Allocation</th>
                                                    <th>Year</th>
                                                    <th>Jan</th>
                                                    <th>Feb</th>
                                                    <th>Mar</th>
                                                    <th>Apr</th>
                                                    <th>May</th>
                                                    <th>Jun</th>
                                                    <th>Jul</th>
                                                    <th>Aug</th>
                                                    <th>Sep</th>
                                                    <th>Oct</th>
                                                    <th>Nov</th>
                                                    <th>Dec</th>
                                                    <th>Batch Code</th>
                                                    <th>Type</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                foreach ($data as $row) {
                                                    echo "<tr>";
                                                    echo "<td>" . $row->schoolID . "</td>";
                                                    echo "<td>" . $row->alloc_amount . "</td>";
                                                    echo "<td>" . $row->alloc_year . "</td>";
                                                    echo "<td>" . $row->mo_jan . "</td>";
                                                    echo "<td>" . $row->mo_feb . "</td>";
                                                    echo "<td>" . $row->mo_mar . "</td>";
                                                    echo "<td>" . $row->mo_apr . "</td>";
                                                    echo "<td>" . $row->mo_may . "</td>";
                                                    echo "<td>" . $row->mo_jun . "</td>";
                                                    echo "<td>" . $row->mo_jul . "</td>";
                                                    echo "<td>" . $row->mo_aug . "</td>";
                                                    echo "<td>" . $row->mo_sep . "</td>";
                                                    echo "<td>" . $row->mo_oct . "</td>";
                                                    echo "<td>" . $row->mo_nov . "</td>";
                                                    echo "<td>" . $row->mo_dec . "</td>";
                                                    echo "<td>" . $row->alloc_batch . "</td>";
                                                    echo "<td>" . $row->alloc_type . "</td>";
                                                ?>
                                                    <td>
                                                        <a data-toggle="modal" data-id="<?= $row->id; ?>" data-item="<?= $row->alloc_amount; ?>" class="open-AddBookDialog text text-success w-lg" href="#alloc"><i class="mdi mdi-file-document-box-check-outline"></i>Edit</a> &nbsp; &nbsp;
                                                        <a onclick="return confirm('Are you sure?')" href="<?= base_url(); ?>Page/allocation_delete/<?= $row->id; ?>" class="open-AddBookDialog text text-danger w-lg"><i class="ion ion-ios-trash"></i>Delete</a>
                                                        
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
                    <!-- end container-fluid -->

                </div>
                <!-- end content -->

                <!-- sample modal content -->
                <div id="alloc" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">

                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="myModalLabel">Change Allocation</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    
                                                    <div class="modal-body">
                                                    <?= form_open('Page/fund_update'); ?>
                                                        <div class="form-group col-md-12">
                                                            <label>Fund Allocation</label>
                                                            <input type="text" required value="<?= set_value('pass'); ?>" id="item" name="alloc_amount" class="form-control"> 
                                                        </div>
                                                        <input type="hidden" name="id" id="id" value="">
                                                    
                                                        <div class="modal-footer">
                                                            <button type="submit" name="submit" class="btn btn-primary waves-effect waves-light">Update</button>
                                                        </div>
                                                </form>
                                            </div>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->



                                        





            <?php include('templates/footer.php'); ?>

            <script>
                                        $(document).ready(function() {
                                            $("#school option").hide();

                                            $("#dist").change(function() {
                                                var val = $(this).val();
                                                $("#school option").hide();
                                                $("#school").val("");
                                                $("#school [data-dist='" + val + "']").show(); //show options where attribute value matches.
                                                $("#school").change();
                                            });

                                            // Fund Allocation Amount: live thousand-separator formatting
                                            $("#alloc_amount").on("input", function() {
                                                var caretEnd = this.selectionEnd;
                                                var oldLen = this.value.length;
                                                var raw = this.value.replace(/[^\d.]/g, "");
                                                var parts = raw.split(".");
                                                var intPart = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                                                var decPart = parts.length > 1 ? "." + parts[1].slice(0, 2) : "";
                                                this.value = intPart + decPart;
                                                var newLen = this.value.length;
                                                this.selectionEnd = Math.max(0, caretEnd + (newLen - oldLen));
                                            });

                                            // strip commas before submit so the server receives a plain number
                                            $("#alloc_amount").closest("form").on("submit", function() {
                                                var f = $("#alloc_amount");
                                                f.val(f.val().replace(/,/g, ""));
                                            });

                                            });
            </script>

            