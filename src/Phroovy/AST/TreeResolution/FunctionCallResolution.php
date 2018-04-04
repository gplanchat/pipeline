<?php

namespace Kiboko\Component\Phroovy\AST\TreeResolution;

use Kiboko\Component\Phroovy\AST\Exception;
use Kiboko\Component\Phroovy\AST\Node;
use Kiboko\Component\Phroovy\AST\TokenConstraint;
use Kiboko\Component\Phroovy\AST\TokenStream;
use Kiboko\Component\Phroovy\Lexer\Token;

class FunctionCallResolution implements TreeResolutionInterface
{
    /**
     * @return Token[]|iterable
     */
    public function constraints(): iterable
    {
        return [
            TokenConstraint::identifier(),
        ];
    }

    /**
     * @param TokenStream $tokenStream
     *
     * @return bool
     */
    public function assert(TokenStream $tokenStream): bool
    {
        return $tokenStream->assert(...$this->constraints());
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
        while (!$tokenStream->assert(TokenConstraint::closingBracket())) {
            if ($tokenStream->assert(...TokenConstraint::anyString())) {
                $arguments[] = $tokenStream->consume()->value;
            } else if ($tokenStream->assert(TokenConstraint::identifier())) {
                $key = $tokenStream->consume()->value;

                $tokenStream->expect(TokenConstraint::operator(':'));

                if ($tokenStream->assert(TokenConstraint::integer())) {
                    $arguments[$key] = (int) $tokenStream->consume()->value;
                } else if ($tokenStream->assert(TokenConstraint::float())) {
                    $arguments[$key] = (float) $tokenStream->consume()->value;
                } else if ($tokenStream->assert(...TokenConstraint::anyString())) {
                    $arguments[$key] = $tokenStream->consume()->value;
                } else {
                    throw Exception\UnexpectedTokenException::unmatchedConstraints(
                        $tokenStream->watch(),
                        ...TokenConstraint::anyNumber(),
                        ...TokenConstraint::anyString()
                    );
                }
            } else {
                throw Exception\UnexpectedTokenException::expectedString($tokenStream->watch());
            }

            if ($tokenStream->assert(TokenConstraint::operator(','))) {
                $tokenStream->consume();
                continue;
            }
        }

        $tokenStream->expect(TokenConstraint::closingBracket());

        return new Node\FunctionCallNode($functionName->value, $arguments);
    }
}
