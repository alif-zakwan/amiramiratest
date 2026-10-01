<?php
/**
 * ArifAmira — Wedding Invitation
 * Entry point. Run locally with e.g.:
 *   php -S localhost:8000
 * then open http://localhost:8000/
 *
 * URL options:
 *   ?mode=page        page-by-page experience (switched off in config)
 */

require __DIR__ . '/includes/bootstrap.php';

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/opening.php';
?>
<main class="experience" id="experience" data-mode="<?= e($mode) ?>">
<?php foreach ($sections as $index => $section): ?>
<?php include __DIR__ . '/includes/sections/' . $section . '.php'; ?>
<?php endforeach; ?>
</main>
<?php
include __DIR__ . '/includes/footer.php';
