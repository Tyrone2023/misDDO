<!-- sample modal content -->
<div id="comlogo" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="myModalLabel">Update Company Logo</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <?= form_open_multipart('upload_com_logo'); ?>
                                                        <div class="form-group col-md-12">
                                                            <label>Image size: 1200x300 px</label>
                                                            <input type="file" required value="<?= set_value('image'); ?>" name="image" class="form-control">
                                                        </div>
                                                        <div class="modal-body">
                                                            <input type="hidden" name="id" id="id" value="">
                                                        </div>    

                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" name="submit" class="btn btn-primary waves-effect waves-light">Save changes</button>
                                                    </div>
                                                    </form>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->