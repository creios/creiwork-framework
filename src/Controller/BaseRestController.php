<?php

namespace Creios\Creiwork\Framework\Controller;

use Creios\Creiwork\Framework\Message\Factory\ErrorFactory;
use Creios\Creiwork\Framework\Repository\RepositoryBaseInterface;
use Creios\Creiwork\Framework\Result\NoContentResult;
use Creios\Creiwork\Framework\Result\SerializableResult;
use Creios\Creiwork\Framework\StatusCodes;
use Noodlehaus\Config;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class BaseRestController
 * @package Creios\Creiwork\Framework
 */
abstract class BaseRestController extends BaseController
{

    /** @var RepositoryBaseInterface */
    protected $repository;
    /** @var Config */
    protected $config;

    /**
     * BaseRestController constructor.
     * @param Config $config
     * @param RepositoryBaseInterface $repository
     */
    public function __construct(Config $config, RepositoryBaseInterface $repository)
    {
        $this->config = $config;
        $this->repository = $repository;
    }

    /**
     * @return string
     */
    abstract public function getModel();

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
        try {
            $entity = $this->repository->find($id);
            return new SerializableResult($entity);
        } catch (\Exception $e) {
            $error = (new ErrorFactory($this->getSupportContact()))
                ->setSuggestion("Perhaps you are using the wrong id (${id})")
                ->buildError("Record with id ${id} not found");

            return (new SerializableResult($error))
                ->withStatusCode(StatusCodes::HTTP_NOT_FOUND);
        }
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
     * @param ServerRequestInterface $request
     * @return SerializableResult
     */
    protected function standardList(ServerRequestInterface $request)
    {
        $limitKey = 'limit';
        $offsetKey = 'offset';
        $queryParams = $request->getQueryParams();
        if (isset($queryParams[$limitKey], $queryParams[$offsetKey])) {
            $limit = (int)$queryParams[$limitKey];
            $offset = (int)$queryParams[$offsetKey];
            $count = $this->repository->count();
            $entities = $this->repository->limit($limit, $offset);
            $data = new Page($count, $entities);
        } else {
            $data = $this->repository->all();
        }
        return new SerializableResult($data);
    }

    /**
     * @return string
     */
    private function getSupportContact()
    {
        return $this->config->get('support-contact');
    }

}