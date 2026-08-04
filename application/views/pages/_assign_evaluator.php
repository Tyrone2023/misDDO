<?php
/**
 * Optional single-evaluator assignment block for the Remarks modals.
 *
 * Renders the "Current evaluator" banner plus the "Assign Evaluator (optional)"
 * dropdown. Must be included INSIDE the remarks <form>; it posts `rater_id`,
 * which Pages::Unqualified, Pages::Unqualified_none, Pages::Unqualified_promotion
 * and Pages::Unqualifiededit consume when the remark is Qualified.
 *
 * Usage: $this->load->view('pages/_assign_evaluator', ['ae_app_id' => $aa->appID]);
 *
 * @var mixed $ae_app_id application appID the assignment is tied to
 */

$ae_app_id = isset($ae_app_id) ? $ae_app_id : null;

// Eligible assignees: Evaluators (egroup 1) plus ASDS users
$ae_raters = $this->db
    ->select('id,fname,mname,lname,username')
    ->from('users')
    ->group_start()
        ->group_start()
            ->where('position', 'Evaluator')
            ->where('egroup', 1)
        ->group_end()
        ->or_where('position', 'asds')
    ->group_end()
    ->order_by('lname', 'asc')
    ->order_by('fname', 'asc')
    ->get()
    ->result();

// Current single-assignment info (latest)
$ae_current = null;
if (!empty($ae_app_id)) {
    $ae_current = $this->db
        ->select('ra.assigned_at, ra.rater_user_id, u.fname, u.mname, u.lname, u.username')
        ->from('hris_rater_assignments ra')
        ->join('users u', 'u.id = ra.rater_user_id', 'left')
        ->where('ra.app_id', $ae_app_id)
        ->order_by('ra.assigned_at', 'desc')
        ->limit(1)
        ->get()
        ->row();
}

$ae_current_id = $ae_current ? (int) $ae_current->rater_user_id : null;

// The assignee may no longer be an egroup 1 Evaluator. Keep them in the list so
// the browser cannot silently pre-select somebody else and reassign on submit.
$ae_current_listed = false;
foreach ($ae_raters as $r) {
    if ($ae_current_id && (int) $r->id === $ae_current_id) {
        $ae_current_listed = true;
        break;
    }
}

// Some evaluator records keep the whole name in fname, so drop the separator
// rather than printing a dangling ", ".
$ae_name = function ($row) {
    $lname = trim((string) ($row->lname ?? ''));
    $rest = trim(trim((string) ($row->fname ?? '')) . ' ' . trim((string) ($row->mname ?? '')));
    if ($lname !== '' && $rest !== '') {
        return $lname . ', ' . $rest;
    }
    return $lname !== '' ? $lname : $rest;
};

$ae_label = function ($row) use ($ae_name) {
    $label = $ae_name($row);
    if (!empty($row->username)) {
        $label .= ' (' . $row->username . ')';
    }
    return html_escape($label);
};
?>

<?php if($ae_current): ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="alert alert-warning py-2">
                <strong>Current evaluator:</strong>
                <?= $ae_label($ae_current); ?>
                <?php if(!empty($ae_current->assigned_at)) echo ' · assigned '.date('M d, Y h:i A', strtotime($ae_current->assigned_at)); ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-lg-12">
        <h4 class="header-title">Assign Evaluator <span class="text-muted">(optional)</span></h4>
        <div class="form-group">
            <select class="form-control evaluator-select2" name="rater_id" data-placeholder="-- select evaluator --">
                <?php if(empty($ae_current_id)): ?>
                    <option value="">-- select evaluator --</option>
                <?php elseif(!$ae_current_listed): ?>
                    <option value="<?= (int) $ae_current_id; ?>" selected><?= $ae_label($ae_current); ?></option>
                <?php endif; ?>
                <?php foreach($ae_raters as $r):
                    $selected = ($ae_current_id && (int)$r->id === $ae_current_id) ? 'selected' : ''; ?>
                    <option value="<?= (int) $r->id; ?>" <?= $selected; ?>><?= $ae_label($r); ?></option>
                <?php endforeach; ?>
            </select>
            <small class="text-muted">If set to Qualified, the selected evaluator will be assigned.</small>
        </div>
    </div>
</div>
