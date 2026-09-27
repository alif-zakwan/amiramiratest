<?php
/**
 * RSVP endpoint.
 *
 *   GET  rsvp.php            -> { ok:true, wishes:[ {name, attending, message, time}, ... ] }
 *   POST rsvp.php (JSON body) -> saves an entry, returns the same shape
 *
 * Data is stored in data/rsvp.json as a plain JSON array. No database
 * required, so this works out of the box on a bare PHP localhost.
 */

header('Content-Type: application/json; charset=utf-8');

$dataFile = __DIR__ . '/data/rsvp.json';

function read_entries(string $file): array {
    if (!file_exists($file)) return [];
    $raw = file_get_contents($file);
    $entries = json_decode($raw, true);
    return is_array($entries) ? $entries : [];
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

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($method === 'GET') {
    $entries = read_entries($dataFile);
    // newest first
    $entries = array_reverse($entries);
    echo json_encode(['ok' => true, 'wishes' => public_view($entries)]);
    exit;
}

if ($method === 'POST') {
    $raw = file_get_contents('php://input');
    $body = json_decode($raw, true);

    if (!is_array($body)) {
        // fall back to normal form POST if JSON wasn't sent
        $body = $_POST;
    }

    $name      = trim((string)($body['name'] ?? ''));
    $attending = trim((string)($body['attending'] ?? ''));
    $pax       = (int)($body['pax'] ?? 1);
    $message   = trim((string)($body['message'] ?? ''));

    if ($name === '' || !in_array($attending, ['hadir', 'tidak_hadir'], true)) {
        http_response_code(422);
        echo json_encode(['ok' => false, 'error' => 'Sila lengkapkan nama dan kehadiran.']);
        exit;
    }

    // basic length guards, this isn't meant to hold essays
    $name    = mb_substr($name, 0, 80);
    $message = mb_substr($message, 0, 240);
    $pax     = max(1, min(6, $pax));

    $entry = [
        'name'      => $name,
        'attending' => $attending,
        'pax'       => $pax,
        'message'   => $message,
        'time'      => date('c'),
        'ip'        => $_SERVER['REMOTE_ADDR'] ?? '',
    ];

    $entries = read_entries($dataFile);
    $entries[] = $entry;

    $written = @file_put_contents(
        $dataFile,
        json_encode($entries, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
        LOCK_EX
    );

    if ($written === false) {
        http_response_code(500);
        echo json_encode(['ok' => false, 'error' => 'Tidak dapat menyimpan RSVP. Sila pastikan folder data/ boleh ditulis (chmod 775).']);
        exit;
    }

    echo json_encode(['ok' => true, 'wishes' => public_view(array_reverse($entries))]);
    exit;
}

http_response_code(405);
echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
