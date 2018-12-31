<?php

namespace Kiboko\Component\Pipeline\Hypervisor;

use React\ChildProcess\Process;
use React\EventLoop\LoopInterface;
use React\Promise\Deferred;
use React\Promise\ExtendedPromiseInterface;

class ProcessHypervisor implements ProcessHypervisorInterface
{
    /**
     * @var int
     */
    private $maxProcesses;

    /**
     * @var Process[]
     */
    private $pendingProcesses;

    /**
     * @var Process[]
     */
    private $currentProcesses;

    /**
     * @param int $maxProcesses
     */
    public function __construct(int $maxProcesses = 1)
    {
        $this->maxProcesses = max($maxProcesses, 1);
        $this->pendingProcesses = new \SplObjectStorage();
        $this->currentProcesses = new \SplObjectStorage();
    }

    public function __destruct()
    {
        if ($this->currentProcesses === null) {
            return;
        }

        /** @var Process $process */
        foreach ($this->currentProcesses as $process) {
            $process->close();
        }
    }

    /**
     * @param Process $process
     *
     * @return ExtendedPromiseInterface
     */
    public function enqueue(Process $process): ExtendedPromiseInterface
    {
        $deferred = new Deferred();

        $this->pendingProcesses->attach($process, $deferred);

        return $deferred->promise();
    }

    /**
     * @param int $processesToStart
     *
     * @return \SplObjectStorage
     */
    private function pickFromPendingProcesses(int $processesToStart): \SplObjectStorage
    {
        $newProcesses = new \SplObjectStorage();
        foreach ($this->pendingProcesses as $process) {
            $deferred = $this->pendingProcesses->offsetGet($process);
            $newProcesses->attach($process, $deferred);

            if (--$processesToStart <= 0) {
                break;
            }
        }

        $this->pendingProcesses->removeAll($newProcesses);

        return $newProcesses;
    }

    /**
     * @return \SplObjectStorage
     */
    private function probeAndPickStoppedProcesses(): \SplObjectStorage
    {
        $stoppedProcesses = new \SplObjectStorage();
        foreach ($this->currentProcesses as $process) {
            if ($process->isRunning()) {
                continue;
            }

            /** @var Deferred $deferred */
            $deferred = $this->currentProcesses->offsetGet($process);
            $stoppedProcesses->attach($process, $deferred);

            if ($process->isTerminated()) {
                $deferred->reject($process->getTermSignal());
            } else if ($process->isStopped()) {
                $deferred->reject($process->getStopSignal());
            } else {
                $deferred->resolve();
            }
        }

        $this->currentProcesses->removeAll($stoppedProcesses);

        return $stoppedProcesses;
    }

    public function run(LoopInterface $loop): void
    {
        while ($this->count() > 0) {
            $processesToStart = $this->maxProcesses - $this->currentProcesses->count();
            if ($processesToStart > 0) {
                $newProcesses = $this->pickFromPendingProcesses($processesToStart);
                $this->startProcesses($loop, $newProcesses);

                $this->currentProcesses->addAll($newProcesses);
            }

            $this->probeAndPickStoppedProcesses();
        }
    }

    /**
     * @param LoopInterface $loop
     * @param Process[]|iterable $processes
     */
    private function startProcesses(LoopInterface $loop, iterable $processes): void
    {
        /** @var Process $process */
        foreach ($processes as $process) {
            try {
                $process->start($loop);
                $process->stdout->on('data', function($data) {
                    file_put_contents('php://output', $data);
                });
            } catch (\RuntimeException $e) {
                throw new UnhandledProcessException(
                    'Could not start process.',
                    $process,
                    $e
                );
            }
        }
    }

    /**
     * @return int
     */
    public function count()
    {
        return $this->pendingProcesses->count() + $this->currentProcesses->count();
    }
}
