<?php
/**
 * Builds cloudflare/public/ — the files Cloudflare Pages serves — from the
 * unchanged ArifAmira/ project. Nothing in ArifAmira/ is modified.
 *
 *   php cloudflare/build.php
 *
 * What it does:
 *   1. runs ArifAmira/index.php once and saves the page as public/index.html
 *      (the page is the same for every visitor, so it doesn't need PHP at runtime)
 *   2. copies css/ and js/, and only the files under assets/ that the page really uses
 *   3. adds the static settings in cloudflare/static/ (_headers, _routes.json, _redirects, 404.html)
 *   4. if self-hosted fonts exist (php cloudflare/fonts.php), uses them instead of Google Fonts
 *   5. refuses to finish if the real Google Sheet URL/secret appears in the output
 *
 * Run it again after changing ArifAmira/config/config.php or any page file,
 * then commit cloudflare/public/ and push.
 */

$repo   = dirname(__DIR__);
$app    = $repo . '/ArifAmira';
$here   = __DIR__;
$out    = $here . '/public';

function fail(string $message): never {
    fwrite(STDERR, "BUILD FAILED: $message\n");
    exit(1);
}

function rrmdir(string $dir): void {
    if (!is_dir($dir)) return;
    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST) as $item) {
        $item->isDir() ? rmdir($item->getPathname()) : unlink($item->getPathname());
    }
    rmdir($dir);
}

function copy_to(string $from, string $to): void {
    if (!is_dir(dirname($to))) mkdir(dirname($to), 0777, true);
    copy($from, $to) || fail("could not copy $from");
}

if (!is_file("$app/index.php")) fail('ArifAmira/index.php not found');
if (basename($out) !== 'public' || basename(dirname($out)) !== 'cloudflare') fail('unexpected output path');

// ---- 1. render the page ---------------------------------------------------------------------
$process = proc_open([PHP_BINARY, 'index.php'], [1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes, $app);
if (!is_resource($process)) fail('could not start PHP');
$html = stream_get_contents($pipes[1]);
$errors = stream_get_contents($pipes[2]);
fclose($pipes[1]); fclose($pipes[2]);
if (proc_close($process) !== 0 || trim($html) === '') fail("index.php did not render:\n$errors");
if ($errors !== '' && stripos($errors, 'warning') !== false) fail("PHP warnings while rendering:\n$errors");

rrmdir($out);
mkdir($out, 0777, true);

// ---- 4. self-hosted fonts (optional) ---------------------------------------------------------------
$fontsDir = "$here/fonts";
$selfHosted = is_file("$fontsDir/fonts.css");
if ($selfHosted) {
    $before = $html;
    // the Google Fonts links (preconnect + the two stylesheets) become one local stylesheet
    $html = preg_replace('#<link rel="preconnect" href="https://fonts\.googleapis\.com">\s*#', '', $html);
    $html = preg_replace('#<link rel="preconnect" href="https://fonts\.gstatic\.com" crossorigin>\s*#', '', $html);
    $html = preg_replace('#<link rel="stylesheet" href="https://fonts\.googleapis\.com/[^"]*">\s*#', '', $html, -1, $removed);
    if ($removed !== 2) fail("expected 2 Google Fonts stylesheet links, found $removed");
    $preloads = '';
    foreach (glob("$fontsDir/preload-*.woff2") ?: [] as $file) {
        $preloads .= '<link rel="preload" as="font" type="font/woff2" crossorigin href="assets/fonts/' . basename($file) . "\">\n";
    }
    $html = str_replace('<link rel="stylesheet" href="css/tokens.css">', $preloads . '<link rel="stylesheet" href="assets/fonts/fonts.css">' . "\n" . '<link rel="stylesheet" href="css/tokens.css">', $html, $count);
    if ($count !== 1) fail('could not place the local fonts stylesheet');
    foreach (glob("$fontsDir/*") as $file) {
        if (is_file($file)) copy_to($file, "$out/assets/fonts/" . basename($file));
    }
}
file_put_contents("$out/index.html", $html);

// ---- 2. css, js, and the assets they use -----------------------------------------------------------
$copied = ['index.html' => strlen($html)];
foreach (['css', 'js'] as $folder) {
    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator("$app/$folder", FilesystemIterator::SKIP_DOTS)) as $file) {
        if (!$file->isFile()) continue;
        $relative = $folder . substr($file->getPathname(), strlen("$app/$folder"));
        $relative = str_replace('\\', '/', $relative);
        copy_to($file->getPathname(), "$out/$relative");
        $copied[$relative] = $file->getSize();
    }
}

