<?php

namespace spec\Kiboko\Component\Pipeline\ExecutionContext;

use Kiboko\Component\Pipeline\ExecutionContext\ProcessManager;
use Kiboko\Component\Pipeline\ExecutionContext\ProcessManagerInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Process\Process;

class ProcessManagerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ProcessManager::class);
    }

    function it_can_enqueue_processes()
    {
    }

    function it_can_execute_processes()
    {
    }
}
