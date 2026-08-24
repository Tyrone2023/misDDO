<?php
$fe_h = static function ($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};

$encoders = $encoders ?? [];
$vacancies = $vacancies ?? [];
$accessMap = $accessMap ?? [];
$reopen = (string) ($reopen ?? '');
$successMessage = $this->session->flashdata('success');
$dangerMessage = $this->session->flashdata('danger');

$fe_modes = ['written' => 'Written', 'interview' => 'Interview', 'both' => 'Both'];

/**
 * The vacancy scope picker, shared by the Add and Edit modals.
 * $prefix keeps ids unique; $current is [job_id => encode_mode].
 * Radio groups are scoped to their own <form>, so the same field names can
 * repeat across modals without the groups bleeding into each other.
 */
$fe_scope_picker = static function (string $prefix, array $current) use ($vacancies, $fe_h, $fe_modes) {
    ?>
    <div class="fe-scope-picker" data-picker>
        <div class="fe-picker-bar">
            <div class="fe-picker-search">
                <i class="mdi mdi-magnify"></i>
                <input type="search" class="form-control" placeholder="Search vacancy" data-picker-search autocomplete="off">
            </div>
            <div class="fe-picker-tools">
                <button type="button" class="fe-mini" data-picker-all>Select all</button>
                <button type="button" class="fe-mini" data-picker-none>Clear</button>
                <span class="fe-mini-sep"></span>
                <span class="fe-mini-label">Set all to</span>
                <?php foreach ($fe_modes as $modeValue => $modeLabel) : ?>
                    <button type="button" class="fe-mini" data-picker-mode="<?= $modeValue; ?>"><?= $modeLabel; ?></button>
                <?php endforeach; ?>
            </div>
        </div>

        <?php if (empty($vacancies)) : ?>
            <div class="fe-picker-empty">
                <i class="mdi mdi-briefcase-remove-outline"></i>
                <div>No open score-eligible vacancy is assigned to your Secretariat account yet.</div>
            </div>
        <?php else : ?>
            <div class="fe-picker-list">
                <?php foreach ($vacancies as $vacancy) :
                    $jobId = (int) $vacancy->jobID;
                    $checked = array_key_exists($jobId, $current);
                    $mode = $checked ? $current[$jobId] : 'both';
                    $rowId = $prefix . '-job-' . $jobId;
                ?>
                <div class="fe-picker-row <?= $checked ? 'is-on' : ''; ?>"
                     data-picker-row
                     data-search="<?= $fe_h(strtolower($vacancy->jobTitle . ' ' . $vacancy->itemNo . ' ' . $vacancy->department . ' ' . $vacancy->sy)); ?>">
                    <label class="fe-picker-check" for="<?= $rowId; ?>">
                        <input type="checkbox" id="<?= $rowId; ?>" name="access[]" value="<?= $jobId; ?>" <?= $checked ? 'checked' : ''; ?> data-picker-toggle>
                        <span class="fe-picker-box"><i class="mdi mdi-check"></i></span>
                        <span class="fe-picker-text">
                            <span class="fe-picker-title"><?= $fe_h($vacancy->jobTitle); ?></span>
                            <span class="fe-picker-meta">
                                Job #<?= $jobId; ?>
                                <?= $vacancy->itemNo ? ' &middot; Item ' . $fe_h($vacancy->itemNo) : ''; ?>
                                <?= $vacancy->sy ? ' &middot; FY ' . $fe_h($vacancy->sy) : ''; ?>
                                <?= isset($vacancy->applicant_total) ? ' &middot; ' . (int) $vacancy->applicant_total . ' applicant(s)' : ''; ?>
                            </span>
                        </span>
                    </label>
                    <div class="fe-seg" data-picker-seg>
                        <?php foreach ($fe_modes as $modeValue => $modeLabel) :
                            $modeId = $rowId . '-' . $modeValue;
                        ?>
                            <input type="radio" id="<?= $modeId; ?>" name="mode[<?= $jobId; ?>]" value="<?= $modeValue; ?>" <?= $mode === $modeValue ? 'checked' : ''; ?> <?= $checked ? '' : 'disabled'; ?>>
                            <label for="<?= $modeId; ?>"><?= $modeLabel; ?></label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="fe-picker-foot">
                <span><b data-picker-count>0</b> of <?= count($vacancies); ?> vacancy(ies) tagged</span>
                <span class="fe-picker-hint">Untagged vacancies stay invisible to this account.</span>
            </div>
        <?php endif; ?>
    </div>
    <?php
};
?>

