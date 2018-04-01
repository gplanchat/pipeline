<?php

namespace Kiboko\Component\Groovy\AST\TreeResolution;

use Kiboko\Component\Groovy\AST\Node;
use Kiboko\Component\Groovy\AST\TokenConstraint;
use Kiboko\Component\Groovy\AST\TokenStream;

class PipelineResolution implements TreeResolutionInterface
{
    /**
     * @var AgentResolution
     */
    private $agentResolution;

    /**
     * @var StageCollectionResolution
     */
    private $stageCollectionResolution;

    /**
     * @var EnvironmentResolution
     */
    private $environmentResolution;

    /**
     * @var PostActionResolution
     */
    private $postActionResolution;

    /**
     * @param AgentResolution $agentResolution
     * @param StageCollectionResolution $stageCollectionResolution
     * @param EnvironmentResolution $environmentResolution
     * @param PostActionResolution $postActionResolution
     */
    public function __construct(
        AgentResolution $agentResolution,
        StageCollectionResolution $stageCollectionResolution,
        EnvironmentResolution $environmentResolution,
        PostActionResolution $postActionResolution
    ) {
        $this->agentResolution = $agentResolution;
        $this->stageCollectionResolution = $stageCollectionResolution;
        $this->environmentResolution = $environmentResolution;
        $this->postActionResolution = $postActionResolution;
    }

    public function assert(TokenStream $tokenStream): bool
    {
        return $tokenStream->assert(TokenConstraint::keyword('pipeline'));
    }

    /**
     * @param TokenStream $tokenStream
     *
     * @return Node\PipelineNode
     */
    public function create(TokenStream $tokenStream): Node\NodeInterface
    {
        $tokenStream->expect(TokenConstraint::keyword('pipeline'));
        $tokenStream->expect(TokenConstraint::openingCurlyBraces());

        $pipeline = new Node\PipelineNode();

        while (true) {
            if ($this->agentResolution->assert($tokenStream)) {
                $pipeline->agent = $this->agentResolution->create($tokenStream);
                continue;
            }

            if ($this->stageCollectionResolution->assert($tokenStream)) {
                $pipeline->stages = $this->stageCollectionResolution->create($tokenStream);
                continue;
            }

            if ($this->environmentResolution->assert($tokenStream)) {
                $pipeline->environment = $this->environmentResolution->create($tokenStream);
                continue;
            }

            if ($this->postActionResolution->assert($tokenStream)) {
                $pipeline->post = $this->postActionResolution->create($tokenStream);
                continue;
            }

            $tokenStream->expect(TokenConstraint::closingCurlyBraces());
            break;
        }

        return $pipeline;
    }
}
