<?php if($this->session->logged_in == false){
redirect(base_url().'log_in');
if($session->position == 'asds'){redirect(base_url());}
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
        
        <style>


            .aip_generate  .rrr{
                margin-bottom:20px;
                font-family:"Calibri", sans-serif;
            }
            .aip_generate  .rrr th{
                background-color:#fff;
                color:#000;
            }
            .aip_generate  .rrr td{
                text-align:left;
            }
            .rsign{
                float:left;
                margin-right:30px;
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

            .ivan {
                background-color:purple;
                color:#fff;
                padding:2px 5px;
                border-radius: 5px;
            }


            /* Style the modal */
.modal {
    display: none;  /* Hidden by default */
    position: fixed; /* Stay in place */
    z-index: 1; /* Sit on top */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    background-color: rgba(0, 0, 0, 0.4); /* Black with transparency */
}

/* Modal Content */
.modal-content {
    background-color: #fff;
    margin: 15% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    max-width: 500px;
    text-align: center;
}

/* The Close Button */
.close {
    color: #aaa;
    font-size: 28px;
    font-weight: bold;
    position: absolute;
    top: 10px;
    right: 20px;
    cursor: pointer;
}

.close:hover,
.close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}


            .text-center{text-align:center !important}
            @page {
            size: A4;
            margin: 50px 0;
            }
            @media print {
            html, body {
                width: 210mm;
                height: 297mm;
                font-size:14px !important;
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
            #btnExport{
                visibility: hidden;
            }
            
           
            }
        </style>
    
    </head>

    <iframe id="txtArea1" style="display:none"></iframe>
    <button id="btnExport" onclick="fnExcelReport();"> EXPORT TO EXCEL</button>



    <body class="aip_generate" id="printTable">

        <div class="cert">
            <img class="logo" src="<?= base_url(); ?>assets/images/report/ke.png" alt="">
            <p style="margin-bottom:0;">
            <span class="rp">Republic of the Philippines</span><br />
                <span class="de">Department of Education</span><br />
                <span class="r">Region XI</span><br />
                <span class="r">Schools Division of Davao Oriental</span>
            </p>

            <div class="hr" style="margin:10px 0"></div>
            <h4 style="margin:40px 0">List of Qualified Applicants</h4>
            <?php 
                $jobTypes = [
                    1 => '- Elementary',
                        2 => '- Secondary',
                        3 => '- Junior High School',
                        4 => '- Senior High School',
                        5 => '- Kindergarten',
                        6 => '- IPED Elementary',
                        7 => '- IPED Secondary',
                        8 => '- IPED Junior High School',
                        9 => '- IPED Senior High School',
                        10 => '- SNED',
                        11 => '- SHS Academic and Core Subjects',
                        12 => '- SHS Arts and Design Track',
                        13 => '- SHS Sports Track',
                        14 => '- SHS Technical-Vocational(TVL) Track',
                                                         
                    ];    
            ?>

            <table class="rrr" id='myTable'>
                <?php
                    $c=1;
                     
                    foreach($job as $jrow){
                        $application = $this->Common->two_cond_row_select('hris_applications','jobID,dq', 'jobID', $jrow->jobID,'dq',1);
                        

                        if(!empty($application)){
                ?>
                <tr>
                    <td style="text-align:center; background-color:#fafcb8;" colspan="17"><b><?= $jrow->jobTitle; ?> <?= $jobTypes[$jrow->job_type] ?? '';?> - <?= $jrow->assign; ?></b></td>
                </tr>
                <tr style="background-color:#BFECFF;">
                    <td class="text-center">NO.</td>
                    <td class="text-center">Application No.</td>
                    <td class="text-center">Fullname</td>
                    <td class="text-center">Address</td>
                    <td class="text-center">Education</td>
                    <td class="text-center">Trainings And Seminars</td>
                    <td class="text-center">Work Experience</td>
                    <td class="text-center">Performance Rating</td>
                    <td class="text-center">COIs</td>
                    <td class="text-center">NCOIs</td>
                    <td class="text-center">Rater</td>
                    <td class="text-center">Status</td>
                </tr>
                <?php 
                //$applications = $this->Common->two_cond('hris_applications', 'jobID', $jrow->jobID,'dq',1);
                $applications = $this->Common->promotion_list($jrow->jobID);
                $count=1;
                foreach($applications as $row){ 
                    $query = $this->Common->three_cond_row_select('hris_application_inquiry','job_id,applicant_id,stat', 'job_id',$jrow->jobID,'applicant_id',$row->empEmail,'stat',0);
                   
                    $ivy = $this->Common->one_cond_row_select('users','id,lname', 'id',$row->eval_id1);
                ?>
                <tr>
                    <td><?= $count++; ?></td>
                    <td><?= $row->code; ?></td>
                    <?php if($row->eval_id1 == $this->session->id || $this->session->position == 'asds' ||  $row->eval_id1 == 0 || $this->session->id == 32993 || $this->session->id == 32994 || $this->session->id == 32996 || $this->session->id == 32996 || $this->session->id == 32532 || $this->session->id == 32534 || $this->session->id == 27774){?>
                    <td>
                        <a style="text-decoration:none; color:#222; white-space: nowrap;" target="_blank" href="<?= base_url(); ?>pages/<?= $row->st; ?>/<?= $row->id; ?>/<?= $row->jobID; ?>/<?= $row->pre_school; ?>/<?= $row->appID; ?>/<?= $row->code; ?>/" class="text-success tooltips" data-placement="top" data-toggle="tooltip" data-original-title="View Applicant Information"><?= $row->LastName.', '. $row->MiddleName.' '.$row->FirstName; ?></a> 
                        
                        <a style="color:red" onclick="return confirm('Are You Sure?')" href="<?= base_url(); ?>Pages/application_delete/<?= $row->appID; ?>">&#128465;</a> 
                        <?php if(!empty($query)){ ?>
                        <a style="color:blue" target="_blank" href="<?= base_url(); ?>Pages/inquiry_non/<?= $row->empEmail; ?>/<?= $row->jobID; ?>/<?= $row->pre_school; ?>/<?= $row->appID; ?>">&#128172;</a> 
                        <?php } ?>
                    </td>
                    <?php }else{ ?>
                        <td><?= $row->LastName.', '. $row->MiddleName.' '.$row->FirstName; ?></td>
                    <?php } ?>

                    <td><?= $row->brgy; ?> <?= $row->resCity; ?></td>
                    <td class="text-center">
                        <?php
                        $own = ($row->eval_id1 == $this->session->id) || ($this->session->position == 'asds');
                        $e   = $row->educ ?? null;
                        echo $own ? ($e == 0.00001 ? '' : $e) : ($e != 0.00001 ? '<span class="ivan">Rated</span>' : '');
                        ?>
                    </td>
                    <td class="text-center">
                        <?php
                        $own = ($row->eval_id1 == $this->session->id) || ($this->session->position == 'asds');
                        $e   = $row->trainings ?? null;
                        echo $own ? ($e == 0.00001 ? '' : $e) : ($e != 0.00001 ? '<span class="ivan">Rated</span>' : '');
                        ?>
                    </td>
                    <td class="text-center">
                        <?php
                        $own = ($row->eval_id1 == $this->session->id) || ($this->session->position == 'asds');
                        $e   = $row->experience ?? null;
                        echo $own ? ($e == 0.00001 ? '' : $e) : ($e != 0.00001 ? '<span class="ivan">Rated</span>' : '');
                        ?>
                    </td>
                    <td class="text-center">
                        <?php
                        $own = ($row->eval_id1 == $this->session->id) || ($this->session->position == 'asds');
                        $e   = $row->performance ?? null;
                        echo $own ? ($e == 0.00001 ? '' : $e) : ($e != 0.00001 ? '<span class="ivan">Rated</span>' : '');
                        ?>
                    </td>
                    <td class="text-center">
                        <?php
                        $own = ($row->eval_id1 == $this->session->id) || ($this->session->position == 'asds');
                        $e   = $row->ppstco ?? null;
                        echo $own ? ($e == 0.00001 ? '' : $e) : ($e != 0.00001 ? '<span class="ivan">Rated</span>' : '');
                        ?>
                    </td>
                    <td class="text-center">
                        <?php
                        $own = ($row->eval_id1 == $this->session->id) || ($this->session->position == 'asds');
                        $e   = $row->ppstpa ?? null;
                        echo $own ? ($e == 0.00001 ? '' : $e) : ($e != 0.00001 ? '<span class="ivan">Rated</span>' : '');
                        ?>
                    </td>
                    <td style="background-color:#BFECFF;"><?= $row->eval_id1 ? $ivy->lname : '' ?></td>
                    <td style="text-align:center"><span style="background-color:<?php if($row->appStatus == 'Rated'){echo "#08C2FF";}elseif($row->appStatus == 'Confirmed'){echo "#ef37ff";}else{echo "#FCF596";}?>; padding:3px 10px; ">
                        <?= $row->appStatus;?></span>
                    </td>
                </tr>
                <?php } ?>
               
                
                <?php  }} ?>
            </table>

         
        
        
        </div>

            <!-- The Modal -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close" id="closeModalBtn">&times;</span>
            <h2>Modal Heading</h2>
            <p>This is a simple modal window. You can put any content here!</p>
        </div>
    </div>


      


        
    </body>
</html>


<script>
    function fnExcelReport() {
    var tab_text = "<table border='2px'><tr bgcolor='#87AFC6'>";
    var j = 0;
    var tab = document.getElementById('myTable'); // id of table

    for (j = 0; j < tab.rows.length; j++) {
        tab_text = tab_text + tab.rows[j].innerHTML + "</tr>";
        //tab_text=tab_text+"</tr>";
    }

    tab_text = tab_text + "</table>";
    tab_text = tab_text.replace(/<A[^>]*>|<\/A>/g, "");//remove if u want links in your table
    tab_text = tab_text.replace(/<img[^>]*>/gi, ""); // remove if u want images in your table
    tab_text = tab_text.replace(/<input[^>]*>|<\/input>/gi, ""); // reomves input params

    var msie = window.navigator.userAgent.indexOf("MSIE ");

    // If Internet Explorer
    if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./)) {
        txtArea1.document.open("txt/html", "replace");
        txtArea1.document.write(tab_text);
        txtArea1.document.close();
        txtArea1.focus();

        sa = txtArea1.document.execCommand("SaveAs", true, "Say Thanks to Sumit.xls");
    } else {
        // other browser not tested on IE 11
        sa = window.open('data:application/vnd.ms-excel,' + encodeURIComponent(tab_text));
    }

    return sa;
}




</script>