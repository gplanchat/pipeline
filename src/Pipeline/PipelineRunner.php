<?php

namespace Kiboko\Component\Pipeline;

use Kiboko\Component\Pipeline\ExecutionContext\ExecutionContextInterface;
use Kiboko\Component\Pipeline\ExecutionContext\PipelineExecutionInterface;
use Kiboko\Component\Pipeline\Plumbing\PipelineInterface;
use Kiboko\Component\Pipeline\Processor\ProcessorInterface;

class PipelineRunner
{
    /**
     * @var ProcessorInterface
     */
    private $pipelineProcessor;

    /**
     * @param ProcessorInterface $pipelineProcessor
     */
    public function __construct(ProcessorInterface $pipelineProcessor)
    {
        $this->pipelineProcessor = $pipelineProcessor;
    }

    /**
     * @param PipelineInterface $pipeline
     * @param ExecutionContextInterface $executionContext
     *
     * @return PipelineExecutionInterface
     */
    public function run(
        PipelineInterface $pipeline,
        ExecutionContextInterface $executionContext
    ): PipelineExecutionInterface {
        return $pipeline->run($executionContext, $this->pipelineProcessor);
    }
}
