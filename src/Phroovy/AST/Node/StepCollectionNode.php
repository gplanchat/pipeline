<?php

namespace Kiboko\Component\Phroovy\AST\Node;

class StepCollectionNode implements NodeCollectionInterface
{
    private $internalIterator;

    /**
     * @param iterable|NodeInterface[] $nodes
     */
    public function __construct(iterable $nodes = [])
    {
        $this->internalIterator = new \ArrayIterator($nodes);
    }

    public function current()
    {
        return $this->internalIterator->current();
    }

    public function next()
    {
        $this->internalIterator->next();
    }

    public function key()
    {
        return $this->internalIterator->key();
    }

    public function valid()
    {
        return $this->internalIterator->valid();
    }

    public function rewind()
    {
        $this->internalIterator->rewind();
    }

    public function count()
    {
        return $this->internalIterator->count();
    }

    public function append(NodeInterface $node)
    {
        if (!$node instanceof StepNode) {
            throw new \InvalidArgumentException();
        }

        $this->internalIterator->append($node);
    }
}
