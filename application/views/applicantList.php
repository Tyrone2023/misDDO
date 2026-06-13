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
                <span class="r">Schools Division of Davao De Oro</span>
            </p>

            <div class="hr" style="margin:10px 0"></div>
            <h4 style="margin:40px 0">List of Applicant</h4>

            <table class="rrr" id='myTable'>
    <tr>
        <th>Applicant ID</th>
        <th>Full Name</th>
        <th>Position</th>
        <th>District</th>
        <th>School</th>
    </tr>
    <?php
    $count = 0;
    foreach ($data as $row) {
        ?>
        <tr>
            <td><?= $row->applicant_id; ?></td>
            <td><?= $row->FirstName . ' ' . $row->MiddleName . ' ' . $row->LastName; ?></td>
            <td>
                                                <?= $row->jobTitle; ?>
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

                                                     echo $jobTypes[$row->job_type] ?? '';
                                                ?>
            <td><?= $row->district; ?></td>
            <td><?= $row->schoolName; ?></td>
        </tr>
        <?php
    }
    ?>
</table>




         
        
        
        </div>

       
    
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