<?php
/**
 * RSVP endpoint.
 *
 *   GET  rsvp.php            -> { ok:true, wishes:[ {name, attending, message, time}, ... ] }
 *   POST rsvp.php (JSON body) -> saves an entry, returns the same shape
 *
 * Storage:
 *   1. Google Sheet (when a web-app URL is set, see docs/google-sheets-rsvp/):
 *      survives redeploys, and the couple can open the sheet any time.
 *   2. data/rsvp.json — used when no sheet is set, and as a safety net if
 *      Google can't be reached, so an RSVP is never lost.
 *      data/ is blocked from the web (.htaccess / Dockerfile) because
 *      entries there also hold the guest's IP address.
 *
 * The sheet URL and secret come from the RSVP_SHEET_URL / RSVP_SHEET_SECRET
 * environment variables if set (use these on Render), else from config.php.
 */

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

$cfg = require __DIR__ . '/config/config.php';
date_default_timezone_set($cfg['timezone'] ?? 'Asia/Kuala_Lumpur');

$dataFile    = __DIR__ . '/data/rsvp.json';
$sheetUrl    = getenv('RSVP_SHEET_URL')    ?: ($cfg['rsvp_sheet']['url']    ?? '');
$sheetSecret = getenv('RSVP_SHEET_SECRET') ?: ($cfg['rsvp_sheet']['secret'] ?? '');
$useSheet    = $sheetUrl !== '' && function_exists('curl_init');
$cacheFile   = sys_get_temp_dir() . '/arifamira-wishes-' . md5($sheetUrl) . '.json';
const WISHES_CACHE_SECONDS = 30;   // Google is slower than a local file

// ---- local file storage --------------------------------------------------

function decode_entries(string $raw): array {
    $entries = json_decode($raw, true);
    return is_array($entries) ? $entries : [];
}

function read_entries(string $file): array {
    if (!file_exists($file)) return [];
    return decode_entries((string) file_get_contents($file));
}

/** Appends one entry under a single lock, so simultaneous guests can't overwrite each other. */
function append_entry(string $file, array $entry): ?array {
    $handle = @fopen($file, 'c+');
    if ($handle === false || !flock($handle, LOCK_EX)) return null;

    $entries = decode_entries((string) stream_get_contents($handle));
    $entries[] = $entry;

    ftruncate($handle, 0);
    rewind($handle);
    $written = fwrite($handle, json_encode($entries, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    fflush($handle);
    flock($handle, LOCK_UN);
    fclose($handle);

    return $written === false ? null : $entries;
}

function public_view(array $entries): array {
    // Only expose what's safe/nice to show on the public wishes wall.
    return array_map(function ($e) {
        return [
            'name'      => $e['name'] ?? '',
            'attending' => $e['attending'] ?? '',
            'message'   => $e['message'] ?? '',
            'time'      => $e['time'] ?? '',
        ];
    }, $entries);
}

// ---- Google Sheet storage ---------------------------------------------------

/**
 * Calls the Google Apps Script web app. Returns its wishes (newest first),
 * or null on any failure. Apps Script answers with a redirect, which curl follows.
 */
function sheet_call(string $url, string $secret, ?array $payload): ?array {
    $ch = curl_init($payload === null ? $url . (str_contains($url, '?') ? '&' : '?') . 'secret=' . rawurlencode($secret) : $url);
    $options = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS      => 5,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT        => 15,
    ];
    if ($payload !== null) {
        $options[CURLOPT_POST]       = true;
        $options[CURLOPT_POSTFIELDS] = json_encode($payload + ['secret' => $secret], JSON_UNESCAPED_UNICODE);
        $options[CURLOPT_HTTPHEADER] = ['Content-Type: text/plain;charset=utf-8'];
    }
    curl_setopt_array($ch, $options);
    $body  = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    $data = is_string($body) ? json_decode($body, true) : null;
    if (!is_array($data) || empty($data['ok']) || !is_array($data['wishes'] ?? null)) {
        error_log('RSVP sheet call failed: ' . ($error !== '' ? $error : substr((string) $body, 0, 200)));
        return null;
    }
    return $data['wishes'];
}

function cache_read(string $file): ?array {
    if (!is_file($file) || time() - filemtime($file) > WISHES_CACHE_SECONDS) return null;
    $wishes = json_decode((string) file_get_contents($file), true);
    return is_array($wishes) ? $wishes : null;
}

function cache_write(string $file, array $wishes): void {
    @file_put_contents($file, json_encode($wishes, JSON_UNESCAPED_UNICODE), LOCK_EX);
}

function respond(int $status, array $body): void {
    http_response_code($status);
    echo json_encode($body, JSON_UNESCAPED_UNICODE);
    exit;
}

/** Wishes for the public wall, newest first, from whichever store is active. */
function current_wishes(): array {
    global $useSheet, $sheetUrl, $sheetSecret, $cacheFile, $dataFile;
    if ($useSheet) {
        $wishes = cache_read($cacheFile) ?? sheet_call($sheetUrl, $sheetSecret, null);
        if ($wishes !== null) {
            cache_write($cacheFile, $wishes);
            return public_view($wishes);
        }
    }
    return public_view(array_reverse(read_entries($dataFile)));
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($method === 'GET') {
    respond(200, ['ok' => true, 'wishes' => current_wishes()]);
}

if ($method === 'POST') {
    $raw = file_get_contents('php://input');
    $body = json_decode($raw, true);

    if (!is_array($body)) {
        // fall back to normal form POST if JSON wasn't sent
        $body = $_POST;
    }

    // Spam trap: the "website" field is invisible to people. Bots fill it
    // in; pretend it worked and store nothing.
    if (trim((string) ($body['website'] ?? '')) !== '') {
        respond(200, ['ok' => true, 'wishes' => current_wishes()]);
    }

    $name      = trim((string)($body['name'] ?? ''));
    $attending = trim((string)($body['attending'] ?? ''));
    $pax       = (int)($body['pax'] ?? 1);
    $message   = trim((string)($body['message'] ?? ''));

    if ($name === '' || !in_array($attending, ['hadir', 'tidak_hadir'], true)) {
        respond(422, ['ok' => false, 'error' => 'Sila lengkapkan nama dan kehadiran.']);
    }

    // basic length guards, this isn't meant to hold essays
    $name    = mb_substr($name, 0, 80);
    $message = mb_substr($message, 0, 240);
    $pax     = $attending === 'hadir' ? max(1, min(6, $pax)) : 0;

    $entry = [
        'name'      => $name,
        'attending' => $attending,
        'pax'       => $pax,
        'message'   => $message,
        'time'      => date('c'),
    ];

    if ($useSheet) {
        $wishes = sheet_call($sheetUrl, $sheetSecret, $entry);
        if ($wishes !== null) {
            cache_write($cacheFile, $wishes);
            respond(200, ['ok' => true, 'wishes' => public_view($wishes)]);
        }
        // Google unreachable: keep the RSVP locally rather than lose it.
        error_log('RSVP saved to data/rsvp.json because the Google Sheet was unreachable: ' . $name);
    }

    $entries = append_entry($dataFile, $entry + ['ip' => $_SERVER['REMOTE_ADDR'] ?? '']);
    if ($entries === null) {
        respond(500, ['ok' => false, 'error' => 'Tidak dapat menyimpan RSVP. Sila cuba lagi sebentar.']);
    }
    @unlink($cacheFile);
    respond(200, ['ok' => true, 'wishes' => public_view(array_reverse($entries))]);
}

respond(405, ['ok' => false, 'error' => 'Method not allowed']);
