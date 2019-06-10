<?php

namespace Tests;

use MilesChou\LaravelBridger\Slim\App;

class AppTest extends \PHPUnit_Framework_TestCase
{
    public function testSample()
    {
        $this->assertInstanceOf(App::class, new App(new \Slim\App()));
    }
}
