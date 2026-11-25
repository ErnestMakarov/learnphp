<?php include __DIR__ . '/../partials/header.php'; ?>
<main class="container" id="top">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div><p class="text-secondary small mb-1">Administration</p><h1>Users</h1></div>
        <a class="btn btn-dark" href="/admin/users/create">New user</a>
    </div>
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead><tr><th scope="col">ID</th><th scope="col">Name</th><th scope="col">Email</th><th scope="col">Role</th><th scope="col">Created</th><th scope="col">Updated</th><th scope="col">Actions</th></tr></thead>
            <tbody>
                <?php if ($users === []): ?><tr><td colspan="7" class="py-4 text-secondary">No users found.</td></tr><?php endif; ?>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= e($user->id) ?></td>
                        <td><?= e($user->name) ?></td>
                        <td><?= e($user->email) ?></td>
                        <td><?= $user->is_admin ? 'Administrator' : 'User' ?></td>
                        <td><?= e($user->created_at) ?></td>
                        <td><?= e($user->updated_at) ?></td>
                        <td><div class="d-flex flex-wrap gap-2">
                            <a class="btn btn-sm btn-outline-secondary" href="/admin/users/view?id=<?= e($user->id) ?>">View</a>
                            <a class="btn btn-sm btn-outline-primary" href="/admin/users/edit?id=<?= e($user->id) ?>">Edit</a>
                            <?php if ($user->id !== $currentUser->id): ?>
                                <form method="post" action="/admin/users/delete" onsubmit="return confirm('Delete this user permanently?');">
                                    <?= csrfField() ?><input type="hidden" name="id" value="<?= e($user->id) ?>">
                                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            <?php else: ?>
                                <span class="text-secondary small align-self-center">Your account</span>
                            <?php endif; ?>
                        </div></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>
<?php include __DIR__ . '/../partials/footer.php'; ?>
