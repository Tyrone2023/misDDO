<?php defined('BASEPATH') OR exit('No direct script access allowed');
if (!function_exists('h')) {
    function h($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); }
}
$report = isset($report) && is_array($report) ? $report : array();
$months = array(1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December');
$monthIndex = (int) (isset($report['report_month']) ? $report['report_month'] : 0);
$period = (isset($months[$monthIndex]) ? $months[$monthIndex] : 'Month ' . $monthIndex) . ' ' . (int) (isset($report['report_year']) ? $report['report_year'] : 0);
$jsonFlags = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT;
?>
<style>
    :root {
        --rai-primary: #1f3a5f;
        --rai-primary-2: #274b7a;
        --rai-accent: #1abc9c;
        --rai-bg: #f4f7fb;
        --rai-border: #e5ecf5;
        --rai-text: #25364a;
        --rai-muted: #7b8794;
        --rai-soft: #eef5ff;
    }

    .content-page { background: var(--rai-bg); min-height: 100vh; }
    .rai-page-shell { padding-bottom: 24px; }

    .rai-hero {
        position: relative;
        overflow: hidden;
        border-radius: 18px;
        padding: 22px 24px;
        margin-bottom: 18px;
        background: linear-gradient(135deg, var(--rai-primary), var(--rai-primary-2));
        box-shadow: 0 14px 35px rgba(31, 58, 95, .18);
        color: #fff;
    }
    .rai-hero:before {
        content: "";
        position: absolute;
        width: 230px; height: 230px;
        border-radius: 50%;
        right: -80px; top: -95px;
        background: rgba(255,255,255,.10);
    }
    .rai-hero:after {
        content: "";
        position: absolute;
        width: 160px; height: 160px;
        border-radius: 50%;
        right: 90px; bottom: -100px;
        background: rgba(26,188,156,.22);
    }
    .rai-hero-content {
        position: relative;
        z-index: 2;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }
    .rai-title-block h4 { color: #fff; font-size: 1.25rem; font-weight: 800; letter-spacing: .2px; margin: 0 0 5px; }
    .rai-title-block p { color: rgba(255,255,255,.82); margin: 0; max-width: 760px; font-size: .86rem; line-height: 1.45; }
    .rai-hero-icon {
        width: 54px; height: 54px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255,255,255,.14);
        border: 1px solid rgba(255,255,255,.18);
        flex: 0 0 auto;
    }
    .rai-hero-icon i { font-size: 28px; color: #fff; }

    .rai-card { border: 0; border-radius: 16px; box-shadow: 0 8px 26px rgba(31, 58, 95, .08); overflow: hidden; margin-bottom: 18px; }
    .rai-card .card-body { padding: 18px; }

    .rai-section-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 14px;
        flex-wrap: wrap;
    }
    .rai-section-title {
        margin: 0;
        font-size: .94rem;
        font-weight: 800;
        color: var(--rai-text);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .rai-section-title i {
        width: 32px; height: 32px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: var(--rai-soft);
        color: var(--rai-primary);
        font-size: 17px;
    }
    .rai-help-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: var(--rai-muted);
        font-size: .76rem;
        background: #f8fafd;
        border: 1px solid var(--rai-border);
        padding: 6px 10px;
        border-radius: 999px;
    }

    .rai-summary { display: grid; grid-template-columns: repeat(4, minmax(0,1fr)); gap: 12px; }
    .rai-summary div { padding: 10px 12px; background: #f8fbff; border: 1px solid var(--rai-border); border-radius: 10px; font-size: .82rem; color: var(--rai-text); }
    .rai-summary label { display: block; font-size: .62rem; text-transform: uppercase; letter-spacing: .5px; color: var(--rai-muted); font-weight: 800; margin-bottom: 3px; }

    .rai-table-wrap { overflow-x: auto; }
    .rai-table { border-collapse: collapse; width: 100%; font-size: .82rem; }
    .rai-table th, .rai-table td { padding: 10px 8px; border-bottom: 1px solid var(--rai-border); text-align: left; vertical-align: middle; }
    .rai-table th { background: #f8fbff; color: #516070; font-size: .68rem; text-transform: uppercase; letter-spacing: .35px; font-weight: 800; }
    .rai-table .rai-tools { white-space: nowrap; }
    .rai-pill { display: inline-block; background: var(--rai-soft); color: var(--rai-primary); border-radius: 999px; padding: 3px 9px; font-size: .68rem; font-weight: 700; }
    .rai-count-pill { display: inline-block; background: var(--rai-soft); color: var(--rai-primary); border-radius: 999px; padding: 4px 12px; font-size: .74rem; font-weight: 800; }

    .rai-empty-state {
        margin: 18px;
        border: 1px dashed #cbd8e7;
        background: #f8fbff;
        color: #526173;
        border-radius: 14px;
        padding: 30px 18px;
        text-align: center;
    }
    .rai-empty-state i { font-size: 36px; color: var(--rai-primary); opacity: .78; display: block; margin-bottom: 8px; }
    .rai-empty-state strong { display: block; color: var(--rai-text); font-size: .94rem; margin-bottom: 4px; }
    .rai-empty-state span { font-size: .8rem; }

    .rai-modal-backdrop { position: fixed; inset: 0; background: rgba(15,29,47,.58); z-index: 2000; display: none; align-items: center; justify-content: center; padding: 20px; }
    .rai-modal-backdrop.is-open { display: flex; }
    .rai-modal-box { background: #fff; border-radius: 14px; width: min(940px, 100%); max-height: 92vh; display: flex; flex-direction: column; box-shadow: 0 20px 80px rgba(0,0,0,.22); }
    .rai-modal-top, .rai-modal-foot { padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; gap: 12px; border-bottom: 1px solid var(--rai-border); }
    .rai-modal-foot { border-top: 1px solid var(--rai-border); border-bottom: 0; }
    .rai-modal-body { overflow: auto; padding: 20px; }
    .rai-section-label { color: var(--rai-primary); border-bottom: 1px solid var(--rai-border); font-weight: 800; font-size: .8rem; margin: 20px 0 13px; padding-bottom: 8px; }

    .rai-note { border-left: 3px solid #e7b150; padding: 10px 13px; background: #fffaf0; border-radius: 3px; color: #876129; font-size: .78rem; }
    .rai-search-row { display: grid; grid-template-columns: minmax(0,2fr) 1fr 1fr auto; gap: 10px; margin-bottom: 13px; }
    .rai-picker-row { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 12px; border-bottom: 1px solid var(--rai-border); }
    .rai-picker-row small { display: block; color: var(--rai-muted); margin-top: 3px; }
    .rai-link { background: none; border: 0; color: var(--rai-primary-2); cursor: pointer; padding: 3px 5px; font-size: .8rem; }
    .rai-error { color: #b23a38; font-size: .82rem; padding: 10px; }
    .rai-hide { display: none !important; }
    .rai-muted { color: var(--rai-muted); font-size: .76rem; }
    .rai-grid { display: grid; grid-template-columns: repeat(4, minmax(0,1fr)); gap: 12px; }
    .rai-col2 { grid-column: span 2; }
    .rai-col4 { grid-column: span 4; }
    .rai-field label { display: block; font-weight: 800; font-size: .62rem; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 5px; color: var(--rai-muted); }
    .rai-field input, .rai-field select, .rai-field textarea { width: 100%; border: 1px solid #d6dfeb; border-radius: 7px; padding: 8px 10px; min-height: 36px; background: #fff; color: var(--rai-text); font-size: .8rem; }
    .rai-actions { display: flex; gap: 4px; flex-wrap: wrap; align-items: center; }
    .rai-icon-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 30px; height: 30px;
        border-radius: 8px;
        padding: 0;
        border: 1px solid transparent;
        background: #f4f7fb;
        color: var(--rai-primary);
        font-size: 15px;
        line-height: 1;
        cursor: pointer;
        transition: background .15s, color .15s, border-color .15s;
    }
    .rai-icon-btn:hover { background: var(--rai-soft); border-color: var(--rai-border); color: var(--rai-primary); text-decoration: none; }
    .rai-icon-btn.is-danger { color: #c0392b; }
    .rai-icon-btn.is-danger:hover { background: #fdecea; border-color: #f5c6c0; color: #a93226; }
    .rai-icon-btn.is-secondary { color: #516070; background: #eef3f9; border-color: #e1e8f1; }
    .rai-icon-btn.is-secondary:hover { background: #e2eaf3; border-color: #d3dde9; color: #3a4757; }
    .rai-icon-btn:disabled { opacity: .4; cursor: not-allowed; }

    @media (max-width: 991.98px) {
        .rai-hero-content { align-items: flex-start; }
        .rai-hero-icon { display: none; }
        .rai-section-head { align-items: flex-start; }
    }
    @media (max-width: 850px) {
        .rai-grid, .rai-summary { grid-template-columns: repeat(2, minmax(0,1fr)); }
        .rai-search-row { grid-template-columns: 1fr 1fr; }
        .rai-col4 { grid-column: span 2; }
    }
    @media (max-width: 767.98px) {
        .rai-hero { padding: 18px; border-radius: 14px; }
        .rai-hero-content { flex-direction: column; }
        .rai-title-block h4 { font-size: 1.1rem; }
        .rai-card .card-body { padding: 14px; }
    }
    @media (max-width: 540px) {
        .rai-grid, .rai-search-row, .rai-summary { grid-template-columns: 1fr; }
        .rai-col2, .rai-col4 { grid-column: auto; }
    }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid rai-page-shell">

            <div class="rai-hero">
                <div class="rai-hero-content">
                    <div class="rai-title-block">
                        <h4>Manage RAI Report — <?= h($period) ?></h4>
                        <p>Add applicants from the system or manually, then preview the CSC form.</p>
                    </div>

                    <div class="d-flex align-items-center flex-wrap justify-content-end" style="gap:10px;">
                        <a href="<?= h($indexUrl) ?>" class="btn btn-light btn-sm font-weight-bold" style="border-radius:10px;">
                            <i class="ph ph-arrow-left mr-1"></i>All Reports
                        </a>
                        <a href="<?= h($editUrl) ?>" class="btn btn-light btn-sm font-weight-bold" style="border-radius:10px;">
                            <i class="ph ph-pencil-simple mr-1"></i>Edit Report Info
                        </a>
                        <div class="rai-hero-icon">
                            <i class="ph ph-users"></i>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($this->session->flashdata('success')): ?>
                <div class="alert alert-success" role="alert"><i class="ph ph-check-circle mr-1"></i><?= h($this->session->flashdata('success')) ?></div>
            <?php endif; ?>
            <?php if ($this->session->flashdata('danger')): ?>
                <div class="alert alert-danger" role="alert"><i class="ph ph-warning-circle mr-1"></i><?= h($this->session->flashdata('danger')) ?></div>
            <?php endif; ?>

            <form id="raiPreviewForm" method="post" action="<?= h($previewUrl) ?>" target="_blank">
                <input type="hidden" id="raiToken" name="rai_token" value="<?= h($raiToken) ?>">
                <input type="hidden" name="payload" id="raiPayload">
            </form>

            <div class="card rai-card">
                <div class="card-body">
                    <div class="rai-section-head">
                        <h5 class="rai-section-title">
                            <i class="ph ph-clipboard-text"></i>
                            Report Information
                        </h5>
                    </div>
                    <div class="rai-summary">
                        <div><label>Agency</label><?= h(isset($report['agency_name']) ? $report['agency_name'] : '') ?></div>
                        <div><label>Division</label><?= h(isset($report['division_name']) ? $report['division_name'] : '') ?></div>
                        <div><label>CSC Resolution No.</label><?= h(isset($report['csc_resolution_no']) && $report['csc_resolution_no'] !== '' ? $report['csc_resolution_no'] : '—') ?></div>
                        <div><label>Resolution Date</label><?= h(isset($report['csc_resolution_date']) && $report['csc_resolution_date'] !== '' ? $report['csc_resolution_date'] : '—') ?></div>
                        <div><label>Certifying Officer</label><?= h(isset($report['certifying_officer']) && $report['certifying_officer'] !== '' ? $report['certifying_officer'] : '—') ?></div>
                        <div><label>Appointing Officer</label><?= h(isset($report['appointing_officer']) && $report['appointing_officer'] !== '' ? $report['appointing_officer'] : '—') ?></div>
                        <div style="grid-column:span 2"><label>Remarks</label><?= h(isset($report['remarks']) && $report['remarks'] !== '' ? $report['remarks'] : '—') ?></div>
                    </div>
                </div>
            </div>

            <div class="card rai-card">
                <div class="card-body">
                    <div class="rai-section-head">
                        <div>
                            <h5 class="rai-section-title">
                                <i class="ph ph-users"></i>
                                Applicants
                                <span id="raiCount" class="rai-count-pill">0 selected</span>
                            </h5>
                            <div class="rai-muted" style="margin-top:6px;">Select issued appointments or add a report-only manual applicant. <span id="raiSaveStatus">Saved</span></div>
                        </div>
                        <div class="rai-actions">
                            <button type="button" class="btn btn-primary btn-sm font-weight-bold" id="raiOpenPicker" style="border-radius:10px;">
                                <i class="ph ph-magnifying-glass mr-1"></i>Select from RQA
                            </button>
                            <button type="button" class="btn btn-outline-primary btn-sm font-weight-bold" id="raiAddManual" style="border-radius:10px;">
                                <i class="ph ph-user-plus mr-1"></i>Manual Applicant
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm font-weight-bold" id="raiPreview" style="border-radius:10px;">
                                <i class="ph ph-printer mr-1"></i>Preview
                            </button>
                        </div>
                    </div>

                    <div class="rai-table-wrap">
                        <table class="rai-table">
                            <thead>
                                <tr>
                                    <th style="width:45px">No.</th>
                                    <th>Applicant</th>
                                    <th>Position / Item</th>
                                    <th>Date Issued</th>
                                    <th>Nature / Classification</th>
                                    <th>Source</th>
                                    <th class="rai-tools">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="raiApplicantRows"></tbody>
                        </table>
                    </div>

                    <div class="rai-empty-state" id="raiEmpty">
                        <i class="ph ph-user-list"></i>
                        <strong>No applicants selected.</strong>
                        <span>Use “Select from RQA” to import appointed records, or add a manual applicant.</span>
                    </div>

                    <div class="rai-note" style="margin-top:14px;">
                        Applicant changes are saved to this report automatically. The preview reflects the list as currently shown.
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<div class="rai-modal-backdrop" id="raiPickerModal" role="dialog" aria-modal="true" aria-label="Select appointed applicants">
    <div class="rai-modal-box">
        <div class="rai-modal-top">
            <strong>Select Applicants — RQA Appointed List</strong>
            <button type="button" class="btn btn-light btn-sm font-weight-bold" data-close style="border-radius:10px;">Close</button>
        </div>
        <div class="rai-modal-body">
            <div class="rai-search-row">
                <input class="form-control" id="raiSearch" placeholder="Search by name, position, item or record ID">
                <select class="form-control" id="raiSearchMonth"><option value="0">All months</option><?php foreach($months as $number=>$name): ?><option value="<?= $number ?>"><?= $name ?></option><?php endforeach ?></select>
                <input class="form-control" type="number" id="raiSearchYear" min="2000" max="2100" placeholder="Year">
                <button type="button" class="btn btn-primary btn-sm font-weight-bold" id="raiSearchButton" style="border-radius:10px;">Search</button>
            </div>
            <div id="raiPickerResults" class="rai-muted">Search to load eligible appointed applicants.</div>
            <div id="raiPickerPager" class="rai-actions" style="margin-top:14px"></div>
        </div>
        <div class="rai-modal-foot">
            <span class="rai-muted">Only confirmed appointed events are shown.</span>
            <button type="button" class="btn btn-light btn-sm font-weight-bold" data-close style="border-radius:10px;">Done</button>
        </div>
    </div>
</div>

<div class="rai-modal-backdrop" id="raiEditorModal" role="dialog" aria-modal="true" aria-label="Edit RAI applicant">
    <div class="rai-modal-box">
        <div class="rai-modal-top">
            <strong id="raiEditorTitle">Applicant Details</strong>
            <button type="button" class="btn btn-light btn-sm font-weight-bold" data-close style="border-radius:10px;">Close</button>
        </div>
        <form id="raiEditorForm">
            <div class="rai-modal-body">
                <div class="rai-note" id="raiSourceNote">Changes affect this report only.</div>
                <div id="raiEditorFields"></div>
            </div>
            <div class="rai-modal-foot">
                <span class="rai-muted">Blank values can be completed later.</span>
                <div class="rai-actions">
                    <button type="button" class="btn btn-light btn-sm font-weight-bold" data-close style="border-radius:10px;">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm font-weight-bold" style="border-radius:10px;">Apply to Report</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
(function () {
'use strict';
const candidateUrl = <?= json_encode($candidateUrl, $jsonFlags) ?>;
const detailUrl = <?= json_encode(rtrim($detailUrl, '/'), $jsonFlags) ?>;
const saveUrl = <?= json_encode($saveUrl, $jsonFlags) ?>;
const reportHeader = <?= json_encode($report, $jsonFlags) ?>;
let items = <?= json_encode(is_array($applicants) ? $applicants : array(), $jsonFlags) ?>;
let editingIndex = null;
let pickerPage = 1;
let saveTimer = null;
const $ = id => document.getElementById(id);
const esc = value => String(value == null ? '' : value).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
const sections = [
 ['Personal Information', [['last_name','Last Name'],['first_name','First Name'],['middle_name','Middle Name'],['name_extension','Name Extension'],['sex','Sex','sex'],['pwd','PWD','pwd']]],
 ['Appointment Information', [['date_issued','Date Issued','date'],['position_level','Position Level','level'],['position_title','Position Title'],['item_number','Item Number'],['employment_status','Employment Status'],['nature_of_appointment','Nature of Appointment','nature'],['classification_key','Classification','class']]],
 ['Salary Information', [['salary_grade','Salary / Job / Pay Grade'],['salary_step','Step'],['monthly_salary','Monthly Salary Rate','number']]],
 ['Employment and Publication', [['employment_period_from','Employment Period From','date'],['employment_period_to','Employment Period To','date'],['publication_from','Publication From','date'],['publication_to','Publication To','date'],['publication_mode','Publication Mode']]],
 ['CSC Information', [['appointment_identification_no','Appointment Identification Number'],['csc_action','CSC Action','action'],['csc_action_date','Date of CSC Action','date'],['csc_release_date','Date of Release','date']]]
];
const classifications = [['','Choose classification'],['sub_original','Substitute — Original'],['sub_reemployment','Substitute — Reemployment'],['perm_original','Permanent — Original'],['perm_transfer','Permanent — Transfer'],['perm_reemployment','Permanent — Reemployment'],['reclassification','Reclassification'],['other','Other']];
const fields = sections.flatMap(s=>s[1]).map(f=>f[0]);
function blankItem(){const x={source_type:'manual',source_id:'',classification_label:''};fields.forEach(k=>x[k]='');return x;}
function modal(id,on){$(id).classList.toggle('is-open',on);document.body.style.overflow=document.querySelector('.rai-modal-backdrop.is-open')?'hidden':'';}
document.querySelectorAll('[data-close]').forEach(b=>b.addEventListener('click',()=>modal(b.closest('.rai-modal-backdrop').id,false)));
document.querySelectorAll('.rai-modal-backdrop').forEach(el=>el.addEventListener('click',e=>{if(e.target===el)modal(el.id,false);}));
function classificationName(k){const found=classifications.find(c=>c[0]===k);return found?found[1]:(k||'Unclassified');}
function render(){const body=$('raiApplicantRows');body.innerHTML='';items.forEach((x,i)=>{const tr=document.createElement('tr');tr.innerHTML=`<td>${i+1}</td><td><strong>${esc([x.last_name,x.first_name].filter(Boolean).join(', ')||'Unnamed applicant')}</strong><div class="rai-muted">${esc(x.middle_name||'')}</div></td><td>${esc(x.position_title||'—')}<div class="rai-muted">${esc(x.item_number||'')}</div></td><td>${esc(x.date_issued||'—')}</td><td>${esc(x.nature_of_appointment||'—')}<div class="rai-muted">${esc(classificationName(x.classification_key))}</div></td><td><span class="rai-pill">${x.source_type==='manual'?'Manual':'RQA'}</span></td><td class="rai-tools"><div class="rai-actions"><button type="button" class="rai-icon-btn" data-action="edit" data-i="${i}" data-toggle="tooltip" data-placement="top" title="Edit"><i class="ph ph-pencil-simple"></i></button><button type="button" class="rai-icon-btn is-secondary" data-action="up" data-i="${i}" ${i===0?'disabled':''} data-toggle="tooltip" data-placement="top" title="Move up"><i class="ph ph-arrow-up"></i></button><button type="button" class="rai-icon-btn is-secondary" data-action="down" data-i="${i}" ${i===items.length-1?'disabled':''} data-toggle="tooltip" data-placement="top" title="Move down"><i class="ph ph-arrow-down"></i></button><button type="button" class="rai-icon-btn is-danger" data-action="remove" data-i="${i}" data-toggle="tooltip" data-placement="top" title="Remove"><i class="ph ph-trash"></i></button></div></td>`;body.appendChild(tr);});$('raiEmpty').classList.toggle('rai-hide',items.length>0);$('raiCount').textContent=items.length+' selected';if(window.jQuery&&window.jQuery.fn.tooltip){window.jQuery('#raiApplicantRows [data-toggle="tooltip"]').tooltip({selector:false});}}
function setSaveStatus(text,isError){const el=$('raiSaveStatus');el.textContent=text;el.style.color=isError?'#b23a38':'';}
function scheduleSave(){setSaveStatus('Saving...');clearTimeout(saveTimer);saveTimer=setTimeout(persistApplicants,300);}
async function persistApplicants(){try{const body=new URLSearchParams({rai_token:$('raiToken').value,payload:JSON.stringify({applicants:items})});const r=await fetch(saveUrl,{method:'POST',credentials:'same-origin',headers:{'Content-Type':'application/x-www-form-urlencoded','X-Requested-With':'XMLHttpRequest'},body});const j=await r.json();if(!r.ok||j.status!=='success')throw Error(j.message||'Save failed');setSaveStatus('Saved');}catch(e){setSaveStatus('Save failed: '+e.message,true);}}
$('raiApplicantRows').addEventListener('click',e=>{const b=e.target.closest('button[data-action]');if(!b)return;const i=Number(b.dataset.i);if(b.dataset.action==='edit')openEditor(i);if(b.dataset.action==='remove'&&confirm('Remove this applicant from the report?')){items.splice(i,1);render();scheduleSave();}if(b.dataset.action==='up'&&i>0){[items[i-1],items[i]]=[items[i],items[i-1]];render();scheduleSave();}if(b.dataset.action==='down'&&i<items.length-1){[items[i+1],items[i]]=[items[i],items[i+1]];render();scheduleSave();}});
function control(key,label,type,item){const val=item[key]||'';let content='';let options=null;if(type==='sex')options=[['','Select'],['M','Male'],['F','Female']];if(type==='pwd')options=[['','Not provided'],['Y','Yes'],['N','No']];if(type==='level')options=[['','Select'],['1st','1st'],['2nd','2nd']];if(type==='action')options=[['','Blank / Not yet recorded'],['V','Validated'],['INV','Invalidated']];if(type==='class')options=classifications;if(type==='nature')options=[['','Select'],['Original','Original'],['Reemployment','Reemployment'],['Transfer','Transfer'],['Promotion','Promotion'],['Reclassification','Reclassification'],['Reappointment','Reappointment'],['Other','Other']];if(options){content='<select name="'+key+'">'+options.map(o=>'<option value="'+esc(o[0])+'" '+(val===o[0]?'selected':'')+'>'+esc(o[1])+'</option>').join('')+'</select>';}else{const inputType=type==='date'?'date':type==='number'?'number':'text';content='<input name="'+key+'" type="'+inputType+'" '+(inputType==='number'?'min="0" step="0.01"':'')+' maxlength="255" value="'+esc(val)+'">';}return '<div class="rai-field"><label>'+esc(label)+'</label>'+content+'</div>';}
function openEditor(index){editingIndex=index;const item=index===null?blankItem():items[index];$('raiEditorTitle').textContent=index===null?'Add Manual Applicant':'Edit Applicant '+(index+1);$('raiSourceNote').textContent=item.source_type==='manual'?'Manual entry for this report only.':'Imported from RQA; changes apply to this report only.';$('raiEditorFields').innerHTML=sections.map(s=>'<div class="rai-section-label">'+esc(s[0])+'</div><div class="rai-grid">'+s[1].map(f=>control(f[0],f[1],f[2],item)).join('')+'</div>').join('');modal('raiEditorModal',true);}
$('raiEditorForm').addEventListener('submit',e=>{e.preventDefault();const f=new FormData(e.target);const entry=editingIndex===null?blankItem():Object.assign({},items[editingIndex]);fields.forEach(k=>entry[k]=String(f.get(k)||'').trim());entry.classification_label=classificationName(entry.classification_key);if(entry.employment_period_from&&entry.employment_period_to&&entry.employment_period_from>entry.employment_period_to){alert('Employment period end date must not precede start date.');return;}if(entry.publication_from&&entry.publication_to&&entry.publication_from>entry.publication_to){alert('Publication end date must not precede start date.');return;}if(editingIndex===null)items.push(entry);else items[editingIndex]=entry;modal('raiEditorModal',false);render();scheduleSave();});
$('raiAddManual').addEventListener('click',()=>openEditor(null));
$('raiOpenPicker').addEventListener('click',()=>{modal('raiPickerModal',true);$('raiSearchMonth').value=reportHeader.report_month||0;$('raiSearchYear').value=reportHeader.report_year||'';pickerPage=1;loadCandidates();});
$('raiSearchButton').addEventListener('click',()=>{pickerPage=1;loadCandidates();});$('raiSearch').addEventListener('keydown',e=>{if(e.key==='Enter'){e.preventDefault();pickerPage=1;loadCandidates();}});
async function loadCandidates(){const p=new URLSearchParams({search:$('raiSearch').value,month:$('raiSearchMonth').value,year:$('raiSearchYear').value,page:String(pickerPage)});$('raiPickerResults').textContent='Loading appointed applicants...';$('raiPickerPager').innerHTML='';try{const r=await fetch(candidateUrl+'?'+p.toString(),{credentials:'same-origin',headers:{'Accept':'application/json'}});const j=await r.json();if(!r.ok||j.status!=='success')throw new Error(j.message||'Unable to retrieve applicants.');$('raiPickerResults').innerHTML='';if(!j.rows.length)$('raiPickerResults').textContent='No appointed applicants found for these filters.';j.rows.forEach(x=>{const taken=items.some(i=>i.source_type==='rqa_recommendation'&&String(i.source_id)===String(x.id));const row=document.createElement('div');row.className='rai-picker-row';row.innerHTML='<div><strong>'+esc(x.name)+'</strong><small>'+esc(x.position||'Position not available')+' • '+esc(x.itemNumber||'No item number')+' • '+esc(x.dateIssued||'No date')+' • '+esc(x.natureOfAppointment||'Nature not available')+'</small></div><button type="button" class="btn '+(taken?'btn-outline-secondary':'btn-primary')+' btn-sm font-weight-bold" '+(taken?'disabled':'')+' data-pick="'+Number(x.id)+'" style="border-radius:10px;">'+(taken?'Added':'Add')+'</button>';$('raiPickerResults').appendChild(row);});const totalPages=Math.max(1,Number(j.pages)||1);const prev=document.createElement('button');prev.className='btn btn-light btn-sm';prev.textContent='← Previous';prev.disabled=pickerPage<=1;prev.onclick=()=>{pickerPage--;loadCandidates();};const next=document.createElement('button');next.className='btn btn-light btn-sm';next.textContent='Next →';next.disabled=pickerPage>=totalPages;next.onclick=()=>{pickerPage++;loadCandidates();};const info=document.createElement('span');info.className='rai-muted';info.textContent='Page '+pickerPage+' of '+totalPages+' • '+j.total+' candidates';$('raiPickerPager').append(prev,info,next);}catch(e){$('raiPickerResults').innerHTML='<div class="rai-error">'+esc(e.message)+'</div>';}}
$('raiPickerResults').addEventListener('click',async e=>{const b=e.target.closest('button[data-pick]');if(!b)return;const id=Number(b.dataset.pick);if(items.some(x=>x.source_type==='rqa_recommendation'&&Number(x.source_id)===id))return;b.disabled=true;b.textContent='Loading...';try{const r=await fetch(detailUrl+'/'+id,{credentials:'same-origin',headers:{'Accept':'application/json'}});const j=await r.json();if(!r.ok||j.status!=='success')throw Error(j.message||'Could not retrieve applicant.');const x=Object.assign(blankItem(),j.applicant);x.source_type='rqa_recommendation';x.source_id=String(id);items.push(x);render();scheduleSave();b.textContent='Added';}catch(e){alert(e.message);b.disabled=false;b.textContent='Add';}});
$('raiPreview').addEventListener('click',()=>{if(!items.length){alert('Select or add an applicant before previewing the report.');return;}const header={};['report_month','report_year','agency_name','division_name','csc_resolution_no','csc_resolution_date','certifying_officer','certifying_position','appointing_officer','appointing_position','remarks'].forEach(k=>header[k]=reportHeader[k]||'');const normalized=items.map(x=>Object.assign({},x,{classification_key:x.classification_key||'Unclassified'}));$('raiPayload').value=JSON.stringify({header:header,applicants:normalized});$('raiPreviewForm').submit();});
render();
})();
</script>
