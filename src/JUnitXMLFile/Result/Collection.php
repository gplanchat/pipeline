<?php

namespace Kiboko\Component\JUnitXMLFile\Result;

interface Collection extends \IteratorAggregate, \Countable
{
    /**
     * Pops a node from the end of the collection
     *
     * @return object
     */
    public function pop();

    /**
     * Shifts a node from the beginning of the collection
     *
     * @return object
     */
    public function shift();

    /**
     * Pushes an element at the end of the collection
     *
     * @param object[] ...$values
     */
    public function push(...$values): void;

    /**
     * Prepends the collection with an element
     *
     * @param object[] ...$values
     */
    public function unshift(...$values): void;

    /**
     * Checks whether the collection is empty.
     *
     * @return bool
     */
    public function isEmpty(): bool;
}
