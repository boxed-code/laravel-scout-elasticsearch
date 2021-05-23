# Laravel Scout Elasticsearch Driver

[![Latest Stable Version](https://poser.pugx.org/boxed-code/laravel-scout-elasticsearch/v/stable)](https://packagist.org/packages/boxed-code/laravel-scout-elasticsearch)
[![License](https://poser.pugx.org/boxed-code/laravel-scout-elasticsearch/license)](https://packagist.org/packages/boxed-code/laravel-scout-elasticsearch)
[![Tests](https://github.com/boxed-code/laravel-scout-elasticsearch/actions/workflows/run_tests.yml/badge.svg)](https://github.com/boxed-code/laravel-scout-elasticsearch/actions/workflows/run_tests.yml)

A basic [elastic](https://www.elastic.co/products/elasticsearch) search backed driver [for Laravel Scout](https://laravel.com/docs/8.0/scout). 

This driver has a simple configuration, requiring you to set the hostname of your elasticsearch node.

Note that this driver uses a separate index for each model type as elasticsearch mapping types have been deprecated in elasticsearch 6.0 and will be removed in 8.0, for more information see https://bit.ly/2TZVZvq.

By default, the driver uses the `simple_query_string` full text parser with `and` as the default operator. More information on the functionality & syntax of this query format can be found [here](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-simple-query-string-query.html).

**Requires Scout ^8.0 or ^9.0 & PHP >=7.2**. Based on the original work of [ErickTamayo](https://github.com/ErickTamayo/laravel-scout-elastic)
(Scout 5/6/7 & elastic search server 5.x are supported by versions of 1.x)

## Contents

- [Installation](#installation)
- [Upgrading from 1.x --> 2.x](#upgrading-from-1x---2x)
- [Usage](#usage)
- [Credits](#credits)
- [License](#license)

## Installation

You can install the package via composer:

``` bash
composer require boxed-code/laravel-scout-elasticsearch
```

If you're using Laravel 5.4, you must add the Scout service provider and the package service provider in your app.php config:

```php
// config/app.php
'providers' => [
    ...
    Laravel\Scout\ScoutServiceProvider::class,
    ...
    ScoutEngines\Elasticsearch\ElasticsearchProvider::class,
],
```

### Add the elastic search configuration

Add the following to your scout configuration:

```php
// config/scout.php
// Set your driver to elasticsearch
    'driver' => env('SCOUT_DRIVER', 'elasticsearch'),

...
    'elasticsearch' => [
        'debug' => false,
        'hosts' => [
            env('ELASTICSEARCH_HOST', 'http://localhost'),
        ],
    ],
...
```

## Upgrading from 1.x -> 2.x

If you are upgrading from 1.x -> 2.x and are using an elastic server version < 7.x you will need to constrain the version of the base elastic driver used in your composer.json. For example if you're running 6.x:

    composer require elasticsearch/elasticsearch:^6.0

** Remember: When you upgrade your server version to 7.x you will need to remove the package or upgrade it to the 7.x driver. **

## Usage

Now you can use Laravel Scout as described in the [official documentation](https://laravel.com/docs/5.8/scout)

## Credits

- [Oliver Green](https://github.com/olsgreen)
- [Erick Tamayo](https://github.com/ericktamayo)
- [All Contributors](../../contributors)

## License

The MIT License (MIT).
