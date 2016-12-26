<?php

namespace Creios\Creiwork\Framework\Result\Interfaces;

/**
 * Interface StatusCodeResultInterface
 * @package Creios\Creiwork\Framework\Result\Interfaces
 */
interface StatusCodeResultInterface
{

    /**
     * @return int
     */
    public function getStatusCode();

    /**
     * @param int $statusCode
     * @return $this
     */
    public function withStatusCode($statusCode);
}