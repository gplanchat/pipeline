<?php

namespace Kiboko\Component\Phroovy\AST\Node\StaticValue;

use Kiboko\Component\Phroovy\AST\Node\NodeInterface;

interface StaticValueNodeInterface extends NodeInterface
{
    public function toPHPValue();
}
