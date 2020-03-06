<?php

declare(strict_types=1);

namespace LaravelBridge\Slim\Providers;

use Illuminate\Support\ServiceProvider;
use Slim\Handlers\NotAllowed;

class NotAllowedProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bindIf('notAllowedHandler', function () {
            return new NotAllowed();
        }, true);
    }
}
