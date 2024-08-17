<?php

declare(strict_types=1);

namespace BackEndTea\Architect\Domain\Container;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

interface ArchitectGetContainer extends ContainerInterface
{
    /**
     * @param class-string<T> $id
     *
     * @return T
     *
     * @throws ContainerExceptionInterface Error while retrieving the entry.
     * @throws NotFoundExceptionInterface  No entry was found for **this** identifier.
     *
     * @template T of object
     */
    public function get(string $id): object;
}
