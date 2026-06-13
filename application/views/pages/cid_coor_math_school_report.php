

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
                              
                                    <div class="clearfix"></div>
                                    
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->

                        

                    

                <div class="row">
                    <div class="col-xl-12">
                        <div class="card-box">
                            <h4 class="header-title mb-4">District Level Proficiency</h4>

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

                                        <?php 
    // get the URI segment (default to 1 if missing/invalid)
    $active_q = (int)$this->uri->segment(3);
    if ($active_q < 1 || $active_q > 4) {
        $active_q = 1;
    }
?>

<!-- Nav Tabs -->
<ul class="nav nav-tabs">
    <?php for ($q = 1; $q <= 4; $q++): ?>
        <li class="nav-item">
            <a href="#q<?= $q ?>" 
               data-toggle="tab" 
               aria-expanded="<?= $q == $active_q ? 'true' : 'false' ?>" 
               class="nav-link <?= $q == $active_q ? 'active' : '' ?>">
                <span class="d-block d-sm-none">
                    <i class="mdi mdi-<?= $q == 1 ? 'home-variant-outline' : ($q == 2 ? 'account-outline' : ($q == 3 ? 'email-outline' : 'settings-outline')) ?> font-18"></i>
                </span>
                <span class="d-none d-sm-block">Quarter <?= $q ?></span>
            </a>
        </li>
    <?php endfor; ?>
</ul>

<!-- Tab Content -->
<div class="tab-content">
    <?php for ($q = 1; $q <= 4; $q++): ?>
        <div class="tab-pane fade <?= $q == $active_q ? 'show active' : '' ?>" id="q<?= $q ?>">
            <table class="table table-bordered mb-0">
                <thead>
                    <tr>
                        <th>District</th>
                        <th class="text-center">No. of Learners<br /> with 75% MPS<br /> and Above</th>
                        <th>Total No. of Learners</th>
                        <th>AVE MPS * No. of Learners</th>
                        <th>PROFICIENCY</th>
                        <th class="text-center">PERCENTAGE OF LEARNERS<br /> WITH ATLEAST 75%</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($school as $row): 
                        $data = $this->Coor_model->get_summary_by_level_subject_by_school_report($row->schoolID, $q);
                    ?>
                    <tr>
                        <th>
                            <a href="<?= base_url(); ?>Coor/coor_entry_view_admin/<?= $q; ?>/<?= $row->schoolID; ?>">
                                <?= $row->schoolName; ?>
                            </a>
                        </th>
                        <td class="text-center"><span class="badge badge-primary"><?= $data->total_abovemps; ?></span></td>
                        <td class="text-center"><span class="badge badge-primary"><?= $data->total_learners; ?></span></td>
                        <td class="text-center"><span class="badge badge-primary"><?= $data->total_total; ?></span></td>
                        <td class="text-center">
                            <?php if($data->total_learners != 0): ?>
                                <span class="badge badge-info"><?= number_format($data->total_total/$data->total_learners, 2); ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php if($data->total_learners != 0): ?>
                                <span class="badge badge-purple"><?= number_format(($data->total_abovemps/$data->total_learners)*100, 2); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endfor; ?>
</div>
                        </div>
                    </div>
                </div>
                <!-- end col -->



                </div>
                    <!-- end container-fluid -->

                </div>
                <!-- end content -->


             

   

 