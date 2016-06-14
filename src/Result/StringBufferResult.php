<?php

namespace Creios\Creiwork\Framework\Result;

use Creios\Creiwork\Framework\Result\Util\DisposableResult;
use Creios\Creiwork\Framework\Result\Util\DisposableResultInterface;
use Creios\Creiwork\Framework\Result\Util\MimeTypeResult;
use Creios\Creiwork\Framework\Result\Util\MimeTypeResultInterface;
use Creios\Creiwork\Framework\Result\Util\Result;
use Creios\Creiwork\Framework\Result\Util\StatusCodeResult;
use Creios\Creiwork\Framework\Result\Util\StatusCodeResultInterface;

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
