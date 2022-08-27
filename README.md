# Criteria

Criteria - value object to handling filter/where, order, limit, paging

```php
use Xtompie\Criteria\Criteria;

$criteria = Criteria::ofInput([
    ['status' => 'active', 'page' => '4'],
    perpageOptions: [],
    perpageDefault: 10,
]);

$criteria->offset(); // 30
$criteria->limit(); // 10
$criteria->where(); // ['status' => 'active']

```

## Requiments

PHP >= 8.0

## Installation

Using [composer](https://getcomposer.org/)

```
composer require xtompie/criteria
```
