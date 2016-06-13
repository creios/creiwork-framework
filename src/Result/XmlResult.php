<?php

namespace Creios\Creiwork\Framework\Result;

use Creios\Creiwork\Framework\Result\Util\DisposableResult;
use Creios\Creiwork\Framework\Result\Util\DisposableResultInterface;
use Creios\Creiwork\Framework\Result\Util\StatusCodeResult;

/**
 * Class XmlResult
 * @package Creios\Creiwork\Framework\Result
 */
class XmlResult extends StatusCodeResult implements DisposableResultInterface
{

    use DisposableResult;

    /**
     * @var string
     */
    private $xml;

    /**
     * XmlResult constructor.
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