<?php

return [
    'database' => [
        'driver' => 'sqlite',
        'path' => getenv('DB_PATH') ?: __DIR__ . '/../var/database.sqlite',
    ],
];
