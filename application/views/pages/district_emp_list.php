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

        <style>
            body{
                font-family: Arial, Helvetica, sans-serif;
                font-size:12px;
                margin:20px;
            }
            .dcpr{
                border-collapse: collapse;
            }

            .dcpr td, .dcpr th{
                border:1px solid #222;
                padding:5px 10px;
            }
            .text-center{
                text-align:center;
            }
            .dcpr th{
                background-color:#7c7979;
                color:#fff;
            }
        </style>
        
    
    </head>

    <button id="btnExport" onclick="fnExcelReport();"> EXPORT TO EXCEL</button>



    <body>
        <div>
            <h1 class="text-center"><?= $title; ?> - <?= $district->discription; ?></h1>

            <table class="dcpr" id='myTable' style="width:80%; margin:auto">
                
                <?php 
                    foreach($school as $row){ 
                    $staff = $this->Common->two_cond('hris_staff', 'schoolID',$row->schoolID,'currentStatus','Active');
                ?>
                <tr>
                    <td style="background-color:#dee1e0" colspan="3"><b><?= strtoupper($row->schoolName); ?></b></td>
                   
                </tr>
                <?php $ivy=1; foreach($staff as $srow){ ?>
                <tr>
                    <td><?= $ivy++; ?></td>
                    <td><?= $srow->FirstName.' '.$srow->MiddleName.' '.$srow->LastName; ?></td>
                    <td><?= $srow->IDNumber; ?></td>
                </tr>
                <?php } } ?>
                
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