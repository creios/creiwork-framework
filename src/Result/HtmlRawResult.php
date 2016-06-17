<?php
namespace Creios\Creiwork\Framework\Result;

use Creios\Creiwork\Framework\Result\Interfaces\DisposableResultInterface;
use Creios\Creiwork\Framework\Result\Interfaces\StatusCodeResultInterface;
use Creios\Creiwork\Framework\Result\Traits\DisposableResult;
use Creios\Creiwork\Framework\Result\Traits\StatusCodeResult;
use Creios\Creiwork\Framework\Result\Util\Result;

/**
 * Class HtmlRawResult
 * @package Creios\Creiwork\Framework\Result
 */
class HtmlRawResult extends Result implements StatusCodeResultInterface, DisposableResultInterface
{

    use StatusCodeResult;
    use DisposableResult;

    /**
     * @var string
     */
    private $html;

    /**
     * HtmlRawResult constructor.
     * @param string $html
     */
    public function __construct($html)
    {
        $this->html = $html;
    }

    /**
     * @return string
     */
    public function getHtml()
    {
        return $this->html;
    }

}
