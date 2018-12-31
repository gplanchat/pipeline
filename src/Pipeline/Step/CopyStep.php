<?php

namespace Kiboko\Component\Pipeline\Step;

use Kiboko\Component\Pipeline\ExecutionContext\Command\Command;
use Kiboko\Component\Pipeline\ExecutionContext\ExecutionContextInterface;
use Kiboko\Component\Pipeline\Hypervisor\ProcessHypervisorInterface;
use Kiboko\Component\Pipeline\Plumbing\StepInterface;
use React\ChildProcess\Process;

class CopyStep implements StepInterface
{
    use ThenableStepTrait;

    /**
     * @var string
     */
    private $from;

    /**
     * @var string
     */
    private $to;

    /**
     * @var Command
     */
    private $command;

    /**
     * @var Process
     */
    private $process;

    /**
     * @param string $from
     * @param string $to
     */
    public function __construct(string $from, string $to)
    {
        $this->from = $from;
        $this->to = $to;
    }

    public function run(
        ProcessHypervisorInterface $processHypervisor,
        ExecutionContextInterface $executionContext
    ): ExecutionContextInterface {
        $processHypervisor->enqueue(
            $this->process = $executionContext->build(
                $this->command = new Command('copy', $this->from, $this->to)
            )
        );

        $this->registerProcess($this->process);

        return $executionContext;
    }

    public static function fromConfig(array $config)
    {
        return new self(
            $config['from'],
            $config['to']
        );
    }
}
