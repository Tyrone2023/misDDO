<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="<?= base_url(); ?>assets/images/hris.ico">
    <title>SDO Davao Oriental - Authority To Travel </title>
    <style>
        .wrap{
            width:80%;
            margin:auto;
            font-family: 'Bookman Old Style', serif;
        }
        .hwrap{
            text-align:center;
        }
        .logo {
            width: 25%;
            object-fit: contain;
            display: block;
            margin: 0 auto 2px;
        }
        .rp{
            font-family: "Old English Text MT"; 
            font-size: 12pt;
        }
            .r{
            font-family: "Trajan Pro";
            font-size: 9pt;
        }
            .de{
            font-family: "Old English Text MT"; 
            font-size: 16pt;
        }
        .title{
            text-align:center;
            font-size:40px;
        }
        .travelt,
        .travelt td,
        .travelt th{
            border:1px solid #222;
            border-collapse: collapse;
            text-align:left;
            padding:5px 15px;
        }
        .td{
            font-style:italic;
        }

        .tsignl{
            float:left;
            width:70%;
            text-align:center;
        }
        .tsignr{
            float:right;
            width:30%;
            text-align:center;
        }
        .date{
            border-top:1px solid #222;
            font-style:italic;
            margin:5px;
        }

        .notes{
            font-style:italic;
            margin-bottom:120px;
            margin-top:0;
        }

        .footer{
            border-top:1px solid #222;
            margin-top:70px;
        }

        .footer .fr{
            padding-top:10px;
        }

        .footer .fr img{
            float:left;
            margin-right:30px;
            margin-left:15%;
        }

        .footer .fr p{
            padding:0 !important;
            margin:0 !important;
        }

        .esigwrap{
            position:relative;
            height:1;
        }

        .myesig{
            position:absolute;
            bottom:-60px;
            left:50%;
            transform: translate(-50%, -50%);
            height:70px;
            width:150px;
        }

        .qrwrap{
            position:relative;
            width:10px;
            
        }
        .qr{
            width:0px;
            position:absolute;
        }


        .blocker{clear:both !important}

         @media print {
            @page {
            margin: 4mm 12mm 12mm 12mm;
            }
            .wrap{
                width:100%;
                margin:0 !important;
                font-size:13px;
            }
            .logo{
                width:40%;
            }
            .title{
                font-size:25px;
            }
            .footer .fr img{
              margin-left:3%;  
            }
            .tl{
                width:30%;
            }
            .notes{
                margin-bottom:50px;
            }
            .renguapo{
                font-size:10px;
                font-weight:normal;
            }

            .qr{
                width:50px;
                top:-15px;
            }

            .footer{
            margin-top:40px;
        }

         }
        


    </style>
</head>
<body>
    <?php $staff = $this->Common->one_cond_row('hris_staff','IDNumber',$travel->IDNumber); ?>
    <?php 
        function smartTitleCase($string) {
            $words = explode(' ', strtolower($string));
            $result = [];

            foreach ($words as $word) {
                if (preg_match('/^(?=[MDCLXVI\d]+$)(M{0,4}(CM|CD|D?C{0,3})(XC|XL|L?X{0,3})(IX|IV|V?I{0,3})|\d+)$/i', $word)) {
                    $result[] = strtoupper($word); 
                } else {
                    $result[] = ucfirst($word);
                }
            }

            return implode(' ', $result);
        }

        $track1 = $this->Common->two_cond_row('travel_tracker','travel_req_id',$this->uri->segment(3),'stat','Pending');
        $track2 = $this->Common->two_cond_row('travel_tracker','travel_req_id',$this->uri->segment(3),'stat','Recommended');
        $track3 = $this->Common->two_cond_row('travel_tracker','travel_req_id',$this->uri->segment(3),'stat','Approved');



        // $esign1 = $this->Common->three_cond_row('travel_sign_settings','position',1,'user_id',$travel->IDNumber,'ttype',$travel->ttype);
        // $esign2 = $this->Common->three_cond_row('travel_sign_settings','position',2,'user_id',$travel->IDNumber,'ttype',$travel->ttype);

        if(!empty($travel->raID)){
        $esigns1 = $this->Common->one_cond_row('hris_staff','IDNumber',$travel->raID);
        }
        $esigns2 = $this->Common->one_cond_row('hris_staff','IDNumber',$travel->aID);
     ?>

     

