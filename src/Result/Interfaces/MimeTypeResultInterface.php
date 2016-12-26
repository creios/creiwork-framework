<?php

namespace Creios\Creiwork\Framework\Result\Interfaces;

/**
 * Interface MimeTypeResultInterface
 * @package Creios\Creiwork\Framework\Result\Interfaces
 */
interface MimeTypeResultInterface
{

    /**
     * @return string
     */
    public function getMimeType();

    /**
     * @param $mimeType
     * @return $this
     */
    public function withMimeType($mimeType);
}