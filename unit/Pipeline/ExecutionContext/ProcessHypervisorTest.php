<?php

namespace test\Kiboko\Component\Pipeline\ExecutionContext;

use Kiboko\Component\Pipeline\Hypervisor\ProcessHypervisor;
use PHPUnit\Framework\TestCase;
use React\ChildProcess\Process;
use React\EventLoop\LoopInterface;

class ProcessHypervisorTest extends TestCase
{
    private function buildRunningProcessMock($runs = 0, $shouldBeInterrupted = false): Process
    {
        $builder = $this->getMockBuilder(LoopInterface::class);

        $loopMock = $builder->getMockForAbstractClass();

        $builder = $this->getMockBuilder(Process::class);

        $builder->setMethods(['start', 'stop', 'isRunning', '__destruct']);
        $builder->setConstructorArgs(['bin/command']);

        $processMock = $builder->getMock();

        $processMock->expects($this->once())
            ->method('start')
            ->with($loopMock);

        for ($run = 1; $run <= $runs; ++$run) {
            $processMock->expects($this->at($run))
                ->method('isRunning')
                ->with()
                ->willReturn(true);
        }

        $processMock->expects($this->at($run))
            ->method('isRunning')
            ->with()
            ->willReturn($shouldBeInterrupted);

        return $processMock;
    }

    private function buildCallbackMock()
    {
        $builder = $this->getMockBuilder('object')
            ->setMethods(['__invoke']);

        $mock = $builder->getMock();

        return $mock;
    }

    public function testRunningProcesses()
    {
        $manager = new ProcessHypervisor(2);

        $manager->enqueue(
            $longRunningProcess = $this->buildRunningProcessMock(2)
        );

        $manager->enqueue(
            $shortRunningProcess = $this->buildRunningProcessMock(1)
        );

        $callback = $this->buildCallbackMock();

        $callback->expects($this->exactly(3))
            ->method('__invoke')
            ->willReturn(true);

        $callback->expects($this->at(0))
            ->method('__invoke')
            ->with($manager, []);

        $callback->expects($this->at(1))
            ->method('__invoke')
            ->with($manager, [$shortRunningProcess]);

        $callback->expects($this->at(2))
            ->method('__invoke')
            ->with($manager, [$longRunningProcess]);

        $manager->run($callback);
    }

    public function testInterruptedProcesses()
    {
        $manager = new ProcessHypervisor();

        $manager->enqueue(
            $longRunningProcess = $this->buildRunningProcessMock(1, true)
        );

        $callback = $this->buildCallbackMock();

        $callback->expects($this->at(0))
            ->method('__invoke')
            ->willReturn(true);

        $callback->expects($this->at(1))
            ->method('__invoke')
            ->willReturn(false);

        $callback->expects($this->exactly(2))
            ->method('__invoke');

        $manager->run($callback);
    }

    public function testPendingProcesses()
    {
        $manager = new ProcessHypervisor(2);

        $manager->enqueue(
            $firstProcess = $this->buildRunningProcessMock(2)
        );

        $manager->enqueue(
            $secondProcess = $this->buildRunningProcessMock(3)
        );

        $manager->enqueue(
            $thirdProcess = $this->buildRunningProcessMock(3)
        );

        $callback = $this->buildCallbackMock();

        $callback->expects($this->exactly(7))
            ->method('__invoke')
            ->willReturn(true);

        $callback->expects($this->at(0))
            ->method('__invoke')
            ->with($manager, [], 3);

        $callback->expects($this->at(2))
            ->method('__invoke')
            ->with($manager, [$firstProcess], 2);

        $callback->expects($this->at(3))
            ->method('__invoke')
            ->with($manager, [$secondProcess], 1);

        $callback->expects($this->at(6))
            ->method('__invoke')
            ->with($manager, [$thirdProcess], 0);

        $manager->run($callback);
    }
}
