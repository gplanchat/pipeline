<?php

namespace Kiboko\Component\Phroovy\AST\TreeResolution;

use Kiboko\Component\Phroovy\AST\Exception\UnexpectedTokenException;
use Kiboko\Component\Phroovy\AST\Node;
use Kiboko\Component\Phroovy\AST\TokenConstraint;
use Kiboko\Component\Phroovy\AST\TokenStream;

class CollectionResolution implements TreeResolutionInterface
{
    /**
     * @var StaticValueResolutionFacade
     */
    private $staticValueFacade;

    /**
     * @param StaticValueResolutionFacade $staticValueFacade
     */
    public function __construct(StaticValueResolutionFacade $staticValueFacade)
    {
        $this->staticValueFacade = $staticValueFacade;
    }

    /**
     * @return TokenConstraint[]|iterable
     */
    public function constraints(): iterable
    {
        return [
            TokenConstraint::openingSquareBracket()
        ];
    }

    public function assert(TokenStream $tokenStream): bool
    {
        return $tokenStream->assert(...$this->constraints());
    }

    /**
     * @param TokenStream $tokenStream
     * @return Node\StaticValue\HashMapNode
     */
    public function create(TokenStream $tokenStream): Node\NodeInterface
    {
        $tokenStream->expect(TokenConstraint::openingSquareBracket());

        $listNode = new Node\StaticValue\ListNode();
        while (true) {
            if ($this->staticValueFacade->assert($tokenStream)) {
                $listNode->push($this->staticValueFacade->create($tokenStream));
            } else if ($tokenStream->assert(...TokenConstraint::anyStringOrIdentifier())) {
                $listNode->push(new Node\StaticValue\StringNode($tokenStream->consume()->value));
            } else {
                throw UnexpectedTokenException::unmatchedConstraints(
                    $tokenStream->watch(),
                    ...$this->constraints(),
                    ...TokenConstraint::anyString()
                );
            }

            if ($tokenStream->assert(TokenConstraint::closingSquareBracket())) {
                break;
            }

            $tokenStream->expect(TokenConstraint::operator(','));
        }

        $tokenStream->expect(TokenConstraint::closingSquareBracket());

        return $listNode;
    }
}
