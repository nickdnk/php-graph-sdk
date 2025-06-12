# Facebook SDK for PHP

[![Build Status](https://github.com/nickdnk/php-graph-sdk/actions/workflows/test.yml/badge.svg?branch=master)](https://github.com/nickdnk/php-graph-sdk/actions/workflows/test.yml)
[![Latest Stable Version](http://img.shields.io/badge/Latest%20Stable-8.0.0-blue.svg)](https://packagist.org/packages/nickdnk/graph-sdk)
[![Downloads](https://img.shields.io/packagist/dt/nickdnk/graph-sdk?label=Downloads)](https://packagist.org/packages/nickdnk/graph-sdk)
### This is an unofficial version of Facebook's PHP SDK designed for PHP 8+. It is being maintained and tested against the newest PHP versions. You can use this in place of version `5.x` of Facebook's deprecated `facebook/graph-sdk` package.

### PHP 8.1 is required.

This repository contains the open source PHP SDK that allows you to access the Facebook Platform from your PHP app.

## Installation

The Facebook PHP SDK can be installed with [Composer](https://getcomposer.org/). Run this command:

```sh
composer require nickdnk/graph-sdk
```

By default, the request will be made via a `Facebook\HttpClients\FacebookHttpClientInterface`. The default
implementation depends on the available PHP extension/packages. In order of priority:

1. Package `guzzlehttp/guzzle` (v6 and v7 only): `Facebook\HttpClients\FacebookGuzzleHttpClient`
2. ext-curl: `Facebook\HttpClients\FacebookCurlHttpClient`
3. Fallback: `Facebook\HttpClients\FacebookStreamHttpClient`

## Usage

Simple GET example of a user's profile.

```php
require_once __DIR__ . '/vendor/autoload.php';

use Facebook\Facebook;
use Facebook\GraphNodes\GraphUser;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;

$fb = new Facebook([
  'app_id' => '{app-id}',
  'app_secret' => '{app-secret}',
  'default_graph_version' => 'v20.0',
  //'default_access_token' => '{access-token}', // optional
]);

// Use one of the helper classes to get a Facebook\Authentication\AccessToken entity.
//   $helper = $fb->getRedirectLoginHelper();
//   $helper = $fb->getJavaScriptHelper();
//   $helper = $fb->getCanvasHelper();
//   $helper = $fb->getPageTabHelper();

try {

  // If you provided a 'default_access_token', the '{access-token}' is optional.
  $response = $fb->get('/me', '{access-token}');
  
  // To decode the response to a PHP class, provide the class of the root node in the
  // response. You will have to match this manually based on the endpoint you
  // requested. Please do open a pull request if you want to add more types.
  
  /** @var GraphUser $me */
  $me = $response->getGraphNode(GraphUser::class);
  echo 'Logged in as ' . $me->getName() . PHP_EOL;
  echo 'User email is ' . $me->getEmail() . PHP_EOL;
  
} catch (FacebookResponseException $e) {

  // When Graph returns an error
  echo 'Graph returned an error: ' . $e->getMessage();
  
} catch (FacebookSDKException $e) {

  // When validation fails or other local issues
  echo 'Facebook SDK returned an error: ' . $e->getMessage();
  
}
```

## Tests

1. [Composer](https://getcomposer.org/) is a prerequisite for running the tests.
   Install composer globally, then run `composer install` to install required files.
2. The tests can be executed by running this command from the root directory:

```bash
$ ./vendor/bin/phpunit
```

## License

Please see the [license file](https://github.com/facebook/php-graph-sdk/blob/master/LICENSE)
for more information.
