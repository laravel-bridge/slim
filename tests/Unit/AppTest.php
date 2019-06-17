<?php

namespace Tests\Unit;

use Illuminate\Container\Container;
use Illuminate\Contracts\Container\Container as ContainerContract;
use LaravelBridge\Slim\App;
use LaravelBridge\Slim\Testing\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Environment;
use stdClass;

class AppTest extends TestCase
{
    public function createApplication()
    {
        $app = new App(new Container());
        $app->get('/', function () {
            return 'bar';
        });

        return $app;
    }

    /**
     * @test
     */
    public function shouldBeOkayWhenTestASimpleRoute()
    {
        $actual = $this->call('GET', '/');

        $this->assertSame('bar', (string)$actual->getBody());
    }

    /**
     * @test
     */
    public function shouldGetTheSameInstanceWhenPresetTheMock()
    {
        $expected = Environment::mock();

        $app = new App(new Container(), false);
        $app->getContainer()->instance('environment', $expected);

        $this->assertSame($expected, $app->getContainer()->get('environment'));
    }

    /**
     * @test
     */
    public function shouldGetTheSameInstanceWhenPresetTheMockUsingArray()
    {
        $expected = Environment::mock();

        $container = [
            'environment' => $expected,
            'obj' => stdClass::class,
            ServerRequestInterface::class => function (ContainerContract $app) {
                return $app->make('request');
            }
        ];

        $actual = (new App($container, false))->getContainer();

        $this->assertSame($expected, $actual->get('environment'));
        $this->assertInstanceOf(stdClass::class, $actual->get('obj'));
        $this->assertSame($actual->get('request'), $actual->get(ServerRequestInterface::class));
    }
}
