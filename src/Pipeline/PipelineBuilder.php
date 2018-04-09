<?php

namespace Kiboko\Component\Pipeline;

use Kiboko\Component\Pipeline\Plumbing\Pipeline;
use Kiboko\Component\Pipeline\Plumbing\PipelineInterface;
use Kiboko\Component\Pipeline\Plumbing\StepChain;
use Kiboko\Component\Pipeline\Plumbing\StepInterface;
use Kiboko\Component\Pipeline\Processor\ProcessorInterface;

class PipelineBuilder implements PipelineBuilderInterface
{
    /**
     * @var StepInterface[]
     */
    private $steps = [];

    /**
     * @param StepInterface[] $steps
     */
    public function __construct(StepInterface ...$steps)
    {
        $this->steps = $steps;
    }

    /**
     * @param StepInterface[] $steps
     *
     * @return PipelineBuilderInterface
     */
    public function add(StepInterface ...$steps): PipelineBuilderInterface
    {
        array_push($this->steps, ...$steps);

        return $this;
    }

    /**
     * Build a new Pipeline object
     *
     * @param  ProcessorInterface|null $processor
     *
     * @return PipelineInterface
     */
    public function build(ProcessorInterface $processor = null): PipelineInterface
    {
        return new Pipeline(new StepChain(...$this->steps));
    }
}
