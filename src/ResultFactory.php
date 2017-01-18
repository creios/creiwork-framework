<?php

namespace Creios\Creiwork\Framework;

use Creios\Creiwork\Framework\Result\StringResult;

/**
 * Class ResultFactory
 * @package Creios\Creiwork\Framework
 */
class ResultFactory
{

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

}