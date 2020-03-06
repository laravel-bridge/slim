<?php

declare(strict_types=1);

namespace LaravelBridge\Slim\Handlers\Strategies;

use Illuminate\Contracts\Container\Container;
use Illuminate\Http\Request as LaravelRequest;
use LaravelBridge\Support\Traits\IlluminateHttpFactoryAwareTrait;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\InvocationStrategyInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class RequestResponse implements InvocationStrategyInterface
{
    use IlluminateHttpFactoryAwareTrait;

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
        $this->container->instance(LaravelRequest::class, $this->getIlluminateHttpFactory()->createRequest($request));

        $response = $this->container->call($callable, [$routeArguments]);

        if ($response instanceof SymfonyResponse) {
            return $this->container->make(PsrHttpFactory::class)->createResponse($response);
        }

        return $response;
    }
}
