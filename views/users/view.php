<?php include __DIR__ . '/../partials/header.php'; ?>
<main class="container form-page" id="top">
    <a href="/admin/users">← All users</a>
    <h1 class="mt-4 mb-4"><?= e($user->name) ?></h1>
    <dl class="row">
        <dt class="col-sm-3">ID</dt><dd class="col-sm-9"><?= e($user->id) ?></dd>
        <dt class="col-sm-3">Email</dt><dd class="col-sm-9 text-break"><?= e($user->email) ?></dd>
        <dt class="col-sm-3">Role</dt><dd class="col-sm-9"><?= $user->is_admin ? 'Administrator' : 'User' ?></dd>
        <dt class="col-sm-3">Created</dt><dd class="col-sm-9"><?= e($user->created_at) ?></dd>
        <dt class="col-sm-3">Updated</dt><dd class="col-sm-9"><?= e($user->updated_at) ?></dd>
    </dl>
</main>
<?php include __DIR__ . '/../partials/footer.php'; ?>
