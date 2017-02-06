<?php

namespace Creios\Creiwork\Framework\Controller;

use Creios\Creiwork\Framework\Result\NoContentResult;
use Creios\Creiwork\Framework\Result\SerializableResult;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class BaseRestController
 * @package Creios\Creiwork\Framework
 */
abstract class BaseRestController extends BaseController
{

//    protected $repository;
    /** @var string */
    protected $model;
    /** @var string */
    protected $mimeType = "application/json";

    /**
     * @param ServerRequestInterface $request
     * @return SerializableResult
     */
    protected function standardCreate(ServerRequestInterface $request)
    {
        //$this->repository->insert($request->getParsedBody());
        return (new SerializableResult([]))->withMimeType($this->mimeType);
    }

    /**
     * @param ServerRequestInterface $request
     * @param int $id
     * @return SerializableResult
     */
    protected function standardRetrieve(ServerRequestInterface $request, $id)
    {
        //$model = $this->repository->get($id);
        return (new SerializableResult([]))->withMimeType($this->mimeType);
    }

    /**
     * @param ServerRequestInterface $request
     * @param int $id
     * @return SerializableResult
     */
    protected function standardUpdate(ServerRequestInterface $request, $id)
    {
        //$this->repository->update($request->getParsedBody());
        return (new SerializableResult([]))->withMimeType($this->mimeType);
    }

    /**
     * @param ServerRequestInterface $request
     * @param int $id
     * @return NoContentResult
     */
    protected function standardDelete(ServerRequestInterface $request, $id)
    {
        //$model = $this->repository->delete($id);
        return new NoContentResult();
    }

    /**
     * @param ServerRequestInterface $request
     * @return SerializableResult
     */
    protected function standardList(ServerRequestInterface $request)
    {
        //$model = $this->repository->all();
        return (new SerializableResult([]))->withMimeType($this->mimeType);
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