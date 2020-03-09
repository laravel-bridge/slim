<?php

declare(strict_types=1);

namespace LaravelBridge\Slim\Providers;

use Illuminate\Support\ServiceProvider;
use Slim\CallableResolver;

class CallableResolverProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bindIf('callableResolver', function () {
            return new CallableResolver($this->app);
        }, true);
    }
}