// every assets/... path mentioned by the page, the stylesheets and the scripts
$haystack = $html;
foreach (array_keys($copied) as $relative) {
    if (preg_match('/\.(css|js)$/', $relative) && $relative !== 'index.html') {
        $haystack .= "\n" . file_get_contents("$out/$relative");
    }
}
preg_match_all('#assets/[A-Za-z0-9_\-./ ]+?\.(?:webp|png|jpe?g|svg|gif|ico|mp3|ogg|m4a|ttf|otf|woff2?)#i', $haystack, $found);
$assets = array_values(array_unique($found[0]));
sort($assets);
foreach ($assets as $asset) {
    if (str_starts_with($asset, 'assets/fonts/')) continue;           // the self-hosted fonts were copied above
    if (!is_file("$app/$asset")) fail("the page refers to $asset, which does not exist in ArifAmira/");
    copy_to("$app/$asset", "$out/$asset");
    $copied[$asset] = filesize("$app/$asset");
}

// ---- 3. static settings ---------------------------------------------------------------------------------
foreach (['_headers', '_routes.json', '_redirects', '404.html'] as $file) {
    if (!is_file("$here/static/$file")) fail("cloudflare/static/$file is missing");
    copy_to("$here/static/$file", "$out/$file");
}

// ---- 5. the secret must not be in anything we publish ----------------------------------------------------------
$private = [];
$local = "$app/config/config.local.php";
if (is_file($local)) {
    $values = (require $local)['rsvp_sheet'] ?? [];
    $private[] = $values['secret'] ?? '';
    $private[] = basename(dirname($values['url'] ?? ''));             // the long id inside the Apps Script URL
}
$private[] = getenv('RSVP_SHEET_SECRET') ?: '';
foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($out, FilesystemIterator::SKIP_DOTS)) as $file) {
    if (!$file->isFile() || preg_match('/\.(webp|png|jpe?g|mp3|woff2?|ttf)$/i', $file->getFilename())) continue;
    $text = file_get_contents($file->getPathname());
    foreach ($private as $secret) {
        if (strlen($secret) >= 6 && str_contains($text, $secret)) {
            rrmdir($out);
            fail('the Google Sheet secret/URL was found in ' . $file->getFilename() . ' — build removed');
        }
    }
    if (str_contains($text, 'script.google.com/macros')) {
        rrmdir($out);
        fail('a Google Apps Script URL was found in ' . $file->getFilename() . ' — build removed');
    }
}

// ---- summary ----------------------------------------------------------------------------------------------------------
$total = array_sum($copied);
echo "Built cloudflare/public: " . count($copied) . " files, " . round($total / 1024 / 1024, 2) . " MB\n";
$byType = [];
foreach ($copied as $name => $size) {
    $type = str_contains($name, '/') ? explode('/', $name)[0] . (str_starts_with($name, 'assets/') ? '/' . explode('/', $name)[1] : '') : 'page';
    $byType[$type] = ($byType[$type] ?? 0) + $size;
}
arsort($byType);
foreach ($byType as $type => $size) printf("  %-16s %8.1f KB\n", $type, $size / 1024);
echo $selfHosted ? "Fonts: self-hosted\n" : "Fonts: Google Fonts (run `php cloudflare/fonts.php` to self-host them)\n";
