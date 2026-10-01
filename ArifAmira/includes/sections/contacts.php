<?php
/** Family contact numbers, tap-to-call. */
section_open('contacts', 'ivory', $cfg['labels']['contacts_title'] ?? '', 'accent-right');
?>
<h2 class="section-title" data-anim="item"><?= t('contacts_title') ?></h2>
<?php divider('ink'); ?>

<ul class="contact-list">
    <?php foreach ($cfg['contacts'] as $contact): ?>
    <li data-anim="item">
        <a href="tel:<?= e(preg_replace('/[^0-9+]/', '', $contact['phone'])) ?>">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M6.6 10.8c1.4 2.8 3.8 5.1 6.6 6.6l2.2-2.2c.3-.3.7-.4 1-.2 1.1.4 2.3.6 3.6.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1C10.6 21 3 13.4 3 4c0-.6.4-1 1-1h3.5c.6 0 1 .4 1 1 0 1.3.2 2.5.6 3.6.1.4 0 .8-.2 1L6.6 10.8z"/></svg>
            <span class="contact-list__phone"><?= e($contact['phone']) ?></span>
            <span class="contact-list__name">(<?= e($contact['name']) ?>)</span>
        </a>
    </li>
    <?php endforeach; ?>
</ul>
<?php section_close(); ?>
