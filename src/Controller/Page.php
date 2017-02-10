<?php

namespace Creios\Creiwork\Framework\Controller;

class Page
{
    /** @var int */
    protected $count;
    /** @var string */
    protected $next;
    /** @var string */
    protected $previous;
    /** @var object[] */
    protected $results;

    /**
     * Page constructor.
     * @param int $count
     * @param string $next
     * @param string $previous
     * @param \object[] $results
     */
    public function __construct($count, $next, $previous, array $results)
    {
        $this->count = $count;
        $this->next = $next;
        $this->previous = $previous;
        $this->results = $results;
    }

}