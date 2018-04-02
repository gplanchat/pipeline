<?php

namespace Kiboko\Component\Phroovy\AST\TreeResolution;

use Kiboko\Component\Phroovy\AST\Node;
use Kiboko\Component\Phroovy\AST\TokenStream;

interface TreeResolutionInterface
{
    /**
     * @param TokenStream $tokenStream
     *
     * @return bool
     */
    public function assert(TokenStream $tokenStream): bool;

    /**
     * @param TokenStream $tokenStream
     *
     * @return Node\NodeInterface
     */
    public function create(TokenStream $tokenStream): Node\NodeInterface;
}
