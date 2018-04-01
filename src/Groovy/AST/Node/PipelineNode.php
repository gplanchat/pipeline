<?php

namespace Kiboko\Component\Groovy\AST\Node;

class PipelineNode implements NodeInterface
{
    use AgentAwareNode;
    use EnvironmentAwareNode;
    use PostActionsAwareNode;

    /**
     * @var StageCollectionNode
     */
    public $stages;
}
