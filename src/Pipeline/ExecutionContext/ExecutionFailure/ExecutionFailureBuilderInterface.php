<?php

namespace Kiboko\Component\Pipeline\ExecutionContext\ExecutionFailure;

interface ExecutionFailureBuilderInterface
{
    public function build(\Throwable $throwable): ExecutionFailureChainInterface;
}
