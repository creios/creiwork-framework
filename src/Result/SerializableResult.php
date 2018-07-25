<?php
namespace Creios\Creiwork\Framework\Result;

use Creios\Creiwork\Framework\Result\Interfaces\DataResultInterface;
use Creios\Creiwork\Framework\Result\Interfaces\DisposableResultInterface;
use Creios\Creiwork\Framework\Result\Interfaces\MimeTypeResultInterface;
use Creios\Creiwork\Framework\Result\Interfaces\StatusCodeResultInterface;
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

    use MimeTypeResult;
    use StatusCodeResult;
    use DisposableResult;

    /** @var object|array  */
    private $data;
    /**
     * @var array
     */
    private $attributesToBeSerialized;

    /**
     * SerializableResult constructor.
     * @param mixed $data
     * @param array $attributesToBeSerialized
     */
    public function __construct($data, $attributesToBeSerialized = [])
    {
        $this->data = $data;
        $this->attributesToBeSerialized = $attributesToBeSerialized;
    }

    /**
     * @param mixed $data
     * @param array $attributesToBeSerialized
     * @return SerializableResult
     */
    public static function createJsonResult($data,  $attributesToBeSerialized = [])
    {
        return (new SerializableResult($data, $attributesToBeSerialized))->withMimeType('application/json');
    }

    /**
     * @param mixed $data
     * @param array $attributesToBeSerialized
     * @return SerializableResult
     */
    public static function createXmlResult($data, $attributesToBeSerialized = [])
    {
        return (new SerializableResult($data, $attributesToBeSerialized))->withMimeType('text/xml');
    }

    /**
     * @return array|object
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function getAttributesToBeSerialized(): array
    {
        return $this->attributesToBeSerialized;
    }



}