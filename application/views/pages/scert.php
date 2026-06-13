  
       <!DOCTYPE html>
       <html lang="en">
       <meta charset="utf-8" />
       <head>
        <title>QAME | QUALITY ASSURANCE MONITORING AND EVALUATION</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="Responsive bootstrap 4 admin template" name="description" />
        <meta content="Coderthemes" name="author" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <!-- App favicon -->
        <link rel="shortcut icon" href="<?= base_url(); ?>assets/images/davor.ico">
        <link href="https://db.onlinewebfonts.com/a/0nH393RJctHgt1f2YvZvyruY" rel="stylesheet" type="text/css"/>
        <link href="https://db.onlinewebfonts.com/c/84ae358e627d67d90bd613fcedc20c10?family=Edwardian+Script+ITC" rel="stylesheet"> 
    <style>
        .wrap{
            width:80%;
            margin:auto;
            margin-bottom:50px;
        }
        .ivy{
            margin-bottom:30px;
        }
        .ivy h3, .ivy h1{
            text-align:center;
        }
        table,td,th{
            font-family: "Bookman Old Style", Georgia, serif;
            font-size:12px;
            border-collapse: collapse;
            border:1px solid #222;
            text-align:center;
        }
        td,th{
            padding:17px 15px;
        }
        th{
            font-size:16px;
        }
        table{
            width:100%;
        }
        .qame{
            color:#6600ff;
            font-size:30px;
        }
        .cert {
        margin:auto;
        text-align:center;
        padding:20px 0;
        font-family: 'Times New Roman', Times, serif;
        }

        .cert p{
        font-weight:normal;
        font-size:16px;
        margin-bottom:20px;
        line-height: 1.7em;
        }
        .cert p span.ca{
        font-weight:bold;
        font-size:20px
        }

        .ft{
            margin-top:60px;
            margin-bottom:60px;
        }
        .sign td, .sign th, table{
            border:0 !important;
        }
        .c{
            float:left;
            margin-right:150px;
        }
        .sign span{
            text-align:center;
            display:block;
            font-size:16px;
        }
        .sign span.cname{
            margin-top:60px;
            font-weight:bold;
        }
        .sign span.deg{
            font-size:18px;
            font-style:italic;
        }


        .de{
        font-family: "Old English Text MT"; 
        font-size: 18pt;
        }
        .rp{
        font-family: "Old English Text MT"; 
        font-size: 12pt;
        }
        .r{
        font-family: "Trajan Pro";
        font-size: 10pt;
        }
        .logo {
        width: 70px;
        height: 70px;
        }
        .namecert{
            color:#002060; 
            font-size:30px; 
            margin:50px 0
        }
        .q{
            font-size:25px;
        }
        .signwrap{
            border:1px solid red;
        }
        table td.certtable{
            width:40%;
        }

       
      
        @media print {
        body {
            margin: 0; // 
        }
        }

        @page {
            size: A4;
            margin: .5;
            }
            @media print {
            html, body {
                width: 210mm;
                height: 297mm;
                font-size:14px !important;
            }
            .wrap{
                width:90%;
                padding-top:1px;
            }

            .namecert{
            font-size:20px; 
            }
            .q{
                font-size:15px;
                line-height:1.5em;
            }
            .c{
                float:left;
            }
            .cert p{
                line-height:1.2em;
            }
            .namecert{
                margin:20px 0;
            }
            .certify {
                text-align:justify;
            }
        }
        *{
            padding:0; margin:0
        }

        .blocker{clear:both !important}
    </style>
</head>
<body>

<div class="cert">
            <img class="logo" src="<?= base_url(); ?>assets/images/ke.png" alt="">
            <p>
            <span class="rp">Republic of the Philippines</span><br />
                <span class="de">Department of Education</span><br />
                <span class="r">Region XI</span><br />
                <span class="r">Schools Division of Davao Oriental</span><br/>
                <span class="r">Government Center, Dahican, City of Mati, Davao Oriental</span>
            </p>
</div>


<div class="wrap">

