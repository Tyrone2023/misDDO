
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

        // "Jan 5, 2025 - Jan 9, 2025"; whichever side of the range exists.
        $ier_date_range = function ($from, $to) {
            $fmt = function ($v) {
                if (empty($v) || substr((string) $v, 0, 10) === '0000-00-00') { return ''; }
                $ts = strtotime((string) $v);
                return $ts ? date('M d, Y', $ts) : '';
            };
            $a = $fmt($from); $b = $fmt($to);
            if ($a !== '' && $b !== '') { return $a === $b ? $a : $a . ' - ' . $b; }
            return $a !== '' ? $a : $b;
        };

        // Relevance rows are created lazily on the profile; make sure the
        // table exists before the IER joins it.
        $this->Reg->ensure_record_relevance_table();
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
               
                <th>Details</th>
                <th>Hours</th>
                <th>Details</th>
                <th>Years</th>
                <th>Eligibility</th>
            </tr>
            <?php $c=1; foreach($data as $row){ $ier = $this->Common->two_cond_row('ier_info','jobID',$row->jobID,'code',$row->code);
                // Only the credentials an evaluator marked relevant to this
                // vacancy count on the IER - the relevance table keeps that
                // judgement per job, together with any credited figure the
                // evaluator set over the record's own hours/duration.
                $tr = $this->db->query(
                    "select t.trainingTitle, t.sponsor, t.dateStarted, t.dateFinished,
                            t.noHours, r.credited_hours
                       from hris_trainings t
                       join hris_record_relevance r
                         on r.record_type = 'training' and r.record_id = t.trainingID
                        and r.job_id = ? and r.stat = 1 and r.updated_by is not null
                      where t.IDNumber = ?
                      order by t.dateStarted desc, t.trainingID desc",
                    array($row->jobID, $row->id))->result();
                $xp = $this->db->query(
                    "select e.title, e.position_title, e.date_from, e.date_to, e.ny, e.nm,
                            r.credited_months
                       from hris_experience e
                       join hris_record_relevance r
                         on r.record_type = 'experience' and r.record_id = e.id
                        and r.job_id = ? and r.stat = 1 and r.updated_by is not null
                      where e.id_number = ?
                      order by e.date_from desc, e.id desc",
                    array($row->jobID, $row->id))->result();

                $training_sum = 0;
                foreach ($tr as $trow) {
                    $trow->credit_hours = $trow->credited_hours !== null ? (float) $trow->credited_hours : (float) $trow->noHours;
                    $training_sum += $trow->credit_hours;
                }

                $ex_months = 0;
                foreach ($xp as $xrow) {
                    $xfrom = !empty($xrow->date_from) && $xrow->date_from !== '0000-00-00' ? $xrow->date_from : null;
                    $xto   = !empty($xrow->date_to) && $xrow->date_to !== '0000-00-00' ? $xrow->date_to : null;
                    $ex_months += $xrow->credited_months !== null
                        ? (int) $xrow->credited_months
                        : (($xfrom && $xto) ? $this->Reg->experience_months($xfrom, $xto) : ((int) $xrow->ny * 12) + (int) $xrow->nm);
                }

            ?>
            <tr>
                <td><?= $c++; ?></td>
                <td><?= $row->code; ?></td>

                <td><?= $row->bachelor; ?></td>
                <td style="text-align:left">
                    <?php foreach($tr as $trrow){
                        // Training Title - Conducted By - Inclusive Date - Hours
                        $trParts = array(
                            trim((string) ($trrow->trainingTitle ?? '')),
                            trim((string) ($trrow->sponsor ?? '')),
                            $ier_date_range($trrow->dateStarted ?? null, $trrow->dateFinished ?? null),
                        );
                        $trParts = array_filter($trParts, function ($p) { return $p !== ''; });
                        $trHours = $trrow->credit_hours == (int) $trrow->credit_hours ? (int) $trrow->credit_hours : $trrow->credit_hours;
                        $trParts[] = $trHours . ' hrs';
                    ?>
                              <span class="xp"><?= implode(' - ', array_map(function ($p) { return htmlspecialchars($p, ENT_QUOTES, 'UTF-8'); }, $trParts)); ?></span>
                        <?php } ?>
                </td>
                <td><?= $training_sum == (int) $training_sum ? (int) $training_sum : $training_sum; ?></td>
                <td style="text-align:left; padding:0 !important">
                        <?php foreach($xp as $xprow){
                            // Company / Office - Job Title / Position - Inclusive Date
                            $xpParts = array(
                                trim((string) ($xprow->title ?? '')),
                                trim((string) ($xprow->position_title ?? '')),
                                $ier_date_range($xprow->date_from ?? null, $xprow->date_to ?? null),
                            );
                            $xpParts = array_filter($xpParts, function ($p) { return $p !== ''; });
                        ?>
                              <span class="xp"><?= implode(' - ', array_map(function ($p) { return htmlspecialchars($p, ENT_QUOTES, 'UTF-8'); }, $xpParts)); ?></span>
                        <?php } ?>

                </td>
                <td><?php
                        $totalYears  = intdiv($ex_months, 12);
                        $remainingMo = $ex_months % 12;

                        if ($totalYears === 0 && $remainingMo === 0) {
                            echo '0';
                        } else {
                            echo $totalYears . ' years and ' . $remainingMo . ' months';
                        }
                        ?>
                </td>
                <td><?php
                    $eligRating = trim((string) ($row->csEligibilityRating ?? ''));
                    echo htmlspecialchars((string) ($row->csEligibility ?? ''), ENT_QUOTES, 'UTF-8')
                        . ($eligRating !== '' ? ' - ' . htmlspecialchars($eligRating, ENT_QUOTES, 'UTF-8') : '');
                ?></td>
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