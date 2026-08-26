<?php
/**
 * One issued applicant document as its own A4 page, opened in a new tab from
 * the Qualified / Disqualified lists.
 *
 * The Secretariat gets the document editable in place with a toolbar to save,
 * release and print; the applicant gets the same sheet read-only. The layout
 * follows the division's own forms: the DepEd letterhead, the document body,
 * and the division footer band the source templates carry.
 *
 *   assessment - ANNEX E (qualified) / ANNEX F (disqualified),
 *                EVALUATIVE-ASSESSMENT-OF-HRMPSB
 *   letter     - LETTER TO APPLICANTS NON-COMPLIANT OF DOCS, which also carries
 *                the controlled-document reference block
 */
$mis_settings = $mis_settings ?? [];
$division = strtoupper(trim((string) ($mis_settings[0]->division ?? '')));
$region = strtoupper(trim((string) ($mis_settings[0]->Region ?? '')));
$docType = ($docType ?? 'assessment') === 'letter' ? 'letter' : 'assessment';
$isLetter = ($docType === 'letter');
$editable = !empty($editable);
$saved = !empty($saved);
$released = !empty($released);
$appId = (int) ($appId ?? 0);
$page_h = static function ($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="shortcut icon" href="<?= base_url(); ?>assets/images/hris.ico">
    <link href="<?= base_url(); ?>assets/css/renren.css" rel="stylesheet" type="text/css" />
    <link href="https://db.onlinewebfonts.com/a/0nH393RJctHgt1f2YvZvyruY" rel="stylesheet" type="text/css" />
    <link href="<?= base_url(); ?>assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <title><?= $page_h($title ?? 'Applicant Document'); ?></title>

    <style>
        @page { size: A4; margin: 12mm 0; }

        body { background: #eef1f6; margin: 0; padding: 0 0 40px; }

        /* The sheet is a page on screen too, so what you edit is what prints. */
        .ad-sheet { background: #fff; box-shadow: 0 8px 30px rgba(24, 49, 83, .13); margin: 22px auto; padding: 14mm 15mm; width: 210mm; }
        .ad-head { text-align: center; }
        .ad-head img.ad-seal { height: 78px; width: 78px; }
        .ad-head p { line-height: 1.25; margin: 4px 0 0; }
        .ad-head .rp { font-size: 13pt; }
        .ad-head .de { font-size: 19pt; }
        .ad-head .r { display: block; font-size: 10pt; letter-spacing: .04em; }
        .ad-head .ad-division { display: block; font-size: 11pt; font-weight: 700; letter-spacing: .03em; }
        .ad-rule { background: #000; height: 2px; margin: 6px 0 18px; width: 100%; }
        .ad-body { min-height: 140mm; }

        .ad-foot { margin-top: 26px; }
        .ad-foot img { display: block; width: 100%; }
        .ad-refbox { border-collapse: collapse; float: right; font-family: "Calibri", Arial, sans-serif; font-size: 9pt; margin-top: 6px; }
        .ad-refbox td { border: 1px solid #000; padding: 2px 7px; white-space: nowrap; }

        /* Toolbar: screen only. */
        .ad-bar { align-items: center; background: #fff; border-bottom: 1px solid #dfe5ee; box-shadow: 0 2px 12px rgba(24, 49, 83, .07); display: flex; flex-wrap: wrap; gap: 12px; justify-content: space-between; padding: 11px 20px; position: sticky; top: 0; z-index: 20; }
        .ad-bar-name { color: #183153; font-family: "Segoe UI", Arial, sans-serif; font-size: 14px; font-weight: 800; }
        .ad-bar-sub { color: #6b7a90; font-family: "Segoe UI", Arial, sans-serif; font-size: 12px; margin-top: 1px; }
        .ad-bar-actions { display: flex; flex-wrap: wrap; gap: 8px; }
        .ad-btn { align-items: center; border: 1px solid #d5dde8; border-radius: 8px; background: #fff; color: #3d5876; cursor: pointer; display: inline-flex; font-family: "Segoe UI", Arial, sans-serif; font-size: 13px; font-weight: 600; gap: 6px; padding: 8px 14px; text-decoration: none; }
        .ad-btn:hover { background: #f2f6fd; border-color: #b9cbe8; color: #2457d6; }
        .ad-btn-primary { background: #2457d6; border-color: #2457d6; color: #fff; }
        .ad-btn-primary:hover { background: #1c48b8; border-color: #1c48b8; color: #fff; }
        .ad-btn-go { background: #2ca66e; border-color: #2ca66e; color: #fff; }
        .ad-btn-go:hover { background: #248a5c; border-color: #248a5c; color: #fff; }
        .ad-btn-undo { background: #fff; border-color: #e2b3b3; color: #b23b3b; }
        .ad-btn-undo:hover { background: #fdeaea; border-color: #d99b9b; color: #932f2f; }
        .ad-btn[disabled] { cursor: default; opacity: .6; }
        .ad-pill { border-radius: 20px; font-family: "Segoe UI", Arial, sans-serif; font-size: 11px; font-weight: 700; padding: 5px 10px; }
        .ad-pill-draft { background: #fff3d8; color: #8a5b00; }
        .ad-pill-saved { background: #e8efff; color: #2457d6; }
        .ad-pill-released { background: #e2f6eb; color: #197447; }
        .ad-hint { background: #eef6ff; border: 1px solid #d9eaff; border-radius: 9px; color: #31577d; font-family: "Segoe UI", Arial, sans-serif; font-size: 12px; margin: 14px auto 0; max-width: 210mm; padding: 10px 14px; }
        .ad-flash { border-radius: 9px; font-family: "Segoe UI", Arial, sans-serif; font-size: 13px; margin: 12px auto 0; max-width: 210mm; padding: 10px 14px; }
        .ad-flash-ok { background: #e2f6eb; border: 1px solid #bfe6d1; color: #197447; }
        .ad-flash-bad { background: #fdeaea; border: 1px solid #f2c5c5; color: #a52c2c; }

        @media (max-width: 900px) {
            .ad-sheet { padding: 16px; width: auto; }
            .ad-hint, .ad-flash { max-width: none; }
        }

        @media print {
            html, body { background: #fff; padding: 0; width: 210mm; }
            .ad-bar, .ad-hint, .ad-flash { display: none !important; }
            .ad-sheet { box-shadow: none; margin: 0; padding: 0; width: 100%; }
        }
    </style>
</head>

<body>
    <div class="ad-bar">
        <div>
            <div class="ad-bar-name"><?= $page_h($title ?? 'Applicant Document'); ?></div>
            <div class="ad-bar-sub"><?= $page_h($applicant ?? ''); ?></div>
        </div>
        <div class="ad-bar-actions">
            <?php if ($editable) : ?>
                <span id="ad-state" class="ad-pill <?= $released ? 'ad-pill-released' : ($saved ? 'ad-pill-saved' : 'ad-pill-draft'); ?>">
                    <?= $released ? 'Released' : ($saved ? 'Saved' : 'Draft'); ?>
                </span>
            <?php endif; ?>
            <button type="button" class="ad-btn" onclick="window.print();"><i class="mdi mdi-printer"></i>Print / PDF</button>
            <?php if ($editable) : ?>
                <button type="button" class="ad-btn <?= $released ? 'ad-btn-undo' : 'ad-btn-go'; ?>" id="ad-release">
                    <i class="mdi <?= $released ? 'mdi-undo-variant' : 'mdi-send-outline'; ?>"></i><span><?= $released ? 'Withdraw from applicant' : 'Release to applicant'; ?></span>
                </button>
                <button type="button" class="ad-btn ad-btn-primary" id="ad-save"><i class="mdi mdi-content-save-outline"></i>Save</button>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($editable) : ?>
        <div class="ad-hint">
            Click any shaded value or table cell to edit it &mdash; the <em>CSC-approved QS</em> column is left blank for you to fill in.
            <strong>Save</strong> keeps your changes, <strong>Release</strong> makes this document available to the applicant from their own application list.
        </div>
    <?php endif; ?>

    <div id="ad-flash" class="ad-flash" style="display:none"></div>

    <div class="ad-sheet">
        <div class="ad-head">
            <img class="ad-seal" src="<?= base_url(); ?>assets/images/report/ke.png" alt="">
            <p>
                <span class="rp">Republic of the Philippines</span><br />
                <span class="de">Department of Education</span><br />
                <span class="r"><?= $page_h($region); ?></span>
                <span class="ad-division">SCHOOLS DIVISION OF <?= $page_h($division); ?></span>
            </p>
        </div>

        <div class="ad-rule"></div>

        <div class="ad-body">
            <?php $this->load->view('pages/_secretariat_assessment_doc', [
                'doc'      => $doc ?? [],
                'docType'  => $docType,
                'editable' => $editable,
                'esig'     => $esig ?? '',
            ]); ?>
        </div>

        <div class="ad-foot">
            <img src="<?= base_url(); ?>assets/images/report/<?= $isLetter ? 'ddo_footer_letter.png' : 'ddo_footer_annex.png'; ?>" alt="">

            <?php if ($isLetter) : ?>
                <table class="ad-refbox">
                    <tr>
                        <td>Doc. Ref. Code</td>
                        <td>PAWIM-F-019</td>
                        <td>Rev</td>
                        <td>00</td>
                    </tr>
                    <tr>
                        <td>Effectivity</td>
                        <td>09.12.22</td>
                        <td>Page</td>
                        <td>1 of 1</td>
                    </tr>
                </table>
            <?php endif; ?>
        </div>
    </div>

<?php if ($editable) : ?>
<script>
(function () {
    var SAVE_URL = '<?= base_url('secretariat/assessment/save'); ?>';
    var RELEASE_URL = '<?= base_url('secretariat/assessment/release'); ?>';
    var APP_ID = '<?= $appId; ?>';
    var DOC_TYPE = '<?= $page_h($docType); ?>';
    var released = <?= $released ? 'true' : 'false'; ?>;

    function el(id) { return document.getElementById(id); }

    function flash(ok, message) {
        var box = el('ad-flash');
        if (!box) return;
        if (!message) { box.style.display = 'none'; box.textContent = ''; return; }
        box.className = 'ad-flash ' + (ok ? 'ad-flash-ok' : 'ad-flash-bad');
        box.textContent = message;
        box.style.display = '';
    }

    function setState(saved) {
        var pill = el('ad-state');
        if (pill) {
            pill.className = 'ad-pill ' + (released ? 'ad-pill-released' : (saved ? 'ad-pill-saved' : 'ad-pill-draft'));
            pill.textContent = released ? 'Released' : (saved ? 'Saved' : 'Draft');
        }

        var release = el('ad-release');
        if (release) {
            release.className = 'ad-btn ' + (released ? 'ad-btn-undo' : 'ad-btn-go');
            release.querySelector('i').className = 'mdi ' + (released ? 'mdi-undo-variant' : 'mdi-send-outline');
            release.querySelector('span').textContent = released ? 'Withdraw from applicant' : 'Release to applicant';
        }
    }

    /* The Position Applied for cell is merged down the whole table, so adding or
       removing a criterion has to move it and re-span it. */
    function fixMergedCell() {
        var table = document.querySelector('[data-doc-type="assessment"] tbody[data-rows]');
        if (!table) return;

        var rows = table.querySelectorAll('[data-row]');
        var merged = table.querySelector('[data-itemno]');
        if (!rows.length || !merged) return;

        if (merged.parentNode !== rows[0]) {
            rows[0].insertBefore(merged, rows[0].firstChild);
        }
        merged.setAttribute('rowspan', rows.length);
    }

    /* Read the edited document back out of the page as plain text. */
    function collect() {
        var root = document.querySelector('.ad-doc');
        if (!root) return null;

        var body = {};
        Array.prototype.forEach.call(root.querySelectorAll('[data-field]'), function (node) {
            body[node.getAttribute('data-field')] = (node.innerText || '').trim();
        });

        var items = [];
        Array.prototype.forEach.call(root.querySelectorAll('[data-rows] [data-row]'), function (row) {
            var cells = {};
            Array.prototype.forEach.call(row.querySelectorAll('[data-cell]'), function (node) {
                cells[node.getAttribute('data-cell')] = (node.innerText || '').trim();
            });
            items.push(cells);
        });
        body.items = items;

        return body;
    }

    function post(url, params) {
        var form = new URLSearchParams();
        Object.keys(params).forEach(function (key) { form.append(key, params[key]); });

        return fetch(url, {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
            body: form.toString()
        }).then(function (response) {
            return response.json().catch(function () {
                throw new Error('The server returned an unexpected response.');
            }).then(function (payload) {
                if (!response.ok || !payload.ok) {
                    throw new Error(payload.message || 'The request could not be completed.');
                }
                return payload;
            });
        });
    }

    function saveDocument() {
        var body = collect();
        if (!body) { return Promise.reject(new Error('Nothing to save.')); }

        return post(SAVE_URL, { app_id: APP_ID, doc: DOC_TYPE, body: JSON.stringify(body) })
            .then(function (payload) {
                dirty = false;
                setState(true);
                return payload;
            });
    }

    /* Row tools inside the document. */
    document.addEventListener('click', function (event) {
        if (!event.target.closest) return;

        var remove = event.target.closest('[data-remove-row]');
        if (remove) {
            var row = remove.closest('[data-row]');
            if (!row || !row.parentNode) return;
            if (row.parentNode.querySelectorAll('[data-row]').length < 2) return;

            var merged = row.querySelector('[data-itemno]');
            if (merged) {
                var next = row.nextElementSibling;
                if (next) { next.insertBefore(merged, next.firstChild); }
            }

            row.parentNode.removeChild(row);
            fixMergedCell();
            dirty = true;
            return;
        }

        var add = event.target.closest('[data-add-row]');
        if (add) {
            var container = document.querySelector('.ad-doc [data-rows]');
            var rows = container ? container.querySelectorAll('[data-row]') : [];
            if (!container || !rows.length) return;

            var clone = rows[rows.length - 1].cloneNode(true);
            var carried = clone.querySelector('[data-itemno]');
            if (carried && carried.parentNode) { carried.parentNode.removeChild(carried); }
            Array.prototype.forEach.call(clone.querySelectorAll('[data-cell]'), function (node) {
                node.textContent = '';
            });
            container.appendChild(clone);
            fixMergedCell();
            dirty = true;
        }
    });

    var dirty = false;
    document.addEventListener('input', function (event) {
        if (event.target.closest && event.target.closest('.ad-doc')) { dirty = true; }
    });

    window.addEventListener('beforeunload', function (event) {
        if (!dirty) return;
        event.preventDefault();
        event.returnValue = '';
    });

    el('ad-save').addEventListener('click', function () {
        var button = this;
        button.disabled = true;
        flash(true, '');
        saveDocument()
            .then(function (payload) { flash(true, payload.message); })
            .catch(function (error) { flash(false, error.message); })
            .then(function () { button.disabled = false; });
    });

    el('ad-release').addEventListener('click', function () {
        var button = this;
        var wanted = !released;
        button.disabled = true;
        flash(true, '');

        // Releasing always publishes what is on screen, so the edits and the
        // release cannot drift apart.
        saveDocument()
            .then(function () {
                return post(RELEASE_URL, { app_id: APP_ID, doc: DOC_TYPE, released: wanted ? 1 : 0 });
            })
            .then(function (payload) {
                released = !!payload.released;
                setState(true);
                flash(true, payload.message);
            })
            .catch(function (error) { flash(false, error.message); })
            .then(function () { button.disabled = false; });
    });

    fixMergedCell();
})();
</script>
<?php endif; ?>
</body>

</html>
