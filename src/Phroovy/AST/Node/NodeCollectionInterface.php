<?php

namespace Kiboko\Component\Phroovy\AST\Node;

interface NodeCollectionInterface extends NodeInterface, \IteratorAggregate, \Countable
{
    /**
     * Push one or more elements onto the end of a collection
     *
     * @param NodeInterface[] ...$node
     */
    public function push(NodeInterface ...$node): void;

    /**
     * Pop the element off the end of a collection
     *
     * @return NodeInterface
     */
    public function pop(): NodeInterface;

    /**
     * Shift an element off the beginning of a collection
     *
     * @return NodeInterface $node
     */
    public function shift(): NodeInterface;

    /**
     * Prepend one or more elements to the beginning of an a collection
     *
     * @param NodeInterface[] $node
     * @return mixed
     */
    public function unshift(NodeInterface ...$node): void;
}
