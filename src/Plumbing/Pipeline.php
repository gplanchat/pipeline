<?php

namespace Kiboko\Component\Pipeline\Plumbing;

use InvalidArgumentException;
use Kiboko\Component\Pipeline\ExecutionContext\ExecutionContextInterface;
use Kiboko\Component\Pipeline\ExecutionContext\PipelineExecutionInterface;
use Kiboko\Component\Pipeline\Processor\ProcessorInterface;

class Pipeline implements PipelineInterface
{
    /**
     * @var StepChainInterface[]
     */
    private $stepChain;

    /**
     * Constructor.
     *
     * @param StepChainInterface $stepChain
     *
     * @throws InvalidArgumentException
     */
    public function __construct(StepChainInterface $stepChain)
    {
        $this->stepChain = $stepChain;
    }

    public function __clone()
    {
        $this->stepChain = clone $this->stepChain;
    }

    /**
     * @inheritdoc
     */
    public function pipe(StepInterface ...$steps): PipelineInterface
    {
        $pipeline = clone $this;
        $pipeline->stepChain->pipe(...$steps);

        return $pipeline;
    }

    /**
     * @param ExecutionContextInterface $executionContext
     * @param ProcessorInterface $processor
     *
     * @return PipelineExecutionInterface
     */
    public function __invoke(
        ExecutionContextInterface $executionContext,
        ProcessorInterface $processor
    ): PipelineExecutionInterface {
        return $processor->process($executionContext, $this->stepChain);
    }
}
