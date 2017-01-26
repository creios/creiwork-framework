<?php

namespace Creios\Creiwork\Framework;

class TestController extends BaseController
{
    /**
     * @return Result\StringResult
     */
    public function test()
    {
        return ResultFactory::createPlainTextResult('Controller has been executed');
    }
}

class CreiworkTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @runInSeparateProcess
     */
    public function testStart()
    {
        (new Creiwork(__DIR__ . '/../asset/config.json'))->start();
        $this->expectOutputString('Controller has been executed');
    }
}
