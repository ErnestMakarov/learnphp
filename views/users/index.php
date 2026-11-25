<?php include __DIR__ . '/../partials/header.php'; ?>
<main class="container" id="top">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div><p class="text-secondary small mb-1">Administration</p><h1>Users</h1></div>
        <a class="btn btn-dark" href="/admin/users/create">New user</a>
    </div>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead><tr><th scope="col">ID</th><th scope="col">Name</th><th scope="col">Email</th><th scope="col">Role</th><th scope="col">Actions</th></tr></thead>
            <tbody>
                <?php if ($users === []): ?><tr><td colspan="5" class="py-4 text-secondary">No users found.</td></tr><?php endif; ?>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= e($user->id) ?></td>
                        <td><?= e($user->name) ?></td>
                        <td><?= e($user->email) ?></td>
                        <td><?= $user->is_admin ? 'Administrator' : 'User' ?></td>
                        <td><div class="d-flex flex-wrap gap-2">
                            <a class="btn btn-sm btn-outline-secondary" href="/admin/users/view?id=<?= e($user->id) ?>">View</a>
                            <a class="btn btn-sm btn-outline-primary" href="/admin/users/edit?id=<?= e($user->id) ?>">Edit</a>
                        </div></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>
<?php include __DIR__ . '/../partials/footer.php'; ?>
