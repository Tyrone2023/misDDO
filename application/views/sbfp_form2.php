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

        <table class="sbfp sbfpren sbfpf2">
            <tr>
                <td class="f2" rowspan="3">Number of<br /> Undernourished School<br /> Children by Grade Level</td>
                <td class="f2" colspan="12">Nutritional Status at Start/End of Feeding</td>
                <td class="f2" colspan="7" rowspan="2">Nutritional Status at Start/End of Feeding</td>
                <td class="f2" rowspan="3">No. of 4 Learners Dewormed</td>
                <td class="f2" rowspan="3">No. of 4 Ps Beneficiaries</td>
                <td class="f2" rowspan="3">Total Primary Beneficiary</td>
                <td class="f2" rowspan="3">Total Secondary Beneficiaries</td>
                <td class="f2" rowspan="3">Total Beneficiaries</td>
                <td class="f2" rowspan="3">Date Feeding Started/Ended</td>
            </tr>
            <tr>
                <td class="f2" colspan="6">BODY MASS INDEX</td>
                <td class="f2" colspan="6">HEIGHT FOR AGE</td>
            </tr>
            <tr>
                <td class="f2">Severely Wasted</td>
                <td class="f2">Wasted</td>
                <td class="f2">Normal</td>
                <td class="f2">OW</td>
                <td class="f2">O</td>
                <td class="f2">Total BMI</td>
                <td class="f2">Severely Stunted</td>
                <td class="f2">Stunted</td>
                <td class="f2">Normal</td>
                <td class="f2">Tall</td>
                <td class="f2">No HFA (19 Above)</td>
                <td class="f2">Total HFA</td>
                <td class="f2">No. of Pupils-at-risk-of-dropping-out (PARDOs)</td>
                <td class="f2">Stunted</td>
                <td class="f2">Severely Stunted</td>
                <td class="f2">No. of Indigent Learners</td>
                <td class="f2">No. of Indigenous Peoples (IPs)</td>
                <td class="f2">Other Kinder Learners</td>
                <td class="f2">other Learners in Schools with below 100 enrollees</td>
            </tr>
            <tr>
                <td class="f2">1.  Kinder</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="f2"></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="f2"></td>
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
                <td class="f2"></td>
                <td class="f2"></td>
                <td class="f2"></td>
            </tr>
            <tr>
                <td class="f2"></td>
                <td colspan="5" style="color:red; font-size:8px; text-align:left">Include this in copying</td>
                <td class="f2"></td>
                <td colspan="5" style="color:red; font-size:8px; text-align:left">Include this in copying</td>
                <td class="f2"></td>
                <td colspan="10" style="color:red; font-size:8px; text-align:left">Include this in copying</td>
                <td class="f2"></td>
                <td class="f2"></td>
                <td class="f2"></td>
            </tr>
            
            <tr>
                <td class="f2">Total</td>
                <td class="f2"></td>
                <td class="f2"></td>
                <td class="f2"></td>
                <td class="f2"></td>
                <td class="f2"></td>
                <td class="f2"></td>
                <td class="f2"></td>
                <td class="f2"></td>
                <td class="f2"></td>
                <td class="f2"></td>
                <td class="f2"></td>
                <td class="f2"></td>
                <td class="f2"></td>
                <td class="f2"></td>
                <td class="f2"></td>
                <td class="f2"></td>
                <td class="f2"></td>
                <td class="f2"></td>
                <td class="f2"></td>
                <td class="f2"></td>
                <td class="f2"></td>
                <td class="f2"></td>
                <td class="f2"></td>
                <td class="f2"></td>
                <td class="f2"></td>
            </tr>
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

        <p style="padding-top:50px">Note:  This form shall be prepared by the school before the start of feeding and after feeding, to be compiled by the SDO, and for final compilation by the RO, for submission to  DepEd BLSS-SHD</p>
        <p style="color:red; font-size:12px">RED FONT IN TOTAL BENEFICIARIES MEANS THE NUMBER OF LEARNERS DO NOT COINCIDE WITH THE NUMBER OF LEARNERS IN THE NUTRITIONAL STATUS</p>
        <p style="font-size:12px">TOTAL OF BMI AND TOTAL OF HFA MUST BE THE SAME</p>
    </div>







    
</body>
</html>