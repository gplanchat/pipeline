<?php

namespace Kiboko\Component\Pipeline\Step;

use Kiboko\Component\Pipeline\ExecutionContext\Command\Command;
use Kiboko\Component\Pipeline\ExecutionContext\ExecutionContextInterface;
use Kiboko\Component\Pipeline\Hypervisor\ProcessHypervisorInterface;
use Kiboko\Component\Pipeline\Plumbing\StepInterface;
use React\ChildProcess\Process;

class CommandStep implements StepInterface
{
    use ThenableStepTrait;

    /**
     * @var string[]
     */
    private $shellCommand;

    /**
     * @var string[]
     */
    private $environment;

    /**
     * @var Command
     */
    private $command;

    /**
     * @var Process
     */
    private $process;

    /**
     * @param string[] $shellCommand
     * @param string[] $environment
     */
    public function __construct(array $shellCommand, array $environment = [])
    {
        $this->shellCommand = $shellCommand;
        $this->environment = $environment;
    }

    public function run(
        ProcessHypervisorInterface $processHypervisor,
        ExecutionContextInterface $executionContext
    ): ExecutionContextInterface {
        $processHypervisor->enqueue(
            $this->process = $executionContext->build(
                $this->command = new Command(...$this->shellCommand)
            )
        );

        $this->registerProcess($this->process);

        return $executionContext;
    }

    public static function fromConfig(array $config)
    {
        return new self(
            $config
        );
    }
}
