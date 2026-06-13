<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8" />
        <?php include('includes/page-title.php'); ?>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="Responsive bootstrap 4 admin template" name="description" />
        <meta content="Coderthemes" name="author" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <!-- App favicon -->
        <link rel="shortcut icon" href="<?= base_url(); ?>assets/images/hris.ico">

        <!-- Plugins css-->
        <link href="<?= base_url(); ?>assets/css/renren.css" rel="stylesheet" type="text/css" />
        <link href="https://db.onlinewebfonts.com/a/0nH393RJctHgt1f2YvZvyruY" rel="stylesheet" type="text/css"/>
        <link href="https://db.onlinewebfonts.com/c/84ae358e627d67d90bd613fcedc20c10?family=Edwardian+Script+ITC" rel="stylesheet"> 
        <style>
            .qrwrap {
                position:relative;
                width:50px;
            }
            .qrwrap div{
                position:absolute;
                bottom:-100px;
            }
            .qrwrap div p{
                font-size:12px !important;
            }
           
            @page {
            size: A4;
            margin: 0;
            }
            @media print {
            html, body {
                width: 210mm;
                height: 297mm;
                font-size:14px !important;
            }
            .cert{
                width:90%;
                padding-top:1px;
            }
            .aip_generate .cert p{
                margin-bottom:20px;
                line-height: 1.1em;
            }
            .cafleft{
                padding-right:0 !important;
                font-size:12px !important;
            }
            .cafright{
                padding-left:0 !important;
                font-size:12px !important;
            }
            .signfwrap{
                margin-top:0;
            }

            .aip_generate .hr{
                margin:20px 0;
            }

            .ca{
                margin-bottom:10px;
                display:block;
            }

            .signers{
                top:20px
            }
            .signfads{
                top:-10px
            }
            
            
           
            }
        </style>
    
    </head>
    <?php $settings = $this->Common->one_cond_row_select('mis_settings','divAddress','settingsID',1); ?>



    <body class="aip_generate" id="printTable">

        <div class="cert">
            <img class="logo" src="<?= base_url(); ?>assets/images/report/ke.png" alt="">
            <p>
            <span class="rp">Republic of the Philippines</span><br />
                <span class="de">Department of Education</span><br />
                <span class="r">Region XI</span><br />
                <span class="r">Schools Division of <?php ucfirst($school->division); ?></span>
            </p>
            <div class="hr"></div>

            <p>Awards this</p>
            <p>
                <span class="ca" style="font-family: Edwardian Script ITC; font-size:50px">Certificate of Acceptance</span>  <br />
                electronic –Annual Implementation and Procurement Plan <br />
                for Regular MOOE
            </p><br />


            <p>
                <b><u><?= strtoupper($school->schoolName); ?></u></b><br />
                <?= ucfirst($school->brgy).', '.ucfirst($school->city); ?>, <?php ucfirst($school->division); ?><br />
                School ID <b><u><?= $school->schoolID; ?></u></b><br />
              <?= $aip->b_code; ?> - <?= $alloc->alloc_group; ?>
            </p>

            <?php $at = $this->SGODModel->one_cond_row('sgod_aip_track', 'submit_id', $aip->id); ?>

            <p class="text-justify" style="margin-bottom:10px; text-indent: 50px;">for having successfully complied with and met the requirements and standards set forth by the Department of Education, in accordance with DepEd Order 44, s.2015. Furthermore, the school demonstrated strict adherence to the implementing Guidelines on the Direct Release, Use, Monitoring and Reporting of Maintenance and Other Operating Expenses (MOOE) Allocation of Schools, as well as, the management of other funds, for the Fiscal Year <?= $alloc->alloc_year; ?>, as stipulated in Deped Order No. 8,s. 2019.</p>
            <p class="text-justify" style="text-indent: 50px">Given this <u> &nbsp; &nbsp; <?= date('d', strtotime($a->tdate)); ?>th &nbsp;&nbsp;</u>day of <u> &nbsp; &nbsp; <?= date('F', strtotime($a->tdate)); ?> &nbsp;&nbsp;</u>, <?= date('Y', strtotime($a->tdate)); ?>, at DepEd <?php ucfirst($school->division); ?>, <?= $settings->divAddress; ?>.</p>

        <div class="signfwrap">

            <div class="signleft">
                <p>Validated by</p>
                <p style="position:relative">
                    <img style="position:absolute; width:40px; top:-30px; left:60px" src="<?= base_url()?>assets/images/alan.png" alt="" class="sign"><br />
                    <b>ALAN D.LIMBADAN</b> <br />
                    SEPS-SMME <br />
                    SBM Coordinator
                </p>
            </div>
            
            <div class="signright">
                <p>Funds Available:</p>
                <p style="position:relative">
                    <img style="position:absolute; width:40px; top:-30px; left:60px" src="<?= base_url()?>assets/images/dens.png" alt="" class="sign"><br />
                    <b>DENNIS Y. BELARMINO, CPA</b> <br />
                    Accountant III <br />
                </p>
            </div>

            <div class="blocker"></div>

        </div>

        <div>
            <p class="signWrap">
                Recommending Approval:<br /><br />
                <img src="<?= base_url()?>assets/images/cabanes.png" alt="" class="sign signers"><br />
                <b>ERNESTO H. CABANES</b><br />
                Chief of Education Supervisor<br />
                School Governance and Operations Division<br />
            </p><br /><br />

            <div class="qrwrap">
                <div class="qr">
                    <img style="width:120px; float:left;" src="https://qrcode.tec-it.com/API/QRCode?data=<?= base_url(); ?>Page/generate_ca/<?= $aip->school_id.'/'.$aip->b_code.'/'.$aip->id; ?>" title="" />
                    <p>Scan to Verify</p>
                </div>
            </div>
            
            <p class="signWrap">
                Approved:<br /><br />
                <img src="<?= base_url()?>assets/images/fadul.png" alt="" class="sign signfads"><br />
                <b>DR. JOSEPHINE L. FADUL</b><br />
                Schools Division Superintendent<br />
            </p>
        </div>

        <div class="hr"></div>


        <div class="cafooter">
            <img width="100%" src="<?= base_url(); ?>assets/images/f.png" alt="">
            <div class="blocker"></div>
        </div>
        
        
        
        
        </div>




    </body>
</html>