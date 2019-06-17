<?php

namespace Tests\Unit;

use Illuminate\Container\Container;
use LaravelBridge\Slim\App;
use LaravelBridge\Slim\Testing\TestCase;
use Slim\Http\Environment;

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
}
