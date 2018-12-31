<?php

namespace Kiboko\Component\PHPUnitPipeline\Step;

use Kiboko\Component\Pipeline\ExecutionContext\Command\Command;
use Kiboko\Component\Pipeline\ExecutionContext\ExecutionContextInterface;
use Kiboko\Component\Pipeline\Hypervisor\ProcessHypervisorInterface;
use Kiboko\Component\Pipeline\Plumbing\StepInterface;

class PHPUnitStep implements StepInterface
{
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
    }

    public function run(
        ProcessHypervisorInterface $processHypervisor,
        ExecutionContextInterface $executionContext
    ): ExecutionContextInterface {
        $processHypervisor->enqueue(
            $executionContext->build(
                new Command(...$this->buildCommandArguments())
            )
        );

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

        yield sprintf('--log-junit=%s.xml', uniqid('phpunit_'));

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
}
