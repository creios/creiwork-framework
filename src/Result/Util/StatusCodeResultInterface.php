<?php

namespace Creios\Creiwork\Framework\Result\Util;

/**
 * Interface StatusCodeResultInterface
 * @package Creios\Creiwork\Framework\Result\Util
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