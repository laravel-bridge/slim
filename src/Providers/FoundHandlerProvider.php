<?php

declare(strict_types=1);

namespace LaravelBridge\Slim\Providers;

use Illuminate\Support\ServiceProvider;
use Slim\Handlers\Strategies\RequestResponse;

class FoundHandlerProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bindIf('foundHandler', function () {
            return new RequestResponse();
        }, true);
    }
}
