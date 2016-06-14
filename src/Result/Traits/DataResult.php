<?php

namespace Creios\Creiwork\Framework\Result\Traits;

/**
 * Class DataResult
 * @package Creios\Creiwork\Framework\Result\Traits
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
