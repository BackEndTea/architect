<?php

declare(strict_types=1);

namespace BackEndTea\Architect\Domain\Reflection;

use Roave\BetterReflection\Reflection\ReflectionClass;
use Roave\BetterReflection\Reflection\ReflectionConstant;
use Roave\BetterReflection\Reflection\ReflectionFunction;

class Reflector
{
    public function __construct(
        private ReflectionClass|ReflectionFunction|ReflectionConstant|null $reflection,
    ) {
    }

    public function isNativePHP(): bool|null
    {
        return $this->reflection?->isInternal();
    }

    public function getFQN(): string|null
    {
        return $this->reflection?->getName();
    }

    public function getFileName(): string|null
    {
        return $this->reflection?->getFileName();
    }

    public function isFunction(): bool|null
    {
        return $this->reflection instanceof ReflectionFunction;
    }

    public function isClass(): bool|null
    {
        return $this->reflection instanceof ReflectionClass;
    }

    public function isConstant(): bool|null
    {
        return $this->reflection instanceof ReflectionConstant;
    }
}
