<?php $currentUser = auth(); $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); ?>
<header class="container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 border-bottom pb-3">
        <a class="brand text-decoration-none" href="/">LearnPHP <span class="text-secondary fs-6">/ journal</span></a>
        <div class="d-flex flex-wrap align-items-center gap-3">
            <?php if ($currentUser): ?>
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"><?= e($currentUser->name) ?></button>
                    <div class="dropdown-menu dropdown-menu-end p-2">
                        <form method="post" action="/logout">
                            <?= csrfField() ?>
                            <button class="dropdown-item">Log out</button>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <a class="btn btn-sm btn-outline-dark" href="/login">Log in</a>
                <a class="btn btn-sm btn-dark" href="/register">Register</a>
            <?php endif; ?>
        </div>
    </div>
    <nav class="nav gap-3 pt-3" aria-label="Main navigation">
        <?php foreach (['/' => 'World', '/us' => 'U.S.', '/tech' => 'Technology', '/forms' => 'Forms'] as $url => $label): ?>
            <a class="nav-link px-0 <?= $currentPath === $url ? 'fw-bold text-dark' : 'text-secondary' ?>"
               href="<?= e($url) ?>" <?= $currentPath === $url ? 'aria-current="page"' : '' ?>><?= e($label) ?></a>
        <?php endforeach; ?>
        <?php if ($currentUser?->is_admin): ?>
            <a class="nav-link px-0" href="/admin/articles">Manage articles</a>
            <a class="nav-link px-0" href="/admin/users">Manage users</a>
        <?php endif; ?>
    </nav>
</header>
