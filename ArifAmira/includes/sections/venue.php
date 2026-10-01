<?php
/** Map of the venue. */
section_open('venue', 'ivory', $cfg['labels']['location_title'] ?? '', 'accent-left');
?>
<h2 class="section-title" data-anim="item"><?= t('location_title') ?></h2>
<?php divider('ink'); ?>

<div class="map-frame" data-anim="item">
    <iframe src="<?= e($cfg['map_embed_src']) ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="<?= t('map_title') ?>"></iframe>
</div>
<p class="venue__address" data-anim="item"><?= e($cfg['venue_address']) ?></p>
<a class="btn btn--outline" data-anim="item" href="<?= e($cfg['map_link']) ?>" target="_blank" rel="noopener"><?= t('open_map') ?></a>
<?php section_close(); ?>
