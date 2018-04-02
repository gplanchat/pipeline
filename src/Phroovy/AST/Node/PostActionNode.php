<?php


namespace Kiboko\Component\Phroovy\AST\Node;

class PostActionNode implements NodeInterface
{
    /**
     * @var StepCollectionNode
     */
    public $always;

    /**
     * @var StepCollectionNode
     */
    public $success;

    /**
     * @var StepCollectionNode
     */
    public $change;

    /**
     * @var StepCollectionNode
     */
    public $unstable;

    /**
     * @var StepCollectionNode
     */
    public $failure;
}
