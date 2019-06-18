<?php

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
    }

    protected function registerResponse()
    {
        $this->app->bindIf('response', function () {
            $headers = new Headers(['Content-Type' => 'text/html; charset=UTF-8']);
            $response = new Response(200, $headers);
            return $response->withProtocolVersion($this->app->make('settings')['httpVersion']);
        }, true);
    }
}
