<?php

namespace Tests\Feature;

use LaravelBridge\Slim\App;
use LaravelBridge\Slim\Testing\TestCase;

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
