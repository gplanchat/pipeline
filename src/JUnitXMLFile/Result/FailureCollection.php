<?php

namespace Kiboko\Component\JUnitXMLFile\Result;

class FailureCollection implements Collection
{
    /**
     * @var Failure[]
     */
    private $values;

    /**
     * @param Failure[] ...$errors
     */
    public function __construct(Failure ...$errors)
    {
        $this->values = $errors;
    }

    /**
     * @return \Traversable|Failure[]
     */
    public function getIterator()
    {
        yield from $this->values;
    }

    /**
     * @return Failure
     */
    public function pop(): Failure
    {
        return array_pop($this->values);
    }

    /**
     * @return Failure
     */
    public function shift(): Failure
    {
        return array_shift($this->values);
    }

    /**
     * @param Failure[] ...$values
     */
    public function push(...$values): void
    {
        array_push($this->values, ...$values);
    }

    /**
     * @param Failure[] ...$values
     */
    public function unshift(...$values): void
    {
        array_unshift($this->values, ...$values);
    }

    public function isEmpty(): bool
    {
        return count($this->values) <= 0;
    }

    public function count()
    {
        return count($this->values);
    }
}
