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


    <div class="wrap">

        <div class="sbfp_header">
            <img class="logo" style="width:70px; height:70px;" src="<?= base_url(); ?>assets/images/report/ke.png" alt="">
            <p>
            <span class="rp">Republic of the Philippines</span><br />
                <span class="de">Department of Education</span><br />
                <span class="r">Region XI</span><br />
                <span class="r">School Division of Davao Oriental</span><br />
                <!-- <span class="r">School Division of Davao Oriental</span><br />
                 
            </p>
            

        </div>


        <br /><br />
        <h1 class="text-center">NUTRITIONAL STATUS SUMMARY</h1>

        <?php 
            $district = $this->Common->one_cond_row('district','id',$this->input->post('d_id'));
            $school = $this->Common->one_cond('schools','district',$district->discription);

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
            <?php foreach($school as $row){ ?>
            <tr>
                <td colspan="7" style="background:#60B5FF; padding:8px 0;"><?= $row->schoolName; ?></td>
            </tr>
            <tr>
                <td rowspan="2">SEX</td>
                <td colspan="6">Nutritional Status</td>
            </tr>
            <tr>
                <td>Severely Wasted</td>
                <td>Wasted</td>
                <td>Normal</td>
                <td>Overweight</td>
                <td>Obese</td>
                <td>TOTAL</td>
            </tr>
            <tr>
                <td>MALE</td>
                <td><?= $sw->num_rows(); ?></td>
                <td><?= $wasted->num_rows(); ?></td>
                <td><?= $normal->num_rows(); ?></td>
                <td><?= $ow->num_rows(); ?></td>
                <td><?= $obese->num_rows(); ?></td>
                <td><?= $sw->num_rows()+$wasted->num_rows()+$normal->num_rows()+$ow->num_rows()+$obese->num_rows() ?></td>
            </tr>
            <tr>
                <td>FEMALE</td>
                <td><?= $fsw->num_rows(); ?></td>
                <td><?= $fwasted->num_rows(); ?></td>
                <td><?= $fnormal->num_rows(); ?></td>
                <td><?= $fow->num_rows(); ?></td>
                <td><?= $fobese->num_rows(); ?></td>
                <td><?= $fsw->num_rows()+$fwasted->num_rows()+$fnormal->num_rows()+$fow->num_rows()+$fobese->num_rows() ?></td>
            </tr>
            <tr>
                <td>TOTAL</td>
                <td><?php $swt = $sw->num_rows()+$fsw->num_rows(); echo $swt; ?></td>
                <td><?php $wastedt = $wasted->num_rows()+$fwasted->num_rows(); echo $wastedt; ?></td>
                <td><?php $nt =  $normal->num_rows()+$fnormal->num_rows(); echo $nt; ?></td>
                <td><?php $owt = $ow->num_rows()+$fow->num_rows(); echo $owt; ?></td>
                <td><?php $obeset =  $obese->num_rows()+$fobese->num_rows(); echo $obeset; ?></td>
                <td><?= $swt+$wastedt+$nt+$owt+$obeset; ?></td>
            </tr>
            <?php } ?>

            

        </table>

    </div>







    
</body>
</html>