            <!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->

            <div class="content-page">
                <div class="content">

                    <!-- Start Content-->
                    <div class="container-fluid">

                        <?php
                        // ------------------------------------------------------------------
                        // The four switches that decide what an applicant sees on the
                        // rating sheet. They used to sit as separate table rows with their
                        // own confirm prompt, and Confirmation / Rating / Exam were in fact
                        // one value (settings id 7) - now each is its own row so they can be
                        // set independently. Same behaviour as before, just separated.
                        // Matched by settings_name, not id: Rating and Exam are seeded by
                        // Page_model::setting_row() so their id differs per database.
                        // All four share the same polarity: status 1 = HIDDEN, 0 = SHOWN.
                        // ------------------------------------------------------------------
                        $group_meta = array(
                            'confirm' => array(
                                'label' => 'Confirmation',
                                'icon'  => 'mdi-check-decagram',
                                'desc'  => 'The CONFIRM / WITH QUERY buttons on the applicant rating sheet.',
                                'on'    => 'Confirmation Hidden',
                                'off'   => 'Confirmation Shown',
                            ),
                            'Hide DQ' => array(
                                'label' => 'Status',
                                'icon'  => 'mdi-eye-off-outline',
                                'desc'  => 'The application status. When hidden the applicant sees "Application Submitted" instead of the real status, including Disqualified.',
                                'on'    => 'Status Hidden',
                                'off'   => 'Status Shown',
                            ),
                            'Hide Rating Scores' => array(
                                'label' => 'Rating',
                                'icon'  => 'mdi-numeric',
                                'desc'  => 'Education, Training, Experience, LET and the other criteria scores. When hidden they show a "Rated" badge instead of the number.',
                                'on'    => 'Rating Hidden',
                                'off'   => 'Rating Shown',
                            ),
                            'Hide Interview and Exam' => array(
                                'label' => 'Exam',
                                'icon'  => 'mdi-clipboard-text-outline',
                                'desc'  => 'Interview, Written Examination and Skills scores. When hidden they show a "Rated" badge instead of the number.',
                                'on'    => 'Exam Hidden',
                                'off'   => 'Exam Shown',
                            ),
                        );

                        // split the settings coming from the controller
                        $group_rows = array();
                        $other_rows = array();

                        foreach ($page as $row) {
                            if (isset($group_meta[$row->settings_name])) {
                                $group_rows[$row->settings_name] = $row;
                            } else {
                                $other_rows[] = $row;
                            }
                        }
                        $group_on = 0;
                        foreach ($group_rows as $g) {
                            if ((int) $g->status === 1) {
                                $group_on++;
                            }
                        }
                        ?>

                        <!-- start page title -->
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box">
                                    <h4 class="page-title mb-0"><?= $title; ?></h4>
                                    <p class="text-muted mb-0">Enable or disable system-wide behaviour. Changes take effect immediately.</p>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->

                        <?php if ($this->session->flashdata('success')) : ?>
                            <div class="row">
                                <div class="col-12">
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <i class="mdi mdi-check-circle-outline mr-1"></i><?= $this->session->flashdata('success'); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($this->session->flashdata('danger')) : ?>
                            <div class="row">
                                <div class="col-12">
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <i class="mdi mdi-alert-circle-outline mr-1"></i><?= $this->session->flashdata('danger'); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($group_rows)) : ?>
                        <!-- ===== Grouped: Confirmation ===== -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card os-card">
                                    <div class="card-body">

                                        <div class="os-group-head">
                                            <div>
                                                <h4 class="header-title mb-1">
                                                    <i class="mdi mdi-shield-check-outline text-primary mr-1"></i>Applicant Rating Sheet
                                                </h4>
                                                <p class="text-muted mb-0">Each one is independent — switch any of them without touching the others. <strong>ON = hidden from the applicant, OFF = shown.</strong></p>
                                            </div>
                                            <span class="badge badge-primary os-count"><?= $group_on; ?> of <?= count($group_rows); ?> ON</span>
                                        </div>

                                        <form id="confirmation-form" method="post" action="<?= base_url(); ?>Pages/update_settings_group">

                                            <div class="os-switch-list">
                                                <?php foreach ($group_meta as $gname => $meta) : ?>
                                                    <?php if (!isset($group_rows[$gname])) { continue; } ?>
                                                    <?php
                                                        $g     = $group_rows[$gname];
                                                        $is_on = ((int) $g->status === 1);
                                                    ?>
                                                    <div class="os-switch-item">
                                                        <div class="os-switch-icon">
                                                            <i class="mdi <?= $meta['icon']; ?>"></i>
                                                        </div>

                                                        <div class="os-switch-text">
                                                            <div class="os-switch-title">
                                                                <?= $meta['label']; ?>
                                                                <small class="text-muted">(<?= $g->settings_name; ?>)</small>
                                                            </div>
                                                            <div class="os-switch-desc"><?= $meta['desc']; ?></div>
                                                            <span class="badge os-state <?= $is_on ? 'badge-success' : 'badge-secondary'; ?>"
                                                                  data-on="<?= $meta['on']; ?>" data-off="<?= $meta['off']; ?>">
                                                                <?= $is_on ? $meta['on'] : $meta['off']; ?>
                                                            </span>
                                                        </div>

                                                        <div class="os-switch-control">
                                                            <input type="hidden" name="settings_id[]" value="<?= (int) $g->id; ?>">
                                                            <div class="custom-control custom-switch">
                                                                <input type="checkbox" class="custom-control-input os-toggle"
                                                                       id="set_<?= (int) $g->id; ?>"
                                                                       name="settings_status[<?= (int) $g->id; ?>]"
                                                                       value="1" <?= $is_on ? 'checked' : ''; ?>>
                                                                <label class="custom-control-label" for="set_<?= (int) $g->id; ?>"></label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>

                                            <div class="text-right mt-3">
                                                <a href="<?= base_url(); ?>Pages/other_settings" class="btn btn-light waves-effect mr-1">Reset</a>
                                                <button type="submit" class="btn btn-primary waves-effect waves-light">
                                                    <i class="mdi mdi-content-save-outline mr-1"></i>Save Confirmation Settings
                                                </button>
                                            </div>

                                        </form>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- ===== Remaining settings ===== -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card os-card">
                                    <div class="card-body table-responsive">
                                        <h4 class="header-title mb-1">
                                            <i class="mdi mdi-tune text-primary mr-1"></i>Other Settings
                                        </h4>
                                        <p class="text-muted mb-3">Individual switches. Each one is applied right away.</p>

                                        <table id="datatable" class="table table-hover table-centered mb-0 os-table" style="border-collapse: collapse; border-spacing: 0; width: 100%;">

                                            <thead class="thead-light">
                                                <tr>
                                                    <th style="width:60%;">SETTINGS NAME</th>
                                                    <th style="width:20%;">STATUS</th>
                                                    <th style="width:20%;" class="text-right">ACTION</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <?php foreach ($other_rows as $row) { ?>
                                                    <?php $locked = ((int) $row->status === 1); ?>
                                                    <tr>
                                                        <td>
                                                            <span class="os-name"><?= $row->settings_name; ?></span>
                                                        </td>
                                                        <td>
                                                            <span class="badge <?= $locked ? 'badge-danger' : 'badge-success'; ?> os-state">
                                                                <?= $locked ? 'Locked' : 'Unlocked'; ?>
                                                            </span>
                                                        </td>
                                                        <td class="text-right">
                                                            <a onclick="return confirm('Are you sure?')"
                                                               href="<?= base_url(); ?>Pages/update_settings/<?= $row->id; ?>/<?= $locked ? 0 : 1; ?>"
                                                               class="btn btn-sm <?= $locked ? 'btn-outline-success' : 'btn-outline-danger'; ?>">
                                                                <i class="mdi <?= $locked ? 'mdi-lock-open-variant-outline' : 'mdi-lock-outline'; ?> mr-1"></i><?= $locked ? 'Unlock' : 'Lock'; ?>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
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

<style>
    .os-card { border: 1px solid #e9edf3; border-radius: .5rem; box-shadow: 0 1px 2px rgba(16, 24, 40, .05); }
    .os-group-head { display: flex; align-items: flex-start; justify-content: space-between; flex-wrap: wrap; gap: .5rem; padding-bottom: .85rem; margin-bottom: 1rem; border-bottom: 1px solid #eef1f6; }
    .os-count { font-size: .72rem; letter-spacing: .3px; padding: .4rem .6rem; }

    .os-switch-list { display: flex; flex-direction: column; gap: .65rem; }
    .os-switch-item { display: flex; align-items: center; gap: .9rem; padding: .9rem 1rem; border: 1px solid #e9edf3; border-radius: .45rem; background: #fcfdff; transition: border-color .15s ease, box-shadow .15s ease, background .15s ease; }
    .os-switch-item:hover { border-color: #cdd8e6; box-shadow: 0 2px 6px rgba(16, 24, 40, .06); background: #fff; }

    .os-switch-icon { flex: 0 0 40px; width: 40px; height: 40px; border-radius: 50%; background: #eef3ff; color: #3b7ddd; display: flex; align-items: center; justify-content: center; font-size: 1.15rem; }
    .os-switch-text { flex: 1 1 auto; min-width: 0; }
    .os-switch-title { font-weight: 600; color: #313a46; margin-bottom: .15rem; }
    .os-switch-title small { font-weight: 400; }
    .os-switch-desc { font-size: .8125rem; color: #8a94a6; margin-bottom: .35rem; }
    .os-switch-control { flex: 0 0 auto; padding-left: .5rem; }
    .os-switch-control .custom-control-input { width: 2.25rem; height: 1.25rem; }

    .os-state { font-weight: 500; letter-spacing: .2px; padding: .35rem .55rem; }
    .os-name { font-weight: 500; color: #313a46; }
    .os-table thead th { font-size: .72rem; letter-spacing: .4px; color: #8a94a6; border-top: 0; }

    @media (max-width: 575.98px) {
        .os-switch-item { flex-wrap: wrap; }
        .os-switch-control { width: 100%; padding-left: 0; padding-top: .5rem; }
    }
</style>

<script>
    (function () {
        // live badge update so the admin sees the effect before saving
        document.querySelectorAll('#confirmation-form .os-toggle').forEach(function (el) {
            el.addEventListener('change', function () {
                var badge = this.closest('.os-switch-item').querySelector('.os-state');
                if (!badge) { return; }
                badge.innerHTML = this.checked ? badge.getAttribute('data-on') : badge.getAttribute('data-off');
                badge.classList.toggle('badge-success', this.checked);
                badge.classList.toggle('badge-secondary', !this.checked);
            });
        });

        var form = document.getElementById('confirmation-form');
        if (form) {
            form.addEventListener('submit', function (e) {
                if (!confirm('Save the Confirmation settings?')) {
                    e.preventDefault();
                }
            });
        }
    })();
</script>
