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
                font-size:10px;
                margin:20px;
            }
            a{
                text-decoration:none;
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
            .rwrap{
                margin:auto 20px;
                overflow-x:auto;
                overflow: scroll;
            }

             .inline-container {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        background-color: #ffffff; /* or any color you like */
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 30px;
        gap: 20px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        z-index: 9999; /* ensure it's on top of other content */
        flex-wrap: wrap;
    }

    body {
        padding-top: 80px; /* prevent content from being hidden behind the fixed header */
    }

    #btnExport {
        padding: 10px 20px;
        background-color: #2196F3;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
        transition: background-color 0.3s;
        white-space: nowrap;
    }

    #btnExport:hover {
        background-color: #0b7dda;
    }

    form {
        display: flex;
        gap: 10px;
        align-items: center;
        background: #f5f5f5;
        padding: 10px 15px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    select, input[type="submit"] {
        padding: 8px 12px;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 14px;
    }

    input[type="submit"] {
        background-color: #4CAF50;
        color: white;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    input[type="submit"]:hover {
        background-color: #45a049;
    }
            
        </style>
        
    
    </head>
    

    

   


    <body>


    <div class="inline-container">
    <!-- Left: Export Button -->
    <button id="btnExport" onclick="fnExcelReport();">EXPORT TO EXCEL</button>

    <!-- Right: Inline Form -->
    <form action="<?= base_url(); ?>Pages/employee_report" method="post">
        <select name="pgroup">
            <option value="">Select Group</option>
            <?php foreach($plan as $row){ ?>
                <option value="<?= $row->pGroup; ?>"><?= $row->pGroup; ?></option>
            <?php } ?>
        </select>
        <input type="submit" value="Submit" name="submit">
    </form>
</div>


        <div class="rwrap">
            <h1 class="text-center"><?= $title; ?></h1>

            <table class="dcpr" id='myTable'>
                <tr>
                    <th>ORGANIZATIONAL<br /> UNIT (1)</th>
                    <th>ITEM NUMBER (2)</th>	
                    <th>POSITION TITLE (3)</th>	
                    <th>SALARY GRADE (4)</th>	
                    <th>AUTHORIZED <br />ANNUAL SALARY (5)</th>	
                    <th>ACTUAL ANNUAL<br /> SALARY (6)</th>	
                    <th>STEP (7)</th>	
                    <th>AREA CODE (8)</th>	
                    <th>AREA TYPE (9)</th>	
                    <th>LEVEL (10)</th>		
                    <th>LAST NAME (11)</th>	
                    <th>FIRST NAME (12)</th>	
                    <th>MIDDLE NAME (13)</th>	
                    <th>SEX (14)</th>	
                    <th>DATE OF BIRTH (15)</th>	
                    <th>TIN (16)</th>	
                    <th>UMID NO. (17)</th>	
                    <th>DATE OF <br />ORIGINAL <br />APPOINTMENT (18)</th>	
                    <th>DATE OF <br />LAST PROMOTION/<br /> APPOINTMENT (19)</th>	
                    <th>STATUS (20)</th>
                    <th>CIVIL <br />SERVICE <br />ELIGIBILITY (21)</th>	
                    <th>COMMENT/ <br />ANNOTATION (22)</th>
                </tr>

                <?php if(isset($_POST['submit'])) { ?>

                <?php foreach($plantilla as $row){ ?>
                <tr style="background-color:#d2d2d2">
                    <td colspan="22"><?= $row->pGroup; ?></td>
                </tr>
                <?php 
                    $c=1;
                    $p = $this->Common->one_cond('hris_plantilla','pGroup',$row->pGroup);
                    foreach($p as $prow){
                        $staff = $this->Common->one_cond_row_select('hris_staff','empPosition,perZipCode,actualSalary,stepNo,itemNo,LastName,FirstName,MiddleName,IDNumber,Sex,BirthDate,tinNo,umid,dateHired,lastAppointmentDate,csEligibility','itemNo',$prow->itemNo);
                ?>
                <tr>
                    <td><?= $c++; ?></td>
                    <td <?php if(empty($staff)) { echo "style='background:#0984e3;'"; } ?>><a style="white-space: nowrap;" href="<?= base_url(); ?>Pages/plantilla_update/<?= $prow->id; ?>" target="_blank"><?= $prow->itemNo; ?></a></td>
                    <td style="white-space: nowrap;"><?php if(!empty($staff)){if(TRIM($prow->itemPosition) == TRIM($staff->empPosition)){echo $prow->itemPosition; }else{echo "<span style='color:red'>".$prow->itemPosition."</span>";}}else{echo $prow->itemPosition;} ?></td>
                    <td><?= $prow->sg; ?></td>
                    <td><?= number_format($prow->authAnnualSalary); ?></td>
                    <td><?= !empty($staff) ? htmlspecialchars($staff->actualSalary) : '' ?></td>
                    <td><?= !empty($staff) ? htmlspecialchars($staff->stepNo) : '' ?></td>
                    <td><?= !empty($staff) ? htmlspecialchars($staff->perZipCode) : '' ?></td>
                    <td></td>
                    <td></td>
                    <td style="white-space: nowrap;">
                        <a href="<?= base_url(); ?>Pages/employee_edit/<?= !empty($staff) ? htmlspecialchars($staff->IDNumber) : '' ?>" target="_blank"><?= !empty($staff) ? htmlspecialchars($staff->LastName) : '' ?></a>
                    </td>
                    <td style="white-space: nowrap;"><?= !empty($staff) ? htmlspecialchars($staff->FirstName) : '' ?></td>
                    <td style="white-space: nowrap;"><?= !empty($staff) ? htmlspecialchars($staff->MiddleName) : '' ?></td>
                    <td><?= !empty($staff) ? htmlspecialchars($staff->Sex) : '' ?></td>
                    <td><?= !empty($staff) ? htmlspecialchars($staff->BirthDate) : '' ?></td>
                    <td><?= !empty($staff) ? htmlspecialchars($staff->tinNo) : '' ?></td>

                    <td style="white-space: nowrap;"><?= !empty($staff) ? htmlspecialchars($staff->umid) : '' ?></td>
                    <td><?= !empty($staff) ? htmlspecialchars($staff->dateHired) : '' ?></td>
                    <td><?= !empty($staff) ? htmlspecialchars($staff->lastAppointmentDate) : '' ?></td>
                    <td>P</td>
                    <td style="white-space: nowrap;"><?= !empty($staff) ? htmlspecialchars($staff->csEligibility) : '' ?></td>
                    <td></td>
                </tr>
                <?php } } ?>

                <?php } ?>

         
        
        
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