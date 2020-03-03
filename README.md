# Laravel Scout Elasticsearch Driver

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://travis-ci.com/boxed-code/laravel-scout-elasticsearch.svg?branch=master)](https://travis-ci.com/boxed-code/laravel-scout-elasticsearch)

A basic [elastic](https://www.elastic.co/products/elasticsearch) search backed driver [for Laravel Scout](https://laravel.com/docs/6.0/scout). 

This driver has a simple configuration, requiring you to set the hostname of your elasticsearch node.

Note that this driver uses a separate index for each model type as elasticsearch mapping types have been deprecated in elasticsearch 6.0 and will be removed in 8.0, for more information see https://bit.ly/2TZVZvq.

By default, the driver uses the `simple_query_string` full text parser with `and` as the default operator. More information on the functionality & syntax of this query format can be found [here](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-simple-query-string-query.html).

**Requires Scout ^7.0 or ^8.0, Laravel >=5.4 & PHP >=7.0**. Based on the original work of [ErickTamayo](https://github.com/ErickTamayo/laravel-scout-elastic)

## Contents

- [Installation](#installation)
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
        'hosts' => [
            env('ELASTICSEARCH_HOST', 'http://localhost'),
        ],
    ],
...
```

## Usage

Now you can use Laravel Scout as described in the [official documentation](https://laravel.com/docs/5.8/scout)

## Credits

- [Oliver Green](https://github.com/olsgreen)
- [Erick Tamayo](https://github.com/ericktamayo)
- [All Contributors](../../contributors)

## License

The MIT License (MIT).
