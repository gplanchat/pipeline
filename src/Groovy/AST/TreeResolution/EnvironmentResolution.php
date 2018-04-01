<?php

namespace Kiboko\Component\Groovy\AST\TreeResolution;

use Kiboko\Component\Groovy\AST\Exception;
use Kiboko\Component\Groovy\AST\Node;
use Kiboko\Component\Groovy\AST\TokenConstraint;
use Kiboko\Component\Groovy\AST\TokenStream;

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

    public function assert(TokenStream $tokenStream): bool
    {
        return $tokenStream->assert(TokenConstraint::keyword('environment'));
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

        while (true) {
            $variable = $tokenStream->expect(TokenConstraint::identifier());
            $tokenStream->expect(TokenConstraint::operator('='));
            if ($tokenStream->assertAny(TokenConstraint::anyString())) {
                $childNode = $tokenStream->consume()->value;
            } else if ($this->functionCallResolution->assert($tokenStream)) {
                $childNode = $this->functionCallResolution->create($tokenStream);
            } else {
                throw Exception\UnexpectedTokenException::expectedStringOrIdentifier($tokenStream->watch());
            }

            $environment->set($variable->value, $childNode);

            if ($tokenStream->assert(TokenConstraint::closingCurlyBraces())) {
                $tokenStream->step();
                break;
            }
        }

        return $environment;
    }
}
