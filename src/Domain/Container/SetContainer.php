<?php

declare(strict_types=1);

namespace BackEndTea\Architect\Domain\Container;

use Psr\Container\ContainerInterface;

interface SetContainer
{
    /**
     * @param class-string<T> $id
     * @param T               $value
     *
     * @template T of object
     */
    public function set(string $id, object $value): void;

    /**
     * @param class-string<T>                 $id
     * @param callable(ContainerInterface): T $valueProvider
     *
     * @template T of object
     */
    public function setLazy(string $id, callable $valueProvider): void;
}
