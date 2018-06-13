<?php

namespace Creios\Creiwork\Framework\Provider;

class SharedDataProvider
{
    /** @var array */
    protected $data = [];

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return bool
     */
    public function hasData()
    {
        return count($this->data) > 0;
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function addData($key, $value)
    {
        $this->data[$key] = $value;
    }

}