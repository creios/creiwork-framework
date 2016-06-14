<?php

namespace Creios\Creiwork\Framework\Result\Util;

/**
 * Class DataResult
 * @package Creios\Creiwork\Framework\Result\Util
 */
trait DataResult
{

    /**
     * @var array
     */
    protected $data;

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

}
