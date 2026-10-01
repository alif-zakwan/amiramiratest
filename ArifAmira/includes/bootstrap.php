<?php
/**
 * Loads the config, fills defaults for keys added after the first
 * version (so an older config.php keeps working), and works out the
 * few values the templates need: guest name, experience mode, the
 * countdown target and which sections to render.
 *
 * Exposes: $cfg, $guestName, $mode, $sections, and the helpers e() / t().
 */

$cfg = require __DIR__ . '/../config/config.php';

$cfg += [
    'timezone'   => 'Asia/Kuala_Lumpur',
    'experience' => [],
    'labels'     => [],
    'music_file' => '',
];
$cfg['experience'] += ['default_mode' => 'scroll', 'show_mode_switcher' => false];

date_default_timezone_set($cfg['timezone']);

require __DIR__ . '/components.php';

/** HTML-escape for output. */
function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/** Interface label from config 'labels', escaped. */
function t(string $key): string
{
    global $cfg;
    return e($cfg['labels'][$key] ?? '');
}

// ---- guest personalisation: ?to=Puan+Salmah -----------------------
$guestName = isset($_GET['to']) ? trim(mb_substr((string) $_GET['to'], 0, 60)) : '';

// ---- experience mode: ?mode=scroll|page overrides the config -------
$modes = ['scroll', 'page'];
$mode = $_GET['mode'] ?? $cfg['experience']['default_mode'];
if (!in_array($mode, $modes, true)) {
    $mode = 'scroll';
}

// ---- countdown target as ISO-8601 with the venue's UTC offset ------
$weddingAt = new DateTimeImmutable($cfg['wedding_datetime'], new DateTimeZone($cfg['timezone']));
$weddingIso = $weddingAt->format(DATE_ATOM);

// ---- section order (same list drives both scroll and page mode) ----
$sections = ['cover', 'countdown', 'invitation', 'details', 'venue', 'contacts'];
if (!empty($cfg['show_rsvp'])) {
    array_push($sections, 'rsvp', 'wishes');
}
if (!empty($cfg['enable_salam_kaut'])) {
    $sections[] = 'gift';
}
$sections[] = 'closing';

/** Link to the current page in another mode, keeping ?to= intact. */
function mode_url(string $targetMode): string
{
    $query = $_GET;
    $query['mode'] = $targetMode;
    return '?' . http_build_query($query);
}
