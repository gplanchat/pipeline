<?php

namespace Kiboko\Component\Phroovy\AST\TreeResolution;

use Kiboko\Component\Phroovy\AST\Exception\UnexpectedTokenException;
use Kiboko\Component\Phroovy\AST\Node;
use Kiboko\Component\Phroovy\AST\TokenConstraint;
use Kiboko\Component\Phroovy\AST\TokenStream;
use Kiboko\Component\Phroovy\Lexer\Token;

class StepResolution implements TreeResolutionInterface
{
    /**
     * @var StaticValueResolutionFacade
     */
    private $arrayResolution;

    /**
     * @param StaticValueResolutionFacade $arrayResolution
     */
    public function __construct(StaticValueResolutionFacade $arrayResolution)
    {
        $this->arrayResolution = $arrayResolution;
    }

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

        if ($this->arrayResolution->assert($tokenStream)) {
            $step->arguments = $this->arrayResolution->create($tokenStream);
        } else {
            UnexpectedTokenException::unmatchedConstraints(
                $tokenStream->watch(),
                ...$this->arrayResolution->constraints()
            );
        }

        return $step;
    }
}
