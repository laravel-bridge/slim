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
    /**
     * @var array
     */
    protected $settings = [
        'httpVersion' => '1.1',
        'responseChunkSize' => 4096,
        'outputBuffering' => 'append',
        'determineRouteBeforeAppMiddleware' => false,
        'displayErrorDetails' => false,
        'addContentLengthHeader' => true,
        'routerCacheFile' => false,
    ];

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

    /**
     * @param array $settings
     * @return static
     */
    public function setSettings(array $settings)
    {
        $this->settings = array_merge($this->settings, $settings);

        return $this;
    }

    protected function registerCallableResolver()
    {
        $this->singletonIf('callableResolver', function () {
            return new CallableResolver($this->app);
        });
    }

    protected function registerEnvironment()
    {
        $this->singletonIf('environment', function () {
            return new Environment($_SERVER);
        });
    }

    protected function registerErrorHandler()
    {
        $this->singletonIf('errorHandler', function () {
            return new Error(
                $this->app->make('settings')['displayErrorDetails']
            );
        });
    }

    protected function registerFoundHandler()
    {
        $this->singletonIf('foundHandler', function () {
            return new RequestResponse;
        });
    }

    protected function registerNotAllowedHandler()
    {
        $this->singletonIf('notAllowedHandler', function () {
            return new NotAllowed;
        });
    }

    protected function registerNotFoundHandler()
    {
        $this->singletonIf('notFoundHandler', function () {
            return new NotFound;
        });
    }

    protected function registerPhpErrorHandler()
    {
        $this->singletonIf('phpErrorHandler', function () {
            return new PhpError($this->app->make('settings')['displayErrorDetails']);
        });
    }

    protected function registerRequest()
    {
        $this->singletonIf('request', function () {
            return Request::createFromEnvironment($this->app->make('environment'));
        });
    }

    protected function registerResponse()
    {
        $this->singletonIf('response', function () {
            $headers = new Headers(['Content-Type' => 'text/html; charset=UTF-8']);
            $response = new Response(200, $headers);
            return $response->withProtocolVersion($this->app->make('settings')['httpVersion']);
        });
    }

    protected function registerRouter()
    {
        $this->singletonIf('router', function () {
            $routerCacheFile = false;
            if (isset($this->app->make('settings')['routerCacheFile'])) {
                $routerCacheFile = $this->app->make('settings')['routerCacheFile'];
            }
            $router = (new Router())->setCacheFile($routerCacheFile);
            if (method_exists($router, 'setContainer')) {
                $router->setContainer($this->app);
            }
            return $router;
        });
    }

    protected function registerSetting()
    {
        $this->singletonIf('settings', function () {
            return new Collection($this->settings);
        });
    }

    protected function singletonIf($abstract, $concrete = null)
    {
        $this->app->bindIf($abstract, $concrete, true);
    }
}
