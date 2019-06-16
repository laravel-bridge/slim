<?php

namespace Tests\Unit;

use Exception;
use Illuminate\Http\Request as LaravelRequest;
use Illuminate\Http\Response as LaravelResponse;
use LaravelBridge\Slim\App;
use PHPUnit\Framework\TestCase;
use Recca0120\LaravelBridge\Laravel;
use Slim\Collection;
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

        $container = new Laravel();

        $app = new App($container, false);
        $app->getContainer()->getApp()->instance('environment', $expected);

        $this->assertSame($expected, $app->getContainer()->get('environment'));
    }

    /**
     * @test
     */
    public function shouldBeOkayWhenTestASimpleRouteWithLaravelService()
    {
        $container = new Laravel();

        $app = new App($container, false);
        $app->get('/', function (LaravelRequest $request, $args) {
            return new LaravelResponse('bar');
        });

        $actual = $app(Request::createFromEnvironment(Environment::mock()), new Response());

        $this->assertSame('bar', (string)$actual->getBody());
    }
}
