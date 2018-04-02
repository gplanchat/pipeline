<?php

namespace Kiboko\Component\Phroovy\AST\Node;

class NoneAgentNode extends AgentNode
{
    public function __construct()
    {
        parent::__construct(null);
    }
}
