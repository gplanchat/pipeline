<?php

namespace Kiboko\Component\Groovy\AST;

use Kiboko\Component\Groovy\AST\Node;
use Kiboko\Component\Groovy\AST\TreeResolution;
use Kiboko\Component\Groovy\Lexer\Token;

class Tree
{
    private $stepResolution;
    private $stepCollectionResolution;
    private $environmentResolution;
    private $stageResolution;
    private $stageCollectionResolution;
    private $postActionResolution;
    private $agentResolution;
    private $pipelineResolution;

    public function __construct()
    {
        $this->stepResolution = new TreeResolution\StepResolution();

        $this->stepCollectionResolution = new TreeResolution\StepCollectionResolution(
            $this->stepResolution
        );

        $this->environmentResolution = new TreeResolution\EnvironmentResolution(
            new TreeResolution\FunctionCallResolution()
        );

        $this->postActionResolution = new TreeResolution\PostActionResolution(
            new TreeResolution\PostActionStepCollectionResolution(
                $this->stepResolution
            )
        );

        $this->stageResolution = new TreeResolution\StageResolution(
            $this->stepCollectionResolution,
            $this->environmentResolution,
            $this->postActionResolution
        );

        $this->stageCollectionResolution = new TreeResolution\StageCollectionResolution(
            $this->stageResolution
        );

        $this->agentResolution = new TreeResolution\AgentResolution();

        $this->pipelineResolution = new TreeResolution\PipelineResolution(
            $this->agentResolution,
            $this->stageCollectionResolution,
            $this->environmentResolution,
            $this->postActionResolution
        );
    }

    /**
     * @param Token[]|iterable $tokens
     *
     * @return Node\NodeInterface[]|iterable
     */
    public function compile(iterable $tokens): iterable
    {
        if (is_array($tokens)) {
            $tokens = new \ArrayIterator($tokens);
        }

        if ($tokens instanceof \IteratorAggregate) {
            $tokens = $tokens->getIterator();
        }

        $tokenStream = new TokenStream($tokens);
        if ($tokenStream->finished()) {
            return [];
        }

        return new \ArrayIterator([
            $this->pipelineResolution->create($tokenStream)
        ]);
    }
}
