<?php

namespace Guuzen\PsalmEnumPlugin\Tests;

enum StringEnum: string
{
    case ONE = 'one';

    /**
     * @param 'two' $value
     */
    public static function createSelf(string $value): void
    {
        /**
         * @psalm-suppress InvalidEnumValue
         */
        self::from($value);
    }

    /**
     * @param 'two' $value
     */
    public static function createAlias(string $value): void
    {
        /**
         * @psalm-suppress InvalidEnumValue
         */
        StringEnum::from($value);
    }


    /**
     * @param 'two' $value
     */
    public static function createFqcn(string $value): void
    {
        /**
         * @psalm-suppress InvalidEnumValue
         */
        \Guuzen\PsalmEnumPlugin\Tests\StringEnum::from($value);
    }

    public static function nonLiteralString(string $value): void
    {
        // non-literal strings are allowed for now
        self::from($value);
    }
}
