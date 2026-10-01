<?php
/** Guest wishes wall, filled from rsvp.php (js/features.js → renderWishes). */
section_open('wishes', 'ivory', $cfg['labels']['wishes_title'] ?? '', 'accent-right');
?>
<h2 class="section-title" data-anim="item"><?= t('wishes_title') ?></h2>
<?php divider('ink'); ?>

<ul class="wishes-list" id="wishesList" data-anim="item" aria-live="polite">
    <li class="wishes-list__empty"><?= t('wishes_empty') ?></li>
</ul>
<?php section_close(); ?>
