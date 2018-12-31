<?php

namespace Kiboko\Component\Pipeline\Step;

use Kiboko\Component\Pipeline\ExecutionContext\Command\Command;
use Kiboko\Component\Pipeline\ExecutionContext\ExecutionContextInterface;
use Kiboko\Component\Pipeline\Hypervisor\ProcessHypervisorInterface;
use Kiboko\Component\Pipeline\Plumbing\StepInterface;
use React\ChildProcess\Process;

class ComposerStep implements StepInterface
{
    use ThenableStepTrait;

    /**
     * @var string
     */
    private $composerCommand;

    /**
     * @var string
     */
    private $path;

    /**
     * @var Command
     */
    private $command;

    /**
     * @var Process
     */
    private $process;

    /**
     * @param string $composerCommand
     * @param string $path
     */
    public function __construct(string $composerCommand, ?string $path = null)
    {
        $this->composerCommand = $composerCommand;
        $this->path = $path;
    }

    public function run(
        ProcessHypervisorInterface $processHypervisor,
        ExecutionContextInterface $executionContext
    ): ExecutionContextInterface {
        $processHypervisor->enqueue(
            $this->process = $executionContext->build(
                $this->command = new Command(...$this->buildCommandArguments())
            )
        );

        $this->registerProcess($this->process);

        return $executionContext;
    }

    private function buildCommandArguments()
    {
        yield 'composer';
        yield $this->composerCommand;

        if ($this->path !== null) {
            yield sprintf('--working-dir=%s', $this->path);
        }
    }

    public static function fromConfig(array $config)
    {
        return new self(
            $config['command'] ?? 'install',
            $config['path'] ?? null
        );
    }
}
