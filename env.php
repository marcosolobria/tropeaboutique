<?php
// Minimal .env loader - no Composer/dotenv dependency available on this host.
// Values already set in the real environment (e.g. by the hosting panel) take precedence.
function load_env(string $path): void {
    if (!is_readable($path)) {
        return;
    }
    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#' || !str_contains($line, '=')) {
            continue;
        }
        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value, " \t\"'");
        if (getenv($key) === false) {
            putenv("$key=$value");
        }
    }
}

// En el servidor el .env vive FUERA de public_html, donde la web no puede
// alcanzarlo ni aunque el .htaccess falle. En local está junto a este fichero.
load_env(dirname(__DIR__) . '/.env');
load_env(__DIR__ . '/.env');
?>
