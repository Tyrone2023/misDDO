<!-- sample modal content -->
<div id="addAwards" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="myModalLabel">Awards and Recognitions</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <?= form_open('awards'); ?>
                                                   
                                                        <div class="form-group col-md-12">
                                                            <label >Award</label>
                                                            <input type="text" required value="<?= set_value('award'); ?>" name="award" class="form-control"> 
                                                        </div>
                                                        <div class="form-group col-md-12">
                                                        <label >Award Description</label>
                                                            <input type="text" required value="<?= set_value('awardDesc'); ?>" name="awardDesc" class="form-control">
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
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->