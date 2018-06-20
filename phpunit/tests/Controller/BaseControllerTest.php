<?php

namespace Creios\Creiwork\Framework\Controller;

use PHPUnit\Framework\TestCase;

class WrappedBaseController extends BaseController
{
    /**
     * @return String
     */
    public function getReroutedPath()
    {
        return $this->reroutedPath;
    }

}

class BaseControllerTest extends TestCase
{

    public function testSetReroutedPath()
    {
        $controller = new WrappedBaseController();
        $controller->setReroutedPath("/new/path");
        $this->assertEquals("/new/path", $controller->getReroutedPath());
    }

}
