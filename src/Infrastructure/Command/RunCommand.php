<?php

declare(strict_types=1);

namespace BackEndTea\Architect\Infrastructure\Command;

use BackEndTea\Architect\Domain\Config\Configuration;
use BackEndTea\Architect\Domain\Config\ConfigurationBuilder;
use BackEndTea\Architect\Infrastructure\Runner;
use InvalidArgumentException;
use Roave\BetterReflection\BetterReflection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use function array_merge;
use function getcwd;
use function is_string;
use function realpath;

#[AsCommand(
    name: 'run',
    description: 'Run architect',
)]
class RunCommand extends Command
{
    protected function configure(): void
    {
        $this->addOption('config', 'c', InputOption::VALUE_REQUIRED, 'Path to the configuration file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $configuration = $this->buildConfiguration($input);

        $io        = new SymfonyStyle($input, $output);
        $reflector =  (new BetterReflection())
            ->reflector();

        $runner   = new Runner($reflector);
        $errors   = [];
        $hasRan   = false;
        $progress = new ProgressBar($output);
        $io->writeln('Running Architect');
        foreach ($configuration->paths as $path) {
            $progress->advance();
            $hasRan = true;
            $errors = array_merge($errors, $runner->run($configuration, $path));
        }

        $progress->finish();
        $io->writeln('');

        if (! $hasRan) {
            $output->writeln('No paths configured to run');

            return Command::FAILURE;
        }

        if ($errors !== []) {
            foreach ($errors as $error) {
                $output->writeln($error);
            }

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    private function buildConfiguration(InputInterface $input): Configuration
    {
        $path = $input->getOption('config');

        if ($path === null) {
            $path = getcwd() . '/architect.php';
        }

        if (! is_string($path)) {
            throw new InvalidArgumentException('Path should be a string');
        }

        $path = realpath($path);
        if (! $path) {
            throw new InvalidArgumentException('Path should be a valid file');
        }

        $config = require $path;

        if ($config instanceof ConfigurationBuilder) {
            return $config->build();
        }

        if ($config instanceof Configuration) {
            return $config;
        }

        throw new InvalidArgumentException('Configuration file should return a Configuration(Builder) object');
    }
}
