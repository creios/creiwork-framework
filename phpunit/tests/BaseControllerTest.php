<?php

namespace Creios\Creiwork\Framework;

use Aura\Session\SegmentInterface;
use Monolog\Logger;
use Psr\Http\Message\ServerRequestInterface;

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

    /** @var \PHPUnit_Framework_MockObject_MockObject|ServerRequestInterface */
    private $serverRequest;
    /** @var \PHPUnit_Framework_MockObject_MockObject|Logger */
    private $logger;
    /** @var \PHPUnit_Framework_MockObject_MockObject|SegmentInterface */
    private $segment;

    public function setUp()
    {
        $this->serverRequest = $this->createMock(ServerRequestInterface::class);
        $this->logger = $this->createMock(Logger::class);
        $this->segment = $this->createMock(SegmentInterface::class);
    }

    public function testSetReroutedPath()
    {
        $controller = new WrappedBaseController($this->serverRequest, $this->logger, $this->segment);
        $controller->setReroutedPath("/new/path");
        $this->assertEquals("/new/path", $controller->getReroutedPath());
    }

}
