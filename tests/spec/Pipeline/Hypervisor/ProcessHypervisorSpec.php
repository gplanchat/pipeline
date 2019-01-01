<?php

namespace spec\Kiboko\Component\Pipeline\Hypervisor;

use Kiboko\Component\Pipeline\Hypervisor\ProcessHypervisorInterface;
use PhpSpec\ObjectBehavior;
use React\ChildProcess\Process;
use React\EventLoop\LoopInterface;
use React\Promise\ExtendedPromiseInterface;

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
            ->shouldReturnAnInstanceOf(ExtendedPromiseInterface::class);

        $this->count()
            ->shouldReturn(1);
    }

    function it_can_enqueue_several_processes(
        Process $process1,
        Process $process2,
        Process $process3
    ) {
        $this->enqueue($process1)
            ->shouldReturnAnInstanceOf(ExtendedPromiseInterface::class);

        $this->enqueue($process2)
            ->shouldReturnAnInstanceOf(ExtendedPromiseInterface::class);

        $this->enqueue($process3)
            ->shouldReturnAnInstanceOf(ExtendedPromiseInterface::class);

        $this->count()
            ->shouldReturn(3);
    }

    function it_can_execute_successful_processes(
        LoopInterface $loop,
        Process $process
    ) {
        $process->start($loop)->shouldBeCalled();
        $process->isRunning()->willReturn(false);
        $process->isTerminated()->willReturn(false);
        $process->isStopped()->willReturn(false);

        $this->enqueue($process)
            ->shouldReturnAnInstanceOf(ExtendedPromiseInterface::class);

        $this->run($loop);
    }

    function it_can_execute_terminated_processes(
        LoopInterface $loop,
        Process $process
    ) {
        $process->start($loop)->shouldBeCalled();
        $process->isRunning()->willReturn(false);
        $process->isTerminated()->willReturn(true);
        $process->getTermSignal()->willReturn(SIGTERM);
        $process->isStopped()->willReturn(false);

        $this->enqueue($process)
            ->shouldReturnAnInstanceOf(ExtendedPromiseInterface::class);

        $this->run($loop);
    }

    function it_can_execute_stopped_processes(
        LoopInterface $loop,
        Process $process
    ) {
        $process->start($loop)->shouldBeCalled();
        $process->isRunning()->willReturn(false);
        $process->isTerminated()->willReturn(false);
        $process->isStopped()->willReturn(true);
        $process->getStopSignal()->willReturn(SIGSTOP);

        $this->enqueue($process)
            ->shouldReturnAnInstanceOf(ExtendedPromiseInterface::class);

        $this->run($loop);
    }
}
