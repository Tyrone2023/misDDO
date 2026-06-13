

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

                                         <table class="table table-bordered mb-0">
                                            <tbody>
                                                <tr>
                                                    <td>#</td>
                                                    <td></td>
                                                    <td>Number of Male</td>
                                                    <td>Number of Female</td>
                                                </tr>
                                                <?php $ic=1; foreach($data as $row){
                                                    //$entry = $this->Common->two_cond_count_row('private_other_data','pq_id',$row->id,'school_id',$this->session->username);
                                                    $quarter = $this->input->post('quarter');
                                                    $year = $this->input->post('year');
                                                    $gl = $this->input->post('grade_level');
                                                    $pq_id = $row->id;
                                                        
                                                    $total = $this->Ps_model->get_others_total($quarter, $year,$gl,$pq_id);
                                                    ?>
                                                <tr>
                                                    <td><?= $ic++; ?></td>
                                                    <td><?= $row->question; ?></td>
                                                    <td class="text-center"><?= $total->nmale_total; ?></td>
                                                    <td class="text-center"><?= $total->nfemale_total; ?></td>
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
                                                    <div class="modal-header bg-primary">
                                                        <h5 class="modal-title" id="modalTitle">Modal Heading</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">

                                                    <div class="row">
                                            <div class="col-12">
                                                <div class="">
                                                        <?= validation_errors(); ?>

                                                        <?php
                                                        $attributes = array('class' => 'parsley-examples form-horizontal');
                                                        echo form_open('Ps/private_other_add', $attributes);
                                                        ?>
                                                        <div class="form-group row">
                                                            <label class="col-lg-6 col-form-label">Year</label>
                                                            <div class="col-lg-6">
                                                                <?php
                                                                $currentYear = date("Y")+1; 
                                                                $earliestYear = $currentYear - 6; 
                                                                ?>
                                                                <select name="year" class="form-control" required>
                                                                <option value="" disabled selected>Select Year</option>
                                                                <?php
                                                                    for ($year = $currentYear; $year >= $earliestYear; $year--) {
                                                                        echo "<option value=\"$year\">$year</option>";
                                                                    }
                                                                ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label class="col-lg-6 col-form-label">Quarter</label>
                                                            <div class="col-lg-6">
                                                                <select class="form-control" name="quarter" required>
                                                                    <option value="" disabled selected>Select Quarter</option>
                                                                    <?php
                                                                        $quarters = ['First', 'Second', 'Third', 'Fourth'];
                                                                        foreach ($quarters as $index => $label) {
                                                                            $value = $index + 1;
                                                                            echo "<option value=\"$value\">$label Quarter</option>";
                                                                        }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label class="col-lg-6 col-form-label">Grade Level</label>
                                                            <div class="col-lg-6">
                                                                <select class="form-control" name="grade_level" required>
                                                                    <option value="" disabled selected>Select Grade Level</option>
                                                                    <option value="0">Kinder</option>
                                                                    <?php for($i = 1; $i <= 12; $i++): ?>
                                                                        <option value="<?= $i ?>">Grade <?= $i ?></option>
                                                                    <?php endfor; ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label class="col-lg-6 col-form-label">Number of Male</label>
                                                            <div class="col-lg-6">
                                                                <input type="text" name="nmale" class="form-control number-only" value="" placeholder='Number of Male' required>
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label class="col-lg-6 col-form-label">Number of Female</label>
                                                            <div class="col-lg-6">
                                                                <input type="text" name="nfemale" class="form-control number-only" value="" placeholder='Number of Female' required>
                                                            </div>
                                                        </div>
                                                        <input type="hidden" id="questionID" name="id" value="">
                                                        <input type="hidden" value="<?= $this->session->username; ?>" name="school_id">

                                                        
                                                    
                                                </div>
                                            </div>

                                        </div>
                                        <!-- end row -->
                                                        
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-primary waves-effect waves-light">Save</button>
                                                    </div>
                                                    </form>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->


                                    <script>
                                        document.addEventListener('DOMContentLoaded', function () {
                                            const modal = document.getElementById('myModal');
                                            const modalTitle = document.getElementById('modalTitle');
                                            const inputID = document.getElementById('questionID');

                                            document.querySelectorAll('.openModalBtn').forEach(button => {
                                                button.addEventListener('click', function () {
                                                    const question = this.getAttribute('data-question');
                                                    const id = this.getAttribute('data-id');

                                                    modalTitle.textContent = question;
                                                    inputID.value = id;
                                                });
                                            });
                                        });
                                    </script>

                                    <!-- Long Content Scroll Modal -->
                                        <div class="modal fade" id="yourmodal" tabindex="-1" role="dialog"
                                        aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalScrollableTitle">Add New Indicator</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">

                                                    <div class="row">
                                            <div class="col-12">
                                                <div class="">
                                                        <?= validation_errors(); ?>

                                                        <?php
                                                        $attributes = array('class' => 'parsley-examples form-horizontal');
                                                        echo form_open('Ps/private_indicator_add', $attributes);
                                                        ?>
                                                        <div class="form-group row">
                                                            <label class="col-lg-12 col-form-label">Indicator Description</label>
                                                            <div class="col-lg-12">
                                                                <textarea class="form-control" name="question" rows="5" id="example-textarea"></textarea>
                                                            </div>
                                                        </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                        
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-primary">Submit</button>
                                                    </div>
                                                </form>
                                                </div>
                                            </div>
                                        </div>


   

 