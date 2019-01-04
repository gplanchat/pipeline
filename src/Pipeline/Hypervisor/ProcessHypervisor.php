<?php

namespace Kiboko\Component\Pipeline\Hypervisor;

use Clue\React\Mq\Queue;
use React\ChildProcess\Process;
use React\EventLoop\LoopInterface;
use React\Promise\Deferred;
use React\Promise\ExtendedPromiseInterface;

class ProcessHypervisor implements ProcessHypervisorInterface
{
    /**
     * @var LoopInterface
     */
    private $loop;

    /**
     * @var Queue
     */
    private $queue;

    /**
     * @param LoopInterface $loop
     * @param int           $concurrency
     * @param int|null      $processesLimit
     */
    public function __construct(LoopInterface $loop, int $concurrency = 1, ?int $processesLimit = null)
    {
        $this->loop = $loop;

        $this->queue = new Queue(
            max($concurrency, 1),
            $processesLimit === null ? null : max($concurrency, 1),
            function(Process $process) {
                $process->start($this->loop);

                $deferred = new Deferred();

                $process->on('exit', function(int $exitCode, int $termSignal) use($deferred) {
                    if ($exitCode === 0) {
                        $deferred->resolve();
                    } else {
                        $deferred->reject(new \Exception(strtr(
                            'The process terminated with code %code%, got signal %signal%.',
                            [
                                '%code%' => sprintf('%d', $exitCode),
                                '%signal%' => $this->getSignalString($termSignal),
                            ]
                        )));
                    }
                });

                return $deferred->promise();
            }
        );
    }

    private function getSignalString(int $signalCode)
    {
        $signals = [
            1 => 'SIGHUP',
            2 => 'SIGINT',
            3 => 'SIGQUIT',
            4 => 'SIGILL',
            6 => 'SIGABRT',
            8 => 'SIGFPE',
            9 => 'SIGKILL',
            11 => 'SIGSEGV',
            13 => 'SIGPIPE',
            14 => 'SIGALRM',
            15 => 'SIGTERM',
        ];

        return $signals[$signalCode] ?? sprintf('%d', $signalCode);
    }

    /**
     * @param Process $process
     *
     * @return ExtendedPromiseInterface
     */
    public function enqueue(Process $process): ExtendedPromiseInterface
    {
        return ($this->queue)($process);
    }
}
