<?php

namespace Kiboko\Component\Phroovy\AST\TreeResolution;

use Kiboko\Component\Phroovy\AST\Exception;
use Kiboko\Component\Phroovy\AST\Node;
use Kiboko\Component\Phroovy\AST\TokenConstraint;
use Kiboko\Component\Phroovy\AST\TokenStream;

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

        if ($tokenStream->assert(TokenConstraint::keyword('any'))) {
            $tokenStream->consume();
            return new Node\AnyAgentNode();
        }

        if ($tokenStream->assert(TokenConstraint::keyword('none'))) {
            $tokenStream->consume();
            return new Node\NoneAgentNode();
        }

        if ($tokenStream->assertAny(TokenConstraint::anyStringOrIdentifier())) {
            return new Node\AgentNode($tokenStream->consume()->value);
        }

        if (!$tokenStream->assert(TokenConstraint::openingCurlyBraces())) {
            throw Exception\UnexpectedTokenException::unmatchedConstraints(
                $tokenStream->watch(),
                array_merge(
                    [
                        TokenConstraint::openingCurlyBraces(),
                    ],
                    TokenConstraint::anyStringOrIdentifier()
                )
            );
        }
        $tokenStream->consume();

        $agent = $tokenStream->expect(TokenConstraint::identifier())->value;

        if ($tokenStream->assertAny(TokenConstraint::anyString())) {
            $arguments = [
                $tokenStream->consume()->value
            ];
        } else if ($tokenStream->assert(TokenConstraint::openingCurlyBraces())){
            $tokenStream->consume();

            $arguments = [];
            while (!$tokenStream->assert(TokenConstraint::closingCurlyBraces())) {
                $key = $tokenStream->expect(TokenConstraint::identifier())->value;
                $value = $tokenStream->expectAny(TokenConstraint::anyString())->value;

                $arguments[$key] = $value;
            }

            $tokenStream->consume();
        } else {
            throw Exception\UnexpectedTokenException::unmatchedConstraints(
                $tokenStream->watch(),
                array_merge(
                    [
                        TokenConstraint::openingCurlyBraces(),
                    ],
                    TokenConstraint::anyString()
                )
            );
        }

        $tokenStream->expect(TokenConstraint::closingCurlyBraces());

        return new Node\AgentNode($agent, $arguments);
    }
}
