<?php

namespace Kiboko\Component\Phroovy\AST\TreeResolution;

use Kiboko\Component\Phroovy\AST\Node;
use Kiboko\Component\Phroovy\AST\TokenConstraint;
use Kiboko\Component\Phroovy\AST\TokenStream;

class ListResolution implements TreeResolutionInterface
{
    /**
     * @return TokenConstraint[]|iterable
     */
    public function constraints(): iterable
    {
        return TokenConstraint::anyStringOrIdentifier();
    }

    public function assert(TokenStream $tokenStream): bool
    {
        return $tokenStream->assert(...$this->constraints());
    }

    /**
     * @param TokenStream $tokenStream
     * @return Node\StaticValue\HashMapNode
     */
    public function create(TokenStream $tokenStream): Node\NodeInterface
    {
        $listNode = new Node\StaticValue\ListNode();
        $tokenStream->keepNewlines();
        while ($tokenStream->assert(...TokenConstraint::anyStringOrIdentifier())) {
            $listNode->push(new Node\StaticValue\StringNode($tokenStream->consume()->value));
        }

        $tokenStream->expect(TokenConstraint::newLine());
        $tokenStream->skipNewlines();

        return $listNode;
    }
}
