<?php include('templates/head.php'); ?>
<?php include('templates/header.php'); ?>

<?php
/*
 * Selective IER / RQA - applicant picker of one round.
 * Ticking a row adds that hris_applications.appID to the round; the IER and
 * RQA buttons open the vacancy's own reports with ?batch={id} so only the
 * ticked applicants are printed.
 */
$rs_h = static function ($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};

$jobID     = (int) $job->jobID;
$batchID   = (int) $batch->id;
$jobTypes  = isset($job_types) && is_array($job_types) ? $job_types : array();
$typeLabel = $jobTypes[(int) $job->job_type] ?? '';
$groupName = (int) $job->promotion === 1 ? 'Promotion' : ($groups[(int) $job->position] ?? 'Vacancy');
$dqLabel   = array(0 => 'Pending', 1 => 'Qualified', 2 => 'Disqualified');
$dqClass   = array(0 => 'is-pending', 1 => 'is-qualified', 2 => 'is-dq');
$pickedCount = count($picked);
?>

<style>
    .rs-page { --rs-ink:#132c4a; --rs-muted:#6b7b91; --rs-line:#e5eaf2; --rs-blue:#2457d6; --rs-soft:#f6f9fd; }
    .rs-page .rs-hero { background:linear-gradient(135deg,#ffffff 0%,#f4f8ff 100%); border:1px solid var(--rs-line); border-radius:18px; box-shadow:0 6px 24px rgba(24,52,88,.05); display:flex; flex-wrap:wrap; gap:14px; justify-content:space-between; padding:22px 24px; }
    .rs-page .rs-back { align-items:center; color:#5c7188; display:inline-flex; font-size:12px; font-weight:650; gap:5px; margin-bottom:8px; }
    .rs-page .rs-back:hover { color:var(--rs-blue); text-decoration:none; }
    .rs-page .rs-title { color:var(--rs-ink); font-size:24px; font-weight:800; letter-spacing:-.4px; margin:0; }
    .rs-page .rs-subtitle { color:var(--rs-muted); font-size:13px; margin:6px 0 0; max-width:620px; }
    .rs-page .rs-tagline { align-items:center; display:flex; flex-wrap:wrap; gap:6px; margin-top:8px; }
    .rs-page .rs-chip { border-radius:6px; font-size:9px; font-weight:800; letter-spacing:.06em; padding:4px 8px; text-transform:uppercase; }
    .rs-page .rs-chip-group { background:#eaf0ff; color:#2c55b5; }
    .rs-page .rs-chip-type { background:#e6f5ef; color:#1c7a55; }
    .rs-page .rs-chip-plain { background:#f1f4f9; color:#65758c; }
    .rs-page .rs-hero-stats { display:flex; gap:26px; align-items:center; }
    .rs-page .rs-hero-stat strong { color:var(--rs-ink); display:block; font-size:22px; font-weight:800; line-height:1.1; }
    .rs-page .rs-hero-stat span { color:#8494a8; font-size:10px; font-weight:700; letter-spacing:.06em; text-transform:uppercase; }
    .rs-page .rs-card { background:#fff; border:1px solid var(--rs-line); border-radius:16px; box-shadow:0 4px 18px rgba(31,58,91,.045); margin-top:18px; overflow:hidden; }
    .rs-page .rs-bar { align-items:center; border-bottom:1px solid #eef2f8; display:flex; flex-wrap:wrap; gap:8px; padding:14px 18px; }
    .rs-page .rs-search { position:relative; flex:1 1 230px; max-width:320px; }
    .rs-page .rs-search i { color:#93a0b2; left:12px; position:absolute; top:9px; }
    .rs-page .rs-search input { background:#fff; border:1px solid #d9e1ec; border-radius:9px; font-size:13px; height:36px; padding-left:34px; width:100%; }
    .rs-page .rs-search input:focus { border-color:var(--rs-blue); box-shadow:0 0 0 3px rgba(36,87,214,.1); outline:none; }
    .rs-page .rs-btn { align-items:center; border:1px solid #dbe3ee; border-radius:9px; color:#3d5876; cursor:pointer; display:inline-flex; font-size:13px; font-weight:650; gap:6px; padding:8px 13px; transition:all .14s ease; }
    .rs-page .rs-btn:hover { background:#f2f6fd; border-color:#b9cbe8; color:var(--rs-blue); text-decoration:none; }
    .rs-page .rs-btn-primary { background:var(--rs-blue); border-color:var(--rs-blue); color:#fff; }
    .rs-page .rs-btn-primary:hover { background:#1c48b8; border-color:#1c48b8; color:#fff; }
    .rs-page .rs-btn-sm { font-size:12px; padding:6px 11px; }
    .rs-page .rs-count { color:var(--rs-muted); font-size:12px; margin-left:auto; }
    .rs-page .rs-count strong { color:var(--rs-ink); font-size:14px; }
    .rs-page table.rs-table { margin:0; width:100%; }
    .rs-page .rs-table thead th { background:#fafcff; border-bottom:1px solid #eef2f8; border-top:0; color:#8494a8; font-size:10px; font-weight:800; letter-spacing:.06em; padding:11px 14px; text-transform:uppercase; }
    .rs-page .rs-table td { border-top:1px solid #f1f5fa; color:#3d5876; font-size:13px; padding:11px 14px; vertical-align:middle; }
    .rs-page .rs-table tr.rs-on td { background:#f3f9f5; }
    .rs-page .rs-table tr:hover td { background:#f8fbff; }
    .rs-page .rs-table tr.rs-on:hover td { background:#eef6f1; }
    .rs-page .rs-check { cursor:pointer; height:17px; width:17px; }
    .rs-page .rs-name { color:var(--rs-ink); font-weight:700; }
    .rs-page .rs-sub { color:#93a0b2; font-size:11px; }
    .rs-page .rs-tag { border-radius:20px; font-size:10px; font-weight:800; padding:3px 9px; white-space:nowrap; }
    .rs-page .rs-tag.is-qualified { background:#e9f7ef; color:#1c7a55; }
    .rs-page .rs-tag.is-dq { background:#fdeceb; color:#b8443c; }
    .rs-page .rs-tag.is-pending { background:#f1f4f9; color:#65758c; }
    .rs-page .rs-tag.is-used { background:#fdf3e8; color:#95610b; }
    .rs-page .rs-tag.is-hired { background:#f4f1f8; color:#6b5c8a; }
    .rs-page .rs-pts { color:var(--rs-ink); font-weight:700; text-align:right; }
    .rs-page .rs-foot { align-items:center; background:#fafcff; border-top:1px solid #eef2f8; bottom:0; display:flex; flex-wrap:wrap; gap:10px; padding:13px 18px; position:sticky; z-index:5; }
    .rs-page .rs-dirty { color:#95610b; display:none; font-size:12px; font-weight:650; }
    .rs-page .rs-reports { align-items:center; display:flex; flex-wrap:wrap; gap:7px; margin-left:auto; }
    .rs-page .dropdown-menu { border:1px solid #e3e9f2; border-radius:10px; box-shadow:0 10px 26px rgba(24,52,88,.12); font-size:13px; padding:6px; }
    .rs-page .dropdown-item { border-radius:7px; color:#3d5876; padding:7px 10px; }
    .rs-page .dropdown-item:hover { background:#f2f6fd; color:var(--rs-blue); }
    .rs-page .rs-hint { color:var(--rs-muted); font-size:12px; margin:10px 2px 0; }
    @media (max-width:575px) {
        .rs-page .rs-hero { padding:18px; }
        .rs-page .rs-count { margin-left:0; width:100%; }
        .rs-page .rs-reports { margin-left:0; }
    }
</style>

<div class="content-page rs-page">
    <div class="content">
        <div class="container-fluid">

            <div class="rs-hero">
                <div>
                    <a href="<?= base_url(); ?>Reselection/index/<?= $jobID; ?>" class="rs-back"><i class="mdi mdi-arrow-left"></i> Selection rounds</a>
                    <h2 class="rs-title">Round <?= (int) $batch->round_no; ?> &middot; <?= $rs_h($batch->batch_name); ?></h2>
                    <p class="rs-subtitle">Tick only the applicants included in this round, save, then open the IER or RQA below. Rows already used by another round are marked.</p>
                    <div class="rs-tagline">
                        <span class="rs-chip rs-chip-group"><?= $rs_h($groupName); ?></span>
                        <?php if ($typeLabel !== '') : ?><span class="rs-chip rs-chip-type"><?= $rs_h($typeLabel); ?></span><?php endif; ?>
                        <span class="rs-chip rs-chip-plain"><?= $rs_h($job->jobTitle); ?></span>
                        <span class="rs-chip rs-chip-plain">FY <?= $rs_h($job->sy); ?></span>
                    </div>
                </div>
                <div class="rs-hero-stats">
                    <div class="rs-hero-stat"><strong id="rs-picked"><?= $pickedCount; ?></strong><span>Picked</span></div>
                    <div class="rs-hero-stat"><strong><?= count($rows); ?></strong><span>Applicants</span></div>
                </div>
            </div>

            <div class="rs-card">
                <div class="rs-bar">
                    <div class="rs-search">
                        <i class="mdi mdi-magnify"></i>
                        <input type="search" id="rs-search" placeholder="Search name, code, municipality" autocomplete="off">
                    </div>
                    <button type="button" class="rs-btn rs-btn-sm" id="rs-free"><i class="mdi mdi-account-clock-outline"></i> Not yet included</button>
                    <button type="button" class="rs-btn rs-btn-sm" id="rs-qualified"><i class="mdi mdi-check-decagram-outline"></i> Qualified only</button>
                    <button type="button" class="rs-btn rs-btn-sm" id="rs-none"><i class="mdi mdi-close"></i> Clear</button>
                    <span class="rs-count"><strong id="rs-shown"><?= count($rows); ?></strong> shown</span>
                </div>

                <div class="table-responsive">
                    <table class="rs-table" id="rs-table">
                        <thead>
                            <tr>
                                <th style="width:44px;"><input type="checkbox" class="rs-check" id="rs-head-check" title="Select everything shown"></th>
                                <th style="width:46px;">No.</th>
                                <th>Applicant</th>
                                <th style="width:110px;">Code</th>
                                <th style="width:120px;">Status</th>
                                <th style="width:90px;" class="text-right">Points</th>
                                <th style="width:150px;">Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $n = 1; foreach ($rows as $row) :
                                $appID  = (int) $row->appID;
                                $isOn   = isset($picked[$appID]);
                                $usedBy = $used[$appID] ?? null;
                                $dq     = (int) $row->dq;
                                $name   = trim(strtoupper($row->LastName . ', ' . $row->FirstName . ' ' . $row->MiddleName . ' ' . $row->NameExtn));
                                $search = strtolower($name . ' ' . $row->code . ' ' . $row->resCity . ' ' . $row->appStatus);
                            ?>
                            <tr class="<?= $isOn ? 'rs-on' : ''; ?>"
                                data-search="<?= $rs_h($search); ?>"
                                data-dq="<?= $dq; ?>"
                                data-used="<?= $usedBy ? 1 : 0; ?>"
                                data-hired="<?= (int) $row->hired; ?>">
                                <td><input type="checkbox" class="rs-check rs-pick" value="<?= $appID; ?>" <?= $isOn ? 'checked' : ''; ?>></td>
                                <td class="rs-sub"><?= $n++; ?></td>
                                <td>
                                    <div class="rs-name"><?= $rs_h($name !== ',' ? $name : '(no profile)'); ?></div>
                                    <?php if (!empty($row->resCity)) : ?><div class="rs-sub"><?= $rs_h($row->resCity); ?></div><?php endif; ?>
                                </td>
                                <td><?= $rs_h($row->code); ?></td>
                                <td><span class="rs-tag <?= $dqClass[$dq] ?? 'is-pending'; ?>"><?= $dqLabel[$dq] ?? 'Pending'; ?></span></td>
                                <td class="rs-pts"><?= ($row->total_points !== null) ? number_format((float) $row->total_points, 2) : '&mdash;'; ?></td>
                                <td>
                                    <?php if ((int) $row->hired === 1) : ?><span class="rs-tag is-hired">Hired</span><?php endif; ?>
                                    <?php if ($usedBy) : ?><span class="rs-tag is-used">Round <?= (int) $usedBy->round_no; ?></span><?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="rs-foot">
                    <button type="button" class="rs-btn rs-btn-primary" id="rs-save"><i class="mdi mdi-content-save-outline"></i> Save Selection</button>
                    <span class="rs-count" style="margin-left:0"><strong id="rs-selected"><?= $pickedCount; ?></strong> selected</span>
                    <span class="rs-dirty" id="rs-dirty"><i class="mdi mdi-alert-outline"></i> Unsaved changes</span>
                    <a class="rs-btn rs-leave" href="<?= base_url(); ?>Reselection/index/<?= $jobID; ?>"><i class="mdi mdi-arrow-left"></i> Back to Rounds</a>

                    <div class="rs-reports">
                        <div class="dropdown">
                            <a class="rs-btn dropdown-toggle rs-report" href="#" data-toggle="dropdown" aria-expanded="false"><i class="mdi mdi-file-chart-outline"></i> IER</a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <?php foreach ($links['ier'] as $link) : ?>
                                    <a class="dropdown-item rs-report-link" target="_blank" href="<?= $link['url']; ?>"><?= $link['label']; ?></a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="dropdown">
                            <a class="rs-btn dropdown-toggle rs-report" href="#" data-toggle="dropdown" aria-expanded="false"><i class="mdi mdi-file-document-outline"></i> RQA</a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <?php foreach ($links['rqa'] as $link) : ?>
                                    <a class="dropdown-item rs-report-link" target="_blank" href="<?= $link['url']; ?>"><?= $link['label']; ?></a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <p class="rs-hint">
                <i class="mdi mdi-information-outline"></i>
                The reports keep their own rules (rated, qualified, 50 points and above) - this round only narrows them to the applicants ticked here.
            </p>

        </div>
    </div>

    <?php include('templates/footer.php'); ?>
</div>

<script>
    $(document).ready(function () {

        var batchID = <?= $batchID; ?>;
        var roundsUrl = '<?= base_url(); ?>Reselection/index/<?= $jobID; ?>';
        var dirty = false;

        function visibleRows() {
            return $('#rs-table tbody tr:visible');
        }

        function refresh() {
            var total = $('.rs-pick:checked').length;
            $('#rs-selected').text(total);
            $('#rs-picked').text(total);
            $('#rs-shown').text(visibleRows().length);
        }

        function markDirty() {
            dirty = true;
            $('#rs-dirty').show();
        }

        $(document).on('change', '.rs-pick', function () {
            $(this).closest('tr').toggleClass('rs-on', this.checked);
            markDirty();
            refresh();
        });

        $('#rs-search').on('keyup', function () {
            var q = $.trim($(this).val()).toLowerCase();

            $('#rs-table tbody tr').each(function () {
                var hit = (q === '') || ($(this).attr('data-search') || '').indexOf(q) !== -1;
                $(this).toggle(hit);
            });

            refresh();
        });

        function setShown(filter) {
            visibleRows().each(function () {
                var $row = $(this);
                var keep = filter($row);
                $row.find('.rs-pick').prop('checked', keep);
                $row.toggleClass('rs-on', keep);
            });
            markDirty();
            refresh();
        }

        $('#rs-head-check').on('change', function () {
            var on = this.checked;
            setShown(function () { return on; });
        });

        $('#rs-none').on('click', function () {
            $('.rs-pick').prop('checked', false);
            $('#rs-table tbody tr').removeClass('rs-on');
            $('#rs-head-check').prop('checked', false);
            markDirty();
            refresh();
        });

        // not yet acted on: not carried by another round and not hired
        $('#rs-free').on('click', function () {
            setShown(function ($row) {
                return $row.attr('data-used') === '0' && $row.attr('data-hired') === '0';
            });
        });

        $('#rs-qualified').on('click', function () {
            setShown(function ($row) { return $row.attr('data-dq') === '1'; });
        });

        $('#rs-save').on('click', function () {
            var $btn = $(this);
            var ids = [];

            $('.rs-pick:checked').each(function () { ids.push($(this).val()); });

            $btn.prop('disabled', true);

            $.post('<?= base_url(); ?>Reselection/save', { batch_id: batchID, appIDs: ids }, null, 'json')
                .done(function (res) {
                    if (res && res.status === 'success') {
                        dirty = false;
                        $('#rs-dirty').hide();

                        // saving usually ends the picking, so offer the way back to the
                        // rounds page without forcing it - the list stays editable here
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                title: 'Saved',
                                text: res.message,
                                icon: 'success',
                                showCancelButton: true,
                                confirmButtonText: 'Back to Selection Rounds',
                                cancelButtonText: 'Stay here',
                                confirmButtonColor: '#2457d6'
                            }).then(function (result) {
                                if (result.value) {
                                    window.location.href = roundsUrl;
                                }
                            });
                        } else if (confirm(res.message + '\n\nGo back to the selection rounds?')) {
                            window.location.href = roundsUrl;
                        }
                    } else {
                        alert((res && res.message) || 'The selection could not be saved.');
                    }
                })
                .fail(function () { alert('The selection could not be saved. Please try again.'); })
                .always(function () { $btn.prop('disabled', false); });
        });

        $('.rs-leave, .rs-back').on('click', function (e) {
            if (dirty && !confirm('You have unsaved changes. Leave this round without saving?')) {
                e.preventDefault();
            }
        });

        $('.rs-report-link').on('click', function (e) {
            if (dirty && !confirm('You have unsaved changes. Open the report with the last saved selection?')) {
                e.preventDefault();
            }
        });

        refresh();
    });
</script>
