<?php

namespace Kiboko\Component\Phroovy\AST\Node;

class FunctionCallNode implements NodeInterface
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string[]
     */
    public $arguments;

    /**
     * @param string $name
     * @param string[] $arguments
     */
    public function __construct(string $name, array $arguments = [])
    {
        $this->name = $name;
        $this->arguments = $arguments;
    }
}
