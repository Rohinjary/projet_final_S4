<?php
$exception = $exception ?? null;
$severity  = $severity ?? 'Error';
$message   = $message ?? ($exception ? $exception->getMessage() : 'An error occurred.');
$filename  = $filename ?? '';
$line      = $line ?? '';
?>
Error: <?= $severity ?>
Message: <?= $message ?>
<?php if ($filename !== '') : ?>
File: <?= $filename ?>
Line: <?= $line ?>
<?php endif; ?>
<?php if ($exception) : ?>

Trace:
<?= $exception->getTraceAsString() ?>
<?php endif; ?>
