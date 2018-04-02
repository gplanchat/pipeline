<?php

namespace Kiboko\Component\Pipeline\Hypervisor;

use React\EventLoop\LoopInterface;

interface ProcessHypervisorInterface extends ProcessEnqueueInterface, \Countable
{
    /**
     * @param LoopInterface $loop
     */
    public function run(LoopInterface $loop): void;
}
