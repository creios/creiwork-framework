<?php

namespace Creios\Creiwork\Framework\Controller;

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

class BaseControllerTest extends \PHPUnit_Framework_TestCase
{

    public function testSetReroutedPath()
    {
        $controller = new WrappedBaseController();
        $controller->setReroutedPath("/new/path");
        $this->assertEquals("/new/path", $controller->getReroutedPath());
    }

}
