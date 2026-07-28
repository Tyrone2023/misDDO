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
         <style>
            /* =====================================================================
               Palette: black / gray / blue
                 #111827 near-black text      #1f3a5f deep navy (headings)
                 #1f4e79 blue (accents)       #3f4a58 slate gray (table headers)
                 #f2f5f8 light gray (zebra)   #cfe0f2 light blue (highlight cells)
               ===================================================================== */
            .aip_generate{
                font-family:'Segoe UI', Calibri, 'Helvetica Neue', Arial, sans-serif;
                color:#111827;
                -webkit-font-smoothing:antialiased;
            }
            .aip_generate h1{
                font-size:21px;
                font-weight:800;
                letter-spacing:.3px;
                color:#111827;
                line-height:1.35;
            }
            .aip_generate h3{
                font-size:15px;
                font-weight:700;
                color:#1f4e79;
                margin-bottom:4px;
            }

            .ivan{
                background-color:#e2e8f0 !important;
                color:#111827 !important;
            }
            .ivy{
                background-color:#cfe0f2 !important;
                color:#10233a !important;
            }
            .ivy:hover {
                background-color:#b6d2ee !important;
            }

            /* ===== Fit-to-width table (no sideways scroll) ===== */
            .aip_generate table{
                width:100%;
                table-layout:fixed;          /* share width across all columns so nothing overflows */
                border-collapse:collapse;
                font-size:11px;
            }
            .aip_generate table th,
            .aip_generate table td{
                border:1px solid #94a3b8;
                padding:5px 4px;
                word-wrap:break-word;
                overflow-wrap:break-word;
                hyphens:auto;
                vertical-align:middle;
            }
            .aip_generate table th{
                background-color:#3f4a58 !important;   /* overrides the shared teal in renren.css */
                color:#ffffff !important;
                font-size:10px;
                font-weight:700;
                line-height:1.25;
                text-transform:uppercase;
                letter-spacing:.3px;
            }
            .aip_generate table td{
                background-color:#ffffff;
                color:#111827;
            }
            .aip_generate tbody tr:nth-child(even) td{ background-color:#f2f5f8; }
            .aip_generate tbody tr:hover td{ background-color:#e4ebf3; }

            /* ===== Quarter filter / print controls (screen only) ===== */
            .smea-controls{
                display:inline-flex;
                align-items:center;
                flex-wrap:wrap;
                gap:8px;
                border:1px solid #cbd5e1;
                background:#f6f8fb;
                border-radius:6px;
                padding:9px 14px;
                margin:0 auto 14px;
                font-size:13px;
                color:#1f3a5f;
            }
            .smea-controls .smea-controls-label{
                font-weight:700;
                margin-right:2px;
            }
            .smea-qbtn{
                border:1px solid #b8c4d2;
                background:#ffffff;
                color:#1f3a5f;
                font-weight:600;
                font-size:13px;
                padding:6px 14px;
                border-radius:4px;
                cursor:pointer;
                line-height:1.2;
            }
            .smea-qbtn:hover{ background:#e8eef6; }
            .smea-qbtn.is-active{
                background:#1f4e79;
                border-color:#1f4e79;
                color:#ffffff;
            }
            .smea-print-btn{
                border:1px solid #3f4a58;
                background:#3f4a58;
                color:#ffffff;
                font-weight:700;
                font-size:13px;
                padding:6px 16px;
                border-radius:4px;
                cursor:pointer;
                margin-left:6px;
            }
            .smea-print-btn:hover{ background:#2f3845; }
            .smea-empty{
                color:#6b7280;
                font-size:13px;
                margin:24px 0;
            }

            /* DepEd letterhead: printed sheets only */
            .print-letterhead{ display:none; }

            @media (max-width:900px){
                .aip_generate table{ font-size:9px; }
                .aip_generate table th{ font-size:8px; }
                .aip_generate table th,
                .aip_generate table td{ padding:3px 2px; }
            }

            /* ===== Print: Folio (F4) landscape, fit all columns across the width ===== */
            @page{
                size:330mm 210mm;   /* Folio / F4 in landscape */
                margin:8mm;
            }
            @media print{
                html, body{ width:330mm; }
                .aip_generate{ padding:0; }
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
                .back-to-top, .smea-controls{ display:none !important; }

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
            background-color: #3f4a58;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
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

      


    <img class="print-letterhead" src="<?= base_url(); ?>assets/images/header.png"
        alt="Republic of the Philippines - Department of Education - Region XI - School Division of Davao Oriental" />

    <h1>SCHOOL MONITORING, EVALUATION AND ADJUSTMENT<br />FY <?= $fy; ?></h1>

    <!-- Choose which quarter(s) are shown — the hidden ones are left out of the printout too. -->
    <div class="smea-controls">
        <span class="smea-controls-label">Quarter to display / print:</span>
        <button type="button" class="smea-qbtn is-active" data-q="all">All</button>
        <button type="button" class="smea-qbtn" data-q="1">Q1</button>
        <button type="button" class="smea-qbtn" data-q="2">Q2</button>
        <button type="button" class="smea-qbtn" data-q="3">Q3</button>
        <button type="button" class="smea-qbtn" data-q="4">Q4</button>
        <button type="button" class="smea-print-btn" id="smeaPrintBtn">Print</button>
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
            var shown = 0;

            sections.forEach(function (section) {
                var visible = (choice === 'all' || section.dataset.quarter === choice);
                section.style.display = visible ? '' : 'none';
                // Each printed quarter starts on its own sheet; the first one must not.
                section.style.pageBreakBefore = (visible && shown > 0) ? 'always' : 'auto';
                if (visible) { shown++; }
            });

            buttons.forEach(function (b) {
                b.classList.toggle('is-active', b.dataset.q === choice);
            });
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