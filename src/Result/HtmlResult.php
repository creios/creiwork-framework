<?php
namespace Creios\Creiwork\Framework\Result;

use Creios\Creiwork\Framework\Result\Util\DisposableResult;
use Creios\Creiwork\Framework\Result\Util\DisposableResultInterface;
use Creios\Creiwork\Framework\Result\Util\Result;
use Creios\Creiwork\Framework\Result\Util\StatusCodeResult;
use Creios\Creiwork\Framework\Result\Util\StatusCodeResultInterface;

/**
 * Class HtmlResult
 * @package Creios\Creiwork\Framework\Result
 */
class HtmlResult extends Result implements StatusCodeResultInterface, DisposableResultInterface
{

    use StatusCodeResult;
    use DisposableResult;

    /**
     * @var string
     */
    private $html;

    /**
     * HtmlResult constructor.
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
