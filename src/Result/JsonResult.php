<?php

namespace Creios\Creiwork\Framework\Result;

use Creios\Creiwork\Framework\Result\Util\DataResult;
use Creios\Creiwork\Framework\Result\Util\DisposableResult;
use Creios\Creiwork\Framework\Result\Util\DisposableResultInterface;

/**
 * Class JsonResult
 * @package Creios\Creiwork\Util\Results
 */
class JsonResult extends DataResult implements DisposableResultInterface
{

    use DisposableResult;
    
}
