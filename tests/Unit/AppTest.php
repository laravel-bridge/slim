<?php

namespace Tests\Unit;

use Illuminate\Container\Container;
use LaravelBridge\Slim\App;
use PHPUnit\Framework\TestCase;
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
        $app = new App(new Container());
        $app->get('/', function () {
            return 'bar';
        });

        $actual = $app(Request::createFromEnvironment(Environment::mock()), new Response());

        $this->assertSame('bar', (string)$actual->getBody());
    }
}
