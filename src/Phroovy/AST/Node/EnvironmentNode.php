<?php

namespace Kiboko\Component\Phroovy\AST\Node;

class EnvironmentNode implements NodeInterface
{
    /**
     * @var array
     */
    public $environment;

    /**
     * EnvironmentNode constructor.
     * @param $environment
     */
    public function __construct(array $environment = [])
    {
        $this->environment = $environment;
    }

    /**
     * @param string $key
     * @param string|int $value
     */
    public function set(string $key, $value)
    {
        $this->environment[$key] = $value;
    }
}
