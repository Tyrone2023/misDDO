<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>RFTP Form No. 2-A (DBM-DepEd JC 01 s.2025) - Complete</title>
  <link rel="shortcut icon" href="<?= base_url(); ?>assets/images/hris.ico">
  <link href="https://fonts.googleapis.com/css2?family=UnifrakturCook:wght@700&display=swap" rel="stylesheet">

  <style>
    @page { size: A4; margin: 2mm; background:#fff; }

    html, body{
      background:#fff !important;
    }

    @media print {
      body { padding:0 !important; }
      .no-print { display:none !important; }
      .page { box-shadow:none !important; margin:0 !important; }
      .page-break { page-break-before: always; }

      *{
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
      }
    }


    body{
      margin:0;
      padding:20px;
      font-family:"Times New Roman", Times, serif;
      color:#000;
    }

    .no-print{ text-align:center; margin-bottom:14px; }
    .no-print button{
      padding:10px 14px;
      border:0;
      border-radius:10px;
      background:#111;
      color:#fff;
      cursor:pointer;
      font-family: Arial, Helvetica, sans-serif;
    }

    .page{
      width:210mm;
      min-height:297mm;
      margin:0 auto 20px auto;
      background:#fff; /* paper stays white */
      box-shadow:0 8px 30px rgba(0,0,0,.08);
      position:relative;
      overflow:hidden;
    }
    .page-content{ padding:12mm; }

    .topline{
      display:flex;
      justify-content:space-between;
      font-size:12px;
      margin-bottom:6px;
    }

    .center{ text-align:center; }
    .seal{
      width:52px; height:52px;
      border:1px solid #000;
      border-radius:50%;
      display:inline-flex;
      align-items:center;
      justify-content:center;
      font-size:9px;
      margin:6px auto 0 auto;
    }
    .seal img{
      width:52px; height:52px;
    }
    .rp{ font-size:12px; font-family: "Old English Text MT", "UnifrakturCook", cursive;}
    .dept{ font-size:16px; font-weight:bold; font-family: "Old English Text MT", "UnifrakturCook", cursive;}
    .form-title{
      font-size:13px;
      font-weight:bold;
      margin-top:12px;
      text-transform:uppercase;
    }

    /* --- lines/fields --- */
    .fields{
      width:100%;
      border-collapse:collapse;
      font-size:12px;
      margin-top:8px;
    }
    .fields td{
      padding:3px 6px;
      vertical-align:bottom;
    }
    .line{
      display:inline-block;
      border-bottom:1px solid #000;
      min-width:260px;
      height:14px;
      vertical-align:bottom;
    }
    .line.sm{ min-width:170px; }
    .line.xs{ min-width:140px; }

    .level-form{ width:100%; margin-top:6px; font-size:12px; }
    .level-form .row{
      display:grid;
      grid-template-columns: 70px 1fr 30px 1fr;
      align-items:start;
      column-gap:12px;
    }
    .level-form .label{ font-weight:bold; padding-top:2px; }
    .level-col{ display:grid; row-gap:6px; }
    .level-item{ display:flex; align-items:baseline; gap:6px; }
    .level-line{
      display:inline-block;
      width:150px;
      border-bottom:1px solid #000;
      height:12px;
    }

    /* --- headings --- */
    .sec-h{
      font-size:12px;
      font-weight:bold;
      margin-top:10px;
      text-transform:uppercase;
    }
    .section-title{
      font-size:12px;
      font-weight:bold;
      margin-top:10px;
      text-transform:uppercase;
    }
    .tiny{ font-size:11px; }
    .note{ font-size:11px; margin-top:4px; }

    /* --- tables --- */
    table.formtbl{
      width:100%;
      border-collapse:collapse;
      font-size:12px;
      margin-top:4px;
    }
    .formtbl th, .formtbl td{
      border:1px solid #000;
      padding:4px 6px;
      vertical-align:top;
    }
    .formtbl th{ text-align:center; font-weight:bold; }
    .perf td, .perf th{ padding:4px 6px; }

    /* PPST */
    .ppst th{ font-size:12px; }
    .ppst td{ font-size:12px; }
    .ppst .ncol{ width:38px; text-align:center; }
    .ppst .ocol, .ppst .vscol{ width:46px; text-align:center; }
    .ppst .domain-head{
      font-weight:bold;
      background:#f4f4f4;
      text-align:left;
    }

    /* --- signatures --- */
    .sign-row{
      display:flex;
      justify-content:space-between;
      gap:18mm;
      margin-top:10px;
      font-size:12px;
    }
    .sign-block{ width:48%; }
    .sigline{
      margin-top:18px;
      border-bottom:1px solid #000;
      height:12px;
      width:100%;
    }
    .siglabel{
      margin-top:3px;
      font-size:11px;
      text-align:center;
    }
    .center{text-align:center !important}
  </style>
</head>

<body>
  <div class="no-print">
    <button onclick="window.print()">Print / Save as PDF</button>
  </div>

  <?php 
    $staff = $this->Common->one_cond_row('hris_staff', 'IDNumber',$id); 
    $job = $this->Common->one_cond_row_select('hris_jobvacancy','jobTitle,jobID','jobID',$this->uri->segment(4)); 
  ?>

  <!-- =========================
       PAGE 1 (WITH HEADER)
       ========================= -->
  <div class="page">
    <div class="page-content">

      <div class="topline">
        <div><b>DBM-DepEd JC</b> <b>01</b> s.2025, <b>Form No. 2-A</b></div>
        <div><i>For Teacher II, III, IV, V, VI, VII</i></div>
      </div>

      <!-- BIG HEADER only here -->
      <div class="center">
        <div class="seal"><img src="<?= base_url(); ?>assets/images/ke.png" alt=""></div>
        <div class="rp">Republic of the Philippines</div>
        <div class="dept">Department of Education</div>
        <div class="form-title">RECLASSIFICATION FORM FOR TEACHING POSITIONS (RFTP)</div>
      </div>

      <table class="fields">
        <tr>
          <td style="width:50%;"><b>Name:</b> <span class="line"><?= strtoupper($staff->FirstName); ?> <?= !empty($staff->FirstName) ? mb_strtoupper(mb_substr($staff->FirstName, 0, 1)) . '.' : '' ?> <?= strtoupper($staff->LastName); ?> <?= strtoupper($staff->NameExtn); ?></span></td>
          <td style="width:50%;"><b>Current Position:</b> <span class="line sm"><?= strtoupper($staff->empPosition); ?></span></td>
        </tr>
        <tr>
          <td><b>Position Applied:</b> <span class="line sm"><?= isset($job) && isset($job->jobTitle) ? $job->jobTitle : ''; ?></span></td>
          <td><b>Item Number:</b> <span class="line xs"></span></td>
        </tr>
        <tr>
          <td><b>Station/School:</b> <span class="line sm"><?= strtoupper($staff->Department); ?></span></td>
          <td><b>SG/Annual Salary:</b> <span class="line xs"><?= strtoupper($staff->sgNo); ?>/<?= strtoupper($staff->authAnSalary); ?></span></td>
        </tr>
      </table>

      <div class="level-form">
        <div class="row">
          <div class="label">Level:</div>
          <div class="level-col">
            <div class="level-item"><span class="level-line"></span><span>Kindergarten</span></div>
            <div class="level-item"><span class="level-line"></span><span>Elementary</span></div>
          </div>
          <div></div>
          <div class="level-col">
            <div class="level-item"><span class="level-line"></span><span>Junior High School</span></div>
            <div class="level-item"><span class="level-line"></span><span>Senior High School</span></div>
          </div>
        </div>
      </div>

      <div class="sec-h">I.&nbsp; QUALIFICATION STANDARDS</div>
      <table class="formtbl tiny">
        <thead>
          <tr>
            <th style="width:20%;">Elements</th>
            <th style="width:30%;">QS of the Position<br><span style="font-weight:normal;">To be filled-out by the HRMO</span></th>
            <th style="width:30%;">QS of the Applicant<br><span style="font-weight:normal;">To be filled-out by the HRMO</span></th>
            <th style="width:20%;">Remarks</th>
          </tr>
        </thead>
        <tbody>
          <tr><td>Education</td><td style="height:22px;"></td><td></td><td></td></tr>
          <tr><td>Training</td><td style="height:22px;"></td><td></td><td></td></tr>
          <tr><td>Experience</td><td style="height:22px;"></td><td></td><td></td></tr>
          <tr><td>Eligibility</td><td style="height:22px;"></td><td></td><td></td></tr>
        </tbody>
      </table>
      <div class="note"><b>Note:</b> Indicate the QS of the Position Applied for based on the CSC-Approved QS</div>

      <div class="sec-h">II.&nbsp; PERFORMANCE REQUIREMENTS</div>
      <div class="tiny" style="margin-top:2px;">
        <div>1.&nbsp; Copy of duly approved IPCRF for the school year immediately preceding the application.</div>
        <div>2.&nbsp; The applicant must meet the following performance requirements depending on the position applied for.</div>
      </div>

      <table class="formtbl perf tiny" style="margin-top:6px;">
        <thead>
          <tr>
            <th style="width:20%;">Position Applied</th>
            <th>Performance Requirement/s</th>
          </tr>
        </thead>
        <tbody>
          <tr><td class="center">Teacher II</td><td>At least 6 Proficient COIs at Very Satisfactory; and<br>At least 4 Proficient NCOIs at Very Satisfactory</td></tr>
          <tr><td class="center">Teacher III</td><td>At least 12 Proficient COIs at Very Satisfactory; and<br>At least 8 Proficient NCOIs at Very Satisfactory</td></tr>
          <tr><td class="center">Teacher IV</td><td>21 Proficient COIs at Very Satisfactory; and<br>16 Proficient NCOIs at Very Satisfactory</td></tr>
          <tr><td class="center">Teacher V</td><td>At least 6 Proficient COIs at Outstanding; and<br>At least 4 Proficient NCOIs at Outstanding</td></tr>
          <tr><td class="center">Teacher VI</td><td>At least 12 Proficient COIs at Outstanding; and At least 4 Proficient NCOIs at Very Satisfactory; and 4 Proficient NCOIs at Outstanding</td></tr>
          <tr><td class="center">Teacher VII</td><td>At least 18 Proficient COIs at Outstanding; and At least 6 Proficient NCOIs at Very Satisfactory and 6 Proficient NCOIs at Outstanding</td></tr>
        </tbody>
      </table>

      <div class="sec-h" style="margin-top:10px;">Summary of the Achievement of PPST Indicators</div>
      <table class="formtbl ppst">
        <thead>
          <tr>
            <th colspan="2" style="text-align:left;">Domain / Strand / Indicators</th>
            <th class="ocol">O</th>
            <th class="vscol">VS</th>
          </tr>
          <tr>
            <th class="ncol">No.</th>
            <th style="text-align:left;">Domain 1. Content Knowledge and Pedagogy</th>
            <th class="ocol"></th>
            <th class="vscol"></th>
          </tr>
        </thead>
        <tbody>
          <?php $indicator = $this->Common->one_cond('hris_rftp_domain_indicators','domain_id',1); $ivy=1; foreach($indicator as $row){
            $rftp1 = $this->Common->two_cond_row('hris_rftp','IDNumber',$id,'fy',$fy);
            $field = 'q' . $row->id;
            ?>
          <tr><td class="ncol"><?= $ivy++; ?></td><td><?= $row->description; ?></td><td class="center"><?= ($rftp1->$field == 5) ? '&#10003;' : ""; ?></td><td class="center"><?= ($rftp1->$field == 4) ? '&#10003;' : ""; ?> </td></tr>
          <?php } ?>
        </tbody>
      </table>

    </div>
  </div>

  <!-- =========================
       PAGE 2 (NO BIG HEADER)
       ========================= -->
  <div class="page page-break">
    <div class="page-content">


      <table class="formtbl ppst" style="margin-top:0;">
        <thead>
          <tr>
            <th colspan="2" style="text-align:left;">Domain / Strand / Indicators</th>
            <th class="ocol">O</th>
            <th class="vscol">VS</th>
          </tr>
        </thead>
        <tbody>
          <tr><th class="ncol domain-head"></th><th class="domain-head" colspan="3">Domain 2. Learning Environment</th></tr>
          <?php $indicator2 = $this->Common->one_cond('hris_rftp_domain_indicators','domain_id',2);foreach($indicator2 as $row){
            $rftp2 = $this->Common->two_cond_row('hris_rftp','IDNumber',$id,'fy',$fy);
            $field = 'q' . $row->id;
            ?>
          <tr><td class="ncol"><?= $ivy++; ?></td><td><?= $row->description; ?></td><td class="center"><?= ($rftp2->$field == 5) ? '&#10003;' : ""; ?></td><td class="center"><?= ($rftp2->$field == 4) ? '&#10003;' : ""; ?></td></tr>
          <?php } ?>

          <tr><th class="ncol domain-head"></th><th class="domain-head" colspan="3">Domain 3. Diversity of Learners</th></tr>
          <?php $indicator3 = $this->Common->one_cond('hris_rftp_domain_indicators','domain_id',3); foreach($indicator3 as $row){
            $rftp3 = $this->Common->two_cond_row('hris_rftp','IDNumber',$id,'fy',$fy);
            $field = 'q' . $row->id;
            ?>
          <tr><td class="ncol"><?= $ivy++; ?></td><td><?= $row->description; ?></td><td class="center"><?= ($rftp3->$field == 5) ? '&#10003;' : ""; ?></td><td class="center"><?= ($rftp3->$field == 4) ? '&#10003;' : ""; ?></td></tr>
          <?php } ?>

          <tr><th class="ncol domain-head"></th><th class="domain-head" colspan="3">Domain 4. Curriculum and Planning</th></tr>
          <?php $indicator4 = $this->Common->one_cond('hris_rftp_domain_indicators','domain_id',4); foreach($indicator4 as $row){
            $rftp4 = $this->Common->two_cond_row('hris_rftp','IDNumber',$id,'fy',$fy);
            $field = 'q' . $row->id;
            ?>
          <tr><td class="ncol"><?= $ivy++; ?></td><td><?= $row->description; ?></td><td class="center"><?= ($rftp4->$field == 5) ? '&#10003;' : ""; ?></td><td class="center"><?= ($rftp4->$field == 4) ? '&#10003;' : ""; ?></td></tr>
          <?php } ?>

          <tr><th class="ncol domain-head"></th><th class="domain-head" colspan="3">Domain 5. Assessment and Reporting</th></tr>
          <?php $indicator5 = $this->Common->one_cond('hris_rftp_domain_indicators','domain_id',5); foreach($indicator5 as $row){
            $rftp5 = $this->Common->two_cond_row('hris_rftp','IDNumber',$id,'fy',$fy);
            $field = 'q' . $row->id;
            ?>
          <tr><td class="ncol"><?= $ivy++; ?></td><td><?= $row->description; ?></td><td class="center"><?= ($rftp5->$field == 5) ? '&#10003;' : ""; ?></td><td class="center"><?= ($rftp5->$field == 4) ? '&#10003;' : ""; ?></td></tr>
          <?php } ?>
        </tbody>
      </table>

    </div>
  </div>

  <!-- =========================
       PAGE 3 (NO BIG HEADER)
       ========================= -->
  <div class="page page-break">
    <div class="page-content">

      
      <table class="formtbl ppst" style="margin-top:0;">
        <thead>
          <tr>
            <th colspan="2" style="text-align:left;">Domain / Strand / Indicators</th>
            <th class="ocol">O</th>
            <th class="vscol">VS</th>
          </tr>
        </thead>
        <tbody>
          <tr><th class="ncol domain-head"></th><th class="domain-head" colspan="3">Domain 6. Community Linkages and Professional Engagement</th></tr>
          <?php $indicator6 = $this->Common->one_cond('hris_rftp_domain_indicators','domain_id',6); foreach($indicator6 as $row){ 
            $rftp6 = $this->Common->two_cond_row('hris_rftp','IDNumber',$id,'fy',$fy);
            $field = 'q' . $row->id;
            ?>
          <tr><td class="ncol"><?= $ivy++; ?></td><td><?= $row->description; ?></td><td class="center"><?= ($rftp6->$field == 5) ? '&#10003;' : ""; ?></td><td class="center"><?= ($rftp6->$field == 4) ? '&#10003;' : ""; ?></td></tr>
          <?php } ?>

          <tr><th class="ncol domain-head"></th><th class="domain-head" colspan="3">Domain 7. Personal Growth and Professional Development</th></tr>
          <?php $indicator7 = $this->Common->one_cond('hris_rftp_domain_indicators','domain_id',7); foreach($indicator7 as $row){
            $rftp7 = $this->Common->two_cond_row('hris_rftp','IDNumber',$id,'fy',$fy);
            $field = 'q' . $row->id;
            ?>
          <tr><td class="ncol"><?= $ivy++; ?></td><td><?= $row->description; ?></td><td class="center"><?= ($rftp7->$field == 5) ? '&#10003;' : ""; ?></td><td class="center"><?= ($rftp7->$field == 4) ? '&#10003;' : ""; ?></td></tr>
          <?php } ?>

          <tr>
            <td class="ncol"></td>
            <td class="center"><b>Total Number of O and VS</b></td>
            <td></td><td></td>
          </tr>
        </tbody>
      </table>

      <div class="section-title">III. Comparative Assessment Result</div>
      <table class="formtbl tiny">
        <thead>
          <tr>
            <th>Education</th>
            <th>Training</th>
            <th>Experience</th>
            <th>Performance</th>
            <th>Classroom Observable<br>Indicators</th>
            <th>Non-Classroom Observable<br>Indicators</th>
            <th>Total Score</th>
          </tr>
        </thead>
        <tbody>
          <tr style="height:34px;">
            <td></td><td></td><td></td><td></td><td></td><td></td><td></td>
          </tr>
        </tbody>
      </table>

      <div class="sign-row">
        <div class="sign-block">
          <div>Conforme:</div>
          <div class="sigline"></div>
          <div class="siglabel">Teacher Applicant</div>
        </div>
        <div class="sign-block">
          <div>Attested by:</div>
          <div class="sigline"></div>
          <div class="siglabel">HRMPSB Chair</div>
        </div>
      </div>

      <div class="section-title">IV. DepEd Schools Division Office Action</div>
      <table class="formtbl tiny">
        <thead>
          <tr><th colspan="6" class="center">Reclassification of Position</th></tr>
          <tr>
            <th style="width:18%;">From</th>
            <th style="width:14%;">Salary Grade</th>
            <th style="width:18%;">To</th>
            <th style="width:14%;">Salary Grade</th>
            <th style="width:18%;">Date Processed</th>
            <th>Remarks</th>
          </tr>
        </thead>
        <tbody>
          <tr style="height:34px;">
            <td></td><td></td><td></td><td></td><td></td><td></td>
          </tr>
        </tbody>
      </table>

      <div class="sign-row">
        <div class="sign-block" style="margin-top:80px">
          <div>Certified Correct</div>
          <div class="sigline"></div>
          <div class="siglabel">Administrative Officer V (Admin Services)</div>
        </div>
        <div class="sign-block">
          <div>Evaluated by:</div>
          <div class="sigline"></div>
          <div class="siglabel">Administrative Officer IV (HRMO)</div>
        </div>
      </div>

      <div class="sign-row">
        <div class="sign-block" style="width:100%;">
          <div>Recommending Approval:</div>
          <div class="sigline"></div>
          <div class="siglabel">Schools Division Superintendent</div>
        </div>
      </div>

    </div>
  </div>

</body>
</html>
