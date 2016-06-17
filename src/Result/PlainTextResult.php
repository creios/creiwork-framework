<?php

namespace Creios\Creiwork\Framework\Result;

use Creios\Creiwork\Framework\Result\Interfaces\DisposableResultInterface;
use Creios\Creiwork\Framework\Result\Interfaces\StatusCodeResultInterface;
use Creios\Creiwork\Framework\Result\Traits\DisposableResult;
use Creios\Creiwork\Framework\Result\Traits\StatusCodeResult;
use Creios\Creiwork\Framework\Result\Util\Result;

/**
 * Class PlainTextResult
 * @package Creios\Creiwork\Framework\Result
 */
class PlainTextResult extends Result implements StatusCodeResultInterface, DisposableResultInterface
{

    use StatusCodeResult;
    use DisposableResult;

    /** @var string */
    private $plainText;

    /**
     * PlainTextResult constructor.
     * @param string $plainText
     */
    public function __construct($plainText)
    {
        $this->plainText = $plainText;
    }

    /**
     * @return string
     */
    public function getPlainText()
    {
        return $this->plainText;
    }
    

}