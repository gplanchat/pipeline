<?php

namespace Kiboko\Component\Phroovy\AST\Node\StaticValue;

class HashMapNode implements CompositeStaticValueNodeInterface
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
