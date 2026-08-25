<?php
/**
 * Application tracking button + modal.
 *
 * Replaces the small status/rater icons that used to be scattered around the
 * rating page with one button that opens the full activity log for a single
 * application (status changes, validations, ratings encoded, remarks…),
 * newest entry on top.
 *
 * Only HR admin, SDS and ASDS may open it.
 *
 * Expected data:
 *   $trk_app_id       int    hris_applications.appID
 *   $trk_applicant_id mixed  applicant id (for legacy rows without app_id)
 *   $trk_job_id       mixed  hris_jobvacancy.jobID
 *   $trk_applicant    string applicant display name        (optional)
 *   $trk_position     string position applied              (optional)
 *   $trk_status       string current application status    (optional)
 *   $trk_block        string 'button' | 'modal' | 'both'   (default 'both')
 *   $trk_btn_class    string extra classes for the button  (optional)
 */

$CI =& get_instance();

$allowedRoles = ['asds', 'sds', 'Human Resource Admin'];
$sessionRole  = trim((string) $CI->session->position);

$canTrack = false;
foreach ($allowedRoles as $role) {
    if (strcasecmp($sessionRole, $role) === 0) {
        $canTrack = true;
        break;
    }
}

if (!$canTrack) {
    return;
}

$trk_app_id       = isset($trk_app_id) ? (int) $trk_app_id : 0;
$trk_applicant_id = isset($trk_applicant_id) ? $trk_applicant_id : null;
$trk_job_id       = isset($trk_job_id) ? $trk_job_id : null;
$trk_applicant    = isset($trk_applicant) ? (string) $trk_applicant : '';
$trk_position     = isset($trk_position) ? (string) $trk_position : '';
$trk_status       = isset($trk_status) ? (string) $trk_status : '';
$trk_block        = isset($trk_block) ? $trk_block : 'both';
$trk_btn_class    = isset($trk_btn_class) ? (string) $trk_btn_class : '';

$entries = $trk_app_id > 0
    ? $CI->Audit->application_log($trk_app_id, $trk_applicant_id, $trk_job_id)
    : [];

$h = function ($v) {
    return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
};
?>

<?php if ($trk_block === 'button' || $trk_block === 'both'): ?>
<button type="button" class="btn btn-light btn-sm app-track-btn <?= $h($trk_btn_class); ?>"
        data-toggle="modal" data-target="#appTrackingModal">
    <i class="mdi mdi-timeline-text-outline mr-1"></i>Tracking
    <span class="badge badge-pill badge-dark ml-1"><?= count($entries); ?></span>
</button>
<?php endif; ?>

