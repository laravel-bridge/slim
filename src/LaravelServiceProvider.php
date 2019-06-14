<?php

namespace LaravelBridge\Slim;

use LaravelBridge\Slim\Handlers\Strategies\RequestResponse;
use Slim\Http\Request;

class LaravelServiceProvider extends SlimDefaultServiceProvider
{
    public function registerFoundHandler()
    {
        $this->app->singleton('foundHandler', function () {
            return new RequestResponse($this->app);
        });
    }

    public function registerRequest()
    {
        $this->app->singleton('request', function () {
            return Request::createFromEnvironment($this->app->get('environment'));
        });
    }
}
