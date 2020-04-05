<?php

namespace Tests\Feature;

use LaravelBridge\Slim\App;
use LaravelBridge\Slim\ContainerBuilder;
use LaravelBridge\Slim\Testing\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\Test\TestLogger;
use Slim\App as SlimApp;
use Tests\Fixtures\TestMiddleware;

class AppTest extends TestCase
{
    public function createSlimApplication(): SlimApp
    {
        $container = (new ContainerBuilder())
            ->useLaravelCallableResolver()
            ->build();

        return new App($container);
    }

    /**
     * @test
     */
    public function shouldBeOkayWhenTestRoute(): void
    {
        $this->slim->get('/', function () {
            return 'bar';
        });

        $this->call('GET', '/')
            ->assertSee('bar');
    }

    /**
     * @test
     */
    public function shouldBeOkayWhenTestMiddleware(): void
    {
        $this->slim->get('/', function () {
            return 'bar';
        });

        $this->slim->add(TestMiddleware::class);

        $testLogger = new TestLogger();

        $this->instance(LoggerInterface::class, $testLogger);

        $this->call('GET', '/');

        $this->assertTrue($testLogger->hasInfo('TEST'));
    }
}
