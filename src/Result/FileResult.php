<?php

namespace Creios\Creiwork\Framework\Result;

/**
 * Class FileResult
 * @package Creios\Creiwork\Framework\Result
 */
class FileResult extends Result implements DownloadableInterface
{

    use Downloadable;
    
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
