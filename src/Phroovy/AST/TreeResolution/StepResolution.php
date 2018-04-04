<?php

namespace Kiboko\Component\Phroovy\AST\TreeResolution;

use Kiboko\Component\Phroovy\AST\Node;
use Kiboko\Component\Phroovy\AST\TokenConstraint;
use Kiboko\Component\Phroovy\AST\TokenStream;
use Kiboko\Component\Phroovy\Lexer\Token;

class StepResolution implements TreeResolutionInterface
{
    /**
     * @return Token[]|iterable
     */
    public function constraints(): iterable
    {
        return TokenConstraint::anyString();
    }

    /**
     * @param TokenStream $tokenStream
     *
     * @return bool
     */
    public function assert(TokenStream $tokenStream): bool
    {
        return $tokenStream->assert(...TokenConstraint::anyString());
    }

    /**
     * @param TokenStream $tokenStream
     *
     * @return Node\StepNode
     */
    public function create(TokenStream $tokenStream): Node\NodeInterface
    {
        $step = new Node\StepNode($tokenStream->expect(TokenConstraint::identifier())->value);

        if ($tokenStream->assert(...TokenConstraint::anyString())) {
            $step->arguments[] = $tokenStream->consume()->value;
        }

        return $step;
    }
}
