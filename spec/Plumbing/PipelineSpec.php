<?php

namespace spec\Kiboko\Component\Pipeline\Plumbing;

use Kiboko\Component\Pipeline\ExecutionContext\ExecutionContextInterface;
use Kiboko\Component\Pipeline\ExecutionContext\PipelineExecutionInterface;
use Kiboko\Component\Pipeline\Plumbing\Pipeline;
use Kiboko\Component\Pipeline\Plumbing\PipelineInterface;
use Kiboko\Component\Pipeline\Plumbing\StepChainInterface;
use Kiboko\Component\Pipeline\Plumbing\StepInterface;
use Kiboko\Component\Pipeline\Processor\ProcessorInterface;
use PhpSpec\ObjectBehavior;

class PipelineSpec extends ObjectBehavior
{
    function it_is_initializable(
        StepChainInterface $stepChain
    ) {
        $this->beConstructedWith($stepChain);
        $this->shouldHaveType(Pipeline::class);
    }

    function it_can_pipe_steps(
        StepChainInterface $stepChain,
        StepInterface $step
    ) {
        $stepChain->pipe($step)->willReturn($stepChain);

        $this->beConstructedWith($stepChain);
        $this->pipe($step)
            ->shouldReturnAnInstanceOf(PipelineInterface::class);

        $this->pipe($step)
            ->shouldNotBeEqualTo($this->getWrappedObject());
    }

    function it_executes_steps(
        ExecutionContextInterface $executionContext,
        ProcessorInterface $processor,
        StepChainInterface $stepChain,
        StepInterface $step,
        PipelineExecutionInterface $pipelineExecution
    ) {
        $processor->process($executionContext, $stepChain)
            ->shouldBeCalled()
            ->willReturn($pipelineExecution);

        $stepChain->getIterator()
            ->willReturn(new \ArrayIterator([$step->getWrappedObject()]));

        $this->beConstructedWith($stepChain);
        $this->callOnWrappedObject('__invoke', [$executionContext, $processor])
            ->shouldReturn($pipelineExecution);
    }
}
