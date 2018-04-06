<?php

namespace Kiboko\Component\Phroovy\AST\TreeResolution;

use Kiboko\Component\Phroovy\AST\Node;
use Kiboko\Component\Phroovy\AST\TokenConstraint;
use Kiboko\Component\Phroovy\AST\TokenStream;
use Kiboko\Component\Phroovy\Lexer\Token;

class StepCollectionResolution implements TreeResolutionInterface
{
    /**
     * @var StepResolution
     */
    private $stepResolution;

    /**
     * @param StepResolution $stepResolution
     */
    public function __construct(StepResolution $stepResolution)
    {
        $this->stepResolution = $stepResolution;
    }

    /**
     * @return Token[]|iterable
     */
    public function constraints(): iterable
    {
        return [
            TokenConstraint::keyword('steps'),
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
     * @return Node\StepCollectionNode
     */
    public function create(TokenStream $tokenStream): Node\NodeInterface
    {
        $tokenStream->expect(TokenConstraint::keyword('steps'));
        $tokenStream->expect(TokenConstraint::openingCurlyBraces());

        $stepCollection = new Node\StepCollectionNode();

        while (!$tokenStream->assert(TokenConstraint::closingCurlyBraces())) {
            $stepCollection->push($this->stepResolution->create($tokenStream));
        }

        $tokenStream->expect(TokenConstraint::closingCurlyBraces());

        return $stepCollection;
    }
}
