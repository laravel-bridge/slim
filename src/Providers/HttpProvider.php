<?php

declare(strict_types=1);

namespace LaravelBridge\Slim\Providers;

use Illuminate\Support\ServiceProvider;
use Slim\Http\Headers;
use Slim\Http\Request;
use Slim\Http\Response;

class HttpProvider extends ServiceProvider
{
    public function register()
    {
        $this->registerRequest();
        $this->registerResponse();
    }

    protected function registerRequest()
    {
        $this->app->bindIf('request', function () {
            return Request::createFromEnvironment($this->app->make('environment'));
        }, true);

        $this->app->bind(Request::class, 'request');
    }

    protected function registerResponse()
    {
        $this->app->bindIf('response', function () {
            $headers = new Headers(['Content-Type' => 'text/html; charset=UTF-8']);
            $response = new Response(200, $headers);
            return $response->withProtocolVersion($this->app->make('settings')['httpVersion']);
        }, true);

        $this->app->bind(Response::class, 'response', true);
    }
}
