<?php
/** Formal invitation — follows the top half of page 2 of the printed card. */
section_open('invitation', 'maroon', $cfg['bride_full'] . ' & ' . $cfg['groom_full'], 'feature-alt');
?>
<div class="watermark" aria-hidden="true">
    <img src="assets/images/emblem-doves.webp" alt="" width="368" height="368">
</div>

<p class="caps-text" data-anim="item"><?= e($cfg['invite_intro']) ?></p>

<p class="formal__parents" data-anim="item">
    <span><?= e($cfg['bride_father']) ?></span>
    <span class="formal__amp">&amp;</span>
    <span><?= e($cfg['bride_mother']) ?></span>
</p>

<p class="caps-text" data-anim="item"><?= e($cfg['invite_persilakan']) ?></p>

<?php divider(); ?>

<p class="formal__name formal__name--script" data-anim="item" data-emph="focus"><?= e($cfg['bride_full']) ?></p>
<p class="caps-text caps-text--relation" data-anim="item"><?= e($cfg['bride_relation_line']) ?></p>
<p class="formal__name formal__name--script" data-anim="item" data-emph="focus"><?= e($cfg['groom_full']) ?></p>

<?php divider(); ?>

<img class="drift-blossom" data-anim="drift" src="assets/images/blossom.png" alt="" aria-hidden="true" width="93" height="115">
<?php section_close(); ?>
