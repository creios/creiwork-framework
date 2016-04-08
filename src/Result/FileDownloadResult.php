<?php
namespace Creios\Creiwork\Framework\Result;

/**
 * Class FileDownloadResult
 * @package Creios\Creiwork\Framework\Result
 */
class FileDownloadResult extends Result
{

    /**
     * @var string
     */
    private $path;

    /**
     * FileDownloadResult constructor.
     * @param $path
     */
    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }
}
