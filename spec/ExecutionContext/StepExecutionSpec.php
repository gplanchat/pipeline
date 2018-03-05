<?php

namespace spec\Kiboko\Component\Pipeline\ExecutionContext;

use Kiboko\Component\Pipeline\ExecutionContext\ExecutionContextInterface;
use Kiboko\Component\Pipeline\ExecutionContext\ExecutionFailure\ExecutionFailureBuilder;
use Kiboko\Component\Pipeline\ExecutionContext\PipelineExecutionInterface;
use Kiboko\Component\Pipeline\ExecutionContext\ProcessManager;
use Kiboko\Component\Pipeline\ExecutionContext\ProcessManagerInterface;
use Kiboko\Component\Pipeline\ExecutionContext\StepExecution;
use Kiboko\Component\Pipeline\Plumbing\StepInterface;
use PhpSpec\ObjectBehavior;

class StepExecutionSpec extends ObjectBehavior
{
    function it_is_initializable(
        PipelineExecutionInterface $pipelineExecution,
        StepInterface $step,
        ExecutionFailureBuilder $executionFailureBuilder
    ) {
        $this->beConstructedWith($pipelineExecution, $step, $executionFailureBuilder);
        $this->shouldHaveType(StepExecution::class);
    }

    function it_executes_the_step(
        PipelineExecutionInterface $pipelineExecution,
        StepInterface $step,
        ExecutionFailureBuilder $executionFailureBuilder,
        ProcessManagerInterface $processManager,
        ExecutionContextInterface $executionContext
    ) {
        $this->beConstructedWith($pipelineExecution, $step, $executionFailureBuilder);

        $step->__invoke($processManager->getWrappedObject(), $executionFailureBuilder->getWrappedObject())
            ->willReturn($executionContext);

        $this->execute($processManager->getWrappedObject(), $executionContext->getWrappedObject())
            ->shouldReturn($executionContext);
    }
}
