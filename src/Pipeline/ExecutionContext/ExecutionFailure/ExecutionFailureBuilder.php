<?php

namespace Kiboko\Component\Pipeline\ExecutionContext\ExecutionFailure;

class ExecutionFailureBuilder implements ExecutionFailureBuilderInterface
{
    private $maxDepth = 2;

    public function build(\Throwable $throwable): ExecutionFailureChainInterface
    {
        return new ExecutionFailureChain(...$this->transformException($throwable));
    }

    private function transformException(\Throwable $throwable): iterable
    {
        do {
            yield new ExecutionFailure(
                $throwable->getMessage(),
                $throwable->getCode(),
                $throwable->getFile(),
                $throwable->getLine(),
                iterator_to_array($this->flattenTrace($throwable)),
                $throwable->getTraceAsString()
            );
        } while ($throwable = $throwable->getPrevious());
    }

    /**
     * @param \Throwable $throwable
     *
     * @return iterable|ExecutionTraceInterface[]
     */
    private function flattenTrace(\Throwable $throwable): iterable
    {
        $trace = $throwable->getTrace();
        foreach ($trace as $call) {
            yield new ExecutionTrace(
                $call['file'] ?? null,
                $call['line'] ?? null,
                $call['function'] ?? null,
                $call['class'] ?? null,
                $this->flattenArgs($call['args'] ?? []),
                ($call['type'] ?? null) === '->'
            );
        }
    }

    private function flattenArgs(array $args, int $level = 0)
    {
        return array_map(function($item) use($level) {
            if ($item instanceof \Closure) {
                $closureReflection = new \ReflectionFunction($item);
                return sprintf(
                    '(Closure at %s:%s)',
                    $closureReflection->getFileName(),
                    $closureReflection->getStartLine()
                );
            } else if (is_object($item)) {
                return sprintf('object(%s)', get_class($item));
            } else if (is_resource($item)) {
                return sprintf('resource(%s)', get_resource_type($item));
            } else if (is_array($item)) {
                if ($level >= $this->maxDepth) {
                    return sprintf('array(%d)', count($item));
                }

                return $this->flattenArgs($item, $level + 1);
            }

            return $item;
        }, $args);
    }
}
