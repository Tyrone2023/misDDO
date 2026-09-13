<?php
/**
 * Secretariat re-tag block for the rating pages (Pages::ma, Pages::ma_staff).
 *
 * Lets the Secretariat that owns the vacancy hand this one application to
 * another evaluator without going back to the tagging screen - the case being
 * a re-evaluation, so it stays available at every stage.
 *
 * It posts to secretariat/applicant-tagging/tag, the same endpoint the
 * Applicant Evaluator Tagging screen uses, so the guard, the vacancy check and
 * the rating hand-over all stay in one place.
 *
 * Usage: $this->load->view('pages/_retag_evaluator');
 * Reads the application straight off the URL, the way the rating pages do:
 * segment(4) = jobID, segment(6) = appID.
 */

// Tagging is a Secretariat responsibility; evaluators only record decisions.
if ((string) $this->session->userdata('position') !== 'Secretariat') {
    return;
}

$rt_job_id = (int) $this->uri->segment(4);
$rt_app_id = (int) $this->uri->segment(6);
$rt_user_id = (int) ($this->session->id ?? $this->session->userdata('id'));

if ($rt_job_id <= 0 || $rt_app_id <= 0 || $rt_user_id <= 0) {
    return;
}

$this->load->model('Secretariat_model', 'secretariat');

// Only the Secretariat the vacancy is assigned to may re-tag it, exactly as on
// the tagging screen. The endpoint enforces this too.
if (!$this->secretariat->secretariat_has_vacancy($rt_user_id, $rt_job_id)) {
    return;
}

// pages/rate_applicant reuses these views with a different URL shape, so the
// appID off the URL is confirmed against the vacancy before anything is shown.
$rt_application = $this->db
    ->select('a.appID, j.sy', false)
    ->from('hris_applications a')
    ->join('hris_jobvacancy j', 'j.jobID = a.jobID')
    ->where('a.appID', $rt_app_id)
    ->where('a.jobID', $rt_job_id)
    ->get()
    ->row();

if (empty($rt_application)) {
    return;
}

