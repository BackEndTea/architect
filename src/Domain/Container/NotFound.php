<?php

declare(strict_types=1);

namespace BackEndTea\Architect\Domain\Container;

use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;

class NotFound extends RuntimeException implements NotFoundExceptionInterface
{
}
