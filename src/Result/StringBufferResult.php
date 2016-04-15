<?php

namespace Creios\Creiwork\Framework\Result;

/**
 * Class StringBufferResult
 * @package Creios\Creiwork\Framework\Result
 */
class StringBufferResult extends Result implements DownloadableInterface
{

    use Downloadable;

    /**
     * @var string
     */
    protected $buffer;

    /**
     * Result constructor.
     * @param string $buffer
     */
    public function __construct($buffer)
    {
        $this->buffer = $buffer;
    }

    /**
     * @return string
     */
    public function getBuffer()
    {
        return $this->buffer;
    }
    
}