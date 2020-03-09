<?php

namespace Tests\Feature;

use Illuminate\Container\Container;
use Illuminate\Http\Request as LaravelRequest;
use Illuminate\Http\Response as LaravelResponse;
use LaravelBridge\Slim\App;
use LaravelBridge\Slim\Testing\TestCase;
use Slim\App as SlimApp;

class LaravelServiceTest extends TestCase
{
    public function createSlimApplication(): SlimApp
    {
        $app = new App(new Container(), true);
        $app->getContainer()->get('settings')->set('displayErrorDetails', true);

        $app->get('/', function (LaravelRequest $request, $args) {
            return new LaravelResponse('bar');
        });

        return $app;
    }

    /**
     * @test
     */
    public function shouldBeOkayWhenTestASimpleRouteWithLaravelService(): void
    {
        $actual = $this->call('GET', '/');

        $this->assertSame('bar', (string)$actual->getBody());
    }
}
