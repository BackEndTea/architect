<?php

declare(strict_types=1);

namespace BackEndTea\Architect\Infrastructure;

use BackEndTea\Architect\Domain\Config\Configuration;
use BackEndTea\Architect\Domain\Declaration;
use BackEndTea\Architect\Domain\Rule\Error;
use BackEndTea\Architect\Domain\Rule\ErrorBuilder;
use BackEndTea\Architect\Infrastructure\PHPParser\DeclaringCollector;
use BackEndTea\Architect\Infrastructure\PHPParser\NameUsingCollector;
use LogicException;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\ParserFactory;
use Roave\BetterReflection\Reflector\Exception\IdentifierNotFound;
use Roave\BetterReflection\Reflector\Reflector;
use SplFileInfo;

use function dirname;
use function file_get_contents;

class Runner
{
    public function __construct(
        private Reflector $reflector,
    ) {
    }

    /** @return list<Error> */
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
        $configDir = dirname($config->configLocation);

        $traverser->traverse($ast);
        foreach ($declaring->declarations as $declaration) {
            foreach ($using->usedNames as $usedName) {
                foreach ($config->rules as $rule) {
                    if (
                         ! $rule->isForbidden(
                             new Declaration(
                                 $declaration,
                                 $this->safeReflect($declaration),
                                 $configDir,
                             ),
                             new Declaration(
                                 $usedName->toString(),
                                 $this->safeReflect($usedName->toString()),
                                 $configDir,
                             ),
                         )
                    ) {
                        continue;
                    }

                    $output[] = ErrorBuilder::create()
                        ->fromName($declaration)
                        ->toName($usedName->toString())
                        ->ruleType($rule->getName())
                        ->fileName($fileInfo->getPathname())
                        ->line($usedName->getLine())
                        ->build();
                }
            }
        }

        return $output;
    }

    private function safeReflect(string $class): \BackEndTea\Architect\Domain\Reflection\Reflector|null
    {
        try {
            return new \BackEndTea\Architect\Domain\Reflection\Reflector($this->reflector->reflectClass($class));
        } catch (IdentifierNotFound) {
        }

        try {
            return new \BackEndTea\Architect\Domain\Reflection\Reflector($this->reflector->reflectFunction($class));
        } catch (IdentifierNotFound) {
        }

        try {
            return new \BackEndTea\Architect\Domain\Reflection\Reflector($this->reflector->reflectConstant($class));
        } catch (IdentifierNotFound) {
        }

        return null;
    }
}
