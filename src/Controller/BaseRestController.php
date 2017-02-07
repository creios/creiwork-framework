<?php

namespace Creios\Creiwork\Framework\Controller;

use Creios\Creiwork\Framework\Repository\RepositoryBaseInterface;
use Creios\Creiwork\Framework\Result\NoContentResult;
use Creios\Creiwork\Framework\Result\SerializableResult;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class BaseRestController
 * @package Creios\Creiwork\Framework
 */
abstract class BaseRestController extends BaseController
{

    /** @var string */
    protected $model;
    /** @var string */
    protected $mimeType = "application/json";
    /** @var  RepositoryBaseInterface */
    protected $repository;

    /**
     * @param ServerRequestInterface $request
     * @return SerializableResult
     */
    protected function standardCreate(ServerRequestInterface $request)
    {
        $entity = $request->getParsedBody();
        $this->repository->insert($entity);
        return (new SerializableResult($entity))->withMimeType($this->mimeType);
    }

    /**
     * @param ServerRequestInterface $request
     * @param int $id
     * @return SerializableResult
     */
    protected function standardRetrieve(ServerRequestInterface $request, $id)
    {
        $entity = $this->repository->find($id);
        return (new SerializableResult($entity))->withMimeType($this->mimeType);
    }

    /**
     * @param ServerRequestInterface $request
     * @param int $id
     * @return SerializableResult
     */
    protected function standardUpdate(ServerRequestInterface $request, $id)
    {
        //Todo: Not sure how update is performed. Using $id or not?
        $entity = $request->getParsedBody();
        $this->repository->update($entity);
        return (new SerializableResult($entity))->withMimeType($this->mimeType);
    }

    /**
     * @param ServerRequestInterface $request
     * @param int $id
     * @return NoContentResult
     */
    protected function standardDelete(ServerRequestInterface $request, $id)
    {
        $this->repository->delete($id);
        return new NoContentResult();
    }

    /**
     * @param ServerRequestInterface $request
     * @return SerializableResult
     */
    protected function standardList(ServerRequestInterface $request)
    {
        $entities = $this->repository->all();
        return (new SerializableResult($entities))->withMimeType($this->mimeType);
    }

    /**
     * @return string
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @return string
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

}