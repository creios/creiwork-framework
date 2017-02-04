<?php

namespace Creios\Creiwork\Framework;

use Aura\Session\SegmentInterface;
use Creios\Creiwork\Framework\Result\NoContentResult;
use Creios\Creiwork\Framework\Result\SerializableResult;
use JMS\Serializer\Serializer;
use Monolog\Logger;
use Noodlehaus\Config;
use Psr\Http\Message\ServerRequestInterface;

class WrappedBaseRestController extends BaseRestController
{

    public function _create()
    {
        return $this->standardCreate();
    }

    public function _update()
    {
        return $this->standardUpdate();
    }

    public function _retrieve()
    {
        return $this->standardRetrieve();
    }

    public function _delete()
    {
        return $this->standardDelete();
    }

    public function _list()
    {
        return $this->standardList();
    }
}

class BaseRestControllerTest extends \PHPUnit_Framework_TestCase
{

    /** @var \PHPUnit_Framework_MockObject_MockObject|ServerRequestInterface */
    private $serverRequest;
    /** @var \PHPUnit_Framework_MockObject_MockObject|Logger */
    private $logger;
    /** @var \PHPUnit_Framework_MockObject_MockObject|SegmentInterface */
    private $segment;
    /** @var \PHPUnit_Framework_MockObject_MockObject|Config */
    private $config;
    /** @var \PHPUnit_Framework_MockObject_MockObject|Serializer */
    private $serializer;
    /** @var WrappedBaseRestController */
    private $controller;

    public function setUp()
    {
        $this->serverRequest = $this->createMock(ServerRequestInterface::class);
        $this->logger = $this->createMock(Logger::class);
        $this->segment = $this->createMock(SegmentInterface::class);
        $this->config = $this->createMock(Config::class);
        $this->serializer = $this->createMock(Serializer::class);
        $this->controller = new WrappedBaseRestController(
            $this->serverRequest,
            $this->logger,
            $this->segment,
            $this->config,
            $this->serializer);
    }

    public function testCreate()
    {
        $result = $this->controller->_create();
        $this->assertInstanceOf(SerializableResult::class, $result);
    }

    public function testRetrieve()
    {
        $result = $this->controller->_retrieve();
        $this->assertInstanceOf(SerializableResult::class, $result);
    }

    public function testUpdate()
    {
        $result = $this->controller->_update();
        $this->assertInstanceOf(SerializableResult::class, $result);
    }

    public function testDelete()
    {
        $result = $this->controller->_delete();
        $this->assertInstanceOf(NoContentResult::class, $result);
    }

    public function testList()
    {
        $result = $this->controller->_list();
        $this->assertInstanceOf(SerializableResult::class, $result);
    }
}
