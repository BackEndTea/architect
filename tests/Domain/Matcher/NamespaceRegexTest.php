<?php

declare(strict_types=1);

namespace BackEndTea\Architect\Test\Domain\Matcher;

use BackEndTea\Architect\Domain\Declaration;
use BackEndTea\Architect\Domain\Matcher\NamespaceRegex;
use BackEndTea\Architect\Domain\Rule;
use BackEndTea\Architect\Infrastructure\Command\RunCommand;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class NamespaceRegexTest extends TestCase
{
    #[DataProvider('provideMatchCases')]
    public function testMatches(string $regex, string $namespace): void
    {
        $matcher = new NamespaceRegex($regex);
        $this->assertTrue($matcher->matches(new Declaration($namespace, null, '')));
    }

    public static function provideMatchCases(): Generator
    {
        yield ['#\\\Domain\\\#', Rule::class];
        yield ['#\\\Test\\\#', self::class];
        yield ['/\\\Domain\\\\/', 'Me\Domain\SomeClass'];
        yield ['/\\\\Infrastructure\\\\/', 'Me\Infrastructure\\SomeClass'];
    }

    #[DataProvider('provideNotMatchCases')]
    public function testNotMatches(string $regex, string $namespace): void
    {
        $matcher = new NamespaceRegex($regex);

        $this->assertFalse($matcher->matches(new Declaration($namespace, null, '')));
    }

    public static function provideNotMatchCases(): Generator
    {
        yield ['#\\\Domain\\\#', RunCommand::class];
        yield ['#\\\Test\\\#', Rule::class];
    }

    public function testItIsNullForNoNamespace(): void
    {
        $matcher = new NamespaceRegex('/.*/');

        $this->assertNull($matcher->matches(new Declaration(null, null, '')));
    }
}
