<?php

namespace Kiboko\Component\Phroovy\AST\TreeResolution;

use Kiboko\Component\Phroovy\AST\Exception\UnexpectedTokenException;
use Kiboko\Component\Phroovy\AST\Node;
use Kiboko\Component\Phroovy\AST\TokenConstraint;
use Kiboko\Component\Phroovy\AST\TokenStream;

class StaticValueResolutionFacade implements TreeResolutionInterface
{
    /**
     * @var TreeResolutionInterface[]
     */
    private $handlers;

    /**
     * @param TreeResolutionInterface $handler
     */
    public function attach(TreeResolutionInterface $handler)
    {
        $this->handlers[] = $handler;
    }

    public function constraints(): iterable
    {
        return array_merge(
            ...array_map(function(TreeResolutionInterface $item) {
                return $item->constraints();
            }, $this->handlers)
        );
    }

    public function assert(TokenStream $tokenStream): bool
    {
        return $tokenStream->assert(...$this->constraints());
    }

    public function create(TokenStream $tokenStream): Node\NodeInterface
    {
        foreach ($this->handlers as $handler) {
            if (!$handler->assert($tokenStream)) {
                continue;
            }

            return $handler->create($tokenStream);
        }

        throw UnexpectedTokenException::unmatchedConstraints(...$this->constraints());
    }
}
