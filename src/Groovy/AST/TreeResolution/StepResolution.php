<?php

namespace Kiboko\Component\Groovy\AST\TreeResolution;

use Kiboko\Component\Groovy\AST\Node;
use Kiboko\Component\Groovy\AST\TokenConstraint;
use Kiboko\Component\Groovy\AST\TokenStream;

class StepResolution implements TreeResolutionInterface
{
    public function assert(TokenStream $tokenStream): bool
    {
        return $tokenStream->assertAny(TokenConstraint::anyString());
    }

    public function create(TokenStream $tokenStream): Node\NodeInterface
    {
        $step = new Node\StepNode($tokenStream->expect(TokenConstraint::identifier())->value);

        while (true) {
            if (!$tokenStream->assertAny(TokenConstraint::anyString())) {
                break;
            }

            $step->arguments[] = $tokenStream->consume()->value;
        }

        return $step;
    }
}
