<?php

namespace Kiboko\Component\Pipeline\Step;

use Kiboko\Component\Pipeline\ExecutionContext\Command\Command;
use Kiboko\Component\Pipeline\ExecutionContext\ExecutionContextInterface;
use Kiboko\Component\Pipeline\Hypervisor\ProcessHypervisorInterface;
use Kiboko\Component\Pipeline\Plumbing\StepInterface;

class PHPSpecStep implements StepInterface
{
    /**
     * @var string
     */
    private $path;

    /**
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->path = $path;
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
        yield 'phpspec';

        yield $this->path;
    }

    public static function fromConfig(array $config)
    {
        return new self(
            $config['path'] ?? '.'
        );
    }
}
