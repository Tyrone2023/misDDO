
<?php include('templates/head.php'); ?>
<link href="<?= base_url(); ?>assets/libs/custombox/custombox.min.css" rel="stylesheet" type="text/css">
<?php include('templates/header.php'); ?>

<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            
        
        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box">
                                <div class="card">
                                    <div class="card-body">

                                                    <?= form_open('Sbfp/sbfp_bmi_dn_form'); ?>

                                                        <div class="form-group row">
                                                            <div class="col-lg-4">
                                                                <label >School Name </label>
                                                                <select class="form-control" name="schoolID" data-toggle="select2" required>
                                                                    <option></option>
                                                                    <?php foreach($school as $row){?>
                                                                        <option value="<?= $row->schoolID; ?>"><?= $row->schoolName; ?></option>
                                                                    <?php }?>
                                                                    
                                                                </select>
                                                            </div>
                                                        </div>
                                                        
   
                                                        <div class="form-group row">
                                                            <div class="col-lg-12">
                                                                <input type="submit" name="submit" class="btn btn-primary waves-effect waves-light" value="Submit" />
                                                            </div>
                                                        </div>
                                                    </form>
                                    </div>
                                </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>

           
            




    




    <?php include('templates/footer.php'); ?>

