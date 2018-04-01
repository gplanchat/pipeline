<?php

namespace Kiboko\Component\Groovy\AST\Node;

interface NodeCollectionInterface extends NodeInterface, \Iterator, \Countable
{
    public function append(NodeInterface $node);
}
