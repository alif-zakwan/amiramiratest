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
<?php $maps = map_app_links($cfg); ?>
<div class="map-apps" id="mapApps" data-anim="item">
    <p class="map-apps__label"><?= t('map_open_with') ?></p>
    <a class="map-apps__btn map-apps__btn--wide" href="<?= e($maps['google']) ?>" target="_blank" rel="noopener">Google Maps</a>
    <a class="map-apps__btn" href="<?= e($maps['waze']) ?>" target="_blank" rel="noopener">Waze</a>
    <a class="map-apps__btn" href="<?= e($maps['apple']) ?>" target="_blank" rel="noopener">Apple Maps</a>
    <a class="map-apps__btn map-apps__btn--wide map-apps__btn--other" id="mapOther" href="<?= e($maps['other']) ?>" hidden><?= t('map_other') ?></a>
</div>
<?php section_close(); ?>
