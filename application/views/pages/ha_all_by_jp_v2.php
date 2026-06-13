
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Responsive bootstrap 4 admin template" name="description" />
    <meta content="Coderthemes" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="<?= base_url(); ?>assets/images/hris.ico">
    <link href="<?= base_url(); ?>assets/css/renren_style.css" rel="stylesheet" type="text/css" />
    <link href="https://db.onlinewebfonts.com/a/0nH393RJctHgt1f2YvZvyruY" rel="stylesheet" type="text/css"/>
    <title><?= $title; ?></title>
    <style>
        @media print {
            #btnExport {
                display: none !important;
            }
        }

        .xp{
            display:block; 
            border-bottom:1px dotted #222; 
            padding:5px 10px;
        }
        .xp:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body>
<?php 
        $jobTypes = [
            1 => '- Elementary',
            2 => '- Secondary',
            3 => '- Junior High School',
            4 => '- Senior High School'
        ];
    ?>
    <div class="hwrap">
    <img class="logo" src="<?= base_url(); ?>assets/images/ke.png" alt="">
        <p class="textwrap">
        <span class="rp">Republic of the Philippines</span>
            <span class="de">Department of Education</span>
            <span class="r">Region XI</span>
           <span class="r">Schools Division of <?php echo $mis_settings[0]->division; ?></span>
        </p>
        <iframe id="txtArea1" style="display:none"></iframe>
        <button id="btnExport" onclick="fnExcelReport();"> EXPORT TO EXCEL</button>
</div>
<div class="blocker"></div>
  <div class="wrap">
    <div class="inner">
        <h5 style="font-style:italic;">Annex D</h5>
        <h1 style="font-size:20px"><?= $title; ?></h1>

        

        <table class="data" id="myTable" style="border:0">
            <tr>
                <td style="text-align:left; border:0">Position:</td>
                <td style="border:0; border-bottom:1px solid #222" colspan="4"></td>
            </tr>
            <tr>
                <td style="text-align:left; border:0; font-size:10px" colspan="2">Salary Grade and Monthly Salary:</td>
                <td style="border:0; border-bottom:1px solid #222" colspan="3"></td>
            </tr>
            <tr>
                <td style="text-align:left; border:0" colspan="2">Qualification Standards:</td>
                <td style="border:0; border-bottom:1px solid #222" colspan="3"></td>
            </tr>
            <tr>
                <td style="border:0"></td>
                <td style="text-align:left; border:0">Education</td>
                <td style="border:0; border-bottom:1px solid #222" colspan="3"></td>
                <td colspan="15" style="border:0"></td>
            </tr>
            <tr>
                <td style="border:0"></td>
                <td style="text-align:left; border:0">Training</td>
                <td style="border:0; border-bottom:1px solid #222" colspan="3"></td>
                <td colspan="15" style="border:0"></td>
            </tr>
            <tr>
                <td style="border:0"></td>
                <td style="text-align:left; border:0">Experience</td>
                <td style="border:0; border-bottom:1px solid #222" colspan="3"></td>
                <td colspan="15" style="border:0"></td>
            </tr>
            <tr>
                <td style="border:0"></td>
                <td style="text-align:left; border:0">Eligibility</td>
                <td style="border:0; border-bottom:1px solid #222" colspan="3"></td>
                <td colspan="15" style="border:0"></td>
            </tr>
            <tr>
                <td colspan="19" style="border:0"></td>
            </tr>
            <tr>
                <th rowspan="2">No.</th>
                <th rowspan="2">Application Code</th>
                <th rowspan="2">Education</th>
                <th colspan="2">Training</th>
                <th colspan="3">Experience</th>
                <th rowspan="2">Remarks <br />(Qualified or <br />Disqualified)</th>
            </tr>
            <tr>
               
                <th>Title</th>
                <th>Hours</th>
                <th>Details</th>
                <th>Years</th>
                <th>Eligibility</th>
            </tr>
            <?php $c=1; foreach($data as $row){ $ier = $this->Common->two_cond_row('ier_info','jobID',$row->jobID,'code',$row->code); 
                $xp = $this->Common->two_cond('hris_experience','id_number',$row->id,'stat',1); 
                $tr = $this->Common->two_cond('hris_trainings','IDNumber',$row->id,'stat',1); 

                $training_sum = $this->Reg->gettotaltraining_staff('hris_trainings','noHours',$row->id);
                $ex_year_sum = $this->Reg->gettotaltraining('hris_experience','ny',$row->id);
                $ex_month_sum = $this->Reg->gettotaltraining('hris_experience','nm',$row->id);

            ?>
            <tr>
                <td><?= $c++; ?></td>
                <td><?= $row->code; ?></td>
                
                <td><?= $row->bachelor; ?></td>
                <td>
                    <?php foreach($tr as $trrow){ ?>
                              <span class="xp"><?= $trrow->trainingTitle ?? ''; ?></span>
                        <?php } ?>
                </td>
                <td><?= $training_sum ?? 0; ?></td>
                <td style="text-align:left; padding:0 !important">
                        <?php foreach($xp as $xprow){ ?>
                              <span class="xp"><?= $xprow->title ?? ''; ?></span>
                        <?php } ?>
                    
                </td>
                <td><?php
                        $ex_year_sum  = (int) ($ex_year_sum  ?? 0);
                        $ex_month_sum = (int) ($ex_month_sum ?? 0);

                        $totalYears  = $ex_year_sum + intdiv($ex_month_sum, 12);
                        $remainingMo = $ex_month_sum % 12;

                        if ($totalYears === 0 && $remainingMo === 0) {
                            echo '0';
                        } else {
                            echo $totalYears . ' years and ' . $remainingMo . ' months';
                        }
                        ?>
                </td>
                <td><?= $row->csEligibility ?? '' ?></td>
                <td><?=  $row->dq == 1 ? "Qualified" : ($row->dq == 2 ? "Disqualified" : ""); ?></td>
            </tr>
            <?php } ?>
        </table>

        

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
        
       

    
</body>
</html>