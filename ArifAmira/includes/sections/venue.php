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
<!-- One button. Android: opens the phone's own list of map apps (geo: link).
     iPhone / computer: opens the popup below. Without JavaScript: Google Maps. -->
<div class="map-open" data-anim="item">
    <a class="btn btn--outline" id="mapOpen" href="<?= e($maps['google']) ?>" data-geo="<?= e($maps['other']) ?>" target="_blank" rel="noopener" aria-haspopup="dialog"><?= t('map_open_app') ?></a>
</div>

<dialog class="paper-dialog" id="mapDialog" aria-labelledby="mapDialogTitle">
    <div class="paper-dialog__card">
        <button class="paper-dialog__close" id="mapClose" type="button" aria-label="<?= t('gb_close') ?>">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" d="M6 6l12 12M18 6L6 18"/></svg>
        </button>
        <svg class="note__heart" aria-hidden="true"><use href="#ornament-heart"/></svg>
        <h3 class="paper-dialog__title" id="mapDialogTitle"><?= t('map_choose') ?></h3>
        <ul class="map-choices">
            <li><a class="map-choices__btn" href="<?= e($maps['google']) ?>" target="_blank" rel="noopener">Google Maps</a></li>
            <li><a class="map-choices__btn" href="<?= e($maps['waze']) ?>" target="_blank" rel="noopener">Waze</a></li>
            <li><a class="map-choices__btn" href="<?= e($maps['apple']) ?>" target="_blank" rel="noopener">Apple Maps</a></li>
        </ul>
    </div>
</dialog>
<?php section_close(); ?>
