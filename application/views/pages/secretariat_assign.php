<?php
function h($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); }
$selectedId = $selected_id ?? 0;
$active     = $assignments[$selectedId] ?? [];
?>

<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="page-title mb-0"><?= h($title ?? 'Assign Secretariat Levels'); ?></h4>
                    </div>
                </div>
            </div>

            <?php if ($this->session->flashdata('success')) : ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= h($this->session->flashdata('success')); ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <?php if ($this->session->flashdata('danger')) : ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= h($this->session->flashdata('danger')); ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-lg-7">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title">Assign Levels to Secretariat</h4>
                            <?php if (empty($secretariats)) : ?>
                                <div class="alert alert-warning mb-0">No Secretariat accounts yet. Create one first via Manage Users.</div>
                            <?php else : ?>
                                <?= form_open('SecretariatAssign/save'); ?>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label class="col-form-label">Secretariat User</label>
                                        <select name="secretariat_id" class="form-control" onchange="window.location='<?= base_url('SecretariatAssign?id='); ?>'+this.value;">
                                            <?php foreach ($secretariats as $sec) : ?>
                                                <option value="<?= (int) $sec->id; ?>" <?= $sec->id == $selectedId ? 'selected' : ''; ?>>
                                                    <?= h("{$sec->lname}, {$sec->fname} ({$sec->username})"); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-form-label d-block">Levels to handle</label>
                                    <div class="row">
                                        <?php foreach ($job_types as $id => $label) : ?>
                                            <div class="col-md-6 mb-2">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox"
                                                           class="custom-control-input"
                                                           id="jt<?= (int) $id; ?>"
                                                           name="job_types[]"
                                                           value="<?= (int) $id; ?>"
                                                           <?= in_array((int) $id, $active, true) ? 'checked' : ''; ?>>
                                                    <label class="custom-control-label" for="jt<?= (int) $id; ?>"><?= h($label); ?></label>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary">Save Assignment</button>
                                <?= form_close(); ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title">Current Assignments</h4>
                            <?php if (empty($secretariats)) : ?>
                                <p class="text-muted mb-0">No data to display.</p>
                            <?php else : ?>
                                <table class="table table-sm table-striped mb-0">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Username</th>
                                            <th>Levels</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($secretariats as $sec) : ?>
                                            <?php
                                                $levels = $assignments[$sec->id] ?? [];
                                                $labels = [];
                                                foreach ($levels as $lv) {
                                                    $labels[] = $job_types[$lv] ?? $lv;
                                                }
                                            ?>
                                            <tr>
                                                <td><?= h("{$sec->lname}, {$sec->fname}"); ?></td>
                                                <td><?= h($sec->username); ?></td>
                                                <td><?= empty($labels) ? '<span class="text-muted">None</span>' : h(implode(', ', $labels)); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
