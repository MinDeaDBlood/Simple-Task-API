<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';

use App\Database;

try {
    $config = require __DIR__ . '/../config/config.php';
    $database = new Database($config);
    $pdo = $database->getPdo();

    $sql = file_get_contents(__DIR__ . '/../migrations/001_create_tasks.sql');
    $pdo->exec($sql);

    echo "Migration completed successfully!\n";
    echo "Database created at: " . $config['database']['path'] . "\n";

} catch (\Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
