<?php
/** Salam kaut / money gift (only rendered when enable_salam_kaut is true). */
section_open('gift', 'ivory', $cfg['labels']['gift_title'] ?? '', 'accent-left');
?>
<h2 class="section-title" data-anim="item"><?= t('gift_title') ?></h2>
<?php divider('ink'); ?>
<p class="section-note" data-anim="item"><?= e($cfg['gift_note']) ?></p>

<div class="gift-card" data-anim="item">
    <p class="gift-card__bank"><?= e($cfg['gift_bank']) ?></p>
    <p class="gift-card__acc" id="giftAccNo"><?= e($cfg['gift_account_no']) ?></p>
    <p class="gift-card__name"><?= e($cfg['gift_account_name']) ?></p>
    <button type="button" class="btn btn--light" id="copyAccBtn"><?= t('gift_copy') ?></button>
</div>
<?php section_close(); ?>
