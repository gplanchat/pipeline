<?php

namespace spec\Kiboko\Component\Pipeline\ExecutionContext;

use Kiboko\Component\Pipeline\ExecutionContext\ProcessHypervisor;
use Kiboko\Component\Pipeline\ExecutionContext\ProcessHypervisorInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Process\Process;

class ProcessHypervisorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ProcessHypervisorInterface::class);
    }

    function it_can_enqueue_one_process(
        Process $process
    ) {
        $this->enqueue($process)
            ->shouldReturnAnInstanceOf(ProcessHypervisorInterface::class);

        $this->count()
            ->shouldReturn(1);
    }

    function it_can_enqueue_process_with_callback(
        Process $process
    ) {
        $this->enqueue($process, function() {})
            ->shouldReturnAnInstanceOf(ProcessHypervisorInterface::class);

        $this->count()
            ->shouldReturn(1);
    }

    function it_can_enqueue_several_processes(
        Process $process1,
        Process $process2,
        Process $process3
    ) {
        $this->enqueue($process1)
            ->shouldReturnAnInstanceOf(ProcessHypervisorInterface::class);

        $this->enqueue($process2)
            ->shouldReturnAnInstanceOf(ProcessHypervisorInterface::class);

        $this->enqueue($process3)
            ->shouldReturnAnInstanceOf(ProcessHypervisorInterface::class);

        $this->count()
            ->shouldReturn(3);
    }

    function it_can_execute_processes(
        Process $process
    ) {
        $process->start()->shouldBeCalled();
        $process->isRunning()->willReturn(false);

        $this->enqueue($process)
            ->shouldReturnAnInstanceOf(ProcessHypervisorInterface::class);

        $this->run(function() {return true;})
            ->shouldReturnAnInstanceOf(ProcessHypervisorInterface::class);
    }
}