<div class="ivy">
    <h3 class="q"><span class="qame">Q</span>uality <span class="qame">A</span>ssurance, <span class="qame">M</span>onitoring and <span class="qame">E</span>valuation </h3>
    <h1 class="namecert">CERTIFICATION OF LEARNING FACILITATOR’S QAME RATINGS</h1>
    <p style="line-height:2em; font-size:12px" class="certify">This is to certify that <span style="font-weight:bold; text-decoration:underline; font-size:20px; color:#002060"><?= trim(strtoupper($this->input->get('name'))); ?></span> served as a Learning Facilitator (LF) during the <?= strtoupper($act->act_name); ?> held at <?= $act->act_address; ?> on <?= $act->date_start; ?> with the following QAME ratings:</p>
</div>



<table>
    <tr style="background:#e2efd9;">
        <th>Session</th>
        <th>Competency</th>
        <th>QAME Rating</th>
        <th>Description </th>
    </tr>
    
    <?php 
        $ta=0;
        foreach($data as $row){
    ?>
    <tr>
        <td rowspan="6" style="font-size:12px; font-weight:bold;"><?= $row->stopic; ?> </td>
    </tr>
    <tr>
        <td style="text-align:left">A. PD Program Objectives</td>
        <td>
            <?php 
                if($row->qs_id == $this->input->get('name')){
                    $name = trim($this->input->get('name'));
                }else{
                    $name = $this->input->get('name');
                } 
                
                $tf = $this->Page_model->four_cond('qame_mc_sf','act_id',$this->uri->segment(3),'speaker',$name,'day',$row->day,'session',$row->session); 
                $c = $this->Page_model->four_cond_count_rows('qame_mc_sf','act_id',$this->uri->segment(3),'speaker',$name,'day',$row->day,'session',$row->session); 
            ?>
            
                    <?php 
                        $fpda=0;
                        foreach($tf as $frow){
                            $frow->pda1;
                            $frow->pda2;
                            $frow->pda3;

                            $t = ($frow->pda1+$frow->pda2+$frow->pda3)/3;

                            $fpda += $t;} 
                    ?>
                            
                    <?php $pda = $fpda/$c->num_rows(); echo number_format($pda, 2); ?>
                

            
        </td>
        <td>
            <?php 
                $fpda = number_format($pda, 1);
                if($fpda >= 3.6){
                    echo "Excellent";
                }elseif($fpda >= 3.1){
                    echo "Very Good";
                }elseif($fpda >= 2.6){
                    echo "Good";
                }elseif($fpda >= 2.1){
                    echo "Fair";
                }else{echo "Poor";}
            ?>
        </td>
    </tr>
    <tr>
        <td style="text-align:left">B. PD Learning Resource Materials</td>
        <td>
        <?php 
                        $fpdb=0;
                        foreach($tf as $frow){
                            $frow->pdb1;
                            $frow->pdb2;
                            $frow->pdb3;
                            $frow->pdb4;

                            $t = ($frow->pdb1+$frow->pdb2+$frow->pdb3+$frow->pdb4)/4;

                            $fpdb += $t;} 
                    ?>
                            
                    <?php $pdb = $fpdb/$c->num_rows(); echo number_format($pdb, 2); ?>
        </td>
        <td>
        <?php 
                $fpdb = number_format($pdb, 1);
                if($fpda >= 3.6){
                    echo "Excellent";
                }elseif($fpdb >= 3.1){
                    echo "Very Good";
                }elseif($fpdb >= 2.6){
                    echo "Good";
                }elseif($fpdb >= 2.1){
                    echo "Fair";
                }else{echo "Poor";}
            ?>
        </td>
    </tr>
    <tr>
        <td style="text-align:left">C. PD Content Relevance</td>
        <td>
        <?php 
                        $fpdc=0;
                        foreach($tf as $frow){
                            $frow->pdc1;
                            $frow->pdc2;
                            $frow->pdc3;
                            $frow->pdc4;

                            $t = ($frow->pdc1+$frow->pdc2+$frow->pdc3+$frow->pdc4)/4;

                            $fpdc += $t;} 
                    ?>
                            
                    <?php $pdc = $fpdc/$c->num_rows(); echo number_format($pdc, 2);  ?>
        </td>
        </td>
        <td>
        <?php 
                $fpdc = number_format($pdc, 1);
                if($fpda >= 3.6){
                    echo "Excellent";
                }elseif($fpdc >= 3.1){
                    echo "Very Good";
                }elseif($fpdc >= 2.6){
                    echo "Good";
                }elseif($fpdc >= 2.1){
                    echo "Fair";
                }else{echo "Poor";}
            ?>
        </td>
    </tr>
    <tr>
        <td style="text-align:left">D. Resource Person/Subject Matter Expert Knowledge</td>
        <td>
        <?php 
                        $fpdd=0;
                        foreach($tf as $frow){
                            $frow->pdd1;
                            $frow->pdd2;
                            $frow->pdd3;
                            $frow->pdd4;
                            $frow->pdd5;
                            $frow->pdd6;
                            $frow->pdd7;
                            $frow->pdd8;
                            $frow->pdd9;

                            $t = ($frow->pdd1+$frow->pdd2+$frow->pdd3+$frow->pdd4+$frow->pdd5+$frow->pdd6+$frow->pdd7+$frow->pdd8+$frow->pdd9)/9;

                            $fpdd += $t;} 
                    ?>
                            
                    <?php  $pdd = $fpdd/$c->num_rows(); echo number_format($pdd, 2); ?>
        </td>
        <td>
            <?php 
                $fpdd = number_format($pdd, 1);
                if($fpda >= 3.6){
                    echo "Excellent";
                }elseif($fpdd >= 3.1){
                    echo "Very Good";
                }elseif($fpdd >= 2.6){
                    echo "Good";
                }elseif($fpdd >= 2.1){
                    echo "Fair";
                }else{echo "Poor";}
            ?>
        </td>
    </tr>
    <tr>
        <td style="text-align:right; font-weight:bold">AVERAGE</td>
        <td><b><?php $sub_average = ($pda+$pdb+$pdc+$pdd)/4;  echo number_format($sub_average, 2); ?></b></td>
        <td>
        <?php 
                $ss = number_format($sub_average, 1);
                if($ss >= 3.6){
                    echo "Excellent";
                }elseif($ss >= 3.1){
                    echo "Very Good";
                }elseif($ss >= 2.6){
                    echo "Good";
                }elseif($ss >= 2.1){
                    echo "Fair";
                }else{echo "Poor";}
            ?>
        </td>
    </tr>
    <?php $ta += $sub_average; } ?>
    <tr>
        <td colspan="2" style="text-align:right; font-weight:bold">OVERALL RATING</td>
        <td><b><?php 
                if($cd->num_rows() != 0){
                    $tt = $ta/$cd->num_rows(); echo number_format($tt, 2);
                }else{ $tt = 0;}
                
        ?></b></td>
        <td>
        <?php 
                $tt = number_format($tt, 1);
                if($tt >= 3.6){
                    echo "Excellent";
                }elseif($tt >= 3.1){
                    echo "Very Good";
                }elseif($tt >= 2.6){
                    echo "Good";
                }elseif($tt >= 2.1){
                    echo "Fair";
                }else{echo "Poor";}
            ?>
        </td>
    </tr>
    
</table>


<p class="ft">Given at <?= $act->act_address; ?> on the 24th day of May <?= date('Y'); ?>.</p>

<table class="sign">
    <tr>
        <td class="certtable">Certified:</td>
        <td>Noted:</td>
    </tr>
    <tr>
    <td>
            <span class="cname">ALAN D. LIMBADAN</span>
            <span class="deg">Senior Education Program Specialist</span>
    </td>
    <td>
            <span class="cname">ERNESTO H. CABANES</span>
            <span class="deg">SGOD Chief</span>
    </td>
    </tr>
    <tr>
        <td></td>
        <td></td>
    </tr>
</table>





</div>

    
</body>
</html>