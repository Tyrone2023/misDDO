<?php
/**
 * TESDA NC dialog and the shared script for the "Include in IER" controls.
 * Loaded once by Pages::ma() when $ier_can_include is true.
 */
$ierAppID = (int) $this->uri->segment(6);
$ierJobTitle = trim((string) ($this->Common->one_cond_row('hris_jobvacancy', 'jobID', (int) $this->uri->segment(4))->jobTitle ?? 'this vacancy'));
?>
<div class="modal fade" id="ierTesdaModal" tabindex="-1" role="dialog" aria-labelledby="ierTesdaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form class="modal-content" id="ierTesdaForm" novalidate>
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white" id="ierTesdaModalLabel">
                    <i class="mdi mdi-certificate-outline mr-1"></i>Include TESDA NC in IER Training
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="mb-3">
                    The TESDA National Certificate will be listed under <strong>Training</strong> in the IER for
                    <strong><?= html_escape($ierJobTitle); ?></strong>. All fields are optional.
                </p>
                <div class="form-row">
                    <div class="form-group col-sm-6">
                        <label for="ierTesdaFrom">Inclusive date from</label>
                        <input type="date" class="form-control" id="ierTesdaFrom" name="date_from">
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="ierTesdaTo">Inclusive date to</label>
                        <input type="date" class="form-control" id="ierTesdaTo" name="date_to">
                    </div>
                </div>
                <div class="form-group mb-0">
                    <label for="ierTesdaHours">Total hours</label>
                    <input type="number" class="form-control" id="ierTesdaHours" name="hours" min="0" max="99999" step="0.5" inputmode="decimal">
                    <small class="form-text text-muted">Calculated as 8 hours per day from the inclusive dates. You can change it.</small>
                </div>
                <div class="alert alert-danger mt-3 mb-0 py-2" id="ierTesdaError" role="alert" hidden></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" id="ierTesdaSubmit">
                    <i class="mdi mdi-check mr-1"></i>Confirm and include
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    'use strict';
    var endpoint = <?= json_encode(base_url('Pages/save_ier_inclusion')); ?>;
    var appID = <?= $ierAppID; ?>;
    var jobTitle = <?= json_encode($ierJobTitle); ?>;
    var form = document.getElementById('ierTesdaForm');
    var from = document.getElementById('ierTesdaFrom');
    var to = document.getElementById('ierTesdaTo');
    var hours = document.getElementById('ierTesdaHours');
    var error = document.getElementById('ierTesdaError');
    var submit = document.getElementById('ierTesdaSubmit');
    var hoursEdited = false;

    function send(fields) {
        var body = new URLSearchParams();
        body.append('app_id', appID);
        Object.keys(fields).forEach(function (key) { body.append(key, fields[key]); });
        return fetch(endpoint, {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'},
            credentials: 'same-origin',
            body: body.toString()
        }).then(function (response) {
            return response.json().catch(function () { return {}; }).then(function (result) {
                if (!response.ok || !result.ok) throw new Error(result.message || 'Unable to update the IER.');
                return result;
            });
        });
    }

    // 8 hours per inclusive calendar day, until the user types their own total.
    function autoHours() {
        if (hoursEdited || !from.value || !to.value) return;
        var days = Math.round((new Date(to.value) - new Date(from.value)) / 86400000) + 1;
        if (days > 0) hours.value = days * 8;
    }

    document.addEventListener('click', function (event) {
        var include = event.target.closest('.js-ier-include');
        var remove = event.target.closest('.js-ier-remove');
        var tesda = event.target.closest('.js-ier-tesda-open');

        if (include) {
            if (!window.confirm('Include the ' + include.dataset.label + ' in the IER Education for ' + jobTitle + '?')) return;
            include.disabled = true;
            send({item: include.dataset.item, action: 'include'})
                .then(function () { window.location.reload(); })
                .catch(function (err) { include.disabled = false; window.alert(err.message); });
        } else if (remove) {
            if (!window.confirm('Remove the ' + remove.dataset.label + ' from the IER for ' + jobTitle + '?')) return;
            remove.disabled = true;
            send({item: remove.dataset.item, action: 'remove'})
                .then(function () { window.location.reload(); })
                .catch(function (err) { remove.disabled = false; window.alert(err.message); });
        } else if (tesda) {
            from.value = tesda.dataset.dateFrom || '';
            to.value = tesda.dataset.dateTo || '';
            hours.value = tesda.dataset.hours || '';
            hoursEdited = hours.value !== '';
            error.hidden = true;
            window.jQuery('#ierTesdaModal').modal('show');
        }
    });

    from.addEventListener('change', autoHours);
    to.addEventListener('change', autoHours);
    hours.addEventListener('input', function () { hoursEdited = hours.value !== ''; });

    form.addEventListener('submit', function (event) {
        event.preventDefault();
        error.hidden = true;
        if (from.value && to.value && from.value > to.value) {
            error.textContent = 'The "from" date must not be after the "to" date.';
            error.hidden = false;
            return;
        }
        submit.disabled = true;
        send({item: 'tesda', action: 'include', date_from: from.value, date_to: to.value, hours: hours.value})
            .then(function () { window.location.reload(); })
            .catch(function (err) {
                submit.disabled = false;
                error.textContent = err.message;
                error.hidden = false;
            });
    });
});
</script>
