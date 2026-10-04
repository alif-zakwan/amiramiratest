<?php
/** Event details — follows the lower half of page 2 of the printed card. */
section_open('details', 'maroon', $cfg['labels']['schedule'] ?? '', 'feature');
?>
<dl class="details">
    <div class="details__item" data-anim="item" data-emph="high">
        <dt class="caps-label"><?= t('on') ?></dt>
        <dd><?php date_line('details'); ?></dd>
    </div>

    <div class="details__item" data-anim="item" data-emph="high">
        <dt class="caps-label"><?= t('venue') ?></dt>
        <dd>
            <span class="details__value"><?= e($cfg['venue_name']) ?></span>
            <a class="details__link" href="#venue"><?= t('map_view') ?> &darr;</a>
        </dd>
    </div>

    <div class="details__item" data-anim="item" data-emph="high">
        <dt class="caps-label"><?= t('schedule') ?></dt>
        <dd class="details__value"><?= e($cfg['event_start']) ?> &ndash; <?= e($cfg['event_end']) ?></dd>
    </div>

    <div class="details__item" data-anim="item" data-emph="high">
        <dt class="caps-label"><?= t('arrival') ?></dt>
        <dd class="details__value details__value--sm"><?= e($cfg['pengantin_tiba']) ?></dd>
    </div>
</dl>
<?php section_close(); ?>
