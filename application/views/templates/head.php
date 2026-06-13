<?php
// Redirect to login for all pages except the explicitly public Research endpoints
$seg1 = strtolower((string) $this->uri->segment(1)); // controller
$seg2 = strtolower((string) $this->uri->segment(2)); // method

$is_public_research = (
    $seg1 === 'research' &&
    in_array($seg2, ['public_form', 'public_store', 'public_success', 'report'], true)
);

if ($this->session->logged_in == false && !$is_public_research) {
  redirect(base_url() . 'log_in');
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <title>EduVision MIS</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta content="Responsive bootstrap 4 admin template" name="description" />
  <meta content="Coderthemes" name="author" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <!-- App favicon -->
  <link rel="shortcut icon" href="<?= base_url(); ?>assets/images/hris.ico">


  <!-- third party css -->
  <link href="<?= base_url(); ?>assets/libs/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
  <link href="<?= base_url(); ?>assets/libs/datatables/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />
  <link href="<?= base_url(); ?>assets/libs/datatables/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css" />
  <link href="<?= base_url(); ?>assets/libs/datatables/select.bootstrap4.min.css" rel="stylesheet" type="text/css" />
  <!-- <link href="<?= base_url(); ?>assets/libs/rwd-table/rwd-table.min.css" rel="stylesheet" type="text/css" /> -->

  <!-- App css -->
  <link href="<?= base_url(); ?>assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" id="bootstrap-stylesheet" />
  <link href="<?= base_url(); ?>assets/css/icons.min.css" rel="stylesheet" type="text/css" />
  <link href="<?= base_url(); ?>assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-stylesheet" />
  <link href="<?= base_url(); ?>assets/libs/custombox/custombox.min.css" rel="stylesheet" type="text/css">
  <link href="<?= base_url(); ?>assets/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css" />


  <link href="<?= base_url(); ?>assets/libs/fullcalendar/fullcalendar.min.css" rel="stylesheet" type="text/css" />
  <link href="<?= base_url(); ?>assets/libs/select2/select2.min.css" rel="stylesheet" type="text/css" />

  <link href="<?= base_url(); ?>assets/libs/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
  <link href="<?= base_url(); ?>assets/libs/datatables/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />
  <link href="<?= base_url(); ?>assets/libs/datatables/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css" />
  <link href="<?= base_url(); ?>assets/libs/datatables/select.bootstrap4.min.css" rel="stylesheet" type="text/css" />



  <link href="<?= base_url(); ?>assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.css" rel="stylesheet" />
  <link href="<?= base_url(); ?>assets/libs/bootstrap-timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
  <link href="<?= base_url(); ?>assets/libs/bootstrap-colorpicker/bootstrap-colorpicker.min.css" rel="stylesheet">
  <link href="<?= base_url(); ?>assets/libs/bootstrap-datepicker/bootstrap-datepicker.css" rel="stylesheet">


</head>
