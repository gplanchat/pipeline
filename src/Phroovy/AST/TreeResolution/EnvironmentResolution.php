<?php

namespace Kiboko\Component\Phroovy\AST\TreeResolution;

use Kiboko\Component\Phroovy\AST\Exception;
use Kiboko\Component\Phroovy\AST\Node;
use Kiboko\Component\Phroovy\AST\TokenConstraint;
use Kiboko\Component\Phroovy\AST\TokenStream;

/**
 * Required: yes
 * Parameters: none
 * Allowed: inside the pipeline block, or within stage directives.
 *
 * @see https://jenkins.io/doc/book/pipeline/syntax/#environment
 */
class EnvironmentResolution implements TreeResolutionInterface
{
    /**
     * @var FunctionCallResolution
     */
    private $functionCallResolution;

    /**
     * @param FunctionCallResolution $functionCallResolution
     */
    public function __construct(FunctionCallResolution $functionCallResolution)
    {
        $this->functionCallResolution = $functionCallResolution;
    }

    /**
     * @return TokenConstraint[]|iterable
     */
    public function constraints(): iterable
    {
        return [
            TokenConstraint::keyword('environment'),
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
     * @return Node\NodeInterface
     */
    public function create(TokenStream $tokenStream): Node\NodeInterface
    {
        $tokenStream->expect(TokenConstraint::keyword('environment'));
        $tokenStream->expect(TokenConstraint::openingCurlyBraces());

        $environment = new Node\EnvironmentNode();

        while (!$tokenStream->assert(TokenConstraint::closingCurlyBraces())) {
            $variable = $tokenStream->expect(TokenConstraint::identifier());
            $tokenStream->expect(TokenConstraint::operator('='));
            if ($tokenStream->assert(...TokenConstraint::anyString())) {
                $childNode = $tokenStream->consume()->value;
            } else if ($this->functionCallResolution->assert($tokenStream)) {
                $childNode = $this->functionCallResolution->create($tokenStream);
            } else {
                throw Exception\UnexpectedTokenException::expectedStringOrIdentifier($tokenStream->watch());
            }

            $environment->set($variable->value, $childNode);
        }

        $tokenStream->step();

        return $environment;
    }
}
