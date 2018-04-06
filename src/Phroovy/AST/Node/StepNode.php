<?php

namespace Kiboko\Component\Phroovy\AST\Node;

use Kiboko\Component\Phroovy\AST\Node\StaticValue\CompositeStaticValueNodeInterface;

class StepNode implements NodeInterface
{
    /**
     * @var string
     */
    public $type;

    /**
     * @var CompositeStaticValueNodeInterface
     */
    public $arguments;

    /**
     * @param string $type
     * @param CompositeStaticValueNodeInterface|null $arguments
     */
    public function __construct(string $type, CompositeStaticValueNodeInterface $arguments = null)
    {
        $this->type = $type;
        $this->arguments = $arguments;
    }
}
