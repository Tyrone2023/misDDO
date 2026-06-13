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
            body {
            font-family: Arial, sans-serif;
            font-size: 7pt;
            color: black;
            line-height: 1.5;
            }
            .sbfp_header{
                margin:0 !important;
                padding:0 !important;
            }
            .bs{
                font-size: 12pt; 
                margin-top:10px;
                margin-bottom:15px;
            }
        }
    </style>
</head>
<body>

<?php 
    function categorizeValue($value) {
        if ($value <= 12.4) {
            return "Severely Wasted";
        } elseif ($value >= 12.5 && $value < 13.5) {
            return "Wasted";
        } elseif ($value >= 13.5 && $value < 20.5) {
            return "Normal";
        } elseif ($value >= 20.5 && $value < 24.3) {
            return "Overweight";
        } elseif ($value >= 24.3) {
            return "Obese";
        } else {
            return "Invalid input";
        }
    }
?>

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
            

            <h1 class="bs">Department of Education<br />School Form 8 learner's Basic Health and Nutrition Report (SF8)</h1>
        </div>



        <table class="sbfp">
            <tr>
                <td rowspan="2">No.</td>
                <td rowspan="2">LRN</td>
                <td rowspan="2">Learner's Name <br />(Last Name, First Name, Name <br />Extension, Middle Name)</td>
                <td rowspan="2">Birthdate<br />(MM/DD/YYYY)</td>
                <td rowspan="2">Age</td>
                <td rowspan="2">Weight<br />(kg)</td>
                <td rowspan="2">Height <br />(m)</td>
                <td rowspan="2">Height<sup>2</sup> <br />(m<sup>2</sup>)</td>
                <td colspan="2">Nutritional Status</td>
                <td rowspan="2">Height for <br />Age(HFA)</td>
                <td rowspan="2">Remarks</td>
            </tr>
            <tr>
                <td>BMI<br />(kg/m<sup>2</sup>)</td>
                <td>BMI <br />Category</td>
            </tr>
            <tr style="background:#b1b1b1">
                <td colspan="2">MALE</td>
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
            <?php 
                $ren=1;
                foreach($sbfp as $row){
                //$stud = $this->Common->one_cond_row('studeprofile', 'StudentNumber', $row->StudentNumber);
                if($row->Sex == 'Male'){
                    $hm2 = $row->height*$row->height;
            ?>
            <tr>
                <td><?= $ren++; ?></td>
                <td><?= $row->LRN; ?></td>
                <td class="text-left"><?= $row->LastName; ?><?= empty($row->FirstName) ? '' : ', '.$row->FirstName; ?><?= empty($row->nameExt) ? '' : ', '.$row->nameExt.', '; ?> <?= $row->MiddleName; ?> </td>
                <td><?= $row->BirthDate; ?></td>
                <td><?= $row->age; ?></td>
                <td><?= $row->weight; ?></td>
                <td><?= $row->height; ?></td>
                <td><?= $hm2; ?></td>
                <td><?php $result = ($hm2 != 0) ? $row->weight / $hm2 : "0";  ?> <?= $result; ?></td>
                <td><?php // categorizeValue($result); ?> <?= $row->bmi; ?></td>
                <td></td>
                <td></td>
            </tr>
            <?php } } ?>
            <tr style="background:#b1b1b1">
                <td colspan="2">FEMALE</td>
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
            <?php 
                $ren=1;
                foreach($sbfp as $row){
                //$stud = $this->Common->one_cond_row('studeprofile', 'StudentNumber', $row->StudentNumber);
                if($row->Sex == 'Female'){
                $hm2 = $row->height*$row->height;
            ?>
            <tr>
                <td><?= $ren++; ?></td>
                <td><?= $row->LRN; ?></td>
                <td class="text-left"><?= $row->LastName; ?><?= empty($row->FirstName) ? '' : ', '.$row->FirstName; ?><?= empty($row->nameExt) ? '' : ', '.$row->nameExt.', '; ?> <?= $row->MiddleName; ?> </td>
                <td><?= $row->BirthDate; ?></td>
                <td><?= $row->age; ?></td>
                <td><?= $row->weight; ?></td>
                <td><?= $row->height; ?></td>
                <td><?php if($hm2 != 0){echo number_format($hm2, 2);}else{echo $hm2;} ?></td>
                <td><?php $result = ($hm2 != 0) ? $row->weight / $hm2 : "0";  ?> <?php if($result != 0){echo number_format($result, 2);}else{echo $result;} ?></td>
                <td><?php // categorizeValue($result); ?> <?= $row->bmi; ?></td>
                <td></td>
                <td></td>
            </tr>
            <?php } } ?>
        </table>

        <br /><br />
        <h1 class="text-center">SUMMARY TABLE</h1>

        <?php 
            $sw = $this->Common->two_join_two_cond_count('semesterstude', 'studeprofile', '*', 'a.StudentNumber=b.StudentNumber', 'a.bmi', 'Severely Wasted', 'b.Sex', 'Male');
            $wasted = $this->Common->two_join_two_cond_count('semesterstude', 'studeprofile', '*', 'a.StudentNumber=b.StudentNumber', 'a.bmi', 'Wasted', 'b.Sex', 'Male');
            $normal = $this->Common->two_join_two_cond_count('semesterstude', 'studeprofile', '*', 'a.StudentNumber=b.StudentNumber', 'a.bmi', 'Normal', 'b.Sex', 'Male');
            $ow = $this->Common->two_join_two_cond_count('semesterstude', 'studeprofile', '*', 'a.StudentNumber=b.StudentNumber', 'a.bmi', 'Overweight', 'b.Sex', 'Male');
            $obese = $this->Common->two_join_two_cond_count('semesterstude', 'studeprofile', '*', 'a.StudentNumber=b.StudentNumber', 'a.bmi', 'Obese', 'b.Sex', 'Male');

            $fsw = $this->Common->two_join_two_cond_count('semesterstude', 'studeprofile', '*', 'a.StudentNumber=b.StudentNumber', 'a.bmi', 'Severely Wasted', 'b.Sex', 'Female');
            $fwasted = $this->Common->two_join_two_cond_count('semesterstude', 'studeprofile', '*', 'a.StudentNumber=b.StudentNumber', 'a.bmi', 'Wasted', 'b.Sex', 'Female');
            $fnormal = $this->Common->two_join_two_cond_count('semesterstude', 'studeprofile', '*', 'a.StudentNumber=b.StudentNumber', 'a.bmi', 'Normal', 'b.Sex', 'Female');
            $fow = $this->Common->two_join_two_cond_count('semesterstude', 'studeprofile', '*', 'a.StudentNumber=b.StudentNumber', 'a.bmi', 'Overweight', 'b.Sex', 'Female');
            $fobese = $this->Common->two_join_two_cond_count('semesterstude', 'studeprofile', '*', 'a.StudentNumber=b.StudentNumber', 'a.bmi', 'Obese', 'b.Sex', 'Female');

        ?>

        <table class="sbfp">
            <tr>
                <td rowspan="2">SEX</td>
                <td colspan="6">Nutritional Status</td>
                <td colspan="5">Height for Age</td>
            </tr>
            <tr>
                <td>Severely Wasted</td>
                <td>Wasted</td>
                <td>Normal</td>
                <td>Overweight</td>
                <td>Obese</td>
                <td>TOTAL</td>
                <td>Severely Stunted</td>
                <td>Stunted</td>
                <td>Normal</td>
                <td>Tall</td>
                <td>Total</td>
            </tr>
            <tr>
                <td>MALE</td>
                <td><?= $sw->num_rows(); ?></td>
                <td><?= $wasted->num_rows(); ?></td>
                <td><?= $normal->num_rows(); ?></td>
                <td><?= $ow->num_rows(); ?></td>
                <td><?= $obese->num_rows(); ?></td>
                <td><?= $sw->num_rows()+$wasted->num_rows()+$normal->num_rows()+$ow->num_rows()+$obese->num_rows() ?></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>FEMALE</td>
                <td><?= $fsw->num_rows(); ?></td>
                <td><?= $fwasted->num_rows(); ?></td>
                <td><?= $fnormal->num_rows(); ?></td>
                <td><?= $fow->num_rows(); ?></td>
                <td><?= $fobese->num_rows(); ?></td>
                <td><?= $fsw->num_rows()+$fwasted->num_rows()+$fnormal->num_rows()+$fow->num_rows()+$fobese->num_rows() ?></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>TOTAL</td>
                <td><?php $swt = $sw->num_rows()+$fsw->num_rows(); echo $swt; ?></td>
                <td><?php $wastedt = $wasted->num_rows()+$fwasted->num_rows(); echo $wastedt; ?></td>
                <td><?php $nt =  $normal->num_rows()+$fnormal->num_rows(); echo $nt; ?></td>
                <td><?php $owt = $ow->num_rows()+$fow->num_rows(); echo $owt; ?></td>
                <td><?php $obeset =  $obese->num_rows()+$fobese->num_rows(); echo $obeset; ?></td>
                <td><?= $swt+$wastedt+$nt+$owt+$obeset; ?></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            
        </table>

    </div>







    
</body>
</html>