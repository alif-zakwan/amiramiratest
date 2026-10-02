<?php
/**
 * Ucapan Tetamu — a small digital guestbook. The wishes are a stack of paper
 * cards (swipe, drag or use the arrows to flip through them); a button below
 * opens a popup where guests write their own (js/guestbook.js, backend:
 * guestbook.php). Separate from RSVP.
 */
section_open('wishes', 'ivory', $cfg['labels']['gb_title'] ?? '', 'accent-right');
?>
<h2 class="section-title" data-anim="item"><?= t('gb_title') ?></h2>
<?php divider('ink'); ?>
<p class="section-note guestbook__intro" data-anim="item"><?= t('gb_intro') ?></p>

<div class="guestbook" data-anim="item">
    <div class="guestbook__empty" id="gbEmpty">
        <svg class="guestbook__heart" aria-hidden="true"><use href="#ornament-heart"/></svg>
        <p><?= t('gb_empty') ?></p>
    </div>

    <div class="stage" id="gbStage" hidden>
        <button class="stage__arrow stage__arrow--prev" id="gbPrev" type="button" aria-label="<?= t('gb_prev') ?>">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="none" stroke="currentColor" stroke-width="1.6" d="M15 5l-7 7 7 7"/></svg>
        </button>
        <div class="stack" id="gbStack" data-no-swipe tabindex="0" role="group" aria-roledescription="carousel" aria-label="<?= t('gb_list_aria') ?>">
            <ul class="stack__cards" id="gbCards"></ul>
        </div>
        <button class="stage__arrow stage__arrow--next" id="gbNext" type="button" aria-label="<?= t('gb_next') ?>">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="none" stroke="currentColor" stroke-width="1.6" d="M9 5l7 7-7 7"/></svg>
        </button>
    </div>
    <p class="guestbook__count" id="gbCounter" aria-live="polite" hidden></p>
</div>

<div class="guestbook__add" data-anim="item">
    <button class="btn btn--outline" id="gbAdd" type="button" aria-haspopup="dialog"><?= t('gb_add') ?></button>
    <p class="form-status" id="gbThanks" role="status" aria-live="polite"></p>
</div>

<!-- Popup: write a wish -->
<dialog class="gb-dialog" id="gbDialog" aria-labelledby="gbDialogTitle">
    <div class="gb-dialog__card">
        <button class="gb-dialog__close" id="gbClose" type="button" aria-label="<?= t('gb_close') ?>">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" d="M6 6l12 12M18 6L6 18"/></svg>
        </button>
        <svg class="note__heart" aria-hidden="true"><use href="#ornament-heart"/></svg>
        <h3 class="gb-dialog__title" id="gbDialogTitle"><?= t('gb_dialog_title') ?></h3>

        <form class="guestbook-form" id="guestbookForm" novalidate>
            <label class="field">
                <span class="field__label"><?= t('gb_name') ?></span>
                <input type="text" id="gbName" name="name" maxlength="60" autocomplete="name" placeholder="<?= t('gb_name_ph') ?>" required>
            </label>
            <label class="field">
                <span class="field__label"><?= t('gb_message') ?></span>
                <textarea id="gbMessage" name="message" rows="5" maxlength="280" placeholder="<?= t('gb_message_ph') ?>" required></textarea>
                <span class="field__count" id="gbCount" aria-hidden="true">0 / 280</span>
            </label>
            <label class="field field--trap" aria-hidden="true">
                Website <input type="text" name="website" tabindex="-1" autocomplete="off">
            </label>
            <p class="form-status" id="gbStatus" role="status" aria-live="polite"></p>
            <button type="submit" class="btn btn--solid" id="gbSubmit"><?= t('gb_submit') ?></button>
        </form>
    </div>
</dialog>
<?php section_close(); ?>
