<?php

declare(strict_types=1);

namespace BackEndTea\Architect\Infrastructure\PHPParser;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class DeclaringCollector extends NodeVisitorAbstract
{
    /** @var array<string> */
    public array $declarations = [];

    public function enterNode(Node $node): Node|null
    {
        if (
            ! $node instanceof Node\Stmt\Interface_
            && ! $node instanceof Node\Stmt\Class_
            && ! $node instanceof Node\Stmt\Trait_
            && ! $node instanceof Node\Stmt\Enum_
        ) {
            return null;
        }

        if ($node->namespacedName instanceof Node\Name) {
            $this->declarations[] =  $node->namespacedName->toString();
        }

        return null;
    }
}
