<?php

namespace Kiboko\Component\Phroovy\AST;

use Kiboko\Component\Phroovy\AST\Node;
use Kiboko\Component\Phroovy\AST\TreeResolution;
use Kiboko\Component\Phroovy\Lexer\CommentFilterIterator;
use Kiboko\Component\Phroovy\Lexer\Token;

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
    private $optionsResolution;
    private $functionCallResolution;
    private $arrayResolution;
    private $listResolution;

    public function __construct()
    {
        $this->arrayResolution = new TreeResolution\StaticValueResolutionFacade();
        $this->arrayResolution->attach(new TreeResolution\HashMapResolution($this->arrayResolution));
        $this->arrayResolution->attach(new TreeResolution\CollectionResolution($this->arrayResolution));
        $this->listResolution = new TreeResolution\ListResolution();

        $this->stepResolution = new TreeResolution\StepResolution(
            $this->arrayResolution,
            $this->listResolution
        );

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

        $this->agentResolution = new TreeResolution\AgentResolution();

        $this->functionCallResolution = new TreeResolution\FunctionCallResolution();

        $this->optionsResolution = new TreeResolution\OptionsResolution(
            $this->functionCallResolution
        );

        $this->stageResolution = new TreeResolution\StageResolution(
            $this->stepCollectionResolution,
            $this->environmentResolution,
            $this->agentResolution,
            $this->postActionResolution,
            $this->optionsResolution
        );

        $this->stageCollectionResolution = new TreeResolution\StageCollectionResolution(
            $this->stageResolution
        );

        $this->pipelineResolution = new TreeResolution\PipelineResolution(
            $this->agentResolution,
            $this->stageCollectionResolution,
            $this->environmentResolution,
            $this->postActionResolution,
            $this->optionsResolution
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

        $tokenStream = new TokenStream(new CommentFilterIterator($tokens));
        if ($tokenStream->finished()) {
            return [];
        }

        return new \ArrayIterator([
            $this->pipelineResolution->create($tokenStream)
        ]);
    }
}
