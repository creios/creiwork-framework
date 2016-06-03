<?php

namespace Creios\Creiwork\Framework\Result;

/**
 * Class FileResult
 * @package Creios\Creiwork\Framework\Result
 */
class FileResult extends MimeTypeResult implements DownloadableResultInterface
{

    use DownloadableResult;

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
