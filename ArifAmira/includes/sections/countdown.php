<?php
/** Live countdown to the wedding (js/features.js → initCountdown). */
section_open('countdown', 'ivory', $cfg['labels']['countdown_title'] ?? '', 'accent-right');
$units = ['days' => 'countdown_days', 'hours' => 'countdown_hours', 'mins' => 'countdown_mins', 'secs' => 'countdown_secs'];
?>
<h2 class="section-title" data-anim="item"><?= t('countdown_title') ?></h2>
<?php divider('ink'); ?>

<div class="countdown" id="countdownClock" data-target="<?= e($weddingIso) ?>" data-anim="item" role="timer">
    <?php foreach ($units as $unit => $label): ?>
    <div class="countdown__unit">
        <span class="countdown__num" data-unit="<?= $unit ?>">00</span>
        <span class="countdown__label"><?= t($label) ?></span>
    </div>
    <?php endforeach; ?>
</div>
<p class="countdown__done" id="countdownDone" data-anim="item" hidden><?= t('countdown_done') ?></p>

<?php date_line('ink'); ?>
<?php section_close(); ?>
