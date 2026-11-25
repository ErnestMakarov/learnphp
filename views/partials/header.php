<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'LearnPHP') ?> · LearnPHP</title>
    <link rel="stylesheet" href="/assets/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/app.css">
</head>
<body>
<?php include __DIR__ . '/nav.php'; ?>
<?php if (isset($_SESSION['flash'])): ?>
    <div class="container"><div class="alert alert-success" role="status"><?= e($_SESSION['flash']) ?></div></div>
    <?php unset($_SESSION['flash']); ?>
<?php endif; ?>
