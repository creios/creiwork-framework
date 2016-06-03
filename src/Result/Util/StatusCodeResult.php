<?php

namespace Creios\Creiwork\Framework\Result\Util;

/**
 * Class StatusCodeResult
 * @package Creios\Creiwork\Framework\Result
 */
abstract class StatusCodeResult extends Result
{

    /** @var int */
    protected $statusCode;

    /**
     * @return mixed
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param $statusCode
     * @return $this
     */
    public function withStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

}