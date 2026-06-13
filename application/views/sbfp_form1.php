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
            

            <h1 class="bs">Master List Beneficiaries for School-Based Feeding Program SBFP(SY date)</h1>
        </div>

        <table class="sbfp sbfpren">
            <tr>
                <th colspan="2" class="nb">Division/Province: </th>
                <th colspan="3" class="nb"></th>
                <th colspan="3" class="nb">Name of Principal :</th>
                <th colspan="4" class="nb"></th>
                <th class="nb"></th>
                <th class="nb"></th>
                <th class="nb"></th>
                <th class="nb"></th>
                <th class="nb"></th>
            </tr>
            <tr>
                <th colspan="2" class="nb">City/ Municipality/Barangay : </th>
                <th colspan="3" class="nb"></th>
                <th colspan="3" class="nb">Name of Feeding Focal Person : </th>
                <th colspan="4" class="nb"></th>
                <th class="nb"></th>
                <th class="nb"></th>
                <th class="nb"></th>
                <th class="nb"></th>
                <th class="nb"></th>
            </tr>
            <tr>
                <th colspan="2" class="nb">Name of School / School District :</th>
                <th colspan="3" class="nb"></th>
                <th colspan="3" class="nb"></th>
                <th colspan="4" class="nb"></th>
                <th class="nb"></th>
                <th class="nb"></th>
                <th class="nb"></th>
                <th class="nb"></th>
                <th class="nb"></th>
            </tr>
            <tr>
                <th colspan="2" class="nb">School ID Number: </th>
                <th colspan="3" class="nb"></th>
                <th colspan="3" class="nb"></th>
                <th colspan="4" class="nb"></th>
                <th class="nb"></th>
                <th class="nb"></th>
                <th class="nb"></th>
                <th class="nb"></th>
                <th class="nb"></th>
            </tr>
            <tr>
                <th rowspan="2">No.</th>
                <th rowspan="2">Name</th>
                <th rowspan="2">Sex</th>
                <th rowspan="2">Grade/Section</th>
                <th rowspan="2">Birthday <br />mm/dd/yy</th>
                <th rowspan="2">Date of<br /> Weighign/<br />Measuring<br />mm/dd/yy</th>
                <th rowspan="2">Age in <br />Years/Months</th>
                <th rowspan="2">Weight<br /> (kg)</th>
                <th rowspan="2">Height<br /> (cm)</th>
                <th rowspan="2">BMI for 6<br />Y.O. and <br /> above</th>
                <th colspan="2">Nutritional Status (NS)</th>
                <th rowspan="2">Dewormed?<br /> (yes or no)</th>
                <th rowspan="2">Parent's<br />  consent for<br />  milk? <br /> (yes or no)</th>
                <th rowspan="2">Participation<br /> in 4Ps<br /> (yes or no)</th>
                <th rowspan="2">Beneficiary<br /> of SBFP in<br /> Previous Years<br /> (yes or no)</th>
                <th rowspan="2">Remarks</th>
            </tr>
            <tr>
                <th>BMI-A</th>
                <th>HFA</th>
            </tr>
            <?php foreach($sbf as $row){
                $stud = $this->Common->one_cond_row('studeprofile', 'StudentNumber', $row->StudentNumber);
                $cbmi = $this->Common->one_cond_row('semester_sbfp', 'StudentNumber', $row->StudentNumber);
                ?>
            <tr>
                <td></td>
                <td style="text-align:left"><?= $stud->LastName; ?><?= empty($stud->FirstName) ? '' : ', '.$stud->FirstName; ?><?= empty($stud->nameExt) ? '' : ', '.$stud->nameExt.', '; ?> <?= $stud->MiddleName; ?> </td>
                <td><?= $stud->Sex; ?></td>
                <td><?= $row->Section; ?></td>
                <td><?= $stud->BirthDate; ?></td>
                <td><?= $cbmi->sbfp_date; ?></td>
                <td><?= $cbmi->y_mo; ?></td>
                <td><?= $row->weight; ?></td>
                <td></td>
                <td></td>
                <td><?= $row->age; ?></td>
                <td></td>
                <td><?= $row->bmi; ?></td>
                <td><?= $row->nut_stat; ?></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <?php } ?>
        </table>

        <div class="pre" style="float:left; margin-right:20%">
            <p>Prepared by: </p><br /><br />
            <p><b>Name of the person</b></p>
            <p>Feeding Focal Person</p>
        </div>

        <div class="pre" style="float:left;">
            <p>Prepared by: </p><br /><br />
            <p><b>Name of the person</b></p>
            <p>School Head</p>
        </div>
        <div class="blocker"></div>

        <p style="padding-top:50px">Note: This form shall be prepared by the school before the start of feeding to be compiled by the SDO.</p>
        <p>Keep columns 6-12 blank if nutritional assesment is still suspended.</p>

    </div>







    
</body>
</html>