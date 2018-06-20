<?php

namespace Creios\Creiwork\Framework\Controller;

use Creios\Creiwork\Framework\Repository\RepositoryBaseInterface;
use Creios\Creiwork\Framework\Result\NoContentResult;
use Creios\Creiwork\Framework\Result\SerializableResult;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use TimTegeler\Routerunner\Controller\RestControllerInterface;

class WrappedBaseRestController extends BaseRestController implements RestControllerInterface
{

    public function __construct(RepositoryBaseInterface $repository)
    {
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
        return $this->standardList($request);
    }

    /**
     * @return string
     */
    public function getModel()
    {
        return "";
    }
}

class BaseRestControllerTest extends TestCase
{

    /** @var \PHPUnit_Framework_MockObject_MockObject|ServerRequestInterface */
    private $serverRequest;
    /** @var \PHPUnit_Framework_MockObject_MockObject|RepositoryBaseInterface */
    private $repository;
    /** @var WrappedBaseRestController */
    private $controller;

    public function setUp()
    {
        $this->serverRequest = $this->createMock(ServerRequestInterface::class);
        $this->repository = $this->createMock(RepositoryBaseInterface::class);
        $this->controller = new WrappedBaseRestController($this->repository);
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

    public function testListLimitOffsetPagination()
    {
        // config mocks
        $entities = [new \stdClass(), new \stdClass()];
        $page = new Page(2, $entities);
        $this->serverRequest->method('getQueryParams')->willReturn(['limit' => 2, 'offset' => 0]);
        $this->repository->method('limit')->with(2, 0)->willReturn($entities);
        $this->repository->method('count')->willReturn(2);
        // actual tests
        $result = $this->controller->_list($this->serverRequest);
        $this->assertInstanceOf(SerializableResult::class, $result);
        $this->assertEquals($page, $result->getData());
    }
}
