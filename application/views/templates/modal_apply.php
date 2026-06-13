<!-- sample modal content -->
<div id="addBookDialog" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="myModalLabel">Please Add File Links</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <?= form_open_multipart('apply'); ?>
                                                        <div class="form-group col-md-12">
                                                            <label >File Links</label>
                                                            <input type="text" required value="<?= set_value('links'); ?>" name="links" class="form-control"> 
                                                        </div>
                                                        <div class="form-group col-md-12">
                                                        <div class="modal-body">
                                                            <input type="hidden" name="id" id="id" value="">
                                                            <input type="hidden" name="item" id="item" value="">
                                                        </div>    

                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" name="submit" class="btn btn-primary waves-effect waves-light">Save</button>
                                                    </div>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->