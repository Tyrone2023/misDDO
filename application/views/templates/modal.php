<!-- sample modal content -->
<div id="addBookDialog" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="myModalLabel">Upload 201 File</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <?= form_open_multipart('twoOonefiles'); ?>
                                                        <div class="form-group col-md-12">
                                                            <label >File Description</label>
                                                            <input type="text" required value="<?= set_value('discription'); ?>" name="discription" class="form-control"> 
                                                        </div>
                                                        <div class="form-group col-md-12">
                                                        <label >File attachment</label>
                                                            <input type="file" required value="<?= set_value('attachment'); ?>" name="attachment" class="form-control">
                                                        </div>
                                                        <div class="modal-body">
                                                            <input type="hidden" name="id" id="id" value="">
                                                        </div>    

                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" name="submit" class="btn btn-primary waves-effect waves-light">Save</button>
                                                    </div>
                                                </div>
                                                </form>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->


<!-- sample modal content -->
<div id="addAwards" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="myModalLabel">Add New Award and Recognition</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <?= form_open('Pages/awards'); ?>
                                                   
                                                   <div class="form-group col-md-12">
                                                       <label >Award</label>
                                                       <input type="text" required value="<?= set_value('award'); ?>" name="award" class="form-control"> 
                                                   </div>
                                                   <div class="form-group col-md-12">
                                                   <label >Award Description</label>
                                                       <input type="text" value="<?= set_value('awardDesc'); ?>" name="awardDesc" class="form-control">
                                                   </div>

                                                   <div class="form-group col-md-12">
                                                   <label >Awarded By</label>
                                                       <input type="text" required value="<?= set_value('awardedBy'); ?>" name="awardedBy" class="form-control">
                                                   </div>

                                                   <div class="modal-body">
                                                       <input type="hidden" name="id" id="id" value="">
                                                   </div>    

                                               </div>
                                               <div class="modal-footer">
                                               <button type="submit" name="awards" class="btn btn-primary waves-effect waves-light">Save</button>
                                               </div>
                                           </div>
                                            </form>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->

                                        
<!-- Family -->
<div id="familymodal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="myModalLabel">Add Family Members</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <?= form_open('Pages/family'); ?>
                                                   
                                                   <div class="form-group col-md-12">
                                                       <label >Full Name</label>
                                                       <input type="text" required value="<?= set_value('fullName'); ?>" name="fullName" class="form-control"> 
                                                   </div>
                                                   <div class="form-group col-md-12">
                                                   <label >Relationship</label>
                                                       <input type="text" value="<?= set_value('relationship'); ?>" name="relationship" class="form-control">
                                                   </div>

                                                   <div class="form-group col-md-12">
                                                   <label >Birth Date</label>
                                                       <input type="date" required name="bDate" class="form-control">
                                                   </div>

                                                   <div class="modal-body">
                                                       <input type="hidden" name="id" id="id" value="">
                                                   </div>    

                                               </div>
                                               <div class="modal-footer">
                                               <button type="submit" name="awards" class="btn btn-primary waves-effect waves-light">Save</button>
                                               </div>
                                           </div>
                                            </form>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->





<!-- Education -->
<div class="modal fade" id="educationmodal" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="myModalLabel">Education</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <?= form_open('Pages/education'); ?>
                                                   
                                                   <div class="form-group col-md-12">
                                                       <label >Level</label>
                                                       <input type="text" required value="<?= set_value('level'); ?>" name="level" class="form-control"> 
                                                   </div>
                                                   <div class="form-group col-md-12">
                                                   <label >School Name</label>
                                                       <input type="text" value="<?= set_value('schoolName'); ?>" name="schoolName" class="form-control">
                                                   </div>

                                                   <div class="form-group col-md-12">
                                                   <label >Course</label>
                                                       <input type="text" value="<?= set_value('course'); ?>" name="course" class="form-control">
                                                   </div>

                                                   <div class="form-group col-md-12">
                                                   <label >Year Started</label>
                                                       <input type="text" value="<?= set_value('yearStarted'); ?>" name="yearStarted" class="form-control">
                                                   </div>

                                                   <div class="form-group col-md-12">
                                                   <label >Year Ended</label>
                                                       <input type="text" value="<?= set_value('yearEnded'); ?>" name="yearEnded" class="form-control">
                                                   </div>

                                                   <div class="form-group col-md-12">
                                                   <label >Year Graduated</label>
                                                       <input type="text" value="<?= set_value('yearGraduated'); ?>" name="yearGraduated" class="form-control">
                                                   </div>

                                                   
                                                   <div class="form-group col-md-12">
                                                   <label >Scholarship</label>
                                                       <input type="text" value="<?= set_value('scholarship'); ?>" name="scholarship" class="form-control">
                                                   </div>

                                                   <div class="modal-body">
                                                       <input type="hidden" name="id" id="id" value="">
                                                   </div>    

                                               </div>
                                               <div class="modal-footer">
                                               <button type="submit" name="awards" class="btn btn-primary waves-effect waves-light">Save</button>
                                               </div>
                                           </div>
                                            </form>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->


