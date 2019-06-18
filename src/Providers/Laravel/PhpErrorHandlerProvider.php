<?php

namespace LaravelBridge\Slim\Providers\Laravel;

use Illuminate\Support\ServiceProvider;
use LaravelBridge\Slim\Handlers\PhpError;

class PhpErrorHandlerProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bindIf('phpErrorHandler', function () {
            return new PhpError($this->app);
        }, true);
    }
}
