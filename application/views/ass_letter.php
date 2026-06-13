<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ASSIGNMENT LETTER</title>
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
            <span class="hone">THE SCHOOL PRINCIPAL/HEAD TEACHER/OIC</span>
            <span class="htwo">Spur-2 IS</span>
            <span class="htwo">Cateel II District</span>
            <span class="htwo">Davao Oriental</span>
        </p>
        <div class="blocker"></div>
        <p class="date">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </p>
        <div class="blocker"></div>


        <p class="dear">Dear Sir/Madam:</p>
        <div class="content">
            <p style="text-indent: 50px;">
                This will be presented by <span class="name">&nbsp; <?= strtoupper($staff->FirstName); ?> <?= strtoupper($staff->MiddleName); ?> <?= strtoupper($staff->LastName); ?> &nbsp;</span> assigned  in
                your school as <span class="name">&nbsp; <?= strtoupper($school->schoolName); ?>&nbsp; </span> vice
            </p>
            <p class="par">
                All  the  pertinent papers  necessary  for the issuance of his/her appointment have already 
                been submitted to this office.  In order however,  that his/her  appointment  can be  submitted to the
                authorities  concerned  thereby  issuing the  immediate  release  of his/her salary, it is desired that
                the report on  his/her  first day of actual service   be sent to this office by the quickest means available.
            </p>
        </div>
    
        <div class="blocker"></div>

        <p class="vty">Very truly yours,</p>
        <div class="sign">
            <span>DR. JOSEPHINE L. FADUL</span>
            Schools Division Superintendent
        </div>

        <div class="blocker"></div>

        <div class="item">
            <p><span>Item No.: </span> <?= strtoupper($staff->itemNo); ?> </p>
            <p><span>Received by:</span> </p>
            <p><span>Date:</span> </p>
        </div>
    
    </div>




    
</body>
</html>