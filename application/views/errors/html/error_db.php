<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Server paths and CodeIgniter internals are only useful to us, never to the
// people using the system. The live site runs with ENVIRONMENT at its default
// of 'development' (CI_ENV isn't set on the host), so the details are tied to
// the request actually coming from a local machine instead. Everything hidden
// here is written to application/logs/ as well.
$host = isset($_SERVER['HTTP_HOST']) ? strtolower(preg_replace('/:\d+$/', '', $_SERVER['HTTP_HOST'])) : '';
$show_details = (ENVIRONMENT === 'development' && in_array($host, array('localhost', '127.0.0.1', '::1', '[::1]'), TRUE));

// CodeIgniter renders this same template for a refused connection and for a
// failed query, and only the first one is worth telling people to retry.
$is_connection = (strpos($message, 'Unable to connect') !== FALSE);
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title>Service Temporarily Unavailable</title>
<style type="text/css">

* { box-sizing: border-box; }

body {
	background-color: #f4f6f9;
	margin: 0;
	padding: 40px 16px;
	font: 15px/1.6 -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
	color: #3d4852;
}

#container {
	max-width: 560px;
	margin: 0 auto;
	background-color: #fff;
	border: 1px solid #e3e6ea;
	border-radius: 6px;
	box-shadow: 0 2px 6px rgba(0, 0, 0, .06);
	padding: 32px;
	text-align: center;
}

.icon {
	width: 56px;
	height: 56px;
	margin-bottom: 18px;
	color: #f0ad4e;
}

h1 {
	color: #2c3e50;
	font-size: 22px;
	font-weight: 600;
	margin: 0 0 12px 0;
}

p {
	margin: 0 0 14px 0;
}

.muted {
	color: #8795a1;
	font-size: 13px;
	margin-bottom: 0;
}

.btn {
	display: inline-block;
	margin: 12px 0 20px 0;
	padding: 10px 26px;
	background-color: #0072bc;
	border: 0;
	border-radius: 4px;
	color: #fff;
	font-size: 15px;
	font-weight: 600;
	text-decoration: none;
	cursor: pointer;
}

.btn:hover { background-color: #005a94; }

.details {
	margin-top: 24px;
	padding-top: 18px;
	border-top: 1px solid #e3e6ea;
	text-align: left;
	font-family: Consolas, Monaco, "Courier New", monospace;
	font-size: 12px;
	line-height: 1.5;
	color: #8795a1;
	overflow-x: auto;
}

.details p { margin: 0 0 6px 0; }
</style>
</head>
<body>
	<div id="container">
		<svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
			<ellipse cx="12" cy="5.5" rx="8" ry="3"></ellipse>
			<path d="M4 5.5v13c0 1.66 3.58 3 8 3s8-1.34 8-3v-13"></path>
			<path d="M4 12c0 1.66 3.58 3 8 3s8-1.34 8-3"></path>
		</svg>

		<?php if ($is_connection): ?>

		<h1>The system is momentarily unavailable</h1>

		<p>We could not reach the database just now. This usually clears up within a few seconds.</p>

		<p><strong>Nothing you were working on has been lost.</strong> Please wait a moment, then try again.</p>

		<button type="button" class="btn" onclick="window.location.reload();">Try again</button>

		<p class="muted">If this keeps happening, please report it to the MIS / ICT Unit.</p>

		<?php else: ?>

		<h1>Sorry, we could not complete that</h1>

		<p>Something went wrong while processing your request, so it was stopped before any changes were saved.</p>

		<button type="button" class="btn" onclick="window.history.back();">Go back</button>

		<p class="muted">The details have been recorded. Please report this to the MIS / ICT Unit.</p>

		<?php endif; ?>

		<?php if ($show_details): ?>
		<div class="details">
			<p><strong><?php echo $heading; ?></strong></p>
			<?php echo $message; ?>
		</div>
		<?php endif; ?>
	</div>
</body>
</html>
