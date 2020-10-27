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

        // $foo var name must be same
        $app->get('/{foo}', function (LaravelRequest $request, $foo) {
            return new LaravelResponse($foo);
        });

        return $app;
    }

    /**
     * @test
     */
    public function shouldBeOkayWhenTestASimpleRouteWithLaravelService(): void
    {
        $actual = $this->call('GET', '/bar');

        $this->assertSame('bar', (string)$actual->getBody());
    }
}
