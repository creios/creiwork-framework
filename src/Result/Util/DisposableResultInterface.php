<?php

namespace Creios\Creiwork\Framework\Result\Util;

/**
 * Interface DownloadableInterface
 * @package Creios\Creiwork\Framework\Result
 */
interface DisposableResultInterface
{

    /**
     * @param Disposition $disposition
     * @return $this
     */
    public function withDisposition(Disposition $disposition);

    /**
     * @return Disposition
     */
    public function getDisposition();
}
