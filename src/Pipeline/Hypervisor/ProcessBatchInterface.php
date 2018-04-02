<?php

namespace Kiboko\Component\Pipeline\Hypervisor;

interface ProcessBatchInterface extends ProcessEnqueueInterface, \IteratorAggregate, \Countable
{
    /**
     * @param ProcessHypervisorInterface $hypervisor
     */
    public function run(ProcessHypervisorInterface $hypervisor): void;
}
