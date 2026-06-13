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
            

            <h1 class="bs">BASELINE STATUS OF SBFP BENEFICIARIES</h1>
            <h4>SY DATE</h4>
        </div>

        <table class="sbfp">
            <tr>
                <th rowspan="2">Names</th>
                <th rowspan="2">Birthday mm/dd/yy</th>
                <th rowspan="2">Weight (kg)</th>
                <th rowspan="2">Height (meters)</th>
                <th rowspan="2">Sex</th>
                <th rowspan="2">Height<sup>2</sup> <br />(m<sup>2</sup>)</th>
                <th>Age</th>
                <th rowspan="2">Body <br />Mass <br />Index</th>
                <th rowspan="2">Nutritional Status</th>
                <th rowspan="2">Height-For-Age</th>
            </tr>
            <tr>
                <th>y , m</th>
            </tr>
            <?php foreach($sbf as $row){?>
            <tr>
                <td class="text-left"><?= $row->LastName; ?>, <?= $row->FirstName; ?> <?= !empty($row->MiddleName) ? substr($row->MiddleName,0,1).'.' : ''; ?></td>
                <td><?= $row->BirthDate; ?></td>
                <td><?= $row->weight; ?></td>
                <td><?= $row->height; ?></td>
                <td><?= $row->Sex; ?></td>
                <td><?= $row->height*$row->height; ?></td>
                <td><?= $row->Age; ?></td>
                <td><?= $row->bmi; ?></td>
                <td><?= $row->bmi_eqv; ?></td>
                <td></td>
            </tr>
            <?php } ?>
        </table>

        <table class="sbfp2nd">
            <tr>
                <th style="background:#ffef40">Body Mass Index</th>
                <th style="background:#ffef40">M</th>
                <th style="background:#ffef40">F</th>
                <th style="background:#ffef40">T</th>
                <th style="background:#fbb91a">HFA</th>
                <th style="background:#fbb91a">M</th>
                <th style="background:#fbb91a">F</th>
                <th style="background:#fbb91a">TOTAL</th>
            </tr>
            <?php 
                $school = $this->Common->one_cond_row('schools','schoolID',$this->session->username);
                
                $sw = $this->Common->two_join_seven_cond_count('semester_sbfp', 'studeprofile', '*', 'a.StudentNumber=b.StudentNumber', 'a.schoolID', $this->session->c_id, 'a.SY', $this->session->bmi_sy, 'a.Section', $this->session->bmi_Section, 'a.YearLevel', $this->session->bmi_YearLevel, 'a.w_group', 'Baseline', 'a.bmi_eqv', 'Severely Wasted', 'b.Sex', 'Male');
                $wasted = $this->Common->two_join_seven_cond_count('semester_sbfp', 'studeprofile', '*', 'a.StudentNumber=b.StudentNumber', 'a.schoolID', $this->session->c_id, 'a.SY', $this->session->bmi_sy, 'a.Section', $this->session->bmi_Section, 'a.YearLevel', $this->session->bmi_YearLevel, 'a.w_group', 'Baseline', 'a.bmi_eqv', 'Wasted', 'b.Sex', 'Male');
                $normal = $this->Common->two_join_seven_cond_count('semester_sbfp', 'studeprofile', '*', 'a.StudentNumber=b.StudentNumber', 'a.schoolID', $this->session->c_id, 'a.SY', $this->session->bmi_sy, 'a.Section', $this->session->bmi_Section, 'a.YearLevel', $this->session->bmi_YearLevel, 'a.w_group', 'Baseline', 'a.bmi_eqv', 'Normal', 'b.Sex', 'Male');
                $ow = $this->Common->two_join_seven_cond_count('semester_sbfp', 'studeprofile', '*', 'a.StudentNumber=b.StudentNumber', 'a.schoolID', $this->session->c_id, 'a.SY', $this->session->bmi_sy, 'a.Section', $this->session->bmi_Section, 'a.YearLevel', $this->session->bmi_YearLevel, 'a.w_group', 'Baseline', 'a.bmi_eqv', 'Overweight', 'b.Sex', 'Male');
                $obese = $this->Common->two_join_seven_cond_count('semester_sbfp', 'studeprofile', '*', 'a.StudentNumber=b.StudentNumber', 'a.schoolID', $this->session->c_id, 'a.SY', $this->session->bmi_sy, 'a.Section', $this->session->bmi_Section, 'a.YearLevel', $this->session->bmi_YearLevel, 'a.w_group', 'Baseline', 'a.bmi_eqv', 'Obese', 'b.Sex', 'Male');

                $fsw = $this->Common->two_join_seven_cond_count('semester_sbfp', 'studeprofile', '*', 'a.StudentNumber=b.StudentNumber', 'a.schoolID', $this->session->c_id, 'a.SY', $this->session->bmi_sy, 'a.Section', $this->session->bmi_Section, 'a.YearLevel', $this->session->bmi_YearLevel, 'a.w_group', 'Baseline', 'a.bmi_eqv', 'Severely Wasted', 'b.Sex', 'Female');
                $fwasted = $this->Common->two_join_seven_cond_count('semester_sbfp', 'studeprofile', '*', 'a.StudentNumber=b.StudentNumber', 'a.schoolID', $this->session->c_id, 'a.SY', $this->session->bmi_sy, 'a.Section', $this->session->bmi_Section, 'a.YearLevel', $this->session->bmi_YearLevel, 'a.w_group', 'Baseline', 'a.bmi_eqv', 'Wasted', 'b.Sex', 'Female');
                $fnormal = $this->Common->two_join_seven_cond_count('semester_sbfp', 'studeprofile', '*', 'a.StudentNumber=b.StudentNumber', 'a.schoolID', $this->session->c_id, 'a.SY', $this->session->bmi_sy, 'a.Section', $this->session->bmi_Section, 'a.YearLevel', $this->session->bmi_YearLevel, 'a.w_group', 'Baseline', 'a.bmi_eqv', 'Normal', 'b.Sex', 'Female');
                $fow = $this->Common->two_join_seven_cond_count('semester_sbfp', 'studeprofile', '*', 'a.StudentNumber=b.StudentNumber', 'a.schoolID', $this->session->c_id, 'a.SY', $this->session->bmi_sy, 'a.Section', $this->session->bmi_Section, 'a.YearLevel', $this->session->bmi_YearLevel, 'a.w_group', 'Baseline', 'a.bmi_eqv', 'Overweight', 'b.Sex', 'Female');
                $fobese = $this->Common->two_join_seven_cond_count('semester_sbfp', 'studeprofile', '*', 'a.StudentNumber=b.StudentNumber', 'a.schoolID', $this->session->c_id, 'a.SY', $this->session->bmi_sy, 'a.Section', $this->session->bmi_Section, 'a.YearLevel', $this->session->bmi_YearLevel, 'a.w_group', 'Baseline', 'a.bmi_eqv', 'Obese', 'b.Sex', 'Female');

            
            ?>
            <tr>
                <td>No. of Cases</td>
                <td></td>
                <td></td>
                <td></td>
                <td>No. of Cases</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>Severely Wasted</td>
                <td><?= $sw->num_rows(); ?></td>
                <td><?= $fsw->num_rows(); ?></td>
                <td><?= $sw->num_rows() + $fsw->num_rows(); ?></td>
                <td>Sev. Stunted</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>Wasted</td>
                <td><?= $wasted->num_rows(); ?></td>
                <td><?= $fwasted->num_rows(); ?></td>
                <td><?= $wasted->num_rows() + $fwasted->num_rows(); ?></td>
                <td>Stuned</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>Normal</td>
                <td><?= $normal->num_rows(); ?></td>
                <td><?= $fnormal->num_rows(); ?></td>
                <td><?= $normal->num_rows() + $fnormal->num_rows(); ?></td>
                <td>Normal</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>Overweight</td>
                <td><?= $ow->num_rows(); ?></td>
                <td><?= $fow->num_rows(); ?></td>
                <td><?= $ow->num_rows() + $fow->num_rows(); ?></td>
                <td>Tall</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>Obese</td>
                <td><?= $obese->num_rows(); ?></td>
                <td><?= $fobese->num_rows(); ?></td>
                <td><?= $obese->num_rows() + $fobese->num_rows(); ?></td>
                <td>No HFA</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </table>
        <p style="text-align:center;">Body Mass Index=Weight(Kgs) Height squred(m2)</p><br /><br />

        <div class="pre">
            <p>Prepared by: </p><br /><br />
            <p><b>Name of the person</b></p>
            <p>SBFP COORDINATOR</p>
        </div>

    </div>







    
</body>
</html>