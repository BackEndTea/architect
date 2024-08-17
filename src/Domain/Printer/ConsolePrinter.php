<?php

declare(strict_types=1);

namespace BackEndTea\Architect\Domain\Printer;

use BackEndTea\Architect\Domain\Container\ArchitectGetContainer;
use BackEndTea\Architect\Domain\Rule\Error;
use Symfony\Component\Console\Style\SymfonyStyle;

use function array_map;
use function count;
use function sprintf;
use function usort;

class ConsolePrinter implements Printer
{
    private SymfonyStyle $io;

    /** @inheritDoc */
    public function print(array $errors): void
    {
        $errorsByFile = [];

        foreach ($errors as $error) {
            $errorsByFile[$error->fileName][] = $error;
        }

        foreach ($errorsByFile as $errorGroup) {
            usort($errorGroup, static fn (Error $a, Error $b) => $a->line <=> $b->line);
            $this->io->writeln($errorGroup[0]->fileName);
            $this->io->table(
                ['Line', 'Rule', 'From', 'To'],
                array_map(
                    static fn (Error $error) => [
                        $error->line,
                        $error->ruleType,
                        $error->fromName,
                        $error->toName,
                    ],
                    $errorGroup,
                ),
            );
        }

        $totalErrors = count($errors);

        $this->io->error(sprintf(
            'Found %d error%s',
            $totalErrors,
            $totalErrors > 1 ? 's' : '',
        ));
    }

    public function setUp(ArchitectGetContainer $container): void
    {
        $this->io = $container->get(SymfonyStyle::class);
    }
}
