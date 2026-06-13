<?php
/**
 * SCHOOL PARTNERSHIPS DATA SHEET (DPDS)
 * Standalone report view — styled to match the official DPDS Excel template.
 * Opened via target="_blank". Loaded WITHOUT the admin header/footer template.
 *
 * Expects:
 *   $data        -> array of rows (BrigadaModel->dpds_contribution)
 *   $month_value -> selected month (format YYYY-MM, may be null)
 *   $month_label -> human month (e.g. "May 2026")
 *   $sy          -> school year
 *   $school_id   -> school id (login username)
 *   $school_name -> schools.schoolName
 *   $offering    -> schools.course
 *   $region      -> region label
 *   $division    -> division label
 *
 * Layout note: the whole sheet (logos, title, info grid, bands, headers, data)
 * lives in ONE <table> of 25 columns (B..Z) so every width aligns perfectly.
 */
function dpds_e($v){ return ($v === null || $v === '') ? '' : htmlspecialchars($v, ENT_QUOTES); }
$export_qs = http_build_query(['month' => $month_value]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>School Partnerships Data Sheet</title>
    <style>
        :root{
            --grey:#D9D9D9;    /* section 1 band/header */
            --blue:#4472C4;    /* section 2 band/header */
            --lblue:#9DC3E6;   /* section 3 band/header */
            --peri:#8EAADB;    /* "Remarks" helper columns */
            --box:#E7E6E6;     /* form-field value boxes  */
            --line:#000;
            --ink:#1f2937;
        }
        *{box-sizing:border-box}
        body{margin:0;background:#eef1f5;font-family:Arial,Helvetica,sans-serif;color:var(--ink)}

        /* ---------- Toolbar (screen only) ---------- */
        .toolbar{
            position:sticky;top:0;z-index:10;display:flex;gap:10px;align-items:center;
            padding:12px 20px;background:#fff;border-bottom:1px solid #e2e8f0;
            box-shadow:0 1px 3px rgba(15,23,42,.06)
        }
        .toolbar .title{font-weight:700;font-size:14px;color:#0f172a;margin-right:auto}
        .toolbar .range{font-size:12px;color:#64748b;font-weight:600}
        .btn{
            font-size:13px;font-weight:600;cursor:pointer;text-decoration:none;
            padding:8px 16px;border-radius:8px;border:1px solid transparent;
            transition:.15s ease;display:inline-flex;align-items:center;gap:6px
        }
        .btn-export{background:#16a34a;color:#fff}
        .btn-export:hover{background:#15803d;transform:translateY(-1px)}
        .btn-print{background:#fff;color:#334155;border-color:#cbd5e1}
        .btn-print:hover{background:#f8fafc}

        /* ---------- Sheet ---------- */
        .sheet-wrap{padding:24px;overflow-x:auto}
        .sheet{
            background:#fff;margin:0 auto;width:max-content;
            padding:18px;box-shadow:0 1px 6px rgba(15,23,42,.1);border-radius:4px
        }

        /* ---------- Table ---------- */
        table.dpds{border-collapse:collapse;font-size:10px;table-layout:fixed}
        table.dpds td,table.dpds th{
            border:1px solid var(--line);padding:3px 5px;vertical-align:middle;
            word-wrap:break-word;overflow-wrap:break-word
        }
        .noborder{border:none !important}

        /* title block — logos + title centered together as one group */
        .doc-cell{border:none;padding:2px 4px 8px}
        .doc-head{display:flex;align-items:center;justify-content:center;gap:34px;min-height:78px}
        .doc-head .logo{height:66px;width:auto;flex:0 0 auto}
        .doc-head .titles{text-align:center;line-height:1.3;flex:0 0 auto}
        .t-org{font-size:13px}
        .t-doc{font-size:16px;font-weight:700;letter-spacing:.4px}

        /* info grid */
        .lbl{font-weight:700;white-space:nowrap;border:none;text-align:left;font-size:11px;padding-right:8px}
        .vbox{background:var(--box);font-weight:600;color:#111;font-size:11px;text-align:left}

        /* section bands (row 14) */
        .band{font-weight:700;text-align:center;font-size:11px;color:#000;height:22px}
        .band-grey{background:var(--grey)}
        .band-blue{background:var(--blue)}
        .band-lblue{background:var(--lblue)}

        /* column headers (row 15) */
        .colhead th{font-weight:700;text-align:center;vertical-align:middle;height:66px;color:#000}
        .h-grey{background:var(--grey)}
        .h-blue{background:var(--blue)}
        .h-lblue{background:var(--lblue)}
        .h-peri{background:var(--peri)}     /* Remarks header inside blue section */

        /* data (rows 16+) */
        tbody.data td{height:30px;text-align:center;background:#fff}
        tbody.data td.txt{text-align:left}
        tbody.data td.rmk{background:var(--peri)}   /* shaded Remarks helper columns */
        .empty-msg td{text-align:center;color:#94a3b8;font-style:italic;padding:18px;background:#fff}

        @media print{
            @page{size:A4 landscape;margin:7mm}
            body{background:#fff}
            .toolbar{display:none}
            .sheet-wrap{padding:0;overflow:visible}
            .sheet{box-shadow:none;width:auto;padding:0;border-radius:0}
            table.dpds{font-size:8px}
            .h-grey,.h-blue,.h-lblue,.h-peri,.band-grey,.band-blue,.band-lblue,
            tbody.data td.rmk,.vbox{
                -webkit-print-color-adjust:exact;print-color-adjust:exact
            }
        }
    </style>
</head>
<body>

    <div class="toolbar">
        <span class="title">School Partnerships Data Sheet</span>
        <?php if($month_label): ?>
            <span class="range">Month: <?= dpds_e($month_label) ?></span>
        <?php endif; ?>
        <a class="btn btn-export" href="<?= base_url('Brigada/contribution_dpds_export') ?>?<?= $export_qs ?>">&#128229; Export to Excel</a>
        <a class="btn btn-print" href="javascript:window.print()">&#128424; Print</a>
    </div>

    <div class="sheet-wrap">
        <div class="sheet">
            <table class="dpds">
                <colgroup>
                    <col style="width:120px"><!--B General Partner Type-->
                    <col style="width:150px"><!--C Specific Partner Type-->
                    <col style="width:95px"> <!--D Remarks-->
                    <col style="width:120px"><!--E Partner Name-->
                    <col style="width:85px"> <!--F Remarks-->
                    <col style="width:85px"> <!--G Partner Contact Details-->
                    <col style="width:105px"><!--H Contribution Type-->
                    <col style="width:95px"> <!--I Remarks-->
                    <col style="width:100px"><!--J Specific Contribution Type-->
                    <col style="width:85px"> <!--K Remarks-->
                    <col style="width:78px"> <!--L Unit of Contribution-->
                    <col style="width:72px"> <!--M Quantity Contributed-->
                    <col style="width:110px"><!--N Actual Amount-->
                    <col style="width:90px"> <!--O Beneficiary Learners-->
                    <col style="width:78px"> <!--P Beneficiary Personnel-->
                    <col style="width:105px"><!--Q Form of Agreement-->
                    <col style="width:92px"> <!--R Signatory Name-->
                    <col style="width:92px"> <!--S Signatory Designation-->
                    <col style="width:110px"><!--T Agreement Start-->
                    <col style="width:120px"><!--U Agreement End-->
                    <col style="width:92px"> <!--V Project Category-->
                    <col style="width:115px"><!--W Project Name-->
                    <col style="width:80px"> <!--X Status-->
                    <col style="width:95px"> <!--Y Remarks-->
                    <col style="width:105px"><!--Z Initiated by-->
                </colgroup>

                <tbody>
                    <!-- ===== TITLE BLOCK ===== -->
                    <tr>
                        <td colspan="25" class="doc-cell">
                            <div class="doc-head">
                                <img class="logo" src="<?= base_url('assets/images/report/ke.png') ?>" alt="DepEd Seal">
                                <div class="titles">
                                    <div class="t-org">Department of Education</div>
                                    <div class="t-doc">SCHOOL PARTNERSHIPS DATA SHEET</div>
                                </div>
                                <img class="logo" src="<?= base_url('assets/images/report/deped.png') ?>" alt="DepED">
                            </div>
                        </td>
                    </tr>

                    <!-- ===== INFO GRID ===== -->
                    <tr>
                        <td class="lbl">Fiscal Year:</td>
                        <td class="vbox" colspan="5"><?= dpds_e($sy) ?></td>
                        <td class="noborder" colspan="19"></td>
                    </tr>
                    <tr>
                        <td class="lbl">Month:</td>
                        <td class="vbox" colspan="5"><?= dpds_e($month_label) ?></td>
                        <td class="noborder" colspan="19"></td>
                    </tr>
                    <tr>
                        <td class="lbl">Region:</td>
                        <td class="vbox" colspan="5"><?= dpds_e($region) ?></td>
                        <td class="noborder" colspan="19"></td>
                    </tr>
                    <tr>
                        <td class="lbl">Division:</td>
                        <td class="vbox" colspan="5"><?= dpds_e($division) ?></td>
                        <td class="noborder"></td>
                        <td class="lbl" colspan="2">Prepared by:</td>
                        <td class="vbox" colspan="6"></td>
                        <td class="lbl" colspan="3">Approved by:</td>
                        <td class="vbox" colspan="7"></td>
                    </tr>
                    <tr>
                        <td class="lbl">School/LC Name:</td>
                        <td class="vbox" colspan="5"><?= dpds_e($school_name) ?></td>
                        <td class="noborder"></td>
                        <td class="lbl" colspan="2">Position/Designation:</td>
                        <td class="vbox" colspan="6"></td>
                        <td class="lbl" colspan="3">Position/Designation:</td>
                        <td class="vbox" colspan="7"></td>
                    </tr>
                    <tr>
                        <td class="lbl">School ID:</td>
                        <td class="vbox" colspan="5"><?= dpds_e($school_id) ?></td>
                        <td class="noborder"></td>
                        <td class="lbl" colspan="2">Contact No.:</td>
                        <td class="vbox" colspan="6"></td>
                        <td class="lbl" colspan="3">Contact No.:</td>
                        <td class="vbox" colspan="7"></td>
                    </tr>
                    <tr>
                        <td class="lbl">Offering:</td>
                        <td class="vbox" colspan="5"><?= dpds_e($offering) ?></td>
                        <td class="noborder"></td>
                        <td class="lbl" colspan="2">Date:</td>
                        <td class="vbox" colspan="6"></td>
                        <td class="lbl" colspan="3">Date:</td>
                        <td class="vbox" colspan="7"></td>
                    </tr>

                    <!-- spacer -->
                    <tr><td colspan="25" class="noborder" style="height:12px"></td></tr>

                    <!-- ===== SECTION BANDS (row 14) ===== -->
                    <tr>
                        <td class="band band-grey"  colspan="6">SCHOOL/LEARNING CENTER PARTNERS</td>
                        <td class="band band-blue"  colspan="9">PARTNERS' CONTRIBUTIONS</td>
                        <td class="band band-lblue" colspan="10">PARTNERSHIP AGREEMENTS</td>
                    </tr>
                </tbody>

                <!-- ===== COLUMN HEADERS (row 15) ===== -->
                <tbody class="colhead">
                    <tr>
                        <th class="h-grey">General Partner Type</th>
                        <th class="h-grey">Specific Partner Type</th>
                        <th class="h-grey">Remarks</th>
                        <th class="h-grey">Partner Name</th>
                        <th class="h-grey">Remarks</th>
                        <th class="h-grey">Partner Contact Details</th>
                        <th class="h-blue">Contribution Type</th>
                        <th class="h-peri">Remarks</th>
                        <th class="h-blue">Specific Contribution Type</th>
                        <th class="h-peri">Remarks</th>
                        <th class="h-blue">Unit of Contribution</th>
                        <th class="h-blue">Quantity Contributed</th>
                        <th class="h-blue">Actual Amount/Value of Contribution (in Pesos)</th>
                        <th class="h-blue">No. of Beneficiary Learners</th>
                        <th class="h-blue">No. of Beneficiary Personnel</th>
                        <th class="h-lblue">Form of Agreement</th>
                        <th class="h-lblue">Signatory Name</th>
                        <th class="h-lblue">Signatory Designation</th>
                        <th class="h-lblue">Agreement Start Date (dd/mm/yyyy)</th>
                        <th class="h-lblue">Agreement End Date (dd/mm/yyyy)</th>
                        <th class="h-lblue">Project Category</th>
                        <th class="h-lblue">Project Name</th>
                        <th class="h-lblue">Status of Agreement/ Project</th>
                        <th class="h-lblue">Remarks</th>
                        <th class="h-lblue">Initiated by</th>
                    </tr>
                </tbody>

                <!-- ===== DATA (rows 16+) ===== -->
                <tbody class="data">
                    <?php if(!empty($data)): foreach($data as $row): ?>
                        <tr>
                            <!-- SCHOOL/LEARNING CENTER PARTNERS -->
                            <td class="txt"><?= dpds_e(str_replace('_',' ',$row->general_type)) ?></td>
                            <td class="txt"><?= dpds_e(str_replace('_',' ',$row->specific_type)) ?></td>
                            <td class="rmk"></td>
                            <td class="txt"><?= dpds_e(str_replace('_',' ',$row->pname)) ?></td>
                            <td class="rmk"></td>
                            <td class="txt"><?= dpds_e(trim(($row->contact_person ?? '').' '.($row->contact ?? ''))) ?></td>

                            <!-- PARTNERS' CONTRIBUTIONS -->
                            <td class="txt"><?= dpds_e(str_replace('_',' ',$row->cname)) ?></td>
                            <td class="rmk"></td>
                            <td class="txt"><?= dpds_e($row->spicific_contribution) ?></td>
                            <td class="rmk"></td>
                            <td><?= dpds_e($row->unit_of_contribution) ?></td>
                            <td><?= dpds_e($row->quantity_of_conftribution) ?></td>
                            <td><?= ($row->amount !== null && $row->amount !== '') ? number_format((float)$row->amount, 2) : '' ?></td>
                            <td><?= dpds_e($row->no_beneficiary_learnes) ?></td>
                            <td><?= dpds_e($row->no_beneficiary_personnel) ?></td>

                            <!-- PARTNERSHIP AGREEMENTS -->
                            <td class="txt"><?= dpds_e($row->form_of_agreement) ?></td>
                            <td></td><!-- Signatory Name -->
                            <td></td><!-- Signatory Designation -->
                            <td><?= dpds_e($row->agreement_started) ?></td>
                            <td><?= dpds_e($row->agreement_end) ?></td>
                            <td class="txt"><?= dpds_e($row->project_category) ?></td>
                            <td class="txt"><?= dpds_e($row->project_name) ?></td>
                            <td class="txt"><?= dpds_e($row->status_agreement) ?></td>
                            <td class="txt"><?= dpds_e($row->remarks) ?></td>
                            <td class="txt"><?= dpds_e($row->initiated_by) ?></td>
                        </tr>
                    <?php endforeach; else: ?>
                        <tr class="empty-msg"><td colspan="25">No records found for the selected month.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>