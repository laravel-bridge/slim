<?php

declare(strict_types=1);

namespace LaravelBridge\Slim\Providers\Laravel;

use Illuminate\Http\Request as LaravelRequest;
use LaravelBridge\Slim\Providers\HttpProvider as SlimHttpProvider;
use LaravelBridge\Support\IlluminateHttpFactory;

class HttpProvider extends SlimHttpProvider
{
    public function registerRequest(): void
    {
        parent::registerRequest();

        $this->app->singleton(IlluminateHttpFactory::class);

        $this->app->bindIf(LaravelRequest::class, function () {
            /** @var IlluminateHttpFactory $factory */
            $factory = $this->app->make(IlluminateHttpFactory::class);

            return $factory->createRequest($this->app->make('request'));
        }, true);
    }
}
