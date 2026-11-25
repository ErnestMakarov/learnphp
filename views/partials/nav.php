<?php $currentUser = auth(); $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); ?>
<header class="container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 border-bottom pb-3">
        <a class="brand text-decoration-none" href="/">LearnPHP <span class="text-secondary fs-6">/ journal</span></a>
        <div class="d-flex flex-wrap align-items-center gap-3">
            <?php if ($currentUser): ?>
                <span><?= e($currentUser->name) ?></span>
                <form method="post" action="/logout">
                    <?= csrfField() ?>
                    <button class="btn btn-sm btn-outline-secondary">Log out</button>
                </form>
            <?php else: ?>
                <a class="btn btn-sm btn-outline-dark" href="/login">Log in</a>
            <?php endif; ?>
        </div>
    </div>
    <nav class="nav gap-3 pt-3" aria-label="Main navigation">
        <?php foreach (['/' => 'World', '/us' => 'U.S.'] as $url => $label): ?>
            <a class="nav-link px-0 <?= $currentPath === $url ? 'fw-bold text-dark' : 'text-secondary' ?>"
               href="<?= e($url) ?>" <?= $currentPath === $url ? 'aria-current="page"' : '' ?>><?= e($label) ?></a>
        <?php endforeach; ?>
        <a class="nav-link px-0 text-secondary" href="#">Technology</a>
        <?php if ($currentUser?->is_admin): ?>
            <a class="nav-link px-0" href="/admin/articles">Manage articles</a>
        <?php endif; ?>
    </nav>
</header>
