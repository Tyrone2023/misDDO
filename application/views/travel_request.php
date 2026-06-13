<?php include('templates/head.php'); ?>
<?php include('templates/header.php'); ?>
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
?>

<!-- ============================================================== -->
<!-- Start Page Content here -->
<!-- ============================================================== -->

<div class="content-page">
    <div class="content">
        

        <!-- Start Content-->
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <a href="<?= site_url('travel/create') ?>" class="btn btn-primary">Add New Request</a>

                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body table-responsive">
                            <h4 class="header-title mb-4">Travel Requests</h4>
                                    <?php if($this->session->flashdata('success')) : ?>

                                        <?= '<div class="alert alert-success alert-dismissible fade show" role="alert">
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>'
                                                .$this->session->flashdata('success'). 
                                            '</div>'; 
                                        ?>
                                        <?php endif; ?>

                                        <?php if($this->session->flashdata('danger')) : ?>
                                        <?= '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>'
                                                .$this->session->flashdata('danger'). 
                                            '</div>'; 
                                        ?>
                                        <?php endif;  ?>
                                        


                            <table class="table mb-0">
                                <thead>
                                    <tr>
                                        <!-- <th>ID</th> -->
                                        <th>Purpose</th>
                                        <th>Destination</th>
                                        <th>Inclusive Dates</th>
                                        <th>Status</th>
                                        <th>Date Encoded</th>
                                        <th>Travel Type</th>
                                        <th>Attachment</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($requests as $row): ?>
                                        <?php $track = $this->Common->one_cond_count_row('travel_tracker','travel_req_id',$row->id); ?>
                                        <tr>
                                            <!-- <td><?= $row->id ?></td> -->
                                            <td><?= $this->TravelModel->add_br_every_words($row->purpose, 10); ?></td>
                                            <td><?= $row->destination ?></td>
                                            <td><?= $row->inclusive_date ?></td>
                                            <td>
                                                <?php
                                                
                                                $status = $row->status;
                                                $class = '';
                                                switch ($status) {
                                                    case 'Pending':
                                                        $class = 'badge badge-warning'; // yellow/orange
                                                        break;
                                                    case 'Approved':
                                                        $class = 'badge badge-success'; // green
                                                        break;
                                                    case 'Returned':
                                                        $class = 'badge badge-danger'; // red
                                                        break;
                                                    default:
                                                        $class = 'badge badge-secondary'; // grey
                                                }
                                                ?>
                                                <span class="<?= $class ?>">
                                                 <?php if($track->num_rows() >= 1){ ?>   
                                                <?= $status ?>
                                                <?php }else{ ?>
                                                 Invalid
                                                <?php } ?>

                                                <?php if($status == "Rejected"){ $reason = $this->Common->two_cond_row('travel_tracker','stat','Rejected','travel_req_id',$row->id); ?>
                                                : <?= $this->TravelModel->add_br_every_words($reason->remarks, 3); ?>
                                                <?php }?>
                                                </span>
                                            </td>

                                            <td><?= date('F d, Y h:i A', strtotime($row->date_created)); ?></td>
                                            <td class="text-center"><?= $row->ttype == 0 ? '<span class="badge badge-purple">Within the Division</span>' : '<span class="badge badge-info">Outside Division</span>'; ?></span></td>
                                            <td>
                                                <div class="button-list">
                                                <?php if($row->file_url != ''){?>
                                                    <a href="<?= base_url(); ?>uploads/travel/<?= $row->file_url; ?>" target="_blank" class="btn btn-sm btn-purple"><i class="ion ion-md-attach" ></i> View</a>
                                                <?php } ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="button-list"><?php $ca = $this->Common->one_cond_count_row('travel_ca','travel_id',$row->id); ?>
                                                <?php if ($row->status == 'Pending' || $row->status == 'Endorsed'): ?>

                                                    <a href="<?= site_url('travel/edit/' . $row->id) ?>" class="btn btn-sm btn-primary"><i class="fas fa-pencil-alt"></i></a>
                                                    <!-- <a href="<?= site_url('travel/travel_delete/' . $row->id) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this request?')"><i class="fas fa-times"></i></a> -->
                                                    <button type="button"
                                                            class="btn btn-danger btn-sm"
                                                            onclick="if(confirm('Delete this request?')){ window.location.href='<?= base_url('travel/delete_the_travel/' . $row->id); ?>'; }">
                                                        <i class="fa fa-times"></i>
                                                    </button>

                                                    
                                                <?php endif; ?>

                                                <?php if($row->status == 'Approved'){?>
                                                <?php if($row->ttype == 0){ ?>

                                                <?php if($ca->num_rows() >= 1){ ?> 
                                                    <a href="<?= site_url('travel/travel_ca/' . $row->IDNumber .'/'. $row->id) ?>" class="btn btn-sm btn-primary" target="_blank"><i class="fas fa-scroll"></i></a>
                                                <?php }else{ ?> 
                                                    <a href="https://www.csm.depedmis.com/pages/csm_questionnaire/<?= $row->id; ?>" class="btn btn-sm btn-primary" target="_blank"><i class="fas fa-smile-wink "></i></a>
                                                <?php } ?>

                                                <?php } ?>
                                                <?php } ?>
                                                

                                                <?php if($track->num_rows() >= 1){ ?> 
                                                <a href="<?= site_url('travel/travel_print_view/' . $row->id) ?>" class="btn btn-sm btn-success" target="_blank"><i class="fa fa-print"></i></a>
                                                <?php } ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>




                        </div>
                    </div>
                </div>
            </div>
            <!-- end row -->

        </div>
        <!-- end container-fluid -->

    </div>
    <!-- end content -->

    <?php include('templates/footer.php'); ?>