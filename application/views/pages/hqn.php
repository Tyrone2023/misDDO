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
            <h4 style="margin:40px 0">List of Not Qualified Applicants</h4>

            <table class="rrr" id='myTable'>
                <?php
                    $c=1;
                     
                    foreach($job as $jrow){
                        $application = $this->Page_model->dq_list_promotion($jrow->jobID);
                        if(!empty($application)){
                ?>
                <tr>
                    <td style="text-align:center; background-color:#fafcb8;" colspan="10"><b><?= $jrow->jobTitle; ?></b></td>
                    <tr style="text-align:center; background-color:#BFECFF;">
                    <td style="text-align:center;">RECORD NO.</td>
                    <td style="text-align:center;">FULLNAME</td>
                    <td style="text-align:center;">DISTRICT</td>
                    <td style="text-align:center; background-color:#fafcb8;">Letter of<br /> Intent</td>
                    <td style="text-align:center; background-color:#fafcb8;">PDS</td>
                    <td style="text-align:center; background-color:#fafcb8;">PRC</td>
                    <td style="text-align:center; background-color:#fafcb8;">Transcript of Records of<br /> the Baccalaureate Degree</td>
                    <td style="text-align:center; background-color:#fafcb8;">Omnibus Sworn<br /> Statement</td>
                    <td style="text-align:center; background-color:#fafcb8;">Address</td>
                    <td style="text-align:center; background-color:#fafcb8;">Reason</td>
                </tr>
                </tr>
                <?php 
                foreach($application as $row){ 
                ?>
                <tr>
                    <td><?= $row->appID; ?></td>
                    <td><a style="text-decoration:none; color:#222" target="_blank" href="<?= base_url(); ?>pages/<?= $row->st; ?>/<?= $row->code; ?>/<?= $row->jobID; ?>/<?= $row->pre_school; ?>/<?= $row->appID; ?>/<?= $row->code; ?>/" class="text-success tooltips" data-placement="top" data-toggle="tooltip" data-original-title="View Applicant Information"><?= $row->LastName.', '. $row->MiddleName.' '.$row->FirstName; ?></a></td>
                    <td><?= $row->resCity; ?></td>
                    <td style="text-align:center; <?php if($row->li == 0){echo "color:red; font-weight:bold";} ?>"><?php if($row->li == 1){echo "&#x1F5F8;";}else{echo "&#215;";}?></td>
                    <td style="text-align:center; <?php if($row->da_pds == 0){echo "color:red; font-weight:bold";} ?>"><?php if($row->da_pds == 1){echo "&#x1F5F8;";}else{echo "&#215;";}?></td>
                    <td style="text-align:center; <?php if($row->prc == 0){echo "color:red; font-weight:bold";} ?>"><?php if($row->prc == 1){echo "&#x1F5F8;";}else{echo "&#215;";}?></td>
                    <td style="text-align:center; <?php if($row->trbd == 0){echo "color:red; font-weight:bold";} ?>"><?php if($row->trbd == 1){echo "&#x1F5F8;";}else{echo "&#215;";}?></td>
                    <td style="text-align:center; <?php if($row->omni == 0){echo "color:red; font-weight:bold";} ?>"><?php if($row->omni == 1){echo "&#x1F5F8;";}else{echo "&#215;";}?></td>
                    <td style="text-align:center; <?php if($row->local == 1){echo "color:red; font-weight:bold";} ?>"><?php if($row->local == 0){echo "&#x1F5F8;";}else{echo "&#215;";}?></td>
                    <td><?= $row->reason; ?></td>
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