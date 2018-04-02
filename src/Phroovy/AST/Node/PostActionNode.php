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
    public $changed;

    /**
     * @var StepCollectionNode
     */
    public $unstable;

    /**
     * @var StepCollectionNode
     */
    public $failure;

    /**
     * PostActionNode constructor.
     * @param StepCollectionNode|null $always
     * @param StepCollectionNode|null $success
     * @param StepCollectionNode|null $changed
     * @param StepCollectionNode|null $unstable
     * @param StepCollectionNode|null $failure
     */
    public function __construct(
        ?StepCollectionNode $always = null,
        ?StepCollectionNode $success = null,
        ?StepCollectionNode $changed = null,
        ?StepCollectionNode $unstable = null,
        ?StepCollectionNode $failure = null
    ) {
        $this->always = $always;
        $this->success = $success;
        $this->changed = $changed;
        $this->unstable = $unstable;
        $this->failure = $failure;
    }
}
