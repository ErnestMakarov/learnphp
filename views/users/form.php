<?php include __DIR__ . '/../partials/header.php'; ?>
<main class="container form-page" id="top">
    <?php if (!($registering ?? false)): ?><a href="/admin/users">← All users</a><?php endif; ?>
    <h1 class="my-4"><?= ($registering ?? false) ? 'Create account' : ($user->id ? 'Edit user' : 'New user') ?></h1>
    <?php include __DIR__ . '/../partials/errors.php'; ?>
    <form method="post" action="<?= e($action) ?>">
        <?= csrfField() ?>
        <?php if ($user->id): ?><input type="hidden" name="id" value="<?= e($user->id) ?>"><?php endif; ?>
        <div class="mb-3">
            <label class="form-label" for="name">Name</label>
            <input class="form-control" name="name" id="name" value="<?= e($user->name) ?>" maxlength="100" autocomplete="name" required>
        </div>
        <div class="mb-3">
            <label class="form-label" for="email">Email</label>
            <input class="form-control" type="email" name="email" id="email" value="<?= e($user->email) ?>" maxlength="255" autocomplete="email" required>
        </div>
        <div class="mb-3">
            <label class="form-label" for="password"><?= $user->id ? 'New password (optional)' : 'Password' ?></label>
            <input class="form-control" type="password" name="password" id="password" minlength="8" maxlength="72" autocomplete="new-password" aria-describedby="password-help" <?= $user->id ? '' : 'required' ?>>
            <div class="form-text" id="password-help"><?= $user->id ? 'Leave both password fields empty to keep the current password.' : 'Use at least 8 characters.' ?></div>
        </div>
        <div class="mb-4">
            <label class="form-label" for="password_confirm">Confirm password</label>
            <input class="form-control" type="password" name="password_confirm" id="password_confirm" autocomplete="new-password" <?= $user->id ? '' : 'required' ?>>
        </div>
        <button class="btn btn-dark"><?= ($registering ?? false) ? 'Create account' : 'Save user' ?></button>
        <a class="btn btn-link" href="<?= ($registering ?? false) ? '/login' : '/admin/users' ?>"><?= ($registering ?? false) ? 'Already have an account? Log in' : 'Cancel' ?></a>
    </form>
</main>
<?php include __DIR__ . '/../partials/footer.php'; ?>
