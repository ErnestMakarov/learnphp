<?php include __DIR__ . '/../partials/header.php'; ?>
<main class="container form-page" id="top">
    <a href="/admin/articles">← All articles</a>
    <h1 class="mt-4"><?= e($article->title) ?></h1>
    <p class="text-secondary">Created: <?= e($article->created_at) ?> · Updated: <?= e($article->updated_at) ?></p>
    <p class="lh-lg"><?= nl2br(e($article->body)) ?></p>
    <a class="btn btn-dark" href="/admin/articles/edit?id=<?= e($article->id) ?>">Edit article</a>
</main>
<?php include __DIR__ . '/../partials/footer.php'; ?>
