<?php

namespace Kiboko\Component\Pipeline\Processor;

use Kiboko\Component\Pipeline\ExecutionContext\ExecutionContextInterface;
use Kiboko\Component\Pipeline\ExecutionContext\PipelineExecutionInterface;
use Kiboko\Component\Pipeline\Plumbing\StepChainInterface;
use Kiboko\Component\Pipeline\Plumbing\StepInterface;

interface ProcessorInterface
{
    /**
     * @param ExecutionContextInterface $executionContext
     * @param StepChainInterface $stepChain
     *
     * @return PipelineExecutionInterface
     */
    public function run(
        ExecutionContextInterface $executionContext,
        StepChainInterface $stepChain
    ): PipelineExecutionInterface;
}
