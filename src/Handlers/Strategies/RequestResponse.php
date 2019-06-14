<?php

namespace LaravelBridge\Slim\Handlers\Strategies;

use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\InvocationStrategyInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;

class RequestResponse implements InvocationStrategyInterface
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @param Container $container
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * @param array|callable $callable
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $routeArguments
     * @return mixed
     */
    public function __invoke(
        callable $callable,
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $routeArguments
    ) {
        $laravelRequest = (new HttpFoundationFactory)->createRequest($request);

        $this->container->instance(Request::class, $laravelRequest);

        $response = $this->container->call($callable, [$routeArguments]);

        if ($response instanceof Response) {
            $response = (new DiactorosFactory)->createResponse($response);
        }

        return $response;
    }
}
