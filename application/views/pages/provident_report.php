<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        body{
            *{
                margin:0;
                padding:0;
                text-align:center;
            }
        }
        .my-table {
        width: 100%;
        border-collapse: collapse;
        }

        .my-table th,
        .my-table td {
        border: 1px solid #000;
        padding: 8px;
        text-align:center;
        }
        h1{
            text-align:center;
        }
        h2{
            text-align:center;
            font-size:20px;
        }
        .btn-accept{
            margin-top:50px;
            cursor:pointer;
            font-size:12px;
            display:inline-block;
            padding:10px 16px;
            border-radius:10px;
            background:#16a34a; 
            color:#fff;
            font-weight:600;
            text-decoration:none;
            border:1px solid rgba(0,0,0,.08);
            box-shadow:0 8px 18px rgba(22,163,74,.18);
            transition:.18s ease;
        }
        .btn-accept:hover{
            transform:translateY(-1px);
            filter:brightness(.98);
            box-shadow:0 12px 22px rgba(22,163,74,.24);
        }
        .btn-accept:active{
            transform:translateY(0);
            box-shadow:0 6px 14px rgba(22,163,74,.18);
        }
    </style>
</head>
<body>
    <form method="post" action="<?= site_url('Provident/approve_all'); ?>">

  <?php foreach($data as $row){ ?>
    <!-- use your real primary key column here (example: id) -->
    <input type="hidden" name="ids[]" value="<?= (int)$row->id; ?>">
  <?php } ?>
    <?php if($count->num_rows() >= 1){ ?>
    <button type="submit" class="btn-accept"
            onclick="return confirm('Approve/Verify ALL records shown in this list?');">
        Accept
    </button>
  <?php } ?>
  <input type="hidden" value="<?= $fy; ?>" name="fy">
  <input type="hidden" value="<?= $month; ?>" name="month">
</form>
<button id="btnExport" class="btn-accept" onclick="fnExcelReport();"> EXPORT TO EXCEL</button>
    
    <h1>
    <?php $monthName = DateTime::createFromFormat('!m', $month)->format('F'); ?>
    <br />SCHOOL DIVISION OF DAVAO ORIENTAL</h1>
    <h2> <?= strtoupper($monthName); ?> <?= $fy; ?> BILLING</h2>

    <table class="my-table" id="myTable">
        <thead>
            <tr>
                <th rowspan='2'>Employee No.</th>
                <th colspan="3">EMPLOYEE NAME</th>
                <th rowspan='2'>Deduction Code</th>
                <th colspan="2">Effectivity(from)</th>
                <th colspan="2">Effectivity(to)<br /> Month</th>
                <th>For Deletion<br /> of Old Amortization<br /> For Reloan Only</th>
                <th rowspan='2'>Deduction<br /> Amount</th>
                <th rowspan='2'>Principal<br /> Amount</th>
                <th rowspan='2'>Loan Approved<br /> Date</th>
                <th rowspan='2'>Remarks</th>
                <th rowspan='2'>Date Verified<br />(Division Verifier)</th>
            </tr>
            <tr>
                <th>Last Name</th>
                <th>First Name</th>
                <th>Middle Name</th>
                <th>Month</th>
                <th>Year</th>
                <th>Month</th>
                <th>Year</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($data as $row){?>
            <tr>
                <td><?= $row->employee_no; ?></td>
                <td><?= strtoupper($row->LastName); ?></td>
                <td><?= strtoupper($row->FirstName); ?></td>
                <td><?= strtoupper($row->MiddleName); ?></td>
                <td><?= $row->deduction_code; ?></td>
                <td><?= $row->effect_from_month; ?></td>
                <td><?= $row->effect_from_year; ?></td>
                <td><?= $row->effect_to_month; ?></td>
                <td><?= $row->effect_to_year; ?></td>
                <td></td>
                <td><?= $row->deduction; ?></td>
                <td><?= $row->principal; ?></td>
                <td><?= $row->approved_date; ?></td>
                <td><?= $row->remarks; ?></td>
                <td><?= $row->verified; ?></td>
            </tr>
            <?php } ?>
            <?php foreach($del as $row){?>
            <tr>
                <td><?= $row->employee_no; ?></td>
                <td><?= strtoupper($row->LastName); ?></td>
                <td><?= strtoupper($row->FirstName); ?></td>
                <td><?= strtoupper($row->MiddleName); ?></td>
                <td><?= $row->deduction_code; ?></td>
                <td><?= $row->effect_from_month; ?></td>
                <td><?= $row->effect_from_year; ?></td>
                <td><?= $row->effect_to_month; ?></td>
                <td><?= $row->effect_to_year; ?></td>
                <td><?= !empty($row->reloan) ? $row->reloan : ''; ?></td>
                <td><?= $row->deduction; ?></td>
                <td><?= $row->principal; ?></td>
                <td><?= $row->approved_date; ?></td>
                <td><?= $row->remarks; ?></td>
                <td><?= $row->verified; ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    
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