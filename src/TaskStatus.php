<?php

declare(strict_types=1);

namespace App;

class TaskStatus
{
    public const PENDING = 'pending';
    public const IN_PROGRESS = 'in_progress';
    public const DONE = 'done';

    public static function all(): array
    {
        return [
            self::PENDING,
            self::IN_PROGRESS,
            self::DONE,
        ];
    }

    public static function isValid(string $status): bool
    {
        return in_array($status, self::all(), true);
    }
}
