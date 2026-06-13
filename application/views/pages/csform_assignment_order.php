<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Assignment Order</title>

  <style>
    @font-face {
  font-family: "WHRCRT+Old English Five";
  src: url("data:application/octet-stream;base64,d09GRgABAAAAABB4AA0AAAAAFxQAAQABAAAAAAAAAAAAAAAAAAAAAAAAAABPUy8yAAABMAAAAEEAAABOZqnSPWNtYXAAAAF0AAAAjQAAAyAivimWY3Z0IAAAAgQAAAAEAAAABABkADJmcGdtAAACCAAAAMMAAADs+cfNHGdseWYAAALMAAALYQAADltUP1IgaGVhZAAADjAAAAAvAAAANjHqi8hoaGVhAAAOYAAAAB4AAAAkCbMC7mhtdHgAAA6AAAAAXAAAAFw55QRNbG9jYQAADtwAAABTAAAAYAAArR1tYXhwAAAPMAAAACAAAAAgCRUSCG5hbWUAAA9QAAAA+gAAAgSnIZ7XcG9zdAAAEEwAAAAMAAAAIAADAABwcmVwAAAQWAAAACAAAAAg6S1Y93jaY2BgUmGcwMDKwMFwBggZGOXgdBoTAwMDEwMrOxOIYmlgYGBmQAIuwSEMDA5AnaWs0f9WMTSwRjOGAgBk6goXAAAAeNrtjzcOwgAMRV8ChA6hDpkYmHMK2BE3CE0g0US5Omt4cIPMYMvftmxLfkAZKBlTApUgsbMKY3PNUeTGy0HKxKqjzpizYEnGijUbtuzYc+DIiTMXrty48+CZ594V3S9iaSGvy9ViRE+OPl3ahDQZEjOgKmtD4gpjIin5U/8UdaKGuvb57Pud3RuixVZPAAAAAGQAMnjaPY49CsJAEIVnTVS8gRCEWUQL2WBvZZEExCb+FDuNP2DAeAfBQpstPMvYrV0uJjoGsZn3Zubx8TyAmXvo5Pah1J28et88XHtP6ECw3cQelEFMy4TVTpaGkcNIiwsMZhwMsqXtEzp0s4PDDI/7A4eDWuVROBojw8qWMtdW85Sivy2IJsIJv5yw5jgSwulHONUEAbwk1DRz5GCY24XlSxLxNKFIa0y5yi1XSaSJJNX6NxU9l91f57Z0bo1iAPgA7vtEegB42lVXCXAb1Rl+b99qV+dqn1a7q8M611pZkiXLOh3Fh3zGjo8QO3GM4wRykBNCIHGcyy04lDQMCTSQNHTSCSShXOFoB0oZ2sAAhRZKoTPtUGhn6DBT2jKTCQwz0AMrfbuSIexKu2+v9x/f977/PUABslETRgAQYAGAta16F4D5J6kRcvwZAOQN8G8ADACwXwDtnLnyKvofehZMgq1gGtwGjoGOcnzPzWjbdegQuJbmgIu2AC9tBAfocWHv1I1TU/AQfQQO0KUgghtRgmz8u/y7oKkpoW/pZkMxz0fVqKqEGZaDSpi0FdEpS9lMB2RYhhXJE1YuZDOyJDrJG9pPbofFdtgGc4ViOyqSG4wPOrXPWcYOq8+zmSaY177yQ1HV+i+G2TzptOiHshoNR0lbkiWZQVnMsIViQe+sgBrfCauTKy8UDo8paVu3mBBwumkmN+7bVa+giGdNVggp8Tp/GC9OUCzjUHuD29oRhXAg4eMdNIsoymxsOnKUMRuENWc3nUZCuWfREopywWuFWKJ1oNyaW0rTsK87u0SA+1MpS/slec1AwSHQtAnfUw+b73+wvUTTFMVwSfvGhgYEy+3td0vcDXw4HEplU3I+AA0Gl7tZHt29d13rgN2uBDxGd8qpHrwhUeLtfm58bAXNIE82fhDemmxSRpIGKBmMr8WfiMW6VggNBxr/i6y57B2NUJ1/iBKac5n1ezpXijFAgbYrl2GFugi8wA9wmTUBH8uKKKFjpaEUZVMwmo9EawhkMxLJuYaPJLAY+t7hNvnbghBeKM91G1iaNXg5gym48e/34rucPdjPzb33odrtoxBl9xS+ysj1Do6HM3thE7Hsv1Kg4sRyDGSBr8wFbXVJOYtwEkWTUaTTZcGLoijJomZULhQluZgiFJBIE+cIwKyGsk4UEXPQQJzF5B1J9CdiRmuKHYoMLLPGQ+lC922ytOYMwnzmCOc0qnIMbldGEqNZi/VomjKuaveEIo66+g7Z7exs7xUi/R1wanWD2n0yG/CrOxyWVZXHKLZRcLFww7KA6FxMRtCqK5epbcR/BcRBCrjLVoc/kgwkY3XIlERf813PIaPxm1CtWKi2yZ8cZZYhjNeYKSFZZNV8jpyi8I8nOQ/nZ5HJFHb6w337hifNJpNxP9590y9pixFfPLl6ov2zw3haEBrs6S/GlfqcLdjRCt8X7ty6efSHnjjng7MdTw4Mjg3B0PJHSwACkWB8iXqZYGwrG9y804gSumcCK+u5wk6OIoDKBGySPe2knjuG424G0RbBPj5xwdnV0zK2btNtQeriPqciuDi18pFL8TpNvH38Limbhfc2b7n2u7sGia0CsTWHpoEI6socRQlOIBBpABjTNVKRo2YaiVlc5ZFc5RjO5pXKb1qNRt/1/SfERntImJlB05UvXZUHelyy5PAMHHApFpcN/nqYmPFeuYzOkOR3kcTbIq1tOBVCPjfCNqRLzUJ4RDBqYoBJrFpwery1NiZ6I1cRScE81uFhiTMoyuZz5NZT92GEoGx0MRA2H7yleb05wBpT+3ZlNmV5vPgw77G4yRMbpzpaNv6KssoR90t2luRo2kVRkAswbqFyPLthbRbOjll8gqBWzmfXalcTiypnJusjsZ2yUzX4ZBgoeH0RobDjrDuuRpxnPWmXLUBiDBKCzZEY8xq5XMkUF1EUB+LMV5NL0Knvp66KSSLhkramihrt8rka8Uhk5ELTxAt3816j2cpSxX370+uLUjNlNJzAtIGSzV4G2mxNYteWpyFvVU3UxW2SnRXq6yoncjesz8E7ly+qnJ5sJypIz9QhmuJTBh57P1saDDdK5c3HDbaY2xFkyeBO1WTFDgQiK0aO5lkEBOK57nUEE8AZdoEAhSiWMay03ZwdvxUZ/XNzfafuXwP/w2cjy9TKxT4+LWPz9S174dPlyijJiyZZR9ErwAfUsmAyOYhgOYBIihCWzAwjoa9LDWGaTrarGJavZkZUNPrpdUb69ISj0VGP166dkY6kB9z5PKLY0Go84whxHht8c/Ap/w4lVuR/Olg56lHMimD3k8Hfe+VzdBK9DsJABWkQLos0oGgA6mhLPRBcKG1DAMboGhe1ehfBxLJmm2Bi0AMnYBSjNNZrnq4LRFClaBGrUYJWIU/F/zF4/g2j0WHccrryL5MxvOx3tsrvbz/04sG3ByyPPP7Cb1e9vPTU1LIOjtsPzxnvQG+KEA5/WHmhRRADO+9+84j0UOWfb9x1ZHYfN0sdeQs6c2zrvOLa3Zn1+bsJQEsJuXpIRXeDoAaQHTiCyGtYAEgQ5QV4/EiXWl26WBTFZEjJS59pnGIZb6Sz1KuOztKMxCbda05y2HArevbcYUWuvHad1+OxU3Yp6unIwBczfL3kssfnL/XDc2YrvYKAqBL7dxCCRIBYNot+W8gbQooBXT14NTLXFILopCaT1cGaxwRPGWta9YvjgtfPMN7+FS9wbJ1osB4VnRY1tWd3ZmNHmrp4uyiKlUdLPGcRgoOP2fN+zscb2O0u7ho4k9iyJVH5wWQWnkwQdxYTTr1G3JE0iTQCmq9JpEHXRM0uK5LaJ9Y8gqFzfOv+PX0Pb9vJkYpn9EzjOaG5BT7TvXXNNoX3Jit/a3B6JN5KMt1Jur4efQB44NQ6x4A21zqvCa4WzEJJlYtv34fjDemZ6WZkCpW2KMo03iWHw/D5xvVr45XzGVvYbKtHp8+hhwCtsZBeSViYAQXQCsogUnb6QJi2N5YieU+plEeGjg7YSif0Ukr2vxIuVmNSq2VS37XLJkhoh8goqc26SJUn0EMfZPRLWBRVqNdWA4uL1IEzYlkZ9EzkCmFZcKaPO3PROil/9A/vzRvWrdw5zDU39bZ9eeD2x2ctsyd+XHn/niJkPfXmfsh8vMh2Sm73+KHV4WzrODAeCtUfdjcFVvU/0n8MRi6smJyy+0ZWixcciTM3maQHbH3w403vtFUeZkwUtTjz/Bm4vYFkNEe4EyVhh8jcgbAHI0s4HECG2Dfs0WYMCxOEvM5dTgsuylbLHVOsThLw0LkNo4d4rvyWHSdpb0JVBF9ONjVYLNZTt/hMzB573dRAE/zeRCjU8tjng/X1dXaDVahzygHWEfK5c/OfDDb64PJ1Hp+cqhwmNEoSrKeJZ3qlNQNa/BbStUH0jR866JfvFHy9RHSNG0rulgAb52wMmUHhmyQpTU2P+EMh2d0lhWyiHPQNzv8pybux5NELbY5KEFN6CgK8BznNCF2dAr0eLNixQ1kTlYWM6H6oNTdKM7PJG/0mJho9iU0fDY84+K7Xw1njzcVFKUvEYr1m3VQOzq70SI5E5Ynv5/IDJtfyFaPnoWdCVZvPVtrKfuzoymMh6PUW5zcDhrCyl5aIPpPQQRF0gmGwHIyVU11tqCWPOM7lAj6aGgl0DJV6M73poUaHGhBS9l7zEKJ6e6GDBoFUAFU5W6Xtt3ag63pVUrP5qirUwlJENpeiagwmBNbYXWWwQcFFwnB9ZRCtrQhEfT1Q9CPyioGwH+66JC1S+pvfKhTOPBHo8TZ1N2wfFUqRPZfZzZ3lB49x34E7e3pg/z0/mih3jB5bNjTU2TU5urRvSX/ryqJ7oJLND8/1Va58Qdkssvwc/PnuJf3rpjcfaIr3pD1rZeVs5GHROTS01e8+i0c+QHPN85O99rlcbrT51VT2lcSh/J8z/kWljnpcWt7SMEFKjKaQS1GM6HOUJDJYdrpAIx0BNG1nYkFkgxJtVLTZjr6oInhXV0uE9aR+5HNKWGTItMBJyo02E2DFoqwvqYraEojUm788J3T3D46P7zi+r9QchyarZcnYRhx1xno+HRtpT+T29XWvsHq4+QpfKAwHobjyJ8sKLQ1mjB2WVM6tcl7xKy71UtzxSexiEsL/A18K1wAAAAB42mNgZACDuQcOMcbz23xlkGR+AeI/U1ycD6P/T/y3mu0NazSQywbEQB0AciYNiQB42mNgZGBgjf63ioGBxYOB4f9TllAGdCAOAHPLBKoAAAJYAAAESAAyAhAAPQJxAB4CeQBXAdkAFQF/ADYDrQAbAoYAIAIQAAACQgAuAYQAPQOWAEUCbABKAogAJgHEADsBYABBBDoARQJeADQBdQAtAn0ANgRBAEUCcQAmeNotw7sNQFAAhtEv3q5HobOJFSQ20IhEcfs7hRiFMUhEYRu1P+IkBz4NeJUu4Hf6QDDqCeEGkf3vELeQGJ0hrdXpDeaAbNAL8gmKFcr+BfAYC8AAAAEAAAAXEAAEAAD/AP8AAgAAAAAA/wAAAgAA6QD/AB542pWQzYrCQBCEazT+5eBBFryI5CgEgnNIxHN0UBCUIHgTFEOMhAT8e559Ap9oH2YrbnvYgwcbZviqurphBkAbDyiUpdB93mVV0KD64yqpL2yRfeEa1UK4zj0rYRtf2HJKWS06Hs7CFWa+hav0H8IW+Ue4hqayhevoqY6wjYHyNrMojNbuMjs40zzJ0svRMek9juLklu3Ob7pvbO35AT2xSiecT0yRX10T6OFoHBT7k6+xwQwRQp41XCyR4QAHU+RIyCkuOFIb0h0xUzH9Gzs7vvqz2c/Smn/mI5Dc/9QrE2KOCVXB7pU7DfMaQ4wwJhXY48Qd+hdAQFL2AAB42mNgZsALAAB9AAS7AAAAAgACAAArK7sAAQAeADIABSu7AAAADwAZAAUrAA==")
    format("woff");
}

