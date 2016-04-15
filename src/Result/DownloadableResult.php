<?php

namespace Creios\Creiwork\Framework\Result;

/**
 * Trait DownloadableResult
 * @package Creios\Creiwork\Framework\Result
 */
trait DownloadableResult
{

    /**
     * @var string
     */
    protected $filename;

    /**
     * @param string $filename
     * @return DownloadableResultInterface
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