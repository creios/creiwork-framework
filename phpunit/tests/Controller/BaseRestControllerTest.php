<?php

namespace Creios\Creiwork\Framework\Controller;

use Aura\Session\SegmentInterface;
use Creios\Creiwork\Framework\Result\NoContentResult;
use Creios\Creiwork\Framework\Result\SerializableResult;
use JMS\Serializer\Serializer;
use Monolog\Logger;
use Noodlehaus\Config;
use Psr\Http\Message\ServerRequestInterface;
use TimTegeler\Routerunner\Controller\RestControllerInterface;

class WrappedBaseRestController extends BaseRestController implements RestControllerInterface
{

    public function _create(ServerRequestInterface $request)
    {
        return $this->standardCreate($request);
    }

    public function _update(ServerRequestInterface $request, $id)
    {
        return $this->standardUpdate($request, $id);
    }

    public function _retrieve(ServerRequestInterface $request, $id)
    {
        return $this->standardRetrieve($request, $id);
    }

    public function _delete(ServerRequestInterface $request, $id)
    {
        return $this->standardDelete($request, $id);
    }

    public function _list(ServerRequestInterface $request)
    {
        return $this->standardList($request);
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
        $result = $this->controller->_create($this->serverRequest);
        $this->assertInstanceOf(SerializableResult::class, $result);
    }

    public function testRetrieve()
    {
        $result = $this->controller->_retrieve($this->serverRequest, 1);
        $this->assertInstanceOf(SerializableResult::class, $result);
    }

    public function testUpdate()
    {
        $result = $this->controller->_update($this->serverRequest, 1);
        $this->assertInstanceOf(SerializableResult::class, $result);
    }

    public function testDelete()
    {
        $result = $this->controller->_delete($this->serverRequest, 1);
        $this->assertInstanceOf(NoContentResult::class, $result);
    }

    public function testList()
    {
        $result = $this->controller->_list($this->serverRequest);
        $this->assertInstanceOf(SerializableResult::class, $result);
    }
}
