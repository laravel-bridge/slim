<?php

namespace Tests\Unit;

use Illuminate\Container\Container;
use Illuminate\Http\Request as LaravelRequest;
use Illuminate\Http\Response as LaravelResponse;
use LaravelBridge\Slim\App;
use PHPUnit\Framework\TestCase;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Response;

class LaravelServiceTest extends TestCase
{
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
    public function shouldBeOkayWhenTestASimpleRouteWithLaravelService()
    {
        $app = new App(new Container(), false);
        $app->get('/', function (LaravelRequest $request, $args) {
            return new LaravelResponse('bar');
        });

        $actual = $app(Request::createFromEnvironment(Environment::mock()), new Response());

        $this->assertSame('bar', (string)$actual->getBody());
    }
}
