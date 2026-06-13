

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
                                    <!-- <h4 class="page-title" id="myLargeModalLabel">                            
                                        <button type="button" class="btn btn-primary waves-effect waves-light" data-toggle="modal" data-target=".bs-example-modal-lg">+ ADD NEW</button>
                                        <a href="acc" class="btn btn-info waves-effect waves-light" target="_blank">REPORTS</a>
                                    </h4> -->
                                    <!-- <div class="page-title-right">
                                        <ol class="breadcrumb p-0 m-0">
                                            <li class="breadcrumb-item">SGOD Management System v1.0</li>
                                        </ol>
                                    </div> -->
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->


                        
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                        <h4 class="header-title mb-4"><?= $title; ?></h4>
                                        
                                        
                                        <?php $att = array('class' => 'parsley-examples'); ?>
                                        <?= form_open('Page/aip_edit/'.$data->id, $att); ?>
                                            <input type="hidden"  class="form-control" name="school_id" value="<?= $this->session->username; ?>">
                                            <input type="hidden"  class="form-control" name="aip_id" value="<?= $data->id ?>">

                                            <div class="row">
                                                <div class="col-lg-1">
                                                        <div class="form-group">
                                                            <label >FY <span class="text-danger">*</span></label>
                                                            <input type="text" required class="form-control" value="<?= $data->fy; ?>" name="fy">
                                                            <input type="hidden" required class="form-control" value="<?= $_SESSION['aip']; ?>" name="b_code">
                                                        </div>
                                                </div>
                                                
                                                <div class="col-lg-2">
                                                        <div class="form-group">
                                                            <label >GROUP <span class="text-danger">*</span></label>
                                                             <select class="form-control" name="group" required>
                                                                <option></option>
                                                                <?php   
                                                                  //$group = array(1 => 'Elementary School',2 => 'Junior High School', 3 => 'Senior High School');
                                                                  
                                                                    foreach($group as $row){ ?>
                                                                    <option <?php if($data->group == $row->id){echo "selected";} ?> value="<?= $row->id; ?>"><?= $row->alloc_group; ?> </option>
                                                                   <?php } ?>
                                                                
                                                            </select>
                                                        </div>
                                                </div>

                                                <div class="col-lg-3">
                                                        <div class="form-group">
                                                            <label >Pillar <span class="text-danger">*</span></label>
                                                            <select class="form-control" name="pillar" required>
                                                                <option></option>
                                                                <?php   foreach($pillar as $row){
                                                                    echo '<option';
                                                                    if($data->pillar == $row->pillar){echo " selected ";}
                                                                    echo ' value="'.$row->pillar.'">'.$row->pillar.'</option>';
                                                                   }
                                                                ?>
                                                                
                                                            </select>
                                                        </div>
                                                </div>

                                                <div class="col-lg-6">
                                                        <div class="form-group">
                                                            <label>Intermediate Outcome <span class="text-danger">*</span></label>
                                                            <select class="form-control" name="io" required>
                                                                <option></option>
                                                                <?php   foreach($pillar as $row){
                                                                    echo '<optgroup label="'.$row->pillar.'">';
                                                                            $io = $this->SGODModel->one_cond_orderby('sgod_setting_io','pillar_id',$row->id,'id','ASC');
                                                                        foreach($io as $row){
                                                                            echo '<option class="ssigment"';
                                                                            if($data->io == $row->id){echo " selected ";}
                                                                            echo ' value="'.$row->id.'">'.$row->description.'</option>';   
                                                                        }
                                                                    echo '</optgroup>';
                                                                   }
                                                                ?>
                                                            </select>
                                                        </div>
                                                </div>



                                                </div>

                                           
                                            <div class="row">
                                                <!-- <div class="col-lg-4">
                                                        <div class="form-group">
                                                            <label >MATATAG <span class="text-danger">*</span></label>
                                                             <select class="form-control" name="matatag" required>
                                                                <option></option>
                                                                <?php   foreach($matatag as $row){
                                                                    echo '<option';
                                                                    if($data->matatag == $row->id){echo " selected ";}
                                                                    echo ' value="'.$row->id.'">'.$row->matatag.'</option>';
                                                                   }
                                                                ?>
                                                                
                                                            </select>
                                                        </div>
                                                </div>  -->
                                                
                                                <div class="col-lg-4">
                                                        <div class="form-group">
                                                            <label>Current Administration Agenda Outcome Indicators <span class="text-danger">*</span></label>
                                                             <select class="form-control" name="matatag" required>
                                                                <option value=""></option>

                                                                <?php
                                                                $currentGroup = null;
                                                                foreach ($agenda as $row) {

                                                                    if ($currentGroup !== $row->group) {
                                                                        if ($currentGroup !== null) echo "</optgroup>";

                                                                        $currentGroup = $row->group;
                                                                        echo '<optgroup label="'.html_escape($currentGroup).'">';
                                                                    }

                                                                    echo '<option';
                                                                    if($row->id == $data->matatag){echo ' selected ';}
                                                                    echo ' value="'.(int)$row->id.'">'.html_escape($row->indicator).'</option>';
                                                                }
                                                                
                                                                if ($currentGroup !== null) echo "</optgroup>";
                                                                ?>
                                                            </select>
                                                        </div>
                                                </div>

                                            <div class="col-lg-4">
                                                        <div class="form-group">
                                                            <label >Domain <span class="text-danger">*</span></label>
                                                            <select class="form-control" name="domain" required>
                                                                <option></option>
                                                                <?php   foreach($domain as $row){
                                                                    echo '<option';
                                                                    if($data->domain == $row->domain){echo " selected ";}
                                                                    echo ' value="'.$row->domain.'">'.$row->domain.'</option>';
                                                                   }
                                                                ?>
                                                                
                                                            </select>
                                                        </div>
                                                    </div>
                                                <div class="col-lg-4">
                                                        <div class="form-group">
                                                            <label >STRAND<span class="text-danger">*</span></label>
                                                             <select class="form-control" name="strand" required>
                                                                <option></option>
                                                                <?php   foreach($domain as $row){
                                                                    echo '<optgroup label="'.$row->domain.'">';
                                                                    $strand = $this->SGODModel->one_cond_orderby('sgod_settings_strand','domain_id',$row->id,'strand','ASC');
                                                                        foreach($strand as $row){
                                                                            echo '<option';
                                                                            if($data->strand == $row->strand){echo " selected ";}
                                                                            echo ' class="ssigment" value="'.$row->strand.'">'.$row->strand.'</option>';   
                                                                        }
                                                                    echo '</optgroup>';
                                                                   }
                                                                ?>
                                                                
                                                            </select>
                                                        </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-8">
                                                        <div class="form-group">
                                                            <label >PIAs <span class="text-danger">*</span></label>
                                                             <select class="form-control" name="pia" required>
                                                                <option></option>
                                                                <?php   foreach($pias as $row){
                                                                    echo '<option';
                                                                    if($data->pia == $row->pias){echo " selected ";}
                                                                    echo ' value="'.$row->pias.'">'.$row->pias.'</option>';
                                                                   }
                                                                ?>
                                                                
                                                            </select>
                                                        </div>
                                                </div>
                                                <div class="col-lg-4">
                                                        <div class="form-group">
                                                            <label>Category <span class="text-danger">*</span></label>
                                                             <select class="form-control" name="category" required>
                                                                <option></option>
                                                                <?php  
                                                                 $cat = array('MANDATORY BILLS' => 'MANDATORY BILLS','MINOR REPAIR' => 'MINOR REPAIR/MAINTENACE' ,'TEACHING-LEARNING INSTRUCTION' => 'TEACHING-LEARNING INSTRUCTION','TRAININGS/SEMINAR/TRAVEL' => 'TRAININGS/SEMINAR/TRAVEL');
                                                                foreach($cat as $key => $row){
                                                                    echo "<option ";
                                                                    if($data->category == $key){echo " selected ";}
                                                                    echo " value='".$key."'>".$row."</option>";
                                                                   }
                                                                ?>
                                                                
                                                            </select>
                                                        </div>
                                                </div>
                                            </div>



                                            <div class="row">
                                                <div class="col-lg-4">
                                                        <div class="form-group">
                                                                <label >SCHOOL IMPROVEMENT  PROJECT TITLE</label>
                                                                <input type="text" class="form-control" value="<?= $data->sip_project; ?>" name="sip_project">
                                                         </div>
                                                </div>

                                                <div class="col-lg-4">
                                                        <div class="form-group">
                                                            <label >PROJECT OBJECTIVE</label>
                                                             <input type="text" class="form-control" value="<?= $data->sip_pObjective; ?>" name="sip_pObjective">
                                                        </div>
                                                </div>


                                                <div class="col-lg-4">
                                                        <div class="form-group">
                                                            <label >OUTPUT FOR THE YEAR</label>
                                                            <input type="text"  class="form-control" value="<?= $data->sip_output; ?>" name="sip_output" >
                                                        </div>
                                                </div>

                                            </div>
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label >STRATEGY ACTIVITIES </label>
                                                        <input type="text"  class="form-control" value="<?= $data->strategy; ?>" name="strategy" >
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label >PERFORMANCE INDICATORS</label>
                                                        <input type="text"  class="form-control" value="<?= $data->pi; ?>" name="pi" >
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <label >MOVs</label>
                                                        <input type="text"  class="form-control" value="<?= $data->movs; ?>" name="movs" >
                                                    </div>
                                                </div>
                                                <div class="col-lg-8">
                                                    <div class="form-group">
                                                        <label >PERSON(S) RESPONSIBLE</label>
                                                        <input type="text"  class="form-control" value="<?= $data->pr; ?>" name="pr" >
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-2">
                                                    <div class="form-group">
                                                        <label >SCHEDULE</label>
                                                        <input type="text"  class="form-control" value="<?= $data->schedule; ?>" name="schedule" >
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label >VENUE</label>
                                                        <input type="text"  class="form-control" value="<?= $data->venue; ?>" name="venue" >
                                                    </div>
                                                </div>
                                                <div class="col-lg-2">
                                                    <div class="form-group">
                                                        <label >BUDGET PER ACTIVITY</label>
                                                        <input type="text"  class="form-control" value="<?= $data->budget; ?>" name="budget" >
                                                    </div>
                                                </div>
                                                
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label >BUDGET SOURCE</label>
                                                        <select class="form-control" name="budget_source" required>
                                                                <option></option>
                                                                <?php   foreach($bs as $row){
                                                                    echo "<option";
                                                                    if($row->description == $data->budget_source){echo " selected ";}
                                                                    echo " value='".$row->description."'>".$row->description."</option>";
                                                                   }
                                                                ?>
                                                                
                                                            </select>
                                                    </div>
                                                </div>

                                                <div class="col-lg-2">
                                                    <div class="form-group">
                                                        <label>Mode of procurement</label>
                                                        <select class="form-control" name="procurement" required>
                                                                <option></option>
                                                                <?php  
                                                                $procurement = array('Direct Purchase',
                                                                                    'Shopping',
                                                                                    'Framework AgreementPublic Bidding',
                                                                                    'Negotiated', 
                                                                                    'Request for Proposal');
                                                                foreach($procurement as $row){
                                                                    echo "<option";
                                                                    if($data->procurement == $row){echo " selected ";}
                                                                    echo " value='".$row."'>".$row."</option>";
                                                                   }
                                                                ?>
                                                                
                                                            </select>
                                                    </div>
                                                </div>


                                            </div>

                                            


                                            <div class="form-group text-left mb-0">
                                               <input type="submit" name="submit" value="Submit" class="btn btn-primary waves-effect waves-light mr-1">
                                                
                                               
                                            </div>

                                        </form>

                                    </div>
                                </div>

                            </div>

                        </div>
                        <!--- end row -->

                    </div>
                    <!-- end container-fluid -->

                </div>
                <!-- end content -->


            <!-- ============================================================== -->
            <!-- End Page content -->
            <!-- ============================================================== -->

             <!-- Footer Start -->
             <?php include('includes/footer.php'); ?>
            <!-- end Footer -->

        </div>
        <!-- END wrapper -->

        <!-- Vendor js -->
        <script src="<?= base_url(); ?>assets/js/vendor.min.js"></script>

        <!-- App js -->
        <script src="<?= base_url(); ?>assets/js/app.min.js"></script>


 <!-- Required datatable js -->
 <script src="<?= base_url(); ?>assets/libs/datatables/jquery.dataTables.min.js"></script>
        <script src="<?= base_url(); ?>assets/libs/datatables/dataTables.bootstrap4.min.js"></script>
        <!-- Buttons examples -->
        <script src="<?= base_url(); ?>assets/libs/datatables/dataTables.buttons.min.js"></script>
        <script src="<?= base_url(); ?>assets/libs/datatables/buttons.bootstrap4.min.js"></script>
        <script src="<?= base_url(); ?>assets/libs/jszip/jszip.min.js"></script>
        <script src="<?= base_url(); ?>assets/libs/pdfmake/pdfmake.min.js"></script>
        <script src="<?= base_url(); ?>assets/libs/pdfmake/vfs_fonts.js"></script>
        <script src="<?= base_url(); ?>assets/libs/datatables/buttons.html5.min.js"></script>
        <script src="<?= base_url(); ?>assets/libs/datatables/buttons.print.min.js"></script>

                <!-- Plugin js-->
                <script src="<?= base_url(); ?>assets/libs/parsleyjs/parsley.min.js"></script>

