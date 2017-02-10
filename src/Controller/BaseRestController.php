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
     * @param ServerRequestInterface $request
     * @return SerializableResult
     */
    protected function standardList(ServerRequestInterface $request)
    {
        $limitKey = 'limit';
        $offsetKey = 'offset';
        $queryParams = $request->getQueryParams();
        if (isset($queryParams[$limitKey]) && isset($queryParams[$offsetKey])) {
            $limit = (int)$queryParams[$limitKey];
            $offset = (int)$queryParams[$offsetKey];
            $count = $this->repository->count();
            $entities = $this->repository->limit($limit, $offset);

            $data = new Page(
                $count,
                $this->generateNextLink($request, $count, $limit, $offset),
                $this->generatePreviousLink($request, $limit, $offset),
                $entities
            );
        } else {
            $data = $this->repository->all();
        }
        return new SerializableResult($data);
    }

    protected function calculateNextLimitAndOffset($count, $limit, $offset)
    {
        $lastElementNumber = $limit + $offset;
        $remainderNext = $count - $lastElementNumber;
        //checking if next link must be generate
        if ($remainderNext > 0) {
            //set offset to last element
            $offset = $lastElementNumber;
            return ['limit' => $limit, 'offset' => $offset];
        } else {
            return false;
        }
    }

    protected function calculatePreviousLimitAndOffset($limit, $offset)
    {
        if ($offset > 0) {
            if ($offset > $limit) {
                $offset -= $limit;
            } else {
                $offset = 0;
            }
            return ['limit' => $limit, 'offset' => $offset];
        } else {
            return false;
        }
    }

    protected function generateNextLink(ServerRequestInterface $request, $count, $limit, $offset)
    {
        $nextLimitAndOffset = $this->calculateNextLimitAndOffset($count, $limit, $offset);
        if ($nextLimitAndOffset) {
            return $this->generateLink($request, $nextLimitAndOffset['limit'], $nextLimitAndOffset['offset']);
        } else {
            return null;
        }
    }

    protected function generatePreviousLink(ServerRequestInterface $request, $limit, $offset)
    {
        $previousLimitAndOffset = $this->calculatePreviousLimitAndOffset($limit, $offset);
        if ($previousLimitAndOffset) {
            return $this->generateLink($request, $previousLimitAndOffset['limit'], $previousLimitAndOffset['offset']);
        } else {
            return null;
        }
    }

    protected function generateLink(ServerRequestInterface $request, $limit, $offset)
    {
        return $request->getUri()->withQuery(sprintf('limit=%s&offset=%s', $limit, $offset));
    }

}