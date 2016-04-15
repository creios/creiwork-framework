<?php

namespace Creios\Creiwork\Framework\Result;

interface DownloadableInterface
{

    /**
     * @param string $filename
     * @return $this
     */
    public function asDownload($filename);

    /**
     * @return string
     */
    public function getFilename();
    
}