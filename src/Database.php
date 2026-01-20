<?php

declare(strict_types=1);

namespace App;

use PDO;
use PDOException;

class Database
{
    private PDO $pdo;

    public function __construct(array $config)
    {
        $dbPath = $config['database']['path'];
        $dbDir = dirname($dbPath);

        if (!is_dir($dbDir)) {
            mkdir($dbDir, 0755, true);
        }

        try {
            $this->pdo = new PDO('sqlite:' . $dbPath);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new \RuntimeException('Database connection failed: ' . $e->getMessage());
        }
    }

    public function getPdo(): PDO
    {
        return $this->pdo;
    }
}
