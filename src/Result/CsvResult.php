<?php
namespace Creios\Creiwork\Framework\Result;

use Creios\Creiwork\Framework\Result\Interfaces\DisposableResultInterface;
use Creios\Creiwork\Framework\Result\Traits\DisposableResult;
use Creios\Creiwork\Framework\Result\Util\Result;

class CsvResult extends Result implements DisposableResultInterface
{
    use DisposableResult;

    /** @var array|\Iterator */
    private $data;

    /**
     * @param array|\Iterator $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * @return array|\Iterator
     */
    public function getData()
    {
        return $this->data;
    }
}
