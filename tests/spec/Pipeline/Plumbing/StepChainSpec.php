<?php

namespace spec\Kiboko\Component\Pipeline\Plumbing;

use Kiboko\Component\Pipeline\ExecutionContext\ExecutionContextInterface;
use Kiboko\Component\Pipeline\Hypervisor\ProcessHypervisor;
use Kiboko\Component\Pipeline\Plumbing\StepChain;
use Kiboko\Component\Pipeline\Plumbing\StepChainInterface;
use Kiboko\Component\Pipeline\Plumbing\StepInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class StepChainSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(StepChain::class);
    }

    function it_is_initializable_with_several_steps(
        StepInterface $step1,
        StepInterface $step2,
        StepInterface $step3
    ) {
        $this->beConstructedWith($step1, $step2, $step3);
        $this->shouldHaveType(StepChain::class);
    }

    function it_can_pipe_steps(
        StepInterface $step1,
        StepInterface $step2,
        StepInterface $step3
    ) {
        $this->pipe($step1, $step2, $step3)
            ->shouldReturnAnInstanceOf(StepChainInterface::class);
    }

    function it_can_execute_steps(
        StepInterface $step1,
        StepInterface $step2,
        ProcessHypervisor $processHypervisor,
        ExecutionContextInterface $executionContext
    ) {
        $this->beConstructedWith($step1, $step2);

        $step1->run($processHypervisor, $executionContext)
            ->willReturn($executionContext);
        $step2->run($processHypervisor, $executionContext)
            ->willReturn($executionContext);

        $this->run($processHypervisor, $executionContext);

        $step1->run($processHypervisor, $executionContext)
            ->shouldHaveBeenCalled();
        $step2->run($processHypervisor, $executionContext)
            ->shouldHaveBeenCalled();
    }

    function it_can_iterate_steps(
        StepInterface $step1,
        StepInterface $step2,
        StepInterface $step3
    ) {
        $this->beConstructedWith($step1, $step2, $step3);
        $this->shouldHaveType(\IteratorAggregate::class);
        /** \PhpSpec\Wrapper\Collaborator $step1 */
        $this->callOnWrappedObject('getIterator')
            ->shouldYield(new \ArrayIterator([
                $step1->getWrappedObject(),
                $step2->getWrappedObject(),
                $step3->getWrappedObject()
            ]));
    }

    function it_can_count_steps(
        StepInterface $step1,
        StepInterface $step2,
        StepInterface $step3
    ) {
        $this->beConstructedWith($step1, $step2, $step3);
        $this->shouldHaveType(\Countable::class);

        $this->count()->shouldReturn(3);
    }
}
