<?php

namespace LaravelBridge\Slim\Providers;

use Illuminate\Support\ServiceProvider;
use Slim\Handlers\NotFound;

class NotFoundProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bindIf('notFoundHandler', function () {
            return new NotFound;
        }, true);
    }
}
