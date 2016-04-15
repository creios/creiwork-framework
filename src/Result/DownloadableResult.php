<?php

namespace Creios\Creiwork\Framework\Result;

/**
 * Class DownloadableResult
 * @package Creios\Creiwork\Framework\Result
 */
abstract class DownloadableResult extends Result
{

    /**
     * @var string
     */
    protected $filename;

    /**
     * @param string $filename
     * @return $this
     */
    public function asDownload($filename)
    {
        $this->filename = $filename;
        return $this;
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

}