<?php

namespace Tests\Unit;

use Illuminate\Container\Container;
use LaravelBridge\Slim\CallableResolver;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\TestController;

class CallableResolverTest extends TestCase
{
    /**
     * @test
     */
    public function shouldResolveAsInvokeWhenInputIsClassName(): void
    {
        $app = new CallableResolver(new Container());
        $actual = $app->resolve(TestController::class);

        $this->assertSame('TestController:__invoke', $actual());
    }

    /**
     * @test
     */
    public function shouldResolveAsInvokeWhenInputIsClassNameAndMethod(): void
    {
        $app = new CallableResolver(new Container());
        $actual = $app->resolve(TestController::class . ':view');

        $this->assertSame('TestController:view', $actual());
    }
}
