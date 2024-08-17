<?php

declare(strict_types=1);

namespace BackEndTea\Architect\Test\Domain\Rule;

use BackEndTea\Architect\Domain\Declaration;
use BackEndTea\Architect\Domain\Matcher\Always;
use BackEndTea\Architect\Domain\Matcher\NamespaceRegex;
use BackEndTea\Architect\Domain\Rule\Rule;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class RuleTest extends TestCase
{
    public function testCantConstructInvalidFrom(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Rule('', null, null, new Always(), new Always());
    }

    public function testCantConstructInvalidFromTo(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Rule('', new Always(), new Always(), null, null);
    }

    public function testRuleLogic(): void
    {
        // Anything from Domain (but not from Test) shoud not allow infrastructure or application
        $rule = new Rule(
            '',
            from: new NamespaceRegex('/\\Domain\\\\/'),
            notFrom: new NamespaceRegex('/\\\\Test\\\\/'),
            to : new NamespaceRegex('/\\\\(Infrastructure)|(Application)\\\\/'),
            notTo: new NamespaceRegex('/\\\\Exclusion\\\\/'),
        );

        $this->assertTrue($rule->isForbidden(
            new Declaration('Me\Domain\SomeClass', null, ''),
            new Declaration('Me\Infrastructure\SomeClass', null, ''),
        ));

        $this->assertTrue($rule->isForbidden(
            new Declaration('Me\Domain\SomeClass', null, ''),
            new Declaration('Me\Application\SomeClass', null, ''),
        ));

        $this->assertFalse($rule->isForbidden(
            new Declaration('Me\Domain\Test\SomeClass', null, ''),
            new Declaration('Me\Infrastructure\SomeClass', null, ''),
        ));

        $this->assertFalse($rule->isForbidden(
            new Declaration('Me\Domain\Test\SomeClass', null, ''),
            new Declaration('Me\Application\SomeClass', null, ''),
        ));

        $this->assertFalse($rule->isForbidden(
            new Declaration('Me\Domain\Test\SomeClass', null, ''),
            new Declaration('Me\Infrastructure\Exclusion\SomeClass', null, ''),
        ));

        $this->assertFalse($rule->isForbidden(
            new Declaration('Me\Domain\Test\SomeClass', null, ''),
            new Declaration('Me\Application\Exclusion\SomeClass', null, ''),
        ));

        $this->assertFalse($rule->isForbidden(
            new Declaration('Me\Domain\SomeClass', null, ''),
            new Declaration('Me\Infrastructure\Exclusion\SomeClass', null, ''),
        ));

        $this->assertFalse($rule->isForbidden(
            new Declaration('Me\Domain\SomeClass', null, ''),
            new Declaration('Me\Application\Exclusion\SomeClass', null, ''),
        ));
    }

    public function testOnlySelfDepends(): void
    {
        $rule = new Rule(
            'legacy to non legacy',
            from: new NamespaceRegex('#\\\\Legacy\\\\#'),
            notTo: new NamespaceRegex('#\\\\Legacy\\\\#'),
        );

        // NO legacy
        $this->assertFalse($rule->isForbidden(
            new Declaration('Me\SomeClass', null, ''),
            new Declaration('Me\SomeClass', null, ''),
        ));

        // somethign else to legacy
        $this->assertFalse($rule->isForbidden(
            new Declaration('Me\SomeClass', null, ''),
            new Declaration('Me\Legacy\SomeClass', null, ''),
        ));

        // legacy to legacy
        $this->assertFalse($rule->isForbidden(
            new Declaration('Me\Legacy\SomeClass', null, ''),
            new Declaration('Me\Legacy\SomeClass', null, ''),
        ));

        // legacy to non legacy
        $this->assertTrue($rule->isForbidden(
            new Declaration('Me\Legacy\SomeClass', null, ''),
            new Declaration('Me\SomeClass', null, ''),
        ));
    }

    public function testNotFromTo(): void
    {
        $rule = new Rule(
            'non legacy to legacy',
            notFrom: new NamespaceRegex('#\\\\Legacy\\\\#'),
            to: new NamespaceRegex('#\\\\Legacy\\\\#'),
        );

        // non legacy to non legqcy
        $this->assertFalse($rule->isForbidden(
            new Declaration('Me\SomeClass', null, ''),
            new Declaration('Me\SomeClass', null, ''),
        ));

        // non legacy to legacy
        $this->assertTrue($rule->isForbidden(
            new Declaration('Me\SomeClass', null, ''),
            new Declaration('Me\Legacy\SomeClass', null, ''),
        ));

        // legacy to legacy
        $this->assertFalse($rule->isForbidden(
            new Declaration('Me\Legacy\SomeClass', null, ''),
            new Declaration('Me\Legacy\SomeClass', null, ''),
        ));

        // legacy to non legacy
        $this->assertFalse($rule->isForbidden(
            new Declaration('Me\Legacy\SomeClass', null, ''),
            new Declaration('Me\SomeClass', null, ''),
        ));
    }
}
