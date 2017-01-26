<?php

namespace Creios\Creiwork\Framework\Result;

use Creios\Creiwork\Framework\Result\Interfaces\DisposableResultInterface;
use Creios\Creiwork\Framework\Result\Interfaces\MimeTypeResultInterface;
use Creios\Creiwork\Framework\Result\Interfaces\StatusCodeResultInterface;
use Creios\Creiwork\Framework\Result\Traits\DisposableResult;
use Creios\Creiwork\Framework\Result\Traits\MimeTypeResult;
use Creios\Creiwork\Framework\Result\Traits\StatusCodeResult;
use Creios\Creiwork\Framework\Result\Util\Result;

/**
 * Class StringBufferResult
 * @package Creios\Creiwork\Framework\Result
 */
class StringBufferResult extends Result implements MimeTypeResultInterface, StatusCodeResultInterface, DisposableResultInterface
{

    use MimeTypeResult;
    use StatusCodeResult;
    use DisposableResult;

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
