<?php

declare(strict_types=1);

namespace BackEndTea\Architect\Domain\Printer;

use BackEndTea\Architect\Domain\Container\ArchitectGetContainer;
use BackEndTea\Architect\Domain\Rule\Error;

interface Printer
{
    /** @param array<Error> $errors */
    public function print(array $errors): void;

    public function setUp(ArchitectGetContainer $container): void;
}