@font-face {
  font-family: "Trajan Pro";
  src: url("TrajanPro-Regular.otf") format("opentype");
}

@page {
  size: A4;
  margin: 10mm;
}

* {
  box-sizing: border-box;
}

html,
body {
  margin: 0;
  padding: 0;
  background: #d9d9d9;
  font-family: "Bookman Old Style", "Times New Roman", serif;
  font-size: 10pt;
}

.page {
  width: 190mm;
  margin: 20px auto;
  background: #fff;
  padding: 12mm;
  position: relative;
  box-sizing: border-box;
}

.header {
  text-align: center;
  position: relative;
}

.logo-left,
.logo-right {
  width: 75px;
  position: absolute;
  top: 0;
}

.logo-left {
  left: 0;
}

.logo-right {
  right: 0;
}

.header h1,
.header h2,
.header h3,
.header p {
  margin: 0;
  line-height: 1.3;
}

.header h2 {
  font-size: 12pt;
  font-weight: bold;
  font-family: "WHRCRT+Old English Five";
}

.header h3 {
  font-size: 10pt;
  font-weight: bold;
  margin-top: 2px;
  font-family: "Trajan Pro", serif;
  line-height:1em;
}

.header p {
  font-size: 11pt;
  font-family: "Trajan Pro", serif;
  line-height:1em;
}

