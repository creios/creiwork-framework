<?php

namespace Creios\Creiwork\Framework\Controller;

use Creios\Creiwork\Framework\Repository\RepositoryBaseInterface;
use Creios\Creiwork\Framework\Result\NoContentResult;
use Creios\Creiwork\Framework\Result\SerializableResult;
use Creios\Creiwork\Framework\StatusCodes;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class BaseRestController
 * @package Creios\Creiwork\Framework
 */
abstract class BaseRestController extends BaseController
{

    /** @var string */
    protected $model;
    /** @var  RepositoryBaseInterface */
    protected $repository;

    /**
     * @return string
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param ServerRequestInterface $request
     * @return SerializableResult
     */
    protected function standardCreate(ServerRequestInterface $request)
    {
        $entity = $request->getParsedBody();
        $entity->id = $this->repository->insert($entity);
        return (new SerializableResult($entity))->withStatusCode(StatusCodes::HTTP_CREATED);
    }

    /**
     * @param int $id
     * @return SerializableResult
     */
    protected function standardRetrieve($id)
    {
        $entity = $this->repository->find($id);
        return new SerializableResult($entity);
    }

    /**
     * @param ServerRequestInterface $request
     * @return SerializableResult
     */
    protected function standardUpdate(ServerRequestInterface $request)
    {
        $entity = $request->getParsedBody();
        $this->repository->update($entity);
        return new SerializableResult($entity);
    }

    /**
     * @param int $id
     * @return NoContentResult
     */
    protected function standardDelete($id)
    {
        $this->repository->delete($id);
        return new NoContentResult();
    }

    /**
     * @return SerializableResult
     */
    protected function standardList()
    {
        $entities = $this->repository->all();
        return new SerializableResult($entities);
    }

}