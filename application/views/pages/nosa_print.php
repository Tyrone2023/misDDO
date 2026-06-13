<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>A4 Multi Page Print</title>

  <style>
    /* ===== TRUE A4 PRINT ===== */
    @page { size: A4; margin: 12mm;}

    html, body {
      margin: 0;
      padding: 0;
      font-family: Arial, Helvetica, sans-serif;
      -webkit-print-color-adjust: exact;
      print-color-adjust: exact;
      background: #f2f2f2; /* screen preview */
    }

    /* One printed page */
    .sheet {
      width: 210mm;
      min-height: 297mm;
      background: #fff;
      position: relative;
      overflow: hidden;

      /* force new page after each sheet */
      break-after: page;
      page-break-after: always;
    }

    /* prevent last blank page (best effort) */
    .sheet:last-child {
      break-after: auto;
      page-break-after: auto;
    }

     .seal{
        display:flex;
        justify-content:center;
        align-items:center;
        margin-bottom: 2mm;
      }
      .seal img{
        width: 22mm;
        height: 22mm;
        object-fit:contain;
      }
      .seal .seal-placeholder{
        width:22mm;height:22mm;
        border:1px solid #999;
        border-radius:50%;
        display:flex;align-items:center;justify-content:center;
        font-size:10px;color:#666;
      }

      .center-text{
        text-align:center;
        line-height:1.1;
      }
      .center-text .l1{ font-size: 12px; font-weight:600; }
      .center-text .l2{ font-size: 20px; font-weight:700; letter-spacing:.2px; }
      .center-text .l3{ font-size: 13px; font-weight:600; margin-top:2px; }
      .center-text .l4{ font-size: 12px; font-weight:600; margin-top:1px; }

      .notice-row{
        display:flex;
        align-items:flex-end;
        justify-content:center;
        position:relative;
        margin-bottom: 10mm;
      }

      .notice-title{
        text-align:center;
        font-weight:800;
        letter-spacing:.6px;
        font-size: 14px;
        margin-top:15px;
        font-family: "Times New Roman", Times, serif;
      }

      .date-block{
        position:absolute;
        right:0;
        top:40px;
        padding-right:0.5in;
        transform: translateY(-10%);
        font-size:12px;
        display:flex;
        gap:6px;
        align-items:flex-end;
      }
      .date-block .label{ white-space:nowrap; }
      .date-block .date{
        display:inline-block;
        min-width: 42mm;
        text-align:left;
        padding: 0 1mm 1px 1mm;
        border-bottom: 1px solid #000;
        font-style: italic;
        font-weight: 600;
      }

      /* lower-left recipient box */
      .recipient-area{
        display:flex;
        justify-content:flex-start;
        margin-top: 6mm;
      }
      .recipient{
        font-size:12px;
        line-height:1.2;
      }
      .recipient .name{
        font-weight:800;
        text-transform:uppercase;
        margin-bottom:1mm;
      }
      .recipient .sub{ font-size:11px; }

      .mini-grid{
        margin-top:2mm;
        display:inline-grid;
        grid-template-columns: 12mm 28mm 14mm;
        border:1px solid #000;
        font-size:11px;
      }
      .mini-grid > div{
        border-right:1px solid #000;
        padding: 0.6mm 1mm;
        line-height:1;
        display:flex;
        align-items:center;
        justify-content:center;
      }
      .mini-grid > div:last-child{ border-right:0; }

      /* optional “photo paper wrinkle” look OFF by default */
      .muted-bg { background:transparent; }

    /* screen preview styling */
    @media screen {
      body { padding: 12px 0; }
      .sheet {
        margin: 12px auto;
        box-shadow: 0 6px 18px rgba(0,0,0,.12);
      }
    }

    @media print {
      body { background: #fff; padding: 0; }
      .sheet { margin: 0; box-shadow: none; }
    }

    .blocker {clear:both !important}

    /* sections */
    .header {
      padding: 10mm 10mm 6mm;
      border-bottom: 1px solid #222;
    }
    .header .title {
      font-size: 16pt;
      font-weight: 700;
      letter-spacing: .4px;
      margin: 0;
    }
    .header .sub {
      margin-top: 4mm;
      font-size: 10.5pt;
      line-height: 1.35;
    }

    .body {
      padding: 8mm 10mm;
      font-size: 11.5pt;
      line-height: 1.5;
    }

    .footer {
      position: absolute;
      left: 10mm;
      right: 10mm;
      bottom: 10mm;
      font-size: 10pt;
      border-top: 1px solid #222;
      padding-top: 4mm;
      display: flex;
      justify-content: space-between;
      gap: 10mm;
    }

    /* table style */
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 6mm;
      font-size: 11pt;
    }
    th, td {
      vertical-align: top;
    }
    th { font-weight: 700; text-align: left; }

    /* avoid breaking inside important blocks */
    .no-break { break-inside: avoid; page-break-inside: avoid; }

    /* placeholder look (optional) */
    .ph {
      display: inline-block;
      border-bottom: 1px dotted #222;
      min-width: 60mm;
      height: 5mm;
      vertical-align: bottom;
    }
    .box {
      border: 1px solid #222;
      min-height: 30mm;
      padding: 6px;
    }
  </style>
