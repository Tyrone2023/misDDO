<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<link rel="shortcut icon" href="<?= base_url(); ?>assets/images/hris.ico">
<title><?= htmlspecialchars((string) $this->input->get('label'), ENT_QUOTES, 'UTF-8'); ?></title>


<!-- site analytics for pdfobject.com, do not copy into your code -->
<script async src="/js/analytics.js"></script>

<style>
/* basic page styling */
body { font: 15px/120% sans-serif; color: #555; background: #fff; padding: 2rem; margin: 0; }

/* Give the PDF container some basic styling */
.pdfobject-container { height: 600px; border: 1px solid #ccc; }
</style>
</head>

<body>
<h1><?= htmlspecialchars((string) $this->input->get('label'), ENT_QUOTES, 'UTF-8'); ?></h1>



<script src="https://unpkg.com/pdfobject@2.3.0/pdfobject.min.js"></script>
<?php
    $col  = $this->uri->segment(4);
    $file = isset($data->$col) ? (string) $data->$col : '';
?>
<?php if ($file === ''): ?>
<p>No file has been attached yet.</p>
<?php else: ?>
<?php
    // Files uploaded before names were sanitised can still contain spaces, '&'
    // or '#'. Left raw those truncate or corrupt the URL - and on the live host
    // they make the request look hostile, which comes back as a bare 403.
    $src = base_url() . 'uploads/regfile/' . rawurlencode($file);
?>
<script>PDFObject.embed(<?= json_encode($src, JSON_UNESCAPED_SLASHES); ?>, document.body);</script>
<?php endif; ?>

</body>
</html>