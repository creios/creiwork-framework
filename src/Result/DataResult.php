<?php

namespace Creios\Creiwork\Framework\Result;

/**
 * Class DataResult
 * @package Creios\Creiwork\Util\Results
 */
abstract class DataResult extends DownloadableResult
{

    /**
     * @var array
     */
    protected $data;

    /**
     * Result constructor.
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

}
