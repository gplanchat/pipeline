<?php

namespace Kiboko\Component\PHPUnitPipeline\Step;

use Kiboko\Component\Pipeline\ExecutionContext\Command\Command;
use Kiboko\Component\Pipeline\ExecutionContext\ExecutionContextInterface;
use Kiboko\Component\Pipeline\Hypervisor\ProcessHypervisorInterface;
use Kiboko\Component\Pipeline\Plumbing\StepInterface;
use Kiboko\Component\Pipeline\Step\ThenableStepTrait;
use React\ChildProcess\Process;

class PHPUnitStep implements StepInterface
{
    use ThenableStepTrait;

    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $bootstrap;

    /**
     * @var string[]
     */
    private $filter;

    /**
     * @var string[]
     */
    private $resultFiles;

    /**
     * @var Command
     */
    private $command;

    /**
     * @var Process
     */
    private $process;

    /**
     * @param string $path
     * @param string $bootstrap
     * @param string[] $filter
     */
    public function __construct(
        string $path,
        string $bootstrap = null,
        array $filter = []
    ) {
        $this->path = $path;
        $this->bootstrap = $bootstrap;
        $this->filter = $filter;
        $this->resultFiles = [];
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
        yield 'phpunit';

        if ($this->bootstrap !== null) {
            yield sprintf('--bootstrap=%s', $this->bootstrap);
        }

        foreach ($this->filter as $filter) {
            yield sprintf('--filter=%s', $filter);
        }

        yield sprintf('--log-junit=%s', $this->resultFiles[] = tempnam(sys_get_temp_dir(), 'kiboko_phpunit_'));

        yield $this->path;
    }

    public static function fromConfig(array $config)
    {
        return new self(
            $config['path'] ?? '.',
            $config['bootstrap'] ?? null,
            $config['filter'] ?? []
        );
    }

    /**
     * @return string[]
     */
    public function getResultFiles(): array
    {
        return $this->resultFiles;
    }
}
