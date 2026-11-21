            <?php include('templates/head.php'); ?>
            <?php include('templates/header.php'); ?>

            <div class="content-page">
                <div class="content">
                    <div class="container-fluid at-page">

                        <div class="at-header">
                            <div>
                                <span class="at-eyebrow"><i class="mdi mdi-cash-multiple"></i> Budget</span>
                                <h3 class="at-title">Allocation Types</h3>
                                <p class="at-sub">Types available when adding a school allocation &middot; also listed as budget sources</p>
                            </div>
                            <a href="<?= base_url(); ?>Page/school_allocations" class="at-btn"><i class="mdi mdi-arrow-left"></i> School Allocations</a>
                        </div>

                        <?php if ($this->session->flashdata('success')) : ?>
                            <div class="at-alert at-alert-success alert alert-dismissible fade show" role="alert">
                                <i class="mdi mdi-check-circle"></i><span><?= html_escape($this->session->flashdata('success')); ?></span>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            </div>
                        <?php endif; ?>
                        <?php if ($this->session->flashdata('danger')) : ?>
                            <div class="at-alert at-alert-danger alert alert-dismissible fade show" role="alert">
                                <i class="mdi mdi-alert-circle"></i><span><?= html_escape($this->session->flashdata('danger')); ?></span>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            </div>
                        <?php endif; ?>

                        <div class="row">
                            <div class="col-lg-4">
                                <div class="at-card at-pad">
                                    <div class="at-step"><span><i class="mdi mdi-plus"></i></span> New type</div>
                                    <?= form_open('Page/alloc_type_save'); ?>
                                        <input type="hidden" name="id" value="0">
                                        <div class="form-group">
                                            <label>Type name <span class="at-req">*</span></label>
                                            <input type="text" name="description" class="form-control" maxlength="60" required autocomplete="off" placeholder="e.g. ARAL PROGRAM">
                                        </div>
                                        <button type="submit" class="at-btn at-btn-primary w-100 justify-content-center"><i class="mdi mdi-content-save-outline"></i> Add Type</button>
                                    </form>
                                    <p class="at-note"><i class="mdi mdi-lock-outline"></i> MOOE, SBFP and SNED Fund are system types used by the reports and cannot be renamed or deleted. Types in use by allocations cannot be deleted.</p>
                                </div>
                            </div>

                            <div class="col-lg-8">
                                <div class="at-card">
                                    <div class="table-responsive">
                                        <table class="table at-table mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Type</th>
                                                    <th class="text-right">Allocations</th>
                                                    <th class="text-right">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($types)) { ?>
                                                <tr><td colspan="3" class="at-empty">No allocation types yet.</td></tr>
                                                <?php } ?>
                                                <?php foreach ($types as $t) { ?>
                                                <tr>
                                                    <td>
                                                        <span class="at-badge<?= strtoupper(trim($t->description)) === 'MOOE' ? ' at-badge-mooe' : ''; ?>"><?= html_escape($t->description); ?></span>
                                                        <?php if ($t->locked) { ?><span class="at-lock" title="System type"><i class="mdi mdi-lock-outline"></i></span><?php } ?>
                                                    </td>
                                                    <td class="text-right at-num"><?= number_format($t->alloc_count); ?></td>
                                                    <td class="text-right at-actions">
                                                        <?php if ($t->locked) { ?>
                                                        <span class="at-muted">Locked</span>
                                                        <?php } else { ?>
                                                        <button type="button" class="at-icon-btn edit" title="Rename" data-toggle="modal" data-target="#at_edit"
                                                            data-id="<?= (int) $t->id; ?>" data-name="<?= html_escape($t->description); ?>" data-count="<?= (int) $t->alloc_count; ?>"><i class="mdi mdi-pencil-outline"></i></button>
                                                        <?php if ($t->alloc_count == 0) { ?>
                                                        <?= form_open('Page/alloc_type_delete', array('class' => 'd-inline', 'onsubmit' => "return confirm('Delete this allocation type?');")); ?>
                                                            <input type="hidden" name="id" value="<?= (int) $t->id; ?>">
                                                            <button type="submit" class="at-icon-btn del" title="Delete"><i class="mdi mdi-trash-can-outline"></i></button>
                                                        </form>
                                                        <?php } else { ?>
                                                        <span class="at-icon-btn disabled" title="In use by allocations"><i class="mdi mdi-trash-can-outline"></i></span>
                                                        <?php } ?>
                                                        <?php } ?>
                                                    </td>
                                                </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Rename modal -->
                <div id="at_edit" class="modal fade at-modal" tabindex="-1" role="dialog" aria-labelledby="atEditLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <?= form_open('Page/alloc_type_save'); ?>
                            <div class="modal-header">
                                <h5 class="modal-title" id="atEditLabel">Rename Allocation Type</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="id" id="at_edit_id" value="">
                                <div class="form-group mb-2">
                                    <label>Type name <span class="at-req">*</span></label>
                                    <input type="text" name="description" id="at_edit_name" class="form-control" maxlength="60" required autocomplete="off">
                                </div>
                                <p class="at-note mb-0" id="at_edit_note"></p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="at-btn at-btn-ghost" data-dismiss="modal">Cancel</button>
                                <button type="submit" class="at-btn at-btn-primary"><i class="mdi mdi-content-save-outline"></i> Save</button>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>

            <?php include('templates/footer.php'); ?>

            <style>
            .at-page, .at-modal { --at-primary: #2c5282; --at-primary-dark: #1f3a5f; --at-text: #1f2937; --at-muted: #8792a2; --at-line: #edf0f4; --at-bg: #f7f9fc; }
            .at-header { display: flex; flex-wrap: wrap; align-items: flex-end; justify-content: space-between; gap: 1rem; padding: 1.4rem 0 1.2rem; }
            .at-eyebrow { display: inline-flex; align-items: center; gap: .35rem; font-size: .72rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: var(--at-primary); }
            .at-title { margin: .25rem 0 .2rem; font-weight: 700; color: var(--at-text); }
            .at-sub { margin: 0; color: var(--at-muted); font-size: .85rem; }
            .at-btn { display: inline-flex; align-items: center; gap: .4rem; border: 1px solid #dbe2ea; background: #fff; color: #4a5568; border-radius: 10px; padding: .55rem 1rem; font-size: .85rem; font-weight: 600; transition: all .15s ease; cursor: pointer; line-height: 1.2; }
            .at-btn:hover { text-decoration: none; background: var(--at-bg); color: var(--at-primary); }
            .at-btn-primary { background: var(--at-primary); border-color: var(--at-primary); color: #fff; box-shadow: 0 4px 12px rgba(44, 82, 130, .22); }
            .at-btn-primary:hover { background: var(--at-primary-dark); border-color: var(--at-primary-dark); color: #fff; }
            .at-btn-ghost { background: #f1f4f8; border-color: #f1f4f8; }
            .at-alert { display: flex; align-items: center; gap: .6rem; border: 0; border-radius: 12px; padding: .8rem 1rem; font-size: .875rem; }
            .at-alert i { font-size: 1.2rem; }
            .at-alert-success { background: #e8f6ee; color: #1e7d44; }
            .at-alert-danger { background: #fdecec; color: #b42323; }
            .at-card { background: #fff; border: 1px solid var(--at-line); border-radius: 16px; box-shadow: 0 1px 3px rgba(16, 30, 54, .04), 0 8px 24px rgba(16, 30, 54, .04); margin-bottom: 1.5rem; overflow: hidden; animation: atFade .3s ease both; }
            .at-pad { padding: 1.2rem 1.3rem; }
            @keyframes atFade { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: none; } }
            .at-step { display: flex; align-items: center; gap: .5rem; font-size: .72rem; font-weight: 700; letter-spacing: .07em; text-transform: uppercase; color: var(--at-primary); margin-bottom: .9rem; }
            .at-step span { width: 20px; height: 20px; border-radius: 50%; background: var(--at-primary); color: #fff; display: inline-flex; align-items: center; justify-content: center; font-size: .75rem; }
            .at-page label, .at-modal label { font-size: .75rem; font-weight: 600; color: #4a5568; margin-bottom: .35rem; }
            .at-page .form-control, .at-modal .form-control { height: 42px; border-radius: 10px; border-color: #e1e6ed; font-size: .88rem; }
            .at-page .form-control:focus, .at-modal .form-control:focus { border-color: #9dbbe3; box-shadow: 0 0 0 3px rgba(44, 82, 130, .12); }
            .at-req { color: #c53030; }
            .at-note { font-size: .76rem; color: var(--at-muted); margin: .9rem 0 0; }
            .at-table thead th { background: var(--at-bg); border: 0; border-bottom: 1px solid var(--at-line); font-size: .7rem; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: var(--at-muted); padding: .8rem 1.2rem; }
            .at-table td { vertical-align: middle; border-top: 1px solid var(--at-line); padding: .75rem 1.2rem; font-size: .88rem; color: var(--at-text); }
            .at-table tbody tr:hover { background: #fafcff; }
            .at-num { font-variant-numeric: tabular-nums; font-weight: 600; }
            .at-badge { display: inline-block; background: #f1f4f8; color: #4a5568; border-radius: 999px; padding: .25rem .7rem; font-size: .78rem; font-weight: 600; }
            .at-badge-mooe { background: #e6f0fb; color: var(--at-primary); }
            .at-lock { color: var(--at-muted); margin-left: .35rem; }
            .at-muted { color: var(--at-muted); font-size: .78rem; }
            .at-empty { text-align: center; color: var(--at-muted); padding: 2rem !important; }
            .at-actions { white-space: nowrap; }
            .at-icon-btn { display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 8px; border: 0; background: transparent; color: #6b7685; font-size: 1.05rem; cursor: pointer; transition: background .15s, color .15s; }
            .at-icon-btn.edit:hover { background: #e6f0fb; color: var(--at-primary); }
            .at-icon-btn.del:hover { background: #fdecec; color: #b42323; }
            .at-icon-btn.disabled { opacity: .35; cursor: not-allowed; }
            .at-modal .modal-content { border: 0; border-radius: 16px; overflow: hidden; box-shadow: 0 24px 60px rgba(16, 30, 54, .25); }
            .at-modal .modal-header { border-bottom: 1px solid var(--at-line); padding: 1rem 1.3rem; }
            .at-modal .modal-title { font-size: 1rem; font-weight: 700; color: var(--at-text); }
            .at-modal .modal-footer { border-top: 1px solid var(--at-line); background: var(--at-bg); }
            </style>

            <script>
                $(function() {
                    $('#at_edit').on('show.bs.modal', function(e) {
                        var $b = $(e.relatedTarget), n = Number($b.data('count')) || 0;
                        $('#at_edit_id').val($b.data('id'));
                        $('#at_edit_name').val($b.attr('data-name'));
                        $('#at_edit_note').text(n > 0 ? 'The new name will also be applied to the ' + n.toLocaleString() + ' allocation(s) using this type.' : '');
                    });
                    $('#at_edit').on('shown.bs.modal', function() { $('#at_edit_name').trigger('focus').trigger('select'); });
                });
            </script>
