<?php

namespace Creios\Creiwork\Framework\Result;

/**
 * Interface DownloadableInterface
 * @package Creios\Creiwork\Framework\Result
 */
interface DownloadableInterface
{

    /**
     * @param string $filename
     * @return DownloadableInterface
     */
    public function asDownload($filename);

    /**
     * @return string
     */
    public function getFilename();
    
}