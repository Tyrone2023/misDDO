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
            <h1 class="text-center">DCP RECIPIENT REPORTS</h1>

            <table class="dcpr" id='myTable'>
                <tr>
                    <th>#</th>
                    <th>Region</th>
                    <th>Division</th>
                    <th>BEIS School ID</th>
                    <th>School Name</th>
                    <th>Province</th>
                    <th>Municipality</th>
                    <th>BEIS School ID</th>
                    <th>School Name</th>
                    <th>Remarks</th>
                    <th>Contact Person</th>
                    <th>Contact Number</th>
                    <th>Address</th>
                    <th>Electricity</th>
                    <th>Internet</th>
                    <th>Internet Provider</th>
                    <th>School Level</th>
                </tr>
                <?php $ivy=1; foreach($school as $row){ ?>
                <tr>
                    <td><?= $ivy++; ?></td>
                    <td>XI</td>
                    <td><?= $row->division; ?></td>
                    <td><?= $row->schoolID; ?></td>
                    <td><?= strtoupper($row->schoolName); ?></td>
                    <td><?= $row->province; ?></td>
                    <td><?= $row->city; ?></td>
                    <td><?= $row->schoolID; ?></td>
                    <td><?= strtoupper($row->schoolName); ?></td>
                    <td></td>
                    <td><?= $row->adminFName; ?> <?= $row->adminMName; ?> <?= $row->adminLName; ?></td>
                    <td><?= $row->adminMobile; ?></td>
                    <td><?= $row->sitio; ?>, <?= $row->brgy; ?>, <?= $row->city; ?>, <?= $row->province; ?></td>
                    <td><?= $row->electricity == 0 ? "Yes" : "No"; ?></td>
                    <td><?= $row->internet == 0 ? "Yes" : "No"; ?></td>
                    <td><?= $row->provider; ?></td>
                    <td><?= $row->course; ?></td>
                </tr>
                <?php } ?>
                
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