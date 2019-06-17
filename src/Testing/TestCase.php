<?php

namespace LaravelBridge\Slim\Testing;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Facades\Facade;
use LaravelBridge\Support\ContainerBridge;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Slim\App;

abstract class TestCase extends BaseTestCase
{
    use Concerns\MakesHttpRequests;

    /**
     * The Illuminate application instance.
     *
     * @var App
     */
    protected $app;

    /**
     * Creates the application.
     *
     * Needs to be implemented by subclasses.
     *
     * @return App
     */
    abstract public function createApplication();

    /**
     * Setup the test environment.
     */
    protected function setUp()
    {
        if (! $this->app) {
            $this->refreshApplication();
        }

        Facade::clearResolvedInstances();
    }

    /**
     * Refresh the application instance.
     */
    protected function refreshApplication()
    {
        $this->app = $this->createApplication();
    }

    /**
     * @return Container
     */
    protected function resolveContainer()
    {
        $container = $this->app->getContainer();

        if ($container instanceof ContainerBridge) {
            $container = $container->getContainer();
        }

        return $container;
    }

    /**
     * Proxy to Container's instance method
     *
     * @param string $abstract
     * @param mixed $concrete
     */
    protected function instance($abstract, $concrete)
    {
        $this->resolveContainer()->instance($abstract, $concrete);
    }
}
