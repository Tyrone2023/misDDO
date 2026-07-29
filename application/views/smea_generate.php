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
               SMEA report — design language mirrored from the SGOD IPCRF workspace
               (sgod/assets/css/ipcrf.css):

                 --ip-navy  #172554   headings          --ip-blue  #3157c8  accents
                 --ip-ink   #24324a   body copy         --ip-muted #6b7890  labels
                 --ip-line  #dfe6f2   hairlines         --ip-bg    #f3f6fb  page
                 document navy #273856 · document tint #d7e2f3 · soft blue #eef3ff

               Montserrat/Lato for the interface, Georgia for the document title —
               the same split IPCRF uses between its chrome and its printable form.
               ===================================================================== */

            html, body{ margin:0; padding:0; }

            body.aip_generate{
                background:#eef1f6;
                font-family:'Lato', 'Segoe UI', Calibri, Arial, sans-serif;
                color:#24324a;
                -webkit-font-smoothing:antialiased;
                text-rendering:optimizeLegibility;
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
                letter-spacing:.02em;
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
            .smea-status.is-submitted{
                background:#e7f5f1;
                border-color:#9ed5c8;
                color:#0f7f6c;
            }
            .smea-status.is-compliance{
                background:#fdf1e3;
                border-color:#f0c48a;
                color:#96490a;
            }

            /* ===== Meta strip (IPCRF employee-field cards) ===== */
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

            /* ===== Sticky toolbar (IPCRF .ipcrf-toolbar) ===== */
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
            .smea-legend{
                display:inline-flex;
                align-items:center;
                gap:8px;
                color:#4c5b72;
                font-size:12px;
                font-weight:600;
                line-height:1.35;
            }
            .smea-legend .swatch{
                display:inline-block;
                flex:none;
                width:15px;
                height:15px;
                background:#dbe6fa;
                border:1px solid #7f9ed4;
                border-radius:3px;
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
            .smea-btn-soft{
                background:#f0f2f5;
                border-color:#b8c0cc;
                color:#3f4a5c;
            }
            .smea-btn-soft:hover{ background:#e3e6eb; color:#273244; }
            .smea-btn-primary{
                background:#3157c8;
                border-color:#3157c8;
                color:#ffffff !important;
                box-shadow:0 2px 6px rgba(49,87,200,.25);
            }
            .smea-btn-primary:hover{ background:#234aa8; border-color:#234aa8; }
            .smea-btn-primary.is-busy{ opacity:.6; pointer-events:none; }
            /* Validated: the report is finished, so the toolbar reads as a clearance
               rather than as the "you may not edit this" warning it carries while the
               submission is still sitting with the division office. */
            .smea-cleared{
                color:#0f7f6c;
                font-family:'Montserrat', 'Segoe UI', Arial, sans-serif;
                font-size:12px;
                font-weight:800;
                letter-spacing:.02em;
            }
            .smea-submitted-chip{
                display:inline-flex;
                align-items:center;
                gap:6px;
                background:#e7f5f1;
                border:1px solid #9ed5c8;
                border-radius:999px;
                color:#0f7f6c;
                font-family:'Montserrat', 'Segoe UI', Arial, sans-serif;
                font-size:12px;
                font-weight:800;
                padding:7px 13px;
                white-space:nowrap;
            }

            /* ===== Pillar band (IPCRF .ipcrf-document .ipcrf-section-head) ===== */
            .aip_generate ul{
                font-size:13px;
                margin:22px 0 0;
                padding:0;
                text-align:left;
            }
            .aip_generate ul li{
                display:block;
                background:#d7e2f3;
                border:1px solid #b6c6df;
                border-left:4px solid #273856;
                border-radius:3px;
                color:#16294d;
                font-family:Georgia, 'Times New Roman', serif;
                font-size:13px;
                font-weight:800;
                letter-spacing:.025em;
                padding:8px 12px;
                text-transform:uppercase;
            }

            /* ===== Data table ===== */
            .aip_generate table{
                width:100%;
                table-layout:fixed;         /* share width across columns so it never overflows sideways */
                border-collapse:collapse;
                font-size:11px;
                margin-top:8px;
                margin-bottom:34px;
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
            .aip_generate table td{
                background-color:#ffffff;
                color:#24324a;
                line-height:1.35;
            }
            .aip_generate table th{
                background-color:#273856;   /* document navy: white text ~11:1 */
                color:#ffffff;
                font-family:'Montserrat', 'Segoe UI', Arial, sans-serif;
                font-weight:700;
                font-size:10px;
                line-height:1.3;
                text-transform:uppercase;
                letter-spacing:.04em;
            }
            /* Zebra striping + hover for easier row tracking across the wide table */
            .aip_generate tbody tr:nth-child(even) td{
                background-color:#f4f7fc;
            }
            .aip_generate tbody tr:hover td{
                background-color:#e8eefb;
            }

            /* ===== Clickable (highlighted) cells only ===== */
            .ivan{
                background-color:#eceef1 !important;
                color:#24324a !important;
            }
            .ivy{
                background-color:#dbe6fa !important;   /* soft blue action cell: dark text ~10:1 */
                color:#1f3d7a !important;
                font-weight:700;
            }
            .ivy.smea-clickable{
                cursor:pointer;
            }
            .ivy.smea-clickable:hover,
            .aip_generate tbody tr:hover td.ivy{
                background-color:#c7d8f5 !important;
                color:#16305f !important;
            }
            .ivy a,
            .smea-clickable a{
                color:#1f3d7a;
                text-decoration:underline;
                font-weight:700;
            }
            /* Small edit hint shown inside the highlighted (clickable) cell */
            .smea-edit-hint{
                font-size:10px;
                margin-left:3px;
                opacity:.85;
            }

            /* ===== Report footer ===== */
            .aip_generate .fcon{
                border-top:1px solid #dfe6f2;
                margin-top:8px;
                padding-top:14px;
                text-align:left;
            }
            .aip_generate .fcon .lcon{
                color:#6b7890;
                font-size:11.5px;
                line-height:1.5;
            }

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
                /* Printed sheets carry a centred masthead */
                .smea-masthead{
                    display:block;
                    text-align:center;
                    border-bottom:.8px solid #111;
                    padding-bottom:3mm;
                    margin-bottom:3mm;
                }
                .smea-masthead > div{ text-align:center; }
                .smea-masthead h1{ color:#111; font-size:15pt; text-align:center; }
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
                .aip_generate ul li{
                    background:transparent !important;
                    border:.65px solid #111;
                    border-left:1.2mm solid #111;
                    color:#000 !important;
                }
                .aip_generate table{ font-size:9px; page-break-inside:auto; }
                .aip_generate table th{ font-size:8px; }
                .aip_generate table th,
                .aip_generate table td{ padding:3px 3px; }
                .aip_generate tr{ page-break-inside:avoid; }
                /* No background colours when printing — plain white cells, black text, borders kept for structure */
                .aip_generate table th,
                .aip_generate table td,
                .aip_generate tbody tr:nth-child(even) td,
                .aip_generate tbody tr:hover td,
                .ivy,
                .ivy.smea-clickable:hover,
                .aip_generate tbody tr:hover td.ivy{
                    background:transparent !important;
                    background-color:transparent !important;
                    color:#000 !important;
                }
                .ivy a, .smea-clickable a{ color:#000 !important; }
                .aip_generate .fcon{ border-top:.65px solid #111; }
                .aip_generate .fcon .lcon{ color:#000; }
                .smea-edit-hint, .back-to-top, .smea-modal-overlay, .smea-toolbar{ display:none !important; }
            }

            /* --- Modal --- */
            .smea-modal-overlay{
                display:none;
                position:fixed;
                inset:0;
                background:rgba(23,37,84,.45);
                z-index:1050;
                align-items:flex-start;
                justify-content:center;
            }
            .smea-modal-overlay.show{ display:flex; }
            .smea-modal{
                background:#fff;
                width:460px;
                max-width:92%;
                margin-top:8vh;
                border-radius:12px;
                box-shadow:0 14px 40px rgba(35,53,92,.28);
                overflow:hidden;
                font-family:inherit;
            }
            .smea-modal-header{
                display:flex;
                align-items:center;
                justify-content:space-between;
                background:#172554;
                color:#fff;
                padding:12px 16px;
                font-family:'Montserrat', 'Segoe UI', Arial, sans-serif;
                font-size:15px;
                font-weight:700;
            }
            .smea-modal-header .smea-modal-close{
                background:none;
                border:none;
                color:#fff;
                font-size:22px;
                line-height:1;
                cursor:pointer;
            }
            .smea-modal-body{ padding:18px 16px; text-align:left; }
            .smea-modal-sub{ margin:0 0 14px; font-size:13px; color:#6b7890; }
            .smea-field{ margin-bottom:12px; }
            .smea-field label{
                display:block;
                font-size:11px;
                font-weight:800;
                letter-spacing:.035em;
                text-transform:uppercase;
                margin-bottom:5px;
                color:#58647a;
            }
            .smea-req{ color:#d84a4a; }
            .smea-input{
                width:100%;
                padding:8px 10px;
                border:1px solid #d7e0ee;
                border-radius:8px;
                font-size:14px;
                color:#24324a;
                box-sizing:border-box;
            }
            .smea-input:focus{
                border-color:#7390e5;
                box-shadow:0 0 0 3px rgba(49,87,200,.10);
                outline:none;
            }
            .smea-input:disabled{ background:#f4f6fa; color:#56627a; }
            .smea-actions{
                display:flex;
                gap:8px;
                margin-top:6px;
            }
            .smea-btn-save,.smea-btn-cancel{
                border:1px solid transparent;
                border-radius:7px;
                padding:9px 16px;
                font-family:'Montserrat', 'Segoe UI', Arial, sans-serif;
                font-size:13px;
                font-weight:700;
                cursor:pointer;
            }
            .smea-btn-save{ background:#3157c8; border-color:#3157c8; color:#fff; }
            .smea-btn-save:hover{ background:#234aa8; border-color:#234aa8; color:#fff; }
            .smea-btn-cancel{ background:#f0f2f5; border-color:#b8c0cc; color:#3f4a5c; }
            .smea-loading{ text-align:center; color:#6b7890; padding:20px 0; }

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

    <?php if(!isset($q) || $q === null || $q === ''){
        $q=$_SESSION['q'];
    }?>
    <?php
    $smea_submitted = isset($smea_submitted) ? $smea_submitted : false;
    // Set by the controller: $locked is TRUE once the report has been submitted (and
    // stays TRUE after the division office validates it); $readonly is TRUE when the
    // page is opened from the division-office review, which never edits.
    $locked      = isset($locked) ? $locked : false;
    $readonly    = isset($readonly) ? $readonly : false;
    $review_mode = isset($review_mode) ? $review_mode : false;
    $smea_row    = isset($smea_row) ? $smea_row : null;
    $school_id   = isset($school_id) ? $school_id : $this->session->username;
    $bcode       = isset($bcode) ? $bcode : $_SESSION['aip'];
    $can_edit    = !$readonly && !$locked;
    date_default_timezone_set('Asia/Manila');

    // Masthead chip. It has to agree with the status banner further down, so it shows
    // the actual status rather than just submitted / not submitted.
    $smea_chip = 'For Submission';
    $smea_chip_class = '';
    if (!empty($smea_row)) {
        $status_now = trim((string) $smea_row->status);
        $smea_chip = $status_now !== '' ? $status_now : 'Submitted';
        $smea_chip_class = ($smea_chip === 'For Compliance') ? ' is-compliance' : ' is-submitted';
    }

    // Validated is the end of the line, not just another locked state: the school is
    // not waiting on anybody, so the toolbar drops the "closed for editing" warning and
    // the "SMEA Submitted" chip and tells them the report is ready to go up.
    $is_validated = (!empty($smea_row) && trim((string) $smea_row->status) === SGODModel::SMEA_VALIDATED);

    // One highlighted target cell. It is only interactive while the school may still
    // edit — once the SMEA is submitted (or validated) it renders as a plain cell,
    // with no modal hook, no link and no pencil.
    $smea_cell = function ($modal_url, $edit_url, $value) use ($can_edit, $q) {
        if (!$can_edit) {
            return '<td class="ivy">' . $value . '</td>';
        }

        return '<td class="ivy smea-clickable"'
            . ' data-url="' . $modal_url . '" data-q="' . html_escape($q) . '"'
            . ' title="Click to update Quarter ' . html_escape($q) . ' accomplishment">'
            . '<a href="' . $edit_url . '">' . $value . '</a>'
            . '<span class="smea-edit-hint">&#9998;</span></td>';
    };
    ?>

    <div class="smea-doc">

        <header class="smea-masthead">
            <div>
                <span class="smea-kicker">School Governance and Operations Division &middot; Monitoring &amp; Evaluation</span>
                <h1>School Monitoring, Evaluation and Adjustment</h1>
                <p>Quarterly physical and financial accomplishment against the approved Annual Implementation Plan.</p>
            </div>
            <span class="smea-status<?= $smea_chip_class; ?>">
                <?= html_escape($smea_chip); ?>
            </span>
        </header>

        <div class="smea-meta">
            <div class="smea-meta-item">
                <span>Fiscal Year</span><strong><?= html_escape($fy); ?></strong>
            </div>
            <div class="smea-meta-item">
                <span>Reporting Period</span><strong>Quarter <?= html_escape($q); ?></strong>
            </div>
            <div class="smea-meta-item">
                <span>Batch Code</span><strong><?= html_escape($bcode); ?></strong>
            </div>
            <div class="smea-meta-item">
                <span><?= $review_mode ? 'School' : 'School ID'; ?></span>
                <strong><?= html_escape($review_mode && !empty($school) ? $school->schoolName : $school_id); ?></strong>
            </div>
            <div class="smea-meta-item">
                <span>Date Generated</span><strong><?= date('F d, Y'); ?></strong>
            </div>
        </div>

        <?php
        $smea_review_q = (int) $q;
        $smea_back = base_url() . 'Page/smea_review/' . (!empty($smea_row) ? (int) $smea_row->id : 0) . '/' . (int) $q;
        include('includes/smea_status.php');
        ?>

        <!-- Finalize: same submission as the "Submit SMEA" button on Page/smeav2 -->
        <?php if (!$readonly) { ?>
        <div class="smea-toolbar">
            <span class="smea-legend">
                <?php if ($can_edit) { ?>
                    <span class="swatch"></span>
                    <span>Only the highlighted cells are clickable &mdash; click one to update its Quarter <?= html_escape($q); ?> accomplishment.</span>
                <?php } elseif ($is_validated) { ?>
                    <span class="smea-cleared">&#10003; SDO VALIDATED &mdash; Ready for Printing and Posting in the Transparency Board.</span>
                <?php } else { ?>
                    <span>&#128274; This SMEA is closed for editing. The division office has to return it for compliance before it can be changed.</span>
                <?php } ?>
            </span>

            <span class="toolbar-spacer"></span>

            <!-- <button type="button" class="smea-btn smea-btn-soft" id="smeaPrintBtn">Print Report</button> -->

            <?php if ($locked && !$is_validated) { ?>
                <span class="smea-submitted-chip">&#10003; SMEA Submitted</span>
            <?php } elseif (!$locked) { ?>
                <a id="smeaFinalizeBtn" class="smea-btn smea-btn-primary"
                    data-fy="<?= html_escape($fy); ?>"
                    data-resubmit="<?= $smea_submitted ? '1' : '0'; ?>"
                    href="<?= base_url(); ?>Page/submit_smea/<?= $school_id; ?>/<?= $bcode; ?>/<?= $fy; ?>">
                    <?= $smea_submitted ? 'Re-submit SMEA' : 'Finalize &amp; Submit SMEA'; ?>
                </a>
            <?php } ?>
        </div>
        <?php } ?>

        <script>
            (function () {
                var printBtn = document.getElementById('smeaPrintBtn');
                if (printBtn) {
                    printBtn.addEventListener('click', function () { window.print(); });
                }

                var btn = document.getElementById('smeaFinalizeBtn');
                if (!btn) { return; }

                btn.addEventListener('click', function (e) {
                    if (btn.dataset.busy === '1') { e.preventDefault(); return; }

                    var msg = (btn.dataset.resubmit === '1')
                        ? 'Submit this SMEA report for FY ' + btn.dataset.fy + ' again?\n\n' +
                          'It goes back to the division office for validation and can no longer be edited.'
                        : 'Finalize and submit this SMEA report for FY ' + btn.dataset.fy + '?\n\n' +
                          'It will be marked as submitted to the division office and can no longer be edited.';
                    if (!confirm(msg)) { e.preventDefault(); return; }

                    // Guard against a double click firing two inserts.
                    btn.dataset.busy = '1';
                    btn.classList.add('is-busy');
                    btn.textContent = 'Submitting…';
                });
            })();
        </script>


    <?php foreach($data as $row){ 
         $io = $this->SGODModel->one_cond_row('sgod_setting_io', 'id', $row->io);
        ?>
    <ul>
        <li>PILLAR : <?= ucfirst($row->pillar); ?></li>
    </ul>

   

    <table >
            <colgroup>
                <col span="2" style="width:11%">
                <col span="19" style="width:4.1%">
            </colgroup>
            <tr>
                <th rowspan="3">SCHOOL IMPROVEMENT PROJECT TITLE</th>
                <th rowspan="3">PERFORMANCE INDICATORS</th>
                <th colspan="7">PHYSICAL ACCOMPLISHMENTS</th>
                <th colspan="7">FINANCIAL ACCOMPLISHMENTS (MOOE)</th>
                <th colspan="5">FINANCIAL ACCOMPLISHMENTS (OTHER SOURCES OF FUND)</th>
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
            <?php 
            $pia = $this->Common->four_cond('sgod_aip',"school_id", $row->school_id,"fy", $row->fy,"b_code", $row->b_code,"pillar", $row->pillar);
            //$pia = $this->SmeaModel->smea_by_pillar($row->school_id,$row->fy,$row->b_code,$row->pillar);
            $sp = null; 
            foreach($pia as $row){ 
                $pt = $this->SGODModel->two_cond_row('sgod_sop', 'aip_id',$row->id,'type',1);
                $ft = $this->SGODModel->two_cond_row('sgod_sop', 'aip_id',$row->id,'type',2);
                $fto = $this->SGODModel->two_cond_row('sgod_sop', 'aip_id',$row->id,'type',3);

                $field = 'q' . $q; // example: q1, q2, etc.

                $pt_val = isset($pt->$field) ? $pt->$field : null;
                $ft_val = isset($ft->$field) ? $ft->$field : null;
                $fto_val = isset($fto->$field) ? $fto->$field : null;

                // If all three values are null, '', or 0, skip the row
                if (
                    ($pt_val === null || $pt_val === '' || $pt_val == 0) &&
                    ($ft_val === null || $ft_val === '' || $ft_val == 0) &&
                    ($fto_val === null || $fto_val === '' || $fto_val == 0)
                ) {
                    continue;
                }
            ?>
            <tr>
            <?php if($sp !== $row->sip_project) { ?>

                    <td style="text-align:left"><?= $row->sip_project; ?></td>
                    <td style="text-align:left"><?= $row->pi; ?></td>
                    <?php }else{ ?>
                        <td></td>
                        <td></td>
                        <td><?= $row->pi; ?></td>
                        
                    <?php } ?>

                <?php 
                  
                  $pt_smea = 'smea_q'.$q;
                  $pt_sop = 'q'.$q;
                  if(!empty($pt)){

                    if($pt->$pt_sop != 0){$ptt = $pt->$pt_sop;}else{$ptt = 0;}
                    if($pt->$pt_smea != 0){$smea = $pt->$pt_smea;}else{$smea = 0;}
                        
                    $gain = (int)$smea-(int)$ptt;
                ?>
                <?= $smea_cell(base_url().'Page/smea_edit_modal/'.$pt->id.'/'.$q, base_url().'Page/smea_edit/'.$pt->id.'/'.$q, $pt->$pt_sop); ?>
                <td><?php

                    if (strpos($pt->$pt_sop, '%') !== false) {
                        echo $pt->$pt_smea . "%";
                    } else {
                        if($pt->$pt_smea != 0){if($pt->$pt_smea > $pt->$pt_sop){echo $pt->$pt_sop;}else{echo $pt->$pt_smea;}}
                    }



                ?></td>
                <td>
                    <?php 

                        if (strpos($pt->$pt_sop, '%') !== false) {
                            echo $pt->$pt_smea . "%";
                        } else {
                            if ($smea != 0 && $ptt != 0) { 
                                if($pt->$pt_smea < $pt->$pt_sop){
                                    echo (int)(($smea / $ptt) * 100) . "%";
                                }else{
                                    echo (int)(($ptt / $ptt) * 100) . "%";
                            }
                        }
                        
                        }
                    ?>
                </td>
                <td><?php 

                    if (strpos($pt->$pt_sop, '%') !== false) {
                        if($gain >= 0){echo abs($gain) . "%";}
                    } else {
                        if ($gain != 0 && $ptt != 0) { 
                            if($gain >= 0){echo $gain;} 
                        }
                    } 
                ?>
                </td>
                <td>
                    <?php 
                        if (strpos($pt->$pt_sop, '%') !== false) {
                            if($gain >= 0){echo abs($gain) . "%";}
                        } else {
                            if ($gain != 0 && $ptt != 0) { 
                                if($gain >= 0){echo (int)(($gain / $ptt) * 100) . "%";}
                            }
                        } 
                        
                    ?>
                </td>
                <td> <?php  
                    if (strpos($pt->$pt_sop, '%') !== false) {
                        if($gain <= 0){echo abs($gain) . "%";}
                    } else {
                        if($gain <= 0){echo abs($gain);}
                    } 
                ?> </td>
                <td>
                    <?php 
                        if (strpos($pt->$pt_sop, '%') !== false) {
                            if($gain <= 0){echo abs($gain) . "%";}
                        } else {
                            if ($gain != 0 && $ptt != 0) { 
                                if($gain <= 0){echo abs((int)(($gain / $ptt) * 100)) . "%";}
                            }
                        }
                        
                    ?>
                </td>
                <?php }else{?>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                <?php } ?>

                <?php 
                  
                  $ft_smea = 'smea_q'.$q;
                  $ft_sop = 'q'.$q;
                  $ft_remarks = 'remarks_q'.$q;
                  if(!empty($ft)){
                    if($ft->$ft_sop != 0){$fptt = $ft->$ft_sop;}else{$fptt = 0;}
                    if($ft->$ft_smea != 0){$fsmea = $ft->$ft_smea;}else{$fsmea = 0;}

                    //$fgain = ((int)$fsmea/(int)$fptt)*100;

                ?>
                <?= $smea_cell(base_url().'Page/smea_edit_modal/'.$ft->id.'/'.$q, base_url().'Page/smea_edit/'.$ft->id.'/'.$q, $ft->$ft_sop); ?>
                <td><?php if($ft->$ft_smea != 0){echo number_format($ft->$ft_smea, 2);}  ?></td>
                <td>
                    <?php
                        if ($fsmea != 0 && $fptt != 0) {
                            if($fsmea >= 0){echo abs((int)(($fsmea / $fptt) * 100)) . "%";}
                        }
                    ?>
                </td>
                <td>
                <?php
                        if ($fsmea != 0 && $fptt != 0) {
                            if($fsmea >= 0){echo number_format((int)($fptt - $fsmea), 2);}
                        }
                    ?>
                </td>
                <td>
                <?php
                        if ($fsmea != 0 && $fptt != 0) {
                            if($fsmea >= 0){$fgain = abs((int)($fsmea - $fptt));}
                            if($fsmea >= 0){echo number_format(abs((int)(($fgain / $fptt) * 100)), 2) . "%";}
                        }
                    ?>
                </td>
                <td><?= $ft->$ft_remarks; ?></td>
                <?php }else{?>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                <?php } ?>


                <?php 
                  
                  $fto_smea = 'smea_q'.$q;
                  $fto_sop = 'q'.$q;
                  $fto_remarks = 'remarks_q'.$q;
                  if(!empty($fto)){
                    if($fto->$fto_sop != 0){$fptto = $fto->$fto_sop;}else{$fptto = 0;}
                    if($fto->$fto_smea != 0){$fsmeao = $fto->$fto_smea;}else{$fsmeao = 0;}

                    //$fgain = ((int)$fsmea/(int)$fptt)*100;

                ?>
                <?= $smea_cell(base_url().'Page/smea_edit_modal/'.$fto->id.'/'.$q, base_url().'Page/smea_edit/'.$fto->id.'/'.$q, $fto->$fto_sop ? number_format($fto->$fto_sop) : ''); ?>
                <td><?php if($fto->$fto_smea != 0){echo number_format($fto->$fto_smea, 2);}  ?></td>
                <td>
                    <?php 
                        if ($fsmeao != 0 && $fptto != 0) { 
                            if($fsmeao >= 0){echo abs((int)(($fsmeao / $fptto) * 100)) . "%";}
                        }
                    ?>
                </td>
                <td>
                <?php 
                        if ($fsmeao != 0 && $fptto != 0) { 
                            if($fsmeao >= 0){echo number_format((int)($fptto - $fsmeao), 2);}
                        }
                    ?>
                </td>
                <td>
                    <?php
                        if ($fsmeao != 0 && $fptto != 0 && $fptt != 0) {
                            $fgain = abs((int)($fsmeao - $fptto));
                            echo number_format(abs(($fgain / $fptt) * 100), 2) . "%";
                        }
                    ?>
                </td>
                <td><?= $fto->$fto_remarks?></td>
                <?php }else{?>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                <?php } ?>

            </tr>
            <?php }  ?>
           
        </tbody> 
    </table>
    <?php } ?>
                                    <?php 
                                      $val = array(
                                        "1" => "ACCESS",
                                        "2" => "EQUITY",
                                        "3" => "QUALITY",
                                        "4" => "RESILIENCY AND WELL-BEING",
                                        "5" => "ENABLING MECHANISM",
                                        "6" => "RESILIENCY",

                                      );
                                      if(!empty($adjustment)){
                                    ?>

                                        <h1 id="adjustment">SOP ADJUSTMENT</h1>
                                    <?php } ?>

    <?php foreach($adjustment as $row){ 
         //$io = $this->SGODModel->one_cond_row('sgod_setting_io', 'id', $row->io);
        ?>
    <ul>
        <li>PILLAR : <?= $val[$row->pillar]; ?></li>
    </ul>

   

    <table >
            <colgroup>
                <col span="2" style="width:11%">
                <col span="19" style="width:4.1%">
            </colgroup>
            <tr>
                <th rowspan="3">SCHOOL IMPROVEMENT PROJECT TITLE</th>
                <th rowspan="3">PERFORMANCE INDICATORS</th>
                <th colspan="7">PHYSICAL ACCOMPLISHMENTS</th>
                <th colspan="7">FINANCIAL ACCOMPLISHMENTS (MOOE)</th>
                <th colspan="5">FINANCIAL ACCOMPLISHMENTS (OTHER SOURCES OF FUND)</th>
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
            <?php 
            $pia = $this->Common->four_cond('sgod_smea_adjustment',"school_id", $row->school_id,"fy", $row->fy,"b_code", $row->b_code,"pillar", $row->pillar);
            $sp = null; 
            foreach($pia as $row){ ?>
            <tr>
            <?php if($sp !== $row->sip) { ?>

                    <td style="text-align:left"><?= $row->sip; ?></td>
                    <td style="text-align:left"><?= $row->pi; ?></td>
                    <?php }else{ ?>
                        <td></td>
                        <td></td>
                        <td><?= $row->pi; ?></td>
                        
                    <?php } ?>

                <?php 
                  $pt = $this->SGODModel->two_cond_row('sgod_sop_adjustment', 'aip_id',$row->id,'type',1);
                  $pt_smea = 'smea_q'.$q;
                  $pt_sop = 'q'.$q;
                  if(!empty($pt)){

                    if($pt->$pt_sop != 0){$ptt = $pt->$pt_sop;}else{$ptt = 0;}
                    if($pt->$pt_smea != 0){$smea = $pt->$pt_smea;}else{$smea = 0;}
                        
                    $gain = (int)$smea-(int)$ptt;
                ?>
                <?= $smea_cell(base_url().'Page/smea_ad_edit_modal/'.$pt->id.'/'.$q, base_url().'Page/smea_ad_edit/'.$pt->id.'/'.$q, $pt->$pt_sop); ?>
                <td><?php 

                    if (strpos($pt->$pt_sop, '%') !== false) {
                        echo $pt->$pt_smea . "%";
                    } else {
                        if($pt->$pt_smea != 0){if($pt->$pt_smea > $pt->$pt_sop){echo $pt->$pt_sop;}else{echo $pt->$pt_smea;}} 
                    }

                
                
                ?></td>
                <td>
                    <?php 

                        if (strpos($pt->$pt_sop, '%') !== false) {
                            echo $pt->$pt_smea . "%";
                        } else {
                            if ($smea != 0 && $ptt != 0) { 
                                if($pt->$pt_smea < $pt->$pt_sop){
                                    echo (int)(($smea / $ptt) * 100) . "%";
                                }else{
                                    echo (int)(($ptt / $ptt) * 100) . "%";
                            }
                        }
                        
                        }
                    ?>
                </td>
                <td><?php 

                    if (strpos($pt->$pt_sop, '%') !== false) {
                        if($gain >= 0){echo abs($gain) . "%";}
                    } else {
                        if ($gain != 0 && $ptt != 0) { 
                            if($gain >= 0){echo $gain;} 
                        }
                    } 
                ?>
                </td>
                <td>
                    <?php 
                        if (strpos($pt->$pt_sop, '%') !== false) {
                            if($gain >= 0){echo abs($gain) . "%";}
                        } else {
                            if ($gain != 0 && $ptt != 0) { 
                                if($gain >= 0){echo (int)(($gain / $ptt) * 100) . "%";}
                            }
                        } 
                        
                    ?>
                </td>
                <td> <?php  
                    if (strpos($pt->$pt_sop, '%') !== false) {
                        if($gain <= 0){echo abs($gain) . "%";}
                    } else {
                        if($gain <= 0){echo abs($gain);}
                    } 
                ?> </td>
                <td>
                    <?php 
                        if (strpos($pt->$pt_sop, '%') !== false) {
                            if($gain <= 0){echo abs($gain) . "%";}
                        } else {
                            if ($gain != 0 && $ptt != 0) { 
                                if($gain <= 0){echo abs((int)(($gain / $ptt) * 100)) . "%";}
                            }
                        }
                        
                    ?>
                </td>
                <?php }else{?>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                <?php } ?>

                <?php 
                  $ft = $this->SGODModel->two_cond_row('sgod_sop_adjustment', 'aip_id',$row->id,'type',2);
                  $ft_smea = 'smea_q'.$q;
                  $ft_sop = 'q'.$q;
                  $ft_remarks = 'remarks_q'.$q;
                  if(!empty($ft)){
                    if($ft->$ft_sop != 0){$fptt = $ft->$ft_sop;}else{$fptt = 0;}
                    if($ft->$ft_smea != 0){$fsmea = $ft->$ft_smea;}else{$fsmea = 0;}

                    //$fgain = ((int)$fsmea/(int)$fptt)*100;

                ?>
                <?= $smea_cell(base_url().'Page/smea_ad_edit_modal/'.$ft->id.'/'.$q, base_url().'Page/smea_ad_edit/'.$ft->id.'/'.$q, $ft->$ft_sop ? number_format($ft->$ft_sop) : ''); ?>
                <td><?php if($ft->$ft_smea != 0){echo number_format($ft->$ft_smea, 2);}  ?></td>
                <td>
                    <?php 
                        if ($fsmea != 0 && $fptt != 0) { 
                            if($fsmea >= 0){echo abs((int)(($fsmea / $fptt) * 100)) . "%";}
                        }
                    ?>
                </td>
                <td>
                <?php 
                        if ($fsmea != 0 && $fptt != 0) { 
                            if($fsmea >= 0){echo number_format((int)($fptt - $fsmea), 2);}
                        }
                    ?>
                </td>
                <td>
                <?php 
                        if ($fsmea != 0 && $fptt != 0) { 
                            if($fsmea >= 0){$fgain = abs((int)($fsmea - $fptt));}
                            if($fsmea >= 0){echo number_format(abs((int)(($fgain / $fptt) * 100)), 2) . "%";}
                        }
                    ?>
                </td>
                <td></td>
                <?php }else{?>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                <?php } ?>


                <?php 
                  $fto = $this->SGODModel->two_cond_row('sgod_sop_adjustment', 'aip_id',$row->id,'type',3);
                  $fto_smea = 'smea_q'.$q;
                  $fto_sop = 'q'.$q;
                  $fto_remarks = 'remarks_q'.$q;
                  if(!empty($fto)){
                    if($fto->$fto_sop != 0){$fptto = $fto->$fto_sop;}else{$fptto = 0;}
                    if($fto->$fto_smea != 0){$fsmeao = $fto->$fto_smea;}else{$fsmeao = 0;}

                    //$fgain = ((int)$fsmea/(int)$fptt)*100;

                ?>
                <?= $smea_cell(base_url().'Page/smea_ad_edit_modal/'.$fto->id.'/'.$q, base_url().'Page/smea_ad_edit/'.$fto->id.'/'.$q, $fto->$fto_sop ? number_format($fto->$fto_sop) : ''); ?>
                <td><?php if($ft->$ft_smea != 0){echo number_format($ft->$ft_smea, 2);}  ?></td>
                <td>
                    <?php 
                        if ($fsmeao != 0 && $fptto != 0) { 
                            if($fsmeao >= 0){echo abs((int)(($fsmeao / $fptto) * 100)) . "%";}
                        }
                    ?>
                </td>
                <td>
                <?php 
                        if ($fsmeao != 0 && $fptto != 0) { 
                            if($fsmeao >= 0){echo number_format((int)($fptto - $fsmeao), 2);}
                        }
                    ?>
                </td>
                <td>
                <?php 
                        if ($fsmeao != 0 && $fptto != 0) { 
                            if($fsmeao >= 0){$fgain = abs((int)($fsmeao - $fptto));}
                            if($fsmeao >= 0){echo number_format(abs((int)(($fgain / $fptt) * 100)), 2) . "%";}
                        }
                    ?>
                </td>
                <td><?= $fto->$fto_remarks?></td>
                <?php }else{?>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                <?php } ?>

                

            </tr>
            <?php }  ?>
           
        </tbody> 
    </table>
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

    <!-- SMEA accomplishment modal -->
    <div id="smeaModal" class="smea-modal-overlay">
        <div class="smea-modal">
            <div class="smea-modal-header">
                <span>Update Accomplishment</span>
                <button type="button" class="smea-modal-close" aria-label="Close">&times;</button>
            </div>
            <div class="smea-modal-body" id="smeaModalBody">
                <div class="smea-loading">Loading&hellip;</div>
            </div>
        </div>
    </div>

    <script>
    (function () {
        var overlay = document.getElementById('smeaModal');
        var bodyEl  = document.getElementById('smeaModalBody');

        // Only the highlighted (.ivy) cells are clickable — mark them so.
        document.querySelectorAll('td.ivy[data-url]').forEach(function (cell) {
            cell.classList.add('smea-clickable');
        });

        function openModal(url) {
            overlay.classList.add('show');
            bodyEl.innerHTML = '<div class="smea-loading">Loading&hellip;</div>';
            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(function (r) { return r.text(); })
                .then(function (html) { bodyEl.innerHTML = html; bindForm(url); })
                .catch(function () { bodyEl.innerHTML = '<div class="smea-loading">Unable to load the form.</div>'; });
        }

        function closeModal() {
            overlay.classList.remove('show');
            bodyEl.innerHTML = '';
        }

        function bindForm(url) {
            var form = bodyEl.querySelector('form');
            if (!form) { return; }

            // Keep the "total accomplishment" running as the user edits this quarter.
            var acc          = form.querySelector('.smea-acc');
            var totalHidden  = form.querySelector('.smea-total');
            var totalDisplay = form.querySelector('.smea-total-display');
            var othersSum    = parseFloat(form.dataset.othersSum) || 0;
            function recompute() {
                var total = othersSum + (parseFloat(acc.value) || 0);
                if (totalHidden)  { totalHidden.value  = total; }
                if (totalDisplay) { totalDisplay.value = total; }
            }
            if (acc) { acc.addEventListener('input', recompute); recompute(); }

            form.addEventListener('submit', function (e) {
                e.preventDefault();
                var btn = form.querySelector('.smea-btn-save');
                if (btn) { btn.disabled = true; btn.textContent = 'Saving…'; }
                fetch(url, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(function (r) { return r.json(); })
                    .then(function (res) {
                        if (res && res.success) { window.location.reload(); return; }
                        // The server refuses a locked SMEA even if the page is stale.
                        var err = new Error('save failed');
                        err.reason = res && res.message;
                        throw err;
                    })
                    .catch(function (err) {
                        if (btn) { btn.disabled = false; btn.textContent = 'Save'; }
                        alert((err && err.reason) || 'Unable to save. Please try again.');
                    });
            });
        }

        document.addEventListener('click', function (e) {
            var cell = e.target.closest('.smea-clickable');
            if (cell && cell.dataset.url) { e.preventDefault(); openModal(cell.dataset.url); return; }
            if (e.target.closest('.smea-modal-close') || e.target === overlay) { closeModal(); }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') { closeModal(); }
        });
    })();
    </script>







    </body>
                </html>