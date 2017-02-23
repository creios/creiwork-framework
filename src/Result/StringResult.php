<?php

namespace Creios\Creiwork\Framework\Result;

use Creios\Creiwork\Framework\Result\Interfaces\DisposableResultInterface;
use Creios\Creiwork\Framework\Result\Interfaces\MimeTypeResultInterface;
use Creios\Creiwork\Framework\Result\Interfaces\StatusCodeResultInterface;
use Creios\Creiwork\Framework\Result\Traits\DisposableResult;
use Creios\Creiwork\Framework\Result\Traits\MimeTypeResult;
use Creios\Creiwork\Framework\Result\Traits\StatusCodeResult;
use Creios\Creiwork\Framework\Result\Util\Result;
use Creios\Creiwork\Framework\StatusCodes;

/**
 * Class StringResult
 * @package Creios\Creiwork\Framework\Result
 */
class StringResult extends Result implements MimeTypeResultInterface, StatusCodeResultInterface, DisposableResultInterface
{

    use MimeTypeResult;
    use StatusCodeResult;
    use DisposableResult;

    /** @var string */
    private $plainText;

    /**
     * StringResult constructor.
     * @param string $plainText
     */
    public function __construct($plainText)
    {
        $this->plainText = $plainText;
    }

    /**
     * @param $string
     * @return StringResult
     */
    public static function createPlainTextResult($string)
    {
        return (new StringResult($string))->withMimeType('text/plain');
    }

    /**
     * @param $string
     * @return StringResult
     */
    public static function createJsonResult($string)
    {
        return (new StringResult($string))->withMimeType('application/json');
    }

    /**
     * @param $string
     * @return StringResult
     */
    public static function createXmlResult($string)
    {
        return (new StringResult($string))->withMimeType('text/xml');
    }

    /**
     * @param $string
     * @return StringResult
     */
    public static function createHtmlResult($string)
    {
        return (new StringResult($string))->withMimeType('text/html');
    }

    /**
     * @param int $statusCode
     * @return StringResult
     */
    public static function createPlainTextResultFromStatusCode($statusCode)
    {
        return StringResult::createPlainTextResult(
            StatusCodes::getMessageForCode($statusCode))
            ->withStatusCode($statusCode);
    }

    /**
     * @return string
     */
    public function getPlainText()
    {
        return $this->plainText;
    }

}