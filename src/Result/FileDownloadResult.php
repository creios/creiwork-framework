<?php
namespace Creios\Creiwork\Framework\Result;

class FileDownloadResult extends Result
{

    private $path;

    public function __construct($path)
    {
        $this->path = $path;
    }

    public function getPath()
    {
        return $this->path;
    }
}
