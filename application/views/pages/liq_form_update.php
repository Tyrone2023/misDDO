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
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                        <h4 class="header-title mb-4">Liquidation Input</h4>

                                        <?php $att = array('class' => 'parsley-examples','name' => 'abc',); ?>
                                        <?= form_open('Page/liq_update', $att); ?>

                                            <div class="row">
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <label for="userName">Account Name</label>
                                                        <select class="form-control" name="acc_name" required>
                                                                <option></option>
                                                                <?php  
                                                                foreach($acc_name as $row){
                                                                    echo "<option";
                                                                    if($row->id == $data->acc_name){echo " selected ";}
                                                                    echo " value='".$row->id."'>".$row->name."</option>";
                                                                   }
                                                                ?>
                                                                
                                                            </select>
                                                    </div>
                                                </div>


                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <label for="userName">Categories</label>
                                                        <select class="form-control" name="category" required>
                                                                <option></option>
                                                                <?php  
                                                                 $cat = array(1 => 'MANDATORY BILLS',2 => 'MINOR REPAIR' ,3 => 'TEACHING-LEARNING INSTRUCTION',4 => 'TRAININGS/SEMINAR/TRAVEL');
                                                                foreach($cat as $key => $row){
                                                                    echo "<option";
                                                                    if($data->category == $row){echo " selected ";}
                                                                    echo " value='".$row."'>".$row."</option>";
                                                                   }
                                                                ?>
                                                                
                                                            </select>
                                                    </div>
                                                </div>

                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <label for="userName">Item Description</label>
                                                        <input list="browsers" name="item_des" id="browser" class="form-control" value="<?= $data->item_des; ?>">
                                                        <datalist id="browsers">
                                                            <?php 
                                                                $item = $this->Common->no_cond_group('sgod_app','materials'); 
                                                                foreach($item as $row){
                                                            ?>
                                                            <option value="<?= $row->materials; ?>">
                                                            <?php } ?>
                                                        </datalist>
                                                    
                                                    </div>
                                                </div>

                                                
                                      
                                            </div>


                                            

                                            <div class="row">

                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label for="userName">Unit Of Measure</label>
                                                        <input type="text" name="unit_mesure" parsley-trigger="change" required placeholder="Unit Of Measure" class="form-control" value="<?= $data->unit_mesure; ?>">
                                                    </div>
                                                </div>

                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label for="quantity">Quantity</label>
                                                        <input type="number" name="quantity" parsley-trigger="change" required placeholder="Quantity" class="form-control" id="quantity" oninput="calculateTotal()" value="<?= $data->qty; ?>">
                                                    </div>
                                                </div>

                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label for="unit_cost">Unit Cost</label>
                                                        <input type="number" name="unit_cost" parsley-trigger="change" required placeholder="Unit Cost" class="form-control" id="unit_cost" oninput="calculateTotal()" value="<?= $data->cost; ?>">
                                                    </div>
                                                </div>

                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label for="total_cost">Total Cost</label>
                                                        <input type="text" name="total_cost" parsley-trigger="change" required placeholder="Total Cost" class="form-control" id="total_cost" value="<?= $data->cost*$data->qty; ?>" readonly>
                                                    </div>
                                                </div>

                                                <input type="hidden" name="id" value="<?= $data->id; ?>">
                                                <input type="hidden" name="bcode" value="<?= $data->bcode; ?>">
                                                <input type="hidden" name="school_id" value="<?= $data->school_id; ?>">
                                                <input type="hidden" name="fy" value="<?= $data->fy; ?>">
                                                <input type="hidden" name="mm" value="<?= $data->mm; ?>">
                                                <input type="hidden" name="mqty" value="<?= $this->uri->segment(4); ?>">

                                            </div>

                                            <div class="form-group text-left mb-0">
                                                    <input type="submit" value="Submit" name="submit" class="btn btn-primary">
                                                </div>

                                      

                                            

                                        </form>

                                        
                                    </div>
                                </div>
                                <!-- end card -->
                            </div>
                            <!-- end col -->
                        </div>

                        
                    </div>
                    <!-- end container-fluid -->

                </div>
                <!-- end content -->

                <script>
function calculateTotal() {
    const quantity = document.getElementById('quantity').value;
    const unitCost = document.getElementById('unit_cost').value;
    const totalCost = quantity * unitCost;
    document.getElementById('total_cost').value = totalCost || ''; // Ensure it's empty if no value
}
</script>

                

<?php include('templates/footer.php'); ?>       

             
 