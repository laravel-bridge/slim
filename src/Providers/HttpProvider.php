<?php

declare(strict_types=1);

namespace LaravelBridge\Slim\Providers;

use Illuminate\Support\ServiceProvider;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
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

    protected function registerRequest(): void
    {
        $this->app->bindIf('request', function () {
            return Request::createFromEnvironment($this->app->make('environment'));
        }, true);

        $this->app->alias('request', Request::class);
        $this->app->alias('request', RequestInterface::class);
    }

    protected function registerResponse(): void
    {
        $this->app->bindIf('response', function () {
            $headers = new Headers(['Content-Type' => 'text/html; charset=UTF-8']);
            $response = new Response(200, $headers);

            return $response->withProtocolVersion($this->app->make('settings')['httpVersion']);
        }, true);

        $this->app->alias('response', Response::class);
        $this->app->alias('response', ResponseInterface::class);
    }
}