<!-- Validation init js-->
<script src="<?= base_url(); ?>assets/js/pages/form-validation.init.js"></script>

        <!-- Responsive examples -->
        <script src="<?= base_url(); ?>assets/libs/datatables/dataTables.responsive.min.js"></script>
        <script src="<?= base_url(); ?>assets/libs/datatables/responsive.bootstrap4.min.js"></script>

        <script src="<?= base_url(); ?>assets/libs/datatables/dataTables.keyTable.min.js"></script>
        <script src="<?= base_url(); ?>assets/libs/datatables/dataTables.select.min.js"></script>

        <!-- Datatables init -->
        <script src="<?= base_url(); ?>assets/js/pages/datatables.init.js"></script>


        <!-- Modal -->
        <!-- <div id="custom-modal" class="modal-demo">
            <button type="button" class="close" onclick="Custombox.modal.close();">
                <span>&times;</span><span class="sr-only">Close</span>
            </button>

            

            <h4 class="custom-modal-title">Modal title</h4>
            <div class="custom-modal-text">
                Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.
            </div>
        </div> -->


         <!--  Modal for Adding New Accomplishments -->
            <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="display: none;" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="myLargeModalLabel">Add New Accomplishment</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                               </div>
                             
                               <?php if($this->session->flashdata('success')) : ?>

                                        <?= '<div class="alert alert-success alert-dismissible fade show" role="alert">
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>'
                                                .$this->session->flashdata('success'). 
                                            '</div>'; 
                                        ?>
                                        <?php endif; ?>

                               <div class="modal-body">
                               <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                        <form class="parsley-examples" method="post" action="<?= base_url(); ?>Page/addAccomplishments">
                                            <div class="form-group">
                                                <label >Quarter<span class="text-danger">*</span></label>
                                                <select class="form-control" name="quarter" required>
                                                       <option></option>
                                                       <option value="1st Quarter">1st Quarter</option> 
                                                       <option value="2nd Quarter">2nd Quarter</option>
                                                       <option value="3rd Quarter">3rd Quarter</option>
                                                       <option value="4th Quarter">4th Quarter</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label >Year<span class="text-danger">*</span></label>
                                                <input type="text" name="year" required class="form-control" >
                                            </div>
                                            <div class="form-group">
                                                <label >Activity Title<span class="text-danger">*</span></label>
                                                <input type="text" required class="form-control" name="activity">
                                            </div>
                                            <div class="form-group">
                                                <label >Activity Category <span class="text-danger">*</span></label>
                                                <select class="form-control" name="activityCategory">
                                                       <option></option>
                                                       <option>Activity</option> 
                                                       <option>Updates</option> 
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label >Venue <span class="text-danger">*</span></label>
                                                <input type="text" required  class="form-control" name="venue" >
                                            </div>

                                            <div class="form-group">
                                                <label >Date Conducted <span class="text-danger">*</span></label>
                                                <input type="text" required  class="form-control" name="dateConducted" >
                                            </div>

                                            <div class="form-group text-right mb-0">
                                               <input type="submit" name="submit" value="Submit" class="btn btn-primary waves-effect waves-light mr-1">
                                                
                                               
                                            </div>

                                        </form>
                                    </div>
                                </div>
                                <!-- end card -->
                            </div>
                            <!-- end col -->
                           </div>   
                            <!-- end row -->

                           </div>
                        </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
             <!-- /.modal -->


     <script src="<?= base_url(); ?>assets/libs/custombox/custombox.min.js"></script>
     <script type="text/javascript">
                $(document).on("click", ".open-AddBookDialog", function () {
                    var myBookId = $(this).data('id');
                    $(".modal-body #id").val( myBookId );

                    var fieldId = $(this).data('field');
                    $(".modal-body #field").val( fieldId );
                });
            </script>


    </body>
</html>