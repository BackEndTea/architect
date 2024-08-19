<?php

declare(strict_types=1);

namespace BackEndTea\Architect\Domain\Container;

use BackEndTea\Architect\Domain\Container\Exception\InvalidContainerType;
use Psr\Container\ContainerInterface;

use function array_key_exists;
use function get_debug_type;
use function sprintf;

class ArchitectContainer implements ArchitectGetContainer, SetContainer
{
    /** @var array<class-string, object> */
    private array $dependencies = [];

    /** @var array<class-string, callable(ContainerInterface): object> */
    private array $lazyDependencies = [];

    public function get(string $id): object
    {
        if (array_key_exists($id, $this->dependencies)) {
            $result = $this->dependencies[$id];
            if (! $result instanceof $id) {
                throw InvalidContainerType::fomContainerTypes($id, get_debug_type($result));
            }

            return $result;
        }

        if (array_key_exists($id, $this->lazyDependencies)) {
            $result =  $this->lazyDependencies[$id]($this);
            if (! $result instanceof $id) {
                throw InvalidContainerType::fomContainerTypes($id, get_debug_type($result));
            }

            $this->dependencies[$id] = $result;
            unset($this->lazyDependencies[$id]);

            return $result;
        }

        throw new NotFound(sprintf('Could not find %s', $id));
    }

    public function has(string $id): bool
    {
        return array_key_exists($id, $this->dependencies)
            || array_key_exists($id, $this->lazyDependencies);
    }

    public function set(string $id, object $value): void
    {
        $this->dependencies[$id] = $value;
    }

    public function setLazy(string $id, callable $valueProvider): void
    {
        $this->lazyDependencies[$id] = $valueProvider;
    }
}
