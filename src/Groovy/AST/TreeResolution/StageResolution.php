<?php

namespace Kiboko\Component\Groovy\AST\TreeResolution;

use Kiboko\Component\Groovy\AST\Exception;
use Kiboko\Component\Groovy\AST\Node;
use Kiboko\Component\Groovy\AST\TokenConstraint;
use Kiboko\Component\Groovy\AST\TokenStream;

class StageResolution implements TreeResolutionInterface
{
    /**
     * @var StepCollectionResolution
     */
    private $stepCollectionResolution;

    /**
     * @var EnvironmentResolution
     */
    private $environmentResolution;

    /**
     * @var PostActionResolution
     */
    private $postActionResolution;

    /**
     * @param StepCollectionResolution $stepCollectionResolution
     * @param EnvironmentResolution $environmentResolution
     * @param PostActionResolution $postActionResolution
     */
    public function __construct(
        StepCollectionResolution $stepCollectionResolution,
        EnvironmentResolution $environmentResolution,
        PostActionResolution $postActionResolution
    ) {
        $this->stepCollectionResolution = $stepCollectionResolution;
        $this->environmentResolution = $environmentResolution;
        $this->postActionResolution = $postActionResolution;
    }

    public function assert(TokenStream $tokenStream): bool
    {
        return $tokenStream->assert(TokenConstraint::keyword('stage'));
    }

    /**
     * @param TokenStream $tokenStream
     *
     * @return Node\StageNode
     */
    public function create(TokenStream $tokenStream): Node\NodeInterface
    {
        $tokenStream->expect(TokenConstraint::keyword('stage'));

        $stage = new Node\StageNode();

        $tokenStream->expect(TokenConstraint::openingBracket());
        if ($tokenStream->assertAny(TokenConstraint::anyString())) {
            $stage->label = $tokenStream->consume()->value;
        } else {
            throw Exception\UnexpectedTokenException::expectedString($tokenStream->watch());
        }
        $tokenStream->expect(TokenConstraint::closingBracket());

        $tokenStream->expect(TokenConstraint::openingCurlyBraces());

        while (true) {
            if ($this->stepCollectionResolution->assert($tokenStream)) {
                $stage->steps = $this->stepCollectionResolution->create($tokenStream);
                continue;
            }

            if ($this->environmentResolution->assert($tokenStream)) {
                $stage->environment = $this->environmentResolution->create($tokenStream);
                continue;
            }

            if ($this->postActionResolution->assert($tokenStream)) {
                $stage->post = $this->postActionResolution->create($tokenStream);
                continue;
            }

            if ($tokenStream->assert(TokenConstraint::closingCurlyBraces())) {
                $tokenStream->step();
                break;
            }

            throw Exception\UnexpectedTokenException::unmatchedConstraints(
                $tokenStream->watch(),
                [
                    TokenConstraint::keyword('steps'),
                    TokenConstraint::keyword('environment'),
                    TokenConstraint::keyword('post'),
                ]
            );
        }

        return $stage;
    }
}
