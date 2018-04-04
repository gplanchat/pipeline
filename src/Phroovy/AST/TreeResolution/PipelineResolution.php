<?php

namespace Kiboko\Component\Phroovy\AST\TreeResolution;

use Kiboko\Component\Phroovy\AST\Exception\UnexpectedTokenException;
use Kiboko\Component\Phroovy\AST\Node;
use Kiboko\Component\Phroovy\AST\TokenConstraint;
use Kiboko\Component\Phroovy\AST\TokenStream;
use Kiboko\Component\Phroovy\Lexer\Token;

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
     * @var OptionsResolution
     */
    private $optionsResolution;

    /**
     * @param AgentResolution $agentResolution
     * @param StageCollectionResolution $stageCollectionResolution
     * @param EnvironmentResolution $environmentResolution
     * @param PostActionResolution $postActionResolution
     * @param OptionsResolution $optionsResolution
     */
    public function __construct(
        AgentResolution $agentResolution,
        StageCollectionResolution $stageCollectionResolution,
        EnvironmentResolution $environmentResolution,
        PostActionResolution $postActionResolution,
        OptionsResolution $optionsResolution
    ) {
        $this->agentResolution = $agentResolution;
        $this->stageCollectionResolution = $stageCollectionResolution;
        $this->environmentResolution = $environmentResolution;
        $this->postActionResolution = $postActionResolution;
        $this->optionsResolution = $optionsResolution;
    }

    /**
     * @return Token[]|iterable
     */
    public function constraints(): iterable
    {
        return [
            TokenConstraint::keyword('pipeline'),
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
     * @return Node\PipelineNode
     */
    public function create(TokenStream $tokenStream): Node\NodeInterface
    {
        $tokenStream->expect(TokenConstraint::keyword('pipeline'));
        $tokenStream->expect(TokenConstraint::openingCurlyBraces());

        $pipeline = new Node\PipelineNode();

        while (!$tokenStream->assert(TokenConstraint::closingCurlyBraces())) {
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

            if ($this->optionsResolution->assert($tokenStream)) {
                $pipeline->options = $this->optionsResolution->create($tokenStream);
                continue;
            }

            throw UnexpectedTokenException::unmatchedConstraints(
                $tokenStream->watch(),
                ...$this->agentResolution->constraints(),
                ...$this->stageCollectionResolution->constraints(),
                ...$this->environmentResolution->constraints(),
                ...$this->postActionResolution->constraints(),
                ...$this->optionsResolution->constraints()
            );
        }

        $tokenStream->expect(TokenConstraint::closingCurlyBraces());

        return $pipeline;
    }
}
