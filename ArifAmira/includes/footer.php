<?php
/**
 * Page-mode navigation, the settings js/ needs, and the scripts.
 * Only the wording JS displays is passed through — nothing else.
 */
$jsLabels = array_intersect_key($cfg['labels'], array_flip([
    'rsvp_submit', 'rsvp_sending', 'rsvp_ok', 'rsvp_required', 'rsvp_failed', 'rsvp_offline',
    'gb_submit', 'gb_sending', 'gb_ok', 'gb_name_required', 'gb_message_required', 'gb_failed', 'gb_offline',
    'gb_prev', 'gb_next', 'gift_copied', 'share_copied',
]));
?>
<?php if ($mode === 'page'): ?>
<nav class="page-nav" id="pageNav" aria-label="<?= t('mode_page') ?>">
    <button class="page-nav__btn" type="button" data-page-step="-1" aria-label="<?= t('page_prev') ?>">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="none" stroke="currentColor" stroke-width="1.5" d="M15 5l-7 7 7 7"/></svg>
    </button>
    <ol class="page-nav__dots" id="pageDots"></ol>
    <button class="page-nav__btn" type="button" data-page-step="1" aria-label="<?= t('page_next') ?>">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="none" stroke="currentColor" stroke-width="1.5" d="M9 5l7 7-7 7"/></svg>
    </button>
</nav>
<?php endif; ?>

<script type="application/json" id="appSettings"><?= json_encode(
    ['mode' => $mode, 'labels' => $jsLabels],
    JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP
) ?></script>
<script src="js/vendor/gsap.min.js" defer></script>
<script type="module" src="js/app.js"></script>
</body>
</html>