<?php if ($trk_block === 'modal' || $trk_block === 'both'): ?>
<div class="modal fade" id="appTrackingModal" tabindex="-1" role="dialog"
     aria-labelledby="appTrackingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content app-track">

            <div class="modal-header app-track__header">
                <div class="app-track__heading">
                    <h5 class="modal-title" id="appTrackingModalLabel">
                        <i class="mdi mdi-timeline-text-outline mr-1"></i>Application Tracking
                    </h5>
                    <?php if ($trk_applicant !== '' || $trk_position !== ''): ?>
                    <p class="app-track__subtitle">
                        <?php if ($trk_applicant !== ''): ?><span><?= $h($trk_applicant); ?></span><?php endif; ?>
                        <?php if ($trk_position !== ''): ?><span class="app-track__dot">&bull;</span><span><?= $h($trk_position); ?></span><?php endif; ?>
                        <?php if ($trk_app_id > 0): ?><span class="app-track__dot">&bull;</span><span>App&nbsp;#<?= $trk_app_id; ?></span><?php endif; ?>
                    </p>
                    <?php endif; ?>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="app-track__toolbar">
                <?php if ($trk_status !== ''): ?>
                <span class="app-track__status">
                    Current status
                    <strong><?= $h($trk_status); ?></strong>
                </span>
                <?php endif; ?>
                <div class="app-track__search">
                    <i class="mdi mdi-magnify"></i>
                    <input type="text" id="appTrackSearch" class="form-control form-control-sm"
                           placeholder="Search entries, people, remarks…" autocomplete="off">
                </div>
            </div>

            <div class="modal-body app-track__body">
                <?php if (empty($entries)): ?>
                    <div class="app-track__empty">
                        <i class="mdi mdi-timeline-outline"></i>
                        <p>No activity has been logged for this application yet.</p>
                    </div>
                <?php else: ?>
                    <p class="app-track__hint">
                        <i class="mdi mdi-arrow-up"></i>
                        Latest activity first &mdash; the oldest entry is at the bottom.
                    </p>

                    <ol class="app-track__list">
                        <?php $lastDate = null; $i = 0; ?>
                        <?php foreach ($entries as $e): $i++; ?>
                            <?php
                                $dateKey   = $e['ts'] ? date('Y-m-d', $e['ts']) : ($e['date'] ?: '');
                                $dateLabel = $e['ts'] ? date('F j, Y', $e['ts']) : ($e['date'] ?: 'Undated');
                                $timeLabel = $e['ts'] ? date('g:i A', $e['ts']) : trim((string) $e['time']);
                                $detail    = trim((string) $e['detail']);
                                $note      = trim((string) $e['note']);
                                $searchable = strtolower(trim(
                                    $e['label'] . ' ' . $detail . ' ' . $note . ' ' .
                                    $e['actor'] . ' ' . $e['actor_role'] . ' ' . $dateLabel
                                ));
                            ?>
                            <?php if ($dateKey !== $lastDate): $lastDate = $dateKey; ?>
                                <li class="app-track__daymark" data-day="<?= $h($dateKey); ?>">
                                    <span><?= $h($dateLabel); ?></span>
                                </li>
                            <?php endif; ?>

                            <li class="app-track__item app-track__item--<?= $h($e['tone']); ?>"
                                data-day="<?= $h($dateKey); ?>"
                                data-search="<?= $h($searchable); ?>">

                                <span class="app-track__marker">
                                    <i class="mdi <?= $h($e['icon']); ?>"></i>
                                </span>

                                <div class="app-track__card">
                                    <div class="app-track__row">
                                        <span class="app-track__label"><?= $h($e['label']); ?></span>
                                        <?php if ($i === 1): ?>
                                            <span class="app-track__tag app-track__tag--latest">Latest</span>
                                        <?php endif; ?>
                                        <span class="app-track__time"><?= $h($timeLabel); ?></span>
                                    </div>

                                    <?php if ($detail !== ''): ?>
                                        <p class="app-track__detail"><?= nl2br($h($detail)); ?></p>
                                    <?php endif; ?>

                                    <?php if ($note !== ''): ?>
                                        <p class="app-track__note"><i class="mdi mdi-note-outline"></i> <?= nl2br($h($note)); ?></p>
                                    <?php endif; ?>

                                    <div class="app-track__meta">
                                        <?php if (trim((string) $e['actor']) !== ''): ?>
                                            <span class="app-track__actor">
                                                <i class="mdi mdi-account-circle-outline"></i><?= $h($e['actor']); ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="app-track__actor app-track__actor--unknown">
                                                <i class="mdi mdi-account-question-outline"></i>System
                                            </span>
                                        <?php endif; ?>

                                        <!-- <?php if (trim((string) $e['actor_role']) !== ''): ?>
                                            <span class="app-track__role"><?= $h($e['actor_role']); ?></span>
                                        <?php endif; ?> -->

                                        <span class="app-track__source app-track__source--<?= $h($e['source']); ?>">
                                            <?= $e['source'] === 'audit' ? 'Audit trail' : 'Status log'; ?>
                                        </span>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ol>

                    <p class="app-track__noresult" id="appTrackNoResult">No entry matches your search.</p>
                <?php endif; ?>
            </div>

            <div class="modal-footer app-track__footer">
                <span class="app-track__count">
                    <strong><?= count($entries); ?></strong> entr<?= count($entries) === 1 ? 'y' : 'ies'; ?>
                </span>
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>

