<?php

declare(strict_types=1);

namespace BackEndTea\Architect\Infrastructure\Command;

use BackEndTea\Architect\Domain\Config\Configuration;
use BackEndTea\Architect\Domain\Declaration;
use BackEndTea\Architect\Infrastructure\PHPParser\DeclaringCollector;
use BackEndTea\Architect\Infrastructure\PHPParser\NameUsingCollector;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\ParserFactory;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function assert;
use function file_get_contents;
use function sprintf;
use function str_contains;

#[AsCommand(
    name: 'run',
    description: 'Run architect',
)]
class RunCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $config = require_once __DIR__ . '/../../../architect.php';
        assert($config instanceof Configuration);

        foreach ($config->paths as $path) {
            $parser       = (new ParserFactory())->createForHostVersion();
            $ast          = $parser->parse(file_get_contents($path->getRealPath()));
            $traverser    = new NodeTraverser();
            $nameResolver = new NameResolver();
            $traverser->addVisitor($nameResolver);
            $traverser->addVisitor($declaring = new DeclaringCollector());
            $traverser->addVisitor($using = new NameUsingCollector());

            $traverser->traverse($ast);
            foreach ($declaring->declarations as $declaration) {
                foreach ($using->usedNames as $usedName) {
                    foreach ($config->rules as $rule) {
                        if (! str_contains($usedName, 'BackEndTea\Architect\Infrastructure\Command\RunCommand')) {
                            continue;
                        }

                        if ($rule->isAllowed(new Declaration($declaration), new Declaration($usedName))) {
                            continue;
                        }

                        $output->writeln(sprintf('Violation found: %s uses %s', $declaration, $usedName));
                    }
                }
            }
        }

        return Command::SUCCESS;
    }
}
