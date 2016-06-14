<?php

namespace Creios\Creiwork\Framework\Result;

use Creios\Creiwork\Framework\Result\Util\DisposableResult;
use Creios\Creiwork\Framework\Result\Util\DisposableResultInterface;
use Creios\Creiwork\Framework\Result\Util\Result;
use Creios\Creiwork\Framework\Result\Util\StatusCodeResult;
use Creios\Creiwork\Framework\Result\Util\StatusCodeResultInterface;

/**
 * Class XmlResult
 * @package Creios\Creiwork\Framework\Result
 */
class XmlResult extends Result implements StatusCodeResultInterface, DisposableResultInterface
{

    use StatusCodeResult;
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