<style>
    .fe-workspace { --fe-ink:#132c4a; --fe-muted:#6b7b91; --fe-line:#e5eaf2; --fe-blue:#2457d6; --fe-soft:#f6f9fd; }
    .fe-workspace .fe-hero { align-items:center; background:linear-gradient(135deg,#ffffff 0%,#f4f8ff 100%); border:1px solid var(--fe-line); border-radius:16px; box-shadow:0 5px 20px rgba(24,52,88,.05); display:flex; flex-wrap:wrap; gap:12px; justify-content:space-between; margin-bottom:14px; padding:18px 22px; }
    .fe-workspace .fe-eyebrow { color:#7b8ca3; font-size:10px; font-weight:800; letter-spacing:.12em; text-transform:uppercase; }
    .fe-workspace .fe-title { color:var(--fe-ink); font-size:23px; font-weight:800; letter-spacing:-.3px; margin:4px 0 0; }
    .fe-workspace .fe-subtitle { color:var(--fe-muted); font-size:12.5px; margin:4px 0 0; max-width:640px; }
    .fe-workspace .fe-hero-side { align-items:center; display:flex; flex-wrap:wrap; gap:8px; }
    .fe-workspace .fe-back { align-items:center; border:1px solid #dbe3ee; border-radius:9px; color:#3d5876; display:inline-flex; font-size:12px; font-weight:650; gap:5px; padding:9px 14px; transition:all .14s ease; }
    .fe-workspace .fe-back:hover { background:#f2f6fd; border-color:#b9cbe8; color:var(--fe-blue); text-decoration:none; }
    .fe-workspace .fe-add { align-items:center; background:var(--fe-blue); border:1px solid var(--fe-blue); border-radius:9px; color:#fff; display:inline-flex; font-size:12.5px; font-weight:700; gap:6px; padding:9px 16px; transition:all .14s ease; }
    .fe-workspace .fe-add:hover { background:#1c47b4; border-color:#1c47b4; box-shadow:0 4px 12px rgba(36,87,214,.25); color:#fff; }

    .fe-workspace .fe-stats { display:grid; gap:12px; grid-template-columns:repeat(auto-fit,minmax(190px,1fr)); margin-bottom:14px; }
    .fe-workspace .fe-stat { background:#fff; border:1px solid var(--fe-line); border-radius:14px; box-shadow:0 3px 14px rgba(31,58,91,.04); padding:14px 16px; }
    .fe-workspace .fe-stat-label { color:#7b8ca3; font-size:10px; font-weight:800; letter-spacing:.08em; text-transform:uppercase; }
    .fe-workspace .fe-stat-value { color:var(--fe-ink); font-size:24px; font-weight:800; line-height:1.15; margin-top:4px; }
    .fe-workspace .fe-stat-note { color:var(--fe-muted); font-size:11px; margin-top:2px; }

    .fe-workspace .fe-card { background:#fff; border:1px solid var(--fe-line); border-radius:16px; box-shadow:0 4px 18px rgba(31,58,91,.045); margin-bottom:16px; overflow:hidden; }
    .fe-workspace .fe-card-head { align-items:center; background:#fbfcfe; border-bottom:1px solid var(--fe-line); display:flex; flex-wrap:wrap; gap:10px; justify-content:space-between; padding:14px 18px; }
    .fe-workspace .fe-card-title { color:var(--fe-ink); font-size:14px; font-weight:800; margin:0; }
    .fe-workspace .fe-card-note { color:var(--fe-muted); font-size:11.5px; margin:2px 0 0; }
    .fe-workspace .fe-card-search { position:relative; }
    .fe-workspace .fe-card-search i { color:#93a0b2; left:11px; position:absolute; top:9px; }
    .fe-workspace .fe-card-search input { border:1px solid #d9e1ec; border-radius:9px; font-size:12.5px; height:36px; min-width:230px; padding-left:32px; }
    .fe-workspace .fe-card-search input:focus { border-color:var(--fe-blue); box-shadow:0 0 0 3px rgba(36,87,214,.1); outline:none; }

    .fe-workspace .fe-table { margin:0; min-width:760px; }
    .fe-workspace .fe-table thead th { background:#f4f7fb; border-bottom:1px solid #dfe6f0; border-top:0; color:#6c7f96; font-size:10px; font-weight:800; letter-spacing:.06em; padding:11px 14px; text-transform:uppercase; }
    .fe-workspace .fe-table td { border-color:#eef2f8; padding:12px 14px; vertical-align:middle; }
    .fe-workspace .fe-table tbody tr { transition:background .12s ease; }
    .fe-workspace .fe-table tbody tr:hover { background:#fafcff; }
    .fe-workspace .fe-person { align-items:center; display:flex; gap:11px; }
    .fe-workspace .fe-avatar { align-items:center; background:linear-gradient(135deg,#e8f0ff,#dbe7fb); border-radius:11px; color:#2f5aa8; display:flex; flex:0 0 38px; font-size:13px; font-weight:800; height:38px; justify-content:center; width:38px; }
    .fe-workspace .fe-name { color:var(--fe-ink); font-size:13.5px; font-weight:750; line-height:1.25; }
    .fe-workspace .fe-user { align-items:center; color:var(--fe-muted); display:inline-flex; font-size:11.5px; gap:4px; margin-top:2px; }
    .fe-workspace .fe-chips { display:flex; flex-wrap:wrap; gap:5px; max-width:360px; }
    .fe-workspace .fe-chip { background:#f2f6fc; border:1px solid #e2eaf6; border-radius:7px; color:#41577a; font-size:11px; font-weight:600; padding:4px 9px; }
    .fe-workspace .fe-chip-more { background:#fff; border-style:dashed; color:#7b8ca3; }
    .fe-workspace .fe-chip-none { background:#fdf3f2; border-color:#f6dedb; color:#b8443c; }
    .fe-workspace .fe-tag { border-radius:20px; display:inline-block; font-size:10px; font-weight:800; letter-spacing:.03em; margin:2px 3px 2px 0; padding:4px 10px; text-transform:uppercase; }
    .fe-workspace .fe-tag-both { background:#eaf3ff; color:#255bb5; }
    .fe-workspace .fe-tag-written { background:#f1effd; color:#5a49b8; }
    .fe-workspace .fe-tag-interview { background:#eaf7f0; color:#1f7a51; }
    .fe-workspace .fe-tag-none { background:#fdeceb; color:#b8443c; }
    .fe-workspace .fe-act { align-items:center; background:#fff; border:1px solid #dde4ee; border-radius:8px; color:#5a6d87; display:inline-flex; height:32px; justify-content:center; margin-left:4px; transition:all .14s ease; width:32px; }
    .fe-workspace .fe-act:hover { border-color:#b9cbe8; color:var(--fe-blue); text-decoration:none; }
    .fe-workspace .fe-act-danger:hover { background:#fdeceb; border-color:#f0c4c0; color:#b8443c; }
    .fe-workspace .fe-empty { color:var(--fe-muted); padding:52px 20px; text-align:center; }
    .fe-workspace .fe-empty i { color:#c3cddc; font-size:42px; }
    .fe-workspace .fe-empty h5 { color:var(--fe-ink); font-size:15px; font-weight:750; margin:10px 0 4px; }

    /* Modals */
    .fe-modal .modal-content { border:0; border-radius:16px; box-shadow:0 22px 60px rgba(16,36,64,.24); overflow:hidden; }
    .fe-modal .modal-header { align-items:flex-start; background:#fbfcfe; border-bottom:1px solid var(--fe-line); padding:16px 20px; }
    .fe-modal .modal-title { color:#132c4a; font-size:15.5px; font-weight:800; }
    .fe-modal .fe-modal-note { color:#6b7b91; font-size:11.5px; margin:3px 0 0; }
    .fe-modal .modal-body { padding:18px 20px; }
    .fe-modal .modal-footer { background:#fbfcfe; border-top:1px solid var(--fe-line); padding:13px 20px; }
    .fe-modal .fe-label { color:#7285a0; display:block; font-size:10px; font-weight:800; letter-spacing:.07em; margin-bottom:6px; text-transform:uppercase; }
    .fe-modal .form-control { border:1px solid #d9e1ec; border-radius:9px; font-size:13px; height:38px; }
    .fe-modal .form-control:focus { border-color:#2457d6; box-shadow:0 0 0 3px rgba(36,87,214,.1); }
    .fe-modal .fe-section { border-top:1px solid var(--fe-line); margin-top:16px; padding-top:14px; }
    .fe-modal .fe-section-title { color:#132c4a; font-size:12.5px; font-weight:800; }
    .fe-modal .fe-section-note { color:#6b7b91; font-size:11.5px; margin:2px 0 10px; }
    .fe-modal .btn-primary { background:#2457d6; border-color:#2457d6; border-radius:9px; font-size:13px; font-weight:700; padding:9px 18px; }
    .fe-modal .btn-primary:hover { background:#1c47b4; border-color:#1c47b4; }
    .fe-modal .btn-light { border:1px solid #dde4ee; border-radius:9px; font-size:13px; font-weight:650; padding:9px 16px; }
    .fe-modal .fe-pw { position:relative; }
    .fe-modal .fe-pw-eye { background:none; border:0; color:#93a0b2; padding:0 6px; position:absolute; right:6px; top:6px; }
    .fe-modal .fe-pw-eye:hover { color:#2457d6; }

    /* Scope picker */
    .fe-scope-picker { border:1px solid var(--fe-line); border-radius:12px; overflow:hidden; }
    .fe-picker-bar { align-items:center; background:#f8fafd; border-bottom:1px solid var(--fe-line); display:flex; flex-wrap:wrap; gap:8px; justify-content:space-between; padding:10px 12px; }
    .fe-picker-search { position:relative; }
    .fe-picker-search i { color:#93a0b2; left:10px; position:absolute; top:8px; }
    .fe-picker-search input { border:1px solid #dde4ee; border-radius:8px; font-size:12px; height:32px; padding-left:30px; width:210px; }
    .fe-picker-tools { align-items:center; display:flex; flex-wrap:wrap; gap:5px; }
    .fe-mini { background:#fff; border:1px solid #dde4ee; border-radius:7px; color:#5a6d87; font-size:11px; font-weight:700; padding:5px 10px; transition:all .14s ease; }
    .fe-mini:hover { border-color:#b9cbe8; color:#2457d6; }
    .fe-mini-sep { background:#dde4ee; height:16px; width:1px; }
    .fe-mini-label { color:#8b9ab0; font-size:10.5px; font-weight:700; }
    .fe-picker-list { max-height:290px; overflow-y:auto; }
    .fe-picker-row { align-items:center; border-bottom:1px solid #f0f4f9; display:flex; flex-wrap:wrap; gap:10px; justify-content:space-between; padding:10px 12px; transition:background .12s ease; }
    .fe-picker-row:last-child { border-bottom:0; }
    .fe-picker-row.is-on { background:#f7faff; }
    .fe-picker-check { align-items:center; cursor:pointer; display:flex; flex:1 1 260px; gap:10px; margin:0; min-width:0; }
    .fe-picker-check input { position:absolute; opacity:0; pointer-events:none; }
    .fe-picker-box { align-items:center; background:#fff; border:1.5px solid #cfd9e7; border-radius:6px; color:transparent; display:flex; flex:0 0 18px; font-size:12px; height:18px; justify-content:center; transition:all .14s ease; width:18px; }
    .fe-picker-check input:checked ~ .fe-picker-box { background:#2457d6; border-color:#2457d6; color:#fff; }
    .fe-picker-check input:focus ~ .fe-picker-box { box-shadow:0 0 0 3px rgba(36,87,214,.15); }
    .fe-picker-text { min-width:0; }
    .fe-picker-title { color:#132c4a; display:block; font-size:12.5px; font-weight:700; line-height:1.3; }
    .fe-picker-meta { color:#8b9ab0; display:block; font-size:10.5px; margin-top:1px; }
    .fe-seg { background:#eef2f8; border-radius:8px; display:inline-flex; flex:0 0 auto; padding:2px; }
    .fe-seg input { position:absolute; opacity:0; pointer-events:none; }
    .fe-seg label { border-radius:6px; color:#5a6d87; cursor:pointer; font-size:11px; font-weight:700; margin:0; padding:5px 11px; transition:all .14s ease; }
    .fe-seg input:checked + label { background:#fff; box-shadow:0 1px 4px rgba(24,52,88,.14); color:#2457d6; }
    .fe-seg input:disabled + label { color:#aeb9c8; cursor:not-allowed; }
    .fe-seg input:disabled:checked + label { background:#f7f9fc; box-shadow:none; color:#aeb9c8; }
    .fe-picker-foot { align-items:center; background:#f8fafd; border-top:1px solid var(--fe-line); color:#6b7b91; display:flex; flex-wrap:wrap; font-size:11.5px; gap:6px 16px; justify-content:space-between; padding:9px 12px; }
    .fe-picker-foot b { color:#132c4a; }
    .fe-picker-hint { color:#8b9ab0; font-size:11px; }
    .fe-picker-empty { color:#8b9ab0; font-size:12.5px; padding:28px 16px; text-align:center; }
    .fe-picker-empty i { color:#c3cddc; display:block; font-size:32px; margin-bottom:6px; }

    @media (max-width:680px) {
        .fe-workspace .fe-hero { align-items:flex-start; flex-direction:column; }
        .fe-picker-search input { width:100%; }
        .fe-picker-row { align-items:flex-start; flex-direction:column; }
    }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid fe-workspace">

            <div class="fe-hero">
                <div>
                    <div class="fe-eyebrow">Secretariat &middot; Manage Users</div>
                    <h4 class="fe-title">Field Encoder Accounts</h4>
                    <p class="fe-subtitle">Field Encoders can only open <b>Encode Scores</b>. Tag the vacancies each account may reach and whether it may encode the Written Examination, the Interview, or both. They cannot tag, endorse, disqualify, or view reports.</p>
                </div>
                <div class="fe-hero-side">
                    <a href="<?= base_url(); ?>" class="fe-back"><i class="mdi mdi-arrow-left"></i>Dashboard</a>
                    <a href="javascript:void(0);" class="fe-add" data-toggle="modal" data-target="#fe-add"><i class="mdi mdi-account-plus-outline"></i>Add Field Encoder</a>
                </div>
            </div>

            <?php if ($successMessage) : ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <?= $fe_h($successMessage); ?>
                </div>
            <?php endif; ?>

            <?php if ($dangerMessage) : ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <?= $fe_h($dangerMessage); ?>
                </div>
            <?php endif; ?>

            <?php
            $taggedTotal = 0;
            foreach ($encoders as $encoder) {
                $taggedTotal += count($accessMap[(int) $encoder->id] ?? []);
            }
            ?>
            <div class="fe-stats">
                <div class="fe-stat">
                    <div class="fe-stat-label">Field Encoders</div>
                    <div class="fe-stat-value"><?= count($encoders); ?></div>
                    <div class="fe-stat-note">Accounts you created</div>
                </div>
                <div class="fe-stat">
                    <div class="fe-stat-label">Vacancies in your scope</div>
                    <div class="fe-stat-value"><?= count($vacancies); ?></div>
                    <div class="fe-stat-note">Open and score-eligible</div>
                </div>
                <div class="fe-stat">
                    <div class="fe-stat-label">Vacancy tags issued</div>
                    <div class="fe-stat-value"><?= $taggedTotal; ?></div>
                    <div class="fe-stat-note">Across all encoders</div>
                </div>
            </div>

            <div class="fe-card">
                <div class="fe-card-head">
                    <div>
                        <h5 class="fe-card-title">Accounts (<?= count($encoders); ?>)</h5>
                        <p class="fe-card-note">Each account sees only the vacancies tagged to it.</p>
                    </div>
                    <?php if (!empty($encoders)) : ?>
                        <div class="fe-card-search">
                            <i class="mdi mdi-magnify"></i>
                            <input type="search" class="form-control" id="fe-list-search" placeholder="Search name or username" autocomplete="off">
                        </div>
                    <?php endif; ?>
                </div>

                <?php if (empty($encoders)) : ?>
                    <div class="fe-empty">
                        <i class="mdi mdi-account-multiple-outline"></i>
                        <h5>No Field Encoder account yet</h5>
                        <p class="mb-3 small">Create one to let someone encode scores for the vacancies you choose.</p>
                        <a href="javascript:void(0);" class="fe-add" data-toggle="modal" data-target="#fe-add"><i class="mdi mdi-account-plus-outline"></i>Add Field Encoder</a>
                    </div>
                <?php else : ?>
                    <div class="table-responsive">
                        <table class="table fe-table" id="fe-list-table">
                            <thead>
                                <tr>
                                    <th>Account</th>
                                    <th>Vacancy access</th>
                                    <th>Can encode</th>
                                    <th class="text-right">Manage</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($encoders as $encoder) :
                                    $encoderId = (int) $encoder->id;
                                    $access = $accessMap[$encoderId] ?? [];
                                    $fullName = trim(preg_replace('/\s+/', ' ', $encoder->fname . ' ' . $encoder->mname . ' ' . $encoder->lname));
                                    $initials = strtoupper(substr(trim((string) $encoder->fname), 0, 1) . substr(trim((string) $encoder->lname), 0, 1));

                                    $titles = [];
                                    $modeCounts = ['written' => 0, 'interview' => 0, 'both' => 0];
                                    foreach ($vacancies as $vacancy) {
                                        $jobId = (int) $vacancy->jobID;
                                        if (!array_key_exists($jobId, $access)) {
                                            continue;
                                        }
                                        $titles[] = $vacancy->jobTitle;
                                        $modeCounts[$access[$jobId]]++;
                                    }
                                    // Rows kept from a vacancy that has since closed / left the scope.
                                    $orphans = count($access) - count($titles);
                                ?>
                                <tr data-search="<?= $fe_h(strtolower($fullName . ' ' . $encoder->username)); ?>">
                                    <td>
                                        <div class="fe-person">
                                            <div class="fe-avatar"><?= $fe_h($initials !== '' ? $initials : '?'); ?></div>
                                            <div>
                                                <div class="fe-name"><?= $fe_h($fullName); ?></div>
                                                <div class="fe-user"><i class="mdi mdi-account-outline"></i><?= $fe_h($encoder->username); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if (empty($titles) && $orphans <= 0) : ?>
                                            <span class="fe-chip fe-chip-none">No vacancy tagged</span>
                                        <?php else : ?>
                                            <div class="fe-chips">
                                                <?php foreach (array_slice($titles, 0, 2) as $title) : ?>
                                                    <span class="fe-chip"><?= $fe_h($title); ?></span>
                                                <?php endforeach; ?>
                                                <?php if (count($titles) > 2) : ?>
                                                    <span class="fe-chip fe-chip-more">+<?= count($titles) - 2; ?> more</span>
                                                <?php endif; ?>
                                                <?php if ($orphans > 0) : ?>
                                                    <span class="fe-chip fe-chip-more"><?= $orphans; ?> closed</span>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (empty($titles)) : ?>
                                            <span class="fe-tag fe-tag-none">Nothing</span>
                                        <?php else : ?>
                                            <?php foreach ($modeCounts as $modeValue => $modeCount) : ?>
                                                <?php if ($modeCount > 0) : ?>
                                                    <span class="fe-tag fe-tag-<?= $modeValue; ?>"><?= $fe_h($fe_modes[$modeValue]); ?> &middot; <?= $modeCount; ?></span>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-right text-nowrap">
                                        <a href="javascript:void(0);" class="fe-act" data-toggle="modal" data-target="#fe-edit-<?= $encoderId; ?>" title="Edit account and access"><i class="mdi mdi-pencil-outline"></i></a>
                                        <a href="javascript:void(0);" class="fe-act" data-toggle="modal" data-target="#fe-pass-<?= $encoderId; ?>" title="Reset password"><i class="mdi mdi-lock-reset"></i></a>
                                        <a href="javascript:void(0);" class="fe-act fe-act-danger" data-toggle="modal" data-target="#fe-delete"
                                           data-username="<?= $fe_h($encoder->username); ?>"
                                           data-url="<?= base_url('secretariat/encoders/delete/' . $encoderId); ?>"
                                           title="Delete account"><i class="mdi mdi-trash-can-outline"></i></a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<!-- Add -->
<div class="modal fade fe-modal" id="fe-add" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="post" action="<?= base_url('secretariat/encoders/store'); ?>" autocomplete="off">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title">Add Field Encoder</h5>
                        <p class="fe-modal-note">The account is tied to your Secretariat scope and can do nothing but encode scores.</p>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="fe-label" for="fe-add-fname">First Name</label>
                            <input type="text" class="form-control" id="fe-add-fname" name="fname" maxlength="200" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="fe-label" for="fe-add-mname">Middle Name</label>
                            <input type="text" class="form-control" id="fe-add-mname" name="mname" maxlength="200">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="fe-label" for="fe-add-lname">Last Name</label>
                            <input type="text" class="form-control" id="fe-add-lname" name="lname" maxlength="200" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="fe-label" for="fe-add-username">Username</label>
                            <input type="text" class="form-control" id="fe-add-username" name="username" maxlength="45" autocomplete="off" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="fe-label" for="fe-add-password">Password</label>
                            <div class="fe-pw">
                                <input type="password" class="form-control" id="fe-add-password" name="password" minlength="6" autocomplete="new-password" required>
                                <button type="button" class="fe-pw-eye" data-pw-toggle="#fe-add-password"><i class="mdi mdi-eye-outline"></i></button>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="fe-label" for="fe-add-password-confirm">Confirm Password</label>
                            <div class="fe-pw">
                                <input type="password" class="form-control" id="fe-add-password-confirm" name="password_confirm" minlength="6" autocomplete="new-password" required>
                                <button type="button" class="fe-pw-eye" data-pw-toggle="#fe-add-password-confirm"><i class="mdi mdi-eye-outline"></i></button>
                            </div>
                        </div>
                    </div>

                    <div class="fe-section">
                        <div class="fe-section-title">Vacancy access &amp; encoding permission</div>
                        <p class="fe-section-note">Tick every vacancy this account may open, then choose what it may encode there.</p>
                        <?php $fe_scope_picker('fe-add', []); ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary waves-effect waves-light"<?= empty($vacancies) ? ' disabled' : ''; ?>>
                        <i class="mdi mdi-account-plus-outline mr-1"></i>Create Field Encoder
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php foreach ($encoders as $encoder) :
    $encoderId = (int) $encoder->id;
    $access = $accessMap[$encoderId] ?? [];
?>
<!-- Edit -->
<div class="modal fade fe-modal" id="fe-edit-<?= $encoderId; ?>" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="post" action="<?= base_url('secretariat/encoders/update'); ?>" autocomplete="off">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title">Edit &mdash; <?= $fe_h($encoder->username); ?></h5>
                        <p class="fe-modal-note">Saving replaces this account's whole vacancy scope with what is ticked below.</p>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" value="<?= $encoderId; ?>">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="fe-label">First Name</label>
                            <input type="text" class="form-control" name="fname" maxlength="200" value="<?= $fe_h($encoder->fname); ?>" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="fe-label">Middle Name</label>
                            <input type="text" class="form-control" name="mname" maxlength="200" value="<?= $fe_h($encoder->mname); ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="fe-label">Last Name</label>
                            <input type="text" class="form-control" name="lname" maxlength="200" value="<?= $fe_h($encoder->lname); ?>" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="fe-label">Username</label>
                            <input type="text" class="form-control" name="username" maxlength="45" value="<?= $fe_h($encoder->username); ?>" required>
                        </div>
                    </div>

                    <div class="fe-section">
                        <div class="fe-section-title">Vacancy access &amp; encoding permission</div>
                        <p class="fe-section-note">Tick every vacancy this account may open, then choose what it may encode there.</p>
                        <?php $fe_scope_picker('fe-edit-' . $encoderId, $access); ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary waves-effect waves-light">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reset password -->
<div class="modal fade fe-modal" id="fe-pass-<?= $encoderId; ?>" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post" action="<?= base_url('secretariat/encoders/password'); ?>" autocomplete="off">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title">Reset Password</h5>
                        <p class="fe-modal-note"><?= $fe_h($encoder->username); ?> &middot; at least 6 characters</p>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" value="<?= $encoderId; ?>">
                    <div class="mb-3">
                        <label class="fe-label" for="fe-pw-new-<?= $encoderId; ?>">New Password</label>
                        <div class="fe-pw">
                            <input type="password" class="form-control" id="fe-pw-new-<?= $encoderId; ?>" name="password" minlength="6" autocomplete="new-password" required>
                            <button type="button" class="fe-pw-eye" data-pw-toggle="#fe-pw-new-<?= $encoderId; ?>"><i class="mdi mdi-eye-outline"></i></button>
                        </div>
                    </div>
                    <div>
                        <label class="fe-label" for="fe-pw-confirm-<?= $encoderId; ?>">Confirm Password</label>
                        <div class="fe-pw">
                            <input type="password" class="form-control" id="fe-pw-confirm-<?= $encoderId; ?>" name="password_confirm" minlength="6" autocomplete="new-password" required>
                            <button type="button" class="fe-pw-eye" data-pw-toggle="#fe-pw-confirm-<?= $encoderId; ?>"><i class="mdi mdi-eye-outline"></i></button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary waves-effect waves-light">Reset Password</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>

<!-- Delete confirmation -->
<div class="modal fade fe-modal" id="fe-delete" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title">Remove account</h5>
                    <p class="fe-modal-note">This cannot be undone.</p>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <p class="mb-0" style="font-size:13px;color:#41577a;">
                    Remove the Field Encoder account <b id="fe-delete-name"></b>? Its vacancy tags are removed too. Scores already encoded stay on file.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                <a href="#" id="fe-delete-confirm" class="btn btn-danger" style="border-radius:9px;font-size:13px;font-weight:700;padding:9px 18px;">Remove</a>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    'use strict';

    /* ---- scope picker: checkbox drives its mode segment ---- */
    function syncRow(row) {
        var box = row.querySelector('[data-picker-toggle]');
        if (!box) { return; }
        row.classList.toggle('is-on', box.checked);
        row.querySelectorAll('[data-picker-seg] input').forEach(function (radio) {
            radio.disabled = !box.checked;
        });
    }

    function syncCount(picker) {
        var counter = picker.querySelector('[data-picker-count]');
        if (!counter) { return; }
        counter.textContent = picker.querySelectorAll('[data-picker-toggle]:checked').length;
    }

    document.querySelectorAll('[data-picker]').forEach(function (picker) {
        picker.querySelectorAll('[data-picker-row]').forEach(syncRow);
        syncCount(picker);

        picker.addEventListener('change', function (e) {
            if (!e.target.matches('[data-picker-toggle]')) { return; }
            syncRow(e.target.closest('[data-picker-row]'));
            syncCount(picker);
        });

        var search = picker.querySelector('[data-picker-search]');
        if (search) {
            search.addEventListener('input', function () {
                var term = search.value.trim().toLowerCase();
                picker.querySelectorAll('[data-picker-row]').forEach(function (row) {
                    var hay = row.getAttribute('data-search') || '';
                    row.style.display = (term === '' || hay.indexOf(term) !== -1) ? '' : 'none';
                });
            });
        }

        // Bulk tools act on the rows currently visible under the search filter.
        function visibleRows() {
            return Array.prototype.filter.call(
                picker.querySelectorAll('[data-picker-row]'),
                function (row) { return row.style.display !== 'none'; }
            );
        }

        var selectAll = picker.querySelector('[data-picker-all]');
        var clearAll = picker.querySelector('[data-picker-none]');

        if (selectAll) {
            selectAll.addEventListener('click', function () {
                visibleRows().forEach(function (row) {
                    var box = row.querySelector('[data-picker-toggle]');
                    if (box) { box.checked = true; syncRow(row); }
                });
                syncCount(picker);
            });
        }
        if (clearAll) {
            clearAll.addEventListener('click', function () {
                visibleRows().forEach(function (row) {
                    var box = row.querySelector('[data-picker-toggle]');
                    if (box) { box.checked = false; syncRow(row); }
                });
                syncCount(picker);
            });
        }

        picker.querySelectorAll('[data-picker-mode]').forEach(function (button) {
            button.addEventListener('click', function () {
                var mode = button.getAttribute('data-picker-mode');
                visibleRows().forEach(function (row) {
                    var box = row.querySelector('[data-picker-toggle]');
                    if (!box || !box.checked) { return; }
                    var radio = row.querySelector('[data-picker-seg] input[value="' + mode + '"]');
                    if (radio) { radio.checked = true; }
                });
            });
        });
    });

    /* ---- password reveal ---- */
    document.querySelectorAll('[data-pw-toggle]').forEach(function (button) {
        button.addEventListener('click', function () {
            var input = document.querySelector(button.getAttribute('data-pw-toggle'));
            if (!input) { return; }
            var show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            button.querySelector('i').className = show ? 'mdi mdi-eye-off-outline' : 'mdi mdi-eye-outline';
        });
    });

    /* ---- delete confirmation ---- */
    var deleteModal = document.getElementById('fe-delete');
    if (deleteModal) {
        document.querySelectorAll('[data-target="#fe-delete"]').forEach(function (trigger) {
            trigger.addEventListener('click', function () {
                document.getElementById('fe-delete-name').textContent = trigger.getAttribute('data-username') || '';
                document.getElementById('fe-delete-confirm').setAttribute('href', trigger.getAttribute('data-url') || '#');
            });
        });
    }

    /* ---- account list search ---- */
    var listSearch = document.getElementById('fe-list-search');
    if (listSearch) {
        listSearch.addEventListener('input', function () {
            var term = listSearch.value.trim().toLowerCase();
            document.querySelectorAll('#fe-list-table tbody tr').forEach(function (row) {
                var hay = row.getAttribute('data-search') || '';
                row.style.display = (term === '' || hay.indexOf(term) !== -1) ? '' : 'none';
            });
        });
    }

    /* ---- reopen the modal a failed save came from ---- */
    var reopen = <?= json_encode($reopen); ?>;
    if (reopen && window.jQuery) {
        var target = reopen === 'add' ? '#fe-add' : '#fe-' + reopen;
        if (document.querySelector(target)) {
            jQuery(target).modal('show');
        }
    }
})();
</script>
