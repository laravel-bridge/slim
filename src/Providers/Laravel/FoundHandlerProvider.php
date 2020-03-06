<?php

namespace LaravelBridge\Slim\Providers\Laravel;

use Illuminate\Container\Container as LaravelContainer;
use Illuminate\Support\ServiceProvider;
use LaravelBridge\Slim\Handlers\Strategies\RequestResponse;
use Slim\Http\Request;
use Slim\Http\Response;

class FoundHandlerProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('foundHandler', function () {
            return new RequestResponse($this->app);
        }, true);

        $this->app->bind(Request::class, function (LaravelContainer $container) {
            return $container->get('request');
        }, true);

        $this->app->bind(Response::class, function (LaravelContainer $container) {
            return $container->get('response');
        }, true);
    }
}
