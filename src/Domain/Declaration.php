<?php

declare(strict_types=1);

namespace BackEndTea\Architect\Domain;

use Roave\BetterReflection\Reflection\ReflectionClass;

class Declaration
{
    public function __construct(
        private readonly string|null $namespace,
        private readonly ReflectionClass|null $classReflection,
    ) {
    }

    public function getNamespace(): string|null
    {
        return $this->namespace;
    }

    public function fileName(): string|null
    {
        return $this->classReflection?->getFileName();
    }
}