.header .repulic {
  font-size: 11pt;
  font-family: "WHRCRT+Old English Five";
}

.divider {
  border-top: 2px solid #000;
  margin-top: 18px;
  margin-bottom: 30px;
}

.recipient {
  margin-top: 20px;
  line-height: 1.5;
}

.recipient .name {
  font-weight: bold;
  text-transform: uppercase;
}

.title {
  text-align: center;
  font-size: 14pt;
  font-weight: bold;
  margin: 40px 0 30px;
  text-transform: uppercase;
}

.content {
  line-height: 2.2;
  text-align: justify;
}

.underline {
  display: inline-block;
  border-bottom: 1px solid #000;
  line-height:1em;
  padding: 0 5px;
  font-weight: bold;
}

.signature {
  margin-top: 60px;
  width: 320px;
  margin-left: auto;
  text-align: center;
  line-height: 1.5;
}

.signature .name {
  font-weight: bold;
  text-transform: uppercase;
}

.conforme {
  margin-top: 50px;
}

.conforme .line {
  margin-top: 30px;
  width: 260px;
  border-bottom: 1px solid #000;
  text-align: center;
  padding-top: 5px;
  font-weight: bold;
}

.conforme .position {
  text-align: center;
  width: 260px;
}

.ifblank{
  width:250px;
  display:inline-block;
  font-size:9pt;
  text-align:center;
}

