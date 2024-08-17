<?php

declare(strict_types=1);

namespace BackEndTea\Architect\Domain\Printer;

use BackEndTea\Architect\Domain\Container\ArchitectGetContainer;
use BackEndTea\Architect\Domain\Rule\Error;
use Symfony\Component\Filesystem\Filesystem;

use function array_map;
use function json_encode;

use const JSON_THROW_ON_ERROR;

class JsonPrinter implements Printer
{
    private Filesystem $fs;

    public function __construct(
        private string $fileName,
    ) {
    }

    /** @inheritDoc */
    public function print(array $errors): void
    {
        $this->fs->dumpFile(
            $this->fileName,
            json_encode(array_map(
                static fn (Error $error): array => [
                    'rule' => $error->ruleType,
                    'fileName' => $error->fileName,
                    'line' => $error->line,
                    'from' => $error->fromName,
                    'to' => $error->toName,
                ],
                $errors,
            ), JSON_THROW_ON_ERROR),
        );
    }

    public function setUp(ArchitectGetContainer $container): void
    {
        $this->fs = $container->get(Filesystem::class);
    }
}
