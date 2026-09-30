            <?php include('templates/head.php'); ?>
            <?php include('templates/header.php'); ?>

            <?php
            // Rows go to the browser as compact JSON and DataTables renders only the visible page
            // (deferRender) — printing ~3,400 rows x 19 cells as HTML is what made the page slow.
            $sa_months = array('mo_jan', 'mo_feb', 'mo_mar', 'mo_apr', 'mo_may', 'mo_jun', 'mo_jul', 'mo_aug', 'mo_sep', 'mo_oct', 'mo_nov', 'mo_dec');

            $sa_names = array();
            foreach ($school as $s) {
                $sa_names[$s->schoolID] = $s;
            }

            $sa_rows  = array();
            $sa_years = array();
            $sa_types = array();
            foreach ($data as $row) {
                $sch = isset($sa_names[$row->schoolID]) ? $sa_names[$row->schoolID] : null;
                $mo  = array();
                foreach ($sa_months as $c) {
                    $mo[] = round((float) $row->$c, 2);
                }
                $sa_rows[] = array(
                    'id' => (int) $row->id,
                    's'  => (string) $row->schoolID,
                    'n'  => ($sch && $sch->schoolName !== '') ? $sch->schoolName : 'Unknown school',
                    'd'  => $sch ? (string) $sch->district : '',
                    'y'  => (string) $row->alloc_year,
                    'b'  => (string) $row->alloc_batch,
                    't'  => (string) $row->alloc_type,
                    'g'  => (string) $row->alloc_group,
                    'p'  => $this->SGODModel->alloc_program_label($row),
                    'a'  => round((float) str_replace(',', '', $row->alloc_amount), 2),
                    'mo' => $mo,
                );
                $sa_years[$row->alloc_year] = true;
                $sa_types[trim($row->alloc_type)] = true;
            }
            krsort($sa_years);
            ksort($sa_types);
            $sa_next_batch = $last->alloc_batch + 1;

            // Shortest float form in the JSON (226.24, not 226.2400000000000090...).
            $sa_prec = ini_get('serialize_precision');
            ini_set('serialize_precision', -1);
            $sa_json = json_encode($sa_rows, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
            ini_set('serialize_precision', $sa_prec);
            ?>

            <!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->

            <div class="content-page">
                <div class="content">

                    <!-- Start Content-->
                    <div class="container-fluid sa-page">

                        <!-- start page header -->
                        <div class="sa-header">
                            <div>
                                <span class="sa-eyebrow"><i class="mdi mdi-cash-multiple"></i> Budget</span>
                                <h3 class="sa-title">School Allocations</h3>
                                <p class="sa-sub">Fund allocations per school and batch &middot; MOOE is distributed across 12 months</p>
                            </div>
                            <a data-toggle="modal" class="open-AddBookDialog sa-btn sa-btn-primary" href="#add"><i class="mdi mdi-plus"></i> Add Allocation</a>
                        </div>
                        <!-- end page header -->

                        <?php if ($this->session->flashdata('success')) : ?>
                            <div class="sa-alert sa-alert-success alert alert-dismissible fade show" role="alert">
                                <i class="mdi mdi-check-circle"></i><span><?= $this->session->flashdata('success'); ?></span>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            </div>
                        <?php endif; ?>

                        <?php if ($this->session->flashdata('danger')) : ?>
                            <div class="sa-alert sa-alert-danger alert alert-dismissible fade show" role="alert">
                                <i class="mdi mdi-alert-circle"></i><span><?= $this->session->flashdata('danger'); ?></span>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            </div>
                        <?php endif; ?>

                        <div class="sa-card">
                            <!-- toolbar -->
                            <div class="sa-toolbar">
                                <div class="sa-search">
                                    <i class="mdi mdi-magnify"></i>
                                    <input type="search" id="sa_search" placeholder="Search school, ID, district, batch, program…" autocomplete="off">
                                </div>
                                <select id="sa_f_year" class="sa-select">
                                    <option value="">All years</option>
                                    <?php foreach (array_keys($sa_years) as $y) { ?>
                                    <option value="<?= html_escape($y); ?>"><?= html_escape($y); ?></option>
                                    <?php } ?>
                                </select>
                                <select id="sa_f_type" class="sa-select">
                                    <option value="">All types</option>
                                    <?php foreach (array_keys($sa_types) as $t) { ?>
                                    <option value="<?= html_escape($t); ?>"><?= html_escape($t); ?></option>
                                    <?php } ?>
                                </select>
                                <div class="sa-summary" id="sa_summary">&nbsp;</div>
                            </div>

                            <div class="sa-table-wrap">
                                <div class="sa-loading" id="sa_loading">
                                    <?php for ($i = 0; $i < 6; $i++) { ?><div class="sa-skel"></div><?php } ?>
                                </div>
                                <table id="sa-table" class="table sa-table" style="width:100%;">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>School</th>
                                            <th>Year</th>
                                            <th>Batch</th>
                                            <th>Type</th>
                                            <th>Program</th>
                                            <th class="text-right">Total Allocation</th>
                                            <th class="text-right">Monthly</th>
                                            <th class="text-right">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                    <!-- end container-fluid -->

                </div>
                <!-- end content -->

                <!-- Add allocation modal -->
                <div id="add" class="modal fade sa-modal" tabindex="-1" role="dialog" aria-labelledby="addLabel" style="display: none;" aria-hidden="true">
                    <div class="modal-dialog modal-xl modal-dialog-centered">
                        <div class="modal-content">
                            <?= form_open('Page/fund_add'); ?>
                            <div class="modal-header">
                                <div class="sa-mh">
                                    <span class="sa-mh-icon"><i class="mdi mdi-cash-plus"></i></span>
                                    <div>
                                        <h5 class="modal-title" id="addLabel">New School Allocation</h5>
                                        <p>Batch code <span class="sa-chip">#<?= $sa_next_batch; ?></span> is assigned automatically.</p>
                                    </div>
                                </div>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            </div>

                            <div class="modal-body">
                                <input type="hidden" required value="<?= $sa_next_batch; ?>" name="bcode">
                                <div class="row">
                                    <div class="col-lg-7">
                                        <div class="sa-step"><span>1</span> School &amp; amount</div>
                                        <div class="form-group">
                                            <label>School <span class="sa-req">*</span></label>
                                            <select class="form-control" data-toggle="select2" name="schoolID" required style="width:100%" data-placeholder="Search school ID or name">
                                                <option></option>
                                                <?php foreach ($school as $row) { ?>
                                                <option value="<?= $row->schoolID; ?>"><?= $row->schoolID; ?> - <?= $row->schoolName; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Fund Allocation Amount <span class="sa-req">*</span></label>
                                            <div class="sa-money">
                                                <span>&#8369;</span>
                                                <input type="text" required value="<?= set_value('alloc_amount'); ?>" id="alloc_amount" name="alloc_amount" class="form-control" inputmode="decimal" autocomplete="off" placeholder="0.00">
                                            </div>
                                        </div>

                                        <div class="sa-step"><span>2</span> Classification</div>
                                        <div class="form-row">
                                            <div class="form-group col-sm-6">
                                                <label>Fiscal Year <span class="sa-req">*</span></label>
                                                <select class="form-control" name="fy" id="sa_add_fy" required>
                                                    <option value="">Select year</option>
                                                    <?php
                                                    $firstYear = (int) date('Y');
                                                    $lastYear = $firstYear + 5;
                                                    for ($i = $firstYear; $i <= $lastYear; $i++) {
                                                        echo '<option value=' . $i . '>' . $i . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="form-group col-sm-6">
                                                <label>Allocation Type <span class="sa-req">*</span></label>
                                                <select class="form-control" data-toggle="select2" name="type" id="sa_add_type" required style="width:100%" data-placeholder="Select type">
                                                    <option></option>
                                                    <?php foreach ($bs as $row) { ?>
                                                    <option value="<?= $row->description; ?>"><?= $row->description; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label>Group <span class="sa-req">*</span></label>
                                            <div class="sa-seg">
                                                <?php
                                                $g = array('Elementary', 'Junior HS', 'Senior HS');
                                                foreach ($g as $k => $row) {
                                                ?>
                                                <label><input type="radio" name="group" value="<?= $row; ?>" required><span><?= $row; ?></span></label>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div class="form-group mb-1">
                                            <label>Program <small class="sa-muted text-lowercase">(optional)</small></label>
                                            <select class="form-control" data-toggle="select2" name="program" style="width:100%" data-placeholder="Auto-label from type and group">
                                                <option value=""></option>
                                                <?php foreach ($alloc_programs as $p) { ?>
                                                <option value="<?= $p; ?>"><?= $p; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-5">
                                        <div class="sa-summary-panel">
                                            <div class="sa-sp-label">Allocation summary</div>
                                            <div class="sa-sp-total">&#8369;<span id="sa_add_total">0.00</span></div>
                                            <div class="sa-sp-meta" id="sa_add_meta"><span class="sa-muted">Fill in the form to see the summary.</span></div>
                                            <div class="sa-sp-label mt-3">Monthly distribution</div>
                                            <div class="sa-months" id="sa_add_months"></div>
                                            <div class="sa-sp-note" id="sa_add_note"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="sa-btn sa-btn-ghost" data-dismiss="modal">Cancel</button>
                                <button type="submit" name="submit" class="sa-btn sa-btn-primary"><i class="mdi mdi-content-save-outline"></i> Save Allocation</button>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Edit allocation modal -->
                <div id="alloc" class="modal fade sa-modal" tabindex="-1" role="dialog" aria-labelledby="editLabel" style="display: none;" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered sa-dialog-md">
                        <div class="modal-content">
                            <?= form_open('Page/fund_update'); ?>
                            <div class="modal-header">
                                <div class="sa-mh">
                                    <span class="sa-avatar sa-avatar-lg" id="sa_edit_avatar">S</span>
                                    <div>
                                        <h5 class="modal-title" id="editLabel"><span id="sa_edit_school">Change Allocation</span></h5>
                                        <div class="sa-mh-meta" id="sa_edit_meta"></div>
                                    </div>
                                </div>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            </div>

                            <div class="modal-body">
                                <div class="sa-compare">
                                    <div>
                                        <div class="sa-sp-label">Current</div>
                                        <div class="sa-compare-val" id="sa_edit_old">0.00</div>
                                    </div>
                                    <i class="mdi mdi-arrow-right"></i>
                                    <div>
                                        <div class="sa-sp-label">New</div>
                                        <div class="sa-compare-val" id="sa_edit_new">0.00</div>
                                    </div>
                                    <span class="sa-delta" id="sa_edit_delta"></span>
                                </div>

                                <div class="form-group">
                                    <label>Fund Allocation <span class="sa-req">*</span></label>
                                    <div class="sa-money">
                                        <span>&#8369;</span>
                                        <input type="text" required value="<?= set_value('pass'); ?>" id="item" name="alloc_amount" class="form-control" inputmode="decimal" autocomplete="off">
                                    </div>
                                </div>
                                <input type="hidden" name="id" id="id" value="">

                                <div class="sa-sp-label">Monthly distribution</div>
                                <div class="sa-months" id="sa_edit_months"></div>
                                <div class="sa-sp-note" id="sa_edit_note"></div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="sa-btn sa-btn-ghost" data-dismiss="modal">Cancel</button>
                                <button type="submit" name="submit" class="sa-btn sa-btn-primary"><i class="mdi mdi-content-save-outline"></i> Update</button>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>

            <?php include('templates/footer.php'); ?>

            <style>
            .sa-page { --sa-primary: #2c5282; --sa-primary-dark: #1f3a5f; --sa-soft: #eef4fc; --sa-text: #1f2937; --sa-muted: #8792a2; --sa-line: #edf0f4; --sa-bg: #f7f9fc; }
            .sa-modal { --sa-primary: #2c5282; --sa-primary-dark: #1f3a5f; --sa-soft: #eef4fc; --sa-text: #1f2937; --sa-muted: #8792a2; --sa-line: #edf0f4; --sa-bg: #f7f9fc; }
            .sa-muted { color: var(--sa-muted); }

            /* ---- Header ---- */
            .sa-header { display: flex; flex-wrap: wrap; align-items: flex-end; justify-content: space-between; gap: 1rem; padding: 1.6rem 0 1.2rem; }
            .sa-eyebrow { display: inline-flex; align-items: center; gap: .3rem; font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: var(--sa-primary); background: var(--sa-soft); border-radius: 999px; padding: .2rem .65rem; }
            .sa-title { margin: .55rem 0 .2rem; font-weight: 700; color: var(--sa-text); letter-spacing: -.01em; }
            .sa-sub { margin: 0; color: var(--sa-muted); font-size: .875rem; }

            /* ---- Buttons ---- */
            .sa-btn { display: inline-flex; align-items: center; gap: .4rem; border: 1px solid #dbe2ea; background: #fff; color: #4a5568; border-radius: 10px; padding: .55rem 1rem; font-size: .85rem; font-weight: 600; transition: all .15s ease; cursor: pointer; line-height: 1.2; }
            .sa-btn:hover { text-decoration: none; background: var(--sa-bg); color: var(--sa-primary); }
            .sa-btn-primary { background: var(--sa-primary); border-color: var(--sa-primary); color: #fff; box-shadow: 0 4px 12px rgba(44, 82, 130, .22); }
            .sa-btn-primary:hover { background: var(--sa-primary-dark); border-color: var(--sa-primary-dark); color: #fff; transform: translateY(-1px); }
            .sa-btn-ghost { background: #f1f4f8; border-color: #f1f4f8; }

            /* ---- Alerts ---- */
            .sa-alert { display: flex; align-items: center; gap: .6rem; border: 0; border-radius: 12px; padding: .8rem 1rem; font-size: .875rem; }
            .sa-alert i { font-size: 1.2rem; }
            .sa-alert-success { background: #e8f6ee; color: #1e7d44; }
            .sa-alert-danger { background: #fdecec; color: #b42323; }

            /* ---- Card / toolbar ---- */
            .sa-card { background: #fff; border: 1px solid var(--sa-line); border-radius: 16px; box-shadow: 0 1px 3px rgba(16, 30, 54, .04), 0 8px 24px rgba(16, 30, 54, .04); margin-bottom: 1.5rem; animation: saFade .35s ease both; }
            @keyframes saFade { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: none; } }
            .sa-toolbar { display: flex; flex-wrap: wrap; align-items: center; gap: .6rem; padding: 1rem 1.2rem; border-bottom: 1px solid var(--sa-line); }
            .sa-search { position: relative; flex: 1 1 280px; max-width: 420px; }
            .sa-search i { position: absolute; left: .75rem; top: 50%; transform: translateY(-50%); color: var(--sa-muted); font-size: 1.1rem; }
            .sa-search input, .sa-select { height: 40px; border: 1px solid #e1e6ed; border-radius: 10px; background: var(--sa-bg); font-size: .85rem; color: var(--sa-text); transition: border-color .15s, box-shadow .15s, background .15s; }
            .sa-search input { width: 100%; padding: 0 .9rem 0 2.3rem; }
            .sa-select { padding: 0 .75rem; min-width: 130px; }
            .sa-search input:focus, .sa-select:focus { outline: 0; background: #fff; border-color: #9dbbe3; box-shadow: 0 0 0 3px rgba(44, 82, 130, .12); }
            .sa-summary { margin-left: auto; font-size: .82rem; color: var(--sa-muted); white-space: nowrap; }
            .sa-summary b { color: var(--sa-text); }

            /* ---- Table ---- */
            .sa-table-wrap { position: relative; overflow-x: auto; padding: 0 .4rem; }
            .sa-table { margin: 0 !important; border-collapse: separate; border-spacing: 0; }
            .sa-table thead th { border: 0; border-bottom: 1px solid var(--sa-line); color: var(--sa-muted); font-size: .68rem; font-weight: 700; text-transform: uppercase; letter-spacing: .07em; padding: .85rem .8rem; white-space: nowrap; background: #fff; }
            .sa-table tbody td { border: 0; border-bottom: 1px solid #f3f5f8; padding: .75rem .8rem; vertical-align: middle; font-size: .85rem; color: var(--sa-text); white-space: nowrap; }
            .sa-table tbody tr:hover > td { background: #fafcff; }
            .sa-table tbody tr.shown > td { background: #f5f9ff; border-bottom-color: transparent; }
            .sa-table td.sa-num { text-align: right; font-variant-numeric: tabular-nums; }
            .sa-amt { font-weight: 700; }
            .sa-per { color: #4a5568; }
            .sa-per small { color: var(--sa-muted); }
            .sa-zero { color: #b3bcc8; }

            .sa-school { display: flex; align-items: center; gap: .7rem; }
            .sa-avatar { flex: 0 0 auto; width: 36px; height: 36px; border-radius: 10px; background: linear-gradient(135deg, #e3edfa, #cfe0f7); color: var(--sa-primary); font-weight: 700; display: inline-flex; align-items: center; justify-content: center; }
            .sa-avatar-lg { width: 46px; height: 46px; border-radius: 12px; font-size: 1.15rem; }
            .sa-school-text { display: flex; flex-direction: column; line-height: 1.3; }
            .sa-school-name { font-weight: 600; white-space: normal; min-width: 170px; }
            .sa-school-sub { font-size: .74rem; color: var(--sa-muted); }

            .sa-chip { display: inline-block; background: #f1f4f8; border: 1px solid #e4e9f0; border-radius: 6px; padding: .12rem .45rem; font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-size: .75rem; color: #4a5568; }
            .sa-badge { display: inline-flex; align-items: center; gap: .3rem; border-radius: 999px; padding: .2rem .65rem; font-size: .72rem; font-weight: 600; background: #eef0f3; color: #5c6873; }
            .sa-badge::before { content: ""; width: 6px; height: 6px; border-radius: 50%; background: currentColor; opacity: .7; }
            .sa-badge-mooe { background: #e6f5ec; color: #1e7d44; }
            .sa-badge-prog { background: #e8f1fb; color: #1d6fa5; }
            .sa-badge-prog::before { display: none; }

            .sa-expand { width: 28px; height: 28px; border-radius: 8px; border: 1px solid #e1e6ed; background: #fff; color: var(--sa-muted); display: inline-flex; align-items: center; justify-content: center; transition: all .15s; padding: 0; }
            .sa-expand:hover { color: var(--sa-primary); border-color: #9dbbe3; }
            .shown .sa-expand { background: var(--sa-primary); border-color: var(--sa-primary); color: #fff; transform: rotate(90deg); }
            .sa-icon-btn { width: 32px; height: 32px; border-radius: 9px; display: inline-flex; align-items: center; justify-content: center; font-size: 1.05rem; color: #5c6873; transition: all .15s; }
            .sa-icon-btn:hover { text-decoration: none; }
            .sa-icon-btn.edit:hover { background: var(--sa-soft); color: var(--sa-primary); }
            .sa-icon-btn.del:hover { background: #fdecec; color: #c53030; }

            .sa-child { padding: .2rem .8rem 1rem 3.3rem; background: #f5f9ff; }
            .sa-child .sa-months { grid-template-columns: repeat(12, minmax(78px, 1fr)); }

            /* Month tiles (table detail + modals) */
            .sa-months { display: grid; grid-template-columns: repeat(4, 1fr); gap: .45rem; }
            .sa-m { background: #fff; border: 1px solid #e6ebf2; border-radius: 9px; padding: .4rem .5rem; text-align: center; }
            .sa-m span { display: block; font-size: .62rem; font-weight: 700; letter-spacing: .06em; color: var(--sa-muted); text-transform: uppercase; }
            .sa-m b { display: block; font-size: .8rem; font-variant-numeric: tabular-nums; color: var(--sa-text); }
            .sa-m.is-zero b { color: #b3bcc8; font-weight: 600; }

            /* Loading skeleton (hidden once DataTables draws) */
            .sa-loading { padding: 1rem .4rem; }
            .sa-skel { height: 44px; border-radius: 10px; margin-bottom: .55rem; background: linear-gradient(90deg, #f1f4f8 25%, #f8fafc 50%, #f1f4f8 75%); background-size: 200% 100%; animation: saShimmer 1.2s infinite linear; }
            @keyframes saShimmer { from { background-position: 200% 0; } to { background-position: -200% 0; } }
            .sa-table-wrap.is-loading table { display: none; }

            /* DataTables footer */
            .sa-card .dataTables_wrapper .sa-foot { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: .6rem; padding: .9rem .8rem; }
            .sa-card .dataTables_length select { height: 34px; border-radius: 8px; border: 1px solid #e1e6ed; background: var(--sa-bg); margin: 0 .3rem; }
            .sa-card .dataTables_length label, .sa-card .dataTables_info { font-size: .8rem; color: var(--sa-muted); margin: 0; padding: 0 !important; }
            .sa-card .pagination { margin: 0 !important; gap: .25rem; }
            .sa-card .page-link { border: 0; border-radius: 8px !important; color: #4a5568; font-size: .8rem; min-width: 32px; text-align: center; }
            .sa-card .page-item.active .page-link { background: var(--sa-primary); color: #fff; }
            .sa-card .page-item.disabled .page-link { color: #c3cbd5; }
            .sa-card table.dataTable thead .sorting:before, .sa-card table.dataTable thead .sorting:after,
            .sa-card table.dataTable thead .sorting_asc:before, .sa-card table.dataTable thead .sorting_asc:after,
            .sa-card table.dataTable thead .sorting_desc:before, .sa-card table.dataTable thead .sorting_desc:after { bottom: .85em; }
            .sa-empty-row { text-align: center; padding: 2.5rem 1rem !important; color: var(--sa-muted); }

            /* ---- Modals ---- */
            .sa-modal .modal-content { border: 0; border-radius: 18px; overflow: hidden; box-shadow: 0 24px 60px rgba(16, 30, 54, .25); }
            .sa-modal.fade .modal-dialog { transform: translateY(14px) scale(.98); transition: transform .22s ease-out; }
            .sa-modal.show .modal-dialog { transform: none; }
            .sa-dialog-md { max-width: 560px; }
            .sa-modal .modal-header { align-items: center; border-bottom: 1px solid var(--sa-line); padding: 1.1rem 1.4rem; background: #fff; }
            .sa-modal .modal-header .close { margin: -1rem -.5rem -1rem auto; font-size: 1.6rem; color: var(--sa-muted); opacity: 1; }
            .sa-mh { display: flex; align-items: center; gap: .85rem; }
            .sa-mh-icon { width: 46px; height: 46px; border-radius: 12px; background: var(--sa-soft); color: var(--sa-primary); display: inline-flex; align-items: center; justify-content: center; font-size: 1.4rem; }
            .sa-modal .modal-title { font-size: 1.05rem; font-weight: 700; color: var(--sa-text); margin: 0; }
            .sa-mh p { margin: .15rem 0 0; font-size: .8rem; color: var(--sa-muted); }
            .sa-mh-meta { display: flex; flex-wrap: wrap; gap: .35rem; margin-top: .3rem; }
            .sa-modal .modal-body { padding: 1.3rem 1.4rem; }
            .sa-modal .modal-footer { border-top: 1px solid var(--sa-line); background: var(--sa-bg); padding: .9rem 1.4rem; }
            .sa-modal .modal-footer > * { margin: 0 0 0 .5rem; }
            .sa-modal label { font-size: .75rem; font-weight: 600; color: #4a5568; margin-bottom: .35rem; }
            .sa-modal .form-control { height: 42px; border-radius: 10px; border-color: #e1e6ed; font-size: .88rem; }
            .sa-modal .form-control:focus { border-color: #9dbbe3; box-shadow: 0 0 0 3px rgba(44, 82, 130, .12); }
            .sa-req { color: #c53030; }
            .sa-step { display: flex; align-items: center; gap: .5rem; font-size: .72rem; font-weight: 700; letter-spacing: .07em; text-transform: uppercase; color: var(--sa-primary); margin: .2rem 0 .8rem; }
            .sa-step span { width: 20px; height: 20px; border-radius: 50%; background: var(--sa-primary); color: #fff; display: inline-flex; align-items: center; justify-content: center; font-size: .68rem; }
            .sa-step:not(:first-child) { margin-top: 1.1rem; padding-top: 1rem; border-top: 1px dashed var(--sa-line); }

            .sa-money { position: relative; }
            .sa-money span { position: absolute; left: .9rem; top: 50%; transform: translateY(-50%); font-weight: 700; color: var(--sa-muted); font-size: 1.1rem; }
            .sa-modal .sa-money .form-control { height: 52px; padding-left: 2.1rem; font-size: 1.3rem; font-weight: 700; letter-spacing: .01em; font-variant-numeric: tabular-nums; }

            .sa-seg { display: flex; gap: .4rem; background: var(--sa-bg); border: 1px solid #e1e6ed; border-radius: 12px; padding: .3rem; }
            .sa-seg label { flex: 1; margin: 0; position: relative; }
            .sa-seg input { position: absolute; opacity: 0; width: 100%; height: 100%; left: 0; top: 0; margin: 0; cursor: pointer; }
            .sa-seg span { display: block; text-align: center; padding: .5rem .4rem; border-radius: 9px; font-size: .82rem; font-weight: 600; color: #5c6873; transition: all .15s; }
            .sa-seg input:checked + span { background: #fff; color: var(--sa-primary); box-shadow: 0 1px 4px rgba(16, 30, 54, .12); }
            .sa-seg input:focus-visible + span { box-shadow: 0 0 0 3px rgba(44, 82, 130, .2); }

            .sa-summary-panel { height: 100%; background: linear-gradient(180deg, #f5f9ff 0%, #fbfcfe 100%); border: 1px solid #e3ebf6; border-radius: 14px; padding: 1.1rem; }
            .sa-sp-label { font-size: .68rem; font-weight: 700; text-transform: uppercase; letter-spacing: .07em; color: var(--sa-muted); margin-bottom: .45rem; }
            .sa-sp-total { font-size: 1.9rem; font-weight: 700; color: var(--sa-text); letter-spacing: -.01em; font-variant-numeric: tabular-nums; line-height: 1.1; }
            .sa-sp-meta { display: flex; flex-wrap: wrap; gap: .35rem; margin-top: .55rem; font-size: .8rem; }
            .sa-sp-note { font-size: .75rem; color: var(--sa-muted); margin-top: .6rem; }

            .sa-compare { display: flex; align-items: center; gap: 1rem; background: var(--sa-bg); border: 1px solid var(--sa-line); border-radius: 12px; padding: .8rem 1rem; margin-bottom: 1.1rem; }
            .sa-compare > i { color: var(--sa-muted); font-size: 1.2rem; }
            .sa-compare-val { font-size: 1.1rem; font-weight: 700; font-variant-numeric: tabular-nums; color: var(--sa-text); }
            .sa-delta { margin-left: auto; font-size: .78rem; font-weight: 700; border-radius: 999px; padding: .2rem .6rem; }
            .sa-delta.up { background: #e6f5ec; color: #1e7d44; }
            .sa-delta.down { background: #fdecec; color: #b42323; }
            .sa-delta.same { background: #eef0f3; color: #5c6873; }

            /* Select2 inside the modals */
            .sa-modal .select2-container--default .select2-selection--single { height: 42px; border-radius: 10px; border-color: #e1e6ed; }
            .sa-modal .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 40px; padding-left: .8rem; font-size: .88rem; }
            .sa-modal .select2-container--default .select2-selection--single .select2-selection__arrow { height: 40px; }
            .sa-modal .select2-dropdown { border-color: #e1e6ed; border-radius: 10px; overflow: hidden; box-shadow: 0 10px 24px rgba(16, 30, 54, .12); }

            @media (max-width: 991px) {
                .sa-summary-panel { margin-top: 1rem; }
            }
            @media (max-width: 767px) {
                .sa-summary { margin-left: 0; width: 100%; }
                .sa-select { flex: 1; }
                .sa-child { padding-left: .8rem; }
                .sa-child .sa-months { grid-template-columns: repeat(4, 1fr); }
            }
            </style>

            <script>
                $(document).ready(function() {
                    var ROWS = <?= $sa_json; ?>;
                    var BASE = '<?= base_url(); ?>';
                    var MONTHS = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

                    function esc(s) {
                        return String(s == null ? '' : s).replace(/[&<>"']/g, function(c) {
                            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
                        });
                    }
                    function toNumber(v) { return parseFloat(String(v || '').replace(/[^\d.]/g, '')) || 0; }
                    function money(n) { return (Number(n) || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }
                    function isMooe(t) { return String(t || '').trim().toUpperCase() === 'MOOE'; }

                    // Mirrors SGODModel::monthly_split(): 11 equal centavo shares, December takes the remainder.
                    function split(amount) {
                        var m = Math.round(amount / 12 * 100) / 100, out = [];
                        for (var i = 0; i < 11; i++) { out.push(m); }
                        out.push(Math.round((amount - 11 * m) * 100) / 100);
                        return out;
                    }
                    function monthTiles(values) {
                        var html = '';
                        for (var i = 0; i < 12; i++) {
                            var v = Number(values[i]) || 0;
                            html += '<div class="sa-m' + (v === 0 ? ' is-zero' : '') + '"><span>' + MONTHS[i] + '</span><b>' + money(v) + '</b></div>';
                        }
                        return html;
                    }

                    // ---------- Table ----------
                    var $wrap = $('.sa-table-wrap').addClass('is-loading');
                    var table = $('#sa-table').DataTable({
                        data: ROWS,
                        deferRender: true,
                        autoWidth: false,
                        pageLength: 25,
                        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                        order: [[1, 'asc']],
                        dom: 't<"sa-foot"lip>',
                        columns: [
                            { data: null, orderable: false, searchable: false, width: '36px',
                              render: function() { return '<button type="button" class="sa-expand" title="Monthly breakdown"><i class="mdi mdi-chevron-right"></i></button>'; } },
                            { data: 'n', render: function(d, type, r) {
                                if (type === 'filter') { return r.n + ' ' + r.s + ' ' + r.d; }
                                if (type !== 'display') { return r.n; }
                                return '<div class="sa-school"><span class="sa-avatar">' + esc(r.n.trim().charAt(0).toUpperCase()) + '</span>' +
                                    '<span class="sa-school-text"><span class="sa-school-name">' + esc(r.n) + '</span>' +
                                    '<span class="sa-school-sub">' + esc(r.s) + (r.d ? ' &middot; ' + esc(r.d) : '') + '</span></span></div>';
                            } },
                            { data: 'y' },
                            { data: 'b', render: function(d, type) { return type === 'display' ? '<span class="sa-chip">' + esc(d) + '</span>' : d; } },
                            { data: 't', render: function(d, type) { return type === 'display' ? '<span class="sa-badge' + (isMooe(d) ? ' sa-badge-mooe' : '') + '">' + esc(d) + '</span>' : d; } },
                            { data: 'p', render: function(d, type) { return type === 'display' ? '<span class="sa-badge sa-badge-prog">' + esc(d) + '</span>' : d; } },
                            { data: 'a', className: 'sa-num', searchable: false,
                              render: function(d, type) { return type === 'display' ? '<span class="sa-amt">&#8369;' + money(d) + '</span>' : d; } },
                            { data: null, className: 'sa-num', searchable: false,
                              render: function(d, type, r) {
                                  var v = Number(r.mo[0]) || 0;
                                  if (type !== 'display') { return v; }
                                  return v === 0 ? '<span class="sa-zero">0.00</span>' : '<span class="sa-per">' + money(v) + ' <small>/mo</small></span>';
                              } },
                            { data: null, orderable: false, searchable: false, className: 'text-right',
                              render: function(d, type, r) {
                                  return '<a data-toggle="modal" href="#alloc" class="open-AddBookDialog sa-icon-btn edit" title="Edit amount"' +
                                      ' data-id="' + r.id + '" data-item="' + r.a + '" data-school="' + esc(r.n) + '" data-sid="' + esc(r.s) + '"' +
                                      ' data-batch="' + esc(r.b) + '" data-type="' + esc(r.t) + '" data-year="' + esc(r.y) + '" data-program="' + esc(r.p) + '">' +
                                      '<i class="mdi mdi-pencil-outline"></i></a>' +
                                      '<a href="' + BASE + 'Page/allocation_delete/' + r.id + '" class="sa-icon-btn del" title="Delete"' +
                                      ' onclick="return confirm(\'Delete this allocation for ' + esc(String(r.n).replace(/['"\\]/g, '')) + ' (batch ' + esc(String(r.b).replace(/['"\\]/g, '')) + ')? This cannot be undone.\')">' +
                                      '<i class="mdi mdi-trash-can-outline"></i></a>';
                              } }
                        ],
                        language: {
                            lengthMenu: 'Show _MENU_',
                            info: '_START_–_END_ of _TOTAL_',
                            infoEmpty: '0 allocations',
                            infoFiltered: '',
                            emptyTable: '<div class="sa-empty-row"><i class="mdi mdi-cash-remove d-block" style="font-size:2rem;opacity:.5"></i>No allocations yet. Use <b>Add Allocation</b> to create one.</div>',
                            zeroRecords: '<div class="sa-empty-row">No allocations match your filters.</div>',
                            paginate: { previous: '<i class="mdi mdi-chevron-left"></i>', next: '<i class="mdi mdi-chevron-right"></i>' }
                        },
                        initComplete: function() {
                            $wrap.removeClass('is-loading');
                            $('#sa_loading').remove();
                        }
                    });

                    // Summary of whatever the filters currently match
                    function updateSummary() {
                        var rows = table.rows({ search: 'applied' }).data(), sum = 0, schools = {};
                        for (var i = 0; i < rows.length; i++) { sum += rows[i].a; schools[rows[i].s] = 1; }
                        $('#sa_summary').html('<b>' + rows.length.toLocaleString() + '</b> allocations &middot; <b>' +
                            Object.keys(schools).length.toLocaleString() + '</b> schools &middot; <b>&#8369;' + money(sum) + '</b>');
                    }
                    table.on('draw', updateSummary);
                    updateSummary();

                    function reEsc(s) { return String(s).replace(/[.*+?^${}()|[\]\\]/g, '\\$&'); }
                    var searchTimer;
                    $('#sa_search').on('input', function() {
                        var v = this.value;
                        clearTimeout(searchTimer);
                        searchTimer = setTimeout(function() { table.search(v).draw(); }, 180);
                    });
                    $('#sa_f_year').on('change', function() { table.column(2).search(this.value ? '^' + reEsc(this.value) + '$' : '', true, false).draw(); });
                    $('#sa_f_type').on('change', function() { table.column(4).search(this.value ? '^\\s*' + reEsc(this.value) + '\\s*$' : '', true, false).draw(); });

                    // Monthly breakdown row
                    $('#sa-table tbody').on('click', '.sa-expand', function() {
                        var tr = $(this).closest('tr'), row = table.row(tr);
                        if (row.child.isShown()) {
                            row.child.hide();
                            tr.removeClass('shown');
                        } else {
                            var r = row.data(), total = 0;
                            for (var i = 0; i < 12; i++) { total += Number(r.mo[i]) || 0; }
                            row.child('<div class="sa-child"><div class="sa-sp-label d-flex justify-content-between"><span>Monthly distribution</span>' +
                                '<span>Sum &#8369;' + money(total) + '</span></div><div class="sa-months">' + monthTiles(r.mo) + '</div>' +
                                (isMooe(r.t) ? '' : '<div class="sa-sp-note">Only MOOE allocations are distributed monthly.</div>') + '</div>').show();
                            tr.addClass('shown');
                        }
                    });

                    // ---------- Modals ----------
                    // Select2 inside a modal needs the modal as dropdown parent so the search box can take focus.
                    if ($.fn.select2) {
                        $('#add select[data-toggle="select2"]').each(function() {
                            var $s = $(this);
                            if ($s.data('select2')) { $s.select2('destroy'); }
                            $s.select2({ dropdownParent: $('#add'), width: '100%', placeholder: $s.data('placeholder') || '', allowClear: $s.attr('name') === 'program' });
                        });
                    }

                    // Live thousand-separator formatting for both amount fields
                    function formatAmount(el) {
                        var caretEnd = el.selectionEnd;
                        var oldLen = el.value.length;
                        var raw = el.value.replace(/[^\d.]/g, "");
                        var parts = raw.split(".");
                        var intPart = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                        var decPart = parts.length > 1 ? "." + parts[1].slice(0, 2) : "";
                        el.value = intPart + decPart;
                        var newLen = el.value.length;
                        el.selectionEnd = Math.max(0, caretEnd + (newLen - oldLen));
                    }

                    function distribution(amount, mooe) {
                        return mooe ? split(amount) : [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                    }

                    function refreshAdd() {
                        var amount = toNumber($('#alloc_amount').val());
                        var type = $('#sa_add_type').val() || '';
                        var group = $('#add input[name="group"]:checked').val() || '';
                        var fy = $('#sa_add_fy').val() || '';
                        var school = $('#add select[name="schoolID"] option:selected').text() || '';
                        var mooe = isMooe(type);

                        $('#sa_add_total').text(money(amount));
                        var meta = [];
                        if (school.trim()) { meta.push('<span class="sa-badge sa-badge-prog">' + esc(school.replace(/^\s*\S+\s-\s/, '')) + '</span>'); }
                        if (fy) { meta.push('<span class="sa-chip">FY ' + esc(fy) + '</span>'); }
                        if (type) { meta.push('<span class="sa-badge' + (mooe ? ' sa-badge-mooe' : '') + '">' + esc(type) + '</span>'); }
                        if (group) { meta.push('<span class="sa-badge">' + esc(group) + '</span>'); }
                        $('#sa_add_meta').html(meta.length ? meta.join('') : '<span class="sa-muted">Fill in the form to see the summary.</span>');
                        $('#sa_add_months').html(monthTiles(distribution(amount, mooe)));
                        $('#sa_add_note').text(!type ? 'Choose an allocation type to see the distribution.' :
                            (mooe ? 'Spread evenly in centavos; December absorbs the rounding.' : 'Only MOOE allocations are distributed monthly.'));
                    }

                    var editIsMooe = true, editOld = 0;
                    function refreshEdit() {
                        var amount = toNumber($('#item').val()), diff = Math.round((amount - editOld) * 100) / 100;
                        $('#sa_edit_new').html('&#8369;' + money(amount));
                        $('#sa_edit_delta')
                            .removeClass('up down same')
                            .addClass(diff > 0 ? 'up' : (diff < 0 ? 'down' : 'same'))
                            .text(diff === 0 ? 'No change' : (diff > 0 ? '+' : '−') + money(Math.abs(diff)));
                        $('#sa_edit_months').html(monthTiles(distribution(amount, editIsMooe)));
                        $('#sa_edit_note').text(editIsMooe ? 'Spread evenly in centavos; December absorbs the rounding.' : 'Only MOOE allocations are distributed monthly.');
                    }

                    $("#alloc_amount").on("input", function() { formatAmount(this); refreshAdd(); });
                    $("#item").on("input", function() { formatAmount(this); refreshEdit(); });
                    $('#sa_add_type, #sa_add_fy, #add select[name="schoolID"], #add input[name="group"]').on('change', refreshAdd);
                    $('#add').on('show.bs.modal', refreshAdd);
                    $('#add').on('shown.bs.modal', function() { $('#alloc_amount').trigger('focus'); });

                    $(document).on('click', 'a[href="#alloc"]', function() {
                        var $a = $(this);
                        editIsMooe = isMooe($a.data('type'));
                        editOld = Number($a.data('item')) || 0;
                        $('#sa_edit_school').text($a.data('school'));
                        $('#sa_edit_avatar').text(String($a.data('school') || 'S').trim().charAt(0).toUpperCase());
                        $('#sa_edit_old').html('&#8369;' + money(editOld));
                        $('#sa_edit_meta').html(
                            '<span class="sa-chip">' + esc($a.data('sid')) + '</span>' +
                            '<span class="sa-chip">#' + esc($a.data('batch')) + '</span>' +
                            '<span class="sa-chip">FY ' + esc($a.data('year')) + '</span>' +
                            '<span class="sa-badge' + (editIsMooe ? ' sa-badge-mooe' : '') + '">' + esc($a.data('type')) + '</span>' +
                            '<span class="sa-badge sa-badge-prog">' + esc($a.data('program')) + '</span>'
                        );
                    });
                    $('#alloc').on('shown.bs.modal', function() {
                        var el = document.getElementById('item');
                        formatAmount(el);
                        refreshEdit();
                        el.focus();
                        el.select();
                    });

                    // strip commas before submit so the server receives a plain number
                    $("#alloc_amount, #item").closest("form").on("submit", function() {
                        $(this).find("#alloc_amount, #item").each(function() {
                            this.value = this.value.replace(/,/g, "");
                        });
                        $(this).find('button[type="submit"]').prop('disabled', true).css('opacity', .7)
                            .find('i').attr('class', 'mdi mdi-loading mdi-spin');
                    });
                });
            </script>
