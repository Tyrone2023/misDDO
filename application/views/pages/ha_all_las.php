
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
                <th rowspan="2">Names of Applicant</th>
                <th colspan="9">Personal Information</th>
                <th rowspan="2">Education</th>
                <th colspan="2">Training</th>
                <th colspan="3">Experience</th>
                <th rowspan="2">Remarks <br />(Qualified or <br />Disqualified)</th>
                <th rowspan="2">Remarks</th>
            </tr>
            <tr>
                <th>Address</th>
                <th>Age</th>
                <th>Sex</th>
                <th>Civil <br />Status</th>
                <th>Religion</th>
                <th>Disability</th>
                <th>Ethnic <br />Group</th>
                <th>Email Address</th>
                <th>Contact No.</th>
                <th>Title</th>
                <th>Hours</th>
                <th>Details</th>
                <th>Years</th>
                <th>Eligibility</th>
            </tr>
                <?php foreach ($grouped_applicants as $municipality => $jhss_groups): ?>  
    <tr>
        <td colspan="20" style="text-align:left; font-weight:bold; background:#d9edf7;">
            <?= strtoupper(htmlspecialchars($municipality)); ?>
        </td>
    </tr>

    <?php foreach ($jhss_groups as $jhss => $group): ?>
        <tr>
            <td colspan="20" style="text-align:left; font-weight:bold; padding-left:30px; background:#f5f5f5;">
                JHSS: <?= strtoupper(htmlspecialchars($jhss)); ?>
            </td>
        </tr>

        <?php $c = 1; foreach ($group as $row): ?>
            <tr>
                <td><?= $c++; ?></td>
                <td><?= $row->code; ?></td>
                <td style="white-space: nowrap; text-align:left;">
                    <?= strtoupper($row->LastName . ', ' . $row->FirstName . ' ' . $row->MiddleName); ?>
                </td>
                <td style="text-align:left;">
                    <?= strtoupper($row->brgy . ', ' . $row->resCity . ' ' . $row->province); ?>
                </td>
                <td><?= $row->age; ?></td>
                <td><?= $row->sex; ?></td>
                <td><?= $row->ms; ?></td>
                <td><?= $row->religion; ?></td>
                <td><?= ($row->disability == 0) ? 'No' : 'Yes'; ?></td>
                <td><?= $row->ethnicity; ?></td>
                <td style="text-align:left;"><?= $row->email; ?></td>
                <td style="text-align:left;"><?= $row->contactNo; ?></td>
                <td><?= $row->bachelor; ?></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td><?= $row->dq == 1 ? "Qualified" : ($row->dq == 2 ? "Disqualified" : ""); ?></td>
                <td><?= $row->retain_status; ?></td>
            </tr>
        <?php endforeach; ?>
    <?php endforeach; ?>
<?php endforeach; ?>



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