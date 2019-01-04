<?php

namespace Kiboko\Component\Pipeline\Hypervisor;

use React\ChildProcess\Process;
use React\Promise\ExtendedPromiseInterface;

interface ProcessHypervisorInterface
{
    /**
     * @param Process $process
     *
     * @return ExtendedPromiseInterface
     */
    public function enqueue(Process $process): ExtendedPromiseInterface;
}
