<?php

namespace Kiboko\Component\Groovy\AST\TreeResolution;

use Kiboko\Component\Groovy\AST\Node;
use Kiboko\Component\Groovy\AST\TokenConstraint;
use Kiboko\Component\Groovy\AST\TokenStream;

class PostActionResolution implements TreeResolutionInterface
{
    /**
     * @var PostActionStepCollectionResolution
     */
    private $postActionStageResolution;

    /**
     * @param PostActionStepCollectionResolution $postActionStageResolution
     */
    public function __construct(PostActionStepCollectionResolution $postActionStageResolution)
    {
        $this->postActionStageResolution = $postActionStageResolution;
    }

    public function assert(TokenStream $tokenStream): bool
    {
        return $tokenStream->assert(TokenConstraint::keyword('post'));
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
        while ($this->postActionStageResolution->assert($tokenStream)) {
            $section = $tokenStream->watch()->value;

            $node->$section = $this->postActionStageResolution->create($tokenStream);
        }

        $tokenStream->expect(TokenConstraint::closingCurlyBraces());

        return $node;
    }
}
