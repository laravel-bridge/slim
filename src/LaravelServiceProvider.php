<?php

namespace LaravelBridge\Slim;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use Illuminate\Http\Request as LaravelRequest;
use LaravelBridge\Slim\Handlers\Error;
use LaravelBridge\Slim\Handlers\NotAllowed;
use LaravelBridge\Slim\Handlers\NotFound;
use LaravelBridge\Slim\Handlers\PhpError;
use LaravelBridge\Slim\Handlers\Strategies\RequestResponse;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;

class LaravelServiceProvider extends SlimDefaultServiceProvider
{
    protected function registerErrorHandler()
    {
        $this->app->singleton('errorHandler', function () {
            return new Error($this->app);
        });
    }

    protected function registerFoundHandler()
    {
        $this->app->singleton('foundHandler', function () {
            return new RequestResponse($this->app);
        });
    }

    protected function registerNotAllowedHandler()
    {
        $this->app->singleton('notAllowedHandler', function () {
            return new NotAllowed($this->app);
        });
    }

    protected function registerNotFoundHandler()
    {
        $this->app->singleton('notFoundHandler', function () {
            return new NotFound($this->app);
        });
    }

    protected function registerPhpErrorHandler()
    {
        $this->app->singleton('phpErrorHandler', function () {
            return new PhpError($this->app);
        });
    }

    protected function registerRequest()
    {
        $this->app->singleton(HttpFoundationFactory::class);
        $this->app->singleton(DiactorosFactory::class);

        $this->app->singleton('request', function () {
            return ServerRequest::fromGlobals();
        });

        $this->app->singleton(LaravelRequest::class, function () {
            $symfonyRequest = $this->app->make(HttpFoundationFactory::class)->createRequest(
                $this->app->make('request')
            );

            return LaravelRequest::createFromBase($symfonyRequest);
        });
    }

    protected function registerResponse()
    {
        $this->app->singleton('response', function () {
            $headers = ['Content-Type' => 'text/html; charset=UTF-8'];

            return new Response(200, $headers, null, $this->app->get('settings')['httpVersion']);
        });
    }
}
