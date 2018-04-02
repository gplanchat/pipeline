<?php

namespace Kiboko\Component\Phroovy\AST\Node\Agent;

class AgentNode implements AgentNodeInterface
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var array
     */
    public $arguments;

    /**
     * @param string $name
     * @param array $arguments
     */
    public function __construct(?string $name, array $arguments = [])
    {
        $this->name = $name;
        $this->arguments = $arguments;
    }
}
