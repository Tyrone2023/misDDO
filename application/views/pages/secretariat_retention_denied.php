<?php
$rd_h = static function ($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};

$positionGroups = [
    1 => 'Teaching',
    2 => 'School Administration',
    3 => 'Related Teaching',
    4 => 'Non-Teaching',
    5 => 'Promotion',
];

$vacancies = $vacancies ?? [];
$requests = $requests ?? [];
$retentionCounts = $retentionCounts ?? [];
$selectedVacancy = $selectedVacancy ?? null;
$selectedJobId = (int) ($selectedJobId ?? 0);
$jobTypeLabels = $jobTypeLabels ?? [];
$scope = ($scope ?? 'all') === 'mine' ? 'mine' : 'all';
$allCount = (int) ($allCount ?? 0);
$mineCount = (int) ($mineCount ?? 0);

$applicantName = static function ($request) {
    $name = trim(implode(' ', array_filter([
        trim((string) ($request->FirstName ?? '')),
        trim((string) ($request->MiddleName ?? '')),
        trim((string) ($request->LastName ?? '')),
        trim((string) ($request->NameExtn ?? '')),
    ], static function ($part) { return $part !== ''; })));

    return $name !== '' ? $name : 'Applicant ' . (string) ($request->applicant_id ?? '');
};

$profileUrl = static function ($request) {
    if (empty($request->profile_route) || empty($request->appID)) {
        return '';
    }

    return base_url('Pages/' . $request->profile_route . '/'
        . rawurlencode((string) $request->profile_id) . '/'
        . (int) ($request->jobID ?? 0) . '/'
        . rawurlencode((string) $request->pre_school) . '/'
        . (int) $request->appID . '/'
        . rawurlencode((string) $request->record_no));
};

$scopeLabel = static function ($request) {
    $pType = (int) ($request->p_type_resolved ?? $request->position ?? 0);
    if ((int) $request->r_type === 2) {
        return $pType === 1
            ? 'Demonstration Teaching and Teacher Reflection only'
            : 'Interview and Written Examination only';
    }
    return 'All criteria';
};

$fmtDate = static function ($value) {
    $value = trim((string) $value);
    if ($value === '' || strpos($value, '0000-00-00') === 0) {
        return '';
    }
    $stamp = strtotime($value);
    return $stamp ? date('M j, Y', $stamp) : $value;
};

// Chip links keep whichever vacancy is already being viewed.
$scopeUrl = static function ($value) use ($selectedJobId) {
    $query = [];
    if ($selectedJobId > 0) {
        $query['job_id'] = $selectedJobId;
    }
    if ($value === 'mine') {
        $query['scope'] = 'mine';
    }
    return base_url('secretariat/retention/denied' . (empty($query) ? '' : '?' . http_build_query($query)));
};
?>

