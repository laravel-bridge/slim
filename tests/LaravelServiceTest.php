<?php

namespace Tests;

use Illuminate\Http\Request as LaravelRequest;
use Illuminate\Http\Response as LaravelResponse;
use LaravelBridge\Slim\App;
use PHPUnit\Framework\TestCase;
use Recca0120\LaravelBridge\Laravel;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Response;

class LaravelServiceTest extends TestCase
{
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