.fblank{
  width:200px;
  line-height:1em;
  display:inline-block;
  text-align:center;
  font-size:9pt;
}

@media print {

  html,
  body {
    background: #fff;
    width: 100%;
    height: auto;
  }


  .page {
    width: 100%;
    margin: 0;
    padding: 10mm;
    min-height: auto;
    height: auto;
    box-shadow: none;
    page-break-after: avoid;
    break-after: avoid;
  }

  .signature {
    margin-top: 50px;
  }

  .conforme {
    margin-top: 40px;
  }

  .title {
    margin: 35px 0 25px;
  }
  .content{
    line-height:2.2;
  }
}
    
  </style>
</head>

<body>
  <?php 
    $ivan = $this->Common->one_cond_row_select('hris_staff','FirstName,LastName,MiddleName,NameExtn,IDNumber','IDNumber',$data->sds);
    $school = $this->Common->one_cond_row_select('schools','schoolID,schoolName','schoolID',$data->school_id);
  ?>
  <div class="page">

    <!-- HEADER -->
    <div class="header">
      <img class="logo-left" src="<?= base_url(); ?>assets/images/report/ke.png" alt="">
      <img class="logo-right" src="<?= base_url(); ?>assets/images/report/davor.png" alt="">

      <div class="repulic">Republic of the Philippines</div>
      <h2>Department Of Education</h2>
      <p>Region XI</p>
      <h3>SCHOOLS DIVISION OF DAVAO ORIENTAL</h3>
      <p>Government Center, Barangay Dahican,</p>
      <p>City of Mati, Davao Oriental</p>
    </div>

    <div class="divider"></div>

    <!-- RECIPIENT -->
    <div class="recipient">
      <div class="name">
        
        <?= $data->prefix; ?> <?= trim(
    mb_strtoupper($data->FirstName ?? '', 'UTF-8') . ' ' .
    (!empty($data->MiddleName) ? mb_strtoupper(mb_substr($data->MiddleName, 0, 1, 'UTF-8'), 'UTF-8') . '. ' : '') .
    mb_strtoupper($data->LastName ?? '', 'UTF-8') . ' ' .
    (!empty($data->NameExtn) ? mb_strtoupper($data->NameExtn, 'UTF-8') : '')
); ?>

