<?php

declare(strict_types=1);

namespace BackEndTea\Architect\Test\Infrastructure\PHPParser;

use BackEndTea\Architect\Infrastructure\PHPParser\DeclaringCollector;
use BackEndTea\Architect\Infrastructure\PHPParser\NameUsingCollector;
use Generator;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\ParserFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Roave\BetterReflection\Reflection\ReflectionClass;
use Roave\BetterReflection\Reflection\ReflectionConstant;
use Roave\BetterReflection\Reflection\ReflectionFunction;

use function array_map;

class NameUsingCollectorTest extends TestCase
{
    /** @param list<string> $names */
    #[DataProvider('provideCodeAndNameCases')]
    public function testItCollects(string $code, array $names): void
    {
        $collector = new NameUsingCollector();

        $parser = (new ParserFactory())->createForHostVersion();

        $traverser = new NodeTraverser();
        $traverser->addVisitor(new NameResolver());

        $traverser->addVisitor(new DeclaringCollector());
        $traverser->addVisitor($collector);

        $ast = $parser->parse($code);
        if ($ast === null) {
            $this->fail('Could not parse code');
        }

        $traverser->traverse($ast);

        $this->assertSame($names, array_map(static fn (Node\Name $name) => $name->toString(), $collector->usedNames));
    }

    public static function provideCodeAndNameCases(): Generator
    {
        yield 'no statements' => [
            '<?php',
            [],
        ];

        yield 'reflector class' => [
            <<<'PHP'
<?php

namespace BackEndTea\Architect\Domain\Reflection;

use Roave\BetterReflection\Reflection\ReflectionClass;
use Roave\BetterReflection\Reflection\ReflectionConstant;
use Roave\BetterReflection\Reflection\ReflectionFunction;

class Reflector
{
    public function __construct(
        private ReflectionClass|ReflectionFunction|ReflectionConstant|null $classReflection
    ) {
    }

    public function isNativePHP(): bool|null
    {
        return $this->classReflection?->isInternal();
    }

    public function getFQN(): string|null
    {
        return $this->classReflection->getName();
    }

    public function isScalar(): bool|null
    {
        return $this->classReflection->isInternal()
            && in_array($this->classReflection->getName(), ['int', 'string', 'bool', 'float']);
    }

    public function isSelfOrStatic(): ?bool
    {
        if (!$this->classReflection) {
            return null;
        }

        return $this->classReflection->getName() === 'self' || $this->classReflection->getName() === 'static';
    }

    public function getFileName(): string|null
    {
        return $this->classReflection?->getFileName();
    }

    public function isFunction(): bool|null
    {
        return $this->classReflection instanceof ReflectionFunction;
    }

    public function isClass(): bool|null
    {
        return $this->classReflection instanceof ReflectionClass;
    }

    public function isConstant(): bool|null
    {
        return $this->classReflection instanceof ReflectionConstant;
    }
}
PHP,
            [
                //use statements
                ReflectionClass::class,
                ReflectionConstant::class,
                ReflectionFunction::class,
                // constructor
                ReflectionClass::class,
                ReflectionFunction::class,
                ReflectionConstant::class,
                // isScalar
                'in_array',
                // is function
                ReflectionFunction::class,
                // is class
                ReflectionClass::class,
                // is constant
                ReflectionConstant::class,
            ],
        ];

        yield 'ignored types' => [
            <<<'PHP'
            <?php
            namespace Baltazhar;
            class Bar{}
            trait Foo{}
            enum Baz{}
            function foobar(){}
            const MY_CONST = 1;
            interface MyInterface {
                public function a(self $a): self;
                public function b($b): static;
                public function c(int $c): int;
                public function d(string $d): string;
                public function e(bool $e): bool;
                public function f(float $f): float;
                public function g(array $g): array;
                public function h(null $h): null;
                public function i(object $i): object;
                public function j(true $j): true;
                public function k(false $k): false;
            }
            (bool)$a;
            (int)$a;
            (float)$a;
            (string)$a;
            (array)$a;
            function check () {
                return null;
                return 3;
                return 'string';
                return new self();
                return new static();
            }

            new class {
                public string $a;
                public int $b;
                public float $c;
                public bool $d;
            };
            PHP,
            [],
        ];
    }
}
