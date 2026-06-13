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
             #exportBtn {display: none;}

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

           
            
           
            }
        </style>
    
    </head>

    <?php 
                                                            $jobTypes = [
                                                                1 => '- Elementary',
                                                                2 => '- Secondary',
                                                                3 => '- Junior High School',
                                                                4 => '- Senior High School',
                                                                5 => '- kindergarten',
                                                                6 => '- IPED Elementary',
                                                                7 => '- IPED Secondary',
                                                                8 => '- IPED Junior High School',
                                                                9 => '- IPED Senior High School',
                                                                10 => '- SNED',
                                                                
                                                            ];

                                                        ?>
<button id="exportBtn" class="btn btn-success mb-3">Export to Excel</button>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
    document.getElementById('exportBtn').addEventListener('click', function () {
        // Get the table
        var table = document.getElementById('myTable');
        var wb = XLSX.utils.table_to_book(table, { sheet: "Applicants" });

        // Export it
        XLSX.writeFile(wb, 'Multiple_Applicants.xlsx');
    });
</script>


    <body class="aip_generate" id="printTable">


        <div class="cert">
            <img class="logo" src="<?= base_url(); ?>assets/images/report/ke.png" alt="">
            <p style="margin-bottom:0;">
            <span class="rp">Republic of the Philippines</span><br />
                <span class="de">Department of Education</span><br />
                <span class="r">Region XI</span><br />
                <span class="r">Schools Division of <?php echo $settings->division; ?></span>
            </p>

            <div class="hr" style="margin:10px 0"></div>
            <h4 style="margin:40px 0">List of Applicant Multiple Application</h4>

            <table class="rrr" id='myTable'>
                <tr>
                    <th style="text-align:center; background-color:#fafcb8;">NO.</th>
                    <th style="text-align:center; background-color:#fafcb8;">FULLNAME</th>
                    <th style="text-align:center; background-color:#fafcb8;">APPLICANT CODE</th>
                    <th style="text-align:center; background-color:#fafcb8;">ADDRESS</th>
                    <th style="text-align:center; background-color:#fafcb8;">JOB TITLE</th>
                    <th style="text-align:center; background-color:#fafcb8;">EDUCATION</th>
                    <th style="text-align:center; background-color:#fafcb8;">TRAINING</th>
                    <th style="text-align:center; background-color:#fafcb8;">EXPERIENCE</th>
                    <th style="text-align:center; background-color:#fafcb8;">LET RATING</th>
                    <th style="text-align:center; background-color:#fafcb8;">RATER</th>
                    <th style="text-align:center; background-color:#fafcb8;">DEMO RATING</th>
                    <th style="text-align:center; background-color:#fafcb8;">RATER</th>
                    <th style="text-align:center; background-color:#fafcb8;">TR RATING</th>
                    <th style="text-align:center; background-color:#fafcb8;">RATER</th>
                    <th style="text-align:center; background-color:#fafcb8;">STATUS</th>
                </tr>
                <?php 
                    $ic=1; foreach($data as $row){ 
                        $b = $this->Common->one_cond_row_select('hris_applicant','empEmail','empEmail',$row->empEmail);
                        if(!empty($b)){
                            $a = $this->Common->one_cond_row_select('hris_applicant','id,empEmail,record_no, FirstName, MiddleName, LastName, NameExtn,perBarangay,resCity,resBarangay','empEmail',$row->empEmail);
                            $id = $a->id;
                            $record = $a->record_no;
                            $page = 'ma';
                            $brgy = strtoupper($a->resBarangay);
                        }else{
                            $a = $this->Common->one_cond_row_select('hris_staff','empEmail,IDNumber, FirstName, MiddleName, LastName, NameExtn,perBarangay,resCity,resBarangay','IDNumber',$row->empEmail);
                            $id = $a->IDNumber;
                            $record = $a->IDNumber;
                            $page = 'ma_staff';
                            $brgy = strtoupper($a->perBarangay);
                        }
                        $job = $this->Common->one_cond_row('hris_jobvacancy','jobID',$row->jobID);
                        $rating = $this->Common->one_cond_row('hris_applications_rating','appID',$row->appID);

                        if(isset($rating->eval_id1)){$eval1 = $this->Common->one_cond_row_select('users','id,fname','id',$rating->eval_id1);}
                        if(isset($rating->eval_id2)){$eval2 = $this->Common->one_cond_row_select('users','id,fname','id',$rating->eval_id2);}
                        if(isset($rating->eval_id3)){$eval3 = $this->Common->one_cond_row_select('users','id,fname','id',$rating->eval_id3);}

                    //$multi = $this->Common->two_cond_count_row('hris_applications','empEmail',$row->empEmail,'app_year',date('Y'),'appStatus','Endorsed for Rating');
                    $multi = $this->Common->two_join_two_cond_two_ne_cond_count('hris_applications','hris_jobvacancy', 'a.jobID, b.jobID, b.jvStatus, appStatus', 'a.jobID=b.jobID', 'a.empEmail',$row->empEmail, 'b.jvStatus', 'Open','appStatus','Application Submitted','appStatus','Validated');
                    //if($multi->num_rows() >= 2){
                    if($job->jvStatus == 'Open' || $job->position == 1){
                ?>
                <tr>
                    <td><?= $ic++; ?></td>
                    <td <?php if($multi->num_rows() >= 2){echo 'style="background:#faf83e"';} ?>><a style="text-decoration:none; color:#222;" href="<?= base_url(); ?>Pages/<?= $page; ?>/<?= $id; ?>/<?= $row->jobID; ?>/<?= $row->pre_school; ?>" target="_blank"><?= strtoupper($a->FirstName.' '.$a->MiddleName.' '.$a->LastName); ?></a></td>
                    <td><?= $record; ?></td>
                    <td <?php if (stripos($a->resCity, 'mati') !== false || stripos($a->resCity, 'maragusan') !== false) {echo 'style="background:#A0C878"';}?>><?php if($brgy != ""){echo $brgy.', ';}?><?= strtoupper($a->resCity); ?></td>
                    <td <?php if($multi->num_rows() >= 2){echo 'style="background:#faf83e"';} ?>>
                        <?= $job->jobTitle; ?>
                        <?php 
                            $jobTypes = [
                                1 => '- Elementary',
                                2 => '- Secondary',
                                3 => '- Junior High School',
                                4 => '- Senior High School',
                                5 => '- kindergarten',
                                6 => '- IPED Elementary',
                                7 => '- IPED Secondary',
                                8 => '- IPED Junior High School',
                                9 => '- IPED Senior High School',
                                10 => '- SNED',
                                                         
                            ];

                            echo $jobTypes[$job->job_type] ?? '';
                            ?>
                    </td>
                    <td <?php if($multi->num_rows() >= 2){echo 'style="background:#faf83e"';} ?>><?php if(!empty($rating)){if($rating->education != 0.00001){echo $rating->education;}} ?></td>
                    <td <?php if($multi->num_rows() >= 2){echo 'style="background:#faf83e"';} ?>><?php if(!empty($rating)){if($rating->training != 0.00001){echo $rating->training;}} ?></td>
                    <td <?php if($multi->num_rows() >= 2){echo 'style="background:#faf83e"';} ?>><?php if(!empty($rating)){if($rating->experience != 0.00001){echo $rating->experience;}} ?></td>
                    <td <?php if($multi->num_rows() >= 2){echo 'style="background:#faf83e"';} ?>><?php if(!empty($rating)){if($rating->let_rating != 0.00001){echo $rating->let_rating;}} ?></td>
                    <td>
                        <?php
                        if (!empty($rating) && $rating->eval_id1 != 0 && !empty($eval1)) {
                            echo $eval1->fname;
                        }
                        ?>
                    </td>
                    <td <?php if($multi->num_rows() >= 2){echo 'style="background:#faf83e"';} ?>><?php if(!empty($rating)){if($rating->demo_rating != 0.00001){echo $rating->demo_rating;}} ?></td>
                    <td><?php //if(!empty($rating)){if($rating->eval_id2 != 0){echo $eval2->fname;}} ?>
                        <?php
                        if (!empty($rating) && $rating->eval_id2 != 0 && !empty($eval2)) {
                            echo $eval2->fname;
                        }
                        ?>
                    </td>
                    <td <?php if($multi->num_rows() >= 2){echo 'style="background:#faf83e"';} ?>><?php if(!empty($rating)){if($rating->tr_rating != 0.00001){echo $rating->tr_rating;}} ?></td>
                    <td>
                        <?php
                        if (!empty($rating) && $rating->eval_id3 != 0 && !empty($eval3)) {
                            echo $eval3->fname;
                        }
                        ?>
                    </td>
                    <td <?php if($row->appStatus == 'Rated'){echo 'style="background:#716cb0"';}elseif($row->appStatus == 'Confirmed'){echo 'style="background:#33b0e0"';}?>><?= $row->appStatus; ?></td>
                </tr>
                <?php } }?>
                
            </table>

         
        
        
        </div>

       


        
    </body>
</html>