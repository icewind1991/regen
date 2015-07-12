# Regen

Regenerate php code to target older versions

### Usage

```php

<?php

require 'vendor/autoload.php';

// register the regen autoloader and tell it which namespaces should be re-targeted
\Regen\Loader::register(['\\Regen\\Tests\\SourceClasses']);

// any class autoloaded in the registered namespaces will
// be automatically regenerated to work on the current php version
$testClass = new \Regen\Tests\SourceClasses\TypeHintClass();

// throws a TypeError because the argument is not the correct type
echo $testClass->test(2);
```

Note: only classes loadable by the composer autoloaded can be regenerated.

### Supported features

- php7
 - scalar typehints for `integer`, `float`, `string` and `boolean`
 - spaceship (`<=>`) and null-coalesce operator (`??`)
 - annonymous classes
- php5.5
 - limited support for generators
