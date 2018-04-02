<?php

namespace Kiboko\Component\Pipeline\ExecutionContext;

use Kiboko\Component\Pipeline\ExecutionContext\Command\CommandInterface;
use React\ChildProcess\Process;

class ProcessBuilder
{
    /**
     * @var CommandInterface
     */
    private $command;

    /**
     * @var string
     */
    private $workingDirectory;

    /**
     * @var array
     */
    private $env = [];

    /**
     * @param CommandInterface $command
     * @param string $workingDirectory
     */
    public function __construct(
        CommandInterface $command,
        ?string $workingDirectory = null
    ) {
        $this->command = $command;
        $this->workingDirectory = $workingDirectory;
    }

    /**
     * @param string
     */
    public function setWorkingDirectory(string $workingDirectory)
    {
        $this->workingDirectory = $workingDirectory;
    }

    /**
     * @param string      $name  The variable name
     * @param null|string $value The variable value
     *
     * @return $this
     */
    public function setEnv(string $name, ?string $value = null)
    {
        $this->env[$name] = $value;

        return $this;
    }

    /**
     * @return Process
     *
     * @throws \LogicException
     */
    public function getProcess()
    {
        return new Process($this->command, $this->workingDirectory, $this->env);
    }
}
