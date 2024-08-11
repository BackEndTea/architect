<?php

declare(strict_types=1);

namespace BackEndTea\Architect\Infrastructure;

use BackEndTea\Architect\Domain\Config\Configuration;
use BackEndTea\Architect\Domain\Declaration;
use BackEndTea\Architect\Infrastructure\PHPParser\DeclaringCollector;
use BackEndTea\Architect\Infrastructure\PHPParser\NameUsingCollector;
use LogicException;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\ParserFactory;
use Roave\BetterReflection\Reflection\ReflectionClass;
use Roave\BetterReflection\Reflector\Exception\IdentifierNotFound;
use Roave\BetterReflection\Reflector\Reflector;
use SplFileInfo;

use function file_get_contents;
use function sprintf;

class Runner
{
    public function __construct(
        private Reflector $reflector,
    ) {
    }

    /** @return list<string> */
    public function run(Configuration $config, SplFileInfo $fileInfo): array
    {
        $output   = [];
        $parser   = (new ParserFactory())->createForHostVersion();
        $realPath = $fileInfo->getRealPath();
        if ($realPath === false) {
            throw new LogicException('File has issues and could not be found.');
        }

        $content = file_get_contents($realPath);
        if ($content === false) {
            throw new LogicException('File has issues and could not be read');
        }

        $ast = $parser->parse($content);
        if ($ast === null) {
            throw new LogicException('should not happen');
        }

        $traverser = new NodeTraverser();
        $traverser->addVisitor(new NameResolver());
        $traverser->addVisitor($declaring = new DeclaringCollector());
        $traverser->addVisitor($using = new NameUsingCollector());

        $traverser->traverse($ast);
        foreach ($declaring->declarations as $declaration) {
            foreach ($using->usedNames as $usedName) {
                foreach ($config->rules as $rule) {
                    if (
                        $rule->isAllowed(
                            new Declaration(
                                $declaration,
                                $this->safeReflect($declaration),
                            ),
                            new Declaration(
                                $usedName,
                                $this->safeReflect($usedName),
                            ),
                        )
                    ) {
                        continue;
                    }

                    $output[] = sprintf('Violation found: %s uses %s', $declaration, $usedName);
                }
            }
        }

        return $output;
    }

    private function safeReflect(string $class): ReflectionClass|null
    {
        try {
            return $this->reflector->reflectClass($class);
        } catch (IdentifierNotFound) {
            return null;
        }
    }
}
