<?php

namespace Creios\Creiwork\Framework\Result;

use Creios\Creiwork\Framework\Result\Util\DownloadableResult;
use Creios\Creiwork\Framework\Result\Util\DownloadableResultInterface;
use Creios\Creiwork\Framework\Result\Util\MimeTypeResult;

/**
 * Class StreamResult
 * @package Creios\Creiwork\Framework\Result
 */
class StreamResult extends MimeTypeResult implements DownloadableResultInterface
{

    use DownloadableResult;

    /**
     * @var resource
     */
    private $stream;

    /**
     * StreamResult constructor.
     * @param resource $stream
     * @param string $mimeType
     */
    public function __construct($stream, $mimeType)
    {
        $this->stream = $stream;
        $this->mimeType = $mimeType;
    }

    /**
     * @return resource
     */
    public function getStream()
    {
        return $this->stream;
    }

}