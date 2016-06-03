<?php
namespace Creios\Creiwork\Framework\Result;

/**
 * Class HtmlResult
 * @package Creios\Creiwork\Framework\Result
 */
class HtmlResult extends Result implements DownloadableResultInterface
{

    use DownloadableResult;

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
