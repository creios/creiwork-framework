<?php

namespace Creios\Creiwork\Framework\Result\Abstracts;

use Creios\Creiwork\Framework\Result\Interfaces\DisposableResultInterface;
use Creios\Creiwork\Framework\Result\Interfaces\MimeTypeResultInterface;
use Creios\Creiwork\Framework\Result\Interfaces\StatusCodeResultInterface;
use Creios\Creiwork\Framework\Result\Traits\DisposableResult;
use Creios\Creiwork\Framework\Result\Traits\MimeTypeResult;
use Creios\Creiwork\Framework\Result\Traits\StatusCodeResult;
use Creios\Creiwork\Framework\Result\Util\Result;

/**
 * Class AbstractFileResult
 * @package Creios\Creiwork\Framework\Result
 */
class AbstractFileResult extends Result implements StatusCodeResultInterface, MimeTypeResultInterface, DisposableResultInterface
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
     * @param string $path
     * @param string $mimeType
     */
    public function __construct($path, $mimeType = null)
    {
        $this->path = $path;
        $this->mimeType = $mimeType;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

}
