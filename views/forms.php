<?php include __DIR__ . '/partials/header.php'; ?>
<main class="container form-page" id="top">
    <h1 class="mb-4">Forms</h1>
    <p class="text-secondary">A small classroom example: enter a name and age to receive a greeting.</p>
    <?php if ($message !== ''): ?><p class="alert alert-info" role="status"><?= e($message) ?></p><?php endif; ?>
    <form method="post" action="/forms">
        <?= csrfField() ?>
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input class="form-control" id="name" name="name" value="<?= e($name) ?>" required>
        </div>
        <div class="mb-3">
            <label for="age" class="form-label">Age</label>
            <input class="form-control" type="number" id="age" name="age" min="0" max="130" value="<?= e($age) ?>" required>
        </div>
        <button class="btn btn-dark">Send</button>
    </form>
</main>
<?php include __DIR__ . '/partials/footer.php'; ?>
