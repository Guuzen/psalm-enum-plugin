# The problem

Psalm doesn't detect situations when we try to create backed enums using `from` method with a wrong literal

For example:

```php
enum StringEnum: string
{
    case ONE = 'one';

    /**
     * @param 'two' $value
     */
    public static function createSelf(string $value): void
    {
        /**
         * Psalm won't complain
         */
        self::from($value);
    }
}

```

Might be useful if you generate OAS enums as phpdoc union literals and want to know about type problems 
before running your code

# Installation

[Psalm documentation](https://psalm.dev/docs/running_psalm/plugins/using_plugins/#installing-plugins)

```
composer require --dev guuzen/psalm-enum-plugin
```

It needs to be enabled by either using psalm-plugin tool:

```
vendor/bin/psalm-plugin enable guuzen/psalm-enum-plugin
```

or by manually adding to your psalm.xml
```xml
<plugins>
    <pluginClass class="Guuzen\PsalmEnumPlugin\Plugin"/>
</plugins>
```
