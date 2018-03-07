<?php

namespace spec\Kiboko\Component\Pipeline\ExecutionContext;

use Kiboko\Component\Pipeline\ExecutionContext\ProcessManager;
use Kiboko\Component\Pipeline\ExecutionContext\ProcessManagerInterface;
use PhpSpec\ObjectBehavior;
use Symfony\Component\Process\Process;

class ProcessManagerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ProcessManagerInterface::class);
    }

    function it_can_enqueue_one_process(
        Process $process
    ) {
        $this->enqueue($process)
            ->shouldReturnAnInstanceOf(ProcessManagerInterface::class);

        $this->count()
            ->shouldReturn(1);
    }

    function it_can_enqueue_process_with_callback(
        Process $process
    ) {
        $this->enqueue($process, function() {})
            ->shouldReturnAnInstanceOf(ProcessManagerInterface::class);

        $this->count()
            ->shouldReturn(1);
    }

    function it_can_enqueue_several_processes(
        Process $process1,
        Process $process2,
        Process $process3
    ) {
        $this->enqueue($process1)
            ->shouldReturnAnInstanceOf(ProcessManagerInterface::class);

        $this->enqueue($process2)
            ->shouldReturnAnInstanceOf(ProcessManagerInterface::class);

        $this->enqueue($process3)
            ->shouldReturnAnInstanceOf(ProcessManagerInterface::class);

        $this->count()
            ->shouldReturn(3);
    }

    function it_can_execute_processes(
        Process $process
    ) {
        $process->start()->shouldBeCalled();
        $process->isRunning()->willReturn(false);

        $this->enqueue($process)
            ->shouldReturnAnInstanceOf(ProcessManagerInterface::class);

        $this->run(function() {
            return true;
        })->shouldReturnAnInstanceOf(ProcessManagerInterface::class);
    }
}
