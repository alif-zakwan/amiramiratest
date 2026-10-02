<?php
/**
 * Ucapan Tetamu — a small digital guestbook: guests leave a wish, and the
 * wishes appear below as paper notes you swipe through
 * (js/guestbook.js, backend: guestbook.php). Separate from RSVP.
 */
section_open('wishes', 'ivory', $cfg['labels']['gb_title'] ?? '', 'accent-right');
?>
<h2 class="section-title" data-anim="item"><?= t('gb_title') ?></h2>
<?php divider('ink'); ?>
<p class="section-note guestbook__intro" data-anim="item"><?= t('gb_intro') ?></p>

<form class="guestbook-form" id="guestbookForm" data-anim="item" novalidate>
    <label class="field">
        <span class="field__label"><?= t('gb_name') ?></span>
        <input type="text" id="gbName" name="name" maxlength="60" autocomplete="name" placeholder="<?= t('gb_name_ph') ?>" required>
    </label>
    <label class="field">
        <span class="field__label"><?= t('gb_message') ?></span>
        <textarea id="gbMessage" name="message" rows="4" maxlength="280" placeholder="<?= t('gb_message_ph') ?>" required></textarea>
        <span class="field__count" id="gbCount" aria-hidden="true">0 / 280</span>
    </label>
    <label class="field field--trap" aria-hidden="true">
        Website <input type="text" name="website" tabindex="-1" autocomplete="off">
    </label>
    <p class="form-status" id="gbStatus" role="status" aria-live="polite"></p>
    <button type="submit" class="btn btn--solid" id="gbSubmit"><?= t('gb_submit') ?></button>
</form>

<div class="guestbook" data-anim="item">
    <div class="guestbook__empty" id="gbEmpty">
        <svg class="guestbook__heart" aria-hidden="true"><use href="#ornament-heart"/></svg>
        <p><?= t('gb_empty') ?></p>
    </div>
    <ul class="guestbook__track" id="gbTrack" data-no-swipe tabindex="0" aria-label="<?= t('gb_list_aria') ?>" hidden></ul>
    <div class="guestbook__nav" id="gbNav" hidden>
        <button class="guestbook__arrow" id="gbPrev" type="button" aria-label="<?= t('gb_prev') ?>">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="none" stroke="currentColor" stroke-width="1.6" d="M15 5l-7 7 7 7"/></svg>
        </button>
        <span class="guestbook__count" id="gbCounter" aria-live="polite"></span>
        <button class="guestbook__arrow" id="gbNext" type="button" aria-label="<?= t('gb_next') ?>">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="none" stroke="currentColor" stroke-width="1.6" d="M9 5l7 7-7 7"/></svg>
        </button>
    </div>
</div>
<?php section_close(); ?>
