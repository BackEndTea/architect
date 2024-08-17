<?php

declare(strict_types=1);

namespace BackEndTea\Architect\Infrastructure\PHPParser;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

use function in_array;

final class NameUsingCollector extends NodeVisitorAbstract
{
    /** @var array<Node\Name> */
    public array $usedNames = [];

    private const IGNORED_NAMES = [
        'self',
        'static',
//        'int',
//        'string',
//        'bool',
//        'float',
//        'array',
        'null',
    ];

    public function enterNode(Node $node): Node|int|null
    {
        if (
            ! ($node instanceof Node\Name)
            || $node->getAttribute('isDeclaration')
            || in_array($node->toString(), self::IGNORED_NAMES, true)
        ) {
            return null;
        }

        $this->usedNames[] = $node;

        return null;
    }
}
