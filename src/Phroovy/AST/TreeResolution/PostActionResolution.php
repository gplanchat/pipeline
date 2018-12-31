<?php

namespace Kiboko\Component\Phroovy\AST\TreeResolution;

use Kiboko\Component\Phroovy\AST\Node;
use Kiboko\Component\Phroovy\AST\TokenConstraint;
use Kiboko\Component\Phroovy\AST\TokenStream;

class PostActionResolution implements TreeResolutionInterface
{
    /**
     * @var PostActionStepCollectionResolution
     */
    private $postActionStepCollectionResolution;

    /**
     * @param PostActionStepCollectionResolution $postActionStepCollectionResolution
     */
    public function __construct(PostActionStepCollectionResolution $postActionStepCollectionResolution)
    {
        $this->postActionStepCollectionResolution = $postActionStepCollectionResolution;
    }

    /**
     * @return TokenConstraint[]|iterable
     */
    public function constraints(): iterable
    {
        return [
            TokenConstraint::keyword('post'),
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
     * @return Node\PostActionNode
     */
    public function create(TokenStream $tokenStream): Node\NodeInterface
    {
        $tokenStream->expect(TokenConstraint::keyword('post'));
        $tokenStream->expect(TokenConstraint::openingCurlyBraces());

        $node = new Node\PostActionNode();
        while ($this->postActionStepCollectionResolution->assert($tokenStream)) {
            $section = $tokenStream->watch()->value;

            $node->$section = $this->postActionStepCollectionResolution->create($tokenStream);
        }

        // TODO: inspect what is happening, if it is expected to skip this expectation
        $tokenStream->expect(TokenConstraint::closingCurlyBraces());

        return $node;
    }
}
