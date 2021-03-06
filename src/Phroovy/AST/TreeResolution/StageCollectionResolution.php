<?php

namespace Kiboko\Component\Phroovy\AST\TreeResolution;

use Kiboko\Component\Phroovy\AST\Node;
use Kiboko\Component\Phroovy\AST\TokenConstraint;
use Kiboko\Component\Phroovy\AST\TokenStream;

class StageCollectionResolution implements TreeResolutionInterface
{
    /**
     * @var StageResolution
     */
    private $stageResolution;

    /**
     * @param StageResolution $stageResolution
     */
    public function __construct(StageResolution $stageResolution)
    {
        $this->stageResolution = $stageResolution;
    }

    /**
     * @return TokenConstraint[]|iterable
     */
    public function constraints(): iterable
    {
        return [
            TokenConstraint::keyword('stages'),
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
     * @return Node\StageCollectionNode
     */
    public function create(TokenStream $tokenStream): Node\NodeInterface
    {
        $tokenStream->expect(TokenConstraint::keyword('stages'));
        $tokenStream->expect(TokenConstraint::openingCurlyBraces());

        $stageCollection = new Node\StageCollectionNode();

        while (!$tokenStream->assert(TokenConstraint::closingCurlyBraces())) {
            $stageCollection->push($this->stageResolution->create($tokenStream));
        }

        //$tokenStream->expect(TokenConstraint::closingCurlyBraces());

        return $stageCollection;
    }
}
