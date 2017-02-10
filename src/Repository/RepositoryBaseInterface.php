<?php

namespace Creios\Creiwork\Framework\Repository;

/**
 * Interface RepositoryInterface
 * @package Creios\Creiwork\Framework\Repository
 */
interface RepositoryBaseInterface
{

    /**
     * @param $id
     * @return object
     */
    public function find($id);

    /**
     * @return object[]
     */
    public function all();

    /**
     * @param int $limit
     * @param int $offset
     * @return object[]
     */
    public function limit($limit, $offset = 0);

    /**
     * @return int
     */
    public function count();

    /**
     * @param object
     * @return int
     */
    public function insert($entity);

    /**
     * @param object
     */
    public function update($entity);

    /**
     * @param int $id
     */
    public function delete($id);
    
}
