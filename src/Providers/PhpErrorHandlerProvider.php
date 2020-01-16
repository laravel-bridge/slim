<?php

namespace LaravelBridge\Slim\Providers;

use Illuminate\Support\ServiceProvider;
use Slim\Handlers\PhpError;

class PhpErrorHandlerProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bindIf('phpErrorHandler', function () {
            return new PhpError();
        }, true);
    }
}
