<?php

declare(strict_types=1);

namespace BackEndTea\Architect\Domain\Container\Exception;

use Psr\Container\ContainerExceptionInterface;
use RuntimeException;

use function sprintf;

class InvalidContainerType extends RuntimeException implements ContainerExceptionInterface
{
    public static function fomContainerTypes(string $expected, string $received): self
    {
        return new self(sprintf('Expected to receive a container of type %s, but received a container of type %s', $expected, $received));
    }
}
