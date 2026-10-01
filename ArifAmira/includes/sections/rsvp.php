<?php
/**
 * RSVP form. Submits JSON to rsvp.php (js/features.js → initRsvp).
 * The number of guests only appears once "Akan hadir" is chosen.
 * The "website" field is a hidden spam trap: people never see it,
 * bots fill it in, and rsvp.php quietly ignores those submissions.
 */
section_open('rsvp', 'ivory', $cfg['labels']['rsvp_title'] ?? '', 'accent-left');
?>
<h2 class="section-title" data-anim="item"><?= t('rsvp_title') ?></h2>
<?php divider('ink'); ?>
<p class="section-note" data-anim="item"><?= t('rsvp_note') ?> <strong><?= e($cfg['rsvp_deadline']) ?></strong>.</p>

<form class="rsvp-form" id="rsvpForm" data-anim="item" novalidate>
    <label class="field">
        <span class="field__label"><?= t('rsvp_name') ?></span>
        <input type="text" id="rsvpName" name="name" maxlength="80" autocomplete="name" required>
    </label>
    <label class="field">
        <span class="field__label"><?= t('rsvp_attending') ?></span>
        <select id="rsvpAttending" name="attending" required>
            <option value="" disabled selected><?= t('rsvp_choose') ?></option>
            <option value="hadir"><?= t('rsvp_yes') ?></option>
            <option value="tidak_hadir"><?= t('rsvp_no') ?></option>
        </select>
    </label>
    <label class="field" id="rsvpPaxField" hidden>
        <span class="field__label"><?= t('rsvp_pax') ?></span>
        <select id="rsvpPax" name="pax" disabled>
            <?php for ($p = 1; $p <= 6; $p++): ?>
            <option value="<?= $p ?>"<?= $p === 1 ? ' selected' : '' ?>><?= $p ?> <?= t('rsvp_pax_unit') ?></option>
            <?php endfor; ?>
        </select>
    </label>
    <label class="field">
        <span class="field__label"><?= t('rsvp_message') ?> <em><?= t('rsvp_optional') ?></em></span>
        <textarea id="rsvpMessage" name="message" rows="3" maxlength="240"></textarea>
    </label>
    <label class="field field--trap" aria-hidden="true">
        Website <input type="text" name="website" tabindex="-1" autocomplete="off">
    </label>
    <p class="rsvp-form__status" id="rsvpStatus" role="status" aria-live="polite"></p>
    <button type="submit" class="btn btn--solid" id="rsvpSubmit"><?= t('rsvp_submit') ?></button>
</form>
<?php section_close(); ?>
