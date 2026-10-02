<?php
/**
 * Guestbook ("Ucapan Tetamu") endpoint — wishes, prayers and notes for the couple.
 * Separate from RSVP: no attendance, no guest count.
 *
 *   GET  guestbook.php            -> { ok:true, wishes:[ {name, message}, ... ] }  newest first
 *   POST guestbook.php (JSON)     -> saves { name, message }, returns the same shape
 *
 * Storage: the "Ucapan Tetamu" tab of the Google Sheet, or data/ucapan.json
 * (see includes/store.php). Only name and message are ever sent to visitors.
 */

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

$cfg = require __DIR__ . '/config/config.php';
date_default_timezone_set($cfg['timezone'] ?? 'Asia/Kuala_Lumpur');
require __DIR__ . '/includes/store.php';

$store     = store_settings($cfg);
$dataFile  = __DIR__ . '/data/ucapan.json';
$cacheFile = sys_get_temp_dir() . '/arifamira-ucapan-' . md5($store['url']) . '.json';
$rateFile  = sys_get_temp_dir() . '/arifamira-gb-' . md5($_SERVER['REMOTE_ADDR'] ?? '');
const LIST_LIMIT          = 50;   // newest wishes shown
const CACHE_SECONDS       = 30;
const MIN_SECONDS_BETWEEN = 8;    // per visitor, against rapid-fire spam
const NAME_MAX            = 60;
const MESSAGE_MAX         = 280;

/** Only what visitors may see: name + message. */
function public_wishes(array $entries): array {
    return array_map(fn ($e) => [
        'name'    => (string) ($e['name'] ?? ''),
        'message' => (string) ($e['message'] ?? ''),
    ], array_slice($entries, 0, LIST_LIMIT));
}

/** Newest-first wishes from whichever store is active. */
function current_wishes(): array {
    global $store, $cacheFile, $dataFile;
    if ($store['useSheet']) {
        $wishes = cache_read($cacheFile, CACHE_SECONDS) ?? sheet_call($store, 'ucapan', null);
        if ($wishes !== null) {
            cache_write($cacheFile, $wishes);
            return $wishes;
        }
    }
    return array_reverse(read_entries($dataFile));
}

/** Trims, drops control characters, keeps at most one blank line. */
function clean_text(string $text, int $max): string {
    $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', str_replace("\r\n", "\n", $text)) ?? '';
    $text = preg_replace("/\n{3,}/", "\n\n", trim($text)) ?? '';
    return mb_substr($text, 0, $max);
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($method === 'GET') {
    respond(200, ['ok' => true, 'wishes' => public_wishes(current_wishes())]);
}

if ($method !== 'POST') {
    respond(405, ['ok' => false, 'error' => 'Method not allowed']);
}

$body = json_decode((string) file_get_contents('php://input'), true);
if (!is_array($body)) {
    $body = $_POST;
}

// Spam trap: invisible to people, filled in by bots. Pretend it worked.
if (trim((string) ($body['website'] ?? '')) !== '') {
    respond(200, ['ok' => true, 'wishes' => public_wishes(current_wishes())]);
}

$name    = clean_text(preg_replace('/\s+/', ' ', (string) ($body['name'] ?? '')) ?? '', NAME_MAX);
$message = clean_text((string) ($body['message'] ?? ''), MESSAGE_MAX);

if ($name === '') {
    respond(422, ['ok' => false, 'error' => 'Sila isi nama anda.']);
}
if (mb_strlen($message) < 2) {
    respond(422, ['ok' => false, 'error' => 'Sila tulis ucapan anda.']);
}

// Too soon after this visitor's last wish?
if (is_file($rateFile) && time() - filemtime($rateFile) < MIN_SECONDS_BETWEEN) {
    respond(429, ['ok' => false, 'error' => 'Sila tunggu sebentar sebelum menghantar ucapan lagi.']);
}

// The same wish again (double tap, refresh): accept quietly, store once.
$same = fn ($a, $b) => mb_strtolower(trim((string) $a)) === mb_strtolower(trim((string) $b));
foreach (array_slice(current_wishes(), 0, 20) as $existing) {
    if ($same($existing['name'] ?? '', $name) && $same($existing['message'] ?? '', $message)) {
        respond(200, ['ok' => true, 'wishes' => public_wishes(current_wishes())]);
    }
}

$entry = ['name' => $name, 'message' => $message, 'time' => date('c')];

if ($store['useSheet']) {
    $wishes = sheet_call($store, 'ucapan', $entry);
    if ($wishes !== null) {
        cache_write($cacheFile, $wishes);
        @touch($rateFile);
        respond(200, ['ok' => true, 'wishes' => public_wishes($wishes)]);
    }
    error_log('Guestbook wish saved to data/ucapan.json because the Google Sheet was unreachable: ' . $name);
}

$entries = append_entry($dataFile, $entry + ['ip' => $_SERVER['REMOTE_ADDR'] ?? '']);
if ($entries === null) {
    respond(500, ['ok' => false, 'error' => 'Tidak dapat menyimpan ucapan. Sila cuba lagi sebentar.']);
}
@unlink($cacheFile);
@touch($rateFile);
respond(200, ['ok' => true, 'wishes' => public_wishes(array_reverse($entries))]);
