<?php

namespace Tests\Feature;

use Illuminate\Container\Container;
use Illuminate\Contracts\Container\Container as ContainerContract;
use LaravelBridge\Slim\App;
use LaravelBridge\Slim\ContainerBuilder;
use LaravelBridge\Slim\Testing\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Environment;
use stdClass;

class AppTest extends TestCase
{
    public function createApplication()
    {
        $app = new App();
        $app->get('/', function () {
            return 'bar';
        });

        return $app;
    }

    /**
     * @test
     */
    public function shouldBeOkayWhenTestASimpleRoute(): void
    {
        $actual = $this->call('GET', '/');

        $this->assertSame('bar', (string)$actual->getBody());
    }
}
