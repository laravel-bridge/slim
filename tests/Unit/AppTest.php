<?php

namespace Tests\Unit;

use LaravelBridge\Slim\App;
use PHPUnit\Framework\TestCase;
use Recca0120\LaravelBridge\Laravel;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Response;

class AppTest extends TestCase
{
    /**
     * @test
     */
    public function shouldBeOkayWhenTestASimpleRoute()
    {
        $container = new Laravel();
        $container->bootstrap();

        $app = new App($container);
        $app->get('/', function () {
            return 'bar';
        });

        $actual = $app(Request::createFromEnvironment(Environment::mock()), new Response());

        $this->assertSame('bar', (string)$actual->getBody());
    }

    /**
     * @test
     */
    public function shouldNotOverridePresetEntry()
    {
        $container = new Laravel();
        $container->bootstrap();

        $container->getApp()['setting'] = ['some', 'array'];

        $app = new App($container);

        $actual = $app->getContainer()->get('setting');

        $this->assertSame(['some', 'array'], $actual);
    }
}
