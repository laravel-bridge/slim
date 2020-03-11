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

### Using array as container

App params is array, like following code.

```php
use Slim\App;

$container = [
    SomeClass::class => function() {},
]

$app = new App($container);
```

Replace class name `Slim\App` to bridge class [`LaravelBridge\Slim\App`](/src/App.php):

```php
// Rename use class
use LaravelBridge\Slim\App;
```

It will work on most Slim project. Here has an [example](https://github.com/laravel-bridge/slim-example/tree/using-laravel-bridge) for more detail.

### Using Container

App params is Container, like following code.

```php
use Slim\App;
use Slim\Container;

$container = new Container();
$container[SomeClass::class] = function() {};

$app = new App($container);
```

Use [`ContainerBuilder`](/src/ContainerBuilder.php) is good for this case. The builder will build an instance of [Scratch Application](https://github.com/laravel-bridge/scratch).

```php
use LaravelBridge\Slim\ContainerBuilder;
use Slim\App;

$containerBuilder = new ContainerBuilder();

// Use builder mixin the Scratch Application / Laravel Container
$containerBuilder->singleton(SomeClass::class, function() {});

$containerBuilder->setupDatabase($conncetion)
    ->setupProvider(YourProvider::class);

// Register provider for Slim Framework
$containerBuilder
    ->useLaravelFoundHandler()
    ->useLaravelHttp();

// Build Container and bootstrap
$container = $containerBuilder->buildAndBootstrap();

$app = new App($container);
```

## Using Laravel Services

`LaravelBridge\Slim\App` will use the Slim default service (e.g. `Slim\Handlers\Error`). If you want to use the Laravel Error handler, you can set the second argument. It will use all Laravel service defined in this bridge.

```php
use LaravelBridge\Slim\App;

$app = new App([], true);
```

ContainerBuilder is like Bridge App:

```php
use LaravelBridge\Slim\ContainerBuilder;

$app = new ContainerBuilder([], true);
```

### `foundHandler`

The `foundHandler` in Slim is invoke when the route found.

This bridge implements a auto injection handler for call a callable, names [`RequestResponse`](/src/Handlers/Strategies/RequestResponse.php). Use Laravel Service or call `ContainerBuilder::useLaravelFoundHandler()` can enable handler.

```php
$container = (new ContainerBuilder())
    ->useLaravelFoundHandler()
    ->buildAndBootstrap();

$app = new App($container);

$app->get('/', function (IlluminateRequest $request, $args) {
    // Auto-inject Illuminate Request in clousre
});
```

### `callableResolver`

This bridge implements a auto injection handler for new controller, names [`CallableResolver`](/src/CallableResolver.php). Use Laravel Service or call `ContainerBuilder::useLaravelCallableResolver()` to enable.

```php
class SomeController
{
    public function __construct(Dep $dep) {}

    public function __invoke() {}

    public function view() {}
}


$container = (new ContainerBuilder())
    ->useLaravelCallableResolver()
    ->buildAndBootstrap();

$app = new App($container);

// Will call SomeController::__invoke()
$app->get('/', 'SomeController');

// Will call SomeController::view()
$app->get('/', 'SomeController:view');
```

### `settings`

Laravel Bridge use the `Collection` class default. [Using Laravel Services](#using-laravel-services) or call `useLaravelSettings()` method on ContainerBuilder will use the `Illuminate\Config\Repository` class. 

```php
$container = (new ContainerBuilder())
    ->setSettings(['foo' => 'bar'])
    ->useLaravelSettings()
    ->buildAndBootstrap();

// Return a Repository instance
$container->get('settings');
```
