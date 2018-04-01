<?php

namespace Kiboko\Component\Groovy\AST\Node;

class StepNode implements NodeInterface
{
    /**
     * @var string
     */
    public $type;

    /**
     * @var string[]
     */
    public $arguments;

    /**
     * @param string $type
     * @param string[] $arguments
     */
    public function __construct(string $type, array $arguments = [])
    {
        $this->type = $type;
        $this->arguments = $arguments;
    }
}
