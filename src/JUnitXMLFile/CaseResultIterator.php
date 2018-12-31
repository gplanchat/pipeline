<?php

namespace Kiboko\Component\JUnitXMLFile;

use Kiboko\Component\JUnitXMLFile\Result\JUnitCase;

final class CaseResultIterator implements \Iterator, \Countable
{
    /**
     * @var JUnitCase[]
     */
    private $cases;

    /**
     * @param JUnitCase[] $cases
     */
    public function __construct(array $cases = [])
    {
        $this->cases = new \ArrayIterator($cases);
    }

    public function appendCase(JUnitCase $case)
    {
        $this->cases->append($case);
    }

    public function current()
    {
        return $this->cases->current();
    }

    public function next()
    {
        $this->cases->next();
    }

    public function key()
    {
        return $this->cases->key();
    }

    public function valid()
    {
        return $this->cases->valid();
    }

    public function rewind()
    {
        $this->cases->rewind();
    }

    public function count()
    {
        return $this->cases->count();
    }
}
