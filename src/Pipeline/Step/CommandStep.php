<?php

namespace Kiboko\Component\Pipeline\Step;

use Kiboko\Component\Pipeline\ExecutionContext\Command\Command;
use Kiboko\Component\Pipeline\ExecutionContext\ExecutionContextInterface;
use Kiboko\Component\Pipeline\Hypervisor\ProcessHypervisorInterface;
use Kiboko\Component\Pipeline\Plumbing\StepInterface;

class CommandStep implements StepInterface
{
    /**
     * @var string[]
     */
    private $command;

    /**
     * @var string[]
     */
    private $environment;

    /**
     * @param string[] $environment
     * @param string[] $command
     */
    public function __construct(array $command, array $environment = [])
    {
        $this->command = $command;
        $this->environment = $environment;
    }

    public function run(
        ProcessHypervisorInterface $processHypervisor,
        ExecutionContextInterface $executionContext
    ): ExecutionContextInterface {
        $processHypervisor->enqueue(
            $executionContext->build(
                new Command(
                    ...$this->command
                )
            )
        );

        return $executionContext;
    }

    public static function fromConfig(array $config)
    {
        return new self($config);
    }
}
