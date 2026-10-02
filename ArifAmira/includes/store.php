<?php
/**
 * Storage helpers shared by rsvp.php and guestbook.php.
 *
 * Both endpoints save to a Google Sheet when one is configured (see
 * docs/google-sheets-rsvp/), and to a local JSON file in data/ otherwise
 * or if Google can't be reached, so a submission is never lost.
 * data/ is blocked from the web (.htaccess / Dockerfile) because
 * entries there also hold the guest's IP address.
 *
 * The sheet URL and secret come from the RSVP_SHEET_URL / RSVP_SHEET_SECRET
 * environment variables if set (use these on Render), else from config.php.
 */

/** Google Sheet connection settings. */
function store_settings(array $cfg): array {
    $url = getenv('RSVP_SHEET_URL') ?: ($cfg['rsvp_sheet']['url'] ?? '');
    return [
        'url'      => $url,
        'secret'   => getenv('RSVP_SHEET_SECRET') ?: ($cfg['rsvp_sheet']['secret'] ?? ''),
        'useSheet' => $url !== '' && function_exists('curl_init'),
    ];
}

// ---- local file storage ----------------------------------------------------

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

// ---- Google Sheet storage -----------------------------------------------------

/**
 * Calls the Google Apps Script web app for one kind of entry ('rsvp' or
 * 'ucapan'). Returns the list the script answers with (newest first), or
 * null on any failure. Apps Script answers with a redirect, which curl follows.
 *
 * For any kind other than 'rsvp' the secret is sent as "secret|kind". An older
 * script that doesn't know about kinds rejects that as forbidden, instead of
 * quietly writing a guestbook message into the RSVP tab.
 */
function sheet_call(array $store, string $kind, ?array $payload): ?array {
    $sent = $kind === 'rsvp' ? $store['secret'] : $store['secret'] . '|' . $kind;
    $url  = $store['url'];
    $ch = curl_init($payload === null ? $url . (str_contains($url, '?') ? '&' : '?') . 'secret=' . rawurlencode($sent) : $url);
    $options = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS      => 5,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT        => 15,
    ];
    if ($payload !== null) {
        $options[CURLOPT_POST]       = true;
        $options[CURLOPT_POSTFIELDS] = json_encode($payload + ['secret' => $sent, 'kind' => $kind], JSON_UNESCAPED_UNICODE);
        $options[CURLOPT_HTTPHEADER] = ['Content-Type: text/plain;charset=utf-8'];
    }
    curl_setopt_array($ch, $options);
    $body  = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    $data = is_string($body) ? json_decode($body, true) : null;
    if (!is_array($data) || empty($data['ok']) || !is_array($data['wishes'] ?? null)
        || ($data['kind'] ?? 'rsvp') !== $kind) {
        error_log("Sheet call ($kind) failed: " . ($error !== '' ? $error : substr((string) $body, 0, 200)));
        return null;
    }
    return $data['wishes'];
}

// ---- small cache (Google answers more slowly than a local file) --------------------

function cache_read(string $file, int $seconds): ?array {
    if (!is_file($file) || time() - filemtime($file) > $seconds) return null;
    $data = json_decode((string) file_get_contents($file), true);
    return is_array($data) ? $data : null;
}

function cache_write(string $file, array $data): void {
    @file_put_contents($file, json_encode($data, JSON_UNESCAPED_UNICODE), LOCK_EX);
}

function respond(int $status, array $body): void {
    http_response_code($status);
    echo json_encode($body, JSON_UNESCAPED_UNICODE);
    exit;
}
