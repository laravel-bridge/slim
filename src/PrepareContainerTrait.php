<?php

namespace MilesChou\LaravelBridger\Slim;

use Recca0120\LaravelBridge\Laravel;
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

trait PrepareContainerTrait
{
    /**
     * @param Laravel $container
     * @return Laravel
     */
    protected function prepareLaravelContainer(Laravel $container)
    {
        if (!$container->has('setting')) {
            $container->singleton('settings', function () {
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

        if (!$container->has('environment')) {
            $container->singleton('environment', function () {
                return new Environment($_SERVER);
            });
        }

        if (!$container->has('request')) {
            $container->singleton('request', function () use ($container) {
                return Request::createFromEnvironment($container->get('environment'));
            });
        }

        if (!$container->has('response')) {
            $container->singleton('response', function () use ($container) {
                $headers = new Headers(['Content-Type' => 'text/html; charset=UTF-8']);
                $response = new Response(200, $headers);
                return $response->withProtocolVersion($container->get('settings')['httpVersion']);
            });
        }

        if (!$container->has('router')) {
            $container->singleton('router', function () use ($container) {
                $routerCacheFile = false;
                if (isset($container->get('settings')['routerCacheFile'])) {
                    $routerCacheFile = $container->get('settings')['routerCacheFile'];
                }
                $router = (new Router)->setCacheFile($routerCacheFile);
                if (method_exists($router, 'setContainer')) {
                    $router->setContainer($container);
                }
                return $router;
            });
        }

        if (!$container->has('phpErrorHandler')) {
            $container->singleton('phpErrorHandler', function () use ($container) {
                return new PhpError($container->get('settings')['displayErrorDetails']);
            });
        }

        if (!$container->has('foundHandler')) {
            $container->singleton('foundHandler', function () {
                return new RequestResponse;
            });
        }

        if (!$container->has('errorHandler')) {
            $container->singleton('errorHandler', function () use ($container) {
                return new Error(
                    $container->get('settings')['displayErrorDetails']
                );
            });
        }

        if (!$container->has('notFoundHandler')) {
            $container->singleton('notFoundHandler', function () {
                return new NotFound;
            });
        }

        if (!$container->has('notAllowedHandler')) {
            $container->singleton('notAllowedHandler', function () {
                return new NotAllowed;
            });
        }

        if (!$container->has('callableResolver')) {
            $container->singleton('callableResolver', function () use ($container) {
                return new CallableResolver($container);
            });
        }

        return $container;
    }
}
