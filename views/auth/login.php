<?php include __DIR__ . '/../partials/header.php'; ?>
<main class="container form-page" id="top">
    <p class="text-secondary small text-uppercase">Administration</p>
    <h1 class="mb-4">Log in</h1>
    <?php if ($error !== ''): ?><div class="alert alert-danger" role="alert"><?= e($error) ?></div><?php endif; ?>
    <form method="post" action="/login">
        <?= csrfField() ?>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input class="form-control" type="email" name="email" id="email" value="<?= e($email) ?>" autocomplete="username" required>
        </div>
        <div class="mb-4">
            <label for="password" class="form-label">Password</label>
            <input class="form-control" type="password" name="password" id="password" autocomplete="current-password" required>
        </div>
        <button class="btn btn-dark">Log in</button>
    </form>
</main>
<?php include __DIR__ . '/../partials/footer.php'; ?>
