<?php

namespace Kiboko\Component\JUnitXMLFile\Result;

class ErrorCollection implements Collection
{
    /**
     * @var Error[]
     */
    private $values;

    /**
     * @param Error[] ...$errors
     */
    public function __construct(Error ...$errors)
    {
        $this->values = $errors;
    }

    /**
     * @return \Traversable|Error[]
     */
    public function getIterator()
    {
        yield from $this->values;
    }

    /**
     * @return Error
     */
    public function pop(): Error
    {
        return array_pop($this->values);
    }

    /**
     * @return Error
     */
    public function shift(): Error
    {
        return array_shift($this->values);
    }

    /**
     * @param Error[] ...$values
     */
    public function push(...$values): void
    {
        array_push($this->values, ...$values);
    }

    /**
     * @param Error[] ...$values
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