</head>

<body>

  <!-- ========================= -->
  <!-- SHEET 1 (repeat per record) -->
  <!-- ========================= -->
  <div class="sheet">
    <div class="header">
      <div class="seal">
        <!-- Replace with actual logo path if you have it -->
        <!-- <img src="logo.png" alt="DepEd Seal"> -->
        <div class="seal-placeholder"><img class="logo" src="<?= base_url(); ?>/assets/images/report/ke.png" alt=""></div>
      </div>

      <!-- Center headings -->
      <div class="center-text">
        <div class="l1" style='font-family: "Old English Text MT"; font-size:12px'>Republic of the Philippines</div>
        <div class="l2" style='font-family: "Old English Text MT"; font-size:16px'>Department of Education</div>
        <div class="l3" style='font-family: "Trajan Pro"; font-size:9px'>Region XI</div>
        <div class="l4" style='font-family: "Trajan Pro"; font-size:9px;'>Schools Division of Davao Oriental</div>
      </div>
    </div>

    <div class="notice-row">
        <div class="notice-title">NOTICE OF SALARY ADJUSTMENT</div>

        <div class="date-block">
          <span class="label">Date :</span>
          <span class="date">January 27, 2026</span>
        </div>
      </div>

      <!-- Lower-left recipient block -->
      <div class="recipient-area">
        <div class="recipient" style="padding-left:0.5in">
          <div class="name">CHONA M. TAN</div>
          <div class="sub">Governor Generoso North</div>
          <div class="sub">Division of Davao Oriental</div>

          <div class="mini-grid" aria-label="reference boxes">
            <div>112</div>
            <div>6343980</div>
            <div>014</div>
          </div>
        </div>
      </div>

    <div class="body">
      <p>Sir/Madam:</p>
      <p style="text-indent: 0.5in">Pursuant to National Budget Circular No. <u>601</u> dated January 22, 2026, implementing
        <br />Executive Order No. 64 Dated August 2,2024, your salary is hereby adjusted effective
        <br /><span style="display:block; text-indent:0.5in;"><strong><i>January 1, 2026</i></strong> &nbsp; &nbsp; &nbsp; as follows:</span>
      </p>
     

      <div class="no-break">
        <table>
            <tr>
                <td>1</td>
                <td>Adjusted monthly basic salary effective</td>
                <td></td>
                <td style="text-align:center; border-bottom:1px solid #222" colspan="2"><strong><i>January 1, 2026</i></strong></td>
                <td>under the</td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td>new Salary Schedule  &nbsp; &nbsp; <i>SG -</i></td>
                <td style="text-align:center; border-bottom:1px solid #222"><strong> &nbsp; &nbsp; 16 &nbsp; &nbsp; </strong></td>
                <td><i>Step - </i></td>
                <td style="text-align:center; border-bottom:1px solid #222"> &nbsp; &nbsp; 8 &nbsp; &nbsp; </td>
                <td style="text-align:right"><strong>P</strong></td>
                <td style="text-align:center; border-bottom:1px solid #222"><strong><i>49,020.00</i></strong></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>2</td>
                <td>Actual monthly basic salary as of</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td>December 31, 2025  &nbsp; &nbsp;  &nbsp; &nbsp;  &nbsp; &nbsp;  &nbsp; &nbsp; <i>SG -</i></td>
                <td style="text-align:center; border-bottom:1px solid #222"><strong> &nbsp; &nbsp; 16 &nbsp; &nbsp; </strong></td>
                <td><i>Step - </i></td>
                <td style="text-align:center; border-bottom:1px solid #222"> &nbsp; &nbsp; 8 &nbsp; &nbsp; </td>
                <td style="text-align:right"><strong>P</strong></td>
                <td style="text-align:center; border-bottom:1px solid #222"><strong><i>46,730.00</i></strong></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>3</td>
                <td>Monthly salary adjustment effective</td>
                <td></td>
                <td style="text-align:center; border-bottom:1px solid #222" colspan="2"><strong><i>January 1, 2026</i></strong></td>
                <td style="text-align:right"><strong>P</strong></td>
                <td style="text-align:center; border-bottom:1px solid #222"><strong><i>2,290.00</i></strong></td>
                
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td style="text-align:center" colspan="2">(1 less 2 total)</td>
                <td></td>
                <td></td>
            </tr>
        </table>
      </div>

      <p style="text-indent: 0.5in">It is understood that this salary adjustment is subject to review and post-audit, and to appropriate re-adjustment and refund if found not in order.</p>

      <div>
        <div style="float:right; margin-right:100px">
          <p style="margin-bottom:50px">Very truly yours,</p>
          <p style="line-height:1.2em; text-indent: 0.5in"><b>DR. JOSEPHINE L. FADUL</b><br /><span style="text-indent: 0.5in; display:inline-block;">Schools Division Superintendent</span></p>
        </div>
        <div class="blocker"></div>
      </div>

      <div style="margin-top: 5mm; font-size:12px">
        POSITION TITLE : <span style="margin-left:40px; font-weight:bold;"><u>Nurse II</u></span><br />
        SALARY GRADE : <span style="margin-left:40px; font-weight:bold;"><u>16</u></span><br />
        item No. &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;: <span style="margin-left:40px; font-weight:bold;">fdsafdsafd</span>
      </div>
    </div>

    <!-- <div class="footer">
      <div>Prepared by: {{PreparedBy}}</div>
      <div>Page {{PageNo}} of {{TotalPages}}</div>
    </div> -->
  </div>

  <!-- ========================= -->
  <!-- SHEET 2 (example) -->
  <!-- ========================= -->
  <div class="sheet">
    <div class="header">
      <div class="seal">
        <!-- Replace with actual logo path if you have it -->
        <!-- <img src="logo.png" alt="DepEd Seal"> -->
        <div class="seal-placeholder"><img class="logo" src="<?= base_url(); ?>/assets/images/report/ke.png" alt=""></div>
      </div>

      <!-- Center headings -->
      <div class="center-text">
        <div class="l1" style='font-family: "Old English Text MT"; font-size:12px'>Republic of the Philippines</div>
        <div class="l2" style='font-family: "Old English Text MT"; font-size:16px'>Department of Education</div>
        <div class="l3" style='font-family: "Trajan Pro"; font-size:9px'>Region XI</div>
        <div class="l4" style='font-family: "Trajan Pro"; font-size:9px;'>Schools Division of Davao Oriental</div>
      </div>
    </div>

    <div class="notice-row">
        <div class="notice-title">NOTICE OF SALARY ADJUSTMENT</div>

        <div class="date-block">
          <span class="label">Date :</span>
          <span class="date">January 27, 2026</span>
        </div>
      </div>

      <!-- Lower-left recipient block -->
      <div class="recipient-area">
        <div class="recipient" style="padding-left:0.5in">
          <div class="name">CHONA M. TAN</div>
          <div class="sub">Governor Generoso North</div>
          <div class="sub">Division of Davao Oriental</div>

          <div class="mini-grid" aria-label="reference boxes">
            <div>112</div>
            <div>6343980</div>
            <div>014</div>
          </div>
        </div>
      </div>

    <div class="body">
      <p>Sir/Madam:</p>
      <p style="text-indent: 0.5in">Pursuant to National Budget Circular No. <u>601</u> dated January 22, 2026, implementing
        <br />Executive Order No. 64 Dated August 2,2024, your salary is hereby adjusted effective
        <br /><span style="display:block; text-indent:0.5in;"><strong><i>January 1, 2026</i></strong> &nbsp; &nbsp; &nbsp; as follows:</span>
      </p>
     

      <div class="no-break">
        <table>
            <tr>
                <td>1</td>
                <td>Adjusted monthly basic salary effective</td>
                <td></td>
                <td style="text-align:center; border-bottom:1px solid #222" colspan="2"><strong><i>January 1, 2026</i></strong></td>
                <td>under the</td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td>new Salary Schedule  &nbsp; &nbsp; <i>SG -</i></td>
                <td style="text-align:center; border-bottom:1px solid #222"><strong> &nbsp; &nbsp; 16 &nbsp; &nbsp; </strong></td>
                <td><i>Step - </i></td>
                <td style="text-align:center; border-bottom:1px solid #222"> &nbsp; &nbsp; 8 &nbsp; &nbsp; </td>
                <td style="text-align:right"><strong>P</strong></td>
                <td style="text-align:center; border-bottom:1px solid #222"><strong><i>49,020.00</i></strong></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>2</td>
                <td>Actual monthly basic salary as of</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td>December 31, 2025  &nbsp; &nbsp;  &nbsp; &nbsp;  &nbsp; &nbsp;  &nbsp; &nbsp; <i>SG -</i></td>
                <td style="text-align:center; border-bottom:1px solid #222"><strong> &nbsp; &nbsp; 16 &nbsp; &nbsp; </strong></td>
                <td><i>Step - </i></td>
                <td style="text-align:center; border-bottom:1px solid #222"> &nbsp; &nbsp; 8 &nbsp; &nbsp; </td>
                <td style="text-align:right"><strong>P</strong></td>
                <td style="text-align:center; border-bottom:1px solid #222"><strong><i>46,730.00</i></strong></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>3</td>
                <td>Monthly salary adjustment effective</td>
                <td></td>
                <td style="text-align:center; border-bottom:1px solid #222" colspan="2"><strong><i>January 1, 2026</i></strong></td>
                <td style="text-align:right"><strong>P</strong></td>
                <td style="text-align:center; border-bottom:1px solid #222"><strong><i>2,290.00</i></strong></td>
                
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td style="text-align:center" colspan="2">(1 less 2 total)</td>
                <td></td>
                <td></td>
            </tr>
        </table>
      </div>

      <p style="text-indent: 0.5in">It is understood that this salary adjustment is subject to review and post-audit, and to appropriate re-adjustment and refund if found not in order.</p>

      <div>
        <div style="float:right; margin-right:100px">
          <p style="margin-bottom:50px">Very truly yours,</p>
          <p style="line-height:1.2em; text-indent: 0.5in"><b>DR. JOSEPHINE L. FADUL</b><br /><span style="text-indent: 0.5in; display:inline-block;">Schools Division Superintendent</span></p>
        </div>
        <div class="blocker"></div>
      </div>

      <div style="margin-top: 5mm; font-size:12px">
        POSITION TITLE : <span style="margin-left:40px; font-weight:bold;"><u>Nurse II</u></span><br />
        SALARY GRADE : <span style="margin-left:40px; font-weight:bold;"><u>16</u></span><br />
        item No. &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;: <span style="margin-left:40px; font-weight:bold;">fdsafdsafd</span>
      </div>
    </div>

    <!-- <div class="footer">
      <div>Prepared by: {{PreparedBy}}</div>
      <div>Page {{PageNo}} of {{TotalPages}}</div>
    </div> -->
  </div>

</body>
</html>
