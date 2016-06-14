<?php

namespace Creios\Creiwork\Framework\Result\Traits;

/**
 * Class StatusCodeResult
 * @package Creios\Creiwork\Framework\Result\Traits
 */
trait StatusCodeResult
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