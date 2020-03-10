<?php

declare(strict_types=1);

namespace LaravelBridge\Slim;

use Illuminate\Container\Container;
use Illuminate\Contracts\Container\BindingResolutionException;
use LaravelBridge\Container\Traits\LaravelContainerAwareTrait;
use RuntimeException;
use Slim\Interfaces\CallableResolverInterface;

/**
 * This class copy from slim CallableResolver, and make class by Laravel Container
 */
class CallableResolver implements CallableResolverInterface
{
    use LaravelContainerAwareTrait;

    private const CALLABLE_PATTERN = '!^([^\:]+)\:([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)$!';

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Resolve toResolve into a closure so that the router can dispatch.
     *
     * If toResolve is of the format 'class:method', then try to extract 'class'
     * from the container otherwise instantiate it and then dispatch 'method'.
     *
     * @param callable|string $toResolve
     * @return callable
     * @throws RuntimeException If the callable does not exist
     * @throws RuntimeException If the callable is not resolvable
     * @throws BindingResolutionException
     */
    public function resolve($toResolve)
    {
        if (is_callable($toResolve)) {
            return $toResolve;
        }

        $resolved = $toResolve;

        if (is_string($toResolve)) {
            [$class, $method] = $this->parseCallable($toResolve);
            $resolved = $this->resolveCallable($class, $method);
        }

        $this->assertCallable($resolved);

        return $resolved;
    }

    /**
     * Extract class and method from toResolve
     *
     * @param string $toResolve
     * @return array
     */
    protected function parseCallable($toResolve): array
    {
        if (preg_match(self::CALLABLE_PATTERN, $toResolve, $matches)) {
            return [$matches[1], $matches[2]];
        }

        return [$toResolve, '__invoke'];
    }

    /**
     * Check if string is something in the Container
     *
     * @param string $class
     * @param string $method
     * @return callable
     * @throws RuntimeException if the callable does not exist
     * @throws BindingResolutionException
     */
    protected function resolveCallable($class, $method)
    {
        if ($this->container->has($class)) {
            return [$this->container->make($class), $method];
        }

        if (class_exists($class)) {
            return [$this->container->make($class), $method];
        }

        throw new RuntimeException(sprintf('Callable %s does not exist', $class));
    }

    /**
     * @param Callable $callable
     * @throws RuntimeException if the callable is not resolvable
     */
    protected function assertCallable($callable)
    {
        if (!is_callable($callable)) {
            throw new RuntimeException(sprintf(
                '%s is not resolvable',
                is_array($callable) || is_object($callable) ? json_encode($callable) : $callable
            ));
        }
    }
}
