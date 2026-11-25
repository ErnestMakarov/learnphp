<h1 class="display-5 mb-4"><?= e($title) ?></h1>
<?php if ($posts === []): ?>
    <p class="text-secondary">No articles yet.</p>
<?php endif; ?>
<?php foreach ($posts as $post): ?>
    <article class="mb-5 border-bottom pb-4">
        <p class="text-secondary small mb-2"><?= e($post->created_at) ?></p>
        <h2 class="h3"><?= e($post->title) ?></h2>
        <p class="lh-lg"><?= nl2br(e($post->body)) ?></p>
    </article>
<?php endforeach; ?>
