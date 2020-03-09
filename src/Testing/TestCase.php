<?php

declare(strict_types=1);

namespace LaravelBridge\Slim\Testing;

use Illuminate\Support\Facades\Facade;
use LaravelBridge\Scratch\Application as LaravelBridge;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Slim\App;

abstract class TestCase extends BaseTestCase
{
    use Concerns\MakesHttpRequests;

    /**
     * The LaravelBridge Container
     *
     * @var LaravelBridge
     */
    protected $container;

    /**
     * The Slim application instance.
     *
     * @var App
     */
    protected $slim;

    /**
     * Creates the application.
     *
     * @return App
     */
    abstract public function createSlimApplication(): App;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        if (!$this->slim) {
            $this->refreshApplication();
        }

        Facade::clearResolvedInstances();
    }

    /**
     * Refresh the application instance.
     */
    protected function refreshApplication(): void
    {
        $this->slim = $this->createSlimApplication();
        $this->container = $this->slim->getContainer();
    }

    /**
     * Proxy to Container's instance method
     *
     * @param string $abstract
     * @param mixed $concrete
     * @return TestCase
     */
    protected function instance($abstract, $concrete): TestCase
    {
        $this->container->instance($abstract, $concrete);

        return $this;
    }
}
