<?php

namespace LaravelBridge\Slim\Providers\Laravel;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use Illuminate\Http\Request as LaravelRequest;
use Illuminate\Support\ServiceProvider;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;

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
            return ServerRequest::fromGlobals();
        }, true);

        $this->app->bindIf(LaravelRequest::class, function () {
            $symfonyRequest = $this->app->make(HttpFoundationFactory::class)->createRequest(
                $this->app->make('request')
            );

            return LaravelRequest::createFromBase($symfonyRequest);
        }, true);
    }

    protected function registerResponse()
    {
        $this->app->bindIf('response', function () {
            $headers = ['Content-Type' => 'text/html; charset=UTF-8'];

            return new Response(200, $headers, null, $this->app->get('settings')['httpVersion']);
        }, true);
    }
}
