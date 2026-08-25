<?php
$job = $job ?? null;
$applicants = $applicants ?? [];
$withContact = !empty($withContact);

$jobTypes = [
    1  => '- Elementary',
    2  => '- Secondary',
    3  => '- Junior High School',
    4  => '- Senior High School',
    5  => '- Kindergarten',
    6  => '- IPED Elementary',
    7  => '- IPED Secondary',
    8  => '- IPED Junior High School',
    9  => '- IPED Senior High School',
    10 => '- SNED',
];

$positionTitle = $job ? $job->jobTitle . ' ' . ($jobTypes[(int) $job->job_type] ?? '') : '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="shortcut icon" href="<?= base_url(); ?>assets/images/hris.ico">
    <link href="<?= base_url(); ?>assets/css/renren.css" rel="stylesheet" type="text/css" />
    <link href="https://db.onlinewebfonts.com/a/0nH393RJctHgt1f2YvZvyruY" rel="stylesheet" type="text/css" />
    <title><?= htmlspecialchars($title ?? 'Shortlist', ENT_QUOTES, 'UTF-8'); ?></title>

    <style>
        .aip_generate .rrr {
            margin-bottom: 20px;
            font-family: "Calibri", sans-serif;
        }

        .aip_generate .rrr th {
            background-color: #fff;
            color: #000;
        }

        .aip_generate .rrr td {
            text-align: left;
        }

        .aip_generate .rrr td.num,
        .aip_generate .rrr th.num {
            text-align: center;
            width: 45px;
        }

        @page {
            size: A4;
            margin: 50px 0;
        }

        @media print {

            html,
            body {
                width: 210mm;
                height: 297mm;
                font-size: 14px !important;
            }

            #btnExport {
                display: none !important;
            }

            .aip_generate .hr {
                margin: 20px 0;
                width: 100%;
            }

            .cert {
                width: 90%;
                padding-top: 1px;
            }

            .aip_generate .rrr th,
            .aip_generate .rrr td {
                padding: 6px 4px;
            }
        }
    </style>
</head>

<body class="aip_generate" id="printTable">
    <iframe id="txtArea1" style="display:none"></iframe>
    <button id="btnExport" onclick="fnExcelReport();">EXPORT TO EXCEL</button>

    <div class="cert">
        <img class="logo" src="<?= base_url(); ?>assets/images/report/ke.png" alt="">
        <p style="margin-bottom:0;">
            <span class="rp">Republic of the Philippines</span><br />
            <span class="de">Department of Education</span><br />
            <span class="r">Region XI</span><br />
            <span class="r">Schools Division of <?php echo $mis_settings[0]->division; ?></span>
        </p>

        <div class="hr" style="margin:10px 0"></div>

        <h4 style="margin:40px 0">Shortlist of <?= htmlspecialchars(trim($positionTitle), ENT_QUOTES, 'UTF-8'); ?></h4>

        <?php if (!empty($applicants)) : ?>
            <table class="rrr" id="myTable">
                <thead>
                    <tr>
                        <th class="num">No.</th>
                        <th>Record No.</th>
                        <th>Name</th>
                        <?php if ($withContact) : ?>
                            <th>Contact Number</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php $num = 1; ?>
                    <?php foreach ($applicants as $row) : ?>
                        <?php
                        $name = trim(preg_replace('/\s+/', ' ',
                            $row->FirstName . ' ' . $row->MiddleName . ' ' . $row->LastName . ' ' . $row->NameExtn
                        ));
                        ?>
                        <tr>
                            <td class="num"><?= $num++; ?></td>
                            <td><?= htmlspecialchars((string) $row->record_no, ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?></td>
                            <?php if ($withContact) : ?>
                                <td><?= htmlspecialchars((string) $row->contact_no, ENT_QUOTES, 'UTF-8'); ?></td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <h4 style="margin:40px 0">No shortlisted applicants found for this vacancy.</h4>
        <?php endif; ?>
    </div>

    <script>
        function fnExcelReport() {
            var tab_text = "<table border='2px'><tr bgcolor='#87AFC6'>";
            var j = 0;
            var tab = document.getElementById('myTable');
            if (!tab) return;

            for (j = 0; j < tab.rows.length; j++) {
                tab_text = tab_text + tab.rows[j].innerHTML + "</tr>";
            }

            tab_text = tab_text + "</table>";
            tab_text = tab_text.replace(/<A[^>]*>|<\/A>/g, "");
            tab_text = tab_text.replace(/<img[^>]*>/gi, "");
            tab_text = tab_text.replace(/<input[^>]*>|<\/input>/gi, "");

            var msie = window.navigator.userAgent.indexOf("MSIE ");

            if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./)) {
                txtArea1.document.open("txt/html", "replace");
                txtArea1.document.write(tab_text);
                txtArea1.document.close();
                txtArea1.focus();

                sa = txtArea1.document.execCommand("SaveAs", true, "shortlist.xls");
            } else {
                sa = window.open('data:application/vnd.ms-excel,' + encodeURIComponent(tab_text));
            }

            return sa;
        }
    </script>
</body>

</html>
