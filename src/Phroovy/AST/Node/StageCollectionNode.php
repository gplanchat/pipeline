<?php

namespace Kiboko\Component\Phroovy\AST\Node;

class StageCollectionNode implements NodeCollectionInterface
{
    /**
     * @var iterable|NodeInterface[]
     */
    private $nodes;

    /**
     * @param iterable|NodeInterface[] $nodes
     */
    public function __construct(iterable $nodes = [])
    {
        $this->nodes = $nodes;
    }

    public function getIterator()
    {
        if (is_array($this->nodes)) {
            return new \ArrayIterator($this->nodes);
        }

        return clone $this->nodes;
    }

    public function count()
    {
        if (is_array($this->nodes)) {
            return count($this->nodes);
        }
        if ($this->nodes instanceof \Countable) {
            return $this->nodes->count();
        }

        throw new \RuntimeException('Node list could not be counted.');
    }

    public function push(NodeInterface ...$nodes): void
    {
        foreach ($nodes as $node) {
            if (!$node instanceof StageNode) {
                throw new \InvalidArgumentException();
            }
        }

        if (is_array($this->nodes) ||
            $this->nodes instanceof \ArrayAccess
        ) {
            array_push($this->nodes, ...$nodes);
            return;
        }

        throw new \RuntimeException('Node list could not be pushed.');
    }

    public function pop(): NodeInterface
    {
        if (is_array($this->nodes) ||
            $this->nodes instanceof \ArrayAccess
        ) {
            return array_pop($this->nodes);
        }

        throw new \RuntimeException('Node list could not be popped.');
    }

    public function shift(): NodeInterface
    {
        if (is_array($this->nodes) ||
            $this->nodes instanceof \ArrayAccess
        ) {
            return array_shift($this->nodes);
        }

        throw new \RuntimeException('Node list could not be shifted.');
    }

    public function unshift(NodeInterface ...$nodes): void
    {
        foreach ($nodes as $node) {
            if (!$node instanceof StageNode) {
                throw new \InvalidArgumentException();
            }
        }

        if (is_array($this->nodes) ||
            $this->nodes instanceof \ArrayAccess
        ) {
            array_unshift($this->nodes, ...$nodes);
            return;
        }

        throw new \RuntimeException('Node list could not be unshifted.');
    }
}
