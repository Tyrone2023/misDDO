<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ASSIGNMENT ORDER</title>
    <meta content="Coderthemes" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="<?= base_url(); ?>assets/images/hris.ico">
    <link href="<?= base_url(); ?>assets/css/ren.css" rel="stylesheet" type="text/css" />
</head>
<body>

    <div class="wrap">
        <div class="heading">
            <img class="imgLeft" src="<?= base_url(); ?>assets/images/report/ke.png" alt="">
            <img class="imgRight"  src="<?= base_url(); ?>assets/images/report/davor.png" alt="">
            <p>
                <span class="one">Republic of the Philippines</span>
                <span class="two">DEPARTMENT OF EDUCATION</span>
                <span class="three">Region XI</span>																								
                <span class="four">SCHOOLS DIVISION OF DAVAO ORIENTAL</span>																								
                <span class="five">Government Center, Barangay Dahican,</span>																								
                <span class="five">City of Mati, Davao Oriental</span>	
            </p>
            
        </div>
        <?php $school = $this->Common->one_cond_row('schools','schoolID',$staff->schoolID); ?>

        <p class="hname">
            <span class="hone"><b><?= strtoupper($staff->FirstName); ?> <?= strtoupper($staff->MiddleName); ?> <?= strtoupper($staff->LastName); ?></b></span>
            <span class="htwo">Appointee</span>
            <span class="htwo">SCHOOLS DIVISION OF DAVAO ORIENTAL</span>
        </p>
        <div class="blocker"></div>
        <p class="date">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </p>
        <div class="blocker"></div>


        <p class="title">ASSIGNMENT ORDER</p>
        <div class="content">
            <p style="text-indent: 50px; line-height:1.7em; text-align:justify">
                In view of the approval of your appointment as <span style="text-decoration: underline; font-weight:bold;"> &nbsp; <?= strtoupper($staff->empPosition); ?> &nbsp; - &nbsp; PERMANENT &nbsp; </span> in   the   Schools   Division  of   Davao   Oriental,  you   are   hereby  advised    of   your    assignment   at <span style="text-decoration: underline; font-weight:bold;">&nbsp; <?= strtoupper($school->schoolName); ?>&nbsp; </span> - <span style="text-decoration: underline; font-weight:bold;">&nbsp; <?= strtoupper($school->district); ?>&nbsp; </span> to    perform   the    duties and   responsibilities   attached  to  your  position  and  such  other  related function  as  may be  assigned.
            </p>
            <p class="par" style="line-height:1.7em">
                It  is  understood   that  you  may  be  transferred/reassigned  anytime  to  other  school  within  the Schools Division where you are presently deployed or where your services are needed.
            </p>
        </div>
    
        <div class="blocker"></div>

        <div class="sign">
            <span>DR. JOSEPHINE L. FADUL</span>
            Schools Division Superintendent
        </div>

        <div class="blocker"></div>

        <div class="item">
            <p style="margin-bottom:50px">CONFORME:</p>
            <p class="con">
                <span class="sname">&nbsp; <?= strtoupper($staff->FirstName); ?> <?= strtoupper($staff->MiddleName); ?> <?= strtoupper($staff->LastName); ?> &nbsp; </span>
                <span class="spos"><?= strtoupper($staff->empPosition); ?></span>
            </p>
        </div>
    
    </div>




    
</body>
</html>