<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$jobTypes = $jobTypes ?? [];

if (!function_exists('eh')) {
    function eh($v) {
        return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
    }
}
?>

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">

<style>
    .widget-flat {
        border: 0;
        box-shadow: 0 2px 12px rgba(0,0,0,.06);
        border-radius: 12px;
    }
    .widget-icon {
        width: 46px; height: 46px;
        display: inline-flex;
        align-items: center; justify-content: center;
        font-size: 22px;
    }
    .page-title-box { margin-bottom: 20px; }
    .header-title   { font-weight: 600; }
    .table td, .table th { vertical-align: middle !important; }
    .badge { font-size: 12px; padding: 6px 10px; }
    .dataTables_wrapper .dataTables_filter input {
        margin-left:.5em; display:inline-block;
        width:auto; min-width:220px;
    }
    .dataTables_wrapper .dataTables_length select { min-width:80px; }
    .table-responsive { overflow-x:auto; }
    /* subtle flash when a row moves tables */
    @keyframes rowHighlight {
        from { background: #fff3cd; }
        to   { background: transparent; }
    }
    .row-highlight td { animation: rowHighlight 2s ease-out; }
</style>

<div class="content-page">
  <div class="content">
    <div class="container-fluid">

      <div class="row">
        <div class="col-12">
          <div class="page-title-box">
            <h4 class="page-title">Assigned Applicants Dashboard</h4>
          </div>
        </div>
      </div>

      <!-- Reminder Alert -->
      <?php if (isset($pending_queries) && $pending_queries > 0): ?>
      <div class="alert alert-info alert-dismissible fade show" role="alert">
        <strong>Reminder:</strong> You have <?= (int)$pending_queries ?> pending query(ies) that need to be finalized. Please review and finalize them promptly.
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <?php endif; ?>

      <!-- Summary Cards -->
      <div class="row">
        <div class="col-md-3">
          <div class="card widget-flat">
            <div class="card-body">
              <div class="float-right">
                <i class="mdi mdi-account-multiple widget-icon bg-primary rounded-circle text-white"></i>
              </div>
              <h5 class="text-muted font-weight-normal mt-0">Total Assigned</h5>
              <h3 class="mt-3 mb-3" id="cnt-total"><?= (int)($counts['total']   ?? 0); ?></h3>
              <p class="mb-0 text-muted">All applicants assigned to you</p>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card widget-flat">
            <div class="card-body">
              <div class="float-right">
                <i class="mdi mdi-timer-sand widget-icon bg-warning rounded-circle text-white"></i>
              </div>
              <h5 class="text-muted font-weight-normal mt-0">Pending Assigned Applicants</h5>
              <h3 class="mt-3 mb-3 text-warning" id="cnt-pending"><?= (int)($counts['pending'] ?? 0); ?></h3>
              <p class="mb-0 text-muted">No rating yet / default stub only</p>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card widget-flat">
            <div class="card-body">
              <div class="float-right">
                <i class="mdi mdi-check-circle widget-icon bg-success rounded-circle text-white"></i>
              </div>
              <h5 class="text-muted font-weight-normal mt-0">With Scores</h5>
              <h3 class="mt-3 mb-3 text-success" id="cnt-scored"><?= (int)($counts['scored']  ?? 0); ?></h3>
              <p class="mb-0 text-muted">Already has actual scoring / rated</p>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card widget-flat">
            <div class="card-body">
              <div class="float-right">
                <i class="mdi mdi-message-alert widget-icon bg-info rounded-circle text-white"></i>
              </div>
              <h5 class="text-muted font-weight-normal mt-0">Pending Queries</h5>
              <h3 class="mt-3 mb-3 text-info" id="cnt-pending-queries"><?= (int)($pending_queries ?? 0); ?></h3>
              <p class="mb-0 text-muted">Queries not yet finalized</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Pending Table -->
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-body">
              <h4 class="header-title mb-3">Pending Assigned Applicants</h4>
              <div class="table-responsive">
                <!--
                  5 columns, indices 0-4:
                  0: Record No | 1: Applicant Name | 2: Position Applied | 3: Status | 4: Action
                -->
                <table id="pendingApplicantsTable"
                       class="table table-striped table-bordered dt-responsive nowrap w-100">
                  <thead class="thead-light">
                    <tr>
                      <th>Record No</th>
                      <th>Applicant Name</th>
                      <th>Position Applied</th>
                      <th>Status</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (!empty($pending)): ?>
                      <?php foreach ($pending as $row): ?>
                        <?php
                          $recordNo   = trim((string)($row->record_no  ?? $row->applicant_id ?? ''));
                          $firstName  = trim((string)($row->FirstName  ?? ''));
                          $middleName = trim((string)($row->MiddleName ?? ''));
                          $lastName   = trim((string)($row->LastName   ?? ''));
                          $fullName   = trim($lastName.', '.$firstName.' '.$middleName);
                          $jobTitle   = trim((string)($row->jobTitle   ?? ''));
                          $jobTypeId  = (int)($row->job_type ?? 0);
                          $jobType    = $jobTypes[$jobTypeId] ?? '';
                          $preSchool  = trim((string)($row->pre_school ?? ''));
                          $status     = trim((string)($row->appStatus  ?? 'Pending'));
                          $jobId      = (int)($row->jobID ?? $row->job_id ?? 0);
                          $appId      = (int)($row->appID ?? $row->app_id ?? 0);
                          $openUrl    = base_url(
                            'EvaluatorAssigned/open/'.
                            rawurlencode($recordNo).'/'.$jobId.'/'.
                            rawurlencode($preSchool).'/'.$appId.'/'.
                            rawurlencode($recordNo)
                          );
                        ?>
                        <tr data-app-id="<?= $appId; ?>">
                          <td><?= eh($recordNo); ?></td>
                          <td><?= eh($fullName); ?></td>
                          <td><?= eh($jobTitle); ?> - <?= eh($jobType); ?></td>
                          <td><span class="badge badge-warning"><?= eh($status); ?></span></td>
                          <td>
                            <a href="<?= $openUrl; ?>" target="_blank" class="btn btn-primary btn-sm">
                              <i class="mdi mdi-open-in-new"></i> Open
                            </a>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <tr><td colspan="5" class="text-center">No pending assigned applicants found.</td></tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- With Scores Table -->
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-body">
              <h4 class="header-title mb-3">With Scores</h4>
              <div class="table-responsive">
                <!--
                  5 columns, indices 0-4:
                  0: Record No | 1: Applicant Name | 2: Position Applied | 3: Status | 4: Action
                -->
                <table id="scoredApplicantsTable"
                       class="table table-striped table-bordered dt-responsive nowrap w-100">
                  <thead class="thead-light">
                    <tr>
                      <th>Record No</th>
                      <th>Applicant Name</th>
                      <th>Position Applied</th>
                      <th>Status</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (!empty($scored)): ?>
                      <?php foreach ($scored as $row): ?>
                        <?php
                          $recordNo   = trim((string)($row->record_no  ?? $row->applicant_id ?? ''));
                          $firstName  = trim((string)($row->FirstName  ?? ''));
                          $middleName = trim((string)($row->MiddleName ?? ''));
                          $lastName   = trim((string)($row->LastName   ?? ''));
                          $fullName   = trim($lastName.', '.$firstName.' '.$middleName);
                          $jobTitle   = trim((string)($row->jobTitle   ?? ''));
                          $jobTypeId  = (int)($row->job_type ?? 0);
                          $jobType    = $jobTypes[$jobTypeId] ?? '';
                          $preSchool  = trim((string)($row->pre_school ?? ''));
                          $status     = trim((string)($row->appStatus  ?? 'With Scores'));
                          $jobId      = (int)($row->jobID ?? $row->job_id ?? 0);
                          $appId      = (int)($row->appID ?? $row->app_id ?? 0);
                          $openUrl    = base_url(
                            'EvaluatorAssigned/open/'.
                            rawurlencode($recordNo).'/'.$jobId.'/'.
                            rawurlencode($preSchool).'/'.$appId.'/'.
                            rawurlencode($recordNo)
                          );
                        ?>
                        <tr data-app-id="<?= $appId; ?>">
                          <td><?= eh($recordNo); ?></td>
                          <td><?= eh($fullName); ?></td>
                          <td><?= eh($jobType); ?> - <?= eh($jobTitle); ?></td>
                          <td><span class="badge badge-success"><?= eh($status); ?></span></td>
                          <td>
                            <a href="<?= $openUrl; ?>" target="_blank" class="btn btn-success btn-sm">
                              <i class="mdi mdi-open-in-new"></i> Open
                            </a>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <tr><td colspan="5" class="text-center">No scored applicants found.</td></tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<!-- jQuery and DataTables JS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<script>
$(document).ready(function () {

    // ── Helper: build a DataTable with our shared config ─────────────────
    function makeTable(id, searchLabel, emptyLabel) {
        if ($.fn.DataTable.isDataTable(id)) {
            $(id).DataTable().destroy();
        }
        return $(id).DataTable({
            responsive  : true,
            pageLength  : 10,
            lengthMenu  : [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
            order       : [[1, 'asc']],   // ← col 1 = Applicant Name (# col removed)
            columnDefs  : [
                // Action column (index 4) – not sortable / searchable
                { targets: [4], orderable: false, searchable: false }
            ],
            language: {
                search      : searchLabel,
                lengthMenu  : 'Show _MENU_ entries',
                zeroRecords : emptyLabel,
                info        : 'Showing _START_ to _END_ of _TOTAL_ entries',
                infoEmpty   : 'Showing 0 to 0 of 0 entries',
                infoFiltered: '(filtered from _MAX_ total entries)'
            }
        });
    }

    var dtPending = makeTable(
        '#pendingApplicantsTable',
        'Search Pending:',
        'No pending assigned applicants found'
    );
    var dtScored = makeTable(
        '#scoredApplicantsTable',
        'Search With Scores:',
        'No scored applicants found'
    );

    // ── Build open-URL from a data object (returned by check_updates) ────
    var BASE = '<?= base_url(); ?>';
    function openUrl(d) {
        return BASE + 'EvaluatorAssigned/open/' +
            encodeURIComponent(d.record_no) + '/' +
            d.jobID + '/' +
            encodeURIComponent(d.pre_school) + '/' +
            d.appID + '/' +
            encodeURIComponent(d.record_no);
    }

    // ── Build a <tr> string for the Pending table ────────────────────────
    function buildPendingRow(d) {
        var fullName = (d.lastName + ', ' + d.firstName + ' ' + d.middleName).trim();
        var pos      = d.jobTitle + ' - ' + d.jobType;
        var status   = d.appStatus || 'Pending';
        return '<tr data-app-id="' + d.appID + '">' +
            '<td>' + _eh(d.record_no) + '</td>' +
            '<td>' + _eh(fullName)    + '</td>' +
            '<td>' + _eh(pos)         + '</td>' +
            '<td><span class="badge badge-warning">' + _eh(status) + '</span></td>' +
            '<td><a href="' + openUrl(d) + '" target="_blank" class="btn btn-primary btn-sm">' +
                '<i class="mdi mdi-open-in-new"></i> Open</a></td>' +
            '</tr>';
    }

    // ── Build a <tr> string for the With Scores table ────────────────────
    function buildScoredRow(d) {
        var fullName = (d.lastName + ', ' + d.firstName + ' ' + d.middleName).trim();
        var pos      = d.jobType + ' - ' + d.jobTitle;
        var status   = d.appStatus || 'With Scores';
        return '<tr data-app-id="' + d.appID + '">' +
            '<td>' + _eh(d.record_no) + '</td>' +
            '<td>' + _eh(fullName)    + '</td>' +
            '<td>' + _eh(pos)         + '</td>' +
            '<td><span class="badge badge-success">' + _eh(status) + '</span></td>' +
            '<td><a href="' + openUrl(d) + '" target="_blank" class="btn btn-success btn-sm">' +
                '<i class="mdi mdi-open-in-new"></i> Open</a></td>' +
            '</tr>';
    }

    function _eh(s) {
        return String(s)
            .replace(/&/g,'&amp;').replace(/</g,'&lt;')
            .replace(/>/g,'&gt;').replace(/"/g,'&quot;')
            .replace(/'/g,'&#039;');
    }

    // ── Snapshot of current known states ─────────────────────────────────
    // key = appID, value = 'pending' | 'scored'
    var stateMap = {};

    // Seed stateMap from the rendered HTML so we don't flash on first poll
    $('#pendingApplicantsTable tbody tr[data-app-id]').each(function () {
        stateMap[$(this).data('app-id')] = 'pending';
    });
    $('#scoredApplicantsTable tbody tr[data-app-id]').each(function () {
        stateMap[$(this).data('app-id')] = 'scored';
    });

    // ── Sync function: reconcile server state with current DOM ───────────
    function syncTables(data) {
        // Update summary counters
        $('#cnt-total').text(data.counts.total   || 0);
        $('#cnt-pending').text(data.counts.pending || 0);
        $('#cnt-scored').text(data.counts.scored  || 0);
        $('#cnt-pending-queries').text(data.counts.pending_queries || 0);

        var serverState = {};

        // Build a map of what the server says
        $.each(data.pending, function (_, d) { serverState[d.appID] = { state: 'pending', data: d }; });
        $.each(data.scored,  function (_, d) { serverState[d.appID] = { state: 'scored',  data: d }; });

        $.each(serverState, function (appID, info) {
            appID = parseInt(appID, 10);
            var prev = stateMap[appID];         // what we currently show
            var curr = info.state;

            if (prev === curr) return; // nothing changed for this row

            // ── pending → scored ─────────────────────────────────────────
            if (prev === 'pending' && curr === 'scored') {
                // Remove from pending table
                var pendingRow = dtPending.row('tr[data-app-id="' + appID + '"]');
                if (pendingRow.length) pendingRow.remove().draw(false);

                // Add to scored table with highlight animation
                var newRowHtml = buildScoredRow(info.data);
                var newNode    = dtScored.row.add($(newRowHtml)).draw(false).node();
                $(newNode).addClass('row-highlight');
                stateMap[appID] = 'scored';
            }

            // ── scored → pending (edge case: score cleared) ───────────────
            if (prev === 'scored' && curr === 'pending') {
                var scoredRow = dtScored.row('tr[data-app-id="' + appID + '"]');
                if (scoredRow.length) scoredRow.remove().draw(false);

                var newRowHtml2 = buildPendingRow(info.data);
                var newNode2    = dtPending.row.add($(newRowHtml2)).draw(false).node();
                $(newNode2).addClass('row-highlight');
                stateMap[appID] = 'pending';
            }

            // ── brand-new appID not seen before ───────────────────────────
            if (prev === undefined) {
                if (curr === 'pending') {
                    var html1 = buildPendingRow(info.data);
                    dtPending.row.add($(html1)).draw(false);
                } else {
                    var html2 = buildScoredRow(info.data);
                    dtScored.row.add($(html2)).draw(false);
                }
                stateMap[appID] = curr;
            }
        });
    }

    // ── Poll the server every 30 seconds ─────────────────────────────────
    var POLL_URL = BASE + 'EvaluatorAssigned/check_updates';
    var pollTimer;

    function poll() {
        $.ajax({
            url      : POLL_URL,
            method   : 'GET',
            dataType : 'json',
            success  : function (data) {
                if (data && !data.error) {
                    syncTables(data);
                }
            },
            error    : function () {
                // silent fail – next poll will retry
            }
        });
    }

    function startPolling(intervalMs) {
        clearInterval(pollTimer);
        pollTimer = setInterval(poll, intervalMs);
    }

    startPolling(30000); // every 30 s

    // ── Also poll immediately when the tab regains focus ─────────────────
    // (handles the case where the user closes the scoring tab and comes back)
    $(window).on('focus', function () { poll(); });

    // ── BroadcastChannel: scoring page can trigger an instant update ──────
    // In your scoring/rating view, add:
    //   new BroadcastChannel('scores_saved').postMessage({ appID: <id> });
    // after a successful save, and this page will update immediately.
    if (window.BroadcastChannel) {
        var bc = new BroadcastChannel('scores_saved');
        bc.onmessage = function () { poll(); };
    }

});
</script>