<div class="wrap">
    <?php //if($travel->status != 'Approved'){?>
    <!-- <div style="
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) rotate(-30deg);
        font-size: 80px;
        color: rgba(0, 0, 0, 0.10);
        z-index: 9999;
        pointer-events: none;
        user-select: none;
        white-space: nowrap;">
        <?php // strtoupper($travel->status); ?>
    </div> -->
    <?php // } ?>

    <div class="hwrap">
        <img src="<?= base_url(); ?>assets/images/header.png" alt="DepEd Logo" class="logo">
    </div>
    <?php 
     $tc = str_word_count($travel->purpose);
    ?>


    <hr />

    <h1 class="title">AUTHORITY TO TRAVEL</h1>

    <table class="travelt">
        <tr>
            <th class="tl">Name</th>
            <th><?= strtoupper($staff->FirstName) ?> <?= !empty($staff->MiddleName) ? strtoupper(substr($staff->MiddleName, 0, 1)) . '.' : '' ?> <?= strtoupper($staff->LastName) ?> <?= strtoupper($staff->NameExtn) ?></th>
        </tr>
        <tr>
            <th>Position/Designation</th>
            <th><?= $staff->empPosition; ?></th>
        </tr>
        <tr>
            <th>Permanent Station</th>
            <th><?= $travel->permanent_station; ?></th>
        </tr>
        <tr>
            <th>Purpose of Travel</th>
            <th <?php if ($tc > 25) {echo "class='renguapo'";}?> ><?= $travel->purpose; ?></th>
        </tr>
        <tr>
            <th>Host of Activity</th>
            <th><?= $travel->activity_host; ?></th>
        </tr>
        <tr>
            <th>Inclusive Dates</th>
            <th><?= $travel->inclusive_date; ?></th>
        </tr>
        <tr>
            <th>Destination</th>
            <th><?= $travel->destination; ?></th>
        </tr>
        <tr>
            <th>Fund Source</th>
            <th><?= strtoupper($travel->fund_source); ?></th>
        </tr>
        <tr>
            <td colspan="2">
                <p class="notes">I hereby attest that the information in this form and in the supporting documents attached hereto are true and correct.</p>
                <div class="tsign">
                    <div class="tsignl">

                        <div class="esigwrap">
                            <img class="myesig" src="<?= base_url(); ?>uploads/esig/<?= $staff->esig; ?>" alt="">
                        </div>
                        
                        <b><u><?= strtoupper($staff->FirstName) ?> <?= !empty($staff->MiddleName) ? strtoupper(substr($staff->MiddleName, 0, 1)) . '.' : '' ?> <?= strtoupper($staff->LastName) ?> <?= strtoupper($staff->NameExtn) ?></u></b><br />
                        <?= $staff->empPosition; ?>
                    </div>
                    <div class="tsignr"><?php echo (!empty($track1) && !empty($track1->dt) && ($date = @new DateTime($track1->dt))) ? $date->format('Y-m-d') : ''; ?><p class="date">Date</p></div>

                    <div class="blocker"></div>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <p class="notes">This is to certify that the requesting employee satisfies all the minimum conditions for authorized official travel and that alternatives to travel are insufficient for purpose stated herein.</p>
                <?php if($staff->emp_type == 1){?>
                <?php if(!empty($travel->raID)){ ?>
                <div class="tsign">
                    <div class="tsignl">
                        <?php if(!empty($track2)){?>
                        <div class="esigwrap">
                            <img class="myesig" src="<?= base_url(); ?>uploads/esig/<?= $esigns1->esig; ?>" alt="">
                        </div>
                        <?php } ?>

                        <b><u><?= strtoupper($esigns1->FirstName) ?> <?= !empty($esigns1->MiddleName) ? strtoupper(substr($esigns1->MiddleName, 0, 1)) . '.' : '' ?> <?= strtoupper($esigns1->LastName) ?></u></b><br />
                        <?php echo $esigns1->IDNumber == '8301540' ? $esigns1->jobTitle : $esigns1->empPosition; ?>

                    </div>
                    <div class="tsignr"><?php echo (!empty($track2) && !empty($track2->dt) && ($date = @new DateTime($track2->dt))) ? $date->format('Y-m-d') : ''; ?><p class="date">Date</p></div>

                    <div class="blocker"></div>
                </div>
                <?php } ?>
                <?php }else{ ?>
                    <div class="tsign">
                    <div class="tsignl">
                        <?php if(!empty($track2)){?>
                        <div class="esigwrap">
                            <img class="myesig" src="<?= base_url(); ?>uploads/esig/<?= $esigns1->esig; ?>" alt="">
                        </div>
                        <?php } ?>

                        <b><u><?= strtoupper($esigns1->FirstName) ?> <?= !empty($esigns1->MiddleName) ? strtoupper(substr($esigns1->MiddleName, 0, 1)) . '.' : '' ?> <?= strtoupper($esigns1->LastName) ?></u></b><br />
                        <?php echo $esigns1->IDNumber == '8301540' ? $esigns1->jobTitle : $esigns1->empPosition; ?>

                    </div>
                    <div class="tsignr"><?php echo (!empty($track2) && !empty($track2->dt) && ($date = @new DateTime($track2->dt))) ? $date->format('Y-m-d') : ''; ?><p class="date">Date</p></div>

                    <div class="blocker"></div>
                </div>
                <?php }?>

            </td>
        </tr>
        <tr>
            <td colspan="2">
                <h3 class="approved">APPROVED</h3>
                <div class="tsign">
                    <div class="tsignl">
                        <?php if(!empty($track3)){?>
                        <div class="qrwrap">
                            <img class="qr" src="https://qrcode.tec-it.com/API/QRCode?data=<?= base_url(); ?>travel/travel_print_view/<?= $this->uri->segment(3); ?>" title="" />
                        </div>
                        
                        <div class="esigwrap">
                            <img class="myesig" src="<?= base_url(); ?>uploads/esig/<?= $esigns2->esig; ?>" alt="">
                        </div>
                        <?php } ?>
                        <b><u><?= strtoupper($esigns2->FirstName) ?> <?= !empty($esigns2->MiddleName) ? strtoupper(substr($esigns2->MiddleName, 0, 1)) . '.' : '' ?> <?= strtoupper($esigns2->LastName) ?></u></b><br />
                        <?= $esigns2->empPosition; ?>
                    </div>
                    <div class="tsignr"><?php echo (!empty($track3) && !empty($track3->dt) && ($date = @new DateTime($track3->dt))) ? $date->format('Y-m-d') : ''; ?><p class="date">Date</p></div>

                    <div class="blocker"></div>
                </div>

            </td>
        </tr>
    </table>

    <div class="footer">
        <div class="fr">
            <img src="<?= base_url(); ?>assets/images/logo3.jpg" alt="">
            <p><b>Address:</b> Government Center, Dahican, Mati City</p>
            <p><b>Contact No.:</b> (087) 388-3372</p>
            <p><b>Email Address:</b> davao.oriental@deped.gov.ph</p>
            <p><b>Official Website:</b> <a href="https://depeddavor.com/">https://depeddavor.com/</a></p>
            <p class="italic-quote">“Where the Sunrise Beckons the Sweetest Smile”</p>
        </div>

        <div class="blocker"></div>
    </div>

  </div>
    
</body>
</html>