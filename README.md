# guzzle-router
[![Build Status](https://travis-ci.com/dburiy/guzzle-router.svg?branch=master)](https://travis-ci.com/dburiy/guzzle-router)
[![Total Downloads](https://poser.pugx.org/dburiy/guzzle-router/d/total.png)](https://packagist.org/packages/dburiy/guzzle-router/stats)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

## Install 
The recommended way to install this package is through Composer:

```
$ composer require dburiy/guzzle-router 1.0
```

#### Example

```
<?php

use Dburiy\Router\Router;

include 'vendor/autoload.php';

$options = [
    'timeout'  => 10,
    'verify' => false
];

$client = new GuzzleHttp\Client($options);

$router = new Router($client);

$router->map('get', 'info', 'https://api.myip.com');

try {
    $response = $router->call('info')->getBody()->getContents();
    $json = json_decode($response, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    var_dump($json);
} catch (\Exception $e) {
    var_dump($e);
}
```
