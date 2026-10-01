<?php
/**
 * Loads the config, fills defaults for keys added after the first
 * version (so an older config.php keeps working), and works out the
 * few values the templates need: guest name, experience mode, the
 * countdown target and which sections to render.
 *
 * Exposes: $cfg, $mode, $showModeSwitcher, $sections, and the helpers e() / t().
 */

$cfg = require __DIR__ . '/../config/config.php';

$cfg += [
    'timezone'   => 'Asia/Kuala_Lumpur',
    'experience' => [],
    'labels'     => [],
    'music_file' => '',
];
$cfg['experience'] += ['default_mode' => 'scroll', 'allowed_modes' => ['scroll'], 'show_mode_switcher' => false];

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

// ---- experience mode: ?mode=scroll|page overrides the config -------
$allowedModes = array_values(array_intersect((array) $cfg['experience']['allowed_modes'], ['scroll', 'page'])) ?: ['scroll'];
$mode = $_GET['mode'] ?? $cfg['experience']['default_mode'];
if (!in_array($mode, $allowedModes, true)) {
    $mode = in_array($cfg['experience']['default_mode'], $allowedModes, true)
        ? $cfg['experience']['default_mode']
        : $allowedModes[0];
}
$showModeSwitcher = !empty($cfg['experience']['show_mode_switcher']) && count($allowedModes) > 1;

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

/** Link to the current page in another mode. */
function mode_url(string $targetMode): string
{
    $query = $_GET;
    $query['mode'] = $targetMode;
    return '?' . http_build_query($query);
}
