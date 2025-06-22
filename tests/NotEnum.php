<?php

declare(strict_types=1);

final class NotEnum
{
    public static function from(string $value): void
    {
    }

    public static function create(): void
    {
        // Not an enum, should be ignored
        self::from('some');
    }
}
