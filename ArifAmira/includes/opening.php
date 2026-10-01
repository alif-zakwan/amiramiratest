<?php
/**
 * Opening screen: a sealed burgundy envelope. Tapping the wax seal
 * plays the opening sequence (js/opening.js) and reveals the cover.
 *
 * Layers, back to front:
 *   reveal   — the emblem in its oval frame, seen between the flaps
 *   flaps    — left/right envelope flaps meeting under the seal
 *   florals  — a spray on each side, echoing the card
 *   text     — "Kepada" + guest name, couple's names, hint
 *   seal     — the button
 */
?>
<div class="opening" id="opening">
    <div class="opening__reveal" data-opening="reveal">
        <div class="opening__monogram" data-opening="monogram">
            <svg class="opening__oval" aria-hidden="true"><use href="#ornament-oval"/></svg>
            <img class="opening__emblem" src="assets/images/emblem-doves.webp" alt="" width="368" height="368">
        </div>
    </div>

    <div class="opening__flap opening__flap--left" data-opening="flap-left"><span></span></div>
    <div class="opening__flap opening__flap--right" data-opening="flap-right"><span></span></div>

    <div class="opening__florals" data-opening="florals" aria-hidden="true">
        <img class="opening__floral opening__floral--tr" src="assets/images/flower2.webp" alt="" width="390" height="391">
        <img class="opening__floral opening__floral--bl" src="assets/images/flower1.webp" alt="" width="295" height="662">
    </div>

    <p class="opening__to" data-opening="text">
        <span class="opening__to-label"><?= t('to') ?></span>
        <span class="opening__to-name"><?= $guestName !== '' ? e($guestName) : t('to_default') ?></span>
    </p>

    <div class="opening__footer" data-opening="text">
        <p class="opening__names"><span><?= e($cfg['bride_short']) ?></span> <span><i>&amp;</i> <?= e($cfg['groom_short']) ?></span></p>
        <p class="opening__hint" id="openingHint"><?= t('open_hint') ?></p>
    </div>

    <button class="seal" id="openSeal" type="button" aria-label="<?= t('open_aria') ?>" aria-describedby="openingHint">
        <span class="seal__half seal__half--left" data-opening="seal-left"></span>
        <span class="seal__half seal__half--right" data-opening="seal-right"></span>
        <span class="seal__letters" data-opening="seal-letters"><?= e($cfg['wax_seal_letter']) ?></span>
        <span class="seal__glint" aria-hidden="true"></span>
    </button>

    <button class="opening__skip" id="openingSkip" type="button" hidden><?= t('skip') ?> &rsaquo;</button>
</div>
