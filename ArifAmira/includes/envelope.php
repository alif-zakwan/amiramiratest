<?php
/**
 * The letter/envelope cover screen.
 * Guests must tap the wax seal to "open" the invitation.
 * Expects $cfg and $guestName.
 */
?>
<div class="envelope-screen" id="envelopeScreen">
    <div class="envelope" id="envelope">

        <div class="envelope__texture"></div>

        <div class="envelope__flap envelope__flap--top"></div>
        <div class="envelope__flap envelope__flap--left"></div>
        <div class="envelope__flap envelope__flap--right"></div>

        <div class="envelope__body">
            <p class="envelope__to">
                Kepada<?= $guestName ? ':' : '' ?>
                <span><?= $guestName ? htmlspecialchars($guestName) : 'Tetamu Yang Dihormati' ?></span>
            </p>
            <p class="envelope__names"><?= htmlspecialchars($cfg['bride_short']) ?> </br> &amp; </br> <?= htmlspecialchars($cfg['groom_short']) ?></p>
        </div>

        <button class="wax-seal" id="waxSeal" type="button" aria-label="Tekan untuk buka jemputan">
            <span class="wax-seal__ring"></span>
            <span class="wax-seal__letters"><?= htmlspecialchars($cfg['wax_seal_letter']) ?></span>
            <span class="spark-fx" id="sealSpark" aria-hidden="true">
                <span class="spark-fx__ray spark-fx__ray--1"></span>
                <span class="spark-fx__ray spark-fx__ray--2"></span>
                <span class="spark-fx__ray spark-fx__ray--3"></span>
                <span class="spark-fx__ray spark-fx__ray--4"></span>
                <span class="spark-fx__core"></span>
            </span>
        </button>

        <div class="envelope__flap envelope__flap--bottom"></div>
    </div>

    <p class="envelope__hint" id="envelopeHint">Tekan meterai lilin untuk membuka jemputan</p>
</div>
