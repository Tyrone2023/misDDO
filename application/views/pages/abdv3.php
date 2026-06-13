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
            <h4 style="margin:40px 0">List of Applicant</h4>

            <table class="rrr" id='myTable'>
                <?php
                    $c=1;
                    foreach($data as $mrow){
                     $school = $this->Common->one_cond_row('schools', 'schoolID', $mrow->pre_school);
                     $app = $this->Common->one_cond_count_row('hris_applications', 'pre_school', $mrow->pre_school);
                     $validated = $this->Common->two_cond_count_row('hris_applications', 'pre_school', $mrow->pre_school,'appStatus','Endorsed for Rating');
                     
                     $job = $this->Common->one_cond_loop_order_by('hris_jobvacancy', 'sy', date('Y'),'jobID','DESC');
                     
                   
                ?>
                <?php 
                    foreach($job as $jrow){
                    $sv = $this->Common->three_cond_order_by('hris_applications', 'district', $mrow->district,'dq',1,'jobID',$jrow->jobID,'pre_school','ASC');
                    //$sv = $this->Common->two_join_three_cond('hris_applications','hris_applicant','a.pre_school,a.district,a.empEmail,a.appStatus,b.empEmail,b.FirstName,b.MiddleName,b.LastName','b.empEmail=a.empEmail','a.appStatus','Endorsed for Rating','a.district', $mrow->district,'a.jobID',$jrow->jobID,'b.FirstName','ASC');
                ?>
                <tr>
                    <td></td>
                    <td style="text-align:center; background-color:#fafcb8;"><b>APPLICATION CODE</b></td>
                    <td style="text-align:center; background-color:#fafcb8;"><b>DISTRICT</b></td>
                    <td style="text-align:center; background-color:#fafcb8;"><b>SCHOOL APPLIED</b></td>
                    <td style="text-align:center; background-color:#fafcb8;"><b>POSITION APPLIED</b></td>
                    <td style="text-align:center; background-color:#fafcb8;"><b>BACHELORS DEGREE</b></td>
                    <td style="text-align:center; background-color:#fafcb8;"><b>SPECIALIZATION</b></td>
                </tr>
                <?php 
                    $ca=1;
                    foreach($sv as $row){
                    $applicant = $this->Common->one_cond_row('hris_applicant', 'empEmail', $row->empEmail);
                    $s = $this->Common->one_cond_row('schools', 'schoolID', $row->pre_school);
                ?>
                
                <tr>
                    <td><?= $ca++; ?></td>
                    <td><?= strtoupper($applicant->record_no); ?></td>
                    <td><?= strtoupper($s->district); ?></td>
                    <td><?= strtoupper($s->schoolName); ?></td>
                    <td><?= strtoupper($jrow->jobTitle); ?></td>
                    <td><?= strtoupper($applicant->bd); ?></td>
                    <td><?= strtoupper($applicant->jhss); ?></td>
                </tr>
                <?php } ?>
                
                <?php  }} ?>
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