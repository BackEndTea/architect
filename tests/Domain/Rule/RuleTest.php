<?php

declare(strict_types=1);

namespace BackEndTea\Architect\Test\Domain\Rule;

use BackEndTea\Architect\Domain\Declaration;
use BackEndTea\Architect\Domain\Matcher\All;
use BackEndTea\Architect\Domain\Matcher\NamespaceRegex;
use BackEndTea\Architect\Domain\Rule\Rule;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class RuleTest extends TestCase
{
    public function testCantConstructInvalidFrom(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Rule(null, null, new All(), new All());
    }

    public function testCantConstructInvalidFromTo(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Rule(new All(), new All(), null, null);
    }

    public function testRuleLogic(): void
    {
        // Anything from Domain (but not from Test) shoud not allow infrastructure or application
        $rule = new Rule(
            from: new NamespaceRegex('/\\Domain\\\\/'),
            notFrom: new NamespaceRegex('/\\\\Test\\\\/'),
            to : new NamespaceRegex('/\\\\(Infrastructure)|(Application)\\\\/'),
            notTo: new NamespaceRegex('/\\\\Exclusion\\\\/'),
        );

        $this->assertFalse($rule->isAllowed(
            new Declaration('Me\Domain\SomeClass', null),
            new Declaration('Me\Infrastructure\SomeClass', null),
        ));

        $this->assertFalse($rule->isAllowed(
            new Declaration('Me\Domain\SomeClass', null),
            new Declaration('Me\Application\SomeClass', null),
        ));

        $this->assertTrue($rule->isAllowed(
            new Declaration('Me\Domain\Test\SomeClass', null),
            new Declaration('Me\Infrastructure\SomeClass', null),
        ));

        $this->assertTrue($rule->isAllowed(
            new Declaration('Me\Domain\Test\SomeClass', null),
            new Declaration('Me\Application\SomeClass', null),
        ));

        $this->assertTrue($rule->isAllowed(
            new Declaration('Me\Domain\Test\SomeClass', null),
            new Declaration('Me\Infrastructure\Exclusion\SomeClass', null),
        ));

        $this->assertTrue($rule->isAllowed(
            new Declaration('Me\Domain\Test\SomeClass', null),
            new Declaration('Me\Application\Exclusion\SomeClass', null),
        ));

        $this->assertTrue($rule->isAllowed(
            new Declaration('Me\Domain\SomeClass', null),
            new Declaration('Me\Infrastructure\Exclusion\SomeClass', null),
        ));

        $this->assertTrue($rule->isAllowed(
            new Declaration('Me\Domain\SomeClass', null),
            new Declaration('Me\Application\Exclusion\SomeClass', null),
        ));
    }
}
