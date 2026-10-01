<?php
/** Closing — thanks, names, share. Sits directly on the paper, no card. */
section_open('closing', 'plain', $cfg['bride_short'] . ' & ' . $cfg['groom_short']);
?>
<div class="medallion" data-anim="item" aria-hidden="true">
    <img src="assets/images/emblem-doves.webp" alt="" width="368" height="368">
</div>
<p class="closing__thanks" data-anim="item"><?= t('thanks') ?></p>
<p class="closing__names" data-anim="item"><span><?= e($cfg['bride_short']) ?></span> <span><i>&amp;</i> <?= e($cfg['groom_short']) ?></span></p>
<?php divider('ink'); ?>
<button class="btn btn--outline" id="shareBtn" type="button" data-anim="item"><?= t('share') ?></button>
<p class="closing__hashtag" data-anim="item"><?= e($cfg['hashtag']) ?></p>
<?php section_close(); ?>
