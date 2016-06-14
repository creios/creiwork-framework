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
 * Class FileResult
 * @package Creios\Creiwork\Framework\Result
 */
class FileResult extends Result implements StatusCodeResultInterface, MimeTypeResultInterface, DisposableResultInterface
{

    use StatusCodeResult;
    use MimeTypeResult;
    use DisposableResult;

    /**
     * @var string
     */
    private $path;

    /**
     * FileDownloadResult constructor.
     * @param $path
     * @param $mimeType
     */
    public function __construct($path, $mimeType = null)
    {
        $this->path = $path;
        $this->mimeType = $mimeType;
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

}
