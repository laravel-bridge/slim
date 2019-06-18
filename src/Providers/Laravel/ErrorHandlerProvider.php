<?php

namespace LaravelBridge\Slim\Providers\Laravel;

use Illuminate\Support\ServiceProvider;
use LaravelBridge\Slim\Handlers\Error;

class ErrorHandlerProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bindIf('errorHandler', function () {
            return new Error($this->app);
        }, true);
    }
}
