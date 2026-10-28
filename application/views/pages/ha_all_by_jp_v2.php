
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
            #btnExport, .ier-filter {
                display: none !important;
            }
        }

        .ier-filter {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin: -6px 0 18px;
        }
        .ier-filter a {
            display: inline-block;
            padding: 7px 13px;
            border: 1px solid #697785;
            border-radius: 4px;
            color: #263746;
            background: #fff;
            font-weight: 600;
            text-decoration: none;
        }
        .ier-filter a.is-active {
            border-color: #245b88;
            color: #fff;
            background: #245b88;
        }
        .ier-record-state {
            display: block;
            padding: 8px 10px;
            color: #555;
            font-style: italic;
            text-align: center;
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

        $ier_duration = function ($months) {
            $months = max(0, (int) $months);
            $years = intdiv($months, 12);
            $remaining = $months % 12;
            $parts = array();

            if ($years > 0) {
                $parts[] = $years . ' year' . ($years === 1 ? '' : 's');
            }
            if ($remaining > 0) {
                $parts[] = $remaining . ' month' . ($remaining === 1 ? '' : 's');
            }

            return empty($parts) ? '0 months' : implode(' and ', $parts);
        };

        $display_filter = isset($display_filter) ? $display_filter : 'all';
        $applicant_counts = isset($applicant_counts) ? $applicant_counts : array(
            'all' => count((array) $data),
            'qualified' => 0,
            'disqualified' => 0,
        );
        $ier_filter_url = function ($filter) {
            $query = $this->input->get();
            $query = is_array($query) ? $query : array();

            if ($filter === 'all') {
                unset($query['display']);
            } else {
                $query['display'] = $filter;
            }

            return current_url() . (!empty($query) ? '?' . http_build_query($query) : '');
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

        <nav class="ier-filter" aria-label="Filter applicants by qualification status">
            <?php foreach (array('all' => 'All Applicants', 'qualified' => 'Qualified', 'disqualified' => 'Disqualified') as $filterKey => $filterLabel): ?>
                <a href="<?= htmlspecialchars($ier_filter_url($filterKey), ENT_QUOTES, 'UTF-8'); ?>"
                   class="<?= $display_filter === $filterKey ? 'is-active' : ''; ?>"
                   aria-current="<?= $display_filter === $filterKey ? 'page' : 'false'; ?>">
                    <?= $filterLabel; ?> (<?= (int) ($applicant_counts[$filterKey] ?? 0); ?>)
                </a>
            <?php endforeach; ?>
        </nav>

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
                // Read every credential so the IER can tell the difference
                // between an applicant with no records, records still waiting
                // for review, and records all marked Not Relevant.
                $all_trainings = $this->db->query(
                    "select t.trainingTitle, t.sponsor, t.dateStarted, t.dateFinished,
                            t.noHours, r.stat as relevance_stat, r.updated_by,
                            r.credited_hours
                       from hris_trainings t
                       left join hris_record_relevance r
                         on r.record_type = 'training' and r.record_id = t.trainingID
                        and r.job_id = ?
                      where t.IDNumber = ?
                      order by t.dateStarted desc, t.trainingID desc",
                    array($row->jobID, $row->id))->result();
                $all_experiences = $this->db->query(
                    "select e.title, e.position_title, e.date_from, e.date_to, e.ny, e.nm,
                            r.stat as relevance_stat, r.updated_by,
                            r.credited_months
                       from hris_experience e
                       left join hris_record_relevance r
                         on r.record_type = 'experience' and r.record_id = e.id
                        and r.job_id = ?
                      where e.id_number = ?
                      order by e.date_from desc, e.id desc",
                    array($row->jobID, $row->id))->result();

                $tr = array();
                $training_has_no_action = false;
                foreach ($all_trainings as $training) {
                    $was_reviewed = $training->updated_by !== null;
                    $stat = (int) ($training->relevance_stat ?? 0);
                    if ($was_reviewed && $stat === 1) {
                        $tr[] = $training;
                    } elseif (!$was_reviewed || $stat === 0) {
                        $training_has_no_action = true;
                    }
                }
                $training_state = !empty($tr)
                    ? 'relevant'
                    : (!empty($all_trainings) && $training_has_no_action ? 'no_action' : 'none');

                $xp = array();
                $experience_has_no_action = false;
                foreach ($all_experiences as $experience) {
                    $was_reviewed = $experience->updated_by !== null;
                    $stat = (int) ($experience->relevance_stat ?? 0);
                    if ($was_reviewed && $stat === 1) {
                        $xp[] = $experience;
                    } elseif (!$was_reviewed || $stat === 0) {
                        $experience_has_no_action = true;
                    }
                }
                $experience_state = !empty($xp)
                    ? 'relevant'
                    : (!empty($all_experiences) && $experience_has_no_action ? 'no_action' : 'none');

                $training_sum = 0;
                foreach ($tr as $trow) {
                    $trow->credit_hours = $trow->credited_hours !== null ? (float) $trow->credited_hours : (float) $trow->noHours;
                    $training_sum += $trow->credit_hours;
                }

                $ex_months = 0;
                foreach ($xp as $xrow) {
                    $xfrom = !empty($xrow->date_from) && $xrow->date_from !== '0000-00-00' ? $xrow->date_from : null;
                    $xto   = !empty($xrow->date_to) && $xrow->date_to !== '0000-00-00' ? $xrow->date_to : null;
                    $xrow->credit_months = $xrow->credited_months !== null
                        ? (int) $xrow->credited_months
                        : (($xfrom && $xto) ? $this->Reg->experience_months($xfrom, $xto) : ((int) $xrow->ny * 12) + (int) $xrow->nm);
                    $ex_months += $xrow->credit_months;
                }

            ?>
            <tr>
                <td><?= $c++; ?></td>
                <td><?= $row->code; ?></td>

                <td><?= $row->bachelor; ?></td>
                <td style="text-align:left">
                    <?php if ($training_state === 'relevant'): foreach($tr as $trrow){
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
                        <?php } elseif ($training_state === 'no_action'): ?>
                            <span class="ier-record-state">No action yet</span>
                        <?php else: ?>
                            <span class="ier-record-state">No relevant training</span>
                        <?php endif; ?>
                </td>
                <td><?= $training_state === 'no_action' ? '&mdash;' : ($training_sum == (int) $training_sum ? (int) $training_sum : $training_sum); ?></td>
                <td style="text-align:left; padding:0 !important">
                        <?php if ($experience_state === 'relevant'): foreach($xp as $xprow){
                            // Company / Office - Job Title / Position - Inclusive
                            // Date - duration credited for this relevant record.
                            $xpParts = array(
                                trim((string) ($xprow->title ?? '')),
                                trim((string) ($xprow->position_title ?? '')),
                                $ier_date_range($xprow->date_from ?? null, $xprow->date_to ?? null),
                                'Duration: ' . $ier_duration($xprow->credit_months),
                            );
                            $xpParts = array_filter($xpParts, function ($p) { return $p !== ''; });
                        ?>
                              <span class="xp"><?= implode(' - ', array_map(function ($p) { return htmlspecialchars($p, ENT_QUOTES, 'UTF-8'); }, $xpParts)); ?></span>
                        <?php } elseif ($experience_state === 'no_action'): ?>
                            <span class="ier-record-state">No action yet</span>
                        <?php else: ?>
                            <span class="ier-record-state">No relevant experience</span>
                        <?php endif; ?>

                </td>
                <td><?php
                        echo $experience_state === 'no_action'
                            ? '&mdash;'
                            : htmlspecialchars($ier_duration($ex_months), ENT_QUOTES, 'UTF-8');
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
            <?php if (empty($data)): ?>
                <tr><td colspan="9" style="padding:20px; color:#555;">No <?= $display_filter === 'all' ? '' : htmlspecialchars($display_filter, ENT_QUOTES, 'UTF-8') . ' '; ?>applicants found.</td></tr>
            <?php endif; ?>
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
