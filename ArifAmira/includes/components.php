<?php
/**
 * Small reusable pieces of card markup, shared by every section and by
 * both experience modes. Content always comes from $cfg; animation code
 * only looks at the data-anim hooks, never at the wording.
 *
 *   data-anim="card"    the card itself (fades / rises in)
 *   data-anim="floral"  floral sprays (bloom in after the card)
 *   data-anim="item"    content lines (soft blur-to-focus, staggered)
 */

/** Line · diamonds · flourish divider from the printed card. */
function divider(string $modifier = ''): void
{
    $class = 'divider' . ($modifier !== '' ? ' divider--' . $modifier : '');
    echo '<svg class="' . $class . '" data-anim="item" aria-hidden="true" focusable="false"><use href="#ornament-divider"/></svg>';
}

/** Scroll curls + sparkles that sit in a card's four scooped corners. */
function card_corners(): void
{
    echo '<div class="card__corners" aria-hidden="true">';
    foreach (['tl', 'tr', 'bl', 'br'] as $corner) {
        echo '<svg class="card__corner card__corner--' . $corner . '"><use href="#ornament-corner"/></svg>';
        echo '<svg class="card__star card__star--' . $corner . '"><use href="#ornament-star"/></svg>';
    }
    echo '</div>';
}

/**
 * Floral sprays around a card.
 *   'feature' — the card's composition: a lush spray top-right and
 *               another bottom-left, spilling over the frame
 *   'feature-alt' — the same, mirrored (top-left / bottom-right), so
 *               consecutive maroon cards don't repeat exactly
 *   'accent-left' / 'accent-right' — one small spray, for insert cards
 */
function florals(string $variant): void
{
    $sprays = [
        'feature' => [
            ['flower2', 'tr-main', 390, 391],
            ['flower1', 'tr-trail', 295, 662],
            ['flower1', 'bl-trail', 295, 662],
            ['flower3', 'bl-main', 489, 204],
        ],
        'feature-alt' => [
            ['flower2', 'tl-main', 390, 391],
            ['flower1', 'tl-trail', 295, 662],
            ['flower1', 'br-trail', 295, 662],
            ['flower3', 'br-main', 489, 204],
        ],
        'accent-left'  => [['flower3', 'accent-tl', 489, 204]],
        'accent-right' => [['flower2', 'accent-tr', 390, 391]],
    ][$variant] ?? [];

    echo '<div class="florals florals--' . e($variant) . '" aria-hidden="true">';
    foreach ($sprays as [$file, $position, $w, $h]) {
        echo '<span class="floral floral--' . $position . '"><img data-anim="floral" src="assets/images/' . $file
            . '.webp" alt="" width="' . $w . '" height="' . $h . '" decoding="async"></span>';
    }
    echo '</div>';
}

/** "SABTU | 28 NOVEMBER 2026", as set on the card. */
function date_line(string $modifier = ''): void
{
    global $cfg;
    $class = 'date-line' . ($modifier !== '' ? ' date-line--' . $modifier : '');
    echo '<p class="' . $class . '" data-anim="item">'
        . '<span>' . e($cfg['wedding_day_my']) . '</span>'
        . '<span class="date-line__bar" aria-hidden="true"></span>'
        . '<span>' . e($cfg['wedding_date_my']) . '</span>'
        . '</p>';
}

/** Opening tag for a section + its card. Pair with section_close(). */
function section_open(string $id, string $tone, string $label, string $florals = ''): void
{
    echo '<section class="section section--' . e($id) . '" id="' . e($id) . '" data-section aria-label="' . e($label) . '">';
    echo '<div class="card card--' . e($tone) . '" data-anim="card">';
    if ($tone === 'maroon') {
        card_corners();
    }
    if ($florals !== '') {
        florals($florals);
    }
    echo '<div class="card__body">';
}

function section_close(): void
{
    echo '</div></div></section>';
}

/** wa.me link for a phone number: 011-55059882 -> https://wa.me/601155059882 */
function whatsapp_url(string $phone, string $countryCode): string
{
    $digits = preg_replace('/\D+/', '', $phone);
    if (str_starts_with($digits, '0')) {
        $digits = $countryCode . substr($digits, 1);
    }
    return 'https://wa.me/' . $digits;
}
