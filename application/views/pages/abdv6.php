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
            <h4 style="margin:40px 0">List of Applicants</h4>

            <table class="rrr" id='myTable'>
                <tr>
                    <td>NO.</td>
                    <td style="text-align:center; background-color:#fafcb8;"><b>APPLICATION <br />CODE</b></td>
                    <td style="text-align:center; background-color:#fafcb8;"><b>FULLNAME</b></td>
                    <td style="text-align:center; background-color:#fafcb8;"><b>POSITION APPLIED</b></td>
                    <td style="text-align:center; background-color:#fafcb8;"><b>EDUCATION</b></td>
                    <td style="text-align:center; background-color:#fafcb8;"><b>TRAININGS AND<br /> SEMINARS</b></td>
                    <td style="text-align:center; background-color:#fafcb8;"><b>WORK <br />EXPERIENCE</b></td>
                    <td style="text-align:center; background-color:#fafcb8;"><b>LET RATING</b></td>
                    <td style="text-align:center; background:#33b0e0;"><b>DOCUMENT RATER</b></td>
                    <td style="text-align:center; background-color:#fafcb8;"><b>DEMO</b></td>
                    <td style="text-align:center; background:#33b0e0;"><b>DEMO RATER</b></td>
                    <td style="text-align:center; background-color:#fafcb8;"><b>TEACHERS <br />REFLECTION</b></td>
                    <td style="text-align:center; background:#33b0e0;"><b>TR RATER</b></td>
                    <td style="text-align:center; background:#33b0e0;"><b>STATUS</b></td>
                </tr>
                <?php 
                    $c=1;
                    foreach($a as $row){
                    $a = $this->Common->one_cond_row('hris_applicant', 'empEmail', $row->empEmail);
                    $job = $this->Common->one_cond_row('hris_jobvacancy', 'jobID', $row->jobID);
                    $rating = $this->Common->two_cond_row('hris_applications_rating', 'record_no', $a->record_no,'appID',$row->appID);
                    $app = $this->Common->three_cond_count_row('hris_applications','dq',1, 'app_year', date('Y'),'empEmail',$a->empEmail);
                    $apps = $this->Common->three_cond_row('hris_applications','dq',1, 'app_year', date('Y'),'empEmail',$a->empEmail);
                    $r = $this->Common->three_cond_not_equal_count_row('hris_applications_rating','record_no',$a->record_no, 'fy', $row->app_year,'tr_rating','0.00001');
                    $wr = $this->Common->three_cond_not_equal_row('hris_applications_rating','record_no',$a->record_no, 'fy', $row->app_year,'tr_rating','0.00001');
                    if(isset($rating)){
                    $eval1 = $this->Common->one_cond_row('users', 'id', $rating->eval_id1);
                    $eval2 = $this->Common->one_cond_row('users', 'id', $rating->eval_id2);
                    $eval3 = $this->Common->one_cond_row('users', 'id', $rating->eval_id3);
                    }
                ?>
                <tr>
                    <td <?php if($app->num_rows() >= 2){echo "style='background:#e9fd60'";}?>><?= $c++ ?></td>
                    <td <?php if($app->num_rows() >= 2){echo "style='background:#e9fd60'";}?>><a target="_blank" style="text-decoration:none" href="<?= base_url(); ?>pages/ma/<?= $a->id; ?>/<?= $row->jobID; ?>/<?= $row->pre_school; ?>/<?= $row->appID; ?>/<?= $a->record_no; ?>/" class="text-success tooltips" data-placement="top" data-toggle="tooltip" data-original-title="View Applicant Information"><?= strtoupper($a->record_no); ?></a></td>
                    <td <?php if($app->num_rows() >= 2){echo "style='background:#e9fd60'";}?>><?= strtoupper($a->FirstName); ?> <?= mb_substr($a->MiddleName, 0, 1); ?><?php if($a->MiddleName != ""){echo ".";} ?> <?= strtoupper($a->LastName); ?></td>
                    <td <?php if($app->num_rows() >= 2){echo "style='background:#e9fd60'";}?>><?= $job->jobTitle; ?></td>
                    <td style="text-align:center<?php if($app->num_rows() >= 2){echo '; background:#e9fd60';}?>"><?php if($rating->education != 0.00001){echo $rating->education;} ?></td>
                    <td style="text-align:center<?php if($app->num_rows() >= 2){echo '; background:#e9fd60';}?>"><?php if($rating->training != 0.00001){echo $rating->training;} ?></td>
                    <td style="text-align:center<?php if($app->num_rows() >= 2){echo '; background:#e9fd60';}?>"><?php if($rating->experience != 0.00001){echo $rating->experience;} ?></td>
                    <td style="text-align:center<?php if($app->num_rows() >= 2){echo '; background:#e9fd60';}?>"><?php if($rating->let_rating != 0.00001){echo $rating->let_rating;} ?></td>
                    <td style="text-align:center; background:#33b0e0; color:#fff"><?php if(isset($eval1)){echo $eval1->fname.' '.$eval1->lname;} ?></td>
                    <td style="text-align:center<?php if($app->num_rows() >= 2){echo '; background:#e9fd60';}?>"><?php if($rating->demo_rating != 0.00001){echo $rating->demo_rating;} ?></td>
                    <td style="text-align:center; background:#33b0e0; color:#fff"><?php if(isset($eval2)){echo $eval2->fname.' '.$eval2->lname;} ?></td>
                    <td style="text-align:center<?php if($app->num_rows() >= 2){echo '; background:#e9fd60';}?>"><?php if($rating->tr_rating != 0.00001){echo $rating->tr_rating;}else{
                        if($r->num_rows() >= 1){ ?>
                          <a href="<?= base_url(); ?>Pages/tr_special_update/<?= $wr->tr_rating; ?>/<?= $wr->fy; ?>/<?= $wr->record_no; ?>" style="background:#33b0e0;text-decoration:none;padding:3px 5px; color:#222">Update</a>
                       <?php  }} ?></td>
                    <td style="text-align:center; background:#33b0e0; color:#fff"><?php if(isset($eval3)){echo $eval3->fname.' '.$eval3->lname;} ?></td>
                    <td style="text-align:center; <?php if($apps->appStatus == 'Confirmed'){echo 'background:#3c78d8; color:#fff';} ?>"><?= $apps->appStatus; ?></td>
                </tr>
                <?php } ?>
            </table>

         
        
        
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