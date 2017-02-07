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
     * @return int
     */
    public function count();

    /**
     * @param object
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
