<?php
namespace Creios\Creiwork\Framework\Result;

use Creios\Creiwork\Framework\Result\Interfaces\DataResultInterface;
use Creios\Creiwork\Framework\Result\Interfaces\DisposableResultInterface;
use Creios\Creiwork\Framework\Result\Interfaces\MimeTypeResultInterface;
use Creios\Creiwork\Framework\Result\Interfaces\StatusCodeResultInterface;
use Creios\Creiwork\Framework\Result\Traits\DataResult;
use Creios\Creiwork\Framework\Result\Traits\DisposableResult;
use Creios\Creiwork\Framework\Result\Traits\MimeTypeResult;
use Creios\Creiwork\Framework\Result\Traits\StatusCodeResult;
use Creios\Creiwork\Framework\Result\Util\Result;

class SerializableResult extends Result implements
    DataResultInterface,
    MimeTypeResultInterface,
    DisposableResultInterface,
    StatusCodeResultInterface
{

    use DataResult;
    use MimeTypeResult;
    use StatusCodeResult;
    use DisposableResult;

    /**
     * SerializableResult constructor.
     * @param mixed $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Creates an SerializableResult from a string.
     * You need to provide an encoded JSON string.
     * @param mixed $data
     * @return SerializableResult
     */
    public static function createJsonResult($data)
    {
        return (new SerializableResult($data))->withMimeType('application/json');
    }

    /**
     * Creates an SerializableResult form an array using json_encode.
     * @param array|object $data array or object.
     * @param int $options (optional) json_encode otptions
     * @param int $depth (optional) json_encode depth
     * @return SerializableResult
     */
    public static function createEncodedJsonResult($data, $options = null, $depth = null){
        if ($options === null) {
            return self::createJsonResult(json_encode($data));
        }
        if ($depth === null) {
            return self::createJsonResult(json_encode($data, $options));
        }
        return self::createJsonResult(json_encode($data, $options, $depth));
    }

    /**
     * @param mixed $data
     * @return SerializableResult
     */
    public static function createXmlResult($data)
    {
        return (new SerializableResult($data))->withMimeType('text/xml');
    }

    /**
     * @param mixed $data
     * @return SerializableResult
     */
    public static function createPlainTextResult($data)
    {
        return (new SerializableResult($data))->withMimeType('text/plain');
    }

    /**
     * @param mixed $data
     * @return SerializableResult
     */
    public static function createHtmlResult($data)
    {
        return (new SerializableResult($data))->withMimeType('text/html');
    }
}
