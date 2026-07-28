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
         <!-- Same type family the SGOD/IPCRF workspace uses; falls back to Segoe UI offline -->
         <link href="https://fonts.googleapis.com/css?family=Lato:400,700|Montserrat:600,700,800&display=swap" rel="stylesheet" type="text/css" />
         <style>
            /* =====================================================================
               SMEA summary — same design language as the SGOD IPCRF workspace
               (sgod/assets/css/ipcrf.css) and the quarterly SMEA report.

                 navy #172554 / #273856 · blue #3157c8 · ink #24324a
                 muted #6b7890 · hairline #dfe6f2 · page #eef1f6 · tint #d7e2f3
               ===================================================================== */

            html, body{ margin:0; padding:0; }

            body.aip_generate{
                background:#eef1f6;
                font-family:'Lato', 'Segoe UI', Calibri, Arial, sans-serif;
                color:#24324a;
                -webkit-font-smoothing:antialiased;
                padding:0 0 40px;
            }

            /* ===== Document surface ===== */
            .smea-doc{
                background:#ffffff;
                border:1px solid #d7deea;
                border-radius:6px;
                box-shadow:0 5px 24px rgba(30,43,69,.10);
                margin:14px auto 0;
                max-width:1760px;
                padding:22px 24px 26px;
                text-align:center;
            }

            /* ===== Masthead ===== */
            .smea-masthead{
                display:flex;
                align-items:flex-end;
                justify-content:space-between;
                gap:16px;
                text-align:left;
                border-bottom:1px solid #dfe6f2;
                padding-bottom:14px;
                margin-bottom:16px;
            }
            .smea-kicker{
                display:block;
                color:#3157c8;
                font-family:'Montserrat', 'Segoe UI', Arial, sans-serif;
                font-size:11px;
                font-weight:800;
                letter-spacing:.12em;
                text-transform:uppercase;
                margin-bottom:5px;
            }
            .smea-masthead h1{
                margin:0 0 4px;
                color:#172554;
                font-family:Georgia, 'Times New Roman', serif;
                font-size:26px;
                font-weight:800;
                letter-spacing:-.01em;
                line-height:1.2;
                text-align:left;
            }
            .smea-masthead p{
                margin:0;
                color:#6b7890;
                font-size:13px;
                line-height:1.45;
            }
            .smea-status{
                display:inline-flex;
                align-items:center;
                gap:7px;
                flex:none;
                background:#eef3ff;
                border:1px solid #cad7ff;
                border-radius:999px;
                color:#3157c8;
                font-family:'Montserrat', 'Segoe UI', Arial, sans-serif;
                font-size:12px;
                font-weight:800;
                padding:8px 14px;
                white-space:nowrap;
            }
            .smea-status:before{
                content:'';
                background:currentColor;
                border-radius:50%;
                height:7px;
                width:7px;
            }

            /* ===== Meta strip ===== */
            .smea-meta{
                display:grid;
                grid-template-columns:repeat(auto-fit, minmax(150px, 1fr));
                gap:10px;
                text-align:left;
                margin-bottom:16px;
            }
            .smea-meta-item{
                background:#f8faff;
                border:1px solid #e5ebf6;
                border-radius:10px;
                padding:9px 12px;
            }
            .smea-meta-item span{
                display:block;
                color:#6b7890;
                font-size:10px;
                font-weight:800;
                letter-spacing:.06em;
                text-transform:uppercase;
                margin-bottom:3px;
            }
            .smea-meta-item strong{
                display:block;
                color:#24324a;
                font-size:13px;
                font-weight:700;
                line-height:1.3;
                overflow-wrap:anywhere;
            }

            /* ===== Sticky toolbar: quarter filter + print ===== */
            .smea-toolbar{
                display:flex;
                align-items:center;
                flex-wrap:wrap;
                gap:8px;
                background:rgba(255,255,255,.97);
                border:1px solid #dfe6f2;
                border-radius:11px;
                box-shadow:0 7px 22px rgba(38,55,93,.08);
                padding:9px 12px;
                margin-bottom:16px;
                position:sticky;
                top:10px;
                z-index:90;
                text-align:left;
            }
            .smea-toolbar .toolbar-spacer{ flex:1 1 auto; }
            .smea-toolbar-label{
                color:#58647a;
                font-family:'Montserrat', 'Segoe UI', Arial, sans-serif;
                font-size:11px;
                font-weight:800;
                letter-spacing:.05em;
                text-transform:uppercase;
                margin-right:2px;
            }
            .smea-btn{
                display:inline-flex;
                align-items:center;
                gap:6px;
                border-radius:7px;
                border:1px solid transparent;
                font-family:'Montserrat', 'Segoe UI', Arial, sans-serif;
                font-size:12px;
                font-weight:700;
                padding:8px 14px;
                cursor:pointer;
                text-decoration:none;
                white-space:nowrap;
            }
            .smea-qbtn{
                background:#ffffff;
                border-color:#c3ceda;
                color:#3f4a5c;
                min-width:46px;
                justify-content:center;
            }
            .smea-qbtn:hover{ background:#eef3ff; border-color:#9db0e8; color:#234aa8; }
            .smea-qbtn.is-active{
                background:#e8eeff;
                border-color:#9db0e8;
                color:#234aa8;
                box-shadow:inset 0 0 0 1px #9db0e8;
            }
            .smea-print-btn{
                background:#3157c8;
                border-color:#3157c8;
                color:#ffffff;
                box-shadow:0 2px 6px rgba(49,87,200,.25);
            }
            .smea-print-btn:hover{ background:#234aa8; border-color:#234aa8; }

            /* ===== Quarter section heads (IPCRF document section head) ===== */
            .smea-quarter{ margin-top:22px; }
            .aip_generate .smea-quarter h3{
                background:#d7e2f3;
                border:1px solid #b6c6df;
                border-left:4px solid #273856;
                border-radius:3px;
                color:#16294d;
                font-family:Georgia, 'Times New Roman', serif;
                font-size:13px;
                font-weight:800;
                letter-spacing:.025em;
                margin:0;
                padding:8px 12px;
                text-align:left;
                text-transform:uppercase;
            }

            /* ===== Data table ===== */
            .ivan{
                background-color:#eceef1 !important;
                color:#24324a !important;
            }
            .ivy{
                background-color:#dbe6fa !important;
                color:#1f3d7a !important;
            }
            .ivy:hover {
                background-color:#c7d8f5 !important;
            }

            .aip_generate table{
                width:100%;
                table-layout:fixed;          /* share width across all columns so nothing overflows */
                border-collapse:collapse;
                font-size:11px;
                margin-top:8px;
                margin-bottom:0;
            }
            .aip_generate table th,
            .aip_generate table td{
                border:1px solid #9aa3b2;
                padding:5px 4px;
                word-wrap:break-word;
                overflow-wrap:break-word;
                hyphens:auto;
                vertical-align:middle;
            }
            .aip_generate table th{
                background-color:#273856 !important;   /* overrides the shared teal in renren.css */
                color:#ffffff !important;
                font-family:'Montserrat', 'Segoe UI', Arial, sans-serif;
                font-size:10px;
                font-weight:700;
                line-height:1.25;
                text-transform:uppercase;
                letter-spacing:.04em;
            }
            .aip_generate table td{
                background-color:#ffffff;
                color:#24324a;
            }
            .aip_generate tbody tr:nth-child(even) td{ background-color:#f4f7fc; }
            .aip_generate tbody tr:hover td{ background-color:#e8eefb; }

            /* ===== Report footer ===== */
            .aip_generate .fcon{
                border-top:1px solid #dfe6f2;
                margin-top:26px;
                padding-top:14px;
                text-align:left;
            }
            .aip_generate .fcon .lcon{
                color:#6b7890;
                font-size:11.5px;
                line-height:1.5;
            }

            .smea-empty{
                color:#6b7890;
                font-size:13px;
                margin:24px 0;
            }

            /* DepEd letterhead: printed sheets only */
            .print-letterhead{ display:none; }

            @media (max-width:1100px){
                .smea-doc{ margin:0; border-radius:0; border-left:0; border-right:0; padding:16px 14px 20px; }
                .smea-masthead{ flex-direction:column; align-items:flex-start; }
            }
            @media (max-width:900px){
                .aip_generate table{ font-size:9px; }
                .aip_generate table th{ font-size:8px; }
                .aip_generate table th,
                .aip_generate table td{ padding:3px 2px; }
                .smea-masthead h1{ font-size:21px; }
            }

            /* ===== Print: Folio (F4) landscape, fit all columns across the width ===== */
            @page{
                size:330mm 210mm;   /* Folio / F4 in landscape */
                margin:8mm;
            }
            @media print{
                html, body{ width:330mm; }
                body.aip_generate{ background:#fff; padding:0; }
                .aip_generate{ padding:0; }
                .smea-doc{
                    background:#fff;
                    border:0;
                    border-radius:0;
                    box-shadow:none;
                    margin:0;
                    max-width:none;
                    padding:0;
                }
                /* Printed sheets carry a centred masthead under the letterhead */
                .smea-masthead{
                    display:block;
                    text-align:center;
                    border-bottom:.8px solid #111;
                    padding-bottom:2mm;
                    margin-bottom:2mm;
                }
                .smea-masthead > div{ text-align:center; }
                .smea-masthead h1{ color:#111; font-size:13pt; text-align:center; }
                .smea-kicker{ color:#111; font-size:7pt; }
                .smea-masthead p{ display:none; }
                .smea-status{
                    background:transparent !important;
                    border:.8px solid #111;
                    border-radius:2px;
                    color:#111 !important;
                    font-size:8pt;
                    margin-top:1.5mm;
                }
                .smea-status:before{ display:none; }
                .smea-meta{ gap:0; grid-template-columns:repeat(5, 1fr); margin-bottom:3mm; }
                .smea-meta-item{
                    background:transparent !important;
                    border:.65px solid #111;
                    border-radius:0;
                    padding:1.2mm 2mm;
                }
                .smea-meta-item span{ color:#111; font-size:6pt; }
                .smea-meta-item strong{ color:#111; font-size:8pt; }
                .aip_generate .smea-quarter h3{
                    background:transparent !important;
                    border:.65px solid #111;
                    border-left:1.2mm solid #111;
                    color:#000 !important;
                    page-break-after:avoid;   /* never strand a quarter heading at the foot of a sheet */
                    break-after:avoid;
                }
                /* Quarters flow one after another to fill the sheet; a quarter is only
                   pushed to the next page when it cannot fit whole on the current one. */
                .smea-quarter{
                    margin-top:4mm;
                    page-break-inside:avoid;
                    break-inside:avoid;
                }
                .aip_generate table{ font-size:9px; page-break-inside:auto; }
                .aip_generate table th{ font-size:8px; }
                .aip_generate table th,
                .aip_generate table td{ padding:3px 3px; }
                .aip_generate tr{ page-break-inside:avoid; }
                /* No background colours when printing — plain white cells, black text, borders kept */
                .aip_generate table th,
                .aip_generate table td,
                .aip_generate tbody tr:nth-child(even) td,
                .aip_generate tbody tr:hover td,
                .ivan, .ivy, .ivy:hover{
                    background:transparent !important;
                    background-color:transparent !important;
                    color:#000 !important;
                }
                .aip_generate .fcon{ border-top:.65px solid #111; }
                .aip_generate .fcon .lcon{ color:#000; }
                .back-to-top, .smea-toolbar{ display:none !important; }

                /* DepEd letterhead at the top of the printed report */
                .print-letterhead{
                    display:block;
                    width:80mm;
                    height:auto;
                    margin:0 auto 5mm;
                }
            }

            .back-to-top {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #273856;
            color: white;
            border: none;
            padding: 10px 18px;
            font-family:'Montserrat', 'Segoe UI', Arial, sans-serif;
            font-size: 13px;
            font-weight:700;
            border-radius: 7px;
            box-shadow:0 6px 18px rgba(23,37,84,.22);
            cursor: pointer;
            display: none; /* Initially hidden */
            }

            /* Style when the button is visible */
            .back-to-top.show {
            display: block;
            }

            /* Smooth scrolling behavior */
            html {
            scroll-behavior: smooth;
            }


         </style>
         

    </head>


    <body class="aip_generate sop_gen aip" id="printTable">
    <?php 
    function gcd($a, $b) {
        return ($b == 0) ? $a : gcd($b, $a % $b);
    }
    ?>




    <!-- <img class="logo" src="<?= base_url(); ?>assets/images/report/ke.png" alt="">
    <p>
    <span class="rp">Republic of the Philippines</span><br />
        <span class="de">Department of Education</span><br />
        <span class="r">Region XI</span><br />
        <span class="r">School Division of Davao Oriental</span><br />
        <span class="sadress"><?= strtoupper($school->district); ?><br />
         <?= strtoupper($school->schoolName); ?><br />
         <?= strtoupper($school->sitio); ?> <?= strtoupper($school->brgy); ?>, <?= strtoupper($school->city); ?>, <?= strtoupper($school->province); ?></span> 
    </p>
    <div class="hr"></div> -->

      


    <?php date_default_timezone_set('Asia/Manila'); ?>

    <div class="smea-doc">

        <img class="print-letterhead" src="<?= base_url(); ?>assets/images/header.png"
            alt="Republic of the Philippines - Department of Education - Region XI - School Division of Davao De Oro" />

        <header class="smea-masthead">
            <div>
                <span class="smea-kicker">School Governance and Operations Division &middot; Monitoring &amp; Evaluation</span>
                <h1>SMEA Year-End Summary</h1>
                <p>Consolidated physical and financial accomplishment per pillar, quarter by quarter.</p>
            </div>
            <span class="smea-status">FY <?= html_escape($fy); ?></span>
        </header>

        <div class="smea-meta">
            <div class="smea-meta-item">
                <span>Fiscal Year</span><strong><?= html_escape($fy); ?></strong>
            </div>
            <div class="smea-meta-item">
                <span>Coverage</span><strong id="smeaCoverage">Quarters 1 &ndash; 4</strong>
            </div>
            <div class="smea-meta-item">
                <span>Batch Code</span><strong><?= html_escape($data_row->b_code); ?></strong>
            </div>
            <div class="smea-meta-item">
                <span>School ID</span><strong><?= html_escape($data_row->school_id); ?></strong>
            </div>
            <div class="smea-meta-item">
                <span>Date Generated</span><strong><?= date('F d, Y'); ?></strong>
            </div>
        </div>

        <!-- Choose which quarter(s) are shown — the hidden ones are left out of the printout too. -->
        <div class="smea-toolbar">
            <span class="smea-toolbar-label">Quarter to display / print</span>
            <button type="button" class="smea-btn smea-qbtn is-active" data-q="all">All</button>
            <button type="button" class="smea-btn smea-qbtn" data-q="1">Q1</button>
            <button type="button" class="smea-btn smea-qbtn" data-q="2">Q2</button>
            <button type="button" class="smea-btn smea-qbtn" data-q="3">Q3</button>
            <button type="button" class="smea-btn smea-qbtn" data-q="4">Q4</button>

            <span class="toolbar-spacer"></span>

            <button type="button" class="smea-btn smea-print-btn" id="smeaPrintBtn">Print Report</button>
        </div>

    <?php for ($q = 1; $q <= 4; $q++) { $renguapo = 'q'.$q;  $guapoko = 'smea_q'.$q;?>

    <section class="smea-quarter" data-quarter="<?= $q; ?>">

        <h3>Quarter <?= $q; ?></h3>




    


   

    <table >
        
            <tr>
                <th rowspan="3">PILLAR</th>
                <th colspan="7">PHYSICAL ACCOMPLISHMENTS</th>
                <th colspan="6">FINANCIAL ACCOMPLISHMENTS (MOOE)</th>
                <th colspan="6">FINANCIAL ACCOMPLISHMENTS (OTHER SOURCES OF FUND)</th>
            </tr>
           
            <tr>
                <th rowspan="2">Number of<br /> Physical <br />target</th>
                <th rowspan="2">Achieved <br />(base on <br />planned<br /> targets)</th>
                <th rowspan="2">%age of <br />Accomplish<br/>ment</th>
                <th rowspan="2">Gain <br />(base on <br />planned<br /> targets)</th>
                <th rowspan="2">%age of <br />Gain</th>
                <th rowspan="2">Gap/Balance <br />(base on <br />planned<br /> targets)</th>
                <th rowspan="2">%age of <br />Gap</th>


                <!-- end of physical accomplishment -->

                <th rowspan="2">Funds Allocated<br />for Quarter</th>
                <th rowspan="2">Funds Utilized</th>
                <th rowspan="2">% of <br />Utilization(utilization/<br />allocation x100)</th>
                <th colspan="2">Gap on Allocation versus Utilized(Difference between allocated amount and utilized amount)</th>
                <th rowspan="2">Remarks</th>

                <!-- end of financial accomplishment -->

                <th rowspan="2">Funds Allocated<br />for Quarter</th>
                <th rowspan="2">Funds Utilized</th>
                <th rowspan="2">% of <br />Utilization(utilization/<br />allocation x100)</th>
                <th colspan="2">Gap on Allocation versus Utilized(Difference between allocated amount and utilized amount)</th>
                <th rowspan="2">Remarks</th>

            </tr>
            <tr>
                <th>Amount</th>
                <th>%</th>

                <th>Amount</th>
                <th>%</th>
            </tr>   
        <tbody>
          
        <?php foreach($data as $row){ 
         $pt = $this->SmeaModel->smea_by_pillar($row->school_id,$row->fy,$row->b_code,$row->pillar,$renguapo,1);
         $ft = $this->SmeaModel->smea_by_pillar($row->school_id,$row->fy,$row->b_code,$row->pillar,$renguapo,2);
         $fto = $this->SmeaModel->smea_by_pillar($row->school_id,$row->fy,$row->b_code,$row->pillar,$renguapo,3);

         $spt = $this->SmeaModel->smea_by_pillar($row->school_id,$row->fy,$row->b_code,$row->pillar,$guapoko,1);
         $sft = $this->SmeaModel->smea_by_pillar($row->school_id,$row->fy,$row->b_code,$row->pillar,$guapoko,2);
         $sfto = $this->SmeaModel->smea_by_pillar($row->school_id,$row->fy,$row->b_code,$row->pillar,$guapoko,3);

         $gpt = $this->SmeaModel->smea_gane($row->school_id,$row->fy,$row->b_code,$row->pillar,$guapoko,1);
         $gft = $this->SmeaModel->smea_gane($row->school_id,$row->fy,$row->b_code,$row->pillar,$guapoko,1);
         $gfto = $this->SmeaModel->smea_gane($row->school_id,$row->fy,$row->b_code,$row->pillar,$guapoko,1);


         $gappt = $this->SmeaModel->smea_gap($row->school_id,$row->fy,$row->b_code,$row->pillar,$guapoko,1);
         $gapft = $this->SmeaModel->smea_gap($row->school_id,$row->fy,$row->b_code,$row->pillar,$guapoko,1);
         $gapfto = $this->SmeaModel->smea_gap($row->school_id,$row->fy,$row->b_code,$row->pillar,$guapoko,1);

         // Funds are amounts, not counts: total the allocated (q{q}) and utilized (smea_q{q}) values per pillar.
         $mooe_alloc = 0;  foreach ($ft->result()   as $r) { $mooe_alloc  += (float) $r->$renguapo; }
         $mooe_util  = 0;  foreach ($sft->result()  as $r) { $mooe_util   += (float) $r->$guapoko; }
         $other_alloc = 0; foreach ($fto->result()  as $r) { $other_alloc += (float) $r->$renguapo; }
         $other_util  = 0; foreach ($sfto->result() as $r) { $other_util  += (float) $r->$guapoko; }

        ?>
            <tr>
                <!-- PHYSICAL ACCOMPLISHMENTS -->
                <td><?= $row->pillar; ?></td>
                <td><?= $pt->num_rows(); ?></td>
                <td><?= $spt->num_rows(); ?></td>
                <td>
                    <?php 
                        $score = $spt->num_rows();
                        $total = $pt->num_rows();

                        if ($total == 0) {
                            $percent = 'N/A'; // or 0%
                        } else {
                            $percent = ($score / $total) * 100;
                        }

                        echo is_numeric($percent) ? number_format($percent) . '%' : $percent;
                    ?>
                </td>
                <td><?= $gpt->num_rows(); ?></td>
                <td>
                     <?php 
                        $score = $gappt->num_rows();
                        $total = $pt->num_rows();

                        if ($total == 0) {
                            $percent = 'N/A'; // or 0%
                        } else {
                            $percent = ($score / $total) * 100;
                        }

                        echo is_numeric($percent) ? number_format($percent) . '%' : $percent;
                    ?>
                </td>
                <td><?= $gappt->num_rows(); ?></td>
                <td></td>
                <!-- FINANCIAL ACCOMPLISHMENTS (MOOE) : Allocated | Utilized | %Util | Gap Amount | Gap % | Remarks -->
                <td><?= $mooe_alloc != 0 ? number_format($mooe_alloc, 2) : ''; ?></td>
                <td><?= $mooe_util  != 0 ? number_format($mooe_util, 2)  : ''; ?></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <!-- FINANCIAL ACCOMPLISHMENTS (OTHER SOURCES OF FUND) : Allocated | Utilized | %Util | Gap Amount | Gap % | Remarks -->
                <td><?= $other_alloc != 0 ? number_format($other_alloc, 2) : ''; ?></td>
                <td><?= $other_util  != 0 ? number_format($other_util, 2)  : ''; ?></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        <?php } ?>

        
            
           
        </tbody>
    </table>

    </section>

    <?php } ?>


    <div class="fcon">
                <img style="width:90px; float:left;" src="https://qrcode.tec-it.com/API/QRCode?data=<?= base_url(); ?>Page/smea_qr/<?= $data_row->school_id.'/'.$data_row->fy.'/'.$data_row->b_code ?>" title="" />
                <div class="lcon">
                    
                    System Generated Report<br />
                    School Monitoring, Evaluation And Adjustment<br />
                    Date Generated: <?php  date_default_timezone_set('Asia/Manila'); echo date('F d, Y', time()); ?><br />
                    
                </div>
                
                <div class="blocker"></div>
    </div>

    </div>
    <!-- /.smea-doc -->

    <button id="backToTopBtn" class="back-to-top">Back to Top</button>

    <script>
        // Get the button element
const backToTopButton = document.getElementById("backToTopBtn");

// Show the button when the user scrolls down 100px from the top
window.onscroll = function() {
  if (document.body.scrollTop > 100 || document.documentElement.scrollTop > 100) {
    backToTopButton.classList.add("show");
  } else {
    backToTopButton.classList.remove("show");
  }
};

// Scroll to the top of the page when the button is clicked
backToTopButton.onclick = function() {
  window.scrollTo({ top: 0, behavior: 'smooth' });
};
    </script>

    <script>
    (function () {
        var buttons  = document.querySelectorAll('.smea-qbtn');
        var sections = document.querySelectorAll('.smea-quarter');
        if (!buttons.length || !sections.length) { return; }

        function apply(choice) {
            sections.forEach(function (section) {
                var visible = (choice === 'all' || section.dataset.quarter === choice);
                section.style.display = visible ? '' : 'none';
            });

            buttons.forEach(function (b) {
                b.classList.toggle('is-active', b.dataset.q === choice);
            });

            var coverage = document.getElementById('smeaCoverage');
            if (coverage) {
                coverage.innerHTML = (choice === 'all') ? 'Quarters 1 &ndash; 4' : 'Quarter ' + choice;
            }
        }

        buttons.forEach(function (b) {
            b.addEventListener('click', function () { apply(b.dataset.q); });
        });

        var printBtn = document.getElementById('smeaPrintBtn');
        if (printBtn) {
            printBtn.addEventListener('click', function () { window.print(); });
        }

        apply('all');
    })();
    </script>







    </body>
                </html>