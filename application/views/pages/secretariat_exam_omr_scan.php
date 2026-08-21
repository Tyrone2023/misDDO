<?php
$h = static function ($value) { return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); };
$questions = $questions ?? [];
$applicants = $applicants ?? [];
$omrAttempts = $omrAttempts ?? [];
$selectedAppId = (int) ($selectedAppId ?? 0);
$result = $result ?? null;
$questionsPerPage = 60;
$pageCount = max(1, (int) ceil(count($questions) / $questionsPerPage));
$nameOf = static function ($row) {
    return trim((string) $row->LastName . ', ' . (string) $row->FirstName . ' ' . (string) $row->MiddleName . ' ' . (string) ($row->NameExtn ?? ''));
};
$scanQuestions = [];
foreach ($questions as $index => $question) {
    $choices = [];
    foreach ((array) $question->choices as $ci => $choice) {
        $choices[] = [
            'id' => (string) (is_array($choice) ? ($choice['id'] ?? '') : $choice),
            'letter' => chr(65 + $ci),
        ];
    }
    $page = intdiv($index, $questionsPerPage) + 1;
    $localIndex = $index % $questionsPerPage;
    $column = intdiv($localIndex, 30);
    $row = $localIndex % 30;
    $baseX = 18 + ($column * 99);
    $scanQuestions[] = [
        'id' => (int) $question->question_id,
        'number' => $index + 1,
        'page' => $page,
        'type' => (string) $question->question_type,
        'x' => $baseX,
        'y' => 70.5 + ($row * 7.08),
        'choices' => $choices,
    ];
}
?>
<style>
    .oms-page{--ink:#1f2a37;--muted:#6d7885;--line:#e1e6ed;--blue:#0d6efd;--soft:#f6f8fb;padding-bottom:90px}.oms-page .container-fluid{max-width:1120px}
    .oms-hero{align-items:center;background:linear-gradient(125deg,#103764,#2357d5 62%,#4d82ed);border-radius:14px;color:#fff;display:flex;flex-wrap:wrap;gap:14px;justify-content:space-between;margin-bottom:18px;padding:22px 24px}.oms-hero h3{color:#fff;font-size:22px;margin:0 0 4px}.oms-hero p{color:#dce8ff;margin:0}.oms-hero a{background:rgba(255,255,255,.16);border-radius:8px;color:#fff;padding:8px 12px;text-decoration:none}
    .oms-grid{display:grid;gap:18px;grid-template-columns:minmax(0,1.25fr) minmax(300px,.75fr)}.oms-card{background:#fff;border:1px solid var(--line);border-radius:12px;margin-bottom:18px;overflow:hidden}.oms-head{align-items:center;background:var(--soft);border-bottom:1px solid var(--line);display:flex;gap:8px;justify-content:space-between;padding:13px 16px}.oms-head h5{color:var(--ink);font-size:14px;font-weight:700;margin:0}.oms-body{padding:16px}.oms-step{align-items:center;display:flex;gap:9px;margin-bottom:10px}.oms-step span{align-items:center;background:var(--blue);border-radius:50%;color:#fff;display:inline-flex;font-size:12px;font-weight:700;height:24px;justify-content:center;width:24px}.oms-label{color:var(--muted);display:block;font-size:11px;font-weight:700;letter-spacing:.05em;margin-bottom:5px;text-transform:uppercase}.oms-select,.oms-file{border:1px solid #cfd6df;border-radius:8px;padding:10px;width:100%}.oms-file{background:#f8fafc}.oms-help{color:var(--muted);font-size:12px;line-height:1.45;margin-top:6px}.oms-actions{display:flex;flex-wrap:wrap;gap:8px;margin-top:12px}.oms-btn{background:var(--blue);border:0;border-radius:8px;color:#fff;cursor:pointer;font-weight:650;padding:9px 13px}.oms-btn.secondary{background:#edf2f8;color:#354052}.oms-btn:disabled{cursor:not-allowed;opacity:.5}.oms-status{border-radius:8px;display:none;font-size:12.5px;line-height:1.45;margin-top:12px;padding:10px 12px}.oms-status.visible{display:block}.oms-status.info{background:#eaf2ff;color:#1d56a5}.oms-status.success{background:#e8f6ed;color:#187447}.oms-status.error{background:#fdecec;color:#a72f35}
    .oms-preview{background:#242b34;border-radius:10px;margin-top:14px;overflow:hidden}.oms-preview canvas{display:block;height:auto;max-height:66vh;object-fit:contain;width:100%}.oms-review{display:grid;gap:7px;grid-template-columns:repeat(2,minmax(0,1fr));max-height:660px;overflow:auto;padding-right:3px}.oms-row{align-items:center;border:1px solid var(--line);border-radius:8px;display:flex;gap:5px;padding:7px}.oms-num{color:var(--ink);font-size:12px;font-weight:700;min-width:24px}.oms-bubble{align-items:center;background:#fff;border:1px solid #aeb8c5;border-radius:50%;color:#465160;cursor:pointer;display:inline-flex;font-size:11px;font-weight:700;height:27px;justify-content:center;padding:0;width:27px}.oms-bubble.marked{background:#162b4a;border-color:#162b4a;color:#fff}.oms-row.ambiguous{background:#fff7e1;border-color:#e9c86c}.oms-detected{color:var(--muted);font-size:11px;margin-left:auto}.oms-submit{background:#16804e;border:0;border-radius:9px;color:#fff;font-size:14px;font-weight:700;margin-top:14px;padding:11px 16px;width:100%}.oms-submit:disabled{opacity:.45}
    .oms-page-divider{background:#eaf2ff;border-radius:7px;color:#245a9c;font-size:11px;font-weight:700;grid-column:1/-1;padding:6px 9px;text-transform:uppercase}
    .oms-result{background:#eaf8ef;border:1px solid #9ed1b0;border-radius:12px;margin-bottom:18px;padding:16px}.oms-result strong{color:#11693e;font-size:21px}.oms-history{border-collapse:collapse;width:100%}.oms-history th,.oms-history td{border-bottom:1px solid var(--line);font-size:12px;padding:8px;text-align:left}.oms-history th{color:var(--muted);font-size:10.5px;text-transform:uppercase}
    @media(max-width:850px){.oms-grid{grid-template-columns:1fr}.oms-review{grid-template-columns:1fr}.oms-hero{padding:18px}.oms-page .content{padding-top:10px}}
</style>

<div class="content-page oms-page"><div class="content"><div class="container-fluid">
    <div class="oms-hero">
        <div><h3><i class="mdi mdi-camera-outline mr-1"></i> Scan OMR answer sheet</h3><p><?= $h($exam->title); ?> &middot; <?= $h($exam->vacancy_title ?: $exam->job_title); ?></p></div>
        <div><a href="<?= base_url('secretariat/exams/' . (int) $exam->exam_id); ?>"><i class="mdi mdi-arrow-left mr-1"></i> Exam</a> <a href="<?= base_url('secretariat/exams/' . (int) $exam->exam_id . '/omr/print'); ?>"><i class="mdi mdi-printer-outline mr-1"></i> Print</a></div>
    </div>

    <?php foreach (['success' => 'alert-success', 'danger' => 'alert-danger'] as $flash => $class) : ?>
        <?php if ($this->session->flashdata($flash)) : ?><div class="alert <?= $class; ?>"><?= $h($this->session->flashdata($flash)); ?></div><?php endif; ?>
    <?php endforeach; ?>
    <?php if ($result) : ?>
        <div class="oms-result"><div>Saved OMR grade for application #<?= (int) $result->app_id; ?></div><strong><?= $h(rtrim(rtrim(number_format((float) $result->score, 2, '.', ''), '0'), '.')); ?> / <?= $h(rtrim(rtrim(number_format((float) $result->total_points, 2, '.', ''), '0'), '.')); ?> points &middot; <?= $h(rtrim(rtrim(number_format((float) $result->percentage, 2, '.', ''), '0'), '.')); ?>%</strong></div>
    <?php endif; ?>

    <div class="oms-grid">
        <div>
            <div class="oms-card"><div class="oms-head"><h5>Capture answer-sheet pages</h5><span class="text-muted" style="font-size:11px">Nothing is uploaded until you save the reviewed result</span></div><div class="oms-body">
                <div class="oms-step"><span>1</span><strong>Confirm the automatically read examinee number</strong></div>
                <label class="oms-label" for="omrApplicant">Applicant</label>
                <select id="omrApplicant" class="oms-select">
                    <option value="">Select applicant</option>
                    <?php foreach ($applicants as $applicant) : ?>
                        <option value="<?= (int) $applicant->appID; ?>" <?= $selectedAppId === (int) $applicant->appID ? 'selected' : ''; ?>>#<?= (int) $applicant->appID; ?> — <?= $h($nameOf($applicant)); ?></option>
                    <?php endforeach; ?>
                </select>
                <div class="oms-help">The scanner reads the 10-digit bubble grid and selects the matching application automatically. Use this list only to correct an unreadable or incorrectly shaded number.</div>

                <div class="oms-step" style="margin-top:18px"><span>2</span><strong>Photograph each complete A4 answer-sheet page</strong></div>
                <input type="file" id="omrPhoto" class="oms-file" accept="image/*" capture="environment">
                <div class="oms-help">This exam has <?= $pageCount; ?> answer-sheet page<?= $pageCount === 1 ? '' : 's'; ?>. Capture them one at a time; the printed page code is read automatically and the answers are combined. Keep all four black corner squares visible.</div>
                <div class="oms-actions"><button type="button" class="oms-btn secondary" id="manualReview">Review manually</button><label style="align-items:center;display:flex;font-size:12px;gap:7px;margin:0 0 0 auto">Sensitivity <input type="range" id="sensitivity" min="18" max="62" value="34"></label></div>
                <div class="oms-status" id="scanStatus" role="status" aria-live="polite"></div>
                <div class="oms-preview" id="previewWrap" style="display:none"><canvas id="previewCanvas"></canvas></div>
                <canvas id="sourceCanvas" style="display:none"></canvas>
            </div></div>
        </div>

        <div>
            <div class="oms-card"><div class="oms-head"><h5>Review detected answers</h5><span class="text-muted" style="font-size:11px"><span id="pageProgress">0/<?= $pageCount; ?> pages</span> &middot; <span id="answerCount">0 marked</span></span></div><div class="oms-body">
                <div class="oms-help" style="margin:0 0 12px">Tap a letter to correct it. Multiple-choice rows allow more than one bubble. Yellow rows need attention.</div>
                <div id="reviewGrid" class="oms-review"><div class="text-muted" style="font-size:13px">Capture a sheet or open manual review.</div></div>
                <form method="post" action="<?= base_url('secretariat/exams/' . (int) $exam->exam_id . '/omr/submit'); ?>" id="omrSubmitForm">
                    <input type="hidden" name="app_id" id="submitAppId">
                    <input type="hidden" name="answers_json" id="answersJson">
                    <button type="submit" class="oms-submit" id="saveResult" disabled>Save and grade reviewed sheet</button>
                </form>
            </div></div>

            <div class="oms-card"><div class="oms-head"><h5>Recent scanned results</h5></div><div class="oms-body" style="padding:0 10px 10px">
                <?php if (empty($omrAttempts)) : ?><p class="text-muted" style="font-size:12px;padding:12px 6px;margin:0">No scanned sheets yet.</p><?php else : ?>
                    <div class="table-responsive"><table class="oms-history"><thead><tr><th>Applicant</th><th>Grade</th><th>Time</th></tr></thead><tbody>
                    <?php foreach (array_slice($omrAttempts, 0, 8) as $attempt) : ?>
                        <tr><td>#<?= (int) $attempt->app_id; ?> <?= $h(trim((string) $attempt->LastName . ', ' . (string) $attempt->FirstName)); ?></td><td><strong><?= $h(rtrim(rtrim(number_format((float) $attempt->percentage, 2, '.', ''), '0'), '.')); ?>%</strong></td><td><?= $h(date('M j, g:i A', strtotime((string) $attempt->submitted_at))); ?></td></tr>
                    <?php endforeach; ?>
                    </tbody></table></div>
                <?php endif; ?>
            </div></div>
        </div>
    </div>
</div></div></div>

<script>
(function(){
    'use strict';
    var pageCount = <?= (int) $pageCount; ?>;
    var questions = <?= json_encode($scanQuestions, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
    var answers = {};
    var scannedPages = {};
    var ambiguousAnswers = {};
    var manualMode = false;
    var capturedApplicantId = '';
    var markerPoints = null;
    var applicant = document.getElementById('omrApplicant');
    var photo = document.getElementById('omrPhoto');
    var source = document.getElementById('sourceCanvas');
    var preview = document.getElementById('previewCanvas');
    var sourceCtx = source.getContext('2d', {willReadFrequently:true});
    var previewCtx = preview.getContext('2d');
    var statusBox = document.getElementById('scanStatus');
    var reviewGrid = document.getElementById('reviewGrid');
    var saveButton = document.getElementById('saveResult');
    var sensitivity = document.getElementById('sensitivity');

    function setStatus(kind, text){statusBox.className='oms-status visible '+kind;statusBox.textContent=text;}
    function attr(value){return String(value).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');}
    function blankAnswers(){questions.forEach(function(q){answers[q.id]=[];});}
    function syncForm(){
        document.getElementById('submitAppId').value=applicant.value;
        document.getElementById('answersJson').value=JSON.stringify(answers);
        var count=Object.keys(answers).reduce(function(n,k){return n+(answers[k]||[]).length;},0);
        var pages=Object.keys(scannedPages).length;
        document.getElementById('answerCount').textContent=count+' marked';
        document.getElementById('pageProgress').textContent=pages+'/'+pageCount+' pages';
        saveButton.disabled=!applicant.value||!reviewGrid.querySelector('.oms-row')||(!manualMode&&pages<pageCount);
    }
    function renderReview(ambiguous){
        ambiguous=ambiguous||ambiguousAnswers;
        var currentPage=0,html='';
        questions.forEach(function(q){
            if(q.page!==currentPage){currentPage=q.page;html+='<div class="oms-page-divider">Answer-sheet page '+currentPage+(scannedPages[currentPage]?' &middot; scanned':' &middot; pending')+'</div>';}
            var buttons=q.choices.map(function(c){var on=(answers[q.id]||[]).indexOf(c.id)!==-1;return '<button type="button" class="oms-bubble '+(on?'marked':'')+'" data-q="'+q.id+'" data-choice="'+attr(c.id)+'">'+c.letter+'</button>';}).join('');
            html+='<div class="oms-row '+(ambiguous[q.id]?'ambiguous':'')+'"><span class="oms-num">'+q.number+'.</span>'+buttons+(ambiguous[q.id]?'<span class="oms-detected">check</span>':'')+'</div>';
        });
        reviewGrid.innerHTML=html;
        syncForm();
    }
    reviewGrid.addEventListener('click',function(e){
        var b=e.target.closest('.oms-bubble');if(!b)return;
        var q=questions.find(function(item){return String(item.id)===b.dataset.q;});if(!q)return;
        var list=answers[q.id]||[],choice=b.dataset.choice,pos=list.indexOf(choice);
        if(q.type==='multiple_choice'){if(pos===-1)list.push(choice);else list.splice(pos,1);answers[q.id]=list;}
        else{answers[q.id]=pos===-1?[choice]:[];}
        delete ambiguousAnswers[q.id];renderReview(ambiguousAnswers);
    });

    function componentsIn(imageData, zone){
        var w=imageData.width,h=imageData.height,data=imageData.data;
        var x0=Math.max(0,Math.floor(zone[0]*w)),y0=Math.max(0,Math.floor(zone[1]*h)),x1=Math.min(w,Math.ceil(zone[2]*w)),y1=Math.min(h,Math.ceil(zone[3]*h));
        var zw=x1-x0,zh=y1-y0,seen=new Uint8Array(zw*zh),parts=[];
        function dark(x,y){var p=(y*w+x)*4;return (data[p]*.299+data[p+1]*.587+data[p+2]*.114)<78;}
        for(var yy=y0;yy<y1;yy++)for(var xx=x0;xx<x1;xx++){
            var si=(yy-y0)*zw+(xx-x0);if(seen[si]||!dark(xx,yy)){seen[si]=1;continue;}
            var stack=[[xx,yy]],area=0,sx=0,sy=0,minx=xx,maxx=xx,miny=yy,maxy=yy;seen[si]=1;
            while(stack.length){var p=stack.pop(),x=p[0],y=p[1];area++;sx+=x;sy+=y;minx=Math.min(minx,x);maxx=Math.max(maxx,x);miny=Math.min(miny,y);maxy=Math.max(maxy,y);
                [[x-1,y],[x+1,y],[x,y-1],[x,y+1]].forEach(function(n){if(n[0]<x0||n[0]>=x1||n[1]<y0||n[1]>=y1)return;var ni=(n[1]-y0)*zw+(n[0]-x0);if(!seen[ni]){seen[ni]=1;if(dark(n[0],n[1]))stack.push(n);}});
            }
            var bw=maxx-minx+1,bh=maxy-miny+1,fill=area/(bw*bh),minDim=Math.min(w,h);
            if(bw>minDim*.008&&bh>minDim*.008&&bw<minDim*.08&&bh<minDim*.08&&bw/bh>.58&&bw/bh<1.7&&fill>.5)parts.push({x:sx/area,y:sy/area,area:area,fill:fill});
        }
        return parts.sort(function(a,b){return b.area*b.fill-a.area*a.fill;});
    }
    function findMarkers(imageData){
        var zones=[[0,0,.24,.24],[.76,0,1,.24],[0,.76,.24,1],[.76,.76,1,1]];
        var found=zones.map(function(z){return componentsIn(imageData,z)[0]||null;});
        return found.every(Boolean)?found:null;
    }
    function solve(matrix){
        for(var i=0;i<8;i++){var max=i;for(var r=i+1;r<8;r++)if(Math.abs(matrix[r][i])>Math.abs(matrix[max][i]))max=r;var tmp=matrix[i];matrix[i]=matrix[max];matrix[max]=tmp;if(Math.abs(matrix[i][i])<1e-9)return null;var div=matrix[i][i];for(var c=i;c<9;c++)matrix[i][c]/=div;for(r=0;r<8;r++){if(r===i)continue;var f=matrix[r][i];for(c=i;c<9;c++)matrix[r][c]-=f*matrix[i][c];}}
        return matrix.map(function(row){return row[8];});
    }
    function homography(dst){
        var src=[[8,8],[202,8],[8,289],[202,289]],m=[];
        src.forEach(function(s,i){var x=s[0],y=s[1],u=dst[i].x,v=dst[i].y;m.push([x,y,1,0,0,0,-u*x,-u*y,u]);m.push([0,0,0,x,y,1,-v*x,-v*y,v]);});
        var h=solve(m);if(!h)return null;return function(x,y){var d=h[6]*x+h[7]*y+1;return{x:(h[0]*x+h[1]*y+h[2])/d,y:(h[3]*x+h[4]*y+h[5])/d};};
    }
    function darkness(data,cx,cy,r){
        var w=data.width,h=data.height,pix=data.data,total=0,dark=0,rr=r*r;
        for(var y=Math.max(0,Math.floor(cy-r));y<Math.min(h,Math.ceil(cy+r));y++)for(var x=Math.max(0,Math.floor(cx-r));x<Math.min(w,Math.ceil(cx+r));x++){var dx=x-cx,dy=y-cy;if(dx*dx+dy*dy>rr)continue;var p=(y*w+x)*4,g=pix[p]*.299+pix[p+1]*.587+pix[p+2]*.114;total++;if(g<135)dark++;}
        return total?dark/total:0;
    }
    function bubbleRatio(data,map,x,y,radiusMm){
        var center=map(x,y),edge=map(x+radiusMm,y),radius=Math.max(1.5,Math.hypot(edge.x-center.x,edge.y-center.y)*.72);
        return darkness(data,center.x,center.y,radius);
    }
    function readPageNumber(data,map,threshold){
        if(bubbleRatio(data,map,137,51,1.35)<threshold)return 0;
        var code=0;
        for(var bit=0;bit<8;bit++)if(bubbleRatio(data,map,147+(bit*5.7),51,1.25)>=threshold)code+=(1<<bit);
        var page=code+1;
        return page>=1&&page<=pageCount?page:0;
    }
    function readApplicationNumber(data,map,threshold){
        var digits='',uncertain=false;
        for(var column=0;column<10;column++){
            var hits=[];
            for(var digit=0;digit<=9;digit++){
                var ratio=bubbleRatio(data,map,23+(column*5.2),25+(digit*3.05),1.15);
                if(ratio>=threshold)hits.push({digit:digit,ratio:ratio});
                if(Math.abs(ratio-threshold)<.045)uncertain=true;
            }
            if(hits.length!==1)return {valid:false,code:digits,uncertain:true};
            digits+=String(hits[0].digit);
        }
        var appId=parseInt(digits,10),option=applicant.querySelector('option[value="'+appId+'"]');
        return {valid:!!option&&appId>0,appId:appId,code:digits,uncertain:uncertain};
    }
    function recognize(){
        if(!source.width)return;
        var data=sourceCtx.getImageData(0,0,source.width,source.height),markers=findMarkers(data);
        if(!markers){markerPoints=null;setStatus('error','The four registration squares were not found. Retake the photo closer, flatter, and in even light, or use manual review.');return;}
        markerPoints=markers;var map=homography(markers);if(!map){setStatus('error','The page geometry could not be read. Retake the photo directly above the sheet.');return;}
        var threshold=Number(sensitivity.value)/100,pageNumber=readPageNumber(data,map,threshold);
        if(!pageNumber){setStatus('error','The answer-sheet page code could not be read. Check that this sheet belongs to the same exam and that the full page is visible.');return;}
        var identity=readApplicationNumber(data,map,threshold);
        if(identity.valid){
            if(capturedApplicantId&&String(identity.appId)!==String(capturedApplicantId)){setStatus('error','This page belongs to application #'+identity.appId+', but the earlier captured page belongs to application #'+capturedApplicantId+'. Keep each examinee\'s pages together.');return;}
            capturedApplicantId=String(identity.appId);
            applicant.value=String(identity.appId);
        }
        questions.filter(function(q){return q.page===pageNumber;}).forEach(function(q){
            var selected=[],uncertain=false;
            q.choices.forEach(function(c,ci){var ratio=bubbleRatio(data,map,q.x+16+(ci*8),q.y,2.45);if(ratio>=threshold)selected.push(c.id);if(Math.abs(ratio-threshold)<.055)uncertain=true;});
            answers[q.id]=selected;
            if(uncertain||(q.type!=='multiple_choice'&&selected.length>1))ambiguousAnswers[q.id]=true;else delete ambiguousAnswers[q.id];
        });
        scannedPages[pageNumber]=true;
        preview.width=source.width;preview.height=source.height;previewCtx.drawImage(source,0,0);previewCtx.strokeStyle='#13b66a';previewCtx.lineWidth=Math.max(2,source.width/350);markers.forEach(function(p){previewCtx.beginPath();previewCtx.arc(p.x,p.y,Math.max(7,source.width/90),0,Math.PI*2);previewCtx.stroke();});document.getElementById('previewWrap').style.display='block';
        renderReview(ambiguousAnswers);
        var captured=Object.keys(scannedPages).length,identityText=identity.valid?'Application #'+identity.appId+' identified. ':'The examinee number was not read reliably; choose the applicant manually. ';
        var warning=identity.uncertain||Object.keys(ambiguousAnswers).length;
        setStatus(warning?'info':'success',identityText+'Page '+pageNumber+' of '+pageCount+' scanned. '+(captured<pageCount?'Capture the remaining '+(pageCount-captured)+' page'+((pageCount-captured)===1?'':'s')+'.':'All pages are ready for final review and grading.'));
    }
    photo.addEventListener('change',function(){
        var file=photo.files&&photo.files[0];if(!file)return;setStatus('info','Reading the sheet…');var img=new Image(),url=URL.createObjectURL(file);
        img.onload=function(){var scale=Math.min(1,1200/Math.max(img.naturalWidth,img.naturalHeight));source.width=Math.round(img.naturalWidth*scale);source.height=Math.round(img.naturalHeight*scale);sourceCtx.drawImage(img,0,0,source.width,source.height);URL.revokeObjectURL(url);recognize();photo.value='';};img.onerror=function(){setStatus('error','That image could not be opened. Choose another photo.');URL.revokeObjectURL(url);photo.value='';};img.src=url;
    });
    sensitivity.addEventListener('change',function(){if(source.width)recognize();});
    document.getElementById('manualReview').addEventListener('click',function(){manualMode=true;renderReview(ambiguousAnswers);setStatus('info','Manual review is open. Enter any unreadable page directly, then verify the applicant before saving.');});
    applicant.addEventListener('change',function(){syncForm();if(capturedApplicantId&&applicant.value&&applicant.value!==capturedApplicantId)setStatus('info','The automatically read application #'+capturedApplicantId+' was manually changed to #'+applicant.value+'. Verify the paper before saving.');});
    document.getElementById('omrSubmitForm').addEventListener('submit',function(e){syncForm();if(!applicant.value){e.preventDefault();setStatus('error','Choose the applicant before saving this result.');}});
    blankAnswers();syncForm();
})();
</script>
