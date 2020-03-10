<?php

declare(strict_types=1);

namespace LaravelBridge\Slim\Providers\Laravel;

use Illuminate\Http\Request as LaravelRequest;
use Illuminate\Support\ServiceProvider;
use LaravelBridge\Slim\Providers\HttpProvider as SlimHttpProvider;
use LaravelBridge\Support\IlluminateHttpFactory;

class HttpProvider extends ServiceProvider
{
    public function register()
    {
        (new SlimHttpProvider($this->app))->register();

        $this->app->bindIf(LaravelRequest::class, function () {
            return (new IlluminateHttpFactory())->createRequest($this->app->make('request'));
        }, true);
    }
}
