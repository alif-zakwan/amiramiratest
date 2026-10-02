<?php
/**
 * RSVP endpoint.
 *
 *   POST rsvp.php (JSON body: name, attending, pax) -> saves an RSVP, returns { ok:true }
 *
 * RSVPs are private: nothing here lists them (guests' names and attendance
 * are not shown publicly). Guest messages are a separate feature, see
 * guestbook.php. Storage: Google Sheet or data/rsvp.json, see includes/store.php.
 */

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

$cfg = require __DIR__ . '/config/config.php';
date_default_timezone_set($cfg['timezone'] ?? 'Asia/Kuala_Lumpur');
require __DIR__ . '/includes/store.php';

$store    = store_settings($cfg);
$dataFile = __DIR__ . '/data/rsvp.json';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    respond(405, ['ok' => false, 'error' => 'Method not allowed']);
}

$body = json_decode((string) file_get_contents('php://input'), true);
if (!is_array($body)) {
    // fall back to normal form POST if JSON wasn't sent
    $body = $_POST;
}

// Spam trap: the "website" field is invisible to people. Bots fill it
// in; pretend it worked and store nothing.
if (trim((string) ($body['website'] ?? '')) !== '') {
    respond(200, ['ok' => true]);
}

$name      = trim((string)($body['name'] ?? ''));
$attending = trim((string)($body['attending'] ?? ''));
$pax       = (int)($body['pax'] ?? 1);

if ($name === '' || !in_array($attending, ['hadir', 'tidak_hadir'], true)) {
    respond(422, ['ok' => false, 'error' => 'Sila lengkapkan nama dan kehadiran.']);
}

$entry = [
    'name'      => mb_substr($name, 0, 80),
    'attending' => $attending,
    'pax'       => $attending === 'hadir' ? max(1, min(6, $pax)) : 0,
    'message'   => '',
    'time'      => date('c'),
];

if ($store['useSheet'] && sheet_call($store, 'rsvp', $entry) !== null) {
    respond(200, ['ok' => true]);
}
if ($store['useSheet']) {
    // Google unreachable: keep the RSVP locally rather than lose it.
    error_log('RSVP saved to data/rsvp.json because the Google Sheet was unreachable: ' . $entry['name']);
}

if (append_entry($dataFile, $entry + ['ip' => $_SERVER['REMOTE_ADDR'] ?? '']) === null) {
    respond(500, ['ok' => false, 'error' => 'Tidak dapat menyimpan RSVP. Sila cuba lagi sebentar.']);
}
respond(200, ['ok' => true]);
