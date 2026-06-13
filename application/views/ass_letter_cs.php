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

    <div class="assWrap">


        <p class="for">For Regulated Agencies</p>
        <div class="blocker"></div>

        <div class="innerwrap">
            <div class="iwrap">
                <p class="cs">
                    CS Form No. 33-A
                    <span>Revised 2017</span>
                </p>
                <div class="assHearding">
                    <div class="heading" style="border:0">
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
                
            </div>
            <?php $school = $this->Common->one_cond_row('schools','schoolID',$staff->schoolID); ?>

            <div class="content1">
                <p style="margin-bottom:30px">Mr.: <span>IRENEO O. CRODUA JR.</span></p>
                <P style="text-indent: 50px;">You are hereby appointed as _________(SG/JG/PG____)</P>
                <P>under_________________Status at the _____________</P>
                <P style="margin-bottom:20px">(Permanent, Temporary, etc.)		(Office/Department/Unit)</P>

                <P style="text-indent: 50px;">with a compensation rate of ___________________________(P__________)</P>
                <P style="margin-bottom:20px">pesos per month.</P>

                <P style="text-indent: 50px;">The nature of this appointment is _____________________vice ____________</P>
                <P>____________________, who _______________ with Plantilla Item No._______</P>
                <P style="margin-bottom:20px">Page ______________.</P>

                <P style="text-indent: 50px;">This appointment shall take effect on the date of signing by the appointing officer/authority.</P>

            </div>


            </div>


            <div class="iwrap">
                

            <div class="content1">
                <p>CSC ACTION: </p>


                <P>Authorized Official</P>


            </div>


            </div>


            
        </div>



    </div>
    
    



    
</body>
</html>