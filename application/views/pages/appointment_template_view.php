<?php
if (!function_exists('h')) {
    function h($value) { return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); }
}
$ext = strtolower((string) $template->extension);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= h($title); ?> — <?= h($template->original_name); ?></title>
<link href="<?= base_url(); ?>assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="<?= base_url(); ?>assets/css/icons.min.css" rel="stylesheet" type="text/css" />
<style>
    :root { --tp-primary:#1f3a5f; --tp-bg:#eef2f7; --tp-border:#e3eaf3; --tp-muted:#758396; }
    html, body { margin:0; padding:0; min-height:100%; background:var(--tp-bg); }
    .tp-bar { position:sticky; top:0; z-index:50; background:#fff; border-bottom:1px solid var(--tp-border); padding:10px 18px; display:flex; align-items:center; gap:14px; flex-wrap:wrap; box-shadow:0 4px 16px rgba(31,58,95,.07); }
    .tp-bar h6 { margin:0; font-weight:800; color:#26384d; font-size:.9rem; }
    .tp-meta { color:var(--tp-muted); font-size:.72rem; margin-top:3px; }
    .tp-pill { display:inline-flex; padding:.14rem .5rem; border-radius:999px; font-size:.63rem; font-weight:800; background:#eef5ff; color:#315a85; border:1px solid #dae8f8; margin-right:4px; }
    .tp-pill.tp-guide { background:#fff6e8; color:#926719; border-color:#f3dfb8; }
    .tp-actions { margin-left:auto; display:flex; gap:8px; }
    .tp-frame-wrap { padding:18px; }
    .tp-frame { width:100%; border:0; background:#fff; border-radius:14px; box-shadow:0 10px 30px rgba(31,58,95,.10); height:calc(100vh - 120px); }
    .tp-paper { max-width:850px; margin:22px auto 40px; background:#fff; padding:56px 64px; border-radius:4px; box-shadow:0 10px 30px rgba(31,58,95,.12); font-family:'Times New Roman',serif; color:#1a1a1a; line-height:1.55; }
    .tp-paper p { margin:0 0 .35rem; }
    .tp-paper p.dp-empty { margin:0; }
    .dp-tab { display:inline-block; width:2.4em; }
    table.dp-tbl { width:100%; border-collapse:collapse; margin:.5rem 0; }
    table.dp-tbl td { border:1px solid #b8c2cf; padding:4px 8px; vertical-align:top; }
    .tp-fallback { max-width:560px; margin:90px auto; text-align:center; color:var(--tp-muted); }
    .tp-fallback i { font-size:52px; color:#c4d1df; display:block; margin-bottom:12px; }
    @media(max-width:767px){ .tp-paper{padding:30px 22px; margin:12px} .tp-actions{width:100%; margin-left:0} }
</style>
</head>
<body>
    <div class="tp-bar">
        <a href="<?= base_url('Pages/appointment_template_setup'); ?>" class="btn btn-sm btn-light" title="Back to Template Setup"><i class="mdi mdi-arrow-left"></i></a>
        <div>
            <h6><?= h($template->original_name); ?></h6>
            <div class="tp-meta">
                <span class="tp-pill"><?= h($groupName); ?></span>
                <span class="tp-pill"><?= h($natureName); ?></span>
                <span class="tp-pill"><?= h($typeName); ?></span>
                <?php if ((int) $template->is_guide === 1) : ?><span class="tp-pill tp-guide">Appointment guide</span><?php endif; ?>
                <?= strtoupper(h($ext)); ?> · <?= number_format(((int) $template->file_size) / 1024, 1); ?> KB
            </div>
        </div>
        <div class="tp-actions">
            <a class="btn btn-sm btn-outline-secondary" href="<?= base_url('Pages/appointment_template_download/' . (int) $template->id); ?>"><i class="mdi mdi-download mr-1"></i>Download</a>
        </div>
    </div>

    <?php if ($preview && $preview['kind'] === 'spreadsheet') : ?>
        <div class="tp-frame-wrap">
            <iframe class="tp-frame" srcdoc="<?= h($preview['html']); ?>"></iframe>
        </div>
    <?php elseif ($preview && $preview['kind'] === 'docx') : ?>
        <div class="tp-paper"><?= $preview['html']; ?></div>
    <?php else : ?>
        <div class="tp-fallback">
            <i class="mdi mdi-file-hidden"></i>
            <h5 class="mb-1">No preview available</h5>
            <p>This file could not be rendered in the browser. Download it to open in Excel or Word.</p>
            <a class="btn btn-primary" href="<?= base_url('Pages/appointment_template_download/' . (int) $template->id); ?>"><i class="mdi mdi-download mr-1"></i>Download File</a>
        </div>
    <?php endif; ?>
</body>
</html>
