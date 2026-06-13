<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<link rel="shortcut icon" href="<?= base_url(); ?>assets/images/hris.ico">
<title><?= $this->input->get('label'); ?></title>


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
<h1><?= $this->input->get('label'); ?></h1>



<script src="https://unpkg.com/pdfobject@2.3.0/pdfobject.min.js"></script>
<?php $col = $this->uri->segment(4); ?>
<script>PDFObject.embed("<?= base_url(); ?>uploads/regfile/<?= $data->$col; ?>", document.body);</script>

</body>
</html>