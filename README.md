<p align="center"><img src="docs/logo.svg"></p>

<h1 align="center">Laravel Bridge for Slim Framework</h1>

<p align="center">
<a href="https://travis-ci.com/laravel-bridge/slim"><img src="https://travis-ci.com/laravel-bridge/slim.svg?branch=master" alt="Build Status"></a>
<a href="https://codecov.io/gh/laravel-bridge/slim"><img src="https://codecov.io/gh/laravel-bridge/slim/branch/master/graph/badge.svg" alt="codecov"></a>
<a href="https://packagist.org/packages/laravel-bridge/slim"><img src="https://poser.pugx.org/laravel-bridge/slim/v/stable" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel-bridge/slim"><img src="https://poser.pugx.org/laravel-bridge/slim/downloads" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel-bridge/slim"><img src="https://poser.pugx.org/laravel-bridge/slim/license" alt="License"></a>
</p>

The bridge for Laravel in Slim framework

## Installation

Using Composer to install package:

```bash
composer require laravel-bridge/slim
```

Replace class name `Slim\App` to bridge class [`LaravelBridge\Slim\App`](/src/App.php):

```php
// Rename use class
use LaravelBridge\Slim\App;

// Or rename new instace class
$app = new \LaravelBridge\Slim\App();
```

Finally, it will work on most Slim project. Here has an [example](https://github.com/laravel-bridge/slim-example/tree/using-laravel-bridge) for more detail.

## Using Laravel Service

`LaravelBridge\Slim\App` will use the Slim default service (e.g. `Slim\Handlers\Error`). If you want to use the Laravel Error handler, you can set the second argument, It will use all Laravel service defined in this bridge.

```php
$app = new App([], true);
```

If you want to use some of Laravel service, use the [`ContainerBuilder`](/src/ContainerBuilder.php).

```php
$container = (new ContainerBuilder())
    ->useLaravelErrorHandler()
    ->useLaravelNotFoundHandler()
    ->build();

$app = new App($container);
```

### `foundHandler`

The `foundHandler` in Slim is invoke when the route found.

This bridge implements a auto injection handler like Laravel, names [`RequestResponse`](/src/Handlers/Strategies/RequestResponse.php). Use Laravel Service or call `ContainerBuilder::useLaravelFoundHandler()` can enable handler.

```php
$container = (new ContainerBuilder())
    ->useLaravelFoundHandler()
    ->build();

$app = new App($container);

$app->get('/', function (IlluminateRequest $request, $args) {
    // Auto-inject Illuminate Request in clousre
});
```

### `settings`

Slim use the `Collection` class, Laravel Bridge use the `Fluent` class.
