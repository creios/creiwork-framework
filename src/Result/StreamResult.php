<?php

namespace Creios\Creiwork\Framework\Result;

use Creios\Creiwork\Framework\Result\Util\DisposableResult;
use Creios\Creiwork\Framework\Result\Util\DisposableResultInterface;
use Creios\Creiwork\Framework\Result\Util\MimeTypeResult;
use Creios\Creiwork\Framework\Result\Util\MimeTypeResultInterface;
use Creios\Creiwork\Framework\Result\Util\StatusCodeResult;
use Creios\Creiwork\Framework\Result\Util\StatusCodeResultInterface;

/**
 * Class StreamResult
 * @package Creios\Creiwork\Framework\Result
 */
class StreamResult implements MimeTypeResultInterface, StatusCodeResultInterface, DisposableResultInterface
{

    use MimeTypeResult;
    use StatusCodeResult;
    use DisposableResult;

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
