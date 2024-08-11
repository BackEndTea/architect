<?php

declare(strict_types=1);

namespace BackEndTea\Architect\Infrastructure\Console;

use Symfony\Component\Console\Application as SymfonyApplication;

class Application extends SymfonyApplication
{
    /** @inheritDoc */
    public function getDefaultCommands(): array
    {
        return [
            ...parent::getDefaultCommands(),
        ];
    }
}
