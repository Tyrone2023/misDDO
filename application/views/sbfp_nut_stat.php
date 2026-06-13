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
            <tr>
                <td>FEMALE</td>
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
            <tr>
                <td>TOTAL</td>
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
            
        </table>

    </div>







    
</body>
</html>