<?php if($this->session->logged_in == false){
redirect(base_url().'log_in');
} ?>
<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="Responsive bootstrap 4 admin template" name="description" />
        <meta content="Coderthemes" name="author" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <!-- App favicon -->
        <link rel="shortcut icon" href="<?= base_url(); ?>assets/images/hris.ico">

        <!-- Plugins css-->
        <link href="<?= base_url(); ?>assets/css/renren.css" rel="stylesheet" type="text/css" />
        <link href="https://db.onlinewebfonts.com/a/0nH393RJctHgt1f2YvZvyruY" rel="stylesheet" type="text/css"/>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <style>
            .aip_generate  .rrr{
                margin-bottom:20px;
                font-family:arial;
            }
            .aip_generate  p{
                font-family:arial;
            }
            .aip_generate  .rrr td{
                text-align:left;
            }
            .rsign{
                float:left;
                margin-right:20px;
                margin-top:30px;
                position:relative;
            }
            .rsign span{
                display:block;
                line-height:1em;
            }
            .rsign .chona{
                position:absolute;
                left:20px;
                top:30px;
            }
            .rsign .feb{
                position:absolute;
                top:40px;
                width:50px;
            }
            select{
                border:0;
                background-color:#fff;
            }
            input[type=submit]{
                background-color:#0facfe;
                border:1px solid #0facfe;
                cursor:pointer;
                padding:5px 20px;
            }
            .blankdistrict {
                background-color:#3a86ff;
                color:#fff;
                text-decoration:none;
                padding:5px 10px;
            }
            @page {
            size: A4;
            margin: 0;
            }
            @media print {
            html, body {
                width: 210mm;
                height: 297mm;
                font-size:10px !important;
                line-height:1em;
            }
           

            .aip_generate .hr{
                margin:20px 0;
                width:100%;
            }

            .cert{
                width:90%;
                padding-top:1px;
            }
            .aip_generate .dra_aip th, 
            .aip_generate .dra_aip td {
                padding:10px 0;
            }

            .footer {
                position: fixed;
                margin: 0 auto; /* Only centers horizontally not vertically! */
                bottom: 0;
                width:86%;
            }

            .aip_generate  .rrr{
                margin-bottom:0;
            }
            .aip_generate  .rrr td{
                text-align:left;
            }
            .rsign{
                float:left;
                margin-right:40px;
                margin-top:30px;
                position:relative;
            }
            .rsign span{
                display:block;
                line-height:1em;
                font-size:12px;
            }
            .rsign .chona{
                position:absolute;
                left:20px;
                top:30px;
            }
            .rsign .feb{
                position:absolute;
                top:40px;
                width:50px;
            }

            .aip_generate .cert p{
                margin-bottom:20px;
            }
            .blankdistrict{display:none}
            
           
            }
        </style>
    
    </head>



    <body class="aip_generate" id="printTable">
            <?php 
                $dist = $this->Common->no_cond('district');
                $school = $this->Common->no_cond('schools');  
                $cschool = $this->Common->one_cond_row('schools','schoolID',$application->pre_school);  
            
            ?>

        <div class="cert">
            <img class="logo" src="<?= base_url(); ?>assets/images/report/ke.png" alt="">
            <p style="margin-bottom:0;">
            <span class="rp">Republic of the Philippines</span><br />
                <span class="de">Department of Education</span><br />
                <span class="r">Region XI</span><br />
                <span class="r">Schools Division of Davao Oriental</span>
            </p>

            <div class="hr" style="margin:10px 0"></div>

            <h4 style="margin:10px 0"><?= $title; ?></h2>
            <p>(Note: Only examination/ reflections/ interviews taken within the last 12 months can be retained)</p>

            <table class="rrr">
                <?php 
                    $m = mb_substr($applicant->MiddleName, 0, 1);
                    if(isset($m)){$mn = $m.'.';}else{$mn = '';}
                    $fullname = $applicant->FirstName.' '.$mn.' '.$applicant->LastName;
                    $array = array('Name'=> $fullname,'contact Number'=>$applicant->contactNo,'Position Applied'=>$job->jobTitle);
                    foreach($array as $row => $key){
                ?>
                <tr>
                    <td><?= $row; ?></td>
                    <td><b><?= $key; ?></b></td>
                </tr>
                <?php } ?>
            </table>

            <?php if($application->district != ""){?>
            <table class="rrr">
                <tr>
                    <td colspan="2">**for Teacher Applicants 
                        <a onclick="return confirm('Are you sure?')" class="blankdistrict" href="<?= base_url(); ?>Pages/update_app_dis_blank/<?= $this->uri->segment(3); ?>/<?= $this->uri->segment(4); ?>/<?= $this->uri->segment(5); ?>">Edit District</a> 
                    </td>
                </tr>
                <?php 
                    $array = array('School Applied'=>$cschool->schoolName,'District Applied'=>$application->district);
                    foreach($array as $row => $key){
                ?>
                <tr>
                    <td><?= $row; ?></td>
                    <td><b><?= strtoupper($key); ?></b></td>
                </tr>
                <?php } ?>
            </table>
            <?php }else{ ?> 
                <?php 
                                                        $attributes = array('class' => 'parsley-examples');
                                                        echo form_open('pages/update_app');
                                                    ?>
                                                    <input type="hidden" value="<?= $this->uri->segment(3); ?>" name="applicant_id">
                                                    <input type="hidden" value="<?= $this->uri->segment(4); ?>" name="jobID">
                                                    <input type="hidden" value="<?= $this->uri->segment(5); ?>" name="appID">
                <table class="rrr">
                    <tr>
                        <td colspan="2">**for Teacher Applicants</td>
                    </tr>
                    <tr>
                        <td>District Applied</td>
                        <td>
                        <div class="form-group row">
                                                            <div class="col-md-9">
                                                                <select class="form-control" name="district" id="dist" required>
                                                                <option value="">Please Select Your District</option>
                                                                <?php foreach($dist as $row){?>
                                                                <option value="<?= $row->discription; ?>"><?= strtoupper($row->discription); ?></option>
                                                                <?php } ?>

                                                                </select>
                                                            </div>
                                                        </div> 
                        </td>
                    </tr>
                    <tr>
                        <td>School Applied</td>
                        <td>
                        <div class="form-group row">
                                                            <div class="col-md-9">
                                                                <select class="form-control" name="school" id="school" required>
                                                                    <option value="">Please Select School</option>
                                                                    <?php foreach($school as $row){?>
                                                                        <option data-dist="<?= $row->district; ?>" value="<?= $row->schoolID; ?>"><?= strtoupper($row->schoolName); ?></option>
                                                                    <?php } ?>

                                                                </select>
                                                                <input type="submit" name="submit" value="Submit" class="btn btn-info waves-effect waves-light">
                                                            </div>
                                                        </div>
                                                        
                        </td>
                    </tr>
                    
                </table>
                    </form>
                
            
            <?php } ?>

            <p style="margin:10px 0">This is to request retention of my rating in:</p>
            <table class="rrr">
                <tr>
                    <td colspan="3"><b>For <?= $job->jobTitle; ?></b></td>
                </tr>
                <tr>
                    <td style="text-align:center"><b>CRITERIA</b></td>
                    <td style="text-align:center"><b>MONTH TAKEN</b></td>
                    <td style="text-align:center"><b>YEAR TAKEN</b></td>
                </tr>
                <tr>
                    <td>Teacher Reflection Form</td>
                    <td style="text-align:center"><b><?php // date('F', strtotime($job->datePosted)); ?>June</b></td>
                    <td style="text-align:center"><b><?= $job->sy; ?></b></td>
                </tr>
                <tr>
                    <td>Demonstration Teaching</td>
                    <td style="text-align:center"><b><?php // date('F', strtotime($job->datePosted)); ?>June</b></td>
                    <td style="text-align:center"><b><?= $job->sy; ?></b></td>
                </tr>
            </table>

            <table class="rrr">
                <tr>
                    <td colspan="3">For other position</td>
                </tr>
                <tr>
                    <td>CRITERIA</td>
                    <td>MONTH TAKEN</td>
                    <td>YEAR TAKEN</td>
                </tr>
                <tr>
                    <td>Written Examination</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>interview</td>
                    <td></td>
                    <td></td>
                </tr>
            </table>

            <div class="hr" style="margin:20px 0"></div>
            
            <p style="margin:10px 0">(This portion will be filled up by the PSB Secretariat)</p>
            <table class="rrr">
                <tr>
                    <td>CRITERIA</td>
                    <td style="text-align:center"><b>Rating</b></td>
                    <td style="text-align:center"><b>Remarks</b></td>
                </tr>
                <tr>
                    <td>Teacher Reflection Form</td>
                    <td style="text-align:center"><b><?php if($rating->tr_rating != 0.00001){echo $rating->tr_rating;} ?></b></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Demonstration Teaching</td>
                    <td style="text-align:center"><b><?php  if($rating->demo_rating != 0.00001){echo $rating->demo_rating;} ?></b></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Written Examination</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>interview</td>
                    <td></td>
                    <td></td>
                </tr>
            </table>

            <div class="blocker"></div>

            <p class="rsign">Requested By:<br /><br /><br /><br />
                <span><?= $applicant->FirstName.' '.$applicant->MiddleName.' '.$applicant->LastName; ?> (SGD)<br/>
                Applicant Fullname
                </span>
            </p>


            <p class="rsign">Recommending Approval: <br /><br /><br /><br />
                <img class="chona"  src="<?= base_url(); ?>assets/images/chona.png" alt="">
                <span>CHONA L. ROJAS<br/>
                Administrative Officer IV-HRM
                </span>
            </p>

            <p class="rsign">Approved: <br /><br /><br /><br />
                <img class="feb" src="<?= base_url(); ?>assets/images/feb.png" alt="">
                <span>PHOEBE GAY L. REFAMONTE<br/>
                Assistant Schools Division Superintendent
                </span>
            </p>


           
           
            <div class="footer">

                <div class="hr" style="margin:5px 0"></div>


                <div class="cafooter">
                    <img width="100%" src="<?= base_url(); ?>assets/images/f.png" alt="">
                    <div class="blocker"></div>
                </div>

            </div>
        
        
        
        
        </div>

        


                                    <script>
                                        $(document).ready(function() {
                                            $("#school option").hide();

                                            $("#dist").change(function() {
                                                var val = $(this).val();
                                                $("#school option").hide();
                                                $("#school").val("");
                                                $("#school [data-dist='" + val + "']").show(); //show options where attribute value matches.
                                                $("#school").change();
                                            });


                                            });
                                    </script>

                                  




    </body>
</html>