<?php

namespace Kiboko\Component\Pipeline\Plumbing;

use Kiboko\Component\Pipeline\ExecutionContext\ExecutionContextInterface;
use Kiboko\Component\Pipeline\ExecutionContext\PipelineExecutionInterface;
use Kiboko\Component\Pipeline\Processor\ProcessorInterface;

interface PipelineInterface
{
    /**
     * @param ExecutionContextInterface $executionContext
     * @param ProcessorInterface $processor
     *
     * @return PipelineExecutionInterface
     */
    public function run(
        ExecutionContextInterface $executionContext,
        ProcessorInterface $processor
    ): PipelineExecutionInterface;

    /**
     * Create a new pipeline with an appended stage.
     *
     * @param StepInterface[] $steps
     *
     * @return PipelineInterface
     */
    public function pipe(StepInterface ...$steps): PipelineInterface;
}
