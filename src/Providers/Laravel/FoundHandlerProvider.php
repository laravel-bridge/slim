<?php

namespace LaravelBridge\Slim\Providers\Laravel;

use Illuminate\Support\ServiceProvider;
use LaravelBridge\Slim\Handlers\Strategies\RequestResponse;

class FoundHandlerProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('foundHandler', function () {
            return new RequestResponse($this->app);
        }, true);
    }
}