<style>
    .rd-page { --rd-ink:#1f2a37; --rd-muted:#6d7885; --rd-line:#e6e9ef; --rd-accent:#5a4bc4; --rd-red:#b03636; --rd-soft:#f7f8fb; padding-bottom:34px; }
    .rd-page .container-fluid { max-width:1280px; }

    .rd-page .rd-head { align-items:flex-start; border-bottom:1px solid var(--rd-line); display:flex; flex-wrap:wrap; gap:12px; justify-content:space-between; margin-bottom:20px; padding:6px 0 16px; }
    .rd-page .rd-head h2 { color:var(--rd-ink); font-size:22px; font-weight:700; letter-spacing:-.01em; margin:0; }
    .rd-page .rd-head p { color:var(--rd-muted); font-size:13.5px; line-height:1.55; margin:6px 0 0; max-width:720px; }
    .rd-page .rd-back { align-items:center; border:1px solid #d6dbe4; border-radius:8px; color:#4a5566; display:inline-flex; font-size:12.5px; font-weight:600; gap:6px; padding:8px 13px; white-space:nowrap; }
    .rd-page .rd-back:hover { background:var(--rd-soft); border-color:#bcc4d1; color:var(--rd-accent); text-decoration:none; }

    .rd-page .rd-card { background:#fff; border:1px solid var(--rd-line); border-radius:12px; margin-bottom:18px; }
    .rd-page .rd-card-head { align-items:center; border-bottom:1px solid var(--rd-line); display:flex; flex-wrap:wrap; gap:10px; justify-content:space-between; padding:14px 20px; }
    .rd-page .rd-card-title { color:var(--rd-ink); font-size:15px; font-weight:700; margin:0; }
    .rd-page .rd-card-count { color:var(--rd-muted); font-size:12.5px; }
    .rd-page .rd-card-body { padding:18px 20px; }

    .rd-page .rd-filters { align-items:end; display:grid; gap:14px; grid-template-columns:minmax(240px,1.4fr) auto minmax(200px,.9fr); }
    .rd-page label.rd-label { color:var(--rd-ink); display:block; font-size:12.5px; font-weight:600; margin-bottom:6px; }
    .rd-page .rd-select { border:1px solid #d6dbe4; border-radius:8px; color:var(--rd-ink); font-size:13.5px; height:42px; padding:0 10px; width:100%; }
    .rd-page .rd-select:focus { border-color:var(--rd-accent); box-shadow:0 0 0 3px rgba(90,75,196,.12); outline:none; }
    .rd-page .rd-search { border:1px solid #d6dbe4; border-radius:8px; font-size:13.5px; height:42px; padding:0 12px; width:100%; }
    .rd-page .rd-search:focus { border-color:var(--rd-accent); box-shadow:0 0 0 3px rgba(90,75,196,.12); outline:none; }

    .rd-page .rd-scope { display:inline-flex; gap:8px; }
    .rd-page .rd-scope a { align-items:center; background:#fff; border:1px solid #d6dbe4; border-radius:20px; color:#5a6473; display:inline-flex; font-size:12.5px; font-weight:650; gap:6px; height:42px; padding:0 16px; white-space:nowrap; }
    .rd-page .rd-scope a:hover { border-color:#b6aee6; color:var(--rd-accent); text-decoration:none; }
    .rd-page .rd-scope a.active { background:var(--rd-accent); border-color:var(--rd-accent); color:#fff; }
    .rd-page .rd-scope b { background:rgba(0,0,0,.07); border-radius:10px; font-size:11px; font-weight:800; padding:1px 7px; }
    .rd-page .rd-scope a.active b { background:rgba(255,255,255,.25); }

    .rd-page .rd-stats { display:grid; gap:12px; grid-template-columns:repeat(auto-fit,minmax(150px,1fr)); }
    .rd-page .rd-stat { background:var(--rd-soft); border:1px solid var(--rd-line); border-radius:10px; padding:12px 14px; }
    .rd-page .rd-stat-label { color:var(--rd-muted); font-size:11px; font-weight:700; letter-spacing:.05em; text-transform:uppercase; }
    .rd-page .rd-stat-value { color:var(--rd-ink); font-size:23px; font-weight:750; line-height:1.2; margin-top:3px; }
    .rd-page .rd-stat-note { color:var(--rd-muted); font-size:11.5px; margin-top:2px; }
    .rd-page .rd-stat-denied .rd-stat-value { color:var(--rd-red); }

    .rd-page .rd-table-wrap { overflow-x:auto; }
    .rd-page table.rd-table { margin:0; min-width:1020px; width:100%; }
    .rd-page table.rd-table thead th { background:#f6f7fa; border-bottom:1px solid var(--rd-line); border-top:0; color:#6c7684; cursor:pointer; font-size:10.5px; font-weight:750; letter-spacing:.055em; padding:11px 14px; text-transform:uppercase; user-select:none; white-space:nowrap; }
    .rd-page table.rd-table thead th:hover { color:var(--rd-accent); }
    .rd-page table.rd-table thead th.rd-nosort { cursor:default; }
    .rd-page table.rd-table thead th.rd-nosort:hover { color:#6c7684; }
    .rd-page table.rd-table thead th .rd-arrow { color:#aab2be; font-size:11px; margin-left:3px; }
    .rd-page table.rd-table thead th.sorted .rd-arrow { color:var(--rd-accent); }
    .rd-page table.rd-table td { border-color:#eef0f4; color:var(--rd-ink); font-size:13px; padding:11px 14px; vertical-align:top; }
    .rd-page table.rd-table tbody tr:hover { background:#fafbfd; }
    .rd-page .rd-name { color:var(--rd-ink); font-size:13.5px; font-weight:650; }
    .rd-page .rd-sub { color:var(--rd-muted); font-size:11.5px; margin-top:3px; }
    .rd-page .rd-sub a { color:var(--rd-accent); font-weight:600; }
    .rd-page .rd-eval-none { color:#8b939e; font-style:italic; }
    .rd-page .rd-chip { border-radius:6px; display:inline-block; font-size:11px; font-weight:650; padding:4px 9px; white-space:nowrap; }
    .rd-page .rd-chip-scope { background:#f1efff; color:#5546b5; }
    .rd-page .rd-chip-dq { background:#fbeaea; color:var(--rd-red); }
    .rd-page .rd-chip-mine { background:#e7f0fb; color:#245b95; }
    .rd-page .rd-reason { background:#fff5f5; border-left:3px solid #d98b8b; border-radius:0 6px 6px 0; color:#8f2b2b; font-size:12.5px; line-height:1.5; padding:7px 10px; }
    .rd-page .rd-reason-none { color:#8b939e; font-style:italic; }
    .rd-page .rd-dq-detail { color:#8f2b2b; font-size:11.5px; line-height:1.45; margin-top:6px; }
    .rd-page .rd-nothing { color:var(--rd-muted); font-size:13.5px; padding:44px 20px; text-align:center; }

    @media (max-width:900px) {
        .rd-page .rd-filters { grid-template-columns:1fr; }
        .rd-page .rd-scope { flex-wrap:wrap; }
        .rd-page .rd-scope a { flex:1; justify-content:center; }
    }
</style>

<div class="content-page rd-page">
    <div class="content">
        <div class="container-fluid">

            <div class="rd-head">
                <div>
                    <h2>Denied Retention Requests</h2>
                    <!-- <p>A record of retention requests that were refused, with the reason given and the evaluator the applicant was tagged to. Nothing here can be changed &mdash; reopen a decision from the applicant's MA page if it needs revisiting.</p> -->
                </div>
                <a href="<?= base_url('secretariat/retention' . ($selectedJobId > 0 ? '?job_id=' . $selectedJobId : '')); ?>" class="rd-back">
                    <i class="mdi mdi-arrow-left"></i>Back to retention queue
                </a>
            </div>

            <?php foreach (['success' => 'alert-success', 'danger' => 'alert-danger'] as $flash => $class) : ?>
                <?php if ($this->session->flashdata($flash)) : ?>
                    <div class="alert <?= $class; ?> alert-dismissible fade show" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <?= $rd_h($this->session->flashdata($flash)); ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>

            <div class="card rd-card">
                <div class="rd-card-body">
                    <form method="get" action="<?= base_url('secretariat/retention/denied'); ?>" class="rd-filters" id="rd-filter-form">
                        <?php if ($scope === 'mine') : ?><input type="hidden" name="scope" value="mine"><?php endif; ?>
                        <div>
                            <label for="job_id" class="rd-label">Vacancy applied for</label>
                            <select name="job_id" id="job_id" class="rd-select">
                                <option value="">All assigned vacancies</option>
                                <?php foreach ($vacancies as $vacancy) : ?>
                                    <?php
                                    $jobId = (int) $vacancy->jobID;
                                    $vc = $retentionCounts[$jobId] ?? ['denied' => 0];
                                    $group = $positionGroups[(int) $vacancy->position] ?? 'Vacancy';
                                    $typeLabel = $jobTypeLabels[(int) ($vacancy->job_type ?? 0)] ?? '';
                                    ?>
                                    <option value="<?= $jobId; ?>" <?= $jobId === $selectedJobId ? 'selected' : ''; ?>>
                                        <?= $rd_h($vacancy->jobTitle . ($typeLabel !== '' ? ' — ' . $typeLabel : '') . ' — ' . $group . ' — FY ' . $vacancy->sy); ?>
                                        (<?= (int) $vc['denied']; ?> denied)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <span class="rd-label">Show</span>
                            <div class="rd-scope">
                                <a href="<?= $rd_h($scopeUrl('all')); ?>" class="<?= $scope === 'all' ? 'active' : ''; ?>">All denied <b><?= $allCount; ?></b></a>
                                <a href="<?= $rd_h($scopeUrl('mine')); ?>" class="<?= $scope === 'mine' ? 'active' : ''; ?>">My denied only <b><?= $mineCount; ?></b></a>
                            </div>
                        </div>
                        <div>
                            <label for="rd-search" class="rd-label">Find</label>
                            <input type="search" id="rd-search" class="rd-search" placeholder="Applicant, number, evaluator, reason" autocomplete="off">
                        </div>
                    </form>
                </div>
            </div>

            <div class="card rd-card">
                <div class="rd-card-body">
                    <div class="rd-stats">
                        <div class="rd-stat rd-stat-denied">
                            <div class="rd-stat-label">Denied</div>
                            <div class="rd-stat-value"><?= $allCount; ?></div>
                            <div class="rd-stat-note"><?= $selectedVacancy ? 'On this vacancy' : 'Across every assigned vacancy'; ?></div>
                        </div>
                        <div class="rd-stat">
                            <div class="rd-stat-label">Denied by you</div>
                            <div class="rd-stat-value"><?= $mineCount; ?></div>
                            <div class="rd-stat-note"><?= $allCount > 0 ? round(($mineCount / $allCount) * 100) . '% of the denials shown' : 'No denials recorded yet'; ?></div>
                        </div>
                        <?php if ($selectedVacancy) : ?>
                            <?php $vc = $retentionCounts[(int) $selectedVacancy->jobID] ?? ['pending' => 0, 'granted' => 0, 'total' => 0]; ?>
                            <div class="rd-stat">
                                <div class="rd-stat-label">Granted</div>
                                <div class="rd-stat-value"><?= (int) $vc['granted']; ?></div>
                                <div class="rd-stat-note">Points retained</div>
                            </div>
                            <div class="rd-stat">
                                <div class="rd-stat-label">Still pending</div>
                                <div class="rd-stat-value"><?= (int) $vc['pending']; ?></div>
                                <div class="rd-stat-note">Waiting for a decision</div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="card rd-card">
                <div class="rd-card-head">
                    <h5 class="rd-card-title">
                        <?= $scope === 'mine' ? 'Denied by you' : 'All denied requests'; ?>
                        <?php if ($selectedVacancy) : ?>
                            &middot; <?= $rd_h($selectedVacancy->jobTitle); ?>
                        <?php endif; ?>
                    </h5>
                    <span class="rd-card-count"><strong id="rd-visible-count"><?= count($requests); ?></strong> of <?= count($requests); ?> shown</span>
                </div>

                <?php if (empty($requests)) : ?>
                    <div class="rd-nothing">
                        <?= $scope === 'mine'
                            ? 'You have not denied a retention request' . ($selectedVacancy ? ' on this vacancy' : '') . ' yet.'
                            : 'No retention request has been denied' . ($selectedVacancy ? ' on this vacancy' : ' on your assigned vacancies') . ' yet.'; ?>
                    </div>
                <?php else : ?>
                    <div class="rd-table-wrap">
                        <table class="table table-hover rd-table" id="rd-table">
                            <thead>
                                <tr>
                                    <th data-sort="text" style="width:20%">Applicant <span class="rd-arrow">&#8597;</span></th>
                                    <th data-sort="text" style="width:16%">Vacancy <span class="rd-arrow">&#8597;</span></th>
                                    <th data-sort="text" style="width:14%">Evaluator <span class="rd-arrow">&#8597;</span></th>
                                    <th data-sort="text" style="width:12%">Scope <span class="rd-arrow">&#8597;</span></th>
                                    <th data-sort="number" style="width:13%">Denied on <span class="rd-arrow">&#8597;</span></th>
                                    <th class="rd-nosort" style="width:25%">Reason</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($requests as $request) : ?>
                                    <?php
                                    $name = $applicantName($request);
                                    $url = $profileUrl($request);
                                    $evalName = trim((string) ($request->evaluator_name ?? ''));
                                    $reason = trim((string) ($request->deny_reason ?? ''));
                                    $resolver = trim((string) ($request->resolved_by ?? ''));
                                    $isMine = (int) $request->res === (int) ($currentUserId ?? 0);
                                    $group = $positionGroups[(int) $request->position] ?? 'Vacancy';
                                    $typeLabel = $jobTypeLabels[(int) ($request->job_type ?? 0)] ?? '';
                                    $deniedOn = $fmtDate($request->adate);
                                    $sortStamp = strtotime((string) $request->adate) ?: 0;
                                    $searchText = strtolower(implode(' ', [
                                        $name,
                                        (string) ($request->record_no ?? ''),
                                        (string) ($request->applicant_id ?? ''),
                                        (string) ($request->appID ?? ''),
                                        (string) ($request->jobTitle ?? ''),
                                        $typeLabel,
                                        $evalName,
                                        $reason,
                                        $resolver,
                                    ]));
                                    ?>
                                    <tr data-rd-search="<?= $rd_h($searchText); ?>">
                                        <td data-sort-value="<?= $rd_h(strtolower($name)); ?>">
                                            <div class="rd-name"><?= $rd_h($name); ?></div>
                                            <div class="rd-sub">
                                                No. <?= $rd_h($request->record_no); ?>
                                                <?php if ($url !== '') : ?>&middot; <a href="<?= $rd_h($url); ?>" target="_blank" rel="noopener">View</a><?php endif; ?>
                                            </div>
                                            <?php if ((int) $request->dq === 2) : ?>
                                                <?php $dqReason = trim((string) ($request->dq_reason ?? '')); ?>
                                                <div class="rd-dq-detail"><span class="rd-chip rd-chip-dq">Disqualified</span>
                                                    <?= $dqReason !== '' ? ' ' . nl2br($rd_h($dqReason)) : ''; ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td data-sort-value="<?= $rd_h(strtolower((string) $request->jobTitle)); ?>">
                                            <div><?= $rd_h($request->jobTitle); ?></div>
                                            <div class="rd-sub">
                                                <?= $rd_h($group); ?><?= $typeLabel !== '' ? ' &middot; ' . $rd_h($typeLabel) : ''; ?> &middot; FY <?= $rd_h($request->sy); ?>
                                            </div>
                                        </td>
                                        <td data-sort-value="<?= $rd_h(strtolower($evalName)); ?>">
                                            <?= $evalName !== '' ? $rd_h($evalName) : '<span class="rd-eval-none">Not tagged</span>'; ?>
                                        </td>
                                        <td data-sort-value="<?= $rd_h(strtolower($scopeLabel($request))); ?>">
                                            <span class="rd-chip rd-chip-scope"><?= $rd_h($scopeLabel($request)); ?></span>
                                        </td>
                                        <td data-sort-value="<?= (int) $sortStamp; ?>">
                                            <?= $deniedOn !== '' ? $rd_h($deniedOn) : '<span class="rd-eval-none">No date</span>'; ?>
                                            <div class="rd-sub">
                                                <?php if ($resolver !== '') : ?>by <?= $rd_h($resolver); ?><?php else : ?>by an unknown user<?php endif; ?>
                                                <?php if ($isMine) : ?> <span class="rd-chip rd-chip-mine">You</span><?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if ($reason !== '') : ?>
                                                <div class="rd-reason"><?= nl2br($rd_h($reason)); ?></div>
                                            <?php else : ?>
                                                <span class="rd-reason-none">No reason was recorded.</span>
                                            <?php endif; ?>
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

<script>
(function () {
    var form = document.getElementById('rd-filter-form');
    var vacancy = document.getElementById('job_id');
    var search = document.getElementById('rd-search');
    var table = document.getElementById('rd-table');
    var counter = document.getElementById('rd-visible-count');

    if (vacancy && form) {
        vacancy.addEventListener('change', function () { form.submit(); });
    }

    if (!table) return;

    var body = table.querySelector('tbody');
    var rows = Array.prototype.slice.call(body.querySelectorAll('tr'));

    if (search) {
        search.addEventListener('keydown', function (event) {
            if (event.key === 'Enter') event.preventDefault();
        });
        search.addEventListener('input', function () {
            var needle = search.value.trim().toLowerCase();
            var visible = 0;
            rows.forEach(function (row) {
                var matches = !needle || (row.getAttribute('data-rd-search') || '').indexOf(needle) !== -1;
                row.style.display = matches ? '' : 'none';
                if (matches) visible += 1;
            });
            if (counter) counter.textContent = visible;
        });
    }

    // Column sorting reads data-sort-value, so a date sorts by its timestamp
    // rather than the "Mar 4, 2026" text shown in the cell.
    var headers = Array.prototype.slice.call(table.querySelectorAll('thead th[data-sort]'));
    headers.forEach(function (header, columnIndex) {
        header.addEventListener('click', function () {
            var numeric = header.dataset.sort === 'number';
            var ascending = header.dataset.direction !== 'asc';

            headers.forEach(function (other) {
                other.classList.remove('sorted');
                if (other !== header) delete other.dataset.direction;
            });
            header.classList.add('sorted');
            header.dataset.direction = ascending ? 'asc' : 'desc';
            header.querySelector('.rd-arrow').innerHTML = ascending ? '&#8593;' : '&#8595;';
            headers.forEach(function (other) {
                if (other !== header) other.querySelector('.rd-arrow').innerHTML = '&#8597;';
            });

            rows.sort(function (a, b) {
                var av = a.cells[columnIndex].getAttribute('data-sort-value') || '';
                var bv = b.cells[columnIndex].getAttribute('data-sort-value') || '';
                var result = numeric
                    ? (Number(av) || 0) - (Number(bv) || 0)
                    : av.localeCompare(bv);
                return ascending ? result : -result;
            });

            rows.forEach(function (row) { body.appendChild(row); });
        });
    });
})();
</script>
