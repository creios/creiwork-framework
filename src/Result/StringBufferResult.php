<?php

namespace Creios\Creiwork\Framework\Result;

/**
 * Class StringBufferResult
 * @package Creios\Creiwork\Framework\Result
 */
class StringBufferResult extends MimeTypeResult implements DownloadableResultInterface
{

    use DownloadableResult;

    /**
     * @var string
     */
    protected $buffer;

    /**
     * Result constructor.
     * @param string $buffer
     * @param string $mimeType
     */
    public function __construct($buffer, $mimeType = null)
    {
        $this->buffer = $buffer;
        $this->mimeType = $mimeType;
    }

    /**
     * @return string
     */
    public function getBuffer()
    {
        return $this->buffer;
    }

}