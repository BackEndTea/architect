<?php

declare(strict_types=1);

namespace BackEndTea\Architect\Domain;

use BackEndTea\Architect\Domain\Reflection\Reflector;

class Declaration
{
    public function __construct(
        private readonly string|null $namespace,
        private readonly Reflector|null $reflector,
        private readonly string $rootDirectory,
    ) {
    }

    public function getNamespace(): string|null
    {
        return $this->namespace;
    }

    public function fileName(): string|null
    {
        return $this->reflector?->getFileName();
    }

    public function isNativePHP(): bool|null
    {
        return $this->reflector?->isNativePHP();
    }

    public function rootDirectory(): string
    {
        return $this->rootDirectory;
    }
}
