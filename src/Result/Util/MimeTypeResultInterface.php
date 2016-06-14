<?php

namespace Creios\Creiwork\Framework\Result\Util;

/**
 * Interface MimeTypeResultInterface
 * @package Creios\Creiwork\Framework\Result\Util
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