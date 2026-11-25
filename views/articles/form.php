<?php include __DIR__ . '/../partials/header.php'; ?>
<main class="container form-page" id="top">
    <a href="/admin/articles">← All articles</a><h1 class="my-4"><?= $article->id ? 'Edit article' : 'New article' ?></h1>
    <?php include __DIR__ . '/../partials/errors.php'; ?>
    <form method="post" action="<?= e($action) ?>">
        <?= csrfField() ?>
        <?php if ($article->id): ?><input type="hidden" name="id" value="<?= e($article->id) ?>"><?php endif; ?>
        <div class="mb-3">
            <label class="form-label" for="title">Title</label>
            <input class="form-control" name="title" id="title" value="<?= e($article->title) ?>" maxlength="255" required>
        </div>
        <div class="mb-4">
            <label class="form-label" for="body">Content</label>
            <textarea class="form-control" name="body" id="body" rows="8" required><?= e($article->body) ?></textarea>
        </div>
        <button class="btn btn-dark">Save article</button><a class="btn btn-link" href="/admin/articles">Cancel</a>
    </form>
</main>
<?php include __DIR__ . '/../partials/footer.php'; ?>
