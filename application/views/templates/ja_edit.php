<?php 
                $dist = $this->Common->no_cond('district');
                $school = $this->Common->no_cond('schools');  
            
                ?>


<div class="modal fade applyedit" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="myLargeModalLabel">Edit my Application</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                        
                                                    <?php 
                                                        $attributes = array('class' => 'parsley-examples');
                                                        echo form_open('pages/edit_application/'.$this->session->c_id);
                                                    ?>

                                                         <input type="hidden" name="id" id="job">
                                                        
                                                        
                                                        <div class="form-group row">
                                                            <label for="inputPassword3" class="col-md-3 col-form-label">District</label>
                                                            <div class="col-md-9">
                                                                <select class="form-control" name="district" id="distedit" required>
                                                                <option value="">Please Select Your District</option>
                                                                <?php foreach($dist as $row){?>
                                                                <option value="<?= $row->discription; ?>"><?= strtoupper($row->discription); ?></option>
                                                                <?php } ?>

                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label for="inputPassword3" class="col-md-3 col-form-label">School</label>
                                                            <div class="col-md-9">
                                                                <select class="form-control" name="school" id="schooledit" required>
                                                                    <option value="">Please Select School</option>
                                                                    <?php foreach($school as $row){?>
                                                                        <option data-distedit="<?= $row->district; ?>" value="<?= $row->schoolID; ?>"><?= strtoupper($row->schoolName); ?></option>
                                                                    <?php } ?>

                                                                </select>
                                                            </div>
                                                        </div>

                                                       
                                                        
                                                        
                                                       
                                                        <div class="form-group mb-0 justify-content-end row">
                                                            <div class="col-md-9">
                                                                <input type="submit" name="submit" value="Submit" class="btn btn-info waves-effect waves-light">
                                                                <!-- <button type="submit" class="btn btn-info waves-effect waves-light">Submit</button> -->
                                                            </div>
                                                        </div>
                                                    </form>

                                                    </div>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                <!-- /.modal -->

                


                <script>
                                        $(document).ready(function() {
                                            $("#schooledit option").hide();

                                            $("#distedit").change(function() {
                                                var val = $(this).val();
                                                $("#schooledit option").hide();
                                                $("#schooledit").val("");
                                                $("#schooledit [data-distedit='" + val + "']").show(); //show options where attribute value matches.
                                                $("#schooledit").change();
                                            });


                                            });
                                    </script>