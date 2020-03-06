<?php

declare(strict_types=1);

namespace LaravelBridge\Slim\Providers\Laravel;

use Illuminate\Support\ServiceProvider;
use LaravelBridge\Slim\Handlers\NotFound;

class NotFoundProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bindIf('notFoundHandler', function () {
            return new NotFound($this->app);
        }, true);
    }
}
