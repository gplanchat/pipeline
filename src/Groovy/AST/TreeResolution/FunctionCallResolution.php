<?php

namespace Kiboko\Component\Groovy\AST\TreeResolution;

use Kiboko\Component\Groovy\AST\Exception;
use Kiboko\Component\Groovy\AST\Node;
use Kiboko\Component\Groovy\AST\TokenConstraint;
use Kiboko\Component\Groovy\AST\TokenStream;

class FunctionCallResolution implements TreeResolutionInterface
{
    public function assert(TokenStream $tokenStream): bool
    {
        return $tokenStream->assert(TokenConstraint::identifier());
    }

    /**
     * @param TokenStream $tokenStream
     *
     * @return Node\FunctionCallNode
     */
    public function create(TokenStream $tokenStream): Node\NodeInterface
    {
        $functionName = $tokenStream->expect(TokenConstraint::identifier());
        $tokenStream->expect(TokenConstraint::openingBracket());

        $arguments = [];
        while (true) {
            if ($tokenStream->assertAny(TokenConstraint::anyString())) {
                $arguments[] = $tokenStream->consume()->value;
            } else if (false && $tokenStream->assert(TokenConstraint::identifier())) {
                $arguments[] = $tokenStream->consume()->value;

                $tokenStream->expect(TokenConstraint::operator(':'));
            } else {
                throw Exception\UnexpectedTokenException::expectedString($tokenStream->watch());
            }

            if ($tokenStream->assert(TokenConstraint::operator(','))) {
                $tokenStream->consume();
                continue;
            } else if ($tokenStream->assert(TokenConstraint::closingBracket())) {
                break;
            } else {
                throw Exception\UnexpectedTokenException::unmatchedConstraints(
                    $tokenStream->watch(),
                    [
                        TokenConstraint::operator(','),
                        TokenConstraint::closingBracket(),
                    ]
                );
            }
        }

        $tokenStream->expect(TokenConstraint::closingBracket());

        return new Node\FunctionCallNode($functionName->value, $arguments);
    }
}
