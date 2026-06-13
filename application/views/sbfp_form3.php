<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>DepEd Davao Oriental MIS</title>
    <link href="<?= base_url(); ?>assets/css/renren_sbfp.css" rel="stylesheet" type="text/css" />
    <link href="https://db.onlinewebfonts.com/a/0nH393RJctHgt1f2YvZvyruY" rel="stylesheet" type="text/css"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Responsive bootstrap 4 admin template" name="description" />
    <meta content="Coderthemes" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="shortcut icon" href="<?= base_url(); ?>assets/images/hris.ico">
    <style>
        @media print {
            @page {size: portrait}
            .sbfp{
                margin-top:0
            }
        }
    </style>
</head>
<body>

    <div class="wrap">

        <div class="sbfp_header">
            <img class="logo" style="width:70px; height:70px;" src="<?= base_url(); ?>assets/images/report/ke.png" alt="">
            <p>
            <span class="rp">Republic of the Philippines</span><br />
                <span class="de">Department of Education</span><br />
                <span class="r">Region XI</span><br />
                <!-- <span class="r">School Division of Davao Oriental</span><br />
                <span class="sadress"><?= strtoupper($school->district); ?><br />
                <?= strtoupper($school->schoolName); ?><br />
                <?= strtoupper($school->sitio); ?> <?= strtoupper($school->brgy); ?>, <?= strtoupper($school->city); ?>, <?= strtoupper($school->province); ?></span>  -->
            </p>
            

            <h1 class="bs">SCHOOL-BASED FEEDING PROGRAM<br />RECORD OF DAILY FEEDING</h1>
        </div>

        <table class="sbfp sbfpren">
            <tr>
                <th colspan="5" class="nb">Region: XI</th>
                <th colspan="5" class="nb"></th>
                <th class="nb tr bt">L</th>
                <th colspan="10" class="nb bt tr2">(H ) - Present, served with Hot meals/NFP</th>
                <th class="nb"></th>
                <th class="nb"></th>
                <th class="nb"></th>
                <th class="nb"></th>
                <th class="nb"></th>
                <th class="nb"></th>
            </tr>
            <tr>
                <th colspan="5" class="nb">Division: Davao Oriental</th>
                <th colspan="5" class="nb"></th>
                <th class="nb tr">E</th>
                <th colspan="10" class="nb tr2"> (M ) - Present, served with Milk</th>
                <th class="nb"></th>
                <th class="nb"></th>
                <th class="nb"></th>
                <th class="nb"></th>
                <th class="nb"></th>
                <th class="nb"></th>
            </tr>
            <tr>
                <th colspan="5" class="nb">District:</th>
                <th colspan="5" class="nb"></th>
                <th class="nb tr">G</th>
                <th colspan="10" class="nb tr2"> (HM ) - Present, served with Hot meals/NFP & Milk</th>
                <th class="nb"></th>
                <th class="nb"></th>
                <th class="nb"></th>
                <th class="nb"></th>
                <th class="nb"></th>
                <th class="nb"></th>
            </tr>
            <tr>
                <th colspan="5" class="nb">School:</th>
                <th colspan="5" class="nb"></th>
                <th class="nb tr">E</th>
                <th colspan="10" class="nb tr2">( A ) - Absent, not served</th>
                <th class="nb"></th>
                <th class="nb"></th>
                <th class="nb"></th>
                <th class="nb"></th>
                <th class="nb"></th>
                <th class="nb"></th>
            </tr>
            <tr>
                <th colspan="5" class="nb">School ID:</th>
                <th colspan="5" class="nb"></th>
                <th class="nb tr">N</th>
                <th colspan="10" class="nb tr2">(HHMM,HHM,HMM) - Present, served twice</th>
                <th class="nb"></th>
                <th class="nb"></th>
                <th class="nb"></th>
                <th class="nb"></th>
                <th class="nb"></th>
                <th class="nb"></th>
            </tr>
            <tr>
                <th colspan="5" class="nb"></th>
                <th colspan="5" class="nb"></th>
                <th class="nb tr">D</th>
                <th colspan="10" class="nb tr2">(NF) - No Feeding/Done Feeding</th>
                <th class="nb"></th>
                <th class="nb"></th>
                <th class="nb"></th>
                <th class="nb"></th>
                <th class="nb"></th>
                <th class="nb"></th>
            </tr>
            <tr>
                <th rowspan="3">Name</th>
                <th colspan="19">ACTUAL FEEDING</th>
                <th colspan="6">FIRST 20 DAYS SUMMARY</th>
            </tr>
            <tr>
                <?php for($x = 0; $x <= 18; $x++){ ?>
                <th class="vert"><?= date('Y'); ?></th>
                <?php } ?>
                <th rowspan="2">TOTAL ABSENT</th>
                <th rowspan="2">TOTAL MILK GIVEN</th>
                <th rowspan="2">TOTAL HOTMEAL/NFP GIVEN</th>
                <th rowspan="2">ACTUAL FEEDING CONDUCTED</th>
                <th rowspan="2">TOTAL NUMBER PRESENT</th>
                <th rowspan="2">PERCENTAGE</th>
            </tr>
            <tr>
                <th>1</th>
                <th>2</th>
                <th>3</th>
                <th>4</th>
                <th>5</th>
                <th>6</th>
                <th>7</th>
                <th>8</th>
                <th>9</th>
                <th>10</th>
                <th>11</th>
                <th>12</th>
                <th>13</th>
                <th>14</th>
                <th>16</th>
                <th>17</th>
                <th>18</th>
                <th>19</th>
                <th>20</th>
            </tr>
            <?php foreach($sbf as $row){?>
            <tr>
                <td><?= $row->fName; ?> <?= $row->mName; ?> <?= $row->lName; ?></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <?php } ?>
        </table>


        <table class="signtable">
            <tr>
                <td class="tableTextLeft tableNoBorder">Prepared by:</td>
                <td class="nb"></td>
                <td class="nb"></td>
            </tr>
            <tr>
                <td class="tableNoBorder">&nbsp;</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td class="tableNoBorder">Elena D. Cantilado</th>
                <td rowspan="2">B. Deworming</td>
                <td rowspan="2">D. Actual Feeding</td>
            </tr>
            <tr>
                <td class="tableBorderTop">Feeding Teacher / School Nurse</td>
                
            </tr>
            <tr>
                <td class="tableNoBorder">&nbsp;</td>
                <td class="noBorderBottom">( x ) - not dewormed</td>
                <td class="noBorderBottom"> (H ) - Present, served with Hot meals/NFP</td>
            </tr>
            <tr>
                <td class="tableTextLeft tableNoBorder">Approved by:</td>
                <td class="noBorderBottom">( √ ) - dewormed</td>
                <td class="noBorderBottom"> (M ) - Present, served with Milk</td>
            </tr>
            <tr>
                <td class="tableNoBorder">&nbsp;</td>
                <td class="noBorderBottom"></td>
                <td class="noBorderBottom"> (HM ) - Present, served with Hot meals/NFP & Milk</td>
            </tr>
            <tr>
                <td class="tableNoBorder">Juliana D. Juarez</td>
                <td class="noBorderBottom"></td>
                <td class="noBorderBottom">( A ) - Absent, not served</td>
            </tr>
            <tr>
                <td class="tableNoBorder tableBorderTop">School Head</td>
                <td class="noBorderBottom"></td>
                <td class="noBorderBottom">(HHMM,HHM,HMM) - Present, served twice</td>
            </tr>
            <tr>
                <td class="tableNoBorder"></td>
                <td class="noBorderTop"></td>
                <td class="noBorderTop">(NF) - No Feeding/Done Feeding</td>
            </tr>
        </table>
        <p>Note:  This form shall be prepared by the  school to be consolidated using the Revised OKD Form A.</p>

    </div>







    
</body>
</html>