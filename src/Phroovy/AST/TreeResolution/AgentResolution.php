<?php

namespace Kiboko\Component\Phroovy\AST\TreeResolution;

use Kiboko\Component\Phroovy\AST\Exception;
use Kiboko\Component\Phroovy\AST\Node;
use Kiboko\Component\Phroovy\AST\TokenConstraint;
use Kiboko\Component\Phroovy\AST\TokenStream;

/**
 * Required: yes
 * Parameters: any, none, label, node, docker, dockerfile
 * Allowed: in the top-level pipeline block and each stage block.
 *
 * @see https://jenkins.io/doc/book/pipeline/syntax/#agent
 */
class AgentResolution implements TreeResolutionInterface
{
    /**
     * @return TokenConstraint[]|iterable
     */
    public function constraints(): iterable
    {
        return [
            TokenConstraint::keyword('agent'),
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
     * @return Node\Agent\AgentNodeInterface
     */
    public function create(TokenStream $tokenStream): Node\NodeInterface
    {
        $tokenStream->expect(TokenConstraint::keyword('agent'));

        if ($tokenStream->assert(TokenConstraint::keyword('any'))) {
            $tokenStream->consume();
            return new Node\Agent\AnyAgentNode();
        }

        if ($tokenStream->assert(TokenConstraint::keyword('none'))) {
            $tokenStream->consume();
            return new Node\Agent\NoneAgentNode();
        }

        if ($tokenStream->assert(...TokenConstraint::anyStringOrIdentifier())) {
            return new Node\Agent\AgentNode($tokenStream->consume()->value);
        }

        if (!$tokenStream->assert(TokenConstraint::openingCurlyBraces())) {
            throw Exception\UnexpectedTokenException::unmatchedConstraints(
                $tokenStream->watch(),
                TokenConstraint::openingCurlyBraces(),
                ...TokenConstraint::anyStringOrIdentifier()
            );
        }
        $tokenStream->consume();

        $agent = $tokenStream->expect(TokenConstraint::identifier())->value;

        if ($tokenStream->assert(...TokenConstraint::anyString())) {
            $arguments = [
                $tokenStream->consume()->value
            ];
        } else if ($tokenStream->assert(TokenConstraint::openingCurlyBraces())){
            $tokenStream->consume();

            $arguments = [];
            while (!$tokenStream->assert(TokenConstraint::closingCurlyBraces())) {
                $key = $tokenStream->expect(TokenConstraint::identifier())->value;
                $value = $tokenStream->expect(...TokenConstraint::anyString())->value;

                $arguments[$key] = $value;
            }

            $tokenStream->consume();
        } else {
            throw Exception\UnexpectedTokenException::unmatchedConstraints(
                $tokenStream->watch(),
                TokenConstraint::openingCurlyBraces(),
                ...TokenConstraint::anyString()
            );
        }

        $tokenStream->expect(TokenConstraint::closingCurlyBraces());

        return new Node\Agent\AgentNode($agent, $arguments);
    }
}
