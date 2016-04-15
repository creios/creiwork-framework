<?php

namespace Creios\Creiwork\Framework\Result;

/**
 * Trait Downloadable
 * @package Creios\Creiwork\Framework\Result
 */
trait Downloadable
{

    /**
     * @var string
     */
    protected $filename;

    /**
     * @param string $filename
     * @return DownloadableInterface
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