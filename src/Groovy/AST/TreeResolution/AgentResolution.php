<?php

namespace Kiboko\Component\Groovy\AST\TreeResolution;

use Kiboko\Component\Groovy\AST\Exception;
use Kiboko\Component\Groovy\AST\Node;
use Kiboko\Component\Groovy\AST\TokenConstraint;
use Kiboko\Component\Groovy\AST\TokenStream;

class AgentResolution implements TreeResolutionInterface
{
    public function assert(TokenStream $tokenStream): bool
    {
        return $tokenStream->assert(TokenConstraint::keyword('agent'));
    }

    /**
     * @param TokenStream $tokenStream
     *
     * @return Node\AgentNode
     */
    public function create(TokenStream $tokenStream): Node\NodeInterface
    {
        $tokenStream->expect(TokenConstraint::keyword('agent'));

        if ($tokenStream->assertAny(TokenConstraint::anyStringOrIdentifier())) {
            return new Node\AgentNode($tokenStream->consume()->value);
        }

        throw Exception\UnexpectedTokenException::unmatchedConstraints(
            $tokenStream->watch(), TokenConstraint::anyStringOrIdentifier()
        );
    }
}
