<?php

namespace test\Kiboko\Component\Pipeline\ExecutionContext;

use Kiboko\Component\Pipeline\Hypervisor\ProcessHypervisor;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use React\ChildProcess\Process;
use React\EventLoop\LoopInterface;
use React\Stream\ReadableResourceStream;
use React\Stream\WritableResourceStream;

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

        $this->buildStreamResourcesMock($loopMock, $processMock);

        return $processMock;
    }

    private function buildStreamResourcesMock(LoopInterface $loopMock, Process $processMock)
    {
        $streamCloseHandler = function () use (&$closeCount, $loopMock, $processMock) {
            $closeCount++;

            if ($closeCount < 2) {
                return;
            }

            // process already closed => report immediately
            if (!$processMock->isRunning()) {
                $processMock->close();
                $processMock->emit('exit', [$processMock->getExitCode(), $processMock->getTermSignal()]);
                return;
            }

            // close not detected immediately => check regularly
            $loopMock->addPeriodicTimer(.1, function ($timer) use ($processMock, $loopMock) {
                if (!$processMock->isRunning()) {
                    $loopMock->cancelTimer($timer);
                    $processMock->close();
                    $processMock->emit('exit', [$processMock->getExitCode(), $processMock->getTermSignal()]);
                }
            });
        };

        $processMock->stdin = new WritableResourceStream(fopen('php://temp', 'w'), $loopMock);
        $processMock->stdout = new ReadableResourceStream(fopen('php://temp', 'w'), $loopMock);
        $processMock->stderr = new ReadableResourceStream(fopen('php://temp', 'w'), $loopMock);

        $processMock->stdout->on('close', $streamCloseHandler);
        $processMock->stderr->on('close', $streamCloseHandler);
    }

    /**
     * @return LoopInterface|MockObject
     */
    private function buildLoopMock(): LoopInterface
    {
        $builder = $this->getMockBuilder(LoopInterface::class);

        $mock = $builder->getMockForAbstractClass();

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

        $loop = $this->buildLoopMock();

        $manager->run($loop);
    }

    public function testInterruptedProcesses()
    {
        $manager = new ProcessHypervisor();

        $manager->enqueue(
            $longRunningProcess = $this->buildRunningProcessMock(1, true)
        );

        $callback = $this->buildLoopMock();

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

        $callback = $this->buildLoopMock();

        $manager->run($callback);
    }
}
