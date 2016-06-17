<?php

namespace Creios\Creiwork\Framework\Result;

use Creios\Creiwork\Framework\Result\Interfaces\DisposableResultInterface;
use Creios\Creiwork\Framework\Result\Interfaces\StatusCodeResultInterface;
use Creios\Creiwork\Framework\Result\Traits\DisposableResult;
use Creios\Creiwork\Framework\Result\Traits\StatusCodeResult;
use Creios\Creiwork\Framework\Result\Util\Result;

/**
 * Class XmlRawResult
 * @package Creios\Creiwork\Framework\Result
 */
class XmlRawResult extends Result implements StatusCodeResultInterface, DisposableResultInterface
{

    use StatusCodeResult;
    use DisposableResult;

    /**
     * @var string
     */
    private $xml;

    /**
     * XmlRawResult constructor.
     * @param string $xml
     */
    public function __construct($xml)
    {
        $this->xml = $xml;
    }

    /**
     * @return string
     */
    public function getXml()
    {
        return $this->xml;
    }

}