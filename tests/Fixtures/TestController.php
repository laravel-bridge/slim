<?php

namespace Tests\Fixtures;

class TestController
{
    public function __invoke()
    {
        return 'TestController:__invoke';
    }

    public function view()
    {
        return 'TestController:view';
    }
}
