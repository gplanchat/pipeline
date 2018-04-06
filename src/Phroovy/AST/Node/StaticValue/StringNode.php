<?php

namespace Kiboko\Component\Phroovy\AST\Node\StaticValue;

use Kiboko\Component\Phroovy\AST\Node\NodeInterface;

class StringNode implements NodeInterface, StaticValueNodeInterface
{
    /**
     * @var string
     */
    private $value;

    /**
     * @param $value
     */
    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function toPHPValue()
    {
        return $this->value;
    }
}
