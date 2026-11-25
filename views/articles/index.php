<?php include __DIR__ . '/../partials/header.php'; ?>
<main class="container" id="top">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <h1>Articles</h1><a class="btn btn-dark" href="/admin/articles/create">New article</a>
    </div>
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead><tr><th scope="col">ID</th><th scope="col">Title</th><th scope="col">Created</th><th scope="col">Updated</th><th scope="col">Actions</th></tr></thead>
            <tbody>
                <?php if ($articles === []): ?><tr><td colspan="5" class="text-secondary py-4">No articles yet. Create the first one.</td></tr><?php endif; ?>
                <?php foreach ($articles as $article): ?>
                    <tr>
                        <td><?= e($article->id) ?></td><td><?= e($article->title) ?></td><td><?= e($article->created_at) ?></td><td><?= e($article->updated_at) ?></td>
                        <td><div class="d-flex flex-wrap gap-2">
                            <a class="btn btn-sm btn-outline-secondary" href="/admin/articles/view?id=<?= e($article->id) ?>">View</a>
                            <a class="btn btn-sm btn-outline-primary" href="/admin/articles/edit?id=<?= e($article->id) ?>">Edit</a>
                            <form method="post" action="/admin/articles/delete" onsubmit="return confirm('Delete this article?');">
                                <?= csrfField() ?><input type="hidden" name="id" value="<?= e($article->id) ?>">
                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </div></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>
<?php include __DIR__ . '/../partials/footer.php'; ?>
