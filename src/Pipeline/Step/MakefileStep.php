<?php

namespace Kiboko\Component\Pipeline\Step;

use Kiboko\Component\Pipeline\ExecutionContext\Command\Command;
use Kiboko\Component\Pipeline\ExecutionContext\ExecutionContextInterface;
use Kiboko\Component\Pipeline\Hypervisor\ProcessHypervisorInterface;
use Kiboko\Component\Pipeline\Plumbing\StepInterface;
use React\ChildProcess\Process;

class MakefileStep implements StepInterface
{
    use ThenableStepTrait;

    /**
     * @var string[]
     */
    private $tasks;

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
     * @param string[] $tasks
     * @param string[] $environment
     */
    public function __construct(array $tasks = [], array $environment = [])
    {
        $this->tasks = $tasks;
        $this->environment = $environment;
    }

    public function run(
        ProcessHypervisorInterface $processHypervisor,
        ExecutionContextInterface $executionContext
    ): ExecutionContextInterface {
        $processHypervisor->enqueue(
            $this->process = $executionContext->build(
                $this->command = new Command('make', ...$this->inlineEnvironments(), ...$this->tasks)
            )
        );

        $this->registerProcess($this->process);

        return $executionContext;
    }

    private function inlineEnvironments(): \Generator
    {
        foreach ($this->environment as $variable => $value) {
            yield sprintf('%s=%s', $variable, $value);
        }
    }

    public static function fromConfig(array $config)
    {
        return new self(
            $config['tasks'] ?? [],
            $config['env'] ?? []
        );
    }
}
