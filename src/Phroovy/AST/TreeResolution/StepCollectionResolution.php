<?php

namespace Kiboko\Component\Phroovy\AST\TreeResolution;

use Kiboko\Component\Phroovy\AST\Node;
use Kiboko\Component\Phroovy\AST\TokenConstraint;
use Kiboko\Component\Phroovy\AST\TokenStream;

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

    public function assert(TokenStream $tokenStream): bool
    {
        return $tokenStream->assert(TokenConstraint::keyword('steps'));
    }

    /**
     * @param TokenStream $tokenStream
     *
     * @return Node\NodeInterface
     */
    public function create(TokenStream $tokenStream): Node\NodeInterface
    {
        $tokenStream->expect(TokenConstraint::keyword('steps'));
        $tokenStream->expect(TokenConstraint::openingCurlyBraces());

        $stepCollection = new Node\StepCollectionNode();

        while (!$tokenStream->assert(TokenConstraint::closingCurlyBraces())) {
            $stepCollection->append($this->stepResolution->create($tokenStream));
        }

        $tokenStream->expect(TokenConstraint::closingCurlyBraces());

        return $stepCollection;
    }
}
