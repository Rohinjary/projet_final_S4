<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Error</title>
<style>
body{font-family:Arial,sans-serif;margin:40px;background:#f6f7fb;color:#111}
.card{max-width:900px;margin:0 auto;background:#fff;border:1px solid #ddd;border-radius:12px;padding:24px}
code,pre{white-space:pre-wrap;word-break:break-word}
.title{font-size:20px;font-weight:700;margin-bottom:10px}
.meta{color:#555;margin:8px 0}
</style>
</head>
<body>
<div class="card">
<div class="title">An error occurred</div>
<?php $exception = $exception ?? null; ?>
<?php if ($exception) : ?>
<div class="meta"><strong><?= esc($exception->getMessage()) ?></strong></div>
<div class="meta">File: <?= esc($exception->getFile()) ?>:<?= esc((string) $exception->getLine()) ?></div>
<pre><?= esc($exception->getTraceAsString()) ?></pre>
<?php else : ?>
<div class="meta">The application could not complete the request.</div>
<?php endif; ?>
</div>
</body>
</html>
