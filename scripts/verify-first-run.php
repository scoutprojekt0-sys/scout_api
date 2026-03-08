<?php

declare(strict_types=1);

$root = dirname(__DIR__);

$envPath = $root.DIRECTORY_SEPARATOR.'.env';
$vendorAutoloadPath = $root.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';
$sqlitePath = $root.DIRECTORY_SEPARATOR.'database'.DIRECTORY_SEPARATOR.'database.sqlite';

if (!file_exists($sqlitePath)) {
    @touch($sqlitePath);
}

$checks = [
    '.env exists' => $envPath,
    'vendor/autoload.php exists' => $vendorAutoloadPath,
    'database/database.sqlite exists (sqlite profile)' => $sqlitePath,
];

$missing = [];

foreach ($checks as $label => $path) {
    if (file_exists($path)) {
        echo "[OK] {$label}".PHP_EOL;
        continue;
    }

    echo "[MISSING] {$label}".PHP_EOL;
    $missing[] = $label;
}

if ($missing === []) {
    echo PHP_EOL.'Project first-run check passed.'.PHP_EOL;
    exit(0);
}

echo PHP_EOL.'Project first-run check found missing items.'.PHP_EOL;
if (!file_exists($envPath)) {
    echo '- Run setup to create .env from .env.example.'.PHP_EOL;
}
if (!file_exists($vendorAutoloadPath)) {
    echo '- Install dependencies to generate vendor files.'.PHP_EOL;
}
exit(1);
