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
     * @param mixed $data
     * @return SerializableResult
     */
    public static function createJsonResult($data)
    {
        return (new SerializableResult($data))->withMimeType('application/json');
    }

    /**
     * @param mixed $data
     * @return SerializableResult
     */
    public static function createXmlResult($data)
    {
        return (new SerializableResult($data))->withMimeType('text/xml');
    }

}