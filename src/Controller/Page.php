<?php

namespace Creios\Creiwork\Framework\Controller;

class Page
{
    /** @var int */
    protected $count;
    /** @var object[] */
    protected $results;

    /**
     * Page constructor.
     * @param int $count
     * @param object[] $results
     */
    public function __construct($count, $results)
    {
        $this->count = $count;
        $this->results = $results;
    }
}