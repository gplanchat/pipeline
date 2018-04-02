<?php

namespace Kiboko\Component\Phroovy\AST\Node;

interface NodeCollectionInterface extends NodeInterface, \Iterator, \Countable
{
    public function append(NodeInterface $node);
}
