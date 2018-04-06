<?php

namespace Kiboko\Component\Phroovy\AST\Node\StaticValue;

use Kiboko\Component\Phroovy\AST\Node\NodeCollectionInterface;
use Kiboko\Component\Phroovy\AST\Node\NodeInterface;

class ListNode implements NodeCollectionInterface, CompositeStaticValueNodeInterface
{
    /**
     * @var StaticValueNodeInterface[]
     */
    private $value;

    /**
     * @param array $value
     */
    public function __construct(array $value = [])
    {
        $this->value = $value;
    }

    public function push(NodeInterface ...$node): void
    {
        array_push($this->value, ...$node);
    }

    public function pop(): NodeInterface
    {
        return array_pop($this->value);
    }

    public function shift(): NodeInterface
    {
        return array_shift($this->value);
    }

    public function unshift(NodeInterface ...$node): void
    {
        array_unshift($this->value, ...$node);
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->value);
    }

    public function count()
    {
        return count($this->value);
    }

    public function offsetExists($offset)
    {
        return isset($this->value[$offset]);
    }

    public function offsetGet($offset)
    {
        if (!isset($this->value[$offset])) {
            throw new \OutOfBoundsException();
        }

        return $this->value[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->value[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        if (!isset($this->value[$offset])) {
            throw new \OutOfBoundsException();
        }

        unset($this->value[$offset]);
    }

    public function toPHPValue()
    {
        return iterator_to_array($this->flatten());
    }

    private function flatten()
    {
        foreach ($this->value as $key => $value) {
            yield $key => $value->toPHPValue();
        }
    }
}
