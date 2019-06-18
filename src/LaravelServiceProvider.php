<?php

namespace LaravelBridge\Slim;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use Illuminate\Http\Request as LaravelRequest;
use Illuminate\Support\Fluent;
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
        $this->singletonIf('errorHandler', function () {
            return new Error($this->app);
        });
    }

    protected function registerFoundHandler()
    {
        $this->singletonIf('foundHandler', function () {
            return new RequestResponse($this->app);
        });
    }

    protected function registerNotAllowedHandler()
    {
        $this->singletonIf('notAllowedHandler', function () {
            return new NotAllowed($this->app);
        });
    }

    protected function registerNotFoundHandler()
    {
        $this->singletonIf('notFoundHandler', function () {
            return new NotFound($this->app);
        });
    }

    protected function registerPhpErrorHandler()
    {
        $this->singletonIf('phpErrorHandler', function () {
            return new PhpError($this->app);
        });
    }

    protected function registerRequest()
    {
        $this->singletonIf(HttpFoundationFactory::class);
        $this->singletonIf(DiactorosFactory::class);

        $this->singletonIf('request', function () {
            return ServerRequest::fromGlobals();
        });

        $this->singletonIf(LaravelRequest::class, function () {
            $symfonyRequest = $this->app->make(HttpFoundationFactory::class)->createRequest(
                $this->app->make('request')
            );

            return LaravelRequest::createFromBase($symfonyRequest);
        });
    }

    protected function registerResponse()
    {
        $this->singletonIf('response', function () {
            $headers = ['Content-Type' => 'text/html; charset=UTF-8'];

            return new Response(200, $headers, null, $this->app->get('settings')['httpVersion']);
        });
    }

    protected function registerSetting()
    {
        $this->singletonIf('settings', function () {
            return new Fluent($this->settings);
        });
    }
}
