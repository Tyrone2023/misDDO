<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8" />
        <title>QAME | QUALITY ASSURANCE MONITORING AND EVALUATION</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="Responsive bootstrap 4 admin template" name="description" />
        <meta content="Coderthemes" name="author" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <!-- App favicon -->
        <link rel="shortcut icon" href="<?= base_url(); ?>assets/images/davor.ico">

        <!-- Plugins css-->
        <link href="<?= base_url(); ?>assets/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css" />

        <!-- App css -->
        <link href="<?= base_url(); ?>assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" id="bootstrap-stylesheet" />
        <link href="<?= base_url(); ?>assets/css/icons.min.css" rel="stylesheet" type="text/css" />
        <link href="<?= base_url(); ?>assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-stylesheet" />

    </head>

    <body data-layout="horizontal">

        <!-- Begin page -->
        <div id="wrapper">

            <!-- Navigation Bar-->
            <header id="topnav">
                    <!-- Topbar Start -->
                    <div class="navbar-custom">
                        <div class="container-fluid">
                            <ul class="list-unstyled topnav-menu float-right mb-0">
    
                                <li class="dropdown notification-list">
                                    <!-- Mobile menu toggle-->
                                    <a class="navbar-toggle nav-link">
                                        <div class="lines">
                                            <span></span>
                                            <span></span>
                                            <span></span>
                                        </div>
                                    </a>
                                    <!-- End mobile menu toggle-->
                                </li>
                                

                                

                               
        

                                <li class="dropdown notification-list">
                                    <a class="nav-link dropdown-toggle nav-user mr-0 waves-effect waves-light" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                                        <img src="<?= base_url(); ?>assets/images/davor.png" alt="user-image" class="rounded-circle">
                                        <span class="pro-user-name ml-1">
                                            Visitor   <i class="mdi mdi-chevron-down"></i> 
                                        </span>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right profile-dropdown ">
                                        <!-- item-->
                                        <div class="dropdown-header noti-title">
                                            <h6 class="text-overflow m-0">Welcome !</h6>
                                        </div>
            
            
                                        <div class="dropdown-divider"></div>
            
                                        <!-- item-->
                                        <a href="<?= base_url(); ?>" class="dropdown-item notify-item">
                                            <i class="mdi mdi-logout-variant"></i>
                                            <span>Login</span>
                                        </a>
            
                                    </div>
                                </li>
    
                               
                            </ul>
                
                             <!-- LOGO -->
                             <div class="logo-box">
                                <a href="index.html" class="logo text-center logo-dark">
                                    <span class="logo-lg">
                                        <img src="<?= base_url(); ?>assets/images/logo-dark.png" alt="" height="40">
                                        <!-- <span class="logo-lg-text-dark">Velonic</span> -->
                                    </span>
                                    <span class="logo-sm">
                                        <!-- <span class="logo-lg-text-dark">V</span> -->
                                        <img src="<?= base_url(); ?>assets/images/logo-sm.png" alt="" height="22">
                                    </span>
                                </a>

                                <a href="index.html" class="logo text-center logo-light">
                                    <span class="logo-lg">
                                        <img src="<?= base_url(); ?>assets/images/logo-light.png" alt="" height="40">
                                        <!-- <span class="logo-lg-text-dark">Velonic</span> -->
                                    </span>
                                    <span class="logo-sm">
                                        <!-- <span class="logo-lg-text-dark">V</span> -->
                                        <img src="<?= base_url(); ?>assets/images/logo-sm.png" alt="" height="20">
                                    </span>
                                </a>
                            </div>
                            <!-- LOGO -->
    
                            <div class="clearfix"></div>
                        </div>
                    </div>
                    <!-- end Topbar -->
    
                    
                </header>
                <!-- End Navigation Bar-->

            <!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->

            <div class="content-page">
                <div class="content">

                    <!-- Start Content-->
                    <div class="container-fluid">

                    <?php if($this->session->flashdata('success')) : ?>

                        <?= '<br /><div class="alert alert-success alert-dismissible fade show" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>'
                                .$this->session->flashdata('success'). 
                            '</div>'; 
                        ?>
                        <?php endif; ?>

                        <?php if($this->session->flashdata('danger')) : ?>
                        <?= '<br /><div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>'
                                .$this->session->flashdata('danger'). 
                            '</div>'; 
                        ?>
                        <?php endif;  ?>


                    

                     <!-- start page title -->
                     <div class="row">
                            <div class="col-sm-12">
                                <div class="profile-bg-picture" style="background-image:url('<?= base_url(); ?>assets/images/bg-profile.jpg')">
                                    <span class="picture-bg-overlay"></span>
                                    <!-- overlay -->
                                </div>
                                <!-- meta -->
                                <div class="profile-user-box">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <h2 class='text-center'><?= $data->act_name; ?><br /><br />Day <?= $ns; ?></h2>
                                        </div>
                                        
                                    </div>
                                </div>
                                <!--/ meta -->
                            </div>
                        </div>
                        <!-- end row -->

                        <?php 
                            $attributes = array('class' => 'parsley-examples');
                            echo form_open('pages/mcea', $attributes);
                        ?>

                        <div class="row">
                            
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-header">
                                        DECLARATION AND ATTESTATION: 
                                    </div>
                                    <div class="card-body">
                                        <blockquote class="card-bodyquote">
                                            <p class="text-justify">DepEd Davao Oriental abides and strictly enforces the provisions of RA 10173 or the Data Privacy Act of 2012. Your response to this online survey tool will provide DepEd Davao Oriental with the needed information to determine the level of satisfaction on the quality of the training conducted. By selecting the checkbox below this statement and clicking "submit" you give your consent to the following: 1. Allow DepEd Davao Oriental to collect, process and keep your personal information for lawful purposes; 2. DepEd Davao Oriental cannot disclose your personal information to any third party without your explicit permission. It can, however, share said information with its functional divisions, sections, units, and schools for legitimate and lawful objectives and purposes or to comply with law enforcement and legal processes. You confirm that you are well-informed of the purposes of this effort and have agreed to the above-cited information.</p>
                                            <footer class="text-xs"> <input type="checkbox" required name="confirm" value="1"> &nbsp;<cite class="text-danger">confirm that I have read and understood the above information and agree to its every detail.</cite>
                                            </footer>
                                        </blockquote>
                                    </div>
                                </div>
                            </div>

                           
                        </div>
                        <!-- end row -->

                        <div class="row">

                            <!-- <div class="col-lg-12">
                                <div class="card-deck-wrapper">
                                    <div class="card-deck">
                                        <div class="card mb-3">
                                            <img class="card-img-top img-fluid" src="<?= base_url(); ?>assets/images/qameratings.png" alt="Card image cap">
                                            <div class="card-body"><br />
                                                <h5 class="card-title">Standard:</h5><br />
                                                
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div> -->

                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                    <h4 class="header-title mb-4">Participant's Information</h4>

                                        
                                            <!-- <div class="form-group row">
                                                <label for="inputEmail3" class="col-md-4 col-form-label">Day<span class="text-danger">*</span></label>
                                                <div class="col-md-8">
                                                    <?php 
                                                    $date1 = $data->date_start;
                                                    $date2 = $data->date_end;
                                                    
                                                    $diff = abs(strtotime($date2) - strtotime($date1));
                                                    
                                                    $years = floor($diff / (365*60*60*24));
                                                    $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
                                                    $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
                                                    
                                                    //printf("%d years, %d months, %d days\n", $years, $months, $days);
                                                    $day = $days+1;
                                                    
                                                    ?>
                                                    <select class="custom-select mt-3" name="day">
                                                        <option selected></option>
                                                        <?php for($i=1; $i < $day+1; $i++){ ?>
                                                        <option value="<?= $i; ?>">Day <?= $i; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div> -->

                                            
                                            <input type="hidden" name="day" value="<?php if(!empty($s->day)){echo $s->day;}else{echo $ns; } ?>">

                                            <div class="form-group row">
                                                <label class="col-md-4 col-form-label">Complete Name<span class="text-danger">*</span></label>
                                                <div class="col-md-2">
                                                    <label class="col-md-12 col-form-label">Firstname</label>
                                                    <input onkeyup="this.value = this.value.toUpperCase();"  value="<?= (!empty($fb)) ? $fb->fname : strtoupper($this->input->post('fname')); ?>" type="text" required  class="form-control" style="text-transform: uppercase" name="fname">
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="col-md-12 col-form-label">Middlename</label>
                                                    <input onkeyup="this.value = this.value.toUpperCase();"   value="<?= (!empty($fb)) ? $fb->mname : strtoupper($this->input->post('mname')); ?>" type="text"  class="form-control" name="mname">
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="col-md-12 col-form-label">Lastname</label>
                                                    <input onkeyup="this.value = this.value.toUpperCase();"  value="<?= (!empty($fb)) ? $fb->lname : strtoupper($this->input->post('lname')); ?>" type="text" required  class="form-control" name="lname">
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="col-md-12 col-form-label">Age</label>
                                                    <input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1').replace(/^0[^.]/, '0');" value="<?= (!empty($fb)) ? $fb->age : ''; ?>" type="text" required  class="form-control" name="age" value='<?= (!empty($fb)) ? $fb->age : strtoupper($this->input->post('age')); ?>'>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-md-4 col-form-label">Position<span class="text-danger">*</span></label>
                                                <div class="col-md-8">
                                                    <input onkeyup="this.value = this.value.toUpperCase();" value="<?= (!empty($fb)) ? $fb->position : ''; ?>" type="text" required  name="position" class="form-control" >
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-md-4 col-form-label">DepEd Email<span class="text-danger">*</span></label>
                                                <div class="col-md-8">
                                                    <input  value="<?= (!empty($fb)) ? $fb->email : ''; ?>" type="email" required  name="email" class="form-control" >
                                                </div>
                                            </div>
                                            
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-md-4 col-form-label">Gender<span class="text-danger">*</span></label>
                                                <div class="col-md-8">
                                                    <select class="custom-select mt-3"  name="gender" required>
                                                    `   <option></option>
                                                        <?php 
                                                            $sex = array(1=>'Male',2=>'Female',3=>'Others');
                                                            foreach($sex as $key => $row){
                                                        ?>
                                                        <option  <?php if(!empty($fb)){if($key == $fb->gender){echo "selected";}} ?> value="<?= $key; ?>"><?= $row; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-md-4 col-form-label">Ethnicity<span class="text-danger">*</span></label>
                                                <div class="col-md-8">
                                                    <select class="custom-select mt-3" required name="ethnicity">
                                                        <option value="0" disabled selected>Choose Ethnicity</option>
                                                        <?php foreach($ethnicity as $row){ ?>
                                                        <option <?php if(!empty($fb)){if($row->id == $fb->gender){echo "selected";}} ?> value="<?= $row->id; ?>"><?= $row->name; ?></option>
                                                        <?php } ?>
                                                    </select>   
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-md-4 col-form-label">Religion<span class="text-danger">*</span></label>
                                                <div class="col-md-8">
                                                    <select class="custom-select mt-3" required name="religion">
                                                        <option value="0" disabled selected>Choose Religion</option>
                                                        <?php foreach($religion as $row){ ?>
                                                        <option  <?php if(!empty($fb)){if($row->id == $fb->religion){echo "selected";}} ?> value="<?= $row->id; ?>"><?= $row->name; ?></option>
                                                        <?php } ?>
                                                    </select>   
                                                </div>
                                            </div>


                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-md-4 col-form-label">Division<span class="text-danger">*</span></label>
                                                <div class="col-md-8">
                                                    <select class="custom-select mt-3" required name="division">
                                                    <option value="2" selected>Davao Oriental</option>
                                                        <!-- <?php foreach($division as $row){ if($row->id != 1){?>
                                                        <option <?php if(!empty($fb)){if($row->id == $fb->division){echo "selected";}} ?> value="<?= $row->id; ?>"><?= $row->name; ?></option>
                                                        <?php }} ?> -->
                                                    </select>   
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-md-4 col-form-label">Districts</label>
                                                <div class="col-md-8">
                                                    <select class="custom-select mt-3" required name="districts">
                                                        <option value="0" disabled selected>Choose District</option>
                                                        <?php foreach($district as $row){ ?>
                                                        <option <?php if(!empty($fb)){if($row->id == $fb->districts){echo "selected";}} ?> value="<?= $row->id; ?>"><?= $row->discription; ?></option>
                                                        <?php } ?>
                                                    </select>   
                                                </div>
                                            </div>
                                            
                                    </div>
                                </div>
                                <!-- end card -->
                            </div>
                            <!-- end col -->
                           
                        </div>
                        <!-- end row -->

                       
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                    <div class="table-responsive">
                                        <h4 class="header-title mb-4">Other Information of Participants:</h4>
                                            <table class="table table-borderless mb-0">
                                                <thead>
                                                    <tr>
                                                        <th></th>
                                                        <th class="text-center">Yes</th>
                                                        <th class="text-center">No</th>
                                                    </tr>
                                                </thead>
                                                
                                                <tbody>
                                                    <?php 
                                                        $question = array(
                                                            'Are you a member of LGBTQ+?','Are you a lactating mother? ( For female participant only)',
                                                            'Are you a person with Disability?','Are you pregnant? (for female pax only)'
                                                                        );
                                                        $name = 'oipq'; 
                                                        $no = 1;
                                                        $c = 0;
                                                        foreach($question as $row){

                                                    ?>
                                                    <tr <?php echo (++$c%2 ? "" : "class='table-active'"); ?>>
                                                        <td><?= $row; ?><?php $nn = $name.$no++; ?><input type="radio" name="<?= $nn; ?>" checked value="0" style="opacity: 0;"></td>
                                                        <td class="text-center"><input type="radio" <?php if(!empty($fb)){if($fb->$nn == 2){echo "checked";}} ?> name="<?= $nn; ?>" value="2"></td>
                                                        <td class="text-center"><input type="radio"  <?php if(!empty($fb)){if($fb->$nn == 1){echo "checked";}} ?> name="<?= $nn; ?>" value="1"></td>
                                                    </tr>
                                                    <?php } ?>
                                                    
                                                </tbody>
                                            </table>
                                        </div>
                                        
                                    </div>
                                </div>
                                <!-- end card -->
                            </div>
                            <!-- end col -->

                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                    <div class="table-responsive">
                                        <h4 class="header-title mb-4">On Pregnant and Lactating Women participants  :</h4>
                                            <table class="table table-borderless mb-0">
                                                <thead>
                                                    <tr>
                                                        <th></th>
                                                        <th class="text-center"><i class="mdi mdi-emoticon mdi-36px text-info"></i></th>
                                                        <th class="text-center"><i class="mdi mdi-emoticon-happy mdi-36px text-info"></i></th>
                                                        <th class="text-center"><i class="mdi mdi-emoticon-sad mdi-36px text-danger"></i></th>
                                                        <th class="text-center"><i class="mdi mdi-emoticon-angry mdi-36px text-danger"></i></th>
                                                    </tr>
                                                    <tr>
                                                        <th></th>
                                                        <th class="text-center">Strongly Agree</th>
                                                        <th class="text-center">Agree</th>
                                                        <th class="text-center">Disagree</th>
                                                        <th class="text-center">Strong Disagree</th> 
                                                    </tr>
                                                </thead>
                                                
                                                <tbody>
                                                    <?php 
                                                        $question = array(
                                                            'Accessibility of the venue for pregnant women','Ramp and elevator for venue in higher floors for pregnant women are available.',
                                                            'Breaks to facilitate breastfeeding of their infants for lactating mother'
                                                                        );
                                                        $name = 'plwpq'; 
                                                        $no = 1;
                                                        $c = 0;
                                                        foreach($question as $row){

                                                    ?>
                                                    <tr <?php echo (++$c%2 ? "" : "class='table-active'"); ?>>
                                                        <td><?= $row; ?><?php $nn = $name.$no++; ?><input type="radio" name="<?= $nn; ?>" checked value="0" style="opacity: 0;"></td>
                                                        <td class="text-center"><input <?php if(!empty($fb)){if($fb->$nn == 4){echo "checked";}} ?> type="radio" name="<?= $nn; ?>" value="4"></td>
                                                        <td class="text-center"><input <?php if(!empty($fb)){if($fb->$nn == 3){echo "checked";}} ?> type="radio" name="<?= $nn; ?>" value="3"></td>
                                                        <td class="text-center"><input <?php if(!empty($fb)){if($fb->$nn == 2){echo "checked";}} ?> type="radio" name="<?= $nn; ?>" value="2"></td>
                                                        <td class="text-center"><input <?php if(!empty($fb)){if($fb->$nn == 1){echo "checked";}} ?> type="radio" name="<?= $nn; ?>" value="1"></td>
                                                    </tr>
                                                    <?php } ?>
                                                    
                                                </tbody>
                                            </table>
                                        </div>
                                        
                                    </div>
                                </div>
                                <!-- end card -->
                            </div>
                            <!-- end col -->
                        </div>
                        <!-- end row -->


                        


                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                    <div class="table-responsive">
                                        <h4 class="header-title mb-4">On Persons with Disability:</h4>
                                            <table class="table table-borderless mb-0">
                                                <thead>
                                                    <tr>
                                                        <th></th>
                                                        <th class="text-center"><i class="mdi mdi-emoticon mdi-36px text-info"></i></th>
                                                        <th class="text-center"><i class="mdi mdi-emoticon-happy mdi-36px text-info"></i></th>
                                                        <th class="text-center"><i class="mdi mdi-emoticon-sad mdi-36px text-danger"></i></th>
                                                        <th class="text-center"><i class="mdi mdi-emoticon-angry mdi-36px text-danger"></i></th>
                                                    </tr>
                                                    <tr>
                                                        <th></th>
                                                        <th class="text-center">Strongly Agree</th>
                                                        <th class="text-center">Agree</th>
                                                        <th class="text-center">Disagree</th>
                                                        <th class="text-center">Strong Disagree</th>
                                                    </tr>
                                                </thead>
                                                
                                                <tbody>
                                                    <?php 
                                                        $question = array(
                                                            'Visually impaired participants is provided with assistive tools such as braille and text-to-speech devices',
                                                            'Sign language interpreters are designated and called upon when needed to cater to deaf-mute participants.',
                                                            'Wheel chair is made available to physically impaired participants',
                                                            'Ramps and elevator are available in case the venue is on higher floors to cater to physically impaired participants.'
                                                                        );
                                                        $name = 'pwdq'; 
                                                        $no = 1;
                                                        $c = 0;
                                                        foreach($question as $row){

                                                    ?>
                                                    <tr <?php echo (++$c%2 ? "" : "class='table-active'"); ?>>
                                                        <td><?= $row; ?><?php $nn = $name.$no++; ?><input type="radio" name="<?= $nn; ?>" checked value="0" style="opacity: 0;"></td>
                                                        <td class="text-center"><input <?php if(!empty($fb)){if($fb->$nn == 4){echo "checked";}} ?> type="radio" name="<?= $nn; ?>" value="4"></td>
                                                        <td class="text-center"><input <?php if(!empty($fb)){if($fb->$nn == 3){echo "checked";}} ?> type="radio" name="<?= $nn; ?>" value="3"></td>
                                                        <td class="text-center"><input <?php if(!empty($fb)){if($fb->$nn == 2){echo "checked";}} ?> type="radio" name="<?= $nn; ?>" value="2"></td>
                                                        <td class="text-center"><input <?php if(!empty($fb)){if($fb->$nn == 1){echo "checked";}} ?> type="radio" name="<?= $nn; ?>" value="1"></td>
                                                    </tr>
                                                    <?php } ?>
                                                    
                                                </tbody>
                                            </table>
                                        </div>
                                        
                                    </div>
                                </div>
                                <!-- end card -->
                            </div>
                            <!-- end col -->

                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                    <div class="table-responsive">
                                        <h4 class="header-title mb-4">On Lesbian, Gay, Bisexual, Transgender, and Queer (LGBTQ+)</h4>
                                            <table class="table table-borderless mb-0">
                                                <thead>
                                                    <tr>
                                                        <th></th>
                                                        <th class="text-center"><i class="mdi mdi-emoticon mdi-36px text-info"></i></th>
                                                        <th class="text-center"><i class="mdi mdi-emoticon-happy mdi-36px text-info"></i></th>
                                                        <th class="text-center"><i class="mdi mdi-emoticon-sad mdi-36px text-danger"></i></th>
                                                        <th class="text-center"><i class="mdi mdi-emoticon-angry mdi-36px text-danger"></i></th>
                                                    </tr>
                                                    <tr>
                                                        <th></th>
                                                        <th class="text-center">Strongly Agree</th>
                                                        <th class="text-center">Agree</th>
                                                        <th class="text-center">Disagree</th>
                                                        <th class="text-center">Strong Disagree</th>
                                                    </tr>
                                                </thead>
                                                
                                                <tbody>
                                                    <?php 
                                                        $question = array(
                                                            'Venue have an all-gender restroom facility.',
                                                            'Dress codes for learning and development activities is inclusive and flexible yet in a dignified manner.'
                                                                        );
                                                        $name = 'lgbtqq'; 
                                                        $no = 1;
                                                        $c = 0;
                                                        foreach($question as $row){

                                                    ?>
                                                    <tr <?php echo (++$c%2 ? "" : "class='table-active'"); ?>>
                                                        <td><?= $row; ?><?php $nn = $name.$no++; ?><input type="radio" name="<?= $nn; ?>" checked value="0" style="opacity: 0;"></td>
                                                        <td class="text-center"><input <?php if(!empty($fb)){if($fb->$nn == 4){echo "checked";}} ?> type="radio" name="<?= $nn; ?>" value="4"></td>
                                                        <td class="text-center"><input <?php if(!empty($fb)){if($fb->$nn == 3){echo "checked";}} ?> type="radio" name="<?= $nn; ?>" value="3"></td>
                                                        <td class="text-center"><input <?php if(!empty($fb)){if($fb->$nn == 2){echo "checked";}} ?> type="radio" name="<?= $nn; ?>" value="2"></td>
                                                        <td class="text-center"><input <?php if(!empty($fb)){if($fb->$nn == 1){echo "checked";}} ?> type="radio" name="<?= $nn; ?>" value="1"></td>
                                                    </tr>
                                                    <?php } ?>
                                                    
                                                </tbody>
                                            </table>
                                        </div>
                                        
                                    </div>
                                </div>
                                <!-- end card -->
                            </div>
                            <!-- end col -->
                        </div>
                        <!-- end row -->

                        <div class="row">

                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                        <h4>I. PROGRAM MANAGEMENT</h4>                
                                    
                                        </div>  
                                </div>
                                <!-- end card -->
                            </div>
                            <!-- end col -->


                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                    <div class="table-responsive">
                                            <table class="table table-borderless mb-0">
                                                <thead>
                                                    <tr>
                                                        <th></th>
                                                        <th class="text-center"><i class="mdi mdi-emoticon mdi-36px text-info"></i></th>
                                                        <th class="text-center"><i class="mdi mdi-emoticon-happy mdi-36px text-info"></i></th>
                                                        <th class="text-center"><i class="mdi mdi-emoticon-sad mdi-36px text-danger"></i></th>
                                                        <th class="text-center"><i class="mdi mdi-emoticon-angry mdi-36px text-danger"></i></th>
                                                    </tr>
                                                    <tr>
                                                        <th></th>
                                                        <th class="text-center">Strongly Agree</th>
                                                        <th class="text-center">Agree</th>
                                                        <th class="text-center">Disagree</th>
                                                        <th class="text-center">Strong Disagree</th>
                                                    </tr>
                                                </thead>
                                                
                                                <tbody>
                                                    <?php 
                                                        $question = array(
                                                            'The program started and ended on time.',
                                                            'The information and instructions given throughout the program were clear and easy to follow.',
                                                            'The organization of the program was logical.',
                                                            'The time allotted for the session was sufficient.',
                                                            'Adequate session breaks (mid-morning, lunch, and mid-afternoon.',
                                                            'The program was structured properly.',
                                                            'Socially inclusive, gender-sensitive, and non-discriminatory stereotypical language was always used.',
                                                            'The program was managed efficiently.',
                                                            'The PMT is responsive to the needs of the participants.'
                                                                        );
                                                        $name = 'pmq'; 
                                                        $no = 1;
                                                        $count = 1;
                                                        $c = 0;
                                                        foreach($question as $row){
                                                    ?>
                                                    <tr <?php echo (++$c%2 ? "" : "class='table-active'"); ?>>
                                                        <td><?= $count++; ?>. <?= $row; ?><?php $nn = $name.$no++; ?></td>
                                                        <td class="text-center"><input required  type="radio" name="<?= $nn; ?>" value="4"></td>
                                                        <td class="text-center"><input required  type="radio" name="<?= $nn; ?>" value="3"></td>
                                                        <td class="text-center"><input required  type="radio" name="<?= $nn; ?>" value="2"></td>
                                                        <td class="text-center"><input required  type="radio" name="<?= $nn; ?>" value="1"></td>
                                                    </tr>
                                                    <?php } ?>
                                                    
                                                </tbody>
                                            </table>
                                        </div>
                                        
                                    </div>
                                </div>
                                <!-- end card -->
                            </div>
                            <!-- end col -->

                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                        <h4>II. TRAINING VENUE</h4>                
                                    
                                        </div>  
                                </div>
                                <!-- end card -->
                            </div>
                            <!-- end col -->


                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                    <div class="table-responsive">
                                            <table class="table table-borderless mb-0">
                                                <thead>
                                                    <tr>
                                                        <th></th>
                                                        <th class="text-center"><i class="mdi mdi-emoticon mdi-36px text-info"></i></th>
                                                        <th class="text-center"><i class="mdi mdi-emoticon-happy mdi-36px text-info"></i></th>
                                                        <th class="text-center"><i class="mdi mdi-emoticon-sad mdi-36px text-danger"></i></th>
                                                        <th class="text-center"><i class="mdi mdi-emoticon-angry mdi-36px text-danger"></i></th>
                                                    </tr>
                                                    <tr>
                                                        <th></th>
                                                        <th class="text-center">Strongly Agree</th>
                                                        <th class="text-center">Agree</th>
                                                        <th class="text-center">Disagree</th>
                                                        <th class="text-center">Strong Disagree</th>
                                                    </tr>
                                                </thead>
                                                
                                                <tbody>
                                                    <?php 
                                                        $question = array(
                                                            'The venue is well-lighted and well-ventilated.',
                                                            'The venue has sufficient space for program activities.',
                                                            'The venue has adequate soundproofing.',
                                                            'The venue is clean and has accessible comfort rooms.',
                                                            'The venue has a strong and reliable internet connection working during the program.',
                                                            'Meals were of satisfactory quality and varied.',
                                                            'Meals were nutritious.'

                                                                        );
                                                        $name = 'tvq'; 
                                                        $no = 1;
                                                        $count = 1;
                                                        $c = 0;
                                                        foreach($question as $row){

                                                    ?>
                                                    <tr <?php echo (++$c%2 ? "" : "class='table-active'"); ?>>
                                                        <td><?= $count++; ?>. <?= $row; ?><?php $nn = $name.$no++; ?></td>
                                                        <td class="text-center"><input required  type="radio" name="<?= $nn; ?>" value="4"></td>
                                                        <td class="text-center"><input required  type="radio" name="<?= $nn; ?>" value="3"></td>
                                                        <td class="text-center"><input required  type="radio" name="<?= $nn; ?>" value="2"></td>
                                                        <td class="text-center"><input required  type="radio" name="<?= $nn; ?>" value="1"></td>
                                                    </tr>
                                                    <?php } ?>
                                                    
                                                </tbody>
                                            </table>
                                        </div>
                                        
                                    </div>
                                </div>
                                <!-- end card -->
                            </div>
                            <!-- end col -->

                            

                            <?php if($data->accom == 1){ ?>

                                <div class="col-lg-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <h4>III. ACCOMMODATIONS</h4>                
                                        
                                            </div>  
                                    </div>
                                    <!-- end card -->
                                </div>
                                <!-- end col -->
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                    <div class="table-responsive">
                                            <input type="hidden" value="0" name="accom">
                                            <table class="table table-borderless mb-0">
                                                <thead>
                                                    <tr>
                                                        <th></th>
                                                        <th class="text-center"><i class="mdi mdi-emoticon mdi-36px text-info"></i></th>
                                                        <th class="text-center"><i class="mdi mdi-emoticon-happy mdi-36px text-info"></i></th>
                                                        <th class="text-center"><i class="mdi mdi-emoticon-sad mdi-36px text-danger"></i></th>
                                                        <th class="text-center"><i class="mdi mdi-emoticon-angry mdi-36px text-danger"></i></th>
                                                    </tr>
                                                    <tr>
                                                        <th></th>
                                                        <th class="text-center">Strongly Agree</th>
                                                        <th class="text-center">Agree</th>
                                                        <th class="text-center">Disagree</th>
                                                        <th class="text-center">Strong Disagree</th>
                                                    </tr>
                                                </thead>
                                                
                                                <tbody>
                                                    <?php 
                                                        $question = array(
                                                            'The accommodation has sufficient space. (If applicable)',
                                                            'The accommodation is clean and comfortable. (If applicable)',
                                                            'The facilities are in good working condition.',
                                                            'Toiletries and accommodation necessities were provided to the participants. (If applicable)',

                                                                        );
                                                        $name = 'accomq'; 
                                                        $no = 1;
                                                        $count = 1;
                                                        $c = 0;
                                                        foreach($question as $row){

                                                    ?>
                                                    <tr <?php echo (++$c%2 ? "" : "class='table-active'"); ?>>
                                                        <td><?= $count++; ?>. <?= $row; ?><?php $nn = $name.$no++; ?></td>
                                                        <td class="text-center"><input required  type="radio" name="<?= $nn; ?>" value="4"></td>
                                                        <td class="text-center"><input required  type="radio" name="<?= $nn; ?>" value="3"></td>
                                                        <td class="text-center"><input required  type="radio" name="<?= $nn; ?>" value="2"></td>
                                                        <td class="text-center"><input required  type="radio" name="<?= $nn; ?>" value="1"></td>
                                                    </tr>
                                                    <?php } ?>
                                                    
                                                </tbody>
                                            </table>
                                        </div>
                                        
                                    </div>
                                </div>
                                <!-- end card -->
                            </div>
                            <!-- end col -->

                         <?php }else{?>
                            <input type="hidden" value="1" name="accom">
                            
                            <?php } ?>

                            


                            
                            
                        </div>
                        <!-- row end -->













                        

                        

                        
                            

                            

                        <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                        <h6>What important insights have you learned about the topics<span class="text-danger">*</span></h6>
                                        <input type="text" required name="wcslp"  class="form-control" placeholder="Your answer">                   
                                    
                                        </div>  
                                </div>
                                <!-- end card -->
                            </div>
                            <!-- end col -->

                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                        <h6>How will your learning impact your work as an educator/employee?<span class="text-danger">*</span></h6>
                                        <input type="text" required  name="hwyl" class="form-control" placeholder="Your answer">                   
                                    
                                        </div>  
                                </div>
                                <!-- end card -->
                            </div>
                            <!-- end col -->

                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                        <h6>What could have been better to improve the conduct of the training on the MATATAG Curriculum?<span class="text-danger">*</span></h6>
                                        <input type="text" required name="dyhc"  class="form-control" placeholder="Your answer">  <br /> 
                                        
                                        <input type="hidden" name="act_id" value="<?= $data->id; ?>">
                                        
                                        <div class="form-group mb-0">
                                                <div>
                                                    <button type="submit" class="btn btn-primary waves-effect waves-light mr-1">
                                                        Submit
                                                    </button>
                                                </div>
                                            </div>
                                    
                                    </div>  
                                </div>
                                <!-- end card -->
                            </div>
                            <!-- end col -->

                            


                       


                        </div>
                        <!-- end row -->

                        </form>

                    

                    </div>
                    <!-- end container-fluid -->

                </div>
                <!-- end content -->

                

                <!-- Footer Start -->
                <footer class="footer">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                            2022 - <?= date('Y'); ?> &copy;
                            </div>
                        </div>
                    </div>
                </footer>
                <!-- end Footer -->

            </div>

            <!-- ============================================================== -->
            <!-- End Page content -->
            <!-- ============================================================== -->

        </div>
        <!-- END wrapper -->

        
        

      
        <!-- Vendor js -->
        <script src="<?= base_url(); ?>assets/js/vendor.min.js"></script>

        <!-- Plugin js-->
        <script src="<?= base_url(); ?>assets/libs/parsleyjs/parsley.min.js"></script>

        <!-- Validation init js-->
        <script src="<?= base_url(); ?>assets/js/pages/form-validation.init.js"></script>

        <!-- App js -->
        <script src="<?= base_url(); ?>assets/js/app.min.js"></script>


    </body>

</html>