<!-- Trainings -->
<div class="modal fade" id="trainingmodal" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
                                            <div class="modal-dialog modal-lg" role="document" >
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="myModalLabel">Trainings and Serminars Attended</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <?php 
                                                        $attributes = array('class' => 'parsley-examples');
                                                        echo form_open_multipart('Pages/trainings', $attributes);
                                                    ?>

                                                    <div class="form-group row">
                                                            <label for="inputPassword5" class="col-md-3 col-form-label">Training Title</label>
                                                            <div class="col-lg-9">
                                                                <textarea name="trainingTitle" class="form-control" rows="5" id=""></textarea>
                                                            </div>
                                                        </div>
                                                   
                                                

                                                   <div class="form-group row">
                                                        <label for="inputPassword5" class="col-md-3 col-form-label">Date Started</label>
                                                        <div class="col-lg-9">
                                                            <input type="date" value="<?= set_value('dateStarted'); ?>" name="dateStarted" class="form-control">
                                                        </div>
                                                   </div>

                                                   

                                                   <div class="form-group row">
                                                        <label for="inputPassword5" class="col-md-3 col-form-label">Date Finished</label>
                                                        <div class="col-lg-9">
                                                            <input type="date" value="<?= set_value('dateFinished'); ?>" name="dateFinished" class="form-control">
                                                        </div>
                                                   </div>
                                                   
                                                   <div class="form-group row">
                                                        <label for="inputPassword5" class="col-md-3 col-form-label">Conducted By</label>
                                                        <div class="col-lg-9">
                                                            <input type="text" value="<?= set_value('sponsor'); ?>" name="sponsor" class="form-control">
                                                        </div>
                                                   </div>

                                                        
                                                        <div class="form-group row">
                                                            <label for="inputPassword5" class="col-md-3 col-form-label">Attachment</label>
                                                            <div class="col-md-9">
                                                                <input type="file" class="form-control" name="file" required>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="form-group row">
                                                            <label for="inputPassword5" class="col-md-3 col-form-label">No. of hours</label>
                                                            <div class="col-lg-9">
                                                                <input name="noHours" type="text" maxlength="11" oninput="this.value=this.value.replace(/[^0-9]/g,'')" name="assign" class="form-control" value="" required>
                                                            </div>
                                                        </div>

                                                   
                                                   <div class="modal-body">
                                                       <input type="hidden" name="id" id="id" value="">
                                                   </div>    

                                               </div>
                                               <div class="modal-footer">
                                               <button type="submit" name="awards" class="btn btn-primary waves-effect waves-light">Save</button>
                                               </div>
                                           </div>
                                            </form>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->


<!-- Employment -->
<div class="modal fade" id="employmentmodal" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="myModalLabel">Employment</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <?= form_open('Pages/employment'); ?>
                                                   
                                                   <div class="form-group col-md-12">
                                                       <label >Position</label>
                                                       <input type="text" required value="<?= set_value('empPosition'); ?>" name="empPosition" class="form-control"> 
                                                   </div>
                                                   <div class="form-group col-md-12">
                                                   <label >Appointment Date</label>
                                                       <input type="date" value="<?= set_value('appointDate'); ?>" name="appointDate" class="form-control">
                                                   </div>

                                                   <div class="form-group col-md-12">
                                                   <label >Salary Grade</label>
                                                       <input type="text" value="<?= set_value('sgNo'); ?>" name="sgNo" class="form-control">
                                                   </div>

                                                   <div class="form-group col-md-12">
                                                   <label >Step Increment</label>
                                                       <input type="text" value="<?= set_value('stepInc'); ?>" name="stepInc" class="form-control">
                                                   </div>

                                                   <div class="form-group col-md-12">
                                                   <label >Salary</label>
                                                       <input type="text" value="<?= set_value('salary'); ?>" name="salary" class="form-control">
                                                   </div>

                                                   <div class="form-group col-md-12">
                                                   <label >Status</label>
                                                       <input type="text" value="<?= set_value('empStatus'); ?>" name="empStatus" class="form-control">
                                                   </div>

                                                   <div class="form-group col-md-12">
                                                   <label >Item No.</label>
                                                       <input type="text" value="<?= set_value('itemNo'); ?>" name="itemNo" class="form-control">
                                                   </div>

                                                   <div class="form-group col-md-12">
                                                   <label >Station/Department</label>
                                                       <input type="text" value="<?= set_value('empStation'); ?>" name="empStation" class="form-control">
                                                   </div>

                                                                                  
                                                   <div class="modal-body">
                                                       <input type="hidden" name="id" id="id" value="">
                                                   </div>    

                                               </div>
                                               <div class="modal-footer">
                                               <button type="submit" name="awards" class="btn btn-primary waves-effect waves-light">Save</button>
                                               </div>
                                           </div>
                                            </form>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->

<!-- IPCR -->
<div class="modal fade" id="ipcrmodal" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="myModalLabel">IPCR Uploading</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <?= form_open_multipart('Pages/ipcr'); ?>
                                                        <div class="form-group col-md-12">
                                                            <label >Calendar Year</label>
                                                            <input type="text" required value="<?= set_value('cYear'); ?>" name="cYear" class="form-control"> 
                                                        </div>

                                                        <div class="form-group col-md-12">
                                                            <label >Rating</label>
                                                            <input type="text" required value="<?= set_value('aRating'); ?>" name="aRating" class="form-control"> 
                                                        </div>
                                                        <div class="form-group col-md-12">
                                                            <label >Adjectival Rating</label>
                                                            <input type="text" required value="<?= set_value('adRating'); ?>" name="adRating" class="form-control"> 
                                                        </div>

                                                        <div class="form-group col-md-12">
                                                        <label >IPCR Scan Copy (In PDF format)</label>
                                                            <input type="file" required value="<?= set_value('fileName'); ?>" name="fileName" class="form-control">
                                                        </div>
                                                        <div class="modal-body">
                                                            <input type="hidden" name="id" id="id" value="">
                                                        </div>    

                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" name="submit" class="btn btn-primary waves-effect waves-light">Save</button>
                                                    </div>
                                                </div>
                                                </form>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->

