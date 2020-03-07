<?php

namespace Tests\Unit;

use Illuminate\Container\Container;
use Illuminate\Contracts\Container\Container as ContainerContract;
use LaravelBridge\Slim\App;
use LaravelBridge\Slim\ContainerBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Environment;
use stdClass;

class AppTest extends TestCase
{
    /**
     * @test
     */
    public function shouldGetTheSameInstanceWhenPresetTheMock(): void
    {
        $expected = Environment::mock();

        $app = new App(new Container(), false);
        $app->getContainer()->instance('environment', $expected);

        $this->assertSame($expected, $app->getContainer()->get('environment'));
    }

    /**
     * @test
     */
    public function shouldGetTheSameInstanceWhenPresetTheMockUsingArray(): void
    {
        $expected = Environment::mock();

        $container = [
            'environment' => $expected,
            'obj' => stdClass::class,
            ServerRequestInterface::class => function (ContainerContract $app) {
                return $app->make('request');
            },
        ];

        $actual = (new App($container, false))->getContainer();

        $this->assertSame($expected, $actual->get('environment'));
        $this->assertInstanceOf(stdClass::class, $actual->get('obj'));
        $this->assertSame($actual->get('request'), $actual->get(ServerRequestInterface::class));
    }

    /**
     * @test
     */
    public function shouldBeOkayWhenUsingReadyContainer(): void
    {
        $container = (new ContainerBuilder())
            ->useLaravelFoundHandler()
            ->build()
            ->bootstrap();

        $target = new App($container);

        $this->assertInstanceOf(App::class, $target);
    }

    /**
     * @test
     */
    public function shouldBeOkayWhenUsingSlimSettings(): void
    {
        $settings = [
            'settings' => [
                'foo' => 'bar',
            ]
        ];

        $target = new App($settings);

        $this->assertSame(['foo' => 'bar'], $target->getContainer()->get('settings'));
    }
}
