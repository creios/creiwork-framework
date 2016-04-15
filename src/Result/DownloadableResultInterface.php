<?php

namespace Creios\Creiwork\Framework\Result;

/**
 * Interface DownloadableInterface
 * @package Creios\Creiwork\Framework\Result
 */
interface DownloadableResultInterface
{

    /**
     * @param string $filename
     * @return DownloadableResultInterface
     */
    public function asDownload($filename);

    /**
     * @return string
     */
    public function getFilename();

}