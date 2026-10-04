<?php
/** Cover — follows page 1 of the printed card. */
section_open('cover', 'maroon', $cfg['bride_short'] . ' & ' . $cfg['groom_short'], 'feature');
?>
<p class="cover__arabic" data-anim="item" lang="ar" dir="rtl"><?= e($cfg['greeting_arabic']) ?></p>
<p class="cover__tagline" data-anim="item"><?= e($cfg['tagline']) ?></p>

<img class="cover__emblem" data-anim="item" src="assets/images/emblem-doves.webp" alt="" width="368" height="368">

<h1 class="names">
    <span class="names__line" data-anim="item" data-emph="focus"><?= e($cfg['bride_short']) ?></span>
    <span class="names__amp" data-anim="item" data-emph="focus">&amp;</span>
    <span class="names__line" data-anim="item" data-emph="focus"><?= e($cfg['groom_short']) ?></span>
</h1>

<?php divider(); ?>
<?php date_line('cover'); ?>
<?php divider(); ?>

<blockquote class="cover__quote" data-anim="item">
    <p><?= e($cfg['quote_text']) ?></p>
    <cite><?= e($cfg['quote_source']) ?></cite>
</blockquote>
<?php section_close(); ?>
