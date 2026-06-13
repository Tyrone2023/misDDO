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
    <link href="<?= base_url(); ?>assets/css/list_personnelv1.css" rel="stylesheet" type="text/css" />
    <link href="<?= base_url(); ?>assets/css/list_personnelv1_print.css" rel="stylesheet" type="text/css" />

    <title>LIST OF PERSONNEL</title>
</head>

<body>

                <div class="container">


                <table class="headertable">



                

                <tr colspan="10">
                            <div class="imageheader">
                                <img src="<?= base_url(); ?>assets/images/hris/header.png" alt="header">
                             </div>

                </tr>

                <div class="sub-title">
                    <h3>LIST OF PERSONNEL</h3>
                    <h4>GSIS, PAGIBIG, PHILHEALTH AND TIN</h4>
                </div>
              

                <thead>
                                            <tr>
                                                <th>Last Name</th>
												<th>First Name</th>
												<th>Middle Name</th>
                                                <th>Employee No.</th>
                                                <th>Position</th>
                                                <th>Department</th>
                                                <th>GSIS</th>
                                                <th>Pagibig</th>
                                                <th>PhilHealth</th>
                                                <th>TIN</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                               <?php
										  $i=1;
										  foreach($data as $row)
										  {
										  echo "<tr>";
										  echo "<td>".$row->LastName."</td>";
										  echo "<td>".$row->FirstName."</td>";
										  echo "<td>".$row->MiddleName."</td>";
										  echo "<td>".$row->IDNumber."</td>";
                                          echo "<td>".$row->empPosition."</td>";
                                          echo "<td>".$row->Department."</td>";
                                          echo "<td>".$row->gsis."</td>";
                                          echo "<td>".$row->pagibig."</td>";
                                          echo "<td>".$row->philHealth."</td>";
                                          echo "<td>".$row->tinNo."</td>";
										  echo "</tr>";
									  
															}
										   ?>

                    </tbody>




</div>
</div>



</body>

</html>

