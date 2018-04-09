<?php

namespace Kiboko\Component\Pipeline\ExecutionContext;

class InterruptedPipelineExecution implements PipelineExecutionInterface
{
    /**
     * @var PipelineExecutionInterface
     */
    private $decorated;

    /**
     * @var ExecutionContextInterface
     */
    private $executionContext;

    /**
     * InterruptedPipelineExecution constructor.
     *
     * @param PipelineExecutionInterface $decorated
     * @param ExecutionContextInterface $executionContext
     */
    public function __construct(
        PipelineExecutionInterface $decorated,
        ExecutionContextInterface $executionContext
    ) {
        $this->decorated = $decorated;
        $this->executionContext = $executionContext;
    }

    /**
     * @return ExecutionContextInterface
     */
    public function getExecutionContext(): ExecutionContextInterface
    {
        return $this->executionContext;
    }

    /**
     * @return \Traversable
     */
    public function getIterator()
    {
        return $this->decorated->getIterator();
    }

    /**
     * @return int
     */
    public function count()
    {
        return $this->decorated->count();
    }
}
