<?php

namespace LaravelBridge\Slim;

use Illuminate\Support\ServiceProvider;
use Slim\CallableResolver;
use Slim\Collection;
use Slim\Handlers\Error;
use Slim\Handlers\NotAllowed;
use Slim\Handlers\NotFound;
use Slim\Handlers\PhpError;
use Slim\Handlers\Strategies\RequestResponse;
use Slim\Http\Environment;
use Slim\Http\Headers;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;

class SlimDefaultServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->registerCallableResolver();
        $this->registerEnvironment();
        $this->registerErrorHandler();
        $this->registerFoundHandler();
        $this->registerNotAllowedHandler();
        $this->registerNotFoundHandler();
        $this->registerPhpErrorHandler();
        $this->registerRequest();
        $this->registerResponse();
        $this->registerRouter();
        $this->registerSetting();
    }

    public function registerCallableResolver()
    {
        $this->app->singleton('callableResolver', function () {
            return new CallableResolver($this->app);
        });
    }

    public function registerEnvironment()
    {
        $this->app->singleton('environment', function () {
            return new Environment($_SERVER);
        });
    }

    public function registerErrorHandler()
    {
        $this->app->singleton('errorHandler', function () {
            return new Error(
                $this->app->get('settings')['displayErrorDetails']
            );
        });
    }

    public function registerFoundHandler()
    {
        $this->app->singleton('foundHandler', function () {
            return new RequestResponse;
        });
    }

    public function registerNotAllowedHandler()
    {
        $this->app->singleton('notAllowedHandler', function () {
            return new NotAllowed;
        });
    }

    public function registerNotFoundHandler()
    {
        $this->app->singleton('notFoundHandler', function () {
            return new NotFound;
        });
    }

    public function registerPhpErrorHandler()
    {
        $this->app->singleton('phpErrorHandler', function () {
            return new PhpError($this->app->get('settings')['displayErrorDetails']);
        });
    }

    public function registerRequest()
    {
        $this->app->singleton('request', function () {
            return Request::createFromEnvironment($this->app->get('environment'));
        });
    }

    public function registerResponse()
    {
        $this->app->singleton('response', function () {
            $headers = new Headers(['Content-Type' => 'text/html; charset=UTF-8']);
            $response = new Response(200, $headers);
            return $response->withProtocolVersion($this->app->get('settings')['httpVersion']);
        });
    }

    public function registerRouter()
    {
        $this->app->singleton('router', function () {
            $routerCacheFile = false;
            if (isset($this->app->get('settings')['routerCacheFile'])) {
                $routerCacheFile = $this->app->get('settings')['routerCacheFile'];
            }
            $router = (new Router())->setCacheFile($routerCacheFile);
            if (method_exists($router, 'setContainer')) {
                $router->setContainer($this->app);
            }
            return $router;
        });
    }

    public function registerSetting()
    {
        $this->app->singleton('settings', function () {
            return new Collection([
                'httpVersion' => '1.1',
                'responseChunkSize' => 4096,
                'outputBuffering' => 'append',
                'determineRouteBeforeAppMiddleware' => false,
                'displayErrorDetails' => false,
                'addContentLengthHeader' => true,
                'routerCacheFile' => false,
            ]);
        });
    }
}
