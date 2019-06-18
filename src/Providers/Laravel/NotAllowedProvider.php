<?php

namespace LaravelBridge\Slim\Providers\Laravel;

use Illuminate\Support\ServiceProvider;
use LaravelBridge\Slim\Handlers\NotAllowed;

class NotAllowedProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bindIf('notAllowedHandler', function () {
            return new NotAllowed($this->app);
        }, true);
    }
}
