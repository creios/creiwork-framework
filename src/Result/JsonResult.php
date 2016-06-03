<?php

namespace Creios\Creiwork\Framework\Result;

use Creios\Creiwork\Framework\Result\Util\DataResult;
use Creios\Creiwork\Framework\Result\Util\DownloadableResult;
use Creios\Creiwork\Framework\Result\Util\DownloadableResultInterface;

/**
 * Class JsonResult
 * @package Creios\Creiwork\Util\Results
 */
class JsonResult extends DataResult implements DownloadableResultInterface
{

    use DownloadableResult;
    
}