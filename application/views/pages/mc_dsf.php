<meta charset="utf-8" />
        <title>QAME | QUALITY ASSURANCE MONITORING AND EVALUATION</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="Responsive bootstrap 4 admin template" name="description" />
        <meta content="Coderthemes" name="author" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <!-- App favicon -->
        <link rel="shortcut icon" href="https://localhost/qame/assets/images/davor.ico">
        <style>
            
            table,td,th{
                font-family: Arial, Helvetica, sans-serif;
                font-size:12px;
                border-collapse: collapse;
                border:1px solid #222;
                margin:10px 2%;
            }
        </style>
</head>
<body>



<table>
    <tr>
        <th>speaker</th>
        <th>session</th>
        <th>Topic</th>
        <th>LR</th>
        <th>pda1</th> 
        <th>pda2</th>
        <th>pda3</th>
        <th></th>

        <th>pdb1</th>
        <th>pdb2</th> 
        <th>pdb3</th> 
        <th>pdb4</th> 
        <th></th>

        <th>pdc1</th> 
        <th>pdc2</th> 
        <th>pdc3</th> 
        <th>pdc4</th> 
        <th></th>

        <th>pdd1</th> 
        <th>pdd2</th> 
        <th>pdd3</th>
        <th>pdd4</th>
        <th>pdd5</th>
        <th>pdd6</th>
        <th>pdd7</th>
        <th>pdd8</th>
        <th>pdd9</th>
        <th></th>
    </tr>
    <?php 
        $t1=0;
        $t2=0;
        $t3=0;
        $t4=0;
        foreach($data as $row){
            $str = trim($row->speaker);
        $topic = $this->Page_model->four_cond_row('qame_mc_speaker_day','act_id',$this->uri->segment(3),'day',$this->uri->segment(4),'session',$row->session,'qs_id',$str);
        ?>
    <tr>
        <td><?= $row->speaker; ?></td>
        <td><?= $row->session; ?></td>
        <td><?= $topic->stopic; ?></td>
        <td><?= $topic->sl; ?></td>

        <td><?= $row->pda1; ?></td> 
        <td><?= $row->pda2; ?></td>
        <td><?= $row->pda3; ?></td>
        <td style="background:#34a853; color:#fff"><?php $tpda = ($row->pda1+$row->pda2+$row->pda3)/3; echo $tpda; ?></td>

        <td><?= $row->pdb1; ?></td>
        <td><?= $row->pdb2; ?></td> 
        <td><?= $row->pdb3; ?></td> 
        <td><?= $row->pdb4; ?></td>
        <td style="background:#34a853; color:#fff"><?php $tpdb = ($row->pdb1+$row->pdb2+$row->pdb3+$row->pdb4)/4; echo $tpdb; ?></td>

        <td><?= $row->pdc1; ?></td> 
        <td><?= $row->pdc2; ?></td> 
        <td><?= $row->pdc3; ?></td> 
        <td><?= $row->pdc4; ?></td> 
        <td style="background:#34a853; color:#fff"><?php $tpdc = ($row->pdc1+$row->pdc2+$row->pdc3+$row->pdc4)/4; echo $tpdc; ?></td>

        <td><?= $row->pdd1; ?></td> 
        <td><?= $row->pdd2; ?></td> 
        <td><?= $row->pdd3; ?></td>
        <td><?= $row->pdd4; ?></td>
        <td><?= $row->pdd5; ?></td>
        <td><?= $row->pdd6; ?></td>
        <td><?= $row->pdd7; ?></td>
        <td><?= $row->pdd8; ?></td>
        <td><?= $row->pdd9; ?></td>
        <td style="background:#34a853; color:#fff"><?php $tpdd = ($row->pdd1+$row->pdd2+$row->pdd3+$row->pdd4+$row->pdd5+$row->pdd6+$row->pdd7+$row->pdd8+$row->pdd9)/9; echo $tpdd; ?></td>
    </tr>
    <?php
        $t1 += $tpda;
        $t2 += $tpdb;
        $t3 += $tpdc;
        $t4 += $tpdd;
    } ?>

    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        
        <td><?php $ft1 = $t1/$cd->num_rows(); echo $ft1; ?></td>
        
        <td></td>
        <td></td>
        <td></td>
        <td></td>

        <td><?php $ft2 = $t2/$cd->num_rows(); echo $ft2; ?></td>
        
        <td></td>
        <td></td>
        <td></td>
        <td></td>

        <td><?php $ft3 = $t3/$cd->num_rows(); echo $ft3; ?></td>

        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td><?php $ft4 = $t4/$cd->num_rows(); echo $ft4; ?></td>
    </tr>
    <tr>
        <td style="background:#36b056; color:#fff; text-align:right" colspan="28"><b><?php $final = ($ft1+$ft2+$ft3)/3; echo number_format($final, 2); ?></b></td>
    </tr>
</table>















    
</body>
</html>