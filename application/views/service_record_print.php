<?php if ($this->session->logged_in == false) {
    redirect(base_url() . 'log_in');
} ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Responsive bootstrap 4 admin template" name="description" />
    <meta content="Coderthemes" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="<?= base_url(); ?>assets/images/hris.ico">
    <link href="<?= base_url(); ?>assets/css/hris.css" rel="stylesheet" type="text/css" />

    <title>Service Record</title>
</head>

<body>

    <div class="container">


        <table class="headertable">



            <thead>

                <th colspan="6">
                    <div class="imageheader">
                        <img src="<?= base_url(); ?>assets/images/hris/header.png" alt="header">
                    </div>

                </th>
            </thead>

            <tr>
            <td colspan="6" style="padding: 5px; margin: 0; padding-bottom: 20px !important;">
    <?php if (!$data) { 
        echo "<h3>NO RECORDS FOUND!</h3>";
    } else { ?>
        <h3 style="margin: 0; padding: 0;">SERVICE RECORD</h3>
        <strong style="margin-top: 0; padding: 0;">(Purpose: <?php echo $data[0]->purpose ?> )</strong>
</td>


            <tr>
                <td>NAME:</td>
                <td> <strong class="data"><?php echo $data[0]->LastName ?></strong></td>
                <td> <strong class="data"><?php echo $data[0]->FirstName ?></strong></td>
                <td> <strong><?php echo $data[0]->MiddleName ?></strong></td>
                <td colspan="3">
                    <p style="text-align: left;"> ( If married woman, give maiden name )</p>
                </td>
            </tr>

            <tr>
                <td></td>
                <td style="border-top: 1px solid #000;"> <i>(Surename)</i></td>
                <td style="border-top: 1px solid #000;"> <i>(Given Name)</i></td>
                <td style="border-top: 1px solid #000;"> <i>(M-Name)</i></strong></td>
                <td colspan="3"></td>
            </tr>



            <tr>
                <td>
                    <p>BIRTH: &nbsp;</p>
                </td>

                <td>
                    <div class="birthdate">
                        <strong>
                            <?php
                            $birthDate = date_create($data[0]->BirthDate);
                            echo date_format($birthDate, 'F j, Y');
                            ?>
                        </strong>
                    </div>
                </td>

                <td colspan="2">
                    <div class="birthplace">
                        <strong><?php echo $data[0]->BirthPlace; ?></strong>
                    </div>
                </td>

                <td colspan="3">
                    <p style="text-align: left;">( Data herein slould be checked from birth or</p>
                </td>

            </tr>

            <tr>

                <td></td>
                <td style="border-top: 1px solid #000;"><i>( Date )</i></td>
                <td colspan="2" style="border-top: 1px solid #000;"><i>( Place )</i></td>
                <td colspan="3">
                    <p style="text-align: left;"> baptismal certificate or some other reliable documents )</p>
                </td>

            </tr>
            <tr>
                <td colspan="9" style="text-align: left;"></td>
            </tr>
            <tr>
                <td colspan="9" style="text-align: left;">
                    <p>This is to certify that the above named employee actually rendered services in this Office and shown by the "SERVICE RECORD" below each line of which is supported by appointments and other papers actually issued and approved by the authorities concerned.</p>
                </td>
            </tr>
            <!-- <tr>
                        <td colspan="9" style="margin-bottom: 35px;" ><p> papers actually issued and approved by the authorities concerned.</p></td>
                    </tr> -->



        <?php } ?>

        </table>

        <table class="content">










            <!--                                     
                                    

                                    
                                        <div class="note">
                                                <p>This is to certify that the employee herein above-named actually rendered services in this office as shown by the service record below each line of which is supported by appointments and other papers actually issued by this office and approved by the authorities concerned.</p>
                                            </div>
                                        </div> -->





            <thead>


                <tr>
                    <th colspan="2">SERVICE
                        <p>(Inclusive Dates)</p>
                    </th>
                    <th colspan="3">RECORD OF APPOINTMENT</th>
                    <th colspan="1">OFFICE ENTITY</th>
                    <th>LV/ABS w/out Pay</th>
                    <th>Separation Cause/d</th>
                </tr>

                <tr>
                    <td>FROM</td>
                    <td>TO</td>
                    <td>Designation</td>
                    <td>Status</td>
                    <td>Annual Salary</td>
                    <td>(Station/Place of Assignment)</td>
                    <td></td>
                    <td></td>
                </tr>

            </thead>

            <tbody>

                <?php
                foreach ($data as $row) {
                    echo "<tr>";
                    echo "<td>" . $row->appointDate . "</td>";
                    echo "<td>" . $row->endDate . "</td>";
                    echo "<td>" . $row->empPosition . "</td>";
                    echo "<td>" . $row->empStatus . "</td>";
                    echo "<td>" . $row->salary . "</td>";
                    echo "<td>" . $row->empStation . "</td>";
                    echo "<td>" . $row->lvwithoutpay . "</td>";
                    echo "<td>" . $row->separation . "</td>";
                    echo "</tr>";
                } ?>

            </tbody>



            <tr>
                <td></td>
                <td colspan="6">*****NOTHING FOLLOWS*****</td>
                <td></td>
            </tr>

            <tr>
                <td colspan="10" style="border: none;">

                    <div class="note">
                        <p>Issued in compliance with Executive Order. 54 dated August 10,. 1954 and in accordance with Circular No. 58 dated August 10, 1954 of the system.</p>
                    </div>

                </td>
            </tr>


          

            <tr style="border: none;">
                <td colspan="3" style="border: none;">

                    <div class="bottom1">
                        <p>Purpose: ________________________________</p>
                    </div>
                </td>

                <td colspan="3" style="border: none;">
                    <div class="bottom2" style="border: none;">
                        <div class="bottom2-box">

                            <p><strong> VERIFIED</strong></p><br>
                            <p style="text-align: left;">BY: <?php echo !empty($data[0]->action_user) ? $data[0]->action_user : ''; ?></p><br>
                            <p style="text-align: left;">DATE: <?php echo !empty($data[0]->action_date) ? $data[0]->action_date : ''; ?>
                            </p>
                        </div>
                    </div>
                </td>


                <td colspan="2" style="border: none;">
                    <div class="bottom3">
                    <?php if (isset($qrCodeUrl)): ?>
        <img style="width:90px; float:left;" 
             src="https://qrcode.tec-it.com/API/QRCode?data=<?= urlencode($qrCodeUrl); ?>" 
             title="QR Code" />
    <?php else: ?>
        <p>QR Code is not available.</p>
    <?php endif; ?>
                        </div>
                    </div>

                </td>

            </tr>



            <tr>
                <td colspan="3" style="border: none;">

                    <div class="date">
                        <?php echo date('F'); ?> <?php echo date('d'); ?> <?php echo date('Y'); ?>
                        <p>___________________</p>
                        <p>Date</p>
                    </div>
                </td>
      
            </tr>

            <!-- <tfoot>

                    <tr>
                        <td colspan="10" style="border: none;">
                         <div class="footer">
                              <img src="<?= base_url(); ?>assets/images/hris/footer.png" alt="footer">
                            </div>    
                        </td>
                    </tr>

                    </tfoot> -->




        </table>


        



    </div>






</body>

</html>