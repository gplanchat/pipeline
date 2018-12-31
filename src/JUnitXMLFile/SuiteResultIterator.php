<?php

namespace Kiboko\Component\JUnitXMLFile;

use Kiboko\Component\JUnitXMLFile\Result\JUnitCase;
use Kiboko\Component\JUnitXMLFile\Result\JUnitSuite;

final class SuiteResultIterator implements \RecursiveIterator, \Countable
{
    /**
     * @var SuiteResultIterator[]
     */
    private $children;

    /**
     * @var JUnitCase[]
     */
    private $suites;

    /**
     * @param SuiteResultIterator[] $children
     * @param JUnitCase[]           $cases
     */
    public function __construct(array $children = [], array $cases = [])
    {
        $this->children = new \ArrayIterator($children);
        $this->suites = new \ArrayIterator($cases);
    }

    public function appendSuite(JUnitSuite $suite)
    {
        $this->suites->append($suite);
    }

    public function appendChild(SuiteResultIterator $child)
    {
        $this->children->append($child);
    }

    public function current()
    {
        return $this->suites->current();
    }

    public function next()
    {
        $this->suites->next();
    }

    public function key()
    {
        return $this->suites->key();
    }

    public function valid()
    {
        return $this->suites->valid();
    }

    public function rewind()
    {
        $this->suites->rewind();
    }

    public function hasChildren()
    {
        return $this->children->count() > 0;
    }

    public function getChildren()
    {
        return $this->children;
    }

    public function count()
    {
        $count = $this->suites->count();

        foreach ($this->children as $item) {
            $count += $item->count();
        }

        return $count;
    }
}
