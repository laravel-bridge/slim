<?php

namespace LaravelBridge\Slim\Handlers\Strategies;

use Illuminate\Container\Container;
use Illuminate\Http\Request as LaravelRequest;
use LaravelBridge\Slim\Traits\HttpTransformerTrait;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\InvocationStrategyInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class RequestResponse implements InvocationStrategyInterface
{
    use HttpTransformerTrait;

    /**
     * @var Container|ContainerInterface
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
        $this->container->instance(LaravelRequest::class, $this->createLaravelRequest($request));

        $response = $this->container->call($callable, [$routeArguments]);

        if ($response instanceof SymfonyResponse) {
            return (new DiactorosFactory)->createResponse($response);
        }

        return $response;
    }
}
