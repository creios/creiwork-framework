<?php

namespace Creios\Creiwork\Framework;

use Creios\Creiwork\Framework\Controller\BaseController;
use Creios\Creiwork\Framework\Result\StringResult;
use PHPUnit\Framework\TestCase;

class TestController extends BaseController
{
    /**
     * @return Result\StringResult
     */
    public function test()
    {
        return StringResult::createPlainTextResult('Controller has been executed');
    }
}

class CreiworkTest extends TestCase
{
    /**
     * @runInSeparateProcess
     */
    public function testStart()
    {
        (new Creiwork(__DIR__ . '/../asset'))->start();
        $this->expectOutputString('Controller has been executed');
    }
}
