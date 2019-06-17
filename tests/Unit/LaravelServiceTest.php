<?php

namespace Tests\Unit;

use Illuminate\Container\Container;
use Illuminate\Http\Request as LaravelRequest;
use Illuminate\Http\Response as LaravelResponse;
use LaravelBridge\Slim\App;
use LaravelBridge\Slim\Testing\TestCase;

class LaravelServiceTest extends TestCase
{
    /**
     * @test
     */
    public function shouldBeOkayWhenTestASimpleRouteWithLaravelService()
    {
        $actual = $this->call('GET', '/');

        $this->assertSame('bar', (string)$actual->getBody());
    }

    public function createApplication()
    {
        $app = new App(new Container(), false);
        $app->getContainer()->get('settings')->set('displayErrorDetails', true);

        $app->get('/', function (LaravelRequest $request, $args) {
            return new LaravelResponse('bar');
        });

        return $app;
    }
}
