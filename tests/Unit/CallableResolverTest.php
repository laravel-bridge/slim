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
        $target = new CallableResolver(new Container());
        $actual = $target->resolve(TestController::class);

        $this->assertSame('TestController:__invoke', $actual());
    }

    /**
     * @test
     */
    public function shouldResolveAsInvokeWhenInputIsClassNameAndMethod(): void
    {
        $target = new CallableResolver(new Container());
        $actual = $target->resolve(TestController::class . ':view');

        $this->assertSame('TestController:view', $actual());
    }
}