</div>
      <div>Appointee</div>
      <div>Schools Division of Davao Oriental</div>
    </div>

    <!-- TITLE -->
    <div class="title">Assignment Order</div>

    <!-- CONTENT -->
    <div class="content">
      <p> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
        In view of the approval of your appointment as
        <span class="underline"><?= strtoupper($data->jobTitle); ?> &mdash; <?= strtoupper($data->description); ?></span>
        in the Schools Division of Davao Oriental, you are hereby advised
        of your assignment at
        <?php $dis = $this->Common->one_cond_row('district','id',$data->d_id); ?>
        <span class="underline"><span class="fblank"><?= !empty($school->schoolName) ? html_escape(mb_strtoupper($school->schoolName, 'UTF-8')) : ''; ?>
</span> &mdash; <span class="ifblank"> <?= !empty($dis->discription) ? strtoupper($dis->discription) . ' DISTRICT' : ''; ?></span></span>
        to perform the duties and responsibilities attached to your position
        and such other related function as may be assigned.
      </p>

      <p> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
        It is understood that you may be transferred/reassigned anytime
        to other school within the Schools Division where you are presently
        deployed or where your services are needed.
      </p>
    </div>

    <!-- SIGNATURE -->
    <div class="signature">
      <div class="name">DR. <?= isset($ivan) && !empty($ivan) ? strtoupper($ivan->FirstName) . ' ' . (!empty($ivan->MiddleName) ? strtoupper(substr($ivan->MiddleName, 0, 1)) . '.' : '') . ' ' . strtoupper($ivan->LastName) . ' ' . strtoupper($ivan->NameExtn) : ''; ?></div>
      <div>Schools Division Superintendent</div>
    </div>

    <!-- CONFORME -->
    <div class="conforme">
      <div>CONFORME:</div>

      <div class="line"><?= strtoupper($data->FirstName); ?> <?= !empty($data->MiddleName) ? strtoupper(substr($data->MiddleName, 0, 1)) . '.' : ''; ?> <?= strtoupper($data->LastName); ?> <?= strtoupper($data->NameExtn); ?></div>
      <div class="position"><?= strtoupper($data->jobTitle); ?></div>
    </div>

  </div>
</body>
</html>