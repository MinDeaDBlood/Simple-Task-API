<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$dbPath = getenv('DB_PATH');

// Normalize database path
if ($dbPath === false || $dbPath === '') {
    // Default path
    $dbPath = $root . '/var/database.sqlite';
} elseif (!preg_match('~^([A-Za-z]:[\\\\/]|/)~', $dbPath)) {
    // Relative path - make it relative to project root
    $dbPath = $root . '/' . ltrim($dbPath, "/\\");
}

return [
    'database' => [
        'driver' => 'sqlite',
        'path' => $dbPath,
    ],
];
