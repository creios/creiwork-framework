<?php

namespace Creios\Creiwork\Framework\Result\Util;

/**
 * Class DataResult
 * @package Creios\Creiwork\Util\Results
 */
abstract class DataResult extends StatusCodeResult
{

    /**
     * @var array
     */
    protected $data;

    /**
     * Result constructor.
     * @param mixed $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

}