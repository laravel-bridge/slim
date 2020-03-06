<?php

declare(strict_types=1);

namespace LaravelBridge\Slim\Providers\Laravel;

use Illuminate\Http\Request as LaravelRequest;
use Illuminate\Support\ServiceProvider;
use LaravelBridge\Slim\Providers\HttpProvider as SlimHttpProvider;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;

class HttpProvider extends ServiceProvider
{
    public function register()
    {
        (new SlimHttpProvider($this->app))->register();

        $this->app->bindIf(LaravelRequest::class, function () {
            $symfonyRequest = $this->app->make(HttpFoundationFactory::class)->createRequest(
                $this->app->make('requ  est')
            );

            return LaravelRequest::createFromBase($symfonyRequest);
        }, true);
    }
}
