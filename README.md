# Doctrine Types Bundle

This bundle adds Doctrine Types.

## Requirements
Symfony ``6.4.*``

PHP ``>=8.2``

## Install
### Composer
```shell
composer require npowest/doctrine-types-bundle
```
### Register bundle

```php
// config/bundles.php

return [
    // ...
    Npowest\Bundle\DoctrineTypes\NpowestDoctrineTypesBundle::class => ['all' => true],
    // ...
]
```
