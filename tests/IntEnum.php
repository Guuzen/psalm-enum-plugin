<?php

namespace Guuzen\PsalmEnumPlugin\Tests;

enum IntEnum: int
{
    case ONE = 1;

    /**
     * @param 2 $value
     */
    public static function createSelf(int $value): void
    {
        /**
         * @psalm-suppress InvalidEnumValue
         */
        self::from($value);
    }

    /**
     * @param 2 $value
     */
    public static function createAlias(int $value): void
    {
        /**
         * @psalm-suppress InvalidEnumValue
         */
        IntEnum::from($value);
    }


    /**
     * @param 2 $value
     */
    public static function createFqcn(int $value): void
    {
        /**
         * @psalm-suppress InvalidEnumValue
         */
        \Guuzen\PsalmEnumPlugin\Tests\IntEnum::from($value);
    }

    public static function nonLiteralInt(int $value): void
    {
        // non-literal ints are allowed for now
        self::from($value);
    }
}
