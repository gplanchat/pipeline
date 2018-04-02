<?php

namespace Kiboko\Component\Phroovy\AST\Node;

class OptionsNode implements NodeInterface
{
    /**
     * @var FunctionCallNode[]
     */
    public $options;

    /**
     * @param FunctionCallNode[] $options
     */
    public function __construct(array $options = [])
    {
        $this->options = $options;
    }
}
