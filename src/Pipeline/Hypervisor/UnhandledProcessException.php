<?php

namespace Kiboko\Component\Pipeline\Hypervisor;

use React\ChildProcess\Process;

class UnhandledProcessException extends \RuntimeException
{
    /**
     * @var Process
     */
    private $process;

    /**
     * @param string $message
     * @param Process $process
     * @param \Throwable|null $e
     */
    public function __construct(string $message, Process $process, \Throwable $e = null)
    {
        parent::__construct($message, null, $e);

        $this->process = $process;
    }

    /**
     * @return Process
     */
    public function getProcess(): Process
    {
        return $this->process;
    }
}
