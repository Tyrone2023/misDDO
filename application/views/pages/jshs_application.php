
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
                                    <h4 class="header-title mb-4">List of <span class="badge badge-success"><?= ($this->uri->segment(3) == 3) ? 'Junior High School' : 'Senior High School'; ?></span> Applicants</h4><br />
                                        <table class="table table-bordered mb-0">
                                        <thead>
                                            <tr>
                                                <th>NO.</th>
                                                <th>FULLNAME</th>
                                                <th>APPLICANT CODE</th>
                                                <th><?= ($this->uri->segment(3) == 3) ? 'LEARNING AND SPECIALIZED AREA' : 'GROUP AND STRAND'; ?></th>
                                                <th class="text-center">ACTION</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $nameCounts = [];
                                                foreach ($data as $r) {
                                                    $fullNameKey = strtoupper(trim($r->FirstName . ' ' . $r->MiddleName . ' ' . $r->LastName));
                                                    $nameCounts[$fullNameKey] = ($nameCounts[$fullNameKey] ?? 0) + 1;
                                                }
                                                ?>
                                                <?php $ivy = 1; foreach ($data as $row) { 
                                                    $fullName = strtoupper(trim($row->FirstName . ' ' . $row->MiddleName . ' ' . $row->LastName));
                                                    $isDuplicate = $nameCounts[$fullName] > 1;
                                                ?>
                                                <tr>
                                                    <td><?= $ivy++; ?></td>
                                                    <td>
                                                        <a style="<?= $isDuplicate ? 'color:#2433fe;' : '' ?>" href="<?= base_url(); ?>pages/<?= $row->st; ?>/<?= $row->id; ?>/<?= $row->jobID; ?>/<?= $row->pre_school; ?>/<?= $row->appID; ?>/<?= $row->code; ?>" target="_blank"><?= $fullName; ?></a>
                                                    </td>
                                                    <td><?= $row->code; ?></td>
                                                    <td><?= ($this->uri->segment(3) == 3) ? $row->jhss : $row->shss; ?></td>
                                                    <td>
                                                        <a href="#" class="btn btn-primary waves-effect waves-light open-AddBookDialog" data-item="<?= $row->st; ?>" data-id="<?= $row->id; ?>" data-toggle="modal" data-target="#myModal">Update</a>
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

                <?php if($this->uri->segment(3) == 3){?>
                <!-- sample modal content -->
                                        <div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="myModalLabel">LEARNING AND SPECIALIZED AREA</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <?= form_open('Pages/jhs_update'); ?>
                                                        <input type="hidden" id="id" name="id">
                                                        <input type="hidden" id="item" name="item">
                                                        <div class="row">
                                                                                            <!-- Learning Area -->
                                                                                            <div class="col-lg-12">
                                                                                                <label class="col-form-label">Learning Area</label>
                                                                                                <select class="form-control" required name="learn">
                                                                                                    <option></option>
                                                                                                    <?php 
                                                                                                    $la = ['Filipino','English','Mathematics','Science','Araling Panlipunan',
                                                                                                        'Edukasyon sa Pagpapakatao','Music and Arts Physical Education and Health',
                                                                                                        'Special Education','Technology and Livelihood Education','Values Educaction'];
                                                                                                    foreach ($la as $row) {
                                                                                                        $selected = ($learn == $row) ? 'selected' : '';
                                                                                                        echo "<option value=\"$row\" $selected>$row</option>";
                                                                                                    }
                                                                                                    ?>
                                                                                                </select>
                                                                                            </div>  

                                                                                            <input type="hidden" name="ren" value="<?= $this->uri->segment(6); ?>">

                                                                                            <!-- Specialized Area -->
                                                                                            <div class="col-lg-12">
                                                                                                <label class="col-form-label">Specialized Area</label>
                                                                                                <select class="form-control" name="special">
                                                                                                    <option></option>
                                                                                                    <?php 
                                                                                                    $specialized_areas = [
                                                                                                        'Mathematics' => 'Algebra, Trigonometry, Geometry, Statistics',
                                                                                                        'Science' => 'General Science, Biology, Chemistry, Physics',
                                                                                                        'TLE' => 'Agri - Fishery Arts, Home Economics, Information and Communications Technology, Industrial Arts'
                                                                                                    ];
                                                                                                    foreach ($specialized_areas as $group_label => $values) {
                                                                                                        $items = explode(', ', $values);
                                                                                                        echo "<optgroup label=\"$group_label\">";
                                                                                                        foreach ($items as $val) {
                                                                                                            $selected = ($special == $val) ? 'selected' : '';
                                                                                                            echo "<option value=\"$val\" $selected>$val</option>";
                                                                                                        }
                                                                                                        echo "</optgroup>";
                                                                                                    }
                                                                                                    ?>
                                                                                                </select>
                                                                                            </div>
                                                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Close</button>
                                                        <button type="submit" class="btn btn-primary waves-effect waves-light">Save changes</button>
                                                    </div>
                                                </div>
                                                </form>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->
                                <?php }else{ ?>

                                <!-- sample modal content -->
                                        <div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="myModalLabel">LEARNING AND SPECIALIZED AREA</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <?= form_open('Pages/shs_update'); ?>
                                                        <input type="hidden" id="id" name="id">
                                                        <input type="hidden" id="item" name="item">
                                                        <div class="row">
                                                                                            <!-- Group -->
                                                                                            <div class="col-lg-12">
                                                                                                <label class="col-form-label">Group</label>
                                                                                                <select class="form-control" required name="group">
                                                                                                    <option></option>
                                                                                                    <?php 
                                                                                                    $groups = ['HUMSS','ABM','STEM','TVL','Sports','Arts and Design'];
                                                                                                    foreach ($groups as $row) {
                                                                                                        echo "<option value=\"$row\">$row</option>";
                                                                                                    }
                                                                                                    ?>
                                                                                                </select>
                                                                                            </div>  

                                                                                            <!-- Strand -->
                                                                                            <div class="col-lg-12">
                                                                                                <label class="col-form-label">Strand</label>
                                                                                                <select class="form-control" name="strand">
                                                                                                    <option></option>
                                                                                                    <?php 
                                                                                                    $strands = [
                                                                                                    'HUMMS'=>'I-A: Oral Communication&#44 Reading and Writing&#44 English for Academic and Professional Purposes&#44 Practical Research, 
                                                                                                             I-B: Komunikasyon at Pananaliksik sa Wika at Kulturang Pilipino&#44Pagbasa at Pagsusuri ng Iba’t ibang Teksto sa Pananaliksik&#44Pagsulat sa Filipino sa Piling Larangan, 
                                                                                                             I-C: 21st Century Literature from the Philippines and the World; Contemporary Philippine Arts from the Region; Understanding Culture&#44 Society and Politics; Introduction<br /> to the Philosophy of the Human Person  and related specialized HUMSS subjects, 
                                                                                                             I-D: Media and Information Literacy; Empowerment Technologies (for the Strands)',
                                                                                                    'STEM'=>'III-A: General Mathematics&#44 Statistics and Probability and related specialized STEM subjects, 
                                                                                                            III-B: Earth Science&#44 Earth and Life Science&#44 Physical Science and related specialized STEM subjects',
                                                                                                    'TVL'=>'IV-A: Specialized TVL/Agri-Fisheries, 
                                                                                                            IV-B: Specialized TVL/Industrial Arts, 
                                                                                                            IV-C: Specialized TVL/ICT, 
                                                                                                            IV-D: Specialized TVL/Home Economics',
                                                                                                    'Others' => 'ABM and Entrepreneurship&#44 Research and Work Immersion, 
                                                                                                                Physical Education and Health&#44 Personal Development and related specialized Sports Subjects, 
                                                                                                                Arts and Design',
                                                                                                    ];
                                                                                                    foreach ($strands as $label => $list) {
                                                                                                        $items = explode(', ', $list);
                                                                                                        echo "<optgroup label=\"$label\">";
                                                                                                        foreach ($items as $val) {
                                                                                                            echo "<option value=\"$val\">$val</option>";
                                                                                                        }
                                                                                                        echo "</optgroup>";
                                                                                                    }
                                                                                                    ?>
                                                                                                </select>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Close</button>
                                                        <button type="submit" class="btn btn-primary waves-effect waves-light">Save changes</button>
                                                    </div>
                                                </div>
                                                </form>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->


                                        
                                <?php } ?>

               

             
                                      