<style>
.app-track-btn{
    font-weight:600;
    border:1px solid rgba(255,255,255,.55);
    background:rgba(255,255,255,.16);
    color:#fff;
    border-radius:20px;
    padding:.2rem .8rem;
    line-height:1.5;
    transition:background .15s ease,box-shadow .15s ease;
}
.app-track-btn:hover,.app-track-btn:focus{
    background:#fff;
    color:#188ae2;
    box-shadow:0 2px 6px rgba(0,0,0,.18);
}
.app-track-btn .badge{background:rgba(0,0,0,.28);font-weight:600;}
.app-track-btn:hover .badge{background:#188ae2;color:#fff;}

/* text-align / font-weight are pinned because the button that opens this modal
   lives inside a centred, bold table header. */
.app-track{
    border:0;border-radius:10px;overflow:hidden;box-shadow:0 12px 40px rgba(0,0,0,.25);
    text-align:left;font-weight:400;
}
.app-track__header{
    background:linear-gradient(135deg,#2b3d51 0%,#3a5673 100%);
    color:#fff;border-bottom:0;align-items:flex-start;padding:1rem 1.25rem;
}
.app-track__header .close{color:#fff;opacity:.85;text-shadow:none;}
.app-track__header .close:hover{opacity:1;}
/* the theme paints every heading #36404c — force it back to white here */
.app-track__heading .modal-title{
    font-size:1.05rem;font-weight:600;letter-spacing:.2px;color:#fff;margin:0;
}
.app-track__subtitle{margin:.3rem 0 0;font-size:.78rem;color:rgba(255,255,255,.75);}
.app-track__dot{margin:0 .4rem;opacity:.6;}

.app-track__toolbar{
    display:flex;flex-wrap:wrap;align-items:center;gap:.75rem;
    padding:.7rem 1.25rem;background:#f4f6f9;border-bottom:1px solid #e3e8ef;
}
.app-track__status{font-size:.75rem;color:#8a94a6;text-transform:uppercase;letter-spacing:.4px;}
.app-track__status strong{
    display:inline-block;margin-left:.4rem;padding:.15rem .55rem;border-radius:12px;
    background:#e7f1ff;color:#1b62b3;text-transform:none;letter-spacing:0;font-size:.78rem;
}
.app-track__search{position:relative;margin-left:auto;min-width:230px;flex:1 1 230px;max-width:340px;}
.app-track__search i{
    position:absolute;left:.6rem;top:50%;transform:translateY(-50%);
    color:#a3adbd;font-size:1rem;pointer-events:none;
}
.app-track__search .form-control{padding-left:2rem;border-radius:18px;border-color:#dde3ec;background:#fff;}

.app-track__body{padding:1.1rem 1.25rem 1.4rem;background:#fbfcfe;overflow-y:auto;}
.app-track__hint{
    margin:0 0 1rem;font-size:.73rem;color:#96a0b0;
    display:flex;align-items:center;gap:.25rem;
}
.app-track__empty{text-align:center;padding:2.5rem 1rem;color:#9aa4b4;}
.app-track__empty i{font-size:2.6rem;display:block;margin-bottom:.5rem;opacity:.5;}
.app-track__empty p{margin:0;font-size:.88rem;}
.app-track__noresult{display:none;text-align:center;color:#9aa4b4;font-size:.85rem;padding:1.5rem 0 .5rem;margin:0;}

.app-track__list{list-style:none;margin:0;padding:0 0 0 1.65rem;position:relative;}
.app-track__list:before{
    content:"";position:absolute;left:.62rem;top:.35rem;bottom:.35rem;width:2px;
    background:linear-gradient(180deg,#cfd8e6 0%,#e6ebf3 100%);border-radius:2px;
}
.app-track__daymark{
    position:relative;margin:.15rem 0 .65rem -1.65rem;padding-left:1.65rem;
}
.app-track__daymark span{
    display:inline-block;background:#eef1f6;color:#6b7688;
    font-size:.68rem;font-weight:700;letter-spacing:.7px;text-transform:uppercase;
    padding:.2rem .6rem;border-radius:11px;
}
.app-track__item{position:relative;margin-bottom:.7rem;}
.app-track__marker{
    position:absolute;left:-1.65rem;top:.55rem;width:1.25rem;height:1.25rem;border-radius:50%;
    display:flex;align-items:center;justify-content:center;
    background:#fff;border:2px solid #cfd8e6;color:#8a94a6;
}
.app-track__marker i{font-size:.72rem;line-height:1;}
.app-track__card{
    background:#fff;border:1px solid #e5eaf1;border-left:3px solid #cfd8e6;
    border-radius:6px;padding:.6rem .8rem;
    transition:box-shadow .15s ease,transform .15s ease;
}
.app-track__card:hover{box-shadow:0 4px 14px rgba(43,61,81,.1);transform:translateX(1px);}
.app-track__row{display:flex;align-items:center;flex-wrap:wrap;gap:.45rem;}
.app-track__label{font-weight:600;font-size:.85rem;color:#3b4658;}
.app-track__time{margin-left:auto;font-size:.72rem;color:#9aa4b4;white-space:nowrap;}
.app-track__tag{
    font-size:.6rem;font-weight:700;letter-spacing:.5px;text-transform:uppercase;
    padding:.1rem .4rem;border-radius:9px;
}
.app-track__tag--latest{background:#e6f7ee;color:#1c9c62;}
.app-track__detail{
    margin:.35rem 0 0;font-size:.82rem;color:#5b6678;line-height:1.45;
    font-weight:400;white-space:pre-line;word-break:break-word;
}
.app-track__note{
    margin:.4rem 0 0;font-size:.78rem;color:#7a6a3d;background:#fdf6e3;
    border-radius:4px;padding:.3rem .5rem;word-break:break-word;
}
.app-track__meta{
    display:flex;flex-wrap:wrap;align-items:center;gap:.4rem;
    margin-top:.45rem;font-size:.72rem;color:#8a94a6;
}
.app-track__actor{display:inline-flex;align-items:center;gap:.2rem;font-weight:600;color:#6b7688;}
.app-track__actor--unknown{font-weight:500;font-style:italic;}
.app-track__role,.app-track__source{
    padding:.05rem .4rem;border-radius:9px;font-size:.66rem;
    font-weight:600;text-transform:uppercase;letter-spacing:.3px;
}
.app-track__role{background:#eef1f6;color:#6b7688;}
.app-track__source--audit{background:#eef4ff;color:#3f6ec0;}
.app-track__source--track{background:#f2f0fb;color:#6b5bbd;}

/* Per-event colour accents */
.app-track__item--success .app-track__card{border-left-color:#10c469;}
.app-track__item--success .app-track__marker{border-color:#10c469;color:#10c469;}
.app-track__item--info .app-track__card{border-left-color:#35b8e0;}
.app-track__item--info .app-track__marker{border-color:#35b8e0;color:#35b8e0;}
.app-track__item--primary .app-track__card{border-left-color:#188ae2;}
.app-track__item--primary .app-track__marker{border-color:#188ae2;color:#188ae2;}
.app-track__item--purple .app-track__card{border-left-color:#5b69bc;}
.app-track__item--purple .app-track__marker{border-color:#5b69bc;color:#5b69bc;}
.app-track__item--warning .app-track__card{border-left-color:#f9c851;}
.app-track__item--warning .app-track__marker{border-color:#f9c851;color:#dcab30;}
.app-track__item--danger .app-track__card{border-left-color:#ff5b5b;}
.app-track__item--danger .app-track__marker{border-color:#ff5b5b;color:#ff5b5b;}

.app-track__footer{
    background:#f4f6f9;border-top:1px solid #e3e8ef;padding:.6rem 1.25rem;
    justify-content:space-between;
}
.app-track__count{font-size:.76rem;color:#8a94a6;}
.app-track__count strong{color:#3b4658;}

@media (max-width:575.98px){
    .app-track__toolbar{flex-direction:column;align-items:stretch;}
    .app-track__search{margin-left:0;max-width:none;}
    .app-track__time{margin-left:0;width:100%;}
}
</style>

<script>
(function () {
    var modal = document.getElementById('appTrackingModal');
    if (!modal || modal.dataset.trackReady === '1') { return; }
    modal.dataset.trackReady = '1';

    var search   = modal.querySelector('#appTrackSearch');
    var noResult = modal.querySelector('#appTrackNoResult');
    if (!search) { return; }

    var items = Array.prototype.slice.call(modal.querySelectorAll('.app-track__item'));
    var days  = Array.prototype.slice.call(modal.querySelectorAll('.app-track__daymark'));

    function filter() {
        var q = search.value.trim().toLowerCase();
        var shown = 0;

        items.forEach(function (item) {
            var hit = q === '' || (item.getAttribute('data-search') || '').indexOf(q) !== -1;
            item.style.display = hit ? '' : 'none';
            if (hit) { shown++; }
        });

        // Hide a date heading when everything under it is filtered out.
        days.forEach(function (day) {
            var key = day.getAttribute('data-day');
            var visible = items.some(function (item) {
                return item.getAttribute('data-day') === key && item.style.display !== 'none';
            });
            day.style.display = visible ? '' : 'none';
        });

        if (noResult) { noResult.style.display = shown === 0 ? 'block' : 'none'; }
    }

    search.addEventListener('input', filter);

    $(modal).on('shown.bs.modal', function () { search.focus(); });
    $(modal).on('hidden.bs.modal', function () { search.value = ''; filter(); });
})();
</script>
<?php endif; ?>
