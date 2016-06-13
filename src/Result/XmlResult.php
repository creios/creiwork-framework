<?php

namespace Creios\Creiwork\Framework\Result;

use Creios\Creiwork\Framework\Result\Util\DownloadableResult;
use Creios\Creiwork\Framework\Result\Util\DownloadableResultInterface;
use Creios\Creiwork\Framework\Result\Util\StatusCodeResult;

/**
 * Class XmlResult
 * @package Creios\Creiwork\Framework\Result
 */
class XmlResult extends StatusCodeResult implements DownloadableResultInterface
{

    use DownloadableResult;

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