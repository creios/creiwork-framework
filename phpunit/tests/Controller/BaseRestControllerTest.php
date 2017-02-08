<?php

namespace Creios\Creiwork\Framework\Controller;

use Aura\Session\SegmentInterface;
use Creios\Creiwork\Framework\Repository\RepositoryBaseInterface;
use Creios\Creiwork\Framework\Result\NoContentResult;
use Creios\Creiwork\Framework\Result\SerializableResult;
use Monolog\Logger;
use Noodlehaus\Config;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use TimTegeler\Routerunner\Controller\RestControllerInterface;

class WrappedBaseRestController extends BaseRestController implements RestControllerInterface
{

    public function __construct(ServerRequestInterface $serverRequest,
                                LoggerInterface $logger,
                                SegmentInterface $session,
                                Config $config,
                                RepositoryBaseInterface $repository)
    {
        parent::__construct($serverRequest, $logger, $session, $config);
        $this->repository = $repository;
    }

    public function _create(ServerRequestInterface $request)
    {
        return $this->standardCreate($request);
    }

    public function _update(ServerRequestInterface $request, $id)
    {
        return $this->standardUpdate($request);
    }

    public function _retrieve(ServerRequestInterface $request, $id)
    {
        return $this->standardRetrieve($id);
    }

    public function _delete(ServerRequestInterface $request, $id)
    {
        return $this->standardDelete($id);
    }

    public function _list(ServerRequestInterface $request)
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
    /** @var \PHPUnit_Framework_MockObject_MockObject|RepositoryBaseInterface */
    private $repository;
    /** @var WrappedBaseRestController */
    private $controller;

    public function setUp()
    {
        $this->serverRequest = $this->createMock(ServerRequestInterface::class);
        $this->logger = $this->createMock(Logger::class);
        $this->segment = $this->createMock(SegmentInterface::class);
        $this->config = $this->createMock(Config::class);
        $this->repository = $this->createMock(RepositoryBaseInterface::class);
        $this->controller = new WrappedBaseRestController(
            $this->serverRequest,
            $this->logger,
            $this->segment,
            $this->config,
            $this->repository);
    }

    public function testCreate()
    {
        // config mocks
        $entity = new \stdClass();
        $this->serverRequest->method('getParsedBody')->willReturn($entity);
        $this->repository->method('insert')->with($this->equalTo($entity));
        // actual tests
        $result = $this->controller->_create($this->serverRequest);
        $this->assertInstanceOf(SerializableResult::class, $result);
        $this->assertEquals(201, $result->getStatusCode());
        $this->assertEquals($entity, $result->getData());
    }

    public function testRetrieve()
    {
        // config mocks
        $entity = new \stdClass();
        $this->repository->method('find')->with(1)->willReturn($entity);
        // actual tests
        $result = $this->controller->_retrieve($this->serverRequest, 1);
        $this->assertInstanceOf(SerializableResult::class, $result);
        $this->assertEquals($entity, $result->getData());
    }

    public function testUpdate()
    {
        // config mocks
        $entity = new \stdClass();
        $this->serverRequest->method('getParsedBody')->willReturn($entity);
        $this->repository->method('update')->with($entity);
        // actual tests
        $result = $this->controller->_update($this->serverRequest, 1);
        $this->assertInstanceOf(SerializableResult::class, $result);
        $this->assertEquals($entity, $result->getData());
    }

    public function testDelete()
    {
        // config mocks
        $this->repository->method('delete')->with(1);
        // actual tests
        $result = $this->controller->_delete($this->serverRequest, 1);
        $this->assertInstanceOf(NoContentResult::class, $result);
    }

    public function testList()
    {
        // config mocks
        $entities = [new \stdClass(), new \stdClass()];
        $this->repository->method('all')->willReturn($entities);
        // actual tests
        $result = $this->controller->_list($this->serverRequest);
        $this->assertInstanceOf(SerializableResult::class, $result);
        $this->assertEquals($entities, $result->getData());
    }

}