$rt_current = $this->db
    ->select("ra.rater_user_id, ra.assigned_at,
        CONCAT_WS(' ', NULLIF(TRIM(u.fname), ''), NULLIF(TRIM(u.mname), ''), NULLIF(TRIM(u.lname), '')) AS evaluator_name", false)
    ->from('hris_rater_assignments ra')
    ->join('users u', 'u.id = ra.rater_user_id', 'left')
    ->where('ra.app_id', $rt_app_id)
    ->order_by('ra.id', 'desc')
    ->limit(1)
    ->get()
    ->row();

$rt_current_id = $rt_current ? (int) $rt_current->rater_user_id : 0;
$rt_evaluators = $this->secretariat->eligible_evaluators(
    (int) ($rt_application->sy ?: date('Y'))
);
?>

<div class="row">
    <div class="col-lg-12">
        <div class="card" id="retag-evaluator-card">
            <div class="card-body">
                <div class="d-flex flex-wrap align-items-center justify-content-between mb-2">
                    <h4 class="header-title mb-0">
                        <i class="mdi mdi-account-switch mr-1"></i>Evaluator Tagging
                    </h4>
                    <a href="<?= base_url('secretariat/applicant-tagging?job_id=' . $rt_job_id); ?>" class="text-muted"><small>Open the full tagging list</small></a>
                </div>

                <div class="alert <?= $rt_current_id ? 'alert-warning' : 'alert-secondary'; ?> py-2" id="retag-current-banner">
                    <strong>Current evaluator:</strong>
                    <span id="retag-current-name"><?= html_escape($rt_current_id ? ($rt_current->evaluator_name ?: 'Evaluator #' . $rt_current_id) : 'Not tagged yet'); ?></span>
                    <span id="retag-current-date"><?php if (!empty($rt_current->assigned_at)) { echo ' &middot; tagged ' . html_escape(date('M d, Y h:i A', strtotime($rt_current->assigned_at))); } ?></span>
                </div>

                <div class="alert d-none" id="retag-message" role="alert"></div>

                <?php if (empty($rt_evaluators)) : ?>
                    <div class="alert alert-warning mb-0">No user account with the Evaluator position is currently available.</div>
                <?php else : ?>
                    <form id="retag-evaluator-form" method="post" action="<?= base_url('secretariat/applicant-tagging/tag'); ?>">
                        <input type="hidden" name="app_id" value="<?= $rt_app_id; ?>">
                        <input type="hidden" name="job_id" value="<?= $rt_job_id; ?>">
                        <div class="form-row align-items-end">
                            <div class="col-lg-6 col-md-8 form-group mb-2">
                                <label class="mb-1" for="retag-evaluator-select">
                                    <?= $rt_current_id ? 'Re-tag to evaluator' : 'Tag to evaluator'; ?>
                                </label>
                                <select class="form-control evaluator-select2" id="retag-evaluator-select" name="rater_id" data-placeholder="-- select evaluator --" required>
                                    <option value="">-- select evaluator --</option>
                                    <?php foreach ($rt_evaluators as $rt_evaluator) :
                                        $rt_name = trim(implode(' ', array_filter([
                                            trim((string) ($rt_evaluator->fname ?? '')),
                                            trim((string) ($rt_evaluator->mname ?? '')),
                                            trim((string) ($rt_evaluator->lname ?? '')),
                                        ], static function ($part) { return trim((string) $part) !== ''; })));
                                        $rt_username = trim((string) ($rt_evaluator->username ?? ''));
                                        $rt_label = ($rt_name !== '' ? $rt_name : 'Evaluator #' . (int) $rt_evaluator->id)
                                            . ($rt_username !== '' ? ' — ' . $rt_username : '');
                                        ?>
                                        <option value="<?= (int) $rt_evaluator->id; ?>" <?= $rt_current_id === (int) $rt_evaluator->id ? 'selected' : ''; ?>><?= html_escape($rt_label); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-lg-6 col-md-4 form-group mb-2">
                                <button type="submit" class="btn btn-primary"><?= $rt_current_id ? 'Save re-tag' : 'Save tag'; ?></button>
                            </div>
                        </div>
                        <small class="text-muted d-block">
                            Re-tagging hands this application - and the Education to ALD rating already on it - to the evaluator you pick, so it can be re-evaluated. Scores, Interview and Written Examination entries are kept.
                        </small>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    var form = document.getElementById('retag-evaluator-form');
    if (!form || !window.fetch) return;

    var message = document.getElementById('retag-message');
    var banner = document.getElementById('retag-current-banner');
    var currentName = document.getElementById('retag-current-name');
    var currentDate = document.getElementById('retag-current-date');

    form.addEventListener('submit', function (event) {
        event.preventDefault();

        var button = form.querySelector('button[type="submit"]');
        var select = document.getElementById('retag-evaluator-select');
        if (!select.value) {
            message.className = 'alert alert-danger';
            message.textContent = 'Select an evaluator before saving.';
            return;
        }

        var originalText = button.textContent;
        button.disabled = true;
        button.textContent = 'Saving...';

        fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            credentials: 'same-origin'
        })
        .then(function (response) {
            return response.json().then(function (body) {
                if (!response.ok || !body.ok) throw new Error(body.message || 'Unable to save the evaluator tag.');
                return body;
            });
        })
        .then(function (body) {
            currentName.textContent = body.evaluator_name;
            currentDate.textContent = ' · tagged just now';
            banner.className = 'alert alert-warning py-2';
            message.className = 'alert alert-success';
            message.textContent = body.message + ' Reload the page to see the rating column as the new evaluator sees it.';
            button.textContent = 'Save re-tag';
        })
        .catch(function (error) {
            message.className = 'alert alert-danger';
            message.textContent = error.message;
            button.textContent = originalText;
        })
        .then(function () {
            button.disabled = false;
        });
    });
})();
</script>
